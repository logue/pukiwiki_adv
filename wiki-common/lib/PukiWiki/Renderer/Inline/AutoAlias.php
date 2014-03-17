<?php
/**
 * 自動エイリアス変換クラス
 *
 * @package   PukiWiki\Renderer
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2014 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: InlineConverter.php,v 1.0.1 2014/03/17 19:22:00 Logue Exp $
 */

namespace PukiWiki\Renderer\Inline;

use PukiWiki\Renderer\InlineFactory;
use PukiWiki\Factory;
use PukiWiki\Renderer\Trie;
use PukiWiki\Config\Config;

class AutoAlias extends Inline
{
	const AUTOALIAS_PATTERN_CACHE = 'autoalias';
	const AUTOALIAS_TERM_CACHE = 'autoalias-terms';
	const AUTOALIAS_TERM_PATTERN = '\[\[((?:(?!\]\]).)+)>((?:(?!\]\]).)+)\]\]';

	protected $forceignorepages = array();
	protected $auto;
	protected $auto_a; // alphabet only
	protected $aliases;

	public function __construct($start)
	{
		global  $aliaspage;

		parent::__construct($start);

		if ($this->page == $aliaspage){
			return;
		}
		list($auto, $auto_a, $forceignorepages) = self::getAutoAliasPattern();

		$this->auto = $auto;
		$this->auto_a = $auto_a;
		$this->forceignorepages = $forceignorepages;
		$this->aliases = array();
	}
	public function getPattern()
	{
		return isset($this->auto) ? '(' . $this->auto . ')' : FALSE;
	}
	public function getCount()
	{
		return 1;
	}
	public function setPattern($arr,$page)
	{
		list($name) = $this->splice($arr);
		// Ignore pages listed
		if (in_array($name, $this->forceignorepages)) {
			return FALSE;
		}
		return parent::setParam($page,$name,null,'pagename',$name);
	}
	public function __toString()
	{
		$this->aliases = self::getAutoAliasDict($this->name);
		if (empty($this->aliases)) return;

		$link = '[[' . $this->name  . ']]';
		return InlineFactory::factory($link);
	}
	/**
	 * AutoAliasの正規表現パターンを生成
	 * @return string
	 */
	private function getAutoAliasPattern($force = false){
		global $cache, $aliaspage;
		static $pattern;

		$wiki = Factory::Wiki($aliaspage);
		if (! $wiki->has()) return null;

		// AutoAliasNameの更新チェック
		if ($cache['wiki']->hasItem(self::AUTOALIAS_PATTERN_CACHE)){
			$term_cache_meta = $cache['wiki']->getMetadata(self::AUTOALIAS_PATTERN_CACHE);
			if ($term_cache_meta['mtime'] < $wiki->time()) {
				$force = true;
			}
		}

		// キャッシュ処理
		if ($force) {
			unset($pattern);
			$cache['wiki']->removeItem(self::AUTOALIAS_PATTERN_CACHE);
		}else if (!empty($pattern)) {
			return $pattern;
		}else if ($cache['wiki']->hasItem(self::AUTOALIAS_PATTERN_CACHE)) {
			$pattern = $cache['wiki']->getItem(self::AUTOALIAS_PATTERN_CACHE);
			$cache['wiki']->touchItem(self::AUTOALIAS_PATTERN_CACHE);
			return $pattern;
		}

		global $WikiName, $autolink, $nowikiname;

		$config = new Config('AutoAlias');
		$config->read();
		$ignorepages	  = $config->get('IgnoreList');
		$forceignorepages = $config->get('ForceIgnoreList');
		unset($config);
		$auto_pages = array_merge($ignorepages, $forceignorepages);

		foreach (self::getAutoAliasDict($force) as $term=>$val){
			if (preg_match('/^' . $WikiName . '$/', $term) ?
				$nowikiname : mb_strlen($term) >= $autolink)
				$auto_terms[] = $term;
		}

		if (empty($auto_terms)) {
			$result = $result_a = $nowikiname ? '(?!)' : $WikiName;
		} else {
			$auto_terms = array_unique($auto_terms);
			sort($auto_terms, SORT_STRING);

			$auto_terms_a = array_values(preg_grep('/^[A-Z]+$/i', $auto_terms));
			$auto_terms   = array_values(array_diff($auto_terms,  $auto_terms_a));

			$result   = Trie::regex($auto_terms);
			$result_a = Trie::regex($auto_terms_a);
		}
		$pattern = array($result, $result_a, $forceignorepages);
		$cache['wiki']->setItem(self::AUTOALIAS_PATTERN_CACHE, $pattern);

		return $pattern;
	}
	/**
	 * AutoAliasNameからエイリアス先を呼び出す
	 * @param string $term エイリアス名
	 * @return string
	 */
	public static function getAutoAlias($term = ''){
		$pairs = self::getAutoAliasDict();

		if (!isset($pairs[$term])) return null;
		return $pairs[$term];
	}
	/**
	 * AutoAliasNameページからエイリアス名とエイリアス先の辞書キャッシュを作成
	 * @param string $term 用語
	 * @return string
	 */
	public static function getAutoAliasDict($force = false)
	{
		global $cache, $aliaspage, $autoalias_max_words;
		static $pairs;

		$wiki = Factory::Wiki($aliaspage);
		if (! $wiki->has()) return array();

		// キャッシュ処理
		if ($force) {
			unset($pairs);
			$cache['wiki']->removeItem(self::AUTOALIAS_TERM_CACHE);
		}else if (!empty($pairs)) {
			return $pairs;
		}else if ($cache['wiki']->hasItem(self::AUTOALIAS_TERM_CACHE)) {
			$pairs = $cache['wiki']->getItem(self::AUTOALIAS_TERM_CACHE);
			$cache['wiki']->touchItem(self::AUTOALIAS_TERM_CACHE);
			return $pairs;
		}

		$matches = array();
		$count = 0;
		$max   = max($autoalias_max_words, 0);
		if (preg_match_all('/' . self::AUTOALIAS_TERM_PATTERN . '/x', $wiki->get(true), $matches, PREG_SET_ORDER)) {
			foreach($matches as $key => $value) {
				if ($count == $max) break;
				$name = trim($value[1]);
				++$count;
				$pairs[$name] = trim($value[2]);
				unset($matches[$key]);
			}
		}
		$pairs = array_unique($pairs);
		$cache['wiki']->setItem(self::AUTOALIAS_TERM_CACHE, $pairs);
		return $pairs;
	}
}

class AutoAlias_Alphabet extends AutoAlias
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

/* End of file BlacketName.php */
/* Location: /vender/PukiWiki/Lib/Renderer/Inline/AutoAlias.php */