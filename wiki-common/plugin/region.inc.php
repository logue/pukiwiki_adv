<?php
/**
 * PukiWiki Advance - Yet another WikiWikiWeb clone.
 * $Id: region.inc.php,v 1.0.0 2014/03/24 11:39:00 Logue Exp $
 * Copyright (C)
 *   2014 PukiWiki Advance Developers Team
 * License: GPL v2 or (at your option) any later version
 *
 * Inspired from
 *   uupaa's flod.inc.php and まのたろう's region.inc.php
 *
 * region plugin
 */

use PukiWiki\Renderer\RendererFactory;
use PukiWiki\Utility;

function plugin_region_convert() {
	static $first = 0; // at first call
	$usage = '<p class="alert alert-warning">#region usage: <code>#region(title){{ content }}</code> or <code>#region([[Pagename#hash]])</code></p>';

	$title = $body = '';
	$args = func_get_args();
	$num = func_num_args();
	
	$title = array_shift($args);
	if ($num === 1){
		// BlacketNameだった場合、そのページをajaxで取得
		if (preg_match('/^\[\[(.*)\]\]$/', $title, $match)){
			$ret[] = '<div class="plugin-region clearfix">';
			$ret[] = '<div class="plugin-region-title">' . RendererFactory::factory($title) . '</div>';
			$ret[] = '<div class="plugin-region-body" data-page="'.$match[1] . '"></div>';
			$ret[] = '</div>';
			return join("\n",$ret);
		}
	}else if ($num === 2){
		$ret[] = '<div class="plugin-region clearfix">';
		$ret[] = '<div class="plugin-region-title">' . Utility::htmlsc($title) . '</div>';
		$ret[] = '<div class="plugin-region-body">' . RendererFactory::factory(array_pop($args)) . '</div>';
		$ret[] = '</div>';

		return join("\n",$ret);
	}
	return $usage;
}

