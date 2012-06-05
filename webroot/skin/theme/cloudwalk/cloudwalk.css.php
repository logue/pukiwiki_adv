<?php
$expire = isset($_GET['expire']) ? (int)$_GET['expire'] * 86400	: '604800';	// Default is 7 days.
// Send header
header('Content-Type: text/css; charset: UTF-8');
header('Cache-Control: private');
header('Expires: ' .gmdate('D, d M Y H:i:s',time() + $expire) . ' GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime('color_cloudwalk.css')) . ' GMT');
ob_start('ob_gzhandler');
readfile('base_3float.css');
readfile('plugin.css');
readfile('color_cloudwalk.css');
ob_end_flush();

/* End of file cloudwalk.css.php */
/* Location: ./webroot/skin/theme/cloudwalk/cloudwalk.css.php */