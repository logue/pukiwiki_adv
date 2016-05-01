<?php
/**
 * PukiWiki Advance - Yet another WikiWikiWeb clone.
 * $Id: xxxlogue.ini.php,v 2.4.6 2014/02/07 18:08:30 Logue Exp $
 * Copyright (C) 2010-2014,2014 PukiWiki Advance Developers Team
 *               2007-2010 Logue
 *
 * xxxLogue skin for PukiWiki Advance
 *
 * Based on
 *   Xu Yiyang's (http://xuyiyang.com/) Unnamed (http://xuyiyang.com/wordpress-themes/unnamed/)
 *
 * License: GPL v3 or (at your option) any later version
 * http://www.opensource.org/licenses/gpl-3..html
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
	'ui_theme'		=> 'redmond',
	/**
	 * Bootswatchのテーマを選択
	 * http://bootswatch.com/
	 * ameria, cerulean, cosmo, cyborg, flatly, journal, lumen, readable, 
	 * simplex, slate, spacelab, superhero, united, yeti
	 */ 
	'bootswatch' => false,

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