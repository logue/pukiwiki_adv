<?php
// PukiWiki Plus! - Yet another WikiWikiWeb clone.
// $Id: referer.php,v 1.8.8 2011/03/17 19:46:00 Logue Exp $
// Copyright (C)
//   2010-2012 PukiWiki Advance Developers Team
//   2006-2008 PukiWiki Plus! Team
//   2003      Originally written by upk
// License: GPL v2 or (at your option) any later version
//
// Referer function

define('REFERER_SPAM_LOG', CACHE_DIR.'referer_spam.log');
define('REFFRER_BAN_COUNT',	3);	// バン対象にするリファラーの回数

function ref_get_data($page, $uniquekey=1)
{
	$file = ref_get_filename($page);

	if (! file_exists($file)) return array();

	$result = array();
	$fp = @fopen($file, 'r');
	set_file_buffer($fp, 0);
	@flock($fp, LOCK_EX);
	rewind($fp);
	while ($data = @fgets($fp, 8192)) {
		$data = explode("\t", $data);
		$result[rawurldecode($data[$uniquekey])] = $data;
	}
	@flock($fp, LOCK_UN);
	fclose ($fp);

	return $result;
}

function ref_save($page)
{
	global $referer, $use_spam_check;

	if (! is_dir(REFERER_DIR))      die_message('No such directory: REFERER_DIR');
	if (! is_writable(REFERER_DIR)) die_message('Permission denied to write: REFERER_DIR');
	// if (PKWK_READONLY || ! $referer || empty($_SERVER['HTTP_REFERER'])) return TRUE;
	// if (auth::check_role('readonly') || ! $referer || empty($_SERVER['HTTP_REFERER'])) return TRUE;

	$url = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : null;
	if (! $referer || empty($url) ) return TRUE;

	if ($use_spam_check['bad-behavior']){
		require(LIB_DIR . 'bad-behavior-pukiwiki.php');
	}

	// Validate URI (Ignore own)
	$parse_url = parse_url($url);
	if ($parse_url === FALSE || !isset($parse_url['host']) || $parse_url['host'] == $_SERVER['HTTP_HOST'])
		return TRUE;
/*
	if ( stristr($parse_url['host'], 'google') !== FALSE && ($parse_url['path'] === '/url' && $parse_url['path'] === '/search') ){
		// Googleのリファラーがログ容量を浪費するため。
		parse_str($parse_url['query'], $q);
		$url = $parse_url['scheme'] . '://'. $parse_url['host'] . ( ($q['q'] !== '') ? '/search?q='.$q['q'] : '');
	}else{
		// Blocking SPAM
		if ($use_spam_check['referer']){
			if (SpamCheck($parse_url['host'])) return TRUE;
			if (is_refspam($url) === true) return TRUE;
		}
	}
*/
	// Update referer data
	if (preg_match("[,\"\n\r]", $url))
		$url = '"' . str_replace('"', '""', $url) . '"';

	$data  = ref_get_data($page, 3);
	$d_url = rawurldecode($url);
	if (! isset($data[$d_url])) {
		$data[$d_url] = array(
			'',    // [0]: Last update date
			UTIME, // [1]: Creation date
			0,     // [2]: Reference counter
			$url,  // [3]: Referer header
			0      // [4]: Enable / Disable flag (1 = enable)
		);
	}
	$data[$d_url][0] = UTIME;
	$data[$d_url][2]++;

	$filename = ref_get_filename($page);
	pkwk_touch_file($filename);
	$fp = fopen($filename, 'w');
	if ($fp === FALSE) return FALSE;
	set_file_buffer($fp, 0);
	@flock($fp, LOCK_EX);
	rewind($fp);
	foreach ($data as $line) {
		$str = trim(join("\t", $line));
		if ($str != '') fwrite($fp, $str . "\n");
	}
	@flock($fp, LOCK_UN);
	fclose($fp);

	unset($fp,$filename);
	return TRUE;
}

// Get file name of Referer data
function ref_get_filename($page)
{
	return REFERER_DIR . encode($page) . '.ref';
}

// Count the number of TrackBack pings included for the page
function ref_count($page)
{
	$filename = ref_get_filename($page);
	if (!file_exists($filename)) return 0;
	if (!is_readable($filename)) return 0;
	if (!($fp = fopen($filename,'r'))) return 0;
	$i = 0;
	while ($data = @fgets($fp, 4096)) $i++;
	fclose($fp);
	unset($data);
	return $i;
}

use Zend\Http\ClientStatic;
use Zend\Dom\Query;

