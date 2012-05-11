<?php
// $Id: template.inc.php,v 1.22.7 2012/05/11 18:14:00 Logue Exp $
//
// Load template plugin

define('MAX_LEN', 60);

function plugin_template_init()
{
	$msg = array(
		'_template_msg' => array(
			'title_edit'				=> T_('Edit of  $1'),
			'msg_template_start'		=> T_('Start:'),
			'msg_template_end'			=> T_('End:'),
			'msg_template_page'			=> T_('$1/copy'),
			'msg_template_refer'		=> T_('Page:'),
			'msg_template_force'		=> T_('Edit with a page name which already exists'),
			'err_template_already'		=> T_(' $1 already exists.'),
			'err_template_invalid'		=> T_(' $1 is not a valid page name.'),
			'btn_template_create'		=> T_('Create'),
			'title_template'			=> T_('create a new page, using  $1 as a template.'),
			'title_page_notfound'		=> T_(' $1 was not found.'),
			'msg_page_notfound'			=> T_('cannot display the page source.'),
			'msg_template_prohibited'	=> T_('Prohibited')
		)
	);
	set_plugin_messages($msg);
}

function plugin_template_action()
{
	global $script, $vars;
	global $_template_msg;

	if (auth::check_role('safemode') || auth::check_role('readonly')) die_message($_template_msg['msg_template_prohibited']);
	if (! isset($vars['refer']) || ! is_page($vars['refer']))
		return FALSE;

	if (! is_page($vars['refer']) || ! check_readable($vars['refer'], false, false))
		return array(
			'msg' => $_template_msg['title_page_notfound'],
			'body' => $_template_msg['msg_page_notfound']
		);

	$lines = get_source($vars['refer']);
	auth::is_role_page($lines);

	// Remove '#freeze'
	if (! empty($lines) && strtolower(rtrim($lines[0])) == '#freeze')
		array_shift($lines);

	$begin = (isset($vars['begin']) && is_numeric($vars['begin'])) ? $vars['begin'] : 0;
	$end   = (isset($vars['end'])   && is_numeric($vars['end']))   ? $vars['end'] : count($lines) - 1;
	if ($begin > $end) {
		$temp  = $begin;
		$begin = $end;
		$end   = $temp;
	}
	$page    = isset($vars['page']) ? $vars['page'] : '';
	$is_page = is_page($page);

	// edit
	if ($is_pagename = is_pagename($page) && (! $is_page || ! empty($vars['force']))) {
		$postdata       = join('', array_splice($lines, $begin, $end - $begin + 1));
		$retvar['msg']  = $_template_msg['title_edit'];
		$retvar['body'] = edit_form($vars['page'], $postdata);
		$vars['refer']  = $vars['page'];
		return $retvar;
	}
	$begin_select = $end_select = '';
	for ($i = 0; $i < count($lines); $i++) {
		$line = htmlsc(mb_strimwidth($lines[$i], 0, MAX_LEN, '...'));

		$tag = ($i == $begin) ? ' selected="selected"' : '';
		$begin_select .= "<option value=\"$i\"$tag>$line</option>\n";

		$tag = ($i == $end) ? ' selected="selected"' : '';
		$end_select .= "<option value=\"$i\"$tag>$line</option>\n";
	}

	$_page = htmlsc($page);
	$msg = $tag = '';
	if ($is_page) {
		$msg = $_template_msg['err_template_already'];
		$tag = '<input type="checkbox" name="force" value="1" id="_p_template_force" /><label for="_p_template_force">'.$_template_msg['msg_template_force'].'</label>';
	} else if ($page != '' && ! $is_pagename) {
		$msg = str_replace('$1', $_page, $_template_msg['err_template_invalid']);
	}

	$s_refer = htmlsc($vars['refer']);
	$s_page  = ($page == '') ? str_replace('$1', $s_refer, $_template_msg['msg_template_page']) : $_page;
	$ret     = <<<EOD
<form action="$script" method="post" class="template_form">
	<input type="hidden" name="cmd" value="template" />
	<input type="hidden" name="refer"  value="$s_refer" />
	<div>
		<dl>
			<dt><label for="_p_template_begin">{$_template_msg['msg_template_start']}</label></dt>
			<dd><select name="begin" size="10" id="_p_template_begin">$begin_select</select></dd>
			<dt><labbel for="_p_template_end">{$_template_msg['msg_template_end']}</label></dt>
			<dd><select name="end"   size="10" id="_p_template_end">$end_select</select></dd>
		</dl>
		<label for="_p_template_refer">{$_template_msg['msg_template_refer']}</label>
		<input type="text" name="page" id="_p_template_refer" value="$s_page" />
		<input type="submit" name="submit" value="{$_template_msg['btn_template_create']}" />
		$tag
	</div>
</form>
EOD;

	$retvar['msg']  = ($msg == '') ? $_template_msg['title_template'] : $msg;
	$retvar['body'] = $ret;

	return $retvar;
}
?>
