#!/bin/sh
composer install
php bin/console doctrine:database:create
php bin/console --no-interaction d:m:m
npm install
npm audit fix
echo "starting php-fpm"
# shellcheck disable=SC2068
exec $@