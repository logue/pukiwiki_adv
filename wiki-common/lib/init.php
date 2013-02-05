<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: init.php,v 1.57.12 2012/12/05 17:21:00 Logue Exp $
// Copyright (C)
//   2010-2012 PukiWiki Advance Developers Team
//   2005-2009 PukiWiki Plus! Team
//   2002-2007,2009,2011 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// Init PukiWiki here
// Plus!I18N:(policy)not merge official cvs(1.44->1.45)
// Plus!NOTE:(policy)not merge official cvs(1.51->1.52) See Question/181

// PukiWiki version / Copyright / License
define('S_APPNAME', 'PukiWiki Advance');
define('S_VERSION', 'v1.1-alpha');
define('S_REVSION', '20121205');
define('S_COPYRIGHT',
	'<strong>'.S_APPNAME.' ' . S_VERSION . '</strong>' .
	' Copyright &#169; 2010-2012' .
	' <a href="http://pukiwiki.logue.be/" rel="external">PukiWiki Advance Developers Team</a>.<br />' .
	' Licensed under the <a href="http://www.gnu.org/licenses/gpl-2.0.html" rel="external">GPLv2</a>.' .
	' Based on <a href="http://pukiwiki.cafelounge.net/plus/" rel="external">"PukiWiki Plus! i18n"</a>'
);

define('GENERATOR', S_APPNAME.' '.S_VERSION);

/////////////////////////////////////////////////
// Init PukiWiki Advance Enviroment variables

define('UTIME',time());
define('MUTIME',getmicrotime());

defined('DEBUG')			or define('DEBUG', false);
defined('PKWK_WARNING')		or define('PKWK_WARNING', false);
defined('ROOT_URI')			or define('ROOT_URI', dirname($_SERVER['PHP_SELF']).'/');
defined('WWW_HOME')			or define('WWW_HOME', '');
defined('COMMON_URI')		or define('COMMON_URI', ROOT_URI);

/////////////////////////////////////////////////
// Init server variables

// Compat and suppress notices
$HTTP_SERVER_VARS = array();

foreach (array('SCRIPT_NAME', 'SERVER_ADMIN', 'SERVER_NAME', 'SERVER_SOFTWARE') as $key) {
	define($key, isset($_SERVER[$key]) ? $_SERVER[$key] : '');
	unset(${$key}, $_SERVER[$key], $HTTP_SERVER_VARS[$key]);
}

