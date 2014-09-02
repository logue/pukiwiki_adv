pukiwiki-skin_twilight
======================

PukiWiki original skin "Twilight"

デフォルトスキンでは、新規作成や編集等のメニュー配置がわかりづらかったので、変更しました。  
wiki全体に関わるものは、上部メニューに設置し、  
ページごとに必要な機能は、ページ上部のメニューに設置しました。  

動作確認
-------
Mac OS X v10.7.5  
Google Chrome v25.0  
Safari v6.0  
Firefox v19.0  

使い方
------
1. [pukiwiki-skin_twilightのzipファイル](https://github.com/fuyukoma/pukiwiki-skin_twilight/archive/master.zip)をダウンロードする
2. zipファイルを解凍し、pukiwiki.css.phpとpukiwiki.skin.phpをskinフォルダ直下に配置する

### Default skinを書き換えたくない場合 ###
1. [pukiwiki-skin_twilightのzipファイル](https://github.com/fuyukoma/pukiwiki-skin_twilight/archive/master.zip)をダウンロードする  
2. 解凍後のフォルダをskinフォルダ直下に配置する  
3. pukiwikiフォルダ直下にあるpukiwiki.ini.phpを以下のように変更する  

```PHP
// Skins / Stylesheets  
//define('SKIN_DIR', 'skin/');  
define('SKIN_DIR', 'skin/pukiwiki-skin_twilight-master');  
```

ライセンス
---------
pukiwiki（GPL v2）に準じます。
