<?php
// PukPukiPlus.
// $Id: attach.inc.php,v 1.92.51 2014/01/25 8:38:00 Logue Exp $
// Copyright (C)
//   2010-2014 PukiWiki Advance Developers Team <http://pukiwiki.logue.be/>
//   2005-2009 PukiWiki Plus! Team
//   2003-2007,2009,2011 PukiWiki Developers Team
//   2002-2003 PANDA <panda@arino.jp> http://home.arino.jp/
//   2002      Y.MASUI <masui@hisec.co.jp> http://masui.net/pukiwiki/
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//

use PukiWiki\Attach;
use PukiWiki\Auth\Auth;
use PukiWiki\Factory;
use PukiWiki\File\AttachFile;
use PukiWiki\File\FileFactory;
use PukiWiki\Listing;
use PukiWiki\Renderer\Header;
use PukiWiki\Renderer\PluginRenderer;
use PukiWiki\Router;
use PukiWiki\Spam\Spam;
use PukiWiki\Utility;
use Zend\Json\Json;

// NOTE (PHP > 4.2.3):
//    This feature is disabled at newer version of PHP.
//    Set this at php.ini if you want.
// Max file size for upload on PHP (PHP default: 2MB)


defined('PLUGIN_ATTACH_ILLEGAL_CHARS_PATTERN')	or define('PLUGIN_ATTACH_ILLEGAL_CHARS_PATTERN', '/[%|=|&|?|#|\r|\n|\0|\@|\t|;|\$|+|\\|\[|\]|\||^|{|}]/');		// default: 4MB

defined('PLUGIN_ATTACH_UPLOAD_MAX_FILESIZE')	or define('PLUGIN_ATTACH_UPLOAD_MAX_FILESIZE', '16M');		// default: 16MB
ini_set('upload_max_filesize', PLUGIN_ATTACH_UPLOAD_MAX_FILESIZE);

// Max file size for upload on script of PukiWikiX_FILESIZE
defined('PLUGIN_ATTACH_MAX_FILESIZE')		or define('PLUGIN_ATTACH_MAX_FILESIZE', 16777216);		// default: 16MB

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

// 添付ファイルフォームの名前
defined('PLUGIN_ATTACH_FILE_FIELD_NAME') or define('PLUGIN_ATTACH_FILE_FIELD_NAME', 'attach_file');

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
			'msg_multiple'	=> T_('File may be specified any number of times.'),
			'msg_count'		=> T_('%s download'),
			'msg_password'	=> T_('password'),
			'msg_adminpass'	=> T_('Administrator password'),
			'msg_delete'	=> T_('Delete file.'),
			'msg_freeze'	=> T_('Freeze file.'),
			'msg_unfreeze'	=> T_('Unfreeze file.'),
			'msg_renamed'	=> T_('The file has been renamed'),
			'msg_isfreeze'	=> T_('File is frozen.'),
			'msg_notfreeze'	=> T_('File is not frozen.'),
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
			'msg_uploaded'  => T_('The file was updaded.'),
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
	
	if (empty($page)) return '<div class="alert alert-warning">#attach(): No page defined.</div>';

	$ret = '';
	if (! $nolist) {
//		$obj  = new Attach($page);
//		$ret .= $obj->toString($page, TRUE);
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

	if (!empty($page)){
		$wiki = Factory::Wiki($page);

		if ($wiki->isValied()) {
			// メソッドによってパーミッションを分ける
			if(in_array($pcmd, array('info', 'open', 'list'))) {
				// 読み込み許可
				$wiki->checkReadable();
			} else {
				// 書き込み許可があるか
				$wiki->checkEditable();
			}
		}
	}
	

	if(in_array($pcmd, array('delete', 'freeze', 'unfreeze'))) {
		if (Auth::check_role('readonly')) Utility::dieMessage( $_string['error_prohibit'] );
	}

	switch ($pcmd) {
		case 'info'     : return attach_info();
		case 'delete'   : return attach_delete();
		case 'open'     : return attach_open();
		case 'list'     : return attach_list($page);
		case 'freeze'   : return attach_freeze(TRUE);
		case 'unfreeze' : return attach_freeze(FALSE);
		case 'rename'   : return attach_rename();
		default:
		case 'upload'   : return attach_showform();
		case 'form'     : return array('msg'  =>str_replace('$1', $refer, $_attach_messages['msg_upload']), 'body'=>attach_form($refer));
		case 'post'     : return attach_upload($page, $pass);
		case 'progress' : return PluginRenderer::getUploadProgress();
	}
	return (empty($page) || ! $wiki->isValied()) ? attach_list() : attach_showform();
}

