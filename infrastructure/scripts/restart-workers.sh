#!/usr/bin/env bash
# Reinicia el worker y el scheduler (timer) de un servicio de forma
# controlada, tipicamente despues de activar una release nueva.
#
# Uso: restart-workers.sh <servicio>
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/lib/common.sh"

SERVICE="${1:-}"
require_service "$SERVICE"

info "reiniciando worker de $SERVICE"
sudo systemctl restart "veraguas-$SERVICE-worker.service"

info "reiniciando scheduler timer de $SERVICE"
sudo systemctl restart "veraguas-$SERVICE-scheduler.timer"

sleep 2

if ! systemctl is-active --quiet "veraguas-$SERVICE-worker.service"; then
    fail "el worker de $SERVICE no quedo activo tras el reinicio"
fi
if ! systemctl is-active --quiet "veraguas-$SERVICE-scheduler.timer"; then
    fail "el scheduler timer de $SERVICE no quedo activo tras el reinicio"
fi

log_release_event "$SERVICE" "restart-workers"
info "worker y scheduler de $SERVICE activos"
