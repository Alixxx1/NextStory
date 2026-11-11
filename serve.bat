@echo off
rem Dev server with OPcache (faster). Use 0.0.0.0 to let other cashiers on the LAN connect.
if not exist storage\framework\opcache mkdir storage\framework\opcache
cd public
php -d zend_extension=opcache -d opcache.enable=1 -d opcache.file_cache=..\storage\framework\opcache -d opcache.validate_timestamps=1 -d opcache.revalidate_freq=2 -S 0.0.0.0:8000 ..\vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php
