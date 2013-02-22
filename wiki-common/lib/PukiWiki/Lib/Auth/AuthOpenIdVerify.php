<?php

namespace PukiWiki\Lib\Auth;

use PukiWiki\Lib\Auth\AuthOpenId;

class AuthOpenIdVerify extends AuthOpenId
{
	function __construct()
	{
		$this->auth_name = 'openid_verify';
		// $this->field_name = array('openid.server','openid.delegate','ts','page');
		$this->field_name = array('author','server_url','local_id','ts','page');
		$this->response = array();
	}
	function get_host()
	{
		$msg = $this->auth_session_get();
		$arr = parse_url($msg['server_url']);
		return strtolower($arr['host']);
	}
	function get_delegate()
	{
		$msg = $this->auth_session_get();
		return $msg['local_id'];

	}
}