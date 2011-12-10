<?php
// PukiWiki Plus! - Yet another WikiWikiWeb clone
// $Id: link.php,v 1.20.8 2011/11/28 21:36:00 Logue Exp $
// Copyright (C)
//   2010-2011 PukiWiki Advance Developers Team
//   2005-2007 PukiWiki Plus! Team
//   2003-2007,2011 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Backlinks / AutoLinks related functions

// ------------------------------------------------------------
// DATA STRUCTURE of *.ref and *.rel files

// CACHE_DIR/encode('foobar').ref
// ---------------------------------
// Page-name1<tab>0<\n>
// Page-name2<tab>1<\n>
// ...
// Page-nameN<tab>0<\n>
//
//	0 = Added when link(s) to 'foobar' added clearly at this page
//	1 = Added when the sentence 'foobar' found from the page
//	    by AutoLink feature

// CACHE_DIR/encode('foobar').rel
// ---------------------------------
// Page-name1<tab>Page-name2<tab> ... <tab>Page-nameN
//
//	List of page-names linked from 'foobar'

// ------------------------------------------------------------

 // Get related-pages from DB
function links_get_related_db($page)
{
	global $memcache;
	
	$times = array();
	if ($memcache !== null){
		$data = $memcache->get(MEMCACHE_PREFIX.'ref-'.encode($page));
		if ($data === FALSE) return array();
		foreach ($data as $line) {
			$time = get_filetime($line[0]);
			if($time != 0) $times[$line[0]] = $time;
		}
	}else{
		$ref_name = CACHE_DIR . encode($page) . '.ref';
		if (! file_exists($ref_name)) return array();

		foreach (file($ref_name) as $line) {
			list($_page) = explode("\t", rtrim($line));
			$time = get_filetime($_page);	
			if($time != 0) $times[$_page] = $time;
		}
		
	}
	// $times['pagename'] = utime;
	return $times;
}

// Update link-relationships between pages
function links_update($page)
{
	global $memcache;
	// if (PKWK_READONLY) return; // Do nothing
	if (auth::check_role('readonly')) return; // Do nothing

	if (ini_get('safe_mode') == '0') set_time_limit(0);

	$time = is_page($page, TRUE) ? get_filetime($page) : 0;
	
	if ($memcache !== null){
		$rel_name = MEMCACHE_PREFIX.'rel-'.encode($page);
		$rel_old = $memcache->get($rel_name);
		if ($rel_old !== FALSE){
			$memcache->delete($rel_name);
			$rel_file_exist = TRUE;
		}else{
			$rel_file_exist = FALSE;
			$rel_old        = array();
		}
	}else{
		$rel_old        = array();
		$rel_name       = CACHE_DIR . encode($page) . '.rel';
		$rel_file_exist = file_exists($rel_name);
		if ($rel_file_exist === TRUE) {
			$lines = file($rel_name);
			unlink($rel_name);
			if (isset($lines[0]))
				$rel_old = explode("\t", rtrim($lines[0]));
		}
	}

	$rel_new  = array();	// Reference to
	$rel_auto = array();	// by AutoLink
	$links    = links_get_objects($page, TRUE);
	foreach ($links as $_obj) {
		if (! isset($_obj->type) || $_obj->type != 'pagename' ||
		    $_obj->name === $page || $_obj->name == '')
			continue;

		if (is_a($_obj, 'Link_autolink')) { // Not cool though
			$rel_auto[] = $_obj->name;
		} else if (is_a($_obj, 'Link_autoalias')) {
			$_alias = get_autoaliases($_obj->name);
			if (is_pagename($_alias)) {
				$rel_auto[] = $_alias;
			}
		} else {
			$rel_new[]  = $_obj->name;
		}
	}
	$rel_new = array_unique($rel_new);
	
	// All pages "Referenced to" only by AutoLink
	$rel_auto = array_diff(array_unique($rel_auto), $rel_new);

	// All pages "Referenced to"
	$rel_new = array_merge($rel_new, $rel_auto);

	// .rel: Pages referred from the $page
	if ($time) {
		// Page exists
		if (! empty($rel_new)) {
			if ($memcache !== null){
				$memcache->set($rel_name, $rel_new, MEMCACHE_FLAG, MEMCACHE_EXPIRE);
			}else{
				pkwk_touch_file($rel_name);
				$fp = fopen($rel_name, 'w')
					or die_message('cannot write ' . htmlsc($rel_name));
				fputs($fp, join("\t", $rel_new));
				fclose($fp);
			}
		}
	}

	// .ref: Pages refer to the $page
	links_add($page, array_diff($rel_new, $rel_old), $rel_auto);
	links_delete($page, array_diff($rel_old, $rel_new));

	global $WikiName, $autolink, $nowikiname, $search_non_list;

	// $page seems newly created, and matches with AutoLink
	if ($time && ! $rel_file_exist && $autolink
		&& (preg_match("/^$WikiName$/", $page) ? $nowikiname : strlen($page) >= $autolink))
	{
		// Update all, because they __MAY__ refer the $page [HEAVY]
		$search_non_list = 1;
		$pages           = do_search($page, 'AND', TRUE);
		foreach ($pages as $_page) {
			if ($_page !== $page)
				links_update($_page);
		}
	}
	
	if ($memcache !== null){
		$ref_name = MEMCACHE_PREFIX.'ref-'.encode($page);
		$data = $memcache->get($ref_name);
		if (! $time && $data !== false) {
			foreach($data as $ref_page=>$ref_auto){
				// Update pages they refer the $page by AutoLink only [HEAVY]
				if ($ref_auto) {
					links_delete($ref_page, array($page));
				}
			}
		}
	}else{
		$ref_file = CACHE_DIR . encode($page) . '.ref';

		// If the $page had been removed
		if (! $time && file_exists($ref_file)) {
			foreach (file($ref_file) as $line) {
				list($ref_page, $ref_auto) = explode("\t", rtrim($line));

				// Update pages they refer the $page by AutoLink only [HEAVY]
				if ($ref_auto) {
					links_delete($ref_page, array($page));
				}
			}
		}
	}
	return;
}

