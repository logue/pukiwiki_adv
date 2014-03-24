<?php
/**
 * PukiWiki Advance - Yet another WikiWikiWeb clone.
 * $Id: panel.inc.php,v 1.0.0 2014/03/25 08:07:00 Logue Exp $
 * Copyright (C)
 *   2014 PukiWiki Advance Developers Team
 * License: GPL v2 or (at your option) any later version
 *
 * Panel plugin
 */

use PukiWiki\Renderer\RendererFactory;
use PukiWiki\Utility;

function plugin_panel_convert() {
	$title = $body = '';
	$type = '';

	$num_of_arg = func_num_args();
	$args = func_get_args();

	switch ($num_of_arg){
		default:
			return '<p class="alert alert-warning">#panel(title[,type]){{body}}</p>';
			break;
		case 1:
			$body = $args[0];
			break;
		case 2:
			$title = $args[0];
			$body = $args[1];
			break;
		case 3:
			$title = $args[0];
			$type = $args[1];
			$body = $args[2];
			break;
	}
	if (preg_match('/^(primary|info|warning|danger)$/', $type) === 0){
		$type = 'default';
	}

	$ret[] = '<div class="panel panel-'. $type .'">';
	if (!empty($title)) {
		$ret[] = '<div class="panel-heading">' . Utility::htmlsc($title) . '</div>';
	} else {
		$body = str_replace(array(chr(0x0d) . chr(0x0a), chr(0x0d), chr(0x0a)), "\n", $body);
	}
	$ret[] = '<div class="panel-body">' . RendererFactory::factory($body) . '</div>';
	$ret[] = '</div>';

	return join("\n",$ret);
}