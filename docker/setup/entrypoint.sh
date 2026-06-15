#!/bin/bash
set -e

# Docker network hostnames (override .env values for 127.0.0.1)
export DB_HOST="${DB_HOST:-postgres}"
export DB_PORT="${DB_PORT:-5432}"
export REDIS_HOST="${REDIS_HOST:-redis}"
export REDIS_PORT="${REDIS_PORT:-6379}"
export REDIS_USERNAME=""
export REDIS_PASSWORD="${REDIS_USER_PASSWORD:-${REDIS_PASSWORD:-root}}"

echo "==> php artisan config:clear"
php artisan config:clear

echo "==> php artisan route:clear"
php artisan route:clear

if [ ! -d vendor ] || [ ! -f vendor/autoload.php ]; then
  echo "==> composer install"
  composer install --no-interaction --prefer-dist --optimize-autoloader
else
  echo "==> composer install (skipped, vendor exists)"
fi

if [ ! -d node_modules ]; then
  echo "==> npm install"
  npm install --ignore-scripts
else
  echo "==> npm install (skipped, node_modules exists)"
fi

echo "==> php artisan migrate"
php artisan migrate --force

echo "==> php artisan api:token:generate"
php artisan api:token:generate

echo "==> composer run dev (wait for: Server running on [http://0.0.0.0:8000])"
exec composer run dev
