<?php if (!defined('BB2_CORE')) die('I said no cheating!');

function bb2_whitelist($package)
{
	//$whitelists = @parse_ini_file(dirname(BB2_CORE) . "/whitelist.ini");
	// ホワイトリストに入っている場合はチェックしない
	$config = new Config(CONFIG_BADBEHAVIOR_WHITELIST);
	$config->read();
	$lines = $config->get('WhiteList');
	foreach ($lines as $line){
		$whitelists[$line[0]] = $line[1];
	}
	unset($lines,$line,$WhiteListLine);

	if (@!empty($whitelists['ip'])) {
		foreach ($whitelists['ip'] as $range) {
			if (match_cidr($package['ip'], $range)) return true;
		}
	}
	if (@!empty($whitelists['useragent'])) {
		foreach ($whitelists['useragent'] as $user_agent) {
			if (!strcmp($package['headers_mixed']['User-Agent'], $user_agent)) return true;
		}
	}
	if (@!empty($whitelists['url'])) {
		if (strpos($package['request_uri'], "?") === FALSE) {
			$request_uri = $package['request_uri'];
		} else {
			$request_uri = substr($package['request_uri'], 0, strpos($package['request_uri'], "?"));
		}
		foreach ($whitelists['url'] as $url) {
			if (!strcmp($request_uri, $url)) return true;
		}
	}
	return false;
}
