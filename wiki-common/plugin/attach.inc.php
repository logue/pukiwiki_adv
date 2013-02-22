<?php
// PukPukiPlus.
// $Id: attach.inc.php,v 1.92.51 2012/10/11 19:05:00 Logue Exp $
// Copyright (C)
//   2010-2012 PukiWiki Advance Developers Team <http://pukiwiki.logue.be/>
//   2005-2009 PukiWiki Plus! Team
//   2003-2007,2009,2011 PukiWiki Developers Team
//   2002-2003 PANDA <panda@arino.jp> http://home.arino.jp/
//   2002      Y.MASUI <masui@hisec.co.jp> http://masui.net/pukiwiki/
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// File attach plugin
use PukiWiki\Lib\Auth\Auth;
// NOTE (PHP > 4.2.3):
//    This feature is disabled at newer version of PHP.
//    Set this at php.ini if you want.
// Max file size for upload on PHP (PHP default: 2MB)
defined('PLUGIN_ATTACH_UPLOAD_MAX_FILESIZE')	or define('PLUGIN_ATTACH_UPLOAD_MAX_FILESIZE', '4M');		// default: 4MB
ini_set('upload_max_filesize', PLUGIN_ATTACH_UPLOAD_MAX_FILESIZE);

// Max file size for upload on script of PukiWikiX_FILESIZE
defined('PLUGIN_ATTACH_MAX_FILESIZE')		or define('PLUGIN_ATTACH_MAX_FILESIZE', (2048 * 1024));		// default: 1MB

// 管理者だけが添付ファイルをアップロードできるようにする
defined('PLUGIN_ATTACH_UPLOAD_ADMIN_ONLY')	or define('PLUGIN_ATTACH_UPLOAD_ADMIN_ONLY', FALSE);		// FALSE or TRUE

// 管理者だけが添付ファイルを削除できるようにする
defined('PLUGIN_ATTACH_DELETE_ADMIN_ONLY')	or define('PLUGIN_ATTACH_DELETE_ADMIN_ONLY', FALSE);		// FALSE or TRUE

// 管理者が添付ファイルを削除するときは、バックアップを作らない
// PLUGIN_ATTACH_DELETE_ADMIN_ONLY=TRUEのとき有効
defined('PLUGIN_ATTACH_DELETE_ADMIN_NOBACKUP')	or define('PLUGIN_ATTACH_DELETE_ADMIN_NOBACKUP', FALSE);	// FALSE or TRUE

// アップロード/削除時にパスワードを要求する(ADMIN_ONLYが優先)
defined('PLUGIN_ATTACH_PASSWORD_REQUIRE')	or define('PLUGIN_ATTACH_PASSWORD_REQUIRE', FALSE);		// FALSE or TRUE

// 添付ファイル名を変更できるようにする
defined('PLUGIN_ATTACH_RENAME_ENABLE')		or define('PLUGIN_ATTACH_RENAME_ENABLE', TRUE);			// FALSE or TRUE

// ファイルのアクセス権
defined('PLUGIN_ATTACH_FILE_MODE')		or define('PLUGIN_ATTACH_FILE_MODE', 0644);
// define('PLUGIN_ATTACH_FILE_MODE', 0604);			// for XREA.COM

// mime-typeを記述したページ
define('PLUGIN_ATTACH_CONFIG_PAGE_MIME', 'plugin/attach/mime-type');

defined('PLUGIN_ATTACH_UNKNOWN_COMPRESS')	or define('PLUGIN_ATTACH_UNKNOWN_COMPRESS', 0);			// 1(compress) or 0(raw)
defined('PLUGIN_ATTACH_COMPRESS_TYPE')		or define('PLUGIN_ATTACH_COMPRESS_TYPE', 'TGZ');		// TGZ, GZ, BZ2 or ZIP

// 添付ファイルキャッシュを使う（ページの表示やページごとの添付ファイル一覧表示は早くなりますが、全ページではむしろ重くなります）
defined('PLUGIN_ATTACH_USE_CACHE')    or define('PLUGIN_ATTACH_USE_CACHE', false);
// 添付ファイルのキャッシュの接頭辞
defined('PLUGIN_ATTACH_CACHE_PREFIX') or define('PLUGIN_ATTACH_CACHE_PREFIX', 'attach-');

function plugin_attach_init()
{
	global $_string;
	$messages = array(
		'_attach_messages' => array(
			'msg_uploaded'	=> T_('Uploaded the file to $1'),
			'msg_deleted'	=> T_('Deleted the file in $1'),
			'msg_freezed'	=> T_('The file has been frozen.'),
			'msg_unfreezed'	=> T_('The file has been unfrozen'),
			'msg_upload'	=> T_('Upload to $1'),
			'msg_info'		=> T_('File information'),
			'msg_confirm'	=> T_('Delete %s.'),
			'msg_list'		=> T_('List of attached file(s)'),
			'msg_listpage'	=> T_('List of attached file(s) in $1'),
			'msg_listall'	=> T_('Attached file list of all pages'),
			'msg_file'		=> T_('Attach file'),
			'msg_maxsize'	=> T_('Maximum file size is %s.'),
			'msg_count'		=> T_('%s download'),
			'msg_password'	=> T_('password'),
			'msg_adminpass'	=> T_('Administrator password'),
			'msg_delete'	=> T_('Delete file.'),
			'msg_freeze'	=> T_('Freeze file.'),
			'msg_unfreeze'	=> T_('Unfreeze file.'),
			'msg_renamed'	=> T_('The file has been renamed'),
			'msg_isfreeze'	=> T_('File is frozen.'),
			'msg_rename'	=> T_('Rename'),
			'msg_newname'	=> T_('New file name'),
			'msg_require'	=> T_('(require administrator password)'),
			'msg_filesize'	=> T_('size'),
			'msg_type'		=> T_('type'),
			'msg_date'		=> T_('date'),
			'msg_dlcount'	=> T_('access count'),
			'msg_md5hash'	=> T_('MD5 hash'),
			'msg_page'		=> T_('Page'),
			'msg_filename'	=> T_('Stored filename'),
			'msg_thispage'	=> T_('This page'),
			'err_noparm'	=> T_('Cannot upload/delete file in $1'),
			'err_exceed'	=> T_('File size too large to $1'),
			'err_exists'	=> T_('File already exists in $1'),
			'err_notfound'	=> T_('Could not find the file in $1'),
			'err_noexist'	=> T_('File does not exist.'),
			'err_delete'	=> T_('Cannot delete file in  $1'),
			'err_rename'	=> T_('Cannot rename this file'),
			'err_password'	=> $_string['invalidpass'],
			'err_upload'	=> T_('It failed in uploading.'),
			'err_adminpass'	=> T_('Wrong administrator password'),
			'err_ini_size'	=> T_('The value of the upload_max_filesize directive of php.ini is exceeded.'),
			'err_form_size'	=> T_('MAX_FILE_SIZE specified by the HTML form is exceeded.'),
			'err_partial'	=> T_('Only part is uploaded.'),
			'err_no_file'	=> T_('The file was not uploaded.'),
			'err_no_tmp_dir'=> T_('There is no temporary directory.'),
			'err_cant_write'=> T_('It failed in writing in the disk.'),
			'err_extension'	=> T_('The uploading of the file was stopped by the enhancement module.'),
			'btn_upload'	=> T_('Upload'),
			'btn_info'		=> T_('Information'),
			'btn_submit'	=> T_('Submit'),
			'err_too_long'	=> T_('Query string (page name and/or file name) too long'),
			'err_nopage'	=> T_('No such page'),
			'err_tmp_fail'	=> T_('It failed in the generation of a temporary file.'),
			'err_load_file'	=> T_('The uploaded file cannot be read.'),			// アップロードされたファイルが読めません。
			'err_write_tgz'	=> T_('The compression file cannot be written.'),	// 圧縮ファイルが書けません。
			'err_filename'	=> T_('File name is too long. Please rename more short file name before upoload.')	// ファイル名が長すぎます。アップロードする前に短いファイル名にしてください。
		),
	);
	set_plugin_messages($messages);
}

