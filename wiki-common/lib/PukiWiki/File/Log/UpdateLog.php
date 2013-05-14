<?php
/**
 * 更新ログファイルクラス
 *
 * @package   PukiWiki\File\Log
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2013 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: UpdateLog.php,v 1.0.0 2013/01/29 19:54:00 Logue Exp $
 */
namespace PukiWiki\File\Log;

use PukiWiki\File\File;
use PukiWiki\File\LogFile;

/**
 * 更新ログファイルクラス
 */
class UpdateLog extends LogFile{
	function __construct($page) {
		$this->kind = 'update';
		$this->isWiki = false;
		parent::__construct($page);
	}

	public function set($data){
		if ($this->config['guess_user']['use']){
			self::log_put_guess($data);
		}
		parent::set($data);
	}

	/*
	 * 推測ユーザデータの出力
	 */
	private function log_put_guess($data)
	{
		// ユーザを推測する
		$user = self::guess_user( $data['user'], $data['ntlm'], $data['sig'] );
		if (empty($user)) return;

		$file = new File($this->dir . 'guess_user.log');

		if (!$file->has()) {
			// 最初の１件目
			$data = self::array2table( array( $data['ua'], $data['host'], $user,null ) );
			$file->set($data);
			return;
		}

		$sw = FALSE;

		foreach($file->get() as $line) {
			$field = self::table2array($line);	// PukiWiki 表形式データを配列データに変換
			if (count($field) == 0) continue;
			if ($field[0] != $data['ua']  ) continue;
			if ($field[1] != $data['host']) continue;
			if ($field[2] != $user        ) continue;
			$sw = TRUE;
			break;
		}
		if ($sw) return; // 既に存在
		// データの更新
		$data = self::array2table( array( $data['ua'], $data['host'], $user, null ) );
	 	$file->set();
	}
}

/* End of file UpdateLog.php */
/* Location: /vendor/PukiWiki/Lib/File/UpdateLog.php */
