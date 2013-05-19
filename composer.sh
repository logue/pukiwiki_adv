#!/bin/sh

if [ ! -e composer.phar ]; then
curl -s https://getcomposer.org/installer | php
chmod +x composer.phar
fi

if [ ! -e composer.lock ]; then
php composer.phar install
fi

php composer.phar $*