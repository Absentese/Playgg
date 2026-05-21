#!/usr/bin/env sh
set -eu

echo "==> Railway pre-deploy: playgg"

php artisan migrate --force

if [ "${RAILWAY_RUN_SEED:-}" = "1" ]; then
  echo "==> Seeding database"
  php artisan db:seed --force
fi

php artisan storage:link 2>/dev/null || true

php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Pre-deploy finished"
