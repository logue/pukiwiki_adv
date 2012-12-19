<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: func.php,v 1.104.48 2012/06/23 06:59:00 Logue Exp $
// Copyright (C)
//   2010-2012 PukiWiki Advance Developers Team
//   2005-2009 PukiWiki Plus! Team
//   2002-2007,2009-2011 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version

// Load Hangul Libraly
require(LIB_DIR . 'hangul.php');

function is_url($str, $only_http = FALSE)
{
	// URLでありえない文字はfalseを返す
	if ( preg_match( "|[^-/?:#@&=+$,\w.!~*;'()%]|", $str ) ) {
		return FALSE;
	}
	// 許可するスキーマー
	$scheme = $only_http ? 'https?' : 'https?|ftp|news';

	// URLマッチパターン
	$pattern = (
		"!^(?:".$scheme.")://"					// scheme
		. "(?:\w+:\w+@)?"						// ( user:pass )?
		. "("
		. "(?:[-_0-9a-z]+\.)+(?:[a-z]+)\.?|"	// ( domain name |
		. "\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}|"	//   IP Address  |
		. "localhost"							//   localhost )
		. ")"
		. "(?::\d{1,5})?(?:/|$)!iD"				// ( :Port )?
	);
	// 正規処理
	$ret = preg_match($pattern, $str);
	// マッチしない場合は0が帰るのでFALSEにする
	return ($ret === 0) ? FALSE : $ret;
}



function is_cantedit($page)
{
	global $cantedit;
	static $is_cantedit;

	if (! isset($is_cantedit)) {
		foreach($cantedit as $key) {
			$is_cantedit[$key] = TRUE;
		}
	}

	return isset($is_cantedit[$page]);
}



// Auto template
function auto_template($page)
{
	global $auto_template_func, $auto_template_rules;

	if (! $auto_template_func) return '';

	$body = '';
	$matches = array();
	foreach ($auto_template_rules as $rule => $template) {
		$rule_pattrn = '/' . $rule . '/';

		if (! preg_match($rule_pattrn, $page, $matches)) continue;

		$template_page = preg_replace($rule_pattrn, $template, $page);
		if (! is_page($template_page)) continue;

		$body = FileFactory::Wiki($template_page)->source();

		// Remove fixed-heading anchors
		$body = preg_replace('/^(\*{1,3}.*)\[#[A-Za-z][\w-]+\](.*)$/m', '$1$2', $body);

		// Remove '#freeze'
		$body = preg_replace('/^#freeze\s*$/m', '', $body);

		$count = count($matches);
		for ($i = 0; $i < $count; $i++)
			$body = str_replace('$' . $i, $matches[$i], $body);

		break;
	}
	return $body;
}

