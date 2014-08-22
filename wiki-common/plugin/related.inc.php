<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: related.inc.php,v 1.12.2 2011/02/05 12:21:00 Logue  Exp $
// Copyright (C)
//   2011      PukiWiki Advance Developers Team
//   2007-2008 PukiWiki Plus! Developers Team
//   2005,2007,2011 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Related plugin: Show Backlinks for the page

use PukiWiki\Factory;
use PukiWiki\Relational;
use PukiWiki\Utility;

function plugin_related_init(){
	$messages['_related_messages'] = array(
		'related' => T_('Related pages: '),
		'msg' => T_('Backlinks for: %s'),
		'msg_return' => T_('Return to %s'),
		'msg_nomatch' => T_('No related pages found.')
	);
	set_plugin_messages($messages);
}

function plugin_related_convert()
{
	global $vars;

	return make_related($vars['page'], 'dl');
}

// Related pages
function make_related($_page, $tag = '')
{
	global $vars, $_related_messages;

	$links = Factory::Wiki($_page)->related();

	if ($tag) {
		ksort($links, SORT_STRING);	// Page name, alphabetical order
	} else {
		arsort($links, SORT_NUMERIC);	// Last modified date, newer
	}

	$_links = array();
	foreach ($links as $page=>$lastmod) {
		$wiki = Factory::Wiki($page);
		if ($wiki->isHidden()) continue;

		$_links[] = '<a href="' . $wiki->uri() . '">' . Utility::htmlsc($page) . '</a>' . $wiki->passage(true,true);
	}
	if (empty($_links)) return ''; // Nothing

	if ($tag == 'p') { // From the line-head
		$retval =  "\n" .
			'<ul>' . "\n" .
			'<li>' . join("</li>\n<li>", $_links) . '</li>' . "\n" .
			'</ul>' . "\n";
	}else if ($tag == 'dl') {
		$retval =  "\n" .
			'<dl>'."\n".
			'<dt>'.$_related_messages['related'].'</dt>' . "\n" .
			'<dd>' . join("</dd>\n<dd>", $_links) . '</dd>' . "\n" .
			'</dl>' . "\n";
	} else if ($tag) {
		$retval = join("</li>\n<li>", $_links);
	} else {
		$retval = join("\n ", $_links);
	}

	return $retval;
}

// Show Backlinks: via related caches for the page
function plugin_related_action()
{
	global $vars, $defaultpage, $_related_messages;

	$_page = isset($vars['page']) ? $vars['page'] : null;
	if ( empty($_page) ) $_page = $defaultpage;

	if (!IS_AJAX) {
		// Result
		$retval[]  = '<a href="' . get_page_uri($_page) . '">' .
			sprintf($_related_messages['msg_return'],Utility::htmlsc($_page)) .'</a><br />'. "\n";
	}

	// Get related from cache
	$links = new Relational($_page);
	$data = $links->getRelated();
	if (empty($data)) {
		$retval[] = '<ul><li>' . $_related_messages['msg_nomatch'] . '</li></ul>' . "\n";
	}else{
		// Hide by array keys (not values)
		foreach(array_keys($data) as $page) {
			$wiki = Factory::Wiki($page);
			if (! $wiki->isEditable() || $wiki->isHidden()) {
				unset($data[$page]);
			}
		}
		unset($wiki);
		// Show count($data)?
		ksort($data, SORT_STRING);

		$retval[] = '<ul class="list_pages">' . "\n";
		foreach ($data as $page=>$time) {
			$wiki = Factory::Wiki($page);
			$retval[] = ' <li><a href="' . $wiki->uri() . '">' . Utility::htmlsc($page) .
				'</a> ' . $wiki->passage(true,true) . '</li>';
		}
		$retval[] .= '</ul>' . "\n";
	}
	return array('msg'=>sprintf($_related_messages['msg'], Utility::htmlsc($_page)), 'body'=>join("\n",$retval));
}
/* End of file related.inc.php */
/* Location: ./wiki-common/plugin/related.inc.php */