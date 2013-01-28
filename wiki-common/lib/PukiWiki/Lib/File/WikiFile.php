<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: FileInterface.php,v 1.0.0 2012/12/11 19:55:00 Logue Exp $
// Copyright (C)
//   2012 PukiWiki Advance Developers Team
// License: GPL v2 or (at your option) any later version
//
namespace PukiWiki\Lib\File;
use PukiWiki\Lib\File\File;
use PukiWiki\Lib\File\FileUtility;
use PukiWiki\Lib\Renderer\RendererFactory;
use PukiWiki\Lib\Router;
use PukiWiki\Lib\Relational;
use PukiWiki\Lib\Utility;
use PukiWiki\Lib\Auth\Auth;
use PukiWiki\Lib\Auth\AuthUtility;
use PukiWiki\Lib\Diff;

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
	 * ページが存在するか（hasのエイリアス）
	 * @paran boolean $clearcache
	 * @return boolean
	 */
	public function is_page($clearcache = true){
		if ($clearcache) clearstatcache();
		return $this->has();
	}
	/**
	 * 編集可能か
	 * @param boolean $authenticate 認証画面を通すかのフラグ
	 * @return boolean
	 */
	public function is_editable($authenticate = false)
	{
		global $edit_auth, $edit_auth_pages, $auth_api, $defaultpage, $_title, $edit_auth_pages_accept_ip;
		if (!$this->is_valied()) return false;	// 無効なページ名
		if ($this->is_freezed()) return false;	// 凍結されている

		if (!$edit_auth) return true;	// 編集時に認証が有効になっていない

		// 認証画面を表示する
		if ($authenticate && !AuthUtility::auth($this->page, true, false, $edit_auth_pages, $_title['cannotedit'])) return false;

		if (Auth::check_role('readonly')) return false;	// 未認証時に読み取り専用になっている

		// ユーザ別の権限を読む
		$info = Auth::get_user_info();
		if (!empty($info['key']) &&
			Auth::is_page_readable($this->page, $info['key'], $info['group']) &&
			Auth::is_page_editable($this->page, $info['key'], $info['group'])) {
			return true;
		}

		// Basic, Digest 認証を利用していない場合
		if (!$auth_api['plus']['use']) return Auth::is_page_readable($this->page, null, null);
		
		if (Auth::is_page_readable($this->page, null, null) && Auth::is_page_editable($this->page,null,null)) return true;

		return false;
	}
	/**
	 * 凍結されているか
	 * @global boolean $function_freeze
	 * @return boolean
	 */
	public function is_freezed()
	{
		$buffer = parent::head(1);	// 先頭1行のみ読み込む
		return strstr($buffer,'#freeze');
	}
	/**
	 * 読み込み可能か
	 * @param boolean $authenticate 認証画面を通すかのフラグ
	 * @return boolean
	 */
	public function is_readable($authenticate = false)
	{
		global $read_auth, $read_auth_pages, $auth_api, $_title, $read_auth_pages_accept_ip;

		if (!$read_auth) return true;

		// 許可IPの場合チェックしない
		if ( AuthUtility::ip_auth($this->page, true, false, $read_auth_pages_accept_ip, $_title['cannotread'])) {
			return TRUE;
		}

		$info = auth::get_user_info();
		if (!empty($info['key']) &&
		    Auth::is_page_readable($page, $info['key'], $info['group'])) {
			return true;
		}

		if (!$auth_api['plus']['use']) return Auth::is_page_readable($page, null, null);

		if ($authenticate && !AuthUtility::auth($this->page, true, false, $read_auth_pages, $_title['cannotread'])) return false;
		return Auth::is_page_readable($this->page, null, null);
	}
	/**
	 * 有効なページ名か（is_page()）
	 * @return boolean
	 */
	public function is_valied(){
		global $BracketName;
		return (
				! self::is_interwiki() &&	// InterWikiでない
				preg_match('/^(?!\/)' . $BracketName . '$(?<!\/$)/', $this->page) !== false &&	// BlacketNameである
				preg_match(self::INVALIED_PAGENAME_PATTERN, $this->page) !== false &&	// 無効な文字が含まれていない。
				! preg_match('#(^|/)\.{1,2}(/|$)#', $this->page) &&
				preg_match(self::VALIED_PAGENAME_PATTERN, $this->page)	// 使用可能な文字である
		);
	}
	/**
	 * 読み込み可能かをチェック（メッセージを表示する）
	 */
	public function check_readable(){
		if (! self::is_readable()){
			die_message('You have not permisson to read this page.',403);
		}
	}
	/**
	 * 編集可能かをチェック（メッセージを表示する）
	 */
	public function check_editable(){
		if (! self::is_editable()){
			die_message('You have not permisson to edit this page.',403);
		}
	}
	/**
	 * InterWikiか
	 * @return boolean
	 */
	public function is_interwiki(){
		global $InterWikiName;
		return preg_match('/^' . $InterWikiName . '/', $this->page);
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
		return $this->get(false) ? $this->get(false) : array();
	}
	/**
	 * HTMLに変換
	 * @return string
	 */
	public function render(){
		return RendererFactory::factory($this->source());
	}
	/**
	 * ダイジェスト（ページの内容のMD5ハッシュ）を出力
	 * @return string
	 */
	public function digest(){
		return md5($this->get(true));
	}
	/**
	 * 経過時間を取得
	 * @param boolean $use_tag タグでくくる
	 * @param boolean $quote ()でくくる
	 * @return string
	 */
	public function passage($use_tag = true, $quote = true){
		$pg_passage = $quote ? '('.parent::getPassage().')' : parent::getPassage();
		return $use_tag ? '<small class="passage">' . $pg_passage . '</small>' : $pg_passage;
	}
	/**
	 * ページを書き込む
	 * @param string $str 書き込むデーター
	 * @param boolean $notimestamp タイムスタンプを更新するかのフラグ
	 * @return void
	 */
	public function set($str,$keeptimestamp = false){
		global $trackback;
		global $use_spam_check, $_strings, $_title, $post;
		global $vars, $now, $akismet_api_key;

		if (is_array($str)) {
			// ポカミス対策：配列だった場合文字列に変換
			$str = join("\n", $str);
		}

		// captcha check
		if ( (isset($use_spam_check['captcha']) && $use_spam_check['captcha'] !== 0) && (isset($use_spam_check['multiple_post']) && $use_spam_check['multiple_post'] !== 1)) {
			captcha_check(( $use_spam_check['captcha'] === 2 ? false : true) );
		}

		// roleのチェック
		if (Auth::check_role('readonly')) return; // Do nothing
		if (Auth::is_check_role(PKWK_CREATE_PAGE))
			die_message( sprintf($_strings['error_prohibit'], 'PKWK_READONLY') );

		if (empty($str)){
			// 入力が空の場合、削除とする
			self::create_recent_deleted();
			return parent::set('');
		}

		// Create and write diff
		$postdata = self::make_str_rules($str);
		$oldpostdata = self::has() ? self::get(TRUE) : '';
		$diff = new Diff($postdata, $oldpostdata);

		// 差分から追加のみを取得
		foreach ($diff->getSes() as $key=>$line){
			if ($key !== $diff::SES_ADD) continue;
			$added_data[] = $line;
		}
/*
		$links = array();
		// ページ内のリンクを取得（TrackBackと、スパムチェックで使用）
		if ( ($trackback > 1) || ( $use_spam_check['page_contents']) ) {
			$links = self::get_this_time_links($postdata,$oldpostdata);
		}

		$referer = (isset($_SERVER['HTTP_REFERER'])) ? Utility::htmlsc($_SERVER['HTTP_REFERER']) : 'None';
		$user_agent = Utility::htmlsc($_SERVER['HTTP_USER_AGENT']);

		if (isset($vars['page']) && $vars['page'] === $this->page || !Auth::check_role('role_adm_contents') ){
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
					Router::get_script_absuri()
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
						$akismet_post['comment_content'] = $addedata;
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
		//$diffdata .= '// IP:"'. REMOTE_ADDR . '" TIME:"' . $now . '" REFERER:"' . $referer . '" USER_AGENT:"' . $user_agent. "\n";
*/
		// Update data
		$difffile = new DiffFile($this->page);
		$difffile->set($diff->getDiff());

		unset($oldpostdata, $diff, $difffile);

		// Update *.rel *.ref data.
		//update_cache($this->page, false, $notimestamp);

		// Logging postdata (Plus!)
		postdata_write();

		if ($trackback > 1) {
			// TrackBack Ping
			tb_send($this->page, $links);
		}

		log_write('update',$this->page);

		self::create_recent_changes();

		// Create backup
		//make_backup($this->page, $postdata == ''); // Is $postdata null?
		$backup = new BackupFile($this->page);
		$backup->setBackup();

		// Create wiki text
		parent::set($postdata);
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
		$post_links = self::replace_plugin_link2null($post);
		$diff_links = self::get_link_list($diff);

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
	 * 一時的にプラグインをnullプラグインにして無効化し、リンクのみを取得
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
		$plus  = RendererFactory::factory($added); // WARNING: heavy and may cause side-effect
		preg_match_all('#href="(https?://[^"]+)"#', $plus, $links, PREG_PATTERN_ORDER);
		$links = array_unique($links[1]);

		// Reject from minus list
		if (! empty($removed) ) {
			$links_m = array();
			$minus = RendererFactory::factory($removed); // WARNING: heavy and may cause side-effect
			preg_match_all('#href="(https?://[^"]+)"#', $minus, $links_m, PREG_PATTERN_ORDER);
			$links_m = array_unique($links_m[1]);

			$links = array_diff($links, $links_m);
		}

		unset($plus,$minus);

		// Reject own URL (Pattern _NOT_ started with '$script' and '?')
		$links = preg_grep('/^(?!' . preg_quote(Router::get_script_absuri(), '/') . '\?)./', $links);

		// No link, END
		if (! is_array($links) || empty($links)) return;

		return $links;
	}
	/**
	 * ソースをシステム（rules.ini.phpなど）で定義されているルールに基づいて自動修正
	 * @param array $source ソース
	 * @return string
	 */
	public static function make_str_rules($source){
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
		if ($modify === FALSE && $multiline !== 0) $lines[] = str_repeat('}', $multiline);

		return implode("\n", $lines);
	}
	public function auto_template()
	{
		global $auto_template_func, $auto_template_rules;

		if (! $auto_template_func) return '';

		$body = '';
		$matches = array();
		foreach ($auto_template_rules as $rule => $template) {
			$rule_pattrn = '/' . $rule . '/';

			if (! preg_match($rule_pattrn, $this->page, $matches)) continue;

			$template_page = preg_replace($rule_pattrn, $template, $this->page);
			if (! is_page($template_page)) continue;

			$body = FileFactory::Wiki($template_page)->source();

			// Remove fixed-heading anchors
			$body = preg_replace('/^(\*{1,3}.*)\[#[0-9A-Za-z][\w-]+\](.*)$/m', '$1$2', $body);

			// Remove '#freeze'
			$body = preg_replace('/^#freeze\s*$/m', '', $body);

			$count = count($matches);
			for ($i = 0; $i < $count; $i++)
				$body = str_replace('$' . $i, $matches[$i], $body);

			break;
		}
		return $body;
	}
	/**
	 * 関連リンクを取得
	 * @return array
	 */
	public function getRelated(){
		global $related;
		// Get repated pages from DB
		$link = new Relational($this->page);
		$ret = $related + $link->get_related();
		ksort($ret, SORT_NATURAL);
		return $ret;
	}
	/**
	 * ページのアドレスを取得
	 */
	public function get_uri($cmd='read', $query=array(), $fragment=''){
		return Router::get_resolve_uri($cmd, $this->page, 'rel', $query, $fragment);
	}
	
	/**
	 * 配列からRecentChangesページを作成
	 */
	private function create_recent_changes(){
		global $whatsnew;

		if (!self::is_hidden()) return;

		// Create RecentChanges
		$buffer[] = '#norelated';
		foreach (array_keys(FileUtility::get_recent(true)) as $page) {
			$buffer[] = '-&epoch(' . $recent_pages[$page] . '); - [[' . htmlsc($page) . ']]';
		}
		$file = FileFactory::Wiki($whatsnew);
		$file->set($buffer);
	}
	/**
	 * 削除履歴を作成
	 * @params string $page 削除するページ
	 */
	private function create_recent_deleted(){
		global $whatsdeleted, $maxshow_deleted;
		if (auth::check_role('readonly') || !self::is_hidden($this->page)) return;

		$delated = FileFactory::Wiki($whatsdeleted);

		foreach ($delated->get() as $line) {
			if (preg_match('/^-(.+) - (\[\[.+\]\])$/', $line, $matches)) {
				$lines[$matches[2]] = $line;
			}
		}

		$_page = '[[' . $page . ']]';

		// Remove a report about the same page
		if (isset($lines[$_page])) unset($lines[$_page]);

		// Add
		array_unshift($lines, '-&epoch(' . UTIME . '); - ' . $_page . htmlsc($subject));
		array_unshift($lines, '#norelated');

		// Get latest $limit reports
		$lines = array_splice($lines, 0, $maxshow_deleted);
		$delated->set($lines);
	}
}

/* End of file WikiFile.php */
/* Location: /vender/PukiWiki/Lib/File/WikiFile.php */
