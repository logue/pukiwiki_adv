<?php
//error_reporting(0); // Nothing
error_reporting(E_ERROR | E_PARSE); // Avoid E_WARNING, E_NOTICE, etc
ini_set('zlib.output_compression', 'Off');
$expire = isset($_GET['expire']) ? (int)$_GET['expire'] * 86400	: '604800';	// Default is 7 days.
// Send header
header('Content-Type: text/css; charset: UTF-8');
header('Cache-Control: private');
header('Expires: ' .gmdate('D, d M Y H:i:s',time() + $expire) . ' GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($color)) . ' GMT');
ob_start('ob_gzhandler');
readfile('180wiki.css');
ob_end_flush();

/* End of file 180wiki.css.php */
/* Location: ./webroot/skin/theme/180wiki/180wiki.css.php */