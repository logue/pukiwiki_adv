<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: func.php,v 1.104.48 2012/06/23 06:59:00 Logue Exp $
// Copyright (C)
//   2010-2012 PukiWiki Advance Developers Team
//   2005-2009 PukiWiki Plus! Team
//   2002-2007,2009-2011 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version

// Show text formatting rules
function catrule()
{
	global $rule_page;

	if (! is_page($rule_page)) {
		return '<p>Sorry, page \'' . htmlsc($rule_page) .
			'\' unavailable.</p>';
	} else {
		return convert_html(get_source($rule_page));
	}
}
//defined('JQUERY_UI_VER') or define('JQUERY_UI_VER',		'1.9.2');
// Show (critical) error message
function die_message($msg, $error_title='', $http_code = 500){
	global $skin_file, $page_title, $_string, $_title, $_button, $vars;

	$title = !empty($error_title) ? $error_title : $_title['error'];
	$page = $_title['error'];

	if (PKWK_WARNING !== true){	// PKWK_WARNINGが有効でない場合は、詳細なエラーを隠す
		$msg = $_string['error_msg'];
	}
	$ret = array();
	$ret[] = '<p>[ ';
	if ( isset($vars['page']) && !empty($vars['page']) ){
		$ret[] = '<a href="' . get_page_location_uri($vars['page']) .'">'.$_button['back'].'</a> | ';
		$ret[] = '<a href="' . get_cmd_uri('edit',$vars['page']) . '">Try to edit this page</a> | ';
	}
	$ret[] = '<a href="' . get_script_uri() . '">Return to FrontPage</a> ]</p>';
	$ret[] = '<div class="message_box ui-state-error ui-corner-all">';
	$ret[] = '<p style="padding:0 .5em;"><span class="ui-icon ui-icon-alert"></span> <strong>' . $_title['error'] . '</strong> ' . $msg . '</p>';
	$ret[] = '</div>';
	$body = join("\n",$ret);

	global $trackback;
	$trackback = 0;

	if (!headers_sent()){
		pkwk_common_headers(0,0, $http_code);
	}

	if(defined('SKIN_FILE')){
		if (file_exists(SKIN_FILE) && is_readable(SKIN_FILE)) {
			catbody($page, $title, $body);
		} elseif ( !empty($skin_file) && file_exists($skin_file) && is_readable($skin_file)) {
			define('SKIN_FILE', $skin_file);
			catbody($page, $title, $body);
		}
	}else{
		$html = array();
		$html[] = '<!doctype html>';
		$html[] = '<html>';
		$html[] = '<head>';
		$html[] = '<meta charset="utf-8">';
		$html[] = '<meta name="robots" content="NOINDEX,NOFOLLOW" />';
		$html[] = '<link rel="stylesheet" href="http://code.jquery.com/ui/jquery-ui-git.css" type="text/css" />';
		$html[] = '<title>' . $page . ' - ' . $page_title . '</title>';
		$html[] = '</head>';
		$html[] = '<body>' . $body . '</body>';
		$html[] = '</html>';
		echo join("\n",$html);
	}
	pkwk_common_suffixes();
	die();
}

function ridirect($url = ''){
	global $vars;
	if (empty($url)) $url = get_page_location_uri($vars['page']);
	pkwk_headers_sent();
	header('Status: 301 Moved Permanently');
	header('Location: ' . $url);
	$html = array();
	$html[] = '<!doctype html>';
	$html[] = '<html>';
	$html[] = '<head>';
	$html[] = '<meta charset="utf-8">';
	$html[] = '<meta name="robots" content="NOINDEX,NOFOLLOW" />';
	$html[] = '<meta http-equiv="refresh" content="1; URL='.$url.'" />';
	$html[] = '<link rel="stylesheet" href="http://code.jquery.com/ui/' . JQUERY_UI_VER . '/themes/base/jquery-ui.css" type="text/css" />';
	$html[] = '<title>301 Moved Permanently</title>';
	$html[] = '</head>';
	$html[] = '<body>';
	$html[] = '<div class="message_box ui-state-info ui-corner-all">';
	$html[] = '<p style="padding:0 .5em;"><span class="ui-icon ui-icon-alert"></span>Please click <a href="'.$url.'">here</a> if you do not want to move even after a while.</p>';
	$html[] = '</div>';
	$html[] = '</body>';
	$html[] = '</html>';
	echo join("\n",$html);
	exit;
}

