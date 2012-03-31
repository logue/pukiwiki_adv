<?php
// $Id: newpage.inc.php,v 1.16.8 2012/03/31 17:52:00 Logue Exp $
// Copyright (C)
//  2010-2012 PukiWiki Advance Developers Team
//  2007-2008 PukiWiki Plus! Team
//
// Newpage plugin

function plugin_newpage_convert()
{
	global $script, $vars, $BracketName;
	static $id = 0;
	$_btn_edit = T_('Edit');
	$_msg_newpage = T_('New page');

	// if (PKWK_READONLY) return ''; // Show nothing
	if (auth::check_role('readonly')) return ''; // Show nothing
        if (auth::is_check_role(PKWK_CREATE_PAGE)) return '';

	$newpage = '';
	if (func_num_args()) list($newpage) = func_get_args();
	if (! preg_match('/^' . $BracketName . '$/', $newpage)) $newpage = '';

	$s_page    = htmlsc(isset($vars['refer']) ? $vars['refer'] : $vars['page']);
	$s_newpage = htmlsc($newpage);
	++$id;

	$ret = <<<EOD
<form action="$script" method="post">
	<input type="hidden" name="plugin" value="edit" />
	<input type="hidden" name="refer"  value="$s_page" />
	<div class="newpage_form">
		<label for="p_newpage_$id">$_msg_newpage:</label>
		<input type="text"   name="page" id="p_newpage_$id" value="$s_newpage" size="30" />
		<input type="submit" value="$_btn_edit" />
	</div>
</form>
EOD;

	return $ret;
}

function plugin_newpage_action()
{
	global $vars;
	$_btn_edit = T_('Edit');
	$_msg_newpage = T_('New page');

	// if (PKWK_READONLY) die_message('PKWK_READONLY prohibits editing');
	if (auth::check_role('readonly')) die_message( T_('PKWK_READONLY prohibits editing') );
	if (auth::is_check_role(PKWK_CREATE_PAGE)) die_message( T_('PKWK_CREATE_PAGE prohibits editing') );

	if ($vars['page'] == '') {
		$retvars['msg']  = $_msg_newpage;
		$retvars['body'] = plugin_newpage_convert();
		return $retvars;
	} else {
		$page    = strip_bracket($vars['page']);
		if (isset($vars['refer'])) {
			$r_page = get_fullname($page, $vars['refer']);
			$r_refer = 'refer=' .$vars['refer'];
		} else {
			$r_page = $page;
			$r_refer = '';
		}

		pkwk_headers_sent();
		header('Location: ' . get_page_location_uri($r_page,$r_refer));
		exit;
	}
}
?>
