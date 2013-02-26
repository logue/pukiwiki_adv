<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: Utility.php,v 1.0.0 2012/12/31 18:18:00 Logue Exp $
// Copyright (C)
//   2012 PukiWiki Advance Developers Team
// License: GPL v2 or (at your option) any later version

namespace PukiWiki;

use PukiWiki\Renderer\RendererDefines;
use PukiWiki\Renderer\InlineFactory;
use PukiWiki\Router;
use PukiWiki\Factory;
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
			'!^(?:'.$scheme.')://'					// scheme
			. '(?:\w+:\w+@)?'						// ( user:pass )?
			. '('
			. '(?:[-_0-9a-z]+\.)+(?:[a-z]+)\.?|'	// ( domain name |
			. '\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}|'	//   IP Address  |
			. 'localhost'							//   localhost )
			. ')'
			. '(?::\d{1,5})?(?:/|$)!iD'				// ( :Port )?
		);
		// 正規処理
		$ret = preg_match($pattern, $str);
		// マッチしない場合は0が帰るのでFALSEにする
		return ($ret === 0) ? FALSE : $ret;
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
		static $magic_quotes_gpc = NULL;
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
	 * 他のページを読み込むときに余計なものを取り除く
	 * @param string $str
	 * @return string
	 */
	public static function replaceFilter($str){
		global $filter_rules;
		static $patternf, $replacef;

		if (!isset($patternf)) {
			$patternf = array_map(create_function('$a','return "/$a/";'), array_keys($filter_rules));
			$replacef = array_values($filter_rules);
			unset($filter_rules);
		}
		return preg_replace($patternf, $replacef, $str);
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
	public static function dieMessage($msg = '', $error_title='', $http_code = 500){
		global $skin_file, $page_title, $_string, $_title, $_button, $vars;

		$title = !empty($error_title) ? $error_title : $_title['error'];
		$page = $_title['error'];

		if (PKWK_WARNING !== true || empty($msg)){	// PKWK_WARNINGが有効でない場合は、詳細なエラーを隠す
			$msg = $_string['error_msg'];
		}
		$ret = array();
		$ret[] = '<p>[ ';
		if ( isset($vars['page']) && !empty($vars['page']) ){
			$ret[] = '<a href="' . Factory::Wiki($vars['page'])->uri() .'">'.$_button['back'].'</a> | ';
			$ret[] = '<a href="' . Router::get_cmd_uri('edit',$vars['page']) . '">Try to edit this page</a> | ';
		}
		$ret[] = '<a href="' . get_cmd_uri() . '">Return to FrontPage</a> ]</p>';
		$ret[] = '<div class="message_box ui-state-error ui-corner-all">';
		$ret[] = '<p style="padding:0 .5em;"><span class="ui-icon ui-icon-alert" style="display:inline-block;"></span> <strong>' . $_title['error'] . '</strong> ' . $msg . '</p>';
		$ret[] = '</div>';
		$body = join("\n",$ret);

		global $trackback;
		$trackback = 0;

		if (!headers_sent()){
			pkwk_common_headers(0,0, $http_code);
		}

		if(defined('SKIN_FILE')){
			if (file_exists(SKIN_FILE) && is_readable(SKIN_FILE)) {
				catbody($page, $title, $body);
			} elseif ( !empty($skin_file) && file_exists($skin_file) && is_readable($skin_file)) {
				define('SKIN_FILE', $skin_file);
				catbody($page, $title, $body);
			}
		}else{
			$html = array();
			$html[] = '<!doctype html>';
			$html[] = '<html>';
			$html[] = '<head>';
			$html[] = '<meta charset="utf-8">';
			$html[] = '<meta name="robots" content="NOINDEX,NOFOLLOW" />';
			$html[] = '<link rel="stylesheet" href="http://code.jquery.com/ui/' . JQUERY_UI_VER . '/themes/base/jquery-ui.css" type="text/css" />';
			$html[] = '<title>' . $page . ' - ' . $page_title . '</title>';
			$html[] = '</head>';
			$html[] = '<body>' . $body . '</body>';
			$html[] = '</html>';
			echo join("\n",$html);
		}
		pkwk_common_suffixes();
		die();
	}
	/**
	 * リダイレクト
	 * @param string $url リダイレクト先
	 */
	public static function redirect($url = '', $time = 0){
		global $vars;
		$response = new Response();

		if (empty($url)){
			$url = isset($vars['page']) ? Router::get_page_uri($vars['page']) : Router::get_script_uri();
		}
		$s_url = self::htmlsc($url);
		$response->setStatusCode(301);
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
		$html[] = 'Please click <a href="'.$s_url.'">here</a> if you do not want to move even after a while.</p>';
		$html[] = '</div>';
		$html[] = '</body>';
		$html[] = '</html>';
		$response->setContent(join("\n",$html));

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