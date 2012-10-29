<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: fileplus.php,v 1.2.9 2012/04/29 10:08:00 Logue Exp $
// Copyright (C)
//   2010-2012 PukiWiki Advance Team
//   2005-2006,2009 PukiWiki Plus! Team
// License: GPL v2 or (at your option) any later version
//
// File related functions - extra functions

// Marged from PukioWikio post.php
defined('POSTID_PREFIX')	or define('POSTID_PREFIX', 'PostId-');
defined('POSTID_EXPIRE')	or define('POSTID_EXPIRE', 3600);	// 60*60 = 1hour

// Ticket file
defined('PKWK_TICKET_CACHE')	or define('PKWK_TICKET_CACHE', 'ticket');

// Get Ticket
function get_ticket($newticket = FALSE)
{
	global $cache;
	if ($cache->hasItem(PKWK_TICKET_CACHE) && $newticket !== TRUE) {
		$ticket = $cache->getItem(PKWK_TICKET_CACHE);
	}else{
		$ticket = md5(mt_rand());
		$cache->setItem(PKWK_TICKET_CACHE, $ticket);
	}
	return $ticket;
}

// Get EXIF data
function get_exif_data($file)
{
	if (!extension_loaded('exif')) { return FALSE; }
	if (!function_exists('exif_read_data')) { return FALSE; }
	$exif_rawdata = @exif_read_data($file);
	return $exif_rawdata;
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
		$cache->flush();
	}
	
	// Update page list
	if (! $cache->hasItem(PKWK_EXISTS_PREFIX.'wiki')){
		$pages = get_existpages();
		$cache->setItem(PKWK_EXISTS_PREFIX.'wiki', $pages);
	}else{
		$pages = $cache->getItem(PKWK_EXISTS_PREFIX.'wiki');
		$cache->touchItem(PKWK_EXISTS_PREFIX.'wiki');
	}

	// Update attach list
	if (! $cache->hasItem(PKWK_EXISTS_PREFIX.'attach') ){
		$cache->setItem(PKWK_EXISTS_PREFIX.'attach', get_attachfiles());
	}
	
	// Update AutoAliasName
	if ($autoalias !== 0&& (! $cache->hasItem(PKWK_AUTOALIAS_REGEX_CACHE) || $page === $aliaspage) ) {
		$aliases = get_autoaliases();

		if (empty($aliases) ) {
			// Remove
			$cache->removeItem(PKWK_AUTOALIAS_REGEX_CACHE);
		} else {
			// Create or Update
			$cache->setItem(PKWK_AUTOALIAS_REGEX_CACHE, get_autolink_pattern(array_keys($aliases)) );
		}
	}

	// Update AutoGlossary
	if ($autoglossary !== 0 && (! $cache->hasItem(PKWK_GLOSSARY_REGEX_CACHE) || $page === $glossarypage)) {
		$words = get_autoglossaries();
		if (empty($words) ) {
			// Remove
			$cache->removeItem(PKWK_GLOSSARY_REGEX_CACHE);
		} else {
			// Create or Update
			$cache->setItem(PKWK_GLOSSARY_REGEX_CACHE, get_glossary_pattern(@array_keys($words)) );
		}
	}
	
	// Update autolink
	if ($autolink !== 0 && ! $cache->hasItem(PKWK_AUTOLINK_REGEX_CACHE) ) {
		$cache->setItem(PKWK_AUTOLINK_REGEX_CACHE, get_autolink_pattern($pages, $autolink));
	}
	
	// Update AutoBaseAlias
	if ($autobasealias !== 0 && ! $cache->hasItem(PKWK_AUTOBASEALIAS_CACHE) ) {
		$basealiases = get_autobasealias($pages);
		if (empty($basealiase) ) {
			// Remove
			$cache->removeItem(PKWK_AUTOBASEALIAS_CACHE);
		} else {
			// Create or Update
			$cache->setItem(PKWK_AUTOBASEALIAS_CACHE, $basealiases );
		}
	}
	
	// Update rel and ref cache
	if ($force && $page == '') {
		links_init();
	}else {
		links_update($page);
	}

	// Update recent cache
	if (! $cache->hasItem(PKWK_MAXSHOW_CACHE)) put_lastmodified();
}

// Move from file.php

