<?php

namespace PukiWiki\Renderer;

use DirectoryIterator;

class PluginRenderer{
	// Set global variables for plugins
	public static function set_plugin_messages($messages)
	{
		foreach ($messages as $name=>$val)
			if (! isset($GLOBALS[$name]))
				$GLOBALS[$name] = $val;
	}

	// Same as getopt for plugins
	public static function get_plugin_option($args, &$params, $tolower=TRUE, $separator=':')
	{
		if (empty($args)) {
			$params['_done'] = TRUE;
			return TRUE;
		}
		$keys = array_keys($params);

		foreach($args as $val) {
			list($_key, $_val) = array_pad(explode($separator, $val, 2), 2, TRUE);
			if ($tolower === TRUE) $_key = strtolower($_key);
			$_key = trim($_key);
			if (is_string($_val)) $_val = trim($_val);
			if (in_array($_key, $keys) && $params['_done'] !== TRUE) {
				$params[$_key] = $_val;    // Exist keys
			} else if ( !empty($val) ) {
				$params['_args'][] = $val; // Not exist keys, in '_args'
				$params['_done'] = TRUE;
			}
		}
		$params['_done'] = TRUE;
		return TRUE;
	}

	// Check arguments for plugins
	public static function check_plugin_option($val, &$params, $tolower=TRUE)
	{
		if ( !empty($val) ) {
			if ($tolower === TRUE) $_val = strtolower($val);
			foreach (array_keys($params) as $key) {
				if (strpos($key, $_val) === 0) {
					$params[$key] = TRUE;
					return;
				}
			}
		}
		$params['_args'][] = $val;
	}

	

	const PLUGIN_EXISTS_CACHE = 'plugins';

