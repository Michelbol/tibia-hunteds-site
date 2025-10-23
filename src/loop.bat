@echo off
:loop
php artisan world-scraper
echo Executando comando em %TIME%

REM Espera 3 segundos
timeout /t 3 >nul

goto loop
