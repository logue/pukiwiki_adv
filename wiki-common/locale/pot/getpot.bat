@echo off

set PATH=%PATH%;"C:\Program Files (x86)\Poedit\bin"
IF "%1" == "" GOTO usage
IF "%1" == "*" GOTO all

@rem xgettext --from-code=UTF-8 -k_ -o %1.pot ../../plugin/%1.inc.php
xgettext -kT_gettext -kT_ --from-code utf-8 -o %~n1.pot ../../plugin/%~n1.inc.php -L PHP --no-wrap --package-name="PukiWiki Advance" --package-version=1.0 --msgid-bugs-address=logue@hotmail.co.jp
goto end

:all
del -f *.pot
for /F %%A in ('dir /b ..\..\plugin\*.inc.php') do ( 
	xgettext -kT_gettext -kT_ --from-code utf-8 -o %%~nA ../../plugin/%%A -L PHP --no-wrap --package-name="PukiWiki Advance" --package-version=1.0 --msgid-bugs-address=logue@hotmail.co.jp
)
ren *.inc *.pot
goto end

:usage
echo PukiWiki Advance auto pot file generator for plugin directory
echo USAGE : getpot [plugin_name]
pause.

:end
echo finish.