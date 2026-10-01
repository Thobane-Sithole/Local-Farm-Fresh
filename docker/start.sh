#!/bin/bash
set -e

echo "Running migrations..."
php artisan migrate --force

echo "Seeding admin user..."
php artisan db:seed --class=AdminSeeder --force

echo "Caching config, routes and views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Linking storage..."
php artisan storage:link 2>/dev/null || true

echo "Starting Apache..."
exec apache2-foreground
