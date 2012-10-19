<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: cache_ref.inc.php,v 1.48.3.5 2012/10/11 09:06:00 Logue Exp $
//
// copy ref.inc.php

function plugin_cache_ref_action()
{
	global $vars, $use_sendfile_header;

	$usage = 'Usage: cmd=cache_ref&amp;src=filename';

	if (! isset($vars['src']))
		return array('msg'=>'Invalid argument', 'body'=>$usage);

	$filename = $vars['src'] ;

	$ref = CACHE_DIR . $filename;
	if(! file_exists($ref))
		return array('msg'=>'Cache file not found', 'body'=>$usage);

	$got = @getimagesize($ref);
	if (! isset($got[2])) $got[2] = FALSE;
	switch ($got[2]) {
	case 1: $type = 'image/gif' ; break;
	case 2: $type = 'image/jpeg'; break;
	case 3: $type = 'image/png' ; break;
	case 4: $type = 'application/x-shockwave-flash'; break;
	default:
		return array('msg'=>'Seems not an image', 'body'=>$usage);
	}

	// Care for Japanese-character-included file name
	if (LANG == 'ja_JP') {
		switch(UA_NAME . '/' . UA_PROFILE){
		case 'Opera/default':
			// Care for using _auto-encode-detecting_ function
			$filename = mb_convert_encoding($filename, 'UTF-8', 'auto');
			break;
		case 'MSIE/default':
			$filename = mb_convert_encoding($filename, 'SJIS', 'auto');
			break;
		}
	}
	$file = htmlsc($filename);
	$size = filesize($ref);
	$date = filemtime($ref);

	// Output
	ini_set('default_charset', '');
	mb_http_output('pass');
	pkwk_common_headers($date, null, false);
	// for reduce server load
	if ($use_sendfile_header === true){
		// for reduce server load
		header('X-Sendfile: '.realpath($ref));
	}
	header('Content-Disposition: inline; filename="' . $filename . '"');
	header('Content-Length: ' . $size);
	header('Content-Type: '   . $type);

	

	// @readfile($ref);
	plus_readfile($ref);
	pkwk_common_suffixes();
	exit;
}

/* End of file cache_ref.inc.php */
/* Location: ./wiki-common/plugin/cache_ref.inc.php */