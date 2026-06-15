#!/bin/bash
set -e

export DB_HOST="${DB_HOST:-postgres}"
export DB_PORT="${DB_PORT:-5432}"
export REDIS_HOST="${REDIS_HOST:-redis}"
export REDIS_PORT="${REDIS_PORT:-6379}"
export REDIS_USERNAME=""
export REDIS_PASSWORD="${REDIS_USER_PASSWORD:-${REDIS_PASSWORD:-root}}"
export RABBITMQ_HOST="${RABBITMQ_HOST:-rabbitmq}"
export RABBITMQ_PORT="${RABBITMQ_PORT:-5672}"

echo "==> php artisan config:clear"
php artisan config:clear

if [ ! -d vendor ] || [ ! -f vendor/autoload.php ]; then
  echo "==> composer install"
  composer install --no-interaction --prefer-dist --optimize-autoloader
else
  echo "==> composer install (skipped, vendor exists)"
fi

echo "==> php artisan queue:work rabbitmq"
exec php artisan queue:work rabbitmq \
  --queue=notifications.high,notifications,notifications.low \
  --sleep=1 \
  --tries=3 \
  --timeout=90
