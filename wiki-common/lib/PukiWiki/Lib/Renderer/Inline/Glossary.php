<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: Glossary.php,v 1.0.0 2012/12/18 11:00:00 Logue Exp $
// Copyright (C)
//   2012 PukiWiki Advance Developers Team
// License: GPL v2 or (at your option) any later version

namespace PukiWiki\Lib\Renderer\Inline;
use PukiWiki\Lib\File\FileFactory;
use PukiWiki\Lib\Renderer\Trie;
use PukiWiki\Lib\Utility;
use PukiWiki\Lib\Config;

// Glossary
class Glossary extends Inline
{
	const AUTO_GLOSSARY_PATTERN_CACHE = 'glossary';
	const AUTO_GLOSSARY_TERM_CACHE = 'glossary-terms';
	const AUTO_GLOSSARY_TERM_PATTERN = '/^[:|]([^|]+)\|([^|]+)\|?$/';
	const MAX_TERM_LENGTH = 64;

	var $forceignorepages = array();
	var $auto;
	var $auto_a; // alphabet only

	function __construct($start)
	{
		parent::__construct($start);
		list($auto, $auto_a) = self::get_autoglossary_pattern();
		$this->auto = $auto;
		$this->auto_a = $auto_a;
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
		list($name) = $this->splice($arr);
		// Ignore words listed
		if (in_array($name,$this->forceignorepages))
		{
			return FALSE;
		}
		return parent::setParam($page,$name,null,'pagename',$name);
	}
	function toString()
	{
		$term = Utility::strip_bracket($this->name);
		$wiki = FileFactory::Wiki($term);
		$glossary = self::get_glossary_dict($term, true);
		if (! $wiki->has() ) {
			return '<abbr aria-describedby="tooltip" title="' . $glossary . '">' . $this->name . '</abbr>';
		}
		return '<a href="' . $wiki->getUri() . '" title="' . $glossary . ' ' . $wiki->passage(false) . '" aria-describedby="tooltip">' . $this->name . '</a>';
	}
	/**
	 * 長すぎる場合削減
	 * @param string $str
	 * @return string
	 */
	private static function expect_str($str){
		return mb_strlen($str) > self::MAX_TERM_LENGTH ? mb_substr($str, 0, self::MAX_TERM_LENGTH-3) . '...' : $str;
	}

	/**
	 * Glossaryの正規表現パターンを生成
	 * @return string
	 */
	private function get_autoglossary_pattern(){
		global $cache;
		static $pattern;

		if (! isset($pattern)) {
			// 用語マッチパターンキャッシュを生成
			if ($cache['wiki']->hasItem(self::AUTO_GLOSSARY_PATTERN_CACHE)) {
				$pattern = $cache['wiki']->getItem(self::AUTO_GLOSSARY_PATTERN_CACHE);
				$cache['wiki']->touchItem(self::AUTO_GLOSSARY_PATTERN_CACHE);
			}else{
				global $WikiName, $autoglossary, $nowikiname;

				$pairs = self::get_glossary_dict();
				foreach ($pairs as $term=>$val){
					if (preg_match('/^' . $WikiName . '$/', $term) ?
						$nowikiname : mb_strlen($term) >= $min_len)
						$auto_terms[] = $term;
				}

				if (empty($auto_terms)) {
					return array('(?!)', 'PukiWiki', 'PukiWiki');
				} else {
					$auto_terms = array_unique($auto_terms);
					sort($auto_terms, SORT_STRING);

					$auto_terms_a = array_values(preg_grep('/^[A-Z]+$/i', $auto_terms));
					$auto_terms   = array_values(array_diff($auto_terms,  $auto_terms_a));

					$result   = Trie::regex($auto_terms);
					$result_a = Trie::regex($auto_terms_a);
				}
				$pattern = array($result, $result_a);
				$cache['wiki']->setItem(self::AUTO_GLOSSARY_PATTERN_CACHE, $pattern);
			}
		}
		return $pattern;
	}
	/**
	 * Glossaryの用語と内容の辞書キャッシュを作成
	 * @param string $term 用語
	 * @param boolean $expect 要約するか（title属性の中に入れる文字列として出力するか）
	 * @return string
	 */
	public static function get_glossary_dict($term = '', $expect = false){
		global $glossarypage, $cache;
		static $pairs;
		if (! isset($pairs)) {
			$wiki = FileFactory::Wiki($glossarypage);
			$term_cache_meta = $cache['wiki']->getMetadata(self::AUTO_GLOSSARY_TERM_CACHE);
			if ($cache['wiki']->hasItem(self::AUTO_GLOSSARY_TERM_CACHE) &&
				$term_cache_meta['mtime'] > $wiki->getTime()) {
				$pairs = $cache['wiki']->getItem(self::AUTO_GLOSSARY_TERM_CACHE);
			}else{
				$matches = $pairs = array();
				$count = 0;
				foreach ($wiki->source() as $line) {
					if (preg_match(self::AUTO_GLOSSARY_TERM_PATTERN, $line, $matches)) {
						$name = trim($matches[1]);
						$pairs[$name] = trim($matches[2]);
					}
				}
				$cache['wiki']->setItem(self::AUTO_GLOSSARY_TERM_CACHE, $pairs);
				$cache['wiki']->removeItem(self::AUTO_GLOSSARY_PATTERN_CACHE);
			}
		}
		if (empty($term)) return $pairs;
		if (!isset($pairs[$term])) return null;
		$ret = htmlsc($pairs[$term]);
		return $expect ? self::expect_str(str_replace("'", "\\'",$ret)) : $ret;
	}
}

class Glossary_Alphabet extends Glossary
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

/* End of file Glossary.php */
/* Location: /vender/PukiWiki/Lib/Renderer/Inline/Glossary.php */