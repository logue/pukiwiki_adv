<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone
// $Id: pukiwiki.ini.php,v 1.149.54 2012/08/24 17:07:00 Logue Exp $
// Copyright (C)
//   2010-2012 PukiWiki Advance Developers Team
//   2005-2009 PukiWiki Plus! Team
//   2002-2007 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// PukiWiki main setting file
// Plus!NOTE:(policy)not merge official cvs(1.139->1.140)
// Plus!NOTE:(policy)not merge official cvs(1.147->1.148) See Question/181

use PukiWiki\Auth\Auth;
use PukiWiki\Router;
use PukiWiki\Utility;

/////////////////////////////////////////////////
// Functionality settings

// PKWK_OPTIMISE - Ignore verbose but understandable checking and warning
//   If you end testing this PukiWiki, set '1'.
//   If you feel in trouble about this PukiWiki, set '0'.
define('PKWK_OPTIMISE', 0);

$useemoji = true;

/*
return array(
	// スキンファイル
	'skin_file' => isset($cookie['skin_file']) ? $cookie['skin_file'] : SKIN_FILE_DEFAULT,
	// 雛形とするページの読み込みを可能にする
	'load_template' => false,
	// 関連リンクを常時表示
	'display_related' => true,
	// 添付ファイルを表示
	'display_attache' => true,
	// 未作成のページに編集用のリンクを張る
	'display_dangling_link' => true,
	// 検索ワードの色分けを行う
	'word_coloring' => true,
	// 絵文字を使用する
	'use_emoji' => true,
	// Cookieを使用しないIP
	'use_trans_sid_address = array(
	)
);
*/

/////////////////////////////////////////////////
// Security settings
// Auth::ROLE_GUEST          - 機能無効
// Auth::ROLE_FORCE          - 強制モード
// Auth::ROLE_ADMIN          - サイト管理者以上は除く
// Auth::ROLE_CONTENTS_ADMIN - コンテンツ管理者以上は除く
// Auth::ROLE_ENROLLEE       - 登録者
// Auth::ROLE_AUTH           - 認証者(未設定時のデフォルト)以上は除く

// 認証せずには閲覧できない
define('PLUS_PROTECT_MODE', Auth::ROLE_GUEST);

// PKWK_READONLY - Prohibits editing and maintain via WWW
//   NOTE: Counter-related functions will work now (counter, attach count, etc)
define('PKWK_READONLY',     Auth::ROLE_GUEST);

// PKWK_SAFE_MODE - Prohibits some unsafe(but compatible) functions
define('PKWK_SAFE_MODE',    Auth::ROLE_GUEST);

// PKWK_CREATE_PAGE - New page making is prohibited.
define('PKWK_CREATE_PAGE',  Auth::ROLE_GUEST);

// PKWK_USE_REDIRECT - When linking outside, Referer is removed.
define('PKWK_USE_REDIRECT', Auth::ROLE_GUEST);

// PKWK_QUERY_STRING_MAX
//   Max length of GET method, prohibits some worm attack ASAP
//   NOTE: Keep (page-name + attach-file-name) <= PKWK_QUERY_STRING_MAX
define('PKWK_QUERY_STRING_MAX', 640); // Bytes, 0 = OFF

/////////////////////////////////////////////////
// Language / Encoding settings
// <language>_<territory> = <ISO 639>_<ISO 3166>
// ja_JP, ko_KR, en_US, zh_TW ...
define('DEFAULT_LANG', 'ja_JP');

// It conforms at the time of server installation location (DEFAULT_LANG).
// (1: Conforming, 0: Language dependence)
// サーバ設置場所(DEFAULT_LANG)の時刻に準拠する。(1:準拠, 0:言語依存)
$use_local_time = 0;

// Effective making function switch (2 Then, it becomes a judgment of 1 and 2.)
// 0) Invalidity
// 1) Judgment with COOKIE['lang']
// 2) Judgment with HTTP_ACCEPT_LANGUAGE
// 3) Considering judgment to HTTP_USER_AGENT
// 4) Considering judgment to HTTP_ACCEPT_CHARSET
// 5) Considering judgment to REMOTE_ADDR
// 機能有効化スイッチ (2 なら、1と2の判定となる)
// 0) 無効
// 1) COOKIE['lang'] での判定
// 2) HTTP_ACCEPT_LANGUAGE での判定
// 3) HTTP_USER_AGENT までの見做し判定
// 4) HTTP_ACCEPT_CHARSET までの見做し判定
// 5) REMOTE_ADDR までの見做し判定
$language_considering_setting_level = 2;

