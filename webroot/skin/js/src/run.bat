@echo.
@echo off
PATH=%PATH%;C:\xampp\php
cd "./php-bin/"
php -q "batch_compiler.php"
#php -q "batch_yui.php"
pause.
echo.