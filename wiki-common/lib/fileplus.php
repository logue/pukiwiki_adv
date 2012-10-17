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
defined('POSTID_DIR')		or define('POSTID_DIR', 'PostId/');
defined('POSTID_EXPIRE')	or define('POSTID_EXPIRE', 3600);	// 60*60 = 1hour

// Ticket file
defined('PKWK_TICKET_CACHE')	or define('PKWK_TICKET_CACHE', 'ticket'.PKWK_DAT_EXTENTION);

defined('PKWK_EXSISTS_DATA_CACHE')		or define('PKWK_EXSISTS_DATA_CACHE', 'exsists-wiki'.PKWK_TSV_EXTENTION);
defined('PKWK_EXSISTS_ATTACH_CACHE')	or define('PKWK_EXSISTS_ATTACH_CACHE', 'exsists-attach'.PKWK_TSV_EXTENTION);
defined('PKWK_TIMESTAMP_DATA_CACHE')	or define('PKWK_TIMESTAMP_DATA_CACHE', 'timestamp-wiki'.PKWK_DAT_EXTENTION);
defined('PKWK_TIMESTAMP_ATTACH_CACHE')	or define('PKWK_TIMESTAMP_ATTACH_CACHE', 'timestamp-attach'.PKWK_DAT_EXTENTION);

