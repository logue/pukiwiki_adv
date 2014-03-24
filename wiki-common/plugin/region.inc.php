<?php
/**
 * PukiWiki Advance - Yet another WikiWikiWeb clone.
 * $Id: region.inc.php,v 1.0.0 2014/03/24 11:39:00 Logue Exp $
 * Copyright (C)
 *   2014 PukiWiki Advance Developers Team
 * License: GPL v2 or (at your option) any later version
 *
 * Inspired from
 *   uupaa's flod.inc.php and ‚Ü‚Ì‚½‚ë‚¤'s region.inc.php
 *
 * region plugin (cmd=edit)
 */

use PukiWiki\Renderer\RendererFactory;

function plugin_region_convert() {
	static $first = 0; // at first call

	$title = $body = '';
	$args = func_get_args();

	if (func_num_args() > 1) {
		$title = array_shift($args);
		$body = join(',', $args);
	} else {
		$body = str_replace(array(chr(0x0d) . chr(0x0a), chr(0x0d), chr(0x0a)), "\n", $args[0]);
		list($title, $body) = explode("\n", $body, 2);
	}
	$ret[] = '<div class="plugin-region" id="plugin-region-anchor' . $first . '">';
	$ret[] = '<div class="plugin-region-title">' . RendererFactory::factory($title) . '</div>';
	$ret[] = '<div class="plugin-region-body">' . RendererFactory::factory($body) . '</div>';
	$ret[] = '</div>';

	return join("\n",$ret);
}

