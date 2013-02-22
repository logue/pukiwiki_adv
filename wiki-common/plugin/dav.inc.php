<?php
/**
 * dav plugin.
 *
 * @copyright   Copyright &copy; 2010, Katsumi Saito <jo1upk@users.sourceforge.net>
 * @version	 $Id: dav.inc.php,v 1.0.2 2012/10/11 09:08:00 Logue Exp $
 * @license	 http://opensource.org/licenses/gpl-license.php GNU Public License (GPL2)
 *
 * 現状では、DOMDocument の実装上、PHP5以降でのみ稼働。
 * CentOS の場合は、php-xml パッケージを導入する必要あり。
 *
 * == for Windows Vista/7, check ==
 *  HKLM\SYSTEM\CurrentControlSet\Services\WebClient\Parameters\BasicAuthLevel
 *   0 - Basic authentication disabled
 *   1 - Basic authentication enabled for SSL shares only
 *   2 or greater - Basic authentication enabled for SSL shares
 *                  and for non-SSL shares
 *
 * == 謝辞 ==
 * dav.php がこのプラグインが生まれる源です。大幅に改修させていただき、現在の姿があります。
 * http://tyche.pu-toyama.ac.jp/~a-urasim/pukiwiki_dav/
 *
*/
use PukiWiki\Lib\Auth\Auth;
use PukiWiki\Lib\Factory;
defined('PLUGIN_DAV_SHOWONLYEDITABLE') or define('PLUGIN_DAV_SHOWONLYEDITABLE', false);
defined('PLUGIN_DAV_MUST_COMPRESS') or define('PLUGIN_DAV_MUST_COMPRESS', true); // セキュリティ上 false 運用は厳しい
defined('PLUGIN_DAV_CREATE_PAGE') or define('PLUGIN_DAV_CREATE_PAGE', false); // false, true
defined('PLUGIN_DAV_FOLODER_PAGE_BODY') or define('PLUGIN_DAV_FOLODER_PAGE_BODY', '*WebDAV Folder Page');

