<?php
/**
 * ユーティリティクラス
 *
 * @package   PukiWiki
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2013 PukiWiki Advance Developers Team
 * @create    2012/12/31
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Utility.php,v 1.0.0 2013/03/11 08:04:00 Logue Exp $
 **/

namespace PukiWiki;

use PukiWiki\Auth\Auth;
use PukiWiki\Renderer\RendererDefines;
use PukiWiki\Renderer\InlineFactory;
use PukiWiki\Renderer\PluginRenderer;
use PukiWiki\Renderer\Header;
use PukiWiki\Router;
use PukiWiki\Render;
use PukiWiki\Factory;
use PukiWiki\Listing;
use Zend\Http\Response;
use Zend\Math\Rand;

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
	const SPAM_PATTERN = '#(?:cialis|hydrocodone|viagra|levitra|tramadol|xanax|\[/link\]|\[/url\])#i';

	/**
	 * QueryStringをパースし、$_GETに上書き
	 * @return void
	 */
	public static function parseArguments(){
		global $_GET, $_SERVER, $REQUEST_URI, $HTTP_SERVER_VARS, $HTTP_GET_VARS, $HTTP_POST_VARS, $_REQUEST;
		global $get, $post, $vars, $cookie;
		global $defaultpage;

		/////////////////////////////////////////////////
		// GET, POST, COOKIE
		$get    = self::stripNullBytes($_GET);
		$post   = self::stripNullBytes($_POST);
		$cookie = self::stripNullBytes($_COOKIE);

		// 安全のためデフォルトの外部変数はアンセット
		unset($_GET, $_POST, $_COOKIE);

		$arg = '';
		if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
			$arg = $_SERVER['QUERY_STRING'];
		//} else if (array_key_exists('PATH_INFO',$_SERVER) and !empty($_SERVER['PATH_INFO']) ) {
		//	$arg = preg_replace("/^\/*(.+)\/*$/","$1",$_SERVER['PATH_INFO']);
		} else if (isset($_SERVER['argv']) && ! empty($_SERVER['argv'])) {
			$arg = $_SERVER['argv'][0];
		}

		if (strlen($arg) > self::MAX_QUERY_STRING_LENGTH) {
			// Something nasty attack?
			self::dieMessage(_('Query string is too long.'));
		}
		$arg = str_replace('+','%20',self::stripNullBytes($arg)); // \0 除去
		// for QA/250

		// unset QUERY_STRINGs
		//foreach (array('QUERY_STRING', 'argv', 'argc') as $key) {
		// For OpenID Lib (use QUERY_STRING).
		foreach (array('argv', 'argc') as $key) {
			unset(${$key}, $_SERVER[$key], $HTTP_SERVER_VARS[$key]);
		}
		// $_SERVER['REQUEST_URI'] is used at func.php NOW
		unset($REQUEST_URI, $HTTP_SERVER_VARS['REQUEST_URI']);
		
		// Expire risk
		unset($HTTP_GET_VARS, $HTTP_POST_VARS);	//, 'SERVER', 'ENV', 'SESSION', ...
		unset($_REQUEST);	// Considered harmful

		/////////////////////////////////////////////////
		// QUERY_STRINGを分解してコード変換し、$get に上書き
		// URI を urlencode せずに入力した場合に対処する
		if (empty($arg)){
			// Queryがない場合
			$get['cmd'] = 'read';
			$get['page'] = $defaultpage;
		}else if (strpbrk('=', $arg)){
			// =が含まれている場合querystringの配列とみなし、パースする
			$matches = array();
			foreach (explode('&', $arg) as $key_and_value) {
				if (preg_match('/^([^=]+)=(.+)/', $key_and_value, $matches)) {
					$key = trim($matches[1]);
					$value = trim(rawurldecode($matches[2]));
					if (empty($value)) continue;
					$get[$key] = $value;
				}
			}
			unset($matches);
			// if (!isset($get['page'])) $get['page'] = '';	// 本当は不要
		} else {
			// そうでない場合はすべてページ名とみなす
			$get['cmd'] = 'read';
			$get['page'] = rawurldecode($arg);
		}
		
		// 外部からの変数を$vars配列にマージする
		if (empty($post)) {
			$method = 'GET';
			$vars = $get;  // Major pattern: Read-only access via GET
		} else if (empty($get)) {
			$method = 'POST';
			$vars = $post; // Minor pattern: Write access via POST etc.
		} else {
			$method = 'GET and POST';
			$vars = array_merge($get, $post); // Considered reliable than $_REQUEST
		}

		if (! isset($vars['cmd'])){
			// プラグイン名が指定されていない場合readプラグインとみなす
			$get['cmd']  = $post['cmd']  = $vars['cmd']  = 'read';
		}else if (!preg_match(PluginRenderer::PLUGIN_NAME_PATTERN, $vars['cmd']) !== FALSE){
			// 入力チェック: cmdの文字列は英数字以外ありえない
			Utility::dieMessage('Plugin name is invalied or too long! (less than 64 chars)');
		}

		// 文字コード変換 ($_POST)
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

		// 整形: msg, 改行を取り除く（ここでチェックするのは間違い。プラグインで実装すべき）
		if (isset($vars['msg'])) {
			// GETメソッドでmsgが送られて来ることはありえない。
			unset($get['msg']);
			$post['msg'] = $vars['msg'] = str_replace("\r", '', $vars['msg']);
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
			if (isset($_SERVER[$x])) return $_SERVER[$x];
		}
		self::dieMessage('Could not get IP address.');	// IPアドレスが取得できない場合、念のため処理を止める
		// return null;
	}
	/**
	 * 相対指定のページ名から全ページ名を取得
	 * @param string $name
	 * @param string $refer
	 * @return string
	 */
	public static function getPageName($name, $refer) {
		global $defaultpage;

		// 'Here'
		if (empty($name) || $name === './') return $refer;

		// Absolute path
		if ($name{0} === '/') {
			$name = substr($name, 1);
			return empty($name) ? $defaultpage : $name;
		}

		// Relative path from 'Here'
		if (substr($name, 0, 2) === './') {
			$arrn    = preg_split('#/#', $name, -1, PREG_SPLIT_NO_EMPTY);
			$arrn[0] = $refer;
			return join('/', $arrn);
		}

		// Relative path from dirname()
		if (substr($name, 0, 3) === '../') {
			$arrn = preg_split('#/#', $name,  -1, PREG_SPLIT_NO_EMPTY);
			$arrp = preg_split('#/#', $refer, -1, PREG_SPLIT_NO_EMPTY);

			while (! empty($arrn) && $arrn[0] == '..') {
				array_shift($arrn);
				array_pop($arrp);
			}
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
	 * 見出しを作る
	 * @param string $str 入力文字列
	 * @param boolean $strip 見出し編集用のアンカーを削除する
	 * @return string
	 */
	public static function setHeading(& $str, $strip = TRUE)
	{
		// Cut fixed-heading anchors
		$id = '';
		$matches = array();
		if (preg_match('/^(\*{0,3})(.*?)\[#([A-Za-z][\w-]+)\](.*?)$/m', $str, $matches)) {	// 先頭が*から始まってて、なおかつ[#...]が存在する
			$str = $matches[2] . $matches[4];
			$id  = & $matches[3];
		} else {
			$str = preg_replace('/^\*{0,3}/', '', $str);
		}

		// Cut footnotes and tags
		if ($strip === TRUE)
			$str = self::stripHtmlTags(InlineFactory::factory(preg_replace('/'.RendererDefines::NOTE_PATTERN.'/ex', '', $str)));

		return $id;
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
	 * WebDAVからのアクセスか
	 */
	public static function isWebDAV()
	{
		global $log_ua;
		static $status = false;
		if ($status) return true;

		static $ua_dav = array(
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

		switch($_SERVER['REQUEST_METHOD']) {
			case 'OPTIONS':
			case 'PROPFIND':
			case 'MOVE':
			case 'COPY':
			case 'DELETE':
			case 'PROPPATCH':
			case 'MKCOL':
			case 'LOCK':
			case 'UNLOCK':
				$status = true;
				return $status;
			default:
				continue;
		}

		$matches = array();
		foreach($ua_dav as $pattern) {
			if (preg_match('/'.$pattern.'/', $log_ua, $matches)) {
				$status = true;
				return true;
			}
		}
		return false;
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
	 * Remove null(\0) bytes from variables
	 * NOTE: PHP had vulnerabilities that opens "hoge.php" via fopen("hoge.php\0.txt") etc.
	 * [PHP-users 12736] null byte attack
	 * http://ns1.php.gr.jp/pipermail/php-users/2003-January/012742.html
	 *
	 * 2003-05-16: magic quotes gpcの復元処理を統合
	 * 2003-05-21: 連想配列のキーはbinary safe
	 *
	 * @param string $param
	 * @return string
	 */
	public static function stripNullBytes($param)
	{
		static $magic_quotes_gpc;
		if ($magic_quotes_gpc === NULL)
			$magic_quotes_gpc = get_magic_quotes_gpc();

		if (is_array($param)) {
			return array_map('input_filter', $param);
		}
		$result = str_replace('\0', '', $param);
		if ($magic_quotes_gpc) $result = stripslashes($result);
		return $result;
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
	 * WikiNameからHTMLタグを除く
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
	public static function dieMessage($msg = '', $error_title='', $http_code = Response::STATUS_CODE_500){
		global $_string, $_title, $_button, $vars;

		// エラーメッセージの内容
		$body[] = '<p>[ ';
		if ( isset($vars['page']) && !empty($vars['page']) ){
			$body[] = '<a href="' . Factory::Wiki($vars['page'])->uri() .'">'.$_button['back'].'</a> | ';
			$body[] = '<a href="' . Router::get_cmd_uri('edit',$vars['page']) . '">Try to edit this page</a> | ';
		}
		$body[] = '<a href="' . Router::get_cmd_uri() . '">Return to FrontPage</a> ]</p>';
		$body[] = '<div class="message_box ui-state-error ui-corner-all" style="padding:.5em;">';
		$body[] = '<p><span class="ui-icon ui-icon-alert" style="display:inline-block;"></span> <strong>' . $_title['error'] . '</strong>';
		$body[] = PKWK_WARNING !== true || empty($msg) ? $msg = $_string['error_msg'] : $msg;
		$body[] = '</p>';
		$body[] = '</div>';
		if (DEBUG) {
			$body[] = '<div class="message_box ui-state-highlight ui-corner-all" style="padding:.5em;">';
			$body[] = '<p><span class="ui-icon ui-icon-info" style="display:inline-block;"></span> <strong>Back Trace</strong></p>';
			$body[] = '<ol>';
			foreach (debug_backtrace() as $k => $v) {
				if ($k < 2) { 
					continue;
				}
				array_walk($v['args'], function (&$item, $key) {
					$item = var_export($item, true);
				});
				$body[] = '<li>' . (isset($v['file']) ? $v['file'] : '?') . '(<var>' . (isset($v['line']) ? $v['line'] : '?') . '</var>):<br /><code>' . (isset($v['class']) ? '<strong>' . $v['class'] . '</strong>-&gt;' : '') . $v['function'] . '(<var>' . implode(', ', $v['args']) . '</var>)</code></li>' . "\n";
			}
			$body[] = '</ol>';
			$body[] = '</div>';
		}

		new Render($error_title, join("\n",$body), $http_code);
		exit();
	}
	/**
	 * ページが見つからない
	 * @return void 
	 */
	public static function notFound(){
		global $vars, $_button;
		$body[] = '<p>[ ';
		if ( isset($vars['page']) && !empty($vars['page']) ){
			$body[] = '<a href="' . Factory::Wiki($vars['page'])->uri() .'">'.$_button['back'].'</a> | ';
			$body[] = '<a href="' . Router::get_cmd_uri('edit',$vars['page']) . '">Try to edit this page</a> | ';
		}
		$body[] = '<a href="' . Router::get_cmd_uri() . '">Return to FrontPage</a> ]</p>';
		$body[] = '<div class="message_box ui-state-error ui-corner-all" style="padding:.5em;">';
		$body[] = '<p><span class="ui-icon ui-icon-alert" style="display:inline-block;"></span> <strong>Page not found</strong>';
		$body[] = 'Sorry, but the page you were trying to view does not exist or deleted.';
		if ( isset($vars['page']) && !empty($vars['page']) ){
			$body[] = '<br />' . "\n" . sprintf(
				'Please check <a href="%1s" rel="nofollow">backups</a> or <a href="%2s" rel="nofollow">create page</a>.',
				Router::get_cmd_uri('backup',$vars['page']),
				Router::get_cmd_uri('edit',$vars['page'])
			);
		}
		$body[] = '</p>';
		$body[] = '</div>';
		$body[] = '<script type="text/javascript">/' .'* <![CDATA *' . '/';
		$body[] = 'var GOOG_FIXURL_LANG = (navigator.language || null).slice(0,2), GOOG_FIXURL_SITE = location.host;';
		$body[] = '/' . '* ]]> *' . '/</script>';
		$body[] = '<script type="text/javascript" src="http://linkhelp.clients.google.com/tbproxy/lh/wm/fixurl.js"></script>';
		new Render('Page not found', join("\n",$body), Response::STATUS_CODE_404);
		exit();
	}
	/**
	 * リダイレクト
	 * @param string $url リダイレクト先
	 * @param int $time リダイレクトの待ち時間
	 */
	public static function redirect($url = '', $time = 0){
		global $vars;
		$response = new Response();

		// URLが空の場合、ページのアドレスか、スクリプトのアドレスを返す
		if (empty($url)){
			$url = isset($vars['page']) ? Router::get_resolve_uri(null, $vars['page']) : Router::get_script_uri();
		}
		$s_url = self::htmlsc($url);
		$response->setStatusCode(Response::STATUS_CODE_301);
		if (!DEBUG){
			$response->getHeaders()->addHeaderLine('Location', $s_url);
		}
		$html = array();
		$html[] = '<!doctype html>';
		$html[] = '<html>';
		$html[] = '<head>';
		$html[] = '<meta charset="utf-8">';
		$html[] = '<meta name="robots" content="NOINDEX,NOFOLLOW" />';
		if (!DEBUG){
			$html[] = '<meta http-equiv="refresh" content="'.$time.'; URL='.$s_url.'" />';
		}
		$html[] = '<link rel="stylesheet" href="http://code.jquery.com/ui/' . JQUERY_UI_VER . '/themes/base/jquery-ui.css" type="text/css" />';
		$html[] = '<title>301 Moved Permanently</title>';
		$html[] = '</head>';
		$html[] = '<body>';
		$html[] = '<div class="message_box ui-state-highlight ui-corner-all">';
		$html[] = '<p style="padding:0 .5em;">';
		$html[] = '<span class="ui-icon ui-icon-alert" style="display:inline-block;"></span>';
		$html[] = 'The requested page has moved to a new URL. <br />';
		$html[] = 'Please click <a href="'.$s_url.'">here</a> if you do not want to move even after a while.';
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
	 * @param string $postdata 入力データー
	 * @param boolean $show_template テンプレートを表示するか
	 */
	public static function editForm($page, $postdata, $show_template = TRUE)
	{
		global $vars, $session;
		global $_button, $_string;
		global $notimeupdate, $load_template_func, $load_refer_related;

		if (empty($page)) return self::dieMessage('Page name was not defined.');

		$wiki = Factory::Wiki($page);
		$original = isset($vars['original']) ? $vars['original'] : $postdata;

		// ticketは、PliginRenderer::addHiddenField()で自動挿入されるので、同じアルゴリズムでチケット名を生成
		$ticket_name = md5(Utility::getTicket() . REMOTE_ADDR);
		// BugTrack/95 fix Problem: browser RSS request with session
		$session->offsetSet('origin-'.$ticket_name, md5(self::getTicket() . str_replace("\r", '', $original)));

		$ret[] = '<form action="' . Router::get_script_uri() . '" method="post" id="form">';
		$ret[] = '<input type="hidden" name="cmd" value="edit" />';
		$ret[] = '<input type="hidden" name="page" value="' . self::htmlsc($page) .'" />';
		$ret[] = isset($vars['id']) ? '<input type="hidden" name="id" value="' . self::htmlsc($vars['id']) . '" />' : null;
		// 元々のテキスト（比較用）
		$ret[] = '<textarea id="original" name="original" rows="1" cols="1" style="display:none">' . self::htmlsc($original) . '</textarea>';
		
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
			$ret[] = '<div class="template_form">';
			$ret[] = '<select name="template_page" class="template">';
			$ret[] = '<option value="" disabled="disabled" selected="selected">-- ' . $_button['template'] . ' --</option>';
			$ret[] = join("\n", $_pages);
			$ret[] = '</select>';
			$ret[] = '<input type="submit" name="template" value="' . $_button['load'] . '" accesskey="l" />';
			$ret[] = '</div>';
			unset($_s_page, $_w, $_pages);
		}
		// 編集フォーム
		$ret[] = '<div class="edit_form">';
		$ret[] = '<textarea name="msg" id="msg" rows="20" rows="80">' . self::htmlsc(
			// 作成元のページが存在する場合、そのリンクを書き込むデーターの先頭に付加する
			($load_refer_related && isset($vars['refer']) && !empty($vars['refer']) ? '[[' . self::stripBracket($vars['refer']) . ']]' . "\n\n" : '') .
			$postdata
		) . '</textarea>';
		
		if (IS_MOBILE){
			// モバイル用
			$ret[] = '<input type="submit" id="btn_submit" name="write" value="'.$_button['update'].'" data-icon="check" data-inline="true" data-theme="b" />';
			$ret[] = '<input type="submit" id="btn_preview" name="preview" value="'.$_button['preview'].'" accesskey="p" data-icon="gear" data-inline="true" data-theme="e" />';
			$ret[] = '<input type="submit" id="btn_cancel" name="cancel" value="'.$_button['cancel'].'" accesskey="c" data-icon="delete" data-inline="true" />';
			$ret[] = $notimeupdate === 2 && Auth::check_role('role_contents_admin') ? '<div data-role="fieldcontain">' : null;
			if ($notimeupdate !== 0 && $wiki->isValied()){
				// タイムスタンプを更新しないのチェックボックス
				$ret[] = '<input type="checkbox" name="notimestamp" id="_edit_form_notimestamp" value="true" ' . (isset($vars['notimestamp']) ? ' checked="checked"' : null) . ' />';
				$ret[] = '<label for="_edit_form_notimestamp" data-inline="true">'.$_button['notchangetimestamp'].'</label>';
			}
			// 管理人のパス入力
			$ret[] = $notimeupdate == 2 && Auth::check_role('role_contents_admin') ? '<input type="password" name="pass" size="12"  data-inline="true" />' . "\n" . '</div>' : null;
			$ret[] = isset($vars['add']) ? '<input type="checkbox" name="add_top" value="true"' . (isset($vars['add']) ? ' checked="checked"' : '') . ' /><label for="add_top">' . $_button['addtop'] . '</label>' : null;
		}else{
			// 通常用
			$ret[] = '<input type="submit" id="btn_submit" name="write" value="' . $_button['update'] . '" accesskey="s" />';
			$ret[] = isset($vars['add']) ? '<input type="checkbox" name="add_top" value="true"' . (isset($vars['add']) ? ' checked="checked"' : '') . ' /><label for="add_top">' . $_button['addtop'] . '</label>' : null;
			$ret[] = '<input type="submit" id="btn_preview" name="preview" value="' . $_button['preview'] . '" accesskey="p" />';
			if ($notimeupdate !== 0 && $wiki->isValied()){
				// タイムスタンプを更新しないのチェックボックス
				$ret[] = '<input type="checkbox" name="notimestamp" id="_edit_form_notimestamp" value="true"' . (isset($vars['notimestamp']) ? ' checked="checked"' : null) . ' />';
				$ret[] = '<label for="_edit_form_notimestamp">' . $_button['notchangetimestamp'] . '</label>';
			}
			// 管理人のパス入力
			$ret[] = $notimeupdate === 2 && Auth::check_role('role_contents_admin') ? '<input type="password" name="pass" size="12" placeholder="Password" />' : null;
			$ret[] = '<input type="submit" id="btn_cancel" name="cancel" value="' . $_button['cancel'] . '" accesskey="c" />';
		}
		$ret[] = '</div>';
		$ret[] = '</form>';

		if (isset($vars['help'])) {
			// テキストの整形ルールを表示
			global $rule_page;
			$rule_wiki = Factory::Wiki($rule_page);
			$ret[] = '<hr />';
			$ret[] =  $rule_wiki->has() ?  $rule_wiki->render() : '<p>Sorry, page \'' . Utility::htmlsc($rule_page) .'\' unavailable.</p>';
		} else {
			$ret[] = '<ul><li><a href="' . $wiki->uri('edit',array('help'=>'true')) . '" id="FormatRule">' . $_string['help'] . '</a></li></ul>';
		}
		return join("\n", $ret);
	}
	/**
	 * ダンプ
	 */
	public static function dump($type = 'honeypot.log') {
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
		self::dieMessage('Spam Protection','Spam Protection', 500);
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