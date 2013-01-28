<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: AutoLink.php,v 1.0.0 2013/01/05 15:46:00 Logue Exp $
// Copyright (C)
//   2012-2013 PukiWiki Advance Developers Team
// License: GPL v2 or (at your option) any later version
//
// Hyperlink-related functions
namespace PukiWiki\Lib\Renderer\Inline;
use PukiWiki\Lib\Renderer\Trie;
use PukiWiki\Lib\File\FileUtility;

// AutoLinks
class AutoLink extends Inline
{
	const AUTO_LINK_PATTERN_CACHE = 'autolink';
	var $forceignorepages = array();
	var $auto;
	var $auto_a; // alphabet only

	function __construct($start)
	{
		parent::__construct($start);

		list($auto, $auto_a, $forceignorepages) = self::get_autolink_pattern(false);
		$this->auto   = $auto;
		$this->auto_a = $auto_a;
		$this->forceignorepages = $forceignorepages;
	}

	function get_pattern()
	{
		return isset($this->auto) ? '(' . $this->auto . ')' : FALSE;
	}

	function get_count()
	{
		return 1;
	}

	function set($arr, $page)
	{
		list($name) = $this->splice($arr);

		// Ignore pages listed, or Expire ones not found
		if (in_array($name, $this->forceignorepages) || ! is_page($name))
			return FALSE;

		return parent::setParam($page, $name, null, 'pagename', $name);
	}

	function toString()
	{
		return parent::make_pagelink($this->name, $this->alias, null, $this->page, TRUE);
	}

	/**
	 * 自動リンクの正規表現パターンを生成
	 * @return string
	 */
	private function get_autolink_pattern($force = false){
		global $cache;
		static $pattern;

		// キャッシュ処理
		if ($force) {
			unset($pattern);
			$cache['wiki']->removeItem(self::AUTO_LINK_PATTERN_CACHE);
		}else if (!empty($pattern)) {
			return $pattern;
		}else if ($cache['wiki']->hasItem(self::AUTO_LINK_PATTERN_CACHE)) {
			$pattern = $cache['wiki']->getItem(self::AUTO_LINK_PATTERN_CACHE);
			$cache['wiki']->touchItem(self::AUTO_LINK_PATTERN_CACHE);
			return $pattern;
		}

		// 用語マッチパターンキャッシュを生成
		global $WikiName, $autolink, $nowikiname;

		$config = new \Config('AutoLink');	// FIXME
		$config->read();
		$ignorepages	  = $config->get('IgnoreList');
		$forceignorepages = $config->get('ForceIgnoreList');
		unset($config);
		$auto_pages = array_merge($ignorepages, $forceignorepages);

		foreach (FileUtility::get_exsists() as $page)
			if (preg_match('/^' . $WikiName . '$/', $page) ?
				$nowikiname : strlen($page) >= $autolink)
				$auto_pages[] = $page;

		if (empty($auto_pages)) {
			$result = $result_a = $nowikiname ? '(?!)' : $WikiName;
		} else {
			$auto_pages = array_unique($auto_pages);
			sort($auto_pages, SORT_STRING);

			$auto_pages_a = array_values(preg_grep('/^[A-Z]+$/i', $auto_pages));
			$auto_pages   = array_values(array_diff($auto_pages,  $auto_pages_a));

			// 正規表現を最適化
			$result   = Trie::regex($auto_pages);
			$result_a = Trie::regex($auto_pages_a);
		}

		$pattern = array($result, $result_a, $forceignorepages);
		$cache['wiki']->setItem(self::AUTO_LINK_PATTERN_CACHE, $pattern);
		return $pattern;
	}
}

class AutoLink_Alphabet extends AutoLink
{
	function __construct($start)
	{
		parent::__construct($start);
	}

	function get_pattern()
	{
		return isset($this->auto_a) ? '(' . $this->auto_a . ')' : FALSE;
	}
}

/* End of file AutoLink.php */
/* Location: /vender/PukiWiki/Lib/Renderer/Inline/AutoLink.php */