// Expand search words
function get_search_words($words, $do_escape = FALSE)
{
	static $init, $mb_convert_kana, $pre, $post, $quote = '/';
	global $mecab_path;

	if (! isset($init)) {
		// function: mb_convert_kana() is for Japanese code only
		if (LANG == 'ja' && function_exists('mb_convert_kana')) {
			$mb_convert_kana = create_function('$str, $option',
				'return mb_convert_kana($str, $option, SOURCE_ENCODING);');
		} else {
			$mb_convert_kana = create_function('$str, $option',
				'return $str;');
		}
		$pre = $post = '';
		$init = TRUE;
	}

	if (! is_array($words)) $words = array($words);

	// Generate regex for the words
	$regex = array();
	foreach ($words as $word) {
/*
		if (extension_loaded('mecab') || file_exists($mecab_path)){
			$reg = '';
			$wakati = mecab_wakati($word);
			$ws = explode(' ', $wakati);

			foreach ($ws as $k)
				$reg .= !empty($k) ? '(?:'.$k.')' : '';
			$regex[$word] = $reg;
		}else{
*/
			$word = trim($word);
			if (empty($word)) continue;

			// Normalize: ASCII letters = to single-byte. Others = to Zenkaku and Katakana
			$word_nm = $mb_convert_kana($word, 'aKCV');
			$nmlen   = mb_strlen($word_nm, SOURCE_ENCODING);

			// Each chars may be served ...
			$chars = array();
			for ($pos = 0; $pos < $nmlen; $pos++) {
				$char = mb_substr($word_nm, $pos, 1, SOURCE_ENCODING);

				// Just normalized one? (ASCII char or Zenkaku-Katakana?)
				$or = array(preg_quote($do_escape ? htmlsc($char) : $char, $quote));
				if (strlen($char) == 1) {
					// An ASCII (single-byte) character
					foreach (array(strtoupper($char), strtolower($char)) as $_char) {
						if ($char != '&') $or[] = preg_quote($_char, $quote); // As-is?
						$ascii = ord($_char);
						$or[] = sprintf('&#(?:%d|x%x);', $ascii, $ascii); // As an entity reference?
						$or[] = preg_quote($mb_convert_kana($_char, 'A'), $quote); // As Zenkaku?
					}
				} else {
					// NEVER COME HERE with mb_substr(string, start, length, 'ASCII')
					// A multi-byte character
					$or[] = preg_quote($mb_convert_kana($char, 'c'), $quote); // As Hiragana?
					$or[] = preg_quote($mb_convert_kana($char, 'k'), $quote); // As Hankaku-Katakana?
				}
				$chars[] = '(?:' . join('|', array_unique($or)) . ')'; // Regex for the character
			}

			$regex[$word] = $pre . join('', $chars) . $post; // For the word
//		}
	}

	return $regex; // For all words
}

// 'Search' main function
function do_search($word, $type = 'and', $non_format = FALSE, $base = '')
{
	global $script, $whatsnew, $non_list, $search_non_list;
 	global $search_auth, $show_passage, $search_word_color, $ajax;
//	global $_msg_andresult, $_msg_orresult, $_msg_notfoundresult;
	global $_string;

	$_msg_andresult = $_string['andresult'];
	$_msg_orresult = $_string['orresult'];
	$_msg_notfoundresult = $_string['notfoundresult'];

	$retval = array();

	$keys = get_search_words(preg_split('/\s+/', $word, -1, PREG_SPLIT_NO_EMPTY));
	foreach ($keys as $key=>$value)
		$keys[$key] = '/' . $value . '/S';

	$b_type = ($type == 'and'); // AND:TRUE OR:FALSE

	$pages = auth::get_existpages();

	// SAFE_MODE の場合は、コンテンツ管理者以上のみ、カテゴリページ(:)も検索可能
	$role_adm_contents = (auth::check_role('safemode')) ? auth::check_role('role_adm_contents') : FALSE;

	// Avoid
	if ( !empty($base) ) {
		$pages = preg_grep('/^' . preg_quote($base, '/') . '/S', $pages);
	}
	if (! $search_non_list) {
		$pages = array_diff($pages, preg_grep('/' . $non_list . '/S', $pages));
	}
	$pages = array_flip($pages);
	unset($pages[$whatsnew]);

	$count = count($pages);
	foreach (array_keys($pages) as $page) {
		$b_match = FALSE;

		// Search hidden for page name (Plus!)
		if (substr($page, 0, 1) == ':' && $role_adm_contents) {
			unset($pages[$page]);
			--$count;
			continue;
		}

		// Search for page name
		if (! $non_format) {
			foreach ($keys as $key) {
				$b_match = preg_match($key, $page);
				if ($b_type xor $b_match) break; // OR
			}
			if ($b_match) continue;
		}

		// Search auth for page contents
		if ($search_auth && ! check_readable($page, false, false)) {
			unset($pages[$page]);
			--$count;
		}

		// Search for page contents
		$page_source = get_source($page, TRUE, TRUE);
		// 通常検索
		foreach ($keys as $key) {
			$b_match = preg_match($key, $page_source);
			if ($b_type xor $b_match) break; // OR
		}
		if ($b_match) continue;


		unset($pages[$page]); // Miss
	}
	unset($role_adm_contents);	// Plus!

	if ($non_format) return array_keys($pages);

	if (empty($pages))
		return str_replace('$1', htmlsc($word), $_msg_notfoundresult);

	ksort($pages, SORT_STRING);

	$retval = '<ul>' . "\n";
	foreach (array_keys($pages) as $page) {
		$passage = $show_passage ? ' ' . get_passage(get_filetime($page)) : '';
		$retval .= ' <li><a href="' . get_page_uri($page) . '" class="linktip">' . htmlsc($page) . '</a>' . $passage . '</li>' . "\n";
	}
	$retval .= '</ul>' . "\n";

	$retval .= '<p>'.str_replace('$1', htmlsc($word) , str_replace('$2', count($pages),
		str_replace('$3', $count, $b_type ? $_string['andresult'] : $_string['orresult']))).'</p>';

	return $retval;
}

