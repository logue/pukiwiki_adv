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

// Lists (UL, OL, DL)
class ListContainer extends Element
{
	var $tag;
	var $tag2;
	var $level;
	var $style;
	var $margin;
	var $left_margin;

	function __construct($tag, $tag2, $head, $text)
	{
		parent::__construct();

		$var_margin      = '_' . $tag . '_margin';
		$var_left_margin = '_' . $tag . '_left_margin';
		global $$var_margin, $$var_left_margin;

		$this->margin      = $$var_margin;
		$this->left_margin = $$var_left_margin;

		$this->tag   = $tag;
		$this->tag2  = $tag2;
		$this->level = min(3, strspn($text, $head));
		$text = ltrim(substr($text, $this->level));

		parent::insert(new ListElement($this->level, $tag2));
		if ( !empty($text) )
			$this->last = $this->last->insert(Factory::factory('Inline', null, $text));
	}

	function canContain(& $obj)
	{
		//return (! is_a($obj, 'ListContainer')
		return (!($obj instanceof ListContainer)
			|| ($this->tag == $obj->tag && $this->level == $obj->level));
	}

	function setParent(& $parent)
	{
		global $_list_pad_str;

		parent::setParent($parent);

		$step = $this->level;
		if (isset($parent->parent) && ($parent->parent instanceof ListContainer))
			$step -= $parent->parent->level;

		$margin = $this->margin * $step;
		if ($step == $this->level)
			$margin += $this->left_margin;

		$this->style = sprintf($_list_pad_str, $this->level, $margin, $margin);
	}

	function insert(& $obj)
	{
		if (! is_a($obj, get_class($this)))
			return $this->last = $this->last->insert($obj);

		// Break if no elements found (BugTrack/524)
		if (count($obj->elements) == 1 && empty($obj->elements[0]->elements))
			return $this->last->parent; // up to ListElement

		// Move elements
		foreach(array_keys($obj->elements) as $key)
			parent::insert($obj->elements[$key]);

		return $this->last;
	}

	function toString()
	{
		return $this->wrap(parent::toString(), $this->tag, $this->style);
	}
}