<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: InterWiki.php,v 1.0.0 2012/12/18 11:00:00 Logue Exp $
// Copyright (C)
//   2012 PukiWiki Advance Developers Team
// License: GPL v2 or (at your option) any later version
//
// Hyperlink-related functions
namespace PukiWiki\Lib\Renderer\Inline;

use Zend\Uri\UriFactory;

// URLs (InterWiki definition on "InterWikiName")
class InterWiki extends Inline
{
	function __construct($start)
	{
		parent::__construct($start);
	}

	function get_pattern()
	{
		return 
		'\['.       // open bracket
		'('.        // (1) url
		 '(?:(?:https?|ftp|news):\/\/|\.\.?\/)[!~*\'();\/?:\@&=+\$,%#\w.-]*'.
		')'.
		'\s'.
		'([^\]]+)'. // (2) alias
		'\]';       // close bracket
	}

	function get_count()
	{
		return 2;
	}

	function set($arr, $page)
	{
		list(, $name, $alias) = $this->splice($arr);
		return parent::setParam($page, htmlsc($name), null, 'url', $alias);
	}

	function toString()
	{
		global $nofollow;
		$rel = 'external' . ($nofollow === TRUE) ? ' nofollow': '';
		$target = empty($this->redirect) ? $this->name : $this->redirect.rawurlencode($this->name);

		if (extension_loaded('intl')){
			// Fix punycode URL
			$purl = parse_url($target);
			$url = preg_replace('/'.$purl['host'].'/', idn_to_ascii($purl['host']), $target);
		}else{
			$url = $target;
		}

		return open_uri_in_new_window('<a href="' . $url . '" rel="' . $rel . '">' . $this->alias . '</a>', get_class($this));
	}
}

/* End of file InterWiki.php */
/* Location: /vender/PukiWiki/Lib/Renderer/Inline/InterWiki.php */
