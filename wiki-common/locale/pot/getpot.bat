@echo off
set PATH=%PATH%;"C:\Program Files (x86)\Poedit\bin"
IF "%1" == "" GOTO usage
@rem xgettext --from-code=UTF-8 -k_ -o %1.pot ../../plugin/%1.inc.php
xgettext -kT_gettext -kT_ --from-code utf-8 -o %~n1.pot ../../plugin/%~n1.inc.php -L PHP --no-wrap
goto end

:usage
echo PukiWiki Advance auto pot file generator for plugin directory
echo USAGE : getpot [plugin_name]
pause.

:end
echo finish.