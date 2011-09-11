@echo off
set PATH=%PATH%;"C:\Program Files (x86)\Poedit\bin"
IF "%1" == "" GOTO usage
msgmerge %~n1.po ../../pot/%~n1.pot -o %~n1.po
msgfmt -o %~n1.mo %~n1.po

:usage
echo PukiWiki Advance po file updator
echo USAGE : po_update [plugin_name]
pause.
:end
echo finish.