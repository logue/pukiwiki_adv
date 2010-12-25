@echo off
echo PukiWiki Advance auto pot file generator for plugin directory
IF "%1" == "" GOTO usage

@rem xgettext --from-code=UTF-8 -k_ -o %1.pot ../../plugin/%1.inc.php
xgettext -kT_gettext -kT_ --from-code utf-8 -o %1.pot ../../plugin/%1.inc.php -L PHP --no-wrap
echo finish.
goto end
:usage
echo USAGE : getpot [plugin_name]
echo Add gettext path before use.
echo This program running in command prompt.
pause.
:end