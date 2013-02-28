<?php

namespace PukiWiki\Auth;

use PukiWiki\Auth\AuthOpenId;

/**
 * OpenID認証の確認
 */
class AuthOpenIdVerify extends AuthOpenId
{
	function __construct()
	{
		$this->auth_name = 'openid_verify';
		// $this->field_name = array('openid.server','openid.delegate','ts','page');
		$this->field_name = array('author','server_url','local_id','ts','page');
		$this->response = array();
		parent::__construct();
	}
	function get_host()
	{
		$msg = $this->getSession();
		$arr = parse_url($msg['server_url']);
		return strtolower($arr['host']);
	}
	function get_delegate()
	{
		$msg = $this->getSession();
		return $msg['local_id'];

	}
}