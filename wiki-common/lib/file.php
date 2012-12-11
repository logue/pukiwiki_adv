<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: file.php,v 1.95.7 2012/12/11 19:55:00 Logue Exp $
// Copyright (C)
//   2010-2012 PukiWiki Advance Developers Team
//   2005-2009 PukiWiki Plus! Team
//   2002-2009,2011 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// File related functions

// RecentChanges
defined('PKWK_MAXSHOW_ALLOWANCE')		or define('PKWK_MAXSHOW_ALLOWANCE', 10);
defined('PKWK_MAXSHOW_CACHE')			or define('PKWK_MAXSHOW_CACHE', 'recent');

// AutoLink
defined('PKWK_AUTOLINK_REGEX_CACHE')	or define('PKWK_AUTOLINK_REGEX_CACHE', 'autolink');

// AutoAlias
defined('PKWK_AUTOALIAS_REGEX_CACHE')	or define('PKWK_AUTOALIAS_REGEX_CACHE', 'autoalias');

// Auto Glossary (Plus!)
defined('PKWK_GLOSSARY_REGEX_CACHE')	or define('PKWK_GLOSSARY_REGEX_CACHE',  'glossary');

// AutoAlias AutoBase cache (Plus!)
defined('PKWK_AUTOBASEALIAS_CACHE')		or define('PKWK_AUTOBASEALIAS_CACHE', 'autobasealias');

// PageReading cache (Adv.)
defined('PKWK_PAGEREADING_CACHE')		or define('PKWK_PAGEREADING_CACHE', 'PageReading');

// Timestamp prefix
defined('PKWK_TIMESTAMP_PREFIX')		or define('PKWK_TIMESTAMP_PREFIX', 'timestamp-');

// Exsists prefix
defined('PKWK_EXISTS_PREFIX')			or define('PKWK_EXISTS_PREFIX', 'exists-');

// Page cache prefix
defined('PKWK_PAGECACHE_PREFIX')		or define('PKWK_PAGECACHE_PREFIX', 'page-');

//
defined('PKWK_CAPTCHA_SESSION_PREFIX')	or define('PKWK_CAPTCHA_SESSION_PREFIX', 'captcha-');

/*
 * ファイルインターフェース
 */
interface Storage {
	/**
	 * 存在確認
	 */
	public function has();
	/**
	 * 読み込み
	 */
	public function get($join = false);
	/**
	 * 書き込み
	 */
	public function set($str);
	/**
	 * ファイルサイズ
	 */
	public function size();
	/**
	 * MD5ハッシュ
	 */
	public function digest();
	/**
	 * 削除
	 */
	public function remove();
	/**
	 * 更新時刻
	 */
	public function getTime();
	/**
	 * 更新時間を指定
	 */
	public function setTime($value);
	/**
	 * 経過時間
	 */
	public function getPassage();
	/**
	 * アクセス時間
	 */
	public function getAtime();
	/**
	 * アクセス時間を指定
	 */
	public function setAtime($value);
	/**
	 * ファイルの中身
	 */
	public function __toString();

}
/**
 * ファイルの読み書きを行うクラス
 */
class File implements Storage{
	const LOCK_FILE = 'chown.lock';

	protected $filename;

