<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: rename.inc.php,v 1.38.11 2012/05/11 18:20:00 Logue Exp $
// Copyright (C)
//   2010-2012 PukiWiki Advance DevelopersTeam
//   2005-2007 PukiWiki Plus! Team
//   2002-2005, 2007,2011 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Rename plugin: Rename page-name and related data
//
// Usage: http://path/to/index.php?plugin=rename[&refer=page_name]
use PukiWiki\Auth\Auth;
use PukiWiki\Wiki;
use PukiWiki\Factory;
use PukiWiki\Utility;
use PukiWiki\Router;
use PukiWiki\Relational;

define('PLUGIN_RENAME_LOGPAGE', ':RenameLog');

function plugin_rename_init()
{
	$messages = array(
		'_rename_messages' => array(
			'err'				=> T_('Error:%s'),
			'err_nomatch'		=> T_('no matching page(s)'),
			'err_notvalid'		=> T_('the new name is invalid.'),
			'err_adminpass'		=> T_('Incorrect administrator password.'),
			'err_notpage'		=> T_('%s is not a valid pagename.'),
			'err_norename'		=> T_('cannot rename %s.'),
			'err_already'		=> T_('already exists :%s.'),
			'err_already_below'	=> T_('The following files already exist.'),
			'msg_title'			=> T_('Rename page'),
			'msg_page'			=> T_('specify source page name'),
			'msg_regex'			=> T_('rename with regular expressions.'),
			'msg_related'		=> T_('related pages'),
			'msg_do_related'	=> T_('A related page is also renamed.'),
			'msg_rename'		=> T_('rename %s'),
			'msg_oldname'		=> T_('current page name'),
			'msg_newname'		=> T_('new page name'),
			'msg_adminpass'		=> T_('Administrator password'),
			'msg_arrow'			=> T_('->'),
			'msg_exist_none'	=> T_('page is not processed when it already exists.'),
			'msg_exist_overwrite' => T_('page is overwritten when it already exists.'),
			'msg_confirm'		=> T_('The following files will be renamed.'),
			'msg_result'		=> T_('The following files have been overwritten.'),
			'btn_submit'		=> T_('Submit'),
			'btn_next'			=> T_('Next')
		),
	);
	set_plugin_messages($messages);
}

function plugin_rename_action()
{
	global $_string;
	// if (PKWK_READONLY) die_message('PKWK_READONLY prohibits this');
	if (Auth::check_role('readonly')) die_message($_string['prohibit']);
	$method = plugin_rename_getvar('method');
	if ($method == 'regex') {
		$src = plugin_rename_getvar('src');
		if ($src == '') return plugin_rename_phase1();

		$src_pattern = '/' . preg_quote($src, '/') . '/';
		$arr0 = preg_grep($src_pattern, Auth::get_existpages());
		if (! is_array($arr0) || empty($arr0))
			return plugin_rename_phase1('nomatch');

		$dst = plugin_rename_getvar('dst');
		$arr1 = preg_replace($src_pattern, $dst, $arr0);
		foreach ($arr1 as $page){
			if (! is_pagename($page)){
				return plugin_rename_phase1('notvalid');
			}else if (preg_match(Wiki::INVALIED_PAGENAME_PATTERN, $page)){
				die_message($_string['illegal_chars']);
			}
		}

		// Phase one or three
		return plugin_rename_regex($arr0, $arr1);

	} else {
		// $method == 'page'
		$page  = plugin_rename_getvar('page');
		$refer = plugin_rename_getvar('refer');
		
		// Check Illigal Chars
		if (preg_match(Wiki::INVALIED_PAGENAME_PATTERN, $page)){
			die_message($_string['illegal_chars']);
		}

		if (empty($refer)) {
			return plugin_rename_phase1();
		} else if (! is_page($refer)) {
			return plugin_rename_phase1('notpage', $refer);

		} else if (is_cantedit($refer)) {
			return plugin_rename_phase1('norename', $refer);

		 } else if (!empty($page) && $page === $refer) {
			return plugin_rename_phase2();

		} else if (! is_pagename($page)) {
			return plugin_rename_phase2('notvalid');

		} else {
			// Phase three
			return plugin_rename_refer();
		}
	}
}

