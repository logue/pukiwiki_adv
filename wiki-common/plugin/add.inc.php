<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: add.inc.php,v 1.7.4 2011/03/27 22:45:00 Logue Exp $
//
// Add plugin - Append new text below/above existing page
// Usage: cmd=add&page=pagename

use PukiWiki\Lib\Auth\Auth;
use PukiWiki\Lib\Utility;
use PukiWiki\Lib\Factory;

function plugin_add_action()
{
	global $get, $post, $vars, $_string;

	// if (PKWK_READONLY) die_message('PKWK_READONLY prohibits editing');
	if (Auth::check_role('readonly')) Utility::dieMessage($_string['prohibit']);

	$page = isset($vars['page']) ? $vars['page'] : '';
	$wiki = Factory::Wiki($page);
	$wiki->checkEditable();

	$get['add'] = $post['add'] = $vars['add'] = TRUE;
	return array(
		'msg'  => _("Add to $1"),
		'body' => 
			'<ul>' . "\n" .
			'	<li>' . T_('Two and the contents of an input are added for a new-line to the contents of a page of present addition.') . '</li>' . "\n" .
			'</ul>' . "\n" .
			edit_form($page, '')
	);
}
/* End of file add.inc.php */
/* Location: ./wiki-common/plugin/add.inc.php */