function plugin_dav_action()
{
	global $scriptname, $log_ua, $attach_link;

	if (!exist_plugin('attach')) dav_error_exit(500,'attach plugin not found.');

	$scriptname = SCRIPT_NAME;

	header('Expires: Sat,  1 Jan 2000 00:00:00 GMT');
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Cache-Control: post-check=0, pre-check=0', false);
	header('Pragma: no-cache');
	
	//$_SERVER['REQUEST_METHOD'] = 'PROPFIND';

	$req_headers = apache_request_headers();
	$path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';

	switch ($_SERVER['REQUEST_METHOD']) {
	case 'OPTIONS':
		header('DAV: 1');
		// OPTIONS,PROPFIND,GET,HEAD,PUT,DELETE,MOVE,COPY
		// header('Allow: OPTIONS,PROPFIND,GET,PUT,MOVE,COPY');
		header('Allow: OPTIONS,PROPFIND,GET,PUT,MOVE,COPY,LOCK,UNLOCK');
		header('MS-Author-Via: DAV');
		exit;
	case 'PROPFIND':
		// 添付する際にパスワードまたは、管理者のみの場合は、認証を要求
		if (!$attach_link || Auth::is_protect() || PLUGIN_ATTACH_PASSWORD_REQUIRE || PLUGIN_ATTACH_UPLOAD_ADMIN_ONLY) {
			$is_admin = PLUGIN_ATTACH_UPLOAD_ADMIN_ONLY ? true : false; // 管理者パスワードを許容するか
			$login = Auth::check_auth_pw();	// 認証済かどうか
			if (empty($login)) dav_error_exit(401,$is_admin); // 未認証の場合、認証要求
			// 認証判定
			if (dav_login($is_admin) === false || Auth::is_protect()) dav_error_exit(403);
		}

		if( empty($path_info)) {
			dav_error_exit(301, NULL, dav_myurl1().'/');
		}

		$depth = isset($req_headers['Depth']) ? $req_headers['Depth'] : 0;
		list($dir,$file) = dav_get_folder_info($path_info,$depth);
		if (!isset($dir)) dav_error_exit(404);

		$ret = dav_makemultistat($dir, $file, $_SERVER['REQUEST_URI'], $depth);
		if(!isset($ret)) dav_error_exit(301, NULL, dav_myurl().'/');
		header('HTTP/1.1 207 Multi-Status');
		header('Content-Type: text/xml');
		echo $ret->saveXML();
		exit;

	case 'GET':
	case 'HEAD':
		// 通常のファイル参照時は、このメソッドでアクセスされる
		$obj = dav_getfileobj($path_info, true);
		if(isset($obj) && $obj->exists) {
			$obj->open();
		}
		else if($_SERVER['REQUEST_METHOD'] == 'GET' && empty($path_info) && strpos($log_ua, 'MSIE') > 0) {
			dav_officious_message();
			exit;
		}
		else dav_error_exit(404);

		exit;

	case 'PUT':

		if (Auth::check_role('readonly')) dav_error_exit(403, 'PKWK_READONLY prohibits editing');

		// 添付する際にパスワードまたは、管理者のみの場合は、認証を要求
		if (PLUGIN_ATTACH_UPLOAD_ADMIN_ONLY) {
			if (!Auth::check_role('role_adm_contents') && !Auth::is_temp_admin()) {
				dav_error_exit(403);
			}
		}

		$size = isset($req_headers['Content-Length']) ? intval($req_headers['Content-Length']) : 0;
		// Windows 7のクライアントは、まず0バイト書いて、
		// それをLOCKしてから、上書きしにくる。
		// しかし、Pukiwikiは基本上書き禁止。
		// そこで0バイトの時は無視する。
		if($size == 0) exit;

		if($size > PLUGIN_ATTACH_MAX_FILESIZE) {
			dav_error_exit(403, 'file size error');
		}

		// get file
		$tmpfilename = tempnam('/tmp', 'dav');
		$fp = fopen($tmpfilename, 'wb');
		$size = 0;
		$putdata = fopen('php://input','rb');
		while ($data = fread($putdata,1024)){
			$size += strlen($data);
			fwrite($fp,$data);
		}
		@fclose($putdata);
		@fclose($fp);

		list($_page,$_filename) = dav_get_filename($path_info);

		// FIXME - 勝手にファイル名を変更するため、クライアントの挙動がおかしくなる
		if (PLUGIN_DAV_MUST_COMPRESS) {
			$type = get_mimeinfo($tmpfilename);
			$must_compress = attach_is_compress($type,PLUGIN_ATTACH_UNKNOWN_COMPRESS);
		} else {
			$must_compress = false;
		}

		$obj = dav_getfileobj($path_info, false, $must_compress);

		if (!is_object($obj)) dav_error_exit(403, 'no page');
		if($obj->exist){
			@unlink($tmpfilename);
			dav_error_exit(403, 'already exist.');
		}

		$ext = ($must_compress) ? dav_attach_get_ext() : '';
		switch ($ext) {
		case '.tgz':
			$tar = new tarlib();
			$tar->create(CACHE_DIR, 'tgz') or dav_error_exit(500);
			$tar->add_file($tmpfilename, $_filename);
			$tar->close();
			@rename($tar->filename, $obj->filename);
			chmod($obj->filename, PLUGIN_ATTACH_FILE_MODE);
			@unlink($tar->filename);
			break;
		case '.gz':
			$tp = fopen($tmpfilename,'rb') or dav_error_exit(500); // アップロードされたファイルが読めません
			$zp = gzopen($obj->filename, 'wb') or dav_error_exit(500); // 圧縮ファイルが書けません
			while (!feof($tp)) { gzwrite($zp,fread($tp, 8192)); }
			gzclose($zp);
			fclose($tp);
			chmod($obj->filename, PLUGIN_ATTACH_FILE_MODE);
			@unlink($tmpfilename);
			break;
		case '.bz2':
			$tp = fopen($tmpfilename,'rb') or dav_error_exit(500); // アップロードされたファイルが読めません
			$zp = bzopen($obj->filename, 'wb') or dav_error_exit(500); // 圧縮ファイルが書けません
			while (!feof($tp)) { bzwrite($zp,fread($tp, 8192)); }
			bzclose($zp);
			fclose($tp);
			chmod($obj->filename, PLUGIN_ATTACH_FILE_MODE);
			@unlink($tmpfilename);
			break;
		case '.zip':
			$zip = new ZipArchive();
			$zip->addFile($tmpfilename,$_filename);
			if ($zip->status !== 0) dav_error_exit(500); // $zip->status
			$zip->close();
			chmod($obj->filename, PLUGIN_ATTACH_FILE_MODE);
			@unlink($tmpfilename);
			break;
		default:
			if(copy($tmpfilename, $obj->filename)) {
				chmod($obj->filename, PLUGIN_ATTACH_FILE_MODE);
			}
			@unlink($tmpfilename);
		}

		if(is_page($obj->page)) touch(get_filename($obj->page));
		cache_timestamp_touch('attach');
		$pass = dav_get_pass();
		$obj->getstatus();
		$obj->status['pass'] = ($pass !== TRUE && $pass !== NULL) ? md5($pass) : '';
		$obj->putstatus();
		// FIXME
		// $must_compress 時のファイル名変更に追随できない
		exit;

	case 'DELETE':

		if (Auth::check_role('readonly')) dav_error_exit(403, 'PKWK_READONLY prohibits editing');

		// WinXP,Win7 では
		// フォルダーは消せないくせに、消せたように処理してしまう。
		// レスポンスコードを確認しないで消すので無意味。
		// また、フォルダーの削除は、ページを意味するので除外する
		if (substr($path_info,-1) === '/') dav_error_exit(501);

		// 添付する際にパスワードまたは、管理者のみの場合は、認証を要求
		if (PLUGIN_ATTACH_DELETE_ADMIN_ONLY) {
			if (!Auth::check_role('role_adm_contents') && !Auth::is_temp_admin()) {
				dav_error_exit(403);
			}
		}

		$obj = & dav_getfileobj($path_info, false);

		if (!is_object($obj)) dav_error_exit(403);
		if($obj->getstatus() == FALSE) dav_error_exit(404);

		$pass = dav_get_pass();
		$obj->delete($pass);
		if(file_exists($obj->filename)) {
			dav_error_exit(406, "can't delete this file");
		}

		cache_timestamp_touch('attach');

		exit;

	case 'MOVE':
	case 'COPY':
		// 添付ファイルのコピーと移動のみ
		// 同じページ内での添付ファイルの移動もわざわざ消して書いている
		// ページのコピーや移動は未実装 

		if (Auth::check_role('readonly')) dav_error_exit(403, 'PKWK_READONLY prohibits editing');

		// 添付する際にパスワードまたは、管理者のみの場合は、認証を要求
		if (PLUGIN_ATTACH_UPLOAD_ADMIN_ONLY || PLUGIN_ATTACH_DELETE_ADMIN_ONLY) {
			if (!Auth::check_role('role_adm_contents') && !Auth::is_temp_admin()) {
				dav_error_exit(403);
			}
		}

		// GET TO (Destination)
		$destname = isset($req_headers['Destination']) ? $req_headers['Destination'] : '';
		if(strpos($destname, dav_myurl0()) === 0) {
			$destname = substr($destname, strlen(dav_myurl0()));
		}
		if(strpos($destname, $scriptname) === 0) {
			$destname = urldecode(substr($destname, strlen($scriptname)));
		} else {
			dav_error_exit(403, 'not dav directory.');
		}

		// if ($path_info === $destname) dav_error_exit(403); // Forbidden

		// ページ名変更
		if(PLUGIN_DAV_CREATE_PAGE && $_SERVER['REQUEST_METHOD'] == 'MOVE'){
			// FIXME
			// 実在ページ && 添付ファイルなし なら許容
			// 下位ページがあっても、無視して実行、関連ファイルもリネームしていない
			$from = dav_strip_slash($path_info);
			if (is_page($from)) {
				$pages = dav_get_existpages_cache();
				if (isset($pages[$from]['file']) && count($pages[$from]['file']) == 0) {
					$to = dav_strip_slash($destname);
					if (isset($pages[$to])) dav_error_exit(409); // Conflict
					rename( get_filename($from), get_filename($to) );
					cache_timestamp_touch();
				} else {
					dav_error_exit(501); // Method not Implemented
				}
				exit;
			}
		}

		// FROM (PATH_INFO)
		if($_SERVER['REQUEST_METHOD'] == 'MOVE'){
			$obj1 = & dav_getfileobj($path_info, false);
		}
		else {
			$obj1 = & dav_getfileobj($path_info, true); // readonly
		}
		if (!is_object($obj1)) dav_error_exit(403, 'no src page.');
		if($obj1->getstatus() == FALSE) dav_error_exit(404);

		// TO (Destination)
		$obj2 = & dav_getfileobj($destname, false);
		if (!is_object($obj2)) dav_error_exit(403, 'no dst page.');
		if ($obj2->exist) dav_error_exit(409); // Conflict - 'already exist'

		if(copy($obj1->filename, $obj2->filename)) {
			chmod($obj2->filename, PLUGIN_ATTACH_FILE_MODE);
		} else {
			dav_error_exit(406, "can't copy it");
		}

		// COPY
		$pass = dav_get_pass();
		if(is_page($obj2->page)) touch(get_filename($obj2->page));
		$obj2->getstatus();
		$obj2->status['pass'] = ($pass !== TRUE && $pass !== NULL) ? md5($pass) : '';
		$obj2->putstatus();

		// MOVE(DELETE)
		if($_SERVER['REQUEST_METHOD'] == 'MOVE') {
			$obj1->delete($pass);
			if(file_exists($obj1->filename))
				dav_error_exit(406, "can't delete this file");
		}

		cache_timestamp_touch('attach');

		exit;

	case 'MKCOL':
		// Microsoft-WebDAV-MiniRedir などは、[新しいフォルダー] をまず作成しようとするので無意味
		if (!PLUGIN_DAV_CREATE_PAGE) dav_error_exit(403);
		if (Auth::check_role('readonly')) dav_error_exit(403, 'PKWK_READONLY prohibits editing');
		if (Auth::is_check_role(PKWK_CREATE_PAGE)) dav_error_exit(403, 'PKWK_CREATE_PAGE prohibits editing');

		// 添付する際にパスワードまたは、管理者のみの場合は、認証を要求
		if (PLUGIN_ATTACH_UPLOAD_ADMIN_ONLY || PLUGIN_ATTACH_DELETE_ADMIN_ONLY) {
			if (!Auth::check_role('role_adm_contents') && !Auth::is_temp_admin()) {
				dav_error_exit(403);
			}
		}

		// windows の場合、スラッシュで終わらない場合がある
		if (substr($path_info,-1) !== '/') $path_info .= '/';

		if(!isset($path_info)) dav_error_exit(403);
		if(preg_match('/^\/(.+)\/$/', $path_info, $matches) != 1) dav_error_exit(403);
		$page = dav_strip_slash($path_info);
		if(is_page($page)) dav_error_exit(403);

		page_write($page, PLUGIN_DAV_FOLODER_PAGE_BODY); // write initial string to the page.
		cache_timestamp_touch();

		// PROPFIND の挙動 (作成したフォルダーを表示させるため)
		$depth = '1';
		list($dir,$file) = dav_get_folder_info($path_info,$depth);
		if (!isset($dir)) dav_error_exit(404);

		$ret = dav_makemultistat($dir, $file, $_SERVER['REQUEST_URI'], $depth);
		if(!isset($ret)) dav_error_exit(301, NULL, dav_myurl().'/');
		header('HTTP/1.1 200 OK');
		header('Content-Type: text/xml');
		echo $ret->saveXML();
		exit;

	case 'PROPPATCH':
		// PROPPATCH が失敗するとファイルを消すため必要。
		header('HTTP/1.1 207 Multi-Status');
		header('Content-Type: text/xml');
		$ret = dav_proppatch_dummy_response($_SERVER['REQUEST_URI']);
		echo $ret->saveXML();
		exit;

	case 'LOCK':
	case 'UNLOCK':
	case 'POST':
		dav_error_exit(501); // Method not Implemented
		exit;
	default:
		dav_error_exit(405); // Method not Allowed
	}
	exit;
}

