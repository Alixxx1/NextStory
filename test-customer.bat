@echo off
cd /d "%~dp0public"
del /q "%~dp0storage\app\license.dat" "%~dp0storage\app\pending.dat" 2>nul
set LICENSE_ENFORCE=true
set LICENSE_SERVER_URL=http://127.0.0.1:8078
php -S 127.0.0.1:8087 ../vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php

