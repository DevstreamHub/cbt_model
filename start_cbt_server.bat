@echo off
cd /d "C:\xampp\htdocs\cbt"
start "" "http://192.168.0.1:8000"
php artisan serve --host=192.168.0.1 --port=8000
pause
