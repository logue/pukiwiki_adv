<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: table_pagenate.inc.php,v 1.0.0 2012/10/07 08:33:00 Logue Exp $
// Copyright (C) 2012 PukiWiki Advance developers team.
// License: GPL v2 or (at your option) any later version
//
// テーブルのページングを有効化する
//
// Example:
// #pagenate{{
// |~foo|~bar|h
// |blah|blah|
// |... |... |
// }}
//
// see http://twitter.github.com/bootstrap/scaffolding.html#fluidGridSystem

function plugin_table_pagenate_convert(){
	$argv = func_get_args();
	$argc = func_num_args();
	
	if ($argc < 1) return '<p class="alert alert-warning">#pagenate() Please insert table</p>';

	$data = $argv[ --$argc ];
	array_pop($argv);

	return preg_replace('/data-pagenate="false"/','data-pagenate="true"',convert_html(line2array($data)) );
}
/* End of file row.inc.php */
/* Location: ./wiki-common/plugin/table_pagenate.inc.php */