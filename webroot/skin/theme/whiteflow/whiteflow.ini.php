<?php
/**
 * PukiWiki Advance - Yet another WikiWikiWeb clone.
 * $Id: whiteflow.ini.php,v 1.0.1 2014/02/07 18:24:30 Logue Exp $
 *
 * White Flow skin for PukiWiki Advance.
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
	'ui_theme'		=> 'smoothness',
	/**
	 * Bootswatchのテーマを選択
	 * http://bootswatch.com/
	 * ameria, cerulean, cosmo, cyborg, flatly, journal, lumen, readable, 
	 * simplex, slate, spacelab, superhero, united, yeti
	 */ 
	'bootswatch' => 'readable',
	/**
	 * Navibar系プラグインでもアイコンを表示する
	 */
	'showicon'		=> true,
	/**
	 * アドレスの代わりにパスを表示
	 */
	'topicpath'		=> true,
	/**
	 * ロゴ設定
	 */
	'logo'=>array(
		'src'		=> IMAGE_URI.'pukiwiki_adv.logo.png',
		'alt'		=> '[PukiWiki Adv.]',
		'width'		=> '80',
		'height'	=> '80'
	),
	/**
	 * 広告表示領域
	 */
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