// 変数を取得する
function plugin_rename_getvar($key)
{
	global $vars;
	return isset($vars[$key]) ? $vars[$key] : '';
}

// Generating error messages
function plugin_rename_err($err, $page = '')
{
	global $_rename_messages;

	if ($err == '') return '';

	$body = $_rename_messages['err_' . $err];
	if (is_array($page)) {
		$tmp = '';
		foreach ($page as $_page) $tmp .= '<br />' . $_page;
		$page = $tmp;
	}
	if ($page != '') $body = sprintf($body, htmlsc($page));

	$msg = sprintf($_rename_messages['err'], $body);
	return $msg;
}

// Phase one: Specifying page name or regex
function plugin_rename_phase1($err = '', $page = '')
{
	global $_rename_messages;

	$msg    = plugin_rename_err($err, $page);
	$refer  = plugin_rename_getvar('refer');
	$method = plugin_rename_getvar('method');

	$radio_regex = $radio_page = '';
	if ($method == 'regex') {
		$radio_regex = ' checked="checked"';
	} else {
		$radio_page  = ' checked="checked"';
	}
	$select_refer = plugin_rename_getselecttag($refer);

	$s_src = Utility::htmlsc(plugin_rename_getvar('src'));
	$s_dst = Utility::htmlsc(plugin_rename_getvar('dst'));
	$script = Router::get_script_uri();

	return array(
		'msg'	=> $_rename_messages['msg_title'],
		'body'	=> <<<EOD
<fieldset>
	<legend>{$_rename_messages['msg_title']}</legend>
	<form action="$script" method="post" class="plugin-rename-form">
		<input type="hidden" name="cmd" value="rename" />
		<div class="radio">
			<input type="radio"  name="method" id="_p_rename_page" value="page"$radio_page />
			<label for="_p_rename_page">{$_rename_messages['msg_page']}:</label>
			$select_refer
		</div>
		<div class="radio">
			<input type="radio"  name="method" id="_p_rename_regex" value="regex"$radio_regex />
			<label for="_p_rename_regex">{$_rename_messages['msg_regex']}:</label>
			<div class="row">
				<div class="col-xs-6 form-group">
					<label for="_p_rename_from">From:</label>
					<input type="text" name="src" id="_p_rename_from" size="40" value="$s_src" class="form-control" />
				</div>
				<div class="col-xs-6 form-group">
					<label for="_p_rename_to">To:</label>
					<input type="text" name="dst" id="_p_rename_to"   size="40" value="$s_dst" class="form-control" />
				</div>
			</div>
		</div>
		<input type="submit" class="btn btn-warning" value="{$_rename_messages['btn_next']}" />
	</form>
</fieldset>
EOD
	);
}

