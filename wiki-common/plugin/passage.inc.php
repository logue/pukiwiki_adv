<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: passage.inc.php,v 0.2 2011/02/05 12:02:00 Logue Exp $

function plugin_passage_inline()
{
	$argv = func_get_args();
	$argc = func_num_args();

	$field = array('time','paren');
	for($i=0; $i<$argc; $i++) {
		$$field[$i] = htmlsc($argv[$i], ENT_QUOTES);
	}

	if (empty($time)) return '';
	$paren = (empty($paren)) ? FALSE : TRUE;
	return get_passage($time, $paren);
}

/* End of file passage.inc.php */
/* Location: ./wiki-common/plugin/passage.inc.php */
