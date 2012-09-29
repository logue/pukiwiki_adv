<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: row.inc.php,v 1.0.0 2012/09/29 08:07:00 Logue Exp $
// Copyright (C) 2012 PukiWiki Advance developers team.
// License: GPL v2 or (at your option) any later version
//
// 流体グリッドシステムプラグイン
// Fluid grid system plugin.
// This plugin used in conjunction with span.inc.php.
//
// Example:
// #row{{{{
// #span(6){{ some contents }}
// #span(6){{ some contents }}
// }}}}
//
// see http://twitter.github.com/bootstrap/scaffolding.html#fluidGridSystem

function plugin_row_convert(){
	$argv = func_get_args();
	$argc = func_num_args();
	
	$data = $argv[ --$argc ];
	$fluid = isset($argv[0]) && $argv[0] === true ? 'row-fluid' : 'row';
	return '<div class="'.$fluid.'">'."\n".convert_html(line2array($data))."\n".'</div>'."\n";
}
/* End of file row.inc.php */
/* Location: ./wiki-common/plugin/row.inc.php */