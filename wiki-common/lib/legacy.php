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
use PukiWiki\Lib\Renderer\RendererFactory;
use PukiWiki\Lib\Renderer\Inline\Inline;
use PukiWiki\Lib\Relational;
use PukiWiki\Lib\Search;
use Zend\Uri\UriFactory;

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
	return FileFactory::Wiki($page)->getTime();
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
	// global $maxshow, $whatsnew, $autolink;
	global $maxshow, $whatsnew, $autolink, $autobasealias;
	global $cache;

	// AutoLink implimentation needs everything, for now
	//if ($autolink) {
	if ($autolink || $autobasealias) {
		put_lastmodified(); // Try to (re)create ALL
		return;
	}

	if (($update == '' || check_non_list($update)) && $remove == '')
		return; // No need

	// Check cache exists
	if (! $cache['wiki']->hasItem(PKWK_MAXSHOW_CACHE)){
		put_lastmodified(); // Try to (re)create ALL
		return;
	}else{
		$recent_pages = $cache['wiki']->getItem(PKWK_MAXSHOW_CACHE);
	}

	// Remove if it exists inside
	if (isset($recent_pages[$update])) unset($recent_pages[$update]);
	if (isset($recent_pages[$remove])) unset($recent_pages[$remove]);

	// Add to the top: like array_unshift()
	// if ($update != '')
	if ($update != '' && $update != $whatsnew && ! check_non_list($update))
		$recent_pages = array($update => get_filetime($update)) + $recent_pages;

	// Check
	$abort = count($recent_pages) < $maxshow;

	// Update cache
	$cache['wiki']->setItem(PKWK_MAXSHOW_CACHE, $recent_pages);

	if ($abort) {
		put_lastmodified(); // Try to (re)create ALL
		return;
	}

	// ----
	// Update the page 'RecentChanges'

	$recent_pages = array_splice($recent_pages, 0, $maxshow);
	$file = get_filename($whatsnew);

	// Open
	pkwk_touch_file($file);
	$fp = fopen($file, 'r+') or
		die_message('Cannot open ' . htmlsc($whatsnew));
	set_file_buffer($fp, 0);
	flock($fp, LOCK_EX);

	// Recreate
	ftruncate($fp, 0);
	rewind($fp);

	foreach ($recent_pages as $_page=>$time)
		fputs($fp, '- &epoch('.$time.');' .
			' - ' . '[[' . htmlsc($_page) . ']]' . "\n");

	fputs($fp, '#norelated' . "\n"); // :)

	ignore_user_abort($last);	// Plus!

	flock($fp, LOCK_UN);
	fclose($fp);
}

// Re-create PKWK_MAXSHOW_CACHE (Heavy)
function put_lastmodified()
{
	// global $maxshow, $whatsnew, $autolink;
	global $maxshow, $whatsnew, $autolink, $autobasealias;
	global $cache;

	// if (PKWK_READONLY) return; // Do nothing
	if (Auth::check_role('readonly')) return; // Do nothing

	// Get WHOLE page list
	$pages = get_existpages();

	// Check ALL filetime
	$recent_pages = array();
	foreach($pages as $page)
		if ($page !== $whatsnew && ! check_non_list($page))
			$recent_pages[$page] = FileFactory::Wiki($page)->getTime();

	// Sort decending order of last-modification date
	arsort($recent_pages, SORT_NUMERIC);

	// Cut unused lines
	// BugTrack2/179: array_splice() will break integer keys in hashtable
	$count   = $maxshow + PKWK_MAXSHOW_ALLOWANCE;
	$_recent = array();
	foreach($recent_pages as $key=>$value) {
		unset($recent_pages[$key]);
		$_recent[$key] = $value;
		if (--$count < 1) break;
	}
	$recent_pages = & $_recent;

	// Save to recent cache data
	$cache['wiki']->setItem(PKWK_MAXSHOW_CACHE, $recent_pages);

	// Create RecentChanges
	foreach (array_keys($recent_pages) as $page) {
		$buffer[] = '-&epoch(' . $recent_pages[$page] . '); - [[' . htmlsc($page) . ']]';
	}
	$file = FileFactory::Wiki($whatsnew);
	$file->set(join("\n",$buffer));

	// For AutoLink
	if ($autolink){
		$cache['wiki']->setItem(PKWK_AUTOLINK_REGEX_CACHE, get_autolink_pattern($pages, $autolink));
	}

	// AutoBaseAlias (Plus!)
	if ($autobasealias) {
		$cache['wiki']->setItem(PKWK_AUTOBASEALIAS_CACHE, get_autobasealias($pages));
	}
}

// touch() with trying pkwk_chown()
function pkwk_touch_file($filename, $time = FALSE, $atime = FALSE)
{
	return FileFactory::Generic($filename)->touch($time, $atime);
}