/*
function pkwkErrorHandler($errno, $errstr, $errfile, $errline){
	global $info, $_string, $_error_type;
	$die = false;
	$msg = '';

	if (DEBUG !== true || PKWK_WARNING !== true){	// デバッグモード時および、警報表示モード時以外はエラーを
		if (!(error_reporting() & $_error_type)) {
			// error_reporting 設定に含まれていないエラーコードです
			return;
		}

		switch ($errno) {
			case E_ERROR:
			case E_CORE_ERROR:
			case E_USER_ERROR:
				$die = true;
			case E_WARNING:
			case E_CORE_WARNING:
			case E_USER_WARNING:
				$msg = '<span class="ui-icon ui-icon-alert" style="float:left;"></span>';
				break;
			default:
				$msg ='<span class="ui-icon ui-icon-info" style="float:left;"></span>';
				break;
		}
		$msg .= (!isset($_error_type[$errno])) ? '' : '<strong>'.$_error_type[$errno].'</strong>';

		$msg .= '<output>'.htmlsc($errstr).'</output><br />'."\n".
			'Fatal error on line <var>'.$errline.'</var> in file <var>'.$errfile.'</var>.';

		if ($die === true){
			$msg .= 'Script execution has been aborted.';
		}

		$ret = <<<EOD
<div class="message_box ui-state-error ui-corner-all">
	<p>$msg</p>
</div>
EOD;
		if ($die === true){
			die($ret);
		}else if (headers_sent()){
			$info[] = $msg;
		}else{
			echo $ret;
		}
	}

	// PHP の内部エラーハンドラを実行しません
	return true;
}
set_error_handler("pkwkErrorHandler");
*/
// Have the time (as microtime)
function getmicrotime()
{
	list($usec, $sec) = explode(' ', microtime());
	return ((float)$sec + (float)$usec);
}

// Elapsed time by second
function elapsedtime()
{
	$at_the_microtime = MUTIME;
	return sprintf('%01.03f', getmicrotime() - $at_the_microtime);
}

// Get the date
function get_date($format, $timestamp = NULL)
{
/*
	$format = preg_replace('/(?<!\\\)T/',
		preg_replace('/(.)/', '\\\$1', ZONE), $format);

	$time = ZONETIME + (($timestamp !== NULL) ? $timestamp : UTIME);

	return date($format, $time);
*/
	/*
	 * $format で指定される T を ZONE で置換したいが、
	 * date 関数での書式指定文字となってしまう可能性を回避するための事前処理
	 */
	$l = strlen(ZONE);
	$zone = '';
	for($i=0;$i<$l;$i++) {
		$zone .= '\\'.substr(ZONE,$i,1);
	}

	$format = str_replace('\T','$$$',$format); // \T の置換は除く
	$format = str_replace('T',$zone,$format);
	$format = str_replace('$$$','\T',$format); // \T に戻す

	$time = ZONETIME + (($timestamp !== NULL) ? $timestamp : UTIME);
	$str = gmdate($format, $time);
	if (ZONETIME == 0) return $str;

	$zonetime = get_zonetime_offset(ZONETIME);
	return str_replace('+0000', $zonetime, $str);
}

function get_zonetime_offset($zonetime)
{
	$pm = ($zonetime < 0) ? '-' : '+';
	$zonetime = abs($zonetime);
	(int)$h = $zonetime / 3600;
	$m = $zonetime - ($h * 3600);
	return sprintf('%s%02d%02d', $pm,$h,$m);
}

