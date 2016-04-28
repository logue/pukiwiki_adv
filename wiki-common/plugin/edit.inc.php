<?php
/**
 * PukiWiki Advance - Yet another WikiWikiWeb clone.
 * $Id: edit.inc.php,v 1.49.48 2015/12/06 00:21:00 Logue Exp $
 * Copyright (C)
 *   2010-2015 PukiWiki Advance Developers Team
 *   2005-2009 PukiWiki Plus! Team
 *   2001-2007,2011 PukiWiki Developers Team
 * License: GPL v2 or (at your option) any later version
 *
 * Edit plugin (cmd=edit)
 */

use PukiWiki\Auth\Auth;
use PukiWiki\Factory;
use PukiWiki\Renderer\Header;
use PukiWiki\Renderer\RendererFactory;
use PukiWiki\Renderer\RendererDefines;
use PukiWiki\Text\Rules;
use PukiWiki\Time;
use PukiWiki\Utility;
use Zend\Json\Json;

// Remove #freeze written by hand
define('PLUGIN_EDIT_FREEZE_REGEX', '/^(?:#freeze(?!\w)\s*)+/im');

// Define part-edit area - 'compat':1.4.4compat, 'level':level
defined('PLUGIN_EDIT_PARTAREA') or define('PLUGIN_EDIT_PARTAREA', 'compat');

function plugin_edit_init(){
	global $_string;
	$msg = array(
		'_edit_msg' => array(
			'msg_edit'          => T_('Edit'),
			'err_empty_page'    => T_('Pagename is missing.'),
			'title_edit'        => T_('Edit of %s'),
			'err_long'          => T_('Pagename is too long.'),
			'title_preview'     => T_('Preview of %s'),
			'msg_preview'       => T_('To confirm the changes, click the button at the bottom of the page'),
			'msg_preview_delete'=> T_('(The contents of the page are empty. Updating deletes this page.)'),
			'title_deleted'     => T_(' $1 was deleted'),
			'msg_invalidpass'   => $_string['invalidpass']
		)
	);
	set_plugin_messages($msg);
}

function plugin_edit_action()
{
// global $vars, $_title_edit, $load_template_func;
	global $vars, $load_template_func, $_string, $_edit_msg;

	$page = isset($vars['page']) ? $vars['page'] : null;
	if (empty($page)) return array('msg'=> $_edit_msg['msg_edit'], 'body'=>$_edit_msg['err_empty_page']);
	$wiki = Factory::Wiki($page);

	if (!$wiki->isEditable(true)){
		Utility::dieMessage( $_string['err_empty_page'], 403 );
	}

	// if (PKWK_READONLY) die_message(  sprintf($_string['error_prohibit'], 'PKWK_READONLY') );
	if (Auth::check_role('readonly')) Utility::dieMessage( $_string['error_prohibit'],403 );

	if (PKWK_READONLY == Auth::ROLE_AUTH && Auth::get_role_level() > Auth::ROLE_AUTH) {
		Utility::dieMessage( sprintf($_string['error_prohibit'], 'PKWK_READONLY'), 403 );
	}

	if (isset($vars['realview'])) {
		return plugin_edit_realview();
	}

	if (!$wiki->has() && Auth::is_check_role(PKWK_CREATE_PAGE)) {
		Utility::dieMessage( sprintf($_string['error_prohibit'], 'PKWK_CREATE_PAGE'),403 );
	}

	if (preg_match($wiki::INVALIED_PAGENAME_PATTERN, $page)){
		Utility::dieMessage($_string['illegal_chars']);
	}

	if (isset($vars['preview']) || ($load_template_func && isset($vars['template']))) {
		return plugin_edit_preview();
	} else if (isset($vars['write'])) {
		return plugin_edit_write();
	} else if (isset($vars['cancel'])) {
		return plugin_edit_cancel();
	}

	$postdata = $vars['original'] = $wiki->get(true);
	Auth::is_role_page($postdata);

	if (isset($vars['id']) && !empty($vars['id']))
	{
		$source = $wiki->get();
		$postdata = plugin_edit_parts($vars['id'],$source);
		if ($postdata === FALSE)
		{
			unset($vars['id']); // なかったことに :)
			$postdata = $vars['original'];
		}
	}

	if (empty($postdata) ){
		// Check Page name length
		// http://pukiwiki.sourceforge.jp/dev/?PukiWiki%2F1.4%2F%A4%C1%A4%E7%A4%C3%A4%C8%CA%D8%CD%F8%A4%CB%2F%C4%B9%A4%B9%A4%AE%A4%EB%A5%DA%A1%BC%A5%B8%CC%BE%A4%CE%A5%DA%A1%BC%A5%B8%A4%CE%BF%B7%B5%AC%BA%EE%C0%AE%A4%F2%CD%DE%BB%DF
		$filename_max_length = 250;
		$filename = Utility::encode($page) . '.txt';
		$filename_length = strlen($filename);
		if ($filename_length > $filename_max_length){
			// Filename too long
			return array('msg'=>$_edit_msg['title_edit'], 'body'=>join("\n", array(
				'<p class="alert alert-warning"><span class="fa fa-exclamation-triangle"></span>' . $_edit_msg['err_long'] , '</p>',
				'<dl class="dl-horizontal">',
				'<dt>Page name</dt>',
				'<dd>' . Utility::htmlsc($page) . '</dd>',
				'<dt>Filename</dt>',
				'<dd>' . $filename . '</dd>',
				'<dt>Filename length</dt>',
				'<dd>' . $filename_length . '</dd>',
				'<dt>Filename limit</dt>',
				'<dd>' . $filename_max_length . '</dd>',
				'</dl>'
				)
			));
		}else{
			$postdata = $wiki->auto_template();
		}
	}

	return array('msg'=> sprintf($_edit_msg['title_edit'], $page), 'body'=>Utility::editForm($page, $postdata));
}