// Init link cache (Called from link plugin)
function links_init()
{
	global $memcache;
	// if (PKWK_READONLY) return; // Do nothing
	if (auth::check_role('readonly')) return; // Do nothing

	if (ini_get('safe_mode') == '0') set_time_limit(0);

	// Init database
	if ($memcache !== null){
		foreach(getMemcacheKeyList() as $key){
			if (preg_match('/^'.MEMCACHE_PREFIX.'(rel-|ref-)/', $key) !== FALSE){
				if (!empty($key)){
					$memcache->delete($key);
				}
			}
		}
	}else{
		foreach (get_existfiles(CACHE_DIR, '.ref') as $cache)
			unlink($cache);
		foreach (get_existfiles(CACHE_DIR, '.rel') as $cache)
			unlink($cache);
	}

	$ref   = array(); // Reference from
	foreach (get_existpages() as $page) {
		if (is_cantedit($page)) continue;

		$rel   = array(); // Reference to
		$links = links_get_objects($page);
		foreach ($links as $_obj) {
			if (! isset($_obj->type) || $_obj->type != 'pagename' ||
			    $_obj->name == $page || $_obj->name == '')
				continue;

			$_name = $_obj->name;
			if (is_a($_obj, 'Link_autoalias')) {
				$_alias = get_autoaliases($_name);
				if (! is_pagename($_alias))
					continue;	// not PageName
				$_name = $_alias;
			}
			$rel[] = $_name;
			if (! isset($ref[$_name][$page]))
				$ref[$_name][$page] = 1;
			if (! is_a($_obj, 'Link_autolink'))
				$ref[$_name][$page] = 0;
		}
		$rel = array_unique($rel);
		
		if (! empty($rel)) {
			if ($memcache !== null){
				$memcache->set(MEMCACHE_PREFIX.'rel-'.encode($page), $rel, MEMCACHE_FLAG, MEMCACHE_EXPIRE);
			}else{
				$fp = fopen(CACHE_DIR . encode($page) . '.rel', 'w')
					or die_message('cannot write ' . htmlsc(CACHE_DIR . encode($page) . '.rel'));
				fputs($fp, join("\t", $rel));
				fclose($fp);
			}
		}
	}

	if ($memcache !== null){
		$memcache->set(MEMCACHE_PREFIX.'ref-'.encode($page), $ref, MEMCACHE_FLAG, MEMCACHE_EXPIRE);
	}else{
		foreach ($ref as $page=>$arr) {
			$filename = CACHE_DIR . encode($page) . '.rel';
			pkwk_touch_file($filename);
			$fp = fopen($filename, 'w')
				or die_message('cannot write ' . htmlsc(CACHE_DIR . encode($page) . '.ref'));
			foreach ($arr as $ref_page=>$ref_auto)
				fputs($fp, $ref_page . "\t" . $ref_auto . "\n");
			fclose($fp);
		}
	}
	
	global $autoalias, $autoglossary;
	// Initialize autoalias.dat (AutoAliasName)
	if ($autoalias) {
		$aliases = get_autoaliases();
		if (empty($aliases)) {
			// Remove
			autolink_pattern_delete(PKWK_AUTOALIAS_REGEX_CACHE);
		} else {
			// Create or Update
			autolink_pattern_write(PKWK_AUTOALIAS_REGEX_CACHE,
				get_autolink_pattern(array_keys($aliases), $autoalias));
		}
	}

	// Initialize glossary.dat (AutoGlossary)
	if ($autoglossary) {
		$words = get_autoglossaries();
		if (empty($words)) {
			// Remove
			autolink_pattern_delete(PKWK_GLOSSARY_REGEX_CACHE);
		} else {
			// Create or Update
			autolink_pattern_write(PKWK_GLOSSARY_REGEX_CACHE,
				get_glossary_pattern(array_keys($words), $autoglossary));
		}
	}
}