// Argument check for program
function arg_check($str)
{
	global $vars;
	return isset($vars['cmd']) && (strpos($vars['cmd'], $str) === 0);
}

// Encode page-name
function encode($str)
{
	$value = @strval($str);
	return empty($value) ? '' : strtoupper(bin2hex($value));
	// Equal to strtoupper(join('', unpack('H*0', $str)));
	// But PHP 4.3.10 says 'Warning: unpack(): Type H: outside of string in ...'
}

// Decode page name
function decode($str)
{
	return hex2bin($str);
}

// hex2bin -- Converts the hex representation of data to binary
// (PHP 5.4.0)
// Inversion of bin2hex()
if (! function_exists('hex2bin')) {
	function hex2bin($hex_string)
	{
		// preg_match : Avoid warning : pack(): Type H: illegal hex digit ...
		// (string)   : Always treat as string (not int etc). See BugTrack2/31
		return preg_match('/^[0-9a-f]+$/i', $hex_string) ?
			pack('H*', (string)$hex_string) : $hex_string;
	}
}

// Remove [[ ]] (brackets)
function strip_bracket($str)
{
	$match = array();
	if (preg_match('/^\[\[(.*)\]\]$/', $str, $match)) {
		return $match[1];
	} else {
		return $str;
	}
}

