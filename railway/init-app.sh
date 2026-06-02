#!/usr/bin/env sh
set -e

php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan migrate --force

if [ "${ENABLE_DEMO_SEED}" = "true" ]; then
  php artisan db:seed --force
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache
