@echo off
del /q "%~dp0storage\app\license.dat" "%~dp0storage\app\pending.dat" 2>nul
set LICENSE_ENFORCE=true
set HQ_ENABLED=false
set LICENSE_SERVER_URL=http://127.0.0.1:8078
cd /d "%~dp0electron-app"
call npm start
