<?php
return array(
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
	'Facebook'	=> array(
		'use'		=> false,
		'appId'		=> '',
		'secret'	=> '',
		'cookie'	=> true,
		'scope'		=> '',
		'display'	=> false
	),
	// Twitter
	'Twitter'	=> array(
		'use'		=> false,
		'key'		=> '',
		'secret'	=> ''
	)
);

/* End of file auth_api.ini.php */
/* Location: ./wiki-common/auth_api.ini.php */