	/**
	 * コンストラクタ
	 * @param string $filename ファイル名（パスも含めること）
	 */
	public function __construct($filename) {
		if (empty($filename)){
			throw new Exception('File name is missing!');
		}

		$this->filename = realpath($filename);
	}
	/**
	 * デストラクタ
	 */
	public function __destruct() {
		// ロックファイルを開放
		//if (file_exists($this->lockfile)) unlink($this->lockfile);
	}
	/**
	 * ファイルが存在するか
	 * @return boolean
	 */
	public function has(){
		return file_exists($this->filename);
	}
	/**
	 * ファイルの指定行数を取得
	 * @param int $count 読み込む行数
	 * @return array
	 */
	public function head($count = 1){
		// Read top N lines as an array
		// (Use PHP file() function if you want to get ALL lines)
		if (!$this->exists()) return false;

		$fp = fopen($this->filename, 'r');
		if ($fp === FALSE) return FALSE;

		set_file_buffer($fp, 0);
		flock($fp, LOCK_SH);
		rewind($fp);
		$index  = 0;
		$array = array();
		while (! feof($fp)) {
			$line = fgets($fp);
			if ($line != FALSE) $array[] = $line;
			if (++$index >= $count) break;
		}
		flock($fp, LOCK_UN);
		if (! fclose($fp)) return FALSE;

		return $array;
	}
	/**
	 * ファイルの内容を取得
	 * @param boolean $join 行を含めた文字列として読み込むか、行を配列として読み込むかのフラグ
	 * @return string or array
	 */
	public function get($join = false){
		if (!$this->has()) return false;
		$result = $join ? '' : array();
		// Compat for "implode('', get_source($file))",
		// 	-- this is slower than "get_source($file, TRUE, TRUE)"
		// Compat for foreach(get_source($file) as $line) {} not to warns

		$fp = fopen($this->filename, 'r');
		if ($fp === FALSE) return FALSE;

		flock($fp, LOCK_SH);	// ロック
		if ($join) {
			// 文字列として処理する場合
			$size = filesize($this->filename);
			if ($size === FALSE) {
				$result = FALSE;
			} else if ($size == 0) {
				$result = null;
			} else {
				$result = fread($fp, $size);	// Returns a value
			}
		} else {
			// 配列として出力する場合
			$result = file($this->filename);	// Returns an array
		}
		flock($fp, LOCK_UN);	// ロックを解除

		fclose($fp);

		if ($result !== FALSE) {
			// Removing line-feeds
			$result = str_replace("\r", '', $result);
		}
		return $result;
	}
	/**
	 * ファイルの書き込み処理
	 * @param string $str 書き込む文字列
	 * @return boolean
	 */
	public function set($str){
		// 書き込むものがなかった場合、削除とみなす
		if (empty($str)) return $this->remove();
		// タイムスタンプが数値でなくtrueの場合、現在のタイムスタンプを保持
		// 記入するデーターの整形
		$str = rtrim(preg_replace('/' . "\r" . '/', '', $str)) . "\n";
		// ファイルを書き込む場所を確保
		if (!$this->has()) $this->chown();
		// ファイルを書き込む
		$fp = fopen($this->filename,'w');
		if ($fp === FALSE) return FALSE;
		flock($fp, LOCK_EX);
		$ret = fputs($fp, $str);
		flock($fp,LOCK_UN);
		fclose($fp);

		return $ret;
	}
	/**
	 * ファイルサイズ
	 * @return int
	 */
	public function size(){
		return $this->has() ? filesize($this->filename) : 0;
	}
	/**
	 * ハッシュ
	 * @return string
	 */
	public function digest(){
		return $this->has() ? md5($this->get(true)) : null;
	}
	/**
	 * ファイルを削除
	 * @return int
	 */
	public function remove(){
		return $this->has() ? unlink($this->filename) : false;
	}
	/**
	 * 更新時刻を取得
	 * @return int
	 */
	public function getTime(){
		return $this->has() ? filemtime($this->filename) : 0;
	}
	/**
	 * 更新日時を指定
	 * @param type $time
	 * @return boolean
	 */
	public function setTime($time){
		return $this->touch($time);
	}
	/**
	 * ファイルの経過時間を取得
	 * @return string
	 */
	public function getPassage(){
		static $units = array('m'=>60, 'h'=>24, 'd'=>1);
		$time = max(0, (MUTIME - $this->getTime()) / 60); // minutes

		foreach ($units as $unit=>$card) {
			if ($time < $card) break;
			$time /= $card;
		}
		$time = floor($time) . $unit;
		return $time;
	}
	/**
	 * アクセス時刻を取得
	 * @return int
	 */
	public function getAtime(){
		return ($this->has()) ? fileatime($this->filename) : 0;
	}
	/**
	 * アクセス日時を指定
	 * @param int $atime
	 * @return boolean
	 */
	public function setAtime($atime){
		return $this->touch($this->getTime(), $atime);
	}
	/**
	 * ファイルの所有者変更
	 * @param boolean $preserve_time 時刻を保持
	 * @return boolean
	 */
	private function chown($preserve_time = TRUE){
		// check UID
		$this->php_uid = extension_loaded('posix') ? posix_getuid() : 0;
		$lockfile = CACHE_DIR . self::LOCK_FILE;

		// Lock for pkwk_chown()
		$flock = fopen($lockfile, 'a') or
			die('pkwk_chown(): fopen() failed for: CACHEDIR/' .
				basename(htmlsc($this->lockfile)));
		flock($flock, LOCK_EX) or die('pkwk_chown(): flock() failed for lock');

		// Check owner
		touch($this->filename);
		$stat = stat($this->filename) or
			die('pkwk_chown(): stat() failed for: '  . basename(htmlsc($this->filename)));
		if ($stat[4] === $this->php_uid) {
			// NOTE: Windows always here
			$result = TRUE; // Seems the same UID. Nothing to do
		} else {
			$tmp = $this->filename . '.' . getmypid() . '.tmp';

			// Lock source $filename to avoid file corruption
			// NOTE: Not 'r+'. Don't check write permission here
			$ffile = fopen($this->filename, 'r') or
				die('pkwk_chown(): fopen() failed for: ' .
					basename(htmlsc($this->filename)));

			// Try to chown by re-creating files
			// NOTE:
			//   * touch() before copy() is for 'rw-r--r--' instead of 'rwxr-xr-x' (with umask 022).
			//   * (PHP 4 < PHP 4.2.0) touch() with the third argument is not implemented and retuns NULL and Warn.
			//   * @unlink() before rename() is for Windows but here's for Unix only
			flock($ffile, LOCK_EX) or die('pkwk_chown(): flock() failed');
			$result = touch($tmp) && copy($this->filename, $tmp) &&
				($preserve_time ? (touch($tmp, $stat[9], $stat[8]) || touch($tmp, $stat[9])) : TRUE) &&
				rename($tmp, $this->filename);
			flock($ffile, LOCK_UN) or die('pkwk_chown(): flock() failed');

			fclose($ffile) or die('pkwk_chown(): fclose() failed');

			if ($result === FALSE) @unlink($tmp);
		}

		// Unlock for pkwk_chown()
		flock($flock, LOCK_UN) or die('pkwk_chown(): flock() failed for lock');
		fclose($flock) or die('pkwk_chown(): fclose() failed for lock');

		return $result;
	}
	/**
	 * ファイルの更新日時を修正
	 *
	 * @param int $time 更新日時
	 * @param int $atime アクセス日時
	 * @return boolean
	 */
	private function touch($time = FALSE, $atime = FALSE){
		// Is the owner incorrected and unable to correct?
		if (! $this->exists() || $this->chown()) {
			if ($time === FALSE) {
				// ファイルの領域を確保
				$result = touch($this->filename);
			} else if ($atime === FALSE) {
				// ファイルの更新日時を指定して領域を確保
				$result = touch($this->filename, $time);
			} else {
				// ファイルの更新日時とアクセス日時を指定して領域を確保
				$result = touch($this->filename, $time, $atime);
			}
			return $result;
		} else {
			die_message('pkwk_touch_file(): Invalid UID and (not writable for the directory or not a flie): ' .
				htmlsc(basename($this->filename)));
		}
	}
	/**
	 * 再帰的にディレクトリを作成
	 * @return boolean
	 */
	protected function mkdir_r(){
		if ($this->exists()) return false;

		$dirname = dirname($this->filename);	// ファイルのディレクトリ名
		// 階層指定かつ親が存在しなければ再帰
		if (strpos($dirname, '/') && !file_exists(dirname($dirname))) {
			// 親でエラーになったら自分の処理はスキップ
			if ($this->mkdir_r(dirname($dirname)) === false) return false;
		}
		return mkdir($dirname);
	}
	/**
	 * 特殊：文字列化（readと等価）
	 * @return string
	 */
	public function __toString(){
		return $this->get(true);
	}
}
/**
 * Fileのファクトリークラス
 */
