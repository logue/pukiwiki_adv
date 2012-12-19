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

// Paragraph: blank-line-separated sentences
class Paragraph extends Element
{
	var $param;

	function __construct($text, $param = '')
	{
		parent::__construct();
		$this->param = $param;
		if (empty($text)) return;

		if (substr($text, 0, 1) == '~')
			$text = ' ' . substr($text, 1);

		$this->insert(Factory::factory('Inline', null, $text));
	}

	function canContain(& $obj)
	{
		//return is_a($obj, 'Inline');
		return ($obj instanceof Inline);
	}

	function toString()
	{
		return $this->wrap(parent::toString(), 'p', $this->param);
	}
}