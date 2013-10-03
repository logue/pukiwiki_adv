<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: help.inc.php,v 0.4 2008/01/05 18:17:00 upk Exp $
//
use PukiWiki\Utility;
use PukiWiki\Factory;
function plugin_help_action()
{
	global $help_page;
	Utility::redirect(Factory::Wiki('Help')->uri());
}
/* End of file help.inc.php */
/* Location: ./wiki-common/plugin/help.inc.php */