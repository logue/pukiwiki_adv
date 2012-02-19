<?php
// PukiPlus - Yet another WikiWikiWeb clone
// $Id: auth.ini.php,v 0.0.17 2012/01/30 19:35:00 Logue Exp $
// Copyright (C)
//   2010-2012 PukiWiki Advance Developers Team
//   2005-2008 PukiWiki Plus! Team
// License: GPL v2 or (at your option) any later version
//

// Authentication Type
// 1: basic
// 2: digest
$auth_type = 1;

/////////////////////////////////////////////////
// Authentication Parameter REALM
$realm = 'PukiWiki Adv. Auth';

/////////////////////////////////////////////////
// Admin password for this Wikisite

// CHANGE THIS
$adminpass = '{x-php-md5}1a1dc91c907325c69271ddf0c944bc72'; // md5('pass')
//$adminpass = '{CRYPT}$1$AR.Gk94x$uCe8fUUGMfxAPH83psCZG/'; // CRYPT 'pass'
//$adminpass = '{MD5}Gh3JHJBzJcaScd3wyUS8cg==';             // MD5   'pass'
//$adminpass = '{SMD5}o7lTdtHFJDqxFOVX09C8QnlmYmZnd2Qx';    // SMD5  'pass'

/////////////////////////////////////////////////
// User definition
//
// ROLE
//
// Data is managed by the plugin.
define('PKWK_AUTH_FILE', add_homedir('auth_users.ini.php'));
require_once(PKWK_AUTH_FILE);

define('PKWK_AUTH_WKGRP_FILE', add_homedir('auth_wkgrp.ini.php'));
require_once(PKWK_AUTH_WKGRP_FILE);

/////////////////////////////////////////////////
// Auth API
define('PKWK_AUTH_API_FILE', add_homedir('auth_api.ini.php'));
require_once(PKWK_AUTH_API_FILE);

/////////////////////////////////////////////////
// Authentication method

$auth_method_type = 'pagename'; // By Page name
//$auth_method_type = 'contents'; // By Page contents

/////////////////////////////////////////////////
// Read auth (0:Disable, 1:Enable)
$read_auth = 0;

$read_auth_pages = array(
	// Regex                   Username or array('user'=>Username,'group'=>Groupname,'role'=>Role),
	'/:log/'		=> 'hoge',
	'#FooBar#'		=> 'hoge',
	'#(Foo|Bar)#'		=> 'foo,bar,hoge',
);

/////////////////////////////////////////////////
// Edit auth (0:Disable, 1:Enable)
$edit_auth = 1;

$edit_auth_pages = array(
	// Regex                   Username or array('user'=>Username,'group'=>Groupname,'role'=>Role),
	'#(FrontPage|MenuBar|SideBar|Navigation|InterWikiName|Glossary|AutoAliasName|#'		=> 'admin',
	'#FooBar#'			=> 'hoge',
	'#(Foo|Bar)#'		=> 'foo,bar,hoge',
);

/////////////////////////////////////////////////
// Search auth
// 0: Disabled (Search read-prohibited page contents)
// 1: Enabled  (Search only permitted pages for the user)
$search_auth = 0;

/////////////////////////////////////////////////
// Check Role
$check_role = 1;

?>