function links_add($page, $add, $rel_auto)
{
	global $memcache;
	// if (PKWK_READONLY) return; // Do nothing
	if (auth::check_role('readonly')) return; // Do nothing

	$rel_auto = array_flip($rel_auto);

	foreach ($add as $_page) {
		$all_auto = isset($rel_auto[$_page]);
		$is_page  = is_page($_page);

		if ($memcache === null){
			$ref      = $page . "\t" . ($all_auto ? 1 : 0) . "\n";
			$ref_file = CACHE_DIR . encode($_page) . '.ref';
			if (file_exists($ref_file)) {
				foreach (file($ref_file) as $line) {
					list($ref_page, $ref_auto) = explode("\t", rtrim($line));
					if (! $ref_auto) $all_auto = FALSE;
					if ($ref_page !== $page) $ref .= $line;
				}
				unlink($ref_file);
			}
			if ($is_page || ! $all_auto) {
				pkwk_touch_file($ref_file);
				$fp = fopen($ref_file, 'w')
					 or die_message('cannot write ' . htmlsc($ref_file));
				fputs($fp, $ref);
				fclose($fp);
			}
		}else{
			$ref_name = MEMCACHE_PREFIX.'ref-'.encode($_page);
			$data = $memcache->get($ref_name);
			$ref[] = array($page, $all_auto);
			if ($data !== FALSE){
				foreach ($data as $line) {
					if ($line[0] !== $page) $ref[] = array($line[0], $line[1]);
				}
			}

			$ref = array_unique($ref);

			if ($is_page || ! $all_auto || count($ref) !== 0) {
				$memcache->set($ref_name, $ref, MEMCACHE_FLAG, MEMCACHE_EXPIRE);
			}else{
				$memcache->delete($ref_name);
			}
			unset($data, $ref);
		}
	}
}

function links_delete($page, $del)
{
	global $memcache;
	// if (PKWK_READONLY) return; // Do nothing
	if (auth::check_role('readonly')) return; // Do nothing

	foreach ($del as $_page) {
		$all_auto = TRUE;
		$is_page = is_page($_page);
		if ($memcache === null){
			$ref_file = CACHE_DIR . encode($_page) . '.ref';
			if (! file_exists($ref_file)) continue;

			$ref = '';
			foreach (file($ref_file) as $line) {
				list($ref_page, $ref_auto) = explode("\t", rtrim($line));
				if ($ref_page !== $page) {
					if (! $ref_auto) $all_auto = FALSE;
					$ref .= $line;
				}
			}
			unlink($ref_file);
			if (($is_page || ! $all_auto) && $ref != '') {
				pkwk_touch_file($ref_file);
				$fp = fopen($ref_file, 'w')
					or die_message('cannot write ' . htmlsc($ref_file));
				fputs($fp, $ref);
				fclose($fp);
			}
		}else{
			$ref_name = MEMCACHE_PREFIX.'ref-'.encode($_page);
			$data = $memcache->get($ref_name);
			if ($data === FALSE) continue;

			$ref = array();
			foreach ($data as $line) {
				list($ref_page, $ref_auto) = $line;
				if ($ref_page !== $page) {
					$ref[] = array($ref_page, $ref_auto);
				}
			}
			if ($is_page || ! $all_auto || count($ref) == 1) {
				$memcache->set($ref_name, $ref, MEMCACHE_FLAG, MEMCACHE_EXPIRE);
			}else{
				$memcache->delete($ref_name);
			}
			unset($data, $ref);
		}
	}
}

function & links_get_objects($page, $refresh = FALSE)
{
	static $obj;

	if (! isset($obj) || $refresh)
		$obj = new InlineConverter(NULL, array('note'));

	$result = $obj->get_objects(join('', preg_grep('/^(?!\/\/|\s)./', get_source($page))), $page);
	return $result;
}
?>
