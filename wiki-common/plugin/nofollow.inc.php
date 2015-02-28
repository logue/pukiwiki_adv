<?php
// $Id: nofollow.inc.php,v 1.2.1 2015/02/26 21:27:36 Logue Exp $
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

	$page = isset($vars['page']) ? $vars['page'] : null;
	if (empty($page)) {
		// ページ名が無い
		return '<p class="alert alert-warning">#nofollow: Page name is missing.</p>';
	}
	if(!Factory::Wiki($page)->isFreezed()){
		// フリーズされてない
		return '<p class="alert alert-warning">#nofollow: Page not freezed.</p>';
	}
	$nofollow = 1;
	
}
/* End of file nofollow.inc.php */
/* Location: ./wiki-common/plugin/nofollow.inc.php */