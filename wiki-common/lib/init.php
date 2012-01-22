<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: init.php,v 1.57.8 2011/11/28 21:35:00 Logue Exp $
// Copyright (C)
//   2010-2011 PukiWiki Advance Developers Team
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
define('S_VERSION', 'v1.0 alpha2');
define('S_REVSION', '20111204');
define('S_COPYRIGHT',
	'<strong>'.S_APPNAME.' ' . S_VERSION . '</strong>' .
	' Copyright &#169; 2010-2012' .
	' <a href="http://pukiwiki.logue.be/" rel="product">PukiWiki Advance Developers Team</a>.<br />' .
	' Licensed under the <a href="http://www.gnu.org/licenses/gpl-2.0.html" rel="license">GPLv2</a>.' .
	' Based on <a href="http://pukiwiki.cafelounge.net/plus/">"PukiWiki Plus! i18n"</a>'
);

define('GENERATOR', S_APPNAME.' '.S_VERSION);

/////////////////////////////////////////////////
// Init PukiWiki Advance Enviroment variables

defined('DEBUG')				or define('DEBUG', false);
defined('PKWK_WARNING')			or define('PKWK_WARNING', false);
defined('ROOT_URI')				or define('ROOT_URI', dirname($_SERVER['PHP_SELF']).'/');
defined('WWW_HOME')				or define('WWW_HOME', '');
defined('PLUS_THEME')			or define('PLUS_THEME',	'default');

// HTTP_X_REQUESTED_WITHヘッダーで、ajaxによるリクエストかを判別
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' || isset($vars['ajax']));

// ページ名やファイル名として使用できない文字（エンコード前の文字）
defined('PKWK_ILLEGAL_CHARS_PATTERN') or define('PKWK_ILLEGAL_CHARS_PATTERN', '/[%|=|&|?|~|#|\r|\n|\0|\@|;|\$|+|\\|\[|\]|\||^|{|}|\']/');

/////////////////////////////////////////////////
// Init server variables

// Compat and suppress notices
if (!isset($HTTP_SERVER_VARS)) $HTTP_SERVER_VARS = array();

foreach (array('SCRIPT_NAME', 'SERVER_ADMIN', 'SERVER_NAME',
	'SERVER_PORT', 'SERVER_SOFTWARE', 'HTTPS') as $key) {
	define($key, isset($_SERVER[$key]) ? $_SERVER[$key] : '');
	unset(${$key}, $_SERVER[$key], $HTTP_SERVER_VARS[$key]);
}

/////////////////////////////////////////////////
// Init grobal variables

$foot_explain = array();	// Footnotes
$related      = array();	// Related pages
$head_tags    = array();	// XHTML tags in <head></head> (Obsolete in Adv.)
$foot_tags    = array();	// XHTML tags before </body> (Obsolete in Adv.)

$info         = array();	// For debug use.

if (!IS_AJAX){
	// Init grobal variables
	$meta_tags    = array();	// <meta />Tags
	$link_tags    = array();	// <link />Tags
	$js_tags      = array();	// <script></script>Tags
	$js_blocks    = array();	// Inline scripts(<script>//<![CDATA[ ... //]]></script>)
	$css_blocks   = array();	// Inline styleseets(<style>/*<![CDATA[*/ ... /*]]>*/</style>)
	$js_vars      = array();	// JavaScript initial value.
	$_SKIN        = array();
}

/////////////////////////////////////////////////
// Require INI_FILE

define('USR_INI_FILE', add_homedir('pukiwiki.usr.ini.php'));
$read_usr_ini_file = false;
if (file_exists(USR_INI_FILE) && is_readable(USR_INI_FILE)) {
	require(USR_INI_FILE);
	$read_usr_ini_file = true;
}

define('INI_FILE',  add_homedir('pukiwiki.ini.php'));
$die = '';
if (! file_exists(INI_FILE) || ! is_readable(INI_FILE)) {
	$die .= T_('File is not found.').' (INI_FILE)' . "\n";
} else {
	require(INI_FILE);
}
if ($die) die_message(nl2br("\n\n" . $die));

if ($read_usr_ini_file) {
	require(USR_INI_FILE);
	unset($read_usr_ini_file);
}

/////////////////////////////////////////////////
// I18N
set_language();
set_time();
require(LIB_DIR . 'public_holiday.php');

// Init Resource(for gettext)
if (! ini_get('safe_mode')){
	putenv('LANGUAGE='.PO_LANG);
	putenv('LANG='.PO_LANG);
	putenv('LC_ALL='.PO_LANG);
	putenv('LC_MESSAGES='.PO_LANG);
}
T_setlocale(LC_ALL,PO_LANG);
T_setlocale(LC_CTYPE,PO_LANG);
T_bindtextdomain(DOMAIN,LANG_DIR);
T_bind_textdomain_codeset(DOMAIN,SOURCE_ENCODING); 
T_textdomain(DOMAIN);

