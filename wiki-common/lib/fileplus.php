<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: fileplus.php,v 1.2.9 2012/04/29 10:08:00 Logue Exp $
// Copyright (C)
//   2010-2012 PukiWiki Advance Team
//   2005-2006,2009 PukiWiki Plus! Team
// License: GPL v2 or (at your option) any later version
//
// File related functions - extra functions

// Ticket file
defined('PKWK_TICKET_CACHE')	or define('PKWK_TICKET_CACHE', 'ticket');

// Get Ticket
function get_ticket($flush = FALSE)
{
	global $cache;

	if ($flush) $cache['wiki']->removeItem(PKWK_TICKET_CACHE);

	if ($cache['wiki']->hasItem(PKWK_TICKET_CACHE)) {
		$ticket = $cache['wiki']->getItem(PKWK_TICKET_CACHE);
	}else{
		$ticket = Zend\Math\Rand::getString(32);
		$cache['wiki']->setItem(PKWK_TICKET_CACHE, $ticket);
	}
	return $ticket;
}

// Get EXIF data
function get_exif_data($file)
{
	if (!extension_loaded('exif')) { return FALSE; }
	if (!function_exists('exif_read_data')) { return FALSE; }
	$exif_rawdata = @exif_read_data($file);
	return $exif_rawdata;
}

function plus_readfile($filename)
{
	if (($fp = fopen($filename,'rb')) === FALSE) return FALSE;
	while (!feof($fp))
	{
		echo fread($fp, 4096);
		flush();
	}
	fclose($fp);
	while (@ob_end_flush());
}

function update_cache($page = '', $force = false){
	global $cache, $aliaspage, $autoalias, $autoglossary, $glossarypage, $autobasealias, $autolink;

	if ($force) {
		// forceフラグがたってる時は、キャッシュをすべて作り直し
		$cache['wiki']->flush();
		$cache['raw']->flush();
	}

	// Update page list
	if (! $cache['wiki']->hasItem(PKWK_EXISTS_PREFIX.'wiki')){
		$pages = get_existpages();
		$cache['wiki']->setItem(PKWK_EXISTS_PREFIX.'wiki', $pages);
	}else{
		$pages = $cache['wiki']->getItem(PKWK_EXISTS_PREFIX.'wiki');
	}

	// Update autolink
	if ( $autolink !== 0 ) {
		PukiWiki\Lib\Renderer\AutoLinkPattern::get_pattern(-1,true);
	}

	// Update rel and ref cache
	$links = new PukiWiki\Lib\Relational($page);
	if (!empty($page) ){
		$links->update($page);
	} else if ($force) {
		$links->init();
	}
/*
	// Update Lastmodifed cache
	put_lastmodified();

	// Update attach list
	get_attachfiles($page);

	// Update AutoAliasName
	// http://pukiwiki.cafelounge.net/plus/?Documents%2FRedirect
	if ($autoalias !== 0 && (! $cache['wiki']->hasItem(PKWK_AUTOALIAS_REGEX_CACHE) || $page === $aliaspage) ) {
		$aliases = get_autoaliases();
		if (empty($aliases) ) {
			// Remove
			$cache['wiki']->removeItem(PKWK_AUTOALIAS_REGEX_CACHE);
		} else {
			// Create or Update
			$cache['wiki']->setItem(PKWK_AUTOALIAS_REGEX_CACHE, get_autolink_pattern(array_keys($aliases)) );
		}
	}

	// Update AutoGlossary
	// http://pukiwiki.cafelounge.net/plus/?Documents%2FGlossary
	if ($autoglossary !== 0 && (! $cache['wiki']->hasItem(PKWK_GLOSSARY_REGEX_CACHE) || $page === $glossarypage) ) {
		$words = get_autoglossaries();
		if (empty($words) ) {
			// Remove
			$cache['wiki']->removeItem(PKWK_GLOSSARY_REGEX_CACHE);
		} else {
			// Create or Update
			$cache['wiki']->setItem(PKWK_GLOSSARY_REGEX_CACHE, get_glossary_pattern(@array_keys($words)) );
		}
	}

	// Update AutoBaseAlias
	// http://pukiwiki.cafelounge.net/plus/?Documents%2FAutoBaseAlias
	if ($autobasealias !== 0 ) {
		$basealiases = get_autobasealias($pages);
		if (empty($basealiase) ) {
			// Remove
			$cache['wiki']->removeItem(PKWK_AUTOBASEALIAS_CACHE);
		} else {
			// Create or Update
			$cache['wiki']->setItem(PKWK_AUTOBASEALIAS_CACHE, $basealiases );
		}
	}
*/
	return true;
}