//-------- 実体
// ファイルアップロード
// $pass = NULL : パスワードが指定されていない
// $pass = TRUE : アップロード許可
function attach_upload($page, $pass = NULL)
{
	global $_attach_messages, $_string;
	
	// if (PKWK_READONLY) die_message('PKWK_READONLY prohibits editing');
	if (Auth::check_role('readonly')) Utility::dieMessage($_string['error_prohibit']);

	$msgs = array();

	if (empty($page)){
		// 添付先のページが空
		return array(
			'result' => FALSE,
			'msg' => '#attach: page name is missing.'
		);
	}
	
	$wiki = Factory::Wiki($page);

	if (! $wiki->isValied()) {
		return array(
			'result' => FALSE,
			'msg' => $_attach_messages['err_nopage']
		);
	}

	if ($pass !== TRUE) {
		if (! $wiki->isEditable()){
			return array(
				'result'=>FALSE,
				'msg'=>$_attach_messages['err_noparm']);
		}
	
		if (PLUGIN_ATTACH_UPLOAD_ADMIN_ONLY && Auth::check_role('role_contents_admin') &&  ($pass === NULL || ! pkwk_login($pass))) {
			return array(
				'result'=>FALSE,
				'msg'=>$_attach_messages['err_adminpass']);
		}
	}

	foreach ($_FILES[PLUGIN_ATTACH_FILE_FIELD_NAME]['name'] as $key => $value) {
		$file = $_FILES[PLUGIN_ATTACH_FILE_FIELD_NAME]['name'][$key];

		// 無効な文字が含まれている
		if (preg_match(PLUGIN_ATTACH_ILLEGAL_CHARS_PATTERN, $file)){
			$msgs[$file] = $_string['illegal_chars'];
			continue;
		}

		// 添付ファイルがアップされた時のクエリの長さを取得
		$query = Router::get_cmd_uri('attach', '', '', array(
			'refer'=>$page,
			'pcmd'=>'info',
			'file'=>$file
		));
		// ファイル名が長すぎる
		if (PKWK_QUERY_STRING_MAX && strlen($query) > PKWK_QUERY_STRING_MAX) {
			$msgs[$file] = $_attach_messages['err_too_long'];
			continue;
		}

		// アップロードに失敗
		if ($_FILES[PLUGIN_ATTACH_FILE_FIELD_NAME]['error'][$key] !== UPLOAD_ERR_OK) {
			$msgs[$file] = attach_set_error_message($_FILES[PLUGIN_ATTACH_FILE_FIELD_NAME]['error'][$key]);
			continue;
		}

		// 一時ファイルの生成に失敗
		if (empty($_FILES[PLUGIN_ATTACH_FILE_FIELD_NAME]['tmp_name'][$key]) || ! is_uploaded_file($_FILES[PLUGIN_ATTACH_FILE_FIELD_NAME]['tmp_name'][$key])) {
			$msgs[$file] = $_attach_messages['err_upload'];
			continue;
		}

		// サイズが大きすぎる
		if ($_FILES[PLUGIN_ATTACH_FILE_FIELD_NAME]['size'][$key] > PLUGIN_ATTACH_MAX_FILESIZE) {
			$msgs[$file] = $_attach_messages['err_exceed'];
			continue;
		}

		$ret = attach_doupload($file, $page, $pass, $_FILES[PLUGIN_ATTACH_FILE_FIELD_NAME]['tmp_name'][$key]);
		$msgs[$file] = $ret['msg'];
	}
	$body[] = '<ul>';
	
	foreach ($msgs as $file=>$_result){
		$body[] = '<li>'.$file.': '.$_result.'</li>';
	}
	$body[] = '</ul>';
	
	return array('msg'=> sprintf($_attach_messages['msg_uploaded'], $page), 'body'=>'<ul>'.join("\n", $body).'</ul>', 'result'=>true);
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

function attach_doupload($file, $page, $pass=NULL, $temp)
{
	global $_attach_messages, $_string;
	global $notify, $notify_subject, $notify_exclude, $spam;

	$filename = Utility::encode($page).'_'.Utility::encode($file);
	$type = Utility::getMimeInfo($temp);
	$must_compress = (PLUGIN_ATTACH_UNKNOWN_COMPRESS !== 0) ? attach_is_compress($type,PLUGIN_ATTACH_UNKNOWN_COMPRESS) : false;	// 不明なファイルを圧縮するか？
	// ファイル名の長さをチェック
	$filename_length = strlen($filename);
	if ( $filename_length  >= 255 || ($must_compress && $filename_length >= 251 )){
		return array(
			'result'=>FALSE,
			'msg'=>$_attach_messages['err_filename']
		);
	}

	// スパムチェック
	if ($spam !== 0) {
		// ファイルの内容でスパムチェック
		// if attach spam, filtering attach file.
		$vars['uploadname'] = $file['name'];
		$vars['uploadtext'] = attach_gettext($file['tmp_name']);
		if ($vars['uploadtext'] === '' || $vars['uploadtext'] === FALSE) return FALSE;

		if (isset($spam['method']['attach'])) {
			$_method = & $spam['method']['attach'];
		} else if (isset($spam['method']['_default'])) {
			$_method = & $spam['method']['_default'];
		} else {
			$_method = array();
		}
		$exitmode = isset($spam['exitmode']) ? $spam['exitmode'] : '';
		Spam::pkwk_spamfilter('File Attach', $page, $vars, $_method, $exitmode);
	}

	if ($must_compress) {
		// 添付ファイルを圧縮する

		switch (PLUGIN_ATTACH_COMPRESS_TYPE){
			case 'GZ' :
				if (!extension_loaded('zlib')) Utility::dieMessage('#attach: zlib extention has not loaded.');
				$obj = new AttachFile($page, $file . '.gz');
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
				break;
			case 'ZIP' :
				if (!class_exists('ZipArchive')) Utility::dieMessage('#attach: ZipArchive class has not defined.');
				$obj = new AttachFile($page, $file . '.zip');
				if ($obj->exist)
					return array('result'=>FALSE,
						'msg'=>$_attach_messages['err_exists']);
				$zip = new ZipArchive();

				$zip->addFile($temp,$file);
				// if ($zip->status !== ZIPARCHIVE::ER_OK)
				if ($zip->status !== 0)
					die_message( $_attach_messages['err_upload'].'('.$zip->status.').' );
				$zip->close();
				chmod($obj->filename, PLUGIN_ATTACH_FILE_MODE);
				break;
			case 'BZ2' :
				if (!extension_loaded('bz2')) Utility::dieMessage('#attach: bz2 extention has not loaded.');
				$obj = new AttachFile($page, $file . '.bz2');
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
				break;
			default:
				//miko
				$obj = new AttachFile($page, $file);
				if ($obj->exist)
					return array('result'=>FALSE,
						'msg'=>$_attach_messages['err_exists']);

				if (move_uploaded_file($temp, $obj->filename))
					chmod($obj->filename, PLUGIN_ATTACH_FILE_MODE);
				break;
		}
	}else{
		// 通常添付
		$obj = new AttachFile($page, $file);
		if (isset($obj->exist) )
			return array('result'=>FALSE,
				'msg'=>$_attach_messages['err_exists']);

		if (move_uploaded_file($temp, $obj->filename))
			chmod($obj->filename, PLUGIN_ATTACH_FILE_MODE);
	}
	
	if (file_exists($temp)){
		unlink($temp);
	}

	// ページのタイムスタンプを更新
	Factory::Wiki($page)->touch();

	$obj->status['pass'] = ($pass !== TRUE && $pass !== NULL) ? md5($pass) : '';

	if ($notify) {
		$notify_exec = TRUE;
		foreach ($notify_exclude as $exclude) {
			$exclude = preg_quote($exclude);
			if (substr($exclude, -1) == '.')
				$exclude .= '*';
			if (preg_match('/^' . $exclude . '/', get_remoteip())) {
				$notify_exec = FALSE;
				break;
			}
		}
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
		'msg'=>$_attach_messages['msg_uploaded']
	);
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
			'msword'                => 0, // doc
			'vnd.ms-excel'          => 0, // xls
			'vnd.ms-powerpoint'     => 0, // ppt
			'vnd.visio'             => 0,
			'octet-stream'          => 0, // bin dms lha lzh exe class so dll img iso
			'x-bcpio'               => 0, // bcpio
			'x-bittorrent'          => 0, // torrent
			'x-bzip2'               => 0, // bz2
			'x-compress'            => 0,
			'x-cpio'                => 0, // cpio
			'x-dvi'                 => 0, // dvi
			'x-gtar'                => 0, // gtar
			'x-gzip'                => 0, // gz tgz
			'x-rpm'                 => 0, // rpm
			'x-shockwave-flash'     => 0, // swf
			'zip'                   => 0, // zip
			'x-java-archive'        => 0, // jar
			'x-javascript'          => 1, // js
			'ogg'                   => 0, // ogg
			'pdf'                   => 0, // pdf
		),
	);
	if (isset($composite_type[$discrete][$composite])) {
		return $composite_type[$discrete][$composite];
	}

	// discrete-type
	static $discrete_type = array(
		'text'                          => 1,
		'image'                         => 0,
		'audio'                         => 0,
		'video'                         => 0,
	);
	if (isset($discrete_type[$discrete])) {
		return $discrete_type[$discrete];
	}

	return $compress;
}

