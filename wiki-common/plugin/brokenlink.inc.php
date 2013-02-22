<?php
/**
 * PukiWiki Advance brokenlink Plugin
 *
 * @copyright   Copyright &copy; 2006,2009, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @version     $Id: brokenlink.inc.php,v 0.2 2010/12/26 13:33:00 Logue Exp $
 * @license     http://opensource.org/licenses/gpl-license.php GNU Public License (GPL2)
 *
 */

use PukiWiki\Auth\Auth;
use PukiWiki\Lib\Renderer\RendererFactory;

function plugin_brokenlink_init()
{
	$messages = array(
		'_brokenlink_msg' => array(
			'msg_title'				=> T_('Broken Link List'),
			'msg_all_ok'			=> T_('All links are effective.'),
			'msg_param_error'		=> T_('The parameter is illegal.'),
			'msg_not_access'		=> T_('Not authorized to access.'),
			'msg_not_found_xbel'	=> T_('The xbel plugin is not found.'),
		)
	);
	set_plugin_messages($messages);
}

function plugin_brokenlink_action()
{
	global $vars, $_brokenlink_msg;

	$retval = array('msg'=>$_brokenlink_msg['msg_title'], 'body'=>'');

	if (empty($vars['page'])) {
		$retval['body'] = $_brokenlink_msg['msg_param_error'];
		return $retval;
	}

	// ユーザ認証されていない
	$id = Auth::check_auth();
	if (empty($id)) {
		$retval['body'] = $_brokenlink_msg['msg_not_access'];
		return $retval;
	}

	if (! exist_plugin('xbel')) {
		$retval['body'] = $_brokenlink_msg['msg_not_found_xbel'];
		return $retval;
	}

	$links = xbel::get_link_list($vars['page']);

	$data = '';
	foreach($links as $href=>$aname) {
		$rc = http_request($href, 'HEAD');
		switch ($rc['rc']) {
		case 200: // Ok
		case 301: // Moved Permanently
		case 401: // Unauthorized
			continue;
		default:
			$data .= '-[['.$aname.'>'.$href.']] ('.$rc['rc'].")\n";
		}
	}

	if ($data == '') {
		$data = $_brokenlink_msg['msg_all_ok'];
	}

	$retval['body'] = RendererFactorty::factory($data);
	return $retval;
}

/* End of file brokenlink.inc.php */
/* Location: ./wiki-common/plugin/brokenlink.inc.php */