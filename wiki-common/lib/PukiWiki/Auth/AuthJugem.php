<?php
/**
 * PukiWiki Advance Jugem 認証処理
 *
 * @copyright   Copyright &copy; 2006,2008, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @author      Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @version     $Id: hatena.inc.php,v 0.14.1 2013/02/24 17:17:00 Logue Exp $
 * @license     http://opensource.org/licenses/gpl-license.php GNU Public License (GPL2)
 */

namespace PukiWiki\Auth;

use PukiWiki\Auth\AuthApi;

/**
 * Jugem認証
 */
class AuthJugem extends AuthApi
{
	var $sec_key,$api_key;
	
	const JUGEMKEY_URL_AUTH = 'https://secure.jugemkey.jp/?mode=auth_issue_frob';
	const JUGEMKEY_URL_TOKEN = 'http://api.jugemkey.jp/api/auth/token';
	const JUGEMKEY_URL_USER = 'http://api.jugemkey.jp/api/auth/user';
	const ROLE_AUTH_JUGEMKEY = 5.4;

	function __construct()
	{
		global $auth_api;
		$this->auth_name = 'jugemkey';
		$this->sec_key = $auth_api[$this->auth_name]['sec_key'];
		$this->api_key = $auth_api[$this->auth_name]['api_key'];
		$this->field_name = array('title','token');
		$this->response = array();
		parent::__construct();
	}

	function make_login_link()
	{
		$query = array(
			'api_key'       => $this->api_key,
			'perms'         => 'auth',
			'api_sig'       => hash_hmac('sha1', $this->api_key . $this->callbackUrl . 'auth', $this->sec_key),
			'callback_url'  => $this->callbackUrl
		);
		return self::JUGEMKEY_URL_AUTH.http_build_query($query);
	}

	function auth($cert)
	{
		// $created = substr_replace(get_date('Y-m-d\TH:i:sO', UTIME), ':', -2, 0);
		$created = str_replace('+0000', 'Z', gmdate('Y-m-d\TH:i:sO', time()));
		$headers = array(
			'X-JUGEMKEY-API-CREATED'=> $created,
			'X-JUGEMKEY-API-KEY'	=> $this->api_key,
			'X-JUGEMKEY-API-FROB'	=> $cert,
			'X-JUGEMKEY-API-SIG'	=> hash_hmac('sha1', $this->api_key . $created . $cert, $this->sec_key),
		);

		$data = http_request(self::JUGEMKEY_URL_TOKEN, 'GET', $headers);

		$this->response['rc'] = $data['rc'];
		if ($data['rc'] != 200) {
			return $this->response;
		}

		$this->responce_xml_parser($data['data']);
		return $this->response;
	}

	function get_userinfo($token)
	{
		//$created = substr_replace(get_date('Y-m-d\TH:i:sO', UTIME), ':', -2, 0);
		$created = str_replace('+0000', 'Z', gmdate('Y-m-d\TH:i:sO', time()));
		$headers = array(
			'X-JUGEMKEY-API-CREATED'=> $created,
			'X-JUGEMKEY-API-KEY'    => $this->api_key,
			'X-JUGEMKEY-API-TOKEN'  => $token,
			'X-JUGEMKEY-API-SIG'    => hash_hmac('sha1', $this->api_key . $created.$token, $this->sec_key),
		);

		$data = http_request(self::JUGEMKEY_URL_USER, 'GET', $headers);
		$this->response['rc'] = $data['rc'];
		if ($data['rc'] != 200 && ($data['rc'] != 401)) return $this->response;

		$this->responce_xml_parser($data['data']);
		return $this->response;
	}
}