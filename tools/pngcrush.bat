IF "%1" == "" GOTO full

rem ドラッグ＆ドロップ時の動作
move %1 %1.bak
pngcrush -rem alla -reduce -brute %1.bak %1
rem pngcrush -rem alla -l 9 "%1.bak" "%1"
GOTO end

:full
rem 同一ディレクトリの圧縮したpngをoutディレクトリに出力
pngcrush -rem alla -reduce -brute -d "./out" *

:end