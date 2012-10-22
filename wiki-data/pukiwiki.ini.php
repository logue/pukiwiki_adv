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

/////////////////////////////////////////////////
// Functionality settings

// PKWK_OPTIMISE - Ignore verbose but understandable checking and warning
//   If you end testing this PukiWiki, set '1'.
//   If you feel in trouble about this PukiWiki, set '0'.
if (! defined('PKWK_OPTIMISE'))
	define('PKWK_OPTIMISE', 0);

/////////////////////////////////////////////////
// Security settings
// 0 - 機能無効
// 1 - 強制モード
// 2 - サイト管理者以上は除く
// 3 - コンテンツ管理者以上は除く
// 4 - 登録者
// 5 - 認証者(未設定時のデフォルト)以上は除く

// 認証せずには閲覧できない
defined('PLUS_PROTECT_MODE')	or define('PLUS_PROTECT_MODE',	0); // 0,2,3,4,5

// PKWK_READONLY - Prohibits editing and maintain via WWW
//   NOTE: Counter-related functions will work now (counter, attach count, etc)
defined('PKWK_READONLY')		or define('PKWK_READONLY',		0);		// 0,1,2,3,4,5

// PKWK_SAFE_MODE - Prohibits some unsafe(but compatible) functions
defined('PKWK_SAFE_MODE')		or define('PKWK_SAFE_MODE',		0);	// 0,1,2,3,4,5

// PKWK_CREATE_PAGE - New page making is prohibited.
defined('PKWK_CREATE_PAGE')		or define('PKWK_CREATE_PAGE',	0); // 0,1,2,3,4,5

// PKWK_USE_REDIRECT - When linking outside, Referer is removed.
defined('PKWK_USE_REDIRECT')	or define('PKWK_USE_REDIRECT',	0); // 0,1

// PKWK_DISABLE_INLINE_IMAGE_FROM_URI - Disallow using inline-image-tag for URIs
//   Inline-image-tag for URIs may allow leakage of Wiki readers' information
//   (in short, 'Web bug') or external malicious CGI (looks like an image's URL)
//   attack to Wiki readers, but easy way to show images.
defined('PKWK_DISABLE_INLINE_IMAGE_FROM_URI')
								or define('PKWK_DISABLE_INLINE_IMAGE_FROM_URI', 0);

// PKWK_QUERY_STRING_MAX
//   Max length of GET method, prohibits some worm attack ASAP
//   NOTE: Keep (page-name + attach-file-name) <= PKWK_QUERY_STRING_MAX
define('PKWK_QUERY_STRING_MAX', 640); // Bytes, 0 = OFF

/////////////////////////////////////////////////
// Experimental features

// Multiline plugin hack (See BugTrack2/84)
// EXAMPLE(with a known BUG):
//   #plugin(args1,args2,...,argsN){{
//   argsN+1
//   argsN+1
//   #memo(foo)
//   argsN+1
//   }}
//   #memo(This makes '#memo(foo)' to this)
define('PKWKEXP_DISABLE_MULTILINE_PLUGIN_HACK', 0); // 1 = Disabled

/////////////////////////////////////////////////
// Language / Encoding settings
// <language>_<territory> = <ISO 639>_<ISO 3166>
// ja_JP, ko_KR, en_US, zh_TW ...
defined('DEFAULT_LANG') or define('DEFAULT_LANG', 'ja_JP');

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
defined('DEFAULT_TZ_NAME') or define('DEFAULT_TZ_NAME', 'Asia/Tokyo');

// The view on public holiday applies to installation features.
// 祝日の表示は、設置場所に準ずる (0:設置者視点, 1:閲覧者視点)
$public_holiday_guest_view = 0;

/////////////////////////////////////////////////
// Directory settings I (ended with '/', permission '777')

// You may hide these directories (from web browsers)
// by setting DATA_HOME at index.php.

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

/////////////////////////////////////////////////
// Directory settings II (ended with '/')

// Skins / Stylesheets

// Skin files (SKIN_DIR/*.skin.php) are needed at
// ./DATAHOME/SKIN_DIR from index.php.
define('SKIN_DIR',		WWW_HOME . 'skin/');

