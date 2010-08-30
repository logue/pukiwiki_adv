<?php
/////////////////////////////////////////////////
// PukiPlus - Yet another WikiWikiWeb clone.
//
// PukiWiki Plus! skin for PukiWiki Advance.
// Override to skin config.
//
// $Id: default.skin.php,v 1.0.0 2010/08/16 20:44:00 Logue Exp $
//
global $ui_theme, $showicon, $link_tags,$js_tags, $_SKIN;
/*
UI Themes
jQuery(jQuery UI): 
	base, black-tie, blitzer, cupertino, dark-hive, dot-luv, eggplant, excite-bike, flick, hot-sneaks
	humanity, le-frog, mint-choc, overcast, pepper-grinder, redmond, smoothness, south-street,
	start, sunny, swanky-purse, trontastic, ui-darkness, ui-lightness, vader

see also
http://www.devcurry.com/2010/05/latest-jquery-and-jquery-ui-theme-links.html
http://jqueryui.com/themeroller/
*/
$ui_theme = 'redmond';

// $site_titleimage = $theme_path.'your.site.logo.png';
$_SKIN = array(
	'logo'=>IMAGE_URI.'pukiwiki_adv.logo.png',
	'logo_alt'=>'[PukiWiki Adv.]',
	'logo_w'=>'80',
	'logo_h'=>'80'
);

// 読み込むスタイルシート
$link_tags[] = array('rel'=>'stylesheet','href'=>SKIN_URI.'scripts.css.php', 'media'=>'screen');
$link_tags[] = array('rel'=>'stylesheet','href'=>SKIN_URI.'iconset/default_iconset.css.php', 'media'=>'screen');
$link_tags[] = array('rel'=>'stylesheet','href'=>SKIN_URI.THEME_PLUS_NAME.PLUS_THEME.'/default.css', 'media'=>'screen');
$link_tags[] = array('rel'=>'stylesheet','href'=>SKIN_URI.THEME_PLUS_NAME.PLUS_THEME.'/blue.css', 'media'=>'screen', 'id'=>'coloring');
$link_tags[] = array('rel'=>'stylesheet','href'=>SKIN_URI.THEME_PLUS_NAME.PLUS_THEME.'/print.css', 'media'=>'print');

// 読み込むスクリプト
$js_tags[] = array('type'=>'text/javascript', 'src'=>SKIN_URI.THEME_PLUS_NAME.PLUS_THEME.'/default.js');
// Navibar系プラグインでもアイコンを表示する
$showicon     = false;