<?php
//
//	guiedit - PukiWiki Plugin
//
//	$Id: guiedit.inc.php,v 1.64.4 2010/09/03 16:57:00 Logue Exp $
//
//	License:
//	  GNU General Public License Version 2 or later (GPL)
//	  http://www.gnu.org/licenses/gpl.html
//
//	Copyright (C) 2006-2009 garand
//	PukiWiki : Copyright (C) 2001-2006 PukiWiki Developers Team
//	FCKeditor : Copyright (C) 2003-2008 Frederico Caldeira Knabben
//	PukiWiki Plus! : Copyright (C) 2009 Katsumi Saito
//	PukiWiki Advance : Copyright (C) 2010 PukiWiki Advance Developers Team

defined('GUIEDIT_CONF_PATH')  or define('GUIEDIT_CONF_PATH',  'guiedit/');
defined('GUIEDIT_FULL_SIZE') or define('GUIEDIT_FULL_SIZE', 0);

define('PLUGIN_GUIEDIT_FREEZE_REGEX', '/^(?:#freeze(?!\w)\s*)+/im');

use PukiWiki\Lib\File\WikiFile;
use PukiWiki\Lib\File\FileFactory;
//	コマンド型プラグイン
function plugin_guiedit_action()
{
	// global $vars, $_title_edit, $load_template_func;
	global $vars, $load_template_func;
	global $menubar, $sidebar, $topicpath;

	// if (PKWK_READONLY) die_message( sprintf($_string['error_prohibit'],'PKWK_READONLY') );
	if (auth::check_role('readonly')) die_message(  sprintf($_string['error_prohibit'],'PKWK_READONLY') );

	if (PKWK_READONLY == ROLE_AUTH && auth::get_role_level() > ROLE_AUTH) {
		die_message(  sprintf($_string['error_prohibit'],'PKWK_READONLY') );
	}

    $page = isset($vars['page']) ? $vars['page'] : '';

	$wiki = new WikiFile($page);

	if (! $wiki->is_editable()){
		die_message('You have not permission to edit this page.');
	}

	if (!is_page($page) && auth::is_check_role(PKWK_CREATE_PAGE)) {
		die_message( sprintf($_string['error_prohibit'],'PKWK_CREATE_PAGE') );
	}

	global $guiedit_use_fck;
	$guiedit_use_fck = isset($vars['text']) ? false : true;

	if ($guiedit_use_fck) {
		global $guiedit_pkwk_root;
		$guiedit_pkwk_root = get_baseuri('abs');
	}

	if (GUIEDIT_FULL_SIZE) {
		$menubar = $sidebar = '';
		$topicpath = false;
	}

	if (isset($vars['edit'])) {
		return plugin_guiedit_edit_data($page);
	} else if ($load_template_func && isset($vars['template'])) {
		return plugin_guiedit_template();
	} else if (isset($vars['preview'])) {
		return plugin_guiedit_preview();
	} else if (isset($vars['write'])) {
		return plugin_guiedit_write();
	} else if (isset($vars['cancel'])) {
		return plugin_guiedit_cancel();
	}

	$source = $wiki->source();
	$postdata = $vars['original'] = join('', $source);

	if (isset($vars['text'])) {
		if (! empty($vars['id'])) {
			exist_plugin('edit');
			$postdata = plugin_edit_parts($vars['id'], $source);
			if ($postdata === FALSE) {
				unset($vars['id']);
				$postdata = $vars['original'];
			}
		}
		if ($postdata == '') $postdata = $wiki->auto_template();
	}

	return array('msg'=>'GUI Edit', 'body'=>plugin_guiedit_edit_form($page, $postdata));
}

function plugin_guiedit_send_ajax($postdata){
	//	文字コードを UTF-8 に変換
	//$postdata = mb_convert_encoding($postdata, 'UTF-8', SOURCE_ENCODING);

	//	出力
	pkwk_common_headers();
	header("Content-Type: application/json; charset=".CONTENT_CHARSET);
	echo json_encode(
		array(
			'msg'		=> $postdata,
			'taketime'	=> sprintf('%01.03f', getmicrotime() - MUTIME)
		)
	);
	exit;
}

//	編集するデータ
function plugin_guiedit_edit_data($page)
{
	global $vars;

	$source = FileFactory::Wiki($vars['page'])->source();
	$postdata = $vars['original'] = join('', $source);
	if (! empty($vars['id'])) {
		exist_plugin('edit');
		$postdata = plugin_edit_parts($vars['id'], $source);
		if ($postdata === FALSE) {
			unset($vars['id']);
			$postdata = $vars['original'];
		}
	}
	if ($postdata == '') $postdata = FileFactory::Wiki($page)->auto_template();

	//	構文の変換
	$inc = include_once(GUIEDIT_CONF_PATH . 'wiki2xhtml.php');
	if ($inc === false){
		die_message('guiedit.inc.php : Cannot load Wiki2XHTML Libraly.');
		$postdata = 'ERROR!';
	}else{
		$postdata = guiedit_convert_html($postdata);
	}

	plugin_guiedit_send_ajax($postdata);
}

