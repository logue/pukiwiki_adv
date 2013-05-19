<?php
/**
 * みなしユーザログファイルクラス
 *
 * @package   PukiWiki\File\Log
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013 PukiWiki Advance Developers Team
 * @create    2013/05/14
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: GuessLog.php,v 1.0.0 2013/05/14 16:34:00 Logue Exp $
 */
namespace PukiWiki\File\Log;

use PukiWiki\File\LogFile;

/**
 * みなしユーザログファイルクラス（Wikiに保存するためGuessLog()->get()は使わないこと。）
 */
class GuessLog extends LogFile{
	public function __construct($page = null) {
		$this->kind = 'guess_user';
		$this->isWiki = true;
		parent::__construct(null);
	}
	/**
	 * 推測ユーザデータの出力
	 */
	public function set($data){
		// ユーザを推測する
		$user = self::guess_user( $data['user'], $data['ntlm'], $data['sig'] );
		if (empty($user)) return;

		if ($this->has()){
			// ログが存在する場合前のデーターを読み込んで重複を確認
			$sw = FALSE;
		
			foreach($this->get() as $_src) {
				$x = trim($_src);
				$field = parent::table2array($x);	// PukiWiki 表形式データを配列データに変換
				if (count($field) == 0) continue;
				if ($field[0] != $data['ua']  ) continue;
				if ($field[1] != $data['host']) continue;
				if ($field[2] != $user        ) continue;
				$sw = TRUE;
				break;
			}
			if ($sw) return;	// 重複
		}
		// 追記するデーター
		$data = parent::array2table( array( $data['ua'], $data['host'], $user, '' ) );
		return parent::set($data);
	}
	/**
	 * ユーザを推測する
	 * @static
	 */
	private static function guess_user($user,$ntlm,$sig)	{
		if (!empty($user)) return $user; // 署名ユーザ
		if (!empty($ntlm)) return $ntlm; // NTLM認証ユーザ
		if (!empty($sig))  return $sig;  // 本人の署名
		return null;
	}
}

/* End of file GuessLog.php */
/* Location: /vendor/PukiWiki/File/Log/GuessLog.php */
