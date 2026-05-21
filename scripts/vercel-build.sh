#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/.."

echo "==> Composer install (production)"
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

if [ ! -f .env ] && [ -f .env.example ]; then
  cp .env.example .env
fi

if [ -z "${APP_KEY:-}" ]; then
  export APP_KEY="base64:$(openssl rand -base64 32)"
fi

echo "==> Laravel optimize for deploy"
php artisan config:clear
php artisan route:clear
php artisan view:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

if [ "${VERCEL_RUN_MIGRATIONS:-}" = "1" ] && [ -n "${POSTGRES_URL:-}${DATABASE_URL:-}" ]; then
  echo "==> Running migrations"
  php artisan migrate --force
fi

echo "==> Vercel build finished"
