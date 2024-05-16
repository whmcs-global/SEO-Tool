# dependencies
Laravel 11
php 8.3


# setup process
copy .env.example to .env
edit .env file and set the database connection
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve



