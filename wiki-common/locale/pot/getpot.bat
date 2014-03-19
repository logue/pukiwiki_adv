@echo off

set PATH=%PATH%;"C:\Program Files (x86)\Poedit\GettextTools\bin"
IF "%1" == "" GOTO usage
IF "%1" == "*" GOTO all
IF "%1" == "core" GOTO core

@rem xgettext --from-code=UTF-8 -k_ -o %1.pot ../../plugin/%1.inc.php
xgettext -kT_gettext -kT_ --from-code utf-8 -o %~n1.pot ../../plugin/%~n1.inc.php -L PHP --no-wrap --package-name="PukiWiki Advance" --package-version=2.0 --msgid-bugs-address=logue@hotmail.co.jp --copyright-holder="PukiWiki Advance Developers Team"
goto end

:all
del -f *.pot
for /F %%A in ('dir /b ..\..\plugin\*.inc.php') do ( 
	xgettext -kT_gettext -kT_ --from-code utf-8 -o %%~nA ../../plugin/%%A -L PHP --no-wrap --package-name="PukiWiki Advance" --package-version=2.0 --msgid-bugs-address=logue@hotmail.co.jp --copyright-holder="PukiWiki Advance Developers Team"
)
ren *.inc *.pot
goto end

:core
xgettext -kT_gettext -kT_ --from-code utf-8 -o pukiwiki.pot ../../lib/resource.php -L PHP --no-wrap --package-name="PukiWiki Advance" --package-version=2.0 --msgid-bugs-address=logue@hotmail.co.jp --copyright-holder="PukiWiki Advance Developers Team"
goto end

:usage
echo PukiWiki Advance auto pot file generator for plugin directory
echo USAGE : 
echo   getpot [plugin_name] - Generete plugin pot file.
echo   getpot *             - Generete All plugin pot files.
echo   getpot core          - Generete core pot file.
pause.

:end
echo finish.