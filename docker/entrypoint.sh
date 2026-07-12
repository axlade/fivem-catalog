#!/usr/bin/env bash
set -e

cd /var/www/html

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

CURRENT_KEY=$(grep -m1 '^APP_KEY=' .env | cut -d= -f2-)
if [ -z "$CURRENT_KEY" ]; then
    echo "[entrypoint] Generating APP_KEY..."
    php artisan key:generate --force
fi

echo "[entrypoint] Waiting for the database..."
until php artisan migrate --force; do
    echo "[entrypoint] Database not ready yet, retrying in 2s..."
    sleep 2
done

echo "[entrypoint] Linking storage..."
rm -f public/storage
php artisan storage:link

if [ "$DB_SEED" = "true" ] && [ ! -f storage/.seeded ]; then
    echo "[entrypoint] Seeding demo data..."
    php artisan db:seed --force
    touch storage/.seeded
fi

echo "[entrypoint] Ready."

exec "$@"
