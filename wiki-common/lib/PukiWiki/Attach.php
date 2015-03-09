<?php
/**
 * 添付管理
 *
 * @package   PukiWiki
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2014 PukiWiki Advance Developers Team
 * @create    2014/01/23
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Attach.php,v 1.0.1 2014/12/24 23:29:00 Logue Exp $
 */

namespace PukiWiki;

use PukiWiki\Auth\Auth;
use PukiWiki\Config\Config;
use PukiWiki\Factory;
use PukiWiki\File\AttachFile;
use PukiWiki\File\File;
use PukiWiki\File\FileFactory;
use PukiWiki\Renderer\Header;
use PukiWiki\Text\Reading;
use PukiWiki\Utility;
use Zend\Http\Response;

/**
 * 添付ファイル管理クラス
 */
class Attach{
	public $files, $logfile, $path;
	/**
	 * 添付ファイルのMIMEタイプの定義ページ名
	 */
	const ATTACH_CONFIG_PAGE_MIME = 'plugin/attach/mime-type';
	/**
	 * デフォルトのMIMEタイプ（強制的にダウンロードさせる）
	 */
	const DEFAULT_MIME_TYPE = 'application/force-download';
	// const DEFAULT_MIME_TYPE = 'application/octet-stream';
	/**
	 * ファイルの改名を有効にする
	 */
	const ENABLE_RENAME = true;
	/**
	 * 管理人のみ削除可
	 */
	const DELETE_ADMIN_ONLY = false;
	/**
	 * ステータス
	 */
	private $status = array('count'=>array(0), 'age'=>'', 'pass'=>'', 'freeze'=>FALSE);
	/**
	 * コンストラクタ
	 * @param string $page ページ名
	 * @param string $file 添付ファイル名
	 */
	public function __construct($page, $filename, $age = 0){
		if (!isset($page)) Utility::dieMessage('Pagename is missing');
		if (!isset($filename)) Utility::dieMessage('Filename is missing');
		$this->page = $page;
		$this->wiki = Factory::Wiki($page);
		$this->filename = $filename;
		$this->age = $age;
		$attaches = $this->wiki->attach(false);
		$this->files = $attaches[$filename];	// ファイルのバックアップ一覧
		$this->update = false;	// ステータスの更新フラグ
		$this->path = AttachFile::$dir . $this->files[$this->age];	// ファイルのパス

		// ログファイルが存在しない場合
		if (!isset($this->files['log'])){
			$this->files['log'] = Utility::encode($filename).'.log';
		}

		$this->logfile = new File(AttachFile::$dir . $this->files['log']);
		// 管理情報を取得
		if ($this->logfile->has()){;
			$data = $this->logfile->get();
			foreach ($this->status as $key=>$value) {
				$this->status[$key] = chop(array_shift($data));
			}
			$this->status['count'] = explode(',', $this->status['count']);
		}
		// 実際使ううえではlogはアンセット
		unset($this->files['log']);
	}
	/**
	 * 
	 */
	public function updateStatus(){
		// ファイルのステータスを保存
		$this->status['count'] = join(',', $this->status['count']);
		foreach ($this->status as $key=>$value) {
			$data[] = $value;
		}
		$this->logfile->set($data);
	}
	/**
	 * ファイルを開く
	 * @param string $file ファイル名
	 * @param int $age バックアップ世代
	 * @return string
	 */
	public function get(){
		$fileinfo = new File($this->path);
		return $fileinfo->get();
	}
	/**
	 * ファイルの存在確認
	 * @return boolean
	 */
	public function has(){
		return isset($this->files[$this->age]);
	}
	/**
	 * ファイルをセットする
	 */
	public function set($content, $keeptimestamp = false){
		$fileinfo = new File($this->path);
		$this->status['count'][$this->age]++;
		$this->update = true;
		return $fileinfo->set($content, $keeptimestamp);
	}
	/**
	 * リネームする
	 * @param string $to
	 */
	public function rename($to){
		if ($this->status['freeze']) return attach_info('msg_isfreeze');

		if (Auth::check_role('role_contents_admin') && ! Auth::login($pass)) return attach_info('err_adminpass');

		// 基点名（エンコードされたページ名＋_）
		$basename = AttachFile::$dir . Utility::encode($this->page) . '_';
		// 新しいファイル名
		$newname = Utility::encode($to);
		if (file_exists($basename . $newname)) {
			// ファイルが存在する場合
			return false;
		}
		$rename_targets = array();
		foreach (AttachFile::exists() as $file){
			if (! preg_match('/^(' . preg_quote(Utility::encode($this->filename)) . ')(\.((\d+)|(log)))$/', $file, $matches)){
				continue;
			}
			// 0 …ファイル名全体 1…ファイル名 2…拡張子
			rename($basename . $matches[0], $basename . $newname . $matches[2]);
		}
		AttachFile::clearCache();
		return true;
	}
	/**
	 * 削除する
	 * @param int $age バックアップ世代
	 */
	public function delete($pass){
		if ($this->status['freeze']) return attach_info('msg_isfreeze');

		// if (! pkwk_login($pass)) {
		if (Auth::check_role('role_contents_admin') && ! Auth::login($pass)) {
			if (PLUGIN_ATTACH_DELETE_ADMIN_ONLY || $this->age) {
				return attach_info('err_adminpass');
			} else if (PLUGIN_ATTACH_PASSWORD_REQUIRE &&
				md5($pass) !== $this->status['pass']) {
				return attach_info('err_password');
			}
		}

		// バックアップ
		if ($this->age ||
			(ATTACH_DELETE_ADMIN_ONLY && ATTACH_DELETE_ADMIN_NOBACKUP)) {
			unlink($this->filename);
		} else {
			$basename = AttachFile::$dir . Utility::encode($this->page) . '_' . Utility::encode($this->filename);
			do {
				$age = ++$this->status['age'];
			} while (file_exists($basename . '.' . $this->age));

			if (! rename($basename, $basename . '.' . $this->age)) {
				// 削除失敗 why?
				return Utility::dieMessage('Cannot rename this file.');
			}

			$this->status['count'][$this->age] = $this->status['count'][0];
			$this->status['count'][0] = 0;
			$this->updateStatus();
		}

		if ($this->wiki->has())
			$this->wiki->touch();

		if ($notify) {
			$footer['ACTION']   = 'File deleted';
			$footer['FILENAME'] = & $this->file;
			$footer['PAGE']     = & $this->page;
			$footer['URI']      = get_page_absuri($this->page);
			$footer['USER_AGENT']  = TRUE;
			$footer['REMOTE_ADDR'] = TRUE;
			pkwk_mail_notify($notify_subject, "\n", $footer) or
				Utility::dieMessage('pkwk_mail_notify(): Failed');
		}

		return true;
	}
	/**
	 * ファイルを凍結／解除
	 * @param boolean $freeze
	 * @param boolean
	 */
	public function freeze($freeze, $pass){
		if (Auth::check_role('role_contents_admin') && ! Auth::login($pass))
			return attach_info('err_adminpass');
		
		$this->status['freeze'] = $freeze;
		$this->update = true;
		return true;
	}
	/**
	 * ファイルを出力
	 * @param string $file ファイル名
	 * @param int $age バックアップ世代
	 * @return void
	 */
	public function render(){
		global $vars;
		$response = new Response();

		// ステータスコード
		$status_code = Response::STATUS_CODE_200;

		$file = new File($this->path);

		if (! ($file->has() && $file->isReadable())) {
			Header::writeResponse($header, $status_code, $buffer);
			$response->setStatusCode(Response::STATUS_CODE_404);
			header($response->renderStatusLine());
			echo 'File not found.';
			exit;
		}

		// ファイルの読み込み
		$f = $file->openFile('rb');
		// ファイルの内容をGZ圧縮してバッファに保存
		ob_start('ob_gzhandler');
		// ファイルを一応ロック
		$f->flock(LOCK_SH);
		// ファイルサイズ
		$filesize = $f->getSize();
		// ファイルの残量
		$rest = 0;
		// 分割転送する場合のブロックサイズ
		$blocksize = 4096;

		if ( isset($_SERVER['HTTP_RANGE']) ){
			// 環境変数HTTP_RANGEから全データー量と転送済みのデーター量を取得する
			list($toss, $range) = explode('=', $_SERVER['HTTP_RANGE']);
			list($range_start, $range_end) = explode('-', $range);
			
			$range_end = $filesize - 1;
			$length = $filesize - $range_start;
			if ($range_start > 0){
				// 206 Partial Content
				$status_code = STATUS_CODE_206;
				// Content Rangeヘッダーを付加
				$header['Content-Range'] = 'bytes ' . $range . '/' . $filesize;
			}
			// 転送済みのファイルの長さの分だけポインタを進める
			$f->fseek($length);
			// 出力完了位置
			$rest = $range_end - $range_start + 1;

			// ファイルを出力
			$readsize = 0;
			while (!$f->eof() && $rest > 0) {
				if ($blocksize > $rest) {
					$readsize = $rest;
				}
				echo $f->fgets();
				$rest -= $readsize;
			}
		}else{
			while (!$f->eof()) {
				echo $f->fgets();
			}
		}
		$f->flock(LOCK_SH);
		$buffer = ob_get_clean();

		$mime = self::getMime();
		// HTMLの場合、ダウンロードとする
		$content_type = $mime == 'text/html' ? self::DEFAULT_MIME_TYPE : $mime;

		$filename = mb_convert_encoding($this->filename, 'UTF-8', 'auto');
		// ヘッダー出力
		$header = Header::getHeaders($content_type, $f->getMTime());

		// attach系プラグインから読み取るときはattachment、refなど埋め込み系プラグインから読み取るときはinline
		$disposition = $vars['cmd'] === 'attach' ? 'attachment' : 'inline';
		
		// 添付ファイルダウンロードで日本語ファイル名が文字化けする
		// http://pukiwiki.sourceforge.jp/dev/?BugTrack2%2F354
		$header['Content-Disposition'] = $disposition . '; filename="' . $this->filename . '"; filename*=utf-8\'\'' . rawurlencode($this->filename);

		$header['X-Sendfile'] = $file->getRealPath();
		$header['Content-Length'] = $filesize;

		// 読み込み回数のカウンタを更新
		
		if ($this->age === 0){
			// レジュームやキャッシュからの場合もカウントされるがこの実装でいいのだろうか？
			$this->status['count'][0]++;
			$this->updateStatus();
		}

		// 出力
		Header::writeResponse($header, $status_code, $buffer);

		exit;
	}
	/**
	 * ファイルの詳細
	 * @param string $file ファイル名
	 * @return array
	 */
	public function info($err)
	{
		global $_attach_messages, $vars, $_LANG;

		$role_contents_admin = Auth::check_role('role_contents_admin');
		$msg_require = ($role_contents_admin) ? $_attach_messages['msg_require'] : '';

		$file = $this->path;

		$ret[] = empty($err) ? '' : '<p class="error error-warning">' . $_attach_messages[$err] . '</p>';

		if (IS_AJAX) {
			$retval = array('msg'=>sprintf($_attach_messages['btn_info'], Utility::htmlsc($this->filename)));
			$ret[] = '<div id="attach-info-tabs" class="tabs">';
			$ret[] = '<ul role="tablist">';
			$ret[] = '<li role="tab" id="tab1" aria-controls="attach_info"><a href="#attach_info">' . $_attach_messages['msg_info'] . '</a></li>';
			$ret[] = '<li role="tab" id="tab2" aria-controls="attach_form_edit"><a href="#attach_form_edit">' . $_LANG['skin']['edit'] . '</a></li>';
			$ret[] = '</ul>';
		}else{
			$retval = array('msg'=>sprintf($_attach_messages['msg_info'], htmlsc($this->filename)));
			$ret[] = '<nav>';
			$ret[] = '<ul class="attach_navibar">';
			$ret[] = '<li><a href="' . Router::get_cmd_uri('attach','','',array('pcmd'=>'list','refer'=>$this->page)) . '">' . $_attach_messages['msg_list'] .'</a></li>';
			$ret[] = '<li><a href="' . Router::get_cmd_uri('attach','','',array('pcmd'=>'list')) . '">' . $_attach_messages['msg_listall'] .'</a></li>';
			$ret[] = '</ul>';
			$ret[] = '</nav>';
		}

		// 情報タブ
		$ret[] = '<div id="attach_info" role="tabpanel" aria-labeledby="tab1">';
		$ret[] = '<details>';
		$ret[] = '<summary>' . $this->toString(TRUE, FALSE) . '</summary>';

		$f = new File(UPLOAD_DIR . $this->files[$this->age]);

		$ret[] = '<dl class="dl-horizontal">';
		if ($role_contents_admin !== FALSE) {
			$ret[] = '<dt>' . $_attach_messages['msg_filename'] . ($this->status['freeze'] ? '<span class="fa fa-lock"></span>' : '' ) .'</dt>';
			$ret[] = '<dd><var>' . $this->filename . '</var></dd>';
		}
		$ret[] = '<dt>' . $_attach_messages['msg_page'] . ':</dt>';
		$ret[] = '<dd><var>' . Utility::htmlsc($this->page) . '</var></dd>';
		$ret[] = '<dt>Content-type:</dt>';
		$ret[] = '<dd><var>' . $this->getMime($this->age) . '</var></dd>';
		$ret[] = '<dt>' . $_attach_messages['msg_filesize'] . ':</dt>';
		$ret[] = '<dd><var>' . $f->getSize() . '</var>KB</dd>';
		$ret[] = '<dt>' . $_attach_messages['msg_date'] . ':</dt>';
		$ret[] = '<dd><var>' . $f->getMTime() . '</var></dd>';
		$ret[] = '<dt>' . $_attach_messages['msg_dlcount'] . ':</dt>';
		$ret[] = '<dd><var>' . $this->status['count'][$this->age] . '</var></dd>';
		$ret[] = '<dt>' . $_attach_messages['msg_md5hash'] . ':</dt>';
		$ret[] = '<dd><var>' . $f->md5() . '</var></dd>';
		$ret[] = '</dl>';
		$ret[] = '</details>';
		$ret[] = $this->getThumbnail();
		$ret[] = '</div>';

		$ret[] = IS_AJAX ? '' : '<hr />';

		// 操作タブ
		$ret[] = '<div id="attach_form_edit" role="tabpanel" aria-labeledby="tab2">';
		$ret[] = '<form action="' . Router::get_script_uri() . '" method="post" class="form form-attach">';
		$ret[] = '<input type="hidden" name="cmd" value="attach" />';
		$ret[] = '<input type="hidden" name="page" value="' . $this->page . '" />';
		$ret[] = '<input type="hidden" name="file" value="' . $this->filename . '" />';
		$ret[] = '<input type="hidden" name="age" value="' . $this->age . '" />';

		if ($this->age) {
			$ret[] = '<div class="radio">';
			$ret[] = '<label for="_p_attach_delete"><input type="radio" name="pcmd" id="_p_attach_delete" value="delete" />' . $_attach_messages['msg_delete'] . $msg_require . '</label>';
			$ret[] = '</div>';
		} else {
			if ($this->status['freeze']) {
				$ret[] = '<div class="radio">';
				$ret[] = '<label for="_p_attach_unfreeze"><input type="radio" name="pcmd" id="_p_attach_unfreeze" value="unfreeze" />' . $_attach_messages['msg_unfreeze'] . $msg_require . '</label>';
				$ret[] = '</div>';
			} else {
				$ret[] = '<div class="radio">';
				$ret[] = '<label for="_p_attach_delete"><input type="radio" name="pcmd" id="_p_attach_delete" value="delete" />' . $_attach_messages['msg_delete'] . (self::DELETE_ADMIN_ONLY || $this->age ? $msg_require : '') . '</label>';
				$ret[] = '</div>';

				$ret[] = '<div class="radio">';
				$ret[] = '<label for="_p_attach_freeze"><input type="radio" name="pcmd" id="_p_attach_freeze" value="freeze" />' . $_attach_messages['msg_freeze']  . $msg_require . '</label>';
				$ret[] = '</div>';
				if (self::ENABLE_RENAME) {
					$ret[] = '<div class="radio">';
					$ret[] = '<label for="_p_attach_rename"><input type="radio" name="pcmd" id="_p_attach_rename" value="rename" />' . $_attach_messages['msg_rename'] . $msg_require . '</label>';
					$ret[] = '</div>';
					$ret[] = '<div class="form-group">';
					$ret[] = '<label for="_p_attach_newname">' . $_attach_messages['msg_newname'] . ':</label> ';
					$ret[] = '<input type="text" name="newname" class="form-control" id="_p_attach_newname" size="40" value="' .$this->filename . '" />';
					$ret[] = '</div>';
				}
			}
		}
		if ($role_contents_admin !== FALSE) {
			$ret[] = '<div class="form-group">';
			$ret[] = '<label for="_p_attach_password">' . $_attach_messages['msg_password'] . '</label>';
			$ret[] = '<input class="form-control" type="password" name="pass" id="_p_attach_password" size="8" />';
			$ret[] = '</div>';
		}
		$ret[] = '<input type="submit" class="btn btn-danger" value="' . $_attach_messages['btn_submit'] . '" />';
		$ret[] = '</form>';
		$ret[] = '</div>';
		$ret[] = '</div>';

		$ret[] = IS_AJAX ? '</div>' : '';

		$retval['body'] = join("\n",$ret);
		return $retval;
	}
	/**
	 *
	 * @param boolean $showcount
	 * @param boolean $showinfo
	 * @return string
	 */
	public function toString($showcount = true, $showinfo = true){
		global $_attach_messages;

		$fileinfo = new File(UPLOAD_DIR . $this->files[$this->age]);
		$inf = Router::get_cmd_uri('attach','','',array('pcmd'=>'info','refer'=>$this->page,'age'=>$this->age,'file'=>$this->filename));
		$open = Router::get_cmd_uri('attach','','',array('pcmd'=>'open','refer'=>$this->page,'age'=>$this->age,'file'=>$this->filename));

		$label = Utility::htmlsc($this->filename);
		if ($this->age !== 0) {
			$label .= ' (backup No.' . $this->age . ')';
		}
		$info = $count = '';
		if ($showinfo) {
			$info = '<a href="' . Router::get_cmd_uri('attach', null, null, array('pcmd'=>'info','refer'=>$this->page, 'file'=>$this->filename)) .
				'" class="btn btn-default btn-xs" title="' . str_replace('$1', rawurlencode($this->filename), $_attach_messages['msg_info']) . '">' .
				'<span class="fa fa-info"></span></a>';

			if ($this->isFreezed()) $info .= ' <span class="fa fa-lock"></span>';

			$count = ($showcount && ! empty($this->status['count'][$this->age])) ?
				'<small>(<var>' . $this->status['count'][$this->age] . '</var>)</small> ' : '';
		}
		if (IS_MOBILE) {
			return '<a href="'.$open.'" data-role="button" data-inline="true" data-mini="true" data-icon="gear" data-ajax="false">'.$label.'</a>';
		}else{
			return '<a href="'.$open.'" title="' . Time::getZoneTimeDate('Y/m/d H:i:s', $fileinfo->time()) . ' ' .
			sprintf('%01.1f', round($fileinfo->getSize()/1024, 1)) . 'KB"><span class="fa fa-download"></span>'.$label.'</a> '.$count.' '.$info;
		}
	}
	/**
	 * ファイルのMIMEタイプを取得
	 */
	public function getMime(){
		// finfo関数の出力を優先
		if(function_exists('finfo_file')){
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mimetype = finfo_file($finfo, AttachFile::$dir .  $this->files[$this->age]);
			finfo_close($finfo);
			return $mimetype;
		}

		$f = new File($this->path);
		try {
			// @をつけると処理が重いのでtry-catch文を使う
			$size = getimagesize($f->getRealPath());
			// 画像の場合
			switch ($size[2]) {
				case IMAGETYPE_BMP     : return 'image/bmp';
				case IMAGETYPE_GIF     : return 'image/gif';
				case IMAGETYPE_ICO     : return 'image/vnd.microsoft.icon';
				case IMAGETYPE_IFF     : return 'image/iff';
				case IMAGETYPE_JB2     : return 'image/jbig2';
				case IMAGETYPE_JP2     : return 'image/jp2';
				case IMAGETYPE_JPC     : return 'image/jpc';
				case IMAGETYPE_JPEG    : return 'image/jpeg';
				case IMAGETYPE_JPX     : return 'image/jpx';
				case IMAGETYPE_PNG     : return 'image/png';
				case IMAGETYPE_PSD     : return 'image/psd';
				case IMAGETYPE_SWC     :
				case IMAGETYPE_SWF     : return 'application/x-shockwave-flash';
				case IMAGETYPE_TIFF_II :
				case IMAGETYPE_TIFF_MM : return 'image/tiff';
				case IMAGETYPE_WBMP    : return 'image/vnd.wap.wbmp';
				case IMAGETYPE_XBM     : return 'image/xbm';
			}
		}catch(Exception $e) {
			// 画像でない場合エラーが発生するので例外処理で投げる
		}

		// mime-type一覧表を取得
		$config = new Config(self::ATTACH_CONFIG_PAGE_MIME);
		$table = $config->read() ? $config->get('mime-type') : array();
		unset($config); // メモリ節約

		foreach ($table as $row) {
			$_type = trim($row[0]);
			$exts = preg_split('/\s+|,/', trim($row[1]), -1, PREG_SPLIT_NO_EMPTY);

			foreach ($exts as $ext) {
				if (preg_match('/\.' . $ext . '$/i', $this->filename)){
					return $_type;
				}
			}
		}
		
		return self::DEFAULT_MIME_TYPE;
	}
	/**
	 * ファイルタイプによる圧縮添付の判定
	 * @param string $mime_type
	 * @return boolean
	 */
	private function isCompress($mime_type = self::DEFAULT_MIME_TYPE){
		if (empty($mime_type)) return true;

		list($discrete,$composite_tmp) = explode('/', strtolower($mime_type));

		if (strstr($mime_type,';') === false) {
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
				'msword'			=> false, // doc
				'vnd.ms-excel'		=> false, // xls
				'vnd.ms-powerpoint'	=> false, // ppt
				'vnd.visio'			=> false,
				'octet-stream'		=> false, // bin dms lha lzh exe class so dll img iso
				'x-bcpio'			=> false, // bcpio
				'x-bittorrent'		=> false, // torrent
				'x-bzip2'			=> false, // bz2
				'x-compress'		=> false,
				'x-cpio'			=> false, // cpio
				'x-dvi'				=> false, // dvi
				'x-gtar'			=> false, // gtar
				'x-gzip'			=> false, // gz tgz
				'x-rpm'				=> false, // rpm
				'x-shockwave-flash'	=> false, // swf
				'zip'				=> false, // zip
				'x-7z-compressed'	=> false, // 7zip
				'x-lzh-compressed'	=> false, // LZH
				'x-rar-compressed'	=> false, // RAR
				'x-java-archive'	=> false, // jar
				'x-javascript'		=> true, // js
				'ogg'				=> false, // ogg
				'pdf'				=> false, // pdf
			),
		);
		if (isset($composite_type[$discrete][$composite])) {
			return $composite_type[$discrete][$composite];
		}

