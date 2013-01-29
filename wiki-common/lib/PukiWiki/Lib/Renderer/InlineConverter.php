<?php
/**
 * インライン要素変換クラス
 *
 * @package   PukiWiki\Lib\Renderer
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2013 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: InlineConverter.php,v 1.0.0 2013/01/29 19:54:00 Logue Exp $
 */


namespace PukiWiki\Lib\Renderer;
use PukiWiki\Lib\Auth\Auth;
use PukiWiki\Lib\Renderer\Inline\Inline;
/**
 * Converters of inline element
 */
class InlineConverter
{
	var $converters; // as array()
	var $pattern;
	var $pos;
	var $result;

	function get_clone($obj) {
		static $clone_func;

		if (! isset($clone_func)) {
			$clone_func = create_function('$a', 'return clone $a;');
		}
		return $clone_func($obj);
	}

	function __clone() {
		$converters = array();
		foreach ($this->converters as $key=>$converter) {
			$converters[$key] = $this->get_clone($converter);
		}
		$this->converters = $converters;
	}

	function __construct($converters = NULL, $excludes = NULL)
	{
		global $autolink, $autoalias, $autoglossary;
		$autoglossary = 1;
		if ($converters === NULL) {
			$converters = array(
				'Plugin',           // Inline plugins
				'EasyRef',          // Easy Ref {{param|body}}
				'Note',             // Footnotes
				'Url',              // URLs
				'InterWiki',        // URLs (interwiki definition)
				'Mailto',           // mailto: URL schemes
				'InterWikiName',    // InterWikiName
				'BracketName',      // BracketName
				'WikiName',         // WikiName
				$autolink     ? 'AutoLink' : null,              // AutoLink(cjk,other)
				$autoalias    ? 'AutoAlias' : null,             // AutoAlias(cjk,other)
				$autoglossary ? 'Glossary' : null,              // AutoGlossary(cjk,other)
				$autolink     ? 'AutoLink_Alphabet' : null,     // AutoLink(alphabet)
				$autoalias    ? 'AutoAlias_Alphabet' : null,    // AutoAlias(alphabet)
				$autoglossary ? 'Glossary_Alphabet' : null,     // AutoGlossary(alphabet)
			);
		}

		if ($excludes !== NULL)
			$converters = array_diff($converters, $excludes);

		$this->converters = $patterns = array();
		$start = 1;

		foreach ($converters as $name) {
			if (!isset($name)) continue;
			$classname = 'PukiWiki\Lib\Renderer\Inline\\' . $name;
			$converter = new $classname($start);
			$pattern   = $converter->get_pattern();
			if ($pattern === FALSE) continue;

			$patterns[] = '(' . $pattern . ')';
			$this->converters[$start] = $converter;
			$start += $converter->get_count();
			++$start;
		}
		$this->pattern = join('|', $patterns);
	}

	function convert($string, $page)
	{
		$this->page   = $page;
		$this->result = array();
		$string = preg_replace_callback('/' . $this->pattern . '/x',
			array(& $this, 'replace'), $string);

		$arr = explode("\x08", Inline::make_line_rules(htmlsc($string)));
		$retval = '';
		while (! empty($arr)) {
			$retval .= array_shift($arr) . array_shift($this->result);
		}
		return trim($retval);
	}

	function replace($arr)
	{
		$obj = $this->get_converter($arr);

		$this->result[] = ($obj !== NULL && $obj->set($arr, $this->page) !== FALSE) ?
			$obj->toString() : Inline::make_line_rules(Utility::htmlsc($arr[0]));

		return "\x08"; // Add a mark into latest processed part
	}

	function get_objects($string, $page)
	{
		$matches = $arr = array();
		preg_match_all('/' . $this->pattern . '/x', $string, $matches, PREG_SET_ORDER);
		foreach ($matches as $match) {
			$obj = $this->get_converter($match);
			if ($obj->set($match, $page) !== FALSE) {
				$arr[] = $this->get_clone($obj);
				if ( !empty($obj->body) )
					$arr = array_merge($arr, $this->get_objects($obj->body, $page));
			}
		}
		return $arr;
	}

	function & get_converter(& $arr)
	{
		foreach (array_keys($this->converters) as $start) {
			if ($arr[$start] == $arr[0])
				return $this->converters[$start];
		}
		return NULL;
	}
}

/* End of file InlineConverter.php */
/* Location: /vender/PukiWiki/Lib/Renderer/Inline/InlineConverter.php */