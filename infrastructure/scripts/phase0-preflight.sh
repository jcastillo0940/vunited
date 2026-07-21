#!/usr/bin/env bash
set -euo pipefail

APP_DIR="${1:-/var/www/veraguasunited/current}"
BASE_URL="${BASE_URL:-}"

if [[ ! -d "$APP_DIR" ]]; then
    echo "[FAIL] No existe APP_DIR: $APP_DIR" >&2
    exit 1
fi

cd "$APP_DIR"

check_command() {
    command -v "$1" >/dev/null 2>&1 || {
        echo "[FAIL] Comando requerido no disponible: $1" >&2
        exit 1
    }
}

check_env_value() {
    local key="$1"
    local expected="$2"
    local actual
    actual="$(grep -E "^${key}=" .env | tail -n 1 | cut -d= -f2- || true)"

    if [[ "$actual" != "$expected" ]]; then
        echo "[FAIL] ${key} debe ser ${expected}" >&2
        exit 1
    fi
}

check_command php
check_command git
check_command df
check_command free

[[ -f .env ]] || { echo '[FAIL] Falta .env' >&2; exit 1; }
check_env_value APP_ENV production
check_env_value APP_DEBUG false

echo '[INFO] Versiones'
php --version | head -n 1
git rev-parse --short HEAD

echo '[INFO] Capacidad'
free -h
df -h "$APP_DIR"

echo '[INFO] Servicios'
systemctl is-active apache2 || true
systemctl is-active mysql || systemctl is-active mariadb || true

echo '[INFO] Laravel'
php artisan about
php artisan route:list --path=api/v1/store

[[ -w storage ]] || { echo '[FAIL] storage no es escribible' >&2; exit 1; }
[[ -w bootstrap/cache ]] || { echo '[FAIL] bootstrap/cache no es escribible' >&2; exit 1; }

if [[ -n "$BASE_URL" ]]; then
    check_command curl
    curl --fail --silent --show-error "${BASE_URL%/}/up" >/dev/null
    echo '[OK] Health check HTTP'
else
    echo '[SKIP] BASE_URL no definido; no se ejecutó health check HTTP.'
fi

echo '[OK] Preflight productivo de solo lectura completado.'
