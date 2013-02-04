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
	public function __construct($start)
	{
		parent::__construct($start);
	}

	public function getPattern()
	{
		global $WikiName, $nowikiname;

		return $nowikiname ? FALSE : '(' . $WikiName . ')';
	}

	public function getCount()
	{
		return 1;
	}

	public function setPattern($arr, $page)
	{
		list($name) = $this->splice($arr);
		return parent::setParam($page, $name, null, 'pagename', $name);
	}

	public function __toString()
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