// Move from file.php

function get_existpages_cache($dir, $ext){
	global $cache;

	switch($dir){
		case DATA_DIR: $func = 'wiki'; break;
		case COUNTER_DIR: $func = 'counter'; break;
		case BACKUP_DIR: $func = 'backup'; break;
		default: $func = encode($dir.$ext);
	}
	// Update page list
	if (! $cache['wiki']->hasItem(PKWK_EXISTS_PREFIX.$func)){
		$pages = get_existpages($dir, $ext);
		$cache['wiki']->setItem(PKWK_EXISTS_PREFIX.$func, $pages);
	}else{
		$pages = $cache['wiki']->getItem(PKWK_EXISTS_PREFIX.$func);
		$cache['wiki']->touchItem(PKWK_EXISTS_PREFIX.$func);
	}
	// Save timestamp
	$cache['wiki']->setItem(PKWK_TIMESTAMP_PREFIX.$func, UTIME);
	return $pages;
}

function get_attachfiles($page = '', $force = false)
{
	global $cache;
	$retval = array();

	if ($force) {
		$cache['wiki']->removeItem(PKWK_EXISTS_PREFIX.'attach');
	}

	if ($cache['wiki']->hasItem(PKWK_EXISTS_PREFIX.'attach')){
		$retval = $cache['wiki']->getItem(PKWK_EXISTS_PREFIX.'attach');
	}else{
		$handle = opendir(UPLOAD_DIR) or die_message('directory ' . UPLOAD_DIR . ' is not exist or not readable.');
		if ($handle) {
			while (false !== ($entry = readdir($handle))) {
				if (($entry !== '.') && ($entry !== '..')) continue;
				$matches = array();

				if (! preg_match("/^((?:[0-9A-F]{2})+)_((?:[0-9A-F]{2})+)$/", $entry, $matches)) continue; // all page

				// [page][file] = array(time,size);
				$filepath = realpath(UPLOAD_DIR.$entry);
				$_page = decode($matches[1]);
				$_file = decode($matches[2]);
				$retval[$_page][$_file] = array(
					'time'=>filemtime($filepath),
					'size'=>filesize($filepath)
				);
			}
			closedir($handle);
		}
		$cache['wiki']->setItem(PKWK_EXISTS_PREFIX.'attach', $retval);
	}
	if ($page && isset($retval[$page])){
		return $retval[$page];
	}
	return $retval;
}

