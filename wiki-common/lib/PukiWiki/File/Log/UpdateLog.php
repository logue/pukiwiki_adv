<?php
/**
 * 更新ログファイルクラス
 *
 * @package   PukiWiki\File
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2013 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: WikiFile.php,v 1.0.0 2013/01/29 19:54:00 Logue Exp $
 */
namespace PukiWiki\File\Log;

use PukiWiki\File\LogFile;

/**
 * 更新ログファイルクラス
 */
class UpdateLog extends LogFile{
	public static $kind = 'update';

	public function set(){
		if ($this->config['guess_user']['use']){
			// ユーザを推測する
			$user = self::guess_user( $rc['user'], $rc['ntlm'], $rc['sig'] );
			if (empty($user)) return;

			$filename = log::set_filename('guess_user','');	// ログファイル名

			if (file_exists($filename)) {
				$src = file( $filename );			// ログの読み込み
			} else {
				// 最初の１件目
				$data = log::array2table( array( $data['ua'], $data['host'], $user,"" ) );
				log_put( $filename, $data);
				return;
			}

			$sw = FALSE;

			foreach($src as $_src) {
				$x = trim($_src);
				$field = log::table2array($x);	// PukiWiki 表形式データを配列データに変換
				if (count($field) == 0) continue;
				if ($field[0] != $data['ua']  ) continue;
				if ($field[1] != $data['host']) continue;
				if ($field[2] != $user        ) continue;
				$sw = TRUE;
				break;
			}
			if ($sw) return; // 既に存在
			// データの更新
			$data = log::array2table( array( $data['ua'], $data['host'], $user,'' ) );
		}
		parent::set();
	}
}

/* End of file WikiFile.php */
/* Location: /vender/PukiWiki/Lib/File/WikiFile.php */
