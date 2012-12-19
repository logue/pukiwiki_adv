<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: AutoLink.php,v 1.0.0 2012/12/18 11:00:00 Logue Exp $
// Copyright (C)
//   2012 PukiWiki Advance Developers Team
// License: GPL v2 or (at your option) any later version
//
// Hyperlink-related functions
namespace PukiWiki\Lib\Renderer\Inline;

// AutoLinks
class AutoLink extends Inline
{
	var $forceignorepages = array();
	var $auto;
	var $auto_a; // alphabet only

	function __construct($start)
	{
		global $autolink, $cache;

		parent::__construct($start);

		if (! $autolink){
			return;
		}else{
			list($auto, $auto_a, $forceignorepages) = $cache['wiki']->getItem(PKWK_AUTOLINK_REGEX_CACHE);
			$this->auto   = $auto;
			$this->auto_a = $auto_a;
			$this->forceignorepages = $forceignorepages;
		}
	}

	function get_pattern()
	{
		return isset($this->auto) ? '(' . $this->auto . ')' : FALSE;
	}

	function get_count()
	{
		return 1;
	}

	function set($arr, $page)
	{
		list($name) = $this->splice($arr);

		// Ignore pages listed, or Expire ones not found
		if (in_array($name, $this->forceignorepages) || ! is_page($name))
			return FALSE;

		return parent::setParam($page, $name, null, 'pagename', $name);
	}

	function toString()
	{
		return parent::make_pagelink($this->name, $this->alias, null, $this->page, TRUE);
	}
}

class AutoLink_Alphabet extends AutoLink
{
	function __construct($start)
	{
		parent::__construct($start);
	}

	function get_pattern()
	{
		return isset($this->auto_a) ? '(' . $this->auto_a . ')' : FALSE;
	}
}

/* End of file AutoLink.php */
/* Location: /vender/PukiWiki/Lib/Renderer/Inline/AutoLink.php */