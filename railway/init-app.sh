#!/usr/bin/env sh
set -e

php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan migrate --force

if [ "${ENABLE_DEMO_SEED}" = "true" ]; then
  DEMO_CLIENT_COUNT=$(php -r '
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
echo Illuminate\Support\Facades\Schema::hasTable("clients") ? (int) App\Models\Client::count() : 0;
')

  if [ "${DEMO_CLIENT_COUNT}" = "0" ]; then
    php artisan db:seed --class=DatabaseSeeder --force
  else
    php artisan db:seed --class=RbacSeeder --force
    echo "Skipping demo seed because clients table already contains data (${DEMO_CLIENT_COUNT})."
  fi
else
  php artisan db:seed --class=RbacSeeder --force
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache
