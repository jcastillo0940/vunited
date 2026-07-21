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

log_release_event "$SERVICE" "activate release=$RELEASE_NAME"
info "current -> $TARGET"