function get_this_time_links($post,$diff)
{
	$links = array();
	$post_links = (array)replace_plugin_link2null($post);
	$diff_links = (array)get_link_list($diff);

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

function replace_plugin_link2null($data)
{
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

function get_link_list($diffdata)
{
	$links = array();

	list($plus, $minus) = get_diff_lines($diffdata);

	// Get URLs from <a>(anchor) tag from convert_html()
	$plus  = convert_html($plus); // WARNING: heavy and may cause side-effect
	preg_match_all('#href="(https?://[^"]+)"#', $plus, $links, PREG_PATTERN_ORDER);
	$links = array_unique($links[1]);

	// Reject from minus list
	if ($minus !== '') {
		$links_m = array();
		$minus = convert_html($minus); // WARNING: heavy and may cause side-effect
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

function get_diff_lines($diffdata)
{
	$_diff = explode("\n", $diffdata);
	$plus  = join("\n", preg_replace('/^\+/', '', preg_grep('/^\+/', $_diff)));
	$minus = join("\n", preg_replace('/^-/',  '', preg_grep('/^-/',  $_diff)));
	unset($_diff);
	return array($plus, $minus);
}

if (!function_exists('hash_hmac')) {
	// source: http://www.php.net/manual/en/function.sha1.php#39492
	// Open Publication License
	function hash_hmac($algo,$data,$key,$raw_output=false)
	{
		$algo = strtolower($algo); // hash_algos()
		switch($algo) {
		case 'sha1':
		case 'md5':
			continue;
		case 'sha256':
			// for PHP4
			// RFC 2104 HMAC implementation for php.
			// Creates a sha256 HMAC.
			// Eliminates the need to install mhash to compute a HMAC
			// Hacked by Lance Rushing
			// modified by Ulrich Mierendorff to work with sha256 and raw output
			require_once( 'sha256.inc.php');
			continue;
		default:
			return false;
		}

		$blocksize = 64;

		if (strlen($key) > $blocksize) {
			$key = pack('H*', $algo($key));
		}

		$key  = str_pad($key, $blocksize, chr(0x00));
		$ipad = str_repeat(chr(0x36), $blocksize);
		$opad = str_repeat(chr(0x5c), $blocksize);
		$hmac = $algo(($key^$opad) . pack('H*', $algo(($key^$ipad).$data)));
		return ($raw_output) ? pack('H*', $hmac) : $hmac;
	}
}

/** Adv. Extended functions ***********************************************************************/
function compress_file($in, $method, $chmod=644){
	// ファイルの存在確認
	if (!file_exists ($filename) || !is_readable ($filename)) return false;
	// 出力ファイル名
	$out = $file.'.'.$method;
	if ((!file_exists ($out) && !is_writeable (dirname ($out)) || (file_exists($out) && !is_writable($out)) )) return false;
	// テンポラリファイル名
	$tmp_name = $file.'.tmp';

	switch ($method){
		case 'gz' :
			if (extension_loaded('zlib')) {
				$in_file = fopen($in, "r");
				$out_file = gzopen ($out, "w9");	// 最高圧縮
				while (!feof ($in_file)) {
					$buffer= fread($in_file, 2048);
					gzwrite ($out_file, $buffer);
				}
				fclose ($in_file);
				gzclose ($out_file);
				chmod($out_file, $chmod);
				break;
			}
		case 'bz2' :
			if (extension_loaded('bzip2')) {
				$in_file = fopen ($in, "rb");
				$out_file = bzopen ($out, "wb");
				while (!feof ($in_file)) {
					$buffer = fgets ($in_file, 4096);
					bzwrite ($out_file, $buffer, 4096);
				}
				fclose ($in_file);
				bzclose ($out_file);
				chmod($out_file, $chmod);
				break;
			}
		case 'lzf' :
			if (extension_loaded('lzf')) {
				lzf_compress($filename);
				chmod($filename, $chmod);
			}
		case 'zip' :
			if (class_exists('ZipArchive')) {
				$zip = new ZipArchive();

				$zip->addFile($tmp_name,$filename);
				// if ($zip->status !== ZIPARCHIVE::ER_OK)
				if ($zip->status !== 0)die_message( $zip->status);
				$zip->close();
				chmod($filename, $chmod);
				@unlink($tmp_name);
				break;
			}
		case 'tar' :
		default :
			$tar = new tarlib();
			$tar->create(CACHE_DIR, 'tar') or die_message( 'Temporaly file failure.' );
			$tar->add_file($tmp_name, $filename);
			$tar->close();

			@rename($tar->filename, $filename);
			chmod($filename, $chmod);
			@unlink($tar->filename);
		break;
	}
}

/**
 * CAPTCHA認証
 *
 * @access public
 * @param $save セッションにCAPTCHA認証済みかを保存するかのフラグ
 * @param $message メッセージを表示したい場合
 * @return string 認証済みだった場合そのままreturn。そうでなかった場合フォームを表示。
 */

// CAPTCHAセッションの接頭辞（セッション名は、ticketに閲覧者のリモートホストを加えたもののmd5値とする）
defined('PKWK_CAPTCHA_SESSION_PREFIX') or define('PKWK_CAPTCHA_SESSION_PREFIX','captcha-');

// CAPTCHA認証済みセッションの有効期間
defined('PKWK_CAPTCHA_SESSION_EXPIRE') or define('PKWK_CAPTCHA_SESSION_EXPIRE','3600');	// 1時間

// CAPTCHA画像のフォント（GDを使用する場合）
defined('PKWK_CAPTCHA_IMAGE_FONT') or define('PKWK_CAPTCHA_IMAGE_FONT', LIB_DIR.'fonts/Vera.ttf');

// CAPTCHA画像の一時保存先（GDを使用する場合）
defined('PKWK_CAPTCHA_IMAGE_DIR_NAME') or define('PKWK_CAPTCHA_IMAGE_DIR_NAME', 'captcha/');

// CAPTCHA認証の有効期間
defined('PKWK_CAPTCHA_TIMEOUT') or define('PKWK_CAPTCHA_TIMEOUT', 120);	// 2分間

// CAPTCHA認証の入力文字数
defined('PKWK_CAPTCHA_WORD_LENGTH') or define('PKWK_CAPTCHA_WORD_LENGTH', 6);

function captcha_check($save = true, $message = ''){
	global $recaptcha_public_key, $recaptcha_private_key, $vars, $session;

	// Captchaのセッション名（ticketとリモートホストの加算値。ticketはプログラマーから見てもわからない）
	$session_name = PKWK_CAPTCHA_SESSION_PREFIX.md5(get_ticket() . REMOTE_ADDR);

	if ($save && $session->offsetExists($session_name) && $session->offsetGet($session_name) === true){
		// CAPTCHA認証済みの場合
		// return array('msg'=>'CAPTCHA','body'=>'Your host was already to challenged.');
		return;
	}
	if (isset($recaptcha_public_key) && isset($recaptcha_private_key) ){
		// reCaptchaを使う場合
		$captcha = new ZendService\ReCaptcha\ReCaptcha($recaptcha_public_key, $recaptcha_private_key);
		// 入力があった場合
		if ( isset($vars['recaptcha_challenge_field']) && isset($vars['recaptcha_response_field']) ){
			if ($captcha->verify($vars['recaptcha_challenge_field'], $vars['recaptcha_response_field']) ) {
				if ($save){
					// captcha認証済セッションを保存
					$session->offsetSet($session_name, true);
					// captcha認証済セッションの有効期間を設定
					$session->setExpirationSeconds($session_name, PKWK_CAPTCHA_SESSION_EXPIRE);
				}
				// return array('msg'=>'CAPTCHA','body'=>'OK!');
				return;	// ここで書き込み処理に戻る
			}else{
				// CAPTCHA認証失敗ログをつける
				write_challenged();
				$message = 'Failed to authenticate.';
			}
		}
		// 念のためcaptcha認証済みセッションを削除
		$session->offsetUnset($session_name);
		// reCaptchaの設定をオーバーライド
		$captcha->setOption('lang',substr(LANG,0,2));
		$captcha->setOption('theme','clean');
		$form = $captcha->getHTML();
	}else{
		// reCaptchaを使わない場合
		if (isset($vars['challenge_field']) && isset($vars['response_field'] )){
			// Captchaチェック処理
			if ($session->offsetGet(PKWK_CAPTCHA_SESSION_PREFIX.$vars['response_field']) === strtolower($vars['challenge_field'])) {
				if ($save){
					// captcha認証済セッションを保存
					$session->offsetSet($session_name, true);
					// captcha認証済セッションの有効期間を設定
					$session->setExpirationSeconds($session_name, PKWK_CAPTCHA_SESSION_EXPIRE);
				}
				// 認証用セッションの削除
				$session->offsetUnset(PKWK_CAPTCHA_SESSION_PREFIX.$vars['response_field']);
				// キャッシュ画像を削除
				if (file_exists(PKWK_CAPTCHA_IMAGE_CACHE_DIR.$vars['response_field'].'.png')) unlink(PKWK_CAPTCHA_IMAGE_CACHE_DIR.$vars['response_field'].'.png');

				// return array('msg'=>'CAPTCHA','body'=>'OK!');
				return;	// ここで書き込み処理に戻る
			}else{
				// CAPTCHA認証失敗ログをつける
				write_challenged();
				$message = 'Failed to authenticate.';
			}
		}
		// 念のためcaptcha認証済みセッションを削除
		$session->offsetUnset($session_name);
		if (extension_loaded('gd')) {
			// GDが使える場合、画像認証にする
			mkdir_r(CACHE_DIR . PKWK_CAPTCHA_IMAGE_DIR_NAME);
			// 古い画像を削除する
			$handle = opendir(CACHE_DIR . PKWK_CAPTCHA_IMAGE_DIR_NAME);
			if ($handle) {
				while( $entry = readdir($handle) ){
					if( $entry !== '.' && $entry !== '..'){
						$f = realpath(CACHE_DIR . PKWK_CAPTCHA_IMAGE_DIR_NAME . $entry);
						if (time() - filectime($f) > PKWK_CAPTCHA_TIMEOUT) unlink($f);
					}
				}
				closedir($handle);
			}
			$captcha = new Zend\Captcha\Image(array(
				'wordLen' => PKWK_CAPTCHA_WORD_LENGTH,
				'timeout' => PKWK_CAPTCHA_TIMEOUT,
				'font'	=> PKWK_CAPTCHA_IMAGE_FONT,
				'ImgDir' => PKWK_CAPTCHA_IMAGE_CACHE_DIR
			));
			$captcha->generate();
			// cache_refプラグインを用いて画像を表示
			$form = '<img src="'. get_cmd_uri('cache_ref', null,null,array('src'=>PKWK_CAPTCHA_IMAGE_DIR_NAME.$captcha->getId().'.png')) . '" height="'.$captcha->getHeight().'" width="'.$captcha->getWidth().'" alt="'.$captcha->getImgAlt().'" /><br />'."\n";	// 画像を取得
		}else{
			// GDがない場合アスキーアート
			$captcha = new Zend\Captcha\Figlet(array(
				'wordLen' => PKWK_CAPTCHA_WORD_LENGTH,
				'timeout' => PKWK_CAPTCHA_TIMEOUT,
			));
			$captcha->generate();
			// ＼が￥に見えるのでフォントを明示的に指定。
			$form = '<pre style="font-family: Monaco, Menlo, Consolas, \'Courier New\' !important;">'.$captcha->getFiglet()->render($captcha->getWord()).'</pre>'."\n". '<br />'."\n";	// AAを取得
		}
		// 識別子のセッション名
		$response_session = PKWK_CAPTCHA_SESSION_PREFIX.$captcha->getId();
		// 識別子のセッションを発行
		$session->offsetSet($response_session, $captcha->getWord());
		// captchaの有効期間
		$session->setExpirationSeconds($response_session, PKWK_CAPTCHA_TIMEOUT);
		$form .= '<input type="hidden" name="response_field" value="'.$captcha->getId().'" />'."\n";
		$form .= '<input type="text" name="challenge_field" maxlength="'.$captcha->getWordLen().'" size="'.$captcha->getWordLen().'" />';
		// $form .= $captcha->getWord();
	}
//	$ret[] = $session->offsetExists($session_name) ? 'true' : 'false';
//	$ret[] = Zend\Debug\Debug::Dump($vars);
//	$ret[] = Zend\Debug\Debug::Dump($captcha->getSession());


	if (!empty($message)){
		$ret[] = '<div class="message_box ui-state-error ui-corner-all"><p><span class="ui-icon ui-icon-alert"></span>'.$message.'</p></div>';
	}

	// PostIdが有効な場合
	if ( isset($use_spam_check['multiple_post']) && $use_spam_check['multiple_post'] === 1){
		$vars['postid'] = generate_postid($vars['cmd']);
	}

	$ret[] = '<fieldset>';
	$ret[] = '<legend>CAPTCHA</legend>';
	$ret[] = '<p>'.T_('Please enter the text that appears below.').'</p>';
	// フォームを出力
	$ret[] = '<form method="post" action="'.get_script_uri().'" method="post">';
	// ストアされている値を出力
	foreach ($vars as $key=>$value){
		$ret[] = !empty($value) ? '<input type="hidden" name="' . $key . '" value="' . htmlsc($value) . '" />' : null;
	}
	// CAPTCHAフォームを出力
	$ret[] = $form;
	$ret[] = '<input type="submit" />';
	$ret[] = '</form>';
	$ret[] = '</fieldset>';

	// return array('msg'=>'CAPTCHA','body'=>join("\n",$ret));
	catbody('CAPTCHA', $vars['page'], join("\n",$ret));
	exit;
}

function write_challenged(){
	error_log(get_remoteip() . "\t" . UTIME . "\t" . $_SERVER['HTTP_USER_AGENT'] . "\n", 3, CACHE_DIR . 'challenged.log');
}

/**
 * POST data check function $Id$
 *
 * PukioWikio - A WikiWikiWeb clone.
 *  A custom version of PukiWiki.
 *
 * @copyright  &copy; 2008 PukioWikio Developers Team
 * @license GPL v2 or (at your option) any later version
 */

// POSTIDの有効期間
defined('PKWK_POSTID_SESSION_EXPIRE') or define('PKWK_POSTID_SESSION_EXPIRE', 3600);	// 60*60 = 1hour

// POSTIDの接頭辞
defined('PKWK_POSTID_SESSION_PREFIX') or define('PKWK_POSTID_SESSION_PREFIX', 'postid-');

/**
 * generate id from $cmd and random number
 */

function generate_postid($cmd = '')
{
	global $session;
	$idstring = md5($cmd . mt_rand());
	// PostIDの値の中身は、ホストを入力
	$session->offsetSet(PKWKN_POSTID_SESSIO_PREFIX.$idstring, REMOTE_ADDR);
	// 有効期限を設定
	$session->setExpirationSeconds(PKWK_POSTID_SESSION_EXPIRE, PKWK_POSTID_SESSION_PREFIX.$idstring);
	return $idstring;
}

function check_postid($idstring)
{
	global $session;
	$ret = FALSE;
	if ($session->offsetExists(PKWK_POSTID_SESSION_PREFIX.$idstring) && $session->offsetGet(PKWK_POSTID_SESSION_PREFIX.$idstring) === REMOTE_ADDR){
		$ret = TRUE;
		// PostIdを削除
		$session->offsetUnset(PKWK_POSTID_SESSION_PREFIX.$idstring);
	}else{
	//	honeypot_write();
	}
	return $ret;
}

/* End of file fileplus.php */
/* Location: ./wiki-common/lib/fileplus.php */