//-------- convert
function plugin_attach_convert()
{
	global $vars;

	$page = isset($vars['page']) ? $vars['page'] : '';

	$nolist = $noform = FALSE;
	if (func_num_args() > 0) {
		foreach (func_get_args() as $arg) {
			$arg = strtolower($arg);
			$nolist |= ($arg == 'nolist');
			$noform |= ($arg == 'noform');
		}
	}

	$ret = '';
	if (! $nolist) {
		$obj  = new AttachPages($page);
		$ret .= $obj->toString($page, TRUE);
	}
	if (! $noform) {
		$ret .= attach_form($page);
	}

	return $ret;
}

//-------- action
function plugin_attach_action()
{
	global $vars, $_attach_messages, $_string;

	// Backward compatible
	if (isset($vars['openfile'])) {
		$vars['file'] = $vars['openfile'];
		$vars['pcmd'] = 'open';
	}
	if (isset($vars['delfile'])) {
		$vars['file'] = $vars['delfile'];
		$vars['pcmd'] = 'delete';
	}

	$pcmd  = isset($vars['pcmd'])  ? $vars['pcmd']  : NULL;
	$refer = isset($vars['refer']) ? $vars['refer'] : NULL;
	$pass  = isset($vars['pass'])  ? $vars['pass']  : NULL;
	$page  = isset($vars['page'])  ? $vars['page']  : $refer;

	if (!empty($refer) && is_pagename($refer)) {
		if(in_array($pcmd, array('info', 'open', 'list'))) {
			check_readable($refer);
		} else {
			check_editable($refer);
		}
	}

	// Dispatch
	if (isset($_FILES['attach_file'])) {
		// Upload
		return attach_upload($_FILES['attach_file'], $refer, $pass);
	} else {
		switch ($pcmd) {
		case 'delete':	/*FALLTHROUGH*/
		case 'freeze':
		case 'unfreeze':
			// if (PKWK_READONLY) die_message('PKWK_READONLY prohibits editing');
			if (Auth::check_role('readonly')) die_message( $_string['error_prohibit'] );
		}
		switch ($pcmd) {
			case 'info'     : return attach_info();
			case 'delete'   : return attach_delete();
			case 'open'     : return attach_open();
			case 'list'     : return attach_list($page);
			case 'freeze'   : return attach_freeze(TRUE);
			case 'unfreeze' : return attach_freeze(FALSE);
			case 'rename'   : return attach_rename();
			case 'upload'   : return attach_showform();
			case 'form'     : return array('msg'  =>str_replace('$1', $refer, $_attach_messages['msg_upload']), 'body'=>attach_form($refer));
			case 'progress' : return get_upload_progress();
		}
		return ($page == '' || ! is_page($page)) ? attach_list() : attach_showform();
	}
}

//-------- call from skin
function attach_filelist()
{
	global $vars, $_attach_messages;

	$page = isset($vars['page']) ? $vars['page'] : '';
	$obj = new AttachPages($page, 0);

	return isset($obj->pages[$page]) ? ('<dl class="attach_filelist">'."\n".'<dt>'.$_attach_messages['msg_file'].' :</dt>'."\n".$obj->toString($page, TRUE, 'dl') . '</dl>'."\n") : '';
}