define('REMOTE_ADDR', isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR']);

/////////////////////////////////////////////////
// Require INI_FILE

// dist configure
/*
$config_dist = array(
	'security' => array(
		'optimize' => 0,
		'protect_mode' => false,
		'readonly' => false,
		'safemode' => false,
		'disable_create_page' => false,
		'use_redirect' => false,
		'disable_inline_image_from_uri' => false,
		'max_query_string' => 640
	),
	'locale'=> array(
		'default_language' => 'ja_JP',
		'default_timezone' => 'Asia/Tokyo',
		'use_local_time' => false,
		'conside_level' => 2,
		'public_holiday_guest_view' => 0
	),
	'special_pages' = array(
		'default'       => 'FrontPage',
		'recent'        => 'RecentChanges',
		'deleted'       => 'RecentDeleted',
		'interwiki'     => 'InterWikiName',
		'alias'         => 'AutoAliasName',
		'menubar'       => 'MenuBar',
		'sidebar'       => 'SideBar',
		'navigation'    => 'Navigation',
		'glossary       => 'Glossary',
		'headarea'      => ':Header',
		'footarea'      => ':Footer',
		'protect'       => ':login'
	),
	'config' => array(
		'title'         => 'PukiWiki Advance',
		'image'         => COMMON_URI . 'image/pukiwiki.logo.png',
		'modifier'      => 'Anonymous',
		'modifier_link' => WWW_ROOT,
		'nofollow'      => false,
		'static_url'    => true,
		'url_suffix'    => '',
		'anti_spam' => array(
			'page_view'         => 0,
			'page_remote_addr'  => 0,
			'page_contents'     => 0,
			'prohibit_proxy'    => 0,
			'dnsbl'             => 1,
			'trackback'         => 1,
			'referer'           => 1,
			'multiple_post'     => 0,
			'bad-behavior'      => 0,
			'akismet'           => 0,
			'captcha'           => 0
	),
	
	

*/
define('USR_INI_FILE', add_homedir('pukiwiki.usr.ini.php'));
$read_usr_ini_file = false;
if (file_exists(USR_INI_FILE) && is_readable(USR_INI_FILE)) {
	require(USR_INI_FILE);
	$read_usr_ini_file = true;
}

define('INI_FILE',  add_homedir('pukiwiki.ini.php'));
if (! file_exists(INI_FILE) || ! is_readable(INI_FILE)) {
	die_message('File <var>'.INI_FILE.'</var> is not found.'.' (INI_FILE)' . "\n");
} else {
	require(INI_FILE);
}

if ($read_usr_ini_file) {
	require(USR_INI_FILE);
	unset($read_usr_ini_file);
}

defined('DATA_DIR')			or define('DATA_DIR',		DATA_HOME . 'wiki/'     );	// Latest wiki texts
defined('DIFF_DIR')			or define('DIFF_DIR',		DATA_HOME . 'diff/'     );	// Latest diffs
defined('BACKUP_DIR')		or define('BACKUP_DIR',		DATA_HOME . 'backup/'   );	// Backups
defined('CACHE_DIR')		or define('CACHE_DIR',		DATA_HOME . 'cache/'    );	// Some sort of caches
defined('UPLOAD_DIR')		or define('UPLOAD_DIR',		DATA_HOME . 'attach/'   );	// Attached files and logs
defined('COUNTER_DIR')		or define('COUNTER_DIR',	DATA_HOME . 'counter/'  );	// Counter plugin's counts
defined('TRACKBACK_DIR')	or define('TRACKBACK_DIR',	DATA_HOME . 'trackback/');	// TrackBack logs
defined('REFERER_DIR')		or define('REFERER_DIR',	DATA_HOME . 'trackback/');	// Referer logs
defined('LOG_DIR')			or define('LOG_DIR',		DATA_HOME . 'log/'      );	// Logging file
defined('INIT_DIR')			or define('INIT_DIR',		DATA_HOME . 'init/'     );	// Initial value (Contents)

defined('TEMP_DIR')			or define('TEMP_DIR',		SITE_HOME . 'temp/'     );	// System cache
defined('PLUGIN_DIR')		or define('PLUGIN_DIR',		SITE_HOME . 'plugin/'   );	// Plugin directory
defined('LANG_DIR')			or define('LANG_DIR',		SITE_HOME . 'locale/'   );	// Language file
defined('SITE_INIT_DIR')	or define('SITE_INIT_DIR',	SITE_HOME . 'init/'     );	// Initial value (Site)

defined('EXTEND_DIR')		or define('EXTEND_DIR',		SITE_HOME . 'extend/'   );	// Extend directory
defined('EXT_PLUGIN_DIR')	or define('EXT_PLUGIN_DIR',	EXTEND_DIR. 'plugin/'   );	// Extend Plugin directory
defined('EXT_LANG_DIR')		or define('EXT_LANG_DIR',	EXTEND_DIR. 'locale/'   );	// Extend Language file
defined('EXT_SKIN_DIR')		or define('EXT_SKIN_DIR',	EXTEND_DIR. 'skin/'     );	// Extend Skin directory

defined('SKIN_DIR')			or define('SKIN_DIR',		WWW_HOME . 'skin/'      );	// Path to Skin directory
defined('IMAGE_DIR')		or define('IMAGE_DIR',		WWW_HOME . 'image/'     );	// Path to

defined('SKIN_URI')			or define('SKIN_URI',		ROOT_URI . 'skin/'      );	// URI to Skin directory
defined('IMAGE_URI')		or define('IMAGE_URI',		COMMON_URI . 'image/'   );	// URI to Static Image
defined('JS_URI')			or define('JS_URI', 		COMMON_URI . 'js/'      );	// URI to JavaScript Libraly

defined('THEME_PLUS_NAME')	or define('THEME_PLUS_NAME',  'theme/');			// SKIN_URI + THEME_PLUS_NAME

// フレームワークのバージョン
define('JQUERY_VER',		'1.9.0');
define('JQUERY_UI_VER',		'1.10.0');
define('JQUERY_MOBILE_VER',	'1.2.0');

// ページ名やファイル名として使用できない文字（エンコード前の文字）
defined('PKWK_ILLEGAL_CHARS_PATTERN') or define('PKWK_ILLEGAL_CHARS_PATTERN', '/[%|=|&|?|#|\r|\n|\0|\@|\t|;|\$|+|\\|\[|\]|\||^|{|}]/');

// アップロード進捗状況のセッション名（PHP5.4以降のみ有効）
defined('PKWK_PROGRESS_SESSION_NAME') or define('PKWK_PROGRESS_SESSION_NAME', 'pukiwiki_progress');

// PostIDチェックをしないプラグイン
defined('PKWK_IGNOLE_POSTID_CHECK_PLUGINS') or define('PKWK_IGNOLE_POSTID_CHECK_PLUGINS', '/menu|side|header|footer|full|read|include|calendar|login/');

// PukiWiki Adv.共有データーの名前空間（Wikifirm用）
define('PKWK_CORE_NAMESPACE', 'pukiwiki_adv');

// Wikiの名前空間（セッションやキャッシュで他のWikiと名前が重複するのを防ぐため）7文字で十分だろう・・。
define('PKWK_WIKI_NAMESPACE', 'pkwk_'.substr(md5(realpath(DATA_HOME)), 0 ,7) );

// 汎用キャッシュの有効期間
defined('PKWK_CACHE_EXPIRE') or define('PKWK_CACHE_EXPIRE', 604800);	// 60*60*24*7 1week

// convert_htmlのキャッシュ名の有効期間（デフォルト無効（
defined('PKWK_HTML_CACHE_EXPIRE') or define('PKWK_HTML_CACHE_EXPIRE', 0);

// Timestamp prefix
defined('PKWK_TIMESTAMP_PREFIX')		or define('PKWK_TIMESTAMP_PREFIX', 'timestamp-');

// Load optional libraries
if (isset($notify)){ require(LIB_DIR . 'mail.php'); }	// Mail notification
if (isset($trackback)){ require(LIB_DIR . 'trackback.php'); }	// TrackBack

/////////////////////////////////////////////////
// Init grobal variables

$foot_explain = array();	// Footnotes
$related      = array();	// Related pages
$head_tags    = array();	// XHTML tags in <head></head> (Obsolete in Adv.)
$foot_tags    = array();	// XHTML tags before </body> (Obsolete in Adv.)

$info         = array();	// For debug use.

$meta_tags    = array();	// <meta />Tags
$link_tags    = array();	// <link />Tags
$js_tags      = array();	// <script></script>Tags
$js_blocks    = array();	// Inline scripts(<script>//<![CDATA[ ... //]]></script>)
$css_blocks   = array();	// Inline styleseets(<style>/*<![CDATA[*/ ... /*]]>*/</style>)
$js_vars      = array();	// JavaScript initial value.
$_SKIN        = array();

$info[] = '<a href="http://php.net/">PHP</a> <var>'.PHP_VERSION.'</var> is running as <var>'.php_sapi_name().'</var> mode. / Powerd by <var>'.getenv('SERVER_SOFTWARE').'</var>.';
$info[] = 'Using <a href="http://framework.zend.com/">Zend Framework</a> ver.<var>' . Zend\Version\Version::VERSION.'</var>.';

// Initilaize Session
$session = new Zend\Session\Container(PKWK_WIKI_NAMESPACE);

/////////////////////////////////////////////////
// Initilalize Cache
//
use Zend\Cache\StorageFactory;

// 使用するキャッシュストレージを選択
if (!isset($cache_adapter)){
	if ( class_exists('dba') ){
		$cache_adapter = 'Dba';
	}else if ( class_exists('apc') && ini_get('apc.enabled') ){
		$cache_adapter = 'Apc';
	}else if ( class_exists('Memcached') ){
		$cache_adapter = 'Memcached';
	}else{
		$cache_adapter = 'Filesystem';
	}
}

// キャッシュ
$cache = array(
	// PukiWikiのコアで使われる汎用キャッシュ
	'core' => StorageFactory::factory(array(
		'adapter'=> array(
			'name' => $cache_adapter,
			'options' => array(
				'namespace' => PKWK_CORE_NAMESPACE,
				'ttl' => PKWK_CACHE_EXPIRE,
			),
		),
		'plugins' => array(
			($cache_adapter === 'Filesystem') ? 'serializer' : null
		)
	)),
	// Wikiごと個別に使われるキャッシュ
	'wiki' => StorageFactory::factory(array(
		'adapter'=> array(
			'name' => $cache_adapter,
			'options' => array(
				// 他のWikiと競合しないようにするためDATA_HOMEのハッシュを名前空間とする
				'namespace' => ($cache_adapter === 'Filesystem') ? 'zfcache' : PKWK_WIKI_NAMESPACE,
				'cache_dir' => ($cache_adapter === 'Filesystem') ? CACHE_DIR : null
			),
		),
		'plugins' => array(
			($cache_adapter === 'Filesystem') ? 'serializer' : null
		)
	)),
	// ページ間リンクの関連付けキャッシュ
	'link' => StorageFactory::factory(array(
		'adapter'=> array(
			'name' => $cache_adapter,
			'options' => array(
				// 他のWikiと競合しないようにするためDATA_HOMEのハッシュを名前空間とする
				'namespace' => ($cache_adapter === 'Filesystem') ? 'zfcache' : PKWK_WIKI_NAMESPACE,
				'cache_dir' => ($cache_adapter === 'Filesystem') ? CACHE_DIR : null,
				'ttl' => PKWK_CACHE_EXPIRE,
			),
		),
		'plugins' => array(
			($cache_adapter === 'Filesystem') ? 'serializer' : null
		)
	)),
	// 生データーキャッシュ（配列などは使用不可）
	'raw' => StorageFactory::factory(array(
		'adapter'=>array(
			'name'=>'Filesystem',
			'options'=>array(
				'ttl'=>PKWK_CACHE_EXPIRE,
				'cache_dir'=>CACHE_DIR
			)
		)
	)),
	// HTMLキャッシュ（高負荷サイト向け）
	'html' => StorageFactory::factory(array(
		'adapter'=>array(
			'name'=>'Filesystem',
			'options'=>array(
				'ttl'=>PKWK_HTML_CACHE_EXPIRE,
				'cache_dir'=>CACHE_DIR
			)
		)
	))
);
$info[] = 'Cache system using <var>'.$cache_adapter.'</var>.';

use Zend\Db\Adapter\Adapter;

$db = array(
	'links' => new Adapter(array(
		'driver' => 'Pdo_Sqlite',
		'database' => CACHE_DIR . 'links.db'
	))
);

/////////////////////////////////////////////////
// I18N

set_language();
set_time();
require(LIB_DIR . 'public_holiday.php');

T_setlocale(LC_ALL,PO_LANG);
T_bindtextdomain(DOMAIN,LANG_DIR);
T_textdomain(DOMAIN);

/////////////////////////////////////////////////
// リソースファイルの読み込み
require(LIB_DIR . 'resource.php');
// Init encoding hint
// define('PKWK_ENCODING_HINT', isset($_LANG['encode_hint']) ? $_LANG['encode_hint'] : '');
define('PKWK_ENCODING_HINT', (isset($_LANG['encode_hint']) && $_LANG['encode_hint'] !== 'encode_hint') ? $_LANG['encode_hint'] : 'ぷ');
// unset($_LANG['encode_hint']);

/////////////////////////////////////////////////
// INI_FILE: Init $script

if (isset($script)) {
	get_script_uri($script);	// Init manually
} else {
	$script = get_script_uri();	// Init automatically
}

///////////////////////////////////////////////
// Prevent SPAM by REMOTE IP

if (SpamCheckBAN(REMOTE_ADDR)) die('Sorry, your access is prohibited.');

// Block countory via Geolocation
$country_code = '';
if (isset($_SERVER['HTTP_CF_IPCOUNTRY'])){
	// CloudFlareを使用している場合、そちらのGeolocationを読み込む
	// https://www.cloudflare.com/wiki/IP_Geolocation
	$country_code = $_SERVER['HTTP_CF_IPCOUNTRY'];
}else if (isset($_SERVER['GEOIP_COUNTRY_CODE'])){
	// サーバーが$_SERVER['GEOIP_COUNTRY_CODE']を出力している場合
	// Apache : http://dev.maxmind.com/geoip/mod_geoip2
	// nginx : http://wiki.nginx.org/HttpGeoipModule
	// cherokee : http://www.cherokee-project.com/doc/config_virtual_servers_rule_types.html
	$country_code = $_SERVER['GEOIP_COUNTRY_CODE'];
}else if (function_exists('geoip_db_avail') && geoip_db_avail(GEOIP_COUNTRY_EDITION) && function_exists('geoip_region_by_name')) {
	// それでもダメな場合は、phpのgeoip_region_by_name()からGeolocationを取得
	// http://php.net/manual/en/function.geoip-region-by-name.php
	$geoip = geoip_region_by_name(REMOTE_ADDR);
	$country_code = $geoip['country_code'];
	$info[] = (!empty($geoip['country_code']) ) ?
		'GeoIP is usable. Your country code from IP is inferred <var>'.$geoip['country_code'].'</var>.' :
		'GeoIP is NOT usable. Maybe database is not installed. Please check <a href="http://www.maxmind.com/app/installation?city=1" rel="external">GeoIP Database Installation Instructions</a>';
}else if (function_exists('apache_note')) {
	// Apacheの場合
	$country_code = apache_note('GEOIP_COUNTRY_CODE');
}

// 使用可能かをチェック
if ( isset($country_code) && !empty($country_code)) {
	$info[] = 'Your country code from IP is inferred <var>'.$country_code.'</var>.';
} else {
	$info[] = 'Seems Geolocation is not available. <var>$deny_countory</var> value and <var>$allow_countory</var> value is ignoled.';
}

/////////////////////////////////////////////////
// ディレクトリのチェック
$die = array();

foreach(array('DATA_DIR', 'DIFF_DIR', 'BACKUP_DIR', 'CACHE_DIR') as $dir){
	if (! is_writable(constant($dir)))
		$die[] = sprintf($_string,$dir);
}

/////////////////////////////////////////////////
// INI_FILE: $agents:  UserAgentの識別
$user_agent = $matches = array();

$user_agent['agent'] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
$ua = 'HTTP_USER_AGENT';
// unset(${$ua}, $_SERVER[$ua], $HTTP_SERVER_VARS[$ua], $ua);	// safety
if ( empty($user_agent['agent']) ) die();	// UAが取得できない場合は処理を中断

foreach ($agents as $agent) {
	if (preg_match($agent['pattern'], $user_agent['agent'], $matches)) {
		$user_agent = array(
			'profile'	=> isset($agent['profile']) ? $agent['profile'] : '',
			'name'		=> isset($matches[1]) ? $matches[1] : '',	// device or browser name
			'vers'		=> isset($matches[2]) ? $matches[2] : '',	// version
		);
		break;
	}
}
unset($agents, $matches);
//var_dump($user_agent);
// Profile-related init and setting
define('UA_PROFILE', isset($user_agent['profile']) ? $user_agent['profile'] : '');

define('UA_INI_FILE', add_homedir(UA_PROFILE . '.ini.php'));
if (! file_exists(UA_INI_FILE) || ! is_readable(UA_INI_FILE)) {
	die_message('UA_INI_FILE for "' . UA_PROFILE . '" not found.');
} else {
	require(UA_INI_FILE); // Also manually
}

define('UA_NAME', isset($user_agent['name']) ? $user_agent['name'] : '');
define('UA_VERS', isset($user_agent['vers']) ? $user_agent['vers'] : '');
define('UA_CSS', isset($user_agent['css']) ? $user_agent['css'] : '');
//unset($user_agent);	// Unset after reading UA_INI_FILE

// 設定ファイルの変数チェック
$temp = '';
foreach(array('rss_max', 'page_title', 'note_hr', 'related_link', 'show_passage', 'load_template_func') as $var){
	if (! isset(${$var})) $temp .= '<li>$' . $var . "</li>\n";
}
if ($temp) {
	$die[] = sprintf('The following values were not found (Maybe the old *.ini.php?): <ul>%s</ul>',$temp);
}

$temp = '';
foreach(array('LANG', 'PLUGIN_DIR') as $def){
	if (! defined($def)) $temp .= '<li>'.$def . "</li>\n";
}
if ($temp) {
	$die[] = sprintf('The following values were not definded (Maybe the old *.ini.php?): <ul>%s</ul>',$temp);
}

if($die) die_message(join("\n",$die));
unset($die, $temp);

/////////////////////////////////////////////////
// 必須のページが存在しなければ、空のファイルを作成する
foreach(array($defaultpage, $whatsnew, $interwiki) as $page){
	if (! is_page($page)) pkwk_touch_file(get_filename($page));
}

/////////////////////////////////////////////////
// 外部からくる変数のチェック
// Prohibit $_GET attack
foreach (array('msg', 'pass') as $key) {
	if (isset($_GET[$key])) die_message(sprintf(T_('Sorry, %s is already reserved.'),$key));
}

// Expire risk
unset($HTTP_GET_VARS, $HTTP_POST_VARS);	//, 'SERVER', 'ENV', 'SESSION', ...
unset($_REQUEST);	// Considered harmful

// Remove null character etc.
$_GET    = input_filter($_GET);
$_POST   = input_filter($_POST);
$_COOKIE = input_filter($_COOKIE);

// 文字コード変換 ($_POST)
// <form> で送信された文字 (ブラウザがエンコードしたデータ) のコードを変換
// POST method は常に form 経由なので、必ず変換する
//
if (isset($_POST['encode_hint']) && !empty($_POST['encode_hint'])) {
	// do_plugin_xxx() の中で、<form> に encode_hint を仕込んでいるので、
	// encode_hint を用いてコード検出する。
	// 全体を見てコード検出すると、機種依存文字や、妙なバイナリ
	// コードが混入した場合に、コード検出に失敗する恐れがある。
	$encode = mb_detect_encoding($_POST['encode_hint']);
	mb_convert_variables(SOURCE_ENCODING, $encode, $_POST);

} else if (isset($_POST['charset']) && !empty($_POST['charset'])) {
	// TrackBack Ping で指定されていることがある
	// うまくいかない場合は自動検出に切り替え
	if (mb_convert_variables(SOURCE_ENCODING,
	    $_POST['charset'], $_POST) !== $_POST['charset']) {
		mb_convert_variables(SOURCE_ENCODING, 'auto', $_POST);
	}

} else if (! empty($_POST)) {
	// 全部まとめて、自動検出／変換
	mb_convert_variables(SOURCE_ENCODING, 'auto', $_POST);
}

// 文字コード変換 ($_GET)
// GET method は form からの場合と、<a href="http://script/?key=value"> の場合がある
// <a href...> の場合は、サーバーが rawurlencode しているので、コード変換は不要
if (isset($_GET['encode_hint']) && empty($_GET['encode_hint']))
{
	// form 経由の場合は、ブラウザがエンコードしているので、コード検出・変換が必要。
	// encode_hint が含まれているはずなので、それを見て、コード検出した後、変換する。
	// 理由は、post と同様
	$encode = mb_detect_encoding($_GET['encode_hint']);
	mb_convert_variables(SOURCE_ENCODING, $encode, $_GET);
}

/////////////////////////////////////////////////
// QUERY_STRINGを取得
$arg = '';
if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
	$arg = $_SERVER['QUERY_STRING'];
//} else if (array_key_exists('PATH_INFO',$_SERVER) and !empty($_SERVER['PATH_INFO']) ) {
//	$arg = preg_replace("/^\/*(.+)\/*$/","$1",$_SERVER['PATH_INFO']);
} else if (isset($_SERVER['argv']) && ! empty($_SERVER['argv'])) {
	$arg = $_SERVER['argv'][0];
}

