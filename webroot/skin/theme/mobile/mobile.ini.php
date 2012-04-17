<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: mobile.ini.php,v 0.0.2 2012/02/19 21:45:30 Logue Exp $
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
<script type="text/javascript">/*<![CDATA[*/
window.googleAfmcRequest = {
	client: 'ca-mb-pub-3377384624413528',
	format: '320x50_mb',
	output: 'HTML',
	slotname: '1144150456',
};
/*]]>*/</script>
<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_afmc_ads.js"></script>
EOD
);

// 読み込むスタイルシート
$link_tags[] = array('rel'=>'stylesheet','href'=>SKIN_URI.THEME_PLUS_NAME.'mobile/mobile.css.php?base=' . urlencode(IMAGE_URI) );
?>