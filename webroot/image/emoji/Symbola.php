<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// WebFont Proxy script. ( Add to Access-Control-Allow-Origin header)
// Copyright (c)2011 PukiWiki Advance Developer Team

// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 2 of the License, or
// (at your option) any later version.

// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.

// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.

$fontname = 'Symbola602';
$type = (isset($_GET['type'])) ? $_GET['type'] : '.woff';
$agent = $_SERVER["HTTP_USER_AGENT"];

// for Older IE
if(preg_match('/MSIE ([0-8\.]+)?/i', $agent)){
	$type = '.eot';
// for Safari
}elseif(preg_match('/Safari/i', $agent)){
	$type = '.otf';
}
// other case uses WOFF font.


switch($type){
	case '.eot':
		$mime = 'application/vnd.ms-fontobject';
	break;
/*
	case '.ttf':
		$mime = 'font/ttf';
	break;
*/
	case '.woff':
		$mime = 'font/x-woff';
	break;
	case '.otf':
	default:
		$mime = 'font/otf';
	break;
}

$filename = $fontname.$type;
$modified = filemtime($filename);

header('Access-Control-Allow-Origin: *');
header('Content-Type: '.$mime);
/*
// for reduce server load
if (function_exists('apache_get_modules') && in_array( 'mod_xsendfile', apache_get_modules()) ){
	// for Apache mod_xsendfile
	header('X-Sendfile: '.$filename);
}else if (stristr(getenv('SERVER_SOFTWARE'), 'lighttpd') ){
	// for lighttpd
	header('X-Lighttpd-Sendfile: '.$filename);
}else if(stristr(getenv('SERVER_SOFTWARE'), 'nginx') || stristr(getenv('SERVER_SOFTWARE'), 'cherokee')){
	// nginx
//	header('X-Accel-Redirect: '.$filename);
}
*/

header('Content-Length: ' . filesize($filename));
header('Last-Modified: '.gmdate('D, d M Y H:i:s', $modified).' GMT');
ob_start('ob_gzhandler');
readfile($filename);
ob_end_flush();
?>