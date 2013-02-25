<?php
/**
 * PukiWiki Plus! livedoor 認証処理
 *
 * @copyright   Copyright &copy; 2007-2009, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @author      Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @version     $Id: livedoor.inc.php,v 0.8.1 2010/12/26 17:24:00 Logue Exp $
 * @license     http://opensource.org/licenses/gpl-license.php GNU Public License (GPL2)
 */

use PukiWiki\Auth\Auth;
use PukiWiki\Auth\AuthLivedoor;
use PukiWiki\Router;
use PukiWiki\Utility;

function plugin_livedoor_init()
{
	$msg = array(
		'_livedoor_msg' => array(
			'msg_logout'		=> T_("logout"),
			'msg_logined'		=> T_("%s has been approved by livedoor."),
			'msg_invalid'		=> T_("The function of livedoor is invalid."),
			'msg_not_found'		=> T_("pkwk_session_start() doesn't exist."),
			'msg_not_start'		=> T_("The session is not start."),
			'msg_livedoor'		=> T_("livedoor"),
			'btn_login'			=> T_("LOGIN(livedoor)"),
		)
	);
	set_plugin_messages($msg);
}

function plugin_livedoor_convert()
{
	global $vars,$auth_api,$_livedoor_msg;

	if (! $auth_api['livedoor']['use']) return '<p>'.$_livedoor_msg['msg_invalid'].'</p>';

	$obj = new AuthLivedoor();
	$name = $obj->getSession();
	if (isset($name['livedoor_id'])) {
		/*
		$logout_url = $script.'?plugin=livedoor';
		if (! empty($vars['page'])) {
			$logout_url .= '&amp;page='.rawurlencode($vars['page']).'&amp;logout';
		}
		*/
		
		$logout_url = Router::get_cmd_uri('livedoor',$vars['page']).'&amp;logout';

		return <<<EOD
<div>
	<label>livedoor</label>:
	{$name['livedoor_id']}
	(<a href="$logout_url">{$_livedoor_msg['msg_logout']}</a>)
</div>

EOD;
	}

	// 他でログイン
	$auth_key = Auth::get_user_name();
	if (! empty($auth_key['nick'])) return '';

	// ボタンを表示するだけ
	/*
	$login_url = $script.'?plugin=livedoor';
	if (! empty($vars['page'])) {
		$login_url .= '&amp;page='.rawurlencode($vars['page']);
	}
	$login_url .= '&amp;login';
	*/
	$login_url = get_cmd_uri('livedoor',$vars['page']).'&amp;login';

	return <<<EOD
<form action="$login_url" method="post">
	<div>
		<input type="submit" value="{$_livedoor_msg['btn_login']}" />
	</div>
</form>

EOD;

}

function plugin_livedoor_inline()
{
	global $vars,$auth_api,$_livedoor_msg;

	if (! $auth_api['livedoor']['use']) return $_livedoor_msg['msg_invalid'];
	
	$obj = new AuthLivedoor();
	$name = $obj->getSession();

	if (!empty($name['api']) && $obj->auth_name !== $name['api']) return;

	if (isset($name['livedoor_id'])) {
		/*
		$logout_url = $script.'?plugin=livedoor';
		if (! empty($vars['page'])) {
			$logout_url .= '&amp;page='.rawurlencode($vars['page']).'&amp;logout';
		}
		*/
		$logout_url = Router::get_cmd_uri('livedoor',$vars['page']).'&amp;logout';
		return sprintf($_livedoor_msg['msg_logined'],$name['livedoor_id']) .
			'(<a href="'.$logout_url.'">'.$_livedoor_msg['msg_logout'].'</a>)';
	}

	$auth_key = Auth::get_user_name();
	if (! empty($auth_key['nick'])) return $_livedoor_msg['msg_livedoor'];

	$login_url = plugin_livedoor_jump_url(1);
	return '<a href="'.$login_url.'">'.$_livedoor_msg['msg_livedoor'].'</a>';
}

function plugin_livedoor_action()
{
	global $vars,$auth_api,$_livedoor_msg;

	if (! $auth_api['livedoor']['use']) return '';


	// LOGIN
	if (isset($vars['login'])) {
		Utility::redirect(plugin_livedoor_jump_url());
	}

	$obj = new auth_livedoor();

	// LOGOUT
	if (isset($vars['logout'])) {
		$obj->unsetSession();
		$page = (empty($vars['page'])) ? '' : decode($vars['page']);
		Utility::redirect(get_page_location_uri($page));
	}

	// AUTH
	$rc = $obj->auth($vars);

	if (! isset($rc['has_error']) || $rc['has_error'] == 'true') {
		// ERROR
		$body = (isset($rc['message'])) ? $rc['message'] : 'unknown error.';
		$die_message($body);
	}

	$obj->setSession();
	Utility::redirect(get_page_location_uri($obj->get_return_page()));
}

function plugin_livedoor_jump_url($inline=0)
{
	global $vars;
	$obj = new AuthLivedoor();
	$url = $obj->make_login_link($vars['page']);
	return ($inline) ? $url : str_replace('&amp;','&',$url);
}

function plugin_livedoor_get_user_name()
{
	global $auth_api;
	// role,name,nick,profile
	if (! $auth_api['livedoor']['use']) return array('role'=>Auth::ROLE_GUEST,'nick'=>'');
	$obj = new auth_livedoor();
	$msg = $obj->getSession();
	$info = 'http://www.livedoor.com/';
	if (! empty($msg['livedoor_id']))
		return array('role'=>AuthLivedoor::ROLE_AUTH_LIVEDOOR,'nick'=>$msg['livedoor_id'],'key'=>$msg['livedoor_id'],'profile'=>$info);
	return array('role'=>Auth::ROLE_GUEST,'nick'=>'');
}

/* End of file livedoor.inc.php */
/* Location: ./wiki-common/plugin/livedoor.inc.php */
