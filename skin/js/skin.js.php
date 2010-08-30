<?php 
	header("Content-type: text/javascript; charset: UTF-8");
	header("Content-Encoding: deflate");
	header("Cache-Control: must-revalidate");
	header("Expires: " .gmdate("D, d M Y H:i:s",time() + (60 * 60)) . " GMT");
	readfile("skin.gz");