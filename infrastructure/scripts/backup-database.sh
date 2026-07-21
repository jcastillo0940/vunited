#!/usr/bin/env bash
# Dump + cifrado + checksum + copia externa (best-effort) + retencion para
# la base de datos propia de un servicio.
#
# Uso: backup-database.sh <servicio> [dias-de-retencion]
# Requiere poder leer <base>/shared/.env y <base>/shared/.backup-key, por lo
# que debe correr como el usuario veraguas-<servicio> o como root.
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/lib/common.sh"

SERVICE="${1:-}"
RETENTION_DAYS="${2:-14}"
require_service "$SERVICE"

BASE="$(service_base "$SERVICE")"
ENV_FILE="$BASE/shared/.env"
KEY_FILE="$BASE/shared/.backup-key"
LOG_FILE="$BASE/shared/logs/backup.log"

[[ -r "$ENV_FILE" ]] || fail "no se puede leer $ENV_FILE"
[[ -r "$KEY_FILE" ]] || fail "no se puede leer $KEY_FILE"

DB_HOST=$(grep -E '^DB_HOST=' "$ENV_FILE" | cut -d= -f2-)
DB_PORT=$(grep -E '^DB_PORT=' "$ENV_FILE" | cut -d= -f2-)
DB_DATABASE=$(grep -E '^DB_DATABASE=' "$ENV_FILE" | cut -d= -f2-)
DB_USERNAME=$(grep -E '^DB_USERNAME=' "$ENV_FILE" | cut -d= -f2-)
DB_PASSWORD=$(grep -E '^DB_PASSWORD=' "$ENV_FILE" | cut -d= -f2-)

TS="$(date +%Y%m%d%H%M%S)"
PLAIN="$BASE/tmp/${SERVICE}-${TS}.sql"
ENCRYPTED="$BASE/backups/${SERVICE}-${TS}.sql.enc"
CHECKSUM="$ENCRYPTED.sha256"

log_line() {
    printf '%s\tservice=%s\t%s\n' "$(date -Iseconds)" "$SERVICE" "$1" >> "$LOG_FILE"
}

cleanup() {
    rm -f "$PLAIN"
}
trap cleanup EXIT

info "dump de $DB_DATABASE ($SERVICE)"
if ! mysqldump -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" \
    --single-transaction --routines --triggers --no-tablespaces \
    "$DB_DATABASE" > "$PLAIN"; then
    log_line "status=FAIL step=dump"
    fail "mysqldump fallo para $SERVICE"
fi

info "cifrando dump"
if ! openssl enc -aes-256-cbc -pbkdf2 -salt -pass "file:$KEY_FILE" -in "$PLAIN" -out "$ENCRYPTED"; then
    log_line "status=FAIL step=encrypt"
    fail "cifrado fallo para $SERVICE"
fi

sha256sum "$ENCRYPTED" | awk '{print $1}' > "$CHECKSUM"
chmod 600 "$ENCRYPTED" "$CHECKSUM"

SIZE_BYTES=$(stat -c%s "$ENCRYPTED")
info "backup cifrado: $ENCRYPTED ($SIZE_BYTES bytes)"

# Copia externa (best-effort): pendiente de permisos IAM en Storage, ver
# docs/security/secrets-management.md para el mismo bloqueo de scope.
EXTERNAL_STATUS="skipped"
BUCKET="${VERAGUAS_BACKUP_BUCKET:-}"
if [[ -n "$BUCKET" ]]; then
    if gsutil -q cp "$ENCRYPTED" "$CHECKSUM" "gs://$BUCKET/$SERVICE/" 2>>"$LOG_FILE"; then
        EXTERNAL_STATUS="ok"
    else
        EXTERNAL_STATUS="fail"
    fi
fi

# Retencion local.
find "$BASE/backups" -maxdepth 1 -name "${SERVICE}-*.sql.enc*" -mtime "+$RETENTION_DAYS" -print -delete >> "$LOG_FILE" 2>&1 || true

log_line "status=OK step=backup file=$(basename "$ENCRYPTED") bytes=$SIZE_BYTES external=$EXTERNAL_STATUS retention_days=$RETENTION_DAYS"

if [[ "$EXTERNAL_STATUS" == "fail" ]]; then
    echo "[WARN] la copia externa fallo; el backup local si se completo: $ENCRYPTED" >&2
fi

info "backup de $SERVICE completado (copia externa: $EXTERNAL_STATUS)"
