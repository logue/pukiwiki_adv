<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone
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

// LEFT:/CENTER:/RIGHT:
class Align extends Element
{
	var $align;

	function __construct($align)
	{
		parent::__construct();
		$this->align = $align;
	}

	function canContain(& $obj)
	{
		if ($obj instanceof Table || $obj instanceof YTable) {
			$obj->align = $this->align;
		}
		return ($obj instanceof Inline);
	}

	function toString()
	{
		return $this->wrap(parent::toString(), 'div', ' style="text-align:' . $this->align . '"');
	}
}