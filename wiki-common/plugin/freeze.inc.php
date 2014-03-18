<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: freeze.inc.php,v 1.12.3 2011/02/05 10:53:00 Logue Exp $
// Copyright (C)
//   2011      PukiWiki Advance Developers Team
//   2005-2007 PukiWiki Plus! Team
//   2003-2004, 2007,2011 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Freeze(Lock) plugin
use PukiWiki\Auth\Auth;
use PukiWiki\Factory;
use PukiWiki\Utility;
use PukiWiki\Router;

// Reserve 'Do nothing'. '^#freeze' is for internal use only.
function plugin_freeze_convert() { return ''; }

function plugin_freeze_action()
{
	global $vars, $function_freeze;

	$_title_isfreezed = T_(' $1 has already been frozen');
	$_title_freezed   = T_(' $1 has been frozen.');
	$_title_freeze    = T_('Freeze  $1');
	$_msg_invalidpass = T_('Invalid password.');
	$_msg_freezing    = T_('Please input the password for freezing.');
	$_btn_freeze      = T_('Freeze');

	$page = isset($vars['page']) ? $vars['page'] : '';
	$wiki = Factory::Wiki($page);

	if (! $function_freeze || ! $wiki->isEditable(true) || ! $wiki->has())
		return array('msg' => 'Freeze function is disabled.', 'body' => '<p class="alert alert-danger">You have no permission to freeze this page.</p>');

	$pass = isset($vars['pass']) ? $vars['pass'] : NULL;
	$msg = $body = '';
	if ($wiki->isFreezed()) {
		// Freezed already
		$msg  = str_replace('$1', Utility::htmlsc(Utility::stripBracket($page)), $_title_isfreezed);
		$body = '<p class="alert alert-info">' . $msg . '</p>';

	} else if ( ! Auth::check_role('role_contents_admin') || $pass !== NULL && Auth::login($pass) ) {
		// Freeze
		$postdata = $wiki->get();
		array_unshift($postdata, "#freeze\n");	//凍結をページに付加
		$wiki->set($postdata, true);

		// Update
		//$wiki->is_freezed();
		$vars['cmd'] = 'read';
		$msg  = str_replace('$1', Utility::htmlsc(Utility::stripBracket($page)), $_title_freezed);
		$body = (!IS_AJAX) ? '' : '<p class="alert alert-success">' . $msg . '</p><div class="pull-right"><a href="'.$wiki->uri().'" class="btn btn-primary">OK</a></div>';

	} else {
		// Show a freeze form
		$msg    = & $_title_freeze;
		$s_page = Utility::htmlsc($page);
		$body   = ($pass === NULL) ? '' : '<p class="alert alert-warning">$_msg_invalidpass</p>';
		$script = Router::get_script_uri();
		$body  .= <<<EOD
<fieldset>
	<legend>$_msg_freezing</legend>
	<form action="$script" method="post" class="form-inline plugin-freeze-form">
		<input type="hidden"   name="cmd"  value="freeze" />
		<input type="hidden"   name="page" value="$s_page" />
		<input type="password" name="pass" size="12" class="form-control" />
		<button type="submit" class="btn btn-primary" name="ok"><span class="fa fa-lock"></span>$_btn_unfreeze</button>
	</form>
</fieldset>
EOD;
	}

	return array('msg'=>$msg, 'body'=>$body);
}
/* End of file freeze.inc.php */
/* Location: ./wiki-common/plugin/freeze.inc.php */
