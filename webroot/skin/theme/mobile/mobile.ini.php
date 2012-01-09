<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: classic.ini.php,v 0.0.1 2012/01/08 10:06:30 Logue Exp $
// 
// PukiWiki Adv. Mobile Theme
// Copyright (C)
//   2012 PukiWiki Advance Developer Team

// ------------------------------------------------------------
// Settings (define before here, if you want)
global $_SKIN, $link_tags, $js_tags;

define('IS_MOBILE',true);

$_SKIN = array(
/*
UI Themes
jQuery(jQuery Mobile): 
	a,b,c,d,e

see also
http://jquerymobile.com/demos/1.0/docs/pages/pages-themes.html
*/
	'ui_theme'		=> 'b',
);

// 読み込むスタイルシート
$link_tags[] = array('rel'=>'stylesheet',	'type'=>'text/css',	'href'=>SKIN_URI.THEME_PLUS_NAME.PLUS_THEME.'/mobile.css.php');
$link_tags[] = array('rel'=>'stylesheet',	'type'=>'text/css',	'href'=>'http://code.jquery.com/mobile/1.0/jquery.mobile-1.0.min.css');

// 読み込むスクリプト
//$js_tags[] = array('type'=>'text/javascript', 'src'=>SKIN_URI.THEME_PLUS_NAME.PLUS_THEME.'/classic.js');
?>