<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: mobile.ini.php,v 0.0.3 2012/04/29 13:46:30 Logue Exp $
// 
// PukiWiki Adv. Mobile Theme
// Copyright (C)
//   2012 PukiWiki Advance Developer Team

// ------------------------------------------------------------
// Settings (define before here, if you want)
global $_SKIN, $link_tags, $js_tags;

$_SKIN = array(
	// 広告表示領域
	'adarea'	=> <<<EOD
EOD
);

// 読み込むスタイルシート
$link_tags[] = array('rel'=>'stylesheet','href'=>SKIN_URI.THEME_PLUS_NAME.'mobile/mobile.css.php?base=' . urlencode(IMAGE_URI) );

/* End of file mobile.ini.php */
/* Location: ./webroot/skin/theme/mobile/mobile.ini.php */