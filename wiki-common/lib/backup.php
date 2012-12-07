<?php
/**
 *
 * PukiWiki - Yet another WikiWikiWeb clone.
 *
 * backup.php
 *
 * バックアップを管理する
 *
 * @package org.pukiwiki
 * @access  public
 * @author
 * @create
 * @version $Id: backup.php,v 1.14 2012/01/08 17:16:00 Logue Exp $
 * Copyright (C)
 *   2010-2012 PukiWiki Advance Developer Team
 *   2005-2006 PukiWiki Plus! Team
 *   2002-2006,2011 PukiWiki Developers Team
 *   2001-2002 Originally written by yu-ji
 * License: GPL v2 or (at your option) any later version
 **/

// namespace PukiWiki/Lib
class Backup{
	// バックアップの世代ごとの区切り文字（default.ini.php）
	const SPLITTER = PKWK_SPLITTER;
	// バックアップで利用可能な圧縮形式
	protected $available_ext = array('.bz2','.gz','.txt');

	private $splitter_reglex, $page, $ext = '.txt', $name, $file, $time, $cycle, $maxage;

	public function __construct($page){
		global $do_backup, $cycle, $maxage;
		if (auth::check_role('readonly') || ! $do_backup) return;

		// バックアップのページ名
		$this->page = $page;
		// バックアップの拡張子
		if (function_exists("bzcompress")){
			$this->ext  = '.bz';
		}else if (function_exists("gzcompress")){
			$this->ext  = '.gz';
		}
		// バックアップの世代間の区切りの正規表現
		$this->splitter_reglex = '/^(' . preg_quote(self::SPLITTER) . '\s\d+(\s(\d+)|))$/';
		// バックアップの名前（拡張子抜き）
		$this->name = BACKUP_DIR . encode($page);
		// バックアップの最終更新日時
		$this->time = $this->hasBackup() ? filemtime($this->file) : 0;	// このhasBackup()でファイル名（$this->file）も定義
		// バックアップのファイルサイズ
		$this->size = filesize($this->file);
		// バックアップの頻度
		$this->cycle = 60 * 60 * $cycle;
		// バックアップの上限個数
		$this->maxage = $maxage;
	}

	/**
	 * setBackup
	 * バックアップを作成する
	 *
	 * @access    public
	 * @param     Boolean   $delete      TRUE:バックアップを削除する
	 *
	 * @return    Void
	 */
	public function setBackup(){
		// ページが存在しない場合、バックアップ作成しない。
		if (! is_page($this->page)) return;

		// 連続更新があった場合に備えて、バックアップをさくせするまでのインターバルを設ける
		if (! ($this->time == 0 || UTIME - $this->time > $this->cycle) ) return;

		// 現在のバックアップを取得
		$backups = $this->getBackup();
		$count   = count($backups) + 1;

		// 直後に1件追加するので、(最大件数 - 1)を超える要素を捨てる
		if ($count > $this->maxage)
			array_splice($backups, 0, $count - $this->maxage);

		// バッグアップデーターをパース
		$strout = '';
		foreach($backups as $age=>$data) {
			// BugTrack/685 by UPK
			$strout .= self::SPLITTER . ' ' . $data['time'] . ' ' . $data['real'] . "\n"; // Splitter format
			$strout .= join("\n", $data['data']);
			unset($backups[$age]);
		}
		$strout = preg_replace("/([^\n])\n*$/", "$1\n", $strout);

		// 追加するバックアップデーター
		// Escape 'lines equal to self::SPLITTER', by inserting a space
		$body = preg_replace($this->splitter_reglex, '$1 ', get_source($this->page));
		// BugTrack/685 by UPK
		$body = self::SPLITTER . ' ' . $this->time . ' ' . UTIME . "\n" . join('', $body);
		$body = preg_replace("/\n*$/", "\n", $body);

		// 書き込む
		$this->write($strout. "\n". $body);
	}
	/**
	 * getBackup
	 * バックアップを取得する
	 * $age = 0または省略 : 全てのバックアップデータを配列で取得する
	 * $age > 0           : 指定した世代のバックアップデータを取得する
	 *
	 * @access    public
	 * @param     Integer   $age         バックアップの世代番号 省略時は全て
	 *
	 * @return    String    バックアップ       ($age != 0)
	 *            Array     バックアップの配列 ($age == 0)
	 */
	public function getBackup($age = 0){
		$_age = 0;
		$retvars = $match = array();
		$lines = $this->read();

		foreach($lines as $index=>$line) {
			// BugTrack/685 by UPK
			if ( preg_match($this->splitter_reglex, $line, $match) ) {
				// A splitter, tells new data of backup will come
				++$_age;
				if ($age > 0 && $_age > $age) return $retvars[$age];

				// BugTrack/685 by UPK
				// 実際ページを保存した時間が指定されている場合（タイムスタンプを更新しないをチェックして更新した場合）
				// そちらのパラメータをバックアップの日時として使用する。
				$now = (isset($match[3]) && $match[2] !== $match[3]) ? $match[3] : $match[2];	

				// Allocate
				$retvars[$_age] = array('time'=>$match[2], 'real'=>$now, 'data'=>array());
			} else {
				// The first ... the last line of the data
				$retvars[$_age]['data'][] = $line;
			}
			unset($lines[$index]);
		}

		return $retvars;
	}
	/**
	 * hasBackup
	 * バックアップファイルが存在するか
	 *
	 * @access    private
	 *
	 * @return    Boolean   TRUE:ある FALSE:ない
	 */
	public function hasBackup(){
		// 設定を途中で変えたり、後方互換性のため拡張子ごとにチェックする
		foreach ($this->available_ext as $ext){
			$file = $this->name.$ext;
			if (file_exists($file)){
				$this->file = $file;
				return true;
			}
		}
	}
	/**
	 * removeBackup
	 * バックアップファイルを削除する
	 *
	 * @access    private
	 * @param     Array     $age         削除する世代。空なら全部。
	 *
	 * @return    Boolean   FALSE:失敗
	 */
	public function removeBackup($ages = array()){
		if($ages === array()) {
			// バックアップファイルの削除
			return unlink($this->file);
		} else {
			// バックアップから指定世代のみ削除
			$backups = $this->getBackup();
			foreach($ages as $age) {
				unset($backups[$age]);
			}
			// 指定世代を削除したバックアップを書き込む
			$strout = '';
			foreach($backups as $age=>$data) {
				$strout .= self::SPLITTER . ' ' . $data['time'] . ' ' . $data['real'] . "\n"; // Splitter format
				$strout .= join('', $data['data']);
				unset($backups[$age]);
			}
			$this->write(preg_replace("/([^\n])\n*$/", "$1\n", $strout));
		}
	}

