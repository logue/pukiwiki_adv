<?php
/**
 * PukiWiki Advance - Yet another WikiWikiWeb clone.
 * $Id: classic.ini.php,v 1.6.5 2014/02/07 18:28:00 Logue Exp $
 *
 * PukiWiki Classic Skin for PukiWiki Advance
 * Copyright (C)
 *   2010-2014 PukiWiki Advance Developers Team
 *   2005-2007 Logue (LogueWiki Skin)
 *   2002-2005 PukiWiki Developers Team
 *   2001-2002 Originally written by yu-ji
 */
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
	 * Bootswatchのテーマを選択
	 * http://bootswatch.com/
	 * ameria, cerulean, cosmo, cyborg, flatly, journal, lumen, readable, 
	 * simplex, slate, spacelab, superhero, united, yeti
	 */ 
	'bootswatch' => 'lumen',
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
/* End of file classic.ini.php */
/* Location: ./webroot/skin/theme/classic/classic.ini.php */