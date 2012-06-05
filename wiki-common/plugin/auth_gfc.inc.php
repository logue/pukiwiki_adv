<?php
/**
 * PukiWiki Advance Google Friend Connect 認証処理
 *
 * @copyright   Copyright &copy; 2010, Katsumi Saito <jo1upk@users.sourceforge.net>
 * @author      Katsumi Saito <jo1upk@users.sourceforge.net>
 * @version     $Id: auth_gfc.inc.php,v 0.2.2 2012/05/15 19:46:00 Logue Exp $
 * @license     http://opensource.org/licenses/gpl-license.php GNU Public License (GPL2)
 */
require_once(LIB_DIR . 'auth_api.cls.php');

// Google Friend Conect APIのバージョン
// http://code.google.com/intl/ja/apis/friendconnect/
define('GOOGLE_FRIEND_CONNECT_API_VER',1);

class auth_gfc extends auth_api
{
	var $site;
	function auth_gfc()
	{
		global $auth_api;
		$this->auth_name = 'auth_gfc';
		$this->site = $auth_api[$this->auth_name]['site'];
		$this->field_name = array('id','name','displayName','thumbnailUrl','fcauth');
		$this->response = array();
	}
}

function plugin_auth_gfc_init()
{
	global $google_loader, $js_blocks;
	// メッセージ定義
	$msg = array(
		'_auth_gfc_msg' => array(
			'msg_logout'		=> T_("logout"),
			'msg_logined'		=> T_("%s has been approved by GFC."),
			'msg_invalid'		=> T_("The function of GFC is invalid."),
			'msg_not_found'		=> T_("pkwk_session_start() doesn't exist."),
			'msg_not_start'		=> T_("The session is not start."),
			'msg_gfc'			=> T_("GFC"),
			'msg_wait'			=> T_("Please wait for a while."),
			'btn_login'			=> T_("Login"),
			'btn_settings'		=> T_("Settings"),
			'btn_invite'		=> T_("Invite"),
		)
	);
	// GFCを読み込む
	$google_loader[] = array('friendconnect'=>GOOGLE_FRIEND_CONNECT_API_VER);
	// GFCに渡すアドレス（SCRIPTから）
	$js_blocks[] = 'google.friendconnect.container.setParentUrl(SCRIPT);';

	set_plugin_messages($msg);
}

function plugin_auth_gfc_convert()
{
	global $vars,$auth_api,$_auth_gfc_msg, $js_blocks;

	if (! $auth_api['auth_gfc']['use']) return '<p>'.$_auth_gfc_msg['msg_invalid'].'</p>';

	if (! function_exists('pkwk_session_start')) return '<p>'.$_auth_gfc_msg['msg_not_found'].'</p>';
	if (pkwk_session_start() == 0) return '<p>'.$_auth_gfc_msg['msg_not_start'].'</p>';

	$obj  = new auth_gfc();
	$name = $obj->auth_session_get();
	if (!empty($name['api']) && $obj->auth_name !== $name['api']) return ''; // 他でログイン
	$page = (empty($vars['page'])) ? '' : $vars['page'];
	$img  = (empty($name['thumbnailUrl'])) ? '&nbsp;' : '<img src="'.$name['thumbnailUrl'].'" height="32" width="32" title="'.$name['id'].'" />';
	$nick = (empty($name['displayName'])) ? '&nbsp;' : '<strong>'.$name['displayName'].'</strong>';
	$abs  = get_baseuri('abs');

	if (isset($name['name'])) {

		$fc_cookie_id='fcauth'.$obj->site;
		if(!isset($_COOKIE[$fc_cookie_id])){
			// GFC の他のボタンでログアウトした場合
			$obj->auth_session_unset();
			header('Location: '. get_page_location_uri($page));
			die();
		}

		$logout_url = get_cmd_uri('auth_gfc', $page).'&amp;logout';
		// get_location_uri('auth_gfc', $page).'&logout';
		return <<<EOD
<table class="no-js">
 <tr><td rowspan="3">{$img}</td></tr>
 <tr><td>{$nick}</td></tr>
 <tr><td>
<a href="#" onclick="google.friendconnect.requestSettings();">{$_auth_gfc_msg['btn_settings']}</a>
|<a href="#" onclick="google.friendconnect.requestInvite();">{$_auth_gfc_msg['btn_invite']}</a>
|<a href="{$logout_url}" >{$_auth_gfc_msg['msg_logout']}</a>
 </td></tr>
</table>

EOD;
	}

	// ログイン済
	$login_url = get_location_uri('auth_gfc', $page);
	$id = 'auth_gfc_'.uniqid();
	$js_blocks[] = <<<JAVASCRIPT
google.friendconnect.container.loadOpenSocialApi({
	site: '{$obj->site}',
	onload: function() {
		if (!window.timesloaded) {
			window.timesloaded = 1;
		} else {
			window.timesloaded++;
		}
		if (window.timesloaded > 1) {
			window.top.location.href = "{$login_url}";
		}
	}
});
google.friendconnect.renderSignInButton({'id':'{$id}','text':'{$_auth_gfc_msg['btn_login']}','style':'long'});
JAVASCRIPT;
	return '<span id="${id}"></span>';
}

