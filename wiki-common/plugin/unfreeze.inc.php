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
use PukiWiki\Auth\Auth;
use PukiWiki\Factory;
use PukiWiki\Utility;
use PukiWiki\Router;

// Show edit form when unfreezed
defined('PLUGIN_UNFREEZE_EDIT') or define('PLUGIN_UNFREEZE_EDIT', TRUE);

function plugin_unfreeze_init(){
	global $_string;
	$msg = array(
		'_unfreeze_msg' => array(
			'title_isunfreezed'=> T_(' $1 is not frozen'),
			'title_unfreezed'  => T_(' $1 has been unfrozen.'),
			'title_freeze'     => T_('Unfreeze  $1'),
			'title_disabled'   => T_('Unfreeze function is disabled.'),
			'title_unfreeze'   => $_string['invalidpass'],
			'msg_unfreezing'   => T_('Please input the password for unfreezing.'),
			'btn_unfreeze'     => T_('Unfreeze')
		)
	);
	set_plugin_messages($msg);
}

function plugin_unfreeze_action()
{
	global $vars, $function_freeze, $_unfreeze_msg;

	$page = isset($vars['page']) ? $vars['page'] : '';
	$wiki = Factory::Wiki($page);
	if (! $function_freeze || ! $wiki->isEditable() || ! $wiki->isValied($page))
		return array('msg' => $_unfreeze_msg['title_disabled'], 'body' => '<p class="alert alert-danger">You have no permission to unfreeze this page.</p>');

	$pass = isset($vars['pass']) ? $vars['pass'] : NULL;
	
	$msg = '';
	$body = array();
	if (! $wiki->isFreezed()) {
		// Unfreezed already
		$msg  = str_replace('$1', Utility::htmlsc(Utility::stripBracket($page)), $_unfreeze_msg['title_isunfreezed']);
		$body[] = '<p class="alert alert-info">' . $msg . '</p>';
	} else if ( ! Auth::check_role('role_contents_admin') || $pass !== NULL && Auth::login($pass) ) {
		
		// BugTrack2/255
		$wiki->checkReadable();
		// Unfreeze
		$postdata = $wiki->get();
		array_shift($postdata);
		$wiki->set($postdata);

		// Update
		if (PLUGIN_UNFREEZE_EDIT) {
			// BugTrack2/255
			$wiki->checkEditable(true);
//			$vars['cmd'] = 'read'; // To show 'Freeze' link
			$vars['cmd'] = 'edit';
		}else{
			$vars['cmd'] = 'read';
		}
		$msg  = str_replace('$1', Utility::htmlsc(Utility::stripBracket($page)), $_unfreeze_msg['title_unfreezed']);
		$body[] = (!IS_AJAX) ? '' : '<p class="alert alert-success">' . $msg . '</p>';
		$body[] = '<div class="pull-right"><a href="'.$wiki->uri().'" class="btn btn-primary">OK</a></div>';
		Utility::redirect($wiki->uri());
		exit;
	} else {
		// Show unfreeze form
		$msg    = $_unfreeze_msg['title_unfreeze'];
		$body[] = ($pass === NULL) ? '' : '<p class="alert alert-danger">' . $_unfreeze_msg['msg_invalidpass'] .'</p>'."\n";
		$body[] = '<fieldset>';
		$body[] = '<legend>' . $_unfreeze_msg['msg_unfreezing'] . '</legend>';
		$body[] = '<form action="' . Router::get_script_uri() . '" method="post" class="form-inline plugin-freeze-form">';
		$body[] = '<input type="hidden"   name="cmd"  value="unfreeze" />';
		$body[] = '<input type="hidden"   name="page" value="'. Utility::htmlsc($page) . '" />';
		$body[] = '<input type="password" name="pass" size="12" class="form-control" />';
		$body[] = '<button type="submit" class="btn btn-primary" name="ok"><span class="fa fa-lock"></span>' . $_unfreeze_msg['btn_unfreeze'] . '</button>';
		$body[] = '</form>';
		$body[] = '</fieldset>';
	}

	return array('msg'=>$msg, 'body'=>join("\n",$body) );
}
/* End of file unfreeze.inc.php */
/* Location: ./wiki-common/plugin/unfreeze.inc.php */
