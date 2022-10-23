echo "Deploy started..."
cd /var/www/clear-portal
echo "Pulling files..."
git pull
echo "Bringing down the app..."
php artisan down
echo "Composer install..."
composer install --no-dev --prefer-dist
echo "Run migrations..."
php artisan migrate
echo "Bringing app back up..."
php artisan up
echo 'Deploy finished!'
