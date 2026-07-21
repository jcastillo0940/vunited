#!/usr/bin/env bash
# Restaura un backup cifrado (creado por backup-database.sh) sobre la base
# de datos propia de un servicio. Operacion destructiva: sobreescribe el
# contenido actual de esa base. Requiere el flag --yes explicito.
#
# Uso: restore-database.sh <servicio> <archivo.sql.enc> --yes
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/lib/common.sh"

SERVICE="${1:-}"
BACKUP_FILE="${2:-}"
CONFIRM="${3:-}"
require_service "$SERVICE"
[[ -n "$BACKUP_FILE" ]] || fail "uso: restore-database.sh <servicio> <archivo.sql.enc> --yes"
[[ -f "$BACKUP_FILE" ]] || fail "no existe el archivo de backup: $BACKUP_FILE"
[[ "$CONFIRM" == "--yes" ]] || fail "operacion destructiva: se requiere el flag --yes explicito"

BASE="$(service_base "$SERVICE")"
ENV_FILE="$BASE/shared/.env"
KEY_FILE="$BASE/shared/.backup-key"
LOG_FILE="$BASE/shared/logs/backup.log"
CHECKSUM_FILE="$BACKUP_FILE.sha256"

[[ -r "$ENV_FILE" ]] || fail "no se puede leer $ENV_FILE"
[[ -r "$KEY_FILE" ]] || fail "no se puede leer $KEY_FILE"
[[ -f "$CHECKSUM_FILE" ]] || fail "no existe el checksum esperado: $CHECKSUM_FILE"

info "verificando checksum"
EXPECTED="$(cat "$CHECKSUM_FILE")"
ACTUAL="$(sha256sum "$BACKUP_FILE" | awk '{print $1}')"
[[ "$EXPECTED" == "$ACTUAL" ]] || fail "checksum no coincide para $BACKUP_FILE (backup corrupto o alterado)"

DB_HOST=$(grep -E '^DB_HOST=' "$ENV_FILE" | cut -d= -f2-)
DB_PORT=$(grep -E '^DB_PORT=' "$ENV_FILE" | cut -d= -f2-)
DB_DATABASE=$(grep -E '^DB_DATABASE=' "$ENV_FILE" | cut -d= -f2-)
DB_USERNAME=$(grep -E '^DB_USERNAME=' "$ENV_FILE" | cut -d= -f2-)
DB_PASSWORD=$(grep -E '^DB_PASSWORD=' "$ENV_FILE" | cut -d= -f2-)

DECRYPTED="$BASE/tmp/restore-$$-$(basename "${BACKUP_FILE%.enc}")"
cleanup() {
    rm -f "$DECRYPTED"
}
trap cleanup EXIT

info "descifrando backup"
openssl enc -d -aes-256-cbc -pbkdf2 -pass "file:$KEY_FILE" -in "$BACKUP_FILE" -out "$DECRYPTED"
chmod 600 "$DECRYPTED"

info "restaurando sobre $DB_DATABASE ($SERVICE) -- esto sobreescribe datos existentes"
mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" < "$DECRYPTED"

printf '%s\tservice=%s\tstatus=OK\tstep=restore\tfile=%s\n' \
    "$(date -Iseconds)" "$SERVICE" "$(basename "$BACKUP_FILE")" >> "$LOG_FILE"

info "restore de $SERVICE completado desde $(basename "$BACKUP_FILE")"
