@ECHO OFF & setlocal EnableDelayedExpansion

d:
cd \icehr\db\backup

rmdir 1 /s /q

SET x=1
FOR /L %%i in (2, 1, 10) DO (
	set /a dir_name=%%i-!x!
	rem echo !dir_name!
	move %%i !dir_name!
)

mkdir 10

for /f "tokens=2 delims==" %%a in ('wmic OS Get localdatetime /value') do set "dt=%%a"
set "YY=%dt:~2,2%" & set "YYYY=%dt:~0,4%" & set "MM=%dt:~4,2%" & set "DD=%dt:~6,2%"
set "HH=%dt:~8,2%" & set "Min=%dt:~10,2%" & set "Sec=%dt:~12,2%"

set "datestamp=%YYYY%%MM%%DD%" & set "timestamp=%HH%%Min%%Sec%"
set "fullstamp=%YYYY%-%MM%-%DD%_%HH%-%Min%-%Sec%"
rem echo datestamp: "%datestamp%"
rem echo timestamp: "%timestamp%"
rem echo fullstamp: "%fullstamp%"

cd 10
d:\xampp\mysql\bin\mysqldump -u root -h localhost --port=3333 --databases icehr -r %fullstamp%.sql

rem pause