function dav_attach_get_ext()
{
	if (PLUGIN_ATTACH_COMPRESS_TYPE == 'TGZ' && exist_plugin('dump')) return '.tgz';
	if (PLUGIN_ATTACH_COMPRESS_TYPE == 'GZ' && extension_loaded('zlib')) return '.gz';
	if (PLUGIN_ATTACH_COMPRESS_TYPE == 'ZIP' && class_exists('ZipArchive')) return '.zip';
	if (PLUGIN_ATTACH_COMPRESS_TYPE == 'BZ2' && class_exists('bzip')) return '.bz2';
	return '';
}

function dav_get_fullpath_page()
{
	$pos = strpos($_SERVER['REQUEST_URI'], 'index.php');
	$filename = ($pos === false) ? '' : 'index.php';
	$pref = get_baseuri('abs').$filename;
	return substr(rawurldecode($_SERVER['REQUEST_URI']),strlen($pref));
}

function dav_makemultistat($dir, $file, $path, $depth)
{
		$ret = new DOMDocument();
		$ele = $ret->createElementNS('DAV:', 'D:multistatus');
		$ret->appendChild($ele);

	// windows の場合、スラッシュで終わらない場合がある
	if (substr($path,-1) !== '/') $path .= '/';

	if ($depth == '0') {
		// 直接指定なので、指定したものだけ出力
		if (count($dir) === 0) {
			$is_dir = 0;
			$val = & $file;
		} else {
			$is_dir = 1;
			$val = & $dir;
		}
		dav_makemultistat_sub($ret, $ele, $path, $val, $is_dir);
		return $ret;
	}

	// 常に dir カレントディレクトリ情報を出力
	// ここで必要な情報は、親フォルダーの情報となる
	$pages = dav_get_existpages_cache();
	$page = dav_get_fullpath_page();
	$page = dav_strip_slash($page);
	$val = array();
	$val['time'] = (isset($pages[$page])) ? $pages[$page]['time'] : 0;
	dav_makemultistat_sub($ret, $ele, $path, $val, 1);

	// このフォルダーに存在する dir および file の情報
	// dir
	foreach($dir as $page => $val) {
		$str = $path.rawurlencode($page).'/';
		dav_makemultistat_sub($ret, $ele, $str, $val, 1);
	}
	// file
	foreach($file as $name => $val){
		$str = $path.rawurlencode($name);
		dav_makemultistat_sub($ret, $ele, $str, $val, 0);
	}

	return $ret;
}


