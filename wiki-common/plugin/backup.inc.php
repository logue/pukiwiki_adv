<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: backup.inc.php,v 1.29.22 2011/02/05 10:43:00 Logue Exp $
// Copyright (C)
//   2010-2011 PukiWiki Advance Developers Team
//   2008 PukioWikio Developers Team
//   2005-2008 PukiWiki Plus! Team
//   2002-2005,2007 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// Backup plugin

// Prohibit rendering old wiki texts (suppresses load, transfer rate, and security risk)
// define('PLUGIN_BACKUP_DISABLE_BACKUP_RENDERING', PKWK_SAFE_MODE || PKWK_OPTIMISE);
define('PLUGIN_BACKUP_DISABLE_BACKUP_RENDERING', auth::check_role('safemode') || PKWK_OPTIMISE);

// ロールバック機能を有効にする
defined('PLUGIN_BACKUP_USE_ROLLBACK') or define('PLUGIN_BACKUP_USE_ROLLBACK', TRUE);

// 管理人のみロールバック機能を使える
defined('PLUGIN_BACKUP_ROLLBACK_ADMINONLY') or define('PLUGIN_BACKUP_ROLLBACK_ADMINONLY', TRUE);

/**
 * plugin_backup_init()
 * backup plugin initialization.
 * load necessary libraries.
 */
function plugin_backup_init()
{
	$messages = array(
		'_backup_messages' => array(
			'btn_delete'			=> T_('Delete'),
			'btn_jump'				=> T_('Jump'),
			'msg_backup'			=> T_('backup'),
			'msg_backup_adminpass'	=> T_('Please input the password for deleting.'),
			'msg_backup_deleted'	=> T_('Backup of $1 has been deleted.'),
			'msg_backuplist'		=> T_('Backup list'),
			'msg_deleted'			=> T_(' $1 has been deleted.'),
			'msg_diff'				=> T_('diff'),
			'msg_diff_add'			=> T_('The added line is <ins class="diff_added">THIS COLOR</ins>.'),
			'msg_diff_del'			=> T_('The deleted line is <del class="diff_removed">THIS COLOR</del>.'),
			'msg_goto'				=> T_('Go to $1.'),
			'msg_invalidpass'		=> T_('Invalid password.'),
			'msg_nobackup'			=> T_('There are no backup(s) of $1.'),
			'msg_nowdiff'			=> T_('diff current'),
			'msg_source'			=> T_('source'),
			'msg_rollback'			=> T_('roleback'),
			'msg_version'			=> T_('Versions:'),
			'msg_view'				=> T_('View the $1.'),
			'msg_visualdiff'		=> T_('diff for visual'),
			'msg_arrow'				=> T_('-&gt;'),
			
			'title_backup'			=> T_('Backup of $1(No. $2)'),
			'title_backup_delete'	=> T_('Deleting backup of $1'),
			'title_backupdiff'		=> T_('Backup diff of $1(No. $2)'),
			'title_backuplist'		=> T_('Backup list'),
			'title_backupnowdiff'	=> T_('Backup diff of $1 vs current(No. $2)'),
			'title_backupsource'	=> T_('Backup source of $1(No. $2)'),
			'title_pagebackuplist'	=> T_('Backup list of $1'),
			
			'btn_rollback'				=> T_('Rollback'),
			'btn_selectdelete'			=> T_('Delete selected backup(s).'),
			'msg_backup_rollbacked'		=> T_('Rollbackd to $1.'),
			'title_backup_rollback'		=> T_('Rollback this backup(No. %s)'),
			'title_backup_rollbacked'	=> '$1 をバックアップに書き戻しました'
		)
	);
	set_plugin_messages($messages);
}

