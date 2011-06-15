<?php if (!defined('BB2_CWD')) die("I said no cheating!");

// Bad Behavior browser screener
// for PukiWiki Adv use Only!
// $Id: pukiwiki_screener.php,v 0.2 2011/06/01 08:06:00 Logue Exp $

function bb2_screener($settings, $package)
{
	global $js_vars;

	$cookie_name = BB2_COOKIE;

	// Set up a simple cookie
	$screener = array(time(), $package['ip']);
	if (isset($package['headers_mixed']['X-Forwarded-For'])) {
		array_push($screener, $package['headers_mixed']['X-Forwarded-For']);
	}
	if (isset($package['headers_mixed']['Client-Ip'])) {
		array_push($screener, $package['headers_mixed']['Client-Ip']);
	}

	$cookie_value = implode(" ", $screener);
	
	$js_vars[] = 'var BH_NAME = "'.$cookie_name.'";';
	$js_vars[] = 'var BH_VALUE = "'.$cookie_value.'";';
}
