<?php
/**
 * AuthFile.php
 *
 * @copyright
 *	 Copyright &copy; 2010 PukiPlus Developers Team
 *					  2006, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @version	$Id: log.php,v 0.9 2010/07/11 11:38:00 Logue Exp $
 *
 */
namespace PukiWiki\File;

use Exception;
use PukiWiki\Auth\Auth;
use PukiWiki\File\File;

/**
 * 認証ユーザファイルクラス
 */
class AuthFile extends File
{
	/**
	 * 変更なし
	 */
	const USER_NOTCHANGED = 0;
	/**
	 * 追加
	 */
	const USER_ADD = 1;
	/**
	 * パスワードのみ変更
	 */
	const USER_PASS_CHANGED = 2;
	/**
	 * 両方変更
	 */
	const USER_CHANGED = 3;
	/**
	 * 認証ユーザ
	 */
	private $auth_users;

	/**
	 * コンストラクタ
	 */
	function __construct($name='auth_users')
	{
		$this->filename = DATA_HOME . $name.'.ini.php';
		$this->auth_users = parent::has() ? include($this->filename) : array();
		parent::__construct($this->filename);
	}

	/**
	 * 書き込む（元の実装に忠実？）
	 * @global type $adminname
	 * @return type
	 */
	function set()
	{
		global $adminname;
		if ($this->auth_users == array()) return;
		// 管理人の設定は省く
		unset($this->auth_users[$adminname]);

		$lines[] = '<?php';
		$lines[] = 'use PukiWiki\Auth\Auth;';
		$lines[] = 'return array(';
		$lines[] = "\t".'$adminname	=> array($adminpass, Auth::ROLE_ADMIN),';	// 管理人は、ここでハードコーディング

		foreach($this->auth_users as $user=>$val) {
			$line = "\t".'\''.$user.'\' => array('."\n".$val[0];

			for ($i=1;$i<count($val);$i++){
				if (! empty($val[$i])) {
					$line .= ',' . $val[$i];
				}
			}

			$line .= "),";
			$lines[] = $line;
		}
		$lines[] = ');';

		parent::set($lines);
	}
	/**
	 * パスワードを設定
	 * @param string $user ユーザ名
	 * @param string $passwd パスワード
	 * @param int $role 役割
	 * @return
	 */
	function setPassword($user,$passwd,$role='')
	{
		// 1:追加
		if (empty($this->auth_users[$user])) {
			$this->auth_users[$user][self::USER_NOTCHANGED] = $passwd;
			if (! empty($role)) {
				$this->auth_users[$user][self::USER_ADD] = $role;
			}
			return self::USER_ADD;
		}

		$tmp_role = empty($this->auth_users[$user][self::USER_ADD]) ? null : $this->auth_users[$user][self::USER_ADD];

		// 0:変更なし
		if ($this->auth_users[$user][self::USER_NOTCHANGED] == $passwd && $tmp_role == $role) return 0;

		// 2:パスワード変更あり 3:変更あり
		$this->write = TRUE;
		$rc = $this->auth_users[$user][self::USER_NOTCHANGED] !== $passwd ? self::USER_PASS_CHANGED : self::USER_CHANGED;

		$this->auth_users[$user][self::USER_NOTCHANGED] = $passwd;
		$this->auth_users[$user][self::USER_ADD] = $role;
		return $rc;
	}
	/**
	 * ユーザのパスワードを取得
	 * @param string $user ユーザ名
	 * @return array
	 */
	function getPassword ($user) {
		if (empty($this->auth_users[$user])) {
			// scheme, salt, role
			return array(null,null,null);
		}
		$role = empty($this->auth_users[$user][self::USER_ADD]) ? null : $this->auth_users[$user][self::USER_ADD];
		list($scheme, $salt) = Auth::passwd_parse($this->auth_users[$user][self::USER_NOTCHANGED]);
		return array($scheme, $salt, $role);
	}
}
	

/* End of file AuthFile.php */
/* Location: ./vendor/PukiWiki/File/AuthFile.php */