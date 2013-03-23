<?php
/**
 * PukiWiki Advance Livedoor 認証処理
 *
 * @copyright   Copyright &copy; 2006,2008, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @author      Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @version     $Id: hatena.inc.php,v 0.14.1 2013/02/24 17:17:00 Logue Exp $
 * @license     http://opensource.org/licenses/gpl-license.php GNU Public License (GPL2)
 */
namespace PukiWiki\Auth;

use PukiWiki\Auth\AuthApi;
use PukiWiki\Utility;

/**
 * Livedoor認証
 */
class AuthLivedoor extends AuthApi
{
	var $sec_key,$app_key;

	const ROLE_AUTH_LIVEDOOR = 5.6;
	const LIVEDOOR_URL_AUTH = 'http://auth.livedoor.com/login/';
	const LIVEDOOR_VERSION = 1.0;
	const LIVEDOOR_PERMS = 'id';	// userhash or id
	const LIVEDOOR_URL_GETID = 'http://auth.livedoor.com/rpc/auth';
	const LIVEDOOR_TIMEOUT = 600; // 10min

	function __construct()
	{
		global $auth_api;
		$this->auth_name = 'livedoor';
		$this->sec_key = $auth_api[$this->auth_name]['sec_key'];
		$this->app_key = $auth_api[$this->auth_name]['app_key'];
		$this->field_name = array('livedoor_id');
		$this->response = array();
	}

	function make_login_link()
	{
		$query = array(
			'app_key'   => $this->app_key,
			'perms'     => self::LIVEDOOR_PERMS,
			't'         => UTIME,
			'v'         => self::LIVEDOOR_VERSION,
			'userdata'  => empty($this->callbackUrl) ? '' : Utility::encode($this->callbackUrl)
		);
		$query['sig'] = $this->make_hash($query);

		return self::LIVEDOOR_URL_AUTH.http_build_query($query);
	}

	function make_hash($array)
	{
		ksort($array);
		$x = '';
		foreach($array as $key=>$val) {
			$x .= $key.$val;
		}
		return hash_hmac('sha1',$x, $this->sec_key);
	}

	function auth($vars)
	{
		if (! isset($vars['sig'])) return array('has_error'=>'true','message'=>'Signature is not found.');
		if (! isset($vars['token'])) return array('has_error'=>'true','message'=>'Token is not found.');

		if (isset($vars['userdata'])) {
			$this->response['userdata'] = Utility::decode($vars['userdata']);
		}

		$query = array();
		static $keys = array('app_key','userhash','token','t','v','userdata');
		foreach($keys as $key) {
			if (!isset($vars[$key])) continue;
			$query[$key] = $vars[$key];
		}

		$api_sig = $this->make_hash($query);
		if ($api_sig !== $vars['sig']) return array('has_error'=>'true','message'=>'Comparison error of signature.');

		// ログオンしてから 10分経過している場合には、タイムアウトとする
		$time_out = UTIME - self::LIVEDOOR_TIMEOUT;
		if ($vars['t'] < $time_out) return array('has_error'=>'true','message'=>'The time-out was done.');

		if (LIVEDOOR_PERMS !== 'id') {
			return array('has_error'=>'false','message'=>'');
		}

		$post = array(
			'app_key' => $this->app_key,
			'format' => 'xml',
			'token' => $vars['token'],
			't' => UTIME,
			'v' => self::LIVEDOOR_VERSION,
		);
		$post['sig'] = $this->make_hash($post);

		$data = http_request(self::LIVEDOOR_URL_GETID,'POST','',$post);
		if ($data['rc'] != 200) return array('has_error'=>'true','message'=>$data['rc']);

		$this->responce_xml_parser($data['data']);

		$has_error = ($this->response['error'] == 0) ? 'false' : 'true';
		return array('has_error'=>$has_error,'message'=>$this->response['message']);
	}

	function get_return_page()
	{
		return $this->response['userdata'];
	}
}