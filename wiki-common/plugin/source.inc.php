<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: source.inc.php,v 1.14.5 2010/12/26 19:12:00 Logue Exp $
//
// Source plugin
use PukiWiki\Lib\Auth\Auth;
use PukiWiki\Lib\Factory;
use PukiWiki\Lib\Utility;
// Output source text of the page
function plugin_source_action()
{
	global $vars; //, $_source_messages;

	// if (PKWK_SAFE_MODE) die_message('PKWK_SAFE_MODE prohibits this');
	if (Auth::check_role('safemode')) Utility::dieMessage('PKWK_SAFE_MODE prohibits this');

	$page = isset($vars['page']) ? $vars['page'] : '';
	$vars['refer'] = $page;
	$wiki = Factory::Wiki($page);

	if (! $wiki->isValied() || ! $wiki->isReadable())
		return array(
			'msg'	=> T_(' $1 was not found.'),
			'body'	=> T_('cannot display the page source.')
		);

	$source = $wiki->get(true);
	Auth::is_role_page($source);

	return array(
		'msg'	=> T_('Source of  $1'),
		'body'	=> '<pre class="sh">' . Utility::htmlsc($source) . '</pre>'
	);
}
/* End of file source.inc.php */
/* Location: ./wiki-common/plugin/source.inc.php */