// 詳細フォームを表示
function attach_info($err = '')
{
	global $vars, $_attach_messages;
	
	foreach (array('refer', 'file', 'pass') as $var){
		${$var} = isset($vars[$var]) ? $vars[$var] : null;
	}

	if (empty($refer)){
		// 呼び出し元のページが空の場合エラー
		return array('msg'=>'Page name is undefined.');
	}

	// ageが空白の時は0とする
	$age = isset($vars['age']) ? $vars['age'] : 0;

	// Wikiオブジェクト
	$wiki = Factory::Wiki($refer);

	if (! $wiki->has()){
		// 呼び出し元のページが削除されている状態で
		// そのページに貼り付けられたファイルを削除しようとエラー。
		// ※オリジナルとは異なる動作になる
		return array('msg'=>'Page is not exsists.');
	}

	if (! $wiki->isValied()) {
		// 無効なページ
		return array('msg' => $_attach_messages['err_nopage']);
	}

	if (! $wiki->isReadable()){
		// ページが凍結されていたり認証がかかっているなどで編集できない場合
		return array('msg'=>'Page is not readable.');
	}

	// Attachオブジェクトを生成
	$obj = new Attach($refer, $file, $age);

	if (! $obj->has()){
		// ファイルが存在しない
		return array('msg'=>sprintf($_attach_messages['err_notfound'], Utility::htmlsc($refer)));
	}
	return $obj->has() ?
		$obj->info($err) :
		array('msg'=>$_attach_messages['err_notfound'], 'http_code' => 404);
}

