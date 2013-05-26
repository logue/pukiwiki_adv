@echo.
@echo off
PATH=%PATH%;C:\xampp\php;..\..\..\..\..\..\php54;
cd "./php-bin/"
php -q "batch_compiler.php"
pause.
echo.