// Format date string
function format_date($val, $paren = FALSE, $format = null)
{
	global $date_format, $time_format, $_labels;

	$val += ZONETIME;
	$wday = date('w', $val);

	$week   = $_labels['week'][$wday];

	if ($wday == 0) {
		// Sunday
		$style = 'week_sun';
	} else if ($wday == 6) {
		// Saturday
		$style = 'week_sat';
	}else{
		$style = 'week_day';
	}
	if (!isset($format)){
		$date = date($date_format, $val) .
			'(<abbr class="' . $style . '" title="' . $week[1]. '">'. $week[0] . '</abbr>)' .
			gmdate($time_format, $val);
	}else{
		$month  = $_labels['month'][date('n', $val)];
		$month_short = $month[0];
		$month_long = $month[1];


		$date = str_replace(
			array(
				date('M', $val),	// 月。3 文字形式。
				date('l', $val),	// 曜日。フルスペル形式。
				date('D', $val)		// 曜日。3文字のテキスト形式。
			),
			array(
				'<abbr class="month" title="' . $month[1]. '">'. $month[0] . '</abbr>',
				$week[1],
				'(<abbr class="' . $style . '" title="' . $week[1]. '">'. $week[0] . '</abbr>)'
			),
			gmdate($format, $val)
		);
	}

	return $paren ? '(' . $date . ')' : $date;
}

// Get short pagename(last token without '/')
function get_short_pagename($fullpagename)
{
	$pagestack = explode('/', $fullpagename);
	return array_pop($pagestack);
}

// Get short string of the passage, 'N seconds/minutes/hours/days/years ago'
function get_passage($time, $paren = TRUE)
{
	static $units = array('m'=>60, 'h'=>24, 'd'=>1);

	$time = max(0, (MUTIME - $time) / 60); // minutes

	foreach ($units as $unit=>$card) {
		if ($time < $card) break;
		$time /= $card;
	}
	$time = floor($time) . $unit;

	return $paren ? '(' . $time . ')' : $time;
}

// Hide <input type="(submit|button|image)"...>
function drop_submit($str)
{
	return preg_replace('/<input([^>]+)type="(submit|button|image)"/i',
		'<input$1type="hidden"', $str);
}

function get_glossary_pattern(& $pages, $min_len = -1)
{
	global $WikiName, $autoglossary, $nowikiname;

	$config = new Config('Glossary');
	$config->read();
	$ignorepages	  = $config->get('IgnoreList');
	$forceignorepages = $config->get('ForceIgnoreList');
	unset($config);
	$auto_pages = array_merge($ignorepages, $forceignorepages);

	if ($min_len == -1) {
		$min_len = $autoglossary;   // set $autoglossary, when omitted.
	}

	foreach ($pages as $page)
		if (preg_match('/^' . $WikiName . '$/', $page) ?
			$nowikiname : mb_strlen($page) >= $min_len)
			$auto_pages[] = $page;

	if (empty($auto_pages)) {
		return array('(?!)', 'PukiWiki', 'PukiWiki');
	} else {
		$auto_pages = array_unique($auto_pages);
		sort($auto_pages, SORT_STRING);

		$auto_pages_a = array_values(preg_grep('/^[A-Z]+$/i', $auto_pages));
		$auto_pages   = array_values(array_diff($auto_pages,  $auto_pages_a));

		$result   = generate_trie_regex($auto_pages);
		$result_a = generate_trie_regex($auto_pages_a);
	}
	return array($result, $result_a, $forceignorepages);
}

