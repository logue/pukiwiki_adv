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
function make_related($page, $tag = '')
{
	global $vars;

	$links = links_get_related($page);

	if ($tag) {
		ksort($links, SORT_STRING);	// Page name, alphabetical order
	} else {
		arsort($links, SORT_NUMERIC);	// Last modified date, newer
	}

	$_links = array();
	foreach ($links as $page=>$lastmod) {
		if (check_non_list($page)) continue;

		$s_page   = htmlsc($page);
		$passage  = get_passage($lastmod);
		$_links[] = 
			'<a href="' . get_page_uri($page) . '">' .
			$s_page . '</a>' . $passage;
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
			'<dt>'._('Related pages: ').'</dt>' . "\n" .
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

	// Get related from cache
	$data = links_get_related_db($_page);
	if (! empty($data)) {
		// Hide by array keys (not values)
		foreach(array_keys($data) as $page) {
			if (is_cantedit($page) || check_non_list($page)) {
				unset($data[$page]);
			}
		}
	}

	// Result
	$s_word = htmlsc($_page);
	$msg = 'Backlinks for: ' . $s_word;
	$retval  = '<a href="' . get_page_uri($_page) . '">' .
		'Return to ' . $s_word .'</a><br />'. "\n";

	if (empty($data)) {
		$retval .= '<ul><li>No related pages found.</li></ul>' . "\n";	
	} else {
		// Show count($data)?
		ksort($data, SORT_STRING);
		$retval .= '<ul>' . "\n";
		foreach ($data as $page=>$time) {
			$s_page  = htmlsc($page);
			$passage = get_passage($time);
			$retval .= ' <li><a href="' . get_page_uri($page) . '">' . $s_page .
				'</a> ' . $passage . '</li>' . "\n";
		}
		$retval .= '</ul>' . "\n";
	}
	return array('msg'=>$msg, 'body'=>$retval);
}
?>