/////////////////////////////////////////////////
// リソースファイルの読み込み
require(LIB_DIR . 'resource.php');
// Init encoding hint
// define('PKWK_ENCODING_HINT', isset($_LANG['encode_hint']) ? $_LANG['encode_hint'] : '');
define('PKWK_ENCODING_HINT', (isset($_LANG['encode_hint']) && $_LANG['encode_hint'] != 'encode_hint') ? $_LANG['encode_hint'] : '');
// unset($_LANG['encode_hint']);

/////////////////////////////////////////////////
// INI_FILE: Init $script

if (isset($script)) {
	get_script_uri($script);		// Init manually
} else {
	$script = get_script_uri();	// Init automatically
}

/////////////////////////////////////////////////
// INI_FILE: $agents:  UserAgentの識別


$user_agent = $matches = array();

$user_agent['agent'] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
$ua = 'HTTP_USER_AGENT';
// unset(${$ua}, $_SERVER[$ua], $HTTP_SERVER_VARS[$ua], $ua);	// safety
if($user_agent['agent'] == '') die();	// UAが取得できない場合は処理を中断
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

/////////////////////////////////////////////////
// ディレクトリのチェック

$die = array();
foreach(array('DATA_DIR', 'DIFF_DIR', 'BACKUP_DIR', 'CACHE_DIR') as $dir){
	if (! is_writable(constant($dir)))
		$die[] = sprintf($_string,$dir);
}

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
if (isset($_POST['encode_hint']) && $_POST['encode_hint'] != '') {
	// do_plugin_xxx() の中で、<form> に encode_hint を仕込んでいるので、
	// encode_hint を用いてコード検出する。
	// 全体を見てコード検出すると、機種依存文字や、妙なバイナリ
	// コードが混入した場合に、コード検出に失敗する恐れがある。
	$encode = mb_detect_encoding($_POST['encode_hint']);
	mb_convert_variables(SOURCE_ENCODING, $encode, $_POST);

} else if (isset($_POST['charset']) && $_POST['charset'] != '') {
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
// Memcache利用可能時
// Cacheディレクトリ内のキャッシュをMemcacheに保存します。

// Memcacheのホスト。ソケット接続の場合は、unix://var/run/memcache.socketのようにすること。（ラウンドロビン非対応）
defined('MEMCACHE_HOST')		or define('MEMCACHE_HOST', '127.0.0.1');
// Memcacheのポート。ソケット接続の場合は、0にすること。
defined('MEMCACHE_PORT')		or define('MEMCACHE_PORT', 11211);
// memcacheのプリフィックス（デフォルトはキャッシュディレクトリのパスの\や/を_にしたもの。）
defined('MEMCACHE_PREFIX')		or define('MEMCACHE_PREFIX', str_replace(array('/','\\'), '_',realpath(CACHE_DIR)).'_');
// memcache変数を圧縮（ページリストのキャッシュなどの一部の機能では無効化されます。）
defined('MEMCACHE_COMPRESSED')	or define('MEMCACHE_COMPRESSED', false);
// memcacheの有効期限（デフォルトは無制限）
defined('MEMCACHE_EXPIRE')		or define('MEMCACHE_EXPIRE', 0);

if (class_exists('Memcache')){
	$memcache = new Memcache;
	if (!$memcache->connect(MEMCACHE_HOST, MEMCACHE_PORT)) {
		// Memcacheが使用できない場合
		$info[] = sprintf('Couldnot to connect Memcached: <var>%s:%s%s</var>. Please check Memcached is running.', MEMCACHE_HOST, MEMCACHE_PORT, PHP_EOL);
		unset($memcache);
	}else{
		// Memcacheが使用できる場合
		define('PKWK_DAT_EXTENTION', '');
//		$memcache->setCompressThreshold(20000, 0.2);
		$info[] = 'Memcache is enabled! Ver.<var>'.$memcache->getVersion().'</var> / ';
		// セッション管理もMemcacheで行う
		ini_set('session.save_handler', 'memcache');
		ini_set('session.save_path', (strpos(MEMCACHE_HOST, 'unix://') !== FALSE) ? MEMCACHE_HOST : 'tcp://'.MEMCACHE_HOST.':'.MEMCACHE_PORT);

		define('PKWK_DAT_EXTENTION', '');
		define('PKWK_TSV_EXTENTION', '');
		define('PKWK_TXT_EXTENTION', '');
		define('PKWK_REL_EXTENTION', 'rel-');
		define('PKWK_REF_EXTENTION', 'ref-');
	}
}else{
	$info[] = 'Memcache is disabled.';
	unset($memcache);
}

/////////////////////////////////////////////////
// TokyoTyrant利用可能時（未実装）
// 仕様は同上。
/*
defined('TOKYOTYRANT_HOST') or define('TOKYOTYRANT_HOST', '127.0.0.1');
defined('TOKYOTYRANT_PORT') or define('TOKYOTYRANT_PORT', TokyoTyrant::RDBDEF_PORT);
if (class_exists('TokyoTyrant')){
	// Wikiデーターの保存に使用すれば異次元の速度になるだろうなぁ。
	$tokyotyrant = new TokyoTyrant(TOKYOTYRANT_HOST, TOKYOTYRANT_PORT);
	$info[] = 'TokyoTyrant is enabled.';
}
*/
/////////////////////////////////////////////////
// QUERY_STRINGを取得

$arg = '';
if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
	$arg = & $_SERVER['QUERY_STRING'];
} else if (isset($_SERVER['argv']) && ! empty($_SERVER['argv'])) {
	$arg = & $_SERVER['argv'][0];
}
if (PKWK_QUERY_STRING_MAX && strlen($arg) > PKWK_QUERY_STRING_MAX) {
	// Something nasty attack?
	die_message(_('Query string is too long.'));
}
$arg = input_filter($arg); // \0 除去
// for QA/250
$arg = str_replace('+','%20',$arg);