if (PKWK_QUERY_STRING_MAX && strlen($arg) > PKWK_QUERY_STRING_MAX) {
	// Something nasty attack?
	die_message(_('Query string is too long.'));
}
$arg = str_replace('+','%20',input_filter($arg)); // \0 除去
// for QA/250

// unset QUERY_STRINGs
//foreach (array('QUERY_STRING', 'argv', 'argc') as $key) {
// For OpenID Lib (use QUERY_STRING).
if (DEBUG){
	foreach (array('argv', 'argc') as $key) {
		unset(${$key}, $_SERVER[$key], $HTTP_SERVER_VARS[$key]);
	}
	// $_SERVER['REQUEST_URI'] is used at func.php NOW
	unset($REQUEST_URI, $HTTP_SERVER_VARS['REQUEST_URI']);
}

// mb_convert_variablesのバグ(?)対策: 配列で渡さないと落ちる
$args = array($arg);
mb_convert_variables(SOURCE_ENCODING, 'auto', $args);
$arg = $args[0];
/////////////////////////////////////////////////
// QUERY_STRINGを分解してコード変換し、$_GET に上書き

// URI を urlencode せずに入力した場合に対処する
$matches = array();
foreach (explode('&', $arg) as $key_and_value) {
	if (preg_match('/^([^=]+)=(.+)/', $key_and_value, $matches) &&
	    (mb_detect_encoding($matches[2]) !== 'ASCII' || $matches[1] === 'pukiwiki')) {
		$_GET[$matches[1]] = $matches[2];
	}
}
unset($matches);

