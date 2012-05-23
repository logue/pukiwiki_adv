<?php
/////////////////////////////////////////////////
// PukiPlus - Yet another WikiWikiWeb clone.
//
// PukiWiki Plus! skin for PukiWiki Advance.
// Override to skin config.
//
// $Id: default.skin.php,v 1.0.2 2011/09/11 22:55:00 Logue Exp $
//
global $_SKIN, $link_tags, $js_tags;

$_SKIN = array(
	// Navibar系プラグインでもアイコンを表示する
	'showicon'		=> false,

	// アドレスの代わりにパスを表示
	'topicpath'		=> true,
	
	// ロゴ設定
	'logo'=>array(
		'src'		=> IMAGE_URI.'pukiwiki_adv.logo.png',
		'alt'		=> '[PukiWiki Adv.]',
		'width'		=> '80',
		'height'	=> '80'
	),

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

// 読み込むスタイルシート

$link_tags[] = array('rel'=>'stylesheet','href'=>SKIN_URI.THEME_PLUS_NAME.PLUS_THEME.'/css/squawker.css.php?base=' . urlencode(IMAGE_URI));

// 読み込むスクリプト
$js_tags[] = array('type'=>'text/javascript', 'src'=>SKIN_URI.THEME_PLUS_NAME.PLUS_THEME.'/squawker.js.php');
?>