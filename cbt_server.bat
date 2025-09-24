@echo off
cd /d C:\xampp\htdocs\cbt
start "" cmd /k php artisan serve --host=127.0.0.1 --port=8000
