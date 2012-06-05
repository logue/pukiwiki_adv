<?php
/////////////////////////////////////////////////
// PukiWiki Plus! - Yet another WikiWikiWeb clone.
//
// $Id: keywords.inc.php,v 0.3 2004/09/30 09:41:57 miko Exp $
//

function plugin_keywords_convert()
{
	global $keywords;

	$num = func_num_args();
	if ($num == 0) { return 'Usage: #keywords(keyword,...)'; }
	$args = func_get_args();
	$contents = array_map('htmlsc',$args);

	$keywords = join(',', $contents);
	return '';
}
/* End of file keywords.inc.php */
/* Location: ./wiki-common/plugin/keywords.inc.php */