	/**
	 * プラグインおよび、設定一覧キャッシュを生成
	 * @param boolean $force キャッシュ生成を矯正する
	 * @return array
	 */
	private static function getPluginList($force = false){
		global $cache;
		static $plugins;
		//$t1 = microtime();

		if (!$force) {
			// ディレクトリの更新チェック（変更があった場合、キャッシュを再生成）
			foreach(array(PLUGIN_DIR, EXT_PLUGIN_DIR, INIT_DIR, SITE_INIT_DIR) as $p_dir) {
				if (!is_dir($p_dir)) continue;
				$d_modified[] = filemtime($p_dir);
			}
			if ($cache['core']->hasItem(self::PLUGIN_EXISTS_CACHE)){
				$term_cache_meta = $cache['core']->getMetadata(self::PLUGIN_EXISTS_CACHE);
				if ($term_cache_meta['mtime'] < max($d_modified)) {
					$force = true;
				}
			}
		}

		// キャッシュ処理
		if ($force) {
			unset($plugins);
			$cache['core']->removeItem(self::PLUGIN_EXISTS_CACHE);
		}else if (!empty($plugins)) {
			return $plugins;
		}else if ($cache['core']->hasItem(self::PLUGIN_EXISTS_CACHE)) {
			$plugins = $cache['core']->getItem(self::PLUGIN_EXISTS_CACHE);
			$cache['core']->touchItem(self::PLUGIN_EXISTS_CACHE);
			return $plugins;
		}
		
		// プラグインを走査（追加プラグイン内のプラグインがオーバーライドされる）
		foreach(array(PLUGIN_DIR, EXT_PLUGIN_DIR) as $p_dir) {
			if (!is_dir($p_dir)) continue;
			foreach (new DirectoryIterator($p_dir) as $fileinfo) {
				// 読み込み可能ならエイリアスでも読み取ります。
				if (strpos($fileinfo->getBasename(), '.inc.php') !== false && $fileinfo->isReadable()){
					$plugins[$fileinfo->getBasename('.inc.php')] = array(
						// 読み込み済みフラグ
						'loaded' => false,
						// パス
						'path' => $fileinfo->getPathname(),
		//				'lang' => (PLUGIN_DIR == $p_dir) ? LANG_DIR : EXT_LANG_DIR 
					);
				}
			}
		}
		unset($p_dir, $fileinfo);
		// プラグイン設定ファイルを走査（サイト別の設定がオーバーライドされる）
		foreach(array(INIT_DIR, SITE_INIT_DIR) as $p_dir) {
			if (!is_dir($p_dir)) continue;
			foreach (new DirectoryIterator($p_dir) as $fileinfo) {
				if (strpos($fileinfo->getBasename(), '.ini.php') !== false && $fileinfo->isReadable()){
					// 設定ファイル
					$plugins[$fileinfo->getBasename('.ini.php')]['conf'] = $fileinfo->getPathname();
				}
			}
		}
		unset($p_dir, $fileinfo);
		// キャッシュを保存
		$cache['core']->setItem(self::PLUGIN_EXISTS_CACHE, $plugins);
		return $plugins;
	}
	/**
	 * プラグイン一覧からプラグイン情報を取得
	 * @param string $name プラグイン名
	 * @return array
	 */
	private static function getPlugin($name){
		static $plugins;
		global $exclude_plugin;

		$name = strtolower($name);

		// プラグイン名が長すぎる
		if (!preg_match('/^\w{1,64}$/', $name)) Utility::dieMessage('Plugin name is too long! (less than 64 chars)');

		// 無効化しているプラグインの場合
		if (in_array($name, $exclude_plugin)) return FALSE;

		// プラグイン一覧を取得
		if (!isset($plugins)) $plugins = self::getPluginList();

		// プラグイン読み取り
		if (!isset($plugins[$name])) return false;

		return $plugins[$name];
	}
	/**
	 * プラグインを読み込む
	 * @param string $name プラグイン名
	 * @return boolean
	 */
	public static function loadPlugin($name)
	{
		global $exclude_plugin, $plugin_lang_path, $cache;
		static $plugins;

		$plugin = self::getPlugin($name);

		if ($plugin['loaded'] == false){
			// プラグインを読み込む
			require $plugin['path'];
			// 設定を読み込む
			if (isset($plugin['conf'])) require $plugin['conf'];
			// 読み込み可能フラグ
			$plugin['loaded'] = true;
		}
		return true;
	}
	/**
	 * プラグインの呼び出し回数を制限する
	 * @param string $name プラグイン名
	 * @return boolean
	 */
	private static function limitPlugin($name)
	{
		static $count;

		$name = strtolower($name);
		$count[$name] = (!isset($count[$name])) ? 1 : $count[$name]++;

		if ($count[$name] > self::PLUGIN_CALL_TIME_LIMIT) {
			Utility::dieMessage( sprintf($_string['plugin_multiple_call'],  htmlsc($name), PKWK_PLUGIN_CALL_TIME_LIMIT));
		}
		return TRUE;
	}

	// Check if plguin API exists
	public static function exist_plugin_function($name, $method)
	{
		$func = 'plugin_'.$name.'_'.$method;
		if (function_exists($func)) {
			return limit_plugin($name);
		} elseif (self::loadPlugin($name) && function_exists($func)) {
			return limit_plugin($name);
		}
		return true;
	}

	// Call 'init' function for the plugin
	// NOTE: Returning FALSE means "An erorr occurerd"
	public static function do_plugin_init($name)
	{
		global $plugin_lang_path;
		static $done, $checked;

		if (empty($plugin_lang_path[$name])) {
			// bindtextdomain($name, LANG_DIR);
			T_bindtextdomain($name,LANG_DIR);
		} else {
			// bindtextdomain($name, $plugin_lang_path[$name]);
			T_bindtextdomain($name,$plugin_lang_path[$name]);
		}

		$func = 'plugin_' . $name . '_init';
		if (function_exists($func)) {
			// TRUE or FALSE or NULL (return nothing)
			T_textdomain($name);
			$done[$name] = call_user_func($func);
			T_textdomain(DOMAIN);
			if (!isset($checked[$name])) {
				$done[$name] = TRUE; // checked.
			}
		} else {
			$done[$name] = TRUE; // checked.
		}
		return $done[$name];
	}

