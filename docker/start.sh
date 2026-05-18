#!/bin/sh
set -e

echo "🚀 Starting Laravel Application..."

mkdir -p /var/log/php /var/log/supervisor /var/log/nginx

echo "⏳ Waiting for database..."
until php -r "new PDO('mysql:host=${DB_HOST};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}');" 2>/dev/null; do
    echo "Database not ready, waiting 3 seconds..."
    sleep 3
done
echo "✅ Database is ready!"

php artisan config:cache
php artisan route:cache
php artisan view:cache

if [ "${RUN_MIGRATIONS}" = "true" ]; then
    echo "🔄 Running migrations..."
    php artisan migrate --force
fi

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

echo "✅ Starting services..."
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf