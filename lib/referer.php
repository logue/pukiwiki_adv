<?php
// PukiWiki Plus! - Yet another WikiWikiWeb clone.
// $Id: referer.php,v 1.8.5 2010/10/25 19:43:00 Logue Exp $
// Copyright (C)
//   2010      PukiWiki Advance Developers Team
//   2006-2008 PukiWiki Plus! Team
//   2003      Originally written by upk
// License: GPL v2 or (at your option) any later version
//
// Referer function

define('REFERER_SPAM_LIST', CACHE_DIR.'referer_spam.log');

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
		$data = csv_explode(',', $data);
		$result[rawurldecode($data[$uniquekey])] = $data;
	}
	@flock($fp, LOCK_UN);
	fclose ($fp);

	return $result;
}

function ref_save($page)
{
	global $referer, $use_spam_check;

	$url = $_SERVER['HTTP_REFERER'];

	// if (PKWK_READONLY || ! $referer || empty($_SERVER['HTTP_REFERER'])) return TRUE;
	// if (auth::check_role('readonly') || ! $referer || empty($_SERVER['HTTP_REFERER'])) return TRUE;
	if (! $referer || empty($url)) return TRUE;

	// Validate URI (Ignore own)
	$parse_url = parse_url($url);
	if ($parse_url === FALSE || !isset($parse_url['host']) || $parse_url['host'] == $_SERVER['HTTP_HOST'])
		return TRUE;
		
	// Blocking SPAM

	if ($use_spam_check['referer'] && SpamCheck($parse_url['host'])) return TRUE;
	if (is_refspam($parse_url) === true) return TRUE;

	if (! is_dir(REFERER_DIR))      die_message('No such directory: REFERER_DIR');
	if (! is_writable(REFERER_DIR)) die_message('Permission denied to write: REFERER_DIR');

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
			1      // [4]: Enable / Disable flag (1 = enable)
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
		$str = trim(join(',', $line));
		if ($str != '') fwrite($fp, $str . "\n");
	}
	@flock($fp, LOCK_UN);
	fclose($fp);

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

define('CONFIG_REFERER_BL', 'plugin/referer/BlackList');
define('CONFIG_REFERER_WL', 'plugin/referer/WhiteList');

// Referer元spamかのチェック
function is_refspam($url){
	$is_refspam = false;
	// URLをパース
	$parse_url = parse_url($url);
	$condition = $parse_url['host'].$url['path'];	// QueryStringは評価しない。

	// ホワイトリストに入っている場合はチェックしない
	$WhiteList = new Config(CONFIG_REFERER_WL);
	$WhiteList->read();
	$WhiteListLines = $WhiteList->get('WhiteList');
	foreach ($WhiteListLines as $WhiteListLine){
		if(strpos($WhiteListLine[0], $condition) ){
			return false;
		}
	}
	unset($WhiteList,$WhiteListLines,$WhiteListLine);

	// ブラックリストを確認
	$BlackList = new Config(CONFIG_REFERER_BL);
	$BlackList->read();
	$BlackListLines = $BlackList->get('BlackList');

	$ret = array();
	/* |~referer|~count|h */
	foreach ($BlackListLines as $BlackListLine){
		if(strpos($BlackListLine[0], $condition)){
			// マッチした場合
			$count = $BlackListLine[1]+1;
			$BlackList->put('BlackList', array($BlackListLine[0],$count));
			$is_refspam = true;
			break;
		}else if (is_valid_referer($url) === false){
			// マッチしなかった場合
			// リファラーにサイトへのアドレスが存在するかを確認
			$BlackList->add('BlackList', array($condition,1));
			$is_refspam = true;
			break;
		}
	}
	// ブラックリストを更新
	$BlackList->write();
	unset($BlackList,$BlackListLines,$BlackListLine,$ret,$count);

	return $is_refspam;
}

// リンク元にアクセスして自サイトへのアドレスが存在するかのチェック
function is_valid_referer($url){
	// 本来は正規化されたアドレスでチェックするべきだろうが、
	// めんどうだからスクリプトのアドレスを含むかでチェック
	// global $vars;
	// $script = get_page_absuri(isset($vars['page']) ? $vars['page'] : '');

	$script = get_script_uri();
/*
	$error_count = 0;
	do{
		$http = new HTTP_Request($url);
		$http->addHeader("User-Agent", 'Mozilla/5.0 (compatible; '.GENERATOR.')');
//		$http->addHeader("Referer", $script);	// Refererは吐かない方がいいかな？
		$http->addheader('timeout',30);
		$http->sendRequest();
		
		if (PEAR::isError($http->sendRequest())) {
			return true;
		}else{
			// FIXME
			switch ($http->getResponseCode()){
				case 200 :
					$ret = $http->getResponseBody();
				break;
				case 301 :	// Moved Permanently
				case 302 :	// Moved Temporarily
				case 307 :	// Moved Temporarily(HTTP1.1)
				case 403 :	// Forbidden
				case 404 :	// Not Found
				case 401 :	// Unauthorized
					return true;	// そもそもページじゃねぇ。spamとする
				break;
				default:
					$error_count++;
					sleep(10);	// 10秒間待機
				break;
			}
		}
	}while($error_count < 2);

	$html = str_get_html($ret);
	unset($error_count,$ret);
*/
	// useragent setting
	$header = "User-Agent: Mozilla/5.0 (Nintendo Famicom; U; Family Basic 2.0A; ja-JP) AppleWebKit/525.19 (KHTML, like Gecko) Version/3.1.2 Safari/525.21\r\n";
	$header .= "Referer: ".$script;
	$header_options = array("http"=> array("method" => "GET", "header" => $header));
	$header_context = stream_context_create($header_options);
	$html = file_get_html($url, FALSE, $header_context);;
	foreach($html->find('a') as $element){	// aタグを走査
		if (strpos($script,$element->href)){	// aタグに自分のサイトのアドレスが含まれていた場合false（ただし、http://から判定する）
			return true;
			break;
		}
	}
	return false;
}
?>
