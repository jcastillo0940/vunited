#!/usr/bin/env bash
# Revierte un servicio a la release anterior (o a una release especifica).
#
# Uso: rollback-release.sh <servicio> [nombre-de-release]
#
# Sin el segundo parametro, revierte a la ultima release que estuvo
# realmente activa antes de la actual, segun el historial de
# shared/logs/releases.log (no al directorio "anterior" por orden
# alfabetico: releases/ puede contener intentos fallidos que nunca llegaron
# a activarse de verdad, y esos no deben elegirse como destino de rollback).
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/lib/common.sh"

SERVICE="${1:-}"
TARGET_RELEASE="${2:-}"
require_service "$SERVICE"

BASE="$(service_base "$SERVICE")"
[[ -L "$BASE/current" ]] || fail "no hay release activa en $SERVICE, nada que revertir"

CURRENT_RELEASE="$(basename "$(readlink -f "$BASE/current")")"

if [[ -z "$TARGET_RELEASE" ]]; then
    LOGFILE="$BASE/shared/logs/releases.log"
    [[ -r "$LOGFILE" ]] || fail "no existe $LOGFILE, no se puede determinar la release anterior automaticamente (pase el nombre explicitamente)"
    # Extrae, en orden cronologico, cada release que quedo realmente activa
    # (activate/deploy success/rollback success), colapsa repeticiones
    # consecutivas, y toma la que precede a la actual.
    TARGET_RELEASE="$(grep -oE '(activate release=|deploy success release=|rollback success to=)[^[:space:]]+' "$LOGFILE" \
        | sed -E 's/.*=//' \
        | awk 'NR==1 || $0 != last { print; last = $0 }' \
        | awk -v cur="$CURRENT_RELEASE" '$0 == cur { print prev; exit } { prev = $0 }')"
    [[ -n "$TARGET_RELEASE" ]] || fail "no hay una release previa registrada en el historial de $SERVICE antes de $CURRENT_RELEASE"
fi

[[ "$TARGET_RELEASE" != "$CURRENT_RELEASE" ]] || fail "la release destino ($TARGET_RELEASE) es la misma que la activa"
[[ -d "$BASE/releases/$TARGET_RELEASE" ]] || fail "no existe la release destino: $TARGET_RELEASE"

info "revirtiendo $SERVICE: $CURRENT_RELEASE -> $TARGET_RELEASE"
log_release_event "$SERVICE" "rollback start from=$CURRENT_RELEASE to=$TARGET_RELEASE"

set +e
"$SCRIPT_DIR/activate-release.sh" "$SERVICE" "$TARGET_RELEASE" \
    && "$SCRIPT_DIR/restart-workers.sh" "$SERVICE" \
    && "$SCRIPT_DIR/verify-release.sh" "$SERVICE"
ROLLBACK_OK=$?
set -e

if [[ "$ROLLBACK_OK" -eq 0 ]]; then
    log_release_event "$SERVICE" "rollback success to=$TARGET_RELEASE"
    info "rollback de $SERVICE completado: release $TARGET_RELEASE activa"
else
    log_release_event "$SERVICE" "rollback verify failed to=$TARGET_RELEASE"
    fail "rollback de $SERVICE hacia $TARGET_RELEASE no quedo sano (activar/reiniciar/verificar fallo); revisar manualmente, no se intenta un segundo rollback automatico"
fi
