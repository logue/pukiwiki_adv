<?php
/**
 * PukiWiki Advance - Yet another WikiWikiWeb clone.
 *
 * PukiWiki Plus! skin for PukiWiki Advance.
 * Override to skin config.
 *
 * $Id: default.ini.php,v 2.0.1 2014/02/07 18:36:00 Logue Exp $
 */

return array(
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
	 * カラー
	 */
	'css' => 'blue.css',

	/**
	 * ナビバープラグインでもアイコンを表示する
	 */
	'showicon' => false,

	/**
	 * Navigationを使用する
	 */
	'use_navigation' => true,

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

/* End of file default.ini.php */
/* Location: ./webroot/skin/theme/default/default.ini.php */