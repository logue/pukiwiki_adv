<?php
/**
 * PukiWiki Advance - Yet another WikiWikiWeb clone.
 *
 * 180wiki skin for PukiWiki Advance.
 * Override to skin config.
 *
 * $Id: 180wiki.ini.php,v 1.0.3 2014/02/07 18:37:00 Logue Exp $
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
	 * http://jqueryui.com/themeroller/
	 */
	'ui_theme'		=> 'black-tie',
	/**
	 * Bootswatchのテーマを選択
	 * http://bootswatch.com/
	 * ameria, cerulean, cosmo, cyborg, flatly, journal, lumen, readable, 
	 * simplex, slate, spacelab, superhero, united, yeti
	 */ 
	'bootswatch' => 'simplex',
	/**
	 * ナビバーの項目
	 * （|で区切り）
	 */
	'navibar' => 'referer',

	/**
	 * ツールバーの項目
	 */
	'toolbar' => 'reload,|,new,newsub,edit,freeze,source,diff,upload,copy,rename,|,top,list,search,recent,backup,referer,log,|,help,|,rss'
);

/* End of file 180wiki.ini.php */
/* Location: ./webroot/skin/theme/180wiki/180wiki.ini.php */