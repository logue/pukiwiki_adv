<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: cache_ref.inc.php,v 1.48.3.5 2012/10/11 09:06:00 Logue Exp $
//
// copy ref.inc.php

use \SplFileInfo;
use \SplFileObject;
use PukiWiki\Utility;
use PukiWiki\Renderer\Header;
use Zend\Http\Response;


function plugin_cache_ref_action()
{
	global $vars, $use_sendfile_header;

	$usage = 'Usage: cmd=cache_ref&amp;src=filename';

	if (! isset($vars['src']))
		return array('msg'=>'Invalid argument', 'body'=>$usage);

	$filename = $vars['src'] ;

	$ref = CACHE_DIR . $filename;

	$fileinfo = new SplFileInfo($ref);
	
	if(! $fileinfo->isFile() || !$fileinfo->isReadable())
		return array('msg'=>'Cache file not found', 'body'=>$usage);

	try{
		list($width, $height, $_type, $attr) = getimagesize($ref);
		switch ($_type) {
			case 1: $type = 'image/gif' ; break;
			case 2: $type = 'image/jpeg'; break;
			case 3: $type = 'image/png' ; break;
			case 4: $type = 'application/x-shockwave-flash'; break;
			default:
				return array('msg' => 'Seems not an image', 'body' => $usage);
		}
	}catch (Exception $e){
		return array('msg' => 'Seems not an image', 'body' => $usage);
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
	// Output
	ini_set('default_charset', '');
	mb_http_output('pass');
	
	// ヘッダー出力
	$header = Header::getHeaders($type ,$fileinfo->getMTime() );
	$header['Content-Disposition'] = 'inline; filename="' . $filename . '"';
	// ファイルサイズ
	$header['Content-Length'] = $fileinfo->getSize();
	
	if ($use_sendfile_header === true){
		// for reduce server load
		$header['X-Sendfile'] = $fileinfo->getRealPath();
	}
	$response = new Response();
	$response->setStatusCode(Response::STATUS_CODE_200);
	$response->getHeaders()->addHeaders($header);
	header($response->renderStatusLine());
	foreach ($response->getHeaders() as $_header) {
		header($_header->toString());
	}
	$obj = new SplFileObject($ref);
	// ファイルの読み込み
	$obj->openFile('rb');
	// ロック
	$obj->flock(LOCK_SH);
	echo $obj->fpassthru();
	// アンロック
	$obj->flock(LOCK_UN);
	// 念のためオブジェクトを開放
	unset($fileinfo, $obj);
	exit;
}

/* End of file cache_ref.inc.php */
/* Location: ./wiki-common/plugin/cache_ref.inc.php */