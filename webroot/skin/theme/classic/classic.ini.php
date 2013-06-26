<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: classic.ini.php,v 2.2.7 2011/09/11 22:58:30 Logue Exp $
// 
// PukiWiki Classic Skin
// Copyright (C)
//   2010-2011 PukiWiki Advance Developer Team
//   2005-2010 Logue
//   2002-2006 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji

// ------------------------------------------------------------
// Settings (define before here, if you want)

return array(
	/**
	 * UI Themes
	 * jQuery(jQuery UI): 
	 *  base, black-tie, blitzer, cupertino, dark-hive, dot-luv, eggplant, excite-bike, flick, hot-sneaks
	 *  humanity, le-frog, mint-choc, overcast, pepper-grinder, redmond, smoothness, south-street,
	 *  start, sunny, swanky-purse, trontastic, ui-darkness, ui-lightness, vader
	 *
	 * see also
	 * http://www.devcurry.com/2010/05/latest-jquery-and-jquery-ui-theme-links.html
	 * http://jqueryui.com/themeroller/
	 */
	'ui_theme'		=> 'redmond',

	/**
	 * アドレスの代わりにパスを表示
	 */
	'topicpath'		=> true,

	/**
	 * ロゴ設定
	 */
	'logo'=>array(
		'src'       => IMAGE_URI.'pukiwiki_adv.logo.png',
		'alt'       => '[PukiWiki Adv.]',
		'width'     => '80',
		'height'    => '80'
	),

	/**
	 * ナビバープラグインでもアイコンを表示する
	 */
	'showicon' => false,

	/**
	 * ナビバーの項目
	 * （|で区切り）
	 */
	'navibar' => 'top,|,edit,freeze,diff,backup,upload,reload,|,new,list,search,recent,help,|,login',

	/**
	 * ツールバーの項目
	 */
	'toolbar' => 'reload,|,new,newsub,edit,freeze,source,diff,upload,copy,rename,|,top,list,search,recent,backup,referer,log,|,help,|,rss',

	/**
	 * 追加で読み込むJavaScript
	 */
	'js'      => array('classic.js'),

	/**
	 * 広告表示領域
	 */
	'adarea' => array(
		// ページの右上の広告表示領域
		'header' => <<<EOD
EOD
,		// ページ下部の広告表示領域
		'footer' => <<<EOD
EOD
	)
);


// 読み込むスタイルシート
$link_tags[] = array('rel'=>'stylesheet', 'type'=>'text/css', 'href'=>SKIN_URI.'scripts.css.php?base=' . urlencode(IMAGE_URI) );
$link_tags[] = array('rel'=>'stylesheet', 'type'=>'text/css', 'href'=>SKIN_URI.THEME_PLUS_NAME.PLUS_THEME.'/classic.css.php');

// 読み込むスクリプト
$js_tags[] = array('type'=>'text/javascript', 'src'=>SKIN_URI.THEME_PLUS_NAME.PLUS_THEME.'/classic.js', 'defer'=>'defer');

/* End of file classic.ini.php */
/* Location: ./webroot/skin/theme/classic/classic.ini.php */