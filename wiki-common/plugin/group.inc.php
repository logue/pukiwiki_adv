<?php
/**
 * PukiWiki Plus! Group確認プラグイン
 *
 * @copyright   Copyright &copy; 2008, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @version     $Id: group.inc.php,v 0.1 2008/08/02 05:20:00 upk Exp $
 * @license     http://opensource.org/licenses/gpl-license.php GNU Public License(GPL2)
 */
use PukiWiki\Lib\Auth\Auth;
use PukiWiki\Lib\Renderer\RendererFactory;

function plugin_group_init()
{
	$msg = array(
		'_group_msg' => array(
			'group' => T_('Group'),
		)
	);
	set_plugin_messages($msg);
}

function plugin_group_convert()
{
	global $_group_msg;

	$auth_key = Auth::get_user_info();
	if (empty($auth_key['group'])) return '';

	$argv = func_get_args();
	$i = count($argv);
	if ($i < 2) {
		return <<<EOD
<div>
	<label>{$_group_msg['group']}</label>:
	{$auth_key['group']}
</div>

EOD;
	}

	$msg = $argv[$i-1];
	array_pop($argv);
	if (in_array($auth_key['group'], $argv)) return RendererFactory::factory( str_replace("\r", "\n", $msg) );
	return '';
}

/* End of file group.inc.php */
/* Location: ./wiki-common/plugin/group.inc.php */