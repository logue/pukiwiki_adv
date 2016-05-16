<?php
// PukiWiki Advance.
// $Id: main.php,v 1.23.30 2012/12/05 23:15:00 Logue Exp $
//
// PukiWiki Advance
//  Copyright (C) 2010-2016 by PukiWiki Advance Team
//  http://pukiwiki.logue.be/
//
// PukiWiki Plus! 1.4.*
//  Copyright (C) 2002-2009 by PukiWiki Plus! Team
//  http://pukiwiki.cafelounge.net/plus/
//
// PukiWiki 1.4.*
//  Copyright (C) 2002-2011 by PukiWiki Developers Team
//  http://pukiwiki.sourceforge.jp/
//
// PukiWiki 1.3.*
//  Copyright (C) 2002-2004 by PukiWiki Developers Team
//  http://pukiwiki.sourceforge.jp/
//
// PukiWiki 1.3 (Base)
//  Copyright (C) 2001-2002 by yu-ji <sng@factage.com>
//  http://pukiwiki.sourceforge.jp/
//
// Special thanks
//  YukiWiki by Hiroshi Yuki <hyuki@hyuki.com>
//  http://www.hyuki.com/yukiwiki/
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.

if (version_compare(phpversion(), '5.4.0', '<')) {
	throw new RuntimeException('PukiWiki Advance requires PHP version 5.4.0 or newer.');
}
if (!class_exists('PDO')){
    throw new RuntimeException('PukiWiki Advance requires PDO class.');
}
ini_set('memory_limit', '128M');
ini_set('real_path_cache_size','64k');
ini_set('realpath_cache','120');
ini_set('zlib.output_compression', 'Off');
ini_set('zlib.output_handler','mb_output_handler');

/////////////////////////////////////////////////
// Init PukiWiki Advance Enviroment variables

defined('DEBUG')        or define('DEBUG', false);
defined('PKWK_WARNING') or define('PKWK_WARNING', false);
defined('ROOT_URI')     or define('ROOT_URI', dirname($_SERVER['PHP_SELF']).'/');
defined('WWW_HOME')     or define('WWW_HOME', './');
defined('COMMON_URI')   or define('COMMON_URI', ROOT_URI);

if (DEBUG) {
	error_reporting(E_ALL); // Show all errors
	ini_set('display_errors', 'On');
}
defined('LIB_DIR') or define('LIB_DIR', realpath('./').'/');
defined('SITE_HOME') or define('SITE_HOME', realpath('./').'/');
define('VENDOR_DIR', realpath(SITE_HOME . '..'. DIRECTORY_SEPARATOR . 'vendor') . DIRECTORY_SEPARATOR);

// Composer autoloading
if (file_exists(VENDOR_DIR . 'autoload.php')) {
	$loader = include VENDOR_DIR . 'autoload.php';
	$loader->add('Zend', 'vendor/zendframework/zendframework/library');
}

if (!class_exists('Zend\Loader\AutoloaderFactory')) {
    throw new RuntimeException('Unable to load ZF2. Run `php composer.phar install`.');
}

/////////////////////////////////////////////////
// Initilalize PukiWiki
//
Zend\Loader\AutoloaderFactory::factory(array(
    'Zend\Loader\StandardAutoloader' => array(
        'namespaces' => array(
            'PukiWiki' => LIB_DIR.'PukiWiki',
        ),
    ),
));

/////////////////////////////////////////////////

// Load *.ini.php files and init PukiWiki
require(LIB_DIR . 'legacy.php');

// Defaults
require(LIB_DIR . 'init.php');

/* End of file main.php */
/* Location: ./wiki-common/lib/main.php */