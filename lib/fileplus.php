<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: fileplus.php,v 1.2.4 2010/07/23 22:46:00 upk Exp $
// Copyright (C)
//   2010 PukiPlus Team
//   2005-2006,2009 PukiWiki Plus! Team
// License: GPL v2 or (at your option) any later version
//
// File related functions - extra functions

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

function cache_read($filename)
{
	$fp = fopen($filename, 'rb');
	if ($fp === false) return array();
	@flock($fp, LOCK_SH);
	$data = fread($fp, filesize($filename));
	@flock($fp, LOCK_UN);
	if(! fclose($fp)) return array();
	return unserialize($data);
}

function cache_write($data, $filename)
{
	pkwk_touch_file($filename);
	$fp = fopen($filename, 'wb');
	if ($fp === false) return false;
	@flock($fp, LOCK_EX);
	rewind($fp);
	$bytes = fwrite($fp, serialize($data));
	fflush($fp);
	ftruncate($fp, ftell($fp));
	@flock($fp, LOCK_UN);
	fclose($fp);
	return $bytes;
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
	touch (cache_timestamp_get_name($func));
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
	return touch($file, $ts_org);
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

	$pages = get_existpages($dir,$ext);
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
?>