// Generate AutoLink patterns (thx to hirofummy)
function get_autolink_pattern(& $pages, $min_len = -1)
{
	global $WikiName, $autolink, $nowikiname;

	$config = new Config('AutoLink');
	$config->read();
	$ignorepages	  = $config->get('IgnoreList');
	$forceignorepages = $config->get('ForceIgnoreList');
	unset($config);
	$auto_pages = array_merge($ignorepages, $forceignorepages);

	if ($min_len == -1) {
		$min_len = $autolink;   // set $autolink, when omitted.
	}

	foreach ($pages as $page)
		if (preg_match('/^' . $WikiName . '$/', $page) ?
			$nowikiname : strlen($page) >= $min_len)
			$auto_pages[] = $page;

	if (empty($auto_pages)) {
		$result = $result_a = $nowikiname ? '(?!)' : $WikiName;
	} else {
		$auto_pages = array_unique($auto_pages);
		sort($auto_pages, SORT_STRING);

		$auto_pages_a = array_values(preg_grep('/^[A-Z]+$/i', $auto_pages));
		$auto_pages   = array_values(array_diff($auto_pages,  $auto_pages_a));

		$result   = generate_trie_regex($auto_pages);
		$result_a = generate_trie_regex($auto_pages_a);
	}
	return array($result, $result_a, $forceignorepages);
}

// preg_quote(), and also escape PCRE_EXTENDED-related chars
// REFERENCE: http://www.php.net/manual/en/reference.pcre.pattern.modifiers.php
// NOTE: Some special whitespace characters may warned by PCRE_EXTRA,
//	   because of mismatch-possibility between PCRE_EXTENDED and '[:space:]#'.
function preg_quote_extended($string, $delimiter = NULL)
{
	// Escape some more chars
	$regex_from = '/([[:space:]#])/';
	$regex_to   = '\\\\$1';

	if (is_string($delimiter) && preg_match($regex_from, $delimiter)) {
		$delimiter = NULL;
	}

	return preg_replace($regex_from, $regex_to, preg_quote($string, $delimiter));
}

// Generate one compact regex for quick reTRIEval,
// that just matches with all $array-values.
//
// USAGE (PHP >= 4.4.0, PHP >= 5.0.2):
//   $array = array(7 => 'fooa', 5 => 'foob');
//   $array = array_unique($array);
//   sort($array, SORT_LOCALE_STRING);	// Keys will be replaced
//   echo generate_trie_regex($array);	// 'foo(?:a|b)'
//
// USAGE (PHP >= 5.2.9):
//   $array = array(7 => 'fooa', 5 => 'foob');
//   $array = array_unique($array, SORT_LOCALE_STRING);
//   $array = array_values($array);
//   echo generate_trie_regex($array);	// 'foo(?:a|b)'
//
// ARGUMENTS:
//   $array  : A _sorted_string_ array
//	 * array_keys($array) MUST BE _continuous_integers_started_with_0_.
//	 * Type of all $array-values MUST BE string.
//   $_offset : (int) internal use. $array[$_offset	] is the first value to check
//   $_sentry : (int) internal use. $array[$_sentry - 1] is the last  value to check
//   $_pos	: (int) internal use. Position of the letter to start checking. (0 = the first letter)
//
// REFERENCE: http://en.wikipedia.org/wiki/Trie
//
function generate_trie_regex($array, $_offset = 0, $_sentry = NULL, $_pos = 0)
{
	if (empty($array)) return '(?!)'; // Match with nothing
	if ($_sentry === NULL) $_sentry = count($array);

	// Question mark: array('', 'something') => '(?:something)?'
	$skip = ($_pos >= mb_strlen($array[$_offset]));
	if ($skip) ++$_offset;

	// Generate regex for each value
	$regex = array();
	$index = $_offset;
	$multi = FALSE;
	while ($index < $_sentry) {
		if ($index != $_offset) {
			$multi = TRUE;
			$regex[] = '|'; // OR
		}

		// Get one character from left side of the value
		$char = mb_substr($array[$index], $_pos, 1);

		// How many continuous keys have the same letter
		// at the same position?
		for ($i = $index + 1; $i < $_sentry; $i++) {
			if (mb_substr($array[$i], $_pos, 1) != $char) break;
		}

		if ($index < ($i - 1)) {
			// Some more keys found
			// Recurse
			$regex[] = preg_quote_extended($char, '/');
			$regex[] = generate_trie_regex($array, $index, $i, $_pos + 1);
		} else {
			// Not found
			$regex[] = preg_quote_extended(mb_substr($array[$index], $_pos), '/');
		}
		$index = $i;
	}

	if ($skip || $multi) {
		array_unshift($regex, '(?:');
		$regex[] = ')';
	}
	if ($skip) $regex[] = '?'; // Match for $pages[$_offset - 1]

	return implode('', $regex);
}
// Compat
function get_autolink_pattern_sub(& $pages, $start, $end, $pos)
{
	 return generate_trie_regex($pages, $start, $end, $pos);
}

