#!/bin/sh
set -e

echo "🚀 Starting HelpDesk Pro..."

cd /var/www/html

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    echo "⚙️  Generating APP_KEY..."
    php artisan key:generate --force
fi

# Wait for database to be ready
echo "⏳ Waiting for database..."
until php artisan db:monitor --databases=mysql 2>/dev/null || \
      mysql -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" -e "SELECT 1" 2>/dev/null; do
    echo "   Database not ready, retrying in 3s..."
    sleep 3
done
echo "✅ Database connected!"

# Run migrations
echo "📦 Running migrations..."
php artisan migrate --force --seed 2>/dev/null || php artisan migrate --force

# Storage setup
echo "🔗 Setting up storage..."
php artisan storage:link --force 2>/dev/null || true

# Clear & cache config for production
echo "⚡ Optimizing..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Fix permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "✅ HelpDesk Pro siap! Akses di http://localhost:8080"
echo ""
echo "📧 Admin: admin@helpdesk.com"
echo "🔑 Password: password"
echo ""

# Start services via supervisord
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
