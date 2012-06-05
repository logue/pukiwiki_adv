<?php
/**
 * adm_auth_wkgrp Plugin.
 *
 * @copyright   Copyright &copy; 2009, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @version     $Id: adm_auth_wkgrp.inc.php,v 0.1.2 2011/04/03 09:42:00 Logue Exp $
 *
 */

defined('PLUGIN_ADM_AUTH_WKGRP_USE_WRITE_FUNC') or define('PLUGIN_ADM_AUTH_WKGRP_USE_WRITE_FUNC', false);
// 構成定義ファイル
define('CONFIG_AUTH_WKGRP','auth/auth_wkgrp');

function plugin_adm_auth_wkgrp_init()
{
	$msg = array(
		'_adm_auth_wkgrp_msg' => array(
			'msg_title'			=> T_('User registration management'),
			'head_title'		=> T_('User registration(auth_wkgrp)'),
			'btn_gen'			=> T_('Generation'),
			'btn_auth_wkgrp'	=> T_('User registration menu'),
			'msg_head_page'		=> T_('Page information'),
			'msg_head_file'		=> T_('Definition file information'),
			'msg_head_update'	=> T_('Updated DateTime'),
			'msg_head_gen'		=> T_('Generation DateTime'),
			'msg_check'			=> T_('Check'),
			'msg_view'			=> T_('View'),
			'msg_import'		=> T_('Import'),
			'msg_ok'			=> T_('The %s file was generated.'),
			'msg_ok_import'		=> T_('Data was taken into %s.'),
			'msg_gen'			=> T_('It is necessary to generate it.'),
			'err_authority'		=> T_('The manager authority is necessary.'),
			'err_not_use'		=> T_('The generation function is invalid.'),
			'err_already'		=> T_('Because the page already exists, processing is discontinued.'),
			'msg_chk_1'			=> T_('The following images are the one (reading auth_wkgrp, and conversion into page image).'),
		)
	);
	set_plugin_messages($msg);
}

function plugin_adm_auth_wkgrp_convert()
{
	global $_adm_auth_wkgrp_msg, $_LANG;

	if (auth::check_role('role_adm'))  return '';
	if (! PLUGIN_ADM_AUTH_WKGRP_USE_WRITE_FUNC) return '';

	$config_page_name = ':config/'.CONFIG_AUTH_WKGRP;
	$msg = '';
	
	$script      = get_script_uri();
	$cmd_view    = get_page_uri($config_page_name);
	$cmd_edit    = get_cmd_uri('edit',$config_page_name);
	$cmd_guiedit = get_cmd_uri('guiedit',$config_page_name);
	$cmd_check   = get_cmd_uri('adm_auth_wkgrp','','',array('pcmd'=>'check'));
	$cmd_import  = get_cmd_uri('adm_auth_wkgrp','','',array('pcmd'=>'import'));

	$filetime_auth_wkgrp  = filemtime(PKWK_AUTH_WKGRP_FILE);
	$date_auth_wkgrp  = format_date($filetime_auth_wkgrp);

	if (is_page($config_page_name)) {
		$filetime_config_page = get_filetime($config_page_name);
		$date_config_page = format_date($filetime_config_page);
		$guide_msg = ($filetime_config_page > $filetime_auth_wkgrp) ? '<strong>'.$_adm_auth_wkgrp_msg['msg_gen'].'</strong>' : '';

		$link_page = <<<EOD
[<a href="$cmd_edit">{$_LANG['skin']['edit']}</a>]
[<a href="$cmd_guiedit">{$_LANG['skin']['guiedit']}</a>]

EOD;
		$link_file = <<<EOD
<form action="$script" method="post" class="adm_auth_wkgrp_form">
	<input type="hidden" name="cmd" value="adm_auth_wkgrp" />
	<input type="hidden" name="pcmd" value="gen" />
	<input type="submit" value="{$_adm_auth_wkgrp_msg['btn_gen']}" />
	$guide_msg
</form>

EOD;

	} else {
		$date_config_page = 'N/A';
		$link_page = '[<a href="'.$cmd_import.'">'.$_adm_auth_wkgrp_msg['msg_import'].'</a>]';
		$link_file = '';
	}


	$rc = <<<EOD
<div class="adm_auth_wkgrp">
	<fieldset>
		<legend>{$_adm_auth_wkgrp_msg['head_title']}</legend>
		<table class="style_table">
			<thead>
				<tr>
					<th class="style_th">
						{$_adm_auth_wkgrp_msg['msg_head_page']}
						(<a href="$cmd_view">{$_adm_auth_wkgrp_msg['msg_view']}</a>)
					</td>
					<th class="style_th">
						{$_adm_auth_wkgrp_msg['msg_head_file']}
						(<a href="$cmd_check">{$_adm_auth_wkgrp_msg['msg_check']}</a>)
					</td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="style_td">{$_adm_auth_wkgrp_msg['msg_head_update']}: $date_config_page</td>
					<td class="style_td">{$_adm_auth_wkgrp_msg['msg_head_gen']}: $date_auth_wkgrp</td>
				</tr>
				<tr>
					<td class="style_td">$link_page</td>
					<td class="style_td">$link_file</td>
				</tr>
			</tbody>
		</table>
	</fieldset>
</div>
EOD;

	return $rc;
}

