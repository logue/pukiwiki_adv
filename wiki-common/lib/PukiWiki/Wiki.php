<?php
/**
 * Wikiクラス
 *
 * @package   PukiWiki
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2013 PukiWiki Advance Developers Team
 * @create    2012/12/31
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Wiki.php,v 1.0.0 2013/09/02 22:56:00 Logue Exp $
 **/

namespace PukiWiki;

use PukiWiki\Auth\Auth;
use PukiWiki\Backup;
use PukiWiki\Diff\Diff;
use PukiWiki\File\AttachFile;
use PukiWiki\File\FileFactory;
use PukiWiki\File\LogFactory;
use PukiWiki\File\FileUtility;
use PukiWiki\Relational;
use PukiWiki\Renderer\PluginRenderer;
use PukiWiki\Renderer\RendererFactory;
use PukiWiki\Router;
use PukiWiki\Spam\Captcha;
use PukiWiki\Spam\IpFilter;
use PukiWiki\Spam\ProxyChecker;
use PukiWiki\Text\Rules;
use PukiWiki\Utility;
use Zend\XmlRpc\Client;
use Zend\XmlRpc\Request;
use ZendService\Akismet\Akismet;

/**
 * Wikiのコントローラー
 */
class Wiki{
	/**
	 * ページ名として使用可能な文字
	 */
	const VALIED_PAGENAME_PATTERN = '/^(?:[\x00-\x7F]|(?:[\xC0-\xDF][\x80-\xBF])|(?:[\xE0-\xEF][\x80-\xBF][\x80-\xBF]))+$/';
	/**
	 * ページ名に含めることができない文字
	 */
	const INVALIED_PAGENAME_PATTERN = '/[%|=|&|?|#|\r|\n|\0|\@|\t|;|\$|+|\\|\[|\]|\||^|{|}]/';
	/**
	 * 投稿ログ
	 */
	const POST_LOG_FILENAME = 'postlog.log';
	/**
	 * 投稿内容のロギングを行う（デバッグ用）
	 */
	const POST_LOGGING = false;
	/**
	 * HTML内のリンクのマッチパターン（画像なども対象とする）アドレスは[2]に格納される
	 */
	const HTML_URI_MATCH_PATTERN = '/<.+? (src|href)="(.*?)".+?>/is';
	/**
	 * weblogUpdates ping
	 */
	private static $ping_server = array(
		'http://rpc.weblogs.com/',
		'http://ping.feedburner.com/',
		'http://blogsearch.google.com/ping/RPC2',
		'http://www.blogpeople.net/ping/'
	);

