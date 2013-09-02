<?php
namespace PukiWiki;

use PukiWiki\File\FileFactory;

/**
 * バックアップのコントローラー
 */
class Backup{
	// バックアップの世代ごとの区切り文字（default.ini.php）
	const SPLITTER = '>>>>>>>>>>';
	// バックアップのインターバル（１時間）
	const BACKUP_INTERVAL = 360;

	public function __construct($page){
		global $cycle, $maxage;
		$this->page = $page;
		// バックアップの頻度
		//$this->cycle = 0;
		$this->cycle = !empty($cycle) ? 60 * 60 * $cycle : self::BACKUP_INTERVAL;
		// バックアップの上限個数
		$this->maxage = $maxage;
		// 以下はSplFileInfoの派生クラス
		$this->wiki = FileFactory::Wiki($this->page);
		$this->backup = FileFactory::Backup($this->page);
		// バックアップの世代間の区切りの正規表現
		$this->splitter_reglex = '/^(' . preg_quote(self::SPLITTER) . '\s\d+(\s(\d+)|))$/';
	}
	/**
	 * バックアップを作成する
	 * @param string $newdata バックアップに記載するソース
	 * @return    Void
	 */
	public function set(){
		// ページが存在しない場合、バックアップ作成しない。
		if (! $this->wiki->has() ) return;
		
		// バックアップを取るWikiデーター
		$newdata = join("\n",$this->wiki->get())."\n";

		if (! $this->backup->has()){
			// バックアップが存在しない場合、バックアップ新規作成
			return $this->backup->set(self::SPLITTER . ' ' . $this->wiki->time() . ' ' . UTIME . "\n" . $newdata);
		}else if (! UTIME - $this->backup->time() > $this->cycle){
			// 連続更新があった場合に備えて、バックアップを作成するまでのインターバルを設ける
			return;
		}

		// 現在のバックアップを取得
		$backups = $this->get();

		// 直後に1件追加するので、(最大件数 - 1)を超える要素を捨てる
		if (count($backups) + 1 > $this->maxage)
			array_splice($backups, 0, $count - $this->maxage);

		// バッグアップデーターをパース
		$pastdata = '';
		foreach($backups as $age=>$data) {
			// BugTrack/685 by UPK
			$pastdata .= self::SPLITTER . ' ' . $data['time'] . ' ' . $data['real'] . "\n"; // Splitter format
			$pastdata .= join("\n", $data['data'])."\n";
			unset($backups[$age]);
		}
		$pastdata = preg_replace('/([^\n])\n*$/', "$1\n", $pastdata);

		// 追加するバックアップデーター
		$adddata =
			self::SPLITTER . ' ' . $this->wiki->time() . ' ' . UTIME . "\n" .
			preg_replace($this->backup->splitter_reglex, '$1 ', $newdata);	// BugTrack/685 by UPK

		$adddata = preg_replace('/\n*$/', "\n", $adddata);

		//echo '<pre>'.$pastdata.$adddata.'</pre>';
		// 書き込む
		return $this->backup->set($pastdata . $adddata);
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
				if ( preg_match($this->backup->splitter_reglex, $line, $match) ) {
					// A splitter, tells new data of backup will come
					++$_age;
					if ($age > 0 && $_age > $age) return $retvars[$age];

					// BugTrack/685 by UPK
					// 実際ページを保存した時間が指定されている場合（タイムスタンプを更新しないをチェックして更新した場合）
					// そちらのパラメータをバックアップの日時として使用する。
					$now = (isset($match[3]) && $match[2] !== $match[3]) ? $match[3] : $match[2];

					// Allocate
					$retvars[$_age] = array('time'=>trim($match[2]), 'real'=>trim($now), 'data'=>array());
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
			$backups = self::get();
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