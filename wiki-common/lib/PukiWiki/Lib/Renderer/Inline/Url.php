<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: Url.php,v 1.0.0 2012/12/18 11:00:00 Logue Exp $
// Copyright (C)
//   2012 PukiWiki Advance Developers Team
// License: GPL v2 or (at your option) any later version

namespace PukiWiki\Lib\Renderer\Inline;

// URLs
class Url extends Inline
{
	function __construct($start)
	{
		parent::__construct($start);
	}

	function get_pattern()
	{
		$s1 = $this->start + 1;
		return 
			'(\[\['.                // (1) open bracket
			 '((?:(?!\]\]).)+)'.    // (2) alias
			 '(?:>|:)'.
			')?'.
			'('.                    // (3) url
			 '(?:(?:https?|ftp|news):\/\/|mailto:)[\w\/\@\$()!?&%#:;.,~\'=*+-]+'.
			')'.
			'(?('.$s1.')\]\])';         // close bracket
	}

	function get_count()
	{
		return 3;
	}

	function set($arr, $page)
	{
		list(, , $alias, $name) = $this->splice($arr);
		return parent::setParam($page, htmlsc($name), null, 'url', empty($alias) ? $name : $alias);
	}

	function toString()
	{
		global $nofollow;
		$rel = 'external' . ($nofollow === TRUE) ? ' nofollow': '';

		$target = (empty($this->redirect)) ? $this->name : $this->redirect.rawurlencode($this->name);
		return open_uri_in_new_window('<a href="' . $target . '" rel="' . $rel . '">' . $this->alias . '</a>', get_class($this));
	}
}

/* End of file Url.php */
/* Location: /vender/PukiWiki/Lib/Renderer/Inline/Url.php */