// Please define it when two or more TimeZone such as en_US exists.
// Please refer to lib/timezone.php for the defined character string.
// en_US など、複数のタイムゾーンが存在する場合に定義して下さい。
// 定義する文字列は、lib/timezone.php を参照して下さい。
define('DEFAULT_TZ_NAME', 'Asia/Tokyo');

// The view on public holiday applies to installation features.
// 祝日の表示は、設置場所に準ずる (0:設置者視点, 1:閲覧者視点)
$public_holiday_guest_view = 0;

/////////////////////////////////////////////////
// Directory settings I (ended with '/', permission '777')

// データーの入ったディレクトリ設定です。
// 通常は変更する必要はありません。
/*
define('DATA_DIR',		DATA_HOME . 'wiki/'     );	// Latest wiki texts
define('DIFF_DIR',		DATA_HOME . 'diff/'     );	// Latest diffs
define('BACKUP_DIR',	DATA_HOME . 'backup/'   );	// Backups
define('CACHE_DIR',		DATA_HOME . 'cache/'    );	// Some sort of caches
define('UPLOAD_DIR',	DATA_HOME . 'attach/'   );	// Attached files and logs
define('COUNTER_DIR',	DATA_HOME . 'counter/'  );	// Counter plugin's counts
define('TRACKBACK_DIR',	DATA_HOME . 'trackback/');	// TrackBack logs
define('REFERER_DIR',	DATA_HOME . 'trackback/');	// Referer logs
define('LOG_DIR',		DATA_HOME . 'log/'      );	// Logging file
define('INIT_DIR',		DATA_HOME . 'init/'     );	// Initial value (Contents)

define('PLUGIN_DIR',	SITE_HOME . 'plugin/'   );	// Plugin directory
define('LANG_DIR',		SITE_HOME . 'locale/'   );	// Language file
define('SITE_INIT_DIR',	SITE_HOME . 'init/'     );	// Initial value (Site)

define('EXTEND_DIR',	SITE_HOME . 'extend/'   );	// Extend directory
define('EXT_PLUGIN_DIR',EXTEND_DIR. 'plugin/'   );	// Extend Plugin directory
define('EXT_LANG_DIR',	EXTEND_DIR. 'locale/'   );	// Extend Language file
define('EXT_SKIN_DIR',	EXTEND_DIR. 'skin/'     );	// Extend Skin directory
*/

define('SKIN_DIR',		WWW_HOME . 'skin/');

// Static image files
define('IMAGE_DIR', 	WWW_HOME . 'image/');

define('SKIN_URI',		ROOT_URI . 'skin/');
define('IMAGE_URI',		COMMON_URI . 'image/');
define('JS_URI', 		COMMON_URI . 'js/');

define('THEME_PLUS_NAME',  'theme/');


/////////////////////////////////////////////////
// Title of your Wikisite (Name this)
// Also used as RSS feed's channel name etc
$site_name = 'PukiWiki Advance';

// Specify PukiWiki Advance URL (default: auto)
//$script = './';

// Site Logo
$site_logo = IMAGE_DIR.'pukiwiki_adv.logo.png';

// Site Image (for OGP)
$site_image = IMAGE_DIR.'pukiwiki_adv.image.png';

// Shorten $script: Cut its file name (default: not cut)
$script_directory_index = 'index.php';

// absoluteURI
// $script の値に、相対URIを指定した場合に効果があります。
//
// 0: It depends on the value of $script.
// 1: The value of absoluteURI is always returned.
// ----
// 0: $script の値に準ずる
// 1: 常に、絶対URIを戻す
$absolute_uri = 1;

// Specify PukiWiki Plus! absoluteURI (Only when you set $script to relativeURI)
// $script に相対URIを指定した際に、必要であれば、絶対URIを指定して下さい。
//$script_abs = '';

// Site admin's name (CHANGE THIS)
$modifier = 'anonymous';

// Site admin's Web page (CHANGE THIS)
$modifierlink = Router::get_script_absuri();

// Default page name
$defaultpage	= 'FrontPage';		// Top / Default page
$whatsnew		= 'RecentChanges';	// Modified page list
$whatsdeleted	= 'RecentDeleted';	// Removeed page list
$interwiki		= 'InterWikiName';	// Set InterWiki definition here
$aliaspage		= 'AutoAliasName';	// Set AutoAlias definition here
$menubar		= 'MenuBar';		// Menu

$sidebar		= 'SideBar';		// Side
$navigation		= 'Navigation';		// Popupmenu Defintion here.
$glossarypage	= 'Glossary';	 	// Set Glossary definition here
$headarea		= ':Header';
$footarea		= ':Footer';
$protect		= ':login';			// Protect mode

