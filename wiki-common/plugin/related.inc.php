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

use PukiWiki\Lib\File\FileFactory;
use PukiWiki\Lib\Relational;

function plugin_related_init(){
	$messages['_related_messages'] = array(
		'msg' => T_('Backlinks for: %s'),
		'msg_nomatch' => T_('No related pages found.')
	);
	set_plugin_messages($messages);
}

function plugin_related_convert()
{
	global $vars;

	$args = func_get_args();
	if ($args[0] == 'dl'){
		return make_related($vars['page'], 'dl');
	}else{
		return make_related($vars['page'], 'p');
	}
}

// Related pages
function make_related($_page, $tag = '')
{
	global $vars;

	$links = FileFactory::Wiki($_page)->getRelated();

	if ($tag) {
		ksort($links, SORT_STRING);	// Page name, alphabetical order
	} else {
		arsort($links, SORT_NUMERIC);	// Last modified date, newer
	}

	$_links = array();
	foreach ($links as $page=>$lastmod) {
		$wiki = FileFactory::Wiki($page);
		if ($wiki->isHidden()) continue;

		$_links[] = '<a href="' . get_page_uri($page) . '">' . htmlsc($page) . '</a>' . $wiki->passage(true,true);
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
			'<dt>'.T_('Related pages: ').'</dt>' . "\n" .
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
	global $vars, $defaultpage;

	$_page = isset($vars['page']) ? $vars['page'] : '';
	if ($_page == '') $_page = $defaultpage;
	// Result
	$msg = 'Backlinks for: ' . htmlsc($_page);
	$retval[]  = '<a href="' . get_page_uri($_page) . '">' .
		'Return to ' . htmlsc($_page) .'</a><br />'. "\n";

	// Get related from cache
	$links = new Relational($_page);
	$data = $links->getRelated();
	if (empty($data)) {
		$retval[] = '<ul><li>No related pages found.</li></ul>' . "\n";
	}else{
		// Hide by array keys (not values)
		foreach(array_keys($data) as $page) {
			$wiki = FileFactory::Wiki($page);
			if (! $wiki->isEditable() || $wiki->isHidden()) {
				unset($data[$page]);
			}
		}
		// Show count($data)?
		ksort($data, SORT_STRING);

		$retval[] = '<ul>' . "\n";
		foreach ($data as $page=>$time) {
			$retval[] = ' <li><a href="' . get_page_uri($page) . '">' . htmlsc($page) .
				'</a> ' . FileFactory::Wiki($page)->passage(true,true) . '</li>';
		}
		$retval[] .= '</ul>' . "\n";
	}
	return array('msg'=>$msg, 'body'=>join("\n",$retval));
}
/* End of file related.inc.php */
/* Location: ./wiki-common/plugin/related.inc.php */