<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: File.php,v 1.0.0 2012/12/18 11:00:00 Logue Exp $
// Copyright (C)
//   2012 PukiWiki Advance Developers Team
// License: GPL v2 or (at your option) any later version
//

namespace PukiWiki\Lib\File;
use PukiWiki\Lib\Utility;

/**
 * ファイルの読み書きを行うクラス
 */
class File{
	const LOCK_FILE = 'chown.lock';

	protected $filename;

	/**
	 * コンストラクタ
	 * @param string $filename ファイル名（パスも含めること）
	 */
	public function __construct($filename) {
		static $fileinfo;
		if (empty($filename)){
			throw new \Exception('File name is missing!');
		}
		$this->filename = $filename;
		$this->info = new \SplFileInfo($this->filename);
	}
	/**
	 * ファイルが存在するか
	 * @return boolean
	 */
	public function has(){
		return $this->info->isFile();
	}
	/**
	 * ファイルの指定行数を取得
	 * @param int $count 読み込む行数
	 * @return array
	 */
	public function head($count = 1, $join = false){
		// Read top N lines as an array
		// (Use PHP file() function if you want to get ALL lines)
		if ( !self::has() ) return false;
		if ( !$this->info->isReadable() )
			Utility::die_message(sprintf('File <var>%s</var> is not readable.', Utility::htmlsc($this->filename)));

		// ファイルの読み込み
		$file = $this->info->openFile('r');
		// ロック
		$file->flock(LOCK_SH);
		// 巻き戻し（要るの？）
		$file->rewind();
		// 初期値
		$index  = 0;
		$result = array();
		// ファイルを読み込む
		while (!$file->eof()) {
			if ($count === 1){
				$result = rtrim($file->fgets());
				break;
			}
			$result[] = rtrim($file->fgets());	// 改行を含む余計な空白文字は削除
			if (++$index >= $count) break;
		}
		// アンロック
		$file->flock(LOCK_UN);
		// 念のためオブジェクトを開放
		unset($file);

		// 出力
		return $join || $count !== 1 ? join("\n", $result) : $result;
	}
	/**
	 * ファイルの内容を取得
	 * @param boolean $join 行を含めた文字列として読み込むか、行を配列として読み込むかのフラグ
	 * @param boolean $legacy 昔の読み込み方でロード（互換性のため）
	 * @return string or array
	 */
	public function get($join = false, $legacy = false){
		if ( !$this->has() ) return false;
		if ( !$this->info->isReadable() )
			Utility::die_message(sprintf('File <var>%s</var> is not readable.', Utility::htmlsc($this->filename)));

		// ファイルの読み込み
		$file = $this->info->openFile('r');
		// ロック
		$file->flock(LOCK_SH);
		// 巻き戻し（要るの？）
		$file->rewind();
		// 初期値
		$result = array();
		// 1行毎ファイルを読む
		while (!$file->eof()) {
			$result[] = $legacy ? strtr($file->fgets(), "\r", '') : rtrim($file->fgets());	// 改行を含む余計な空白文字は削除
		}
		// アンロック
		$file->flock(LOCK_UN);
		// 念のためオブジェクトを開放
		unset($file);
		
		// 出力
		return $join ? join("\n", $result) : $result;
	}
	/**
	 * ファイルの書き込み処理
	 * @param string or array $str 書き込む文字列
	 * @return int 書き込んだバイト数
	 */
	public function set($str, $keeptimestamp = false){
		// 書き込み可能かをチェック
		if ($this->has () && ! $this->info->isWritable())
			Utility::die_message(sprintf('File <var>%s</var> is not writable.', Utility::htmlsc($this->filename)));

		// 書き込むものがなかった場合、削除とみなす
		if (empty($str)) return $this->remove();

		// タイムスタンプを取得
		if ($keeptimestamp) $timestamp = self::getTime();

		$data = '';
		if (!is_array($str)){
			// 入力データが配列でない場合
			$str = explode( "\n", $str );
		}
		foreach ($str as $line){
			// 末尾の空白文字やヌル文字などゴミデーターをrtrim命令で削除しつつ整形する
			$data .= rtrim($line) . "\n";
		}
		unset($str);

		// ファイルを読み込み
		$file = $this->info->openFile('w');
		// ロック
		$file->flock(LOCK_EX);
		// 書き込む
		$ret = $file->fwrite($data);
		// アンロック
		$file->flock(LOCK_UN);
		// タイムスタンプを保持する場合
		if ($keeptimestamp) self::setTime($timestamp);
		// 念のためオブジェクトを開放
		unset($file);

		return $ret;
	}
	/**
	 * ファイルサイズ
	 * @return int
	 */
	public function size(){
		return $this->info->getSize();
	}
	/**
	 * ハッシュ
	 * @return string
	 */
	public function digest(){
		return $this->has() ? md5($this->get(true)) : null;
	}
	/**
	 * ファイルを削除
	 * @return int
	 */
	public function remove(){
		return $this->has() ? unlink($this->filename) : false;
	}
	/**
	 * 更新時刻を取得
	 * @return int
	 */
	public function getTime(){
		return $this->has() ? $this->info->getMTime() : 0;
	}
	/**
	 * 更新日時を指定
	 * @param type $time
	 * @return boolean
	 */
	public function setTime($time){
		return $this->touch($time);
	}
	/**
	 * ファイルの経過時間を取得
	 * @return string
	 */
	public function getPassage(){
		static $units = array('m'=>60, 'h'=>24, 'd'=>1);
		$time = max(0, (MUTIME - $this->info->getMTime()) / 60); // minutes

		foreach ($units as $unit=>$card) {
			if ($time < $card) break;
			$time /= $card;
		}
		$time = floor($time) . $unit;
		return $time;
	}
	/**
	 * アクセス時刻を取得
	 * @return int
	 */
	public function getAtime(){
		return $this->info->getATime();
	}
	/**
	 * アクセス日時を指定
	 * @param int $atime
	 * @return boolean
	 */
	public function setAtime($atime){
		return $this->touch($this->getTime(), $atime);
	}
	/**
	 * ファイルの所有者変更
	 * @param boolean $preserve_time 時刻を保持
	 * @return boolean
	 */
	private function chown($preserve_time = TRUE){
		// check UID（Windowsの場合は0になる）
		$this->php_uid = extension_loaded('posix') ? posix_getuid() : 0;
		$lockfile = new \SplFileInfo(CACHE_DIR . self::LOCK_FILE);

		// Lock for pkwk_chown()
		$lock = $lockfile->openFile('a') ;
		$lock->flock(LOCK_EX);

		// ファイルが作成されてないとエラーになる
		touch($this->filename);

		// Check owner
		$owner = $this->info->getOwner();
		if ($owner === $this->php_uid) {
			// NOTE: Windows always here
			$result = TRUE; // Seems the same UID. Nothing to do
		} else {
			$tmp = tmpfile();

			// Lock source $filename to avoid file corruption
			// NOTE: Not 'r+'. Don't check write permission here
			$file = $this->info->openFile('r');

			// Try to chown by re-creating files
			// NOTE:
			//   * touch() before copy() is for 'rw-r--r--' instead of 'rwxr-xr-x' (with umask 022).
			//   * (PHP 4 < PHP 4.2.0) touch() with the third argument is not implemented and retuns NULL and Warn.
			//   * @unlink() before rename() is for Windows but here's for Unix only
			$file->flock(LOCK_EX);
			$result =
				touch($tmp) &&
				copy($this->filename, $tmp) &&
				($preserve_time ? (touch($tmp, $stat[9], $stat[8]) || touch($tmp, $stat[9])) : TRUE) &&
				rename($tmp, $this->filename);
			$file->flock(LOCK_UN);

			if ($result === FALSE) unlink($tmp);
		}

		// Unlock for pkwk_chown()
		$lock->flock(LOCK_UN);

		return $result;
	}
	/**
	 * ファイルの更新日時を修正
	 *
	 * @param int $time 更新日時
	 * @param int $atime アクセス日時
	 * @return boolean
	 */
	public function touch($time = FALSE, $atime = FALSE){
		// Is the owner incorrected and unable to correct?
		if (! $this->has() || $this->chown()) {
			if ($time === FALSE) {
				// ファイルの領域を確保
				$result = touch($this->filename);
			} else if ($atime === FALSE) {
				// ファイルの更新日時を指定して領域を確保
				$result = touch($this->filename, $time);
			} else {
				// ファイルの更新日時とアクセス日時を指定して領域を確保
				$result = touch($this->filename, $time, $atime);
			}
			return $result;
		} else {
			Utility::die_message('pkwk_touch_file(): Invalid UID and (not writable for the directory or not a flie): ' .
				Utility::htmlsc(basename($this->filename)));
		}
	}
	/**
	 * 再帰的にディレクトリを作成
	 * @return boolean
	 */
	public function mkdir_r(){
		if ($this->has()) return false;

		$dirname = dirname($this->filename);	// ファイルのディレクトリ名
		// 階層指定かつ親が存在しなければ再帰
		if (strpos($dirname, '/') && !file_exists(dirname($dirname))) {
			// 親でエラーになったら自分の処理はスキップ
			if ($this->mkdir_r(dirname($dirname)) === false) return false;
		}
		return mkdir($dirname);
	}
	/**
	 * エイリアス：存在確認
	 */
	public function exists(){
		return self::has();
	}
	/**
	 * エイリアス：読み込み
	 */
	public function read($join = false){
		return self::get($join);
	}
	/**
	 * エイリアス：書き込み
	 */
	public function write($str){
		return self::set($str);
	}
	/**
	 * 特殊：文字列化（readと等価）
	 * @return string
	 */
	public function __toString(){
		return $this->get(true);
	}
}

/* End of file File.php */
/* Location: /vender/PukiWiki/Lib/File/File.php */
