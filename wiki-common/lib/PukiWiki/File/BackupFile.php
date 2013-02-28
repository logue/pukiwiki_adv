<?php
/**
 * バックアップファイルクラス
 *
 * @package   PukiWiki\File
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2013 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: BackupFile.php,v 1.0.0 2013/02/06 07:25:00 Logue Exp $
 */

namespace PukiWiki\File;

use PukiWiki\Auth\Auth;
use PukiWiki\Factory;
use PukiWiki\File\File;
use PukiWiki\Utility;

/**
 * バックアップファイルクラス
 */
class BackupFile extends File{
	// バックアップの世代ごとの区切り文字（default.ini.php）
	const SPLITTER = '>>>>>>>>>>';
	// バックアップで利用可能な圧縮形式
	protected $available_ext = array('.lzf', '.bz2', '.gz', '.txt');
	public $splitter_reglex;
	private $page, $ext = '.txt', $name, $time, $cycle, $maxage;
	public static $dir = BACKUP_DIR;
	public static $pattern = '/^((?:[0-9A-F]{2})+)\.(txt|gz|bz2|lzf)$/';

	/**
	 * コンストラクタ
	 * @param string $page ページ名
	 */
	public function __construct($page){
		global $do_backup, $cycle, $maxage;
		if (Auth::check_role('readonly') || ! $do_backup) return;

		// バックアップのページ名
		$this->page = $page;
		// バックアップの拡張子
		if (function_exists('lzf_compress')){
			// lzfが使用出来る場合
			$this->ext = '.lzf';
		}else if (function_exists('bzcompress')){
			// bz2が使用出来る場合
			$this->ext = '.bz2';
		} else if (function_exists('gzcompress')){
			$this->ext = '.gz';
		}
		// バックアップの世代間の区切りの正規表現
		$this->splitter_reglex = '/^(' . preg_quote(self::SPLITTER) . '\s\d+(\s(\d+)|))$/';
		// バックアップの名前（拡張子抜き）
		$this->name = self::$dir . Utility::encode($page);
		// バックアップの最終更新日時
		$this->time = $this->has() ? filemtime($this->filename) : UTIME;	// このhasBackup()でファイル名（$this->file）も定義
		
		
		parent::__construct($this->name.$this->ext);
	}