// 削除
function attach_delete()
{
	global $vars, $_attach_messages;

	foreach (array('refer', 'file', 'pass') as $var){
		${$var} = isset($vars[$var]) ? $vars[$var] : null;
	}

	if (empty($refer)){
		// 呼び出し元のページが空の場合エラー
		return array('msg'=>'Page name is undefined.');
	}

	// ageが空白の時は0とする
	$age = isset($vars['age']) ? $vars['age'] : 0;

	// Wikiオブジェクト
	$wiki = Factory::Wiki($refer);

	if (! $wiki->has()){
		// 呼び出し元のページが削除されている状態で
		// そのページに貼り付けられたファイルを削除しようとエラー。
		// ※オリジナルとは異なる動作になる
		return array('msg'=>'Page is not exsists.');
	}

	if (! $wiki->isValied()) {
		// 無効なページ
		return array('msg' => $_attach_messages['err_nopage']);
	}

	if (! $wiki->isEditable()){
		// ページが凍結されていたり認証がかかっているなどで編集できない場合
		return array('msg'=>'Page is not editable.');
	}

	// Attachオブジェクトを生成
	$obj = new Attach($refer, $file, $age);

	if (! $obj->has()){
		// ファイルが存在しない
		return array('msg'=>sprintf($_attach_messages['err_notfound'], Utility::htmlsc($refer)));
	}

	return $obj->delete($pass) ?
		array('msg'=>sprintf($_attach_messages['msg_deleted'], Utility::htmlsc($refer))) :
		array('msg'=>$_attach_messages['err_delete']);
}

