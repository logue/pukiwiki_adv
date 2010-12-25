@echo off
echo PukiWiki Advance auto pot file generator for lib directory.
echo Add gettext path before use.
xgettext -kT_gettext -kT_ --from-code utf-8 -o pukiwiki.pot ../../lib/*.php -L PHP --no-wrap
pause.