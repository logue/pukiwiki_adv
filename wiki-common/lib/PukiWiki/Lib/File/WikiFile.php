<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: FileInterface.php,v 1.0.0 2012/12/11 19:55:00 Logue Exp $
// Copyright (C)
//   2012 PukiWiki Advance Developers Team
// License: GPL v2 or (at your option) any later version
//
namespace PukiWiki\Lib\File;
use PukiWiki\Lib\File\File;
use PukiWiki\Lib\Renderer\RendererFactory;

/**
 * Wikiページクラス
 */
class WikiFile extends File{
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
	const INVALIED_PAGENAME_PATTERN = '/[%|=|&|?|#|\r|\n|\0|\@|\t|;|\$|+|\\|\[|\]|\||^|{|}]/';
	// ファイル名のパターン
	const FILENAME_PATTERN = '/^((?:[0-9A-F]{2})+).txt$/';
	/**#@-*/

	/**
	 * コンストラクタ
	 * @param string $page
	 */
	public function __construct($page) {

		if (empty($page)){
			throw new \Exception('Page name is missing!');
		}
		$this->page = $page;
		parent::__construct(self::DIR . encode($page) . self::EXT);
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
		if (\auth::check_role('readonly')) return false;

		if (!$edit_auth) return true;

		$info = \auth::get_user_info();
		if (!empty($info['key']) &&
			\auth::is_page_readable($page, $info['key'], $info['group']) &&
			\auth::is_page_editable($page, $info['key'], $info['group'])) {
			return true;
		}

		// Basic, Digest 認証を利用していない場合
		if (!$auth_api['plus']['use']) return \auth::is_page_readable($this->page, null, null);

		$auth_func_name = get_auth_func_name();
		if ($auth_flag && ! $auth_func_name($page, $auth_flag, $exit_flag, $edit_auth_pages, $_title['cannotedit'])) return false;
		if (\auth::is_page_readable($this->page, '', '') && \auth::is_page_editable($this->page,'','')) return true;

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
		if (preg_match(self::INVALIED_PAGENAME_PATTERN, $this->page) !== 0) return false;

		$is_pagename = (! $this->is_interwiki() &&
				preg_match('/^(?!\/)' . $BracketName . '$(?<!\/$)/', $this->page) !== false &&
				preg_match(self::INVALIED_PAGENAME_PATTERN, $this->page) !== false &&
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
		return RendererFactory::factory($this->source());
	}
	/**
	 * 経過時間を取得
	 */
	public function passage($use_tag = true, $quote = true){
		$pg_passage = $quote ? '('.parent::getPassage().')' : parent::getPassage();
		return $use_tag ? '<small class="passage">' . $pg_passage . '</small>' : $pg_passage;
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
		if (\auth::check_role('readonly')) return; // Do nothing
		if (\auth::is_check_role(PKWK_CREATE_PAGE))
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

		if (isset($vars['page']) && $vars['page'] === $this->page || !\auth::check_role('role_adm_contents') ){
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

		// Create wiki text
		parent::set($postdata);
	}
	public function files(){
		$aryret = array();
		$matches = array();
		$handle = opendir(self::DIR);
		if ($handle) {
			while (false !== ($entry = readdir($handle))) {
				if (preg_match(self::FILENAME_PATTERN, $entry, $matches)) {
					$aryret[$entry] = decode($matches[1]);
				}
			}
			closedir($handle);
		}else{
			die_message(self::DIR . ' is not found or not readable.');
		}
		return $aryret;
	}
	/**
	 * 追加されたリンクを取得
	 * @param string $post 入力データ
	 * @param string $diff 差分データ
	 * @return array
	 */
	private function get_this_time_links($post,$diff)
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
	 * 一時的にプラグインをnullプラグインにして無効化
	 * @param $data
	 * @return array
	 */
	private function replace_plugin_link2null($data){
		global $exclude_link_plugin;

		$pattern = $replacement = array();
		foreach($exclude_link_plugin as $plugin) {
			$pattern[] = '/^#'.$plugin.'\(/i';
			$replacement[] = '#null(';
		}

		$exclude = preg_replace($pattern,$replacement, explode("\n", $data));
		$html = convert_html($exclude);
		preg_match_all('#href="(https?://[^"]+)"#', $html, $links, PREG_PATTERN_ORDER);
		$links = array_unique($links[1]);
		unset($except, $html);
		return $links;
	}
	/**
	 * リンク一覧を取得
	 * @param type $diffdata
	 * @return type
	 */
	private function get_link_list($diffdata)
	{
		$links = array();

		list($added, $removed) = get_diff_lines($diffdata);

		// Get URLs from <a>(anchor) tag from convert_html()
		$plus  = \PukiWiki\Lib\Renderer\Factory::factory($added); // WARNING: heavy and may cause side-effect
		preg_match_all('#href="(https?://[^"]+)"#', $plus, $links, PREG_PATTERN_ORDER);
		$links = array_unique($links[1]);

		// Reject from minus list
		if (! empty($removed) ) {
			$links_m = array();
			$minus = \PukiWiki\Lib\Renderer\Factory::factory($removed); // WARNING: heavy and may cause side-effect
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
	 * @param array $source ソース
	 * @return string
	 */
	private function make_str_rules($source){
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
			if ( empty($line) || $line{0} == ' ' || $line{0} == "\t") continue;

			// Modify this line?
			if ($modify) {
				if ($multiline === 0 && preg_match('/#[^{]*(\{\{+)\s*$/', $line, $matches)) {
					// Multiline convert plugin start
					$modify    = FALSE;
					$multiline = strlen($matches[1]); // Set specific number
				}
			} else {
				if ($multiline !== 0 && preg_match('/^\}{' . $multiline . '}\s*$/', $line)) {
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
			if ($fixed_heading_anchor && preg_match('/^(\*{1,3}.*?)(?:\[#([A-Za-z][\w-]*)\]\s*)?$/', $line, $matches) && 
					(! isset($matches[2]) || empty($matches[2]) )) {
				// Generate unique id
				$anchor = Zend\Math\Rand::getString(7,'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
				$line = rtrim($matches[1]) . ' [#' . $anchor . ']';
			}
		}

		// Multiline part has no stopper
		if ($modify === FALSE && $multiline != 0) $lines[] = str_repeat('}', $multiline);

		return implode("\n", $lines);
	}
	/**
	 * 履歴用ページ作成（更新履歴や削除履歴で使用）
	 * @param string $recentpage 履歴ページ名
	 * @param string $subject 追加するページ名
	 * @param int $limit 最大行数
	 */
	public function add_recent($recentpage, $subject = '', $limit = 0) {
		//if (PKWK_READONLY || $limit == 0 || $page == '' || $recentpage == '' ||
		if (\auth::check_role('readonly') || $limit == 0  || $recentpage == '' ||
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
		array_unshift($lines, '- &epoch(' . UTIME . '); - ' . $_page . htmlsc($subject) . "\n");

		// Get latest $limit reports

		$f = new File(self::DIR.encode($recentpage));
		$f->set( '#norelated' . "\n".join('',array_splice($lines, 0, $limit)));
	}
	/**
	 * ページのアドレスを取得
	 */
	public function getUri(){
	//	global $static_url;
	//	return str_replace('\\', '/', dirname($_SERVER["PHP_SELF"])) . 
	//		$static_url ? str_replace('%2F', '/', rawurlencode($this->page)) : rawurlencode($this->page);
		return get_page_uri($this->page);
	}
}

/* End of file WikiFile.php */
/* Location: /vender/PukiWiki/Lib/File/WikiFile.php */
