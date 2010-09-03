@echo off
echo PukiWiki Advance auto pot file generator for lib directory.
echo Add gettext path before use.
xgettext -k_ -o pukiwiki.pot ../../lib/*.php
pause.