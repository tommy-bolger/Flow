@echo off

rem -------------------------------------------------------------
rem  Flow CLI framework command line script for Windows.
rem  This is the bootstrap script for running Flow CLI on Windows.
rem -------------------------------------------------------------

@setlocal

set BIN_PATH=%~dp0

if "%PHP_COMMAND%" == "" set PHP_COMMAND=php.exe

"%PHP_COMMAND%" "%BIN_PATH%flow_cli.php" %*

@endlocal