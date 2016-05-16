<?php
/**
 * ユーティリティクラス
 *
 * @package   PukiWiki
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2016 PukiWiki Advance Developers Team
 * @create    2012/12/31
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Utility.php,v 1.0.4 2016/12/06 15:36:00 Logue Exp $
 **/

namespace PukiWiki;

use PukiWiki\Auth\Auth;
use PukiWiki\Diff\LineDiff;
use PukiWiki\Factory;
use PukiWiki\Listing;
use PukiWiki\Render;
use PukiWiki\Renderer\Header;
use PukiWiki\Renderer\PluginRenderer;
use PukiWiki\Renderer\RendererDefines;
use PukiWiki\Router;
use PukiWiki\Spam\Spam;
use SplFileInfo;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\Response;
use Zend\Json\Json;
use Zend\Math\Rand;

defined('SOURCE_ENCODING') or define('SOURCE_ENCODING', 'UTF-8');

/**
 * 汎用関数
 */
class Utility{
	/**
	 * チケット名
	 */
	const TICKET_NAME = 'ticket';
	/**
	 * QueryStringの最大文字数
	 */
	const MAX_QUERY_STRING_LENGTH = 640; // bytes
	/**
	 * テキストの整形ルールページ
	 */
	const RULE_PAGENAME = 'FormatRule';
	/**
	 * ブラックリストに保存
	 */
	const SAVE_BLACKLIST = true;
	/**
	 * スパムログを使用する
	 */
	const SPAM_LOGGING = true;
	/**
	 * スパムのカウント
	 */
	const SPAM_COUNT = 2;
	/**
	 * スパムの正規表現マッチパターン
	 */
	const SPAM_PATTERN = '#(?:cialis|hydrocodone|viagra|levitra|tramadol|xanax|\</a\>|\[/link\]|\[/url\])#i';
	/**
	 * WebDAVのマッチパターン
	 */
	protected static $ua_dav = array(
		'Microsoft-WebDAV-MiniRedir\/',
		'Microsoft Data Access Internet Publishing Provider',
		'MS FrontPage',
		'^WebDrive',
		'^WebDAVFS\/',
		'^gnome-vfs\/',
		'^XML Spy',
		'^Dreamweaver-WebDAV-SCM1',
		'^Rei.Fs.WebDAV',
	);

