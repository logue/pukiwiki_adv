<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone
// $Id: auth.php,v 1.0 2012/09/25 15:31:00 Logue Exp $
// Copyright (C)
//   2012      PukiWiki Advance Developers Team
// License: GPL v2 or (at your option) any later version
//
// old functions
use PukiWiki\Lib\Utility;
use PukiWiki\Lib\Router;
use PukiWiki\Lib\File\FileFactory;
use PukiWiki\Lib\File\FileUtility;
use PukiWiki\Lib\Renderer\RendererFactory;
use PukiWiki\Lib\Renderer\Inline\Inline;
use PukiWiki\Lib\Relational;
use PukiWiki\Lib\Search;
use PukiWiki\Lib\TimeZone;
/**
 * backup.php
 */
function make_backup($page, $delete = FALSE)
{
	global $del_backup;
	if (empty($page)) return;
	$backup = FileFactory::Backup($page);

	if ($del_backup && $delete) {
		$backup->removeBackup();
		return;
	}
	return $backup->setBackup();
}

function get_backup($page, $age = 0)
{
	if (empty($page)) return;
	return FileFactory::Backup($page)->getBackup($age);
}

function _backup_file_exists($page){
	if (empty($page)) return;
	return FileFactory::Backup($page)->has();
}
/**
 * convert_html.php
 */
function convert_html($lines)
{
	global $vars, $digest;
	static $contents_id = 0;

	// Set digest
	if (empty($digest) && !empty($vars['page'])){
		$digest = FileFactory::Wiki($vars['page'])->digest();
	}

	if (! is_array($lines)) $lines = explode("\n", $lines);

	return RendererFactory::factory($lines);
}
/**
 * file.php
 */
function get_source($page = NULL, $lock = TRUE, $join = FALSE)
{
	if (empty($page)) return;
	$wiki = FileFactory::Wiki($page);
	if (!$wiki->has()){
		return;
	}
	return $wiki->get($join, true);
}

// Get last-modified filetime of the page
function get_filetime($page)
{
	if (empty($page)) return;
	return FileFactory::Wiki($page)->time();
}

// Get physical file name of the page
function get_filename($page)
{
	if (empty($page)) return;
	return FileFactory::Wiki($page)->filename;
}

// Get elapsed date of the page
function get_pg_passage($page, $sw = TRUE)
{
	if (empty($page)) return;
	return FileFactory::Wiki($page)->passage($sw, false);
}

// Put a data(wiki text) into a physical file(diff, backup, text)
function page_write($page, $postdata, $notimestamp = FALSE)
{
	if (empty($page)) return;
	return FileFactory::Wiki($page)->set($postdata, $notimestamp);
}

// Get a list of related pages of the page
function links_get_related($page)
{
	return FileFactory::Wiki($page)->getRetaled();
}

// Update PKWK_MAXSHOW_CACHE itself (Add or renew about the $page) (Light)
// Use without $autolink
function lastmodified_add($update = '', $remove = '')
{
	FileUtility::set_recent($update, $remove);
}

// Re-create PKWK_MAXSHOW_CACHE (Heavy)
function put_lastmodified()
{
	FileUtility::get_recent(true);
}

// touch() with trying pkwk_chown()
function pkwk_touch_file($filename, $time = FALSE, $atime = FALSE)
{
	return FileFactory::Generic($filename)->touch($time, $atime);
}

// Last-Modified header
function header_lastmod($page = NULL)
{
	global $lastmod;

	if ($lastmod && is_page($page)) {
		pkwk_headers_sent();
		header('Last-Modified: ' .
			date('D, d M Y H:i:s', get_filetime($page)) . ' GMT');
	}
}

// Get a list of encoded files (must specify a directory and a suffix)
function get_existfiles($dir = DATA_DIR, $ext = '.txt')
{
	return FileUtility::get_exists($dir);
}

// Get a page list of this wiki
function get_existpages($dir = DATA_DIR, $ext = '.txt')
{
	return FileUtility::get_exists($dir);
}

/**
 * func.php
 */
// Show text formatting rules
function catrule()
{
	global $rule_page;

	$rule_wiki = FileFactory::Wiki($rule_page);
	if (! $rule_wiki->has()) {
		return '<p>Sorry, page \'' . htmlsc($rule_page) .
			'\' unavailable.</p>';
	} else {
		return $rule_wiki->render();
	}
}

function die_message($msg, $error_title='', $http_code = 500){
	return Utility::die_message($msg, $error_title, $http_code);
}


function ridirect($url = ''){
	return Utility::redirect($url);
}

// Have the time (as microtime)
function getmicrotime()
{
	list($usec, $sec) = explode(' ', microtime());
	return ((float)$sec + (float)$usec);
}

