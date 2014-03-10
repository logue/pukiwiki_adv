<?php
/**
 * 用語集クラス
 *
 * @package   PukiWiki\Renderer\Inline
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2013 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Glossary.php,v 1.0.0 2013/01/29 19:54:00 Logue Exp $
 */

namespace PukiWiki\Renderer\Inline;

use PukiWiki\Factory;
use PukiWiki\Renderer\RendererDefines;
use PukiWiki\Renderer\Trie;
use PukiWiki\Utility;

// Glossary
class Glossary extends Inline
{
	// 用語集の正規表現キャッシュ名
	const AUTO_GLOSSARY_PATTERN_CACHE = 'glossary';
	// 用語集の用語キャッシュ名
	const AUTO_GLOSSARY_TERM_CACHE = 'glossary-terms';
	// 用語の定義のパターン
	const AUTO_GLOSSARY_TERM_PATTERN = '/^[:|]([^|]+)\|([^|]+)\|?$/';
	// 用語集のワードに使える最大文字数
	const MAX_TERM_LENGTH = 64;

	var $forceignorepages = array();
	var $auto;
	var $auto_a; // alphabet only

	public function __construct($start)
	{
		parent::__construct($start);
		list($auto, $auto_a) = self::getAutoGlossaryPattern();
		$this->auto = $auto;
		$this->auto_a = $auto_a;
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
		// Ignore words listed
		if (in_array($name,$this->forceignorepages))
		{
			return FALSE;
		}
		return parent::setParam($page,$name,null,'pagename',$name);
	}
	public function __toString()
	{
		$term = Utility::stripBracket($this->name);
		$wiki = Factory::Wiki($term);
		$glossary = self::getGlossary($term, true);
		if (! $wiki->has() ) {
			return '<abbr class="glossary" title="' . $glossary . '">' . $this->name . '</abbr>';
		}
		return '<abbr class="glossary" title="' . $glossary . '"><a href="' . $wiki->uri() . '">' . $this->name . '</a></abbr>';
	}
	/**
	 * 長すぎる場合削減
	 * @param string $str
	 * @return string
	 */
	private static function expectTooltip($str){
		return mb_strlen($str) > self::MAX_TERM_LENGTH ? mb_substr($str, 0, self::MAX_TERM_LENGTH-3) . '...' : $str;
	}
	/**
	 * Glossaryの正規表現パターンを生成
	 * @return string
	 */
	private static function getAutoGlossaryPattern($force = false){
		global $cache, $glossarypage;
		static $pattern;

		$wiki = Factory::Wiki($glossarypage);
		if (! $wiki->has()) return null;

		// Glossaryの更新チェック
		if ($cache['wiki']->hasItem(self::AUTO_GLOSSARY_TERM_CACHE)){
			if (self::getGlossaryTime() < $wiki->time()) {
				$force = true;
			}
		}

		// キャッシュ処理
		if ($force) {
			unset($pattern);
			$cache['wiki']->removeItem(self::AUTO_GLOSSARY_PATTERN_CACHE);
		}else if (!empty($pattern)) {
			return $pattern;
		}else if ($cache['wiki']->hasItem(self::AUTO_GLOSSARY_PATTERN_CACHE)) {
			$pattern = $cache['wiki']->getItem(self::AUTO_GLOSSARY_PATTERN_CACHE);
			$cache['wiki']->touchItem(self::AUTO_GLOSSARY_PATTERN_CACHE);
			return $pattern;
		}

		// パターンキャッシュを生成
		global $autoglossary, $nowikiname;

		// 用語集を取得
		$pairs = self::getGlossaryDict($force);
		foreach ($pairs as $term=>$val){
			if (preg_match('/^' . RendererDefines::WIKINAME_PATTERN . '$/', $term) ?
				$nowikiname : mb_strlen($term) >= $autoglossary)
				$auto_terms[] = $term;
		}

		if (empty($auto_terms)) {
			return array('(?!)', 'PukiWiki', 'PukiWiki');
		} else {
			// 用語辞書パターンからマッチパターン用の正規表現を生成
			$auto_terms = array_unique($auto_terms);
			sort($auto_terms, SORT_STRING);

			$auto_terms_a = array_values(preg_grep('/^[A-Z]+$/i', $auto_terms));
			$auto_terms   = array_values(array_diff($auto_terms,  $auto_terms_a));

			$result   = Trie::regex($auto_terms);
			$result_a = Trie::regex($auto_terms_a);
		}
		$pattern = array($result, $result_a);
		// パターンキャッシュを保存
		$cache['wiki']->setItem(self::AUTO_GLOSSARY_PATTERN_CACHE, $pattern);
		return $pattern;
	}
	/**
	 * 用語から内容を呼び出す
	 * @param string $term 用語
	 * @param boolean $expect 要約するか（title属性の中に入れる文字列として出力するか）
	 * @return string
	 */
	public static function getGlossary($term = '', $expect = false){
		$pairs = self::getGlossaryDict();

		if (empty($term)) return $pairs;
		if (!isset($pairs[$term])) return null;
		$ret = Utility::htmlsc($pairs[$term]);
		return $expect ? self::expectTooltip(str_replace("'", "\\'",$ret)) : $ret;
	}
	/**
	 * Glossaryのキャッシュの時間を取得（時間比較以外にtooltip.inc.phpの更新日時のパラメータとしても使う）
	 * @global array $cache
	 * @return int
	 */
	public static function getGlossaryTime(){
		global $cache;
		$term_cache_meta = $cache['wiki']->getMetadata(self::AUTO_GLOSSARY_TERM_CACHE);
		return $term_cache_meta['mtime'];
	}
	/**
	 * Glossaryページから用語と内容の辞書キャッシュを作成
	 * @param string $term 用語
	 * @param boolean $expect 要約するか（title属性の中に入れる文字列として出力するか）
	 * @return string
	 */
	private static function getGlossaryDict($force = false){
		global $glossarypage, $cache;
		static $pairs;

		$wiki = Factory::Wiki($glossarypage);
		if (! $wiki->has()) return array();

		// キャッシュ処理
		if ($force) {
			unset($pairs);
			$cache['wiki']->removeItem(self::AUTO_GLOSSARY_TERM_CACHE);
		}else if (!empty($pairs)) {
			return $pairs;
		}else if ($cache['wiki']->hasItem(self::AUTO_GLOSSARY_TERM_CACHE)) {
			$pairs = $cache['wiki']->getItem(self::AUTO_GLOSSARY_TERM_CACHE);
			$cache['wiki']->touchItem(self::AUTO_GLOSSARY_TERM_CACHE);
			return $pairs;
		}

		// 辞書キャッシュが存在しない場合自動生成
		$matches = $pairs = array();
		foreach (Factory::Wiki($glossarypage)->get() as $line) {
			if (preg_match(self::AUTO_GLOSSARY_TERM_PATTERN, $line, $matches)) {
				$name = trim($matches[1]);
				$pairs[$name] = trim($matches[2]);
			}
		}
		// 辞書キャッシュを保存
		$cache['wiki']->setItem(self::AUTO_GLOSSARY_TERM_CACHE, $pairs);
		// 正規表現パターンキャッシュを削除
		$cache['wiki']->removeItem(self::AUTO_GLOSSARY_TERM_CACHE);
		return $pairs;
	}
}

class Glossary_Alphabet extends Glossary
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

/* End of file Glossary.php */
/* Location: /vendor/PukiWiki/Lib/Renderer/Inline/Glossary.php */