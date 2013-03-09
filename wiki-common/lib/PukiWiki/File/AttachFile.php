<?php
namespace PukiWiki\File;

use PukiWiki\Auth\Auth;
use PukiWiki\File\File;
use PukiWiki\Utility;
use SplFileInfo;
use Zend\Http\Response;
use Zend\Http\Headers;
class AttachFile extends SplFileInfo{
	public $dir = UPLOAD_DIR;
	public $pattern = '/^((?:[0-9A-F]{2})+)_((?:[0-9A-F]{2})+)$/';
	
	var $page, $file, $age, $basename, $filename, $logname;
	var $time = 0;
	var $size = 0;
	var $time_str = '';
	var $size_str = '';
	var $status = array('count'=>array(0), 'age'=>'', 'pass'=>'', 'freeze'=>FALSE);

	function __construct($page, $file, $age = 0)
	{
		$this->page = $page;
		$this->file = basepagename($file);
		$this->age  = is_numeric($age) ? $age : 0;

		$this->basename = UPLOAD_DIR . Utility::encode($page) . '_' . Utility::encode($this->file);
		$this->filename = $this->basename . ($age ? '.' . $age : '');
		$this->logname  = $this->basename . '.log';
		parent::__construct($this->basename);
		$this->exists   = $this->isFile();
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
		touch($this->logname);
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

		$role_contents_admin = Auth::check_role('role_contents_admin');
		$msg_require = ($role_contents_admin) ? $_attach_messages['msg_require'] : '';

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
		if ($role_contents_admin !== FALSE) {
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
		if (Auth::check_role('role_contents_admin') && ! pkwk_login($pass)) {
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

		if (Auth::check_role('role_contents_admin') && ! pkwk_login($pass))
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
		if (Auth::check_role('role_contents_admin') && ! Auth::login($pass))
			return attach_info('err_adminpass');

		$this->getstatus();
		$this->status['freeze'] = $freeze;
		$this->putstatus();

		return array('msg'=>$_attach_messages[$freeze ? 'msg_freezed' : 'msg_unfreezed']);
	}

	function open()
	{
		global $use_sendfile_header;
		$response = new Response();

		if (! ($this->isFile() && $this->isReadable())) {
			$response->setStatusCode(Response::STATUS_CODE_404);
			header($response->renderStatusLine());
			exit;
		}
		$this->getstatus();
		
		$filename = $this->file;
		$realpath = $this->getRealPath();

		// Care for Japanese-character-included file name
		if (LANG == 'ja_JP') {
			switch(UA_NAME . '/' . UA_PROFILE){
			case 'Opera/default':
				// Care for using _auto-encode-detecting_ function
				$filename = mb_convert_encoding($this->file, 'UTF-8', 'auto');
				break;
			case 'MSIE/default':
				$filename = mb_convert_encoding($this->file, 'SJIS', 'auto');
				break;
			}
		}

		ini_set('default_charset', '');
		mb_http_output('pass');

		// ヘッダー出力
		$header['Last-Modified'] = gmdate('D, d M Y H:i:s', $this->getMTime() ) . ' GMT';
		if ($this->type == 'text/html' || $this->type == 'application/octet-stream') {
			$header['Content-Disposition'] = 'attachment; filename="' . $filename . '"';
			$header['Content-Type'] = 'application/octet-stream';
		} else {
			$header['Content-Disposition'] = 'inline; filename="' . $filename . '"';
			$header['Content-Type'] = $this->type;
		}
	//	if ($use_sendfile_header === true){
			// for reduce server load
			$header['X-Sendfile'] = $realpath;
	//	}
		$header['Content-Length'] = $this->getSize();
		$header['Cache-Control'] = 'max-age=3600, must-revalidate';

		$response->setStatusCode(Response::STATUS_CODE_200);
		$response->getHeaders()->addHeaders($header);

		if (!headers_sent()) {
			header($response->renderStatusLine());
			foreach ($response->getHeaders() as $_header) {
				header($_header->toString());
			}
		}
		// ファイルの読み込み
		$f = $this->openFile('rb');
		// ロック
		$f->flock(LOCK_SH);
		echo $f->fpassthru();
		// アンロック
		$f->flock(LOCK_UN);
		// 念のためオブジェクトを開放
		unset($f);

		// 読み込み回数のカウンタを更新
		$this->status['count'][$this->age]++;
		$this->putstatus();
		exit;
	}
}