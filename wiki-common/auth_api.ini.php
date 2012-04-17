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
		'use'		=> true,
		'appId'		=> '1291914271552false5',
		'secret'	=> '6b46affalse696748a62557397c7739d37bf',
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
?>
