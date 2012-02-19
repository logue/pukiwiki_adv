<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: mobile.ini.php,v 0.0.2 2012/02/19 21:45:30 Logue Exp $
// 
// PukiWiki Adv. Mobile Theme
// Copyright (C)
//   2012 PukiWiki Advance Developer Team

// ------------------------------------------------------------
// Settings (define before here, if you want)
global $_SKIN, $link_tags, $js_tags;

define('IS_MOBILE',true);

// 読み込むスタイルシート
$link_tags[] = array('rel'=>'stylesheet',	'type'=>'text/css',	'href'=>SKIN_URI.THEME_PLUS_NAME.PLUS_THEME.'/mobile.css.php');

// 読み込むスクリプト
//$js_tags[] = array('type'=>'text/javascript', 'src'=>SKIN_URI.THEME_PLUS_NAME.PLUS_THEME.'/classic.js');
?>