// Google API key
// Googlemapsなどのプラグインを使用する際必要です。APIKeyは以下のアドレスから取得可能です。
// http://code.google.com/apis/ajaxsearch/signup.html
$google_api_key = '';

/////////////////////////////////////////////////
// for Access Analyze and SEO use

// Always output "nofollow,noindex" attribute
$nofollow = false; // true = Try hiding from search engines

// Static URL
// アドレスに?を使わない静的なアドレスにします。
$static_url = 0;

// URL Suffix (such as extention)
// 静的なアドレス使用時の拡張子を入れます。
// 拡張子を加えるときはhtaccessも書き換えてください。
// 詳細は、http://pukiwiki.logue.be/Technical%20Note/ReWrite
//$url_suffix = '.html';
$url_suffix = '';

// Google Webmasters Tools
// http://www.google.com/webmasters/sitemaps/
$google_site_verification = '';

// Google Analytics
// https://www.google.com/analytics/settings/?et=reset
$google_analytics = '';

// Yahoo Site Explorer ID
// http://siteexplorer.search.yahoo.com/
$yahoo_site_explorer_id = '';

// Bing Webmaster Tool
// http://www.bing.com/webmaster/
$bing_webmaster_tool = '';

/////////////////////////////////////////////////
// Blocking SPAM
$use_spam_check = array(
	'page_view'			=> 0,	// 閲覧規制（管理者指定による）
	'page_remote_addr'	=> 0,	// 書き込み端末規制（IPBL）
	'page_contents'		=> 0,	// 書き込み内容規制（DNSBL）
	'page_write_proxy'	=> 0,	// Proxy経由での書き込み規制
	'trackback'			=> 1,	// TrackBack。splogなど。（DNSBL）
	'referer'			=> 1,	// Referer SPAM（DNSBL）
	'multiple_post'		=> 0,	// 多重投稿チェック（ここを有効にすると戻るボタンによる更新ができなくなります）
	'bad-behavior'		=> 0,	// Bad Behaviorによるアンチスパム（仮実装）
	'akismet'			=> 0	// Aksmetによるアンチスパムを有効化する（別途$akismet_api_keyを指定する必要があります。）1は差分のみ、2は全て
);

/////////////////////////////////////////////////
// Spam URI insertion filtering

$spam = 0;	// 1 = On

if ($spam) {
	$spam = array();

	// Threshold and rules for insertion (default)
	$spam['method']['_default'] = array(
		'_comment'		=> '_default',
		'quantity'		=>  8,
		//'non_uniquri'	=>  3,
		'non_uniqhost'	=>  3,
		'area_anchor'	=>  0,
		'area_bbcode'	=>  0,
		'uniqhost'		=> TRUE,
		'badhost'		=> TRUE,
		'asap'			=> TRUE, // Stop as soon as possible (quick but less-info)
	);

	// For editing
	// NOTE:
	// Any thresholds may LOCK your contents by
	// "posting one URL" many times.
	// Any rules will lock contents that have NG things already.
	$spam['method']['edit'] = array(
		// Supposed_by_you(n) * Margin(1.5)
		'_comment'     => 'edit',
		//'quantity'     => 60 * 1.5,
		//'non_uniquri'  =>  5 * 1.5,
		//'non_uniqhost' => 50 * 1.5,
		//'area_anchor'  => 30 * 1.5,
		//'area_bbcode'  => 15 * 1.5,
		'uniqhost'     => TRUE,
		'badhost'      => TRUE,
		'asap'         => TRUE,
	);

	//$spam['exitmode'] = 'dump'; // Dump progress
}

// SPAM check for Posted Countory(Based on Apache+mod_geoip+GeoIP)
$allow_countory = array();
$deny_countory = array();

/////////////////////////////////////////////////
// Anti-Spam service config

// Akismet
// https://akismet.com/signup/
// server.ini.phpの設定が優先されます。
$akismet_api_key = '';

/////////////////////////////////////////////////
// Referer list feature
// 0: off
// 1: on
// 2: on
//    IGNORE is not having a look displayed.
$referer = 2;

/////////////////////////////////////////////////
// _Disable_ WikiName auto-linking
$nowikiname = 1;

/////////////////////////////////////////////////
// Symbol of not exists WikiName/BracketName
$_symbol_noexists = '?';

/////////////////////////////////////////////////
// AutoLink feature
// Automatic link to existing pages (especially helpful for non-wikiword pages, but heavy)

