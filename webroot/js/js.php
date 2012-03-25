<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: js.php,v 1.0.0 2012/03/11 22:13:00 Logue Exp $
// Copyright (C)
//   2012 PukiWiki Advance Developers Team
//
// JavaScript Compress & Cache script.

$file   = (isset($_GET['file']) ? $_GET['file']	: 'skin') . '.js';
$expire = isset($_GET['expire']) ? (int)$_GET['expire'] * 86400	: '604800';	// Default is 7 days.

header('Content-type: text/javascript; charset: UTF-8');
header('Cache-Control: private');
header('Expires: ' .gmdate('D, d M Y H:i:s',time() + $expire) . ' GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($file)).' GMT');
ob_start('ob_gzhandler');
readfile($file);
ob_end_flush();