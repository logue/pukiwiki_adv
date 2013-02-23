<?php
/**
 * ファイルクラス
 *
 * @package   PukiWiki\File
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2013 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: File.php,v 1.0.0 2013/01/29 19:54:00 Logue Exp $
 */

namespace PukiWiki\File;

use SplFileInfo;
use PukiWiki\Utility;
use PukiWiki\Time;
use PukiWiki\File\FileUtility;

/**
 * ファイルの読み書きを行うクラス
 */
class File extends SplFileInfo{
	const LOCK_FILE = 'chown.lock';
	const LINE_BREAK = "\n";

	public $filename;

	/**
	 * コンストラクタ
	 * @param string $filename ファイル名（パスも含めること）
	 */
	public function __construct($filename) {
		if (empty($filename)){
			throw new \Exception('File name is missing!');
		}
		if (!is_string($filename)){
			throw new \Exception('File name must be string!');
		}
		$this->filename = $filename;
		parent::__construct($filename);
	}
	/**
	 * ファイルが存在するか
	 * @return boolean
	 */
	public function has(){
		return $this->isFile();
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
		if ( !$this->isReadable() )
			Utility::dieMessage(sprintf('File <var>%s</var> is not readable.', Utility::htmlsc($this->filename)));

		// ファイルの読み込み
		$file = $this->openFile('r');
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
		return $join || $count !== 1 ? join(self::LINE_BREAK, $result) : $result;
	}
	/**
	 * ファイルの内容を取得
	 * @param boolean $join 行を含めた文字列として読み込むか、行を配列として読み込むかのフラグ
	 * @param boolean $legacy 昔の読み込み方でロード（互換性のため）
	 * @return string or array
	 */
	public function get($join = false, $legacy = false){
		if ( !$this->isFile() ) return false;
		if ( !$this->isReadable() )
			Utility::dieMessage(sprintf('File <var>%s</var> is not readable.', Utility::htmlsc($this->filename)));

		// ファイルの読み込み
		$file = $this->openFile('r');
		// ロック
		$file->flock(LOCK_SH);
		// 巻き戻し（要るの？）
		$file->rewind();
		// 初期値
		$result = array();
		// 1行毎ファイルを読む
		while (!$file->eof()) {
			$line = $file->fgets();

			if ($legacy) {
				$result[] = strtr($line, "\r", '');
			}else if (!empty($line) ){
				// こっちの処理のほうが高速
				if ($line[0] === self::LINE_BREAK){
					// 改行のみの場合空白
					$result[] = '';
				}else{
				// 改行を含む末尾の余計な空白文字は削除
				// （ただし先頭の1文字目は、スペースやタブの場合があるため除外）
					$result[] = $line[0] . rtrim(substr($line, 1));
				}
			}
		}
		// アンロック
		$file->flock(LOCK_UN);
		// 念のためオブジェクトを開放
		unset($file);

		// 出力
		return $join ? join(self::LINE_BREAK, $result) : $result;
	}
	/**
	 * ファイルの書き込み処理
	 * @param string or array $str 書き込む文字列
	 * @return int 書き込んだバイト数
	 */
	public function set($str, $keeptimestamp = false){
		// ファイルが存在しない作成
		if ( !$this->isFile() ) $this->touch();

		// 書き込み可能かをチェック
		if (! $this->isWritable())
			Utility::dieMessage(sprintf('File <var>%s</var> is not writable.', Utility::htmlsc($this->filename)));

		// 書き込むものがなかった場合、削除とみなす
		if (empty($str)) return $this->remove();

		// タイムスタンプを取得
		if ($keeptimestamp) $timestamp = self::getTime();

		if (!is_array($str)){
			// 入力データが配列でない場合、念のため改行で分割
			$str = explode(self::LINE_BREAK, $str );
		}

		// 改行を含む末尾の余計な空白文字は削除
		// （ただし先頭の1文字目は、スペースやタブの場合があるため除外）
		foreach ($str as $line){
			$data[] = (!empty($line)) ? $line[0] . rtrim(substr($line, 1)) : '';
		}
		unset($str);

		// ファイルを読み込み
		$file = $this->openFile('w');
		// ロック
		$file->flock(LOCK_EX);
		// 書き込む
		$ret = $file->fwrite(join("\n",$data));
		// アンロック
		$file->flock(LOCK_UN);

		// タイムスタンプを保持する場合
		if ($keeptimestamp){
			self::setTime($timestamp);
		}else{
			FileUtility::clearCache();
		}

		// 念のためオブジェクトを開放
		unset($file);

		return $ret;
	}
	/**
	 * ハッシュ
	 * @return string
	 */
	public function digest(){
		return $this->isFile() ? md5($this->get(true)) : null;
	}
	/**
	 * ファイルを削除
	 * @return int
	 */
	public function remove(){
		FileUtility::clearCache();
		return $this->isFile() ? unlink($this) : false;
	}
	/**
	 * 更新時刻を設定／取得
	 * @param type $time
	 * @return int
	 */
	public function time($time = ''){
		if (empty($time)){
			return $this->isFile() ? $this->getMTime() : 0;
		}else{
			return $this->touch($time);
		}
	}
	/**
	 * ファイルの経過時間を取得
	 * @return string
	 */
	public function passage(){
		return Time::passage($this->time());
	}
	/**
	 * ファイルの所有者変更
	 * @param boolean $preserve_time 時刻を保持
	 * @return boolean
	 */
	private function chown($preserve_time = TRUE){
		// check UID（Windowsの場合は0になる）
		$this->php_uid = extension_loaded('posix') ? posix_getuid() : 0;
		$lockfile = new SplFileInfo(CACHE_DIR . self::LOCK_FILE);

		// Lock for pkwk_chown()
		$lock = $lockfile->openFile('a');
		$lock->flock(LOCK_EX);

		// ファイルが作成されてないとエラーになる
		touch($this->filename);

		// Check owner
		$owner = $this->getOwner();
		if ($owner === $this->php_uid) {
			// NOTE: Windows always here
			$result = TRUE; // Seems the same UID. Nothing to do
		} else {
			$tmp = tmpfile();

			// Lock source $filename to avoid file corruption
			// NOTE: Not 'r+'. Don't check write permission here
			$file = $this->openFile('r');

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
			Utility::dieMessage('pkwk_touch_file(): Invalid UID and (not writable for the directory or not a flie): ' .
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
}

/* End of file File.php */
/* Location: /vender/PukiWiki/Lib/File/File.php */
