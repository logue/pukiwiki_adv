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

use DirectoryIterator;
use Exception;
use PukiWiki\File\FileUtility;
use PukiWiki\Time;
use PukiWiki\Utility;
use SplFileInfo;

/**
 * ファイルの読み書きを行うクラス
 */
abstract class AbstractFile extends SplFileInfo{
	/**
	 * ロックファイル名
	 */
	const LOCK_FILE = 'chown.lock';
	/**
	 * 改行
	 */
	const LINE_BREAK = "\n";
	/**
	 * ファイル一覧キャッシュの接頭辞
	 */
	const EXISTS_CACHE_PREFIX = 'exists-';
	/**
	 * デフォルトのファイルのパーミッション
	 */
	const FILE_PERMISSION = 0644;
	/**
	 * 対象ディレクトリ
	 */
	public static $dir = '';
	/**
	 * デフォルトのファイル名のマッチパターン
	 */
	public static $pattern = '/^((?:[0-9A-F]{2})+)\.txt$/';
	/**
	 * ファイル名
	 */
	public $filename;
	

	/**
	 * コンストラクタ
	 * @param string $filename ファイル名（パスも含めること）
	 */
	public function __construct($filename = null) {
		if (empty($filename)){
			throw new Exception('AbstractFile::__construct(): File name is missing!');
		}
		if (!is_string($filename)){
			throw new Exception('AbstractFile::__construct(): File name must be string!');
		}
		$this->filename = $filename;
		parent::__construct($filename);
	}
	/**
	 * ファイル一覧を取得
	 * @param string $pattern ファイルのマッチパターン
	 * @return array
	 */
	public static function getPages($pattern = ''){
		$ret = array();
		// 継承元のクラス名を取得（PHPは、__CLASS__で派生元のクラス名が取得できない）
		$class =  get_called_class();
		// パターンが指定されていない場合は、クラスで定義されているデフォルトのパターンを使用
		if ( empty($pattern) ) $pattern = $class::$pattern;
		// クラスでディレクトリが定義されていないときは処理しない。(AuthFile.phpなど）
		if ( empty($class::$dir)) return array();

		foreach (self::exists() as $file) {
			$matches = array();
			if (preg_match($pattern, $file, $matches)){
				$ret[] = Utility::decode($matches[1]);
			}
		}
		return $ret;
	}
	/**
	 * ファイル一覧を取得
	 * @param boolean $force キャッシュを再生成する
	 * @param boolean $clearOnly キャッシュのクリアのみ
	 * @return array
	 */
	public static function exists($force = false, $clearOnly = false){
		static $files;
		global $cache;

		// 継承元のクラス名を取得（PHPは、__CLASS__で派生元のクラス名が取得できない）
		$class =  get_called_class();
		// クラスでディレクトリが定義されていないときは処理しない。(AuthFile.phpなど）
		if ( empty($class::$dir)) return array();
		// キャッシュ名
		$cache_name = self::EXISTS_CACHE_PREFIX . strtolower(substr(strrchr($class, '\\'),1,4));

		if (!$force && !$clearOnly) {
			// ディレクトリの更新チェック（変更があった場合、キャッシュを再生成）
			if ($cache['wiki']->hasItem($cache_name)){
				$cache_meta = $cache['wiki']->getMetadata($cache_name);
				if ($cache_meta['mtime'] < filemtime($class::$dir)) {
					$force = true;
				}
			}
		}

		// キャッシュ処理
		if ($force || $clearOnly) {
			unset($files);
			$cache['wiki']->removeItem($cache_name);
			if ($clearOnly) return;
		}else if (!empty($files)) {
			return $files;
		}else if ($cache['wiki']->hasItem($cache_name)) {
			$files = $cache['wiki']->getItem($cache_name);
			$cache['wiki']->touchItem($cache_name);
			return $files;
		}

		// ファイル一覧を走査（
		foreach (new DirectoryIterator($class::$dir) as $fileinfo) {
			if (!$fileinfo->isFile() || !$fileinfo->isReadable()) continue;
			$files[] = $fileinfo->getFilename();
		}
		unset($fileinfo);

		// キャッシュに保存
		$cache['wiki']->setItem($cache_name, $files);
		return $files;
	}
	/**
	 * ファイル一覧キャッシュ削除
	 */
	public static function clearCache(){
		self::exists(true, true);
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
			throw new Exception(sprintf('AbstractFile::get(): File %s is not readable.', $this->getRealPath()));

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
			Utility::dieMessage(sprintf('AbstractFile::get(): File %s is not readable.', $this->getRealPath()));

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
	 * @param string or array $str 書き込む文字列。空欄時は削除
	 * @return int 書き込んだバイト数
	 */
	public function set($str = '', $keeptimestamp = false){
		// 書き込むものがなかった場合、削除とみなす
		if (empty($str)) return $this->remove();

		// ファイルが存在しない作成
		if ( !$this->isFile() ) $this->touch();

		// 書き込み可能かをチェック
		if (! $this->isWritable())
			Utility::dieMessage(sprintf('AbstractFile::set(): File %s is not writable.', $this->getRealPath()));

		// タイムスタンプを取得
		if ($keeptimestamp) $timestamp = self::getTime();

		if (!is_array($str)){
			// 入力データが配列でない場合、念のため改行で分割
			$x = preg_replace(
				array("[\\r\\n]","[\\r]"),
				array(self::LINE_BREAK,self::LINE_BREAK),
				$str
			); // 行末の統一
			$str = explode(self::LINE_BREAK,$x);
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

		if ($ret !== false){
			// タイムスタンプを保持する場合
			if ($keeptimestamp){
				self::setTime($timestamp);
			}else{
				FileUtility::clearCache();
			}
		}
		// 念のためオブジェクトを開放
		unset($file);

		return $ret;
	}
	/**
	 * ファイルの要約
	 * @return string
	 */
	public function digest(){
		return $this->isFile() ? md5($this->get(true)) : null;
	}
	/**
	 * ファイルのMD5ハッシュを取得
	 * @return string
	 */
	public function md5() {
		return $this->isFile() ? md5_file($this->filename) : null;
	}
	/**
	 * ファイルのSHA1ハッシュを取得
	 * @return string
	 */
	public function sha1() {
		return $this->isFile() ? sha1_file($this->filename) : null;
	}
	/**
	 * ファイルを削除
	 * @return boolean
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
		}
		return $this->touch($time);
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
		$php_uid = extension_loaded('posix') ? posix_getuid() : 0;

		if (!$this->has()){
			$this->touch();
		}

		// Check owner
		$owner = $this->getOwner();
		if ($owner === $php_uid) {
			// NOTE: Windows always here
			return; // Seems the same UID. Nothing to do
		}else{
			try{
				chown($this->getRealPath(), $php_uid);
				// 念のためパーミッションを変更（通常は0644）
				chmod($this->getRealPath(), self::FILE_PERMISSION);
			}catch(Exception $e){
				Utility::dieMessage(sprintf('AbstractFile::touch(): File %s is invalid UID and (not writable for the directory or not a flie).' ,$this->getRealPath()));
			}
		}
	}
	/**
	 * ファイルの更新日時を修正
	 *
	 * @param int $time 更新日時
	 * @param int $atime アクセス日時
	 * @return boolean
	 */
	public function touch($time = FALSE, $atime = FALSE){
		if ($time === FALSE) {
			// ファイルの領域を確保
			$result = touch($this->getRealPath());
		} else if ($atime === FALSE) {
			// ファイルの更新日時を指定して領域を確保
			$result = touch($this->getRealPath(), $time);
		} else {
			// ファイルの更新日時とアクセス日時を指定して領域を確保
			$result = touch($this->getRealPath(), $time, $atime);
		}
		return $result;
	}
	/**
	 * 名前変更
	 * string $to 変更先の名前
	 * @return boolean
	 */
	public function rename($to){
		if (empty($to)) Utility::dieMessage('AbsructFile::rename(): New name is undefined.');
		FileUtility::clearCache();
		return rename($this->filename, $to);
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
		return $this->get($join);
	}
	/**
	 * エイリアス：書き込み
	 */
	public function write($str){
		return $this->set($str);
	}
	/**
	 * エイリアス：削除
	 */
	public function delete(){
		return $this->remove();
	}
}

/* End of file File.php */
/* Location: /vender/PukiWiki/Lib/File/File.php */
