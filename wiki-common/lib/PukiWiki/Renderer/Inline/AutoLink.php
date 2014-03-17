<?php
/**
 * 自動リンククラス
 *
 * @package   PukiWiki\Renderer\Inline
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2014 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: AutoLink.php,v 1.0.2 2014/03/17 19:21:00 Logue Exp $
 */

namespace PukiWiki\Renderer\Inline;

use PukiWiki\Listing;
use PukiWiki\Renderer\RendererDefines;
use PukiWiki\Renderer\Trie;
use PukiWiki\Config\Config;

// AutoLinks
class AutoLink extends Inline
{
	const AUTO_LINK_PATTERN_CACHE = 'autolink';
	protected $forceignorepages = array();
	protected $auto;
	protected $auto_a; // alphabet only

	public function __construct($start)
	{
		parent::__construct($start);

		list($auto, $auto_a, $forceignorepages) = self::getAutoLinkPattern(false);
		$this->auto   = $auto;
		$this->auto_a = $auto_a;
		$this->forceignorepages = $forceignorepages;
	}

	public function getPattern()
	{
		return isset($this->auto) ? '(' . $this->auto . ')' : FALSE;
	}

	public function getCount()
	{
		return 1;
	}

	public function setPattern($arr, $page)
	{
		list($name) = $this->splice($arr);

		// Ignore pages listed, or Expire ones not found
		if (in_array($name, $this->forceignorepages) || ! is_page($name))
			return FALSE;

		return parent::setParam($page, $name, null, 'pagename', $name);
	}

	public function __toString()
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

		$config = new Config('AutoLink');	// FIXME
		$config->read();
		$ignorepages	  = $config->get('IgnoreList');
		$forceignorepages = $config->get('ForceIgnoreList');
		unset($config);
		$auto_pages = array_merge($ignorepages, $forceignorepages);

		foreach (Listing::pages('wiki') as $page) {
			if (preg_match('/^' . RendererDefines::WIKINAME_PATTERN . '$/', $page) ?
				$nowikiname : strlen($page) >= $autolink)
				$auto_pages[] = $page;
		}

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
	public function __construct($start)
	{
		parent::__construct($start);
	}

	public function getPattern()
	{
		return isset($this->auto_a) ? '(' . $this->auto_a . ')' : FALSE;
	}
}

/* End of file AutoLink.php */
/* Location: /vendor/PukiWiki/Lib/Renderer/Inline/AutoLink.php */