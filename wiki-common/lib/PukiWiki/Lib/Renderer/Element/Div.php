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

// Block plugin: #something (started with '#')
class Div extends Element
{
	var $name;
	var $param;

	function __construct($out)
	{
		parent::__construct();
		list(, $this->name, $this->param) = array_pad($out, 3, '');
	}

	function canContain(& $obj)
	{
		return FALSE;
	}

	function toString()
	{
		// Call #plugin
		return do_plugin_convert($this->name, $this->param);
	}
}