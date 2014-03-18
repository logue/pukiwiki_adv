<?php
/**
 * プラグインレンダラークラス
 *
 * @package   PukiWiki
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013-2014 PukiWiki Advance Developers Team
 * @create    2013/03/11
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: PluginRenderer.php,v 1.0.0 2014/01/30 14:43:00 Logue Exp $
 **/
 
namespace PukiWiki\Renderer;

use DirectoryIterator;
use Exeption;
use PukiWiki\Auth\Auth;
use PukiWiki\Factory;
use PukiWiki\Renderer\Header;
use PukiWiki\Router;
use PukiWiki\Spam\PostId;
use PukiWiki\Utility;
use Zend\Json\Json;
use Zend\ProgressBar\Upload\SessionProgress;
use Exception;

/**
 * プラグイン処理クラス
 */
class PluginRenderer{
	/**
	 * プラグインのファイル一覧キャッシュ名
	 */
	const PLUGIN_EXISTS_CACHE = 'plugins';
	/**
	 * プラグインの呼び出し回数の上限
	 */
	const PLUGIN_CALL_TIME_LIMIT = 1024;
	/**
	 * PostIDチェックを行わないプラグイン
	 */
	const IGNOLE_POSTID_CHECK_PATTERN = '/^[menu|side|header|footer|full|read|include|calendar|login]$/';
	/**
	 * プラグイン名のマッチパターン
	 */
	const PLUGIN_NAME_PATTERN = '/^\w{1,64}$/';
	/**
	 * プラグインで使用するメッセージテキストをグローバル変数に保存
	 * @param array $messages メッセージ
	 * @return void
	 */
	public static function setPluginMessages($messages)
	{
		foreach ($messages as $name=>$val)
			if (! isset($GLOBALS[$name]))
				$GLOBALS[$name] = $val;
	}
	/**
	 * プラグインのオプションを取得
	 * @param string $args 入力文字
	 * @param array $params パラメーター（参照渡し）
	 * @param boolean $tolower 与えられたパラメーターを小文字にするか
	 * @param string $separator 分割子
	 * @return boolean
	 */
	public static function getPluginOption($args, &$params, $tolower=TRUE, $separator=':')
	{
		if (empty($args)) {
			$params['_done'] = TRUE;
			return TRUE;
		}
		
		$keys = array_keys($params);
		if (is_array($args)){
			foreach($args as $val) {
				list($_key, $_val) = array_pad(explode($separator, $val, 2), 2, TRUE);
				$_key = trim($tolower === TRUE ? strtolower($_key) : $_key);
				if (is_string($_val)) $_val = trim($_val);
				if (in_array($_key, $keys) && $params['_done'] !== TRUE) {
					$params[$_key] = $_val;    // Exist keys
				} else if ( !empty($val) ) {
					$params['_args'][] = $val; // Not exist keys, in '_args'
					$params['_done'] = TRUE;
				}
			}
		}
		$params['_done'] = TRUE;
		return TRUE;
	}
	/**
	 * プラグインおよび、設定一覧キャッシュを生成
	 * （あえて、staticキャッシュは使わない）
	 * @param boolean $force キャッシュ生成を矯正する
	 * @return array
	 */
	public static function getPluginList($force = false){
		global $cache;
		//$t1 = microtime();

		if (!$force) {
			// ディレクトリの更新チェック（変更があった場合、キャッシュを再生成）
			foreach(array(PLUGIN_DIR, EXT_PLUGIN_DIR, INIT_DIR, SITE_INIT_DIR) as $p_dir) {
				if (!is_dir($p_dir)) continue;
				$d_modified[] = filemtime($p_dir);
			}
			if ($cache['wiki']->hasItem(self::PLUGIN_EXISTS_CACHE)){
				$term_cache_meta = $cache['wiki']->getMetadata(self::PLUGIN_EXISTS_CACHE);
				if ($term_cache_meta['mtime'] < max($d_modified)) {
					$force = true;
				}
			}
		}

		// キャッシュ処理
		if ($force) {
			$cache['wiki']->removeItem(self::PLUGIN_EXISTS_CACHE);
		}else if ($cache['wiki']->hasItem(self::PLUGIN_EXISTS_CACHE)) {
			$plugins = $cache['wiki']->getItem(self::PLUGIN_EXISTS_CACHE);
		//	$cache['wiki']->touchItem(self::PLUGIN_EXISTS_CACHE);
			return $plugins;
		}
		
		// プラグインを走査（追加プラグイン内のプラグインがオーバーライドされる）
		foreach(array(PLUGIN_DIR, EXT_PLUGIN_DIR) as $p_dir) {
			if (!is_dir($p_dir)) continue;
			foreach (new DirectoryIterator($p_dir) as $fileinfo) {
				// ファイル以外は無視
				if (!$fileinfo->isFile() || !$fileinfo->isReadable()) continue;

				// 読み込み可能ならエイリアスでも読み取ります。
				if (strpos($fileinfo->getFilename(), '.inc.php') !== false){
					// プラグイン名を取得
					$name = $fileinfo->getBasename('.inc.php');
					// プラグイン名が英数字64文字以下でない
					if (!preg_match('/^\w{1,64}$/', $name)) throw new Exception('PluginRenderer::getPluginList(): Plugin name "'.$name.'" is invalied or too long! (less than 64 chars)');
					$plugins[$name] = array(
						// 利用可能である
						'usable' => true,
						// 読み込み済みフラグ
						'loaded' => false,
						// パス
						'path' => $fileinfo->getPathname(),
						// 追加のプラグインか？
						'is_ext' => ($p_dir == EXT_PLUGIN_DIR)
					);
				}
			}
		}
		unset($p_dir, $fileinfo);
		// プラグイン設定ファイルを走査（サイト別の設定が優先して読み込まれる。オーバーライドではないので注意）
		foreach(array(INIT_DIR, SITE_INIT_DIR) as $p_dir) {
			if (!is_dir($p_dir)) continue;
			foreach (new DirectoryIterator($p_dir) as $fileinfo) {
				// ファイル以外は無視
				if (!$fileinfo->isFile()) continue;
				// 設定ファイルから、拡張子をとった名前
				$ini = $fileinfo->getBasename('.ini.php');
				// プラグインが存在しない場合はスキップ
				if (! array_key_exists($ini, $plugins)) continue;
				// 設定ファイルのパスを変数に代入
				if (strpos($fileinfo->getBasename(), '.ini.php') !== false && $fileinfo->isReadable()){
					// 設定ファイル
					$plugins[$ini]['conf'] = $fileinfo->getPathname();
				}
			}
		}
		unset($p_dir, $fileinfo);
		// キャッシュを保存
		$cache['wiki']->setItem(self::PLUGIN_EXISTS_CACHE, $plugins);
		return $plugins;
	}
	/**
	 * プラグイン一覧からプラグイン情報を取得
	 * @param string $name プラグイン名
	 * @return array
	 */
	public static function getPlugin($name, $load = true){
		static $plugins;
		global $exclude_plugin;
	
		// 念のためプラグイン名を小文字にする
		$name = strtolower($name);

		// 無効化しているプラグインの場合
		if (is_array($exclude_plugin) && in_array($name, $exclude_plugin)) return FALSE;

		// プラグイン一覧を取得
		if (!isset($plugins)) $plugins = self::getPluginList();

		if (!isset($plugins[$name])){
			// プラグインが見つからない
			$plugins[$name] = array(
				'loaded' =>true,
				'method' => array(
					'init'=>false,
					'action'=>false,
					'convert'=>false,
					'inline'=>false
				)
			);
		}else if ($plugins[$name]['loaded'] == false){
			// プラグインが読み込まれてないとき
			if ($load == true){
				// 設定を読み込む
				if (isset($plugins[$name]['conf'])) require_once $plugins[$name]['conf'];
				// プラグインを読み込む
				require_once $plugins[$name]['path'];	// FIXME require_onceじゃあまり意味ない。

				// 読み込み済フラグ
				$plugins[$name]['loaded'] = true;
			}
			// 利用可能なAPIをチェック
			foreach (array('init','action','convert','inline') as $method){
				$plugins[$name]['method'][$method] = function_exists('plugin_'.$name.'_'.$method);	
			}
		}

		/**
		 * 'プラグイン名' => array(
		 *     'loaded' => 読み込み済みか
		 *     'path' => プラグインのパス
		 *     'conf' => 読み込まれた設定のパス
		 *     'is_ext' => サードパーティー製プラグインか？
		 *     'method' => array(
		 *         'init' => true ...
		 *         利用可能なAPIのリスト
		 *      )
		 * );
		 */
		return $plugins[$name];
	}
	/**
	 * プラグインが利用可能か確認
	 */
	public static function hasPlugin($name){
		$plugin = self::getPlugin($name, false);
		return $plugin['loaded'];
	}
	/**
	 * プラグインのメソッドの存在確認
	 * @param string $name プラグイン名
	 * @return boolean
	 */
	public static function hasPluginMethod($name, $method){
		global $_string;
		static $count;
		$plugin = self::getPlugin($name);

		if ($plugin['method'][$method] == true) {
			// プラグインの呼び出し回数をチェック
			$count[$name] = !isset($count[$name]) ? 1 : $count[$name]++;

			if ($count[$name] > self::PLUGIN_CALL_TIME_LIMIT) {
				Utility::dieMessage('PluginRenderer::hasPluginMethod(): '.sprintf($_string['plugin_multiple_call'],  Utility::htmlsc($name), self::PLUGIN_CALL_TIME_LIMIT));
			}
		}
		return true;
	}
	/**
	 * プラグインの初期化コマンドを実行
	 * @staticvar type $done
	 * @staticvar type $checked
	 * @param type $name
	 * @return boolean
	 */
	public static function executePluginInit($name)
	{
		static $done, $checked;

		// 初期化完了済みの場合処理しない
		if (isset($done[$name])) return true;

		$plugin = self::getPlugin($name);

		// 多言語化
		if ($plugin['is_ext']) {
			T_bindtextdomain($name,EXT_LANG_DIR);
		} else {
			T_bindtextdomain($name,LANG_DIR);
		}

		// プラグインの初期化関数を実行（存在する場合）
		if ($plugin['method']['init']) {
			T_textdomain($name);
			$done[$name] = call_user_func('plugin_'.$name.'_init');
			T_textdomain(DOMAIN);
			if (!isset($checked[$name])) {
				$done[$name] = TRUE; // checked.
			}
		}
		$done[$name] = TRUE; // checked.
		return true;
	}

