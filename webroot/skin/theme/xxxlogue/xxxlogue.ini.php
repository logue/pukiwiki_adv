<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: xxxlogue.ini.php,v2.3.1 2010/09/11 22:59:30 Logue Exp $
// Copyright (C) 2010-2011 PukiWiki Advance Developers Team
//               2007-2010 Logue
// PukiWiki Advance xxxLogue skin
//
// Based on
//   Xu Yiyang's (http://xuyiyang.com/) Unnamed (http://xuyiyang.com/wordpress-themes/unnamed/)
//
// License: GPL v3 or (at your option) any later version
// http://www.opensource.org/licenses/gpl-3.0.html
//
// ------------------------------------------------------------
// Settings (define before here, if you want)
global $_SKIN, $link_tags, $js_tags;

return array(
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
	'ui_theme'		=> 'redmond',

	// Navibar系プラグインでもアイコンを表示する
	'showicon'		=> false,

	// アドレスの代わりにパスを表示
	'topicpath'		=> true,
	
	// 画像設置ディレクトリ（このディレクトリからの相対パス）
	'image_dir'		=> './image/',

	// 広告表示領域
	'adarea'	=> array(
		// ページの右上の広告表示領域
		'header'	=> <<<EOD
EOD
,		// ページ下部の広告表示領域
		'footer'	=> <<<EOD
EOD
	)
);

/* End of file xxxlogue.ini.php */
/* Location: ./webroot/skin/theme/whiteflow/xxxlogue.ini.php */