<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone
// $Id: AuthUtility.php,v 1.0.0 2012/09/25 15:31:00 Logue Exp $
// Copyright (C)
//   2012 PukiWiki Advance Developers Team
// License: GPL v2 or (at your option) any later version
//
// Authentication related functions
namespace PukiWiki\Lib\Auth;
use PukiWiki\Lib\Auth\Auth;
use PukiWiki\Lib\File\FileFactory;

class AuthUtility{
	public static function login($pass = '') {
		global $adminpass;

		// if (! PKWK_READONLY && isset($adminpass) &&
		if (! Auth::check_role('readonly') && isset($adminpass) &&
			self::hash_compute($pass, $adminpass) === $adminpass) {
			return TRUE;
		} else {
			sleep(2);       // Blocking brute force attack
			return FALSE;
		}
	}

	// Compute RFC2307 'userPassword' value, like slappasswd (OpenLDAP)
	// $phrase : Pass-phrase
	// $scheme : Specify '{scheme}' or '{scheme}salt'
	// $prefix : Output with a scheme-prefix or not
	// $canonical : Correct or Preserve $scheme prefix
	public static function hash_compute($phrase = '', $scheme = '{php_md5}', $prefix = TRUE, $canonical = FALSE)
	{
		if (! is_string($phrase) || ! is_string($scheme)) return FALSE;

		if (strlen($phrase) > PKWK_PASSPHRASE_LIMIT_LENGTH)
			die('pkwk_hash_compute(): malicious message length');

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


	// Basic-auth related ----

	// Check edit-permission
	static function checkEditable($page, $auth_flag = TRUE, $exit_flag = TRUE) {
		global $_title, $_string, $defaultpage;

		if (edit_auth($page, $auth_flag, $exit_flag) && is_editable($page)) return true;

		if ($exit_flag) {
			if (PKWK_WARNING){
				die_message($_string['not_editable']);
			}else{
				// 無応答
				header( 'Location: ' . get_page_location_uri($defaultpage));
				die();
			}
		}
		return false;
	}


	// Check read-permission
	static function checkReadable($page, $auth_flag = TRUE, $exit_flag = TRUE)
	{
		global $_title, $defaultpage;

		if (read_auth($page, $auth_flag, $exit_flag)) return true;

		if ($exit_flag) {
			if (PKWK_WARNING){
				die_message($_string['not_readable']);
			}else{
				// 無応答
				header( 'Location: ' . get_page_location_uri($defaultpage));
				die();
			}
		}
		return false;
	}

	static function edit_auth($page, $auth_flag = TRUE, $exit_flag = TRUE)
	{
		global $edit_auth, $edit_auth_pages, $auth_api, $defaultpage, $_title, $edit_auth_pages_accept_ip;

		if (Auth::check_role('readonly')) return false;

		if (!$edit_auth) return true;

		// 許可IPの場合チェックしない
		if(ip_auth($page, $auth_flag, $exit_flag, $edit_auth_pages_accept_ip, $_title['cannotedit'])) {
			return TRUE;
		}

		$info = Auth::get_user_info();
		if (!empty($info['key']) &&
		    Auth::is_page_readable($page, $info['key'], $info['group']) &&
		    Auth::is_page_editable($page, $info['key'], $info['group'])) {
			return true;
		}

		// Basic, Digest 認証を利用していない場合
		if (!$auth_api['plus']['use']) return Auth::is_page_readable($page, '', '');

		$auth_func_name = get_auth_func_name();
		if ($auth_flag && ! $auth_func_name($page, $auth_flag, $exit_flag, $edit_auth_pages, $_title['cannotedit'])) return false;
		if (Auth::is_page_readable($page, '', '') && Auth::is_page_editable($page,'','')) return true;

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

	static function read_auth($page, $auth_flag = TRUE, $exit_flag = TRUE)
	{
		global $read_auth, $read_auth_pages, $auth_api, $defaultpage, $_title, $read_auth_pages_accept_ip;

		if (!$read_auth) return true;

		// 許可IPの場合チェックしない
		if(ip_auth($page, $auth_flag, $exit_flag, $read_auth_pages_accept_ip, $_title['cannotread'])) {
			return TRUE;
		}

		$info = Auth::get_user_info();
		if (!empty($info['key']) &&
		    Auth::is_page_readable($page, $info['key'], $info['group'])) {
			return true;
		}

		if (!$auth_api['plus']['use']) return Auth::is_page_readable($page, '', '');

		$auth_func_name = get_auth_func_name();
		// 未認証時で認証不要($auth_flag)であっても、制限付きページかの判定が必要
		if ($auth_flag && ! $auth_func_name($page, $auth_flag, $exit_flag, $read_auth_pages, $_title['cannotread'])) return false;
		return Auth::is_page_readable($page, '', '');

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

	public static function get_auth_func_name()
	{
		global $auth_type;
		$namespace = 'PukiWiki\Lib\Auth\AuthUtility::';
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

	// Basic authentication
	public static function basic_auth($page, $auth_flag, $exit_flag, $auth_pages, $title_cannot)
	{
		global $auth_users, $auth_method_type;
		global $realm;

		if (Auth::is_page_auth($page, $auth_flag, $auth_pages, '','')) return true; // No limit
		$user_list = $auth_users;
		//$user_list = get_auth_page_users($page, $auth_pages);
		// if (empty($user_list)) return TRUE; // No limit

		if (! Auth::check_role('role_adm_contents')) return TRUE; // 既にコンテンツ管理者

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

		// if (PKWK_READONLY ||
		// if (Auth::check_role('readonly') ||
		//	! isset($_SERVER['PHP_AUTH_USER']) ||
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
	static function digest_auth($page, $auth_flag, $exit_flag, $auth_pages, $title_cannot)
	{
		global $auth_users, $auth_method_type, $auth_type;
		global $realm;

		if (Auth::is_page_auth($page, $auth_flag, $auth_pages, '','')) return true; // No limit
		//$user_list = get_auth_page_users($page, $auth_pages);
		//if (empty($user_list)) return true; // No limit

		if (! Auth::check_role('role_adm_contents')) return true; // 既にコンテンツ管理者
		if (Auth::auth_digest($auth_users)) return true;

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
	static function ip_auth($page, $auth_flag, $exit_flag, $auth_pages_accept_ip, $title_cannot)
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
				$target_str = FileFactory::Wiki($page)->source(); // Its contents
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
}

/* End of file AuthUtility.php */
/* Location: ./vender/PukiWiki/Lib/Auth/AuthUtility.php */