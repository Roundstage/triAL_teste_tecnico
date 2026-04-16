#!/bin/sh
set -e

cd /var/www/html

echo "==> Installing PHP dependencies..."
composer install --no-interaction --prefer-dist --no-progress

echo "==> Installing Node dependencies..."
npm install --no-fund --no-audit

echo "==> Setting storage permissions..."
chmod -R 775 storage bootstrap/cache

echo "==> Generating app key..."
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --no-interaction
fi

echo "==> Running migrations..."
php artisan migrate --force --no-interaction

echo "==> Starting Laravel on 0.0.0.0:8000..."
exec php artisan serve --host=0.0.0.0 --port=8000
