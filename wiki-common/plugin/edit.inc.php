<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: edit.inc.php,v 1.49.45 2011/02/05 10:49:00 Logue Exp $
// Copyright (C)
//   2010-2011 PukiWiki Advance Developers Team
//   2005-2009 PukiWiki Plus! Team
//   2001-2007,2011 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Edit plugin (cmd=edit)
// Plus! NOTE:(policy)not merge official cvs(1.40->1.41) See Question/181

// Remove #freeze written by hand
define('PLUGIN_EDIT_FREEZE_REGEX', '/^(?:#freeze(?!\w)\s*)+/im');

// Define part-edit area - 'compat':1.4.4compat, 'level':level
defined('PLUGIN_EDIT_PARTAREA') or define('PLUGIN_EDIT_PARTAREA', 'compat');

function plugin_edit_action()
{
// global $vars, $_title_edit, $load_template_func;
	global $vars, $load_template_func, $_string;

	$page = isset($vars['page']) ? $vars['page'] : null;
	$wiki = new PukiWiki\Lib\File\WikiFile($page);

	// if (PKWK_READONLY) die_message(  sprintf($_string['error_prohibit'], 'PKWK_READONLY') );
	if (auth::check_role('readonly')) die_message( $_string['prohibit'] );

	if (PKWK_READONLY == ROLE_AUTH && auth::get_role_level() > ROLE_AUTH) {
		die_message( sprintf($_string['error_prohibit'], 'PKWK_READONLY') );
	}

	if (isset($vars['realview'])) {
		return plugin_edit_realview();
	}

	if (!$wiki->is_editable()){
		die_message( $_string['not_editable'] );
	}

	//check_editable($page, true, true);

	if (!$wiki->has() && auth::is_check_role(PKWK_CREATE_PAGE)) {
		die_message( sprintf($_string['error_prohibit'], 'PKWK_CREATE_PAGE') );
	}

	if (preg_match(PKWK_ILLEGAL_CHARS_PATTERN, $page)){
		die_message($_string['illegal_chars']);
	}

	if (isset($vars['preview']) || ($load_template_func && isset($vars['template']))) {
		return plugin_edit_preview();
	} else if (isset($vars['write'])) {
		return plugin_edit_write();
	} else if (isset($vars['cancel'])) {
		return plugin_edit_cancel();
	}

	$source = $wiki->source();
	auth::is_role_page($source);

	$postdata = $vars['original'] = join('', $source);
	if (!empty($vars['id']))
	{
		$postdata = plugin_edit_parts($vars['id'],$source);
		if ($postdata === FALSE)
		{
			unset($vars['id']); // なかったことに :)
			$postdata = $vars['original'];
		}
	}

	if ($postdata == ''){
		// Check Page name length
		// http://pukiwiki.sourceforge.jp/dev/?PukiWiki%2F1.4%2F%A4%C1%A4%E7%A4%C3%A4%C8%CA%D8%CD%F8%A4%CB%2F%C4%B9%A4%B9%A4%AE%A4%EB%A5%DA%A1%BC%A5%B8%CC%BE%A4%CE%A5%DA%A1%BC%A5%B8%A4%CE%BF%B7%B5%AC%BA%EE%C0%AE%A4%F2%CD%DE%BB%DF
		$filename_max_length = 250;
		$filename = encode($page) . '.txt';
		$filename_length = strlen($filename);
		if ($filename_length > $filename_max_length){
			$msg = "<b>Error: Filename too long.</b><br />\n" .
				"Page name: " . htmlsc($page) . "<br />\n" .
				"Filename: $filename<br>\n" .
				"Filename length: $filename_length<br />\n" .
				"Filename limit: $filename_max_length<br />\n";
			// Filename too long
			return array('msg'=>$_title_edit, 'body'=>$msg);
		}else{
			$postdata = auto_template($page);
		}
	}

	return array('msg'=> T_('Edit of $1'), 'body'=>edit_form($page, $postdata));
}

