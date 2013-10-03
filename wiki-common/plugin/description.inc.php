<?php
/////////////////////////////////////////////////
// PukiWiki Plus! - Yet another WikiWikiWeb clone.
//
// $Id: description.inc.php,v 0.3 2010/08/26 22:26:57 Logue Exp $
//

use PukiWiki\Utility;

function plugin_description_convert()
{
	global $description;

	$num = func_num_args();
	if ($num == 0) { return '<p class="alert alert-warning">Usage: #description(description)</p>'; }
	$args = func_get_args();

	$description = Utility::htmlsc($args[0]);
	return '';
}
/* End of file description.inc.php */
/* Location: ./wiki-common/plugin/description.inc.php */
