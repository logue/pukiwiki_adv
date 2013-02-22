<?php
namespace PukiWiki\Lib;

use PukiWiki\Lib\File\FileFactory;

/**
 * バックアップのコントローラー
 */
class Backup{

	public function __construct($page){
		$this->page = $page;
		// 以下はSplFileInfoの派生クラス
		$this->wiki = FileFactory::Wiki($this->page);
		$this->backup = FileFactory::Backup($this->page);
	}
	/**
	 * バックアップを作成する
	 *
	 * @access    public
	 * @param     Boolean   $delete      TRUE:バックアップを削除する
	 *
	 * @return    Void
	 */
	public function set(){
		// ページが存在しない場合、バックアップ作成しない。
		if (! $this->wiki->has() ) return;

		// バックアップに追記するデータ
		$newdata = $this->wiki->get(true);

		if ($this->backup->has()){
			// バックアップ新規作成
			return $this->backup->set(self::SPLITTER . ' ' . $this->wiki->time() . ' ' . UTIME . "\n" . $newdata);
		}else if (! $this->time == 0 || (UTIME - $this->backup->time > $this->backup->cycle) ){
			// 連続更新があった場合に備えて、バックアップを作成するまでのインターバルを設ける
			return;
		}
		// 現在のバックアップを取得
		$backups = $this->get();
		$count   = count($backups) + 1;

		// 直後に1件追加するので、(最大件数 - 1)を超える要素を捨てる
		if ($count > $this->maxage)
			array_splice($backups, 0, $count - $this->maxage);

		// バッグアップデーターをパース
		$strout = '';
		foreach($backups as $age=>$data) {
			// BugTrack/685 by UPK
			$strout .= self::BACKUP_SPLITTER . ' ' . $data['time'] . ' ' . $data['real'] . "\n"; // Splitter format
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
	public function get($age = 0){
		$_age = 0;
		$retvars = $match = array();
		if ($this->backup->has()){
			foreach($this->backup->get() as $line) {
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
	public function remove($ages = array()){
		if($ages === array()) {
			// バックアップファイルの削除
			return $this->backup->remove();
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
			$this->backup->set(preg_replace("/([^\n])\n*$/", "$1\n", $strout));
		}
	}
	/**
	 * バックアップファイルが存在するか
	 * @return    Boolean   TRUE:ある FALSE:ない
	 */
	public function has(){
		return $this->backup->has();
	}
}