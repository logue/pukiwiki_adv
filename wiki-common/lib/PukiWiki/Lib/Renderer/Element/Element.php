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

use PukiWiki\Lib\Renderer\InlineFactory;

// Block elements
class Element
{
	var $parent;
	var $elements; // References of childs
	var $last;     // Insert new one at the back of the $last

	public function __construct()
	{
		$this->elements = array();
		$this->last     = & $this;
	}

	function setParent(& $parent)
	{
		$this->parent = & $parent;
	}

	function add(& $obj)
	{
		if ($this->canContain($obj)) {
			return $this->insert($obj);
		} else {
			return $this->parent->add($obj);
		}
	}

	function insert(& $obj)
	{
		global $info;
		if (gettype($obj) === 'object'){
			$obj->setParent($this);
			$this->elements[] = & $obj;

			$this->last = & $obj->last;
		}
		return $this->last;
	}

	function canContain(& $obj)
	{
		return TRUE;
	}

	function wrap($string, $tag, $param = '', $canomit = TRUE)
	{
		return ($canomit && empty($string)) ? '' :
			'<' . $tag . $param . '>' . $string . '</' . $tag . '>';
	}

	function toString()
	{
		$ret = array();
		foreach (array_keys($this->elements) as $key)
			$ret[] = $this->elements[$key]->toString();
		return join("\n", $ret);
	}

	function dump($indent = 0)
	{
		$ret = str_repeat(' ', $indent) . get_class($this) . "\n";
		$indent += 2;
		foreach (array_keys($this->elements) as $key) {
			$ret .= is_object($this->elements[$key]) ?
				$this->elements[$key]->dump($indent) : null;
				//str_repeat(' ', $indent) . $this->elements[$key];
		}
		return $ret;
	}
}