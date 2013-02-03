#!/bin/sh

if [ ! -e composer.phar ]; then
curl -s https://getcomposer.org/installer | php
fi

php composer.phar $*