		// discrete-type
		static $discrete_type = array(
			'text'			=> true,
			'image'			=> false,
			'audio'			=> false,
			'video'			=> false,
		);
		return isset($discrete_type[$discrete]) ? $discrete_type[$discrete] : true;
	}
	/**
	 * ファイルが凍結されているか
	 * @return boolean
	 */
	private function isFreezed(){
		return $this->status['freeze'];
	}
	/**
	 * サムネイルを出力
	 * @return string
	 */
	private function getThumbnail(){
		$ret = array();
		// サムネイル（JPEG、TIFFのみ）
		if (extension_loaded('exif') && extension_loaded('gd')){
			$file = $this->path;
			$image_type = exif_imagetype($file);
			if ($image_type === IMAGETYPE_JPEG || $image_type === IMAGETYPE_TIFF_II || $image_type === IMAGETYPE_TIFF_MM ) {
				$exif = exif_read_data($file);
				$size = getimagesize($file);	// 画像でない場合はfalseを返す
				if ($size !== false){
					if ($size[2] > 0 && $size[2] < 3) {
						if ($size[0] < 200) {
							$w = $size[0]; $h = $size[1];
						} else {
							$w = 200; $h = $size[1] * (200 / ($size[0]!=0?$size[0]:1) );
						}
						$ret[] = '<figure class="img-thumbnail">';
						$ret[] = '<img src="'.Router::get_cmd_uri('ref',null,null,array('page'=>$this->page,'src'=>$this->filename));
						$ret[] = '" width="' . $w .'" height="' . $h . '" />';
						$ret[] = '</figure>';
					}
				}
			}
		}
		return join("\n",$ret);
	}
	/**
	 * ファイルサイズ
	 */
	public function getSize(){
		$fileinfo = new File($this->path);
		return  sprintf('%01.1f', round($fileinfo->getSize()/1024, 1)) . 'KB';
	}
	/**
	 * 更新日時
	 */
	public function getDate(){
		$fileinfo = new File($this->path);
		return Time::getZoneTimeDate('Y/m/d H:i:s', $fileinfo->getMTime());
	}
}