// Preview by Ajax
function plugin_edit_realview()
{
	global $vars;

	$vars['msg'] = preg_replace(PLUGIN_EDIT_FREEZE_REGEX, '' ,$vars['msg']);
	$postdata = $vars['msg'];

	if ($postdata) {
		$postdata = Rules::make_str_rules($postdata);
		$postdata = explode("\n", $postdata);
		$postdata = drop_submit(RendererFactory::factory($postdata));
	}

	if (isset($vars['ajax']) && $vars['ajax'] === 'xml'){
		$headers = Header::getHeaders('application/xml');
		$content = '<' . '?xml version="1.0" encoding="UTF-8" ?' . '>'."\n".'<response>' . "\n" . 
			'<title>' . $this->title . '</title>' . "\n" . 
			'<body><![CDATA[' . $this->body . ']]></body>' . "\n" .
			'<process_time>' . Time::getTakeTime() . '</process_time>' . "\n" . '</response>';
		Header::writeResponse($headers, 200, $content);
	}else{
		$headers = Header::getHeaders('application/json');
		Header::writeResponse($headers, 200, Json::encode(array(
			'data'			=> $postdata,
			'taketime'		=> Time::getTakeTime()
		)));
	}
	exit;
}

// Preview
function plugin_edit_preview()
{
	global $vars, $_edit_msg;
	$_msg_preview			= T_('To confirm the changes, click the button at the bottom of the page');
	$_msg_preview_delete	= T_('(The contents of the page are empty. Updating deletes this page.)');

	$page = isset($vars['page']) ? $vars['page'] : '';

	// Loading template
	if (isset($vars['template_page']) && $wiki->isValied()) {
		$wiki = Factory::Wiki($vars['template_page']);

		$vars['msg'] = $wiki->get(true);

		// Cut fixed anchors
		$vars['msg'] = preg_replace('/^(\*{1,3}.*)\[#[A-Za-z0-9][\w-]+\](.*)$/m', '$1$2', $vars['msg']);
	}else{
		$wiki = Factory::Wiki($page);
	}

	$vars['msg'] = preg_replace(PLUGIN_EDIT_FREEZE_REGEX, '', $vars['msg']);
	$postdata = $vars['msg'];

	// Compat: add plugin and adding contents
	if (isset($vars['add']) && $vars['add']) {
		if (isset($vars['add_top']) && $vars['add_top']) {
			$postdata  .= "\n\n" . $wiki->get(true);
		} else {
			$postdata  = $wiki->get(true) . "\n\n" . $postdata;
		}
	}

	$body = '<p class="alert alert-info">' . $_edit_msg['msg_preview'] . '<br />' . "\n";
	if ($postdata == '')
		$body .= '<strong>' . $_edit_msg['msg_preview_delete'] . '</strong>';
	$body .= '</p>' . "\n";

	if ($postdata) {
		$postdata = Rules::make_str_rules($postdata);
		$postdata = explode("\n", $postdata);
		$postdata = drop_submit(RendererFactory::factory($postdata));
		$body .= '<div id="preview">' . $postdata . '</div>' . "\n";
	}
	$body .= Utility::editForm($page, $vars['msg'], $vars['digest'], FALSE);

	return array('msg'=>sprintf($_edit_msg['title_preview'], $page), 'body'=>$body);
}

// Inline: Show edit (or unfreeze text) link
// NOTE: Plus! is not compatible for 1.4.4+ style(compatible for 1.4.3 style)
function plugin_edit_inline()
{
	static $usage = '<span style="alert alert-warning">&amp;edit(pagename,anchor);</span>';

	global $vars, $fixed_heading_edited;
	
	$page = isset($vars['page']) ? $vars['page'] : null;
	
	// Arguments
	$args = func_get_args();
	
	if (!empty($args[0])) {
		$page = $args[0];
	}

	if (empty($page)) return '';

	$wiki = Factory::Wiki($page);

	if (!$fixed_heading_edited || $wiki->isFreezed() || Auth::check_role('readonly')) {
		return '';
	}

	// {label}. Strip anchor tags only
	$s_label = Utility::stripHtmlTags(array_pop($args), FALSE);
	$s_label_edit = empty($s_label) ? RendererDefines::PARTIAL_EDIT_LINK_ICON : $s_label;

	list($page,$id) = array_pad($args,2,'');
	if (!is_page($page)) {
		$page = $vars['page'];
	}

	$tag_edit = '<a class="anchor_super" id="edit_'.$id.'" href="' . $wiki->uri('edit',array('id'=>$id)) . '" rel="nofollow">' . $s_label_edit . '</a>';
	return $tag_edit;
}

