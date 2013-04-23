<?php
// PukiWiki Advance.
// $Id: main.php,v 1.23.30 2012/12/05 23:15:00 Logue Exp $
//
// PukiWiki Advance
//  Copyright (C) 2010-2012 by PukiWiki Advance Team
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

set_time_limit(0);
ignore_user_abort(true);
ini_set('memory_limit', '128M');
ini_set('real_path_cache_size','64k');
ini_set('realpath_cache','120');
ini_set('zlib.output_compression', 'Off');
ini_set('zlib.output_handler','mb_output_handler');

/*
$info = array();
foreach (array('mbstring','openssl','gd') as $ext){
	if (! extension_loaded($ext)){
		$info[] = 'PukiWiki Adv. needs the <a href="http://www.php.net/manual/book.'.$ext.'.php">'.$ext.' extension</a>.';
	}
}
if (count($info) !== 0){
	throw new Exception(join("<br />\n",$info));
}
*/
/////////////////////////////////////////////////
// Include subroutines

defined('LIB_DIR') or define('LIB_DIR', realpath('./').'/');

/////////////////////////////////////////////////
// Initilalize Zend
//
// Composer autoloading
if (file_exists(SITE_HOME . '../vendor/autoload.php')) {
	$loader = include SITE_HOME . '../vendor/autoload.php';
}
$zf2Path = false;

if (getenv('ZF2_PATH')) {           // Support for ZF2_PATH environment variable or git submodule
    $zf2Path = getenv('ZF2_PATH');
} elseif (get_cfg_var('zf2_path')) { // Support for zf2_path directive value
    $zf2Path = get_cfg_var('zf2_path');
} elseif (is_dir('vendor/ZF2/library')) {
    $zf2Path = 'vendor/zendframework/zendframework/library';
}

if ($zf2Path) {
    if (isset($loader)) {
        $loader->add('Zend', $zf2Path);
    } else {
        include $zf2Path . '/Zend/Loader/AutoloaderFactory.php';
        Zend\Loader\AutoloaderFactory::factory(array(
            'Zend\Loader\StandardAutoloader' => array(
                'autoregister_zf' => true
            )
        ));
    }
}
if (!class_exists('Zend\Loader\AutoloaderFactory')) {
    throw new RuntimeException('Unable to load ZF2. Run `php composer.phar install` or define a ZF2_PATH environment variable.');
}
$info[] = sprintf('Using <a href="http://framework.zend.com/">Zend Framework</a> ver<var>%s</var>.', Zend\Version\Version::VERSION);

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

require('../vendor/bad-behavior/bad-behavior-sqlite.php');

/////////////////////////////////////////////////

// Load *.ini.php files and init PukiWiki

require(LIB_DIR . 'funcplus.php');

require(LIB_DIR . 'config.php');
require(LIB_DIR . 'log.php');
require(LIB_DIR . 'spamplus.php');

require(LIB_DIR . 'legacy.php');

// Defaults
$notify = $trackback = $referer = 0;
require(LIB_DIR . 'init.php');


new PukiWiki\Render($title, $body);
//catbody($title, $page, $body);
exit;

/* End of file main.php */
/* Location: ./wiki-common/lib/main.php */