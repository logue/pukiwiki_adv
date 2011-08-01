<?php
/////////////////////////////////////////////////
// PukiWiki Plus! - Yet another WikiWikiWeb clone.
//
// $Id: description.inc.php,v 0.3 2010/08/26 22:26:57 Logue Exp $
//

function plugin_description_convert()
{
	global $description;

	$num = func_num_args();
	if ($num == 0) { return 'Usage: #description(description)'; }
	$args = func_get_args();

	$description = htmlspecialchars($args[0]);
	return '';
}
?>
