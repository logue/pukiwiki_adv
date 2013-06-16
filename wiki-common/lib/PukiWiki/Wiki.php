<?php
namespace PukiWiki;

use PukiWiki\Auth\Auth;
use PukiWiki\Backup;
use PukiWiki\Diff;
use PukiWiki\File\FileFactory;
use PukiWiki\File\LogFactory;
use PukiWiki\Relational;
use PukiWiki\Renderer\RendererFactory;
use PukiWiki\Router;
use PukiWiki\Spam\IpFilter;
use PukiWiki\Spam\UrlFilter;
use PukiWiki\Text\Rules;
use PukiWiki\Utility;

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
	/**
	 * HTML内のリンクのマッチパターン（画像なども対象とする）アドレスは[2
	 */
	const HTML_URI_MATCH_PATTERN = '/<.+? (src|href)="(.*?)".+?>/is';
	/**#@-*/

	public function __construct($page){
		$this->page = $page;
		// 以下はSplFileInfoの派生クラス
		$this->wiki = FileFactory::Wiki($this->page);
	}

/**************************************************************************************************/
	/**
	 * 編集可能か
	 * @param boolean $authegnticate 認証画面を通すかのフラグ
	 * @return boolean
	 */
	public function isEditable($authenticate = false)
	{
		global $edit_auth, $cantedit;

		// 「編集時に認証する」が有効になっていない
		if (!$edit_auth) return true;
		// 無効なページ名
		if (!$this->isValied()) return false;
		// 凍結されている
		if ($this->isFreezed()) return false;
		// 編集できないページ
		foreach($cantedit as $key) {
			if ($this->page === $key) return false;
		}
		// 未認証時に読み取り専用になっている
		if (Auth::check_role('readonly')) return false;	
		// ユーザ別の権限を読む
		if (Auth::auth($this->page, 'edit', $authenticate)) return true;
		return false;
	}
	/**
	 * 読み込み可能か
	 * @param boolean $authenticate 認証画面を通すかのフラグ
	 * @return boolean
	 */
	public function isReadable($authenticate = false)
	{
		global $read_auth;
		// 存在しない
		if (!$this->wiki->has()) return false;
		// 閲覧時に認証が有効になっていない
		if (!$read_auth) return true;
		// 認証
		if (Auth::auth($this->page, 'read', $authenticate)) return true;
		return false;
	}
	/**
	 * 凍結されているか
	 * @global boolean $function_freeze
	 * @return boolean
	 */
	public function isFreezed()
	{
		// 先頭1行のみ読み込み、そこに#freezeがある場合凍結とみなす
		return strstr($this->wiki->head(1),'#freeze');
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
	public function isSpecial(){
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
	/**
	 * 記事の要約（md5ハッシュ）を取得
	 * @return string
	 */
	public function digest(){
		return $this->wiki->digest();
	}
	/**
	 * ページのアドレスを取得
	 * @return string
	 */
	public function uri($cmd='read', $query=array(), $fragment=''){
		return Router::get_resolve_uri($cmd, $this->page, 'rel', $query, $fragment);
	}
	/**
	 * ページのリンクを取得
	 * @return string
	 */
	public function link($cmd='read', $query=array(), $fragment=''){
		$_page = Utility::htmlsc($this->page);
		return '<a href="' . $this->uri($cmd, $query, $fragment) . '" title="' . $_page . ' ' . $this->passage(false, true) . '">'. $_page . '</a>';
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
		global $use_spam_check, $_strings, $vars;

		// roleのチェック
		if (Auth::check_role('readonly')) return; // Do nothing
		if (Auth::is_check_role(PKWK_CREATE_PAGE))
			Utility::dieMessage( sprintf($_strings['error_prohibit'], 'PKWK_READONLY'), 403 );

		// ログイン済みもしくは、自動更新されるページである
		$has_permission = Auth::check_role('role_contents_admin') || isset($vars['page']) && $vars['page'] === $this->page;

		// 未ログインの場合、S25Rおよび、DNSBLチェック
		if (!$has_permission) {
			$ip_filter = new IpFilter();
			if ($ip_filter->isS25R()) Utility::dieMessage('S25R host is denied.');
			
			if ($use_spam_check['page_remote_addr']) {
				$listed = $ip_filter->checkDNSBL();
				if ($listed) Utility::dieMessage(sprintf($_strings['blacklisted'],$listed), $_title['prohibit'], 400);
			}
		}

		// 簡易スパムチェック（不正なエンコードだった場合ここでエラー）
		if ( isset($vars['encode_hint']) && $vars['encode_hint'] !== PKWK_ENCODING_HINT ){
			Utility::dump();
		}

		// ポカミス対策：配列だった場合文字列に変換
		if (is_array($str)) {
			$str = join("\n", $str);
		}

		if (empty($str)){
			Recent::set(null, $this->page);
			// 入力が空の場合、削除とする
			Recent::create_recent_deleted();
			return $this->wiki->set('');
		}

		if (Utility::isSpamPost())
			Utility::dump();

		// captcha check
		if ( (isset($use_spam_check['captcha']) && $use_spam_check['captcha'] !== 0) && (isset($use_spam_check['multiple_post']) && $use_spam_check['multiple_post'] !== 1)) {
			captcha_check(( $use_spam_check['captcha'] === 2 ? false : true) );
		}

		// 入力データーを整形
		$postdata = Rules::make_str_rules($str);
		$oldpostdata = self::has() ? self::get(TRUE) : '';

		// 差分を生成
		$diff = new Diff($postdata, $oldpostdata);

	//	$referer = (isset($_SERVER['HTTP_REFERER'])) ? Utility::htmlsc($_SERVER['HTTP_REFERER']) : 'None';
	//	$user_agent = Utility::htmlsc();


		if (!$has_permission) {
			// URLBLチェック
			if ( $use_spam_check['page_contents']){
				self::checkUriBl($diff);
			}
			// 匿名プロクシ
			if ($use_spam_check['page_write_proxy'] && is_proxy()) {
				Utility::dump();
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
						'user_agent' => $_SERVER['HTTP_USER_AGENT'],
						'comment_type' => 'comment',
						'comment_author' => isset($vars['name']) ? $vars['name'] : 'Anonymous',
					);
					if ($use_spam_check['akismet'] === 2){
						$akismet_post['comment_content'] = $postdata;
					}else{
						// 差分のみをAkismetに渡す
						foreach ($diff->getSes() as $key=>$line){
							if ($key !== $diff::SES_ADD) continue;
							$added_data[] = $line;
						}
						$akismet_post['comment_content'] = join("\n",$added_data);
						unset($added_data);
					}

					if($akismet->isSpam($akismet_post)){
						Utility::dump('akismet.log');
						die_message('Writing was limited by Akismet (Blocking SPAM).', $_title['prohibit'], 400);
					}
				}else{
					die_message('Akismet API key does not valied.', 500);
				}
			}
		}

		// add client info to diff
		//$diffdata .= '// IP:"'. REMOTE_ADDR . '" TIME:"' . $now . '" REFERER:"' . $referer . '" USER_AGENT:"' . $user_agent. "\n";


		// 差分データーを保存
		FileFactory::Diff($this->page)->set($diff->getDiff());

		unset($oldpostdata, $diff, $difffile);

		global $whatsnew;
		if (!$keeptimestamp || $page !== $whatsnew){
			// 最終更新を更新
			Recent::set($this->page);
		}
		
		// Create backup
		//make_backup($this->page, $postdata == ''); // Is $postdata null?
		$backup = new Backup($this->page);
		$backup->set();

		// Logging postdata (Plus!)
		if (self::POST_LOGGING === TRUE) {
			Utility::dump(self::POST_LOG_FILENAME);
		}

		// 更新ログをつける
		LogFactory::factory('update',$this->page)->set();

		// Create wiki text
		$this->wiki->set($postdata);
	}

