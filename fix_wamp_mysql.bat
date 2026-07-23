@echo off
echo == Fix MySQL WAMP ==
echo.
echo Arret des processus MySQL orphelins...
taskkill /f /im mysqld.exe 2>nul
timeout /t 3 /nobreak >nul
echo.
echo Demarrage du service MySQL...
net start wampmysqld64
echo.
sc query wampmysqld64 | findstr STATE
echo.
pause
