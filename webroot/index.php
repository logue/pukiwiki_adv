<?php
// PukiWiki Advance
// $Id: index.php,v 1.9.9 2014/12/17 21:24:00 Logue Exp $
// Copyright (C)
//   2010-2014 PukiWiki Advance Developers Team
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

// PukiWiki Adv. THEME (NOT compatible as Original and Plus! skin)
// ex.  cloudwalk, classic, xxxlogue, whiteflow, gs2, wikiwikiadv, squawker, 180wiki, twilight
define('PLUS_THEME',	'default');

define('ROOT_URI', dirname(__FILE__).DIRECTORY_SEPARATOR);

// Directory definition
// (Ended with a slash like '../path/to/pkwk/', or '')
define('SITE_HOME',	ROOT_URI . '../wiki-common/');

// define('DATA_HOME', ROOT_URI . '../../wiki-data/contents/');
define('DATA_HOME',	ROOT_URI . '../../data/');
// define('DATA_HOME', ROOT_URI . '../wiki-data/');

define('WWW_HOME', '/');
define('COMMON_URI', '/');

// Force protocol to https://
// define('FORCE_SSL', true)

// to absolute path
// Do not change following lines
define('LIB_DIR',	SITE_HOME . 'lib' . DIRECTORY_SEPARATOR);
require(LIB_DIR .	'main.php');

/* End of file index.php */
/* Location: ./webroot/skin/index.php */
