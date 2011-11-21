<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: fileplus.php,v 1.2.7 2011/11/21 13:51:00 Logue Exp $
// Copyright (C)
//   2010 PukiWiki Advance Team
//   2005-2006,2009 PukiWiki Plus! Team
// License: GPL v2 or (at your option) any later version
//
// File related functions - extra functions

// Marged from PukioWikio's post.php
defined('POSTID_DIR')		or define('POSTID_DIR', 'PostId/');
defined('POSTID_EXPIRE')	or define('POSTID_EXPIRE', 86400);	// 60*60*24 = 1day

// Get Ticket
function get_ticket($newticket = FALSE)
{
	$file = CACHE_DIR . 'ticket.dat';

	if (file_exists($file) && $newticket !== TRUE) {
		$fp = fopen($file, 'r') or die_message('Cannot open ' . 'CACHE_DIR/' . 'ticket.dat');
		$ticket = trim(fread($fp, filesize($file)));
		fclose($fp);
	} else {
		$ticket = md5(mt_rand());
		pkwk_touch_file($file);
		$fp = fopen($file, 'r+') or die_message('Cannot open ' . 'CACHE_DIR/' . 'ticket.dat');
		set_file_buffer($fp, 0);
		@flock($fp, LOCK_EX);
		$last = ignore_user_abort(1);
		ftruncate($fp, 0);
		rewind($fp);
		fputs($fp, $ticket . "\n");
		ignore_user_abort($last);
		@flock($fp, LOCK_UN);
		fclose($fp);
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
	while (@ob_end_flush());
	if (($fp = fopen($filename,'rb')) === FALSE) return FALSE;
	while (!feof($fp))
	{
		echo fread($fp, 4096);
		flush();
	}
	fclose($fp);
}

// structure

// キャッシュ読み込み
function cache_read($filename)
{
	global $memcache;
	if ($memcache !== null){
		$data = $memcache->get($filename);
	}else{
		$fp = fopen($filename, 'rb');
		if ($fp === false) return array();
		@flock($fp, LOCK_SH);
		$data = unserialize( fread($fp, filesize($filename)) );
		@flock($fp, LOCK_UN);
		if(! fclose($fp)) return array();
	}
	return $data;
}

// キャッシュ書き出し＋クリーンアップ
function cache_write($data, $filename, $expire = null)
{
	global $memcache;
	if ($memcache !== null){
		$ret = $memcache->set($filename, $data, MEMCACHE_COMPRESSED ,$expire);
	}else{
		pkwk_touch_file($filename);
		$fp = fopen($filename, 'wb');
		if ($fp === false) return false;
		@flock($fp, LOCK_EX);
		rewind($fp);
		$ret = fwrite($fp, serialize($data));
		fflush($fp);
		ftruncate($fp, ftell($fp));
		@flock($fp, LOCK_UN);
		fclose($fp);
		cache_cleanup($filename, $exepire);
	}
	return $ret;
}

// クリーンアップ処理（memcache無効時は、処理を行わない）
function cache_cleanup($filename, $exepire){
	global $memcache;
	if ($memcache == null){
		// 保存先のディレクトリ名を取得
		$dir = dirname($filename);
		// 拡張子を取得（二重拡張子は不可）
		$ext = substr($filename, strrpos($filename, '.') + 1);
		
		// 同一階層上のファイルを捜査
		foreach(scandir($dir) as $file) {
			// 同一拡張子のファイルをクリーンアップ
			if (mb_strpos($file, $ext)){
				$f = $dir.'/'.$file;	// ファイルのフルパス
				//$filetime = exec ('stat -c %Y '. escapeshellarg ($f));	// filectime()は環境によっては動作しないため。
				$filetime = filectime($f);
				// 有効期限を過ぎたファイルは削除
				if(UTIME - $filetime > $expire) {
					cache_delete($f);
				}
			}
		}
	}
}

// キャッシュ削除
function cache_delete($filename){
	global $memcache;
	if ($memcache !== null){
		$ret = $memcache->delete($filename);
	}else{
		$ret = unlink($filename);
	}
	return $ret;
}

function cache_timestamp_get_name($func='wiki') {
	$filename = CACHE_DIR.'timestamp_';
	switch ($func) {
	case 'attach':
		$filename .= $func;
		break;
	case 'wiki':
	default:
		$filename .= 'page';
		break;
	}
	$filename .= '.txt';
	return $filename;
}

function cache_timestamp_touch($func='wiki') {
	pkwk_touch_file (cache_timestamp_get_name($func));
}

function cache_timestamp_compare_date($func,$file)
{
	$org = cache_timestamp_get_name($func);
	// if (!file_exists($org) || !file_exists($file)) return false;

	if (!file_exists($org)) {
		cache_timestamp_touch($func);
		return false;
	}
	if (!file_exists($file)) return false;

	$ts_org  = filemtime($org);
	$ts_file = filemtime($file);
	if ($ts_org === $ts_file) return true;
	return false;
}

function cache_timestamp_set_date($func,$file)
{
	$org = cache_timestamp_get_name($func);
	if (!file_exists($org) || !file_exists($file)) return false;
	$ts_org = filemtime($org);
	return pkwk_touch_file($file, $ts_org);
}

function get_existpages_cache($dir = DATA_DIR, $ext = '.txt', $compat = true)
{
	$cache_name = CACHE_DIR.encode($dir.$ext).'.txt';
	if (file_exists($cache_name)) {
		if (cache_timestamp_compare_date('wiki',$cache_name)) {
			$pages = get_existpages_cache_read($cache_name,$compat);
			if (!empty($pages)) return $pages;
		}
	}

	cache_timestamp_touch();

	$pages = get_existpages($dir ,$ext);
	$new_pages = get_existpages_cache_write($pages, $cache_name, $compat);

	cache_timestamp_set_date('wiki',$cache_name);
	return ($compat) ? $pages : $new_pages;
}

function get_existpages_cache_read($filename,$compat=true)
{
	// if (! file_exists($filename)) return array();
	$rc = array();
	$fp = @fopen($filename, 'r');
	if ($fp == FALSE) return $rc;
	@flock($fp, LOCK_SH);

	while (! feof($fp)) {
		$line = fgets($fp, 2048);
		if ($line === FALSE) continue;
		$field = explode("\t", $line);
		$page = trim($field[2]);
		$rc[$field[1]] = ($compat) ? $page : array('page'=>$page,'time'=>$field[0]);
	}

	@flock($fp, LOCK_UN);
	if(! fclose($fp)) return array();
	return $rc;
}

function get_existpages_cache_write(& $pages, $filename, $compat=true)
{
	if (!$compat) $retval = array();

	pkwk_touch_file($filename);
	$fp = fopen($filename,'w');
	if ($fp == FALSE) return false;
	@flock($fp, LOCK_EX);
	foreach($pages as $file=>$page) {
		$time = get_filetime($page);
		fwrite($fp, $time."\t".$file."\t".$page."\n");
		if (!$compat) {
			$retval[$file] = array('page'=>$page,'time'=>$time);
		}
	}
	@flock($fp, LOCK_UN);
	@fclose($fp);
	return ($compat) ? $pages : $retval;
}

function get_attachfiles_cache($page='')
{
	$cache_name = CACHE_DIR.'attach_files.txt';
	if (file_exists($cache_name)) {
		if (cache_timestamp_compare_date('attach',$cache_name)) {
			return get_attachfiles_cache_read($cache_name,$page);
		}
	} else {
		cache_timestamp_touch('attach');
	}

	$retval = get_attachfiles_cache_write($cache_name,$page);
	cache_timestamp_set_date('attach',$cache_name);
	return $retval;
}

function get_attachfiles_cache_write($filename,$page)
{
	$dir = opendir(UPLOAD_DIR) or
		die('directory ' . UPLOAD_DIR . ' is not exist or not readable.');
	$retval = array();
	$pattern = "/^((?:[0-9A-F]{2})+)_((?:[0-9A-F]{2})+)$/";

	if (!empty($page)) {
		$page_pattern = preg_quote(encode($page), '/');
		$scan_pattern = "/^({$page_pattern})_((?:[0-9A-F]{2})+)$/";
	}

	pkwk_touch_file($filename);
	$fp = fopen($filename,'w');
	if ($fp == FALSE) return array();
	@flock($fp, LOCK_EX);

	$matches = array();
	while ($file = readdir($dir)) {
		if (! preg_match($pattern, $file, $matches)) continue; // all page
		$_page = decode($matches[1]);
		$_file = decode($matches[2]);
		$time = filemtime(UPLOAD_DIR.$file);
		$size = filesize(UPLOAD_DIR.$file);
		fwrite($fp, $time."\t".$size."\t".$file."\t".$_page."\t".$_file."\n");
		if (! empty($page) && ! preg_match($scan_pattern, $file, $matches)) continue;
		// [page][file] = array(time,size);
		$retval[$_page][$_file] = array('time'=>$time,'size'=>$size);
	}
	@flock($fp, LOCK_UN);
	@fclose($fp);
	closedir($dir);

	return $retval;
}

function get_attachfiles_cache_read($filename,$page)
{
	$retval = array();
	$fp = @fopen($filename, 'r');
	if ($fp == FALSE) return $retval;
	@flock($fp, LOCK_SH);

	$page_pattern = ($page == '') ? '(?:[0-9A-F]{2})+' : preg_quote(encode($page), '/');
	$pattern = "/^({$page_pattern})_((?:[0-9A-F]{2})+)$/";

	$matches = array();
	while (! feof($fp)) {
		$line = fgets($fp, 2048);
		if ($line === FALSE) continue;
		$field = explode("\t", $line);
		$_file = trim($field[4]);
		// [page][file] = array(time,size);
		if (! empty($page) && ! preg_match($pattern, $field[2], $matches)) continue;
		$retval[$field[3]][$_file] = array('time'=>$field[0],'size'=>$field[1]);
	}

	@flock($fp, LOCK_UN);
	if(! fclose($fp)) return array();
	return $retval;
}

// Move from file.php

// Update AutoBaseAlias data
function autobasealias_write($filename, &$pages)
{
	global $autobasealias_nonlist;
	$pairs = array();
	foreach ($pages as $page) {
		if (preg_match('/' . $autobasealias_nonlist . '/', $page)) continue;
		$base = get_short_pagename($page);
		if ($base !== $page) {
			if (! isset($pairs[$base])) $pairs[$base] = array();
			$pairs[$base][] = $page;
		}
	}
	$data = serialize($pairs);

	$fp = fopen($filename, 'w') or
			die_message('Cannot open ' . $filename . '<br />Maybe permission is not writable');
	set_file_buffer($fp, 0);
	@flock($fp, LOCK_EX);
	rewind($fp);
	fputs($fp, $data);
	@flock($fp, LOCK_UN);
	@fclose($fp);
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
	if ($minus != '') {
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
	$idstring_raw = $cmd . mt_rand();		//mt_srand() is necessary if PHP version is lower than 4.2.0
	$idstring = md5($idstring_raw);

	$filename = CACHE_DIR . POSTID_DIR . $idstring .'.dat';
	$data = array(
		'time'=>UTIME,
		'cmd'=>$cmd,
		'ip'=>$_SERVER['REMOTE_ADDR']
	);
	cache_write($data, $filename, POSTID_EXPIRE);
	return $idstring;
}

function check_postid($idstring)
{
	global $memcache;
	$filename = CACHE_DIR. POSTID_DIR . $idstring . '.dat';
	$ret = TRUE;
	if ( file_exists($filename) || $memcache !== 'null'){
		$data = cache_read($filename);
		cache_delete($filename);
		if ($data['ip'] !== $_SERVER['REMOTE_ADDR']){
			$ret = FALSE;
		}
		unset($data);
	}else{
		$ret = FALSE;
	}
	cache_cleanup($filename, POSTID_EXPIRE);
	return $ret;
}
?>
