<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone
// $Id: auth.php,v 1.0 2012/09/25 15:31:00 Logue Exp $
// Copyright (C)
//   2012      PukiWiki Advance Developers Team
// License: GPL v2 or (at your option) any later version
//
// old functions
use PukiWiki\Auth\Auth;
use PukiWiki\Utility;
use PukiWiki\Router;
use PukiWiki\Renderer\RendererFactory;
use PukiWiki\Renderer\Inline\Inline;
use PukiWiki\Renderer\PluginRenderer;
use PukiWiki\Search;
use PukiWiki\Time;
use PukiWiki\Factory;
use PukiWiki\Recent;
use PukiWiki\Listing;

/**************************************************************************************************/
/**
 * auth.php
 */
function pkwk_login($pass = '')
{
	return Auth::login($pass);
}

// Basic-auth related ----

// Check edit-permission
function check_editable($page, $auth_flag = TRUE, $exit_flag = TRUE)
{
	return Factory::Wiki($page)->isEditable($auth_flag);
}
// Check read-permission
function check_readable($page, $auth_flag = TRUE, $exit_flag = TRUE)
{
	return Factory::Wiki($page)->isReadable($auth_flag);
}
/**************************************************************************************************/
/**
 * backup.php
 */
function get_backup($page, $age = 0)
{
	//trigger_error('get_backup($page, $age) is deprecated. Use FileFactory::Backup($page)->getBackup($age)', E_USER_DEPRECATED);
	if (empty($page)) return;
	return Factory::Wiki($page)->getBackup($age);
}

function _backup_file_exists($page){
	//trigger_error('_backup_file_exists($page) is deprecated. Use FileFactory::Backup($page)->has()', E_USER_DEPRECATED);
	if (empty($page)) return;
	return Factory::Wiki($page)->hasBackup();
}
/**************************************************************************************************/
/**
 * convert_html.php
 */
function convert_html($lines)
{
	//trigger_error('convert_html($lines) is deprecated. Use RendererFactory::factory($lines)', E_USER_DEPRECATED);
	global $vars, $digest;
	static $contents_id = 0;

	// Set digest
	if (empty($digest) && !empty($vars['page'])){
		$digest = Factory::Wiki($vars['page'])->digest();
	}

	if (! is_array($lines)) $lines = explode("\n", $lines);

	return RendererFactory::factory($lines);
}
/**************************************************************************************************/
/**
 * file.php
 */
function get_source($page = NULL, $lock = TRUE, $join = false)
{
	//trigger_error('get_source($page, $lock, $join) is deprecated. Use WikiFactory::Wiki($page)->get($join)', E_USER_DEPRECATED);
	if (empty($page)) return;
	$wiki = Factory::Wiki($page);
	if (!$wiki->has()){
		return;
	}
	return $wiki->get($join, true);
}

// Get last-modified filetime of the page
function get_filetime($page)
{
	//trigger_error('get_filetime is deprecated. use WikiFactory::Wiki($page)->time().', E_USER_DEPRECATED );
	if (empty($page)) return;
	return Factory::Wiki($page)->time();
}

// Get physical file name of the page
function get_filename($page)
{
	//trigger_error('get_filename is deprecated. use WikiFactory::Wiki($page)->filename().', E_USER_DEPRECATED );
	if (empty($page)) return;
	return Factory::Wiki($page)->wiki->filename;
}

// Get elapsed date of the page
function get_pg_passage($page, $sw = TRUE)
{
	//trigger_error('get_pg_passage($page) is deprecated. use WikiFactory::Wiki($page)->passage($sw).', E_USER_DEPRECATED );
	if (empty($page)) return;
	return Factory::Wiki($page)->wiki->passage($sw, false);
}

// Put a data(wiki text) into a physical file(diff, backup, text)
function page_write($page, $postdata, $notimestamp = FALSE)
{
	//trigger_error('page_write($page, $postdata, $notimestamp) is deprecated. use WikiFactory::Wiki($page)->set($postdata, $notimestamp).', E_USER_DEPRECATED );
	if (empty($page)) return;
	return Factory::Wiki($page)->set($postdata, $notimestamp);
}

// Get a list of related pages of the page
function links_get_related($page)
{
	//trigger_error('links_get_related($page) is deprecated. use WikiFactory::Wiki($page)->getRetaled().', E_USER_DEPRECATED );
	return Factory::Wiki($page)->getRetaled();
}

// Re-create PKWK_MAXSHOW_CACHE (Heavy)
function put_lastmodified()
{
	//trigger_error('put_lastmodified() is deprecated. use FileUtility::get_recent(true).', E_USER_DEPRECATED );
	Recent::get(true);
}

