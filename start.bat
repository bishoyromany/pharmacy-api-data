ECHO START DATA SYNC CRON 
call RunHiddenConsole.exe .\cron.bat

echo %time%
timeout 10 > NUL
echo %time%