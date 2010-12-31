<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: color.inc.php,v 1.24.3 2010/12/26 16:38:08 Logue Exp $
// Copyright (C)
//   2010      PukiWiki Advance Developers Team
//   2005      PukiWiki Plus! Team
//   2003-2007 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Text color plugin
//
// See Also:
// CCS 2.1 Specification: 4.3.6 Colors
// http://www.w3.org/TR/CSS21/syndata.html#value-def-color

// ----
define('PLUGIN_COLOR_USAGE', '&amp;color(foreground[,background]){text};');
define('PLUGIN_COLOR_REGEX', '/^(?:#[0-9a-f]{3}|#[0-9a-f]{6}|[a-z-]+)$/i');

function plugin_color_inline()
{
	global $pkwk_dtd;

	$args    = func_get_args();
	$text    = strip_autolink(array_pop($args)); // htmlspecialchars(text) already
	$color   = isset($args[0]) ? trim($args[0]) : '';
	$bgcolor = isset($args[1]) ? trim($args[1]) : '';

	if (($color == '' && $bgcolor == '') || func_num_args() > 3) {
		return PLUGIN_COLOR_USAGE;
	}
	if ($text == '' ) {
		if ($color != '' && $bgcolor != '') {
			$text    = htmlspecialchars($bgcolor);
			$bgcolor = '';
		}else{
			return PLUGIN_COLOR_USAGE;
		}
	}
	foreach(array($color, $bgcolor) as $_color){
		if ($_color != '' && ! preg_match(PLUGIN_COLOR_REGEX, $_color)) {
			return '&amp;color():Invalid color: ' . htmlspecialchars($_color) . ';';
		}
	}

	if ($color   != '') $color   = 'color:' . $color;
	if ($bgcolor != '') $bgcolor = 'background-color:' . $bgcolor;
	$delimiter = ($color != '' && $bgcolor != '') ? ';' : '';
	return '<span class="wikicolor" style="' . $color . $delimiter . $bgcolor . '">' .
		$text . '</span>';
}
?>
