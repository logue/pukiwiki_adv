<?php
/**
 * PukiWiki Advance - Yet another WikiWikiWeb clone.
 * $Id: well.inc.php,v 1.0.0 2014/04/24 23:26:00 Logue Exp $
 * Copyright (C)
 *   2014 PukiWiki Advance Developers Team
 * License: GPL v2 or (at your option) any later version
 *
 * Well plugin
 */

use PukiWiki\Renderer\RendererFactory;

function plugin_well_convert() {
	$title = $body = '';
	$type = '';

	$num_of_arg = func_num_args();
	$args = func_get_args();

	switch ($num_of_arg){
		default:
			return '<p class="alert alert-warning">#well(type){{body}}</p>';
			break;
		case 1:
			$body = $args[0];
			break;
	}

	$ret[] = '<div class="well">';
	$body = str_replace(array(chr(0x0d) . chr(0x0a), chr(0x0d), chr(0x0a)), "\n", $body);
	$ret[] = RendererFactory::factory($body);
	$ret[] = '</div>';

	return join("\n",$ret);
}