// 凍結
function attach_freeze($freeze)
{
	global $vars, $_attach_messages;

	foreach (array('refer', 'file', 'pass') as $var) {
		${$var} = isset($vars[$var]) ? $vars[$var] : null;
	}

	if (empty($refer)){
		// 呼び出し元のページが空の場合エラー
		return array('msg'=>'Page name is undefined.');
	}

	// ageが空白の時は0とする
	$age = isset($vars['age']) ? $vars['age'] : 0;

	// Wikiオブジェクト
	$wiki = Factory::Wiki($refer);

	if (! $wiki->has()){
		// 呼び出し元のページが削除されている状態で
		// そのページに貼り付けられたファイルを凍結させようとするとエラー。
		// ※オリジナルとは異なる動作になる
		return array('msg'=>'Page is not exsists.');
	}

	if (! $wiki->isValied()) {
		// 無効なページ
		return array('msg' => $_attach_messages['err_nopage']);
	}

	if (! $wiki->isEditable()){
		// ページが凍結されていたり認証がかかっているなどで編集できない場合
		return array('msg'=>'Page is not editable.');
	}

	// Attachオブジェクトを生成
	$obj = new Attach($refer, $file, $age);

	if (! $obj->has()){
		// ファイルが存在しない
		return array('msg'=>sprintf($_attach_messages['err_notfound'], Utility::htmlsc($refer)));
	}
	
	if ($freeze){
		// 凍結させるときのメッセージ
		$msg = $_attach_messages['msg_freezed'];
		$err = $_attach_messages['msg_isfreeze'];
	}else{
		// 凍結解除させるときのメッセージ
		$msg = $_attach_messages['msg_unfreezed'];
		$err = $_attach_messages['msg_notfreeze'];
	}
	
	// ファイル凍結処理
	return $obj->freeze($freeze, $pass) ?
		array('msg'=>$msg) :
		array('msg'=>$err);
}

// リネーム
function attach_rename()
{
	global $vars, $_attach_messages;

	foreach (array('refer', 'file', 'pass', 'newname') as $var) {
		${$var} = isset($vars[$var]) ? $vars[$var] : null;
	}

	if (empty($refer)){
		// 呼び出し元のページが空の場合エラー
		return array('msg'=>'Page name is undefined.');
	}

	// ageが空白の時は0とする
	$age = isset($vars['age']) ? $vars['age'] : 0;

	// Wikiオブジェクト
	$wiki = Factory::Wiki($refer);

	if (! $wiki->has()){
		// 呼び出し元のページが削除されている状態で
		// そのページに貼り付けられたファイルを凍結させようとするとエラー。
		// ※オリジナルとは異なる動作になる
		return array('msg'=>'Page is not exsists.');
	}

	if (! $wiki->isValied()) {
		// 無効なページ
		return array('msg' => $_attach_messages['err_nopage']);
	}

	if (! $wiki->isEditable()){
		// ページが凍結されていたり認証がかかっているなどで編集できない場合
		return array('msg'=>'Page is not editable.');
	}

	$obj = new Attach($refer, $file, $age);
	
	if (! $obj->has()){
		// ファイルが存在しない
		return array('msg'=>sprintf($_attach_messages['err_notfound'], Utility::htmlsc($refer)));
	}

	if ($obj->rename($pass, $newname)){
		// リネームに成功した
		
		// 添付ファイルキャッシュを更新
		cache_timestamp_touch('attach');
		return array('msg'=>$_attach_messages['msg_renamed']);
	}

	// リネームできなかった
	return array('msg'=>$_attach_messages['err_rename']);
}

