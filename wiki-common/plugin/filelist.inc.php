<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: filelist.inc.php,v 1.3.6 2006/01/15 22:50:00 upk Exp $
//
// Filelist plugin: redirect to list plugin
// cmd=filelist
use PukiWiki\Auth\Auth;
function plugin_filelist_init()
{
	global $_filelist_msg;

	$messages = array(
		'_filelist_msg' => array(
			'msg_input_pass'	=> T_('Please input the password for the Administrator.'),
			'btn_exec'			=> T_('Exec'),
			'msg_no_pass'		=> T_('The password is wrong.'),
			'msg_H0_filelist'	=> T_('Page list'),
		)
	);
	set_plugin_messages($messages);
}

function plugin_filelist_action()
{
	global $vars;


	if (! Auth::check_role('role_contents_admin'))
		return do_plugin_action('list');

	if (!isset($vars['pass'])) return filelist_adm('');

	if (! pkwk_login($vars['pass']))
		return filelist_adm('__nopass__');
	return do_plugin_action('list');
}

// 管理者パスワード入力画面
function filelist_adm($pass)
{
	global $_filelist_msg;
	global $vars;

	$msg_pass = $_filelist_msg['msg_input_pass'];
	$btn      = $_filelist_msg['btn_exec'];
	$body = "";

	if ($pass == '__nopass__')
	{
		$body .= "<p><strong>".$_filelist_msg['msg_no_pass']."</strong></p>";
	}

	$script = get_script_uri();
	$body .= <<<EOD
<fieldset>
	<legend>$msg_pass</legend>
	<form action="$script" method="post" class="filelist_form">
		<input type="hidden" name="cmd" value="filelist" />
		<input type="password" name="pass" size="12" />
		<input type="submit" name="ok" value="$btn" />
	</form>
</fieldset>
EOD;
	return array('msg' => $_filelist_msg['msg_H0_filelist'],'body' => $body);
}
/* End of file fileist.inc.php */
/* Location: ./wiki-common/plugin/filelist.inc.php */
