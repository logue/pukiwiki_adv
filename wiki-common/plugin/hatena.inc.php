<?php
/**
 * PukiWiki Plus! Hatena 認証処理
 *
 * @copyright   Copyright &copy; 2006,2008, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @author      Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @version     $Id: hatena.inc.php,v 0.14.1 2010/12/26 17:17:00 Logue Exp $
 * @license     http://opensource.org/licenses/gpl-license.php GNU Public License (GPL2)
 */

use PukiWiki\Auth\Auth;
use PukiWiki\Auth\AuthHatena;
use PukiWiki\Router;
use PukiWiki\Utility;

function plugin_hatena_init()
{
	$msg = array(
		'_hatena_msg' => array(
			'msg_logout'		=> T_("logout"),
			'msg_logined'		=> T_("%s has been approved by Hatena."),
			'msg_invalid'		=> T_("The function of Hatena is invalid."),
			'msg_not_found'		=> T_("pkwk_session_start() doesn't exist."),
			'msg_not_start'		=> T_("The session is not start."),
			'msg_hatena'		=> T_("Hatena"),
			'btn_login'			=> T_("LOGIN(Hatena)"),
		)
	);
	set_plugin_messages($msg);
}

function plugin_hatena_convert()
{
	global $script,$vars,$auth_api,$_hatena_msg;

	if (! $auth_api['hatena']['use']) return '<p>'.$_hatena_msg['msg_invalid'].'</p>';

	$obj = new AuthHatena();
	$name = $obj->getSession();
	if (isset($name['name'])) {
		// $name = array('name','ts','image_url','thumbnail_url');
		/*
		$logout_url = $script.'?plugin=hatena';
		if (! empty($vars['page'])) {
			$logout_url .= '&amp;page='.rawurlencode($vars['page']);
		}
		$logout_url .= '&amp;logout';
		*/
		$logout_url = Router::get_cmd_uri('hatena',$vars['page']).'&amp;logout';

		return <<<EOD
<div>
	<label>Hatena</label>:
	{$name['name']}
	<img src="{$name['thumbnail_url']}" alt="id:{$name['name']}" />
	(<a href="$logout_url">{$_hatena_msg['msg_logout']}</a>)
</div>

EOD;
	}

	// 他でログイン
	$auth_key = Auth::get_user_name();
	if (! empty($auth_key['nick'])) return '';

	// ボタンを表示するだけ
	$login_url = $script.'?cmd=hatena';
	if (! empty($vars['page'])) {
		$login_url .= '&amp;page='.rawurlencode($vars['page']);
	}
	$login_url .= '&amp;login';

	return <<<EOD
<form action="$login_url" method="post">
	<input type="submit" value="{$_hatena_msg['btn_login']}" />
</form>

EOD;

}

function plugin_hatena_inline()
{
	global $script,$vars,$auth_api,$_hatena_msg;

	if (! $auth_api['hatena']['use']) return $_hatena_msg['msg_invalid'];

	$obj = new AuthHatena();
	$name = $obj->getSession();
	if (!empty($name['api']) && $obj->auth_name !== $name['api']) return;

	if (isset($name['name'])) {
		// $name = array('name','ts','image_url','thumbnail_url');
		$link = $name['name'].'<img src="'.$name['thumbnail_url'].'" alt="id:'.$name['name'].'" />';
		$logout_url = $script.'?cmd=hatena';
		if (! empty($vars['page'])) {
			$logout_url .= '&amp;page='.rawurlencode($vars['page']);
		}
		$logout_url .= '&amp;logout';
		return sprintf($_hatena_msg['msg_logined'],$link) .
			'(<a href="'.$logout_url.'">'.$_hatena_msg['msg_logout'].'</a>)';
	}

	$auth_key = Auth::get_user_name();
	if (! empty($auth_key['nick'])) return $_hatena_msg['msg_hatena'];

	$login_url = plugin_hatena_jump_url(1);
	return '<a href="'.$login_url.'">'.$_hatena_msg['msg_hatena'].'</a>';
}

function plugin_hatena_action()
{
	global $vars,$auth_api;

	if (! $auth_api['hatena']['use']) return '';

	$page = (empty($vars['page'])) ? '' : Utility::decode($vars['page']);

	// LOGIN
	if (isset($vars['login'])) {
		Utility::redirect(plugin_hatena_jump_url());
	}

	$obj = new AuthHatena();

	// LOGOUT
	if (isset($vars['logout'])) {
		$obj->unsetSession();
		Utility::redirect(get_page_location_uri($page));
	}

	// AUTH
	$rc = $obj->auth($vars['cert']);

	if (! isset($rc['has_error']) || $rc['has_error'] == 'true') {
		// ERROR
		$body = (isset($rc['message'])) ? $rc['message'] : 'unknown error.';
		Utility::dieMessage($body);
	}

	$obj->setSession();
	Utility::redirect(get_page_location_uri($page));
}

function plugin_hatena_jump_url($inline=0)
{
	global $vars;
	$obj = new AuthHatena();
	$url = $obj->make_login_link(array('page'=>$vars['page'],'plugin'=>'hatena'));
	return ($inline) ? $url : str_replace('&amp;','&',$url);
}

function plugin_hatena_get_user_name()
{
	global $auth_api;
	// role,name,nick,profile
	if (! $auth_api['hatena']['use']) return array('role'=>Auth::ROLE_GUEST,'nick'=>'');
	$obj = new AuthHatena();
	$msg = $obj->getSession();

	if (! empty($msg['name'])) return array('role'=>AuthHatena::ROLE_AUTH_HATENA,'nick'=>$msg['name'],'profile'=>AuthHatena::HATENA_URL_PROFILE.$msg['name'],'key'=>$msg['name']);
	return array('role'=>Auth::ROLE_GUEST,'nick'=>null);
}
/* End of file hatena.inc.php */
/* Location: ./wiki-common/plugin/hatena.inc.php */