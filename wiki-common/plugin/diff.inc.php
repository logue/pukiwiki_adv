<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: diff.inc.php,v 1.20.9 2011/02/05 10:23:00 Logue Exp $
// Copyright (C)
//   2010-2011 PukiWiki Advance Developers Team
//   2005-2006,2008 PukiWiki Plus! Team
//   2002-2005,2007,2011 PukiWiki Developers Team
//   2002      Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// Showing colored-diff plugin

function plugin_diff_action()
{
	global $vars;

	$page = isset($vars['page']) ? $vars['page'] : '';
	check_readable($page, true, true);

	$action = isset($vars['action']) ? $vars['action'] : '';
	switch ($action) {
		case 'delete': $retval = plugin_diff_delete($page);	break;
		default:       $retval = plugin_diff_view($page);	break;
	}
	return $retval;
}

function plugin_diff_view($page)
{
	global $hr;
//	global $_msg_notfound, $_msg_goto, $_msg_deleted, $_msg_addline, $_msg_delline;
//	global $_title_diff, $_title_diff_delete;

	if (auth::check_role('safemode')) die_message('PKWK_SAFE_MODE prohibits this');

	$_msg_notfound       = T_('The page was not found.');
	$_msg_addline        = T_('The added line is <span class="diff_added">THIS COLOR</span>.');
	$_msg_delline        = T_('The deleted line is <span class="diff_removed">THIS COLOR</span>.');
	$_msg_goto           = T_('Go to $1.');
	$_msg_deleted        = T_(' $1 has been deleted.');
	$_title_diff         = T_('Diff of $1');
	$_title_diff_delete  = T_('Deleting diff of $1');

	$r_page = rawurlencode($page);
	$s_page = htmlsc($page);

	$menu = array(
		'<li>' . $_msg_addline . '</li>',
		'<li>' . $_msg_delline . '</li>'
	);

	$is_page = is_page($page);
	if ($is_page) {
		$menu[] = ' <li>' . str_replace('$1', '<a href="' . get_page_uri($page) . '">' .
			$s_page . '</a>', $_msg_goto) . '</li>';
	} else {
		$menu[] = ' <li>' . str_replace('$1', $s_page, $_msg_deleted) . '</li>';
	}

	$filename = DIFF_DIR . encode($page) . '.txt';
	if (file_exists($filename)) {
		// if (! PKWK_READONLY) {
		if (! auth::check_role('readonly')) {
			$menu[] = '<li><a href="' . get_cmd_uri('diff', $page, null, array('action'=>'delete')) . '">' . str_replace('$1', $s_page, $_title_diff_delete) . '</a></li>';
		}
		$source = join('', file($filename));
		auth::is_role_page($source);
		//$msg = '<pre>' . diff_style_to_css(htmlsc($source)) . '</pre>' . "\n";
		
		$msg = '<pre class="sh" data-brush="diff">' . htmlsc($source) . '</pre>' . "\n";
	} else if ($is_page) {
		$source = join('', get_source($page));
		auth::is_role_page($source);
		$diffdata = trim(htmlsc($source));
		$msg = '<pre class="sh" data-brush="diff"><ins class="diff_added">' . $diffdata . '</ins></pre>' . "\n";
	} else {
		return array('msg'=>$_title_diff, 'body'=>$_msg_notfound);
	}

	$menu = join("\n", $menu);
	$body = <<<EOD
<ul>
$menu
</ul>
$hr
EOD;

	return array('msg'=>$_title_diff, 'body'=>$body . $msg);
}

function plugin_diff_delete($page)
{
	global $vars;
//	global $_title_diff_delete, $_msg_diff_deleted;
//	global $_msg_diff_adminpass, $_btn_delete, $_msg_invalidpass;

	if (auth::check_role('readonly')) die_message('PKWK_READONLY prohibits editing');

	$_title_diff_delete  = T_('Deleting diff of $1');
	$_msg_diff_deleted   = T_('Diff of  $1 has been deleted.');
	$_msg_diff_adminpass = T_('Please input the password for deleting.');
	$_btn_delete         = T_('Delete');
	$_msg_invalidpass    = T_('Invalid password.');

	$filename = DIFF_DIR . encode($page) . '.txt';
	$body = '';
	if (! is_pagename($page))     $body = 'Invalid page name';
	if (! file_exists($filename)) $body = make_pagelink($page) . '\'s diff seems not found';
	if ($body) return array('msg'=>$_title_diff_delete, 'body'=>$body);

	if (! auth::check_role('role_adm_contents')) {
		unlink($filename);
		return array(
			'msg'  => $_title_diff_delete,
			'body' => str_replace('$1', make_pagelink($page), $_msg_diff_deleted)
		);
        }

	if (isset($vars['pass'])) {
		if (pkwk_login($vars['pass'])) {
			unlink($filename);
			return array(
				'msg'  => $_title_diff_delete,
				'body' => str_replace('$1', make_pagelink($page), $_msg_diff_deleted)
			);
		} else {
			$body .= '<p><strong>' . $_msg_invalidpass . '</strong></p>' . "\n";
		}
	}

	$s_page = htmlsc($page);
	$script = get_script_uri();
	$body .= <<<EOD
<p>$_msg_diff_adminpass</p>
<form action="$script" method="post">
 <div>
  <input type="hidden"   name="cmd"    value="diff" />
  <input type="hidden"   name="page"   value="$s_page" />
  <input type="hidden"   name="action" value="delete" />
  <input type="password" name="pass"   size="12" />
  <input type="submit"   name="ok"     value="$_btn_delete" />
 </div>
</form>
EOD;

	return array('msg'=>$_title_diff_delete, 'body'=>$body);
}
?>
