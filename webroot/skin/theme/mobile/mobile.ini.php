<?php
/**
 * PukiWiki - Yet another WikiWikiWeb clone.
 * $Id: mobile.ini.php,v 0.0.5 2014/04/06 17:50:30 Logue Exp $
 * 
 * PukiWiki Adv. Mobile Theme
 * Copyright (C)
 *   2012-2014 PukiWiki Advance Developer Team
 */
defined('IS_MOBILE') or define('IS_MOBILE', true);

return array(
	/**
	 * ここは変更しないでください
	 */
	'showicon' => true,
	'ui_theme' => null,
	'default_css' => false,
	/**
	 * モバイルのテーマ
	 * default: デフォルト
	 * inverse: 白黒反転
	 */
	'mobile_theme' => 'default',
	/**
	 * Bootswatchのテーマ（inverseの時はコメントアウト）
	 */
	//'bootswatch' => 'slate',
	/**
	 * メニューで表示される項目
	 */
	'navibar' => 'top,edit,freeze,diff,backup,upload,reload,new,list,search,recent,help,login',
	/**
	 * 広告表示領域
	 */
	'adarea'	=> <<<EOD
EOD
);

/* End of file mobile.ini.php */
/* Location: ./webroot/skin/theme/mobile/mobile.ini.php */