<?php
/**
 * PukiPlus ログインプラグイン
 *
 * @copyright   Copyright &copy; 2004-2010, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @version     $Id: login.php,v 0.23 2012/04/10 18:02:00 Logue Exp $
 * @license     http://opensource.org/licenses/gpl-license.php GNU Public License (GPL2)
 */
// defined('LOGIN_USE_AUTH_DEFAULT') or define('LOGIN_USE_AUTH_DEFAULT', 1);

use PukiWiki\Auth\Auth;
use PukiWiki\Renderer\RendererFactory;
use PukiWiki\Utility;

/*
 * 初期処理
 */
function plugin_login_init()
{
	$messages = array(
	'_login_msg' => array(
		'msg_username'		=> T_('UserName'),
		'msg_auth_guide'	=> T_('Please attest it with %s to write the comment.'),
		'btn_login'			=> T_('Login'),
		'btn_logout'		=> T_('Logout'),
		'err_notusable'		=>
			'<p class="message_box ui-state-error ui-corner-all">' .
			T_('#login() : Could not use auth function. Please check <var>auth_api.ini.php</var> setting.').
			'</p>',
		'err_auth'			=> T_('Authorization Required'),
		'err_auth_guide'	=>
			'<p class="message_box ui-state-error ui-corner-all"><span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-alert"></span>' .
			T_('This server could not verify that you are authorized to access the document requested. Either you supplied the wrong credentials (e.g., bad password), or your browser doesn\'t understand how to supply the credentials required.') .
			'</p>'
		)
	);
	set_plugin_messages($messages);
}

/*
 * ブロック型プラグイン
 */
function plugin_login_convert()
{
	global $vars, $auth_api, $_login_msg;

	@list($type) = func_get_args();

	$auth_key = Auth::get_user_info();

	// LOGIN
	if (!empty($auth_key['key'])) {
		if (isset($auth_api[$auth_key['api']]['hidden_login']) && $auth_api[$auth_key['api']]['hidden_login']) {
			return  $_login_msg['err_notusable'] ;
		}

		if ($auth_key['api'] == 'plus') {
			return <<<EOD
<div>
        <label>{$_login_msg['msg_username']}</label>:
        {$auth_key['key']}
</div>

EOD;
		}
		if (exist_plugin($auth_key['api'])) {
			return do_plugin_convert($auth_key['api']);
		}
		return $_login_msg['err_notusable'];
	}

	$ret = array();

	$ret[] = '<form action="' . get_script_uri() . '" method="post">';
	$ret[] = '<input type="hidden" name="cmd" value="login" />';
	$ret[] = (isset($type)) ? '<input type="hidden" name="type" value="' . htmlsc($type, ENT_QUOTES) . '" />' : null;
	$ret[] = (isset($vars['page'])) ? '<input type="hidden" name="type" value="' . $vars['page'] . '" />' : null;
	$ret[] = '<div class="login_form">';
	$select = '';
	//if (LOGIN_USE_AUTH_DEFAULT) {
	//	$select .= '<option value="plus" selected="selected">Normal</option>';
	//}
	$sw_ext_auth = false;
	foreach($auth_api as $api=>$val) {
		if (! $val['use']) continue;
		if (isset($val['hidden']) && $val['hidden']) continue;
		if (isset($val['hidden_login']) && $val['hidden_login']) continue;
		$displayname = (isset($val['displayname'])) ? $val['displayname'] : $api;
		if ($api !== 'plus') $sw_ext_auth = true;
		$select .= '<option value="'.$api.'">'.$displayname.'</option>'."\n";
	}

	if (empty($select)) return $_login_msg['err_notusable']; // 認証機能が使えない

	if ($sw_ext_auth) {
		// 外部認証がある
		$ret[] = '<select name="api">'. "\n" .$select.'</select>';
	} else {
		// 通常認証のみなのでボタン
		$ret[] = '<input type="hidden" name="api" value="plus" />';
	}
	$ret[] = '<input type="submit" value="' . $_login_msg['btn_login'] . '" />';
	$ret[] = '</div>';
	$ret[] = '</form>';
	return join("\n",$ret);
}

function plugin_login_inline()
{
	if (PKWK_READONLY != Auth::ROLE_AUTH) return '';

	$auth_key = Auth::get_user_info();
	

	// Offline
	if (empty($auth_key['key'])) {
		return plugin_login_auth_guide();
	}

	// Online
	return exist_plugin($auth_key['api']) ? do_plugin_inline($auth_key['api']) : '';
}

function plugin_login_auth_guide()
{
	global $auth_api,$_login_msg;

	$inline = '';
	$sw = true;
	foreach($auth_api as $api=>$val) {
		if ($val['use']) {
			if (isset($val['hidden']) && $val['hidden']) continue;
			if (! exist_plugin($api)) continue;
			$inline .= ($sw) ? '' : ',';
			$sw = false;
			$inline .= '&'.$api.'();';
		}
	}

	if ($sw) return '';
	return RendererFactory::factory(sprintf($_login_msg['msg_auth_guide'],$inline));
}

/*
 * アクションプラグイン
 */
function plugin_login_action()
{
	global $vars,$auth_type, $auth_users, $realm, $_login_msg;

	$api = isset($vars['api']) ? $vars['api'] : 'plus';

	if ($api !== 'plus') {
		if (! exist_plugin($vars['api'])) return;
		$call_api = 'plugin_'.$vars['api'].'_jump_url';
		Utility::redirect( $call_api());
		exit();
	}

	// NTLM, Negotiate 認証 (IIS 4.0/5.0)
	$srv_soft = (defined('SERVER_SOFTWARE'))? SERVER_SOFTWARE : $_SERVER['SERVER_SOFTWARE'];
	if (substr($srv_soft,0,9) == 'Microsoft') {
		Auth::ntlm_auth();
		login_return_page();
	}

	switch($auth_type) {
	case 1:
		if (! Auth::auth_pw($auth_users)) {
			unset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
			header('HTTP/1.0 401 Unauthorized');
			header('WWW-Authenticate: Basic realm="'.$realm.'"');
		} else {
			// FIXME
			// 認証成功時は、もともとのページに戻れる
			// 下に記述すると認証すら行えないなぁ
			login_return_page();
		}
		break;
	case 2:
		if (! Auth::auth_digest($auth_users)) {
			header('HTTP/1.1 401 Unauthorized');
			header('WWW-Authenticate: Digest realm="'.$realm.
				'", qop="auth", nonce="'.uniqid().'", opaque="'.md5($realm).'"');
		} else {
			login_return_page();
		}
		break;
	//case 3:
	//	plugin_login_session();
	//	break;
	}
	header('HTTP/1.1 401 Unauthorized');
	return array(
		'msg'=>$_login_msg['err_auth'],
		'body'=>$_login_msg['err_auth_guide'],
		'http_code'=>401
	);
}

function login_return_page()
{
	global $vars;

	$page = (empty($vars['page'])) ? '' : $vars['page'];
	log_write('login','');
	Utility::redirect(get_page_location_uri($page));
}
/* End of file login.inc.php */
/* Location: ./wiki-common/plugin/login.inc.php */