// Phase two: Specify new page name
function plugin_rename_phase2($err = '')
{
	global $_rename_messages;

	$msg   = plugin_rename_err($err);
	$page  = plugin_rename_getvar('page');
	$refer = plugin_rename_getvar('refer');
	if ($page == '') $page = $refer;

	$msg_related = '';
	$related = plugin_rename_getrelated($refer);
	if (! empty($related))
		$msg_related = '<input type="checkbox" name="related" id="_p_rename_related" value="1" checked="checked" />' .
		'<label for="_p_rename_related">' . $_rename_messages['msg_do_related'] . '</label><br />';

	$msg_rename = sprintf($_rename_messages['msg_rename'], make_pagelink($refer));
	$s_page  = Utility::htmlsc($page);
	$s_refer = Utility::htmlsc($refer);

	$ret = array();
	$ret['msg']  = $_rename_messages['msg_title'];
	$script = Router::get_script_uri();
	$ret['body'] = <<<EOD
$msg
<fieldset>
	<legend>$msg_rename</legend>
	<form action="$script" method="post" class="plugin-rename-form">
		<input type="hidden" name="cmd" value="rename" />
		<input type="hidden" name="refer"  value="$s_refer" />
		<div class="form-group">
			<label for="_p_rename_newname">{$_rename_messages['msg_newname']}:</label>
			<input type="text" name="page" id="_p_rename_newname" size="40" value="$s_page" class="form-control" />
		</div>
		$msg_related
		<input type="submit" class="btn btn-warning" value="{$_rename_messages['btn_next']}" />
	</form>
</fieldset>
EOD;
	if (! empty($related)) {
		$ret['body'] .= '<hr /><p>' . $_rename_messages['msg_related'] . '</p><ul>';
		sort($related, SORT_STRING);
		foreach ($related as $name)
			$ret['body'] .= '<li>' . make_pagelink($name) . '</li>';
		$ret['body'] .= '</ul>';
	}
	return $ret;
}

// Before phase three:Listing specified page and related pages
function plugin_rename_refer()
{
	$page  = plugin_rename_getvar('page');
	$refer = plugin_rename_getvar('refer');

	if (is_cantedit($page)) {
		return plugin_rename_phase2('notvalid');
	}

	$pages[encode($refer)] = encode($page);
	if (plugin_rename_getvar('related') != '') {
		$from = strip_bracket($refer);
		$to   = strip_bracket($page);
		foreach (plugin_rename_getrelated($refer) as $_page) {
			$pages[encode($_page)] = encode(str_replace($from, $to, $_page));
		}
	}
	return plugin_rename_phase3($pages);
}

// Before phase one or three: Replace specified page and related pages' name
function plugin_rename_regex($arr_from, $arr_to)
{
	$exists = array();
	foreach ($arr_to as $page){
		if (is_page($page)){
			$exists[] = $page;
		}
	}

	if (! empty($exists)) return plugin_rename_phase1('already', $exists);

	$pages = array();
	foreach ($arr_from as $refer) {
		$pages[encode($refer)] = encode(array_shift($arr_to));
		return plugin_rename_phase3($pages);
	}
}

