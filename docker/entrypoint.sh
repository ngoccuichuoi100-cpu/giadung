#!/bin/bash
set -e

# Update Apache port to Render's dynamic PORT environment variable
if [ -n "$PORT" ]; then
    echo "Binding Apache to port $PORT..."
    sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
    sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:$PORT>/g" /etc/apache2/sites-available/*.conf
fi

# Ensure storage directories exist with correct permissions
mkdir -p /var/www/html/storage/framework/{cache,sessions,testing,views} /var/www/html/storage/logs /var/www/html/storage/app/public
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# If SQLite is used and db file doesn't exist, create it
if [ "${DB_CONNECTION}" = "sqlite" ] || [ -z "${DB_CONNECTION}" ]; then
    mkdir -p /var/www/html/database
    touch /var/www/html/database/database.sqlite
    chown -R www-data:www-data /var/www/html/database
    chmod -R 775 /var/www/html/database
fi

# Link storage
php artisan storage:link --force || true

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force || echo "Migration skipped or failed"

# Optimize Laravel caching for production
echo "Caching configuration and routes..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "Starting Apache web server..."
exec apache2-foreground