// ダウンロード
function attach_open()
{
	global $vars, $_attach_messages;

	foreach (array('refer', 'file') as $var) {
		${$var} = isset($vars[$var]) ? $vars[$var] : null;
	}

	$age = isset($vars['age']) ? $vars['age'] : 0;

	$obj = new Attach($vars['refer'], $vars['file'], $age);

	return $obj->has() ?
		$obj->render() :
		array('msg'=>$_attach_messages['err_notfound'],'http_code'=>404);	// 404エラーを出力
}

// 一覧取得
function attach_list()
{
	global $vars, $_attach_messages, $_string;

	if (Auth::check_role('safemode')) Utility::dieMessage( $_string['prohibit'] );

	$page = isset($vars['page']) ? $vars['page'] : null;
	$refer = isset($vars['refer']) ? $vars['refer'] : $page;
	$type = isset($vars['type']) ? $vars['type'] : null;
	if (! empty($refer)) {
		$wiki = Factory::Wiki($refer);
		$wiki->isReadable();
		$attaches = $wiki->attach();
		
		if ($type === 'json'){
			$headers = Header::getHeaders('application/json');
			Header::writeResponse($headers, 200, Json::encode($attaches));
			exit;
		}
		// ページ別添付ファイル一覧
		$msg = str_replace('$1', Utility::htmlsc($refer), $_attach_messages['msg_listpage']);
		if (count($attaches) === 0){
			return array('msg'=>$msg, 'body'=>'No files uploaded.');
		}
		$ret[] = '<table class="table table-borderd plugin-attach-list" data-pagenate="true" data-sortable="true"><thead>';
		$ret[] = '<thead>';
		$ret[] = '<tr>';
		$ret[] = '<th>' . $_attach_messages['msg_file'] . '</th>';
		$ret[] = '<th>' . $_attach_messages['msg_filesize'] . '</th>';
		$ret[] = '<th>' . $_attach_messages['msg_type'] . '</th>';
		$ret[] = '<th>' . $_attach_messages['msg_date'] . '</th>';
		$ret[] = '</tr>';
		$ret[] = '</thead>';
		$ret[] = '<tbody>';
		foreach($attaches as $name=>$files){
			unset($files['log']);
			foreach ($files as $backup=>$file){
				$attach = new Attach($refer, $name, $backup);
				
				$ret[] = '<tr>';
				if ($backup === 0){
					$ret[] = '<td>' . $attach->toString() . '</td>';
				}else{
					$ret[] = '<td>' . $name . '<em>(Backup no.' . $backup . ')</em></td>';
				}
				$ret[] = '<td>' . $attach->getSize() . '</td>';
				$ret[] = '<td>' . $attach->getMime() . '</td>';
				$ret[] = '<td>' . $attach->getDate() . '</td>';
				$ret[] = '</tr>';
			}
		}
		$ret[] = '</tbody>';
		$ret[] = '</table>';

	}else{
		// 全ページ添付ファイル一覧
		$msg = $_attach_messages['msg_listall'];
		$ret[] = Listing::get('attach', 'attach');
	}
	return array('msg'=>$msg, 'body'=>join("\n",$ret));
}