class FileFactory
{
	public static function Generic($filename){
		static $files;
		if (! isset($files[$filename])) $files[$filename] = new File($filename);
		return $files[$filename];
	}
	public static function Wiki($page){
		static $pages = array();
		if (! isset($files[$page])) $files[$page] = new WikiFile($page);
		return $pages[$page];
	}
	public static function reset(){
		$this->file = array();
	}
}
/**
 * Wikiページクラス
 */
class WikiFile extends File implements Storage{
	/**#@+
	 * 宣言
	 */
	// 拡張子
	const EXT = '.txt';
	// 格納ディレクトリ
	const DIR = DATA_DIR;
	// ページ名として使用可能な文字
	const VALIED_PAGENAME_PATTERN = '/^(?:[\x00-\x7F]|(?:[\xC0-\xDF][\x80-\xBF])|(?:[\xE0-\xEF][\x80-\xBF][\x80-\xBF]))+$/';
	// ページ名に含めることができない文字
	const ILLEGAL_CHARS_PATTERN = '/[%|=|&|?|#|\r|\n|\0|\@|\t|;|\$|+|\\|\[|\]|\||^|{|}]/';
	/**#@-*/

	/**
	 * コンストラクタ
	 * @param string $page
	 */
	public function __construct($page) {
		if (empty($page)){
			throw new Exception('Page name is missing!');
		}
		$this->page = $page;
		$this->filename = self::DIR . encode($page) . self::EXT;
	}
	/**
	 * ページが存在するか
	 * @paran boolean $clearcache
	 * @return boolean
	 */
	public function is_page($clearcache = true){
		if ($clearcache) clearstatcache();
		return $this->has();
	}
	/**
	 * 編集可能か
	 * @staticvar array $is_editable
	 * @return boolean
	 */
	public function is_editable()
	{
		global $edit_auth, $edit_auth_pages, $auth_api, $defaultpage, $_title, $edit_auth_pages_accept_ip;
		if (!$this->is_valied()) return false;
		if ($this->is_freezed()) return false;
		if (auth::check_role('readonly')) return false;

		if (!$edit_auth) return true;

		$info = auth::get_user_info();
		if (!empty($info['key']) &&
		    auth::is_page_readable($page, $info['key'], $info['group']) &&
		    auth::is_page_editable($page, $info['key'], $info['group'])) {
			return true;
		}

		// Basic, Digest 認証を利用していない場合
		if (!$auth_api['plus']['use']) return auth::is_page_readable($this->page, null, null);

		$auth_func_name = get_auth_func_name();
		if ($auth_flag && ! $auth_func_name($page, $auth_flag, $exit_flag, $edit_auth_pages, $_title['cannotedit'])) return false;
		if (auth::is_page_readable($this->page, '', '') && auth::is_page_editable($this->page,'','')) return true;

		return false;
	}
	/**
	 * 凍結されているか
	 * @global boolean $function_freeze
	 * @param boolean $clearcache キャッシュをクリアするか
	 * @return boolean
	 */
	public function is_freezed()
	{
		global $function_freeze;

		if ($function_freeze) return;
		if (!$this->exists()) return false;
		$buffer = parent::head(1);	// 先頭1行のみ読み込む
		return strpos(join('',$buffer),'#freeze');
	}
	/**
	 * 読み込み可能か
	 * @return boolean
	 */
	public function is_readable()
	{
		return read_auth($this->page, true, false);
	}
	/**
	 * 有効なページ名か
	 * @return boolean
	 */
	public function is_valied(){
		global $BracketName;
		// 無効な文字が含まれている
		if (preg_match(self::ILLEGAL_CHARS_PATTERN, $this->page) !== 0) return false;

		$is_pagename = (! $this->is_interwiki() &&
				preg_match('/^(?!\/)' . $BracketName . '$(?<!\/$)/', $this->page) !== false &&
				preg_match(self::ILLEGAL_CHARS_PATTERN, $this->page) !== false &&
				! preg_match('#(^|/)\.{1,2}(/|$)#', $this->page));
		return ($is_pagename && preg_match(self::VALIED_PAGENAME_PATTERN, $this->page));
	}
	/**
	 * InterWikiか
	 * @return boolean
	 */
	public function is_interwiki(){
		global $InterWikiName;
		return preg_match('/^' . $InterWikiName . '$/', $this->page);
	}
	/**
	 * 表示しないページか（check_non_list()）
	 * @global array $non_list
	 * @return boolean
	 */
	public function is_hidden(){
		global $non_list;
		return preg_match( '/' . $non_list . '/' , $this->page);
	}
	/**
	 * ページのソースを取得
	 * @return array
	 */
	public function source(){
		return $this->get(false);
	}
	/**
	 * HTMLに変換
	 * @return string
	 */
	public function render(){
		//return BodyFactory::factory($this->read());
		static $contents_id = 0;
		$lines = $this->source();
		$body = new Body(++$contents_id);
		$body->parse($lines);
		return $body->toString();
	}
	/**
	 * ページを書き込む
	 * @param string $postdata 書き込むデーター
	 * @param boolean $notimestamp タイムスタンプを更新するかのフラグ
	 * @return void
	 */
	public function set($str){
		global $trackback;
		global $use_spam_check, $_strings, $_title, $post;
		global $vars, $now, $akismet_api_key;

		if (empty($this->page)) return;

		// captcha check
		if ( (isset($use_spam_check['captcha']) && $use_spam_check['captcha'] !== 0) && (isset($use_spam_check['multiple_post']) && $use_spam_check['multiple_post'] !== 1)) {
			captcha_check(( $use_spam_check['captcha'] === 2 ? false : true) );
		}

		// roleのチェック
		if (auth::check_role('readonly')) return; // Do nothing
		if (auth::is_check_role(PKWK_CREATE_PAGE))
			die_message( sprintf($_strings['error_prohibit'], 'PKWK_READONLY') );

		// Create and write diff
		$postdata = $this->make_str_rules($str);
		$oldpostdata = $this->has() ? $this->get(TRUE) : '';
		$diffdata    = do_diff($oldpostdata, $postdata);

		$links = array();
		// ページ内のリンクを取得（TrackBackと、スパムチェックで使用）
		if ( ($trackback > 1) || ( $use_spam_check['page_contents']) ) {
			$links = $this->get_this_time_links($postdata, $diffdata);
		}

		$referer = (isset($_SERVER['HTTP_REFERER'])) ? htmlsc($_SERVER['HTTP_REFERER']) : 'None';
		$user_agent = htmlsc($_SERVER['HTTP_USER_AGENT']);

		if (isset($vars['page']) && $vars['page'] === $this->page || !auth::check_role('role_adm_contents') ){
			// Blocking SPAM
			if ($use_spam_check['bad-behavior']){
				require_once(LIB_DIR . 'bad-behavior-pukiwiki.php');
			}
			// リモートIPによるチェック
			if ($use_spam_check['page_remote_addr'] && SpamCheck(REMOTE_ADDR ,'ip')) {
				honeypot_write();
				die_message($_strings['blacklisted'] , $_title['prohibit'], 400);
			}
			// ページのリンクよるチェック
			if ($use_spam_check['page_contents'] && SpamCheck($links)) {
				honeypot_write();
				die_message('Writing was limited by DNSBL (Blocking SPAM).', $_title['prohibit'], 400);
			}
			// 匿名プロクシ
			if ($use_spam_check['page_write_proxy'] && is_proxy()) {
				honeypot_write();
				die_message('Writing was limited by PROXY (Blocking SPAM).', $_title['prohibit'], 400);
			}

			// Akismet
			if ($use_spam_check['akismet'] && !empty($akismet_api_key) ){
				$akismet = new ZendService\Akismet(
					$akismet_api_key,
					get_script_absuri()
				);
				if ($akismet->verifyKey($akismet_api_key)) {
					// 送信するデーターをセット
					$akismet_post = array(
						'user_ip' => REMOTE_ADDR,
						'user_agent' => $user_agent,
						'comment_type' => 'comment',
						'comment_author' => isset($vars['name']) ? $vars['name'] : 'Anonymous',
					);
					if ($use_spam_check['akismet'] === 2){
						$akismet_post['comment_content'] = $postdata;
					}else{
						// 差分のみをAkismetに渡す
						$new = explode("\n",$postdata);
						$old = explode("\n",$oldpostdata);
						$diff = implode("\n",array_diff($new, $old));
						$akismet_post['comment_content'] = $diff;
					}

					if($akismet->isSpam($akismet_post)){
						honeypot_write();
						die_message('Writing was limited by Akismet (Blocking SPAM).', $_title['prohibit'], 400);
					}
				}else{
					die_message('Akismet API key does not valied.', 500);
				}
			}
		}

		// add client info to diff
		$diffdata .= '// IP:"'. REMOTE_ADDR . '" TIME:"' . $now . '" REFERER:"' . $referer . '" USER_AGENT:"' . $user_agent. "\n";

		// Create wiki text
		parent::set($postdata);

		// Update data
		$difffile = new DiffFile($this->page);
		$difffile->set($diffdata);
		unset($oldpostdata, $diffdata, $difffile);

		// Create backup
		//make_backup($this->page, $postdata == ''); // Is $postdata null?
		$backup = new BackupFile($this->page);
		$backup->setBackup();

		// Update *.rel *.ref data.
		//update_cache($this->page, false, $notimestamp);

		// Logging postdata (Plus!)
		postdata_write();

		if ($trackback > 1) {
			// TrackBack Ping
			tb_send($this->page, $links);
		}

		log_write('update',$this->page);
	}
	/**
	 * 追加されたリンクを取得
	 * @param string $post 入力データ
	 * @param string $diff 差分データ
	 * @return array
	 */
	protected function get_this_time_links($post,$diff)
	{
		$links = array();
		$post_links = $this->replace_plugin_link2null($post);
		$diff_links = $this->get_link_list($diff);

		foreach($diff_links as $d) {
			foreach($post_links as $p) {
				if ($p == $d) {
					$links[] = $p;
					break;
				}
			}
		}
		unset($post_links, $diff_links);
		return $links;
	}
	/**
	 *
	 * @param type $diffdata
	 * @return type
	 */
	protected function get_link_list($diffdata)
	{
		$links = array();

		list($added, $removed) = get_diff_lines($diffdata);

		// Get URLs from <a>(anchor) tag from convert_html()
		$plus  = convert_html($added); // WARNING: heavy and may cause side-effect
		preg_match_all('#href="(https?://[^"]+)"#', $plus, $links, PREG_PATTERN_ORDER);
		$links = array_unique($links[1]);

		// Reject from minus list
		if (! empty($removed) ) {
			$links_m = array();
			$minus = convert_html($removed); // WARNING: heavy and may cause side-effect
			preg_match_all('#href="(https?://[^"]+)"#', $minus, $links_m, PREG_PATTERN_ORDER);
			$links_m = array_unique($links_m[1]);

			$links = array_diff($links, $links_m);
		}

		unset($plus,$minus);

		// Reject own URL (Pattern _NOT_ started with '$script' and '?')
		$links = preg_grep('/^(?!' . preg_quote(get_script_absuri(), '/') . '\?)./', $links);

		// No link, END
		if (! is_array($links) || empty($links)) return;

		return $links;
	}
	/**
	 * ソースをシステム（rules.ini.phpなど）で定義されているルールに基づいて自動修正
	 * @return string
	 */
	protected function make_str_rules($source){
		// Modify original text with user-defined / system-defined rules
		global $str_rules, $fixed_heading_anchor;

		$lines = explode("\n", $source);
		$count = count($lines);

		$modify    = TRUE;
		$multiline = 0;
		$matches   = array();
		for ($i = 0; $i < $count; $i++) {
			$line = & $lines[$i]; // Modify directly

			// Ignore null string and preformatted texts
			if ($line == '' || $line{0} == ' ' || $line{0} == "\t") continue;

			// Modify this line?
			if ($modify) {
				if (! PKWKEXP_DISABLE_MULTILINE_PLUGIN_HACK &&
				    $multiline == 0 &&
				    preg_match('/#[^{]*(\{\{+)\s*$/', $line, $matches)) {
				    	// Multiline convert plugin start
					$modify    = FALSE;
					$multiline = strlen($matches[1]); // Set specific number
				}
			} else {
				if (! PKWKEXP_DISABLE_MULTILINE_PLUGIN_HACK &&
				    $multiline != 0 &&
				    preg_match('/^\}{' . $multiline . '}\s*$/', $line)) {
				    	// Multiline convert plugin end
					$modify    = TRUE;
					$multiline = 0;
				}
			}
			if ($modify === FALSE) continue;

			// Replace with $str_rules
			foreach ($str_rules as $pattern => $replacement)
				$line = preg_replace('/' . $pattern . '/', $replacement, $line);

			// Adding fixed anchor into headings
			if ($fixed_heading_anchor &&
			    preg_match('/^(\*{1,3}.*?)(?:\[#([A-Za-z][\w-]*)\]\s*)?$/', $line, $matches) &&
			    (! isset($matches[2]) || empty($matches[2]) )) {
				// Generate unique id
				$anchor = Zend\Math\Rand::getString(7);
				$line = rtrim($matches[1]) . ' [#' . $anchor . ']';
			}
		}

		// Multiline part has no stopper
		if (! PKWKEXP_DISABLE_MULTILINE_PLUGIN_HACK &&
			$modify === FALSE && $multiline != 0)
			$lines[] = str_repeat('}', $multiline);

		return implode("\n", $lines);
	}
	protected function add_recent( $recentpage, $subject = '', $limit = 0) {
		//if (PKWK_READONLY || $limit == 0 || $page == '' || $recentpage == '' ||
		if (auth::check_role('readonly') || $limit == 0  || $recentpage == '' ||
			check_non_list($page)) return;

		// Load
		$lines = $matches = array();
		$r = new WikiFile($recentpage);
		foreach ($r->source() as $line) {
			if (preg_match('/^-(.+) - (\[\[.+\]\])$/', $line, $matches)) {
				$lines[$matches[2]] = $line;
			}
		}

		$_page = '[[' . $this->page . ']]';

		// Remove a report about the same page
		if (isset($lines[$_page])) unset($lines[$_page]);

		// Add
	//	array_unshift($lines, '-' . format_date(UTIME) . ' - ' . $_page .
		array_unshift($lines, '- &epoch(' . UTIME . '); - ' . $_page .
			htmlsc($subject) . "\n");

		// Get latest $limit reports

		$f = new File(self::DIR.encode($recentpage));
		$f->write( '#norelated' . "\n".join('',array_splice($lines, 0, $limit)));
	}
}
/**
 * Fileのファクトリークラス
 */