/////////////////////////////////////////////////
// GET, POST, COOKIE
$get    = & $_GET;
$post   = & $_POST;
$cookie = & $_COOKIE;

// GET + POST = $vars
if (empty($_POST)) {
	$method = 'GET';
	$vars = & $_GET;  // Major pattern: Read-only access via GET
} else if (empty($_GET)) {
	$method = 'POST';
	$vars = & $_POST; // Minor pattern: Write access via POST etc.
} else {
	$method = 'GET and POST';
	$vars = array_merge($_GET, $_POST); // Considered reliable than $_REQUEST
}

// 入力チェック: 'cmd=' prohibits nasty 'plugin='
if (isset($vars['plugin']))
	die( T_( 'plugin= is obsoleted.' ) );

// 整形: page, strip_bracket()
if (isset($vars['page'])) {
	$page = $get['page'] = $post['page'] = $vars['page']  = strip_bracket($vars['page']);
} else {
	$page = $get['page'] = $post['page'] = $vars['page'] = null;
}

// 入力チェック: cmdの文字列は英数字以外ありえない
if ( isset($vars['cmd']) && !preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $vars['cmd']) !== FALSE){
	unset($get['cmd'], $post['cmd'], $vars['cmd']);
}

// 整形: msg, 改行を取り除く
if (isset($vars['msg'])) {
	$get['msg'] = $post['msg'] = $vars['msg'] = str_replace("\r", '', $vars['msg']);
}

