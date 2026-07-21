#!/usr/bin/env bash
# Apunta el symlink `current` de un servicio a una release ya existente en
# releases/, de forma atomica (symlink temporal + rename).
#
# Uso: activate-release.sh <servicio> <nombre-de-release>
# El nombre de release es el nombre del directorio dentro de releases/,
# p. ej. 20260721030000
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/lib/common.sh"

SERVICE="${1:-}"
RELEASE_NAME="${2:-}"
require_service "$SERVICE"
[[ -n "$RELEASE_NAME" ]] || fail "uso: activate-release.sh <servicio> <nombre-de-release>"

BASE="$(service_base "$SERVICE")"
TARGET="$BASE/releases/$RELEASE_NAME"

[[ -d "$TARGET" ]] || fail "no existe la release: $TARGET"
[[ -f "$TARGET/public/index.php" ]] || fail "la release no tiene public/index.php, no se activa: $TARGET"

TMP_LINK="$BASE/current.tmp.$$"
ln -sfn "$TARGET" "$TMP_LINK"
mv -T "$TMP_LINK" "$BASE/current"

# PHP-FPM cachea la resolucion de realpath de los symlinks (realpath_cache_ttl,
# 120s por defecto) y opcache por ruta absoluta. Sin este reload, un cambio de
# `current` puede seguir sirviendo la release anterior hasta que ese cache
# expire por su cuenta - encontrado de verdad durante el deploy de Fase 7
# (ticketing sirvio contenido del release anterior varios minutos tras
# activar el nuevo, ver docs/operations/phase7-*.md).
if command -v systemctl >/dev/null 2>&1 && systemctl is-active --quiet php8.3-fpm 2>/dev/null; then
    sudo systemctl reload php8.3-fpm
fi

log_release_event "$SERVICE" "activate release=$RELEASE_NAME"
info "current -> $TARGET (php-fpm recargado)"
