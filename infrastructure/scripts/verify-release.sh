#!/usr/bin/env bash
# Verifica que la release actualmente activa de un servicio esta sana.
# Es una capa fina sobre health-check.sh usada por deploy.sh y
# rollback-release.sh para decidir si mantener o revertir un cambio.
#
# Uso: verify-release.sh <servicio>
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/lib/common.sh"

SERVICE="${1:-}"
require_service "$SERVICE"

info "verificando release activa de $SERVICE"
"$SCRIPT_DIR/health-check.sh" "$SERVICE"
