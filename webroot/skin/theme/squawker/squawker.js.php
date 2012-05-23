<?php
//error_reporting(0); // Nothing
error_reporting(E_ERROR | E_PARSE); // Avoid E_WARNING, E_NOTICE, etc

$expire = isset($_GET['expire']) ? (int)$_GET['expire'] * 86400	: '604800';	// Default is 7 days.
// Send header
header('Content-Type: text/javascript; charset: UTF-8');
header('Cache-Control: private');
header('Expires: ' .gmdate('D, d M Y H:i:s',time() + $expire) . ' GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($color)) . ' GMT');
ob_start('ob_gzhandler');
readfile('js/bootstrap.min.js');
?>

$('.contents').scrollspy();
$('*[aria-describedby="tooltip"]').tooltip();
$('*[title]').tooltip();


$('.style_table').addClass('table table-bordered');
<?php
ob_end_flush();