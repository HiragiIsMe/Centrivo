#!/bin/sh
set -e

echo "========================================"
echo "  Centrivo - Starting up..."
echo "========================================"

echo "[1/5] Waiting for MySQL at ${DB_HOST}:3306..."
MAX_TRIES=40
COUNT=0

until mysqladmin ping -h "${DB_HOST}" -u "${DB_USERNAME}" -p"${DB_PASSWORD}" --silent 2>/dev/null; do
  COUNT=$((COUNT + 1))
  if [ "$COUNT" -ge "$MAX_TRIES" ]; then
    echo "❌ ERROR: MySQL not ready after $MAX_TRIES attempts. Exiting."
    exit 1
  fi
  echo "   MySQL not ready yet... ($COUNT/$MAX_TRIES)"
  sleep 3
done
echo "✅ MySQL is ready!"

echo "[2/5] Waiting for Redis at ${REDIS_HOST}:6379..."
COUNT=0

until redis-cli -h "${REDIS_HOST}" ping 2>/dev/null | grep -q "PONG"; do
  COUNT=$((COUNT + 1))
  if [ "$COUNT" -ge 20 ]; then
    echo "❌ ERROR: Redis not ready after 20 attempts. Exiting."
    exit 1
  fi
  echo "   Redis not ready yet... ($COUNT/20)"
  sleep 2
done
echo "✅ Redis is ready!"

if [ "${RUN_MIGRATIONS}" = "true" ]; then
  echo "[3/5] Running migrations..."
  php /var/www/html/artisan migrate --force
  echo "✅ Migrations done!"
else
  echo "[3/5] Skipping migrations."
fi

echo "[4/5] Optimizing Laravel..."
php /var/www/html/artisan config:cache
php /var/www/html/artisan route:cache
php /var/www/html/artisan view:cache
echo "✅ Optimization done!"

echo "[5/5] Starting Nginx + PHP-FPM via Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf