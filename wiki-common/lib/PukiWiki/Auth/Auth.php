<?php
/**
 * PukiWiki Advance 認証処理
 *
 * @author  Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @version $Id: auth.cls.php,v 0.69 2010/06/15 00:34:00 upk Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License (GPL2)
 */

namespace PukiWiki\Auth;

use PukiWiki\Renderer\RendererFactory;
use PukiWiki\Utility;
use PukiWiki\Factory;
use PukiWiki\Listing;
use PukiWiki\Renderer\Header;
use PukiWiki\Renderer\PluginRenderer;
use PukiWiki\Auth\AuthApi;
use Zend\Http\Response;
use Exception;

/**
 * 認証クラス
 */
class Auth
{
	/**
	 * 管理人のグループ名
	 */
	const ADMIN_GROUP = 'Administrator';
	/**
	 * パスワードの最大長
	 */
	const PASSPHRASE_LIMIT_LENGTH = 512;
	/**
	 * みなし管理人の名前
	 */
	const TEMP_CONTENTS_ADMIN_NAME = 'admin';
	/**
	 * ロール：ゲスト
	 */
	const ROLE_GUEST = 0;
	/**
	 * ロール：強制モード
	 */
	const ROLE_FORCE = 1;
	/**
	 * ロール：サイト管理者
	 */
	const ROLE_ADMIN = 2;
	/**
	 * ロール：コンテンツ管理者
	 */
	const ROLE_CONTENTS_ADMIN = 3;
	/**
	 * ロール：登録者
	 */
	const ROLE_ENROLLEE = 4;
	/**
	 * ロール：認証者
	 */
	const ROLE_AUTH = 5;
	/**
	 * ロール：見做し認証者
	 */
	const ROLE_AUTH_TEMP = 5.1;
	/**
	 * 認証方法：セッション認証（未実装）
	 */
	const AUTH_SESSION = 0;
	/**
	 * 認証方法：BASIC認証
	 */
	const AUTH_BASIC = 1;
	/**
	 * 認証方法：Digest認証
	 */
	const AUTH_DIGEST = 2;
	/**
	 * 認証方法：NTLM認証
	 */
	const AUTH_NTLM = 3;
	/**
	 * 認証方法：LDAP認証（未実装）
	 */
	const AUTH_LDAP = 4;
	/**
	 * 判定条件：ページ名
	 */
	const AUTH_METHOD_PAGENAME = 'pagename';
	/**
	 * 判定条件：ページ内容
	 */
	const AUTH_METHOD_CONTENTS = 'contents';
	/**
	 * 認証条件：読み込み時
	 */
	const AUTH_TYPE_READ = 'read';
	/**
	 * 認証条件：編集時
	 */
	const AUTH_TYPE_EDIT = 'edit';

	/**
	 * 管理人ログイン（非推奨）
	 * @param string $pass パスワード
	 * @return boolean
	 */
	public static function login($pass = '') {
		global $adminpass;

		if (! self::check_role('readonly') && isset($adminpass) &&
			self::hash_verify($pass, $adminpass)) {
			return TRUE;
		}
		sleep(2);       // Blocking brute force attack
		return FALSE;
	}

	/**
	 * パスフレーズとユーザのパスワードを比較する
	 * @param string $phrase パスフレーズ（プレーンテキスト）
	 * @param string $password パスワード（スキームを含むハッシュ値）
	 * @return bool
	 */
	public static function hash_verify($phrase, $password) {
		if (preg_match('/^{x-php-password}(.*)$/', $password, $matches))
			return password_verify($phrase, $matches[1]);
		else
			return self::hash_compute($phrase, $password) === $password;
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

			// PHP password_hash()
			case '{x-php-password}':
				$hash = ($prefix ? ($canonical ? '{x-php-password}' : $scheme) : '') .
					password_hash($phrase, PASSWORD_DEFAULT);
				break;

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
	
	/**
	 * 権限をチェック
	 * @param string $page ページ名
	 * @param string $type チェックする権限（AUTH_TYPE_READ or AUTH_TYPE_EDIT）
	 * @param boolean $authenticate 認証画面を出すか？
	 * @param string $username ユーザ名（認証状態のユーザ名が優先される）
	 * @param string $groupname グループ名（認証状態のユーザのグループが優先される）
	 * @return boolean
	 */
	public static function auth($page, $type = self::AUTH_TYPE_READ, $authenticate = false, $username='', $groupname='')
	{
		global $_title;
		global $auth_method_type, $read_auth_pages, $edit_auth_pages, $read_auth_pages_accept_ip, $edit_auth_pages_accept_ip;

		// マッチさせる文字列
		$target_str = '';
		// 認証の判定条件
		switch($auth_method_type) {
			case self::AUTH_METHOD_PAGENAME:
				// ページ名で判断
				$target_str = $page;
				break;
			case self::AUTH_METHOD_CONTENTS:
				// ページのソースで判断
				$target_str = Factory::Wiki($page)->get(true);
				break;
			default:
				throw new Exception('Auth::auth() : $auth_method_type = '.$auth_method_type.' is invalied or unmounted!.');
				break;
		}
		// 認証のタイプによってメッセージを分ける
		switch($type){
			case self::AUTH_TYPE_READ:
				// IPをチェック
				if (self::checkAcceptIp($read_auth_pages_accept_ip, $target_str)) return true;
				$auth_pages = $read_auth_pages;
				$title_cannot = $_title['cannotread'];
				break;
			case self::AUTH_TYPE_EDIT:
				if (self::checkAcceptIp($edit_auth_pages_accept_ip, $target_str)) return true;
				$auth_pages = $edit_auth_pages;
				$title_cannot = $_title['cannotedit'];
				break;
			default:
				throw new Exception('Auth::auth() : $type = '.$type.' is invalied!.');
				break;
		}

		$user_list = $group_list = $role = $matched = '';
		// ページから認証条件を読む
		// $auth_pages = array(
		//      'ページ名の正規表現' => array(
		//          'user'  => array(ユーザ名のリスト),
		//          'group' => array(グループのリスト),
		//          'role'  => array(役割のリスト)
		//      ),
		//      ...
		// );
		foreach($auth_pages as $pattern=>$val) {
			if ($matched = preg_match($pattern, $target_str)) {
				// 認証条件に対象文字列がヒットした
				if (is_array($val)) {
					$user_list  = empty($val['user'])   ? null : explode(',',$val['user']);
					$group_list = empty($val['group'])  ? null : explode(',',$val['group']);
					$role       = empty($val['role'])   ? null : $val['role'];
				} else {
					$user_list  = empty($val)           ? null : explode(',',$val);
				}
				break;
			}
		}
		// 制限対象のページでない場合ここで終了
		if (empty($user_list) && empty($group_list) && empty($role)) return true;
		// マッチしない場合は対象外
		if ($matched === 0) return true;
		

		if ($authenticate){
			$ret = self::authenticate();
			if (!$ret === true) {
				// 認証失敗
				Utility::dieMessage( str_replace('$1', Utility::htmlsc(Utility::stripBracket($page)), $title_cannot), 'Not Auth', Response::STATUS_CODE_401);
				exit;
			}
		}
		// ユーザの情報を取得
		$info = self::get_user_info();
		if (empty($username)){
			// ユーザ名などが入力されていない場合、ユーザ情報からパラメーターを取得する
			$username = $info['key'];
		}
		if (empty($groupname)){
			$groupname = $info['group'];
		}

		// 未認証者
		if (empty($username)) return false;
		
		// ユーザ名検査
		if (!empty($user_list) && in_array($username, $user_list)) return true;
		// グループ検査
		if (!empty($group_list) && !empty($groupname) && in_array($groupname, $group_list)) return true;
		// role 検査
		if (!empty($role) && !self::is_check_role($role)) return true;
		// そうでない場合はナシ
		return false;
	}
	/**
	 * 認証処理
	 * @return boolean
	 */
	public static function authenticate(){
		global $auth_type, $auth_users;
		
		if (! self::check_role('role_contents_admin')) return TRUE; // 既にコンテンツ管理者

		switch ($auth_type) {
			case self::AUTH_BASIC:
				// BASIC認証
				$user_list = $auth_users;

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
					return FALSE;
				}
				break;
			case self::AUTH_DIGEST:
				// Digest認証
				return self::auth_digest($auth_users);
				break;
			case self::AUTH_NTLM:
				$srv_soft = (defined('SERVER_SOFTWARE'))? SERVER_SOFTWARE : $_SERVER['SERVER_SOFTWARE'];
				if (substr($srv_soft,0,9) !== 'Microsoft') {
					throw new Exception('Auth::authenticate() : Your server does not supported to NTLM authenticate.');
				}
				// NTLM認証
				if (!isset($_SERVER['HTTP_AUTHORIZATION'])) return false;
				$http_auth = $_SERVER['HTTP_AUTHORIZATION'];
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

				if (ord($digest{8})  != 1  ) return false;
				if (ord($digest[13]) != 178) return false;
				break;
/*
			case self::AUTH_LDAP:
				break;
*/
			default:
				throw new Exception('Auth::authenticate() : The authentication method is not supported.');
				break;
		}
		return true;
	}
	/**
	 * digest認証
	 * @param string $auth_users ユーザ
	 * @return boolean
	 */
	public static function auth_digest($auth_users)
	{
		$data = self::http_digest_parse();
		if ($data === false) return false;

		list($scheme, $salt, $role) = self::get_data($data['username'], $auth_users);
		if ($scheme !== '{x-digest-md5}') {
			Utility::dieMessage('Auth::auth_digest(): Digest auth must be password scheme to <var>{x-digest-md5}</var>. To use this authicate method, please click <a href="'.Router::get_cmd_uri('passwd') . '">here</a> to (re)generate password.');
		}

		// $A1 = md5($data['username'] . ':' . $realm . ':' . $auth_users[$data['username']]);
		$A1 = $salt;
		$A2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
		$valid_response = md5($A1 . ':' . $data['nonce'] . ':' . $data['nc'] . ':' . $data['cnonce'] . ':' . $data['qop'] . ':' . $A2);
		if ($data['response'] !== $valid_response) return false;
		return true;
	}
	/**
	 * LDAP認証（未実装）
	 * @param string $auth_users ユーザ
	 * @return boolean
	 */
	public static function auth_ldap($user, $ldappass)
	{
		// LDAPモジュールが入っているか？
		if (!extension_loaded('ldap')) {
			Utility::dieMessage('LDAP extension not loaded');
		}
		$ldapconnect = ldap_connect("localhost", 389);
		if(! $ldapconnect ){
			Utility::dieMessage('Unable to connect to LDAP server.');
		}

		ldap_set_option( $ldapconnect, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option( $ldapconnect, LDAP_OPT_REFERRALS, 0);

		//バインド
		$ldapbind = ldap_bind($ldapconnect,$ldaprdn,$ldappass);

		// クローズ
		ldap_close($ldapconnect);

		return $ldapbind;
	}
	
	/**
	 * パスワードチェック
	 * @global type $auth_type
	 * @return type
	 */
	public static function check_auth_pw()
	{
		global $auth_type, $auth_users;

		$login = '';

		switch ($auth_type) {
			case self::AUTH_BASIC:
				// BASIC認証
				foreach (array('PHP_AUTH_USER', 'AUTH_USER', 'REMOTE_USER', 'LOGON_USER') as $x) {
					if (isset($_SERVER[$x]) && ! empty($_SERVER[$x])) {
						// Digest だったら確実
						if (! empty($_SERVER['AUTH_TYPE']) && $_SERVER['AUTH_TYPE'] == 'Digest') {
							$user = $_SERVER[$x];
							break;
						}
						// ドメイン認証の確認
						$ms = explode('\\', $_SERVER[$x]);
						if (count($ms) === 3) {
							$user = $ms[2]; // DOMAIN\\USERID
							break;
						}
						// この変数の内容で確定する
						$user = $_SERVER[$x];
						break;
					}
				}
				if (empty($user)) return null;

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
				if (empty($pass) || empty($auth_users[$user][0])) return null; // パスワードが空は除く
				$login = self::hash_verify($pass, $auth_users[$user][0]) ? $user : null;
				break;
			case self::AUTH_DIGEST:
				// Digest認証
				$data = self::http_digest_parse();
				if ($data === false) return false;

				list($scheme, $salt, $role) = self::get_data($data['username'], $auth_users);
				if ($scheme !== '{x-digest-md5}') {
					Utility::dieMessage('Auth::check_auth_pw(): Digest auth must be password scheme to <var>{x-digest-md5}</var>.');
				}

				// $A1 = $salt;
				$A1 = md5($data['username'] . ':' . $realm . ':' . $auth_users[$data['username']]);
				$A2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
				$valid_response = md5($A1 . ':' . $data['nonce'] . ':' . $data['nc'] . ':' . $data['cnonce'] . ':' . $data['qop'] . ':' . $A2);
				if ($data['response'] !== $valid_response){
					unset($_SERVER['PHP_AUTH_DIGEST']);
					return false;
				}
				$login = $data['username'];
				break;
			case self::AUTH_NTLM:
				// NTLM認証
				$srv_soft = (defined('SERVER_SOFTWARE'))? SERVER_SOFTWARE : $_SERVER['SERVER_SOFTWARE'];
				if (substr($srv_soft,0,9) !== 'Microsoft') {
					Utility::dieMessage('Auth::check_auth_pw() : Your server does not supported to NTLM authenticate.');
				}
				list(, $login, , ) = self::ntlm_decode();
				break;
			default:
				throw new Exception('Auth::check_auth_pw() : The authentication method does not supported.');
				break;
		}
		
		return $login;
	}
	/**
	 * 認証 (PukiWikiの設定に準ずる)
	 * @static
	 */
	public static function auth_pw($auth_users)
	{
		$user = '';
		foreach (array('PHP_AUTH_USER', 'AUTH_USER') as $x) {
			if (isset($_SERVER[$x])) {
				$ms = explode('\\', $_SERVER[$x]);
				$user = count($ms) === 3 ? $ms[2] : $_SERVER[$x]; // DOMAIN\\USERID
				break;
			}
		}

		$pass = '';
		foreach (array('PHP_AUTH_PW', 'AUTH_PASSWORD', 'HTTP_AUTHORIZATION') as $x) {
			if (! empty($_SERVER[$x])) {
				if ($x == 'HTTP_AUTHORIZATION') {
					// NTLM対応 (domain, login, host, pass)
					$tmp_ntlm = self::ntlm_decode();
					if (empty($tmp_ntlm[3])) continue;
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
		if (!self::hash_verify($pass, $auth_users[$user][0])) return false;
		return true;
	}
	/**
	 * 認証用ヘッダーを取得（Render.phpより呼び出す）
	 * @return string
	 */
	public static function getAuthHeader(){
		global $auth_type, $realm;
		switch ($auth_type) {
			case self::AUTH_BASIC:
				return 'Basic realm="'.$realm.'"';
				break;
			case self::AUTH_DIGEST:
				return 'Digest realm="'.$realm.'", qop="auth-int, auth", algorithm="MD5",  nonce="'.uniqid(rand(),true).'", opaque="' . md5($realm). '"';
				break;
			case self::AUTH_NTLM:
				$srv_soft = (defined('SERVER_SOFTWARE'))? SERVER_SOFTWARE : $_SERVER['SERVER_SOFTWARE'];
				if (substr($srv_soft,0,9) !== 'Microsoft') {
					throw new Exception('Auth::authenticate() : Your server does not supported to NTLM authenticate.');
				}
				$strAuth = 'NTLMSSP'
				. chr(0) . chr(2) . chr(0) . chr(0) . chr(0)
				. chr(0) . chr(0) . chr(0) . chr(0) . chr(40)
				. chr(0) . chr(0) . chr(0) . chr(1) . chr(130)
				. chr(0) . chr(0) . chr(0) . chr(2) . chr(2)
				. chr(2) . chr(0) . chr(0) . chr(0) . chr(0)
				. chr(0) . chr(0) . chr(0) . chr(0) . chr(0)
				. chr(0) . chr(0) . chr(0);

				return 'NTLM ' . trim(base64_encode($strAuth));
				break;
			default:
				throw new Exception('Auth::getAuthHeader() : The authentication method is not supported.');
			break;
		}
		return null;
	}
	/**
	 * PHP_AUTH_DIGEST 変数をパースする関数
	 * function to parse the http auth header
	 * @static
	 */
	private static function http_digest_parse()
	{
		// データが失われている場合への対応
		$needed_parts = array(
			'username'  => null,
			'realm'     => null,
			'nonce'     => null,
			'uri'       => $_SERVER['REQUEST_URI'],
			'cnonce'    => null,
			'nc'        => null,
			'response'  => null,
			'qop'       => null,
			'opaque'    => null
		);
		$digest = null;

		// mod_php
		if (isset($_SERVER['PHP_AUTH_DIGEST'])) {
			$digest = $_SERVER['PHP_AUTH_DIGEST'];
		// most other servers
		} else if (isset($_SERVER['HTTP_AUTHENTICATION']) && strpos(strtolower($_SERVER['HTTP_AUTHENTICATION']),'digest')===0) {
			$digest = substr($_SERVER['HTTP_AUTHORIZATION'], 7);
		}else{
			return false;
		}

		$data = array();

		// url に含まれる文字列を含む必要がある
		// preg_match_all('@(\w+)=([\'"]?)([a-zA-Z0-9=./\_-]+)\2@', $txt, $matches, PREG_SET_ORDER);
		// preg_match_all('@(\w+)=([\'"]?)([a-zA-Z0-9=./%&\?\_-_+]+)\2@', $txt, $matches, PREG_SET_ORDER);
		//preg_match_all('@(\w+)=([\'"]?)([a-zA-Z0-9=./%&\?\_-]+)\2@', $txt, $matches, PREG_SET_ORDER);
		preg_match_all('/(\w+)=("([^"]+)"|([a-zA-Z0-9=.\/\_-]+))/',$digest,$matches,PREG_SET_ORDER);

		foreach ($matches as $m){
			$data[$m[1]] = $m[3] ? $m[3] : $m[4];
			unset($needed_parts[$m[1]]);
		}

		return $needed_parts ? FALSE : $data;
	}
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
	/*
	 * ROLEに応じた挙動の確認
	 * @return boolean
	 */
	static function check_role($func='')
	{
		global $adminpass;

		switch($func) {
			case 'readonly':
				$chk_role = (defined('PKWK_READONLY')) ? PKWK_READONLY : self::ROLE_GUEST;
				break;
			case 'safemode':
				$chk_role = defined('PKWK_SAFE_MODE') ? PKWK_SAFE_MODE : self::ROLE_GUEST;
				break;
			case 'su':
				$now_role = self::get_role_level();
				if ($now_role == self::ROLE_ADMIN || (int)$now_role == self::ROLE_CONTENTS_ADMIN) return FALSE; // 既に権限有
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
			trigger_error('Auth::check_role(\'role_adm_contents\') is not recommond. Instead use Auth::check_role(\'role_contents_admin\').', E_USER_DEPRECATED);
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
	/**
	 * 管理者パスワードなのかどうか（暫定管理人か？）
	 * @return boolean
	 */
	public static function is_temp_admin()
	{
		global $adminpass;
		// 管理者パスワードなのかどうか？
		$temp_admin = !self::hash_verify($_SERVER['PHP_AUTH_PW'], $adminpass) ? false : true;
		if (! $temp_admin && $login == self::TEMP_CONTENTS_ADMIN_NAME) {
			global $vars;
			if (isset($vars['pass']) && self::login($vars['pass'])) $temp_admin = true;
		}
		return $temp_admin;
	}
	/**
	 *ページの管理権限を取得
	 * @return boolean
	 */
	public static function is_page_auth($page, $auth_flag, $auth_pages, $uname, $gname='')
	{
		global $auth_method_type;
		static $info;
		if (! $auth_flag) return true;

		if (!isset($info)) $info = auth::get_user_info();

		$target_str = '';
		switch($auth_method_type) {
			case self::AUTH_METHOD_PAGENAME:
				$target_str = $page;
				break;
			case self::AUTH_METHOD_CONTENTS:
				$target_str = Factory::Wiki($page)->get();
				break;
			}

		$user_list = $group_list = $role = null;
		foreach($auth_pages as $key=>$val) {
			if (preg_match($key, $target_str)) {
				if (is_array($val)) {
					$user_list  = empty($val['user'])  ? null : explode(',',$val['user']);
					$group_list = empty($val['group']) ? null : explode(',',$val['group']);
					$role       = empty($val['role'])  ? null : $val['role'];
				} else {
					$user_list  = empty($val)          ? null : explode(',',$val);
				}
				break;
			}
		}

		// No limit
		if (empty($user_list) && empty($group_list) && empty($role)) return true;
		// 未認証者
		if (empty($uname)) return false;

		// ユーザ名検査
		if (!empty($user_list) && in_array($uname, $user_list)) return true;
		// グループ検査
		if (!empty($group_list) && !empty($gname) && in_array($gname, $group_list)) return true;
		// role 検査
		if (!empty($role) && !auth::is_check_role($role)) return true;
		return false;
	}
	/**
	 * ユーザ情報
	 * @return array
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

	/**
	 * ユーザ情報を取得
	 * @return array
	 */
	private static function get_auth_pw_info()
	{
		global $auth_users, $defaultpage;
		
		$retval = array(
			'role'          => self::ROLE_GUEST,
			'nick'          => null,
			'key'           => null,
			'api'           => 'plus',
			'group'         => null,
			'displayname'   => null,
			'home'          => null,
			'mypage'        => null
		);
		$user = self::check_auth_pw();
		if (empty($user)) return $retval;

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
	/**
	 * 認証されたAPIの情報を取得
	 * @global type $auth_api
	 * @global type $auth_wkgrp_user
	 * @global type $defaultpage
	 * @return array
	 */
	static function get_auth_api_info()
	{
		global $auth_api, $auth_wkgrp_user, $defaultpage;

		$retval = array(
			'role'          => self::ROLE_GUEST,
			'nick'          => null,
			'key'           => null,
			'api'           => 'plus',
			'group'         => null,
			'displayname'   => null,
			'home'          => null,
			'mypage'        => null
		);

		foreach($auth_api as $api=>$val) {
			// どうしても必要な場合のみ開始
			if (! $val['use']) continue;
			break;
		}

		$obj = new AuthApi();
		$msg = $obj->getSession();
		if (isset($msg['api']) && $auth_api[$msg['api']]['use']) {
			if (PluginRenderer::hasPlugin($msg['api'])) {
				$call_func = 'plugin_'.$msg['api'].'_get_user_name';
				$auth_key = $call_func();
				$auth_key['api'] = $msg['api'];
				if (empty($auth_key['nick'])) return $auth_key;

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
	/**
	 * ユーザ名を取得
	 * @return string
	 */
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
	 * @return int
	 */
	static function get_role_level()
	{
		$info = self::get_user_info();
		return $info['role'];
	}

	/*
	 * 指定されるROLEに属するユーザを列挙
	 * @param string $role 役割
	 * @return array
	 */
	public static function get_user_list($role)
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
	 * HTTP_AUTHORIZATION の解読
	 * @static
	 */
	private static function ntlm_decode()
	{
		$rc = array('','','','');
		if (!function_exists('base64_decode')) return $rc;
		if (!isset($_SERVER['HTTP_AUTHORIZATION'])) return $rc;

		list($auth_type,$x) = explode(' ', $_SERVER['HTTP_AUTHORIZATION']);

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
	 * IP認証
	 * @global type $auth_method_type
	 * @param type $page ページ名
	 * @param type $auth_pages_accept_ip 対象となるIPアドレス
	 * @return boolean
	 */
	public static function ip_auth($page, $auth_pages_accept_ip)
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
	/**
	 * 許可IPをチェック
	 * @param array $accept_ip 許可IPのリスト
	 * @param string $target_str チェックパターン
	 * @return boolean
	 */
	private static function checkAcceptIp($accept_ips, $target_str){
		if (!is_array($accept_ips)) return false;
		$remote_addr = isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR'];

		foreach($accept_ips as $key=>$val){
			if (preg_match($key, $target_str)){
				$accept_ip_list = array_merge($accept_ip_list, explode(',', $val));

				if (!empty($accept_ip_list) && isset($remote_addr)) {
					foreach ($accept_ip_list as $ip) {
						if (strpos($remote_addr, $ip) !== false) return TRUE;
					}
				}
			}
		}
		return false;
	}
	/**
	 * データの分解
	 * @static
	 */
	private static function get_data($user,$auth_users)
	{
		if (!isset($auth_users[$user])) {
			// scheme, salt, role
			return array(null,null,null);
		}

		$role = (empty($auth_users[$user][1])) ? '' : $auth_users[$user][1];
		list($scheme,$salt) = self::passwd_parse($auth_users[$user][0]);
		return array($scheme,$salt,$role);
	}

	/**
	 * PukiWiki Passwd の分解
	 * @static
	 */
	public static function passwd_parse($passwd)
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
	public static function get_signature($lines)
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
	/**
	 * ページ一覧を取得
	 * @param type $dir
	 * @param type $ext
	 * @return type
	 */
	public static function get_existpages($type='wiki')
	{
		$rc = array();

		// ページ名の取得
		$pages = Listing::pages($type);

		// $pages = get_existpages($dir, $ext);
		// コンテンツ管理者以上は、: のページも閲覧可能
		$has_permisson = self::check_role('role_contents_admin');

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
	/**
	 * 役割のあるページか
	 * @global type $check_role
	 * @param type $lines
	 * @return boolean
	 */
	public static function is_role_page($lines)
	{
		global $check_role;
		if (! $check_role) return FALSE;
		$cmd = self::use_plugin('check_role',$lines);
		if ($cmd === FALSE) return FALSE;
		RendererFactory::factory($cmd); // die();
		return TRUE;
	}
	/**
	 * Used Plugin?
	 * @param string $pligin
	 * @param string $lines
	 * @return boolean
	 */
	private static function use_plugin($plugin, $lines)
	{
		if (!is_array($lines)) {
			$delim = array("\r\n", "\r");
			$lines = str_replace($delim, "\n", $lines);
			$lines = explode("\n", $lines);
		}

		foreach ($lines as $line) {
			if (substr($line, 0, 2) == '//') continue;
			// Diff data
			if (substr($line, 0, 1) == '+' || substr($line, 0, 1) == '-') {
				$line = substr($line, 1);
			}
			if (preg_match('/^[#|&]' . $plugin . '[^a-zA-Z]*$/', $line, $matches)) {
				return $matches[0];
			}
		}
		return FALSE;
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
	/**
	 * sha1をbase64変換
	 * @param string $x
	 * @return string
	 */
	private static function b64_sha1($x)
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
	/**
	 * ユーザ一覧を配列にして出力
	 * @return array
	 */
	public static function user_list()
	{
		global $auth_users, $auth_wkgrp_user, $defaultpage;
		$rc = array();

		foreach ($auth_users as $user=>$val) {
			$rc['plus'][$user] = array(
				'role'          => empty($val[1]) ? self::ROLE_ENROLLEE : $val[1],
				'displayname'   => empty($val[2]) ? null : $val[2],
				'group'         => null,
				'home'          => empty($val[3]) ? $defaultpage : $val[3],
				'mypage'        => empty($val[4]) ? null : $val[4]
			);
		}

		foreach($auth_wkgrp_user as $api=>$val1) {
			foreach($val1 as $user=>$val) {
				$rc[$api][$user] = is_array($val) ?
					array(
						'role'  => (empty($val['role'])) ? self::ROLE_ENROLLEE : $val['role'],
						'group' => (empty($val['group'])) ? null : $val['group'],
						'name'  => (empty($val['displayname'])) ? $user : $val['displayname'],
						'home'  => (empty($val['home'])) ? $defaultpage : $val['home'],
						'mypage'=> (empty($val['mypage'])) ? null : $val['mypage']
					) :
					array(
						'role'  => $val,
						'group' => null,
						'name'  => $user,
						'home'  => $defaultpage,
						'mypage'=> null
					);
			}
		}
		return $rc;
	}
}

/* End of file Auth.php */
/* Location: ./vendor/PukiWiki/Auth/Auth.php */