function plugin_backup_action()
{
	global $vars, $do_backup, $hr, $script, $_string;
	global $_backup_messages;
	if (! $do_backup) return;

	/**
	 * if page is not set, show list of backup files
	 */
	$page = isset($vars['page']) ? $vars['page']  : '';
	if ($page == ''){
		return array('msg'=>$_backup_messages['title_backuplist'], 'body'=>plugin_backup_get_list_all());
	}

	check_readable($page, true, true);
	$s_page = htmlsc($page);
	$r_page = rawurlencode($page);

	$action = isset($vars['action']) ? $vars['action'] : '';
	
	/**
	 * 指定された世代を確認。指定されていなければ、一覧のみ表示
	 */
	$s_age  = (isset($vars['age']) && is_numeric($vars['age'])) ? $vars['age'] : 0;
	
	if($action == 'delete') {
		// checkboxが選択されずにselectdeleteを実行された場合は、削除処理をしない
		if(! isset($vars['selectages']) &&		// checkboxが選択されていない
			isset($vars['selectdelete'])) {		// 選択削除ボタンが押された
												// 何もしない
		} else {
			if(! isset($vars['selectages'])) {	// 世代引数がない場合は全削除
				return plugin_backup_delete($page);
			}
			return plugin_backup_delete($page, $vars['selectages']);
		}
	}
	
	if($action == 'rollback') {
		return plugin_backup_rollback($page, $s_age);
	}

	if ($s_age <= 0) return array( 'msg'=>$_backup_messages['title_pagebackuplist'], 'body'=>plugin_backup_get_list($page));

	$body  = '<ul>' . "\n";
	//$body .= ' <li><a href="' . get_cmd_uri('backup') . '">' . $_backup_messages['msg_backuplist'] . '</a></li>' ."\n";
	
	$is_page = is_page($page);

	$body = plugin_backup_get_list($page);
/*
	if ($is_page){
		if ($action != 'diff')
			$body .= ' <li>' . str_replace('$1', '<a href="' . get_cmd_uri('backup',$page, null, array('action'=>'diff', 'age'=>$s_age)) . '">' .
			$_backup_messages['msg_diff'] . '</a>', $_backup_messages['msg_view']) . '</li>' . "\n";

		if ($action != 'nowdiff')
			$body .= ' <li>' . str_replace('$1', '<a href="' . get_cmd_uri('backup',$page, null, array('action'=>'nowdiff', 'age'=>$s_age)) . '">' .
			$_backup_messages['msg_nowdiff'] . '</a>', $_backup_messages['msg_view']) . '</li>' . "\n";
	}

	if ($action != 'visualdiff')
		$body .= ' <li>' . str_replace('$1', '<a href="' . get_cmd_uri('backup',$page, null, array('action'=>'visualdiff', 'age'=>$s_age)) . '">' .
		$_backup_messages['msg_visualdiff'] . '</a>', $_backup_messages['msg_view']) . '</li>' . "\n";

	if ($action != 'source')
		$body .= ' <li>' . str_replace('$1', '<a href="' . get_cmd_uri('backup',$page, null, array('action'=>'source', 'age'=>$s_age)) . '">' .
			$_backup_messages['msg_source'] . '</a>', $_backup_messages['msg_view']) . '</li>' . "\n";

	if (! PLUGIN_BACKUP_DISABLE_BACKUP_RENDERING && $action)
		$body .= ' <li>' . str_replace('$1', '<a href="' . get_cmd_uri('backup',$page, null, array('age'=>$s_age)) . '">' .
			$_backup_messages['msg_backup'] . '</a>', $_backup_messages['msg_view']) . '</li>' . "\n";

	if ($is_page) {
		$body .= ' <li>' . str_replace('$1',
			'<a href="' . get_page_uri($page) . '">' . $s_page . '</a>',
			$_backup_messages['msg_goto']) . "\n";
	} else {
		$body .= ' <li>' . str_replace('$1', '<var>'.$s_page.'</var>', $_backup_messages['msg_deleted']) . "\n";
	}
*/
	$backups = get_backup($page);
	$backups_count = count($backups);
	if ($s_age > $backups_count) $s_age = $backups_count;
/*
	if ($backups_count > 0 && $action != 'visualdiff') {
		$body .= '  <ul>' . "\n";
		foreach($backups as $age => $val) {
			$time = (isset($val['real'])) ? $val['real'] : $val['time'];
			$date = format_date($time, TRUE);
			$body .= ($age == $s_age) ?
				'   <li><em>' . $age . ' ' . $date . '</em></li>' . "\n" :
				'   <li><a href="' . get_cmd_uri('backup', $page, null, array('action'=>$action,'age'=>$age)) . '">' . $age . ' ' . $date . '</a></li>' . "\n";
		}
		$body .= '  </ul>' . "\n";
	}
	$body .= ' </li>' . "\n";
	$body .= '</ul>'  . "\n";
*/
	switch ($action){
		case 'diff':
			if (auth::check_role('safemode')) die_message( $_string['prohibit'] );
			$title = & $_backup_messages['title_backupdiff'];
			$old = ($s_age > 1) ? join('', $backups[$s_age - 1]['data']) : '';
			$cur = join('', $backups[$s_age]['data']);
			auth::is_role_page($old);
			auth::is_role_page($cur);
			$body .= plugin_backup_diff(do_diff($old, $cur));
		break;
		case 'nowdiff':
			if (auth::check_role('safemode')) die_message( $_string['prohibit'] );
			$title = & $_backup_messages['title_backupnowdiff'];
			$old = join('', $backups[$s_age]['data']);
			$cur = get_source($page, TRUE, TRUE);
			auth::is_role_page($old);
			auth::is_role_page($cur);
			$body .= plugin_backup_diff(do_diff($old, $cur));
		break;
		case 'visualdiff':
			$old = join('', $backups[$s_age]['data']);
			$cur = get_source($page, TRUE, TRUE);
			auth::is_role_page($old);
				auth::is_role_page($cur);
			// <ins> <del>タグを使う形式に変更。
			$source = do_diff($old,$cur);
			$source = plugin_backup_visualdiff($source);
			$body .= "$hr\n" . drop_submit(convert_html($source));
			$body = preg_replace('#<p>\#del(.*?)(</p>)#si', '<del class="remove_block">$1', $body);
			$body = preg_replace('#<p>\#ins(.*?)(</p>)#si', '<ins class="add_block">$1', $body);
			$body = preg_replace('#<p>\#delend(.*?)(</p>)#si', '$1</del>', $body);
			$body = preg_replace('#<p>\#insend(.*?)(</p>)#si', '$1</ins>', $body);
			// ブロック型プラグインの処理が無いよ～！
			$body = preg_replace('#&amp;del;#i', '<del class="remove_word">', $body);
			$body = preg_replace('#&amp;ins;#i', '<ins class="add_word">', $body);
			$body = preg_replace('#&amp;delend;#i', '</del>', $body);
			$body = preg_replace('#&amp;insend;#i', '</ins>', $body);
			$title = & $_backup_messages['title_backupnowdiff'];
		break;
		case 'source':
			if (auth::check_role('safemode')) die_message( $_string['prohibit'] );
			$title = & $_backup_messages['title_backupsource'];
			auth::is_role_page($backups[$s_age]['data']);
			$body .= '<pre class="sh" data-blush="diff">' . htmlsc(join('', $backups[$s_age]['data'])) .
				'</pre>' . "\n";
		break;
		default:
			if (PLUGIN_BACKUP_DISABLE_BACKUP_RENDERING) {
				die_message( T_('This feature is prohibited') );
			} else {
				$title = & $_backup_messages['title_backup'];
				auth::is_role_page($backups[$s_age]['data']);
				$body .= drop_submit(convert_html($backups[$s_age]['data']));
			}
		break;
	}

	return array('msg'=>str_replace('$2', $s_age, $title), 'body'=>$body);
}

