<?php
$auth_api = array(
	// Basic or Digest
	'plus' => array(
		'use'			=> true,
		'displayname'	=> 'Normal',
	),
	// OpenID
	'openid' => array(
		'use'		=> false,
		'mixi' => array(
			'my_id'			=> array(''),	// 'userid1','userid2', ...
			'community_id'	=> array(''),	// 'community1', 'community2', ...
		),
	),
	// GFC
	'auth_gfc' => array(
		'use'			=> false,
		'site'			=> '',
		'hidden_login'  => true,
	),
	// RemoteIP
	'remoteip' => array(
		'use'			=> false,
		'hidden'		=> true,
	),
	// Hatena
	'hatena' => array(
		'use'			=> false,
		'api_key'		=> '',
		'sec_key'		=> '',
	),
	// livedoor Auth
	'livedoor'  => array(
		'use'			=> false,
		'app_key'		=> '',
		'sec_key'		=> '',
	),
	// TypeKey
	'typekey' => array(
		'use'			=> false,
		'site_token'	=> '',
		'need_email'	=> false,
	),
	// JugemKey
	'jugemkey'  => array(
		'use'			=> false,
		'api_key'		=> '',
		'sec_key'		=> '',
	),
	// QueryStringAuth
	'querystringauth'	=> array(
		'use'		=> false,
		'hidden'	=> true,
	),
	// Facebook
	'facebook'	=> array(
		'use'		=> false,
		'appId'		=> '',
		'secret'	=> '',
		'cookie'	=> true,
		'scope'		=> '',
		'display'	=> false
	),
	'twitter'	=> array(
		'use'		=> false,
		'key'		=> '',
		'secret'	=> ''
	)
);

/* End of file auth_api.ini.php */
/* Location: ./wiki-common/auth_api.ini.php */