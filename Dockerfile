FROM php:8.4-cli-bookworm

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    APP_ENV=production \
    PORT=10000

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    curl \
    nodejs \
    npm \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip bcmath xml \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN mkdir -p database storage/framework/cache/data storage/framework/sessions storage/framework/views bootstrap/cache \
    && touch database/database.sqlite \
    && chmod -R 775 storage bootstrap/cache database

RUN composer install --no-interaction --prefer-dist --optimize-autoloader
RUN npm ci && npm run build

CMD ["sh", "-lc", "php artisan config:clear && php artisan route:clear && php artisan view:clear && php artisan migrate --force && if [ \"$ENABLE_DEMO_SEED\" = \"true\" ]; then php artisan db:seed --force; fi && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]
