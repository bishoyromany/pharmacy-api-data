@REM This File Updates The System Automatically Using Git.
call git add .
call git stash
call git pull https://github.com/bishoyromany/pharmacy-api-data master
