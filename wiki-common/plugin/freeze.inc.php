<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: freeze.inc.php,v 1.12.4 2014/08/18 19:10:00 Logue Exp $
// Copyright (C)
//   2011      PukiWiki Advance Developers Team
//   2005-2007 PukiWiki Plus! Team
//   2003-2004, 2007,2011,2014 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Freeze(Lock) plugin
use PukiWiki\Auth\Auth;
use PukiWiki\Factory;
use PukiWiki\Utility;
use PukiWiki\Router;

function plugin_freeze_init(){
	$msg = array(
		'_freeze_msg' => array(
			'title_isfreezed'  => T_(' $1 has already been frozen'),
			'title_freezed'    => T_(' $1 has been frozen.'),
			'title_freeze'     => T_('Freeze  $1'),
			'title_disabled'   => T_('Freeze function is disabled.'),
			'msg_invalidpass'  => T_('Invalid password.'),
			'msg_freezing'     => T_('Please input the password for freezing.'),
			'btn_freeze'       => T_('Freeze')
		)
	);
	set_plugin_messages($msg);
}


// Reserve 'Do nothing'. '^#freeze' is for internal use only.
function plugin_freeze_convert() { return ''; }

function plugin_freeze_action()
{
	global $vars, $function_freeze, $_freeze_msg;

	$page = isset($vars['page']) ? $vars['page'] : null;
	
	if (is_null($page)){
		return array('msg' => 'Not Found', 'body' => 'Page not found');
	}
	
	$wiki = Factory::Wiki($page);

	if (! $function_freeze || ! $wiki->isEditable(true) || ! $wiki->has())
		return array('msg' => $_freeze_msg['title_disabled'], 'body' => '<p class="alert alert-danger">You have no permission to freeze this page.</p>');

	$pass = isset($vars['pass']) ? $vars['pass'] : NULL;
	$msg = '';
	$body = array();
	if ($wiki->isFreezed()) {
		// Freezed already
		$msg  = str_replace('$1', Utility::htmlsc(Utility::stripBracket($page)), $_freeze_msg['title_isfreezed']);
		$body[] = '<p class="alert alert-info">' . $msg . '</p>';

	} else if ( ! Auth::check_role('role_contents_admin') || $pass !== NULL && Auth::login($pass) ) {
		// Freeze
		$postdata = $wiki->get();
		array_unshift($postdata, "#freeze");	//凍結をページに付加
		$wiki->set($postdata, true);

		// Update
		//$wiki->is_freezed();
		$vars['cmd'] = 'read';
		$msg  = str_replace('$1', Utility::htmlsc(Utility::stripBracket($page)), $_freeze_msg['title_freezed']);
		$body[] = (!IS_AJAX) ? '' : '<p class="alert alert-success">' . $msg . '</p><div class="pull-right"><a href="'.$wiki->uri().'" class="btn btn-primary">OK</a></div>';
	} else {
		// Show a freeze form
		$msg = $_freeze_msg['title_freeze'];
		$body[]   = ($pass === NULL) ? '' : '<p class="alert alert-warning">' . $_freeze_msg['msg_invalidpass'] . '</p>';
		$body[] = '<fieldset>';
		$body[] = '<legend>' . $_freeze_msg['msg_freezing'] . '</legend>';
		$body[] = '<form action="' . Router::get_script_uri() . '" method="post" class="form-inline plugin-freeze-form">';
		$body[] = '<input type="hidden"   name="cmd"  value="freeze" />';
		$body[] = '<input type="hidden"   name="page" value="'. Utility::htmlsc($page) . '" />';
		$body[] = '<input type="password" name="pass" size="12" class="form-control" />';
		$body[] = '<button type="submit" class="btn btn-primary" name="ok"><span class="fa fa-lock"></span>' . $_freeze_msg['btn_freeze'] . '</button>';
		$body[] = '</form>';
		$body[] = '</fieldset>';
	}

	return array('msg'=>$msg, 'body'=>join("\n", $body) );
}
/* End of file freeze.inc.php */
/* Location: ./wiki-common/plugin/freeze.inc.php */
