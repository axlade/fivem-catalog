# syntax=docker/dockerfile:1

# ---- Frontend assets ----------------------------------------------------
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY resources resources
COPY vite.config.js postcss.config.js tailwind.config.js ./
RUN npm run build

# ---- PHP dependencies ----------------------------------------------------
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-interaction \
    --optimize-autoloader \
    --ignore-platform-reqs

# fakerphp/faker is dev-only but is used by the model factories that back the
# optional DB_SEED=true demo-data convenience — install it alone rather than
# pulling in the rest of the dev toolchain (phpunit, sail, pint, ...).
RUN composer require fakerphp/faker --update-no-dev --no-interaction --no-scripts --ignore-platform-reqs

# ---- Application image ----------------------------------------------------
FROM php:8.3-fpm-alpine AS app

RUN apk add --no-cache \
        nginx \
        supervisor \
        bash \
        libzip \
        oniguruma \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        libzip-dev \
        oniguruma-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip bcmath pcntl exif \
    && apk del .build-deps \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-fivem-catalog.ini
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /var/www/html

COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

RUN php artisan package:discover --ansi

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]
CMD ["supervisord", "-c", "/etc/supervisor/supervisord.conf"]
