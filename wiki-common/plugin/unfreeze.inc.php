<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: unfreeze.inc.php,v 1.14.7 2011/02/05 10:19:00 Logue Exp $
// Copyright (C)
//   2010-2011 PukiWiki Advance Developers Team
//   2004-2007 PukiWiki Plus! Team
//   2003-2004, 2007 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Unfreeze(Unlock) plugin

// Show edit form when unfreezed
defined('PLUGIN_UNFREEZE_EDIT') or define('PLUGIN_UNFREEZE_EDIT', TRUE);

function plugin_unfreeze_action()
{
	global $script, $vars, $function_freeze;

	$_title_isunfreezed = T_(' $1 is not frozen');
	$_title_unfreezed   = T_(' $1 has been unfrozen.');
	$_title_unfreeze    = T_('Unfreeze  $1');
	$_msg_invalidpass   = T_('Invalid password.');
	$_msg_unfreezing    = T_('Please input the password for unfreezing.');
	$_btn_unfreeze      = T_('Unfreeze');

	$page = isset($vars['page']) ? $vars['page'] : '';
	if (! $function_freeze || is_cantedit($page) || ! is_page($page))
		return array('msg' => '', 'body' => '');

	$pass = isset($vars['pass']) ? $vars['pass'] : NULL;
	$msg = $body = '';
	if (! is_freeze($page)) {
		// Unfreezed already
		$msg  = $_title_isunfreezed;
		$body = str_replace('$1', htmlsc(strip_bracket($page)),
			$_title_isunfreezed);

	} else
	if ( (! auth::check_role('role_adm_contents') ) ||
	     ($pass !== NULL && pkwk_login($pass)) )
	{
		// BugTrack2/255
		check_readable($page, true, true);
		// Unfreeze
		$postdata = get_source($page);
		array_shift($postdata);
		$postdata = join('', $postdata);
		file_write(DATA_DIR, $page, $postdata, TRUE);

		// Update 
		is_freeze($page, TRUE);
		if (PLUGIN_UNFREEZE_EDIT) {
			// BugTrack2/255
			check_editable($page, true, true);
//			$vars['cmd'] = 'read'; // To show 'Freeze' link
			$vars['cmd'] = 'edit';
			$msg  = $_title_unfreezed;
			$body = edit_form($page, $postdata);
		} else {
			$vars['cmd'] = 'read';
			$msg  = $_title_unfreezed;
			$body = '';
		}

	} else {
		// Show unfreeze form
		$msg    = $_title_unfreeze;
		$s_page = htmlsc($page);
		$body   = ($pass === NULL) ? '' : "<p><strong>$_msg_invalidpass</strong></p>\n";
		$body  .= <<<EOD
<fieldset>
	<legend>$_msg_unfreezing</legend>
	<form action="$script" method="post">
		<input type="hidden"   name="cmd"  value="unfreeze" />
		<input type="hidden"   name="page" value="$s_page" />
		<div class="freeze_form">
			<input type="password" name="pass" size="12" />
			<input type="submit"   name="ok"   value="$_btn_unfreeze" />
		</div>
	</form>
</fieldset>
EOD;
	}

	return array('msg'=>$msg, 'body'=>$body);
}
?>
