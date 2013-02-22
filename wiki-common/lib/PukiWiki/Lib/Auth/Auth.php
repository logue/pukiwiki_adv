<?php
/**
 * PukiWiki Advance 認証処理
 *
 * @author  Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @version $Id: auth.cls.php,v 0.69 2010/06/15 00:34:00 upk Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License (GPL2)
 */

namespace PukiWiki\Lib\Auth;

use PukiWiki\Lib\Renderer\RendererFactory;
use PukiWiki\Lib\Utility;
use PukiWiki\Lib\Factory;
use PukiWiki\Lib\File\FileUtility;
use Zend\Crypt\BlockCipher;


/**
 * 認証クラス
 * @abstract
 */
class Auth
{
	// 管理人のグループ名
	const ADMIN_GROUP = 'Administrator';
	// パスワードの最大長
	const PASSPHRASE_LIMIT_LENGTH = 512;
	
	const TEMP_CONTENTS_ADMIN_NAME = 'admin';
	// ゲスト
	const ROLE_GUEST = 0;
	// 強制モード
	const ROLE_FORCE = 1;
	// サイト管理者
	const ROLE_ADMIN = 2;
	// コンテンツ管理者
	const ROLE_CONTENTS_ADMIN = 3;
	// 登録者
	const ROLE_ENROLLEE = 4;
	// 認証者
	const ROLE_AUTH = 5;
	// 見做し認証者
	const ROLE_AUTH_TEMP = 5.1;
	// OpenID認証者
	const ROLE_AUTH_OPENID = 5.2;

	/**
	 * 管理人ログイン（非推奨）
	 * @param string $pass パスワード
	 * @return boolean
	 */
	public static function login($pass = '') {
		global $adminpass;

		if (! self::check_role('readonly') && isset($adminpass) &&
			self::hash_compute($pass, $adminpass) === $adminpass) {
			return TRUE;
		}
		sleep(2);       // Blocking brute force attack
		return FALSE;
	}
	/**
	 * ユーザのパスワードを出力する（{スキーム}[ハッシュ化されたパス]）
	 * @param string $phrase パスフレーズ
	 * @param string $scheme スキーム（値の最初に来るパスワードの形式）
	 * @param boolean $prefix スキームを出力するか
	 * @param boolean $canonical スキームを含んだ状態で保存するか
	 * @param string
	 */
	public static function hash_compute($phrase = '', $scheme = '{php_md5}', $prefix = TRUE, $canonical = FALSE)
	{
		if (! is_string($phrase) || ! is_string($scheme)) return FALSE;

		if (strlen($phrase) > self::PASSPHRASE_LIMIT_LENGTH)
			Utility::dieMessage('pkwk_hash_compute(): malicious message length');

		// With a {scheme}salt or not
		$matches = array();
		if (preg_match('/^(\{.+\})(.*)$/', $scheme, $matches)) {
			$scheme = & $matches[1];
			$salt   = & $matches[2];
		} else if ($scheme != '') {
			$scheme  = ''; // Cleartext
			$salt    = '';
		}

		// Compute and add a scheme-prefix
		switch (strtolower($scheme)) {

			// PHP crypt()
			case '{x-php-crypt}' :
				$hash = ($prefix ? ($canonical ? '{x-php-crypt}' : $scheme) : '') .
					($salt != '' ? crypt($phrase, $salt) : crypt($phrase));
				break;

			// PHP md5()
			case '{x-php-md5}'   :
				$hash = ($prefix ? ($canonical ? '{x-php-md5}' : $scheme) : '') .
					md5($phrase);
				break;

			// PHP sha1()
			case '{x-php-sha1}'  :
				$hash = ($prefix ? ($canonical ? '{x-php-sha1}' : $scheme) : '') .
					sha1($phrase);
				break;

			// LDAP CRYPT
			case '{crypt}'       :
				$hash = ($prefix ? ($canonical ? '{CRYPT}' : $scheme) : '') .
					($salt != '' ? crypt($phrase, $salt) : crypt($phrase));
				break;

			// LDAP MD5
			case '{md5}'         :
				$hash = ($prefix ? ($canonical ? '{MD5}' : $scheme) : '') .
					base64_encode(hex2bin(md5($phrase)));
				break;

			// LDAP SMD5
			case '{smd5}'        :
				// MD5 Key length = 128bits = 16bytes
				$salt = ($salt != '' ? substr(base64_decode($salt), 16) : substr(crypt(''), -8));
				$hash = ($prefix ? ($canonical ? '{SMD5}' : $scheme) : '') .
					base64_encode(hex2bin(md5($phrase . $salt)) . $salt);
				break;

			// LDAP SHA
			case '{sha}'         :
				$hash = ($prefix ? ($canonical ? '{SHA}' : $scheme) : '') .
					base64_encode(hex2bin(sha1($phrase)));
				break;

			// LDAP SSHA
			case '{ssha}'        :
				// SHA-1 Key length = 160bits = 20bytes
				$salt = ($salt != '' ? substr(base64_decode($salt), 20) : substr(crypt(''), -8));
				$hash = ($prefix ? ($canonical ? '{SSHA}' : $scheme) : '') .
					base64_encode(hex2bin(sha1($phrase . $salt)) . $salt);
				break;

			// LDAP CLEARTEXT and just cleartext
			case '{cleartext}'   : /* FALLTHROUGH */
			case ''              :
				$hash = ($prefix ? ($canonical ? '{CLEARTEXT}' : $scheme) : '') . $phrase;
				break;

			// Invalid scheme
			default:
				$hash = FALSE;
				break;
		}

		return $hash;
	}
	

