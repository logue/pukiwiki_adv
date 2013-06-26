<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// White Flow skin for PukiWiki Advance.
// Override to skin config.
//
// $Id: whiteflow.ini.php,v 1.0.0 2011/12/10 11:39:00 Logue Exp $
//
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
	'ui_theme'		=> 'smoothness',
	
	'image_dir'		=> './image/',

	// Navibar系プラグインでもアイコンを表示する
	'showicon'		=> true,

	// アドレスの代わりにパスを表示
	'topicpath'		=> true,
	
	// ロゴ設定
	'logo'=>array(
		'src'		=> IMAGE_URI.'pukiwiki_adv.logo.png',
		'alt'		=> '[PukiWiki Adv.]',
		'width'		=> '80',
		'height'	=> '80'
	),

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
/* End of file whiteflow.ini.php */
/* Location: ./webroot/skin/theme/whiteflow/whiteflow.ini.php */