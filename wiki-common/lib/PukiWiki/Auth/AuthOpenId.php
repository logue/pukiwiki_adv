<?php

namespace PukiWiki\Auth;

use PukiWiki\Auth\AuthApi;

class AuthOpenId extends AuthApi
{
	function __construct()
	{
		$this->auth_name = 'openid';
		// nickname,email,fullname,dob,gender,postcode,country,language,timezone
		$this->field_name = array('author','nickname','email','local_id','identity_url','fullname');
		$this->response = array();
	}
}