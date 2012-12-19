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

// : definition1 | description1
// : definition2 | description2
// : definition3 | description3
class DList extends ListContainer
{
	function __construct($out)
	{
		parent::__construct('dl', 'dt', ':', $out[0]);
		$this->last = Element::insert(new ListElement($this->level, 'dd'));
		if ( !empty($out[1]) )
			$this->last = $this->last->insert(Factory::factory('Inline', null, $out[1]));
	}
}