function dav_makemultistat_sub(&$doc, &$ele, $name, $type, $is_dir)
{
	$res = $doc->createElementNS('DAV:', 'D:response');
	$ele->appendChild($res);
	$href = $doc->createElementNS('DAV:', 'D:href', $name);
	$res->appendChild($href);

	$propstat = $doc->createElementNS('DAV:', 'D:propstat');
	$res->appendChild($propstat);

	$prop = $doc->createElementNS('DAV:', 'D:prop');
	$propstat->appendChild($prop);

	$resourcetype = $doc->createElementNS('DAV:', 'D:resourcetype');
	$prop->appendChild($resourcetype);

	// dir
	if ($is_dir && count($type)) {
		// D:collection -> dir の場合には付ける
		$coll = $doc->createElementNS('DAV:', 'D:collection');
		$resourcetype->appendChild($coll);
	}

	if (!empty($type['time'])) {
		// file
		// FIXME: 生成日時ではないものの
		// creationdate
		$str_time = gmdate('Y-m-d\TH:i:s\Z',$type['time']);
		$creationdate = $doc->createElementNS('DAV:', 'D:creationdate');
		$creationdate->appendChild($doc->createTextNode(''.$str_time));
		$prop->appendChild($creationdate);
		// getlastmodified
		$str_time = gmdate('D, d M Y H:i:s',$type['time']).' GMT';
		$getlastmodified = $doc->createElementNS('DAV:', 'D:getlastmodified');
		$getlastmodified->appendChild($doc->createTextNode(''.$str_time));
		$prop->appendChild($getlastmodified);
	}

	if (!empty($type['size'])) {
		// getcontentlength
		$getcontentlength = $doc->createElementNS('DAV:', 'D:getcontentlength');
		$getcontentlength->appendChild($doc->createTextNode(''.$type['size']));
		$prop->appendChild($getcontentlength);
	}

	$stat = $doc->createElementNS('DAV:', 'D:status', 'HTTP/1.1 200 OK');
	$propstat->appendChild($stat);
}