class WikiFileFactory
{
	public static function factory($page){
		static $wikifile = array();
		if (! isset($wikifile[$page])) $wikifile[$page] = new WikiFile($page);
		return $wikifile[$page];
	}
}

class DiffFile extends File implements Storage{
	/**#@+
	 * 宣言
	 */
	// 拡張子
	const EXT = '.txt';
	// 格納ディレクトリ
	const DIR = DIFF_DIR;
	/**#@-*/

	/**
	 * コンストラクタ
	 * @param string $page
	 */
	public function __construct($page) {
		if (empty($page)){
			throw new Exception('Page name is missing!');
		}
		$this->page = $page;
		$this->filename = self::DIR . encode($page) . self::EXT;
	}
	/**
	 * 書き込み
	 * @global boolean $notify
	 * @global boolean $notify_diff_only
	 * @param string $str
	 */

	public function set($str){
		global $notify, $notify_diff_only;
		if ($notify){
			if ($notify_diff_only) $str = preg_replace('/^[^-+].*\n/m', '', $str);
			$summary = array(
				'ACTION'		=> 'Page update',
				'PAGE'			=> & $page,
				'URI'			=> get_script_uri() . '?' . rawurlencode($page),
				'USER_AGENT'	=> TRUE,
				'REMOTE_ADDR'	=> TRUE
			);
			pkwk_mail_notify($notify_subject, $str, $summary) or
				die_message('pkwk_mail_notify(): Failed');
		}
		parent::set($str);
	}
}

