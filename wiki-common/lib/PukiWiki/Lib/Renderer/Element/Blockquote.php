<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: convert_html.php,v 1.0 2012/10/30 12:02:00 Logue Exp $
// Copyright (C)
//   2010-2012 PukiWiki Advance Developers Team
//   2005-2008 PukiWiki Plus! Team
//   2002-2005, 2007,2011 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// function 'convert_html()', wiki text parser
// and related classes-and-functions
namespace PukiWiki\Lib\Renderer\Element;
use PukiWiki\Lib\Renderer\Element\Factory;

// > Someting cited
// > like E-mail text
class Blockquote extends Element
{
	var $level;

	function __construct(& $root, $text)
	{
		parent::__construct();

		$head = substr($text, 0, 1);
		$this->level = min(3, strspn($text, $head));
		$text = ltrim(substr($text, $this->level));

		if ($head == '<') { // Blockquote close
			$level       = $this->level;
			$this->level = 0;
			$this->last  = $this->end($root, $level);
			if ($text !== '')
				$this->last = $this->last->insert(Factory::factory('Inline', null, $text));
		} else {
			$this->insert(Factory::factory('Inline', null, $text));
		}
	}

	function canContain(& $obj)
	{
		return (! is_a($obj, get_class($this)) || $obj->level >= $this->level);
	}

	function insert(& $obj)
	{
		if (!is_object($obj)) return;

		// BugTrack/521, BugTrack/545
		if ($obj instanceof inline)
			return parent::insert($obj->toPara(' class="style_blockquote"'));

		if ( $obj instanceof Blockquote && $obj->level == $this->level && count($obj->elements)) {
			$obj = & $obj->elements[0];
			if ($this->last instanceof Paragraph && count($obj->elements))
				$obj = & $obj->elements[0];
		}
		return parent::insert($obj);
	}

	function toString()
	{
		return $this->wrap(parent::toString(), 'blockquote');
	}

	function & end(& $root, $level)
	{
		$parent = & $root->last;

		while (is_object($parent)) {
			if ($parent instanceof Blockquote && $parent->level == $level)
				return $parent->parent;
			$parent = & $parent->parent;
		}
		return $this;
	}
}