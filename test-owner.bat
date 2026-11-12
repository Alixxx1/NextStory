@echo off
cd /d "%~dp0public"
set HQ_ENABLED=true
set LICENSE_ENFORCE=false
php -S 127.0.0.1:8078 ../vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php

