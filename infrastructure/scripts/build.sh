#!/usr/bin/env bash
# Construye una release en un directorio ya extraido (checkout de git),
# antes de que deploy.sh la mueva a releases/.
#
# Uso: build.sh <servicio> <directorio-de-la-release>
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/lib/common.sh"

SERVICE="${1:-}"
RELEASE_DIR="${2:-}"
require_service "$SERVICE"
[[ -n "$RELEASE_DIR" ]] || fail "uso: build.sh <servicio> <directorio-de-la-release>"
[[ -d "$RELEASE_DIR" ]] || fail "no existe el directorio de release: $RELEASE_DIR"

cd "$RELEASE_DIR"

if [[ -f composer.json ]]; then
    info "composer install (sin dev, optimizado)"
    composer install --no-interaction --no-progress --no-dev --optimize-autoloader
fi

if [[ -f package.json ]]; then
    info "npm ci"
    npm ci --no-audit --no-fund
    info "npm run build"
    npm run build
fi

info "build completado para $SERVICE en $RELEASE_DIR"
