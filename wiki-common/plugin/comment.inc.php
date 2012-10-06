<?php
// PukiPlus - Yet another WikiWikiWeb clone
// $Id: comment.inc.php,v 1.41.26 2012/05/11 18:10:00 Logue Exp $
// Copyright (C)
//  2010-2012 PukiWiki Advance Developers Team
//  2005-2008 PukiWiki Plus! Team
//  2002-2007 PukiWiki Developers Team
//  2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// Comment plugin

// ----
defined('PLUGIN_COMMENT_DIRECTION_DEFAULT') or define('PLUGIN_COMMENT_DIRECTION_DEFAULT', '1'); // 1: above 0: below
defined('PLUGIN_COMMENT_SIZE_MSG') or define('PLUGIN_COMMENT_SIZE_MSG',  68);
defined('PLUGIN_COMMENT_SIZE_NAME') or define('PLUGIN_COMMENT_SIZE_NAME', 15);

defined('PLUGIN_COMMENT_USE_TEXTAREA') or define('PLUGIN_COMMENT_USE_TEXTAREA', true);

// ----
define('PLUGIN_COMMENT_FORMAT_MSG',		'$msg');
define('PLUGIN_COMMENT_FORMAT_NAME',	'[[$name]]');
// define('PLUGIN_COMMENT_FORMAT_NOW',	'&new{$now};');
define('PLUGIN_COMMENT_FORMAT_NOW',		'&epoch('.UTIME.',comment_date);');
define('PLUGIN_COMMENT_FORMAT_STRING',	"\x08MSG\x08 -- \x08NAME\x08 \x08NOW\x08");

function plugin_comment_init(){
	global $_string;
	$messages = array(
		'_comment_messages' => array(
			'msg_collided'		=> $_string['comment_collided'],
			'title_collided'	=> $_string['title_collided'],
			'title_updated'		=> $_string['updated'],
			'err_prohibit'		=> $_string['error_prohibit'],
			'label_name'		=> T_('Name: '),
			'label_post'		=> T_('Post Comment'),
			'label_comment'		=> T_('Comment: ')
		),
		'_comment_formats' => array(
			'msg'	=> PLUGIN_COMMENT_FORMAT_MSG,
			'name'	=> PLUGIN_COMMENT_FORMAT_NAME,
			'now'	=> PLUGIN_COMMENT_FORMAT_NOW,
			'str'	=> PLUGIN_COMMENT_FORMAT_STRING
		)
	);
	set_plugin_messages($messages);
}

function plugin_comment_action()
{
	global $vars, $post, $_comment_messages;

	// Petit SPAM Check (Client(Browser)-Server Ticket Check)
	$spam = FALSE;
	if (isset($post['encode_hint']) && $post['encode_hint'] != '') {
		if (PKWK_ENCODING_HINT !== $post['encode_hint']) $spam = TRUE;
	} else {
		if (PKWK_ENCODING_HINT !== '') $spam = TRUE;
	}

	// if (PKWK_READONLY) die_message('PKWK_READONLY prohibits editing');
	if (auth::check_role('readonly')) die_message(sprintf($_comment_messages['err_prohibit'],'PKWK_READONLY'));

	if (!is_page($vars['refer']) && auth::is_check_role(PKWK_CREATE_PAGE)) {
		die_message(sprintf($_comment_messages['err_prohibit'],'PKWK_CREATE_PAGE'));
	}

	// If SPAM, goto jail.
	if ($spam) return plugin_comment_honeypot();
	return plugin_comment_write();
}

function plugin_comment_write()
{
	global $vars, $now;
	global $_no_name, $_comment_messages, $_comment_formats;

	if (! isset($vars['msg'])) return array('msg'=>'', 'body'=>''); // Do nothing

	// Validate
	if (is_spampost(array('msg')))
		return plugin_comment_honeypot();

	$vars['msg'] = str_replace("\n", '', $vars['msg']); // Cut LFs
	$head = '';
	$match = array();
	if (preg_match('/^(-{1,2})-*\s*(.*)/', $vars['msg'], $match)) {
		$head        = & $match[1];
		$vars['msg'] = & $match[2];
	}
	if ($vars['msg'] == '') return array('msg'=>'', 'body'=>''); // Do nothing

	$comment  = str_replace('$msg', $vars['msg'], $_comment_formats['msg']);

	list($nick, $vars['name'], $disabled) = plugin_comment_get_nick();

	if(isset($vars['name']) || ($vars['nodate'] !== '1')) {
		$_name = (! isset($vars['name']) || $vars['name'] == '') ? $_no_name : $vars['name'];
		$_name = ($_name == '') ? '' : str_replace('$name', $_name, $_comment_formats['name']);
		$_now  = ($vars['nodate'] == '1') ? '' :
			str_replace('$now', $now, PLUGIN_COMMENT_FORMAT_NOW);
		$comment = str_replace("\x08MSG\x08",  $comment, $_comment_formats['str']);
		$comment = str_replace("\x08NAME\x08", $_name, $comment);
		$comment = str_replace("\x08NOW\x08",  $_now,  $comment);
	}
	$comment = '-' . $head . ' ' . $comment;

	$postdata    = '';
	$comment_no  = 0;
	$above       = (isset($vars['above']) && $vars['above'] == '1');
	foreach (get_source($vars['refer']) as $line) {
		if (! $above) $postdata .= $line;
		if (preg_match('/^#comment/i', $line) && $comment_no++ == $vars['comment_no']) {
			if ($above) {
				$postdata = rtrim($postdata) . "\n" .
					$comment . "\n" .
					"\n";  // Insert one blank line above #commment, to avoid indentation
			} else {
				$postdata = rtrim($postdata) . "\n" .
					$comment . "\n"; // Insert one blank line below #commment
			}
		}
		if ($above) $postdata .= $line;
	}

	$title = $_comment_messages['title_updated'];
	$body = '';
	if (md5(@join('', get_source($vars['refer']))) !== $vars['digest']) {
		$title = $_comment_messages['title_collided'];
		$body  = $_comment_messages['msg_collided'] . make_pagelink($vars['refer']);
	}

	page_write($vars['refer'], $postdata);

	if ($vars['refpage']) {
		header('Location: ' . get_page_location_uri($vars['refpage']));
		exit;
	}

	$vars['page'] = $vars['refer'];

	return array('msg'=>$title, 'body'=>$body);
}

