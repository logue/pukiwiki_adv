@echo off
set PATH=%PATH%;"C:\Program Files (x86)\Poedit\GettextTools\bin"

for /F %%A in ('dir /b ..\..\pot\*.pot') do ( 
	if exist %%~nA.po (
		msgmerge %%~nA.po ../../pot/%%~nA -o %%~nA.po
	) else (
		copy ..\..\pot\%%~nA.pot %%~nA.po
	)
	msgfmt -o %%~nA.mo %%~nA.po
)
pause.