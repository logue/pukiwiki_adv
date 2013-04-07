<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: js.php,v 1.0.0 2012/03/11 22:13:00 Logue Exp $
// Copyright (C)
//   2012 PukiWiki Advance Developers Team
//
// JavaScript Compress & Cache script.

error_reporting(0); // Nothing
ini_set('zlib.output_compression', 'Off');
ini_set('zlib.output_handler','mb_output_handler');
ini_set('output_buffering','off');
$file   = new SplFileObject( (isset($_GET['file']) ? $_GET['file'] : 'skin') . '.js', 'rb' );
$expire = isset($_GET['expire']) ? (int)$_GET['expire'] * 86400	: '604800';	// Default is 7 days.

// GZ圧縮してバッファに保存
ob_start('ob_gzhandler');
$file->flock(LOCK_SH);
while (!$file->eof()) {
	echo $file->fgets();
}
$file->flock(LOCK_SH);
$buffer = ob_get_clean();
flush();

//ヘッダー出力
header('Content-type: text/javascript; charset: UTF-8');
header('Cache-Control: private');
header('Expires: ' .gmdate('D, d M Y H:i:s',time() + $expire) . ' GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s', $file->getMTime()).' GMT');
header('Content-Length: ' .strlen($buffer));
// 内容を出力
echo $buffer;
exit();

/* End of file scripts.css.php */
/* Location: ./webroot/js/js.php */