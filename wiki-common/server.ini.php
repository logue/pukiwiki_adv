<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone
// $Id: server.ini.php,v 0.0.2 2012/10/15 20:51:00 Logue Exp $
// Copyright (C)
//   2012 PukiWiki Advance Developers Team
// License: GPL v2 or (at your option) any later version
//
/////////////////////////////////////////////////
// Memcache configure
// Memcache Host. When socket connection unix://var/run/memcache.socket
define('MEMCACHE_HOST', '127.0.0.1');
// Memcache Port, When socket connection set 0.
define('MEMCACHE_PORT', 11211);

/////////////////////////////////////////////////
// Page-reading feature settings
// (Automatically creating pronounce datas, for Kanji-included page names,
//  to show sorted page-list correctly)

// Enable page-reading feature.
// (1:Enable, 0:Disable)
$pagereading_enable = 1;

// Set Mecab path
$mecab_path = '/usr/bin/mecab';
// $mecab_path = '/usr/local/bin/mecab';	// for Xrea, Coreserver, Xbeat
// $mecab_path = 'C:\Program Files (x86)\MeCab\bin\mecab.exe';	// for Windows

// Page name contains pronounce data (written by the converter)
$pagereading_config_page = ':config/PageReading';

// Page name of default pronouncing dictionary, used when converter = 'none'
// Japanese Only!
$pagereading_config_dict = ':config/PageReading/dict';

/////////////////////////////////////////////////
// HTTP proxy setting (for TrackBack etc)

// Use HTTP proxy server to get remote data
$use_proxy = 0;

$proxy_host = 'proxy.example.com';
$proxy_port = 8080;

// Do Basic authentication
$need_proxy_auth = 0;
$proxy_auth_user = 'username';
$proxy_auth_pass = 'password';

// Hosts that proxy server will not be needed
$no_proxy = array(
	'localhost',	// localhost
	'127.0.0.0/8',	// loopback
//	'10.0.0.0/8'	// private class A
//	'172.16.0.0/12'	// private class B
//	'192.168.0.0/16'	// private class C
//	'no-proxy.com',
);

////////////////////////////////////////////////
// Mail related settings

// Send mail per update of pages
$notify = 0;

// Send diff only
$notify_diff_only = 1;

// SMTP server (Windows only. Usually specified at php.ini)
$smtp_server = 'localhost';

// Mail recipient (To:) and sender (From:)
$notify_to   = 'to@example.com';	// To:
$notify_from = 'from@example.com';	// From:

// Subject: ($page = Page name wll be replaced)
$notify_subject = '[PukiWiki Adv.] $page';

// Mail header
// NOTE: Multiple items must be divided by "\r\n", not "\n".
$notify_header = '';

// No Mail for Remote Host.
$notify_exclude = array(
//	'192.168.0.',
);

/////////////////////////////////////////////////
// Mail: POP / APOP Before SMTP

// Do POP/APOP authentication before send mail
$smtp_auth = 0;

$pop_server = 'localhost';
$pop_port   = 110;
$pop_userid = '';
$pop_passwd = '';

// Use APOP instead of POP (If server uses)
//   Default = Auto (Use APOP if possible)
//   1       = Always use APOP
//   0       = Always use POP
// $pop_auth_use_apop = 1;

/////////////////////////////////////////////////
// Command execution per update

define('PKWK_UPDATE_EXEC', '');

// Sample: Namazu (Search engine)
// see http://pukiwiki.sourceforge.jp/?PukiWiki%2FNamazu
/*
$mknmz      = '/usr/local/bin/mknmz';			// Namazuへのパス
$index_dir = '/usr/local/var/namazu/index';	// インデックスファイルの保存先
// $index_dir = '/virtual/[ユーザ名]/namazu';	// インデックスファイルの保存先（xrea, coreserverの場合）
define('PKWK_UPDATE_EXEC',
	$mknmz . ' --media-type=text/pukiwiki' .
	' -O ' . $indext_dir . ' -L ja -c -K ' . realpath(DATA_DIR) );
*/

/////////////////////////////////////////////////
// Anti-Spam service config

// Akismet
// https://akismet.com/signup/
// $akismet_api_key = '';

/* End of file server.ini.php */
/* Location: ./wiki-common/server.ini.php */