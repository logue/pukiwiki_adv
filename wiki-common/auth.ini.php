<?php
// PukiPlus - Yet another WikiWikiWeb clone
// $Id: auth.ini.php,v 0.0.17 2012/01/30 19:35:00 Logue Exp $
// Copyright (C)
//   2010-2012 PukiWiki Advance Developers Team
//   2005-2008 PukiWiki Plus! Team
// License: GPL v2 or (at your option) any later version
//

use PukiWiki\Auth\Auth;
use PukiWiki\Utility;

global $defaultpage, $menubar, $interwiki, $aliaspage, $sidebar, $navigation, $glossarypage, $headarea, $footarea, $whatsnew, $whatsdeleted;

// Authentication Type
// Auth::AUTH_BASIC: basic
// Auth::AUTH_DIGEST: digest
// Auth::AUTH_NTLM : ntlm
$auth_type = Auth::AUTH_BASIC;

/////////////////////////////////////////////////
// Authentication Parameter REALM
$realm = 'PukiWiki Adv. Auth';

/////////////////////////////////////////////////
// Admin name and password for this Wikisite

$adminname = 'admin';

// CHANGE THIS
$adminpass = '{x-php-md5}1a1dc91c907325c69271ddf0c944bc72'; // MD5('pass')

/////////////////////////////////////////////////
// User definition
//
// ROLE
//
// Data is managed by the plugin.
$auth_users = Utility::loadConfig('auth_users.ini.php');
$auth_wkgrp_user = Utility::loadConfig('auth_wkgrp.ini.php');
$auth_api = Utility::loadConfig('auth_api.ini.php');

/////////////////////////////////////////////////
// Authentication method

$auth_method_type = Auth::AUTH_METHOD_PAGENAME; // By Page name
//$auth_method_type = Auth::AUTH_METHOD_CONTENTS; // By Page contents

// Accept specified IP without basic_auth
$read_auth_pages_accept_ip = array(
	'#Private\/#' => '127.0.',
);

/////////////////////////////////////////////////
// Read auth (0:Disable, 1:Enable)
// 他のページから参照する場合や、プラグインで使用するページ（:config）を指定しないでください。
// その場合、参照されたタイミングで閲覧制限がかかります。
$read_auth = 0;

$read_auth_pages = array(
	// Regex           Username or array('user'=>Username,'group'=>Groupname,'role'=>Role),
	'/:log/'		=> $adminname,
	'#FooBar#'		=> 'hoge',
	'#(Foo|Bar)#'	=> 'foo,bar,hoge',
);

/////////////////////////////////////////////////
// Edit auth (0:Disable, 1:Enable)
$edit_auth = 1;

$edit_auth_pages = array(
	// 管理人のみ編集できるページ
	// サイトの根幹に関わるページです。必要に応じてコメントアウトしてください
	'/^(' .
		$defaultpage .			// FrontPage
		'|' . $menubar .		// MenuBar
		'|' . $interwiki .		// InterWikiName
		'|' . $aliaspage .		// AutoAliasName
		'|' . $sidebar .		// SideBar
		'|' . $navigation .		// Navigation
		'|' . $glossarypage .	// Glossary
		'|' . $headarea .		// :Header
		'|' . $footarea .		// :Footer

		'|' . $whatsnew .		// RecentChanges
		'|' . $whatsdeleted .	// RecentDeleted
		'|FormatRule' . 		// FormatRule
	')$/'		=> $adminname,
	// 設定ページ/ヘルプ
	'/^\:config|Help/' => $adminname,

	// Regex Username or array('user'=>Username,'group'=>Groupname,'role'=>Role),
	'/FooBar/'			=> 'hoge',
	'/(Foo|Bar)/'		=> 'foo,bar,hoge',
);

/////////////////////////////////////////////////
// Search auth
// 0: Disabled (Search read-prohibited page contents)
// 1: Enabled  (Search only permitted pages for the user)
$search_auth = 0;

/////////////////////////////////////////////////
// Check Role
$check_role = 1;

/* End of file auth.ini.php */
/* Location: ./wiki-common/auth.ini.php */