//	テンプレート
function plugin_guiedit_template()
{
	global $vars;
	global $guiedit_use_fck;

	//	テンプレートを取得
	$wiki = new WikiFile($vars['template_page']);
	if ($wiki->has()) {
		$vars['msg'] = join('', $wiki->source());
		$vars['msg'] = preg_replace('/^(\*{1,3}.*)\[#[A-Za-z][\w-]+\](.*)$/m', '$1$2', $vars['msg']);
		$vars['msg'] = preg_replace(PLUGIN_GUIEDIT_FREEZE_REGEX, '', $vars['msg']);
	}
	else if ($guiedit_use_fck) {
		exit;
	}

	if (!$guiedit_use_fck) {
		return plugin_guiedit_preview();
	}

	//	構文の変換
	$inc = include_once(GUIEDIT_CONF_PATH . 'wiki2xhtml.php');
	if ($inc === false){
		die_message('guiedit.inc.php : Cannot load Wiki2XHTML Libraly.');
		$postdata = 'ERROR!';
	}else{
		$postdata = guiedit_convert_html($vars['msg']);
	}
	plugin_guiedit_send_ajax($postdata);
}

//	プレビュー
function plugin_guiedit_preview()
{
	global $vars;
	// global $_title_preview, $_msg_preview, $_msg_preview_delete;
	global $note_hr, $foot_explain;
	global $guiedit_use_fck;

	//FIXME
	$_msg_preview  = _('To confirm the changes, click the button at the bottom of the page');
	$_msg_preview_delete = _('(The contents of the page are empty. Updating deletes this page.)');

	if ($guiedit_use_fck) {
		//	構文の変換


		//	構文の変換
		$inc = include_once(GUIEDIT_CONF_PATH . 'xhtml2wiki.php');
		if ($inc === false){
			die_message('guiedit.inc.php : Cannot load XHTML2Wiki Libraly.');
			$postdata = 'ERROR!';
		}else{
			$postdata = xhtml2wiki($vars['msg']);
		}
	}

	if ($postdata) {
		$postdata = make_str_rules($postdata);
		$postdata = explode("\n", $postdata);
		$postdata = drop_submit(convert_html($postdata));
	}

	//	テキスト編集の場合
	if (!$guiedit_use_fck) {
		$body = $_msg_preview . '<br />' . "\n";
		if ($postdata == '') {
			$body .= '<strong>' . $_msg_preview_delete . '</strong><br />' . "\n";
		}
		else {
			$body .= '<br />' . "\n";
			$body .= '<div id="preview">' . $postdata . '</div>' . "\n";
		}
		$body .= plugin_guiedit_edit_form($vars['page'], $vars['msg'], $vars['digest'], FALSE);

		return array('msg'=>$_title['preview'], 'body'=>$body);
	}

	//	注釈
	ksort($foot_explain, SORT_NUMERIC);
	$postdata .= ! empty($foot_explain) ? $note_hr . join("\n", $foot_explain) : '';

	//	通常の編集フォーム
	if (DEBUG) {
		global $hr;
		$postdata .= $hr . edit_form($vars['page'], $vars['msg']);
	}

	plugin_guiedit_send_ajax($postdata);
}

