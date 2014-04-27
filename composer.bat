@echo off
PATH = %PATH%;..\..\php54\;..\..\nodejs\;C:\xampp\php\;C:\Program Files (x86)\Git\bin

if not exist "composer.phar" (
	php -r "eval('?>'.file_get_contents('https://getcomposer.org/installer'));"
)

if not exist "composer.lock" (
	php composer.phar install
)

php composer.phar %*