// Generate sorted "list of pages" XHTML, with page-reading hints
function page_list($pages = array('pagename.txt' => 'pagename'), $cmd = 'read', $withfilename = FALSE)
{
//	global $pagereading_enable, $list_index, $_msg_symbol, $_msg_other;
	global $pagereading_enable, $list_index, $_string;

	$_msg_symbol = $_string['symbol'];
	$_msg_other = $_string['other'];

	// Sentinel: symbolic-chars < alphabetic-chars < another(multibyte)-chars
	// = ' ' < '[a-zA-Z]' < 'zz'
	$sentinel_symbol  = '*';
	$sentinel_another = 'zz';

//	$href = get_script_uri() . '?' . ($cmd == 'read' ? '' : 'cmd=' . rawurlencode($cmd) . '&amp;page=');
	$array = $matches = $counts = array();

	if ($pagereading_enable) {
		mb_regex_encoding(SOURCE_ENCODING);
		$readings = get_readings($pages);
	}
	foreach($pages as $file => $page) {
		// Get the initial letter of the page name
		if ($pagereading_enable) {
			// WARNING: Japanese code hard-wired
			if(mb_ereg('^(\:|[A-Za-z])', mb_convert_kana($page, 'a'), $matches) !== FALSE) {
				// 英数字
				$initial = & $matches[1];
			} elseif (isset($readings[$page]) && mb_ereg('^([ァ-ヶ])', $readings[$page], $matches) !== FALSE) { // here
				// カタカナ
				$initial = & $matches[1];
			} elseif (mb_ereg('^[ -~]|[^ぁ-ん亜-熙]', $page)) { // and here
				// ひらがな、常用漢字
				$initial = & $sentinel_symbol;
			} elseif (preg_match('/^([가-힣])/', $page) !== FALSE){
				// ハングル（日本語とバッティングしないため実装）
				// http://pukiwiki.sourceforge.jp/dev/?BugTrack2%2F13
				$initial = hangul_chosung($page);
/*
			} elseif (mb_ereg('/^([一-龥])/',$page) !== FALSE){
				// 簡体中国語
*/
			} else {
				$initial = & $sentinel_another;
			}
		} else {
			if (preg_match('/^(\:|[A-Za-z])/', $page, $matches)) {
				$initial = $matches[1];
			} elseif (preg_match('/^([ -~])/', $page)) {
				$initial = $sentinel_symbol;
			} else {
				$initial = $sentinel_another;
			}
		}

		$str = '			<li>';
		if ($cmd !== 'read'){
			$str .= '<a href="' . get_cmd_uri($cmd, $page) . '" >' . htmlsc($page, ENT_QUOTES) . '</a>';
		}else{
			if (!IS_MOBILE) {
				$str .= '<a href="' . get_page_uri($page) . '" >' . htmlsc($page, ENT_QUOTES) . '</a>' .get_pg_passage($page);
				if ($withfilename) {
					$str .= '<br /><var>' . htmlsc($file) . '</var>';
				}
			}else{
				$str .= '<a href="' . get_page_uri($page) . '" data-transition="slide">' . htmlsc($page, ENT_QUOTES) . '</a>' . '<span class="ui-li-count">'.get_pg_passage($page, false).'</span>';
			}
		}
			$str .= '</li>';
		$array[$initial][$page] = $str;
		$counts[$initial] = count($array[$initial]);
	}
	unset($pages);
	ksort($array, SORT_STRING);

	$cnt = 0;
	$retval = $contents = array();
	if (!IS_MOBILE) {
		$retval[] = '<div class="list_pages">';
		foreach ($array as $_initial => $pages) {
			ksort($pages, SORT_STRING);
			if ($list_index) {
				++$cnt;
				$page_count = $counts[$_initial];
				if ($_initial == $sentinel_symbol) {
					$_initial = htmlsc($_msg_symbol);
				} else if ($_initial == $sentinel_another) {
					$_initial = htmlsc($_msg_other);
				}
				$retval[] = '	<fieldset id="head_' . $cnt .'">';
				$retval[] = '		<legend><a href="#top_' . $cnt . '">' . $_initial . '</a></legend>';
				$retval[] = '		<ul class="list1">';

				$contents[] = '<li id="top_' . $cnt .'">'.
								'<a href="#head_' . $cnt . '" title="'.$page_count.'">' .$_initial . '</a></li>';
			}
			$retval[] = join("\n", $pages);
			if ($list_index) {
				$retval[] = '		</ul>';
				$retval[] = '	</fieldset>';
			}
		}
		$retval[] = '</div>';
	}else{
		foreach ($array as $_initial => $pages) {
			ksort($pages, SORT_STRING);
			if ($list_index) {
				++$cnt;
				$page_count = $counts[$_initial];
				if ($_initial == $sentinel_symbol) {
					$_initial = htmlsc($_msg_symbol);
				} else if ($_initial == $sentinel_another) {
					$_initial = htmlsc($_msg_other);
				}
				$contents[] = '<li data-role="list-divider">' . $_initial . '</li>';

				if ( isset($array[$_initial]) && is_array($array[$_initial]) ){
					foreach($array[$_initial] as $page){
						$contents[] = $page;
					}
				}
				//$contents[] = $array[$_initial][$pages];
			}
		}
	}
	unset($array);

	// Insert a table of contents
	$ret = '';
	if ($list_index && $cnt) {
		while (! empty($contents)) {
			$tmp[] = join('', array_splice($contents, 0));
		}
		$contents = & $tmp;
		if (!IS_MOBILE) {
			array_unshift(
				$retval,
				'<div  class="page_initial"><ul>',
				join("\n" , $contents),
				'</ul></div>');
			$ret = '<div class="pages">'."\n".join("\n", $retval) . "\n".'</div>';
		}else{
			$ret = '<ul data-role="listview">'.join("\n", $contents).'</ul>';
		}
	}

	return $ret;
}

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
	$ret[] = '<a href="' . get_cmd_uri() . '">Return to FrontPage</a> ]</p>';
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
		$html[] = '<link rel="stylesheet" href="http://code.jquery.com/ui/' . JQUERY_UI_VER . '/themes/base/jquery-ui.css" type="text/css" />';
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

