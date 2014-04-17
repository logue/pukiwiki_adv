<?php
/**
 * ブラケット名クラス
 *
 * @package   PukiWiki\Renderer\Inline
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2014 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: BracketName.php,v 1.0.2 2014/04/17 19:33:00 Logue Exp $
 */

namespace PukiWiki\Renderer\Inline;

use PukiWiki\Factory;
use PukiWiki\Renderer\RendererDefines;
use PukiWiki\Utility;

class BracketName extends Inline
{
	protected $anchor, $refer;

	public function __construct($start)
	{
		parent::__construct($start);
	}

	public function getPattern()
	{
		$s2 = $this->start + 2;
		// [[ (1) > (3) # (4) ]]
		// [[ (2) ]]
		return
			'\[\['.                     // Open bracket [[
			'(?:((?:(?!\]\]).)+)>)?'.   // (1) Alias >
			'(\[\[)?'.                  // (2) Open bracket
			'('.                        // (3) PageName
			 '(?:' . RendererDefines::WIKINAME_PATTERN . ')'.
			 '|'.
			 '(?:' . RendererDefines::BRACKETNAME_PATTERN . ')'.
			')?'.
			'(\#(?:[A-Za-z0-9][\w-]*)?)?'. // (4) Anchor
			'(?(' . $s2 . ')\]\])'.     // Close bracket if (2)
			'\]\]';                     // Close bracket ]]
	}

	public function getCount()
	{
		return 4;
	}

	public function setPattern($arr, $page)
	{
		list(, $alias, , $name, $this->anchor) = $this->splice($arr);
		if (empty($name) && empty($this->anchor) ) return FALSE;

		if (empty($name) || ! Utility::isWikiName($name)) {
			if ( empty($alias) ) $alias = $name . $this->anchor;
			if ( !empty($name) ) {
				$name = self::getFullname($name, $page);
				if (!empty($name) && ! Factory::Wiki($name)->isValied()) return FALSE;
			}
		}

		return parent::setParam($page, $name, null, 'pagename', $alias);
	}

	public function __toString()
	{
		return parent::setAutoLink(
			$this->name,
			$this->alias,
			$this->anchor,
			$this->page
		);
	}

	// Resolve relative / (Unix-like)absolute path of the page
	private function getFullname($name, $refer)
	{
		global $defaultpage;

		// 'Here'
		if ( empty($name) || $name == './') return $refer;

		// Absolute path
		if ($name{0} == '/') {
			$name = substr($name, 1);
			return empty($name) ? $defaultpage : $name;
		}

		// Relative path from 'Here'
		if (substr($name, 0, 2) === './') {
			$arrn    = preg_split('#/#', $name, -1, PREG_SPLIT_NO_EMPTY);
			$arrn[0] = $refer;
			return join('/', $arrn);
		}

		// Relative path from dirname()
		if (substr($name, 0, 3) == '../') {
			$arrn = preg_split('#/#', $name,  -1, PREG_SPLIT_NO_EMPTY);
			$arrp = preg_split('#/#', $refer, -1, PREG_SPLIT_NO_EMPTY);

			while (! empty($arrn) && $arrn[0] == '..') {
				array_shift($arrn);
				array_pop($arrp);
			}
			$name = ! empty($arrp) ? join('/', array_merge($arrp, $arrn)) :
				(! empty($arrn) ? $defaultpage . '/' . join('/', $arrn) : $defaultpage);
		}
		

		return $name;
	}
}

/* End of file BlacketName.php */
/* Location: /vendor/PukiWiki/Lib/Renderer/Inline/BlacketName.php */