// TrackBack Ping
if ( isset($vars['tb_id']) && !empty($vars['tb_id']) ) {
	$get['cmd'] = $post['cmd'] = $vars['cmd'] = 'tb';
}


if (! isset($vars['cmd']) ){
	$get['cmd']  = $post['cmd']  = $vars['cmd']  = 'read';

	$argx = explode('&', $arg);
	$arg = is_array($argx) ? $argx[0]:$argx;
	if ($arg == '') $arg = $defaultpage;
	$arg = rawurldecode($arg);
	$arg = strip_bracket($arg);
	$arg = input_filter($arg);
	$get['page'] = $post['page'] = $vars['page'] = $arg;
	unset($vars[$arg]);
}
// HTTP_X_REQUESTED_WITHヘッダーで、ajaxによるリクエストかを判別
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' || isset($vars['ajax']));

/////////////////////////////////////////////////
// Spam filtering

if ($spam && $method !== 'GET') {
	if (isset($country_code) && $country_code !== false){
		if (isset($deny_countory) && !empty($deny_countory)) {
			if (in_array($country_code, $deny_countory)) {
				die('Sorry, access from your country('.$geoip['country_code'].') is prohibited.');
				exit;
			}
		}
		if (isset($allow_countory) && !empty($allow_countory)) {
			if (!in_array($country_code, $allow_countory)) {
				die('Sorry, access from your country('.$geoip['country_code'].') is prohibited.');
				exit;
			}
		}
	}

	// Adjustment
	$_spam   = ! empty($spam);
	$_cmd = strtolower($cmd);
	$_ignore = array();
	switch ($_cmd) {
		case 'search': $_spam = FALSE; break;
		case 'edit':
			$_page = & $page;
			if (isset($vars['add']) && $vars['add']) {
				$_cmd = 'add';
			} else {
				$_ignore[] = 'original';
			}
			break;
		case 'bugtrack': $_page = & $vars['base'];  break;
		case 'tracker':  $_page = & $vars['_base']; break;
		case 'read':     $_page = & $page;  break;
		default: $_page = & $refer; break;
	}

	if ($_spam) {
		require(LIB_DIR . 'spam.php');

		if (isset($spam['method'][$_cmd])) {
			$_method = & $spam['method'][$_cmd];
		} else if (isset($spam['method']['_default'])) {
			$_method = & $spam['method']['_default'];
		} else {
			$_method = array();
		}
		$exitmode = isset($spam['exitmode']) ? $spam['exitmode'] : null;

		// Hack: ignorance several keys
		if ($_ignore) {
			$_vars = array();
			foreach($vars as $key => $value) {
				$_vars[$key] = & $vars[$key];
			}
			foreach($_ignore as $key) {
				unset($_vars[$key]);
			}
		} else {
			$_vars = & $vars;
		}
		pkwk_spamfilter($method . ' to #' . $_cmd, $_page, $_vars, $_method, $exitmode);
	}
}

