<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: span.inc.php,v 1.0.1 2013/08/24 08:38:00 Logue Exp $
// Copyright (C) 2012-2013 PukiWiki Advance developers team.
// License: GPL v2 or (at your option) any later version
//
// 流体グリッドシステムプラグイン
// Fluid grid system plugin.
// This plugin used in conjunction with row.inc.php.
//
// Example:
// #row{{{{
// #span(6){{ some contents }}
// #span(6){{ some contents }}
// }}}}
//
// see http://twitter.github.com/bootstrap/scaffolding.html#fluidGridSystem

function plugin_span_convert(){
	$argv = func_get_args();
	$argc = func_num_args();
	
	if ($argc < 1) return '<p class="alert alert-warning">#span([1-12])</p>';

	$data = $argv[ --$argc ];
	array_pop($argv);

	$colum = isset($argv[0]) && is_numeric($argv[0]) ? $argv[0] : 12;
	$size = isset($argv[1]) ? Utility::htmlsc($argv[1]) : 'md';

	return '<div class="col-'.$size.'-'.$colum.'">'."\n".convert_html(line2array($data))."\n".'</div>'."\n";
}
/* End of file row.inc.php */
/* Location: ./wiki-common/plugin/row.inc.php */