	/**
	 * read()
	 * バックアップファイルの内容を取得する
	 *
	 * @access    private
	 *
	 * @return    Array     ファイルの内容
	 */
	private function read(){
		foreach ($this->available_ext as $ext){
			if (file_exists($this->name.$ext)) return $this->read_sub($ext);
		}
		return array();
	}
	/**
	 * read_sub()
	 * バックアップファイルの内容を圧縮形式に応じて取得する
	 *
	 * @access    private
	 *
	 * @return    Array     ファイルの内容
	 */
	private function read_sub($ext){
		// return file($this->file);
		$data = '';
		// ファイルを取得
		switch ($ext) {
			case '.txt' :
				$data = file_get_contents($this->file);
				break;
			case '.gz':
				$file = @gzopen($this->file, 'r');
				if ($file) { 
					while (!gzeof($file)) {
						$data .= gzread($file, 1024);
					} 
					gzclose($file);
				}
				break;
			case '.bz2':
				$file = @bzopen($this->file, 'r');
				if ($file) { 
					while (!feof($file)) {
						$data .= bzread($file, 1024);
					} 
					gzclose($file);
				} 
				break;
		}
		// 取得した内容を改行ごとに配列として返す
		return explode("\n", $data);
	}
	/**
	 * write()
	 * バックアップファイルに書き込む
	 *
	 * @access    private
	 * @param     String    $content 文字列
	 *
	 * @return    Boolean   FALSE:失敗 その他:書き込んだバイト数
	 */
	private function write($content){
		// 文字列を配列ごとに改行として分割
		$data = join("\n", $content);

		// 古いバックアップを削除（追記する実装でないため）
		foreach ($available_ext as $ext){
			if (file_exists($this->name.$ext)) unlink($this->name.$ext);
		}

		// 書き込み
		pkwk_touch_file($this->file);
		$bytes = '';
		
		switch ($this->ext) {
			case '.txt' :
				/*
				$fp = fopen($this->file, 'wb')
					or die_message('Cannot open <var>' . htmlsc($this->file) . '</var>.<br />Maybe permission is not writable or filename is too long');
				$bytes = fputs($this->file, $content);
				fclose($this->file);
				*/
				return file_put_contents($this->file, $data, LOCK_EX);
				break;
			case '.gz':
				$file = gzopen($this->file,'w');
				if ($file) {
					$bytes = gzwrite($file, $data);
					gzclose($file);
				}else{
					return false;
				}
				break;
			case '.bz2':
				$file = bzopen($this->file,'w');
				if ($file) { 
					$bytes = bzwrite($file, $data);
					bzclose($file);
				}else{
					return false;
				}
				break;
		}
		return $bytes;
	}
	
	public function __toString(){
		return join("\n", $this->read());
	}
}

// for compatibility

/**
 * make_backup
 * バックアップを作成する
 *
 * @access    public
 * @param     String    $page        ページ名
 * @param     Boolean   $delete      TRUE:バックアップを削除する
 *
 * @return    Void
 */

function make_backup($page, $delete = FALSE)
{
	global $del_backup;
	$backup = new Backup($page);

	if ($del_backup && $delete) {
		$backup->removeBackup();
		return;
	}
	return $backup->setBackup();
}

/**
 * get_backup
 * バックアップを取得する
 * $age = 0または省略 : 全てのバックアップデータを配列で取得する
 * $age > 0           : 指定した世代のバックアップデータを取得する
 *
 * @access    public
 * @param     String    $page        ページ名
 * @param     Integer   $age         バックアップの世代番号 省略時は全て
 *
 * @return    String    バックアップ       ($age != 0)
 *            Array     バックアップの配列 ($age == 0)
 */
function get_backup($page, $age = 0)
{
	$backup = new Backup($page);
	return $backup->getBackup($age);
}

function _backup_file_exists($page){
	$backup = new Backup($page);
	return $backup->hasBackup();
}

/* End of file backup.php */
/* Location: ./wiki-common/lib/backup.php */