// Write, add, or insert new comment
function plugin_edit_write()
{
	global $vars, $trackback, $_string, $_msg_edit;
	global $notimeupdate;

	$page   = isset($vars['page'])   ? $vars['page']   : null;
	$add    = isset($vars['add'])    ? $vars['add']    : null;
	$digest = isset($vars['digest']) ? $vars['digest'] : null;
	$partid = isset($vars['id'])     ? $vars['id']     : null;
	$notimestamp = isset($vars['notimestamp']) && $vars['notimestamp'] !== null;

	if (empty($page)) return array('mgs'=>'Error', 'body'=>$_msg_edit['err_empty_page']);

	$wiki = Factory::Wiki($page);
	// Check Validate and Ticket
	if ($notimestamp && !$wiki->isValied()) {
		return plugin_edit_honeypot();
	}

	// Validate
	if (is_spampost(array('msg')))
		return plugin_edit_honeypot();

	// Paragraph edit mode
	if ($partid) {
		$source = preg_split('/([^\n]*\n)/', $vars['original'], -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
		$vars['msg'] = plugin_edit_parts($partid, $source, $vars['msg']) !== FALSE
			? join('', $source)
			: rtrim($vars['original']) . "\n\n" . $vars['msg'];
	}

	$retvars = array();

	if (isset($vars['msg']) && !empty($vars['msg'])) {
		// Delete "#freeze" command for form edit.
		$vars['msg'] = preg_replace('/^#freeze\s*$/im', '', $vars['msg']);
		$msg = $vars['msg']; // Reference
		
		// Action?
		if ($add) {
			// Compat: add plugin and adding contents
			$postdata = isset($vars['add_top']) && $vars['add_top']
				? $msg . "\n\n" . $oldpagesrc
				: $oldpagesrc . "\n\n" . $msg;
		} else {
			// Edit or Remove
			$postdata = & $msg;
		}
	}else{
		// CAPTCHAが有効なときで、ページを削除しようとした時、$vars['msg']は空になる。
		$wiki->set('');
		$retvars['msg'] = sprintf($_msg_edit['title_deleted'], Utility::htmlsc($page));
		$retvars['body'] = '<p class="alert alert-success">' . sprintf($_msg_edit['title_deleted'], Utility::htmlsc($page)) . '</p>';
		return $retvars;
	}

	// $notimeupdate: Checkbox 'Do not change timestamp'
//	$notimestamp = isset($vars['notimestamp']) && $vars['notimestamp'] != '';
//	if ($notimeupdate > 1 && $notimestamp && ! pkwk_login($vars['pass'])) {
	if ($notimeupdate > 1 && $notimestamp && Auth::check_role('role_contents_admin') && !pkwk_login($vars['pass'])) {
		// Enable only administrator & password error
		$retvars['body']  = '<p class="alert alert-danger">' . $_msg_edit['msg_invalidpass'] . '</p>' . "\n";
		$retvars['body'] .= Utility::editForm($page, $msg, FALSE);
		return $retvars;
	}

	$wiki->set($postdata, $notimeupdate !== 0 && $notimestamp);

	if (isset($vars['refpage']) && $vars['refpage'] !== '') {
		$refwiki = Factory::Wiki($vars['refpage']);
		$url = $partid ? $refwiki->uri('read', null, rawurlencode($partid)) : $refwiki->uri();
	} else {
		$url = $partid ? $wiki->uri('read', null ,rawurlencode($partid)) : $wiki->uri();
	}
	if (isset($vars['ajax'])) {
		$headers = Header::getHeaders('application/json');
		Header::writeResponse($headers, 200, Json::encode(array(
			'msg'       => 'Your post has been saved.',
			'posted'    => true,
			'taketime'  => Time::getTakeTime()
		)));
	}else{
		Utility::redirect($url);
	}
	exit;
}

// Cancel (Back to the page / Escape edit page)
function plugin_edit_cancel()
{
	Utility::redirect();
	exit;
}

// Cancel (Back to the page / Escape edit page)
function plugin_edit_honeypot()
{
	if (isset($vars['ajax'])) {
		$headers = Header::getHeaders('application/json');
		Header::writeResponse($headers, 200, Json::encode(array(
			'posted'    => false,
			'message'   => 'Sorry your post has been prohibited.',
			'taketime'  => Time::getTakeTime()
		)));
		exit;
	}
	// SPAM Logging
	Utility::dump();

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
		// 複数行のプラグイン
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

		// アンカーIDによる判定
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
		return join("\n", array_splice($source, $start, $final - $start, $postdata));
	}
	return FALSE;
}

/* End of file edit.inc.php */
/* Location: ./wiki-common/plugin/edit.inc.php */