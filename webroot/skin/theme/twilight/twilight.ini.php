<?php
/**
 * PukiWiki Advance - Yet another WikiWikiWeb clone.
 * $Id: squawker.ini.php,v 0.0.6 2014/03/12 17:54:30 Logue Exp $
 *
 * PukiWiki Adv. Bootstrap Theme
 * Copyright (C)
 *   2012-2014 PukiWiki Advance Developer Team
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
	'ui_theme'		=> 'pepper-grinder',
	/**
	 * Bootswatchのテーマを選択
	 * http://bootswatch.com/
	 * ameria, cerulean, cosmo, cyborg, flatly, journal, lumen, readable, 
	 * simplex, slate, spacelab, superhero, united, yeti
	 */ 
	'bootswatch' => 'united',
	/**
	 * Navibar系プラグインでもアイコンを表示する
	 */
	'showicon'		=> false,
	/**
	 * アドレスの代わりにパスを表示
	 */
	'topicpath'		=> false,
	/**
	 * ナビバーの項目
	 * （|で区切り）
	 */
	'navibar' => 'top,|,new,list,search,recent,help|,login',
	/**
	 * ツールバーの項目
	 */
	'toolbar' => 'reload,|,new,newsub,edit,freeze,source,diff,upload,copy,rename,|,top,list,search,recent,backup,referer,log,|,help,|,rss',
);

/* End of file squawker.ini.php */
/* Location: ./webroot/skin/theme/squawker/squawker.ini.php */