/////////////////////////////////////////////////
// 初期設定($WikiName,$BracketNameなど)
// $WikiName = '[A-Z][a-z]+(?:[A-Z][a-z]+)+';
// $WikiName = '\b[A-Z][a-z]+(?:[A-Z][a-z]+)+\b';
// $WikiName = '(?<![[:alnum:]])(?:[[:upper:]][[:lower:]]+){2,}(?![[:alnum:]])';
// $WikiName = '(?<!\w)(?:[A-Z][a-z]+){2,}(?!\w)';

// BugTrack2/24対処（éなどの文字が使えないため）
$WikiPart = '[A-Z](?:[a-z]|\\xc3[\\x9f-\\xbf])+'; // \c3\9f through \c3\bf correspond to \df through \ff in ISO8859-1
$WikiName = "(?:$WikiPart(?:$WikiPart)+)(?!\w)";

// $BracketName = ':?[^\s\]#&<>":]+:?';
$BracketName = '(?!\s):?[^\r\n\t\f\[\]<>#&":]+:?(?<!\s)';

// InterWiki
$InterWikiName = '(\[\[)?((?:(?!\s|:|\]\]).)+):(.+)(?(1)\]\])';

// 注釈
$NotePattern = '/\(\(((?:(?>(?:(?!\(\()(?!\)\)(?:[^\)]|$)).)+)|(?R))*)\)\)/ex';

/////////////////////////////////////////////////
// 初期設定(ユーザ定義ルール読み込み)
require(add_homedir('rules.ini.php'));

/////////////////////////////////////////////////
// 初期設定(その他のグローバル変数)

// 現在時刻
$now = format_date(UTIME);

// 日時置換ルールを$line_rulesに加える
if ($usedatetime) $line_rules = array_merge($datetime_rules, $line_rules);
unset($datetime_rules);

// フェイスマークを$line_rulesに加える
if ($usefacemark) $line_rules = array_merge($facemark_rules, $line_rules);
unset($facemark_rules);

// 実体参照パターンおよびシステムで使用するパターンを$line_rulesに加える
// XHTML5では&lt;、&gt;、&amp;、&quot;と、&apos;のみ使える。
// http://www.w3.org/TR/html5/the-xhtml-syntax.html
$line_rules = array_merge(array(
	'&amp;(#[0-9]+|#x[0-9a-f]+|(?=[a-zA-Z0-9]{2,8})(?:apos|amp|lt|gt|quot));' => '&$1;',
	"\r"          => '<br />' . "\n",	/* 行末にチルダは改行 */
), $line_rules);

//////////////////////////////////////////////////
// ajaxではない場合
// スキンデーター読み込み
defined('IS_MOBILE') or define('IS_MOBILE', false);
if (IS_MOBILE === true) {
	defined('PLUS_THEME') or define('PLUS_THEME', 'mobile');
	define('SKIN_FILE', add_skindir('mobile'));
}else{
	define('SKIN_FILE', add_skindir(PLUS_THEME));
}