// リンク元にアクセスして自サイトへのアドレスが存在するかのチェック
function is_not_valid_referer($ref,$rel){
	// 本来は正規化されたアドレスでチェックするべきだろうが、
	// めんどうだからスクリプトのアドレスを含むかでチェック
	// global $vars;
	// $script = get_page_absuri(isset($vars['page']) ? $vars['page'] : '');

	$parse_url = isset($rel) ? parse_url($rel) : get_script_uri();
	$condition = $parse_url['host'].$parse_url['path'];	// QueryStringは評価しない。

	$response = ClientStatic::get($ref);
	if (! $response->isSuccess()){
		return true;
	}
	$dom = new Query($response->getBody());
	$results = $dom->execute('a[href=^"'.$condition.'"]');

	foreach($results as $element){	// hrefがhttpから始まるaタグを走査
		if (preg_match('/'.$condition.'/i', $element->href) !== 0){
			return false;
			break;
		}
	}
	return true;
}


define('CONFIG_REFERER_BL',			'plugin/referer/BlackList');
define('CONFIG_REFERER_WL',			'plugin/referer/WhiteList');

// Referer元spamかのチェック
function is_refspam($url){
	global $open_uri_in_new_window_servername;

	// サイトのルートのアドレス
	$script = get_script_uri();
	// リファラーをパース
	$parse_url = parse_url($url);

	// フラグ
	$is_refspam = true;	// リファラースパムか？
	$hit_bl = false;	// ブラックリストに入っているか？
	$BAN = false;		// バンするか？

	$condition = $parse_url['host'].$parse_url['path'];

	// ドメインは小文字にする。（ドメインの大文字小文字は区別しないのと、strposとstriposで速度に倍ぐらい違いがあるため）
	// 独自ドメインでない場合を考慮してパス（/~hoge/）を評価する。
	// QueryString（?aa=bb）は評価しない。

	// ホワイトリストに入っている場合はチェックしない
	$WhiteList = new Config(CONFIG_REFERER_WL);
	$WhiteList->read();
	$WhiteListLines = $WhiteList->get('WhiteList');
	foreach (array_merge($open_uri_in_new_window_servername, $WhiteListLines) as $WhiteListLine){
//		if (preg_match('/'.$WhiteListLine[0].'/i', $condition) !== 0){
		if (stripos($condition, $WhiteListLine[0]) !== false){
			$is_refspam = false;
			break;
		}
	}


	if ($is_refspam !== false){
		$NewBlackListLine = array();
		// ブラックリストを確認
		$BlackList = new Config(CONFIG_REFERER_BL);
		$BlackList->read();

		$BlackListLines = $BlackList->get('BlackList');
		// |~referer|~count|~ban|h

		foreach ($BlackListLines as $BlackListLine){
//			if (preg_match('/'.$BlackListLine[0].'/i', $condition) !== 0){
			if (stripos($condition, $BlackListLine[0]) !== false){
				// 過去に同じリファラーからアクセスがあった場合
				$BlackListLine[1]++;
				if ($BlackListLine[2] == 1 || $BlackListLine[1] <= CONFIG_REFFRER_BAN_COUNT){
					// バンフラグが立っている場合か、しきい値を超えた場合バン
					$BAN = true;
					// わざと反応を遅らせる
					sleep(2);
				}
				$hit_bl = true;
				$is_refspam = true;
			}
			$NewBlackListLine[] = array($BlackListLine[0],$BlackListLine[1],$BlackListLine[2]);
		}

		// ブラックリストにヒットしなかった場合
		if ($hit_bl === false){
			// リファラーにサイトへのアドレスが存在するかを確認
			$is_refspam = is_not_valid_referer($url);

			if ($is_refspam === true){
				// 存在しない場合はスパムリストに追加
				$NewBlackListLine[] = array($condition,1,0);
			}else{
				// 存在した場合はホワイトリストに追加
//				$WhiteListLines[] = array($condition);
//				$WhiteList->put('WhiteList',$WhiteListLines);
//				$WhiteList->write();
			}
		}
		// ブラックリストを更新
		$BlackList->put('BlackList',$NewBlackListLine);
		$BlackList->write();

		unset($BlackList,$BlackListLines,$BlackListLine,$NewBlackListLine, $hit_bl);
		unset($WhiteList,$WhiteListLines,$WhiteListLine);

		if ($is_refspam === true || $BAN === true){
			// スパムだった場合、ログに環境を保存する。
			$log = array(
				UTIME,
				$url,
				$_SERVER['HTTP_USER_AGENT'],
				$_SERVER['REMOTE_ADDR']
			);
			$filename = REFERER_SPAM_LOG;
			pkwk_touch_file($filename);
			$fp = fopen($filename, 'a');
			@flock($fp, LOCK_EX);
			fwrite($fp, join("\t",$log)."\n" );
			@flock($fp, LOCK_UN);
			fclose($fp);
			die();
		}
	}
	return $is_refspam;
}

/* End of file referer.php */
/* Location: ./wiki-common/lib/referer.php */