function dav_proppatch_dummy_response($path)
{
	$doc = new DOMDocument();
	$ele = $doc->createElementNS('DAV:', 'D:multistatus');
	$doc->appendChild($ele);
	$res = $doc->createElementNS('DAV:', 'D:response');
	$ele->appendChild($res);
	$href = $doc->createElementNS('DAV:', 'D:href', $path);
	$res->appendChild($href);
	$propstat = $doc->createElementNS('DAV:', 'D:propstat');
	$res->appendChild($propstat);
	$prop = $doc->createElementNS('DAV:', 'D:prop');
	$propstat->appendChild($prop);
	$prop->appendChild($doc->createElementNS('urn:schemas-microsoft-com:', 'Z:Win32CreationTime'));
	$prop->appendChild($doc->createElementNS('urn:schemas-microsoft-com:', 'Z:Win32LastAccessTime'));
	$prop->appendChild($doc->createElementNS('urn:schemas-microsoft-com:', 'Z:Win32LastModifiedTime'));
	$prop->appendChild($doc->createElementNS('urn:schemas-microsoft-com:', 'Z:Win32FileAttributes'));
	$stat = $doc->createElementNS('DAV:', 'D:status', 'HTTP/1.1 200 OK');
	$propstat->appendChild($stat);
	return $doc;
}

