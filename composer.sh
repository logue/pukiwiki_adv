#!/bin/sh

if [ ! -e composer.phar ]; then
curl -s https://getcomposer.org/installer | php
chmod +x composer.phar
fi

php composer.phar $*