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

// * Heading1
// ** Heading2
// *** Heading3
class Heading extends Element
{
	var $level;
	var $id;
	var $msg_top;
	var $text;

	function __construct(& $root, $text)
	{
		parent::__construct();

		$this->text = $text;
		$this->level = min(3, strspn($text, '*'));
		list($text, $this->msg_top, $this->id) = $root->getAnchor($text, $this->level);
		$this->insert(Factory::factory('Inline', null, $text));
		$this->level++; // h2,h3,h4
	}

	function & insert(& $obj)
	{
		parent::insert($obj);
		return $this->last = & $this;
	}

	function canContain(& $obj)
	{
		return FALSE;
	}

	function toString()
	{

		$fixed_anchor = make_heading($this->text, FALSE);
		$id = (empty($fixed_anchor)) ? $this->id : $fixed_anchor;
		return $this->msg_top .  $this->wrap(parent::toString(),
			'h' . $this->level, ' id="' . $id . '"');
	}
}