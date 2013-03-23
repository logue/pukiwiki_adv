<?
/**
 * PukiWiki Plus! JugemKey 認証処理
 *
 * @copyright   Copyright &copy; 2006-2009, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @author      Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @version     $Id: jugemkey.inc.php,v 0.15 2010/12/26 17:18:00 Logue Exp $
 * @license     http://opensource.org/licenses/gpl-license.php GNU Public License (GPL2)
 */

use PukiWiki\Auth\Auth;
use PukiWiki\Auth\AuthJugem;
use PukiWiki\Router;
use PukiWiki\Utility;

function plugin_jugemkey_init()
{
	$msg = array(
	  '_jugemkey_msg' => array(
		'msg_logout'		=> T_("logout"),
		'msg_logined'		=> T_("%s has been approved by JugemKey."),
		'msg_invalid'		=> T_("The function of JugemKey is invalid."),
		'msg_not_found'		=> T_("pkwk_session_start() doesn't exist."),
		'msg_not_start'		=> T_("The session is not start."),
		'msg_jugemkey'		=> T_("JugemKey"),
		'btn_login'			=> T_("LOGIN(JugemKey)"),
		'msg_userinfo'		=> T_("JugemKey user information"),
		'msg_user_name'		=> T_("User Name"),
	  )
	);
        set_plugin_messages($msg);
}

function plugin_jugemkey_convert()
{
	global $script,$vars,$auth_api,$_jugemkey_msg;

	if (! $auth_api['jugemkey']['use']) return '<p>'.$_jugemkey_msg['msg_invalid'].'</p>';

	$obj = new AuthJugem();
	$name = $obj->getSession();
	if (isset($name['title'])) {
		// $name = array('title','ts','token');
		/*
		$logout_url = $script.'?plugin=jugemkey';
		if (! empty($vars['page'])) {
			$logout_url .= '&amp;page='.rawurlencode($vars['page']).'&amp;logout';
		}
		*/
		$logout_url = Router::get_cmd_uri('jugemkey',$vars['page']).'&amp;logout';

		return <<<EOD
<div>
	<label>JugemKey</label>:
	{$name['title']}
	(<a href="$logout_url">{$_jugemkey_msg['msg_logout']}</a>)
</div>

EOD;
        }

	// 他でログイン
	$auth_key = Auth::get_user_name();
	if (! empty($auth_key['nick'])) return '';

	// ボタンを表示するだけ
	$login_url = $script.'?cmd=jugemkey';
	if (! empty($vars['page'])) {
		$login_url .= '&amp;page='.rawurlencode($vars['page']);
	}
	$login_url .= '&amp;login';

	return <<<EOD
<form action="$login_url" method="post">
	<div>
		<input type="submit" value="{$_jugemkey_msg['btn_login']}" />
	</div>
</form>

EOD;
}

function plugin_jugemkey_inline()
{
	global $script,$vars,$auth_api,$_jugemkey_msg;

	if (! $auth_api['jugemkey']['use']) return $_jugemkey_msg['msg_invalid'];

		$obj = new AuthJugem();
        $name = $obj->getSession();

	if (!empty($name['api']) && $obj->auth_name !== $name['api']) return;

	if (isset($name['title'])) {
		// $name = array('title','ts','token');
		$link = $name['title'];
		$logout_url = $script.'?cmd=jugemkey';
		if (! empty($vars['page'])) {
			$logout_url .= '&amp;page='.rawurlencode($vars['page']).'&amp;logout';
		}
		return sprintf($_jugemkey_msg['msg_logined'],$link) .
			'(<a href="'.$logout_url.'">'.$_jugemkey_msg['msg_logout'].'</a>)';
        }

	$auth_key = Auth::get_user_name();
	if (! empty($auth_key['nick'])) return $_jugemkey_msg['msg_jugemkey'];

	$login_url = plugin_jugemkey_jump_url(1);
	return '<a href="'.$login_url.'">'.$_jugemkey_msg['msg_jugemkey'].'</a>';
}

function plugin_jugemkey_action()
{
	global $vars,$auth_api,$_jugemkey_msg;

	if (! $auth_api['jugemkey']['use']) return '';

	// LOGIN
	if (isset($vars['login'])) {
		Utility::redirect(plugin_jugemkey_jump_url());
		die();
	}

	$obj = new AuthJugem();

	// LOGOUT
	if (isset($vars['logout'])) {
		$obj->unsetSession();
		Utility::redirect();
	}

	// Get token info
	if (isset($vars['userinfo'])) {
		$rc = $obj->get_userinfo($vars['token']);
		if ($rc['rc'] != 200) {
			$msg = (empty($rc['error'])) ? '' : ' ('.$rc['error'].')';
			Utility::dieMessage('JugemKey: RC='.$rc['rc'].$msg);
		}

		$body = '<h3>'.$_jugemkey_msg['msg_userinfo'].'</h3>'.
			'<strong>'.$_jugemkey_msg['msg_user_name'].': '.$rc['title'].'</strong>';
		return array('msg'=>'JugemKey', 'body'=>$body);
	}

	// AUTH
	$rc = $obj->auth($vars['frob']);
	if ($rc['rc'] != 200) {
		$msg = (empty($rc['error'])) ? '' : ' ('.$rc['error'].')';
		Utility::dieMessage('JugemKey: '.$rc['rc'].$msg);
	}

	$obj->setSession();
	Utility::redirect();
	die();
}

function plugin_jugemkey_jump_url($inline=0)
{
	global $vars;
	$page = (empty($vars['page'])) ? '' : $vars['page'];
	$obj = new AuthJugem();
	$url = $obj->make_login_link();
	return ($inline) ? $url : str_replace('&amp;','&',$url);
}

function plugin_jugemkey_get_user_name()
{
	global $auth_api;
	if (! $auth_api['jugemkey']['use']) return array('role'=>Auth::ROLE_GUEST,'nick'=>'');

	$obj = new AuthJugemkey();
	$msg = $obj->getSession();
	// FIXME
	// Because user information can be acquired by token only at online, it doesn't mount. 
	// $info = (empty($msg['token'])) ? '' : get_resolve_uri('jugemkey','', '', 'token='.$msg['token'].'%amp;userinfo');
	// Only, it leaves it only as a location of attestation by JugemKey.
	$info = 'http://jugemkey.jp/';
	if (! empty($msg['title'])) return array('role'=>AuthJugemkey::ROLE_AUTH_JUGEMKEY,'nick'=>$msg['title'],'profile'=>$info,'key'=>$msg['title']);
	return array('role'=>Auth::ROLE_GUEST,'nick'=>'');
}

/* End of file jugemkey.inc.php */
/* Location: ./wiki-common/plugin/jugemkey.inc.php */