function plugin_comment_get_nick()
{
	global $vars, $_no_name;

	$name = (empty($vars['name'])) ? $_no_name : $vars['name'];
	if (PKWK_READONLY != ROLE_AUTH) return array($name,$name,'');

	$auth_key = auth::get_user_name();
	if (empty($auth_key['nick'])) return array($name,$name,'');
	if (auth::get_role_level() < ROLE_AUTH) return array($auth_key['nick'],$name,'');
	$link = (empty($auth_key['profile'])) ? $auth_key['nick'] : $auth_key['nick'].'>'.$auth_key['profile'];
	return array($auth_key['nick'], $link, "disabled=\"disabled\"");
}

// Cancel (Back to the page / Escape edit page)
function plugin_comment_honeypot()
{
	// Logging for SPAM Report
	honeypot_write();

	// Same as "Cancel" action
	return array('msg'=>'', 'body'=>''); // Do nothing
}

function plugin_comment_convert()
{
	global $vars, $digest, $_comment_messages;	//, $_btn_comment, $_btn_name, $_msg_comment;
	static $numbers = array();
	static $all_numbers = 0;
	static $comment_cols = PLUGIN_COMMENT_SIZE_MSG;

	$ret = array();
	if (PKWK_READONLY === ROLE_AUTH) {
		exist_plugin('login');
		$ret[] = do_plugin_inline('login');
		$ret[] = '<br />';
	}

	if (auth::check_role('readonly')) return $auth_guide;
	if (! isset($numbers[$vars['page']])) $numbers[$vars['page']] = 0;
	
	$options = func_num_args() ? func_get_args() : array();
	list($user, $link, $disabled) = plugin_comment_get_nick();

	$ticket = md5(MUTIME);
	if (function_exists('pkwk_session_start') && pkwk_session_start() !== 0) {
		$_SESSION[$ticket] = md5(get_ticket() . $digest);
	}

	$ret[] = '<form action="'. get_script_uri() .'" method="post" class="comment_form">';
	$ret[] = '<input type="hidden" name="cmd" value="comment" />';
	$ret[] = '<input type="hidden" name="digest" value="' . $digest . '" />';
	$ret[] = '<input type="hidden" name="ticket" value="' . $ticket . '" />';
	$ret[] = '<input type="hidden" name="refer"  value="' . htmlsc($vars['page']) . '" />';
	$ret[] = '<input type="hidden" name="refpage" value="" />';
	$ret[] = '<input type="hidden" name="comment_no" value="' . $numbers[$vars['page']]++ . '" />';
	$ret[] = '<input type="hidden" name="nodate" value="' . in_array('nodate', $options) ? '1' : '0' . '" />';
	$ret[] = '<input type="hidden" name="above"  value="' . in_array('above',  $options) ? '1' : (in_array('below', $options) ? '0' : PLUGIN_COMMENT_DIRECTION_DEFAULT) . '" />';

	$comment_all_no = $all_numbers++;
	if (! in_array('noname', $options)) {
		$ret[] =
			'<input type="text" name="name" id="p_comment_name_' .
			$comment_all_no .  '" size="' . PLUGIN_COMMENT_SIZE_NAME .
			'" value="'.$user.'"'.$disabled.' placeholder="'.$_comment_messages['label_name'].'" />';
	}
	$ret[] = (PLUGIN_COMMENT_USE_TEXTAREA) ? 
		'<textarea name="msg" id="p_comment_comment_'.$comment_all_no.'" cols="'.$comment_cols.'" row="1" placeholder="'.$_comment_messages['label_comment'].'"></textarea>' :
		'<input type="text" name="msg" id="p_comment_comment_'.$comment_all_no.'" size="'.$comment_cols.'" placeholder="'.$_comment_messages['label_comment'].'" />';

	$ret[] = '<input type="submit" name="comment" value="' . $_comment_messages['label_post'] . '" />';
	$ret[] = '</form>';

	$string = join("\n",$ret);
	return (IS_MOBILE) ? '<div data-role="collapsible" data-collapsed="true" data-theme="b" data-content-theme="d"><h4>'.$_comment_messages['label_comment'].'</h4>'.$string.'</div>' : $string;
}
/* End of file comment.inc.php */
/* Location: ./wiki-common/plugin/comment.inc.php */
