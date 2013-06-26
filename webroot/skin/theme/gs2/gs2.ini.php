<?php
// PukiWiki - Yet another WikiWikiWeb clone.
//
// PukiWiki original skin "GS2" 1.5.3
//     by yiza < http://www.yiza.net/ >
// Config file
//
// []はデフォルト設定を表します

// Settings (define before here, if you want)
global $link_tags, $js_tags, $showicon, $_SKIN;

/////////////////////////////////////////////////
// GS2スキンの色テーマ設定
// 
// 色テーマとしては以下のものが用意されています

// - blue        青を基調とした明るい感じ
// - green       緑を基調としたかなり明るい感じ
// - red         淡い赤を基調とした落ち着いた感じ
// - sepia       淡い色を基調としたセピアな感じ
// - silver      灰色を基調とした落ち着いた感じ
// - sky         青空のように淡く広がる感じ
// - violet      淡い青紫を基調とした落ち着いた感じ
// - white       silverよりさらに白色系に近づいた
// - winter      雪が降っているような淡い色遣い
// - yellow      淡い黄色を基調とした落ち着いた感じ
// - black       黒を基調とした暗い感じ
// - neongreen   黒背景に鮮やかな緑が光る
// - neonorange  黒背景に鮮やかな橙色が光る
// - print       印刷用モノクロ
// テーマは以上から選択するか、自作したテーマを
// 指定することもできます
// 画面表示時のテーマ色を設定します [blue]
$color = 'blue';

return array(
	/////////////////////////////////////////////////
	// PukiWikiの設定
	
	// タイトル周りのナビゲーションバーを表示します
	// 注意：この設定は表示の切り替えだけで機能を無効にするわけではありません
	'show_navibar'	=> true,
	
	// ページ最下部のツールバーを表示します
	// 注意：この設定は表示の切り替えだけで機能を無効にするわけではありません
	'show_toolbar'	=> true,

	
	'color'			=> $color,

	'ui-theme' => include('gs2_color/pukiwiki_gs2_color_'.$color.'.php'),
	
	/////////////////////////////////////////////////
	// GS2スキンの各種動作設定
	// 左のメニュー部にカウンタプラグインを埋め込みます
	'counter'		=> false,
	
	// ページ右上に検索フォームを表示します
	'search_form'	=> true,
	
	// QRコードを表示
	'qrcode'		=> true,

	// Navibar系プラグインでもアイコンを表示する
	'showicon'     => false,

	// ロゴ設定
	'logo' => array(
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

/* End of file gs2.ini.php */
/* Location: ./webroot/skin/theme/gs2/gs2.ini.php */
