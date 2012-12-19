<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: convert_html.php,v 1.21.31 2012/10/30 12:02:00 Logue Exp $
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

// Inline elements
class Inline extends Element
{
	function __construct($text)
	{
		parent::__construct();
		$this->elements[] = trim((substr($text, 0, 1) == "\n") ?
			$text : make_link($text));
	}

	function insert(& $obj)
	{
		$this->elements[] = $obj->elements[0];
		return $this;
	}

	function canContain(& $obj)
	{
		//return is_a($obj, 'Inline');
		return ($obj instanceof Inline);
	}

	function toString()
	{
		global $line_break;
		return join(($line_break ? '<br />' . "\n" : "\n"), $this->elements);
	}

	function & toPara($class = '')
	{
		$obj = new Paragraph(null, $class);
		$obj->insert($this);
		return $obj;
	}
}