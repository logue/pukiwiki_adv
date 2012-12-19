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
use PukiWiki\Lib\Renderer\Element\ListContainer;
use PukiWiki\Lib\Renderer\Element\Factory;

class Contents_UList extends ListContainer
{
	function __construct($text, $level, $id)
	{
		// Reformatting $text
		// A line started with "\n" means "preformatted" ... X(
		make_heading($text);
		$text = "\n" . '<a href="#' . $id . '"'. ((IS_MOBILE) ? ' data-ajax="false" data-anchor="' . $id . '"' : '') . '>' . $text . '</a>' . "\n";
		parent::__construct('ul', 'li', '-', str_repeat('-', $level));
		$this->insert(Factory::factory('Inline', null, $text));
	}

	function setParent(& $parent)
	{
		global $_list_pad_str;

		parent::setParent($parent);
		$step   = $this->level;
		$margin = $this->left_margin;
		if (isset($parent->parent) && ($parent->parent instanceof ListContainer)) {
			$step  -= $parent->parent->level;
			$margin = 0;
		}
		$margin += $this->margin * ($step == $this->level ? 1 : $step);
		$this->style = sprintf($_list_pad_str, $this->level, $margin, $margin);
	}
}