// Static image files
define('IMAGE_DIR', 	WWW_HOME . 'image/');

define('SKIN_URI',		ROOT_URI . 'skin/');
define('IMAGE_URI',		COMMON_URI . 'image/');
define('JS_URI', 		COMMON_URI . 'js/');

// THEME
// *.skin.php => SKIN_DIR or SKIN_DIR + THEME_PLUS_NAME or EXT_SKIN_DIR + THEME_PLUS_NAME
define('THEME_PLUS_NAME',  'theme/');			// SKIN_URI + THEME_PLUS_NAME

/////////////////////////////////////////////////
// Title of your Wikisite (Name this)
// Also used as RSS feed's channel name etc
$page_title = 'PukiWiki Advance';

// Specify PukiWiki Advance URL (default: auto)
//$script = './';

// Site Logo
$logo = IMAGE_DIR.'pukiwiki_adv.logo.png';

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
$modifierlink = get_script_absuri();

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
// Change default Document Type Definition

// Some web browser's bug, and / or Java apprets may needs not-Strict DTD.
// PukiWiki Adv. does not support HTML4.x.

$pkwk_dtd = PKWK_DTD_HTML_5;	// Adv. Default
//$pkwk_dtd = PKWK_DTD_XHTML_1_1;
//$pkwk_dtd = PKWK_DTD_XHTML_1_0_STRICT;
//$pkwk_dtd = PKWK_DTD_XHTML_1_0_TRANSITIONAL;

// PKWK_STRICT_XHTML
// XHTMLとして処理する場合。（一部のブログパーツやプラグインで不具合が発生する可能性があります）
defined('PKWK_STRICT_XHTML')	or define('PKWK_STRICT_XHTML', false);

// Change IE rendering mode.
// http://msdn.microsoft.com/en-us/library/cc288325(VS.85).aspx
// Note: This setting ignore when edit page for fix IE8 scrolling bug.

$x_ua_compatible = "IE=edge,chrome=1";	// Render as latest IE, if Chrome Frame installed, render as Chrome Frame.　(Default)
// $x_ua_compatible = "IE=emulateIE7";
// $x_ua_compatible = "chrome=1";	// Render as Chrome Frame
// $x_ua_compatible = "IE=edge";	// Render as latest IE

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

// LOG
require_once(add_homedir('config-log.ini.php'));

/////////////////////////////////////////////////
// TrackBack feature

// Enable Trackback
// 0: off
// 1: on
//    Only the reception of ping.
//    Ping is not transmitted by the automatic operation.
// 2: on
//    Function in the past. Automatic ping transmission.
$trackback = 1;

// Enable Trackback Auto-discovery
// Append to TrackBack RDF to body.
// Adv. does not enable this function by default for privend trackback spam.
$tb_auto_discovery = false;

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
// $whatsnew: Max number of RecentChanges
$maxshow = 60;

// $whatsdeleted: Max number of RecentDeleted
// (0 = Disabled)
$maxshow_deleted = 60;

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
$rss_description = $page_title.' RecentChanges';

/////////////////////////////////////////////////
// Backup related settings

// Enable backup
$do_backup = 1;

// When a page had been removed, remove its backup too?
$del_backup = 0;

// Bacukp interval and generation
$cycle  =   3; // Wait N hours between backup (0 = no wait)
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
require_once(add_homedir('auth.ini.php'));

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
// URIの種類によって開く動作を設定。
// "_blank"で別窓へ表示、falseを指定すると無効
$open_uri_in_new_window_opis  = '_blank';     // pukiwikiの外で同一サーバー内
$open_uri_in_new_window_opisi = false;        // pukiwikiの外で同一サーバー内(InterWikiLink)
$open_uri_in_new_window_opos  = '_blank';     // pukiwikiの外で外部サーバー
$open_uri_in_new_window_oposi = '_blank';     // pukiwikiの外で外部サーバー(InterWikiLink)
// (注意：あえて拡張しやすいようにしていますが、'_blank'以外は指定しないでください)

// User-Agent settings
require_once(add_homedir('profile.ini.php'));

// Server settings
require_once(add_homedir('server.ini.php'));

?>