// touch() with trying pkwk_chown()
function pkwk_touch_file($filename, $time = FALSE, $atime = FALSE)
{
	//trigger_error('pkwk_touch_file($filename, $time, $atime) is deprecated. use FileFactory::Generic($filename)->touch($time, $atime).', E_USER_DEPRECATED );
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

// Get a page list of this wiki
function get_existpages($dir = DATA_DIR, $ext = '.txt')
{
	return Listing::get('wiki');
}

/**************************************************************************************************/
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


// Move from file.php

function get_existpages_cache($dir, $ext){
	return Listing::get();
}

/**************************************************************************************************/
/**
 * func.php
 */
// Show text formatting rules
function catrule()
{
	global $rule_page;

	$rule_wiki = Factory::Wiki($rule_page);
	if (! $rule_wiki->has()) {
		return '<p>Sorry, page \'' . Utility::htmlsc($rule_page) .
			'\' unavailable.</p>';
	} else {
		return $rule_wiki->render();
	}
}

function die_message($msg, $error_title='', $http_code = 500){
	return Utility::dieMessage($msg, $error_title, $http_code);
}

function get_passage($time){
	return Time::passage($time);
}
function ridirect($url = ''){
	return Utility::redirect($url);
}

// Have the time (as microtime)
function getmicrotime()
{
	return Time::getMicroTime();
}

// Elapsed time by second
function elapsedtime()
{
	return sprintf('%01.03f', getmicrotime() - MUTIME);
}

// Get the date
function get_date($format, $timestamp = NULL)
{
	return Time::getZoneTimeDate($format, $timestamp);
}

function get_zonetime_offset($zonetime)
{
	return Time::getZoneTimeOffset($zonetime);
}

// Format date string
function format_date($val, $paren = FALSE, $format = null)
{
	return Time::format($val, $paren, $format);
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
	return Listing::get();
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
	return Factory::Wiki($page)->isValied();
}

// If the page exists
function is_page($page, $clearcache = FALSE)
{
	if (empty($page)) return false;
	return Factory::Wiki($page)->has();

}

function is_editable($page)
{
	if (empty($page)) return false;
	return Factory::Wiki($page)->isEditable();
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
	return Factory::Wiki($page)->isFreezed();
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
	return Factory::Wiki($page)->isHidden();
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
	if (empty($page)) return null;
	return  Router::get_cmd_uri('read', $page, $path_reference, $query, $fragment);
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

/**************************************************************************************************/
/**
 * funcplus.php
 */
// SPAM check
function is_spampost($array, $count=0)
{
	return Utility::isSpamPost($array, $count);
}

function is_ignore_page($page)
{
	return Utility::isSpamPost($array, $count);
}
// インクルードで余計なものはソースから削除する
function convert_filter($str)
{
	return Utility::replaceFilter($str);
}

function showtaketime(){
	return Time::getTakeTime();
}

// same as 'basename' for page
function basepagename($str)
{
	return Router::getBasePageName($str);
}

function get_remoteip()
{
	return Utility::getRemoteIp();
}

// タグの追加
function open_uri_in_new_window($anchor, $which = '')
{
	throw new Exception('open_uri_in_new_window() is discontinued. Use similar function Inline::setLink();');
}

// SPAM logging
function honeypot_write()
{
	Utility::dump();
}

function get_baseuri($path='')
{
	global $script;

	// RFC2396,RFC3986 : relativeURI = ( net_path | abs_path | rel_path ) [ "?" query ]
	//				   absoluteURI = scheme ":" ( hier_part | opaque_part )
	$ret = '';

	switch($path) {
	case 'net': // net_path	  = "//" authority [ abs_path ]
		$parsed_url = parse_url(get_script_absuri());
		$pref = '//';
		if (isset($parsed_url['user'])) {
			$ret .= $pref . $parsed_url['user'];
			$pref = '';
			$ret .= (isset($parsed_url['pass'])) ? ':'.$parsed_url['pass'] : '';
			$ret .= '@';
		}
		if (isset($parsed_url['host'])) {
			$ret .= $pref . $parsed_url['host'];
			$pref = '';
		}
		$ret .= (isset($parsed_url['port'])) ? ':'.$parsed_url['port'] : '';
	case 'abs': // abs_path	  = "/"  path_segments
		if ($path === 'abs') $parsed_url = parse_url(get_script_absuri());
		if (isset($parsed_url['path']) && ($pos = strrpos($parsed_url['path'], '/')) !== false) {
			$ret .= substr($parsed_url['path'], 0, $pos + 1);
		} else {
			$ret .= '/';
		}
		break;
	case 'rel': // rel_path	  = rel_segment [ abs_path ]
		if (is_url($script, true)) {
			$ret = './';
		} else {
			$parsed_url = parse_url($script);
			if (isset($parsed_url['path']) && ($pos = strrpos($parsed_url['path'], '/')) !== false) {
				$ret .= substr($parsed_url['path'], 0, $pos + 1);
			}
		}
		break;
	case 'full':
	default:
		$absoluteURI = get_script_absuri();
		$ret = substr($absoluteURI, 0, strrpos($absoluteURI, '/')+1);
		break;
	}

	return $ret;
}


// インラインパラメータのデータを１行毎に分割する
function line2array($x)
{
	$x = preg_replace(
		array("[\\r\\n]","[\\r]"),
		array("\n","\n"),
		$x
	); // 行末の統一
	return explode("\n", $x);
}


function tbl2dat($data)
{
	$x = explode('|',$data);
	if (substr($data,0,1) == '|') array_shift($x);
	if (substr($data,-1)  == '|') array_pop($x);
	return $x;
}

function is_header($x) { return ( substr($x,-2) == '|h') ? true : false; }

function change_uri($cmd='',$force=0)
{
	global $script, $script_abs, $absolute_uri, $script_directory_index;
	static $onece, $bkup, $bkup_script, $bkup_script_abs, $bkup_absolute_uri;
	static $target_fields = array('script'=>'bkup_script','script_abs'=>'bkup_script_abs','absolute_uri'=>'bkup_absolute_uri');

	if (! isset($bkup)) {
		$bkup = true;
		foreach($target_fields as $org=>$bkup) {
			if (! isset($$bkup) && isset($org)) $$bkup = $$org;
		}
	}

	if (isset($onece)) return;

	switch($cmd) {
	case 'reset':
		foreach($target_fields as $org=>$bkup) {
			if (isset($$bkup)) {
				$$org = $$bkup;
			} else {
				if (isset($$org)) unset($$org);
			}
		}
		return;
	case 'net':
	case 'abs':
	case 'rel':
		change_uri('reset');
		$absolute_uri = 0;
		break;
	default:
		$absolute_uri = 1;
	}

	$script = get_baseuri($cmd);
	if (! isset($script_directory_index)) $script .= init_script_filename();
	if ($force === 1) $onece = 1;
	return;
}

function init_script_filename()
{
	// $script にファイル名が設定されていれば、それを求める
	$script = init_script_uri('',1);
	$pos = strrpos($script, '/');
	if ($pos !== false) {
		return substr($script, $pos + 1);
	}
	return '';
}

/**************************************************************************************************/
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
	return '<a href="' . Factory::Wiki($page)->uri('related') . '">' . Utility::htmlsc($page) . '</a>';
}

// Make heading string (remove heading-related decorations from Wiki text)
function make_heading(& $str, $strip = TRUE)
{
	return Utility::setHeading($str, $strip);
}

/**************************************************************************************************/
/**
 * make_link.php
 */
use PukiWiki\Renderer\InlineFactory;
// Hyperlink decoration
function make_link($string, $page = '')
{
	return InlineFactory::Wiki($string, $page);
}


// Make hyperlink for the page
function make_pagelink($page, $alias = '', $anchor = '', $refer = '', $isautolink = FALSE)
{
	return Inline::setAutoLink($page, $alias, $anchor, $refer, $isautolink);
}

// Resolve relative / (Unix-like)absolute path of the page
function get_fullname($name, $refer)
{
	return Utility::getPageName($name, $refer);
}

function set_time()
{
	Time::init();
}
function set_timezone($lang='')
{
	return Time::setTimeZone($lang);
}

function get_localtimezone()
{
	return Time::getTimeZoneLocal();
}

/**************************************************************************************************/
/**
 * plugin.php
 */
// Set global variables for plugins
function set_plugin_messages($messages)
{
	PluginRenderer::setPluginMessages($messages);
}

// Same as getopt for plugins
function get_plugin_option($args, &$params, $tolower=TRUE, $separator=':')
{
	return PluginRenderer::getPluginOption($args, $params, $tolower, $separator);
}

// Check plugin '$name' is here
function exist_plugin($name)
{
	$plugin = PluginRenderer::getPlugin($name);
	return $plugin['loaded'];
}

// Check if plguin API exists
function exist_plugin_function($name, $method)
{
	$plugin = PluginRenderer::getPlugin($name);
	return $plugin['method'][$method];
}

// Check if plugin API 'action' exists
function exist_plugin_action($name) {
	return exist_plugin_function($name, 'action');
}
// Check if plugin API 'convert' exists
function exist_plugin_convert($name) {
	return exist_plugin_function($name, 'convert');
}
// Check if plugin API 'inline' exists
function exist_plugin_inline($name) {
	return exist_plugin_function($name, 'inline');
}

// Call 'init' function for the plugin
// NOTE: Returning FALSE means "An erorr occurerd"
function do_plugin_init($name)
{
	return PluginRenderer::executePluginInit($name);
}

// Call API 'action' of the plugin
function do_plugin_action($name)
{

	return PluginRenderer::executePluginAction($name);
}

// Call API 'convert' of the plugin
function do_plugin_convert($name, $args = '')
{
	return PluginRenderer::executePluginBlock($name, $args);
}

// Call API 'inline' of the plugin
function do_plugin_inline($name, $args='', $body='')
{
	return PluginRenderer::executePluginInline($name, $args, $body);
}

// FIXME:進捗状況表示（attachプラグインのpcmd=progressで出力）
function get_upload_progress(){
	global $vars;
	$key = ini_get('session.upload_progress.prefix'). PKWK_WIKI_NAMESPACE;
	header('Content-Type: application/json; charset='.CONTENT_CHARSET);
	echo Zend\Json\Json::encode( isset($_SESSION[$key]) ? $_SESSION[$key] : null );

	exit;
}

/**************************************************************************************************/
/**
 * proxy.php
 */
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

