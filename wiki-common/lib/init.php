<?php
/**
 * PukiWiki Advance - Yet another WikiWikiWeb clone.
 * $Id: init.php,v 1.57.18 2016/05/16 21:03:00 Logue Exp $
 * Copyright (C)
 *   2010-2016 PukiWiki Advance Developers Team
 *   2005-2009 PukiWiki Plus! Team
 *   2002-2007,2009,2011 PukiWiki Developers Team
 *   2001-2002 Originally written by yu-ji
 * License: GPL v2 or (at your option) any later version
 *
 * Init PukiWiki here
 */
use PukiWiki\Auth\Auth;
use PukiWiki\Lang\Lang;
use PukiWiki\Factory;
use PukiWiki\Utility;
use PukiWiki\Time;
use PukiWiki\Router;
use PukiWiki\Renderer\PluginRenderer;
use PukiWiki\File\LogFactory;
use Zend\Cache\StorageFactory;
use Zend\I18n\Translator\Translator;

// PukiWiki version / Copyright / License
define('S_APPNAME', 'PukiWiki Advance');
define('S_VERSION', 'v 2.0.3');
define('S_REVSION', '20160516');
define('S_COPYRIGHT',
	'<strong>'.S_APPNAME.' ' . S_VERSION . '</strong>' .
	' Copyright &#169; 2010-2016' .
	' <a href="http://pukiwiki.logue.be/" rel="external">PukiWiki Advance Team</a>.<br />' .
	' Licensed under the <a href="http://www.gnu.org/licenses/gpl-2.0.html" rel="external">GPLv2</a> or Later.' .
	' Based on <a href="http://pukiwiki.cafelounge.net/plus/" rel="external">"PukiWiki Plus! i18n"</a>'
);

define('GENERATOR', S_APPNAME.' '.S_VERSION);

/////////////////////////////////////////////////
// Init server variables
define('UTIME',time());
define('MUTIME',Time::getMicroTime());

foreach (array('SCRIPT_NAME', 'SERVER_ADMIN', 'SERVER_NAME', 'SERVER_SOFTWARE') as $key) {
	define($key, isset($_SERVER[$key]) ? $_SERVER[$key] : null);
	unset(${$key}, $_SERVER[$key]);
}