function plugin_adm_auth_wkgrp_action()
{
	global $vars,$_adm_auth_wkgrp_msg;

	$retval = array();
	$retval['msg'] = $_adm_auth_wkgrp_msg['msg_title'];

	// 権限で稼動しないのか？ 機能が有効になっていないのか？を隠蔽するため、この順序で良い
	if (auth::check_role('role_adm'))  {
		$retval['body'] = '<p>'.$_adm_auth_wkgrp_msg['err_authority'].'</p>';
		return $retval;
	}

	if (! PLUGIN_ADM_AUTH_WKGRP_USE_WRITE_FUNC) {
		$retval['body'] = '<p>'.$_adm_auth_wkgrp_msg['err_not_use'].'</p>';
		return $retval;
	}

	$pcmd = empty($vars['pcmd']) ? '' : $vars['pcmd'];

	switch ($pcmd) {
	case 'gen':
		$wkgrp_user = adm_auth_wkgrp_get_page();
		adm_auth_wkgrp_put_file($wkgrp_user);
		$retval['body'] = '<p>'.sprintf($_adm_auth_wkgrp_msg['msg_ok'], '<var>'.PKWK_AUTH_WKGRP_FILE.'</var>').'</p>';
		break;
	case 'check':
		$retval['body'] = adm_auth_wkgrp_check();
		break;
	case 'import':
		$retval['body'] = adm_auth_wkgrp_import();
		break;
	default:
		$retval['body'] = plugin_adm_auth_wkgrp_convert();
	}

	return $retval;
}

function adm_auth_wkgrp_get_page()
{
	global $auth_api, $auth_wkgrp_user;

	$config = new Config(CONFIG_AUTH_WKGRP);
	$config->read();

	$wkgrp_user = array();

	foreach($auth_api as $name=>$val) {
		// if (! $val['use']) continue;
		// if ($name === 'plus' || $name === 'remoteip') continue;
		$temp = $config->get($name);

		foreach($temp as $val) {
			$wkgrp_user[$name][$val[0]]['role'] = is_numeric($val[1]) ? adm_auth_wkgrp_role2define($val[1]) : $val[1];
			$wkgrp_user[$name][$val[0]]['displayname'] = $val[2];
			$wkgrp_user[$name][$val[0]]['group'] = $val[3];
			$wkgrp_user[$name][$val[0]]['mypage'] = $val[4];
			$wkgrp_user[$name][$val[0]]['home'] = $val[5];
		}
	}

	unset($config);
	return $wkgrp_user;
}

