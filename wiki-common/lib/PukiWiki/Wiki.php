<?php
namespace PukiWiki;

use PukiWiki\File\FileFactory;
use PukiWiki\Renderer\RendererFactory;
use PukiWiki\Router;
use PukiWiki\Relational;
use PukiWiki\Utility;
use PukiWiki\Auth\Auth;
use PukiWiki\Diff;

/**
 * Wikiのコントローラー
 */
class Wiki{
	/**#@+
	 * 宣言
	 */
	// ページ名として使用可能な文字
	const VALIED_PAGENAME_PATTERN = '/^(?:[\x00-\x7F]|(?:[\xC0-\xDF][\x80-\xBF])|(?:[\xE0-\xEF][\x80-\xBF][\x80-\xBF]))+$/';
	// ページ名に含めることができない文字
	const INVALIED_PAGENAME_PATTERN = '/[%|=|&|?|#|\r|\n|\0|\@|\t|;|\$|+|\\|\[|\]|\||^|{|}]/';
	// 投稿ログ
	const POST_LOG_FILENAME = 'postlog.log';
	// 投稿内容のロギングを行う（デバッグ用）
	const POST_LOGGING = false;
	/**#@-*/

	public function __construct($page){
		$this->page = $page;
		// 以下はSplFileInfoの派生クラス
		$this->wiki = FileFactory::Wiki($this->page);
//		$this->backup = FileFactory::Backup($this->page);
//		$this->diff = FileFactory::Diff($this->page);
	}

/**************************************************************************************************/
	/**
	 * 編集可能か
	 * @param boolean $authegnticate 認証画面を通すかのフラグ
	 * @return boolean
	 */
	public function isEditable($authenticate = false)
	{
		global $edit_auth, $edit_auth_pages, $auth_api, $defaultpage, $_title, $edit_auth_pages_accept_ip;
		global $cantedit;

		if (!$this->isValied()) return false;	// 無効なページ名

		// 編集できないページ
		foreach($cantedit as $key) {
			if ($this->page === $key) return false;
		}

		if ($this->isFreezed()) return false;	// 凍結されている

		if (!$edit_auth) return true;	// 「編集時に認証する」が有効になっていない

		// 認証画面を表示する
		if ($authenticate && !Auth::auth($this->page, true, false, $edit_auth_pages, $_title['cannotedit'])) return false;

		if (Auth::check_role('readonly')) return false;	// 未認証時に読み取り専用になっている

		// ユーザ別の権限を読む
		if (self::checkPermission('read') && self::checkPermission('edit')) return true;

		return false;
	}
	/**
	 * 読み込み可能か
	 * @param boolean $authenticate 認証画面を通すかのフラグ
	 * @return boolean
	 */
	public function isReadable($authenticate = false)
	{
		global $read_auth, $read_auth_pages, $auth_api, $_title, $read_auth_pages_accept_ip;

		if (!$read_auth) return true;

		// 許可IPの場合チェックしない
		if ( Auth::ip_auth($this->page, $read_auth_pages_accept_ip)) {
			return TRUE;
		}
		// 認証
		if ($authenticate && !Auth::auth($this->page, true, false, $read_auth_pages, $_title['cannotread'])) return false;

		// ユーザ別の権限を読む
		if (self::checkPermission('read')) return true;

		return false;
	}
	/**
	 * 凍結されているか
	 * @global boolean $function_freeze
	 * @return boolean
	 */
	public function isFreezed()
	{
		$buffer = $this->wiki->head(1);	// 先頭1行のみ読み込む
		return strstr($buffer,'#freeze');
	}
	
	/**
	 * 有効なページ名か（is_page()）
	 * @return boolean
	 */
	public function isValied(){
		return (
			! self::isInterWiki($this->page) &&	// InterWikiでない
			Utility::isBracketName($this->page) &&	// BlacketNameである
			! preg_match('#(^|/)\.{1,2}(/|$)#', $this->page) &&
			preg_match(self::INVALIED_PAGENAME_PATTERN, $this->page) !== false &&	// 無効な文字が含まれていない。
			preg_match(self::VALIED_PAGENAME_PATTERN, $this->page)	// 使用可能な文字である
		);
	}
	/**
	 * InterWikiか
	 * @return boolean
	 */
	public function isInterWiki(){
		return Utility::isInterWiki($this->page);
	}
	/**
	 * 表示しないページか（check_non_list()）
	 * @global array $non_list
	 * @return boolean
	 */
	public function isHidden(){
		global $non_list;
		return preg_match( '/' . $non_list . '/' , $this->page);
	}
	/**
	 * 特殊ページか
	 * @return boolean;
	 */
	public function isSpecialPage(){
		global $navigation,$whatsnew,$whatsdeleted,$interwiki,$menubar,$sidebar,$headarea,$footarea;
		
		return preg_match('/['.
			$navigation     . '|' . // Navigation
			$whatsnew       . '|' . // RecentChanges
			$whatsdeleted   . '|' . // RecentDeleted
			$interwiki      . '|' . // InterWikiName
			$menubar        . '|' . // MenuBar
			$sidebar        . '|' . // SideBar
			$headarea       . '|' . // :Headarea
			$footarea.              // :Footarea
		']$/', $page) ? TRUE : FALSE;
	}

/**************************************************************************************************/

	/**
	 * 読み込み可能かをチェック（メッセージを表示する）
	 */
	public function checkReadable($authenticate = false){
		if (! self::isReadable($authenticate)){
			Utility::dieMessage('You have not permisson to read this page.',403);
		}
	}
	/**
	 * 編集可能かをチェック（メッセージを表示する）
	 */
	public function checkEditable($authenticate = false){
		if (! self::isEditable($authenticate)){
			Utility::dieMessage('You have not permisson to edit this page.',403);
		}
	}

/**************************************************************************************************/

