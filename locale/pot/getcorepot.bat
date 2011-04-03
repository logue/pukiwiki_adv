@echo off
set PATH=%PATH%;"C:\Program Files (x86)\Poedit\bin"
echo PukiWiki Advance auto pot file generator for lib directory.
echo Add gettext path before use.
xgettext -kT_ --from-code utf-8 -o pukiwiki.pot ../../lib/*.php -L PHP --no-wrap --package-name="PukiWiki Advance" --package-name=1.0 --msgid-bugs-address=logue@hotmail.co.jp --copyright-holder="(c)2010-2011 PukiWiki Advance Developers Team"
echo Finish.
pause.