	/**
	 * アクション型プラグインを実行
	 * @global type $vars
	 * @global type $_string
	 * @global type $use_spam_check
	 * @global type $post
	 * @param type $name
	 * @return type
	 */
	public static function executePluginAction($name)
	{
		global $vars, $_string, $use_spam_check, $post;
		$plugin = self::getPlugin($name);
		$funcname = 'plugin_' . $name . '_action';

		// 命令が実装されてない
		if (! $plugin['method']['action'] || !function_exists($funcname))
			Utility::dieMessage('PluginRenderer::executePluginAction(): ' .sprintf($_string['plugin_not_implemented'],htmlsc($name)),501);

		// プラグインの初期化
		if (self::executePluginInit($name) === FALSE) {
			Utility::dieMessage('PluginRenderer::executePluginAction(): ' .sprintf( $_string['plugin_init_error'], Utility::htmlsc($name) ));
		}

		// 入力のエンコードをチェック
		if (isset($vars['encode_hint']) && !empty($vars['encode_hint']) && (PKWK_ENCODING_HINT !== $vars['encode_hint']) ) {
			Utility::dieMessage('PluginRenderer::executePluginAction(): ' .$_string['plugin_encode_error']);
		}

	//	if ( isset($post['ticket']) && $post['ticket'] !== md5(Utility::getTicket() . REMOTE_ADDR) ){
	//		die_message('host is mismatch!');
	//	}

		// postidをチェックする
		if ( (isset($use_spam_check['multiple_post']) && $use_spam_check['multiple_post'] === 1) && (isset($vars['postid']) && !PostId::check($vars['postid'])) )
			Utility::dieMessage('PluginRenderer::executePluginAction(): ' .$_string['plugin_postid_error']);

		
		// 実行
		T_textdomain($name);
		$retvar = call_user_func($funcname);
		T_textdomain(DOMAIN);

		$retvar['body'] = isset($retvar['body']) ? self::addHiddenField($retvar['body'], $name) : null;

		return $retvar;
	}

