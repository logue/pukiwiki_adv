<?php
// $Id: nofollow.inc.php,v 1.2 2007/07/29 05:22:36 henoheno Exp $
// Copyright (C) 2005, 2007 PukiWiki Developers Team
// License: The same as PukiWiki
//
// NoFollow plugin
// (See BugTrack2/72)

// Output contents with "nofollow,noindex" option

use PukiWiki\Factory;

function plugin_nofollow_convert()
{
	global $vars, $nofollow;

	$page = isset($vars['page']) ? $vars['page'] : '';
	if (empty($page)) {
		return '<p class="alert alert-warning">#nofollow: Page name is missing.</p>';
	}else if(Factory::Wiki($page)->isFreezed()){
		$nofollow = 1;
		return '<p class="alert alert-warning">#nofollow: Page not freezed.</p>';
	}
}
/* End of file nofollow.inc.php */
/* Location: ./wiki-common/plugin/nofollow.inc.php */