// Minimum length of page name
// Pukiwiki Adv. Recommended "5"
$autolink = 5; // Bytes, 0 = OFF (try 8)

/////////////////////////////////////////////////
// AutoAlias feature
// Automatic link from specified word, to specifiled URI, page or InterWiki

// Minimum length of alias "from" word
// Pukiwiki Adv. Recommended "4"
$autoalias = 4; // Bytes, 0 = OFF (try 8)

// Limit loading valid alias pairs in AutoAliasName page
$autoalias_max_words = 50; // pairs

// AutoBaseAlias - AutoAlias to each page from its basename automatically
$autobasealias = 0;

// nonlist for AutoBaseAlias
$autobasealias_nonlist = '^\:|(^|\/)template$';

/////////////////////////////////////////////////
// AutoGlossary feature
// Automatic tooltip from specified word

// Minimum length of glossary "from" word
// Pukiwiki Adv. Recommended "2"
$autoglossary = 2; // NChars, 0 = OFF

// Limit loading valid glossary pairs
$autoglossary_max_words = 50; // pairs

/////////////////////////////////////////////////
// Enable Freeze / Unfreeze feature
$function_freeze = 1;

/////////////////////////////////////////////////
// Exclude plugin for this site-policy.
// Note: This function is ignole for admin.
$exclude_plugin = array(
	'server',
	'cvscheck',
	'version',
	'versionlist',
);

/////////////////////////////////////////////////
// Exclude Link plugin.
//
// When TrackBack Ping and SPAM Check are processed,
// it is substituted for null plugin.
//
// TrackBack Ping および SPAMチェックの処理の際に、
// null プラグインに置換されます。
$exclude_link_plugin = array(
	'showrss',
	'rssreader',
);

/////////////////////////////////////////////////
// Page names can't be edit via PukiWiki
$cantedit = array( $whatsnew, $whatsdeleted );

/////////////////////////////////////////////////
// HTTP: Output Last-Modified header
// 注意：動作が軽くなりますがカウンタが回らなくなります。
$lastmod = 0;

/////////////////////////////////////////////////
// Date format
$date_format = 'Y-m-d';

// Time format
$time_format = 'H:i:s';

/////////////////////////////////////////////////
// Max number of RSS feed
$rss_max = 15;

// Description
$rss_description = $site_name.' RecentChanges';

/////////////////////////////////////////////////
// Backup related settings

// Enable backup
$do_backup = 1;

// When a page had been removed, remove its backup too?
$del_backup = 0;

// Bacukp interval and generation
$maxage = 120; // Stock latest N backups

// NOTE: $cycle x $maxage / 24 = Minimum days to lost your data
//          3   x   120   / 24 = 15

// Splitter of backup data (NOTE: Too dangerous to change)
define('PKWK_SPLITTER', '>>>>>>>>>>');


/////////////////////////////////////////////////
// Remove the first spaces from Preformatted text
$preformat_ltrim = 1;

/////////////////////////////////////////////////
// Convert linebreaks into <br />
$line_break = 0;

/////////////////////////////////////////////////
// Use date-time rules (See rules.ini.php)
$usedatetime = 1;

/////////////////////////////////////////////////
// Template setting
$auto_template_func = 1;
$auto_template_rules = array(
	'((.+)\/([^\/]+))' => '\2/template'
);
/////////////////////////////////////////////////
// Allow to use 'Do not change timestamp' checkbox
// (0:Disable, 1:For everyone,  2:Only for the administrator)
$notimeupdate = 2;

// Authentication
Utility::loadConfig('auth.ini.php');

/////////////////////////////////////////////////
// Ignore list
// Regex of ignore pages
$non_list = '^\:';

// Search ignored pages
$search_non_list = 1;

/////////////////////////////////////////////////
// Automatically add fixed heading anchor
$fixed_heading_anchor = 1;

/////////////////////////////////////////////////
// 見出しごとの編集を可能にする
//
// 見出し行の固有のアンカ自動挿入されているとき
// のみ有効です
// 0:無効
// 1:edit, 2:guiedit, 3:edit+guiedit プラグインを利用
$fixed_heading_edited = 1;

/////////////////////////////////////////////////
// ページを任意のフレームに開く時に使う設定
$use_open_uri_in_new_window  = 1;

// 同一サーバーとしてみなすホストのURI
$open_uri_in_new_window_servername = array(
	$script,
//	$_SERVER['HTTP_HOST'],
//	'localhost'
);
// (注意：あえて拡張しやすいようにしていますが、'_blank'以外は指定しないでください)

// Server settings
Utility::loadConfig('server.ini.php');