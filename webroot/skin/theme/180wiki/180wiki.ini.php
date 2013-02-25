<?php
/////////////////////////////////////////////////
// PukiWiki Advance - Yet another WikiWikiWeb clone.
//
// 180wiki skin for PukiWiki Advance.
// Override to skin config.
//
// $Id: 180wiki.ini.php,v 1.0.2 2012/05/24 18:08:00 Logue Exp $
//
global $_SKIN, $link_tags, $js_tags;

$_SKIN = array(
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
	'ui_theme'		=> 'black-tie',
	
	// Navibar系プラグインでもアイコンを表示する
	'showicon'		=> false
);

// 読み込むスタイルシート
$link_tags[] = array('rel'=>'stylesheet', 'type'=>'text/css', 'href'=>SKIN_URI.'scripts.css.php?base=' . urlencode(IMAGE_URI) );
$link_tags[] = array('rel'=>'stylesheet', 'type'=>'text/css', 'href'=>SKIN_URI.THEME_PLUS_NAME.PLUS_THEME.'/180wiki.css.php');

/* End of file 180wiki.ini.php */
/* Location: ./webroot/skin/theme/180wiki/180wiki.ini.php */