	public static function edit_auth($page, $auth_flag = TRUE, $exit_flag = TRUE)
	{
		global $edit_auth, $edit_auth_pages, $auth_api, $defaultpage, $_title, $edit_auth_pages_accept_ip;

		if (self::check_role('readonly')) return false;

		if (!$edit_auth) return true;

		// 許可IPの場合チェックしない
		if(self::ip_auth($page, $auth_flag, $exit_flag, $edit_auth_pages_accept_ip, $_title['cannotedit'])) {
			return TRUE;
		}

		$info = self::get_user_info();
		if (!empty($info['key']) &&
		    self::is_page_readable($page, $info['key'], $info['group']) &&
		    self::is_page_editable($page, $info['key'], $info['group'])) {
			return true;
		}

		// Basic, Digest 認証を利用していない場合
		if (!$auth_api['plus']['use']) return self::is_page_readable($page, '', '');

		$auth_func_name = self::get_auth_func_name();
		if ($auth_flag && ! $auth_func_name($page, $auth_flag, $exit_flag, $edit_auth_pages, $_title['cannotedit'])) return false;
		if (self::is_page_readable($page, '', '') && self::is_page_editable($page,'','')) return true;

		if ($exit_flag) {
			// 無応答
			if (PKWK_WARNING){
				die_message('You have no permission to edit this page.');
			}else{
				header( 'Location: ' . get_page_location_uri($defaultpage));
				die();
			}
		}
		return false;
	}
	public static function is_page_editable($page,$uname,$gname='')
	{
		global $edit_auth, $edit_auth_pages;
		global $read_auth, $read_auth_pages;
		if (! self::is_page_auth($page, $read_auth, $read_auth_pages, $uname, $gname)) return false;
		return self::is_page_auth($page, $edit_auth, $edit_auth_pages, $uname, $gname);
	}

	public static function read_auth($page, $auth_flag = TRUE, $exit_flag = TRUE)
	{
		global $read_auth, $read_auth_pages, $auth_api, $defaultpage, $_title, $read_auth_pages_accept_ip;

		if (!$read_auth) return true;

		// 許可IPの場合チェックしない
		if(self::ip_auth($page, $auth_flag, $exit_flag, $read_auth_pages_accept_ip, $_title['cannotread'])) {
			return TRUE;
		}

		$info = self::get_user_info();
		if (!empty($info['key']) &&
		    self::is_page_readable($page, $info['key'], $info['group'])) {
			return true;
		}

		if (!$auth_api['plus']['use']) return self::is_page_readable($page, '', '');

		$auth_func_name = self::get_auth_func_name();
		// 未認証時で認証不要($auth_flag)であっても、制限付きページかの判定が必要
		if ($auth_flag && ! $auth_func_name($page, $auth_flag, $exit_flag, $read_auth_pages, $_title['cannotread'])) return false;
		return self::is_page_readable($page, '', '');

		if ($exit_flag) {
			if (PKWK_WARNING){
				die_message('You have no permission to read this page.');
			}else{
				// 無応答
				header( 'Location: ' . get_page_location_uri($defaultpage));
				die();
			}
		}
		return false;
	}

	public static function is_page_readable($page,$uname,$gname='')
	{
		global $read_auth, $read_auth_pages;
		return self::is_page_auth($page, $read_auth, $read_auth_pages, $uname, $gname);
	}
	

	public static function get_auth_func_name()
	{
		global $auth_type;
		$namespace = 'PukiWiki\Lib\Auth\Auth';
		switch ($auth_type) {
			case 1: return $namespace.'basic_auth';
			case 2: return $namespace.'digest_auth';
		}
		return $namespace.'basic_auth';
	}

	public static function auth($page, $auth_flag, $exit_flag, $auth_pages, $title_cannot){
		global $auth_type;
		switch ($auth_type) {
			case 1: return self::basic_auth($page, $auth_flag, $exit_flag, $auth_pages, $title_cannot);
			case 2: return self::digest_auth($page, $auth_flag, $exit_flag, $auth_pages, $title_cannot);
		}
	}

	

