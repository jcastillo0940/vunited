#!/usr/bin/env bash
# Orquesta un deploy completo: copia el codigo fuente a una release nueva,
# enlaza recursos compartidos, construye, prueba, activa, reinicia workers
# y verifica. Si la verificacion final falla, revierte automaticamente a la
# release anterior.
#
# Uso: deploy.sh <servicio> <directorio-fuente>
# Requiere privilegios de root (escribe en directorios propiedad de
# veraguas-<servicio> y reinicia unidades systemd).
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/lib/common.sh"

SERVICE="${1:-}"
SOURCE_DIR="${2:-}"
require_service "$SERVICE"
[[ -n "$SOURCE_DIR" ]] || fail "uso: deploy.sh <servicio> <directorio-fuente>"
[[ -d "$SOURCE_DIR" ]] || fail "no existe el directorio fuente: $SOURCE_DIR"

BASE="$(service_base "$SERVICE")"
RELEASE_NAME="$(date +%Y%m%d%H%M%S)"
RELEASE_DIR="$BASE/releases/$RELEASE_NAME"

PREVIOUS_RELEASE=""
if [[ -L "$BASE/current" ]]; then
    PREVIOUS_RELEASE="$(basename "$(readlink -f "$BASE/current")")"
fi

info "desplegando $SERVICE: release $RELEASE_NAME (anterior: ${PREVIOUS_RELEASE:-ninguna})"

mkdir -p "$RELEASE_DIR"
rsync -a --delete --exclude=.git --exclude=node_modules --exclude=vendor "$SOURCE_DIR"/ "$RELEASE_DIR"/
chown -R "veraguas-$SERVICE:veraguas-$SERVICE" "$RELEASE_DIR"

# Recursos compartidos entre releases: storage y .env nunca viven dentro de
# la release, se enlazan desde shared/.
rm -rf "$RELEASE_DIR/storage"
sudo -u "veraguas-$SERVICE" ln -sfn "$BASE/shared/storage" "$RELEASE_DIR/storage"
sudo -u "veraguas-$SERVICE" ln -sfn "$BASE/shared/.env" "$RELEASE_DIR/.env"

sudo -u "veraguas-$SERVICE" "$SCRIPT_DIR/build.sh" "$SERVICE" "$RELEASE_DIR"
sudo -u "veraguas-$SERVICE" "$SCRIPT_DIR/test.sh" "$SERVICE" "$RELEASE_DIR"

log_release_event "$SERVICE" "deploy start release=$RELEASE_NAME previous=${PREVIOUS_RELEASE:-none}"

# A partir de aqui cualquier fallo (activar, reiniciar workers o verificar)
# debe caer en el rollback automatico, no cortar el script con set -e.
set +e
"$SCRIPT_DIR/activate-release.sh" "$SERVICE" "$RELEASE_NAME" \
    && "$SCRIPT_DIR/restart-workers.sh" "$SERVICE" \
    && "$SCRIPT_DIR/verify-release.sh" "$SERVICE"
DEPLOY_OK=$?
set -e

if [[ "$DEPLOY_OK" -eq 0 ]]; then
    log_release_event "$SERVICE" "deploy success release=$RELEASE_NAME"
    info "deploy de $SERVICE completado: release $RELEASE_NAME activa"
else
    echo "[FAIL] activar/reiniciar/verificar $SERVICE fallo, revirtiendo" >&2
    log_release_event "$SERVICE" "deploy failed release=$RELEASE_NAME, auto-rollback"
    if [[ -n "$PREVIOUS_RELEASE" ]]; then
        "$SCRIPT_DIR/activate-release.sh" "$SERVICE" "$PREVIOUS_RELEASE"
        sudo systemctl reset-failed "veraguas-$SERVICE-worker.service" >/dev/null 2>&1 || true
        "$SCRIPT_DIR/restart-workers.sh" "$SERVICE"
        log_release_event "$SERVICE" "auto-rollback to release=$PREVIOUS_RELEASE"
    fi
    fail "deploy de $SERVICE fallo, se revirtio a ${PREVIOUS_RELEASE:-ninguna release previa disponible}"
fi
