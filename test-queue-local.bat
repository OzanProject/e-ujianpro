@echo off
REM ============================================
REM Script Test Queue Worker - Windows Local
REM ============================================

echo ==========================================
echo   Test Queue Worker - E-Ujian System
echo ==========================================
echo.

echo [1/3] Clearing cache...
php artisan config:cache
php artisan cache:clear
echo.

echo [2/3] Starting queue worker...
echo Press Ctrl+C to stop
echo.
start cmd /k "title Queue Worker && php artisan queue:work --tries=3"
echo.

echo [3/3] Queue worker started in new window
echo.
echo ==========================================
echo   Ready to Test!
echo ==========================================
echo.
echo Next steps:
echo 1. Buka browser: http://e-ujian.test/admin/student
echo 2. Klik "Import Excel"
echo 3. Upload file dengan ^>50 siswa
echo 4. Centang "Gunakan Queue"
echo 5. Lihat progress di window Queue Worker
echo.
echo Monitor commands:
echo - php artisan queue:monitor
echo - php artisan queue:failed
echo - php artisan queue:retry all
echo.
pause
