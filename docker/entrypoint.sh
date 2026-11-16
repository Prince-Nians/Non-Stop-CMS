#!/bin/bash
# Runs on every container start.
#   RUN_MIGRATIONS=true  -> php artisan migrate --force   (the backend owns the database schema)
#   RUN_SEED=true        -> php artisan db:seed --force   (creates the first admin if missing; never resets an existing password)
set -e
cd /var/www/html

# Zero-config on Render: a stable APP_KEY is derived from APP_KEY_SEED (Render can generate that value),
# and APP_URL defaults to the service's own public address.
if [ -z "${APP_KEY:-}" ] && [ -n "${APP_KEY_SEED:-}" ]; then
  export APP_KEY="$(php -r 'echo "base64:".base64_encode(hash("sha256", getenv("APP_KEY_SEED"), true));')"
fi
if [ -z "${APP_URL:-}" ] && [ -n "${RENDER_EXTERNAL_URL:-}" ]; then
  export APP_URL="$RENDER_EXTERNAL_URL"
fi

php artisan config:cache
php artisan view:cache

if [ "${PUBLIC_DISK_DRIVER:-local}" = "local" ]; then
  php artisan storage:link --force || true
fi

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
  php artisan migrate --force
fi

if [ "${RUN_SEED:-false}" = "true" ]; then
  php artisan db:seed --force
fi

chown -R www-data:www-data storage bootstrap/cache
exec apache2-foreground