// Get Ticket
function get_ticket($newticket = FALSE)
{
	if (cache_check(PKWK_TICKET_CACHE) && $newticket !== TRUE) {
		$ticket = cache_read_raw(PKWK_TICKET_CACHE);
	}else{
		$ticket = md5(mt_rand());
		cache_write_raw($ticket,PKWK_TICKET_CACHE);
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

// structure

/** 汎用キャッシュ処理 ****************************************************************************/
// キャッシュ存在確認
function cache_check($cache_key)
{
	global $memcache;
	$data = array();
	if ($memcache !== null){
		$data = $memcache->get(MEMCACHE_PREFIX.$cache_key);
		$ret = ($data !== false) ? true : false;
	}else{
		$ret = (file_exists(CACHE_DIR.$cache_key) !== FALSE) ? true : false;
	}
	return $ret;
}

// キャッシュ読み込み
function cache_read($cache_key)
{
	global $memcache;
	$data = array();
	if ($memcache !== null){
		$data = $memcache->get(MEMCACHE_PREFIX.$cache_key);
	}else if (file_exists(CACHE_DIR.$cache_key) !== FALSE){
		$fp = fopen(CACHE_DIR.$cache_key, 'rb');
		if ($fp === false) return array();
		@flock($fp, LOCK_SH);
		$data = unserialize( fread($fp, filesize(CACHE_DIR.$cache_key)) );
		@flock($fp, LOCK_UN);
		if(! fclose($fp)) return array();
	}
	return $data;
}
function cache_read_raw($cache_key)
{
	global $memcache;
	$data = array();
	if ($memcache !== null){
		$raw_data = $memcache->get(MEMCACHE_PREFIX.$cache_key);
	}else if (file_exists(CACHE_DIR.$cache_key) !== FALSE){
		$fp = fopen(CACHE_DIR.$cache_key, 'rb');
		if ($fp === false) return array();
		@flock($fp, LOCK_SH);
		$raw_data = fread($fp, filesize(CACHE_DIR.$cache_key));
		@flock($fp, LOCK_UN);
		if(! fclose($fp)) return array();
	}
	return $raw_data;
}

// キャッシュ書き出し
function cache_write($data, $cache_key, $expire = MEMCACHE_EXPIRE, $compress = MEMCACHE_COMPRESSED)
{
	global $memcache;
	if ($memcache !== null){
		$ret = update_memcache(MEMCACHE_PREFIX.$cache_key, $data, $compress ,$expire);
	}else{
		pkwk_touch_file(CACHE_DIR.$cache_key);
		$fp = fopen(CACHE_DIR.$cache_key, 'wb');
		if ($fp === false) return false;
		@flock($fp, LOCK_EX);
		rewind($fp);
		$ret = fwrite($fp, serialize($data));
		fflush($fp);
		ftruncate($fp, ftell($fp));
		@flock($fp, LOCK_UN);
		fclose($fp);
		cache_cleanup($cache_key, $expire);
	}
	return $ret;
}
function cache_write_raw($raw_data, $cache_key, $expire = MEMCACHE_EXPIRE, $compress = MEMCACHE_COMPRESSED)
{
	global $memcache;
	if ($memcache !== null){
		$ret = update_memcache(MEMCACHE_PREFIX.$cache_key, $raw_data, $compress ,$expire);
	}else{
		pkwk_touch_file(CACHE_DIR.$cache_key);
		$fp = fopen(CACHE_DIR.$cache_key, 'wb');
		if ($fp === false) return false;
		@flock($fp, LOCK_EX);
		rewind($fp);
		$ret = fwrite($fp, $raw_data);
		fflush($fp);
		ftruncate($fp, ftell($fp));
		@flock($fp, LOCK_UN);
		fclose($fp);
		cache_cleanup($cache_key, $expire);
	}
	return $ret;
}

// キャッシュ削除
function cache_delete($cache_key){
	global $memcache;
	$ret = false;
	if ($memcache !== null){
		$ret = $memcache->delete(MEMCACHE_PREFIX.$cache_key);
 	}else if (file_exists(CACHE_DIR.$cache_key) !== FALSE){
		unlink(CACHE_DIR.$cache_key);
		$ret = true;
	}
	return $ret;
}

// キャッシュクリア
function cache_clear($pattern = PKWK_DAT_EXTENTION){
	global $memcache;
	if ($memcache !== null){
		foreach (getMemcacheList() as $key){
			if (preg_match('/^'.$pattern.'/', $key) !== FALSE){
				$memcache->delete($key);
			}
		}
	}else{
		$dir = dirname(CACHE_DIR);
		foreach(scandir($dir) as $file) {
			$ext = substr($file, strrpos($file, '.') + 1);
			if (preg_match('/'.$pattern.'$/', $file) !== FALSE){
				$file_path = $dir.'/'.$file;
				if (file_exsists($file_path)){
					@unlink($file_path);
				}
			}
		}
	}
}

// クリーンアップ処理（memcache無効時は、処理を行わない）
function cache_cleanup($file, $expire){
	global $memcache;
	if ($memcache == null){
		$filename = CACHE_DIR.$file;
		// 保存先のディレクトリ名を取得
		$dir = dirname($filename);
		// 拡張子を取得（二重拡張子は不可）
		$ext = substr($filename, strrpos($filename, '.') + 1);
		
		// 同一階層上のファイルを捜査
		foreach (scandir($dir) as $d_file) {
			// 同一拡張子のファイルをクリーンアップ
			if (mb_strpos($d_file, $ext)){
				$f = $dir.'/'.$d_file;	// ファイルのフルパス
				//$filetime = exec ('stat -c %Y '. escapeshellarg ($f));
				$filetime = filectime($f);
				// 有効期限を過ぎたファイルは削除
				if ( (UTIME - $filetime) > $expire) {
					unlink($f);
				}
			}
		}
	}
}

// memcache内のキャッシュのキーを取得
function getMemcacheKeyList(){
	global $memcache;
	$list = array();
	$allSlabs = $memcache->getExtendedStats('slabs');
	$items = $memcache->getExtendedStats('items');
	foreach($allSlabs as $server => $slabs) {
		foreach($slabs AS $slabId => $slabMeta) {
			$cdump = $memcache->getExtendedStats('cachedump',(int)$slabId);
			foreach($cdump AS $keys => $arrVal) {
				if (is_array($arrVal)){
					foreach($arrVal AS $k=>$v) {
						$ret[] = $k;
					}
				}else{
					$ret[] = $arrVal;
				}
			}
		}
	}
	return $ret;
}

// memcacheの値を更新
// memcache無効時はnullを返す。
function update_memcache($key, $value, $compress = MEMCACHE_COMPRESSED ,$expire = MEMCACHE_EXPIRE){
	global $memcache;
	if ($memcache !== null){
		$ret = $memcache->replace($key, $value, $compress ,$expire);
		if( $ret === false ){
			$ret = $memcache->set($key, $value, $compress ,$expire);
		}
	}else{
		$ret = null;
	}
	return $ret;
}

// update autolink data
function autolink_pattern_write($filename, $autolink_pattern)
{
	global $memcache;

	if ($memcache !== null){
		$cache_name = MEMCACHE_PREFIX.substr($filename,0,strrpos($filename, '.'));
		update_memcache($cache_name, $autolink_pattern);
	}else{
		list($pattern, $pattern_a, $forceignorelist) = $autolink_pattern;
		pkwk_touch_file(CACHE_DIR.$filename);
		$fp = fopen(CACHE_DIR.$filename, 'w') or
				die_message('Cannot open ' . $filename);
		set_file_buffer($fp, 0);
		flock($fp, LOCK_EX);
		rewind($fp);
		fputs($fp, $pattern   . "\n");
		fputs($fp, $pattern_a . "\n");
		fputs($fp, join("\t", $forceignorelist) . "\n");
		flock($fp, LOCK_UN);
		fclose($fp);
	}
}

// Delete Autolink data
function autolink_pattern_delete($filename){
	global $memcache;
	if ($memcache !== null){
		$memcache->delete(MEMCACHE_PREFIX.substr($filename,0,strrpos($filename, '.')));
	}else{
		@unlink(CACHE_DIR . $filename);
	}
}

// Read autolink data
function autolink_pattern_read($filename){
	global $memcache;
	if ($memcache !== null){
		$cache = $memcache->get(MEMCACHE_PREFIX.substr($filename,0,strrpos($filename, '.')));
		if ($cache === FALSE){
			return;
		}
		@list($auto, $auto_a, $forceignorepages) = $cache;
	}else{
		$file = CACHE_DIR . $filename;
		if (! file_exists($file)){
			return;
		}
		@list($auto, $auto_a, $forceignorepages_tsv) = file($file);
		$forceignorepages = explode("\t", trim($forceignorepages_tsv));
	}
	return array($auto, $auto_a, $forceignorepages);
}

// キャッシュのタイムスタンプ関連
function cache_timestamp_get_name($func='wiki') {
	$filename = CACHE_DIR.PKWK_TIMESTAMP_PREFIX;
	switch ($func) {
	case 'attach':
		$filename .= $func;
		break;
	case 'wiki':
	default:
		$filename .= 'page';
		break;
	}
	$filename .= PKWK_TXT_EXTENTION;
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

function get_existpages_cache($dir = DATA_DIR, $ext = PKWK_TXT_EXTENTION, $compat = true)
{
	global $memcache;
	
	switch($dir){
		case DATA_DIR: $func = 'wiki'; break;
		case UPLOAD_DIR: $func = 'attach'; break;
		case COUNTER_DIR: $func = 'counter'; break;
		case BACKUP_DIR: $func = 'backup'; break;
		default: $func = encode($dir.$ext);
	}

	if ($memcache !== null){
		$cache_name = MEMCACHE_PREFIX.PKWK_EXISTS_PREFIX.$func;
		$pages = $memcache->get($cache_name);
		if ($pages !== FALSE){
			if (cache_timestamp_compare_date($func, $cache_name)) {
				$pages = get_existpages_cache_read($cache_name,$compat);
				if (!empty($pages)) return $pages;
			}
		}
	}else{
		$cache_name = CACHE_DIR.PKWK_EXISTS_PREFIX.$func.'.txt';
		if (cache_timestamp_compare_date($func,$cache_name)) {
			$pages = get_existpages_cache_read($cache_name,$compat);
			if (!empty($pages)) return $pages;
		}
	}

	cache_timestamp_touch($func);

	$pages = get_existpages($dir ,$ext);
	if ($memcache !== null){
		$new_pages = $memcache->replace($cache_name, $pages, null, MEMCACHE_EXPIRE);
		if( $new_pages === false ){
			$memcache->set($cache_name, $pages, null, MEMCACHE_EXPIRE);
		}
	}else{
		$new_pages = get_existpages_cache_write($pages, $cache_name, $compat);
	}

	cache_timestamp_set_date($func,$cache_name);
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
	global $memcache;
	
	if ($memcache !== null){
		$cache_name = 'attach_files';
		$cache_data = cache_read($cache_name);
		if (cache_timestamp_compare_date('attach',$cache_name) && $cache_data !== FALSE) {
			return $cache_data;
		}else{
			cache_timestamp_touch('attach');
		}
	}else{
		$cache_name = CACHE_DIR.'attach_files.txt';
		if (file_exists($cache_name)) {
			if (cache_timestamp_compare_date('attach',$cache_name)) {
				return get_attachfiles_cache_read($cache_name,$page);
			}
		} else {
			cache_timestamp_touch('attach');
		}
	}

	$retval = get_attachfiles_cache_write($cache_name,$page);
	cache_timestamp_set_date('attach',$cache_name);
	return $retval;
}

function get_attachfiles_cache_write($filename,$page)
{
	global $memcache;

	$dir = opendir(UPLOAD_DIR) or
		die('directory ' . UPLOAD_DIR . ' is not exist or not readable.');
	$retval = array();
	$pattern = "/^((?:[0-9A-F]{2})+)_((?:[0-9A-F]{2})+)$/";

	if (!empty($page)) {
		$page_pattern = preg_quote(encode($page), '/');
		$scan_pattern = "/^({$page_pattern})_((?:[0-9A-F]{2})+)$/";
	}

	if ($memcache === null){
		pkwk_touch_file($filename);
		$fp = fopen($filename,'w');
		if ($fp == FALSE) return array();
		@flock($fp, LOCK_EX);
	}else{
		$data = array();
	}

	$matches = array();
	while ($file = readdir($dir)) {
		if (! preg_match($pattern, $file, $matches)) continue; // all page
		$line = array(filemtime(UPLOAD_DIR.$file), filesize(UPLOAD_DIR.$file), $file,  decode($matches[1]),  decode($matches[2]));
		
		if ($memcache === null){
			fwrite($fp, join("\t",$line)."\n" );
		}else{
			$data[] = $line;
		}
		if (! empty($page) && ! preg_match($scan_pattern, $file, $matches)) continue;
		// [page][file] = array(time,size);
		$retval[$_page][$_file] = array('time'=>$time,'size'=>$size);
	}
	if ($memcache === null){
		@flock($fp, LOCK_UN);
		@fclose($fp);
	}else{
		cache_write($filename, $data);
	}
	closedir($dir);

	return $retval;
}

function get_attachfiles_cache_read($filename,$page)
{
	global $memcache;
	
	$retval = array();
	$page_pattern = ($page == '') ? '(?:[0-9A-F]{2})+' : preg_quote(encode($page), '/');
	$pattern = "/^({$page_pattern})_((?:[0-9A-F]{2})+)$/";

	if ($memcache === null){
		$fp = @fopen($filename, 'r');
		if ($fp == FALSE) return $retval;
		@flock($fp, LOCK_SH);
		
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
	}else{
		$data = $memcache->get($filename);
		foreach($data as $field){
			$_file = trim($field[4]);
			$retval[$field[3]][$_file] = array('time'=>$field[0],'size'=>$field[1]);
		}
	}
	
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
	cache_write($pairs, $filename);
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