/**
 * function plugin_backup_delete
 * Delete backup
 * @param string $page Page name.
 * @param array $ages Ages to delete.
 */
function plugin_backup_delete($page, $ages = array())
{
	global $vars,$script;
	global $_backup_messages;

	if (! _backup_file_exists($page))
		return array('msg'=>$_backup_messages['title_pagebackuplist'], 'body'=>plugin_backup_get_list($page)); // Say "is not found"

	if (! auth::check_role('role_adm_contents')) {
		_backup_delete($page);
		return array(
			'msg'  => $_backup_messages['title_backup_delete'],
			'body' => str_replace('$1', make_pagelink($page), $_backup_messages['msg_backup_deleted'])
		);
	}

	$body = '';
	$invalied = '';

	if (isset($vars['pass'])) {
		if (pkwk_login($vars['pass'])) {
			//_backup_delete($page, $ages);
			return array(
				'msg'  => $_backup_messages['title_backup_delete'],
				'body' => str_replace('$1', make_pagelink($page), $_backup_messages['msg_backup_deleted'])
			);
		} else {
			$body = '<p><strong>' . $_backup_messages['msg_invalidpass'] . '</strong></p>' . "\n";
		}
	}

	$s_page = htmlsc($page);
	$href_ages = '';
	foreach ($ages as $age) {
		$href_ages .= "\t\t".'<input type="hidden" name="selectages[]" value="' . $age . '" />' . "\n";
	}
	$body .= <<<EOD
<fieldset>
	<legend>{$_backup_messages['msg_backup_adminpass']}</legend>
	<form action="$script" method="post" class="backup_delete_form">
		<input type="hidden" name="cmd" value="backup" />
		<input type="hidden" name="page" value="$s_page" />
		<input type="hidden" name="action" value="delete" />
$href_ages
		<input type="password" name="pass" size="12" required="true" />
		<input type="submit" name="ok" value="{$_backup_messages['btn_delete']}" />
	</form>
</fieldset>
EOD;
	return	array('msg'=>$_backup_messages['title_backup_delete'], 'body'=>$body);
}