//	ページの更新
function plugin_guiedit_write()
{
	global $vars;
	global $guiedit_use_fck;

	if ($guiedit_use_fck) {
		$inc = include_once(GUIEDIT_CONF_PATH . 'xhtml2wiki.php');
		if ($inc === false){
			die_message('guiedit.inc.php : Cannot load XHTML2Wiki Libraly.');
		}else{
			$vars['msg'] = xhtml2wiki($vars['msg']);

		}
	}

	if (isset($vars['id']) && $vars['id']) {
		$source = preg_split('/([^\n]*\n)/', $vars['original'], -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		if (plugin_guiedit_parts($vars['id'], $source, $vars['msg']) !== FALSE) {
			$vars['msg'] = join('', $source);
		}
		else {
			$vars['msg'] = rtrim($vars['original']) . "\n\n" . $vars['msg'];
		}
	}

	//	書き込み
	exist_plugin('edit');
	return plugin_edit_write();
}

//	キャンセル
function plugin_guiedit_cancel()
{
	global $vars;

	$location = 'Location: ' . get_script_uri() . '?' . rawurlencode($vars['page']);
	if (!empty($vars['id'])) {
		$location .= '#' . $vars['id'];
	}

	pkwk_headers_sent();
	header($location);
	exit;
}

//	編集フォームの作成
function plugin_guiedit_edit_form($page, $postdata, $digest = FALSE, $b_template = TRUE)
{
	global $vars;
	global $load_template_func, $whatsnew;
	global $_button;
	global $notimeupdate;
	global $js_tags,$link_tags,$js_blocks;
	global $guiedit_use_fck;

	$script = get_script_uri();

	// Newly generate $digest or not
	if ($digest === FALSE) $digest = md5(get_source($page, TRUE, TRUE));

	$s_id  = isset($vars['id']) ? htmlspecialchars($vars['id']) : '';

	if (!$guiedit_use_fck) {
		$body = edit_form($page, $postdata, $digest, $b_template);

		$pattern = "/(<input\s+type=\"hidden\"\s+name=\"cmd\"\s+value=\")edit(\"\s*\/?>)/";
		$replace = "$1guiedit$2\n" . '  <input type="hidden" name="id"     value="' . $s_id . '" />'
				 . '  <input type="hidden" name="text"     value="1" />';
		$body = preg_replace($pattern, $replace, $body);

		return $body;
	}

	require_once(GUIEDIT_CONF_PATH . 'guiedit.ini.php');

	//	フォームの値の設定
	$s_digest    = htmlspecialchars($digest);
	$s_page      = htmlspecialchars($page);
	$s_original  = htmlspecialchars($vars['original']);
	$s_ticket    = md5(MUTIME);
	if (function_exists('pkwk_session_start') && pkwk_session_start() != 0) {
		// BugTrack/95 fix Problem: browser RSS request with session
		$_SESSION[$s_ticket] = md5(get_ticket() . $digest);
		$_SESSION['origin' . $s_ticket] = md5(get_ticket() . str_replace("\r", '', $s_original));
	}

	// テンプレート
	$template = '';
	if($load_template_func) {
		global $guiedit_non_list;
		$pages  = array();
		foreach(get_existpages() as $_page) {
			if ($_page == $whatsnew || check_non_list($_page))
				continue;
			foreach($guiedit_non_list as $key) {
				$pos = strpos($_page . '/', $key . '/');
				if ($pos !== FALSE && $pos == 0)
					continue 2;
			}
			$_s_page = htmlspecialchars($_page);
			$pages[$_page] = '		<option value="' . $_s_page . '">' . $_s_page . '</option>';
		}
		ksort($pages);
		$s_pages  = join("\n", $pages);
		$template = <<<EOD
<select name="template_page">
	<option value="">-- {$_button['template']} --</option>
$s_pages
</select>
<br />
EOD;
	}

	// チェックボックス「タイムスタンプを変更しない」
	$add_notimestamp = '';
	if ($notimeupdate != 0) {
		$checked_time = isset($vars['notimestamp']) ? ' checked="checked"' : '';
		// if ($notimeupdate == 2) {
		if ($notimeupdate == 2 && auth::check_role('role_adm_contents')) {
			$add_notimestamp = '   ' .
				'<input type="password" name="pass" size="12" />' . "\n";
		}
		$add_notimestamp = '<input type="checkbox" name="notimestamp" ' .
			'id="_edit_form_notimestamp" value="true"' . $checked_time . ' />' . "\n" .
			'   ' . '<label for="_edit_form_notimestamp"><span class="small">' .
			$_button['notchangetimestamp']. '</span></label>' . "\n" .
			$add_notimestamp .
			'&nbsp;';
	}

	//	フォーム
	$body = <<<EOD
<div id="guiedit">
	<form id="guiedit_form" action="$script" method="post" style="margin-bottom:0px;">
	$template
		<input type="hidden" name="cmd"    value="guiedit" />
		<input type="hidden" name="page"   value="$s_page" />
		<input type="hidden" name="digest" value="$s_digest" />
		<input type="hidden" name="ticket" value="$s_ticket" />
		<input type="hidden" name="id"     value="$s_id" />
		<textarea name="original" rows="1" cols="1" style="display:none">$s_original</textarea>
		<textarea name="msg" rows="1" cols="1" style="display:none"></textarea>
		<div style="float:left;">
		<button type="submit" name="write"   accesskey="s">{$_button['update']}</button>
		<button type="button" name="preview" accesskey="p">{$_button['preview']}</button>
		$add_notimestamp
		</div>
	</form>
	<form action="$script" method="post" style="margin-top:0px;">
		<input type="hidden" name="cmd"    value="guiedit" />
		<input type="hidden" name="page"   value="$s_page" />
		<input type="submit" name="cancel" value="{$_button['cancel']}" accesskey="c" />
	</form>
</div>
EOD;
	$js_tags[] = array('type'=>'text/javascript', 'src'=>SKIN_URI.'js/plugin/guiedit/ckeditor/ckeditor.js');
	$js_tags[] = array('type'=>'text/javascript', 'src'=>SKIN_URI.'js/plugin/guiedit/ckeditor/adapters/jquery.js');
	$js_tags[] = array('type'=>'text/javascript', 'src'=>SKIN_URI.'js/plugin/guiedit/ckeditor4pukiwiki.js');
	return $body;
}

// ソースの一部を抽出/置換
function plugin_guiedit_parts($id, & $source, $postdata = '')
{
	$postdata = rtrim($postdata)."\n";
	$heads = preg_grep('/^\*{1,3}.+$/', $source);
	$heads[count($source)] = '';

	while (list($start, $line) = each($heads)) {
		if (preg_match("/\[#$id\]/", $line)) {
			list($end, $line) = each($heads);
			return join('', array_splice($source, $start, $end - $start, $postdata));
		}
	}
	return FALSE;
}
/* End of file guiedit.inc.php */
/* Location: ./wiki-common/plugin/guiedit.inc.php */
