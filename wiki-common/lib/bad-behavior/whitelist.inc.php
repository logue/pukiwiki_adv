<?php if (!defined('BB2_CORE')) die('I said no cheating!');
// PukiWiki Advance - Yet another WikiWikiWeb clone
// $Id: whitelist.inc.php,v 0.1 2011/07/10 14:57:00 Logue Exp $
// 
// Bad-behavior for PukiWiki.
// WhiteList Parser.

defined('CONFIG_BADBEHAVIOR_WHITELIST')	or define('CONFIG_BADBEHAVIOR_WHITELIST',	'BadBehavior/WhiteList');

function bb2_whitelist($package)
{
	//$whitelists = @parse_ini_file(dirname(BB2_CORE) . "/whitelist.ini");
	// ホワイトリストに入っている場合はチェックしない
	$config = new Config(CONFIG_BADBEHAVIOR_WHITELIST);
	$config->read();
	$IPs = $config->get('IP');
	if (@!empty($IPs)) {
		foreach ($IPs as $range){
			if (match_cidr($package['ip'], $range)) return true;
		}
	}
	$UAs = $config->get('UserAgent');
	if (@!empty($UAs)) {
		foreach ($UAs as $user_agent) {
			if (!strcmp($package['headers_mixed']['User-Agent'], $user_agent)) return true;
		}
	}
	$URLs = $config->get('URL');
	if (@!empty($URLs)) {
		if (strpos($package['request_uri'], "?") === FALSE) {
			$request_uri = $package['request_uri'];
		} else {
			$request_uri = substr($package['request_uri'], 0, strpos($package['request_uri'], "?"));
		}
		foreach ($URLs as $url) {
			if (!strcmp($request_uri, $url)) return true;
		}
	}
	return false;
}
