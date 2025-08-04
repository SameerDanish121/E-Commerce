@echo off
echo Starting Laravel server at 0.0.0.0:8090...
start cmd /k "php artisan serve --host=192.168.18.33 --port=8000"

echo Starting Vite (npm run dev)...
start cmd /k "npm run dev"


