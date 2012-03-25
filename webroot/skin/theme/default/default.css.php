<?php
$color = (isset($_GET['color']) ? $_GET['color']	: 'blue').'.css';
$expire = isset($_GET['expire']) ? (int)$_GET['expire'] * 86400	: '604800';	// Default is 7 days.
// Send header
header('Content-Type: text/css; charset: UTF-8');
header('Cache-Control: private');
header('Expires: ' .gmdate('D, d M Y H:i:s',time() + $expire) . ' GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($color)) . ' GMT');
ob_start('ob_gzhandler');
readfile('default.css');
readfile($color);
ob_end_flush();