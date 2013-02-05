<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: BlacketName.php,v 1.0.0 2012/12/18 11:00:00 Logue Exp $
// Copyright (C)
//   2012 PukiWiki Advance Developers Team
// License: GPL v2 or (at your option) any later version

namespace PukiWiki\Lib\Renderer\Inline;
use PukiWiki\Lib\File\FileFactory;

// BracketNames
class BracketName extends Inline
{
	var $anchor, $refer;

	public function __construct($start)
	{
		parent::__construct($start);
	}

	public function getPattern()
	{
		global $WikiName, $BracketName;

		$s2 = $this->start + 2;
		return
			'\[\['.                     // Open bracket
			'(?:((?:(?!\]\]).)+)>)?'.   // (1) Alias
			'(\[\[)?'.                  // (2) Open bracket
			'('.                        // (3) PageName
			 '(?:' . $WikiName . ')'.
			 '|'.
			 '(?:' . $BracketName . ')'.
			')?'.
			'(\#(?:[a-zA-Z][\w-]*)?)?'. // (4) Anchor
			'(?(' . $s2 . ')\]\])'.     // Close bracket if (2)
			'\]\]';                     // Close bracket
	}

	public function getCount()
	{
		return 4;
	}

	public function setPattern($arr, $page)
	{
		global $WikiName;

		list(, $alias, , $name, $this->anchor) = $this->splice($arr);
		if (empty($name) && empty($this->anchor) ) return FALSE;

		if (empty($name) || ! preg_match('/^' . $WikiName . '$/', $name)) {
			if ( empty($alias) ) $alias = $name . $this->anchor;
			if ( !empty($name) ) {
				$name = self::getFullname($name, $page);
				if (! FileFactory::Wiki($name)->isValied()) return FALSE;
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
/* Location: /vender/PukiWiki/Lib/Renderer/Inline/BlacketName.php */