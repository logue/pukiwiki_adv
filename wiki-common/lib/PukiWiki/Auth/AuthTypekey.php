<?php
/**
 * PukiWiki Plus! TypeKey 認証処理
 *
 * @copyright   Copyright &copy; 2007-2009, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @author      Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @version     $Id: AuthTypekey.php,v 0.8.1 2010/12/26 17:24:00 Logue Exp $
 * @license     http://opensource.org/licenses/gpl-license.php GNU Public License (GPL2)
 */

namespace PukiWiki\Auth;

use PukiWiki\Auth\AuthApi;
use PukiWiki\Renderer\Inline\Inline;
use PukiWiki\Router;
use PukiWiki\Utility;

/**
 * TypeKey認証
 */
class AuthTypekey extends AuthApi
{
	const TYPEKEY_URL_LOGIN = 'https://www.typekey.com/t/typekey/login';
	//const TYPEKEY_URL_LOGOUT = 'https://www.typekey.com/t/typekey/logout';
	//const TYPEKEY_URL_PROFILE = 'http://profile.typekey.com/';
	const TYPEKEY_URL_LOGOUT = 'http://www.typepad.com/connect/services/signout';
	const TYPEKEY_URL_PROFILE = 'http://profile.typepad.com/';
	const TYPEKEY_REGKEYS = 'http://www.typekey.com/extras/regkeys.txt';
	const TYPEKEY_VERSION = 1.1;
	const TYPEKEY_CACHE_TIME = 172800; // 2 day
	const ROLE_AUTH_TYPEKEY = 6.6;

	var $siteToken, $need_email, $regkeys, $version;

	function __construct()
	{
		global $auth_api;
		$this->auth_name = 'typekey';
		$this->siteToken = trim( $auth_api[$this->auth_name]['site_token']);
		$this->field_name = array('ts','email','name','nick','site_token');
		$this->need_email = 0;
		$this->version = self::TYPEKEY_VERSION;
	}

	function set_need_email($x) { $this->need_email = $x; }
	function set_version($x) { $this->version = $x; }
	function set_regkeys() { $this->regkeys = $this->get_regkeys(); }
	function set_sigKey($sigKey)
	{
		foreach($this->field_name as $key) {
			if ($key == 'site_token') {
				$this->response[$key] = $this->siteToken;
			} else {
				$this->response[$key] = (empty($sigKey[$key])) ? '' : trim($sigKey[$key]);
			}
		}

		// FIXME: DSA署名中に + が混入されると空白に変換される場合があるための対応
		$this->response['sig'] = (empty($sigKey['sig'])) ? '' : str_replace(' ', '+', $sigKey['sig']);
	}

	function get_regkeys()
	{
		$rc = array();

		$regkeys = CACHE_DIR . 'regkeys.txt';
		$now = time();
		if (file_exists($regkeys)) {
			$time_regkeys = filemtime($regkeys) + self::TYPEKEY_CACHE_TIME;
		} else {
			$time_regkeys = $now;
		}

		if ($now < $time_regkeys) {
			$idx = 0;
			$data = file($regkeys);
		} else {
			$data = http_request(self::TYPEKEY_REGKEYS);
			// if ($data['rc'] != 200) return $rc;
			if ($data['timeout'] && file_exists($regkeys)) {
				// タイムアウト時でキャッシュがあれば、再利用する。
				$idx = 0;
				$data = file($regkeys);
			} else {
				$idx = 'data';
				$fp = fopen($regkeys, 'w');
				@flock($fp, LOCK_EX);
				rewind($fp);
				fputs($fp, $data[$idx]);
				@flock($fp, LOCK_UN);
				fclose($fp);
			}
		}

		foreach(explode(' ',$data[$idx]) as $x) {
			list($key,$val) = explode('=',$x);
			$rc[$key] = trim($val);
		}
		return $rc;
	}

	function get_profile($field='nick')
	{
		$message = $this->getSession();
		return (empty($message[$field])) ? null : $message[$field];
	}

	function get_profile_link()
	{
		$message = $this->getSession();
		if (! empty($message['api']) && $this->auth_name !== $message['api']) return false;
		if (empty($message['nick'])) return '';
		return Inline::setLink($message['name'] , self::typekey_profile_url($message['name']), '', 'nofollow', false);
	}

	function gen_message()
	{
		$message = $delm = '';
		// <email>::<name>::<nick>::<ts>::<site-token>
		foreach(array('email','name','nick','ts','site_token') as $key) {
			$message .= $delm.$this->response[$key];
			if (empty($delm)) $delm = '::';
		}
		return $message;
	}

	function typekey_login_url($return='')
	{
		if (empty($return)) {
			$return = Router::get_script_absuri();
		}
		$rc = self::TYPEKEY_URL_LOGIN.'?t='.$this->siteToken.'&amp;v='.$this->version;
		if ($this->need_email != 0) {
			$rc .= '&amp;need_email=1';
		}
		return $rc.'&amp;_return='.$return;
	}

	function typekey_logout_url($return='')
	{
		if (empty($return)) {
			$return = Router::get_script_absuri();
		}
		// return TYPEKEY_URL_LOGOUT.'?_return='.$return;
		return self::TYPEKEY_URL_LOGOUT.'?to='.$return;
	}

	function typekey_login($return)
	{
		Utility::redirect($this->typekey_login_url($return));
	}

	function typekey_profile_url($name)
	{
		return self::TYPEKEY_URL_PROFILE.rawurlencode($name).'/';
	}

	function auth()
	{
		if (empty($this->response['email'])) return false;
		// FIXME: どの程度までチェックするのか？
		if ($this->need_email) {
			if (! strpos($this->response['email'],'@')) return false;
		}
		$message = $this->gen_message();

		require_once(LIB_DIR.'DSA.php');
		return Security_DSA::verify($message, $this->response['sig'], $this->regkeys);
	}

}