function adm_auth_wkgrp_put_file($wkgrp_user)
{
	if (!is_array($wkgrp_user)) return;

	$file = PKWK_AUTH_WKGRP_FILE;
	$fp = fopen($file,'w');
	@flock($fp, LOCK_EX);
	fputs($fp, "<?php\n\$auth_wkgrp_user = array(\n");
	// fputs($fp, "\t// ex. 'user_name' => array('role'=>ROLE_ADM, 'displayname'=>'ななし','group'=>''),\n");

	foreach($wkgrp_user as $name=>$api_val) {
                fputs($fp, "\t'$name'\t=> array(\n");
		foreach($api_val as $id=>$val) {
			fputs($fp,"\t\t'$id'\t=> array('role'=>{$val['role']}, 'displayname'=>'{$val['displayname']}', 'group'=>'{$val['group']}', 'mypage'=>'{$val['mypage']}', 'home'=>'{$val['home']}'),\n");
		}
		fputs($fp, "\t),\n");
	}

	fputs($fp, ");\n?>\n");
	@flock($fp, LOCK_UN);
	@fclose($fp);

	adm_auth_wkgrp_touch_file2page();
}

function adm_auth_wkgrp_touch_file2page()
{
	$filetime_auth_wkgrp  = filemtime(PKWK_AUTH_WKGRP_FILE);
	$config_page_name = ':config/'.CONFIG_AUTH_WKGRP;
	$config_page_filename = get_filename($config_page_name);
	pkwk_touch_file($config_page_filename, $filetime_auth_wkgrp);
}

function adm_auth_wkgrp_file2page()
{
	global $auth_wkgrp_user;
	$field = array('role','displayname','group','mypage','home');

	$rc = '';
	foreach ($auth_wkgrp_user as $type=>$val) {
		$ctr = count($val);
		if ($ctr == 0) continue;

		$rc .= '* '.$type."\n\n";
		// header
		$rc .= '|id|';
		foreach ($field as $name) {
			$rc .= $name.'|';
		}
		$rc .= "h\n";
		// data
		foreach ($val as $id=>$data) {
			$rc .= '|'.$id.'|';
			foreach ($field as $name) {
				if (empty($data[$name])) {
					$rc .= '';
				} else {
					$rc .= ($name === 'role') ? adm_auth_wkgrp_role2define($data[$name]) : $data[$name];
				}
				$rc .= '|';
			}
			$rc .= "\n";
		}
		$rc .= "\n";
	}
	return $rc;
}

function adm_auth_wkgrp_check()
{
	global $_adm_auth_wkgrp_msg;
	$msg = $_adm_auth_wkgrp_msg['msg_chk_1'].adm_auth_wkgrp_file2page();
	return convert_html($msg).adm_auth_wkgrp_add_btn();
}

function adm_auth_wkgrp_import()
{
	global $_adm_auth_wkgrp_msg;

	$config_page_name = ':config/'.CONFIG_AUTH_WKGRP;
	// 処理中に誰かがページを作成した場合にしか発生しないはず
	if (is_page($config_page_name)) return $_adm_auth_wkgrp_msg['err_already'];

	$data = "#check_role(2)\n".adm_auth_wkgrp_file2page();
	// このイメージをページに出力
	page_write($config_page_name, $data);
	// php ファイルのタイムスタンプとページを一致させる
	adm_auth_wkgrp_touch_file2page();
	return sprintf($_adm_auth_wkgrp_msg['msg_ok_import'], '<var>'.$config_page_name.'</var>');
}

function adm_auth_wkgrp_add_btn()
{
	global $script, $_adm_auth_wkgrp_msg;

	return <<<EOD
<form action="$script" method="post" class="form_adm_auth_wkgrp_add">
	<input type="hidden" name="cmd" value="adm_auth_wkgrp" />
	<input type="submit" value="{$_adm_auth_wkgrp_msg['btn_auth_wkgrp']}" />
</form>

EOD;
}

function adm_auth_wkgrp_role2define($role)
{
	static $array_role = array(
		0 => 'ROLE_GUEST',
		1 => 'ROLE_FORCE',
		2 => 'ROLE_ADM',
		3 => 'ROLE_ADM_CONTENTS',
		4 => 'ROLE_ENROLLEE',
		5 => 'ROLE_AUTH'
	);
	return (isset($array_role[$role])) ? $array_role[$role] : $role;
}

/* End of file adm_auth_wkgrp.inc.php */
/* Location: ./wiki-common/plugin/adm_auth_wkgrp.inc.php */