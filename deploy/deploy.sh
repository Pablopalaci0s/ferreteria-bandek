#!/usr/bin/env bash
# Deploy de actualizaciones en el VPS. Correr desde /var/www/bandek:
#   ./deploy/deploy.sh
set -euo pipefail

echo "==> git pull"
git pull --ff-only

echo "==> composer install"
composer install --no-dev --optimize-autoloader

echo "==> npm build"
npm ci
npm run build

echo "==> migraciones"
php artisan migrate --force

echo "==> recachear config/rutas/vistas"
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> permisos"
sudo chown -R www-data:www-data storage bootstrap/cache

echo "==> reiniciar PHP-FPM"
sudo systemctl reload php8.3-fpm

echo "Listo."
