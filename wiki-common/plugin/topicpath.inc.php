<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: topicpath.inc.php,v 1.9.7 2011/02/05 10:21:00 Logue Exp $
// Copyright (C)
//   2011      PukiWiki Advance Developers Team
//   2004-2008 PukiWiki Plus! Team
//   2004-2005,2011 PukiWiki Developers Team
// License: GPL (any version)
//
// 'topicpath' plugin
use PukiWiki\Lib\Auth\Auth;
// Show a link to $defaultpage or not
defined('PLUGIN_TOPICPATH_TOP_DISPLAY') or define('PLUGIN_TOPICPATH_TOP_DISPLAY', 1);
// Label for $defaultpage
defined('PLUGIN_TOPICPATH_TOP_LABEL') or define('PLUGIN_TOPICPATH_TOP_LABEL', 'Top');
// Show the page itself or not
defined('PLUGIN_TOPICPATH_THIS_PAGE_DISPLAY') or define('PLUGIN_TOPICPATH_THIS_PAGE_DISPLAY', 1);
// If PLUGIN_TOPICPATH_THIS_PAGE_DISPLAY, add a link to itself
defined('PLUGIN_TOPICPATH_THIS_PAGE_LINK') or define('PLUGIN_TOPICPATH_THIS_PAGE_LINK', 0);

function plugin_topicpath_convert()
{
	global $topicpath, $pkwk_dtd;
	if (isset($topicpath) && $topicpath == false) return '';
	
	$ret = plugin_topicpath_inline();
	
	if ($ret != ''){
		return (($pkwk_dtd === PKWK_DTD_HTML_5) ? '<nav class="topicpath">'.$ret.'</nav>'."\n" : '<div class="topicpath">'.$ret.'</div>')."\n";
	}
}

function plugin_topicpath_inline()
{
	global $vars, $defaultpage, $topicpath;

	if (isset($topicpath) && $topicpath == false) return '';

	$page = isset($vars['page']) ? $vars['page'] : '';
	if ($page == '' || $page == $defaultpage) return '';

	$parts = explode('/', $page);

	$b_link = TRUE;
	if (PLUGIN_TOPICPATH_THIS_PAGE_DISPLAY) {
		$b_link = PLUGIN_TOPICPATH_THIS_PAGE_LINK;
	} else {
		array_pop($parts); // Remove the page itself
	}

	$topic_path = array();
	while (! empty($parts)) {
		$_landing = join('/', $parts);
		$element = htmlsc(array_pop($parts));
		if (! $b_link)  {
			// This page ($_landing == $page)
			$b_link = TRUE;
			$topic_path[] = $element;
		// } else if (PKWK_READONLY && ! is_page($_landing)) {
		} else if (Auth::check_role('readonly') && ! is_page($_landing)) {
			// Page not exists
			$topic_path[] = $element;
		} else {
			// Page exists or not exists
			$topic_path[] = '<a href="' . get_page_uri($_landing) . '">' .
				$element . '</a>';
		}
	}

	if (PLUGIN_TOPICPATH_TOP_DISPLAY)
		$topic_path[] = make_pagelink($defaultpage, PLUGIN_TOPICPATH_TOP_LABEL);

	return '<ul><li>'.join('</li><li>', array_reverse($topic_path)).'</li></ul>';
}
/* End of file topicpath.inc.php */
/* Location: ./wiki-common/plugin/topicpath.inc.php */