function plugin_backup_diff($str)
{
	global $_backup_messages;
	$ul = <<<EOD
<hr />
<ul>
	<li>{$_backup_messages['msg_diff_add']}</li>
	<li>{$_backup_messages['msg_diff_del']}</li>
</ul>
EOD;

	return $ul . '<pre class="sh" data-brush="diff">' . diff_style_to_css(htmlsc($str)) . '</pre>' . "\n";
}

function plugin_backup_get_list($page)
{
	global $_backup_messages;
	$r_page = rawurlencode($page);
	$s_page = htmlsc($page);
	$script = get_script_uri();
	$retval = array();
	$retval[0] = '<p><a href="'.get_cmd_uri('backup').'">'.$_backup_messages['msg_backuplist'].'</a></p>';
	$retval[0] .= <<<EOD
<form action="$script" method="post" class="backup_select_form">
	<input type="hidden" name="cmd" value="backup" />
	<input type="hidden" name="page" value="$s_page" />
	<input type="hidden" name="action" value="delete" />
	<input type="hidden" name="selectdelete" value="true" />
EOD;
	
	$retval[1] = "\n	<ol>\n";
	$retval[2] = <<<EOD
	</ol>
	<input type="submit" name="delete" value="{$_backup_messages['btn_selectdelete']}" />
EOD;
// if (! PKWK_READONLY) {
	if (! auth::check_role('readonly')) {
		$retval[2] .= '<a class="button" href="' . get_cmd_uri('backup', $page, null, array('action'=>'delete')) . '">';
		$retval[2] .= str_replace('$1', $s_page, $_backup_messages['title_backup_delete']);
		$retval[2] .= '</a>';
	}
	$retval[2] .= '</form>';
	$backups = _backup_file_exists($page) ? get_backup($page) : array();
	if (empty($backups)) {
		$retval[1] .= '   <li>' . str_replace('$1', make_pagelink($page), $_backup_messages['msg_nobackup']) . '</li>' . "\n";
		return join('', $retval);
	}

	$_anchor_from = $_anchor_to   = '';
	$safemode = auth::check_role('safemode');
	foreach ($backups as $age=>$data) {
		if (! PLUGIN_BACKUP_DISABLE_BACKUP_RENDERING) {
			$_anchor_from = '<a href="' . get_cmd_uri('backup', $page, null, array('age'=>$age)) . '">';
			$_anchor_to   = '</a>';
		}
		if (isset($data['real'])) {
			$time = $data['real'];
		}else if(isset($data['time'])){
			$time = $data['time'];
		}else{
			$time = '';
		}
		$retval[1] .= '<li>';
		if (! $safemode) {
			$retval[1] .= '<input type="checkbox" name="selectages[]" value="'.$age.'" />';
		}
		$retval[1] .= $_anchor_from . format_date($time, TRUE) . $_anchor_to;

		if (! $safemode) {
			$retval[1] .= ' <nav class="navibar" style="display:inline;"><ul>';
			$retval[1] .= '<li><a href="'. get_cmd_uri('backup', $page, null, array('action'=>'diff', 'age'=>$age)). '">' . $_backup_messages['msg_diff'] . '</a></li>';
			$retval[1] .= '<li><a href="'. get_cmd_uri('backup', $page, null, array('action'=>'nowdiff', 'age'=>$age)). '">' . $_backup_messages['msg_nowdiff'] . '</a></li>';
			$retval[1] .= '<li><a href="'. get_cmd_uri('backup', $page, null, array('action'=>'visualdiff', 'age'=>$age)). '">' . $_backup_messages['msg_visualdiff'] . '</a></li>';
			$retval[1] .= '<li><a href="'. get_cmd_uri('backup', $page, null, array('action'=>'source', 'age'=>$age)). '">' . $_backup_messages['msg_source'] . '</a></li>';
			if (PLUGIN_BACKUP_USE_ROLLBACK) {
				$retval[1] .= '<li><a href="'. get_cmd_uri('backup', $page, null, array('action'=>'rollback', 'age'=>$age)). '">' . $_backup_messages['msg_rollback'] . '</a></li>';
			}
			$retval[1] .= '</ul></nav>';
		}

		$retval[1] .= '</li>'."\n";
	}

	return join('', $retval);
}

