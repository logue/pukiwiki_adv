<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: version.inc.php,v 1.9.1 2007/01/21 14:25:25 miko Exp $
// Copyright (C)
//   2005-2007 PukiWiki Plus! Team
//   2002-2003, 2005, 2007 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Show PukiWiki version (maybe a sample code)
use PukiWiki\Auth\Auth;
function plugin_version_value()
{
//	if (PKWK_SAFE_MODE) return '';
	if (Auth::check_role('safemode')) return '';
	return S_VERSION;
}

function plugin_version_convert()
{
	return '<p>' . plugin_version_value() . '</p>';
}

function plugin_version_inline()
{
	return plugin_version_value();
}
/* End of file version.inc.php */
/* Location: ./wiki-common/plugin/version.inc.php */