	/**
	 * HTMLに変換
	 * @return string
	 */
	public function render(){
		global $digest;
		if (!$this->wiki->has()) return;
		if (empty($digest)){
			$digest = $this->digest();
		}
		return RendererFactory::factory($this->wiki->get());
	}
	public function digest(){
		return $this->wiki->digest();
	}
	/**
	 * ページのアドレスを取得
	 */
	public function uri($cmd='read', $query=array(), $fragment=''){
		return Router::get_resolve_uri($cmd, $this->page, 'rel', $query, $fragment);
	}
	/**
	 * 関連リンクを取得
	 * @return array
	 */
	public function related(){
		global $related;
		// Get repated pages from DB
		$link = new Relational($this->page);
		$ret = $related + $link->getRelated();
		ksort($ret, SORT_NATURAL);
		return $ret;
	}
	/**
	 * ファイルの存在確認
	 * @param $type 種類
	 * @return boolean
	 */
	public function has(){
		return $this->wiki->isFile();
	}
	/**
	 * 更新時刻を設定／取得
	 * @param type $time
	 * @return int
	 */
	public function time($time = ''){
		return $this->wiki->time($time);
	}
	/**
	 * 経過時間を取得
	 * @param boolean $use_tag タグでくくる
	 * @param boolean $quote ()でくくる
	 * @return string
	 */
	public function passage($use_tag = true, $quote = true){
		$pg_passage = $quote ? '('.$this->wiki->passage().')' : $this->wiki->passage();
		return $use_tag ? '<small class="passage">' . $pg_passage . '</small>' : $pg_passage;
	}
	/**
	 * ページを読み込む
	 * @param string $str 書き込むデーター
	 * @param boolean $notimestamp タイムスタンプを更新するかのフラグ
	 * @return void
	 */
	public function get($join = false){
		return $this->wiki->get($join);
	}
	/**
	 * ページを書き込む
	 * @param string $str 書き込むデーター
	 * @param boolean $notimestamp タイムスタンプを更新するかのフラグ
	 * @return void
	 */
	public function set($str, $keeptimestamp = false){
		global $trackback;
		global $use_spam_check, $_strings, $_title, $post;
		global $vars, $now, $akismet_api_key;

		if (is_array($str)) {
			// ポカミス対策：配列だった場合文字列に変換
			$str = join("\n", $str);
		}

		// SPAM Check (Client(Browser)-Server Ticket Check)
		if ( !isset($vars['encode_hint']) && !defined(PKWK_ENCODING_HINT) )
			Utility::dump();
		if ( isset($vars['encode_hint']) && $vars['encode_hint'] !== PKWK_ENCODING_HINT ){
			Utility::dump();
		}

		if (Utility::isSpamPost())
			Utility::dump();

		// captcha check
		if ( (isset($use_spam_check['captcha']) && $use_spam_check['captcha'] !== 0) && (isset($use_spam_check['multiple_post']) && $use_spam_check['multiple_post'] !== 1)) {
			captcha_check(( $use_spam_check['captcha'] === 2 ? false : true) );
		}

		// roleのチェック
		if (Auth::check_role('readonly')) return; // Do nothing
		if (Auth::is_check_role(PKWK_CREATE_PAGE))
			Utility::dieMessage( sprintf($_strings['error_prohibit'], 'PKWK_READONLY'), 403 );

		if (empty($str)){
			// 入力が空の場合、削除とする
			self::create_recent_deleted();
			return $this->wiki->set('');
		}

		// rule.ini.ph
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

		if (isset($vars['page']) && $vars['page'] === $this->page || !Auth::check_role('role_contents_admin') ){
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
		$this->diff->set($diff->getDiff());

		unset($oldpostdata, $diff, $difffile);

		if ($trackback > 1) {
			// TrackBack Ping
			tb_send($this->page, $links);
		}

//		log_write('update',$this->page);

		self::setRecent($this->page);

/*
		// Create backup
		//make_backup($this->page, $postdata == ''); // Is $postdata null?
		$backup = new BackupFile($this->page);
		$this->backup->setBackup();

		// Logging postdata (Plus!)
		if (self::POST_LOGGING === TRUE) {
			Utility::dump(self::POST_LOG_FILENAME);
		}

		// Create wiki text
		$this->wiki->set($postdata);
*/
	}

/**************************************************************************************************/

	/**
	 * 追加されたリンクを取得
	 * @param string $post 入力データ
	 * @param string $diff 差分データ
	 * @return array
	 */
	private function get_this_time_links($post,$diff)
	{
		$links = array();
		$post_links = self::replaceNullPlugin($post);
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
	private function replaceNullPlugin($data){
		global $exclude_link_plugin;

		$pattern = $replacement = array();
		foreach($exclude_link_plugin as $plugin) {
			$pattern[] = '/^#'.$plugin.'\(/i';
			$replacement[] = '#null(';
		}

		$exclude = preg_replace($pattern,$replacement, explode("\n", $data));
		$links = array();
		$html = RendererFactory::factory($exclude);
		preg_match_all('#href="(https?://[^"]+)"#', $html, $links, PREG_PATTERN_ORDER);
		unset($html);
		return array_unique($links[1]);
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

		return (! is_array($links) || empty($links)) ? null : $links;
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

		return join("\n", $lines);
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

			$body = Factory::Wiki($template_page)->source();

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
}