class AutoAlias{
	const CACHE_NAME = PKWK_AUTOBASEALIAS_CACHE;
	// \[\[					# open bracket
	// ((?:(?!\]\]).)+)>	# (1) alias name
	// ((?:(?!\]\]).)+)		# (2) alias link
	// \]\]					# close bracket
	const PATTERN = '\[\[((?:(?!\]\]).)+)>((?:(?!\]\]).)+)\]\]';

	public function get(){
		global $cache;

		$pairs = array();

		if (! $cache['wiki']->hasItem(CACHE_NAME)){
			$cache['wiki']->setItem(CACHE_NAME, $pairs);
		}else{
			$pairs = $cache['wiki']->getItem(CACHE_NAME);
		}
	}

	private function from_aliaspage(){
		global $aliaspage, $autoalias_max_words;
		$postdata = PukiWiki\Lib\FileFactory::Wiki($aliaspage)->source();
		$matches = array();
		$count = 0;
		$max   = max($autoalias_max_words, 0);
		if (preg_match_all('/' . self::PATTERN . '/x', $postdata, $matches, PREG_SET_ORDER)) {
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
		return $pairs;
	}

	private function from_autobasealias(){
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



// Get absolute-URI of this script
function init_script_uri($init_uri = '',$get_init_value=0)
{
	global $script_directory_index, $absolute_uri;
	static $script;

	if ( empty($init_uri) ) {
		// Get
		if (isset($script)) {
			if ($get_init_value) return $script;
			return $absolute_uri ? get_script_absuri() : $script;
		}
		$script = get_script_absuri();
		return $script;
	}

	// Set manually
	if (isset($script)) die_message('$script: Already init');
	if (! is_reluri($init_uri) && ! is_url($init_uri, TRUE)) die_message('$script: Invalid URI');
	$script = $init_uri;

	// Cut filename or not
	if (isset($script_directory_index)) {
		if (! file_exists($script_directory_index))
			die_message('Directory index file not found: ' .
				htmlsc($script_directory_index));
		$matches = array();
		if (preg_match('#^(.+/)' . preg_quote($script_directory_index, '#') . '$#',
			$script, $matches)) $script = $matches[1];
	}

	return $absolute_uri ? get_script_absuri() : $script;
}

// Get absolute-URI of this script
function get_script_uri($path='')
{
	global $absolute_uri, $script_directory_index;

	if ($absolute_uri === 1) return get_script_absuri();
	$uri = get_baseuri($path);
	if (! isset($script_directory_index)) $uri .= init_script_filename();
	return $uri;
}

// Get absolute-URI of this script
function get_script_absuri()
{
	global $script_abs, $script_directory_index;
	global $script;
	static $uri;

	// Get
	if (isset($uri)) return $uri;

	if (isset($script_abs) && is_url($script_abs,true)) {
		$uri = $script_abs;
		return $uri;
	} else
	if (isset($script) && is_url($script,true)) {
		$uri = $script;
		return $uri;
	}

	// Set automatically
	$msg	 = 'get_script_absuri() failed: Please set [$script or $script_abs] at INI_FILE manually';

	$uri  = ( ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
	$uri .= ($_SERVER['SERVER_PORT'] == 80 || $_SERVER['SERVER_PORT'] == 443) ? '' : ':' . SERVER_PORT;  // port

	// SCRIPT_NAME が'/'で始まっていない場合(cgiなど) REQUEST_URIを使ってみる
	$path	= SCRIPT_NAME;
	if ($path{0} != '/') {
		if (! isset($_SERVER['REQUEST_URI']) || $_SERVER['REQUEST_URI']{0} != '/') {
			die_message($msg);
		}

		// REQUEST_URIをパースし、path部分だけを取り出す
		$parse_url = parse_url($uri . $_SERVER['REQUEST_URI']);
		if (! isset($parse_url['path']) || $parse_url['path']{0} != '/') {
			die_message($msg);
		}

		$path = $parse_url['path'];
	}
	$uri .= $path;

	if (! is_url($uri, true) && php_sapi_name() == 'cgi') {
		die_message($msg);
	}
	unset($msg);

	// Cut filename or not
	if (isset($script_directory_index)) {
		if (! file_exists($script_directory_index))
			die_message('Directory index file not found: ' .
			htmlsc($script_directory_index));
		$matches = array();
		if (preg_match('#^(.+/)' . preg_quote($script_directory_index, '#') . '$#',
			$uri, $matches)) $uri = $matches[1];
	}

	return $uri;
}


/**
 * プラグインのアドレスを出力
 * @param type $cmd プラグイン名
 * @param type $page ページ名
 * @param type $path_reference rel属性
 * @param type $query クエリ（連想配列で）
 * @param type $fragment
 * @return type
 *
 */
// function get_cmd_uri($cmd='', $page='', $query='', $fragment='')
function get_cmd_uri($cmd='', $page='', $path_reference='rel', $query='', $fragment='')
{
	return get_resolve_uri($cmd,$page,$path_reference,$query,$fragment,0);
}

// function get_page_uri($page, $query='', $fragment='')
function get_page_uri($page, $path_reference='rel', $query='', $fragment='')
{
	return get_resolve_uri('',$page,$path_reference,$query,$fragment,0);
}

// Obsolete (svn収容分のみ利用可)
//function get_resolve_uri($cmd='', $page='', $query='', $fragment='', $abs=1, $location=1)
function get_resolve_uri($cmd='', $page='', $path_reference='rel', $query='', $fragment='', $location=1)
{
	global $static_url, $url_suffix, $vars;
	// global $script, $absolute_uri;
	// $ret = ($absolute_uri || $path_reference == 'abs') ? get_script_absuri() : $script;
	$path = (empty($path_reference)) ? 'rel' : $path_reference;
	$ret = get_script_uri($path);

	$flag = '?';
	$page_pref = '';

	if ($cmd == 'read') $cmd = '';
	if (! empty($cmd)) {
		$ret .= $flag.'cmd='.$cmd;
		$flag = '&';
		$page_pref = 'page=';
	}

	if (! empty($page)) {
		$ret .= $flag.$page_pref.rawurlencode($page);

		if (empty($cmd) && $static_url === 1 && (isset($vars['cmd']) && $vars['cmd'] !== 'search') &&
			!( stristr(getenv('SERVER_SOFTWARE'), 'apache') !== FALSE && (strstr($page,':' ) !== FALSE || strstr($page,' ' ) !== FALSE) ) ){	// Apacheは、:が含まれるアドレスを正確に処理できない
			// To static URL
			$ret = str_replace('?', '', $ret);
			$ret = str_replace('%2F', '/', $ret) . $url_suffix;
		}else{
			$flag = '&';
		}
	}

	// query
	if (! empty($query)) {
		if (is_array($query)) {
			$tmp_query = & $query;
		} else {
			parse_str($query,$tmp_query);
		}
		// (PHP5) http_build_query -> funcplus.php
		$ret .= $flag . http_build_query($tmp_query);
		$flag = '&';
	}

	// fragment
	if (! empty($fragment)) {
		$ret .= '#'.$fragment;
	}
	unset($flag, $page_pref);
	return ($location) ? $ret : htmlsc( str_replace('&amp;','&',$ret) );
	// return ($location) ? $ret : htmlsc( $ret );
}

// Obsolete (明示指定用)
function get_cmd_absuri($cmd='', $page='', $query='', $fragment='')
{
	return get_resolve_uri($cmd,$page,'full',$query,$fragment,0);
}
// Obsolete (明示指定用)
function get_page_absuri($page, $query='', $fragment='')
{
	return get_resolve_uri('',$page,'full',$query,$fragment,0);
}

// Obsolete (ポカミス用)
function get_page_location_uri($page='', $query='', $fragment='')
{
	return get_resolve_uri('',$page,'full',$query,$fragment,1);
}
// Obsolete (ポカミス用)
function get_location_uri($cmd='', $page='', $query='', $fragment='')
{
	return get_resolve_uri($cmd,$page,'full',$query,$fragment,1);
}

// Remove null(\0) bytes from variables
//
// NOTE: PHP had vulnerabilities that opens "hoge.php" via fopen("hoge.php\0.txt") etc.
// [PHP-users 12736] null byte attack
// http://ns1.php.gr.jp/pipermail/php-users/2003-January/012742.html
//
// 2003-05-16: magic quotes gpcの復元処理を統合
// 2003-05-21: 連想配列のキーはbinary safe
//
function input_filter($param)
{
	static $magic_quotes_gpc = NULL;
	if ($magic_quotes_gpc === NULL)
		$magic_quotes_gpc = get_magic_quotes_gpc();

	if (is_array($param)) {
		return array_map('input_filter', $param);
	} else {
		$result = str_replace("\0", '', $param);
		if ($magic_quotes_gpc) $result = stripslashes($result);
		return $result;
	}
}

// Sugar with default settings
function htmlsc($string = '', $flags = ENT_QUOTES, $charset = 'UTF-8')
{
	return htmlspecialchars($string, $flags, $charset);	// htmlsc()
}


// Move from proxy.php (rejected)

// Separate IPv4 network-address and its netmask
define('PKWK_CIDR_NETWORK_REGEX', '/^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})(?:\/([0-9.]+))?$/');

/*
 * is_requestable($uri)
 */
function is_requestable($uri)
{
	$arr1 = parse_url(get_script_absuri());
	$arr2 = parse_url($uri);
	$arr1['port']  = isset($arr1['port'])  ? $arr1['port'] : 80;
	$arr2['port']  = isset($arr2['port'])  ? $arr2['port'] : 80;
	$arr1['path']  = isset($arr1['path'])  ? dirname($arr1['path'] . 'dummy') : '/';
	$arr2['path']  = isset($arr2['path'])  ? dirname($arr2['path'] . 'dummy') : '/';

	if ($arr1['scheme'] != $arr2['scheme'] ||
		$arr1['host'] != $arr2['host'] ||
		$arr1['port'] != $arr2['port'] ||
		$arr1['path'] != $arr2['path'])
		return TRUE;

	return FALSE;
}

// Check if the $host is in the specified network(s)
function in_the_net($networks = array(), $host = '')
{
	if (empty($networks) || $host == '') return FALSE;
	if (! is_array($networks)) $networks = array($networks);

	$matches = array();

	if (preg_match(PKWK_CIDR_NETWORK_REGEX, $host, $matches)) {
		$ip = $matches[1];
	} else {
		$ip = gethostbyname($host); // May heavy
	}
	$l_ip = ip2long($ip);

	foreach ($networks as $network) {
		if (preg_match(PKWK_CIDR_NETWORK_REGEX, $network, $matches) &&
		    is_long($l_ip) && long2ip($l_ip) == $ip) {
			// $host seems valid IPv4 address
			// Sample: '10.0.0.0/8' or '10.0.0.0/255.0.0.0'
			$l_net = ip2long($matches[1]); // '10.0.0.0'
			$smask  = isset($matches[2]) ? $matches[2] : 32; // '8' or '255.0.0.0'
			$mask  = is_numeric($smask) ?
				pow(2, 32) - pow(2, 32 - $smask) : // '8' means '8-bit mask'
				ip2long($smask);                   // '255.0.0.0' (the same)

			if (($l_ip & $mask) == $l_net) return TRUE;
		} else {
			// $host seems not IPv4 address. May be a DNS name like 'foobar.example.com'?
			foreach ($networks as $network)
				if (preg_match('/\.?\b' . preg_quote($network, '/') . '$/', $host))
					return TRUE;
		}
	}

	return FALSE; // Not found
}

/* End of file func.php */
/* Location: ./wiki-common/lib/func.php */