	// Call API 'action' of the plugin
	public static function do_plugin_action($name)
	{
		global $vars, $_string, $use_spam_check, $post;
		if (! exist_plugin_action($name)) return array();

		if (do_plugin_init($name) === FALSE) {
			die_message(sprintf( $_string['plugin_init_error'], htmlsc($name) ));
		}

		$func = 'plugin_' . $name . '_action';
		if (!function_exists($func))
			die_message(sprintf($_string['plugin_not_implemented'],htmlsc($name)),501);

		// Check encode
		if (isset($vars['encode_hint']) && !empty($vars['encode_hint']) && (PKWK_ENCODING_HINT !== $vars['encode_hint']) ) {
			die_message($_string['plugin_encode_error']);
		}

	//	if ( isset($post['ticket']) && $post['ticket'] !== md5(get_ticket() . REMOTE_ADDR) ){
	//		die_message('host is mismatch!');
	//	}

		// check postid
		if ( (isset($use_spam_check['multiple_post']) && $use_spam_check['multiple_post'] === 1) && (isset($post['postid']) && !check_postid($post['postid'])) )
			die_message($_string['plugin_postid_error']);

		T_textdomain($name);
		$retvar = call_user_func($func);
		T_textdomain(DOMAIN);

		$retvar['body'] = isset($retvar['body']) ? add_hidden_field($retvar['body'], $name) : '';

		return $retvar;
	}

	// Call API 'convert' of the plugin
	public static function do_plugin_convert($name, $args = '')
	{
		global $digest, $_string;

		if (self::do_plugin_init($name) === FALSE) {
			return '<div class="ui-state-error ui-corner-all">' . sprintf($_string['plugin_init_error'], htmlsc($name)) . '</div>';
		}

		$func = 'plugin_' . $name . '_convert';
		if (!function_exists($func))
			return '<div class="message_box ui-state-error ui-corner-all">'.sprintf($_string['plugin_not_implemented'],'#'.Utility::htmlsc($name).'()').'</div>';

		// Multiline plugin?
		$pos  = strpos($args, "\r"); // "\r" is just a delimiter
		if ($pos !== FALSE) {
			$body = substr($args, $pos + 1);
			$args = substr($args, 0, $pos);
		}

		$aryargs = empty($args) ? array() : explode(',', $args);

		if (isset($body)) $aryargs[] = & $body;     // #plugin(){{body}}

		$_digest = $digest;
		T_textdomain($name);
		$retvar  = call_user_func_array($func, $aryargs);
		T_textdomain(DOMAIN);
		$digest  = $_digest; // Revert

		return ($retvar === FALSE) ?
			'<div class="message_box ui-state-error ui-corner-all">'. Utility::htmlsc('#' . $name . ($args !== '' ? '(' . $args . ')' : '')) .'</div>' :
			self::add_hidden_field($retvar, $name);
	}

	// Call API 'inline' of the plugin
	public static function do_plugin_inline($name, $args='', $body='')
	{
		global $digest, $_string;

		$func = 'plugin_' . $name . '_inline';
		if (!function_exists($func))
			return '&' . htmlsc($name). ';';

		
		if (do_plugin_init($name) === FALSE) {
			return '<span class="ui-state-error">' . sprintf($_string['plugin_init_error'], '&'.htmlsc($name).'();') . '</span>';
		}

		$aryargs = empty($args) ? array() : explode(',', $args);

		// NOTE: A reference of $body is always the last argument
		$aryargs[] = & $body; // func_num_args() != 0

		$_digest = $digest;
		T_textdomain($name);
		$retvar  = call_user_func_array($func, $aryargs);
		T_textdomain(DOMAIN);
		$digest  = $_digest; // Revert

		if($retvar === FALSE) {
			// Do nothing
			return htmlsc('&' . $name . ($args ? '(' . $args . ')' : '') . ';');
		} else {
			return add_hidden_field($retvar, $name);
		}
	}

