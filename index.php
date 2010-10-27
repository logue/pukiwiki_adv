<?php
// PukiPlus
// $Id: index.php,v 1.9.5 2010/08/17 21:26:00 Logue Exp $
// Copyright (C)
//   2010      PukiWiki Advance Developers Team
//   2005-2007,2009 PukiWiki Plus! Team
//   2001-2006 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version

// Error reporting
//error_reporting(0); // Nothing
error_reporting(E_ALL & ~E_NOTICE); // Avoid E_WARNING, E_NOTICE, etc
//error_reporting(E_COMPILE_ERROR|E_RECOVERABLE_ERROR|E_ERROR|E_CORE_ERROR);	// Show only errors 
//error_reporting(E_ALL); // Show all errors

// Debug mode.
define('DEBUG', false);
// Show infomation message.
define('PKWK_WARNING', true);

// Special
//define('PKWK_READONLY',  1); // 0,1,2,3,4
//define('PKWK_SAFE_MODE', 1); // 0,1,2,3,4
//define('PKWK_OPTIMISE',  1); 

// THEME
// tDiary THEME
//define('TDIARY_THEME', 'digital_gadgets');
// PukiWiki Adv. THEME
// ex. bluebox, cloudwalk, classic, iridwire, iridorange, orangebox, pukiwiki, xxxlogue
define('PLUS_THEME',   'default');

// Directory definition
// (Ended with a slash like '../path/to/pkwk/', or '')
// define('SITE_HOME',     realpath('../wiki-common/'));
define('SITE_HOME',	'');
// define('DATA_HOME',     realpath('../../wiki-data/contents/'));
define('DATA_HOME',	'');

// define('ROOT_URI', '');
define('ROOT_URI', dirname($_SERVER['PHP_SELF']).'/');	// when skin directory and index.php is same directory.
define('WWW_HOME', '');

define('LIB_DIR',	SITE_HOME . 'lib/');

require(LIB_DIR . 'main.php');
?>
