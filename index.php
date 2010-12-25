<?php
// PukiPlus
// $Id: index.php,v 1.9.6 2010/11/14 22:29:00 Logue Exp $
// Copyright (C)
//   2010      PukiWiki Advance Developers Team
//   2005-2007,2009 PukiWiki Plus! Team
//   2001-2006 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version

// Error reporting
//error_reporting(0); // Nothing
//error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
//error_reporting(E_COMPILE_ERROR|E_RECOVERABLE_ERROR|E_ERROR|E_CORE_ERROR);	// Show only errors 
error_reporting(E_ALL); // Show all errors

// Debug mode.
define('DEBUG', true);
// Show infomation message.
define('PKWK_WARNING', true);

// Special
//define('PKWK_READONLY',  1); // 0,1,2,3,4
//define('PKWK_SAFE_MODE', 1); // 0,1,2,3,4
//define('PKWK_OPTIMISE',  1); 

// THEME
// tDiary THEME
//define('TDIARY_THEME', 'digital_gadgets');
// PukiWiki Adv. THEME (NOT compatible as Original and Plus! skin)
// ex. bluebox, cloudwalk, classic, iridwire, iridorange, orangebox, pukiwiki, xxxlogue
define('PLUS_THEME',	'default');

// Directory definition
// (Ended with a slash like '../path/to/pkwk/', or '')
// define('SITE_HOME',	'../wiki-common/');
define('SITE_HOME',		'./');
// define('DATA_HOME',	'../../wiki-data/contents/');
define('DATA_HOME',		'./');

define('ROOT_URI', dirname($_SERVER['PHP_SELF']).'/');	// when skin directory and index.php is same directory.
// define('ROOT_URI', '');
define('WWW_HOME', '');

// to absolute path
// Do not change following lines
define('LIB_DIR',	realpath(SITE_HOME) . '/lib/');
require(LIB_DIR .	'main.php');
?>