// アップロードフォームを表示 (action時)
function attach_showform()
{
	global $vars, $_attach_messages, $_string;

	if (Auth::check_role('safemode')) die_message( $_string['prohibit'] );

	$page = isset($vars['page']) ? $vars['page'] : null;

	if (empty($page)) Utility::dieMessage('Page name is not defined');
	
	$isEditable = Factory::Wiki($page)->isEditable();

	$vars['refer'] = $page;

	$html = array();
	if (!IS_AJAX){
		$attach_list = attach_list($page);
		$html[] = '<p><small>[<a href="' . Router::get_cmd_uri('attach', null, null, array('pcmd'=>'list')) . '">'.$_attach_messages['msg_listall'].'</a>]</small></p>';
		if ($isEditable){
			$html[] = '<h2>' . str_replace('$1', $page, $_attach_messages['msg_upload']) . '</h2>'. "\n";
			$html[] = attach_form($page);
		}
		$html[] = '<h2>' . str_replace('$1', $page, $_attach_messages['msg_listpage']) . '</h2>'. "\n";
		$html[] = $attach_list['body'];
	}else{
		$html[] = '<div class="tabs" role="application">';
		$html[] = '<ul role="tablist">';
		if ($isEditable){
			$html[] = '<li role="tab"><a href="' .Router::get_cmd_uri('attach', null, null, array('pcmd'=>'form', 'refer'=>$page)) . '">' . str_replace('$1', $_attach_messages['msg_thispage'], $_attach_messages['msg_upload']) . '</a></li>';
		}
		$html[] = '<li role="tab"><a href="' .Router::get_cmd_uri('attach', null, null, array('pcmd'=>'list', 'refer'=>$page)) . '">' . str_replace('$1', $_attach_messages['msg_thispage'], $_attach_messages['msg_listpage']) . '</a></li>';
		$html[] = '</ul>';
		$html[] = '</div>';
	}

	return array(
		'msg'=>$_attach_messages['msg_upload'],
		'body'=>join("\n",$html)
	);
}



// アップロードフォームの出力
function attach_form($page)
{
	global $_attach_messages;

	if (! ini_get('file_uploads'))	return '<p class="alert alert-warning">#attach(): <code>file_uploads</code> disabled.</p>';
	if (! Factory::Wiki($page)->has())			return '<p class="alert alert-warning">#attach(): No such page.</p>';

	$attach_form[] = '<form enctype="multipart/form-data" action="' . Router::get_script_uri() . '" method="post" class="form-inline plugin-attach-form" data-collision-check="false">';
	$attach_form[] = '<input type="hidden" name="cmd" value="attach" />';
	$attach_form[] = '<input type="hidden" name="pcmd" value="post" />';
	$attach_form[] = '<input type="hidden" name="page" value="'. Utility::htmlsc($page) .'" />';
	$attach_form[] = '<input type="hidden" name="MAX_FILE_SIZE" value="' . PLUGIN_ATTACH_MAX_FILESIZE . '" />';
	$attach_form[] = '<div class="form-group">';
	$attach_form[] = '<label for="_p_attach_file" class="sr-only">' . $_attach_messages['msg_file'] . ':</label>';
	$attach_form[] = '<input type="file" name="' . PLUGIN_ATTACH_FILE_FIELD_NAME . '[]" id="_p_attach_file" class="form-control" multiple="multiple" />';
	$attach_form[] = '</div>';
	if ((PLUGIN_ATTACH_PASSWORD_REQUIRE || PLUGIN_ATTACH_UPLOAD_ADMIN_ONLY) && Auth::check_role('role_contents_admin')){
		$attach_form[] = '<div class="form-group">';
		$attach_form[] = '<input type="password" name="pass" size="8" class="form-control" />';
		$attach_form[] = '</div>';
	}
	$attach_form[] = '<button class="btn btn-primary" type="submit"><span class="fa fa-upload"></span>' . $_attach_messages['btn_upload'] . '</button>';
	$attach_form[] = '</form>';
	$attach_form[] = '<ul class="plugin-attach-ul">';
	$attach_form[] = ( (PLUGIN_ATTACH_PASSWORD_REQUIRE || PLUGIN_ATTACH_UPLOAD_ADMIN_ONLY) && Auth::check_role('role_contents_admin')) ?
						('<li>' . $_attach_messages[PLUGIN_ATTACH_UPLOAD_ADMIN_ONLY ? 'msg_adminpass' : 'msg_password'] . '</li>') : '';
	$attach_form[] = '<li>' . sprintf($_attach_messages['msg_maxsize'], '<var>' . number_format(PLUGIN_ATTACH_MAX_FILESIZE / 1024) . '</var>KB') . '</li>';
	$attach_form[] = '<li>'.$_attach_messages['msg_multiple'].'</li>';
	$attach_form[] = '</ul>';

	return join("\n",$attach_form);
}

/* End of file attach.inc.php */
/* Location: ./wiki-common/plugin/attach.inc.php */