	// Used Plugin?
	public static function use_plugin($plugin, $lines)
	{
		if (!is_array($lines)) {
			$delim = array("\r\n", "\r");
			$lines = str_replace($delim, "\n", $lines);
			$lines = explode("\n", $lines);
		}

		foreach ($lines as $line) {
			if (substr($line, 0, 2) == '//') continue;
			// Diff data
			if (substr($line, 0, 1) == '+' || substr($line, 0, 1) == '-') {
				$line = substr($line, 1);
			}
			if (preg_match('/^[#|&]' . $plugin . '[^a-zA-Z]*$/', $line, $matches)) {
				return $matches[0];
			}
		}
		return FALSE;
	}

	// formタグに追加のフォームを挿入
	public static function add_hidden_field($retvar, $plugin){
		global $use_spam_check, $vars, $session, $digest;
		if (preg_match('/<form\b(?:(?=(\s+(?:method="([^"]*)"|enctype="([^"]*)")|[^\s>]+|\s+))\1)*>/i', $retvar, $matches) !== 0){
			// Insert a hidden field, supports idenrtifying text enconding
			$hidden_field[] = '<!-- Additional fields START-->';
			$hidden_field[] = ( PKWK_ENCODING_HINT ) ? '<input type="hidden" name="encode_hint" value="' . PKWK_ENCODING_HINT . '" />' : '';

			if ($matches[2] !== 'get'){
				// 利用者のホストチェック
				$hidden_field[] = '<input type="hidden" name="ticket" value="' . md5(get_ticket() . REMOTE_ADDR) . '" />';
				// 多重投稿を禁止するオプションが有効かつ、methodがpostだった場合、PostIDを生成する
				if ( (isset($use_spam_check['multiple_post']) && $use_spam_check['multiple_post'] === 1)
					&& preg_match(PKWK_IGNOLE_POSTID_CHECK_PLUGINS,$plugin) !== 1){
					// from PukioWikio
					$hidden_field[] = '<input type="hidden" name="postid" value="'.generate_postid($plugin).'" />';
				}

				// PHP5.4以降かつ、マルチパートの場合、進捗状況セッション用のフォームを付加する
				if (ini_get('session.upload_progress.enabled') && isset($matches[3]) && $matches[3] === 'multipart/form-data') {
					$hidden_field[] = '<input type="hidden" name="' . ini_get("session.upload_progress.name") . '" value="' . PKWK_WIKI_NAMESPACE . '" class="progress_session" />';
				}

				// 更新時の競合を確認するための項目（確認処理はプラグイン側で実装すること）
				if (isset($vars['page']) && !empty($vars['page'])){
					if (empty($digest)){
						$digest = PukiWiki\Factory::Wiki($vars['page'])->digest();
					}
					$hidden_field[] = '<input type="hidden" name="digest" value="' . $digest . '" />';
				}
			}

			$hidden_field[] = '<!-- Additional fields END -->';
			$retvar = preg_replace('/<form[^>]*>/', '$0'. "\n".join("\n",$hidden_field), $retvar);
		}
		return $retvar;
	}

	// FIXME:進捗状況表示（attachプラグインのpcmd=progressで出力）
	function get_upload_progress(){
		global $vars;
		$key = ini_get('session.upload_progress.prefix'). PKWK_WIKI_NAMESPACE;
		header('Content-Type: application/json; charset='.CONTENT_CHARSET);
		echo Zend\Json\Json::encode( isset($_SESSION[$key]) ? $_SESSION[$key] : null );

		exit;
	}
}