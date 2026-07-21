#!/usr/bin/env bash
# Funciones compartidas por los scripts de release de infrastructure/scripts.
# Se espera `set -euo pipefail` en el script que hace source de este archivo.

VERAGUAS_SERVICES=(web store ticketing payments)

require_service() {
    local svc="${1:-}"
    if [[ -z "$svc" ]]; then
        echo "[FAIL] falta el parametro <servicio>. Valores validos: ${VERAGUAS_SERVICES[*]}" >&2
        exit 2
    fi
    local ok=0
    for s in "${VERAGUAS_SERVICES[@]}"; do
        [[ "$svc" == "$s" ]] && ok=1
    done
    if [[ "$ok" -ne 1 ]]; then
        echo "[FAIL] servicio desconocido: '$svc'. Valores validos: ${VERAGUAS_SERVICES[*]}" >&2
        exit 2
    fi
}

service_base() {
    echo "/var/www/veraguas-$1"
}

log_release_event() {
    local svc="$1" event="$2"
    local base
    base="$(service_base "$svc")"
    local logfile="$base/shared/logs/releases.log"
    printf '%s\tuser=%s\tservice=%s\t%s\n' \
        "$(date -Iseconds)" "${SUDO_USER:-$USER}" "$svc" "$event" >> "$logfile"
}

fail() {
    echo "[FAIL] $*" >&2
    exit 1
}

info() {
    echo "[INFO] $*"
}