function plugin_auth_gfc_inline()
{
	global $vars,$auth_api,$_auth_gfc_msg;

	if (! $auth_api['auth_gfc']['use']) return $_auth_gfc_msg['msg_invalid'];

	if (! function_exists('pkwk_session_start')) return $_auth_gfc_msg['msg_not_found'];
	if (pkwk_session_start() == 0) return $_auth_gfc_msg['msg_not_start'];

	$obj = new auth_gfc();
	$name = $obj->auth_session_get();
	if (!empty($name['api']) && $obj->auth_name !== $name['api']) return;

	if (isset($name['name'])) {
		/*
		$logout_url = $script.'?plugin=auth_gfc';
		if (! empty($vars['page'])) {
			$logout_url .= '&amp;page='.rawurlencode($vars['page']);
		}
		$logout_url .= '&amp;logout';
		*/
		$logout_url = get_cmd_uri('auth_gfc',$vars['page']).'&amp;logout';
		
		$nick = '<span title="'.$name['id'].'">'.$name['name'].'</span>';
		return sprintf($_auth_gfc_msg['msg_logined'],$nick) .
			'(<a href="'.$logout_url.'">'.$_auth_gfc_msg['msg_logout'].'</a>)';
	}

	$auth_key = auth::get_user_name();
	if (! empty($auth_key['nick'])) return $_auth_gfc_msg['msg_gfc'];

	return plugin_auth_gfc_jump_url();
	//$login_url = plugin_auth_gfc_jump_url();
	//return '<a href="'.$login_url.'">'.$_auth_gfc_msg['msg_gfc'].'</a>';
}

function plugin_auth_gfc_action()
{
	global $vars,$auth_api,$_auth_gfc_msg,$js_blocks;

	if (! $auth_api['auth_gfc']['use']) return '';
	if (! function_exists('pkwk_session_start')) return '';
	if (pkwk_session_start() == 0) return '';

	$page = (empty($vars['page'])) ? '' : decode($vars['page']);
	$die_message = (PLUS_PROTECT_MODE) ? 'die_msg' : 'die_message';
	$obj = new auth_gfc();

	// LOGOUT
	if (isset($vars['logout'])) {
		$obj->auth_session_unset();
		$abs = get_baseuri('abs');
		$return_page = get_page_location_uri($page);

		$retvars['msg'] = $_auth_gfc_msg['msg_logout'];
		$retvars['body'] = '<div>'.$_auth_gfc_msg['msg_wait'].'</div>';
		$js_blocks[] = <<<JAVASCRIPT
google.friendconnect.container.loadOpenSocialApi({
	site: '{$obj->site}',
	onload: function(securityToken) {
		if (!window.timesloaded) {
			window.timesloaded = 1;
			google.friendconnect.requestSignOut();
		} else {
			window.timesloaded++;
		}
		if (window.timesloaded > 1) {
			window.top.location.href = "{$return_page}";
		}
	}
});
JAVASCRIPT;
		return $retvars;
		//header('Location: '. get_page_location_uri($page));
		//die();
	}

	// LOGIN
	$fc_cookie_id='fcauth'.$obj->site;
	if(isset($_COOKIE[$fc_cookie_id])){
		$obj->response['fcauth'] = $_COOKIE[$fc_cookie_id];
	}
	$response = plugin_auth_gfc_oauth($obj->response['fcauth']);
	$obj->response['id']           = $response['auth_gfc']->id;
	$obj->response['name']         = $response['auth_gfc']->name;
	$obj->response['displayName']  = $response['auth_gfc']->displayName;
	$obj->response['thumbnailUrl'] = $response['auth_gfc']->thumbnailUrl;
	$obj->auth_session_put();
	header('Location: '. get_page_location_uri($page));
	die();
}

function plugin_auth_gfc_jump_url()
{
	global $_auth_gfc_msg,$js_blocks;
	$obj  = new auth_gfc();
	$abs  = get_baseuri('abs');
	$js_blocks[] = <<<JAVASCRIPT
google.friendconnect.container.loadOpenSocialApi({
	site: '{$obj->site}',
	onload: function(securityToken) {}
});
JAVASCRIPT;
	return <<<EOD
<a href="#" onclick="google.friendconnect.requestSignIn();" class="no-js">{$_auth_gfc_msg['msg_gfc']}</a>
EOD;
}

function plugin_auth_gfc_get_user_name()
{
	global $auth_api;
	// role,name,nick,profile
	if (! $auth_api['auth_gfc']['use']) return array('role'=>ROLE_GUEST,'nick'=>'');
	$obj = new auth_gfc();
	$msg = $obj->auth_session_get();

	if (! empty($msg['name'])) return array('role'=>ROLE_AUTH_GFC,'nick'=>$msg['displayName'],'profile'=>$msg['name'],'key'=>$msg['id']);
	return array('role'=>ROLE_GUEST,'nick'=>'');
}

function plugin_auth_gfc_oauth($fcauth)
{
	ini_set('include_path', LIB_DIR . 'opensocial/osapi/');
	require_once('osapi.php');
	ini_restore('include_path');

	$gfc_provider = new osapiFriendConnectProvider();
	$gfc_auth = new osapiFCAuth($fcauth);
	$gfc_osapi = new osapi($gfc_provider, $gfc_auth);
	$batch = $gfc_osapi->newBatch();
	$batch->add($gfc_osapi->people->get(array('userId' => '@me')),'auth_gfc');
	return $batch->execute();
}

/* End of file auth_gfc.inc.php */
/* Location: ./wiki-common/plugin/auth_gfc.inc.php */
