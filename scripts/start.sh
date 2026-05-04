#!/usr/bin/env bash
set -euo pipefail

if [ -z "${APP_KEY:-}" ]; then
  echo "APP_KEY is not set. Configure it in your deployment environment."
  exit 1
fi

mkdir -p storage/framework/{sessions,views,cache}
mkdir -p bootstrap/cache

if [ "${DB_CONNECTION:-}" = "sqlite" ]; then
  mkdir -p database
  touch database/database.sqlite
fi

php artisan migrate --force --no-interaction
php artisan config:cache
php artisan view:cache
php artisan storage:link || true

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
