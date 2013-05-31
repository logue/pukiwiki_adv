<?php
// see http://pukiwiki.cafelounge.net/plus/?Documents%2FUser%20management
use PukiWiki\Auth\Auth;
global $adminname, $adminpass;
/*
|Auth::ROLE_ADMIN           |サイト管理者    |
|Auth::ROLE_CONTENTS_ADMIN  |コンテンツ管理者|
|Auth::ROLE_ENROLLEE        |登録者(会員)    |
*/

return array(
	$adminname	=> array($adminpass,Auth::ROLE_ADMIN),	// Do not change
	// Username => array(password, role, group, home, mypage),
	'bar'	=> array('{x-php-md5}f53ae779077e987718cc285b14dfbe86'), // md5('bar_passwd')
	'hoge'	=> array('{SMD5}OzJo/boHwM4q5R+g7LCOx2xGMkFKRVEx'), // SMD5 'hoge_passwd'
);

/* End of file auth_users.ini.php */
/* Location: ./wiki-common/auth_users.ini.php */