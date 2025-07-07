#!/bin/bash

set -e

echo "Starting Brand Top List Application..."

# Wait for database to be ready
echo "Waiting for database connection..."
while ! php bin/console doctrine:query:sql "SELECT 1" > /dev/null 2>&1; do
    echo "Database is unavailable - sleeping"
    sleep 2
done
echo "Database is ready!"

# Run database migrations
echo "Running database migrations..."
php bin/console doctrine:migrations:migrate --no-interaction

# Load fixtures if in dev environment
if [ "$APP_ENV" = "dev" ]; then
    echo "Loading demo data..."
    php bin/console doctrine:fixtures:load --no-interaction || true
fi

# Clear and warm up cache
echo "Clearing cache..."
php bin/console cache:clear --env=$APP_ENV
php bin/console cache:warmup --env=$APP_ENV

# Set proper permissions
chown -R www-data:www-data /var/www/html/var
chmod -R 777 /var/www/html/var

# Start Apache
echo "Starting Apache..."
service apache2 start

# Start PHP-FPM in background
echo "Starting PHP-FPM..."
php-fpm -D

echo "Application is ready!"
echo ""
echo "Access URLs:"
echo "  Frontend: http://localhost:8011"
echo "  Admin:    http://localhost:8011/admin.html"
echo "  API Docs: http://localhost:8011/api/doc"
echo ""
echo "Default Admin Credentials:"
echo "  Username: admin"
echo "  Password: admin123"
echo ""

# Keep container running by following Apache logs
echo "Following Apache logs..."
tail -f /var/log/apache2/access.log /var/log/apache2/error.log