define('REMOTE_ADDR', isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR']);

// Load Bad-behavior
if (file_exists(VENDOR_DIR . 'bad-behavior' . DIRECTORY_SEPARATOR . 'bad-behavior-sqlite.php') ){
	require( VENDOR_DIR . 'bad-behavior' . DIRECTORY_SEPARATOR . 'bad-behavior-sqlite.php');
}

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

//Utility::loadConfig('pukiwiki.ini.php', true);
foreach (array('pukiwiki.ini.php', 'auth.ini.php', 'server.ini.php') as $file){
	$path = Utility::add_homedir($file);
	if (empty($path)) continue;
	require($path);
}
unset($file, $path);

defined('DATA_DIR')			or define('DATA_DIR',		DATA_HOME . 'wiki/'     );	// Latest wiki texts
defined('DIFF_DIR')			or define('DIFF_DIR',		DATA_HOME . 'diff/'     );	// Latest diffs
defined('BACKUP_DIR')		or define('BACKUP_DIR',		DATA_HOME . 'backup/'   );	// Backups
defined('CACHE_DIR')		or define('CACHE_DIR',		DATA_HOME . 'cache/'    );	// Some sort of caches
defined('UPLOAD_DIR')		or define('UPLOAD_DIR',		DATA_HOME . 'attach/'   );	// Attached files and logs
defined('COUNTER_DIR')		or define('COUNTER_DIR',	DATA_HOME . 'counter/'  );	// Counter plugin's counts
defined('REFERER_DIR')		or define('REFERER_DIR',	DATA_HOME . 'trackback/');	// Referer logs
defined('LOG_DIR')			or define('LOG_DIR',		DATA_HOME . 'log/'      );	// Logging file
defined('INIT_DIR')			or define('INIT_DIR',		DATA_HOME . 'init/'     );	// Initial value (Contents)

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
defined('IMAGE_URI') or define('IMAGE_URI',		COMMON_URI . 'image/');
defined('JS_URI') or define('JS_URI', 		COMMON_URI . 'js/');

defined('PKWK_OPTIMISE') or define('PKWK_OPTIMISE', 0);
defined('PLUS_PROTECT_MODE')	or define('PLUS_PROTECT_MODE',	Auth::ROLE_GUEST); // 0,2,3,4,5
defined('PKWK_READONLY')		or define('PKWK_READONLY',		Auth::ROLE_GUEST);		// 0,1,2,3,4,5
defined('PKWK_SAFE_MODE')		or define('PKWK_SAFE_MODE',		Auth::ROLE_GUEST);	// 0,1,2,3,4,5
defined('PKWK_CREATE_PAGE')		or define('PKWK_CREATE_PAGE',	Auth::ROLE_GUEST); // 0,1,2,3,4,5
defined('PKWK_USE_REDIRECT')	or define('PKWK_USE_REDIRECT',	Auth::ROLE_GUEST); // 0,1

defined('PKWK_DISABLE_INLINE_IMAGE_FROM_URI') or define('PKWK_DISABLE_INLINE_IMAGE_FROM_URI', 0);

defined('DEFAULT_TZ_NAME') or define('DEFAULT_TZ_NAME', 'Asia/Tokyo');

// アップロード進捗状況のセッション名（PHP5.4以降のみ有効）
defined('PKWK_PROGRESS_SESSION_NAME') or define('PKWK_PROGRESS_SESSION_NAME', 'pukiwiki_progress');

defined('DEFAULT_LANG') or define('DEFAULT_LANG', 'ja_JP');

// PukiWiki Adv.共有データーの名前空間（Wikifirm用）
defined('PKWK_CORE_NAMESPACE') or define('PKWK_CORE_NAMESPACE', 'pukiwiki_adv');

// Wikiの名前空間（セッションやキャッシュで他のWikiと名前が重複するのを防ぐため）7文字で十分だろう・・。
defined('PKWK_WIKI_NAMESPACE') or define('PKWK_WIKI_NAMESPACE', 'pkwk_'.substr(md5(realpath(DATA_HOME)), 0 ,7) );

// SORT_NATURALがない場合、SORT_LOCALE_STRINGとする。SORT_NATURAL優先なのは多言語で使うため
defined('SORT_NATURAL') or define('SORT_NATURAL', SORT_LOCALE_STRING);

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

$info[] = '<a href="http://php.net/" rel="external">PHP</a> <var>'.PHP_VERSION.'</var> is running as <var>'.php_sapi_name().'</var> mode. / Powerd by <var>'.getenv('SERVER_SOFTWARE').'</var>.';
$info[] = 'Using <a href="http://framework.zend.com/" rel="external">Zend Framework</a> ver.<var>' . Zend\Version\Version::VERSION.'</var>.';
// Initilaize Session
$session = new Zend\Session\Container(PKWK_WIKI_NAMESPACE);

//ZendSearch\Lucene\Analysis\Analyzer\Analyzer::setDefault(new ZendSearch\Lucene\Analysis\Analyzer\Common\Utf8());

/////////////////////////////////////////////////
// Initilalize Cache
//
// 使用するキャッシュストレージを選択
// Filesystem以外動作チェックはしていません。自己責任で変更してください。
// 例：Apc, Dba, Filesystem, Memcached, Memory, Redis, WinCache, XCache, ZendServerDisk, ZendServerShm
// http://framework.zend.com/manual/current/en/modules/zend.cache.storage.adapter.html
$cache_adapter = 'Filesystem';
/*
if (!isset($cache_adapter)){
	
	if ( class_exists('dba') ){
		$cache_adapter = 'Dba';
	}else if ( class_exists('apc') && ini_get('apc.enabled') ){
		$cache_adapter = 'Apc';
	}else if ( class_exists('Memcached') ){
		$cache_adapter = 'Memcached';
	}
}
*/
// キャッシュ
$cache = array(
	// PukiWikiのコアで使われる汎用キャッシュ
	'core' => StorageFactory::factory(array(
		'adapter'=> array(
			'name' => $cache_adapter,
			'options' => array(
				'namespace' => ($cache_adapter === 'Filesystem') ? null : PKWK_WIKI_NAMESPACE,
				'cache_dir' => ($cache_adapter === 'Filesystem') ? SITE_HOME.'cache/' : null
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
				// デフォルトの有効時間は１日
				'ttl' => 86400,
				// 他のWikiと競合しないようにするためDATA_HOMEのハッシュを名前空間とする
				'namespace' => ($cache_adapter === 'Filesystem') ? null : PKWK_WIKI_NAMESPACE,
				'cache_dir' => ($cache_adapter === 'Filesystem') ? CACHE_DIR : null
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
				'namespace' => 'raw',
				'cache_dir'=>CACHE_DIR
			)
		)
	))
);
$info[] = 'Cache system using <var>'.$cache_adapter.'</var>.';


/////////////////////////////////////////////////
// 他言語化設定
// 
Lang::setLanguage();
Time::init();

$translator = new Translator();
$translator->factory(array(
	'locale' => Lang::$language_prepared,
	'cache' => $cache['core'],
));
T_setlocale(LC_ALL,PO_LANG);
T_bindtextdomain(DOMAIN,LANG_DIR);
T_textdomain(DOMAIN);

/////////////////////////////////////////////////
// リソースファイルの読み込み
require(LIB_DIR . 'resource.php');
// Init encoding hint
define('PKWK_ENCODING_HINT', (isset($_LANG['encode_hint']) && $_LANG['encode_hint'] !== 'encode_hint') ? $_LANG['encode_hint'] : 'ぷ');


/////////////////////////////////////////////////
// INI_FILE: Init $script

if (isset($script)) {
	Router::get_script_uri($script);	// Init manually
} else {
	$script = Router::get_script_uri();	// Init automatically
}
/////////////////////////////////////////////////
// ディレクトリのチェック
$die = array();

foreach(array('DATA_DIR', 'DIFF_DIR', 'BACKUP_DIR', 'CACHE_DIR') as $d){
	$dir = constant($d);
	if (! is_dir($dir)){
		mkdir($dir);
		chmod($dir, 0755);
	}
	if (! is_writable($dir)){
		$die[] = sprintf($_string['not_writable'],$dir);
	}
}
unset($d, $dir);

/////////////////////////////////////////////////
// QUERY_STRINGを取得

$vars = Utility::parseArguments();	// ここの処理はそのうち全部書き直し

/////////////////////////////////////////////////
// 初期設定(その他のグローバル変数)

// 現在時刻
$now = Time::format(UTIME);

//////////////////////////////////////////////////
// ajaxではない場合
// スキンデーター読み込み
defined('IS_MOBILE') or define('IS_MOBILE', false);
if (IS_MOBILE === true) {
	define('SKIN_FILE', 'mobile');
}else{
	define('SKIN_FILE', PLUS_THEME);
}
/*
if ( isset($auth_api['facebook']) ){
	if (extension_loaded('curl')){
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
*/
/////////////////////////////////////////////////
// Execute Plugin.

// auth remoteip
if (isset($auth_api['remoteip']['use']) && $auth_api['remoteip']['use']) {
	PluginRenderer::executePluginInline('remoteip');
}

// プラグインのaction命令を実行
$cmd = strtolower($vars['cmd']);
$is_protect = Auth::is_protect();
if ($is_protect) {
	$plugin_arg = '';
	if (Auth::is_protect_plugin_action($cmd)) {
		PluginRenderer::executePluginAction($cmd);
		// Location で飛ばないプラグインの場合
		$plugin_arg = $cmd;
	}
	PluginRenderer::executePluginBlock('protect', $plugin_arg);
}
if (!empty($cmd)){
	if (! PluginRenderer::hasPluginMethod($cmd, 'action')) {
		// プラグインにactionが定義されてない場合
		Utility::dieMessage(sprintf($_string['plugin_not_implemented'], Utility::htmlsc($cmd)), 501);
	}else{
		// プラグインのactionを実行する。
		// 帰り値：array('title', 'body', 'http_code');
		$retvars = PluginRenderer::executePluginAction($cmd);
	}
}

if ($is_protect) {
 	// Location で飛ぶようなプラグインの対応のため
	// 上のアクションプラグインの実行後に処理を実施
	PluginRenderer::executePluginBlock('protect');
	die('<var>PLUS_PROTECT_MODE</var> is set.');
}

///////////////////////////////////////
// Page output

$auth_key = Auth::get_user_info();
$base = $defaultpage;
if (!empty($auth_key['home']) && isset($vars['page']) && ($vars['page'] == $defaultpage || $vars['page'] == $auth_key['home'])){
	// ログイン時のホームページを基準とする（実際はあまり使われてないが）
	$base = $defaultpage = $auth_key['home'];
}else{
	$base = isset($vars['page']) ? $vars['page'] : $defaultpage;
}

$s_base =  Utility::htmlsc(Utility::stripBracket($base));
if (isset($retvars['msg']) && !empty($retvars['msg']) ) {
	$title = str_replace('$1', $s_base, $retvars['msg']);
	$page  = str_replace('$1', Factory::Wiki($base)->link('related'),  $retvars['msg']);
}else{
	$title = $s_base;
	$page  = Factory::Wiki($base)->link('related');
}

$http_code = isset($retvars['http_code']) ? $retvars['http_code'] : 200;

if (isset($retvars['body']) && !empty($retvars['body'])) {
	$body = $retvars['body'];
} else {
	if (! Factory::Wiki($base)->isValied()) {
		$base  = $defaultpage;
		$title = $s_base;
		$page  = Factory::Wiki($base)->link('related');
	}
	$vars['cmd']  = 'read';
	$vars['page'] = $base;

	if (empty($vars['page'])) die('page is missing!');
	
	$body = Factory::Wiki($vars['page'])->render();
	
	LogFactory::factory('check',$vars['page'])->set();
}

if ($vars['cmd'] === 'read'){
	LogFactory::factory('browse',$vars['page'])->set();
}

// ページを描画
new PukiWiki\Render($title, $body, $http_code);
unset($title, $body, $http_code);
exit;
// ここまで。

/**************************************************************************************************/

/** よく使うグローバル関数 **/
// gettext to Zend gettext emulator
function T_setlocale($type, $locale){
	global $translator, $cache;
	$translator->setLocale($locale);
	$translator->setCache($cache['core']);
}

function T_($string){
	global $translator, $domain, $language;
	$gettext_file = LANG_DIR.$language.'/LC_MESSAGES/'.$domain.'.mo';
	if (file_exists($gettext_file)){
		return $translator->translate($string, $domain, $language);
	}else{
		return $string;
	}
}

function T_bindtextdomain($domain, $dir){
	global $translator, $language, $cache;
	$gettext_file = LANG_DIR.PO_LANG.'/LC_MESSAGES/'.$domain.'.mo';
	if (file_exists($gettext_file)){
		$translator->addTranslationFile('gettext', $gettext_file, $domain, $language);
		$translator->setCache($cache['core']);
	}
}

function T_textdomain($text_domain){
	global $domain;
	$domain = $text_domain;
}

function pr($value, $break = false){
	if (DEBUG){
		Zend\Debug\Debug::dump($value);
	}
	if ($break) exit();
	return;
}

/* End of file init.php */
/* Location: ./wiki-common/lib/init.php */
