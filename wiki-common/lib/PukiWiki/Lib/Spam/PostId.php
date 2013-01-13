<?php
/**
 * POST data check function $Id$
 *
 * PukioWikio - A WikiWikiWeb clone.
 *  A custom version of PukiWiki.
 *
 * @copyright  &copy; 2008 PukioWikio Developers Team
 * @license GPL v2 or (at your option) any later version
 */
namespace PukiWiki\Lib\Spam;

use Zend\Math\Rand;

class PostId{
	// POSTIDの有効期間
	const POSTID_SESSION_EXPIRE = 3600;	// 60*60 = 1hour

	// POSTIDの接頭辞
	const POSTID_SESSION_PREFIX = 'postid-';

	/**
	 * PostIdを生成する
	 * @param $cmd saltとして使用する文字列（通常はプラグイン名）
	 * @return string
	 */
	public static function generate($cmd = ''){
		global $session;
		$idstring = md5($cmd . Rand::getFloat());
		// PostIDの値の中身は、ホストを入力
		$session->offsetSet(self::POSTID_SESSIO_PREFIX.$idstring, REMOTE_ADDR);
		// 有効期限を設定
		$session->setExpirationSeconds(self::POSTID_SESSION_EXPIRE, self::POSTID_SESSION_PREFIX.$idstring);
		return $idstring;
	}

	/**
	 * PostIDをチェックする
	 * @param string $idstring PostIdの名前
	 * @param boolean
	 */
	public static function check($idstring){
		global $session;
		$ret = FALSE;
		if ($session->offsetExists(self::POSTID_SESSION_PREFIX.$idstring) && $session->offsetGet(self::POSTID_SESSION_PREFIX.$idstring) === REMOTE_ADDR){
			// PostIdを削除
			$session->offsetUnset(self::POSTID_SESSION_PREFIX.$idstring);
			return true;
		}
		//	honeypot_write();
		return false;
	}
}