/**************************************************************************************************/
	/**
	 * ソース中のリンクを取得
	 * @param $source Wikiソース
	 * @return array
	 */
	private static function getLinkList($source){
		static $plugin_pattern, $replacement;
		if (empty($plugin_pattern) || empty($replacement)){
			// プラグインを無効化するためのマッチパターンを作成
			foreach(PluginRenderer::getPluginList() as $plugin=>$plugin_value){
				if ($plugin == 'ref' || $plugin = 'attach') continue;	// ただしrefやattachは除外（あまりブロック型で使う人いないけどね）
				$plugin_pattern[] = '/^#'.$plugin.'\(/i';
				$replacement[] = '#null(';
			}
		}
		$ret = array();
		foreach($source as $line){
			$ret[] = preg_replace($plugin_pattern,$replacement, $line);
		}

		$links = array();
		// プラグインを無効化したソースをレンダリング
		$html = RendererFactory::factory($ret);
		// レンダリングしたソースからリンクを取得
		preg_match_all(self::HTML_URI_MATCH_PATTERN, $html, $links, PREG_PATTERN_ORDER);
		unset($html);
		return array_unique($links[2]);
	}
	/**
	 * 差分から追加されたリンクと削除されたリンクを取得しURIBLチェック
	 * @param object $diff
	 * @return type
	 */
	private function checkUriBl($diff)
	{
		
		// 変数の初期化
		$links = $added = $removed = array();

		// 差分から追加行と削除行を取得
		foreach ($diff->getSes() as $key=>$line){
			if ($key === $diff::SES_ADD){
				$added[] = $line;
			}else if ($key === $diff::SES_DELETE){
				$removed[] = $line;
			}
		}

		// それぞれのリンクの差分を取得
		$links = array_diff( self::getLinkList($added) , self::getLinkList($removed));
		
		unset($added, $removed);

		// 自分自身へのリンクを除外
		$links = preg_grep('/^(?!' . preg_quote(Router::get_script_absuri(), '/') . '\?)./', $links);

		// ホストのみ取得
		foreach( $links as $temp_uri ) {
			$temp_uri_info = parse_url( $temp_uri );
			if (empty($temp_uri_info['host'])) continue;
			$uri_filter = new UriFilter($temp_uri_info['host']);
			if ($uri_filter->checkHost()) Utility::dieMessage('URIBL matched! : '.$temp_uri_info['host']);
			if ($uri_filter->isListedNSBL()) Utility::dieMessage('Name server BL! : '.$temp_uri_info['host']);
		}
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