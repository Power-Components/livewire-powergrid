#!/bin/bash

composer create-project laravel/laravel app

cat >> app/.env <<EOF
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=powergridtest
DB_USERNAME=root
DB_PASSWORD=password
EOF

php artisan key:generate

cd app && composer require livewire/livewire ^3.0

php artisan migrate:fresh --seed --force

php artisan serve
