<?php
$auth_api = array(
	// Basic or Digest
	'plus' => array(
		'use'			=> 0,
		'displayname'	=> 'Normal',
	),
	// OpenID
	'openid' => array(
		'use'		=> 0,
		'mixi' => array(
			'my_id'				=> array(''),	// 'userid1','userid2', ...
			'community_id'		=> array(''),	// 'community1', 'community2', ...
		),
	),
	// GFC
	'auth_gfc' => array(
		'use'			=> 0,
		'site'			=> '',
		'hidden_login'  => 1,
	),
	// RemoteIP
	'remoteip' => array(
		'use'			=> 0,
		'hidden'		=> 1,
	),
	// Hatena
	'hatena' => array(
		'use'			=> 0,
		'api_key'		=> '',
		'sec_key'		=> '',
	),
	// livedoor Auth
	'livedoor'  => array(
		'use'			=> 0,
		'app_key'		=> '',
		'sec_key'		=> '',
	),
	// TypeKey
	'typekey' => array(
		'use'			=> 0,
		'site_token'	=> '',
		'need_email'	=> 0,
	),
	// JugemKey
	'jugemkey'  => array(
		'use'			=> 0,
		'api_key'		=> '',
		'sec_key'		=> '',
	),
	// QueryStringAuth
	'querystringauth'	=> array(
		'use'		=> 0,
		'hidden'	=> 1,
	),
);
?>