class CounterFile extends File implements Storage{
	/**#@+
	 * 宣言
	 */
	// 拡張子
	const EXT = '.count';
	// 格納ディレクトリ
	const DIR = COUNTER_DIR;
	/**#@-*/

	/**
	 * コンストラクタ
	 * @param string $page
	 */
	public function __construct($page) {
		if (empty($page)){
			throw new Exception('Page name is missing!');
		}
		$this->page = $page;
		$this->filename = self::DIR . encode($page) . self::EXT;
	}
}
// for compatibility

function get_source($page = NULL, $lock = TRUE, $join = FALSE)
{
	$wiki = new WikiFile($page);
	return $wiki->get($join);
}

// Get last-modified filetime of the page
function get_filetime($page)
{
	$wiki = new WikiFile($page);
	return $wiki->getTime();
}

// Get physical file name of the page
function get_filename($page)
{
	return DATA_DIR . encode($page) . '.txt';
}

// Put a data(wiki text) into a physical file(diff, backup, text)
function page_write($page, $postdata, $notimestamp = FALSE)
{
	$wiki = new WikiFile($page);
	if ($notimestamp){
		$timestamp = $wiki->getTime();
		$wiki->set($postdata);
		$wiki->setTime($timestamp);
	}else{
		$wiki->set($postdata);
	}
}