if (!IS_AJAX || IS_MOBILE){
	global $auth_api, $fb;

	// JavaScriptフレームワーク設定
	// jQueryUI Official CDN
	// http://code.jquery.com/
	$pkwk_head_js[] = array('type'=>'text/javascript', 'src'=>'http://code.jquery.com/jquery-'.JQUERY_VER.'.min.js');

	if (!IS_MOBILE){
		// modernizrの設定
		// $modernizr = 'modernizr.min.js';
		$modernizr = 'js.php?file=modernizr.min';

		// jQuery UI
		$pkwk_head_js[] = array('type'=>'text/javascript', 'src'=>'http://code.jquery.com/ui/'.JQUERY_UI_VER.'/jquery-ui.min.js', 'defer'=>'defer');
		// jQuery UIのCSS
		if (isset($_SKIN['ui_theme'])){
			$link_tags[] = array(
				'rel'=>'stylesheet',
				'href'=>'http://code.jquery.com/ui/'.JQUERY_UI_VER.'/themes/'. $_SKIN['ui_theme'].'/jquery-ui.css',
				'type'=>'text/css',
				'id'=>'ui-theme'
			);
		}

		if (DEBUG === true) {
			// 読み込むsrcディレクトリ内のJavaScript
			$default_js = array(
				/* libraly */
				'tzCalculation_LocalTimeZone',

				/* Use plugins */
				'activity-indicator',
				'jquery.a-tools',
				'jquery.autosize',
				'jquery.beautyOfCode',
				'jquery.cookie',
				'jquery.form',
				'jquery.dataTables',
				'jquery.i18n',
				'jquery.jplayer',
				'jquery.lazyload',
				'jquery.query',
				'jquery.superfish',
				'jquery.tabby',
				'jquery.ui.rlightbox',

				/* MUST BE LOAD LAST */
				'skin.original'
			);
			foreach($default_js as $script_file)
				$pkwk_head_js[] = array('type'=>'text/javascript', 'src'=>JS_URI.'src/'.$script_file.'.js', 'defer'=>'defer');
				//$pkwk_head_js[] = array('type'=>'text/javascript', 'src'=>JS_URI.'js.php?file=src%2F'.$script_file);

		} else {
			//$pkwk_head_js[] = array('type'=>'text/javascript', 'src'=>JS_URI.'skin.js');
			$pkwk_head_js[] = array('type'=>'text/javascript', 'src'=>JS_URI.'js.php?file=skin', 'defer'=>'defer');
		}
	}else{
		// jquery mobileは、mobile.jsで非同期読み込み。
		$modernizr = '';
		if (DEBUG === true) {
			// 読み込むsrcディレクトリ内のJavaScript
			$default_js = array(
				/* Use plugins */
				'jquery.beautyOfCode',
				'jquery.i18n',
				'jquery.lazyload',
				'jquery.tablesorter',

				/* MUST BE LOAD LAST */
				'mobile.original'
			);
			foreach($default_js as $script_file)
				$pkwk_head_js[] = array('type'=>'text/javascript', 'src'=>JS_URI.'mobile/'.$script_file.'.js');
		} else {
			//$pkwk_head_js[] = array('type'=>'text/javascript', 'src'=>JS_URI.'mobile.js', 'defer'=>'defer');
			$pkwk_head_js[] = array('type'=>'text/javascript', 'src'=>JS_URI.'js.php?file=mobile');
		}
	}

	// DNS prefetching
	// http://html5boilerplate.com/docs/DNS-Prefetching/
	$link_tags[] = array('rel'=>'dns-prefetch',		'href'=>'//code.jquery.com');
	if (COMMON_URI !== ROOT_URI){
		$link_tags[] = array('rel'=>'dns-prefetch',		'href'=>COMMON_URI);
	}

	// JS用初期設定
	$js_init = array(
		'DEBUG'=>constant('DEBUG'),
		'DEFAULT_LANG'=>constant('DEFAULT_LANG'),
		'IMAGE_URI'=>constant('IMAGE_URI'),
		'JS_URI'=>constant('JS_URI'),
		'LANG'=>$language,
		'SCRIPT'=>get_script_absuri(),
		'SKIN_DIR'=>constant('SKIN_URI'),
		'THEME_NAME'=>constant('PLUS_THEME')
	);

	$pkwk_head_js[] = array('type'=>'text/javascript', 'src'=>JS_URI.( (DEBUG) ? 'locale.js' : 'js.php?file=locale'), 'defer'=>'defer' );

	if ( isset($auth_api['facebook']) ){
		if (extension_loaded('curl')){
			require(LIB_DIR.'facebook.php');
			$fb = new FaceBook($auth_api['facebook']);
			// FaceBook Integration
			$fb_user = $fb->getUser();

			if ($fb_user === 0) {
				// 認証されていない場合
				$url = $fb->getLoginUrl(array(
					'canvas' => 1,
					'fbconnect' => 0,
					'req_perms' => 'status_update,publish_stream' // ステータス更新とフィードへの書き込み許可
				));
				$info[] = sprintf(T_('Facebook is not authenticated or url is mismathed. Please click <a href="%s">here</a> and authenticate the application.'), str_replace('&','&amp;',$url));
			}else{
				$me = $fb->api('/me');
				try {
					// Proceed knowing you have a logged in user who's authenticated.
					$info[] = sprintf(T_('Facebook is authenticated. Welcome, %s.'), '<var>'.$me['username'].'</var>');
				} catch (FacebookApiException $e) {
					$info[] = 'Facebook Error: <samp>'.$e.'</samp>';
				}
			}
			$js_init['FACEBOOK_APPID'] = $fb->getAppId();
		}else{
			$info[] = T_('Could not to load Facebook. This function needs <code>curl</code> extention.');
		}
	}
}

