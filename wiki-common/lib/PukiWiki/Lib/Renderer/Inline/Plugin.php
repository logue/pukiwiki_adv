<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: Plugin.php,v 1.0.0 2013/01/05 15:46:00 Logue Exp $
// Copyright (C)
//   2012-2013 PukiWiki Advance Developers Team
// License: GPL v2 or (at your option) any later version

namespace PukiWiki\Lib\Renderer\Inline;
use PukiWiki\Lib\Renderer\InlineFactory;
// Inline plugins
class Plugin extends Inline
{
	var $pattern;
	var $plain,$param;

	function __construct($start)
	{
		parent::__construct($start);
	}

	function get_pattern()
	{
		$this->pattern =
			'&'.
			 '('.        // (1) plain
			  '(\w+)'.   // (2) plugin name
			   '(?:'.
			   '\('.
			    '((?:(?!\)[;{]).)*)'. // (3) parameter
			   '\)'.
			  ')?'.
			 ')';
		return $this->pattern .
			 '(?:'.
			  '\{'.
			   '((?:(?R)|(?!};).)*)'. // (4) body
			  '\}'.
			 ')?'.
			';';
	}

	function get_count()
	{
		return 4;
	}

	function set($arr, $page)
	{
		list($all, $this->plain, $name, $this->param, $body) = $this->splice($arr);

		// Re-get true plugin name and patameters (for PHP 4.1.2)
		$matches = array();
		if (preg_match('/^' . $this->pattern . '/x', $all, $matches)
			&& $matches[1] != $this->plain)
			list(, $this->plain, $name, $this->param) = $matches;

		return parent::setParam($page, $name, $body, 'plugin');
	}

	function toString()
	{
		$body = (empty($this->body)) ? null : InlineFactory::factory($this->body);
		$str = FALSE;

		// Try to call the plugin
		if (exist_plugin_inline($this->name))
			$str = do_plugin_inline($this->name, $this->param, $body);

		if ($str !== FALSE) {
			return $str; // Succeed
		} else {
			// No such plugin, or Failed
			$body = (($body == '') ? '' : '{' . $body . '}') . ';';
			return parent::make_line_rules(htmlsc('&' . $this->plain) . $body);
		}
	}
}

/* End of file Plugin.php */
/* Location: /vender/PukiWiki/Lib/Renderer/Inline/Plugin.php */