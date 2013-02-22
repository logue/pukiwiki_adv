<?php
/**
 * 自動リンククラス
 *
 * @package   PukiWiki\Lib\Renderer\Inline
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2013 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: AutoLink.php,v 1.0.0 2013/01/29 19:54:00 Logue Exp $
 */

namespace PukiWiki\Lib\Renderer\Inline;

use PukiWiki\Lib\File\FileUtility;
use PukiWiki\Lib\Renderer\RendererDefines;
use PukiWiki\Lib\Renderer\Trie;

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

		list($auto, $auto_a, $forceignorepages) = self::getAutoLinkPattern(false);
		$this->auto   = $auto;
		$this->auto_a = $auto_a;
		$this->forceignorepages = $forceignorepages;
	}

	function getPattern()
	{
		return isset($this->auto) ? '(' . $this->auto . ')' : FALSE;
	}

	function getCount()
	{
		return 1;
	}

	function setPattern($arr, $page)
	{
		list($name) = $this->splice($arr);

		// Ignore pages listed, or Expire ones not found
		if (in_array($name, $this->forceignorepages) || ! is_page($name))
			return FALSE;

		return parent::setParam($page, $name, null, 'pagename', $name);
	}

	function __toString()
	{
		return parent::setAutoLink($this->name, $this->alias, null, $this->page, TRUE);
	}

	/**
	 * 自動リンクの正規表現パターンを生成
	 * @return string
	 */
	private function getAutoLinkPattern($force = false){
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
		global $autolink, $nowikiname;

		$config = new \Config('AutoLink');	// FIXME
		$config->read();
		$ignorepages	  = $config->get('IgnoreList');
		$forceignorepages = $config->get('ForceIgnoreList');
		unset($config);
		$auto_pages = array_merge($ignorepages, $forceignorepages);

		foreach (FileUtility::getExists() as $page)
			if (preg_match('/^' . RendererDefines::WIKINAME_PATTERN . '$/', $page) ?
				$nowikiname : strlen($page) >= $autolink)
				$auto_pages[] = $page;

		if (empty($auto_pages)) {
			$result = $result_a = $nowikiname ? '(?!)' : RendererDefines::WIKINAME_PATTERN;
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

	function getPattern()
	{
		return isset($this->auto_a) ? '(' . $this->auto_a . ')' : FALSE;
	}
}

/* End of file AutoLink.php */
/* Location: /vendor/PukiWiki/Lib/Renderer/Inline/AutoLink.php */