// Load/get autoalias pairs
function get_autoaliases($word = '')
{
	global $autobasealias;
	static $pairs;
	if (! isset($pairs)) {
		$pairs = get_autoaliases_from_aliaspage();
		if ($autobasealias) {
			$pairs = array_merge($pairs, get_autoaliases_from_autobasealias());
		}
	}

	// An array: All pairs
	if ( empty($word) ) return $pairs;

	// A string: Seek the pair
	return isset($pairs[$word]) ? $pairs[$word] : array();
}

// Load/get pairs of AutoBaseAlias
function get_autoaliases_from_autobasealias()
{
	static $paris;
	global $cache;

	if (! isset($pairs)) {
		$pairs = $cache['wiki']->getItem(PKWK_AUTOBASEALIAS_CACHE);
	}
	if (!is_array($pairs)) $pairs = array();	// safeモードでよくArgument #2 is not an arrayというエラーになるため
	return $pairs;
}

// Load/get setting pairs from AutoAliasName
function get_autoaliases_from_aliaspage()
{
	global $aliaspage, $autoalias_max_words;
	static $pairs = array();

	if (! isset($pairs)) {
		$pairs = array();
		$pattern = <<<EOD
\[\[				# open bracket
((?:(?!\]\]).)+)>   # (1) alias name
((?:(?!\]\]).)+)	# (2) alias link
\]\]				# close bracket
EOD;
		$postdata = get_source($aliaspage, TRUE, TRUE);
		$matches = array();
		$count = 0;
		$max   = max($autoalias_max_words, 0);
		if (preg_match_all('/' . $pattern . '/x', $postdata, $matches, PREG_SET_ORDER)) {
			foreach($matches as $key => $value) {
				if ($count == $max) break;
				$name = trim($value[1]);
				if (! isset($pairs[$name])) {
					$paris[$name] = array();
				}
				++$count;
				$pairs[$name][] = trim($value[2]);
				unset($matches[$key]);
			}
		}
		foreach (array_keys($pairs) as $name) {
			$pairs[$name] = array_unique($pairs[$name]);
		}
	}
	return $pairs;
}

// Get AutoBaseAlias data
function get_autobasealias($pages){
	global $autobasealias_nonlist;
	static $pairs = array();

	foreach ($pages as $page) {
		if (preg_match('/' . $autobasealias_nonlist . '/', $page)) continue;
		$base = get_short_pagename($page);
		if ($base !== $page) {
			if (! isset($pairs[$base])) $pairs[$base] = array();
			$pairs[$base][] = $page;
		}
	}
	return $pairs;
}

// Load/get setting pairs from Glossary
function get_autoglossaries($word = '')
{
	global $glossarypage, $autoglossary_max_words;
	static $pairs;

	if (! isset($pairs)) {
		$pairs = array();
		$pattern = '/^[:|]([^|]+)\|([^|]+)\|?$/';
		$postdata = get_source($glossarypage);
		$matches = array();
		$count = 0;
		$max   = max($autoglossary_max_words, 0);
		foreach ($postdata as $line) {
			if ($count == $max) break;
			if (preg_match($pattern, $line, $matches)) {
				$name = trim($matches[1]);
				if (!isset($pairs[$name])) {
					++$count;
					$pairs[$name] = TRUE;
				}
			}
		}
	}

	// An array: All pairs
	if ( empty($word) ) return $pairs;

	// A string: Seek the pair
	return isset($pairs[$word]) ? $pairs[$word]:'';
}



/* End of file func.php */
/* Location: ./wiki-common/lib/func.php */