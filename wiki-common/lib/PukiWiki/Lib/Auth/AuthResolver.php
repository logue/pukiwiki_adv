<?php
namespace PukiWiki\Lib\Auth;
use Zend\Authentication\Adapter\Http\ResolverInterface;

class AuthResolver implements ResolverInterface{
	/**
	 * 
	 * @param string $username ユーザ名
	 * @param string $realm 役割（未使用）
	 * @param string $password パスワード
	 * @return ユーザのパスワード
	 */
	public function resolve($username, $realm, $password = null)
	{
		global $auth_users;	// auth_users.ini.phpで定義
		
		// 定義されていないユーザ名
		if (!isset($auth_users[$username])) {
			// scheme, salt, role
			return false;
		}
		return $auth_users[$username];
	}
}