// Elapsed time by second
function elapsedtime()
{
	return sprintf('%01.03f', getmicrotime() - MUTIME);
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

// Hide <input type="(submit|button|image)"...>
function drop_submit($str)
{
	return preg_replace('/<input([^>]+)type="(submit|button|image)"/i',
		'<input$1type="hidden"', $str);
}

// Generate sorted "list of pages" XHTML, with page-reading hints
function page_list($pages = array('pagename.txt' => 'pagename'), $cmd = 'read', $withfilename = FALSE)
{
	return FileUtility::get_listing();
}

function is_url($str, $only_http = FALSE)
{
	return Utility::isUri($str, $only_http);
}

function is_interwiki($str)
{
	return Utility::isInterWiki($str);
}

function is_pagename($page)
{
	if (empty($page)) return false;
	return FileFactory::Wiki($page)->isValied();
}

// If the page exists
function is_page($page, $clearcache = FALSE)
{
	if (empty($page)) return false;
	return FileFactory::Wiki($page)->has();

}

function is_editable($page)
{
	if (empty($page)) return false;
	return FileFactory::Wiki($page)->isEditable();
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
function get_search_words($words, $do_escape = FALSE){
	return Search::get_search_words($words, $do_escape);
}

function do_search($word, $type = 'and', $non_format = FALSE, $base = ''){
	return Search::do_search($word, $type, $non_format, $base);
}

function is_freeze($page, $clearcache = FALSE)
{
	if (empty($page)) return false;
	return FileFactory::Wiki($page)->isFreezed();
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
	return Utility::encode($str);
}

// Decode page name
function decode($str)
{
	return Utility::decode($str);
}

// Handling $non_list
// $non_list will be preg_quote($str, '/') later.
function check_non_list($page = '')
{
	if (empty($page)) return false;
	return FileFactory::Wiki($page)->isHidden();
}

// Remove [[ ]] (brackets)
function strip_bracket($str)
{
	return Utility::stripBracket($str);
}

// Get absolute-URI of this script
function get_script_uri($path='')
{
	return Router::get_script_uri();
}

// Get absolute-URI of this script
function get_script_absuri()
{
	return Router::get_script_absuri();
}
// function get_cmd_uri($cmd='', $page='', $query='', $fragment='')
function get_cmd_uri($cmd='', $page='', $path_reference='rel', $query='', $fragment='')
{
	return Router::get_cmd_uri($cmd, $page, $path_reference, $query, $fragment);
}
// function get_page_uri($page, $query='', $fragment='')
function get_page_uri($page, $path_reference='rel', $query='', $fragment='')
{
	return Router::get_page_uri($page, $path_reference, $query, $fragment);
}
// Obsolete (明示指定用)
function get_cmd_absuri($cmd='', $page='', $query='', $fragment='')
{
	return Router::get_resolve_uri($cmd,$page,'full',$query,$fragment,0);
}
// Obsolete (明示指定用)
function get_page_absuri($page, $query='', $fragment='')
{
	return Router::get_resolve_uri('',$page,'full',$query,$fragment,0);
}
// Obsolete (ポカミス用)
function get_page_location_uri($page='', $query='', $fragment='')
{
	return Router::get_resolve_uri('',$page,'full',$query,$fragment,1);
}
// Obsolete (ポカミス用)
function get_location_uri($cmd='', $page='', $query='', $fragment='')
{
	return Router::get_resolve_uri($cmd,$page,'full',$query,$fragment,1);
}

function input_filter($param)
{
	return Utility::stripNullBytes($param);
}

// Sugar with default settings
function htmlsc($string = '', $flags = ENT_QUOTES, $charset = 'UTF-8')
{
	return Utility::htmlsc($string, $flags, $charset);
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

/**
 * fileplus.php
 */

// Get Ticket
function get_ticket($flush = FALSE)
{
	return Utility::getTicket($flush);
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
	$pages = FileUtility::get_exists();

	// Update autolink
//	if ( $autolink !== 0 ) {
//		PukiWiki\Lib\Renderer\AutoLinkPattern::get_pattern(-1,true);
//	}

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
*/
	return true;
}

// Move from file.php

function get_existpages_cache($dir, $ext){
	return FileUtility::get_exists($dir);
}

/**
 * html.php
 */
function make_line_rules($str){
	return Inline::setLineRules($str);
}


// Remove all HTML tags(or just anchor tags), and WikiName-speific decorations
function strip_htmltag($str, $all = TRUE)
{
	return Utility::stripHtmlTags($str, $all);
}

// Remove AutoLink marker with AutoLink itself
function strip_autolink($str)
{
	return Utility::stripAutoLink($str);
}

// Make a backlink. searching-link of the page name, by the page name, for the page name
function make_search($page)
{
	if (empty($page)) return;
	return '<a href="' . FileFactory::Wiki($page)->get_uri('related') . '">' . Utility::htmlsc($page) . '</a>';
}

// Make heading string (remove heading-related decorations from Wiki text)
function make_heading(& $str, $strip = TRUE)
{
	return Utility::setHeading($str, $strip);
}

// Separate a page-name(or URL or null string) and an anchor
// (last one standing) without sharp
function anchor_explode($page, $strict_editable = FALSE)
{
	return Utility::explodeAnchor($page, $strict_editable);
}


/**
 * links.php
 */
function links_get_related_db($page)
{
	if (empty($page)) return false;
	$relational = new Relational($page);
	return $relational->get_related();
}

// Init link cache (Called from link plugin)
function links_init()
{
	$links = new Relational('');
	return $links->init();
}

// Update link-relationships between pages
function links_update($page)
{
	if (empty($page)) return false;
	$links = new Relational($page);
	return $links->update();
}
/**
 * make_link.php
 */
use PukiWiki\Lib\Renderer\InlineFactory;
// Hyperlink decoration
function make_link($string, $page = '')
{
	return InlineFactory::factory($string, $page);
}


// Make hyperlink for the page
function make_pagelink($page, $alias = '', $anchor = '', $refer = '', $isautolink = FALSE)
{
	return Inline::setAutoLink($page, $alias, $anchor, $refer, $isautolink);
}

// Resolve relative / (Unix-like)absolute path of the page
function get_fullname($name, $refer)
{
	global $defaultpage;

	// 'Here'
	if ($name == '' || $name == './') return $refer;

	// Absolute path
	if ($name{0} == '/') {
		$name = substr($name, 1);
		return ($name == '') ? $defaultpage : $name;
	}

	// Relative path from 'Here'
	if (substr($name, 0, 2) == './') {
		$arrn    = preg_split('#/#', $name, -1, PREG_SPLIT_NO_EMPTY);
		$arrn[0] = $refer;
		return join('/', $arrn);
	}

	// Relative path from dirname()
	if (substr($name, 0, 3) == '../') {
		$arrn = preg_split('#/#', $name,  -1, PREG_SPLIT_NO_EMPTY);
		$arrp = preg_split('#/#', $refer, -1, PREG_SPLIT_NO_EMPTY);

		while (! empty($arrn) && $arrn[0] == '..') {
			array_shift($arrn);
			array_pop($arrp);
		}
		$name = ! empty($arrp) ? join('/', array_merge($arrp, $arrn)) :
			(! empty($arrn) ? $defaultpage . '/' . join('/', $arrn) : $defaultpage);
	}

	return $name;
}

/**
 * timezone.php
 */
//set_time
function set_time()
{
	global $language, $use_local_time;

	if ($use_local_time) {
		list($zone, $zonetime) = set_timezone( DEFAULT_LANG );
	} else {
		list($zone, $zonetime) = set_timezone( $language );
		list($l_zone, $l_zonetime) = get_localtimezone();
		if ($l_zonetime != '' && $zonetime != $l_zonetime) {
			$zone = $l_zone;
			$zonetime = $l_zonetime;
		}
	}

	foreach(array('UTIME'=>time(),'MUTIME'=>getmicrotime(),'ZONE'=>$zone,'ZONETIME'=>$zonetime) as $key => $value ){
		defined($key) or define($key,$value);
	}
}

/*
 * set_timezone
 *
 */
function set_timezone($lang='')
{
	if (empty($lang)) {
		return array('UTC', 0);
	}
	$l = accept_language::split_locale_str( $lang );

	// When the name of a country is uncertain (国名が不明な場合)
	if (empty($l[2])) {
		$obj_l2c = new lang2country();
		$l[2] = $obj_l2c->get_lang2country($l[1]);
		if (empty($l[2])) {
			return array('UTC', 0);
		}
	}

	$obj = new TimeZone();
	$obj->set_datetime(UTIME); // Setting at judgment time. (判定時刻の設定)
	$obj->set_country($l[2]); // The acquisition country is specified. (取得国を指定)

	// With the installation country in case of the same
	// 設置者の国と同一の場合
	if ($lang == DEFAULT_LANG) {
		if (defined('DEFAULT_TZ_NAME')) {
			$obj->set_tz_name(DEFAULT_TZ_NAME);
		}
	}

	list($zone, $zonetime) = $obj->get_zonetime();

	if ($zonetime == 0 || empty($zone)) {
		return array('UTC', 0);
	}

	return array($zone, $zonetime);
}

function get_localtimezone()
{
	if (isset($_COOKIE['timezone'])) {
		$tz = $_COOKIE['timezone'];
	} else {
		return array('','');
	}

	$tz = trim($tz);

	$offset = substr($tz,0,1);
	switch ($offset) {
	case '-':
	case '+':
		$tz = substr($tz,1);
		break;
	default:
		$offset = '+';
	}

	$h = substr($tz,0,2);
	$i = substr($tz,2,2);

	$zonetime = ($h * 3600) + ($i * 60);
	$zonetime = ($offset == '-') ? $zonetime * -1 : $zonetime;

	return array($offset.$tz, $zonetime);
}
