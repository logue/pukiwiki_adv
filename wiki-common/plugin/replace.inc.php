<?php
//////////////////////////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: replace.inc.php,v 1.1.7 2006/11/04 18:42:00 upk Exp $
//
// ファイル名一覧の表示
// cmd=replace
use PukiWiki\Lib\Auth\Auth;
// 凍結してあるページも文字列置換の対象とする
defined('REPLACE_IGNORE_FREEZE') or define('REPLACE_IGNORE_FREEZE', TRUE);

function plugin_replace_init()
{
	global $_replace_msg;

	$messages = array(
		'_replace_msg' => array(
			'msg_input_pass'			=> T_('Please input the retrieval character string, the substitution character string, and the password for the Administrator.'),
			'msg_input_str'				=> T_('Please input the retrieval character string, the substitution character string.'),
			'msg_input_search_word'		=> T_('Retrieval character string:'),
			'msg_input_replace_word'	=> T_('Substitution character string:'),
			'btn_exec'					=> T_('Exec'),
			'msg_warn_pass'				=> T_('SECURITY ERROR:') .
										   T_('It remains as the Administrator password distributes it.') .
										   T_('Please change the password.'),
			'msg_pass'					=> T_('Password:'),
			'msg_no_pass'				=> T_('The password is wrong.'),
			'msg_no_search'				=> T_('The retrieval character string to substitute it is empty.'),
			'msg_H0_replace'			=> T_('All page character string substitution'),
			'msg_no_replaced'			=> T_('There is no substituted character string.'),
			'msg_replaced'				=> T_('The following pages were substituted.'),
			'msg_H0_replaced'			=> T_('Replaced.'),
			'msg_H0_no_data'			=> T_('No search data.'),
		)
	);
	set_plugin_messages($messages);
}

function plugin_replace_action()
{
	global $post, $cycle, $cantedit;

	$pass = isset($post['pass']) ? $post['pass'] : '__nopass__';
	$search = isset($post['search']) ? $post['search'] : NULL;
	$replace = isset($post['replace']) ? $post['replace'] : NULL;
	$notimestamp = isset($post['notimestamp']) ? TRUE : FALSE;

	if ($search != '' && ! Auth::check_role('role_adm_contents'))
		return replace_do($search,$replace,$notimestamp);

	// パスワードと検索文字列がないと置換はできない。
	if ($search == '' || !pkwk_login($pass) || $pass == 'pass') {
		$vars['cmd'] = 'read';
		return replace_adm($pass,$search);
	}

	return replace_do($search,$replace,$notimestamp);
}

function replace_do($search,$replace,$notimestamp)
{
	global $cycle, $cantedit;
	global $_replace_msg;

	// パスワードが合ってたらいよいよ置換
	$pages = Auth::get_existpages();
	$replaced_pages = array();
	foreach ($pages as $page)
	{
		if (REPLACE_IGNORE_FREEZE) {
			$editable = (
				! in_array($page, $cantedit)
			);
		} else {
			$editable = (
				! is_freeze($page) and
				! in_array($page, $cantedit)
                	);
		}
		if ($editable) {
			// パスワード一致
			$postdata = '';
			$postdata_old = get_source($page);
			foreach ($postdata_old as $line)
			{
				// キーワードの置換
				$line = str_replace($search,$replace,$line);
				$postdata .= $line;
			}
			if ($postdata != join('',$postdata_old)) {
				$cycle = 0;
				set_time_limit(30);
				page_write($page,$postdata,$notimestamp);
				$replaced_pages[] = '<li><a href="'.get_page_uri($page).'">'.htmlsc($page).'</a></li>';
			}
		}
	}
	$vars['cmd'] = 'read';
	if ( count($replaced_pages) == 0 ) {
		return array(
			'msg'  => $_replace_msg['msg_H0_no_data'],
			'body' => '<p>' . $_replace_msg['msg_no_replaced'] . '</p>'
		);
	}
	return array(
		'msg'  => $_replace_msg['msg_H0_replaced'],
		'body' => '<p>' . $_replace_msg['msg_replaced'] . '</p>' . "\n" . '<ul>' . join("\n", $replaced_pages) . '</ul>'
	);
}

// 置換文字列入力画面
function replace_adm($pass,$search)
{
	global $_replace_msg;
	global $_button;

	$body = '';

	if (! Auth::check_role('role_adm_contents')) {
		$msg = $_replace_msg['msg_input_str'];
		$body_pass = "<br />\n";
	} else {
		$msg = $_replace_msg['msg_input_pass'];
		$body_pass = '<label for="pass">'.$_replace_msg['msg_pass'].'</label><input type="password" name="pass" size="12" id="pass" /><br />';
		if ($pass == 'pass') {
			$body .= '<p><strong>'.$_replace_msg['msg_warn_pass'].'</strong></p>'. "\n";
		} elseif ($pass != '__nopass__') {
			$body .= '<p><strong>'.$_replace_msg['msg_no_pass'].'</strong></p>'."\n";
		}
	}

	if ($search === '') {
		$body .= '<p><strong>'.$_replace_msg['msg_no_search']."</strong></p>\n";
	}
	$script = get_script_uri();
	$body .= <<<EOD
<fieldset>
	<legend>$msg</legend>
	<form action="$script" method="post" class="replace_form">
		<input type="hidden" name="cmd" value="replace" />
		<label for="replace_search">{$_replace_msg['msg_input_search_word']}</label>
		<input type="text" name="search" id="replace_search" size="24" /><br />
		<label for="replace_replace">{$_replace_msg['msg_input_replace_word']}</label>
		<input type="text" name="replace" id="replace_replace" size="24" /><br />
		$body_pass
		<input type="checkbox" name="notimestamp" id="replace_notimestamp" />
		<label for="replace_notimestamp">{$_button['notchangetimestamp']}</label><br />
		<input type="submit" name="ok" value="{$_replace_msg['btn_exec']}" />
	</form>
</fieldset>

EOD;

	return array('msg'=>$_replace_msg['msg_H0_replace'],'body'=>$body);
}
/* End of file replace.inc.php */
/* Location: ./wiki-common/plugin/replace.inc.php */