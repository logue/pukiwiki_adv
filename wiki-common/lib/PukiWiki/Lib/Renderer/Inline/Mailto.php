<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: Mailto.php,v 1.0.0 2012/12/18 11:00:00 Logue Exp $
// Copyright (C)
//   2012 PukiWiki Advance Developers Team
// License: GPL v2 or (at your option) any later version

namespace PukiWiki\Lib\Renderer\Inline;

// mailto: URL schemes
class Mailto extends Inline
{
	function __construct($start)
	{
		parent::__construct($start);
	}

	function get_pattern()
	{
		$s1 = $this->start + 1;
		return 
			'(?:'.
			 '\[\['.
			 '((?:(?!\]\]).)+)(?:>|:)'.     // (1) alias
			')?'.
			'([\w.-]+@[\w-]+\.[\w.-]+)'.    // (2) mailto
			'(?(' . $s1 . ')\]\])';	        // close bracket if (1)
	}

	function get_count()
	{
		return 2;
	}

	function set($arr, $page)
	{
		list(, $alias, $name) = $this->splice($arr);
		return parent::setParam($page, $name, '', 'mailto', $alias == '' ? $name : $alias);
	}

	function toString()
	{
		return '<a href="mailto:' . $this->name . '" rel="nofollow"><span class="pkwk-icon icon-mail">mailto:</span>' . $this->alias . '</a>';
	}
}

/* End of file Mailto.php */
/* Location: /vender/PukiWiki/Lib/Renderer/Inline/Mailto.php */