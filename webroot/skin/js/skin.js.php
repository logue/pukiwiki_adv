<?php 
header("Content-type: text/javascript; charset: UTF-8");
header("Content-Encoding: deflate");
header("Content-Length: " . filesize("skin.gz"));
header("Cache-Control: must-revalidate");
header("Expires: " .gmdate("D, d M Y H:i:s",time() + (60 * 60)) . " GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s", filemtime("skin.gz"))." GMT");
readfile("skin.gz");