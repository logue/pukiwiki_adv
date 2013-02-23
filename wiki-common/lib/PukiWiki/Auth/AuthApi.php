<?php
/**
 * PukiWiki Plus! 認証処理
 *
 * @copyright   Copyright &copy; 2007-2009, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @author      Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @version     $Id: auth_api.cls.php,v 0.6 2008/06/02 01:40:00 upk Exp $
 * @license     http://opensource.org/licenses/gpl-license.php GNU Public License (GPL2)
 */
// require_once(LIB_DIR . 'hash.php');

namespace PukiWiki\Auth;

use PukiWiki\Auth\Auth;
use PukiWiki\Utility;
use PukiWiki\Router;

class AuthApi
{
	const SESSION_PREFIX = 'auth-';
	// auth_session_put    - auth_name, field_name, response
	// responce_xml_parser - response
	var $auth_name, $field_name, $response;

	static function auth_session_get(){
		$val = Auth::getAuthSession(self::message_md5());
		if (empty($val)) {
			return array();
		}
		return self::parse_message($val);
	}

	static function auth_session_put()
	{
		$message = '';
		foreach(array_merge(array('api','ts'),$this->field_name) as $key) {
		// foreach($this->field_name as $key) {
			$message .= (empty($message)) ? '' : '::'; // delm
			$message .= $key.'$$';
			switch($key) {
			case 'api':
				$message .= $this->auth_name;
				break;
			case 'ts':
				$message .= UTIME;
				break;
			default:
				$message .= Utility::encode($this->response[$key]);
			}
		}
		Auth::setAuthSession(self::message_md5(),$message);

		if ($this->auth_name != 'openid_verify') {
			log_write('login','');
		}
	}

	function auth_session_unset()
	{
		// return session_unregister($this->message_md5());
		Auth::unsetAuthSession(self::message_md5());
	}

	static function parse_message($message)
	{
		$rc = array();
		$tmp = explode('::',trim($message));
		for($i=0; $i<count($tmp); $i++) {
			if ($tmp[$i]) {
				$tmp2 = explode('$$',$tmp[$i]);
				if ( isset($tmp2[1]) ) {
					$rc[$tmp2[0]] = decode($tmp2[1]);
				}
			}
		}
		return $rc;
	}

	// response
	function responce_xml_parser($data)
	{
		$xml_parser = xml_parser_create();
		xml_parse_into_struct($xml_parser, $data, $val, $index);
		xml_parser_free($xml_parser);

		foreach($val as $x) {
			if ($x['type'] != 'complete') continue;
			$this->response[strtolower($x['tag'])] = $x['value'];
		}
	}

	static function message_md5()
	{
		// return md5($this->auth_name.'_message_'.get_script_absuri().session_id());
		return self::SESSION_PREFIX.md5(Router::get_script_absuri().session_id());
	}

}

/* End of file auth_api.cls.php */
/* Location: ./wiki-common/lib/auth_api.cls.php */