	/**
	 * コンストラクタ
	 */
	public function __construct($page){
		$this->page = $page;
		// 以下はSplFileInfoの派生クラス
		$this->wiki = FileFactory::Wiki($this->page);
	}
/**************************************************************************************************/
	/**
	 * 編集可能か
	 * @param boolean $authegnticate 認証画面を通すか
	 * @param boolean $ignole_freeze 凍結されたページを無視するか
	 * @return boolean
	 */
	public function isEditable($authenticate = false, $ignole_freeze = false)
	{
		global $edit_auth, $cantedit;

		// 「編集時に認証する」が有効になっていない
		if (!$edit_auth) return true;
		// 無効なページ名
		if (!$this->isValied()) return false;
		// 編集できないページ
		foreach($cantedit as $key) {
			if ($this->page === $key) return false;
		}
		// 未認証時は読み取り専用になっている
		if (Auth::check_role('readonly')) return false;	
		// 凍結されている
		if (!$ignole_freeze && $this->isFreezed()) return false;
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
	 * ページのタイムスタンプを更新
	 * @param int $time 更新日時
	 * @return boolean
	 */
	public function touch($time = false){
		return $this->wiki->touch($time);
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
	 * ページのタイトルを取得
	 * @return string
	 */
	public function title(){
		$lines = self::get();
		while (! empty($lines)) {
			$line = array_shift($lines);
			if (preg_match('/^(TITLE):(.*)$/',$line,$matches)){
				return trim(Utility::stripHtmlTags(RendererFactory::factory($matches[2])));
			}
		}
		return $this->page;
	}
	/**
	 * 添付ファイルを取得
	 * @return array
	 */
	public function attach($with_hidden = false){
		$files = FileUtility::getExists(AttachFile::$dir);
		// ページに含まれる添付ファイルがない場合ここで終了
		if (!isset($files[$this->page])) return;
		return $files[$this->page];
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
	 * 要約を出力
	 * @param boolean $use_tag タグでくくる
	 * @param boolean $quote ()でくくる
	 * @return string
	 */
	public function description($length = 256){
		$source = $this->get();
		// ブロック型プラグイン(#～)削除
		$source = preg_replace("/^\#.*$/",'',$source);
		// インライン型プラグイン・文字参照(&～;)削除
		$source = preg_replace("/\&.*\;/",'',$source);
		// 変換してタグを削除
		$desc = Utility::stripHtmlTags(RendererFactory::factory($source));
		// 長さ調整
		if ($length !== 0) $desc = mb_strimwidth($desc, 0, $length, '...');
		// 改行を<br />タグに変換して出力
		return preg_replace("/([\r\n]\s*)+/m", '<br />', $desc);
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
		global $use_spam_check, $_string, $vars, $_title, $whatsnew, $whatsdeleted;

		// roleのチェック
		if (Auth::check_role('readonly')) return; // Do nothing
		if (Auth::is_check_role(PKWK_CREATE_PAGE))
			Utility::dieMessage( sprintf($_strings['error_prohibit'], 'PKWK_READONLY'), 403 );

		// 簡易スパムチェック（不正なエンコードだった場合ここでエラー）
		if ( !isset($vars['encode_hint']) || $vars['encode_hint'] !== PKWK_ENCODING_HINT ){
			Utility::dump();
			Utility::dieMessage( $_string['illegal_chars'], 403 );
		}

		// ポカミス対策：配列だった場合文字列に変換
		if (is_array($str)) {
			$str = join("\n", $str);
		}

		// 入力データーを整形（※string型です）
		$postdata = Rules::make_str_rules($str);

		// 過去のデーターを取得
		$oldpostdata = self::has() ? self::get(TRUE) : '';

		// 差分を生成（ここでの差分データーはAkismetでも使う）
		$diff = new Diff($postdata, $oldpostdata);

		// ログイン済みもしくは、自動更新されるページである
		$has_not_permission = Auth::check_role('role_contents_admin');

		// 未ログインの場合、S25Rおよび、DNSBLチェック
		if ($has_not_permission) {
			$ip_filter = new IpFilter();
			//if ($ip_filter->isS25R()) Utility::dieMessage('S25R host is denied.');

			// 簡易スパムチェック
			if (Utility::isSpamPost()){
				Utility::dump();
				Utility::dieMessage('Writing was limited. (Blocking SPAM)');
			}
			
			if (isset($use_spam_check['page_remote_addr']) && $use_spam_check['page_remote_addr'] !== 0) {
				// DNSBLチェック
				$listed = $ip_filter->checkHost();
				if ($listed !== false){
					Utility::dump('dnsbl');
					Utility::dieMessage(sprintf($_strings['blacklisted'],$listed), $_title['prohibit'], 400);
				}
			}

			if (isset($use_spam_check['page_contents']) && $use_spam_check['page_contents'] !== 0){
				// URLBLチェック
				$reason = self::checkUriBl($diff);
				if ($reason !== false){
					Utility::dump($reason);
					Utility::dieMessage('Writing was limited by URIBL (Blocking SPAM).', $_title['prohibit'], 400);
				}
			}
			// 匿名プロクシ
			if ($use_spam_check['page_write_proxy'] && ProxyChecker::is_proxy()) {
				Utility::dump('proxy');
				Utility::dieMessage('Writing was limited by PROXY (Blocking SPAM).', $_title['prohibit'], 400);
			}

			// Akismet
			global $akismet_api_key;
			if (isset($use_spam_check['akismet']) && $use_spam_check['akismet'] !== 0 && !empty($akismet_api_key) ){
				$akismet = new Akismet(
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
						'comment_content' => $postdata
					);
					
				//	if ($use_spam_check['akismet'] === 1){
				//		// 差分のみをAkismetに渡す
				//		foreach ($diff->getSes() as $key=>$line){
				//			if ($key !== $diff::SES_ADD) continue;
				//			$added_data[] = $line;
				//		}
				//		$akismet_post['comment_content'] = join("\n",$added_data);
				//		unset($added_data);
				//	}
					

					if($akismet->isSpam($akismet_post)){
						Utility::dump('akismet');
						Utility::dieMessage('Writing was limited by Akismet (Blocking SPAM).', $_title['prohibit'], 400);
					}
				}else{
					Utility::dieMessage('Akismet API key does not valied.', 500);
				}
			}

			// captcha check
			if ( (isset($use_spam_check['captcha']) && $use_spam_check['captcha'] !== 0)) {
				Captcha::check(false);
			}

		}

		// 現時点のページのハッシュを読む
		$old_digest = $this->wiki->has() ? $this->wiki->digest() : 0;

		// オリジナルが送られてきている場合、Wikiへの書き込みを中止し、競合画面を出す。
		// 現時点のページのハッシュと、送信されたページのハッシュを比較して異なる場合、
		// 自分が更新している間に第三者が更新した（＝競合が起きた）と判断する。
		$collided = isset($vars['digest']) && $old_digest !== 0 && $vars['digest'] !== $old_digest;

		if ($collided && isset($vars['original'])){
			return array(
				'msg'=>$_string['title_collided'],
				'body'=>
					$_string['msg_collided'] .
					Utility::showCollision($oldpostdata, $postdata, $vars['original']) .
					Utility::editForm($this->page, $postdata, false)
			);
		}

		// add client info to diff
		//$diffdata .= '// IP:"'. REMOTE_ADDR . '" TIME:"' . $now . '" REFERER:"' . $referer . '" USER_AGENT:"' . $user_agent. "\n";

		// 差分データーを保存
		FileFactory::Diff($this->page)->set($diff->getDiff());

		unset($oldpostdata, $diff, $difffile);

		// Logging postdata (Plus!)
		if (self::POST_LOGGING === TRUE) {
			Utility::dump(self::POST_LOG_FILENAME);
		}

		// 入力が空の場合、削除とする
		if (empty($str)){
			// Wikiページを削除
			$ret = $this->wiki->set('');
			Recent::set(null, $this->page);
			// 削除ログ
			Recent::create_recent_deleted();
			$keeptimestamp = false;
		}else{
			// Wikiを保存
			$ret = $this->wiki->set($postdata, $keeptimestamp);
		}

		if ($this->page !== $whatsnew || $this->page !== $whatsdeleted) {
			// バックアップを更新
			Factory::Backup($this->page)->set();

			// 更新ログをつける
			LogFactory::factory('update',$this->page)->set();

			if (!$keeptimestamp) {
				// weblogUpdates.pingを送信
				self::sendPing();
			}
		}

		// 最終更新を更新
		Recent::set($this->page);

		// 簡易競合チェック
		if ($collided) {
			return array('msg'=>$_string['title_collided'], 'body'=>$_string['msg_collided_auto']);
		}
	}

/**************************************************************************************************/
	/**
	 * ソース中のリンクを取得
	 * @param $source Wikiソース
	 * @return array
	 */
	private static function getLinkList($source){
		static $plugin_pattern, $replacement;
			// プラグインを無効化するためのマッチパターンを作成
		if (empty($plugin_pattern) || empty($replacement)){
			foreach(PluginRenderer::getPluginList() as $plugin=>$plugin_value){
				if ($plugin === 'ref' || $plugin === 'attach' || $plugin === 'attachref') continue;	// ただしrefやattachは除外（あまりブロック型で使う人いないけどね）
				$plugin_pattern[] = '/^#'.$plugin.'\(/i';
				$replacement[] = '#null(';
			}
		}
		$ret = array();
		// １行づつ置き換え
		foreach($source as $line){
			$ret[] = preg_replace($plugin_pattern,$replacement, $line);
		}

		$links = array();
		// プラグインを無効化したソースをレンダリング
		$html = RendererFactory::factory($ret);
		// レンダリングしたソースからリンクを取得
		preg_match_all(self::HTML_URI_MATCH_PATTERN, $html, $links, PREG_PATTERN_ORDER);
		unset($html);
		return array_unique($links[1]);
	}
	/**
	 * 差分から追加されたリンクと削除されたリンクを取得しURIBLチェック
	 * @param object $diff
	 * @return type
	 */
	private function checkUriBl($diff) {
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
			if ($uri_filter->checkHost()){
				return 'uribl';
			}
			if ($uri_filter->isListedNSBL()){
				return 'nsbl';
			}
		}
		return false;
	}
	/**
	 * ソースからひな形を作成
	 */
	public function auto_template()
	{
		global $auto_template_func, $auto_template_rules;

		if (! $auto_template_func) return '';

		$source = '';
		$matches = array();
		foreach ($auto_template_rules as $rule => $template) {
			$rule_pattrn = '/' . $rule . '/';

			if (! preg_match($rule_pattrn, $this->page, $matches)) continue;

			$template_page = preg_replace($rule_pattrn, $template, $this->page);
			if (! is_page($template_page)) continue;

			// ソースを取得
			$source = Factory::Wiki($template_page)->source();

			// Remove fixed-heading anchors
			$source = Rules::removeHeading($source);

			// Remove '#freeze'
			$source = preg_replace('/^#freeze\s*$/m', '', $source);

			$count = count($matches);
			for ($i = 0; $i < $count; $i++)
				$source = str_replace('$' . $i, $matches[$i], $source);

			break;
		}
		return $source;
	}
	/**
	 * weblogUpdates.pingを送信
	 * return void
	 */
	private function sendPing(){
		global $site_name;
		foreach (self::$ping_server as $uri){
			$client = new Client($uri);
			$request = new Request();
			$request->setMethod('weblogUpdates.ping');
			$request->setParams(array($site_name, Router::get_script_absuri()));
			$client->doRequest($request);
		}
	}
}