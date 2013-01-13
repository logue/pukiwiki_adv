<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: AutoAlias.php,v 1.0.0 2013/01/05 15:46:00 Logue Exp $
// Copyright (C)
//   2012-2013 PukiWiki Advance Developers Team
// License: GPL v2 or (at your option) any later version
//
// Hyperlink-related functions
namespace PukiWiki\Lib\Renderer\Inline;
use PukiWiki\Lib\Renderer\InlineFactory;
use PukiWiki\Lib\File\FileFactory;
use PukiWiki\Lib\Renderer\Trie;
// AutoAlias
class AutoAlias extends Inline
{
	const AUTO_AUTOALIAS_PATTERN_CACHE = 'autoalias';
	const AUTO_AUTOALIAS_TERM_CACHE = 'autoalias-terms';

	var $forceignorepages = array();
	var $auto;
	var $auto_a; // alphabet only
	var $aliases;

	function __construct($start)
	{
		global  $aliaspage;

		parent::__construct($start);

		if ($this->page == $aliaspage){
			return;
		}
		list($auto, $auto_a, $forceignorepages) = self::get_autoalias_pattern();

		$this->auto = $auto;
		$this->auto_a = $auto_a;
		$this->forceignorepages = $forceignorepages;
		$this->aliases = array();
	}
	function get_pattern()
	{
		return isset($this->auto) ? '(' . $this->auto . ')' : FALSE;
	}
	function get_count()
	{
		return 1;
	}
	function set($arr,$page)
	{
		global $WikiName;

		list($name) = $this->splice($arr);
		// Ignore pages listed
		if (in_array($name, $this->forceignorepages)) {
			return FALSE;
		}
		return parent::setParam($page,$name,null,'pagename',$name);
	}
	function toString()
	{
		$this->aliases = get_autoaliases($this->name);
		if (empty($this->aliases)) return;

		$link = '[[' . $this->name  . ']]';
		return InlineFactory::factory($link);
	}
	/**
	 * AutoAliasの正規表現パターンを生成
	 * @return string
	 */
	private function get_autoalias_pattern(){
		global $cache;
		static $pattern;

		if (! isset($pattern)) {
			// 用語マッチパターンキャッシュを生成
			if ($cache['wiki']->hasItem(self::AUTO_AUTOALIAS_PATTERN_CACHE)) {
				$pattern = $cache['wiki']->getItem(self::AUTO_AUTOALIAS_PATTERN_CACHE);
				$cache['wiki']->touchItem(self::AUTO_AUTOALIAS_PATTERN_CACHE);
			}else{
				global $WikiName, $autolink, $nowikiname;

				$config = new \Config('AutoAlias');
				$config->read();
				$ignorepages	  = $config->get('IgnoreList');
				$forceignorepages = $config->get('ForceIgnoreList');
				unset($config);
				$auto_pages = array_merge($ignorepages, $forceignorepages);

				foreach (self::get_autoalias_dict() as $term=>$val){
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
				$cache['wiki']->setItem(self::AUTO_AUTOALIAS_PATTERN_CACHE, $pattern);
			}
		}
		return $pattern;
	}
	/**
	 * AutoAliasNameページから用語と内容の辞書キャッシュを作成
	 * @param string $term 用語
	 * @param boolean $expect 要約するか（title属性の中に入れる文字列として出力するか）
	 * @return string
	 */
	public static function get_autoalias_dict($word = '')
	{
		global $cache, $aliaspage, $autoalias_max_words;
		static $pairs;

		if (! isset($pairs)) {
			
			$wiki = FileFactory::Wiki($aliaspage);
			if ($wiki->has()){
				$pairs = array();
				$pattern =
					'\[\['.                # open bracket
					'((?:(?!\]\]).)+)>'.   # (1) alias name
					'((?:(?!\]\]).)+)'.    # (2) alias link
					'\]\]';                # close bracket

				$term_cache_meta = $cache['wiki']->getMetadata(self::AUTO_AUTOALIAS_TERM_CACHE);
				if ($cache['wiki']->hasItem(self::AUTO_AUTOALIAS_TERM_CACHE) &&
					$term_cache_meta['mtime'] > $wiki->getTime()) {
					$pairs = $cache['wiki']->getItem(self::AUTO_AUTOALIAS_TERM_CACHE);
				}else{
					$matches = array();
					$count = 0;
					$max   = max($autoalias_max_words, 0);
					if (preg_match_all('/' . $pattern . '/x', $wiki->get(true), $matches, PREG_SET_ORDER)) {
						foreach($matches as $key => $value) {
							if ($count == $max) break;
							$name = trim($value[1]);
							if (! isset($pairs[$name])) {
								$paris[$name] = array();
							}
							++$count;
							$pairs[$name][] = trim($value[2]);
							unset($matches[$key]);
						}
					}
					foreach (array_keys($pairs) as $name) {
						$pairs[$name] = array_unique($pairs[$name]);
					}
					$cache['wiki']->setItem(self::AUTO_AUTOALIAS_TERM_CACHE, $pairs);
					$cache['wiki']->removeItem(self::AUTO_AUTOALIAS_PATTERN_CACHE);
				}
			}
		}
		if (empty($term)) return $pairs;
		if (!isset($pairs[$term])) return null;
		return $pairs[$term];
	}
}

class AutoAlias_Alphabet extends AutoAlias
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

/* End of file BlacketName.php */
/* Location: /vender/PukiWiki/Lib/Renderer/Inline/AutoAlias.php */