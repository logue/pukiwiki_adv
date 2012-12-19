<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: WikiName.php,v 1.0.0 2012/12/18 11:00:00 Logue Exp $
// Copyright (C)
//   2012 PukiWiki Advance Developers Team
// License: GPL v2 or (at your option) any later version

namespace PukiWiki\Lib\Renderer\Inline;

// WikiNames
class WikiName extends Inline
{
	function __construct($start)
	{
		parent::__construct($start);
	}

	function get_pattern()
	{
		global $WikiName, $nowikiname;

		return $nowikiname ? FALSE : '(' . $WikiName . ')';
	}

	function get_count()
	{
		return 1;
	}

	function set($arr, $page)
	{
		list($name) = $this->splice($arr);
		return parent::setParam($page, $name, null, 'pagename', $name);
	}

	function toString()
	{
		return make_pagelink(
			$this->name,
			$this->alias,
			null,
			$this->page
		);
	}
}

/* End of file WikiName.php */
/* Location: /vender/PukiWiki/Lib/Renderer/Inline/WikiName.php */