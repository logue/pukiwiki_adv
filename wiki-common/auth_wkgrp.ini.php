<?php
use PukiWiki\Auth\Auth;
global $adminname, $modifier;
/*
|Auth::ROLE_ADMIN           |サイト管理者    |
|Auth::ROLE_CONTENTS_ADMIN  |コンテンツ管理者|
|Auth::ROLE_ENROLLEE        |登録者(会員)    |
*/
return array(
	// 管理人の権限。通常は変更しないでください！
	$adminname		=> array(
		'role'          => Auth::ROLE_ADMIN,    // 権限レベル
		'displayname'   => $modifier,           // 表示名
		'group'         => Auth::ADMIN_GROUP,   // グループ名（auth_group.ini.phpの設定が優先）
		'mypage'        => $modifier            // このユーザのページ名
	),
	// ex. 'user_name' => array('role'=>Auth::ROLE_ADMIN, 'displayname'=>'ななし','group'=>'','home'=>'','mypage'=>''),
	'openid'	=> array(
		// openid_identity (openid.delegate)
		// 'http://profile.livedoor.com/YOURNAME/' => array('role'=>Auth::ROLE_ADMIN),
		// 'http://YOURNAME.openid.ne.jp'          => array('role'=>Auth::ROLE_CONTENTS_ADMIN),
		// 'http://YOURNAME.myopenid.com/'         => array('role'=>Auth::ROLE_CONTENTS_ADMIN),
		// 'https://id.mixi.jp/YOUR_ID'            => array('role'=>Auth::ROLE_ADMIN),
	),
	'auth_gfc'      => array(
		// 'user_name1'	=> array('role'=>Auth::ROLE_ADMIN),
		// 'user_name2'	=> array('role'=>Auth::ROLE_CONTENTS_ADMIN),
	),
	'remoteip'	=> array(
		// 'user_name1' => array('role'=>Auth::ROLE_ADMIN),
		// 'user_name2' => array('role'=>Auth::ROLE_CONTENTS_ADMIN),
	),
	'hatena'	=> array(
		// 'user_name1'	=> array('role'=>Auth::ROLE_ADMIN),
		// 'user_name2'	=> array('role'=>Auth::ROLE_CONTENTS_ADMIN),
	),
	'livedoor'	=> array(
		// 'user_name1' => array('role'=>Auth::ROLE_ADMIN),
		// 'user_name2' => array('role'=>Auth::ROLE_CONTENTS_ADMIN),
	),
	'typekey'	=> array(
		// 'user_name1'	=> array('role'=>Auth::ROLE_ADMIN),
		// 'user_name2'	=> array('role'=>Auth::ROLE_CONTENTS_ADMIN),
	),
	'jugemkey'	=> array(
		// 'user_name1'	=> array('role'=>ROLE_ADM),
		// 'user_name2'	=> array('role'=>Auth::ROLE_CONTENTS_ADMIN),
	),
);

/* End of file auth_wkgrp.ini.php */
/* Location: ./wiki-common/auth_wkgrp.ini.php */