	/**
	 * ブロック型プラグインを実行
	 * @param type $name
	 * @param type $args
	 * @return type
	 */
	public static function executePluginBlock($name, $args = '')
	{
		global $digest, $_string;
		$plugin = self::getPlugin($name);
		$funcname = 'plugin_' . $name . '_convert';

		// 命令が実装されてない
		if (! $plugin['method']['convert'] || !function_exists($funcname))
			return '<p class="alert alert-warning">PluginRenderer::executePluginBlock(): ' . sprintf($_string['plugin_not_implemented'],'#'.Utility::htmlsc($name).'()') . '</p>';

		// プラグインの初期化
		if (self::executePluginInit($name) === FALSE) {
			return '<p class="alert alert-danger">PluginRenderer::executePluginBlock(): ' . sprintf($_string['plugin_init_error'], Utility::htmlsc($name)) . '</p>';
		}

		// 複数行のプラグイン
		$pos  = strpos($args, "\r"); // "\r" is just a delimiter
		if ($pos !== FALSE) {
			$body = substr($args, $pos + 1);
			$args = substr($args, 0, $pos);
		}

		// プラグインのパラメータを取得。（#plugin(){}の()内の部分をカンマ区切りで分割）
		$aryargs = empty($args) ? array() : explode(',', $args);

		// プラグインの末尾のパラメータを取得（#plugin(){}の{}内の部分）
		if (isset($body)) $aryargs[] = & $body;

		// digestを知事保存し、ロケールファイルを差し替えて翻訳
		$_digest = $digest;
		T_textdomain($name);
		// プラグインを実行
		$retvar  = call_user_func_array($funcname, $aryargs);
		// ロケールとdigestをもとに戻す
		T_textdomain(DOMAIN);
		$digest  = $_digest; // Revert

		return ($retvar === FALSE) ?
			'<p class="alert alert-danger">PluginRenderer::executePluginBlock(): '. Utility::htmlsc('#' . $name . ($args !== '' ? '(' . $args . ')' : '')) .'</p>' :
			self::addHiddenField($retvar, $name);
	}