// List for all pages
function plugin_backup_get_list_all($withfilename = FALSE)
{
	global $cantedit,$_string;

	if (auth::check_role('safemode')) die_message( $_string['prohibit'] );

	$pages = array_diff(auth::get_existpages(BACKUP_DIR, BACKUP_EXT), $cantedit);

	if (empty($pages)) {
		return '';
	} else {
		return page_list($pages, 'backup', $withfilename);
	}
}

// Plus! Extend - Diff
function plugin_backup_visualdiff($str)
{
	$str = preg_replace('/^(\x20)(.*)$/m', "\x08$2", $str);
	$str = preg_replace('/^(\-)(\x20|#\x20|\-\-\-|\-\-|\-|\+\+\+|\+\+|\+|>|>>|>>>)(.*)$/m', "\x08$2&del;$3&delend;", $str);
	$str = preg_replace('/^(\+)(\x20|#\x20|\-\-\-|\-\-|\-|\+\+\+|\+\+|\+|>|>>|>>>)(.*)$/m', "\x08$2&ins;$3&insend;", $str);
	$str = preg_replace('/^(\-)(.*)$/m', "#del\n$2\n#delend", $str);
	$str = preg_replace('/^(\+)(.*)$/m', "#ins\n$2\n#insend", $str);
	$str = preg_replace('/^(\x08)(.*)$/m', '$2', $str);
	$str = trim($str);
	return $str;
}

