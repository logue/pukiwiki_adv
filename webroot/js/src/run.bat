@echo.
@echo off
PATH=%PATH%;C:\xampp\php;C:\Winginx\php54;
cd "./php-bin/"
php -q "batch_compiler.php"
pause.
echo.