	/**
	 * インライン型プラグインを実行
	 * @global type $digest
	 * @param type $name
	 * @param type $args
	 * @param type $body
	 * @return type
	 */
	public static function executePluginInline($name, $args='', $body='')
	{
		global $digest, $_string;
		$plugin = self::getPlugin($name);
		$funcname = 'plugin_' . $name . '_inline';
		
		// PukiWikiの仕様上、存在しないメソッドの場合、メッセージを出せない（あとで$line_ruleで変換するため）
		if ($plugin['method']['inline'] === false || !function_exists($funcname))
			return  '&' .Utility::htmlsc($name). ';';

		// プラグインの初期化
		if (self::executePluginInit($name) === false) {
			return '<span class="text-warning">PluginRenderer::executePluginInline(): ' . sprintf($_string['plugin_init_error'], Utility::htmlsc('&'.$name).'();') . '</span>';
		}

		// プラグインのパラメータを取得。（&plugin(){}の()内の部分をカンマ区切りで分割）
		$aryargs = empty($args) ? array() : explode(',', $args);

		// プラグインの末尾のパラメータを取得（#plugin(){}の{}内の部分）常に末尾の配列が入る。
		$aryargs[] = $body; // func_num_args() != 0

		// digestを知事保存し、ロケールファイルを差し替えて翻訳
		$_digest = $digest;
		T_textdomain($name);
		// プラグインを実行
		$retvar  = call_user_func_array($funcname, $aryargs);
		// ロケールとdigestをもとに戻す
		T_textdomain(DOMAIN);
		$digest  = $_digest; // Revert

		return ($retvar === FALSE) ?
			// プラグインとメソッドが存在するのに出力がない場合はエラー
			'<span class="text-danger">PluginRenderer::executePluginInline(): '. Utility::htmlsc('&' . $name . ($args !== '' ? '(' . $args . ')' : '')) .'</span>' :
			self::addHiddenField($retvar, $name);
	}
	/**
	 * formタグに追加のフォームを挿入
	 * @param type $retvar
	 * @param type $plugin
	 * @return type 
	 */
	private static function addHiddenField($retvar, $plugin){
		global $use_spam_check, $vars;
		// TODO:複数回実行される問題あり
		if (preg_match('/<form\b(?:(?=(\s+(?:method="([^"]*)"|enctype="([^"]*)")|action="([^"]*)"|data-collision-check="([^"]*)"|data-collision-check-strict="([^"]*)"|[^\s>]+|\s+))\1)*>/i', $retvar, $matches) !== 0){
			// action属性が、このスクリプト以外を指している場合処理しない
			if ($matches[4] === Router::get_script_uri()){
				// Insert a hidden field, supports idenrtifying text enconding
				$hidden_field[] = '<!-- Additional fields START-->';
				$hidden_field[] = PKWK_ENCODING_HINT ? '<input type="hidden" name="encode_hint" value="' . PKWK_ENCODING_HINT . '" />' : null;

				if ($matches[2] !== 'get'){
					// 利用者のホストチェック
					$hidden_field[] = '<input type="hidden" name="ticket" value="' . md5(Utility::getTicket() . REMOTE_ADDR) . '" />';
					// 多重投稿を禁止するオプションが有効かつ、methodがpostだった場合、PostIDを生成する
					if ( (isset($use_spam_check['multiple_post']) && $use_spam_check['multiple_post'] === 1)
						&& preg_match(self::IGNOLE_POSTID_CHECK_PATTERN,$plugin) !== 1){
						// from PukioWikio
						$hidden_field[] = '<input type="hidden" name="postid" value="' . PostId::generate($plugin) . '" />';
					}

					// PHP5.4以降かつ、マルチパートの場合、進捗状況セッション用のフォームを付加する
					if (ini_get('session.upload_progress.enabled') && isset($matches[3]) && $matches[3] === 'multipart/form-data') {
						$hidden_field[] = '<input type="hidden" name="' . ini_get("session.upload_progress.name") . '" value="' . PKWK_WIKI_NAMESPACE . '" class="progress_session" />';
					}

					// ページ名が含まれていて、data-collision-checkがfalseでない場合、競合チェック用フォームを追記する
					// data-collision-check="true"にするのは、pcomment.inc.phpのように別のWikiページを更新するプラグインの場合
					// （これらの自動入力フォームは、常にフォームの先頭に挿入されるので、プラグイン側で重複するフォームがあったところで、
					// HTML文法的に送られるフォームデーターはプラグインで指定された内容が優先されるためわざわざこんな小細工をしなかったところで実害はないが・・・。）
					if (isset($vars['page']) && !(isset($matches[5]) && $matches[5] === 'false')){
						$wiki = Factory::Wiki($vars['page']);
						$hidden_field[] = '<input type="hidden" name="digest" value="' . $wiki->digest() . '" />';
						// 自動競合チェッカー
						// data-collision-check-strict="true"を加えると、ページを送信した時点のオリジナルのソースも送信される。
						// より精度の高い競合チェックを行うことができるが、データーが倍増するので、ページの編集フォーム以外ではあまり使うべきではない。
						if ( (isset($matches[6]) && $matches[6] === 'true') &&isset($vars['page']) && !empty($vars['page'])){
							$hidden_field[] = '<textarea style="display:none;width:0;height:0;" name="original">'. Utility::htmlsc($wiki->get(true)) . '</textarea>';
						}
					}
				}

				$hidden_field[] = '<!-- Additional fields END -->';
				$retvar = preg_replace('/<form[^>]*>/', '$0'. "\n".join("\n",$hidden_field), $retvar);
			}
		}
		return $retvar;
	}
	/**
	 * 進捗状況表示（attachプラグインのpcmd=progressで出力）
	 * @return void
	 */
	public static function getUploadProgress(){
		$headers = Header::getHeaders('application/json');
		$progress = new SessionProgress();
		Header::writeResponse($headers, 200, Json::encode($progress->getProgress(PKWK_WIKI_NAMESPACE)));
		exit;
	}
}