function dav_error_exit($code, $msg = NULL, $url = NULL)
{
	global $auth_type, $realm;

		$array_msg = array(
			301 => array('msg1'=>'Moved',                  'msg2'=>''),
			401 => array('msg1'=>'Authorization Required', 'msg2'=>''),
			403 => array('msg1'=>'Forbidden',              'msg2'=> 'Your request is forbideen.'),
			404 => array('msg1'=>'Not Found',              'msg2'=> 'The file/directory you request is not found.'),
			405 => array('msg1'=>'Method not Allowed',     'msg2'=> 'Your request is not allowd.'),
			406 => array('msg1'=>'Not acceptable',         'msg2'=> 'Your request is not acceptable'),
			409 => array('msg1'=>'Conflict',               'msg2'=> 'A resource cannot be created at the destination until one or more intermediate collections have been created.'), // MOVE,COPY
			423 => array('msg1'=>'Locked',                 'msg2'=> 'The source or the destination resource was locked.'), // MOVE,COPY
			500 => array('msg1'=>'Internal Server Error',  'msg2'=> 'Internal Server Error.'),
			501 => array('msg1'=>'Method not Implemented', 'msg2'=> 'The method you request is not implemented.'),
		);

		if (!array_key_exists($code,$array_msg)) $code = 500;
		$msg1 = & $array_msg[$code]['msg1'];
		$msg2 = & $array_msg[$code]['msg2'];
		header('HTTP/1.1 '.$code.' '.$msg1);

		switch ($code) {
		case 301:
				header('Location: '.$url);
				exit;
		case 401:
		switch ($auth_type) {
		case 2:
			header('WWW-Authenticate: Digest realm="'.$realm.
				'", qop="auth", nonce="'.uniqid().'", opaque="'.md5($realm).'"');
			exit;
		default:
			header('WWW-Authenticate: Basic realm="'.$realm.'"');
					exit;
		}
		exit;
		}

		echo '<html><head>';
		echo '<title>'.$code.' '.$msg1.'</title>';
		echo '</head><body>';
		echo '<h1>'.$code.' '.$msg1.'</h1>';
		echo '<p>'.$msg2.'</p>';
		if(isset($msg)) echo '<p>'.htmlsc($msg).'</p>';
		echo '<p>This script should be used with WebDAV protocol.</p>';
		echo '</body></html>';
		exit;
}

function dav_officious_message()
{
	global $scriptname;

	$myurl1 = dav_myurl1();
	// $port = apache_getenv('SERVER_PORT');
	$port = $_SERVER['SERVER_PORT'];
	echo '<html>';
	echo '<head><title>officious message</title></head>';
	echo '<body>';
	echo '<p>Please use this script with WebDAV protocol.</p>';
	echo '<p>If your client OS is <font size=+2>Windows XP</font>,';

	if( strrpos($scriptname, '/') != 0){
		echo ' you should place this script in document root directory';
	}
	else if($_SERVER['HTTPS'] != 'on' && (!isset($port) || $port == 80)){
		$myurl2 = preg_replace('/^http:/', 'https:', $myurl1);
		echo ' you may be able to click';
		echo ' <a style="behavior: url(#default#AnchorClick);"';
		echo ' Folder="'.$myurl1.'?">'.$myurl1.'?';
		echo '</a> or <a style="behavior: url(#default#AnchorClick);"';
		echo ' Folder="'.$myurl2.'/">'.$myurl2.'/';
		echo '</a>. <br>Or do <font size=+2>"Add Network Place"';
		echo ' in "My Network Place"</font>';
	}
	else {
		echo ' you may be able to click';
		echo ' <a style="behavior: url(#default#AnchorClick);"';
		echo ' Folder="'.$myurl1.'/">'.$myurl1.'/';
		echo '</a>. <br>Or do <font size=+2>"Add Network Place"';
		echo ' in "My Network Place"</font>';
	}

	echo '</p>';
	echo '<p>If your client OS is <font size=+2>Windows 7</font>, ';

	if($_SERVER['HTTPS'] != 'on' && (!isset($port) || $port == 80)){
		$myurl2 = preg_replace('/^http:/', 'https:', $myurl1);
		echo 'you can try <font size=+2>"net use w: '.$myurl1.'"</font>';
		echo ' or <font size=+2>"net use w: '.$myurl2.'".</font>';
		echo ' ("w:" is arbitary drive letter.)';
		echo '<br>Or you are rarely(at the very first time after booting?)';
		echo ' be able to click';
		echo ' <a style="behavior: url(#default#AnchorClick);"';
		echo ' Folder="'.$myurl1.'">'.$myurl1.'</a>';
		echo ' or <a style="behavior: url(#default#AnchorClick);"';
		echo ' Folder="'.$myurl2.'">'.$myurl2.'</a>.';
		echo '<br>(http: can be used only when the value in the regsitry key';
		echo ' "HKLM\SYSTEM\CurrentControlSet\Services\WebClient\Parameters\BasicAuthLevel" is "2")';
	}
	else {
		echo 'you can try <font size=+2>"net use w: '.$myurl1.'"</font>';
		echo ' ("w:" is arbitary drive letter.)';
		echo '<br>Or you are rarely(at the very first time after booting?)';
		echo ' be able to click';
		echo ' <a style="behavior: url(#default#AnchorClick);"';
		echo ' Folder="'.$myurl1.'">'.$myurl1.'</a>.';
	}

	echo '</p>';
	echo '</body>';
	echo '</html>';
}

