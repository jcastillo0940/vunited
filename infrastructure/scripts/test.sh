#!/usr/bin/env bash
# Corre las pruebas automatizadas de una release antes de activarla.
#
# Uso: test.sh <servicio> <directorio-de-la-release>
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/lib/common.sh"

SERVICE="${1:-}"
RELEASE_DIR="${2:-}"
require_service "$SERVICE"
[[ -n "$RELEASE_DIR" ]] || fail "uso: test.sh <servicio> <directorio-de-la-release>"
[[ -d "$RELEASE_DIR" ]] || fail "no existe el directorio de release: $RELEASE_DIR"

cd "$RELEASE_DIR"

ran_something=0

if [[ -x vendor/bin/phpunit ]]; then
    info "phpunit"
    vendor/bin/phpunit --stop-on-failure
    ran_something=1
fi

if [[ -x vendor/bin/pest ]]; then
    info "pest"
    vendor/bin/pest
    ran_something=1
fi

if [[ "$ran_something" -eq 0 ]]; then
    info "no se encontro suite de pruebas (vendor/bin/phpunit o pest); nada que ejecutar"
fi

info "pruebas completadas para $SERVICE"
