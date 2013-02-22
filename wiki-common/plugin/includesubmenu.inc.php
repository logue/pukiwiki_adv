<?php
// $Id: includesubmenu.inc.php,v 1.7.1 2011/02/05 10:56:00 Logue Exp $
// Copyright (C)
//     2011      PukiWiki Advance Developers Team
//     2002-2004, 2007,2011 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Including submenu 

use PukiWiki\Lib\Factory;

function plugin_includesubmenu_convert()
{
	global $vars;

	$ShowPageName = FALSE;

	if (func_num_args()) {
		$aryargs = func_get_args();
		if ($aryargs[0] == 'showpagename') {
			$ShowPageName = TRUE;
		}
	}

	$SubMenuPageName = '';

	$tmppage = strip_bracket($vars['page']);
	//下階層のSubMenuページ名
	$SubMenuPageName1 = $tmppage . '/SubMenu';

	//同階層のSubMenuページ名
	$LastSlash= strrpos($tmppage,'/');
	if ($LastSlash === FALSE) {
		$SubMenuPageName2 = 'SubMenu';
	} else {
		$SubMenuPageName2 = substr($tmppage,0,$LastSlash) . '/SubMenu';
	}
	//echo "$SubMenuPageName1 <br />";
	//echo "$SubMenuPageName2 <br />";
	//下階層にSubMenuがあるかチェック
	//あれば、それを使用
	if (is_page($SubMenuPageName1)) {
		//下階層にSubMenu有り
		$SubMenuPageName = $SubMenuPageName1;
	}
	else if (is_page($SubMenuPageName2)) {
		//同階層にSubMenu有り
		$SubMenuPageName = $SubMenuPageName2;
	}
	else {
		//SubMenu無し
		return "";
	}
	$wiki = WikiFactory::Wiki($SubMenuPageName);

	$body = $wiki->render();

	if ($ShowPageName) {
		$s_page = htmlsc($SubMenuPageName);
		$link = '<a href="' . $wiki->get_uri('edit') . '">$s_page</a>';
		$body = "<h1>$link</h1>\n$body";
	}
	return $body;
}
/* End of file includesubmenu.inc.php */
/* Location: ./wiki-common/plugin/includesubmenu.inc.php */