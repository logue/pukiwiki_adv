<?php if (!defined('BB2_CWD')) die("I said no cheating!");

// Bad Behavior browser screener
// for PukiWiki Adv use Only!

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
	
	$js_vars['BH_NAME'] = $cookie_name;
	$js_vars['BH_VALUE'] = $cookie_value;
}