function dav_myurl0()
{
	// $_SERVER['HTTPS'] - https かどうかの判定用
	// get_script_absuri();
	// rc - http://jo1upk.blogdns.net:80
	$is_https = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? true : false;
	$url = ($is_https ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];

	// $port = apache_getenv('SERVER_PORT');
	$port = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] ? $_SERVER['SERVER_PORT'] : 80;
	if( $port !== 443 && $is_https === true)
		$url .= ':'.$port;
	else if( $port !== 80 && $is_https === false)
		$url .= ':'.$port;

	return $url;
}
function dav_myurl1()
{
	global $scriptname;
	return dav_myurl0() . $scriptname;
}
function dav_myurl()
{
	return dav_myurl0() . $_SERVER['REQUEST_URI'];
}

function dav_getfileobj($path, $readonly=true, $must_compress=false)
{
	// $path = mb_convert_encoding($path, SOURCE_ENCODING, 'UTF-8');
	list($page,$_file) = dav_get_filename($path);
	if (empty($_file)) return NULL;

	if(!is_page($page)) return NULL;
	if(!$readonly && !is_editable($page)) return NULL;

	if(!check_readable($page, false, false)) dav_error_exit(401); // 'user/password'
	if(!$readonly && !check_editable($page, false, false)) dav_error_exit(401); // 'user/password'

	$ext = ($must_compress) ? dav_attach_get_ext() : '';
	return new AttachFile($page, $_file.$ext);
}

function dav_get_folder_info($path,$depth)
{
	global $attach_link;

	$path = dav_strip_slash($path);

	$info_dir = $info_file = array();
	$pages = dav_get_existpages_cache();
	list($page, $file, $exist) = dav_get_page_name($pages, $path);

	// 単一の dir または file の情報を戻す
	if ($depth === '0') {
		if (!empty($file) && !$exist) return array(NULL,NULL); // 新規ファイル
		if (isset($pages[$page]['file'][$file])) {
			// 実在ファイル
			$info_file = $pages[$page]['file'][$file];
		} else {
			$time = (isset($pages[$page]['time'])) ? $pages[$page]['time'] : 0;
			$info_dir = array('path'=>$page,'time'=>$time);
		}
		return array($info_dir,$info_file);
	}
	

	foreach($pages as $_page=>$val) {
		$_time = $val['time'];
		list($_L1_name,$_L1_path) = dav_is_last_name($page,$_page);

		if (!empty($page)) { // TOP以外の場合は、該当の下位ページのみ対象
			if (strpos($_page,$page.'/') !== 0) continue; // 対象ページのみ
			if ($_page === $page) continue; // このページ
		}

		if (!empty($_L1_name)) {
			if (!isset($pages[$_L1_path])) $_time = 0; // 中間架空ページ
			$info_dir[$_L1_name] = array('path'=>$_L1_path,'time'=>$_time);
		}
	}

	// 添付ファイル非表示の場合は、コンテンツ管理者なら表示する
	if (!$attach_link && Auth::check_role('role_adm_contents')) return array($info_dir,$info_file);

	if (isset($pages[$page]['file'])) $info_file = $pages[$page]['file'];
	
	natcasesort($info_file);

	return array($info_dir,$info_file);
}

