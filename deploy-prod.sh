echo "Deploy started..."
cd /var/www/clear-portal
echo "Pulling files..."
git reset --hard
git pull
echo "Bringing down the app..."
php artisan down
echo "Composer install..."
composer install --no-dev --prefer-dist
echo "Clear cache..."
php artisan cache:clear
php artisan config:cache
php artisan route:cache
echo "Run migrations..."
php artisan migrate
echo "Compile assets..."
npm run prod
echo "Bringing app back up..."
php artisan up
echo 'Deploy finished!'
