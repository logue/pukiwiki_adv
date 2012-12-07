<?php
// PukiWiki Plus! - Yet another WikiWikiWeb clone
// $Id: link.php,v 1.20.10 2012/11/26 11:53:00 Logue Exp $
// Copyright (C)
//   2010-2012 PukiWiki Advance Developers Team
//   2005-2007 PukiWiki Plus! Team
//   2003-2007,2011 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Backlinks / AutoLinks related functions

// ------------------------------------------------------------
// DATA STRUCTURE of *.ref and *.rel files

// CACHE_DIR/md5('foobar').ref
// ---------------------------------
// Page-name1<tab>0<\n>
// Page-name2<tab>1<\n>
// ...
// Page-nameN<tab>0<\n>
//
//	0 = Added when link(s) to 'foobar' added clearly at this page
//	1 = Added when the sentence 'foobar' found from the page
//	    by AutoLink feature

// CACHE_DIR/md5('foobar').rel
// ---------------------------------
// Page-name1<tab>Page-name2<tab> ... <tab>Page-nameN
//
//	List of page-names linked from 'foobar'

// ------------------------------------------------------------

//namespace PukiWiki\Lib;

class Links{
	// Links cache namespace
	const CACHE_NAMESPACE = 'wiki';

	// Related cache data prefix
	const REL_PREFIX = 'rel-';
	// Referred cache data prefix
	const REF_PREFIX = 'ref-';

	const LINKS_PREFIX = 'links-';

	private $cache, $page, $rel_name, $ref_name, $links_obj;

	public function __construct($page){
		global $cache;
		$this->cache = $cache[self::CACHE_NAMESPACE];
		$this->links_obj = new InlineConverter(NULL, array('note'));
		if (!empty($page)){
			$this->page = $page;
			$page_hash = md5($page);
			$this->rel_name = self::REL_PREFIX.$page_hash;
			$this->ref_name = self::REF_PREFIX.$page_hash;
		}
	}

	// Get related-pages from DB
	public function get_related(){
		if (! $this->cache->hasItem($this->rel_name)){
			$data = $this->update();
			$this->cache->setItem($rel_name, $data);
		}else{
			$data = $this->cache->getItem($this->rel_name);
			$this->cache->touchItem($this->rel_name);
		}

		$times = array();
		foreach ($data as $page) {
			$time = get_filetime($page);
			if($time !== 0) $times[$page] = $time;
		}
		return $times;
	}

	// Get referred pages from DB
	public function get_referred(){
		if (! $this->cache->hasItem($this->ref_name)){
			$data = $this->update();
			$this->cache->setItem($ref_name, $data);
		}else{
			$data = $this->cache->getItem($this->ref_name);
			$this->cache->touchItem($this->ref_name);
		}

		$times = array();
		foreach ($data as $ref_page=>$ref_auto) {
			$time = get_filetime($ref_page);
			if($time !== 0) $times[$ref_page] = $time;
		}
		return $times;
	}