	/**
	 * バックアップを作成する
	 *
	 * @access    public
	 * @param     Boolean   $delete      TRUE:バックアップを削除する
	 *
	 * @return    Void
	 */
	public function setBackup(){
		$wiki = Factory::Wiki($this->page);
		// ページが存在しない場合、バックアップ作成しない。
		if (! $wiki->has() ) return;

		// バックアップに追記するデータ
		$newdata = $wiki->get(true);

		if (!self::has()){
			// バックアップ新規作成
			return self::set(self::SPLITTER . ' ' . $wiki->time() . ' ' . UTIME . "\n" . $newdata);
		}else if (! $this->time == 0 || (UTIME - $this->time > $this->cycle) ){
			// 連続更新があった場合に備えて、バックアップを作成するまでのインターバルを設ける
			return;
		}
		// 現在のバックアップを取得
		$backups = self::getBackup();
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
		$strout = preg_replace('/([^\n])\n*$/', "$1\n", $strout);

		// 追加するバックアップデーター
		// Escape 'lines equal to self::SPLITTER', by inserting a space
		$body = preg_replace($this->splitter_reglex, '$1 ', $newdata);
		// BugTrack/685 by UPK
		$body = self::SPLITTER . ' ' . $wiki->time() . ' ' . UTIME . "\n" . $body;
		$body = preg_replace('/\n*$/', "\n", $body);

//		pr($body. $strout);
//		die();
		// 先頭に追記して書き込む
		return self::set($body. $strout);
	}
	/**
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
		if (self::has()){
			foreach(self::get() as $line) {
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
					$retvars[$_age]['data'][] = rtrim($line);
				}
			}
		}
		return $retvars;
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
			return $this->remove();
		} else {
			// バックアップから指定世代のみ削除
			$backups = self::getBackup();
			if (is_array($ages)){
				foreach($ages as $age) {
					unset($backups[$age]);
				}
			}else{
				unset($backups[$ages]);
			}
			// 指定世代を削除したバックアップを書き込む
			$strout = '';
			foreach($backups as $age=>$data) {
				$strout .= self::SPLITTER . ' ' . $data['time'] . ' ' . $data['real'] . "\n"; // Splitter format
				$strout .= join("\n", $data['data']);
			}
			self::set(preg_replace("/([^\n])\n*$/", "$1\n", $strout));
		}
	}
	/**
	 * バックアップファイルが存在するか
	 *
	 * @access    private
	 *
	 * @return    Boolean   TRUE:ある FALSE:ない
	 */
	public function has(){
		// 設定を途中で変えたり、後方互換性のため拡張子ごとにチェックする
		foreach ($this->available_ext as $ext){
			$file = realpath($this->name.$ext);
			if (file_exists($file)){
				$this->filename = $file;
				return true;
			}
		}
	}
	/**
	 * バックアップファイルの内容を取得する
	 *
	 * @return    Array     ファイルの内容
	 */
	public function get($join = false, $legacy = false){
		foreach ($this->available_ext as $ext){
			if (file_exists($this->name.$ext)) return $this->read_sub($ext, $join);
		}
		return $join ? '' :array();
	}
	/**
	 * write()
	 * バックアップファイルに書き込む
	 * TODO:gzとbz2圧縮がスレッドセーフでない
	 * @access    private
	 * @param     String    $content 文字列
	 *
	 * @return    Boolean   FALSE:失敗 その他:書き込んだバイト数
	 */
	public function set($data, $keeptimestamp = false){
		// 古いバックアップを削除（追記する実装でないため）
		$this->remove();

		$file = $this->name.$this->ext;
		// 書き込み
		touch($file);
		$bytes = '';

		switch ($this->ext) {
			case '.txt' :
				$bytes = parent::set($data);
				break;
			case '.gz':
				$handle = gzopen($file,'w');
				if ($handle) {
					$bytes = gzwrite($handle, $data);
					gzclose($handle);
				}else{
					return false;
				}
				break;
			case '.bz2':
				$handle = bzopen($file,'w');
				if ($handle) {
					$bytes = bzwrite($handle, $data);
					bzclose($handle);
				}else{
					return false;
				}
				break;
			case '.lzf':
				$bytes = parent::set(lzf_compress($data), false);
			break;
		}
		return $bytes;
	}
	public function remove(){
		foreach ($this->available_ext as $ext){
			if (file_exists($this->name.$ext)) unlink($this->name.$ext);
		}
	}
	/**
	 * バックアップファイルの内容を圧縮形式に応じて取得する
	 * TODO:gzとbz2解凍がスレッドセーフでない
	 * @access    private
	 *
	 * @return    Array     ファイルの内容
	 */
	private function read_sub($ext, $join){
		// ファイルを取得
		$data = '';
		switch ($ext) {
			case '.txt' :
				$data = parent::get();
				break;
			case '.lzf' :
				$data = lzf_decompress(parent::get());
				break;
			case '.gz':
				$handle = gzopen($this->filename, 'r');
				while (!gzeof($handle)) {
					$data .= gzread($handle, 1024);
				}
				gzclose($handle);
				break;
			case '.bz2':
				$handle = bzopen($this->filename, 'r');
				while (!feof($handle)) {
					$data .= bzread($handle, 1024);
				}
				bzclose($handle);
				break;
		}
		// 取得した内容を改行ごとに配列として返す
		return ($join) ? $data : explode("\n",$data);
	}
	/**
	 * 現在との差分
	 * @param $age
	 * @return string
	 */
	public function getDiff($age=0){
		$new = Factory::Wiki($this->page)->source();
		$old = self::getBackup($age);
		$diff = new Diff($new, $old);
		return $diff->getHtml();
	}
}

/* End of file BackupFile.php */
/* Location: /vender/PukiWiki/Lib/File/BackupFile.php */
