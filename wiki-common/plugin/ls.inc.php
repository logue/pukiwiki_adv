<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: ls.inc.php,v 1.11.3 2012/10/17 09:48:45 Logue Exp $
// Copyright (C)
//   2010-2012 PukiWiki Advance Developers Team
//   2005-2006 PukiWiki Plus! Team
//   2002-2004, 2007 PukiWiki Developers Team
//   2002      Y.MASUI GPL2 http://masui.net/pukiwiki/ masui@masui.net
// License: GPL version 2
//
// List plugin
use PukiWiki\Auth\Auth;
use PukiWiki\Renderer\RendererFactory;
function plugin_ls_convert()
{
	global $vars;

	$with_title = FALSE;

	if (func_num_args())
	{
		$args = func_get_args();
		$with_title = in_array('title',$args);
	}

	$prefix = $vars['page'].'/';

	$page  = isset($vars['page']) ? $vars['page'] : '';

	foreach (Auth::get_existpages() as $page)
	{
		if (strpos($page,$prefix) === 0)
		{
			$pages[] = $page;
		}
	}
	natcasesort($pages);

	$ls = array();
	foreach ($pages as $page)
	{
		$comment = '';
		if ($with_title) {
			$array = file_head(get_filename($page), 1);
			if ($array) {
				$comment = ' - ' .
					preg_replace(
						array(
							'/^(\*{1,3}.*)\[#[A-Za-z][\w-]+\](.*)$/S',	// Remove fixed-heading anchors
							'/^(?:-+|\*+)/',	// Remove syntax garbages at this situation
						),
						array(
							'$1$2',
							null,
						),
						current($array)
					);
			}
		}
		$ls[] = "-[[$page]] $comment";
	}

	return RendererFactory::factory($ls);
}
/* End of file ls.inc.php */
/* Location: ./wiki-common/plugin/ls.inc.php */