//-------- 実体
// ファイルアップロード
// $pass = NULL : パスワードが指定されていない
// $pass = TRUE : アップロード許可
function attach_upload($file, $page, $pass = NULL)
{
	global $_attach_messages, $_string;

	// if (PKWK_READONLY) die_message('PKWK_READONLY prohibits editing');
	if (Auth::check_role('readonly')) die_message($_string['error_prohibit']);

	// Check query-string
	$query = get_cmd_uri('attach', '', '', array(
		'refer'=>$page,
		'pcmd'=>'info',
		'file'=>$file['name']
	));

	if ($file['error'] !== UPLOAD_ERR_OK) {
		return array(
			'result'=>FALSE,
			'msg'=>'<p class="message_box ui-state-error">'.attach_set_error_message($file['error']).'</p>'
		);
	}

	if (PKWK_QUERY_STRING_MAX && strlen($query) > PKWK_QUERY_STRING_MAX) {
		pkwk_common_headers();
		echo($_attach_messages['err_too_long']);
		exit;
	} else if (! is_page($page)) {
		die_message($_attach_messages['err_nopage']);
	} else if ($file['tmp_name'] == '' || ! is_uploaded_file($file['tmp_name'])) {
		return array(
			'result'=>FALSE,
			'msg'=>$_attach_messages['err_upload']);
	} else if ($file['size'] > PLUGIN_ATTACH_MAX_FILESIZE) {
		return array(
			'result'=>FALSE,
			'msg'=>$_attach_messages['err_exceed']);
	} else if (! is_pagename($page) || ($pass !== TRUE && ! is_editable($page))) {
		return array(
			'result'=>FALSE,'
			msg'=>$_attach_messages['err_noparm']);

	// } else if (PLUGIN_ATTACH_UPLOAD_ADMIN_ONLY && $pass !== TRUE &&
	} else if (PLUGIN_ATTACH_UPLOAD_ADMIN_ONLY && Auth::check_role('role_adm_contents') && $pass !== TRUE &&
		  ($pass === NULL || ! pkwk_login($pass))) {
		return array(
			'result'=>FALSE,
			'msg'=>$_attach_messages['err_adminpass']);
	}

	if (PLUGIN_ATTACH_USE_CACHE){
		global $cache;
		$cache['wiki']->removeItem(PLUGIN_ATTACH_CACHE_PREFIX.md5($refer));
	}

	return attach_doupload($file, $page, $pass);
}

function attach_set_error_message($err_no)
{
	global $_attach_messages;

	switch($err_no) {
		case UPLOAD_ERR_INI_SIZE:
			return $_attach_messages['err_ini_size'];
		case UPLOAD_ERR_FORM_SIZE:
			return $_attach_messages['err_form_size'];
		case UPLOAD_ERR_PARTIAL:
			return $_attach_messages['err_partial'];
		case UPLOAD_ERR_NO_FILE:
			return $_attach_messages['err_no_file'];
		case UPLOAD_ERR_NO_TMP_DIR:
			return $_attach_messages['err_no_tmp_dir'];
		case UPLOAD_ERR_CANT_WRITE:
			return $_attach_messages['err_cant_write'];
		case UPLOAD_ERR_EXTENSION:
			return $_attach_messages['err_extension'];
	}
	return $_attach_messages['err_upload'];
}

function attach_gettext($path, $lock=FALSE)
{
	$fp = @fopen($path, 'r');
	if ($fp == FALSE) return FALSE;

	if ($lock) {
		@flock($fp, LOCK_SH);
	}

	// Returns a value
	$result = fread($fp, filesize($path));

	if ($lock) {
		@flock($fp, LOCK_UN);
		@fclose($fp);
	}
	return $result;
}

function attach_doupload(&$file, $page, $pass=NULL, $temp='', $copyright=FALSE, $notouch=FALSE)
{
	global $_attach_messages, $_strings;
	global $notify, $notify_subject, $notify_exclude, $spam;

	// Check Illigal Chars
	if (preg_match(PKWK_ILLEGAL_CHARS_PATTERN, $page) || preg_match(PKWK_ILLEGAL_CHARS_PATTERN, $file['name'])){
		die_message($_strings['illegal_chars']);
	}

	$type = get_mimeinfo($file['tmp_name']);
	$must_compress = (PLUGIN_ATTACH_UNKNOWN_COMPRESS !== 0) ? attach_is_compress($type,PLUGIN_ATTACH_UNKNOWN_COMPRESS) : false;

	// ファイル名の長さをチェック
	$filename_length = strlen(encode($page).'_'.encode($file['name']));
	if ( $filename_length  >= 255 || ($must_compress && $filename_length >= 251 )){
		return array(
			'result'=>FALSE,
			'msg'=>$_attach_messages['err_filename']
		);
	}

	if ($must_compress) {
		// if attach spam, filtering attach file.
		$vars['uploadname'] = $file['name'];
		$vars['uploadtext'] = attach_gettext($file['tmp_name']);
		if ($vars['uploadtext'] === '' || $vars['uploadtext'] === FALSE) return FALSE;

		//global $spam;
		if ($spam !== 0) {
			if (isset($spam['method']['attach'])) {
				$_method = & $spam['method']['attach'];
			} else if (isset($spam['method']['_default'])) {
				$_method = & $spam['method']['_default'];
			} else {
				$_method = array();
			}
			$exitmode = isset($spam['exitmode']) ? $spam['exitmode'] : '';
			pkwk_spamfilter('File Attach', $page, $vars, $_method, $exitmode);
		}
	}

	if ($must_compress && is_uploaded_file($file['tmp_name'])) {
		switch (PLUGIN_ATTACH_COMPRESS_TYPE){
			case 'TGZ' :
				if (exist_plugin('dump')) {
					$obj = new AttachFile($page, $file['name'] . '.tgz');
					if ($obj->exist)
						return array('result'=>FALSE,
							'msg'=>$_attach_messages['err_exists']);

					$tar = new tarlib();
					$tar->create(CACHE_DIR, 'tgz') or
						die_message( $_attach_messages['err_tmp_fail'] );
					$tar->add_file($file['tmp_name'], $file['name']);
					$tar->close();

					@rename($tar->filename, $obj->filename);
					chmod($obj->filename, PLUGIN_ATTACH_FILE_MODE);
					@unlink($tar->filename);
				}
			break;
			case 'GZ' :
				if (extension_loaded('zlib')) {
					$obj = new AttachFile($page, $file['name'] . '.gz');
					if ($obj->exist)
						return array('result'=>FALSE,
							'msg'=>$_attach_messages['err_exists']);

					$tp = fopen($file['tmp_name'],'rb') or
						die_message($_attach_messages['err_load_file']);
					$zp = gzopen($obj->filename, 'wb') or
						die_message($_attach_messages['err_write_tgz']);

					while (!feof($tp)) { gzwrite($zp,fread($tp, 8192)); }
					gzclose($zp);
					fclose($tp);
					chmod($obj->filename, PLUGIN_ATTACH_FILE_MODE);
					@unlink($file['tmp_name']);
				}
			break;
			case 'ZIP' :
				if (class_exists('ZipArchive')) {
					$obj = new AttachFile($page, $file['name'] . '.zip');
					if ($obj->exist)
						return array('result'=>FALSE,
							'msg'=>$_attach_messages['err_exists']);
					$zip = new ZipArchive();

					$zip->addFile($file['tmp_name'],$file['name']);
					// if ($zip->status !== ZIPARCHIVE::ER_OK)
					if ($zip->status !== 0)
						die_message( $_attach_messages['err_upload'].'('.$zip->status.').' );
					$zip->close();
					chmod($obj->filename, PLUGIN_ATTACH_FILE_MODE);
					@unlink($file['tmp_name']);
				}
			break;
			case 'BZ2' :
				if (extension_loaded('bz2')){
					$obj = new AttachFile($page, $file['name'] . '.bz2');
					if ($obj->exist)
						return array('result'=>FALSE,
							'msg'=>$_attach_messages['err_exists']);

					$tp = fopen($file['tmp_name'],'rb') or
						die_message($_attach_messages['err_load_file']);
					$zp = bzopen($obj->filename, 'wb') or
						die_message($_attach_messages['err_write_tgz']);

					while (!feof($tp)) { bzwrite($zp,fread($tp, 8192)); }
					bzclose($zp);
					fclose($tp);
					chmod($obj->filename, PLUGIN_ATTACH_FILE_MODE);
					@unlink($file['tmp_name']);
				}
			break;
			default:
//miko
				$obj = new AttachFile($page, $file['name']);
				if ($obj->exist)
					return array('result'=>FALSE,
						'msg'=>$_attach_messages['err_exists']);

				if (move_uploaded_file($file['tmp_name'], $obj->filename))
					chmod($obj->filename, PLUGIN_ATTACH_FILE_MODE);
			break;
		}
	}else{
		$obj = new AttachFile($page, $file['name']);
			if (isset($obj->exist) )
				return array('result'=>FALSE,
					'msg'=>$_attach_messages['err_exists']);

			if (move_uploaded_file($file['tmp_name'], $obj->filename))
				chmod($obj->filename, PLUGIN_ATTACH_FILE_MODE);
	}

	if (is_page($page))
		pkwk_touch_file(get_filename($page));

	$obj->getstatus();
	$obj->status['pass'] = ($pass !== TRUE && $pass !== NULL) ? md5($pass) : '';
	$obj->putstatus();

	if ($notify) {
		$notify_exec = TRUE;
		foreach ($notify_exclude as $exclude) {
			$exclude = preg_quote($exclude);
			if (substr($exclude, -1) == '.')
				$exclude = $exclude . '*';
			if (preg_match('/^' . $exclude . '/', get_remoteip())) {
				$notify_exec = FALSE;
				break;
			}
		}
	} else {
		$notify_exec = FALSE;
	}

	if ($notify_exec !== FALSE) {
		$footer['ACTION']   = 'File attached';
		$footer['FILENAME'] = $file['name'];
		$footer['FILESIZE'] = $file['size'];
		$footer['PAGE']     = $page;
		$footer['URI'] = get_cmd_uri('attach','',array('refer'=>$page,'pcmd'=>'info','file'=>$file['name']));
		$footer['USER_AGENT']  = TRUE;
		$footer['REMOTE_ADDR'] = TRUE;

		pkwk_mail_notify($notify_subject, "\n", $footer);
	}

	return array(
		'result'=>TRUE,
		'msg'=>$_attach_messages['msg_uploaded']);
}

// ファイルタイプによる圧縮添付の判定
function attach_is_compress($type,$compress=1)
{
	if (empty($type)) return $compress;
	list($discrete,$composite_tmp) = explode('/', strtolower($type));
	if (strstr($type,';') === false) {
		$composite = $composite_tmp;
		$parameter = '';
	} else {
		list($composite,$parameter) = explode(';', $composite_tmp);
		$parameter = trim($parameter);
	}
	unset($composite_tmp);

	// type
	static $composite_type = array(
		'application' => array(
			'msword'			=> 0, // doc
			'vnd.ms-excel'		=> 0, // xls
			'vnd.ms-powerpoint'	=> 0, // ppt
			'vnd.visio'			=> 0,
			'octet-stream'		=> 0, // bin dms lha lzh exe class so dll img iso
			'x-bcpio'			=> 0, // bcpio
			'x-bittorrent'		=> 0, // torrent
			'x-bzip2'			=> 0, // bz2
			'x-compress'		=> 0,
			'x-cpio'			=> 0, // cpio
			'x-dvi'				=> 0, // dvi
			'x-gtar'			=> 0, // gtar
			'x-gzip'			=> 0, // gz tgz
			'x-rpm'				=> 0, // rpm
			'x-shockwave-flash'	=> 0, // swf
			'zip'				=> 0, // zip
			'x-7z-compressed'	=> 0, // 7zip
			'x-lzh-compressed'	=> 0, // LZH
			'x-rar-compressed'	=> 0, // RAR
			'x-java-archive'	=> 0, // jar
			'x-javascript'		=> 1, // js
			'ogg'				=> 0, // ogg
			'pdf'				=> 0, // pdf
		),
	);
	if (isset($composite_type[$discrete][$composite])) {
		return $composite_type[$discrete][$composite];
	}

	// discrete-type
	static $discrete_type = array(
		'text'			=> 1,
		'image'			=> 0,
		'audio'			=> 0,
		'video'			=> 0,
	);
	return isset($discrete_type[$discrete]) ? $discrete_type[$discrete] : $compress;
}

// 詳細フォームを表示
function attach_info($err = '')
{
	global $vars, $_attach_messages;

	foreach (array('refer', 'file', 'age') as $var)
		${$var} = isset($vars[$var]) ? $vars[$var] : '';

	$obj = new AttachFile($refer, $file, $age);
	return $obj->getstatus() ?
		$obj->info($err) :
		array('msg'=>$_attach_messages['err_notfound']);
}

// 削除
function attach_delete()
{
	global $vars, $_attach_messages;

	foreach (array('refer', 'file', 'age', 'pass') as $var)
		${$var} = isset($vars[$var]) ? $vars[$var] : '';

	if (is_freeze($refer) || ! is_editable($refer))
		return array('msg'=>$_attach_messages['err_noparm']);

	$obj = new AttachFile($refer, $file, $age);
	if (! $obj->getstatus())
		return array('msg'=>$_attach_messages['err_notfound']);

	if (PLUGIN_ATTACH_USE_CACHE){
		global $cache;
		$cache['wiki']->removeItem(PLUGIN_ATTACH_CACHE_PREFIX.md5($refer));
	}

	return $obj->delete($pass);
}

// 凍結
function attach_freeze($freeze)
{
	global $vars, $_attach_messages;

	foreach (array('refer', 'file', 'age', 'pass') as $var) {
		${$var} = isset($vars[$var]) ? $vars[$var] : '';
	}

	if (is_freeze($refer) || ! is_editable($refer)) {
		return array('msg'=>$_attach_messages['err_noparm']);
	} else {
		$obj = new AttachFile($refer, $file, $age);
		return $obj->getstatus() ?
			$obj->freeze($freeze, $pass) :
			array('msg'=>$_attach_messages['err_notfound']);
	}
}

// リネーム
function attach_rename()
{
	global $vars, $_attach_messages;

	foreach (array('refer', 'file', 'age', 'pass', 'newname') as $var) {
		${$var} = isset($vars[$var]) ? $vars[$var] : '';
	}

	if (is_freeze($refer) || ! is_editable($refer)) {
		return array('msg'=>$_attach_messages['err_noparm']);
	}
	$obj = new AttachFile($refer, $file, $age);
	if (! $obj->getstatus())
		return array('msg'=>$_attach_messages['err_notfound']);

	cache_timestamp_touch('attach');
	return $obj->rename($pass, $newname);
}

// ダウンロード
function attach_open()
{
	global $vars, $_attach_messages;

	foreach (array('refer', 'file', 'age') as $var) {
		${$var} = isset($vars[$var]) ? $vars[$var] : '';
	}

	$obj = new AttachFile($refer, $file, $age);
	return $obj->getstatus() ?
		$obj->open() :
		array('msg'=>$_attach_messages['err_notfound']);
}

// 一覧取得
function attach_list()
{
	global $vars, $_attach_messages, $_string;

	if (Auth::check_role('safemode')) die_message( $_string['prohibit'] );

	$refer = isset($vars['refer']) ? $vars['refer'] : '';
	$obj = new AttachPages($refer);

	if ($refer == ''){
		$msg = $_attach_messages['msg_listall'];
		$body = (isset($obj->pages)) ?
			$obj->toString($refer, FALSE) :
			$_attach_messages['err_noexist'];
	}else{
		$msg = str_replace('$1', htmlsc($refer), $_attach_messages['msg_listpage']);
		$body = (isset($obj->pages[$refer])) ?
			$obj->toRender($refer, FALSE) :
			$_attach_messages['err_noexist'];
	}

	return array('msg'=>$msg, 'body'=>$body);
}

// アップロードフォームを表示 (action時)
function attach_showform()
{
	global $vars, $_attach_messages, $_string;

	if (Auth::check_role('safemode')) die_message( $_string['prohibit'] );

	$page = isset($vars['page']) ? $vars['page'] : '';
	$isEditable = check_editable($page, true, false);

	$vars['refer'] = $page;

	$html = array();
	if (!IS_AJAX){
		$attach_list = attach_list($page);
		$html[] = '<p><small>[<a href="' . get_cmd_uri('attach', null, null, array('pcmd'=>'list')) . '">'.$_attach_messages['msg_listall'].'</a>]</small></p>';
		if ($isEditable){
			$html[] = '<h3>' . str_replace('$1', $page, $_attach_messages['msg_upload']) . '</h3>'. "\n";
			$html[] = attach_form($page);
		}
		$html[] = '<h3>' . str_replace('$1', $page, $_attach_messages['msg_listpage']) . '</h3>'. "\n";
		$html[] = $attach_list['body'];
	}else{
		$html[] = '<div class="tabs" role="application">';
		$html[] = '<ul role="tablist">';
		if ($isEditable){
			$html[] = '<li role="tab"><a href="' .get_cmd_uri('attach', null, null, array('pcmd'=>'form', 'refer'=>$page)) . '">' . str_replace('$1', $_attach_messages['msg_thispage'], $_attach_messages['msg_upload']) . '</a></li>';
		}
		$html[] = '<li role="tab"><a href="' .get_cmd_uri('attach', null, null, array('pcmd'=>'list', 'refer'=>$page)) . '">' . str_replace('$1', $_attach_messages['msg_thispage'], $_attach_messages['msg_listpage']) . '</a></li>';
		$html[] = '</ul>';
		$html[] = '</div>';
	}

	return array(
		'msg'=>$_attach_messages['msg_upload'],
		'body'=>join("\n",$html)
	);
}

//-------- サービス
// mime-typeの決定
function attach_mime_content_type($filename)
{
	$type = 'application/octet-stream'; // default

	if (! file_exists($filename)) return $type;

	$size = @getimagesize($filename);
	if (is_array($size)) {
		switch ($size[2]) {
			case 1: return 'image/gif';
			case 2: return 'image/jpeg';
			case 3: return 'image/png';
			case 4: return 'application/x-shockwave-flash';
		}
	}

	$matches = array();
	if (! preg_match('/_((?:[0-9A-F]{2})+)(?:\.\d+)?$/', $filename, $matches))
		return $type;

	$filename = decode($matches[1]);

	// mime-type一覧表を取得
	$config = new Config(PLUGIN_ATTACH_CONFIG_PAGE_MIME);
	$table = $config->read() ? $config->get('mime-type') : array();
	unset($config); // メモリ節約

	foreach ($table as $row) {
		$_type = trim($row[0]);
		$exts = preg_split('/\s+|,/', trim($row[1]), -1, PREG_SPLIT_NO_EMPTY);
		foreach ($exts as $ext) {
			if (preg_match("/\.$ext$/i", $filename)) return $_type;
		}
	}

	return $type;
}

// アップロードフォームの出力
function attach_form($page)
{
	global $_attach_messages;

	if (! ini_get('file_uploads'))	return '#attach(): <code>file_uploads</code> disabled.<br />';
	if (! is_page($page))			return '#attach(): No such page<br />';

	$attach_form[] = '<form enctype="multipart/form-data" action="' . get_script_uri() . '" method="post" class="attach_form">';
	$attach_form[] = '<input type="hidden" name="cmd" value="attach" />';
	$attach_form[] = '<input type="hidden" name="pcmd" value="post" />';
	$attach_form[] = '<input type="hidden" name="refer" value="'. htmlsc($page) .'" />';
	$attach_form[] = '<input type="hidden" name="max_file_size" value="' . PLUGIN_ATTACH_MAX_FILESIZE . '" />';
	$attach_form[] = '<label for="_p_attach_file">' . $_attach_messages['msg_file'] . ':</label>';
	$attach_form[] = '<input type="file" name="attach_file" id="_p_attach_file" />';
	$attach_form[] = ( (PLUGIN_ATTACH_PASSWORD_REQUIRE || PLUGIN_ATTACH_UPLOAD_ADMIN_ONLY) && Auth::check_role('role_adm_contents')) ?
						'<br />' . ($_attach_messages[PLUGIN_ATTACH_UPLOAD_ADMIN_ONLY ? 'msg_adminpass' : 'msg_password']) .
		 					': <input type="password" name="pass" size="8" />' : '';
	$attach_form[] = '<input type="submit" value="' . $_attach_messages['btn_upload'] . '" />';
	$attach_form[] = '<ul class="attach_info"><li>' . sprintf($_attach_messages['msg_maxsize'], '<var>' . number_format(PLUGIN_ATTACH_MAX_FILESIZE / 1024) . '</var>KB') . '</li></ul>';
	$attach_form[] = '</form>';

	return join("\n",$attach_form);
}

//-------- クラス
// ファイル
class AttachFile
{
	var $page, $file, $age, $basename, $filename, $logname;
	var $time = 0;
	var $size = 0;
	var $time_str = '';
	var $size_str = '';
	var $status = array('count'=>array(0), 'age'=>'', 'pass'=>'', 'freeze'=>FALSE);

	function AttachFile($page, $file, $age = 0)
	{
		$this->page = $page;
		$this->file = basepagename($file);
		$this->age  = is_numeric($age) ? $age : 0;

		$this->basename = UPLOAD_DIR . encode($page) . '_' . encode($this->file);
		$this->filename = $this->basename . ($age ? '.' . $age : '');
		$this->logname  = $this->basename . '.log';
		$this->exists   = file_exists($this->filename);
		$this->time     = $this->exists ? filemtime($this->filename) : 0;
		$this->size     = filesize($this->filename);
	}

	function gethash()
	{
		return $this->exists ? md5_file($this->filename) : '';
	}

	// ファイル情報取得
	function getstatus()
	{
		if (! $this->exists) return FALSE;

		// ログファイル取得
		if (file_exists($this->logname)) {
			$data = file($this->logname);
			foreach ($this->status as $key=>$value) {
				$this->status[$key] = chop(array_shift($data));
			}
			$this->status['count'] = explode(',', $this->status['count']);
		}
		$this->time_str = get_date('Y/m/d H:i:s', $this->time);
		$this->size_str = sprintf('%01.1f', round($this->size/1024, 1)) . 'KB';
		$this->type     = attach_mime_content_type($this->filename);

		return TRUE;
	}

	// ステータス保存
	function putstatus()
	{
		$this->status['count'] = join(',', $this->status['count']);
		pkwk_touch_file($this->logname);
		$fp = fopen($this->logname, 'wb') or
			die_message('cannot write ' . $this->logname);
		set_file_buffer($fp, 0);
		flock($fp, LOCK_EX);
		rewind($fp);
		foreach ($this->status as $key=>$value) {
			fwrite($fp, $value . "\n");
		}
		flock($fp, LOCK_UN);
		fclose($fp);
	}

	// 日付の比較関数
	static function datecomp($a, $b) {
		return ($a->time == $b->time) ? 0 : (($a->time > $b->time) ? -1 : 1);
	}

	function toString($showicon, $showinfo)
	{
		global $_attach_messages;

		$this->getstatus();

		$inf = get_cmd_uri('attach','','',array('pcmd'=>'info','refer'=>$this->page,'age'=>$this->age,'file'=>$this->file));
		$open = get_cmd_uri('attach','','',array('pcmd'=>'open','refer'=>$this->page,'age'=>$this->age,'file'=>$this->file));

		$title = $this->time_str . ' ' . $this->size_str;
		$label = htmlsc($this->file);
		if ($this->age) {
			$label .= ' (backup No.' . $this->age . ')';
		}
		$info = $count = '';
		if ($showinfo) {
			$info = '<small>[<a href="' . $inf . '" title="' . str_replace('$1', rawurlencode($this->file), $_attach_messages['msg_info']) . '">'
				. $_attach_messages['btn_info'] . '</a>]</small>' ."\n";
			$count = ($showicon && ! empty($this->status['count'][$this->age])) ?
				'<small>'.sprintf($_attach_messages['msg_count'], '<var>'.$this->status['count'][$this->age].'</var>').'</small>' : '';
		}
		if (IS_MOBILE) {
			return '<a href="'.$open.'" data-role="button" data-inline="true" data-mini="true" data-icon="gear" data-ajax="false">'.$label.'</a>';
		}else{
			return '<a href="'.$open.'" title="'.$title.'"><span class="pkwk-icon icon-download"></span>'.$label.'</a> '.$count.' '.$info;
		}
	}

	// 情報表示
	function info($err)
	{
		global $_attach_messages, $pkwk_dtd, $vars, $_LANG;


		$script = get_script_uri();
		$r_page = rawurlencode($this->page);
		$s_page = htmlsc($this->page);
		$s_file = htmlsc($this->file);
		$s_err = ($err == '') ? '' : '<p style="font-weight:bold">' . $_attach_messages[$err] . '</p>';

		$list_uri    = get_cmd_uri('attach','','',array('pcmd'=>'list','refer'=>$this->page));
		$listall_uri = get_cmd_uri('attach','','',array('pcmd'=>'list'));

		$role_adm_contents = Auth::check_role('role_adm_contents');
		$msg_require = ($role_adm_contents) ? $_attach_messages['msg_require'] : '';

		$msg_rename  = '';
		if ($this->age) {
			$msg_freezed = '';
			$msg_delete  = '<input type="radio" name="pcmd" id="_p_attach_delete" value="delete" />' .
				'<label for="_p_attach_delete">' .  $_attach_messages['msg_delete'] .
				$msg_require . '</label><br />';
			$msg_freeze  = '';
		} else {
			if ($this->status['freeze']) {
				$msg_freezed = "<dd>{$_attach_messages['msg_isfreeze']}</dd>";
				$msg_delete  = '';
				$msg_freeze  = '<input type="radio" name="pcmd" id="_p_attach_unfreeze" value="unfreeze" />' .
					'<label for="_p_attach_unfreeze">' .  $_attach_messages['msg_unfreeze'] .
					$msg_require . '</label><br />';
			} else {
				$msg_freezed = '';
				$msg_delete = '<input type="radio" name="pcmd" id="_p_attach_delete" value="delete" />' .
					'<label for="_p_attach_delete">' . $_attach_messages['msg_delete'];
				if (PLUGIN_ATTACH_DELETE_ADMIN_ONLY || $this->age)
					$msg_delete .= $msg_require;
				$msg_delete .= '</label><br />';
				$msg_freeze  = '<input type="radio" name="pcmd" id="_p_attach_freeze" value="freeze" />' .
					'<label for="_p_attach_freeze">' .  $_attach_messages['msg_freeze'] .
					$msg_require . '</label><br />';
				if (PLUGIN_ATTACH_RENAME_ENABLE) {
					$msg_rename  = '<input type="radio" name="pcmd" id="_p_attach_rename" value="rename" />' .
						'<label for="_p_attach_rename">' .  $_attach_messages['msg_rename'] .
						$msg_require . '</label><br />' .
						'<label for="_p_attach_newname">' . $_attach_messages['msg_newname'] .
						':</label> ' .
						'<input type="text" name="newname" id="_p_attach_newname" size="40" value="' .
						$this->file . '" /><br />';
				}
			}
		}
		$info = $this->toString(TRUE, FALSE);
		$hash = $this->gethash();

		$_attach_setimage = '';
		if (extension_loaded('exif') && extension_loaded('gd')){
			$file = $this->filename;
			if (exif_imagetype($file) === IMAGETYPE_JPEG) {
				$exif = exif_read_data($file);
				$size = getimagesize($file);	// 画像でない場合はfalseを返す
				if ($size !== false){
					if ($size[2] > 0 && $size[2] < 3) {
						if ($size[0] < 200) { $w = $size[0]; $h = $size[1]; }
						else { $w = 200; $h = $size[1] * (200 / ($size[0]!=0?$size[0]:1) ); }
						$_attach_setimage  = ($pkwk_dtd == PKWK_DTD_HTML_5) ? '<figure class="img_margin attach_info_image">' : '<div class="img_margin attach_info_image">';
						$_attach_setimage .= '<img src="'.get_cmd_uri('ref','','',array('page'=>$this->page,'src'=>$this->file));
						$_attach_setimage .= '" width="' . $w .'" height="' . $h . '" />';
						$_attach_setimage .= ($pkwk_dtd == PKWK_DTD_HTML_5) ? '</figure>' : '</div>';
					}
				}
			}
		}

		$msg_auth = '';
		$info_auth = '';
		if ($role_adm_contents !== FALSE) {
			$msg_auth = <<<EOD
	<label for="_p_attach_password">{$_attach_messages['msg_password']}:</label>
	<input type="password" name="pass" id="_p_attach_password" size="8" />
EOD;
			$info_auth = <<<EOD
	<dt>{$_attach_messages['msg_filename']}</dt>
	<dd><var>{$this->filename}</var></dd>
EOD;
		}

		$retval['body'] = $_attach_setimage;
		if (!IS_AJAX) {
			$retval = array('msg'=>sprintf($_attach_messages['msg_info'], htmlsc($this->file)));
			$retval['body'] = <<< EOD
<nav>
	<ul class="attach_navibar">
		<li><a href="$list_uri">{$_attach_messages['msg_list']}</a></li>
		<li><a href="$listall_uri">{$_attach_messages['msg_listall']}</a></li>
	</ul>
</nav>
EOD;
		}else{
			$retval = array('msg'=>sprintf($_attach_messages['btn_info'], htmlsc($this->file)));
			$retval['body'] = <<< EOD
<ul role="tablist">
	<li role="tab" id="tab1" aria-controls="attach_info"><a href="#attach_info">{$_attach_messages['msg_info']}</a></li>
	<li role="tab" id="tab2" aria-controls="attach_form_edit"><a href="#attach_form_edit">{$_LANG['skin']['edit']}</a></li>
</ul>
EOD;
 		}

		$file_info = <<<EOD
<dl>
	$info_auth
	<dt>{$_attach_messages['msg_page']}:</dt>
	<dd><var>$s_page</var></dd>
	<dt>{$_attach_messages['msg_filesize']}:</dt>
	<dd><var>{$this->size_str}</var> (<var>{$this->size}</var> bytes)</dd>
	<dt>Content-type:</dt>
	<dd><var>{$this->type}</var></dd>
	<dt>{$_attach_messages['msg_date']}:</dt>
	<dd><var>{$this->time_str}</var></dd>
	<dt>{$_attach_messages['msg_dlcount']}:</dt>
	<dd><var>{$this->status['count'][$this->age]}</var></dd>
	<dt>{$_attach_messages['msg_md5hash']}:</dt>
	<dd><var>{$hash}</var></dd>
	$msg_freezed
</dl>
EOD;
		$retval['body'] .= '<div id="attach_info" role="tabpanel" aria-labeledby="tab1">' . "\n" . $_attach_setimage . ( ($pkwk_dtd === PKWK_DTD_HTML_5) ?
			'<details>'."\n".'<summary>'.$info.'</summary>'."\n".$file_info."\n".'</details>' :
			'<fieldset>'."\n".'<legend>'.$info.'</legend>'."\n".$file_info."\n". '</fieldset>').'</div>'."\n";

		if (!IS_AJAX){ $retval['body'] .= '<hr style="clear:both" />'; }
		$retval['body'] .= <<< EOD
<div id="attach_form_edit" role="tabpanel" aria-labeledby="tab2">
	$s_err
	<form action="$script" method="post" class="attach_edit_form">
		<input type="hidden" name="cmd" value="attach" />
		<input type="hidden" name="refer" value="$s_page" />
		<input type="hidden" name="file" value="$s_file" />
		<input type="hidden" name="age" value="{$this->age}" />
		$msg_delete
		$msg_freeze
		$msg_rename
		$msg_auth
		<input type="submit" value="{$_attach_messages['btn_submit']}" />
	</form>
</div>
EOD;
		if (IS_AJAX){ $retval['body'] = '<div id="attach_info_tabs" class="tabs">' .$retval['body'].'</div>'; }
		return $retval;
	}

	function delete($pass)
	{
		global $_attach_messages, $notify, $notify_subject;

		if ($this->status['freeze']) return attach_info('msg_isfreeze');

		// if (! pkwk_login($pass)) {
		if (Auth::check_role('role_adm_contents') && ! pkwk_login($pass)) {
			if (PLUGIN_ATTACH_DELETE_ADMIN_ONLY || $this->age) {
				return attach_info('err_adminpass');
			} else if (PLUGIN_ATTACH_PASSWORD_REQUIRE &&
				md5($pass) !== $this->status['pass']) {
				return attach_info('err_password');
			}
		}

		// バックアップ
		if ($this->age ||
			(PLUGIN_ATTACH_DELETE_ADMIN_ONLY && PLUGIN_ATTACH_DELETE_ADMIN_NOBACKUP)) {
			@unlink($this->filename);
		} else {
			do {
				$age = ++$this->status['age'];
			} while (file_exists($this->basename . '.' . $age));

			if (! rename($this->basename,$this->basename . '.' . $age)) {
				// 削除失敗 why?
				return array('msg'=>$_attach_messages['err_delete']);
			}

			$this->status['count'][$age] = $this->status['count'][0];
			$this->status['count'][0] = 0;
			$this->putstatus();
		}

		if (is_page($this->page))
			pkwk_touch_file(get_filename($this->page));

		if ($notify) {
			$footer['ACTION']   = 'File deleted';
			$footer['FILENAME'] = & $this->file;
			$footer['PAGE']     = & $this->page;
			$footer['URI']      = get_page_absuri($this->page);
			$footer['USER_AGENT']  = TRUE;
			$footer['REMOTE_ADDR'] = TRUE;
			pkwk_mail_notify($notify_subject, "\n", $footer) or
				die('pkwk_mail_notify(): Failed');
		}

		return array('msg'=>$_attach_messages['msg_deleted']);
	}

	function rename($pass, $newname)
	{
		global $_attach_messages, $notify, $notify_subject;

		if ($this->status['freeze']) return attach_info('msg_isfreeze');

		if (Auth::check_role('role_adm_contents') && ! pkwk_login($pass))
			return attach_info('err_adminpass');

		$newbase = UPLOAD_DIR . encode($this->page) . '_' . encode($newname);
		if (file_exists($newbase)) {
			return array('msg'=>$_attach_messages['err_exists']);
		}
		if (! PLUGIN_ATTACH_RENAME_ENABLE) {
			return array('msg'=>$_attach_messages['err_rename']);
		} else {
			// http://pukiwiki.sourceforge.jp/dev/?BugTrack2%2F345
			if (! rename($this->basename, $newbase)) {
				return array('msg'=>$_attach_messages['err_rename']);
			}
			// リネーム成功
			// バックアップファイル・ログファイルもリネームする
			// エラー処理は省略
			$rename_targets = array();
			if ($dir = opendir(UPLOAD_DIR)) {
				$matches_leaf = array();
				if (preg_match('/(((?:[0-9A-F]{2})+)_((?:[0-9A-F]{2})+))$/', $this->basename, $matches_leaf)) {
					$attachfile_leafname = $matches_leaf[1];
					$attachfile_leafname_pattern = preg_quote($attachfile_leafname, '/');
					$pattern = "/^({$attachfile_leafname_pattern})(\.((\d+)|(log)))$/";

					$matches = array();
					while ($file = readdir($dir)) {
						if (! preg_match($pattern, $file, $matches))
							continue;
						$basename2 = $matches[0];
						$newbase2 = $newbase . $matches[2];
						$rename_targets[$basename2] = $newbase2;
					}
				}
				closedir($dir);
			}
			foreach ($rename_targets as $basename2=>$newbase2) {
				$basename2path = UPLOAD_DIR . $basename2;
				// echo "rename '$basename2path' to '$newbase2'<br />\n";
				rename($basename2path, $newbase2);
			}
		}
		return array('msg'=>$_attach_messages['msg_renamed']);
	}

	function freeze($freeze, $pass)
	{
		global $_attach_messages;

		// if (! pkwk_login($pass))
		if (Auth::check_role('role_adm_contents') && ! Auth::login($pass))
			return attach_info('err_adminpass');

		$this->getstatus();
		$this->status['freeze'] = $freeze;
		$this->putstatus();

		return array('msg'=>$_attach_messages[$freeze ? 'msg_freezed' : 'msg_unfreezed']);
	}

	function open()
	{
		global $use_sendfile_header;
		$this->getstatus();
		$this->status['count'][$this->age]++;
		$this->putstatus();
		$filename = $this->file;

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

		ini_set('default_charset', '');
		mb_http_output('pass');

		$file = realpath($this->filename);
		pkwk_common_headers(filemtime($file), null, false);

		if ($use_sendfile_header === true){
			// for reduce server load
			header('X-Sendfile: '.$file);
		}

		$s_filename = htmlsc($filename);
		if ($this->type == 'text/html' || $this->type == 'application/octet-stream') {
			header('Content-Disposition: attachment; filename="' . $s_filename . '"');
			header('Content-Type: application/octet-stream; name="' . $s_filename . '"');
		} else {
			header('Content-Disposition: inline; filename="' . $s_filename . '"');
			header('Content-Type: '   . $this->type);
		}
		// For BugTrack2/102
		// @readfile($this->filename);
		plus_readfile($this->filename);
		log_put_download($this->page,$this->file);
		pkwk_common_suffixes($this->size);
		exit;
	}
}

// ファイルコンテナ
class AttachFiles
{
	var $page;
	var $files = array();

	function AttachFiles($page)
	{
		$this->page = $page;
	}

	function add($file, $age)
	{
		$this->files[$file][$age] = new AttachFile($this->page, $file, $age);
	}

	// ファイル一覧を取得
	function toString($flat = null,$tag = '')
	{
		global $_title;

		if (! check_readable($this->page, FALSE, FALSE)) {
			return str_replace('$1', make_pagelink($this->page), $_title['cannotread']);
		} else if ($tag == 'dl'){
			return $this->to_ddtag();
		} else if ($flat) {
			return $this->to_flat();
		}

		$ret = '';
		$files = array_keys($this->files);
		sort($files, SORT_STRING);

		foreach ($files as $file) {
			$_files = array();
			foreach (array_keys($this->files[$file]) as $age) {
				$_files[$age] = $this->files[$file][$age]->toString(FALSE, TRUE);
			}
			if (! isset($_files[0])) {
				$_files[0] = htmlsc($file);
			}
			ksort($_files, SORT_NUMERIC);
			$_file = $_files[0];
			unset($_files[0]);
			$ret .= " <li>$_file\n";
			if (count($_files)) {
				$ret .= "<ul>\n<li>" . join("</li>\n<li>", $_files) . "</li>\n</ul>\n";
			}
			$ret .= " </li>\n";
		}
		return make_pagelink($this->page) . "\n<ul>\n$ret</ul>\n";
	}

	// ファイル一覧を取得(inline)
	function to_flat()
	{
		$ret = '';
		$files = array();
		foreach (array_keys($this->files) as $file) {
			if (isset($this->files[$file][0])) {
				$files[$file] = & $this->files[$file][0];
			}
		}
		uasort($files, array('AttachFile', 'datecomp'));
		foreach (array_keys($files) as $file) {
			$ret .= $files[$file]->toString(TRUE, TRUE) . ' ';
		}

		return $ret;
	}

	// dlタグで一覧
	function to_ddtag()
	{
		$ret = '';
		$files = array();
		foreach (array_keys($this->files) as $file) {
			if (isset($this->files[$file][0])) {
				$files[$file] = & $this->files[$file][0];
			}
		}
		uasort($files, array('AttachFile', 'datecomp'));
		foreach (array_keys($files) as $file) {
			$ret .= '<dd>'.str_replace("\n",'',$files[$file]->toString(TRUE, TRUE)) . '</dd>'."\n";
		}

		return $ret;
	}

	// ファイル一覧をテーブルで取得
	function toRender($flat)
	{
		global $_attach_messages;
		global $_title;

		if (! check_readable($this->page, FALSE, FALSE)) {
			return str_replace('$1', make_pagelink($this->page), $_title['cannotread']);
		} else if ($flat) {
			return $this->to_flat();
		}

		$ret = '';
		$files = array_keys($this->files);
		sort($files, SORT_STRING);

		foreach ($files as $file) {
			$_files = array();
			foreach (array_keys($this->files[$file]) as $age) {
				$_files[$age] = $this->files[$file][$age]->toString(FALSE, TRUE);
			}
			if (! isset($_files[0])) {
				$_files[0] = htmlsc($file);
			}
			//pr($this->files[$file]);
			ksort($_files, SORT_NUMERIC);
			$_file = $_files[0];
			unset($_files[0]);
			$fileinfo = $this->files[$file];
			if (isset( $fileinfo[0])){
				$ret .= join('',array(
					'<tr><td class="style_td">' . $_file . '</td>',
					'<td class="style_td">' . $fileinfo[0]->size_str . '</td>',
					'<td class="style_td">' . $fileinfo[0]->type . '</td>',
					'<td class="style_td">' . $fileinfo[0]->time_str . '</td></tr>'
				))."\n";
			}
			// else{ ... } // delated FIX me!
		}
		return '<table class="style_table attach_table" data-pagenate="true"><thead>' . "\n" .
		       '<tr><th class="style_th">' . $_attach_messages['msg_file'] . '</th>' .
		       '<th class="style_th">' . $_attach_messages['msg_filesize'] . '</th>' .
		       '<th class="style_th">' . $_attach_messages['msg_type'] . '</th>' .
		       '<th class="style_th">' . $_attach_messages['msg_date'] . '</th></tr></thead>'."\n".
		       '<tbody>' . "\n$ret</tbody></table>\n";
	}
}

// ページコンテナ
class AttachPages
{
	var $pages = array();

	function AttachPages($page = '', $age = NULL, $purge = false)
	{
		global $cache;
		$handle = opendir(UPLOAD_DIR) or
			die('directory ' . UPLOAD_DIR . ' is not exist or not readable.');

		if ($purge)
			$cache['wiki']->clearByPrefix(PLUGIN_ATTACH_CACHE_PREFIX);

		$cache_name = (PLUGIN_ATTACH_USE_CACHE && $page !== '') ? PLUGIN_ATTACH_CACHE_PREFIX.md5($page) : null;

		if ($page !== '' && isset($cache_name) && $cache['wiki']->hasItem($cache_name) ){
			$this->pages[$page] = (object)$cache['wiki']->getItem($cache_name);
		}else{
			$page_pattern = ($page == '') ? '(?:[0-9A-F]{2})+' : preg_quote(encode($page), '/');
			$age_pattern = ($age === NULL) ?
				'(?:\.([0-9]+))?' : ($age ?  "\.($age)" : '');
			$pattern = "/^({$page_pattern})_((?:[0-9A-F]{2})+){$age_pattern}$/";

			$matches = array();
			$_page2 = '';
			while (($file = readdir($handle)) !== FALSE) {
				if (! preg_match($pattern, $file, $matches)) continue;
				$_page = decode($matches[1]);
				if (! check_readable($_page, FALSE, FALSE)) continue;

				if (PLUGIN_ATTACH_USE_CACHE){
					$_cache_name = PLUGIN_ATTACH_CACHE_PREFIX.md5($_page);
					if ( $cache['wiki']->hasItem($_cache_name) ){
						$this->pages[$_page] = $cache['wiki']->getItem($_cache_name);
						continue;
					}
				}

				$_file = decode($matches[2]);
				$_age  = isset($matches[3]) ? $matches[3] : 0;
				if (! isset($this->pages[$_page])) {
					$this->pages[$_page] = new AttachFiles($_page);
				}
				$this->pages[$_page]->add($_file, $_age);
				if (PLUGIN_ATTACH_USE_CACHE){
					$_page2 = $_page;
				}
			}
			closedir($handle);

			// ページごとの添付ファイル情報をキャッシュ
			if (PLUGIN_ATTACH_USE_CACHE){
				if ($page !== '' && isset($this->pages[$page])){
					$cache['wiki']->setItem(PLUGIN_ATTACH_CACHE_PREFIX.md5($page), $this->pages[$page]);
				}else{
					foreach ($this->pages as $line){
						$md5 = PLUGIN_ATTACH_CACHE_PREFIX.md5($line->page);
						if (! $cache['wiki']->hasItem($md5)){
							$cache['wiki']->setItem($md5, $this->pages[$line->page]);
						}
					}
				}
			}
		}
	}

	function toString($page = '', $flat = FALSE, $tag = '')
	{
		if ($page !== '') {
			return (! isset($this->pages[$page])) ? '' : $this->pages[$page]->toString($flat,$tag);
		}
		$ret = '';

		$pages = array_keys($this->pages);
		sort($pages, SORT_STRING);

		foreach ($pages as $page) {
			if (check_non_list($page)) continue;
			$ret .= '<li>' . $this->pages[$page]->toString($flat) . '</li>' . "\n";
		}
		return "\n" . '<ul>' . "\n" . $ret . '</ul>' . "\n";
	}

	function toRender($page = '', $pattern = 0)
	{
		if ($page != '') {
			if (! isset($this->pages[$page])) {
				return '';
			} else {
				return $this->pages[$page]->toRender($pattern);
			}
		}
		$ret = '';

		$pages = array_keys($this->pages);
		sort($pages, SORT_STRING);

		foreach ($pages as $page) {
			if (check_non_list($page)) continue;
			$ret .= '<tr><td>' . $this->pages[$page]->toRender($pattern) . '</td></tr>' . "\n";
		}
		return "\n" . '<table>' . "\n" . $ret . '</table>' . "\n";
	}
}
/* End of file attach.inc.php */
/* Location: ./wiki-common/plugin/attach.inc.php */