# syntax=docker/dockerfile:1
# Production image for Render (or any Docker host). PHP 8.4 + Apache; listens on $PORT (Render sets it).

# --- 1) PHP dependencies -------------------------------------------------------
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction --ignore-platform-reqs
COPY . .
RUN composer dump-autoload --optimize --no-dev --no-scripts --ignore-platform-reqs

# --- 2) Runtime ----------------------------------------------------------------
FROM php:8.4-apache
ENV PORT=10000 APP_ENV=production

RUN apt-get update && apt-get install -y --no-install-recommends \
        libpq-dev libicu-dev libzip-dev libpng-dev libjpeg62-turbo-dev libwebp-dev libfreetype6-dev libonig-dev unzip \
    && docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
    && docker-php-ext-install -j"$(nproc)" pdo_pgsql intl zip gd exif bcmath opcache pcntl \
    && a2enmod rewrite headers \
    && echo 'ServerName localhost' > /etc/apache2/conf-available/servername.conf && a2enconf servername \
    && echo 'Listen ${PORT}' > /etc/apache2/ports.conf \
    && rm -rf /var/lib/apt/lists/*

COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/zz-app.ini
COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

WORKDIR /var/www/html
COPY --from=vendor /app /var/www/html
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && cp -n .env.example .env 2>/dev/null || true
RUN php artisan package:discover --ansi && php artisan filament:assets
RUN rm -f .env && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 10000
ENTRYPOINT ["entrypoint"]
