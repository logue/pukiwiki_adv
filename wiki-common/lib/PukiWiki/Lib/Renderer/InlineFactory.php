<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: InlineFactory.php,v 1.0.0 2012/12/26 11:46:00 Logue Exp $
// Copyright (C)
//   2012 PukiWiki Advance Developers Team
// License: GPL v2 or (at your option) any later version

// Hyperlink decoration (make_link())
namespace PukiWiki\Lib\Renderer;
use PukiWiki\Lib\Renderer\InlineConverter;

class InlineFactory{
	public static function factory($string, $page = ''){
		global $vars;
		static $converter;
		if (!isset($converter)) $converter = new InlineConverter();
		$clone = $converter->get_clone($converter);
		return $clone->convert($string, !empty($page) ? $page : $vars['page']);
	}
}

/* End of file InlineFactory.php */
/* Location: ./vender/PukiWiki/Lib/Renderer/InlineFactory.php */