function get_existpages_cache($dir, $ext){
	global $cache;
	
	switch($dir){
		case DATA_DIR: $func = 'wiki'; break;
		case UPLOAD_DIR: $func = 'attach'; break;
		case COUNTER_DIR: $func = 'counter'; break;
		case BACKUP_DIR: $func = 'backup'; break;
		default: $func = encode($dir.$ext);
	}
	// Update page list
	if (! $cache->hasItem(PKWK_EXISTS_PREFIX.$func)){
		$pages = get_existpages($dir, $ext);
		$cache->setItem(PKWK_EXISTS_PREFIX.$func, $pages);
	}else{
		$pages = $cache->getItem(PKWK_EXISTS_PREFIX.$func);
		$cache->touchItem(PKWK_EXISTS_PREFIX.$func);
	}
	return $pages;
}

function get_attachfiles($page = '')
{
	$dir = opendir(UPLOAD_DIR) or
		die('directory ' . UPLOAD_DIR . ' is not exist or not readable.');
	$retval = array();

	if ($page !== '') {
		$page_pattern = preg_quote(encode($page), '/');
		$pattern = "/^({$page_pattern})_((?:[0-9A-F]{2})+)$/";
	}else{
		$pattern = "/^((?:[0-9A-F]{2})+)_((?:[0-9A-F]{2})+)$/";
	}

	if ($handle = opendir(UPLOAD_DIR)) {
		while (false !== ($entry = readdir($handle))) {
			if (($entry !== '.') && ($entry !== '..')) continue;
			$matches = array();
			
			if (! preg_match($pattern, $entry, $matches)) continue; // all page

			// [page][file] = array(time,size);
			$filepath = realpath(UPLOAD_DIR.$entry);
			$_page = decode($matches[1]);
			$_file = decode($matches[2]);
			$retval[$_page][$_file] = array(
				'time'=>filemtime($filepath),
				'size'=>filesize($filepath)
			);
		}
		closedir($handle);
	}

	return $retval;
}

function get_this_time_links($post,$diff)
{
	$links = array();
	$post_links = (array)replace_plugin_link2null($post);
	$diff_links = (array)get_link_list($diff);

	foreach($diff_links as $d) {
		foreach($post_links as $p) {
			if ($p == $d) {
				$links[] = $p;
				break;
			}
		}
	}
	unset($post_links, $diff_links);
	return $links;
}

function replace_plugin_link2null($data)
{
	global $exclude_link_plugin;

	$pattern = $replacement = array();
	foreach($exclude_link_plugin as $plugin) {
		$pattern[] = '/^#'.$plugin.'\(/i';
		$replacement[] = '#null(';
	}

	$exclude = preg_replace($pattern,$replacement, explode("\n", $data));
	$html = convert_html($exclude);
	preg_match_all('#href="(https?://[^"]+)"#', $html, $links, PREG_PATTERN_ORDER);
	$links = array_unique($links[1]);
	unset($except, $html);
	return $links;
}

function get_link_list($diffdata)
{
	$links = array();

	list($plus, $minus) = get_diff_lines($diffdata);

	// Get URLs from <a>(anchor) tag from convert_html()
	$plus  = convert_html($plus); // WARNING: heavy and may cause side-effect
	preg_match_all('#href="(https?://[^"]+)"#', $plus, $links, PREG_PATTERN_ORDER);
	$links = array_unique($links[1]);

	// Reject from minus list
	if ($minus !== '') {
		$links_m = array();
		$minus = convert_html($minus); // WARNING: heavy and may cause side-effect
		preg_match_all('#href="(https?://[^"]+)"#', $minus, $links_m, PREG_PATTERN_ORDER);
		$links_m = array_unique($links_m[1]);

		$links = array_diff($links, $links_m);
	}

	unset($plus,$minus);

	// Reject own URL (Pattern _NOT_ started with '$script' and '?')
	$links = preg_grep('/^(?!' . preg_quote(get_script_absuri(), '/') . '\?)./', $links);

	// No link, END
	if (! is_array($links) || empty($links)) return;

	return $links;
}

function get_diff_lines($diffdata)
{
	$_diff = explode("\n", $diffdata);
	$plus  = join("\n", preg_replace('/^\+/', '', preg_grep('/^\+/', $_diff)));
	$minus = join("\n", preg_replace('/^-/',  '', preg_grep('/^-/',  $_diff)));
	unset($_diff);
	return array($plus, $minus);
}