function dav_strip_slash($x)
{
	$x = (substr($x, 0, 1) == '/') ? substr($x, 1) : $x; // 先頭の / は一律カット
	$x = (substr($x, -1) == '/') ? substr($x, 0, -1) : $x; // 最後の / は一律カット
	return $x;
}

function dav_get_existpages_cache()
{
	static $retval, $attaches;

	$cache_name = CACHE_DIR.PKWK_EXSISTS_DATA_CACHE;
	if (!cache_timestamp_compare_date('wiki',$cache_name)) {
		unset($retval);
	}

	if (isset($retval)) return $retval;

	$retval = array();
	$auth_key = Auth::get_user_info();
	$pages = get_existpages_cache(DATA_DIR,PKWK_TXT_EXTENTION,false);
	
	if (!isset($attaches)) $attaches = get_attachfiles_cache();

	foreach($pages as $file=>$val) {
		$_page = $val['page'];
		$_time = $val['time'];
		$wiki = Factory::Wiki($_page);

		if ($wiki->isHidden()) continue;
		//if (is_ignore_page($_page)) continue;
		if (! $wiki->isReadable()) continue;
		if (PLUGIN_DAV_SHOWONLYEDITABLE && !$wiki->isEditable()) continue;


		$retval[$_page]['time'] = $_time;
		$retval[$_page]['file'] = isset($attaches[$_page]) ? $attaches[$_page] : array();
	}
	asort($retval);
	return $retval;
}

function dav_get_page_name(& $pages, $path)
{
	// ex. ページ/ページ
	// ex. ページ/ファイル
	if (empty($path)) return array(null, null, 0);
	if (isset($pages[$path])) return array($path, null, 1); // 実在ページ

	// 中間ページの対応
	foreach($pages as $_page=>$val) {
		if (strpos($_page,$path) === 0) return array($path, null ,0);
	}

	// 実在しないため、ファイル名付きかの判定
	$pos = strrpos($path, '/');
	if ($pos !== false) {
		$page = substr($path, 0, $pos);
		$file = substr($path, $pos+1);
		// 実在ページ + 実在ファイル
		if (isset($pages[$page]['file'][$file])) return array($page, $file, 1);
		// 実在ページ + 新規ファイル
		if (isset($pages[$page])) return array($page, $file ,0);
	}

	return array($path, null, 0);
}

function dav_is_last($path,$page)
{
		$a	= explode('/', $path);
		$full = explode('/', $page);

	// 最上位階層の場合
		if (empty($path) || count($full)==1) {
				return array(0=>1,1=>$full[0],2=>$full[0]);
		}

		$b	= array_slice($full, count($a));

		// 0: 末端か？
		$rc[0] = (count($b) < 2) ? true : false;
		// 1: 第１階層名称 (直下のページ名)
		$rc[1] = $b[0];
	// 2: 直下までの絶対ページ名
		$rc[2] = $path.'/'.$b[0];
		return $rc;
}

function dav_is_last_name($path,$name)
{
	$rc_L1 = dav_is_last($path,$name);
	if ($rc_L1[0]) {
		return array($rc_L1[1],$rc_L1[2]);
	} else {
		// 次が末端か？
		$rc_L2 = dav_is_last($page,$rc_L1[2]);
		if ($rc_L2[0]) return array($rc_L1[1],$rc_L1[2]);
	}
	return array('','');
}


function dav_get_filename($path)
{
	if(!isset($path)) return array('','');
	$matches = array();
	if(preg_match('/^\/(.+)\/([^\/]+)$/', $path, $matches) != 1) return array('','');
	return array($matches[1],$matches[2]);
}

function dav_login($is_admin=false)
{
	global $auth_type, $auth_users;

	if ($is_admin && Auth::is_temp_admin()) return true;

	switch($auth_type) {
	case 1: return Auth::auth_pw($auth_users);
	case 2: return Auth::auth_digest($auth_users);
	}

	return false;
}

function dav_get_pass() { return (empty($_SERVER['PHP_AUTH_PW'])) ? NULL : $_SERVER['PHP_AUTH_PW'];}

// http://bugs.developer.mindtouch.com/view.php?id=2746
if (!function_exists('apache_request_headers')) {
	function apache_request_headers()
	{
		$headers = array();
		foreach($_SERVER as $name => $value) {
			if (substr($name,0,5) == 'HTTP_')
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
		}
		return $headers;
	}
}
/* End of file dav.inc.php */
/* Location: ./wiki-common/plugin/dav.inc.php */
