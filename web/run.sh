#!/bin/sh

cd /usr/paysdufle.fr/src
git pull origin main
composer install
php ./build.php