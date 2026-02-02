#!/bin/sh
set -e

echo "🚀 Starting application setup..."

# Wait for database to be ready
echo "⏳ Waiting for database connection..."
while ! php artisan db:monitor --databases=mysql > /dev/null 2>&1; do
    echo "Database not ready, waiting..."
    sleep 2
done
echo "✅ Database is ready!"

# Run migrations
echo "📦 Running migrations..."
php artisan migrate --force

# Clear and cache config/routes/views for production
if [ "$APP_ENV" = "production" ]; then
    echo "🔧 Caching configuration for production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

# Create storage link if not exists
if [ ! -L "public/storage" ]; then
    echo "🔗 Creating storage link..."
    php artisan storage:link
fi

# Set correct permissions
echo "🔐 Setting permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "✅ Application setup complete!"

# Execute the main command
exec "$@"