	/**
	 * 設定ファイルを読み込む
	 * @param string $file ファイル名
	 * @return array or boolean
	 */
	public static function loadConfig($file){
		return include self::add_homedir($file);
	}
	/**
	 * 設定ファイルを取得
	 * @param string $file ファイル名
	 */
	public static function add_homedir($file){
		foreach(array(DATA_HOME, SITE_HOME) as $dir) {
			$f = new SplFileInfo($dir.$file);
			if ($f->isFile() && $f->isReadable()){
				return $dir.$file;
			}
			unset($f);
		}
		return false;
	}
	/**
	 * QueryStringをパースし、$_GETに上書き
	 * @return void
	 */
	public static function parseArguments(){
		global $cookie, $get, $post, $method;
		global $defaultpage;

		$request  = new Request();

		// GET, POST, COOKIE
		$get    = $request->getQuery();
		$post   = $request->getPost();
		$cookie = $request->getCookie();
		$method = $request->getMethod();
		$vars   = array();

		if (strlen($get->toString()) > self::MAX_QUERY_STRING_LENGTH) {
			// Something nasty attack?
			self::dump('suspicious');
			self::dieMessage(_('Query string is too long.'));
		}

		if (count($get) === 0){
			// Queryがない場合
			$get->set('page', $defaultpage);
		}else if (count($get) === 1 && empty(array_values((array)$get)[0])){
			// 配列の長さが1で最初の配列に値が存在しない場合はキーをページ名とする。
			$k = trim(array_keys((array)$get)[0]);
			$get->set('page', rawurldecode($_SERVER['QUERY_STRING']));
			unset($get[$k]);
		}

		// 外部からの変数を$vars配列にマージする
		if (empty($post)) {
			$vars = (array)$get;  // Major pattern: Read-only access via GET
		} else if (empty($get)) {
			$vars = (array)$post; // Minor pattern: Write access via POST etc.
		} else {
			$vars = array_merge((array)$get, (array)$post); // Considered reliable than $_REQUEST
		}

//		var_dump($vars);
//		die;

		if (!isset($vars['cmd'])){
			$vars['cmd'] = 'read';
		}

		if (isset($vars['page']) && is_string($vars['page']) && preg_match(Wiki::INVALIED_PAGENAME_PATTERN, $vars['page']) === false){
			// ページ名チェック
			self::dump('suspicious');
			die('Invalid page name.');
		}

		if (is_string($vars['cmd']) && preg_match(PluginRenderer::PLUGIN_NAME_PATTERN, $vars['cmd']) === false){
			// 入力チェック: cmdの文字列は英数字以外ありえない
			self::dump('suspicious');
			die(sprintf('Plugin name %s is invalied or too long! (less than 64 chars)', $vars['cmd']));
		}

		// 文字コード変換
		// <form> で送信された文字 (ブラウザがエンコードしたデータ) のコードを変換
		// POST method は常に form 経由なので、必ず変換する
		if (isset($vars['encode_hint']) && !empty($vars['encode_hint'])) {
			// do_plugin_xxx() の中で、<form> に encode_hint を仕込んでいるので、
			// encode_hint を用いてコード検出する。
			// 全体を見てコード検出すると、機種依存文字や、妙なバイナリ
			// コードが混入した場合に、コード検出に失敗する恐れがある。
			$encode = mb_detect_encoding($vars['encode_hint']);
			mb_convert_variables(SOURCE_ENCODING, $encode, $vars);
		} else {
			// 全部まとめて、自動検出／変換
			mb_convert_variables(SOURCE_ENCODING, 'auto', $vars);
		}

		// 環境変数のチェック
		self::checkEnv($request->getEnv());

		switch ($method){
			case Request::METHOD_POST:
				self::spamCheck($vars['cmd']);
				break;
			case Request::METHOD_OPTIONS:
			case Request::METHOD_PROPFIND:
			case Request::METHOD_DELETE:
			case 'MOVE':
			case 'COPY':
			case 'PROPPATCH':
			case 'MKCOL':
			case 'LOCK':
			case 'UNLOCK':
				// WebDAV
				$matches = array();
				foreach(self::$ua_dav as $pattern) {
					if (preg_match('/'.$pattern.'/', $log_ua, $matches)) {
						PluginRenderer::executePluginAction('dav');
						exit;
					}
				}
				break;
		}

		return $vars;
	}
	/**
	 * 環境変数のチェック
	 */
	public static function checkEnv($env){
		global $deny_countory, $allow_countory;
		// 国別設定
		$country_code = '';
		if (isset($env['HTTP_CF_IPCOUNTRY'])){
			// CloudFlareを使用している場合、そちらのGeolocationを読み込む
			// https://www.cloudflare.com/wiki/IP_Geolocation
			$country_code = $env['HTTP_CF_IPCOUNTRY'];
		}else if (isset($env['GEOIP_COUNTRY_CODE'])){
			// サーバーが$_SERVER['GEOIP_COUNTRY_CODE']を出力している場合
			// Apache : http://dev.maxmind.com/geoip/mod_geoip2
			// nginx : http://wiki.nginx.org/HttpGeoipModule
			// cherokee : http://www.cherokee-project.com/doc/config_virtual_servers_rule_types.html
			$country_code = $env['GEOIP_COUNTRY_CODE'];
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
		if ( !isset($country_code) || empty($country_code)) {
			$info[] = 'Seems Geolocation is not available. <var>$deny_countory</var> value and <var>$allow_countory</var> value is ignoled.';
			
		} else {
			$info[] = 'Your country code from IP is inferred <var>'.$country_code.'</var>.';
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

		// INI_FILE: $agents:  UserAgentの識別
		$user_agent = $matches = array();

		$user_agent['agent'] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		// unset(${$ua}, $_SERVER[$ua], $HTTP_SERVER_VARS[$ua], $ua);	// safety
		if ( empty($user_agent['agent']) ) die();	// UAが取得できない場合は処理を中断

		foreach (self::loadConfig('profile.ini.php') as $agent) {
			if (preg_match($agent['pattern'], $user_agent['agent'], $matches)) {
				
				$user_agent = array(
					'profile'	=> isset($agent['profile']) ? $agent['profile'] : null,
					'name'		=> isset($matches[1]) ? $matches[1] : null,	// device or browser name
					'vers'		=> isset($matches[2]) ? $matches[2] : null,	// version
				);
				break;
			}
		}

		$ua_file = self::add_homedir($user_agent['profile'].'.ini.php');
		if ($ua_file){
			require($ua_file);
		}

		define('UA_NAME', isset($user_agent['name']) ? $user_agent['name'] : null);
		define('UA_VERS', isset($user_agent['vers']) ? $user_agent['vers'] : null);
		define('UA_CSS', isset($user_agent['css']) ? $user_agent['css'] : null);

		// HTTP_X_REQUESTED_WITHヘッダーで、ajaxによるリクエストかを判別
		define('IS_AJAX', isset($env['HTTP_X_REQUESTED_WITH']) && strtolower($env['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' || isset($vars['ajax']));

	}
	/**
	 * スパムフィルタ
	 * @param string $cmd 動作
	 */
	public static function spamCheck($cmd){
		global $spam, $vars, $method;

		// Adjustment
		$_spam = !empty($spam);
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

			if (isset($spam['method'][$_cmd])) {
				$_method = $spam['method'][$_cmd];
			} else if (isset($spam['method']['_default'])) {
				$_method = $spam['method']['_default'];
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
				$_vars = $vars;
			}
			Spam::pkwk_spamfilter($method . ' to #' . $_cmd, $_page, $_vars, $_method, $exitmode);
		}
	}
	/**
	 * 乱数を生成して暗号化時のsaltを生成する
	 * @param boolean $flush 再生成するか
	 * @return string
	 */
	public static function getTicket($flush = FALSE)
	{
		global $cache;
		static $ticket;

		if ($flush){
			unset($ticket);
			$cache['wiki']->removeItem(self::TICKET_NAME);
		}

		if (isset($ticket)){
			return $ticket;
		}else if ($cache['wiki']->hasItem(self::TICKET_NAME)) {
			$ticket = $cache['wiki']->getItem(self::TICKET_NAME);
		}else{
			// 32バイトの乱数を生成
			$ticket = Rand::getString(32);
			$cache['wiki']->setItem(self::TICKET_NAME, $ticket);
		}
		return $ticket;
	}
	/**
	 * IPアドレスを取得
	 * @return string
	 */
	public static function getRemoteIp(){
		// CloudFlareから送られてきたIP、リバースプロクシから送られてきたIP、リモートIPの順番で読み込む
		static $array_var = array('HTTP_CF_CONNECTING_IP', 'HTTP_X_REMOTE_ADDR','REMOTE_ADDR'); // HTTP_X_FORWARDED_FOR
		foreach($array_var as $x){
			if (!isset($_SERVER[$x])) continue;
			// TODO: IPv6の場合、それが出力される
//			if (filter_var($_SERVER[$x], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)){
//				return $_SERVER['REMOTE_ADDR'];
//			}
			return $_SERVER[$x];
		}
		self::dieMessage('Could not get IP address.');	// IPアドレスが取得できない場合、念のため処理を止める
		// return null;
	}
	/**
	 * 相対指定のページ名から全ページ名を取得
	 * @param string $name 名前の入力値
	 * @param string $refer 引用元のページ名
	 * @return string ページのフルパス
	 */
	public static function getPageName($name = null, $refer = null) {
		global $defaultpage, $vars;
		
		if (empty($refer) && isset($vars['page']) && !empty($vars['page'])){
			// 引用元が指定されていない場合、外部変数からのページ名を引用元とする
			$refer = $vars['page'];
		}

		// 'Here'
		if (empty($name) || $name === './'){
			// ページ名が指定されてない場合、引用元のページ名を返す
			return $refer;
		}

		// Absolute path
		if ($name{0} === '/') {
			$name = substr($name, 1);
			return empty($name) ? $defaultpage : $name;
		}

		// Relative path from 'Here'
		if (substr($name, 0, 2) === './') {
			// 同一ディレクトリ
			$arrn    = preg_split('#/#', $name, -1, PREG_SPLIT_NO_EMPTY);
			$arrn[0] = $refer;
			return join('/', $arrn);
		}

		// Relative path from dirname()
		if (substr($name, 0, 3) === '../') {
			// 上の階層
			$arrn = preg_split('#/#', $name,  -1, PREG_SPLIT_NO_EMPTY);
			$arrp = preg_split('#/#', $refer, -1, PREG_SPLIT_NO_EMPTY);

			// 改装を遡る
			while (! empty($arrn) && $arrn[0] === '..') {
				array_shift($arrn);
				array_pop($arrp);
			}
			// ディレクトリを結合する
			$name = ! empty($arrp) ? join('/', array_merge($arrp, $arrn)) :
				(! empty($arrn) ? $defaultpage . '/' . join('/', $arrn) : $defaultpage);
		}

		return $name;
	}
	/**
	 * パスを含まないページ名を取得
	 * @param $page ページ名
	 */
	public static function getPageNameShort($page)
	{
		$pagestack = explode('/', $page);
		return array_pop($pagestack);
	}
	/**
	 * ファイルのMIMEを取得 
	 * @param string $filename ファイル名
	 * @return string
	 */
	public static function getMimeInfo($filename){
		$type = 'text/plain';
		if (function_exists('finfo_open')) {
			$finfo = finfo_open(FILEINFO_MIME);
			if (!$finfo) return $type;
			$type = finfo_file($finfo, $filename);
			finfo_close($finfo);
			return $type;
		}

		if (function_exists('mime_content_type')) {
			$type = mime_content_type($filename);
			return $type;
		}

		// PHP >= 4.3.0
		$filesize = @getimagesize($filename);
		if (is_array($filesize) && preg_match('/^(image\/)/i', $filesize['mime'])) {
			$type = $filesize['mime'];
		}
		return $type;
	}
	/**
	 * htmlspacialcharsのエイリアス（PHP5.4対策）
	 * @param string $string 文字列
	 * @param int $flags 変換する文字
	 * @param string $charset エンコード
	 * @return string
	 */
	public static function htmlsc($string = '', $flags = ENT_QUOTES, $charset = SOURCE_ENCODING){
		// Sugar with default settings
		return htmlspecialchars($string, $flags, $charset);	// htmlsc()
	}
	/**
	 * ページ名をファイル格納用の名前にする（FrontPage→46726F6E7450616765）
	 * @param string $str
	 * @return string
	 */
	public static function encode($str) {
		$value = strval($str);
		return empty($value) ? null : strtoupper(bin2hex($value));
	}
	/**
	 * ファイル格納用の名前からページ名を取得する（46726F6E7450616765→FrontPage）
	 * @param string $str
	 * @return string
	 */
	public static function decode($str) {
		return hex2bin($str);
	}
	/**
	 * 文字列がURLかをチェック
	 * @param string $str
	 * @param boolean $only_http HTTPプロトコルのみを判定にするか
	 * @return boolean
	 */
	public static function isUri($str, $only_http = FALSE){
		// URLでありえない文字はfalseを返す
		if ( preg_match( '|[^-/?:#@&=+$,\w.!~*;\'()%]|', $str ) ) {
			return FALSE;
		}

		// 許可するスキーマー
		$scheme = $only_http ? 'https?' : 'https?|ftp|news';

		// URLマッチパターン
		$pattern = (
			'!^(?:' . $scheme . ')://' .            // scheme
			'(?:\w+:\w+@)?' .                       // ( user:pass )?
			'('. 
			'(?:[-_0-9a-z]+\.)+(?:[a-z]+)\.?|'.     // ( domain name |
			'\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}|' . //   IP Address  |
			'localhost' .                           //   localhost )
			')'. 
			'(?::\d{1,5})?(?:/|$)!iD'               // ( :Port )?
		);
		// 正規処理
		$ret = preg_match($pattern, $str);
		// マッチしない場合は0が帰るのでFALSEにする
		return $ret === 0 ? FALSE : $ret;
	}
	/**
	 * 簡易スパム判定
	 * @param int $count いくつ存在した時にスパムとみなすか
	 * @return boolean
	 */
	public static function isSpamPost($count=0) {
		global $vars;

		if ($count <= 0) {
			$count = intval(self::SPAM_COUNT);
		}
		$matches = array();
		foreach(array_keys($vars) as $idx) {
			if ($idx === 'original') continue;	// 編集前の内容はチェックしない
			if (preg_match_all(self::SPAM_PATTERN, $vars[$idx], $matches) >= $count)
				return TRUE;
		}
		return FALSE;
	}
	/**
	 * InterWikiNameかをチェック
	 * @param string $str
	 * @return boolean
	 */
	public static function isInterWiki($str){
		return preg_match('/^' . RendererDefines::INTERWIKINAME_PATTERN . '$/', $str);
	}
	/**
	 * ブラケット名か
	 * @param string $str
	 * @return boolean
	 */
	public static function isBracketName($str){
		return preg_match('/^(?!\/)' . RendererDefines::BRACKETNAME_PATTERN . '$(?<!\/$)/', $str);
	}
	/**
	 * Wiki名か
	 * @param string $str
	 * @return boolean
	 */
	public static function isWikiName($str){
		return preg_match('/^' . RendererDefines::WIKINAME_PATTERN . '$/', $str);
	}
	/**
	 * ブラケット（[[ ]]）を取り除く
	 * @param string $str
	 * @return string
	 */
	public static function stripBracket($str)
	{
		$match = array();
		return preg_match('/^\[\[(.*)\]\]$/', $str, $match) ? $match[1] : $str;
	}
	/**
	 * WikiNameからHTMLタグを除く(strip_htmltag)
	 * @param $str string 入力文字
	 * @param $all boolean 全てのタグかaタグのみか
	 * @return string
	 */
	public static function stripHtmlTags($str, $all = true)
	{
		global $_symbol_noexists;
		static $noexists_pattern;

		if (! isset($noexists_pattern))
			$noexists_pattern = '#<span class="noexists">([^<]*)<a[^>]+>' . preg_quote($_symbol_noexists, '#') . '</a></span>#';

		// Strip Dagnling-Link decoration (Tags and "$_symbol_noexists")
		$str = preg_replace($noexists_pattern, '$1', $str);

		return $all ?
			preg_replace('#<[^>]+>#', '', $str) :		// All other HTML tags
			preg_replace('#<a[^>]+>|</a>#i', '', $str);	// All other anchor-tags only
	}
	/**
	 * 自動リンクを削除
	 * @param string $str 入力文字
	 * @return string
	 */
	public static function stripAutolink($str)
	{
		return preg_replace('#<!--autolink--><a [^>]+>|</a><!--/autolink-->#', '', $str);
	}
	/**
	 * ページリンクからページ名とリンクを取得（アンカーは削除）
	 * @param string $page
	 * @param boolean $strict_editable
	 * @return type
	 */
	public static function explodeAnchor($page, $strict_editable = FALSE)
	{
		// Separate a page-name(or URL or null string) and an anchor
		// (last one standing) without sharp
		$pos = strrpos($page, '#');
		if ($pos === FALSE) return array($page, '', FALSE);

		// Ignore the last sharp letter
		if ($pos + 1 == strlen($page)) {
			$pos = strpos(substr($page, $pos + 1), '#');
			if ($pos === FALSE) return array($page, '', FALSE);
		}

		$s_page = substr($page, 0, $pos);
		$anchor = substr($page, $pos + 1);

		return $strict_editable === TRUE && preg_match('/^[a-z][a-f0-9]{7}$/', $anchor) ?
			array ($s_page, $anchor, TRUE) : // Seems fixed-anchor
			array ($s_page, $anchor, FALSE);
	}
	/**
	 * エラーメッセージを表示
	 * @param string $msg エラーメッセージ
	 * @param string $title エラーのタイトル
	 * @param int $http_code 出力するヘッダー
	 */
	public static function dieMessage($msg = '', $error_title='', $http_code = Response::STATUS_CODE_200){
		global $_string, $_title, $_button, $vars;

		// エラーメッセージの内容
		if (!isset($vars['ajax'])) {
			$body[] = '<p>[ ';
			if ( isset($vars['page']) && !empty($vars['page']) ){
				$body[] = '<a href="' . Factory::Wiki($vars['page'])->uri() .'">'.$_button['back'].'</a> | ';
				$body[] = '<a href="' . Router::get_cmd_uri('edit',$vars['page']) . '">'.$_button['try_edit'].'</a> | ';
			}
			$body[] = '<a href="' . Router::get_cmd_uri() . '">'.$_button['return_home'].'</a> ]</p>';
		}
		$body[] = '<p class="alert alert-warning"><span class="fa fa-ban"></span> <strong>' . $_title['error'] . '</strong>';
		$body[] = PKWK_WARNING !== true || empty($msg) ? $msg = $_string['error_msg'] : $msg;
		$body[] = '</p>';

		if (DEBUG) {
			$body[] = '<div class="panel panel-info">';
			$body[] = '<div class="panel-heading"><span class="fa fa-info-circle"></span> Back Trace</div>';
			$body[] = '<div class="panel-body">';
			$body[] = '<ol>';
			foreach (debug_backtrace() as $k => $v) {
				if ($k < 2) { 
					continue;
				}
				array_walk($v['args'], function ($item, $key) {
					$item = var_export($item, true);
				});
				$body[] = '<li>' . (isset($v['file']) ? $v['file'] : '?') . '(<var>' . (isset($v['line']) ? $v['line'] : '?') . '</var>):<br /><code>' . (isset($v['class']) ? '<strong>' . $v['class'] . '</strong>-&gt;' : '') . $v['function'] . '(<var>' . implode(', ', $v['args']) . '</var>)</code></li>' . "\n";
			}
			$body[] = '</ol>';
			$body[] = '</div>';
			$body[] = '</div>';
		}
		
		if (empty($error_title)){
			$error_title = $_title['error'];
		}

		if (isset($vars['ajax'])) {
			$headers = Header::getHeaders('application/json');
			Header::writeResponse($headers, $http_code, Json::encode(array(
				'posted'    => false,
				'title'     => $error_title,
				'body'       => join("\n",$body),
				'taketime'  => Time::getTakeTime()
			)));
		}else{
			new Render($error_title, join("\n",$body), $http_code);
			die(join("\n",$body));
		}
		die();
	}
	/**
	 * ページが見つからない
	 * @return void
	 */
	public static function notFound(){
		global $vars, $_button, $_title, $_string;
		$body[] = '<p>[ ';
		if ( isset($vars['page']) && !empty($vars['page']) ){
			$body[] = '<a href="' . Factory::Wiki($vars['page'])->uri() .'">'.$_button['back'].'</a> | ';
			$body[] = '<a href="' . Router::get_cmd_uri('edit',$vars['page']) . '">'.$_button['try_edit'].'</a> | ';
		}
		$body[] = '<a href="' . Router::get_cmd_uri() . '">Return to FrontPage</a> ]</p>';
		$body[] = '<p class="alert alert-warning"><span class="fa fa-info-sign"></span> <strong>' . $_title['page_not_found'] . '</strong>';
		$body[] = $_string['not_found1'];
		if ( isset($vars['page']) && !empty($vars['page']) ){
			$body[] = '<br />' . "\n" . sprintf(
				$_string['not_found2'],
				Router::get_cmd_uri('backup',$vars['page']),
				Router::get_cmd_uri('edit',$vars['page'])
			);
		}
		$body[] = '</p>';
		$body[] = '<script type="text/javascript">/' .'* <![CDATA *' . '/';
		$body[] = 'var GOOG_FIXURL_LANG = (navigator.language || null).slice(0,2), GOOG_FIXURL_SITE = location.host;';
		$body[] = '/' . '* ]]> *' . '/</script>';
		$body[] = '<script type="text/javascript" src="//linkhelp.clients.google.com/tbproxy/lh/wm/fixurl.js"></script>';
		new Render('Page not found', join("\n",$body), Response::STATUS_CODE_404);
	}
	/**
	 * リダイレクト
	 * （この処理特殊すぎるな・・・。）
	 * @param string $url リダイレクト先
	 * @param int $time リダイレクトの待ち時間
	 * @return void
	 */
	public static function redirect($url = '', $time = 0){
		global $_string, $_title, $vars;
		$response = new Response();

		// URLが空の場合、ページのアドレスか、スクリプトのアドレスを返す
		if (empty($url)){
			$url = isset($vars['page']) ? Router::get_resolve_uri(null, $vars['page']) : Router::get_script_uri();
		}
		$s_url = self::htmlsc($url);
		
		if (!DEBUG){
			$response->setStatusCode(Response::STATUS_CODE_301);
			$response->getHeaders()->addHeaderLine('Location', $s_url);
		}else{
			$response->setStatusCode(Response::STATUS_CODE_200);
		}
		$html = array();
		$html[] = '<!doctype html>';
		$html[] = '<html>';
		$html[] = '<head>';
		$html[] = '<meta charset="utf-8" />';
		$html[] = '<meta name="robots" content="NOINDEX,NOFOLLOW" />';
		if (!DEBUG){
			$html[] = '<meta http-equiv="refresh" content="'.$time.'; URL='.$s_url.'" />';
		}
		$html[] = '<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/' . Render::TWITTER_BOOTSTRAP_VER . '/css/bootstrap.min.css" type="text/css" />';
		$html[] = '<title>' . $_title['redirect'] . '</title>';
		$html[] = '</head>';
		$html[] = '<body>';
		$html[] = '<div class="container">';
		$html[] = '<p class="alert alert-success">';
		$html[] = '<span class="glyphicon glyphicon-info-sign"></span>';
		$html[] = $_string['redirect1'] . '<br />';
		$html[] = sprintf($_string['redirect2'] , $s_url);
		if (!DEBUG){
			$html[] = '<br />NOTICE: No auto redirect when Debug mode.';
		}
		$html[] = '</p>';
		$html[] = '</div>';
		$html[] = '</body>';
		$html[] = '</html>';
		$content = join("\n",$html);
		$response->getHeaders()->addHeaderLine('Content-Length', strlen($content));
		$response->setContent($content);

		if (!headers_sent()) {
			header($response->renderStatusLine());
			foreach ($response->getHeaders() as $header) {
				header($header->toString());
			}
		}

		echo $response->getBody();
		exit;
	}
	/**
	 * 認証要求
	 * @return void
	 */
	public static function notAuth($realm){
		global $_string, $_title, $_button, $vars;

		$response = new Response();

		// URLが空の場合、ページのアドレスか、スクリプトのアドレスを返す
		if (empty($url)){
			$url = isset($vars['page']) ? Router::get_resolve_uri(null, $vars['page']) : Router::get_script_uri();
		}
		$s_url = self::htmlsc($url);
		
			$response->setStatusCode(Response::STATUS_CODE_301);
			$response->getHeaders()->addHeaderLine('Location', $s_url);

		$html = array();
		$html[] = '<!doctype html>';
		$html[] = '<html>';
		$html[] = '<head>';
		$html[] = '<meta charset="utf-8">';
		$html[] = '<meta name="robots" content="noindex,nofollow,noarchive,noodp,noydir" />';
		if (!DEBUG){
			$html[] = '<meta http-equiv="refresh" content="'.$time.'; URL='.$s_url.'" />';
		}
		$html[] = '<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/' . Render::TWITTER_BOOTSTRAP_VER . '/css/bootstrap.min.css" type="text/css" />';
		$html[] = '<title>' . $_title['redirect'] . '</title>';
		$html[] = '</head>';
		$html[] = '<body>';
		$html[] = '<div class="container">';
		$html[] = '<p class="alert alert-success">';
		$html[] = '<span class="glyphicon glyphicon-info-sign"></span>';
		$html[] = $_string['redirect1'] . '<br />';
		$html[] = sprintf($_string['redirect2'] , $s_url);
		if (!DEBUG){
			$html[] = '<br />NOTICE: No auto redirect when Debug mode.';
		}
		$html[] = '</p>';
		$html[] = '</div>';
		$html[] = '</body>';
		$html[] = '</html>';
		$content = join("\n",$html);
		$response->getHeaders()->addHeaderLine('Content-Length', strlen($content));
		$response->setContent($content);

		if (!headers_sent()) {
			header($response->renderStatusLine());
			foreach ($response->getHeaders() as $header) {
				header($header->toString());
			}
		}

		echo $response->getBody();
		exit;
	}
	/**
	 * 編集画面を表示
	 * @param string $page 編集しようとしているページ名
	 * @param string $data 入力データー
	 * @param boolean $show_template テンプレートを表示するか
	 */
	public static function editForm($page, $data, $show_template = TRUE)
	{
		global $vars, $session;
		global $_button, $_string;
		global $notimeupdate, $load_template_func, $load_refer_related;

		if (empty($page)) return self::dieMessage('Page name was not defined.');

		$postdata = is_array($data) ? join("\n", $data) : $data;
		$original = isset($vars['original']) ? $vars['original'] : $postdata;

		// ticketは、PliginRenderer::addHiddenField()で自動挿入されるので、同じアルゴリズムでチケット名を生成
		$ticket_name = md5(Utility::getTicket() . REMOTE_ADDR);
		// BugTrack/95 fix Problem: browser RSS request with session
		$session->offsetSet('origin-'.$ticket_name, md5(self::getTicket() . str_replace("\r", '', $original)));

		$ret[] = '<form action="' . Router::get_script_uri() . '" role="form" method="post" class="form-edit" data-collision-check-strict="true">';
		$ret[] = '<input type="hidden" name="cmd" value="edit" />';
		$ret[] = '<input type="hidden" name="page" value="' . self::htmlsc($page) .'" />';
		$ret[] = isset($vars['id']) ? '<input type="hidden" name="id" value="' . self::htmlsc($vars['id']) . '" />' : null;
		
		if ($load_template_func && $show_template) {
			// ひな形を読み込む
			foreach(Listing::pages() as $_page) {
				$_w = Factory::Wiki($_page);
				if (! $_w->isEditable() || $_w->isHidden())
					continue;
				$_s_page = self::htmlsc($_page);
				$_pages[$_page] = '<option value="' . $_s_page . '">' .$_s_page . '</option>'."\n";
			}
			// ナチュラルソート
			ksort($_pages, SORT_NATURAL);
			$ret[] = '<div class="form-inline">';
			$ret[] = '<div class="form-group">';
			$ret[] = '<select class="form-control" name="template_page" class="template">';
			$ret[] = '<option value="" disabled="disabled" selected="selected">-- ' . $_button['template'] . ' --</option>';
			$ret[] = join("\n", $_pages);
			$ret[] = '</select>';
			$ret[] = '</div>';
			$ret[] = '<button type="submit" class="btn btn-secondary" name="template" accesskey="l">' . $_button['load'] . '</button>';
			$ret[] = '</div>';
			unset($_s_page, $_w, $_pages);
		}

		// 編集フォーム
		$ret[] = '<textarea name="msg" id="msg" rows="15" class="form-control">' . self::htmlsc(
			// 作成元のページが存在する場合、そのリンクを書き込むデーターの先頭に付加する
			($load_refer_related && isset($vars['refer']) && !empty($vars['refer']) ? '[[' . self::stripBracket($vars['refer']) . ']]' . "\n\n" : '') .
			$postdata
		) . '</textarea>';
		$ret[] = '<div class="form-inline">';
		if (IS_MOBILE){
			// モバイル用
			$ret[] = '<input type="submit" id="btn_submit" name="write" value="'.$_button['update'].'" data-icon="check" data-inline="true" data-theme="b" />';
			$ret[] = '<input type="submit" id="btn_preview" name="preview" value="'.$_button['preview'].'" accesskey="p" data-icon="gear" data-inline="true" data-theme="e" />';
			$ret[] = '<input type="submit" id="btn_cancel" name="cancel" value="'.$_button['cancel'].'" accesskey="c" data-icon="delete" data-inline="true" />';
			$ret[] = $notimeupdate === 2 && Auth::check_role('role_contents_admin') ? '<div data-role="fieldcontain">' : null;
			if ($notimeupdate !== 0 && Factory::Wiki($page)->isValied()){
				// タイムスタンプを更新しないのチェックボックス
				$ret[] = '<input type="checkbox" name="notimestamp" id="_edit_form_notimestamp" value="true" ' . (isset($vars['notimestamp']) ? ' checked="checked"' : null) . ' />';
				$ret[] = '<label for="_edit_form_notimestamp" data-inline="true">'.$_button['notchangetimestamp'].'</label>';
			}
			// 管理人のパス入力
			$ret[] = $notimeupdate == 2 && Auth::check_role('role_contents_admin') ? '<input type="password" name="pass" size="12"  data-inline="true" />' . "\n" . '</div>' : null;
			$ret[] = isset($vars['add']) ? '<input type="checkbox" name="add_top" value="true"' . (isset($vars['add']) ? ' checked="checked"' : '') . ' /><label for="add_top">' . $_button['addtop'] . '</label>' : null;
		}else{
			// 通常用
			$ret[] = '<button type="submit" class="btn btn-primary" name="write" accesskey="s"><span class="fa fa-check"></span>' . $_button['update'] . '</button>';
			$ret[] = isset($vars['add']) ? '<input type="checkbox" name="add_top" value="true"' . (isset($vars['add']) ? ' checked="checked"' : '') . ' /><label for="add_top">' . $_button['addtop'] . '</label>' : null;
			$ret[] = '<button type="submit" class="btn btn-secondary" name="preview" accesskey="p"><span class="fa fa-eye"></span>' . $_button['preview'] . '</button>';
			if ($notimeupdate !== 0 && Factory::Wiki($page)->isValied()){
				// タイムスタンプを更新しないのチェックボックス
				$ret[] = '<div class="checkbox">';
				$ret[] = '<input type="checkbox" name="notimestamp" id="_edit_form_notimestamp" value="true"' . (isset($vars['notimestamp']) ? ' checked="checked"' : null) . ' />';
				$ret[] = '<label for="_edit_form_notimestamp">' . $_button['notchangetimestamp'] . '</label>';
				$ret[] = '</div>';
			//	$ret[] = '<div class="checkbox">';
			//	$ret[] = '<input type="checkbox" name="ping" id="_edit_form_ping" value="true"' . (isset($vars['ping']) ? ' checked="checked"' : null) . ' />';
			//	$ret[] = '<label for="_edit_form_ping">' . $_button['send_ping'] . '</label>';
			//	$ret[] = '</div>';
			//	$ret[] = '<div class="checkbox">';
			//	$ret[] = '<input type="checkbox" name="tweet" id="_edit_form_tweet" value="true"' . (isset($vars['tweet']) ? ' checked="checked"' : null) . ' />';
			//	$ret[] = '<label for="_edit_form_tweet"><span class="fa  fa-twitter"></span></label>';
			//	$ret[] = '</div>';
			//	$ret[] = '<div class="checkbox">';
			//	$ret[] = '<input type="checkbox" name="ping" id="_edit_form_fb" value="true"' . (isset($vars['facebook']) ? ' checked="checked"' : null) . ' />';
			//	$ret[] = '<label for="_edit_form_tweet"><span class="fa  fa-facebook"></span></label>';
			//	$ret[] = '</div>';
			}
			// 管理人のパス入力
			if ($notimeupdate === 2 && Auth::check_role('role_contents_admin')) {
				$ret[] = '<div class="form-group">';
				$ret[] = '<div class="input-group">';
				$ret[] = '<span class="input-group-addon"><span class="fa fa-key"></span></span>';
				$ret[] = '<input type="password" name="pass" class="form-control" size="12" placeholder="Password" />';
				$ret[] = '</div>';
				$ret[] = '</div>';
			}
			$ret[] = '<button type="submit" class="btn btn-warning" name="cancel" accesskey="c"><span class="fa fa-ban"></span>' . $_button['cancel'] . '</button>';
		}
		$ret[] = '</div>';
		$ret[] = '</form>';

		if (isset($vars['help'])) {
			// テキストの整形ルールを表示
			$rule_wiki = Factory::Wiki(self::RULE_PAGENAME);
			$ret[] = '<hr />';
			$ret[] =  $rule_wiki->has() ? $rule_wiki->render() : '<p class="alert alert-warning">Sorry, page \'' . Utility::htmlsc(self::RULE_PAGENAME) .'\' unavailable.</p>';
		} else {
			$ret[] = '<ul><li><a href="' . Factory::Wiki($page)->uri('edit',array('help'=>'true')) . '" id="FormatRule">' . $_string['help'] . '</a></li></ul>';
		}
		return join("\n", $ret);
	}
	/**
	 * 競合を出力（do_update_diff）
	 * @param string $pagestr ページのソース
	 * @param string $poststr 編集したWikiデーター
	 * @param string $original 編集時に送信されたオリジナルのソース
	 */
	public static function showCollision($pagestr, $poststr, $original)
	{
		global $_string;
		$obj = new LineDiff();

		$obj->set_str('left', $original, $pagestr);
		$obj->compare();
		$diff1 = $obj->toArray();

		$obj->set_str('right', $original, $poststr);
		$obj->compare();
		$diff2 = $obj->toArray();

		$arr = $obj->arr_compare('all', $diff1, $diff2);

		global $do_update_diff_table;
		$table = array();
		$table[] = '<div class="table_wrapper">';
		$table[] = '<table class="table table_center">';
		$table[] = '<caption>' . $_string['collided_caption'] . '</caption>';
		$table[] = '<colgroup span="1" />';
		$table[] = '<colgroup span="1" />';
		$table[] = '<thead>';
		$table[] = '<tr>';
		$table[] = '<th>l</th>';
		$table[] = '<th>r</th>';
		$table[] = '<th>text</th>';
		$table[] = '</tr>';
		$table[] = '</thead>';
		$table[] = '<tbody>';

		foreach ($arr as $_obj) {
			$table[] = ' <tr>';
			$params = array($_obj->get('left'), $_obj->get('right'), $_obj->text());
			foreach ($params as $key => $text) {
				$text = self::htmlsc(rtrim($text));
				$table[] = '<td>' .$text .'</td>';
			}
			$table[] = ' </tr>';
		}
		$table[] =  '</tbody>';
		$table[] =  '</table>';

		$do_update_diff_table = implode("\n", $table) . "\n";
		unset($table);

		$body = array();
		foreach ($arr as $_obj) {
			if ($_obj->get('left') != '-' && $_obj->get('right') != '-') {
				$body[] = $_obj->text();
			}
		}

		return join("\n",$body);
	}
	/**
	 * ダンプ
	 */
	public static function dump($type = 'honeypot') {
		global $get, $post, $vars, $cookie;

		// Logging for SPAM Address
		// NOTE: Not recommended use Rental Server
		if (self::SAVE_BLACKLIST === TRUE) {
			$line = array(
				self::getRemoteIp(),
				UTIME,
				$type,
				$_SERVER['HTTP_USER_AGENT']
			);
			error_log( join("\t",$line) . "\n", 3, CACHE_DIR . 'blacklist.log');
			unset($line);
		}

		if (self::SPAM_LOGGING === TRUE){
			// Logging for SPAM Report
			// NOTE: Not recommended use Rental Server
			$lines = array(
				'----' . date('Y-m-d H:i:s', time()),
				'[ADDR]' . self::getRemoteIp() . "\t" . $_SERVER['HTTP_USER_AGENT'],
				'[SESS]',
				var_export($cookie, TRUE),
				'[GET]',
				var_export($get, TRUE),
				'[POST]',
				var_export($post, TRUE),
				'[VARS]',
				var_export($vars, TRUE)
			);
			error_log( join("\n",$lines) . "\n", 3, CACHE_DIR . $type . '.log');
			unset($lines);
		}
	}
}

// hex2bin -- Converts the hex representation of data to binary
// (PHP 5.4.0)
// Inversion of bin2hex()
if (! function_exists('hex2bin')) {
	function hex2bin($hex_string) {
		// preg_match : Avoid warning : pack(): Type H: illegal hex digit ...
		// (string)   : Always treat as string (not int etc). See BugTrack2/31
		return preg_match('/^[0-9a-f]+$/i', $hex_string) ?
			pack('H*', (string)$hex_string) : $hex_string;
	}
}

/* End of file Utility.php */
/* Location: /vender/PukiWiki/Lib/Utility.php */