// Phase three: Confirmation
function plugin_rename_phase3($pages)
{
	global $_rename_messages, $vars;

	$msg = $input = '';
	$files = plugin_rename_get_files($pages);

	$exists = array();
	foreach ($files as $_page=>$arr)
		foreach ($arr as $old=>$new)
			if (file_exists($new))
				$exists[$_page][$old] = $new;

	if ( isset($vars['menu']) && ! Auth::check_role('role_contents_admin') ) {
		return plugin_rename_phase4($pages, $files, $exists);
	}

	$pass = plugin_rename_getvar('pass');
	if ($pass != '' && pkwk_login($pass)) {
		return plugin_rename_phase4($pages, $files, $exists);
	} else if ($pass != '') {
		$msg = plugin_rename_err('adminpass');
	}

	$method = plugin_rename_getvar('method');
	if ($method == 'regex') {
		$s_src = htmlsc(plugin_rename_getvar('src'));
		$s_dst = htmlsc(plugin_rename_getvar('dst'));
		$msg   .= $_rename_messages['msg_regex'] . '<br />';
		$input .= '<input type="hidden" name="method" value="regex" />';
		$input .= '<input type="hidden" name="src"    value="' . $s_src . '" />';
		$input .= '<input type="hidden" name="dst"    value="' . $s_dst . '" />';
	} else {
		$s_refer   = htmlsc(plugin_rename_getvar('refer'));
		$s_page    = htmlsc(plugin_rename_getvar('page'));
		$s_related = htmlsc(plugin_rename_getvar('related'));
		$msg   .= $_rename_messages['msg_page'] . '<br />';
		$input .= '<input type="hidden" name="method"  value="page" />';
		$input .= '<input type="hidden" name="refer"   value="' . $s_refer   . '" />';
		$input .= '<input type="hidden" name="page"    value="' . $s_page    . '" />';
		$input .= '<input type="hidden" name="related" value="' . $s_related . '" />';
	}

	if (! empty($exists)) {
		$msg .= $_rename_messages['err_already_below'] . '<ul>';
		foreach ($exists as $page=>$arr) {
			$msg .= '<li>' . make_pagelink(decode($page));
			$msg .= $_rename_messages['msg_arrow'];
			$msg .= htmlsc(decode($pages[$page]));
			if (! empty($arr)) {
				$msg .= '<ul>' . "\n";
				foreach ($arr as $ofile=>$nfile)
					$msg .= '<li>' . $ofile .
					$_rename_messages['msg_arrow'] . $nfile . '</li>' . "\n";
				$msg .= '</ul>';
			}
			$msg .= '</li>' . "\n";
		}
		$msg .= '</ul><hr />' . "\n";

		$input .= '<input type="radio" name="exist" value="0" checked="checked" />' .
			$_rename_messages['msg_exist_none'] . '<br />' . "\n";
		$input .= '<input type="radio" name="exist" value="1" />' .
			$_rename_messages['msg_exist_overwrite'] . '<br />' . "\n";
	}

	$ret = array();
	$auth = '';
	if (Auth::check_role('role_contents_admin')) {
		$auth = <<<EOD
<div class="form-group">
  <label for="_p_rename_adminpass">{$_rename_messages['msg_adminpass']}</label>
  <input type="password" name="pass" id="_p_rename_adminpass" value="" class="form-control" />
</div>
EOD;
	}
	$ret['msg'] = $_rename_messages['msg_title'];
	$script = get_script_uri();
	$ret['body'] = <<<EOD
$msg
	<form action="$script" method="post" class="plugin-rename-form">
		<input type="hidden" name="cmd" value="rename" />
		<input type="hidden" name="menu"   value="1" />
		$input
		$auth
		<input type="submit" class="btn btn-warning" value="{$_rename_messages['btn_submit']}" />
	</form>
	<p>{$_rename_messages['msg_confirm']}</p>
EOD;

	ksort($pages, SORT_STRING);
	$ret['body'] .= '<ul>' . "\n";
	foreach ($pages as $old=>$new)
		$ret['body'] .= '<li>' .  make_pagelink(decode($old)) .
			$_rename_messages['msg_arrow'] .
			Utility::htmlsc(Utility::decode($new)) .  '</li>' . "\n";
	$ret['body'] .= '</ul>' . "\n";
	return $ret;
}

function plugin_rename_get_files($pages)
{
	global $log,$trackback,$referer;

	$files = array();
	$dirs  = array(BACKUP_DIR, DIFF_DIR, DATA_DIR);
	if (exist_plugin_convert('attach'))  $dirs[] = UPLOAD_DIR;
	if (exist_plugin_convert('counter')) $dirs[] = COUNTER_DIR;
	if ($trackback > 0 || $referer > 0) $dirs[] = TRACKBACK_DIR;
	foreach(array('update','download','browse') as $log_subdir) {
		if ($log[$log_subdir]['use']) $dirs[] = LOG_DIR.$log_subdir.'/';
	}
	// and more ...

	$matches = array();
	foreach ($dirs as $path) {
		$dir = opendir($path);
		if (! $dir) continue;	// TODO: !== FALSE or die()?

		while (($file = readdir($dir)) !== FALSE) {
			if ($file == '.' || $file == '..') continue;

			foreach ($pages as $from=>$to) {
				 // TODO: preg_quote()?
				$pattern = '/^' . str_replace('/', '\/', $from) . '([._].+)$/';
				if (preg_match($pattern, $file, $matches)) {
					$newfile = $to . $matches[1];
					$files[$from][$path . $file] = $path . $newfile;
				}
			}
		}
	}
	return $files;
}

