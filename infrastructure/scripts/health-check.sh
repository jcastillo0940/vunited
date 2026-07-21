#!/usr/bin/env bash
# Verifica la salud de un servicio: release activa, socket PHP-FPM, vhost
# nginx, base de datos, Redis, worker y scheduler. Imprime una linea JSON
# (para que Ops Agent / un log-based metric la recoja, ver
# infrastructure/observability/alert-policies/service-down.yaml) y termina
# con 0 si todo esta sano, 1 si algo fallo. Nunca imprime secretos.
#
# Uso: health-check.sh <servicio>
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/lib/common.sh"

SERVICE="${1:-}"
require_service "$SERVICE"

declare -A PORT=( [web]=8081 [store]=8082 [ticketing]=8083 [payments]=8086 )
declare -A HOST=( [web]=web.veraguas.internal [store]=tienda.veraguas.internal [ticketing]=boletos.veraguas.internal [payments]=webhooks-payments.veraguas.internal )

BASE="$(service_base "$SERVICE")"
FAILURES=()

check() {
    local name="$1"; shift
    if "$@" >/dev/null 2>&1; then
        echo "  [OK] $name"
    else
        echo "  [FAIL] $name" >&2
        FAILURES+=("$name")
    fi
}

echo "== health-check: $SERVICE =="

check "release activa (current -> release valida)" \
    test -f "$BASE/current/public/index.php"

check "socket php-fpm existe" \
    test -S "$BASE/sockets/php-fpm.sock"

check "vhost nginx /healthz responde 200" \
    bash -c "curl -sk -o /dev/null -w '%{http_code}' --resolve ${HOST[$SERVICE]}:${PORT[$SERVICE]}:127.0.0.1 https://${HOST[$SERVICE]}:${PORT[$SERVICE]}/healthz | grep -q '^200$'"

check "worker systemd activo" \
    systemctl is-active --quiet "veraguas-$SERVICE-worker.service"

check "scheduler timer systemd activo" \
    systemctl is-active --quiet "veraguas-$SERVICE-scheduler.timer"

if [[ -r "$BASE/shared/.env" ]]; then
    # shellcheck disable=SC1090
    DB_HOST=$(grep -E '^DB_HOST=' "$BASE/shared/.env" | cut -d= -f2-)
    DB_PORT=$(grep -E '^DB_PORT=' "$BASE/shared/.env" | cut -d= -f2-)
    DB_DATABASE=$(grep -E '^DB_DATABASE=' "$BASE/shared/.env" | cut -d= -f2-)
    DB_USERNAME=$(grep -E '^DB_USERNAME=' "$BASE/shared/.env" | cut -d= -f2-)
    DB_PASSWORD=$(grep -E '^DB_PASSWORD=' "$BASE/shared/.env" | cut -d= -f2-)
    check "conexion a base de datos propia" \
        mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" -e "SELECT 1"

    REDIS_USERNAME=$(grep -E '^REDIS_USERNAME=' "$BASE/shared/.env" | cut -d= -f2-)
    REDIS_PASSWORD=$(grep -E '^REDIS_PASSWORD=' "$BASE/shared/.env" | cut -d= -f2-)
    check "conexion a redis propio" \
        redis-cli --user "$REDIS_USERNAME" -a "$REDIS_PASSWORD" --no-auth-warning PING
else
    echo "  [FAIL] no se pudo leer $BASE/shared/.env (permisos o no existe)" >&2
    FAILURES+=("lectura de secretos")
fi

STATUS="PASS"
[[ "${#FAILURES[@]}" -gt 0 ]] && STATUS="FAIL"

printf '{"logName":"veraguas-health-check","service":"%s","status":"%s","failures":%d,"time":"%s"}\n' \
    "$SERVICE" "$STATUS" "${#FAILURES[@]}" "$(date -Iseconds)"

[[ "$STATUS" == "PASS" ]]
