<?php
// $Id: newpage.inc.php,v 1.16.8 2012/03/31 17:52:00 Logue Exp $
// Copyright (C)
//  2010-2012 PukiWiki Advance Developers Team
//  2007-2008 PukiWiki Plus! Team
//
// Newpage plugin
use PukiWiki\Auth\Auth;
use PukiWiki\Utility;
use PukiWiki\Renderer\RendererDefines;

// Message setting
function plugin_newpage_init()
{
	$messages = array(
		'_newpage_messages'=>array(
			'title'  => T_('New page'),
			'form_pagename' => T_('Page name'),
			'btn_new'   => T_('New')
		)
	);
	set_plugin_messages($messages);
}

function plugin_newpage_convert()
{
	global $vars, $_newpage_messages;
	static $id = 0;

	// if (PKWK_READONLY) return ''; // Show nothing
	if (Auth::check_role('readonly')) return ''; // Show nothing
        if (Auth::is_check_role(PKWK_CREATE_PAGE)) return '';

	$newpage = '';
	if (func_num_args()) list($newpage) = func_get_args();
	if (! preg_match('/^' . RendererDefines::BRACKETNAME_PATTERN . '$/', $newpage)) $newpage = '';

	$s_page    = Utility::htmlsc(isset($vars['refer']) ? $vars['refer'] : '');
	$s_newpage = Utility::htmlsc($newpage);
	++$id;
	$script = get_script_uri();

	$ret = <<<EOD
<form action="$script" method="post" class="form-inline plugin-newpage-form">
	<input type="hidden" name="cmd" value="edit" />
	<input type="hidden" name="refer"  value="$s_page" />
	<div class="form-group">
		<input type="text" class="form-control" name="page" id="p_newpage_$id" value="$s_newpage" size="30" placeholder="{$_newpage_messages['form_pagename']}" />
	</div>
	<input type="submit" value="{$_newpage_messages['btn_new']}" class="btn btn-primary" />
</form>
EOD;

	return $ret;
}

function plugin_newpage_action()
{
	global $vars, $_string, $_newpage_messages;

	// if (PKWK_READONLY) die_message('PKWK_READONLY prohibits editing');
	if (Auth::check_role('readonly')) Utility::dieMessage( sprintf($_string['error_prohibit'], 'PKWK_READONLY'),'',403 );
	if (Auth::is_check_role(PKWK_CREATE_PAGE)) Utility::dieMessage( sprintf($_string['error_prohibit'], 'PKWK_CREATE_PAGE'),'',403 );

	if (!isset($vars['page'])) {
		$retvars['msg']  = $_newpage_messages['title'];
		$retvars['body'] = plugin_newpage_convert();
		return $retvars;
	} else {
		$page    = Utility::stripNullBytes($vars['page']);
		if (isset($vars['refer'])) {
			$r_page = Utility::getPageName($page, $vars['refer']);
			$r_refer = 'refer=' .$vars['refer'];
		} else {
			$r_page = $page;
			$r_refer = '';
		}

		Utility::redirect(get_page_location_uri($r_page,$r_refer));
		exit;
	}
}
/* End of file newpage.inc.php */
/* Location: ./wiki-common/plugin/newpage.inc.php */
