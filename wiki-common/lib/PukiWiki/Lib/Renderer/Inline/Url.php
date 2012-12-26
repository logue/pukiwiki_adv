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
			'('.                    // (3) scheme
			 '(?:(?:https?|ftp|news|site):\/\/|mailto:)'.
			')'.
			'([\w.-]+@)?'.          // (4) mailto name
			'([^\/"<>\s]+|\/)'.     // (5) host
			'('.                    // (6) URI
			 '[\w\/\@\$()!?&%#:;.,~\'=*+-]*'.
			')'.
			'(?(' . $s1 . ')\]\])'; // close bracket
	}

	function get_count()
	{
		return 6;
	}

	function set($arr, $page)
	{
		list (,$bracket, $alias, $scheme, $mail, $host, $uri) = $this->splice($arr);
		$this->has_bracket = (substr($bracket, 0, 2) === '[[');
		$this->host = $host;
		if (extension_loaded('intl') && $host !== '/' && preg_match('/[^A-Za-z0-9.-]/', $host)) {
			$host = idn_to_ascii($host);
		}
		$name = $scheme . $mail . $host;
		// https?:/// -> $this->cont['ROOT_URL']
		$name = preg_replace('#^(?:site:|https?:/)//#', ROOT_URI, $name) . $uri;
		if (!$alias) {
			// Punycode化されたドメインかを判別
			$alias = (extension_loaded('intl') && strtolower(substr($host, 0, 4)) === 'xn--') ?
				($scheme . $mail . idn_to_utf8($host) . $uri)
				: $name;
			if (strpos($alias, '%') !== FALSE) {
				$alias = mb_convert_encoding(rawurldecode($alias), SOURCE_ENCODING , 'AUTO');
			}
		}
		return parent :: setParam($page, htmlsc($name), '', ($mail ? 'mailto' : 'url'), $alias);
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