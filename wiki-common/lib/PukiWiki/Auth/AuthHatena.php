<?php
/**
 * PukiWiki Advance Hatena 認証処理
 *
 * @copyright   Copyright &copy; 2006,2008, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @author      Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @version     $Id: hatena.inc.php,v 0.14.1 2013/02/24 17:17:00 Logue Exp $
 * @license     http://opensource.org/licenses/gpl-license.php GNU Public License (GPL2)
 */

namespace PukiWiki\Auth;

use PukiWiki\Auth\AuthApi;
use PukiWiki\Renderer\Inline\Inline;
use PukiWiki\Utility;

/**
 * はてな認証
 */
class AuthHatena extends AuthApi
{
	var $sec_key,$api_key;
	/**
	 * はてな認証URI
	 * @url http://auth.hatena.ne.jp/
	 */
	const HATENA_URL_AUTH = 'http://auth.hatena.ne.jp/auth';
	/**
	 * はてな認証APIのアドレス
	 */
	const HATENA_URL_XML = 'http://auth.hatena.ne.jp/api/auth.xml';
	/**
	 * はてなのプロフィールのアドレス
	 */
	const HATENA_URL_PROFILE = 'http://www.hatena.ne.jp/user?userid=';
	/**
	 * はてなのロール
	 */
	const ROLE_AUTH_HATENA = 5.3;

	/**
	 * コンストラクタ
	 */
	public function __construct()
	{
		global $auth_api;
		$this->auth_name = 'hatena';
		$this->sec_key = $auth_api[$this->auth_name]['sec_key'];
		$this->api_key = $auth_api[$this->auth_name]['api_key'];
		$this->field_name = array('name','image_url','thumbnail_url');
		$this->response = array();
		parent::__construct();
	}

	/**
	 * ログインリンクを生成
	 * @param string $return 戻り先のURL
	 * @return string
	 */
	function make_login_link($return)
	{
		$x1 = $x2 = '';
		foreach($return as $key=>$val) {
			$r_val = ($key == 'page') ? Utility::encode($val) : rawurlencode($val);
			$x1 .= $key.$r_val;
			$x2 .= '&amp;'.$key.'='.$r_val;
		}

		$api_sig = md5($this->sec_key.'api_key'.$this->api_key.$x1);
		return self::HATENA_URL_AUTH.'?api_key='.$this->api_key.'&amp;api_sig='.$api_sig.$x2;
	}
	/**
	 * 認証
	 * @param string $cert
	 * @return boolean
	 */
	function auth($cert)
	{
		$api_sig = md5($this->sec_key.'api_key'.$this->api_key.'cert'.$cert);
		$url = self::HATENA_URL_XML.'?api_key='.$this->api_key.'&amp;cert='.$cert.'&amp;api_sig='.$api_sig;

		$data = http_request($url);
		if ($data['rc'] != 200) return array('has_error'=>'true','message'=>$data['rc']);

		$xml_parser = xml_parser_create();
		xml_parse_into_struct($xml_parser, $data['data'], $val, $index);
		xml_parser_free($xml_parser);

		foreach($val as $x) {
			if ($x['type'] != 'complete') continue;
			$this->response[strtolower($x['tag'])] = $x['value'];
		}
		return $this->response;
	}
	/**
	 * はてなのプロフィール
	 * @param string $name ユーザ名
	 * @return string
	 */
	function hatena_profile_url($name){
		return self::HATENA_URL_PROFILE.rawurlencode($name);
	}
	/**
	 * プロフィールのリンクを生成
	 * @return string
	 */
	function get_profile_link(){
		$message = $this->getSession();
		if (empty($message['name'])) return '';
		return Inline::setLink($message['name'] , self::hatena_profile_url($message['name']), '', 'nofollow', false);
	}
}
