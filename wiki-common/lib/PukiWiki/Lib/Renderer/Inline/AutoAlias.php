<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: AutoAlias.php,v 1.0.0 2012/12/18 11:00:00 Logue Exp $
// Copyright (C)
//   2012 PukiWiki Advance Developers Team
// License: GPL v2 or (at your option) any later version
//
// Hyperlink-related functions
namespace PukiWiki\Lib\Renderer\Inline;

// AutoAlias
class AutoAlias extends Inline
{
	var $forceignorepages = array();
	var $auto;
	var $auto_a; // alphabet only
	var $aliases;

	function __construct($start)
	{
		global $autoalias, $aliaspage, $cache;

		parent::__construct($start);

		if (! $autoalias || $this->page == $aliaspage){
			return;
		}else{
			list($auto, $auto_a, $forceignorepages) = $cache['wiki']->getItem(PKWK_AUTOALIAS_REGEX_CACHE);

			$this->auto = $auto;
			$this->auto_a = $auto_a;
			$this->forceignorepages = $forceignorepages;
			$this->aliases = array();
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
	function set($arr,$page)
	{
		global $WikiName;

		list($name) = $this->splice($arr);
		// Ignore pages listed
		if (in_array($name, $this->forceignorepages)) {
			return FALSE;
		}
		return parent::setParam($page,$name,null,'pagename',$name);
	}
	function toString()
	{
		$this->aliases = get_autoaliases($this->name);
		if (! empty($this->aliases)) {
			$link = '[[' . $this->name  . ']]';
			return parent::make_link($link);
		}
		return '';
	}
}

class AutoAlias_Alphabet extends AutoAlias
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

/* End of file BlacketName.php */
/* Location: /vender/PukiWiki/Lib/Renderer/Inline/AutoAlias.php */