<?php
/**
 * PukiWiki Plus! role確認プラグイン
 *
 * @copyright	Copyright &copy; 2006-2008, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @version	$Id: role.inc.php,v 0.3 2010/12/26 19:05:00 Logue Exp $
 * @license	http://opensource.org/licenses/gpl-license.php GNU Public License
 */
use PukiWiki\Lib\Auth\Auth;
use PukiWiki\Lib\Renderer\RendererFactory;
/*
 * 初期処理
 */
function plugin_role_init()
{
	$msg = array(
		'_role_msg' => array(
			'role'		=> T_('Role'),
			'role_0'	=> T_('Guest'),
			'role_2'	=> T_('Webmaster'),
			'role_3'	=> T_('Contents manager'),
			'role_4'	=> T_('Enrollee'),
			'role_5'	=> T_('Authorized'),
		)
	);
	set_plugin_messages($msg);
}

function plugin_role_convert()
{
	global $_role_msg;

	$role = Auth::get_role_level();
	if ($role == 0) return '';

	$argv = func_get_args();
	$i = count($argv);
	if ($i < 2) {
		return role_list($role);
	}

	$msg = $argv[$i-1];
	if (! Auth::is_check_role($argv[0])) return RendererFactory::factory( str_replace("\r", "\n", $msg) );
	return '';
}

function role_list($role)
{
	global $_role_msg;
	$role_name = array(
		$_role_msg['role_0'],
		'',
		$_role_msg['role_2'],
		$_role_msg['role_3'],
		$_role_msg['role_4'],
		$_role_msg['role_5'],
	);

	return <<<EOD
<div>
	<label>{$_role_msg['role']}</label>:
	{$role_name[(int)$role]}($role)
</div>

EOD;

}

/* End of file role.inc.php */
/* Location: ./wiki-common/plugin/role.inc.php */