if (DEBUG) {
	$exclude_plugin = array();
	if (file_exists($mecab_path)){
		$info[] = 'Mecab is enabled. (It will not work in XAMPP,but not a malfunction....)';
		if (extension_loaded('mecab')){
			$info[] = 'Mecab is module mode.';
		}else{
			$info[] = 'Mecab is stdio mode. Please concider to install <a href="https://github.com/rsky/php-mecab">php-mecab</a> in your server.';
		}
	}else{
		$info[] = 'Mecab is disabled. If you installed, please check mecab path.';
	}
}

/////////////////////////////////////////////////
// Execute Plugin.

// auth remoteip
if (isset($auth_api['remoteip']['use']) && $auth_api['remoteip']['use']) {
	if (exist_plugin_inline('remoteip')) do_plugin_inline('remoteip');
}

// WebDAV
if (is_webdav() && exist_plugin('dav')) {
	do_plugin_action('dav');
	exit;
}

// cmdが指定されていない場合は、readとみなす。
if ( !isset($vars['cmd']) ) {
	// cmdが指定されてない場合は、readとみなす。
	$cmd = $get['cmd']  = $post['cmd']  = $vars['cmd']  = 'read';

	$argx = explode('&', $arg);
	$arg = is_array($argx) ? $argx[0]:$argx;
	if (! empty($arg) ){
		$arg = rawurldecode($arg);
		$arg = strip_bracket($arg);
		$arg = input_filter($arg);
	}else{
		$arg = $defaultpage;
	}
	$get['page'] = $post['page'] = $vars['page'] = $arg;
	unset($vars[$arg]);
}

// プラグインのaction命令を実行
$cmd = strtolower($vars['cmd']);
$is_protect = auth::is_protect();
if ($is_protect) {
	$plugin_arg = '';
	if (auth::is_protect_plugin_action($cmd)) {
		if (exist_plugin_action($cmd)) do_plugin_action($cmd);
		// Location で飛ばないプラグインの場合
		$plugin_arg = $cmd;
	}
	if (exist_plugin_convert('protect')) do_plugin_convert('protect', $plugin_arg);
}
if (! exist_plugin_action($cmd)) {
	header('HTTP/1.1 501 Not Implemented');
	die_message(sprintf($_string['plugin_not_implemented'],htmlsc($cmd)));
}
$retvars = do_plugin_action($cmd);

if ($is_protect) {
 	// Location で飛ぶようなプラグインの対応のため
	// 上のアクションプラグインの実行後に処理を実施
	if (exist_plugin_convert('protect')) do_plugin_convert('protect');
	die('<var>PLUS_PROTECT_MODE</var> is set.');
}
// Set Home
$auth_key = auth::get_user_info();
if (!empty($auth_key['home']) && ($vars['page'] == $defaultpage || $vars['page'] == $auth_key['home'])){
	$base = $defaultpage = $auth_key['home'];
}else{
	$base = $vars['page'];
}
///////////////////////////////////////
// Page output
if (isset($retvars['msg']) && !empty($retvars['msg']) ) {
	$title = str_replace('$1', htmlsc(strip_bracket($base)), $retvars['msg']);
	$page  = str_replace('$1', make_search($base),  $retvars['msg']);
}else{
	$title = htmlsc(strip_bracket($base));
	$page  = make_search($base);
}

use PukiWiki\Lib\File\WikiFile;
if (isset($retvars['body']) && !empty($retvars['body'])) {
	$body = $retvars['body'];
} else {
	if (! is_page($base)) {
		$base  = $defaultpage;
		$title = htmlsc(strip_bracket($base));
		$page  = make_search($base);
	}

	$vars['cmd']  = 'read';
	$vars['page'] = $base;

	if (empty($vars['page'])) die('page is missing!');
	global $fixed_heading_edited;
	$wiki = new WikiFile($vars['page']);

	// Virtual action plugin(partedit).
	// NOTE: Check wiki source only.(*NOT* call convert_html() function)
	$lines = $wiki->source();
	while (! empty($lines)) {
		$line = array_shift($lines);
		if (preg_match("/^\#(partedit)(?:\((.*)\))?/", $line, $matches)) {
			if ( !isset($matches[2]) || empty($matches[2]) ) {
				$fixed_heading_edited = ($fixed_heading_edited ? 0:1);
			} else if ( $matches[2] == 'on') {
				$fixed_heading_edited = 1;
			} else if ( $matches[2] == 'off') {
				$fixed_heading_edited = 0;
			}
		}
	}

	$body = $wiki->render();
	$body .= ($trackback && $tb_auto_discovery) ? tb_get_rdf($base) : ''; // Add TrackBack-Ping URI
	if ($referer){
		require(LIB_DIR . 'referer.php');
		ref_save($base);
	}
	log_write('check',$vars['page']);
	log_write('browse',$vars['page']);
}

// global $always_menu_displayed;
if (arg_check('read')) $always_menu_displayed = 1;
$body_menu = $body_side = '';
if ($always_menu_displayed) {
	if (exist_plugin_convert('menu')) $body_menu = do_plugin_convert('menu');
	if (exist_plugin_convert('side')) $body_side = do_plugin_convert('side');
}

/* End of file init.php */
/* Location: ./wiki-common/lib/init.php */