// unset QUERY_STRINGs
//foreach (array('QUERY_STRING', 'argv', 'argc') as $key) {
// For OpenID Lib (use QUERY_STRING).
foreach (array('argv', 'argc') as $key) {
	unset(${$key}, $_SERVER[$key], $HTTP_SERVER_VARS[$key]);
}
// $_SERVER['REQUEST_URI'] is used at func.php NOW
unset($REQUEST_URI, $HTTP_SERVER_VARS['REQUEST_URI']);

// mb_convert_variablesのバグ(?)対策: 配列で渡さないと落ちる
$arg = array($arg);
mb_convert_variables(SOURCE_ENCODING, 'auto', $arg);
$arg = $arg[0];

/////////////////////////////////////////////////
// QUERY_STRINGを分解してコード変換し、$_GET に上書き

// URI を urlencode せずに入力した場合に対処する
$matches = array();
foreach (explode('&', $arg) as $key_and_value) {
	if (preg_match('/^([^=]+)=(.+)/', $key_and_value, $matches) &&
	    (mb_detect_encoding($matches[2]) != 'ASCII' || $matches[1] == 'pukiwiki')) {
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
if (isset($vars['cmd']) && isset($vars['plugin']))
	die( _( 'Using both cmd= and plugin= is not allowed.' ) );

// 入力チェック: cmd, plugin の文字列は英数字以外ありえない
foreach(array('cmd', 'plugin') as $var) {
	if (isset($vars[$var]) && ! preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $vars[$var]))
		unset($get[$var], $post[$var], $vars[$var]);
}

// 整形: page, strip_bracket()
if (isset($vars['page'])) {
	$get['page'] = $post['page'] = $vars['page']  = strip_bracket($vars['page']);
} else {
	$get['page'] = $post['page'] = $vars['page'] = '';
}

// 整形: msg, 改行を取り除く
if (isset($vars['msg'])) {
	$get['msg'] = $post['msg'] = $vars['msg'] = str_replace("\r", '', $vars['msg']);
}

// TrackBack Ping
if (isset($vars['tb_id']) && $vars['tb_id'] != '') {
	$get['cmd'] = $post['cmd'] = $vars['cmd'] = 'tb';
}

// cmdもpluginも指定されていない場合は、QUERY_STRINGをページ名かInterWikiNameであるとみなす
if (! isset($vars['cmd']) && ! isset($vars['plugin'])) {

	$get['cmd']  = $post['cmd']  = $vars['cmd']  = 'read';

	$argx = explode('&', $arg);
	$arg = is_array($argx) ? $argx[0]:$argx;
	if ($arg == '') $arg = $defaultpage;
	$arg = rawurldecode($arg);
	$arg = strip_bracket($arg);
	$arg = input_filter($arg);
	$get['page'] = $post['page'] = $vars['page'] = $arg;
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
if ($usedatetime) $line_rules = array_merge($datetime_rules,$line_rules);
unset($datetime_rules);

// フェイスマークを$line_rulesに加える
if ($usefacemark) $line_rules = array_merge($facemark_rules,$line_rules);
unset($facemark_rules);

// 実体参照パターンおよびシステムで使用するパターンを$line_rulesに加える
// XHTML5では&lt;、&gt;、&amp;、&quot;と、&apos;のみ使える。
// http://www.w3.org/TR/html5/the-xhtml-syntax.html
$entity_pattern = '(?=[a-zA-Z0-9]{2,8})(?:apos|amp|lt|gt|quot)';

$line_rules = array_merge(array(
	'&amp;(#[0-9]+|#x[0-9a-f]+|' . $entity_pattern . ');' => '&$1;',
	"\r"          => '<br />' . "\n",	/* 行末にチルダは改行 */
), $line_rules);

//////////////////////////////////////////////////
// ajaxではない場合

// スキンデーター読み込み
define('SKIN_FILE', add_skindir(PLUS_THEME));
defined('IS_MOBILE') or define('IS_MOBILE', false);
if (!IS_AJAX || IS_MOBILE){
	global $facebook, $fb, $google_loader;

	// JavaScriptフレームワーク設定
	// google ajax api
	// http://code.google.com/intl/ja/apis/libraries/devguide.html#Libraries
	$google_loader = array(
/*
		'dojo' => array(
			'file'	=> 'dojo.xd.js',
			'ver'	=> '1.6.0'
		),
		'ext-core' => array(
			'file'	=> 'ext-core.js',
			'ver'	=> '3.1.0'
		),
*/
		'jquery' => array(
			'file'	=> 'jquery.min.js',
			'ver'	=> '1.7.1'
		),
		'jqueryui'	=> array(
			'file'	=> 'jquery-ui.min.js',
			'ver'	=> '1.8.17'
		),
		'swfobject' => array(
			'file'	=> 'swfobject.js',
			'ver'	=> '2.2'
		)
	);
	
	if ($x_ua_compatible == 'chrome=1'){
		// X-UA-CompatibleでChromeをレンダリングにするよう指定した場合
		$google_loader['chrome-frame'] = array(
			'file'	=> 'CFInstall.min.js',
			'ver'	=>'1'
		);
	}

	// application/xhtml+xml を認識するブラウザではXHTMLとして出力
	if (PKWK_STRICT_XHTML === TRUE && strstr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml') !== false){
		foreach ($google_loader as $name=>$fw){
			$pkwk_head_js[] = array('type'=>'text/javascript', 'src'=>'https://ajax.googleapis.com/ajax/libs/'.$name.'/'.$fw['ver'].'/'.$fw['file']);
		}
	}else{
		// google.loadはdocument.write命令を使うためXHTMLにならない。
		foreach ($google_loader as $name=>$fw){
			$js_vars[] = 'google.load("'.$name.'","'.$fw['ver'].'");';
		}
	}

	// modernizrの設定
	$modernizr = 'modernizr.min.js';

	// jQueryUIのCSS
	$link_tags[] = array(
		'rel'=>'stylesheet',
		'href'=>'http://ajax.googleapis.com/ajax/libs/jqueryui/'.$google_loader['jqueryui']['ver'].'/themes/'.(!isset($_SKIN['ui_theme']) ? 'base' : $_SKIN['ui_theme']).'/jquery-ui.css',
		'type'=>'text/css',
		'id'=>'ui-theme'
	);

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

	if (DEBUG === true) {
		// 読み込むsrcディレクトリ内のJavaScript
		$default_js = array(
			/* libraly */
			'swfupload',
			'tzCalculation_LocalTimeZone',
			
			/* Use plugins */
			'activity-indicator',
			'jquery.a-tools',
			'jquery.beautyOfCode',
			'jquery.cookie',
			'jquery.colorbox',
			'jquery.i18n',
			'jquery.jplayer',
			'jquery.lazyload',
			'jquery.query',
			'jquery.scrollTo',
			'jquery.superfish',
			'jquery.swfupload',
			'jquery.tabby',
			'jquery.tablesorter',
			'jquery.textarearesizer',
			'jquery.tooltip',
			
			/* MUST BE LOAD LAST */
			'skin.original'
		);
		foreach($default_js as $script_file)
			$pkwk_head_js[] = array('type'=>'text/javascript', 'src'=>JS_URI.'src/'.$script_file.'.js');

		// yui profiler and profileviewer
		/*
			$link_tags[] = array('rel'=>'stylesheet','type'=>'text/css','href'=>JS_URI.'profiling/yahoo-profiling.css');
			$pkwk_head_js[] = array('type'=>'text/javascript', 'src'=>JS_URI.'profiling/yahoo-profiling.min.js');
			$pkwk_head_js[] = array('type'=>'text/javascript', 'src'=>JS_URI.'profiling/config.js');
		*/

	} else {
		$pkwk_head_js[] = array('type'=>'text/javascript', 'src'=>JS_URI.'skin.js');
	}
	$pkwk_head_js[] = array('type'=>'text/javascript', 'src'=>JS_URI.'locale.js');
	
	if (isset($facebook)){
		require(LIB_DIR.'facebook.php');
		$fb = new FaceBook($facebook);
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
	}
}
?>