// Phase four: Rename them, Log them, Redirect
 function plugin_rename_phase4($pages, $files, $exists)
{
	global $now, $_rename_messages;

	if (plugin_rename_getvar('exist') == '')
		foreach ($exists as $key=>$arr)
			unset($files[$key]);

	set_time_limit(0);
	foreach ($files as $page=>$arr) {
		foreach ($arr as $old=>$new) {
			if (isset($exists[$page][$old]) && $exists[$page][$old])
				unlink($new);
			rename($old, $new);

			// Update link database (BugTrack/327) arino
			//links_update($old);
			//links_update($new);
			$links = new Relational();
			$links->update($old);
			$links->update($new);
		}
	}

	$wiki = Factory::Wiki(PLUGIN_RENAME_LOGPAGE);
	$postdata = $wiki->get();
	$postdata[] = '*' . $now . "\n";
	if (plugin_rename_getvar('method') == 'regex') {
		$postdata[] = '-' . $_rename_messages['msg_regex'] . "\n";
		$postdata[] = '--From:[[' . plugin_rename_getvar('src') . ']]' . "\n";
		$postdata[] = '--To:[['   . plugin_rename_getvar('dst') . ']]' . "\n";
	} else {
		$postdata[] = '-' . $_rename_messages['msg_page'] . "\n";
		$postdata[] = '--From:[[' . plugin_rename_getvar('refer') . ']]' . "\n";
		$postdata[] = '--To:[['   . plugin_rename_getvar('page')  . ']]' . "\n";
	}

	if (! empty($exists)) {
		$postdata[] = "\n" . $_rename_messages['msg_result'] . "\n";
		foreach ($exists as $page=>$arr) {
			$postdata[] = '-' . decode($page) .
				$_rename_messages['msg_arrow'] . decode($pages[$page]) . "\n";
			foreach ($arr as $ofile=>$nfile)
				$postdata[] = '--' . $ofile .
					$_rename_messages['msg_arrow'] . $nfile . "\n";
		}
		$postdata[] = '----' . "\n";
	}

	foreach ($pages as $old=>$new)
		$postdata[] = '-' . decode($old) .
			$_rename_messages['msg_arrow'] . decode($new) . "\n";

	// At this time, collision detection is not implemented

	$wiki->set($postdata);

	cache_timestamp_touch();

	$page = plugin_rename_getvar('page');
	if ($page == '') $page = PLUGIN_RENAME_LOGPAGE;

	// Redirection
	pkwk_headers_sent();
	header('Location: ' . get_page_location_uri($page));
	exit;
}

function plugin_rename_getrelated($page)
{
	$related = array();
	$pages = Auth::get_existpages();
	$pattern = '/(?:^|\/)' . preg_quote(strip_bracket($page), '/') . '(?:\/|$)/';
	foreach ($pages as $name) {
		if ($name === $page) continue;
		if (preg_match($pattern, $name)) $related[] = $name;
	}
	return $related;
}

function plugin_rename_getselecttag($page)
{
	global $whatsnew;

	$pages = array();
	foreach (Auth::get_existpages() as $_page) {
		if (is_cantedit($_page)) continue;

		$selected = ($_page === $page) ? ' selected' : '';
		$s_page = htmlsc($_page);
		$pages[$_page] = '<option value="' . $s_page . '"' . $selected . '>' .
			$s_page . '</option>';
	}
	ksort($pages, SORT_STRING);
	$list = join("\n" . ' ', $pages);

	return <<<EOD
<select name="refer" class="form-control">
	<option value="" disabled="disabled" selected="selected">--------------------</option>
	$list
</select>
EOD;

}
/* End of file remote.inc.php */
/* Location: ./wiki-common/plugin/remote.inc.php */