// Plus! Extend - Create Combobox for Backup
function plugin_backup_convert()
{
	global $vars, $script;
	global $_backup_messages;
	global $js_blocks, $plugin_backup_count;

	$page   = isset($vars['page']) ? $vars['page']   : '';
	check_readable($page, false);
	
	if (!$plugin_backup_count){
		$js_blocks[] = <<< EOD
\$('.backup_form').change(function(){
	this.submit();
});
EOD;
		$plugin_backup_count++;
	}

	// Get arguments
	$with_label = TRUE;
	$args = func_get_args();
	while (isset($args[0])) {
		switch(array_shift($args)) {
			case 'default'    : $diff_mode = 0; break;
			case 'nowdiff'    : $diff_mode = 1; break;
			case 'visualdiff' : $diff_mode = 2; break;
			case 'label'      : $with_label = TRUE;  break;
			case 'nolabel'    : $with_label = FALSE; break;
		}
	}
	
	switch($diff_mode) {
		case 2:
			$mode = 'visualdiff';
			break;
		case 1:
			$mode = 'nowdiff';
			break;
	}

	$r_page = rawurlencode($page);
	$s_page = htmlsc($page);
	$retval = array();
	$date = get_date("m/d", get_filetime($page));
	$label = ($with_label) ? '<label for="age">'.$_backup_messages['msg_version'].'</label>' : '';
		$retval[0] = <<<EOD
<form action="$script" method="get" class="backup_form">
	<input type="hidden" name="cmd" value="backup" />
	<input type="hidden" name="action" value="$mode" />
	<input type="hidden" name="page" value="$r_page" />
	$label
	<select id="age" name="age">\n
EOD;
		$retval[1] = "\n";
		$retval[2] = <<<EOD
	</select>
<span class="no-js"><input type="submit" value="{$_backup_messages['btn_jump']}" /></span>
</form>\n
EOD;

	$backups = _backup_file_exists($page) ? get_backup($page) : array();
	if (count($backups) == 0)
	{
		$retval[1] .= '<option value="" selected="selected">' .$_backup_messages['msg_arrow'] . " $date(No.1)</option>\n";
		return join('',$retval);
	}
	$maxcnt = count($backups) + 1;
	$retval[1] .= '<option value="" selected="selected">' . $_backup_messages['msg_arrow'] . " $date(No.$maxcnt)</option>\n";
	$backups = array_reverse($backups, True);
	foreach ($backups as $age=>$data) {
		if (isset($data['real'])) {
			$time = $data['real'];
		}else if(isset($data['time'])){
			$time = $data['time'];
		}else{
			break;
		}
		$date = get_date('m/d', $time);
		$retval[1] .= '<option value="'.$age.'">'.$date.' (No.'.$age.')</option>'."\n";

	}
	return join('',$retval);
}

/**
 * function plugin_backup_rollback($page, $age)
 */
function plugin_backup_rollback($page, $age)
{
	global $vars;
	global $_backup_messages;

	$body = '';
	if(PLUGIN_BACKUP_ROLLBACK_ADMINONLY) {
		$passvalid = isset($vars['pass']) ? pkwk_login($vars['pass']) : FALSE;
	} else {
		$passvalid = TRUE;
	}

	if(!$passvalid) {
		$body = '<p><strong>' . $_backup_messages['msg_invalidpass'] . '</strong></p>' . "\n";
	} else {
		$backups = _backup_file_exists($page) ? get_backup($page) : array();
		if(empty($backups) || empty($backups[$age]) || !check_postid($vars['postid']))
		{
			die();	// Do nothing
		}

		page_write($page, implode('', $backups[$vars['age']]['data']));

		return array(
			'msg'  => $_backup_messages['title_backup_rollbacked'],
			'body' => str_replace('$1', make_pagelink($page) . '(No. ' . $age . ')', $_backup_messages['msg_backup_rollbacked'])
			);
	}

	$script = get_script_uri();
	$s_page = htmlspecialchars($page);
	$body .= <<<EOD
<fieldset>
	<legend>{$_backup_messages['msg_backup_adminpass']}</legend>
	<form action="$script" method="post" class="backup_rollback_form">
		<input type="hidden" name="cmd" value="backup" />
		<input type="hidden" name="action" value="rollback" />
		<input type="hidden" name="age" value="$age" />
		<input type="hidden" name="page" value="$s_page" />
		<input type="password" name="pass" size="12" />
		<input type="submit" name="ok" value="{$_backup_messages['btn_rollback']}" />
	</form>
</legend>
EOD;
	return	array('msg'=>sprintf($_backup_messages['title_backup_rollback'], $age), 'body'=>$body);
}
?>