// Preview by Ajax
function plugin_edit_realview()
{
	global $vars;

	$vars['msg'] = preg_replace(PLUGIN_EDIT_FREEZE_REGEX, '' ,$vars['msg']);
	$postdata = $vars['msg'];

	if ($postdata) {
		$postdata = make_str_rules($postdata);
		$postdata = explode("\n", $postdata);
		$postdata = drop_submit(convert_html($postdata));
	}
	// Feeding start
	pkwk_common_headers();
	$longtaketime = getmicrotime() - MUTIME;
	$taketime     = sprintf('%01.03f', $longtaketime);
	if ($vars['type'] == 'json'){
		$obj = array(
			'data'			=> $postdata,
			'taketime'		=> $taketime
		);
		header("Content-Type: application/json; charset=".CONTENT_CHARSET);
		echo json_encode($obj);
	}else{
		header('Content-type: text/xml; charset=UTF-8');
		print '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		print $postdata;
		print '<span class="small1">(Time:' . $taketime . ')</span>';
	}
	exit;
}

// Preview
function plugin_edit_preview()
{
	global $post, $vars;
	// global $_title_preview, $_msg_preview, $_msg_preview_delete;
	$_title_preview			= T_('Preview of $1');
	$_msg_preview			= T_('To confirm the changes, click the button at the bottom of the page');
	$_msg_preview_delete	= T_('(The contents of the page are empty. Updating deletes this page.)');

	$page = isset($vars['page']) ? $vars['page'] : '';

	// Loading template
	if (isset($vars['template_page']) && is_page($vars['template_page'])) {

		$vars['msg'] = join('', get_source($vars['template_page']));

		// Cut fixed anchors
		$vars['msg'] = preg_replace('/^(\*{1,3}.*)\[#[A-Za-z][\w-]+\](.*)$/m', '$1$2', $vars['msg']);
	}

	$post['msg'] = preg_replace(PLUGIN_EDIT_FREEZE_REGEX, '', $post['msg']);
	$postdata = $post['msg'];

	// Compat: add plugin and adding contents
	if (isset($vars['add']) && $vars['add']) {
		if (isset($post['add_top']) && $post['add_top']) {
			$postdata  = $postdata . "\n\n" . get_source($page, TRUE, TRUE);
		} else {
			$postdata  = get_source($page, TRUE, TRUE) . "\n\n" . $postdata;
		}
	}

	$body = $_msg_preview . '<br />' . "\n";
	if ($postdata == '')
		$body .= '<strong>' . $_msg_preview_delete . '</strong>';
	$body .= '<br />' . "\n";

	if ($postdata) {
		$postdata = make_str_rules($postdata);
		$postdata = explode("\n", $postdata);
		$postdata = drop_submit(convert_html($postdata));
		$body .= '<div id="preview">' . $postdata . '</div>' . "\n";
	}
	$body .= edit_form($page, $post['msg'], $post['digest'], FALSE);

	return array('msg'=>$_title_preview, 'body'=>$body);
}

// Inline: Show edit (or unfreeze text) link
// NOTE: Plus! is not compatible for 1.4.4+ style(compatible for 1.4.3 style)
function plugin_edit_inline()
{
	static $usage = '&edit(pagename,anchor);';

	global $vars, $fixed_heading_edited;
	global $_symbol_paraedit, $_symbol_paraguiedit;

	if (!$fixed_heading_edited || is_freeze($vars['page']) || auth::check_role('readonly')) {
		return '';
	}

	// Arguments
	$args = func_get_args();

	// {label}. Strip anchor tags only
	$s_label = strip_htmltag(array_pop($args), FALSE);
	if (empty($s_label)) {
		$s_label = $_symbol_paraedit;
		$s_label_edit = & $_symbol_paraedit;
//		$s_label_guiedit = & $_symbol_paraguiedit;
	}else{
		$s_label_edit = $s_label;
//		$s_label_guiedit = '';
	}

	list($page,$id) = array_pad($args,2,'');
	if (!is_page($page)) {
		$page = $vars['page'];
	}

	$tag_edit = '<a class="anchor_super" id="edit_'.$id.'" href="' . get_cmd_uri('edit',$page,'',array('id'=>$id)) . '" rel="nofollow">' . $s_label_edit . '</a>';
//	$tag_guiedit = '<a class="anchor_super" id="guiedit_'.$id.'" href="' . get_cmd_uri('guiedit',$page,'',array('id'=>$id)) .'" rel="nofollow">' . $s_label_guiedit . '</a>';
/*
	switch ($fixed_heading_edited) {
	case 2:
		return $tag_guiedit;
	case 3:
		return $tag_edit.$tag_guiedit;
	default:
		return $tag_edit;
	}
*/
	return $tag_edit;
}