// Modify original text with user-defined / system-defined rules
function make_str_rules($source)
{
	global $str_rules, $fixed_heading_anchor;

	$lines = explode("\n", $source);
	$count = count($lines);

	$modify    = TRUE;
	$multiline = 0;
	$matches   = array();
	for ($i = 0; $i < $count; $i++) {
		$line = & $lines[$i]; // Modify directly

		// Ignore null string and preformatted texts
		if ($line == '' || $line{0} == ' ' || $line{0} == "\t") continue;

		// Modify this line?
		if ($modify) {
			if (! PKWKEXP_DISABLE_MULTILINE_PLUGIN_HACK &&
			    $multiline == 0 &&
			    preg_match('/#[^{]*(\{\{+)\s*$/', $line, $matches)) {
			    	// Multiline convert plugin start
				$modify    = FALSE;
				$multiline = strlen($matches[1]); // Set specific number
			}
		} else {
			if (! PKWKEXP_DISABLE_MULTILINE_PLUGIN_HACK &&
			    $multiline != 0 &&
			    preg_match('/^\}{' . $multiline . '}\s*$/', $line)) {
			    	// Multiline convert plugin end
				$modify    = TRUE;
				$multiline = 0;
			}
		}
		if ($modify === FALSE) continue;

		// Replace with $str_rules
		foreach ($str_rules as $pattern => $replacement)
			$line = preg_replace('/' . $pattern . '/', $replacement, $line);
	}

	// Multiline part has no stopper
	if (! PKWKEXP_DISABLE_MULTILINE_PLUGIN_HACK &&
		$modify === FALSE && $multiline != 0)
		$lines[] = str_repeat('}', $multiline);

	return implode("\n", $lines);
}

// Read top N lines as an array
// (Use PHP file() function if you want to get ALL lines)
function file_head($file, $count = 1, $lock = TRUE, $buffer = NULL)
{
	$f = new File($file);
	return $f->head($count, $lock, $buffer);
}

// Output to a file
function file_write($dir, $page, $str, $notimestamp = FALSE)
{
//global $_msg_invalidiwn, $notify, $notify_diff_only, $notify_subject;
	global $notify, $notify_diff_only, $notify_subject, $_string;
	global $whatsdeleted, $maxshow_deleted;

	if (auth::check_role('readonly')) return; // Do nothing
	if ($dir !== DATA_DIR && $dir !== DIFF_DIR) die('file_write(): Invalid directory');

	$page = strip_bracket($page);
	$file = $dir . encode($page) . '.txt';
	// ----
	// Delete?
	$f = new File($file);

	if ($dir == DATA_DIR && empty($str) ) {
		// Page deletion
		if (! $f->exists) return; // Ignore null posting for DATA_DIR
		// Update RecentDeleted (Add the $page)
		$f->add_recent($whatsdeleted, '', $maxshow_deleted);
		// Remove the page
		$f->delete();
		// Update RecentDeleted, and remove the page from RecentChanges
		lastmodified_add($whatsdeleted, $page);
		// Clear is_page() cache
		is_page($page, TRUE);
		return;
	} else if ($dir == DIFF_DIR && $str === " \n") {
		return; // Ignore null posting for DIFF_DIR
	}

	// ----
	// File replacement (Edit)

	if (! is_pagename($page))
		die_message(str_replace('$1', htmlsc($page),
		            str_replace('$2', 'WikiName', $_string['invalidiwn'])));

	$str = rtrim(preg_replace('/' . "\r" . '/', '', $str)) . "\n";
	$timestamp = ($f->exists && $notimestamp) ? filemtime($file) : FALSE;

	$f->write($str, $timestamp);

	// Optional actions
	if ($dir == DATA_DIR) {
		// Update RecentChanges (Add or renew the $page)
		if ($timestamp === FALSE) lastmodified_add($page);

		// Command execution per update
		if (defined('PKWK_UPDATE_EXEC') && PKWK_UPDATE_EXEC)
			system(PKWK_UPDATE_EXEC . ' > /dev/null &');

	} else if ($dir == DIFF_DIR && $notify) {
		if ($notify_diff_only) $str = preg_replace('/^[^-+].*\n/m', '', $str);
		$summary = array(
			'ACTION'		=> 'Page update',
			'PAGE'			=> & $page,
			'URI'			=> get_script_uri() . '?' . rawurlencode($page),
			'USER_AGENT'	=> TRUE,
			'REMOTE_ADDR'	=> TRUE
		);
		pkwk_mail_notify($notify_subject, $str, $summary) or
			die_message('pkwk_mail_notify(): Failed');
	}

	is_page($page, TRUE); // Clear is_page() cache
}