if (!function_exists('hash_hmac')) {
	// source: http://www.php.net/manual/en/function.sha1.php#39492
	// Open Publication License
	function hash_hmac($algo,$data,$key,$raw_output=false)
	{
		$algo = strtolower($algo); // hash_algos()
		switch($algo) {
		case 'sha1':
		case 'md5':
			continue;
		case 'sha256':
			// for PHP4
			// RFC 2104 HMAC implementation for php.
			// Creates a sha256 HMAC.
			// Eliminates the need to install mhash to compute a HMAC
			// Hacked by Lance Rushing
			// modified by Ulrich Mierendorff to work with sha256 and raw output
			require_once( 'sha256.inc.php');
			continue;
		default:
			return false;
		}

		$blocksize = 64;

		if (strlen($key) > $blocksize) {
			$key = pack('H*', $algo($key));
		}

		$key  = str_pad($key, $blocksize, chr(0x00));
		$ipad = str_repeat(chr(0x36), $blocksize);
		$opad = str_repeat(chr(0x5c), $blocksize);
		$hmac = $algo(($key^$opad) . pack('H*', $algo(($key^$ipad).$data)));
		return ($raw_output) ? pack('H*', $hmac) : $hmac;
	}
}

/** Adv. Extended functions ***********************************************************************/
function compress_file($in, $method, $chmod=644){
	// ファイルの存在確認
	if (!file_exists ($filename) || !is_readable ($filename)) return false;
	// 出力ファイル名
	$out = $file.$method;
	if ((!file_exists ($out) && !is_writeable (dirname ($out)) || (file_exists($out) && !is_writable($out)) )) return false; 
	// テンポラリファイル名
	$tmp_name = $file.'.tmp';

	switch ($method){
		case 'gz' :
			if (extension_loaded('zlib')) {
				$in_file = fopen($in, "r");
				$out_file = gzopen ($out, "w9");	// 最高圧縮
				while (!feof ($in_file)) {
					$buffer= fread($in_file, 2048);
					gzwrite ($out_file, $buffer);
				}
				fclose ($in_file); 
				gzclose ($out_file);
				chmod($out_file, $chmod);
				break;
			}
		case 'bz2' :
			if (extension_loaded('bzip2')) {
				$in_file = fopen ($in, "rb");
				$out_file = bzopen ($out, "wb");
				while (!feof ($in_file)) {
					$buffer = fgets ($in_file, 4096);
					bzwrite ($out_file, $buffer, 4096);
				}
				fclose ($in_file); 
				bzclose ($out_file);
				chmod($out_file, $chmod);
				break;
			}
		case 'lzf' :
			if (extension_loaded('lzf')) {
				lzf_compress($filename);
				chmod($filename, $chmod);
			}
		case 'zip' :
			if (class_exists('ZipArchive')) {
				$zip = new ZipArchive();

				$zip->addFile($tmp_name,$filename);
				// if ($zip->status !== ZIPARCHIVE::ER_OK)
				if ($zip->status !== 0)die_message( $zip->status);
				$zip->close();
				chmod($filename, $chmod);
				@unlink($tmp_name);
				break;
			}
		case 'tar' :
		default :
			$tar = new tarlib();
			$tar->create(CACHE_DIR, 'tar') or die_message( 'Temporaly file failure.' );
			$tar->add_file($tmp_name, $filename);
			$tar->close();

			@rename($tar->filename, $filename);
			chmod($filename, $chmod);
			@unlink($tar->filename);
		break;
	}
}

/**
 * POST data check function $Id$
 *
 * PukioWikio - A WikiWikiWeb clone.
 *  A custom version of PukiWiki.
 *
 * @copyright  &copy; 2008 PukioWikio Developers Team
 * @license GPL v2 or (at your option) any later version
 */

/**
 * generate id from $cmd and random number
 */
function generate_postid($cmd = '')
{
	global $cache;
	$idstring_raw = $cmd . mt_rand();		//mt_srand() is necessary if PHP version is lower than 4.2.0
	$idstring = md5($idstring_raw);

	$filename = POSTID_DIR . $idstring . PKWK_DAT_EXTENTION;
	cache_write($_SERVER['REMOTE_ADDR'], $filename, POSTID_EXPIRE);
	cache_cleanup($filename, POSTID_EXPIRE);
	return $idstring;
}

function check_postid($idstring)
{
	global $memcache;
	$filename = POSTID_DIR . $idstring . PKWK_DAT_EXTENTION;
	$ret = TRUE;
	$data = cache_read($filename);
	if ($data !== false){
		if ($data !== $_SERVER['REMOTE_ADDR']){
			$ret = false;
		}
		unset($data);
	}else{
		$ret = FALSE;
	}
	cache_delete($filename);
	cache_cleanup($filename, POSTID_EXPIRE);
	if ($ret === FALSE){
		honeypot_write();
	}
	return $ret;
}

/* End of file fileplus.php */
/* Location: ./wiki-common/lib/fileplus.php */