	// Update link-relationships between pages
	private function update($page = ''){
		if (empty($page)){
			$page = $this->page;
			$rel_name = $this->rel_name;
			$ref_name = $this->ref_name;
		}else{
			$page_hash = md5($page);
			$rel_name = self::REL_PREFIX.$page_hash;
			$ref_name = self::REF_PREFIX.$page_hash;
		}

		$time = is_page($page, TRUE) ? get_filetime($page) : 0;
		$rel_file_exist = $this->cache->hasItem($rel_name);

		$rel_old  = ($rel_exist) ? $this->cache->getItem($rel_name) : array();
		$rel_new  = array();	// Reference to
		$rel_auto = array();	// by AutoLink
		foreach ($this->get_objects($page, TRUE) as $_obj) {
			if (! isset($_obj->type) || $_obj->type !== 'pagename' || $_obj->name === $page || empty($_obj->name) )
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

		// All pages "Referenced to" only by AutoLink
		$rel_auto = array_diff(array_unique($rel_auto), $rel_new);

		// All pages "Referenced to"
		$rel_new = array_merge(array_unique($rel_new), $rel_auto);

		// update Pages referred from the $page
		if ($time) {
			// Page exists
			$this->cache->setItem($rel_name, $rel_new);
		}else if ($rel_file_exist){
			$this->cache->touchItem($rel_name);
		}

		// .ref: Pages refer to the $page
		$this->add(array_diff($rel_new, $rel_old), $rel_auto);
		$this->remove(array_diff($rel_old, $rel_new));

		global $WikiName, $autolink, $nowikiname, $search_non_list;

		// $page seems newly created, and matches with AutoLink
		if ($time && ! $rel_exist && $autolink
			&& (preg_match("/^$WikiName$/", $page !== false) ? $nowikiname : strlen($page) >= $autolink))
		{
			// Update all, because they __MAY__ refer the $page [HEAVY]
			$search_non_list = 1;
			$pages           = do_search($page, 'AND', TRUE);
			foreach ($pages as $_page) {
				if ($_page !== $page)
					$this->update($_page);
			}
		}

		if (! $time && $this->cache->hastItem($ref_name)) {
			foreach($this->cache->getItem($ref_name) as $ref_page=>$ref_auto){
				// Update pages they refer the $page by AutoLink only [HEAVY]
				if ($ref_auto) {
					$this->delete($ref_page, array($page));
				}
			}
		}
		return $rel_new;
	}

	// Init link cache (Called from link plugin)
	public function init() {
		global $cache;
		if (auth::check_role('readonly')) return; // Do nothing

		// Init database
		$this->cache->clearByPrefix(self::REL_PREFIX);
		$this->cache->clearByPrefix(self::REF_PREFIX);
		$this->cache->clearByPrefix(self::LINKS_PREFIX);

		$ref   = array(); // Reference from
		foreach (get_existpages() as $_page) {
			$rel   = array(); // Reference to
			foreach ($this->get_objects($_page) as $_obj) {
				if (! isset($_obj->type) || $_obj->type !== 'pagename' || $_obj->name === $_page || empty($_obj->name) ) continue;

				$_name = $_obj->name;
				if (is_a($_obj, 'Link_autoalias')) {
					$_alias = get_autoaliases($_name);
					if (! is_pagename($_alias))
						continue;	// not PageName
					$_name = $_alias;
				}
				$rel[] = $_name;
				if (! isset($ref[$_name][$_page]))
					$ref[$_name][$_page] = 1;
				if (! is_a($_obj, 'Link_autolink'))
					$ref[$_name][$_page] = 0;
			}
			$this->cache->setItem(self::REL_PREFIX.md5($_page), array_unique($rel));
		}
		unset($rel, $_page);

		foreach ($ref as $ref_page=>$ref_auto) {
			$this->cache->setItem(self::REF_PREFIX.md5($ref_page), $ref_auto);
		}
		unset($ref_page,$ref_auto);

		$this->cache->optimize();
	}

	private function add($add, $rel_auto){
		if (auth::check_role('readonly')) return; // Do nothing

		$rel_auto = array_flip($rel_auto);
		$ref = array();

		foreach ($add as $_page) {
			$all_auto = isset($rel_auto[$_page]);
			$is_page  = is_page($_page);
			$ref_name = self::REF_PREFIX.md5($_page);

			$ref[] = array($page, $all_auto);
			if ($this->cache->hasItem($ref_name)){
				foreach ($this->cache->getItem($ref_name) as $ref_page=>$ref_auto) {
					if (! $ref_auto) $all_auto = FALSE;
					if ($ref_page !== $this->page) $ref[] = array($this->page, ($all_auto ? 1 : 0));
				}
			}

			if ($is_page || ! $all_auto || count($ref) !== 0) {
				$this->cache->replaceItem($ref_name, array_unique($ref));
			}else{
				$this->cache->removeItem($ref_name);
			}
			unset($data, $ref);
		}
	}

	private function remove($del){
		if (auth::check_role('readonly')) return; // Do nothing

		foreach ($del as $_page) {
			$all_auto = TRUE;
			$is_page = is_page($_page);

			$ref_name = self::REF_PREFIX.md5($_page);
			if (! $cache['link']->hasItem($ref_name) ) continue;

			$ref = array();
			foreach ($this->cache->getItem($ref_name) as $ref_page=>$ref_auto) {
				if ($line !== $this->page) $ref[] = array($ref_page, $ref_auto);
			}

			if ($is_page || ! $all_auto || count($ref) == 1) {
				$this->cache->replaceItem($ref_name, array_unique($ref));
			}else{
				$this->cache->removeItem($ref_name);
			}
			unset($data, $ref);
		}
	}

	private function get_objects($page, $refresh = FALSE){
		$cache_name = self::LINKS_PREFIX.md5($page);
		if ($refresh){
			$this->cache->removeItem($cache_name);
		}

		if (! $this->cache->hasItem($cache_name) ){
			/*
			$result = array();
			foreach ($this->links_obj->get_objects(join('', preg_grep('/^(?!\/\/|\s)./', get_source($page))), $page) as $_obj) {
				if (! isset($_obj->type) || $_obj->type !== 'pagename' || $_obj->name === $page || empty($_obj->name) ) continue;
				$result[] = $_obj;
			}
			*/
			$result = $this->links_obj->get_objects(join('', preg_grep('/^(?!\/\/|\s)./', get_source($page))), $page);
			$this->cache->setItem($cache_name, $result);
		}else{
			$result = $this->cache->getItem($cache_name);
		}
		return $result;
	}
}

// for compatibility
function links_get_related_db($page){
	$links = new Links($page);
	return $links->get_related();
}

// Update link-relationships between pages
function links_update($page)
{
	$links = new Links($page);
	return $links->update();
}

// Init link cache (Called from link plugin)
function links_init()
{
	$links = new Links('');
	return $links->init();
}

/* End of file link.php */
/* Location: ./wiki-common/lib/link.php */