	/**
	 * Basic認証
	 * @param string $page
	 * @param
	 * @return boolean
	 */
	public static function basic_auth($page, $auth_flag, $exit_flag, $auth_pages, $title_cannot)
	{
		global $auth_users, $auth_method_type;
		global $realm;

		if (self::is_page_auth($page, $auth_flag, $auth_pages, null,null)) return true; // No limit
		$user_list = $auth_users;

		if (! self::check_role('role_adm_contents')) return TRUE; // 既にコンテンツ管理者

		$matches = array();
		if (! isset($_SERVER['PHP_AUTH_USER']) &&
			! isset($_SERVER ['PHP_AUTH_PW']) &&
			isset($_SERVER['HTTP_AUTHORIZATION']) &&
			preg_match('/^Basic (.*)$/', $_SERVER['HTTP_AUTHORIZATION'], $matches))
		{

			// Basic-auth with $_SERVER['HTTP_AUTHORIZATION']
			list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) =
				explode(':', base64_decode($matches[1]));
		}

		if (! isset($_SERVER['PHP_AUTH_USER']) ||
			! in_array($_SERVER['PHP_AUTH_USER'], $user_list) ||
			! isset($auth_users[$_SERVER['PHP_AUTH_USER']]) ||
			self::_hash_compute(
				$_SERVER['PHP_AUTH_PW'],
				$auth_users[$_SERVER['PHP_AUTH_USER']][0]
				) !== $auth_users[$_SERVER['PHP_AUTH_USER']][0])
		{
			// Auth failed
			if ($auth_flag || $exit_flag) {
				pkwk_common_headers();
			}
			if ($auth_flag) {
				header('WWW-Authenticate: Basic realm="'.$realm.'"');
				header('HTTP/1.0 401 Unauthorized');
			}
			if ($exit_flag) {
				$body = $title = str_replace('$1',
					htmlsc(strip_bracket($page)), $title_cannot);
				$page = str_replace('$1', make_search($page), $title_cannot);
				catbody($title, $page, $body);
				exit;
			}
			return FALSE;
		} else {
			return TRUE;
		}
	}

	// Digest authentication
	public static function digest_auth($page, $auth_flag, $exit_flag, $auth_pages, $title_cannot)
	{
		global $auth_users, $auth_method_type, $auth_type;
		global $realm;

		if (self::is_page_auth($page, $auth_flag, $auth_pages, '','')) return true; // No limit
		//$user_list = get_auth_page_users($page, $auth_pages);
		//if (empty($user_list)) return true; // No limit

		if (! self::check_role('role_adm_contents')) return true; // 既にコンテンツ管理者
		if (self::auth_digest($auth_users)) return true;

		// Auth failed
		if ($auth_flag || $exit_flag) {
			pkwk_common_headers();
		}
		if ($auth_flag) {
			header('HTTP/1.1 401 Unauthorized');
			header('WWW-Authenticate: Digest realm="'.$realm.
				'", qop="auth", nonce="'.uniqid().'", opaque="'.md5($realm).'"');
		}
		if ($exit_flag) {
			$body = $title = str_replace('$1',
				htmlsc(strip_bracket($page)), $title_cannot);
			$page = str_replace('$1', make_search($page), $title_cannot);
			catbody($title, $page, $body);
			exit;
		}
		return false;
	}

	// http://lsx.sourceforge.jp/?Hack%2Fip_auth
	// IP authentication. allows ip without basic_auth
	public static function ip_auth($page, $auth_flag, $exit_flag, $auth_pages_accept_ip, $title_cannot)
	{
		global $auth_method_type;

		$auth = FALSE;
		if (is_array($auth_pages_accept_ip)){

			$remote_addr = isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR'];

			// Checked by:
			$target_str = '';
			if ($auth_method_type == 'pagename') {
				$target_str = $page; // Page name
			} else if ($auth_method_type == 'contents') {
				$target_str = Factory::Wiki($page)->source(); // Its contents
			}

			$accept_ip_list = array();
			foreach($auth_pages_accept_ip as $key=>$val)
				if (preg_match($key, $target_str))
					$accept_ip_list = array_merge($accept_ip_list, explode(',', $val));

			if (!empty($accept_ip_list)) {
				if(isset($remote_addr)) {
					foreach ($accept_ip_list as $ip) {
						if(strpos($remote_addr, $ip) !== false) {
							$auth = TRUE;
							break;
						}
					}
				}
			}
		}
		return $auth;
	}
	
	
	/*
	 *	== IIS ==
	 *	AUTH_USER		- 認証ユーザ名
	 *	AUTH_TYPE		- 認証タイプ
	 *	HTTP_AUTHORIZATION	- パスワードのダイジェスト
	 *	LOGON_USER		- サーバへのログオンユーザ名
	*/

	/*
	 * 認証者名を取得
	 * @static
	 */
	public static function check_auth()
	{
		$login = self::check_auth_pw();
		if (! empty($login)) return $login;

		// 外部認証API
		$auth_key = self::get_user_name();

		// 暫定管理者(su)
		global $vars;
		if (! isset($vars['pass'])) return $auth_key['nick'];
		if (self::login($vars['pass'])) return self::TEMP_CONTENTS_ADMIN_NAME;
		return $auth_key['nick'];
	}

	/**
	 * パスワードチェック
	 * @global type $auth_type
	 * @return type
	 */
	static function check_auth_pw()
	{
		global $auth_type;

		$login = '';
		switch ($auth_type) {
			case 1:
				$login = self::check_auth_basic();
				break;
			case 2:
				$login = self::check_auth_digest();
				break;
		}

		if (! empty($login)) return $login;

		// NTLM対応
		list($domain, $login, $host, $pass) = self::ntlm_decode();
		return $login;
	}
	/**
	 * BASIC認証
	 * @global type $auth_users
	 * @return string
	 */
	static function check_auth_basic()
	{
		global $auth_users;

		$user = '';
		foreach (array('PHP_AUTH_USER', 'AUTH_USER', 'REMOTE_USER', 'LOGON_USER') as $x) {
			if (isset($_SERVER[$x]) && ! empty($_SERVER[$x])) {
				// Digest だったら確実
				if (! empty($_SERVER['AUTH_TYPE']) && $_SERVER['AUTH_TYPE'] == 'Digest') {
					$user = $_SERVER[$x];
					break;
				}
				// ドメイン認証の確認
				$ms = explode('\\', $_SERVER[$x]);
				if (count($ms) == 3) {
					$user = $ms[2]; // DOMAIN\\USERID
					break;
				}
				// この変数の内容で確定する
				$user = $_SERVER[$x];
				break;
			}
		}
		if (empty($user)) return '';

		// 未定義ユーザは、サーバ側で認証時または、ＯＳでの認証時のワークグループ接続的なイメージ
		if (!isset($auth_users[$user])) return $user;

		// 定義ユーザならパスワードのチェックを行う
		$pass = '';
		foreach (array('PHP_AUTH_PW', 'AUTH_PASSWORD', 'HTTP_AUTHORIZATION') as $pw) {
			//if (! empty($_SERVER[$pw])) return $_SERVER[$x];
			if (isset($_SERVER[$pw]) && ! empty($_SERVER[$pw])) {
				$pass = $_SERVER[$pw];
				break;
			}
		}
                if (empty($pass)) return '';
		if (empty($auth_users[$user][0])) return ''; // パスワードが空は除く
		return (self::hash_compute($pass,$auth_users[$user][0]) === $auth_users[$user][0]) ? $user : '';
	}
	/**
	 * Digest認証
	 * @global \PukiWiki\Lib\Auth\type $auth_users
	 * @return string
	 */
	static function check_auth_digest()
	{
		global $auth_users;

		if (! self::auth_digest($auth_users)) return '';
		$data = self::http_digest_parse($_SERVER['PHP_AUTH_DIGEST']);
		if (! empty($data['username'])) return $data['username'];
		return '';
	}
	/**
	 * ユーザ情報
	 * @return type
	 */
	public static function get_user_info()
	{
		// Array ( [role] => 0 [nick] => [key] => [group] => [displayname] => [api] => )
		$retval = self::get_auth_pw_info();
		if (!empty($retval['api'])) {
			return $retval;
		}
		return self::get_auth_api_info();
	}

	public static function get_auth_pw_info()
	{
		global $auth_users, $defaultpage;
		$retval = array(
			'role'=>self::ROLE_GUEST,
			'nick'=>null,
			'key'=>null,
			'api'=>null,
			'group'=>null,
			'displayname'=>null,
			'home'=>null,
			'mypage'=>null
		);
		$user = self::check_auth_pw();
		if (empty($user)) return $retval;

		$retval['api'] = 'plus';
		$retval['key'] = $retval['nick'] = $user;

		// 登録者かどうか
		if (empty($auth_users[$user])) {
			// 未登録者の場合
			// 管理者パスワードと偶然一致した場合でも見做し認証者(ROLE_AUTH_TEMP)
			$retval['role']  = self::ROLE_AUTH_TEMP;
			return $retval;
		}

		$retval['role']  = (empty($auth_users[$user][1])) ? self::ROLE_ENROLLEE : $auth_users[$user][1];
		$retval['group'] = (empty($auth_users[$user][2])) ? null : $auth_users[$user][2];
		$retval['home']  = (empty($auth_users[$user][3])) ? $defaultpage : $auth_users[$user][3];
		$retval['mypage']= (empty($auth_users[$user][4])) ? null : $auth_users[$user][4];
		return $retval;
	}

	static function get_auth_api_info()
	{
		global $auth_api, $auth_wkgrp_user, $defaultpage;

		$auth_key = array(
			'role'=>self::ROLE_GUEST,
			'nick'=>null,
			'key'=>null,
			'api'=>null,
			'group'=>null,
			'displayname'=>null,
			'home'=>null,
			'mypage'=>null
		);

		foreach($auth_api as $api=>$val) {
			// どうしても必要な場合のみ開始
			if (! $val['use']) continue;
			break;
		}

		$obj = new AuthApi();
		$msg = $obj->auth_session_get();
		if (isset($msg['api']) && $auth_api[$msg['api']]['use']) {
			if (exist_plugin($msg['api'])) {
				$call_func = 'plugin_'.$msg['api'].'_get_user_name';
				$auth_key = $call_func();
				$auth_key['api'] = $msg['api'];
				if (empty($auth_key['nick'])) return $retval;

				// 上書き・追加する項目
				if (! empty($auth_wkgrp_user[$auth_key['api']][$auth_key['key']])) {
					$val = & $auth_wkgrp_user[$auth_key['api']][$auth_key['key']];
					$auth_key['role']
						= (empty($val['role'])) ? self::ROLE_ENROLLEE : $val['role'];
					$auth_key['group']
						= (empty($val['group'])) ? null : $val['group'];
					$auth_key['displayname']
						= (empty($val['displayname'])) ? null : $val['displayname'];
					$auth_key['home']
						= (empty($val['home'])) ? $defaultpage : $val['home'];
					$auth_key['mypage']
						= (empty($val['mypage'])) ? null : $val['mypage'];
				}
			}
		}
		return $auth_key;
	}

	public static function get_user_name()
	{
		$auth_key = self::get_user_info();
		if (empty($auth_key['nick'])) return $auth_key;
		if (! empty($auth_key['displayname'])) {
			$auth_key['nick'] = $auth_key['displayname'];
		}
		return $auth_key;
	}

	/**
	 * ユーザのROLEを取得
	 * @static
	 */
	static function get_role_level()
	{
		$info = self::get_user_info();
		return $info['role'];
	}

	/*
	 * 指定されるROLEに属するユーザを列挙
	 * @static
	 */
	static function get_user_list($role)
	{
		global $auth_users;
		$rc = array();
		foreach($auth_users as $user=>$val)
		{
			$def_role = (empty($val[1])) ? self::ROLE_AUTH : $val[1];
			if ($def_role > $role) continue;
			$rc[] = $user;
		}

		$now_role = self::get_role_level();
		// if (($now_role == ROLE_AUTH_TEMP && $role == ROLE_AUTH) || ($now_role == ROLE_ADM_CONTENTS_TEMP && $role == ROLE_ADM_CONTENTS))
		if (($now_role == self::ROLE_AUTH_TEMP && $role == self::ROLE_AUTH))
		{
			$rc[] = self::check_auth();
		}

		return $rc;
	}

	/*
	 * 管理者パスワードなのかどうか
	 * @return bool
	 * @static
	 */
	public static function is_temp_admin()
	{
		global $adminpass;
		// 管理者パスワードなのかどうか？
		$temp_admin = ( self::hash_compute($_SERVER['PHP_AUTH_PW'], $adminpass) !== $adminpass) ? false : true;
		if (! $temp_admin && $login == self::TEMP_CONTENTS_ADMIN_NAME) {
			global $vars;
			if (isset($vars['pass']) && self::login($vars['pass'])) $temp_admin = true;
		}
		return $temp_admin;
	}

	/*
	 * ROLEに応じた挙動の確認
	 * @return bool
	 * @static
	 */
	static function check_role($func='')
	{
		global $adminpass;

		switch($func) {
		case 'readonly':
			$chk_role = (defined('PKWK_READONLY')) ? PKWK_READONLY : self::ROLE_GUEST;
			break;
		case 'safemode':
			$chk_role = (defined('PKWK_SAFE_MODE')) ? PKWK_SAFE_MODE : self::ROLE_GUEST;
			break;
		case 'su':
			$now_role = self::get_role_level();
			if ($now_role == 2 || (int)$now_role == self::ROLE_CONTENTS_ADMIN) return FALSE; // 既に権限有
			$chk_role = self::ROLE_CONTENTS_ADMIN;
			switch ($now_role) {
			case self::ROLE_AUTH_TEMP:
				// FIXME:
				return TRUE;
			case self::ROLE_GUEST:
				// 未認証者は、単に管理者パスワードを要求
				$user = self::TEMP_CONTENTS_ADMIN_NAME;
				break;
			case self::ROLE_ENROLLEE:
			case self::ROLE_AUTH:
				// 認証済ユーザは、ユーザ名を維持しつつ管理者パスワードを要求
				$user = self::check_auth();
				break;
			}
			$auth_temp = array($user => array($adminpass) );

			while(1) {
				if (!self::auth_pw($auth_temp))
				{
					unset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
					header( 'WWW-Authenticate: Basic realm="USER NAME is '.$user.'"' );
					header( 'HTTP/1.0 401 Unauthorized' );
					break;
				}
				// ESC : 認証失敗
				return TRUE;
			}
			break;
		case 'role_admin':
		case 'role_adm':
			$chk_role = self::ROLE_ADMIN;
			break;
		case 'role_adm_contents':
			// 互換性のため
		//	trigger_error('check_role(\'role_adm_contents\') is not recommond. Instead use check_role(\'role_contents_admin\').', E_USER_DEPRECATED);
		case 'role_contents_admin':
			$chk_role = self::ROLE_CONTENTS_ADMIN;
			break;
		case 'role_enrollee':
			$chk_role = self::ROLE_ENROLLEE;
			break;
		case 'role_auth':
			$chk_role = self::ROLE_AUTH;
			break;
		default:
			$chk_role = self::ROLE_GUEST;
		}

		return self::is_check_role($chk_role);
	}

	public static function is_check_role($chk_role)
	{
		static $now_role;
		if ($chk_role == self::ROLE_GUEST) return FALSE;      // 機能無効
		if ($chk_role == self::ROLE_FORCE) return TRUE;       // 強制

		// 役割に応じた挙動の設定
		if (!isset($now_role)) $now_role = (int)self::get_role_level();
		if ($now_role == self::ROLE_GUEST) return TRUE;
		return ($now_role <= $chk_role) ? FALSE : TRUE;
	}

	/**
	 * NTLM, Negotiate 認証 (IIS 4.0/5.0)
	 * @static
	 */
	static function auth_ntlm()
	{
		if (!isset($_SERVER['HTTP_AUTHORIZATION'])) return 0;
		$http_auth = $_SERVER['HTTP_AUTHORIZATION'];

		if ($http_auth === NULL){
			header( 'HTTP/1.0 401 Unauthorized' );
			header( 'WWW-Authenticate: NTLM' );
			exit;
		}

		list($auth_type,$digest64) = explode(' ',$http_auth);
		switch( strtoupper($auth_type) ) {
			case 'NTLM':      // IIS 4.0
				return 1;
			case 'NEGOTIATE': // IIS 5.0 ('Negotiate')
				return 2;

			// IIS用 phpMyAdmin-2.6.2-pl1/libraries/auth/http.auth.lib.php
			case 'BASIC':     // 'Basic'
				if (!function_exists('base64_decode')) return array('','');
				return explode(':', base64_decode(substr($http_auth, 6)));
		}

		$digest = 'NTL' . base64_decode( substr($digest64 ,4) );

		if (ord($digest{8})  != 1  ) return 0;
		if (ord($digest[13]) != 178) return 0;

		$strAuth = 'NTLMSSP'
			. chr(0) . chr(2) . chr(0) . chr(0) . chr(0)
			. chr(0) . chr(0) . chr(0) . chr(0) . chr(40)
			. chr(0) . chr(0) . chr(0) . chr(1) . chr(130)
			. chr(0) . chr(0) . chr(0) . chr(2) . chr(2)
			. chr(2) . chr(0) . chr(0) . chr(0) . chr(0)
			. chr(0) . chr(0) . chr(0) . chr(0) . chr(0)
			. chr(0) . chr(0) . chr(0);

		$strAuth64 = trim(base64_encode($strAuth));
		header( 'HTTP/1.0 401 Unauthorized' );
		header( 'WWW-Authenticate: NTLM '. $strAuth64 );
		exit;

		return 0;
	}

	/**
	 * HTTP_AUTHORIZATION の解読
	 * @static
	 */
	static function ntlm_decode()
	{
		$rc = array('','','','');
		if (!function_exists('base64_decode')) return $rc;
		if (!isset($_SERVER['HTTP_AUTHORIZATION'])) return $rc;
		$http_auth = $_SERVER['HTTP_AUTHORIZATION'];

		list($auth_type,$x) = explode(' ', $http_auth);

		switch( strtoupper($auth_type) ) {
			// IIS用 (http://homepage1.nifty.com/yito/namazu/gbook/20021127.1530.html)
			case 'BASIC':     // 'Basic'
				list($login, $pass) = explode(':', base64_decode(substr($http_auth, 6)));
				return array('',$login,'', $pass);
			case 'NTLM':      // IIS 4.0
				break;
			case 'NEGOTIATE': // IIS 5.0 ('Negotiate')
				break;
			default:
				return $rc;
		}

		$x = 'NTL' . base64_decode( substr($x,4) );

		if(ord($x{8}) != 3) return $rc;

		$rc = array();
		for ($i=30; $i<=46; $i += 8)
		{
			// domain login host
			$len    = (ord($x[$i+1])*256 + ord($x[$i  ]));	// 31,30  39,38 47,46
			$offset = (ord($x[$i+3])*256 + ord($x[$i+2]));	// 33,32  41,40 49,48
			$rc[] = substr($x, $offset, $len);
		}
		$rc[] = ''; // pass
		return $rc; // domain, login, hostname, pass
	}

	/**
	 * 認証 (PukiWikiの設定に準ずる)
	 * @static
	 */
	static function auth_pw($auth_users)
	{
		$user = '';
		foreach (array('PHP_AUTH_USER', 'AUTH_USER') as $x) {
			if (isset($_SERVER[$x])) {
				$ms = explode('\\', $_SERVER[$x]);
				if (count($ms) == 3) {
					$user = $ms[2]; // DOMAIN\\USERID
				} else {
					$user = $_SERVER[$x];
				}
				break;
			}
		}

		$pass = '';
		foreach (array('PHP_AUTH_PW', 'AUTH_PASSWORD', 'HTTP_AUTHORIZATION') as $x) {
			if (! empty($_SERVER[$x])) {
				if ($x == 'HTTP_AUTHORIZATION') {
					// NTLM対応 (domain, login, host, pass)
					$tmp_ntlm = self::ntlm_decode();
					if ($tmp_ntlm[3] == '') continue;
					if (empty($user)) $user = $tmp_ntlm[1];
					$pass = $tmp_ntlm[3];
					unset($tmp_ntml);
					break;
				}
				$pass = $_SERVER[$x];
				break;
			}
		}

		if (empty($user) && empty($pass)) return false;
		if (empty($auth_users[$user][0])) return false;
		if ( self::hash_compute($pass, $auth_users[$user][0]) !== $auth_users[$user][0]) return false;
		return true;
	}

	static function auth_digest($auth_users)
	{
		if (! isset($_SERVER['PHP_AUTH_DIGEST']) || empty($_SERVER['PHP_AUTH_DIGEST'])) return false;
		$data = self::http_digest_parse($_SERVER['PHP_AUTH_DIGEST']);
		if ($data === false) return false;

		list($scheme, $salt, $role) = self::get_data($data['username'], $auth_users);
		if ($scheme != '{x-digest-md5}') return false;

		// $A1 = md5($data['username'] . ':' . $realm . ':' . $auth_users[$data['username']]);
		$A1 = $salt;
		$A2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
		$valid_response = md5($A1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$A2);
		if ($data['response'] != $valid_response) return false;
		return true;
	}

	/**
	 * PHP_AUTH_DIGEST 変数をパースする関数
	 * function to parse the http auth header
	 * @static
	 */
	static function http_digest_parse($txt)
	{
		// protect against missing data
		$needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
		$data = array();

		// url に含まれる文字列を含む必要がある
		// preg_match_all('@(\w+)=([\'"]?)([a-zA-Z0-9=./\_-]+)\2@', $txt, $matches, PREG_SET_ORDER);
		// preg_match_all('@(\w+)=([\'"]?)([a-zA-Z0-9=./%&\?\_-_+]+)\2@', $txt, $matches, PREG_SET_ORDER);
		preg_match_all('@(\w+)=([\'"]?)([a-zA-Z0-9=./%&\?\_-]+)\2@', $txt, $matches, PREG_SET_ORDER);

		foreach ($matches as $m) {
			$data[$m[1]] = $m[3];
			unset($needed_parts[$m[1]]);
		}

		return $needed_parts ? FALSE : $data;
	}

	/**
	 * データの分解
	 * @static
	 */
	static function get_data($user,$auth_users)
	{
		if (!isset($auth_users[$user])) {
			// scheme, salt, role
			return array('','','');
		}

		$role = (empty($auth_users[$user][1])) ? '' : $auth_users[$user][1];
		list($scheme,$salt) = self::passwd_parse($auth_users[$user][0]);
		return array($scheme,$salt,$role);
	}

	/**
	 * PukiWiki Passwd の分解
	 * @static
	 */
	static function passwd_parse($passwd)
	{
		$regs = array();
		if (preg_match('/^(\{.+\})(.*)$/', $passwd, $regs)) {
			return array($regs[1], $regs[2]);
		}
		return array('',$passwd);
	}

	/**
	 * 署名の抽出
	 * @static
	 */
	static function get_signature($lines)
	{
		$patterns = array(
			"'.*? -- \[\[(.*?)\]\] &new{.*?};'si",	// -- [[xxx]] &new{xxx};
			"'.*? -- (.*?) &new{.*?};'si",		// -- xxx &new{xxx};
			"'.*? - \[\[(.*?)\]\] &new{.*?}'si",	// - [[xxx]] &new{xxx};
			"'.*? - (.*?) &new{.*?}'si",		// - xxx &new{xxx};
			"'.*? -- \[\[(.*?)\]\]'si",		// -- [[xxx]]
			"'.*? -- \[(.*?)\]'si",			// -- [xxx]
			"'.*? -- (.*?)'si",			// -- xxx
		);

		foreach ($lines as $_line) {
			foreach ($patterns as $pat) {
				if (preg_match($pat,$_line,$regs)) {
					return $regs[1];
				}
			}
		}
		return '';
	}
	
	public static function get_existpages($dir = DATA_DIR, $ext = '.txt')
	{
		$rc = array();

		// ページ名の取得
		$pages = FileUtility::getExists($dir);

		// $pages = get_existpages($dir, $ext);
		// コンテンツ管理者以上は、: のページも閲覧可能
		$has_permisson = self::check_role('role_adm_contents');

		// 役割の取得
		// $now_role = self::get_role_level();

		$rc = array();
		if (is_array($pages)){
			foreach($pages as $file=>$page) {
				$wiki = Factory::Wiki($page);
				// 存在しない場合、当然スルー
				if (!$wiki->has()) continue;
				// 隠しページの場合かつ、隠しページを表示できる権限がない場合スルー
				if ($wiki->isHidden() && $has_permisson) continue;
				// 閲覧できる権限がない場合はスルー
				if (! $wiki->isReadable()) continue;

				$rc[$file] = $page;
			}
		}
		ksort($rc, SORT_NATURAL);
		return $rc;
	}


	public static function is_role_page($lines)
	{
		global $check_role;
		if (! $check_role) return FALSE;
		$cmd = use_plugin('check_role',$lines);
		if ($cmd === FALSE) return FALSE;
		RendererFactory::factory($cmd); // die();
		return TRUE;
	}
	/**
	 * セッションを取得
	 * @param string $session_name セッション名
	 * @param string
	 */
	public static function des_session_get($session_name)
	{
		global $adminpass, $session;

		// adminpass の処理
		list($scheme, $salt) = self::passwd_parse($adminpass);

		// des化された内容を平文に戻す
		if ($session->offsetExists($session_name)) {
			//require_once(LIB_DIR . 'des.php');
			//return des($salt, base64_decode($session->offsetGet($session_name), 0, 0, null) );
			$blockCipher = BlockCipher::factory('mcrypt', array(
				'algo' => 'des',
				'mode' => 'cfb',
				'hash' => 'sha512',
				'salt' => $salt,
				'padding' => 2
			));
			return $blockCipher->decrypt($session->offsetGet($session_name));
		}
		return '';
	}
	/**
	 * セッションを設定
	 * @param string $session_name セッション名
	 * @param string $val セッションの値
	 * @param string
	 */
	public static function des_session_put($session_name, $val)
	{
		global $adminpass, $session;

		// adminpass の処理
		list($scheme, $salt) = self::passwd_parse($adminpass);
		//require_once(LIB_DIR . 'des.php');
		//$session->offsetSet($session_name, base64_encode( des($salt, $val, 1, 0, null) ) );
//		session_write_close();
		$blockCipher = BlockCipher::factory('mcrypt', array(
			'algo' => 'des',
			'mode' => 'cfb',
			'hash' => 'sha512',
			'salt' => $salt,
			'padding' => 2
		));
		$result = $blockCipher->encrypt($val);
		$session->offsetSet($session_name, $result);
		return $blockCipher;
	}
	/**
	 * セッションを破棄
	 * @param string $session_name セッション名
	 * @param string $val セッションの値
	 * @param string
	 */
	public static function des_session_unset($session_name){
		global $session;
		$session->offsetUnset($session_name);
	}

	// See:
	// Web Services Security: UsernameToken Profile 1.0
	// http://www.xmlconsortium.org/wg/sec/oasis-200401-wss-username-token-profile-1.0-jp.pdf
	function wsse_header($uid,$pass)
	{
		$nonce = hex2bin(md5(rand().UTIME));
		$created = gmdate('Y-m-d\TH:i:s\Z',UTIME);
		$digest = self::b64_sha1($nonce.$created.$pass);
		return 'UsernameToken Username="'.$uid.'", PasswordDigest="'.$digest.'", Nonce="'.base64_encode($nonce).'", Created="'.$created.'"';
	}

	function b64_sha1($x)
	{
		return base64_encode(hex2bin(sha1($x)));
	}

	public static function is_protect_plugin_action($x)
	{
		global $auth_api;
		static $plugin_list = array('login','redirect');

		foreach($plugin_list as $val) {
			if ($val == $x) return true;
		}

		foreach($auth_api as $api=>$val) {
			if ($api == $x) return true;
		}

		// auth_X plugin OK.
		if (strpos($x,'auth_') === 0 && strlen($x) > 5) return true;
        	return false;
	}

	public static function is_protect()
	{
		return (PLUS_PROTECT_MODE && self::is_check_role(PLUS_PROTECT_MODE));
	}

	function user_list()
	{
		global $auth_users, $auth_wkgrp_user, $defaultpage;
		$rc = array();

		foreach ($auth_users as $user=>$val) {
			$role  = (empty($val[1])) ? self::ROLE_ENROLLEE : $val[1];
			$group = (empty($val[2])) ? '' : $val[2];
			$home  = (empty($val[3])) ? $defaultpage : $val[3];
			$mypage= (empty($val[4])) ? '' : $val[4];
			$rc['plus'][$user] = array('role'=>$role,'displayname'=>$user,'group'=>$group,'home'=>$home,'mypage'=>$mypage);
		}

		foreach($auth_wkgrp_user as $api=>$val1) {
			foreach($val1 as $user=>$val) {
				if (is_array($val)) {
					$role  = (empty($val['role'])) ? self::ROLE_ENROLLEE : $val['role'];
					$group = (empty($val['group'])) ? '' : $val['group'];
					$name  = (empty($val['displayname'])) ? $user : $val['displayname'];
					$home  = (empty($val['home'])) ? $defaultpage : $val['home'];
					$mypage= (empty($val['mypage'])) ? '' : $val['mypage'];
				} else {
					$role = $val;
					$group = '';
					$name = $user;
					$home = $defaultpage;
					$mypage = '';
				}
				$rc[$api][$user] = array('role'=>$role, 'displayname'=>$name,'group'=>$group,'home'=>$home,'mypage'=>$mypage);
			}
		}
		return $rc;
	}
}

/* End of file auth.cls.php */
/* Location: ./wiki-common/lib/auth.cls.php */