// Update PKWK_MAXSHOW_CACHE itself (Add or renew about the $page) (Light)
// Use without $autolink
function lastmodified_add($update = '', $remove = '')
{
	// global $maxshow, $whatsnew, $autolink;
	global $maxshow, $whatsnew, $autolink, $autobasealias;
	global $cache;

	// AutoLink implimentation needs everything, for now
	//if ($autolink) {
	if ($autolink || $autobasealias) {
		put_lastmodified(); // Try to (re)create ALL
		return;
	}

	if (($update == '' || check_non_list($update)) && $remove == '')
		return; // No need

	// Check cache exists
	if (! $cache['wiki']->hasItem(PKWK_MAXSHOW_CACHE)){
		put_lastmodified(); // Try to (re)create ALL
		return;
	}else{
		$recent_pages = $cache['wiki']->getItem(PKWK_MAXSHOW_CACHE);
	}

	// Remove if it exists inside
	if (isset($recent_pages[$update])) unset($recent_pages[$update]);
	if (isset($recent_pages[$remove])) unset($recent_pages[$remove]);

	// Add to the top: like array_unshift()
	// if ($update != '')
	if ($update != '' && $update != $whatsnew && ! check_non_list($update))
		$recent_pages = array($update => get_filetime($update)) + $recent_pages;

	// Check
	$abort = count($recent_pages) < $maxshow;

	// Update cache
	$cache['wiki']->setItem(PKWK_MAXSHOW_CACHE, $recent_pages);

	if ($abort) {
		put_lastmodified(); // Try to (re)create ALL
		return;
	}

	// ----
	// Update the page 'RecentChanges'

	$recent_pages = array_splice($recent_pages, 0, $maxshow);
	$file = get_filename($whatsnew);

	// Open
	pkwk_touch_file($file);
	$fp = fopen($file, 'r+') or
		die_message('Cannot open ' . htmlsc($whatsnew));
	set_file_buffer($fp, 0);
	flock($fp, LOCK_EX);

	// Recreate
	ftruncate($fp, 0);
	rewind($fp);

	foreach ($recent_pages as $_page=>$time)
		fputs($fp, '- &epoch('.$time.');' .
			' - ' . '[[' . htmlsc($_page) . ']]' . "\n");

	fputs($fp, '#norelated' . "\n"); // :)

	ignore_user_abort($last);	// Plus!

	flock($fp, LOCK_UN);
	fclose($fp);
}

// Re-create PKWK_MAXSHOW_CACHE (Heavy)
function put_lastmodified()
{
	// global $maxshow, $whatsnew, $autolink;
	global $maxshow, $whatsnew, $autolink, $autobasealias;
	global $cache;

	// if (PKWK_READONLY) return; // Do nothing
	if (auth::check_role('readonly')) return; // Do nothing

	// Get WHOLE page list
	$pages = get_existpages();

	// Check ALL filetime
	$recent_pages = array();
	foreach($pages as $page)
		$wiki = FileFactory::Wiki($page);
		if ($page !== $whatsnew && ! check_non_list($page))
			$recent_pages[$page] = $wiki->getTime();

	// Sort decending order of last-modification date
	arsort($recent_pages, SORT_NUMERIC);

	// Cut unused lines
	// BugTrack2/179: array_splice() will break integer keys in hashtable
	$count   = $maxshow + PKWK_MAXSHOW_ALLOWANCE;
	$_recent = array();
	foreach($recent_pages as $key=>$value) {
		unset($recent_pages[$key]);
		$_recent[$key] = $value;
		if (--$count < 1) break;
	}
	$recent_pages = & $_recent;

	// Save to recent cache data
	$cache['wiki']->setItem(PKWK_MAXSHOW_CACHE, $recent_pages);

	// Create RecentChanges
	$file = FileFactory::Wiki($whatsnew);
/*
	$file = get_filename($whatsnew);
	pkwk_touch_file($file);
	$fp = fopen($file, 'r+') or
		die_message('Cannot open ' . htmlsc($whatsnew));
	set_file_buffer($fp, 0);
	flock($fp, LOCK_EX);
	ftruncate($fp, 0);
	rewind($fp);
	foreach (array_keys($recent_pages) as $page) {
		$time      = $recent_pages[$page];
		$s_page    = htmlsc($page);
		fputs($fp, '-&epoch(' . $time . '); - [[' . $s_page . ']]' . "\n");
	}
	fputs($fp, '#norelated' . "\n"); // :)
	flock($fp, LOCK_UN);
	fclose($fp);
*/
	foreach (array_keys($recent_pages) as $page) {
		$buffer[] = '-&epoch(' . $recent_pages[$page] . '); - [[' . htmlsc($page) . ']]';
	}
	$file->set(join("\n",$buffer));

	// For AutoLink
	if ($autolink){
		$cache['wiki']->setItem(PKWK_AUTOLINK_REGEX_CACHE, get_autolink_pattern($pages, $autolink));
	}

	// AutoBaseAlias (Plus!)
	if ($autobasealias) {
		$cache['wiki']->setItem(PKWK_AUTOBASEALIAS_CACHE, get_autobasealias($pages));
	}
}

// Get elapsed date of the page
function get_pg_passage($page, $sw = TRUE)
{
	global $show_passage;
	if (! $show_passage) return '';

	$time = get_filetime($page);
	$pg_passage = ($time != 0) ? get_passage($time, $sw) : '';

	return $sw ? '<small class="passage">' . $pg_passage . '</small>' : $pg_passage;
}

// Last-Modified header
function header_lastmod($page = NULL)
{
	global $lastmod;

	if ($lastmod && is_page($page)) {
		pkwk_headers_sent();
		header('Last-Modified: ' .
			date('D, d M Y H:i:s', get_filetime($page)) . ' GMT');
	}
}