// Write, add, or insert new comment
function plugin_edit_write()
{
	global $vars, $trackback, $_string;
	global $notimeupdate, $do_update_diff_table;
	global $use_trans_sid_address;
//	global $_title_collided, $_msg_collided_auto, $_msg_collided, $_title_deleted;
//	global $_msg_invalidpass;

	$_title_deleted = T_(' $1 was deleted');
	$_msg_invalidpass = $_string['invalidpass'];

	$page   = isset($vars['page'])   ? $vars['page']   : null;
	$add    = isset($vars['add'])    ? $vars['add']    : null;
	$digest = isset($vars['digest']) ? $vars['digest'] : null;
	$partid = isset($vars['id'])     ? $vars['id']     : null;
	$notimestamp = isset($vars['notimestamp']) && $vars['notimestamp'] !== null;

	// SPAM Check (Client(Browser)-Server Ticket Check)
	if ( isset($vars['encode_hint']) && $vars['encode_hint'] !== PKWK_ENCODING_HINT )
		return plugin_edit_honeypot();
	if ( !isset($vars['encode_hint']) && !defined(PKWK_ENCODING_HINT) )
		return plugin_edit_honeypot();

	// Check Validate and Ticket
	if ($notimestamp && !is_page($page)) {
		return plugin_edit_honeypot();
	}

	// Validate
	if (is_spampost(array('msg')))
		return plugin_edit_honeypot();

	// Paragraph edit mode
	if ($partid) {
		$source = preg_split('/([^\n]*\n)/', $vars['original'], -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
		$vars['msg'] = (plugin_edit_parts($partid, $source, $vars['msg']) !== FALSE)
			? join('', $source)
			: rtrim($vars['original']) . "\n\n" . $vars['msg'];
	}

	// Delete "#freeze" command for form edit.
	$vars['msg'] = preg_replace('/^#freeze\s*$/im', '', $vars['msg']);
	$msg = & $vars['msg']; // Reference

	$retvars = array();

	// Collision Detection
	$oldpagesrc = get_source($page, TRUE, TRUE);
	$oldpagemd5 = md5($oldpagesrc);

	if ($digest !== $oldpagemd5) {
		$vars['digest'] = $oldpagemd5; // Reset
		$original = isset($vars['original']) ? $vars['original'] : null;
		list($postdata_input, $auto) = do_update_diff($oldpagesrc, $msg, $original);

		$_msg_collided_auto = $_string['msg_collided_auto'];
		$_msg_collided = $_string['msg_collided'];
		$retvars['msg'] = $_string['title_collided'];

		$retvars['body'] = ($auto ? $_msg_collided_auto : $_msg_collided)."\n";
		$retvars['body'] .= $do_update_diff_table;

		unset($vars['id']);	// Change edit all-text of pages(from para-edit)
		$retvars['body'] .= edit_form($page, $postdata_input, $oldpagemd5, FALSE);
		return $retvars;
	}

	// Action?
	if ($add) {
		// Compat: add plugin and adding contents
		$postdata = (isset($post['add_top']) && $post['add_top'])
			? $msg . "\n\n" . get_source($page, TRUE, TRUE)
			: get_source($page, TRUE, TRUE) . "\n\n" . $msg;
	} else {
		// Edit or Remove
		$postdata = & $msg;
	}

	// NULL POSTING, OR removing existing page
	if (empty($postdata)) {
		page_write($page, $postdata);
		$retvars['msg'] = $_title_deleted;
		$retvars['body'] = str_replace('$1', htmlsc($page), $_title_deleted);
		if ($trackback) tb_delete($page);
		return $retvars;
	}

	// $notimeupdate: Checkbox 'Do not change timestamp'
//	$notimestamp = isset($vars['notimestamp']) && $vars['notimestamp'] != '';
//	if ($notimeupdate > 1 && $notimestamp && ! pkwk_login($vars['pass'])) {
	if ($notimeupdate > 1 && $notimestamp && auth::check_role('role_adm_contents') && !pkwk_login($vars['pass'])) {
		// Enable only administrator & password error
		$retvars['body']  = '<p><strong>' . $_msg_invalidpass . '</strong></p>' . "\n";
		$retvars['body'] .= edit_form($page, $msg, $digest, FALSE);
		return $retvars;
	}

	page_write($page, $postdata, $notimeupdate != 0 && $notimestamp);

	if (isset($vars['refpage']) && $vars['refpage'] !== '') {
		$url = ($partid) ? get_page_location_uri($vars['refpage'],'',rawurlencode($partid)) : get_page_location_uri($vars['refpage']);
	} else {
		$url = ($partid) ? get_page_location_uri($page,'',rawurlencode($partid)) : get_page_location_uri($page);
	}

	// FaceBook Integration
	global $fb;
	if (isset($fb)){
		$fb_user = $fb->getUser();
		if ($fb_user === 0) {
			try {
				$response = $fb->api(
					array(
						'method' => 'stream.publish',
						'message' => sprintf($_string['update'], '<a href="'.$url.'">'.$page.'</a>'),
						'action_links' => array(
							array(
								'text' => $page_title,
								'href' => get_script_uri()
							),
							array(
								'text' => $page,
								'href' => $url
							)
						)
					)
				);
			} catch (FacebookApiException $e) {

			}
		}
	}
	pkwk_headers_sent();
	header('Location: ' . $url);

	exit;
}

// Cancel (Back to the page / Escape edit page)
function plugin_edit_cancel()
{
	global $vars;
	pkwk_headers_sent();
	header('Location: ' . get_page_location_uri($vars['page']));
	exit;
}

// Cancel (Back to the page / Escape edit page)
function plugin_edit_honeypot()
{
	// SPAM Logging
	honeypot_write();

	// Same as "Cancel" action
	return plugin_edit_cancel();
}

// Replace/Pickup a part of source
// BugTrack/110
function plugin_edit_parts($id, &$source, $postdata='')
{
	$postdata = rtrim($postdata) . "\n";
	$start = -1;
	$final = count($source);
	$multiline = 0;
	$matches = array();
	foreach ($source as $i => $line) {
		// multiline plugin. refer lib/convert_html
		if(defined('PKWKEXP_DISABLE_MULTILINE_PLUGIN_HACK') && PKWKEXP_DISABLE_MULTILINE_PLUGIN_HACK === 0) {
			if ($multiline < 2) {
				if (preg_match('/^#([^\(\{]+)(?:\(([^\r]*)\))?(\{*)/', $line, $matches)) {
					$multiline  = strlen($matches[3]);
				}
			} else {
				if (preg_match('/^\}{' . $multiline . '}/', $line, $matches)) {
					$multiline = 0;
				}
				continue;
			}
		}

		if ($start === -1) {
			if (preg_match('/^(\*{1,3})(.*?)\[#(' . preg_quote($id) . ')\](.*?)$/m', $line, $matches)) {
				$start = $i;
				$hlen = strlen($matches[1]);
			}
		} else {
			if (preg_match('/^(\*{1,3})/m', $line, $matches)) {
				if (PLUGIN_EDIT_PARTAREA !== 'level' or strlen($matches[1]) <= $hlen) {
					$final = $i;
					break;
				}
			}
		}
	}

	if ($start !== -1) {
		return join('', array_splice($source, $start, $final - $start, $postdata));
	}
	return FALSE;
}

/* End of file edit.inc.php */
/* Location: ./wiki-common/plugin/edit.inc.php */