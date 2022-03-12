ECHO START DATA SYNC CRON 
call RunHiddenConsole.exe .\cron.bat

ECHO Your Data Is Being Synced 
ECHO This Popup Gonna Close In 5 Seconds

timeout 5 > NUL