// Get a list of encoded files (must specify a directory and a suffix)
function get_existfiles($dir = DATA_DIR, $ext = '.txt')
{
	$aryret = array();
	$pattern = '/^(?:[0-9A-F]{2})+' . preg_quote($ext, '/') . '$/';

	$handle = opendir($dir);
	if ($handle) {
		while (false !== ($entry = readdir($handle))) {
			if (preg_match($pattern, $entry)) {
				$aryret[] = $dir . $entry;
			}
		}
		closedir($handle);
	}else{
		die_message($dir . ' is not found or not readable.');
	}

	$pages[$dir][$ext] = $aryret;
	return $aryret;
}

// Get a page list of this wiki
function get_existpages($dir = DATA_DIR, $ext = '.txt')
{
	// get_existpages を３行で軽くする
	// http://lsx.sourceforge.jp/?Hack%2Fget_existpages
	// ただし、Adv.の場合ファイルに別途キャッシュしているのであまり意味ないかも・・・。
	static $pages;
	if (isset($pages[$dir][$ext])) return $pages[$dir][$ext];

	$aryret = array();
	$pattern = '/^((?:[0-9A-F]{2})+)' . preg_quote($ext, '/') . '$/';

	$matches = array();
	$handle = opendir($dir);
	if ($handle) {
		while (false !== ($entry = readdir($handle))) {
			if (preg_match($pattern, $entry, $matches)) {
				$aryret[$entry] = decode($matches[1]);
			}
		}
		closedir($handle);
	}else{
		die_message($dir . ' is not found or not readable.');
	}
	$pages[$dir][$ext] = $aryret;

	return $aryret;
}

// Get PageReading(pronounce-annotated) data in an array()
function get_readings()
{
	global $pagereading_enable, $pagereading_config_page, $mecab_path;
	global $pagereading_config_dict;

	$pages = get_existpages();

	$readings = array();
	foreach ($pages as $page)
		$readings[$page] = '';

	$deletedPage = FALSE;
	$matches = array();
	$w = new WikiFile($pagereading_config_page);
	foreach ($w->source() as $line) {
		$line = chop($line);
		if(preg_match('/^-\[\[([^]]+)\]\]\s+(.+)$/', $line, $matches)) {
			if(isset($readings[$matches[1]])) {
				// This page is not clear how to be pronounced
				$readings[$matches[1]] = $matches[2];
			} else {
				// This page seems deleted
				$deletedPage = TRUE;
			}
		}
	}

	// If enabled ChaSen/KAKASI execution
	if($pagereading_enable) {

		// Check there's non-clear-pronouncing page '
		$unknownPage = FALSE;
		foreach ($readings as $page => $reading) {
			if(empty($reading)) {
				$unknownPage = TRUE;
				break;
			}
		}

		if($unknownPage) {
			if (file_exists($mecab_path)){
				foreach ($readings as $page => $reading) {
					if(!empty($reading)) continue;
					$readings[$page] = mecab_reading($page);
				}
			}else{
				$patterns = $replacements = $matches = array();
				$d = new WikiFile($pagereading_config_dict);
				foreach ($d->source() as $line) {
					$line = chop($line);
					if(preg_match('|^ /([^/]+)/,\s*(.+)$|', $line, $matches)) {
						$patterns[]     = $matches[1];
						$replacements[] = $matches[2];
					}
				}
				foreach ($readings as $page => $reading) {
					if(!empty($reading)) continue;

					$readings[$page] = $page;
					foreach ($patterns as $no => $pattern)
						$readings[$page] = mb_convert_kana(mb_ereg_replace($pattern,
							$replacements[$no], $readings[$page]), 'aKCV');
				}
			}
		}

		if($unknownPage || $deletedPage) {
			asort($readings, SORT_STRING); // Sort by pronouncing(alphabetical/reading) order
			$body = '';
			foreach ($readings as $page => $reading)
				$body .= '-[[' . $page . ']] ' . $reading . "\n";

			$w->set($body);
		}
	}

	// Pages that are not prounouncing-clear, return pagenames of themselves
	foreach ($pages as $page) {
		if ( empty($readings[$page]) ) $readings[$page] = $page;
	}

	return $readings;
}

// Get a list of related pages of the page
function links_get_related($page)
{
	global $vars, $related;
	static $links;

	//if (empty($page)) return;

	if (isset($links[$page])) return $links[$page];

	// If possible, merge related pages generated by make_link()
	$links[$page] = ($page === $vars['page']) ? $related : array();

	// Get repated pages from DB
	$link = new Relational($page);
	//$links[$page] += links_get_related_db($page);
	$links[$page] = $link->get_related();

	return $links[$page];
}


// touch() with trying pkwk_chown()
function pkwk_touch_file($filename, $time = FALSE, $atime = FALSE)
{
	//$f = new File($filename);
	//$f->touch($time = FALSE, $atime = FALSE);
}

/**
 * ディレクトリを再帰的に作成（http://d.hatena.ne.jp/studio-m/20070508/1178636785）
 *
 * mkdir_r('hoge/foo') とかやった時にhogeがなければ、hogeを作ってから、その中にfooを作る。
 * ディレクトリの存在チェックはしない（既にある場合は作成に失敗してfalseが返る）ので注意。
 *
 * @access public
 * @param  string  $dirname 作成するディレクトリ名
 * @return boolean 作成に成功すればtrue、失敗ならfalse
 */
function mkdir_r($dirname)
{
	if (file_exists($dirname)) return false;
	// 階層指定かつ親が存在しなければ再帰
	if (strpos($dirname, '/') && !file_exists(dirname($dirname))) {
		// 親でエラーになったら自分の処理はスキップ
		if (mkdir_r(dirname($dirname)) === false) return false;
	}
	return mkdir($dirname);
}

/* End of file file.php */
/* Location: ./wiki-common/lib/file.php */