// Get PageReading(pronounce-annotated) data in an array()
function get_readings()
{
	global $pagereading_enable, $pagereading_config_page, $mecab_path;
	global $pagereading_config_dict;

	$pages = get_existpages();

	$readings = array();
	foreach ($pages as $page)
		$readings[$page] = '';

	$deletedPage = FALSE;
	$matches = array();
	foreach (FileFactory::Wiki($pagereading_config_page)->source() as $line) {
		$line = chop($line);
		if(preg_match('/^-\[\[([^]]+)\]\]\s+(.+)$/', $line, $matches)) {
			if(isset($readings[$matches[1]])) {
				// This page is not clear how to be pronounced
				$readings[$matches[1]] = $matches[2];
			} else {
				// This page seems deleted
				$deletedPage = TRUE;
			}
		}
		$dict[] = $line;
	}
	pr($dict);

	// If enabled ChaSen/KAKASI execution
	if($pagereading_enable) {

		// Check there's non-clear-pronouncing page '
		$unknownPage = FALSE;
		foreach ($readings as $page => $reading) {
			if(empty($reading)) {
				$unknownPage = TRUE;
				break;
			}
		}

		if($unknownPage) {
			if (file_exists($mecab_path)){
				foreach ($readings as $page => $reading) {
					if(!empty($reading)) continue;
					$readings[$page] = mecab_reading($page);
				}
			}else{
				$patterns = $replacements = $matches = array();
				foreach (FileFactory::Wiki($pagereading_config_dict)->source() as $line) {
					$line = chop($line);
					if(preg_match('|^ /([^/]+)/,\s*(.+)$|', $line, $matches)) {
						$patterns[]     = $matches[1];
						$replacements[] = $matches[2];
					}
				}
				foreach ($readings as $page => $reading) {
					if(!empty($reading)) continue;

					$readings[$page] = $page;
					foreach ($patterns as $no => $pattern)
						$readings[$page] = mb_convert_kana(mb_ereg_replace($pattern,
							$replacements[$no], $readings[$page]), 'aKCV');
				}
			}
		}

		if($unknownPage || $deletedPage) {
			asort($readings, SORT_STRING); // Sort by pronouncing(alphabetical/reading) order
			$body = '';
			foreach ($readings as $page => $reading)
				$body .= '-[[' . $page . ']] ' . $reading . "\n";

			$w->set($body);
		}
	}

	// Pages that are not prounouncing-clear, return pagenames of themselves
	foreach ($pages as $page) {
		if ( empty($readings[$page]) ) $readings[$page] = $page;
	}

	return $readings;
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
	$aryret = array();
	$pattern = '/^(?:[0-9A-F]{2})+' . preg_quote($ext, '/') . '$/';

	$handle = opendir($dir);
	if ($handle) {
		while (false !== ($entry = readdir($handle))) {
			if (preg_match($pattern, $entry)) {
				$aryret[] = $dir . $entry;
			}
		}
		closedir($handle);
	}else{
		die_message($dir . ' is not found or not readable.');
	}

	$pages[$dir][$ext] = $aryret;
	return $aryret;
}

// Get a page list of this wiki
function get_existpages($dir = DATA_DIR, $ext = '.txt')
{
	// get_existpages を３行で軽くする
	// http://lsx.sourceforge.jp/?Hack%2Fget_existpages
	// ただし、Adv.の場合ファイルに別途キャッシュしているのであまり意味ないかも・・・。
	static $pages;
	if (isset($pages[$dir][$ext])) return $pages[$dir][$ext];

	$aryret = array();
	$pattern = '/^((?:[0-9A-F]{2})+)' . preg_quote($ext, '/') . '$/';

	$matches = array();
	$handle = opendir($dir);
	if ($handle) {
		while (false !== ($entry = readdir($handle))) {
			if (preg_match($pattern, $entry, $matches)) {
				$aryret[$entry] = decode($matches[1]);
			}
		}
		closedir($handle);
	}else{
		die_message($dir . ' is not found or not readable.');
	}
	$pages[$dir][$ext] = $aryret;

	return $aryret;
}

/**
 * func.php
 */

function is_url($str, $only_http = FALSE)
{
	return Utility::is_uri($str, $only_http);
}

function is_interwiki($str)
{
	return Utility::is_interwiki($str);
}

function is_pagename($page)
{
	if (empty($page)) return false;
	return FileFactory::Wiki($page)->is_valied();
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
	return FileFactory::Wiki($page)->is_editable();
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
	return FileFactory::Wiki($page)->is_freezed();
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
	return FileFactory::Wiki($page)->is_hidden();
}

// Remove [[ ]] (brackets)
function strip_bracket($str)
{
	return Utility::strip_bracket($str);
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
	return Utility::input_filter($param);
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
 * html.php
 */
function make_line_rules($str){
	global $line_rules;
	static $pattern, $replace;

	if (! isset($pattern)) {
		$pattern = array_map(create_function('$a',
			'return \'/\' . $a . \'/\';'), array_keys($line_rules));
		$replace = array_values($line_rules);
		unset($line_rules);
	}

	return preg_replace($pattern, $replace, $str);
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
use PukiWiki\Lib\Renderer\InlineConverter;
use PukiWiki\Lib\Renderer\InlineFactory;
// Hyperlink decoration
function make_link($string, $page = '')
{
	global $vars;
	static $converter;

	if (! isset($converter)){ $converter = new InlineConverter(); }

	$clone = $converter->get_clone($converter);

	return $clone->convert($string, !empty($page) ? $page : $vars['page']);

}


// Make hyperlink for the page
function make_pagelink($page, $alias = '', $anchor = '', $refer = '', $isautolink = FALSE)
{
	return Inline::make_pagelink($page, $alias, $anchor, $refer, $isautolink);
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
