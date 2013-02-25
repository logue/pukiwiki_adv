<?php
/**
 * PukiWiki Plus! TypeKey 認証処理
 *
 * @copyright   Copyright &copy; 2006-2009, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @author      Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @version     $Id: typekey.inc.php,v 0.16 2009/06/11 01:34:00 upk Exp $
 * @license     http://opensource.org/licenses/gpl-license.php GNU Public License (GPL2)
 */
use PukiWiki\Auth\Auth;
use PukiWiki\Auth\AuthTypekey;
use PukiWiki\Router;
use PukiWiki\Utility;

function plugin_typekey_init()
{
	$msg = array(
	  '_typekey_msg' => array(
		'msg_typekey'		=> T_("TypeKey"),
		'msg_logout'		=> T_("logout"),
		'msg_logined'		=> T_("%s has been approved by TypeKey."),
		'msg_error'			=> T_("site_token must be set."),
		'msg_invalid'		=> T_("The function of TypeKey is invalid."),
		'msg_not_found'		=> T_("pkwk_session_start() doesn't exist."),
		'msg_not_start'		=> T_("The session is not start."),
		'btn_login'			=> T_("LOGIN(TypeKey)"),
	  )
	);
	set_plugin_messages($msg);
}

function plugin_typekey_convert()
{
	global $vars,$_typekey_msg,$auth_api;

	if ($auth_api['typekey']['use'] != 1) return '<p>'.$_typekey_msg['msg_invalid'].'</p>';
	if (empty($auth_api['typekey']['site_token'])) return '<p>'.$_typekey_msg['msg_error'].'</p>';

	$obj = new AuthTypekey();

	$user = $obj->get_profile_link();
	if (! empty($user)) {
		$page  = get_script_absuri().rawurlencode('?plugin=typekey');
		if (! empty($vars['page'])) {
			$page .= rawurlencode('&page='.$vars['page']);
		}
		$logout_url = $obj->typekey_logout_url($page).rawurlencode('&logout');
		return <<<EOD
<div>
	<label>TypeKey</label>:
	$user(<a href="$logout_url">{$_typekey_msg['msg_logout']}</a>)
</div>

EOD;
	}

	// 他でログイン
	$auth_key = Auth::get_user_name();
	if (! empty($auth_key['nick'])) return '';

	// ボタンを表示するだけ
	$login_url = plugin_typekey_jump_url();
	return <<<EOD
<form action="$login_url" method="post" class="typekey_form">
	<input type="submit" value="{$_typekey_msg['btn_login']}" />
</form>

EOD;
}

function plugin_typekey_inline()
{
	global $vars,$_typekey_msg,$auth_api;

	if ($auth_api['typekey']['use'] != 1) return $_typekey_msg['msg_invalid'];
	if (empty($auth_api['typekey']['site_token'])) return $_typekey_msg['msg_error'];

	$obj = new AuthTypekey();
	$link = $obj->get_profile_link();
	if ($link === false) return '';

	if (! empty($link)) {
		// 既に認証済
		$page  = get_script_absuri().rawurlencode('?plugin=typekey');
		if (! empty($vars['page'])) {
			$page .= rawurlencode('&page='.$vars['page']);
		}
		return sprintf($_typekey_msg['msg_logined'],$link) .
			'(<a href="'.$obj->typekey_logout_url($page).rawurlencode('&logout').'">' .
			$_typekey_msg['msg_logout'].'</a>)';
	}

	$auth_key = Auth::get_user_name();
	if (! empty($auth_key['nick'])) return $_typekey_msg['msg_typekey'];

	return '<a href="'.plugin_typekey_jump_url().'">'.$_typekey_msg['msg_typekey'].'</a>';
}

function plugin_typekey_action()
{
	global $vars,$auth_api;

	if (empty($auth_api['typekey']['site_token'])) return '';

	$obj = new AuthTypekey();
	$obj->set_regkeys();
	$obj->set_need_email($auth_api['typekey']['need_email']);
	$obj->set_sigKey($vars);

	$page = (empty($vars['page'])) ? '' : $vars['page'];

	if (! $obj->auth()) {
		if (isset($vars['logout'])) {
			$obj->unsetSession();
		}
		Utility::redirect(get_page_location_uri($page));
	}

	// 認証成功
	$obj->setSession();
	Utility::redirect(get_page_location_uri($page));
}

function plugin_typekey_jump_url()
{
	global $auth_api,$vars;

	$page  = get_script_absuri().rawurlencode('?plugin=typekey');
	if (! empty($vars['page'])) {
		$page .= rawurlencode('&page='.$vars['page']);
	}

	$obj = new AuthTypekey($auth_api['typekey']['site_token']);
	$obj->set_need_email($auth_api['typekey']['need_email']);
	return $obj->typekey_login_url($page);
}

function plugin_typekey_get_user_name()
{
	global $auth_api;
	// role,name,nick,profile
	if (! $auth_api['typekey']['use']) return array('role'=>Auth::ROLE_GUEST,'nick'=>'');
	$obj = new AuthTypekey();
	$msg = $obj->getSession();
	if (! empty($msg['nick']) && ! empty($msg['name'])) {
		return array('role'=>AuthTypekey::ROLE_AUTH_TYPEKEY,'name'=>$msg['name'],'nick'=>$msg['nick'],'profile'=>AuthTypekey::TYPEKEY_URL_PROFILE.$msg['name'],'key'=>$msg['name']);
	}
	return array('role'=>Auth::ROLE_GUEST,'nick'=>'');
}

/* End of file typekey.inc.php */
/* Location: ./wiki-common/plugin/typekey.inc.php */
