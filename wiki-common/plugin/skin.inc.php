<?php
/**
 * skin plugin
 *
 * @copyright   Copyright &copy; 2009-2010, Katsumi Saito <jo1upk@users.sourceforge.net>
 * @version	 $Id: skin.inc.php,v 2.2 2011/02/05 12:41:00 Logue Exp $
 *
 */
defined('PLUGIN_SKIN_USE')	or define('PLUGIN_SKIN_USE', 1);		// 0:not use, 1:use
// 有効日数を定義(0 は、ブラウザ終了時消滅)
defined('PLUGIN_SKIN_EXPIRE') or define('PLUGIN_SKIN_EXPIRE', 0);	// Effective days are defined. (0: Disappearance when a browser ends)
use PukiWiki\Lib\Auth\Auth;
function plugin_skin_init()
{
	$msg = array(
		'_skin_msg' => array(
			'msg_change'		=> _('Change Theme'),
			'msg_config_update'	=> _('Update of config page'),						// config page の更新
			'msg_config_list'	=> _('Theme list'),
			'msg_update_end'	=> _('The update work ended.'),						// 更新作業が終わりました。
			'adm_titme'			=> _('Management of skin theme'),					// スキン・テーマの管理
			'adm_config_name'	=> _('Management ledger'),						// 管理台帳
			'adm_config_update'	=> _('Update of management ledger'),					// 管理台帳の更新
			'adm_present_theme'	=> _('Present theme'),							// 現在のテーマ
			'adm_amnagement_op'	=> _('Management operation'),						// 管理操作
			'field_theme_name'	=> _('Theme Name'),
			'field_use'			=> _('Use'),
			'field_display_name'	=> _('Display Name'),
			'field_color'		=> _('Color'),
			'field_memo'		=> _('Memo'),
			'err_not_use'		=> _('This plugin function cannot be used.'),
			// The update work is necessary only that the skin(theme) is set up.
			'err_not_update'	=> _('Only Webmaster for World Wide Web Site can do the update work.'), // 更新作業は、サイト管理者のみが行えます。
		)
	);
	set_plugin_messages($msg);
}

function plugin_skin_convert()
{
	global $vars, $skin_file, $_skin_msg;

	if (!PLUGIN_SKIN_USE) return $_skin_msg['err_not_use'];
	if (func_num_args() == 0) return skin_make_filelist();

	$argv = func_get_args();
	$parm = skin_set_parm($argv);

	if ($parm['cmd'] == 'admin') return skin_menu_admin();

	if (count($parm['list']) > 1) {
		$skin_list = array();
		foreach($parm['list'] as $x) {
			list($skin,$name) = $x;
			$skin_list[$skin] = $name;
		}
		return skin_make_filelist($skin_list);
	}

	$val = explode('.', $parm['list'][0][0]);
	$val[1] = (empty($val[1])) ? $val[0] : $val[1];

	$skin_file = add_skindir($val[0]);

	if (! file_exists($skin_file) || ! is_readable($skin_file)) {
		die_message($skin_file.' (skin file) is not found.');
	}

	if ($parm['temp']) return; // no cookie

	$expire = (PLUGIN_SKIN_EXPIRE > 0) ? time() + (60*60*24) * PLUGIN_SKIN_EXPIRE : PLUGIN_SKIN_EXPIRE;

	setcookie('skin_file', $skin_file, $expire, get_baseuri('abs'));
	$_COOKIE['skin_file'] = $skin_file;

	if ($val[0] == 'tdiary') {
		setcookie('tdiary_theme', $val[1], $expire, get_baseuri('abs'));
		$_COOKIE['tdiary_theme'] = $val[1];
	} else {
		setcookie('tdiary_theme', '', time()-3600); // Because it is not tdiary, it deletes it.
	}

	header('Location: '.get_page_location_uri($vars['page']));
}

function plugin_skin_action()
{
	global $vars, $_skin_msg;
	if (!PLUGIN_SKIN_USE) return array('msg'=>$_skin_msg['msg_change'],'body'=>$_skin_msg['err_not_use']);
	if (isset($vars['update']))
		return array('msg'=>$_skin_msg['msg_config_update'],'body'=>skin_config_update());
	if (isset($vars['list']))
		return array('msg'=>$_skin_msg['msg_config_list'],'body'=>skin_config_list());
	if (empty($vars['theme']))
		return array('msg'=>$_skin_msg['msg_change'],'body'=>skin_make_filelist());
	plugin_skin_convert($vars['theme']);
}

function plugin_skin_inline()
{
	global $_skin_msg;

	$theme = skin_get_plus_theme();
	$retval = $theme;
	if ($theme == 'tdiary') {
		if (isset($_COOKIE['tdiary_theme'])) {
			$retval .= ' ('.$_COOKIE['tdiary_theme'].')';
		} else {
			if (defined('TDIARY_THEME')) {
				$retval .= ' ('.TDIARY_THEME.')';
			}
		}
	}

	return $retval;
}

function skin_set_parm($argv)
{
	$parm = array();
	$parm['temp'] = false;
	$parm['cmd'] = '';
	foreach($argv as $arg) {
		$val = explode('=', $arg);
		$val[1] = (empty($val[1])) ? htmlsc($val[0]) : htmlsc($val[1]);
		switch($val[0]) {
		case 'temp':
			$parm['temp'] = true;
			break;
		case 'admin':
			$parm['cmd'] = 'admin';
		default:
			$parm['list'][] = array($val[0],$val[1]);
		}
	}
	return $parm;
}

function skin_menu_admin()
{
	global $_skin_msg;
	static $array_lang = array('ja_JP'=>'jp.png','en_US'=>'us.png','ko_KR'=>'kr.png','zh_TW'=>'tw.png','de_DE'=>'de.png');

	$theme_plus_name   = substr(THEME_PLUS_NAME,0,-1);
	$theme_tdiary_name = substr(THEME_TDIARY_NAME,0,-1);
	$array_theme = array($theme_plus_name=>'Plus!',$theme_tdiary_name=>'tDiary');

		$update = $list = get_script_uri().'?cmd=skin';
		$list .= '&list';
		$update .= '&update';
	$image_uri = IMAGE_URI;

	$retval = <<<EOD
#legend({$_skin_msg['adm_titme']}){{{
#skin
|~{$_skin_msg['adm_present_theme']}|>|&skin;|

EOD;
	$i = 0;
	foreach($array_theme as $theme=>$theme_name) {
		if (!file_exists(SKIN_URI.$theme)) continue;
		$tmp_title = ($i === 0) ? $_skin_msg['adm_config_name'] : '';
		$i++;
		$retval .= '|~'.$tmp_title.'|&img3('.$image_uri.'skin.png,'.$theme_name.'); [['.$theme_name.'>'.PKWK_CONFIG_PREFIX.'plugin/skin/'.$theme.']]|';
		foreach($array_lang as $locale=>$image) {
			if (is_page(PKWK_CONFIG_PREFIX.'plugin/skin/'.$theme.'/'.$locale)) {
				$retval .=  '[[&img3('.$image_uri.'icon/flags/'.$image.','.$locale.');>'.PKWK_CONFIG_PREFIX.'plugin/skin/'.$theme.'/'.$locale.']] ';
			}
		}
		$retval .= "|\n";
	}

	$retval .= <<<EOD
|~{$_skin_msg['adm_amnagement_op']}|&img3({$image_uri}plus/update.png,update);[[{$_skin_msg['adm_config_update']}>$update]]|&img3({$image_uri}list.png,list);[[{$_skin_msg['msg_config_list']}>$list]]|
}}}

EOD;
	return convert_html($retval);
}

function skin_get_plus_theme()
{
	global $skin_file;

	$skin = (isset($_COOKIE['skin_file'])) ? $_COOKIE['skin_file'] : $skin_file;
	$pos = strrpos($skin, '/');
	$skin = ($pos === false) ? $skin : substr($skin,$pos+1);
	$pos = strpos($skin,'.skin.php');
	return ($pos === false) ? $skin : substr($skin,0,$pos);
}

function skin_make_filelist($list='')
{
	global $vars, $_skin_msg;

	$script = get_script_uri();
	$retval = <<<EOD
<form action="$script" method="post">
 <div>
  <select name="theme">

EOD;

	// Processing and data acquisition of input data
	// 入力データの加工およびデータ取得
	if (empty($list)) {
		// The directory search is evaded when there is config page.
		// config ページがある場合には、ディレクトリ検索を回避する
		list($list_plus, $list_tdiary) = skin_config_maintenance();
	} else {
		// Input data is separated. 
		// 入力データを分離
		$list_plus = $list_tdiary = array();
		foreach($list as $skin=>$name) {
			$val = explode('.', $skin);
			$val[1] = (empty($val[1])) ? $val[0] : $val[1];
			if ($val[0] == 'tdiary') {
				$list_tdiary[ $val[1] ] = array('use'=>'1','name'=>$name);
			} else {
				$list_plus[ $val[1] ] = array('use'=>'1','name'=>$name);
			}
				}
	}

	// plus theme
	if (!empty($list_plus)) {
		$retval .= '   <optgroup label="plus-theme">'."\n";
		$theme = skin_get_plus_theme();
		foreach($list_plus as $skin=>$val) {
			if (!$val['use']) continue;
			switch ($skin) {
			case 'keitai':
			case 'tdiary':
				continue;
			default:
				$selected = (!empty($theme) && $theme == $skin) ? ' selected="selected"' : '';
				$name = (empty($val['name'])) ? $skin : $val['name'];
				$style = (empty($val['color'])) ? '' : skin_set_color($val['color']);
				$retval .= '	 <option value="'.$skin.'"'.$style.$selected.'>'.htmlsc($name).'</option>'."\n";
			}
		}
		$retval .= '   </optgroup>'."\n";
	}

	// tDiary theme
	if (!empty($list_tdiary)) {
		$retval .= '   <optgroup label="tDiary-theme">'."\n";
		$theme = (isset($_COOKIE['tdiary_theme'])) ? $_COOKIE['tdiary_theme'] : '';
		foreach($list_tdiary as $skin=>$val) {
			if (!$val['use']) continue;
			$selected = (!empty($theme) && $theme == $skin) ? ' selected="selected"' : '';
			$name = (empty($val['name'])) ? $skin : $val['name'];
			$style = (empty($val['color'])) ? '' : skin_set_color($val['color']);
			$retval .= '	 <option value="tdiary.'.$skin.'"'.$style.$selected.'>'.htmlsc($name).'</option>'."\n";
			   	}
		$retval .= '   </optgroup>'."\n";
	}

	$retval .= <<<EOD
  </select>
  <input type="hidden" name="cmd" value="skin" />
  <input type="hidden" name="page" value="{$vars['page']}" />
  <input type="submit" value="{$_skin_msg['msg_change']}" />
 </div>
</form>

EOD;
	return $retval;
}

function skin_search()
{
	$config = skin_read_config('plus');
	$retval = array();
	foreach(array(EXT_SKIN_DIR, SKIN_DIR, SKIN_URI, DATA_HOME.SKIN_DIR) as $dir) {
		$rc = skin_find_file($dir, $config);
		if (!empty($rc)) $retval = array_merge($retval,$rc);
	}
	return $retval;
}

function skin_find_file($dir, & $config)
{
		$retval = $matches = array();

	if ($dp = opendir($dir)) {
			while ($file = readdir($dp)) {
					if ($file==='.' || $file==='..') continue;
					if (filetype($dir.$file) === 'dir') {
							$rc = skin_find_file($dir.$file.'/', $config);
							if (!empty($rc)) $retval = array_merge($retval,$rc);
				continue;
			}
			if (preg_match('/(.*)\.skin\.php$/i', $file, $matches)) {
				$skin = & $matches[1]; // alias
				if (isset($config[$skin])) {
					$retval[$skin] = $config[$skin]; // use,name, memo
				} else {
					$retval[$skin] = array('use'=>'1','name'=>'','color'=>'','memo'=>'');
				}
			}
		}
		ksort($retval);
		closedir($dp);
	}
		return $retval;
}

function skin_search_tdiary()
{
	$config = skin_read_config('tdiary');
	$retval = array();
	$dir = SKIN_URI.THEME_TDIARY_NAME;

	if ($dp = opendir($dir)) {
		while ($file = readdir($dp)) {
			if ($file==='.' || $file==='..') continue;
			if (filetype($dir.$file) != 'dir') continue;
			// dir
			if (isset($config[$file])) {
				$retval[$file] = $config[$file]; // use, name
			} else {
				$retval[$file] = array('use'=>'1','name'=>'','color'=>'','memo'=>'');
			}
		}
		ksort($retval);
		closedir($dp);
	}
	return $retval;
}

function skin_config_update()
{
	global $_skin_msg;
	if (Auth::check_role('role_adm')) die_message($_skin_msg['err_not_update']);
	skin_config_maintenance(1);
	return $_skin_msg['msg_update_end'];
}

function skin_config_list()
{
		global $_skin_msg;
	list($list_plus, $list_tdiary) = skin_config_maintenance();

	$title  = 'Plus! Theme';
	$data  = '*'.$title."\n\n";
	$data .= '|'.$_skin_msg['field_theme_name'].'|'.$_skin_msg['field_display_name'].'|'.$_skin_msg['field_memo']."|h\n";
	foreach($list_plus as $theme=>$val) {
		if (!$val['use']) continue;
		$display_name = (empty($val['name'])) ? $theme : $val['name'];
		$data .= '|'.$theme.'|'.$val['color'].$display_name.'|'.$val['memo']."|\n";
	}

	$title  = 'tDiary Theme';
	$data .= '*'.$title."\n\n";
	$data .= '|'.$_skin_msg['field_theme_name'].'|'.$_skin_msg['field_display_name'].'|'.$_skin_msg['field_memo']."|h\n";
	foreach($list_tdiary as $theme=>$val) {
		if (!$val['use']) continue;
		$display_name = (empty($val['name'])) ? $theme : $val['name'];
		$data .= '|'.$theme.'|'.$val['color'].$display_name.'|'.$val['memo']."|\n";
	}

	return convert_html($data);
}

function skin_config_maintenance($update=0)
{
	// is_freeze($page)
	$list_plus = skin_search();
	list($master,$locale,$title) = skin_get_config_name('plus',1);
	skin_put_config($update, $list_plus, $master, $locale, $title);

	$list_tdiary = skin_search_tdiary();
	list($master,$locale,$title) = skin_get_config_name('tdiary',1);
	skin_put_config($update, $list_tdiary, $master, $locale, $title);

	return array($list_plus, $list_tdiary);
}

function skin_get_config_name($theme,$conf_pre=0,$lang='')
{
	$pref = ($conf_pre) ? PKWK_CONFIG_PREFIX : '';
	$lang = (empty($lang)) ? LANG : $lang;

	switch($theme) {
	case 'plus':
		$master = $pref.'plugin/skin/'.substr(THEME_PLUS_NAME,0,-1);
		$locale = $master.'/'.$lang;
		$title  = 'PLUS_THEME';
		break;
	case 'tdiary':
		$master = $pref.'plugin/skin/'.substr(THEME_TDIARY_NAME,0,-1);
		$locale = $master.'/'.$lang;
		$title  = 'TDIARY_THEME';
		break;
	default:
		$master = $locale = $title = '';
	}
	return array($master,$locale,$title);
}

function skin_read_config($theme)
{
	 $retval = array();

	list($master,$locale,$title) = skin_get_config_name($theme);

	$config = new Config($master);
	$config->read();
	$config_data = $config->get($title);
	// 0:Theme Name, 1:Use, 2:Color, 3:Memo
	foreach($config_data as $x) {
		$retval[ $x[0] ] = array('use'=>$x[1],'color'=>$x[2],'name'=>'','memo'=>$x[3]);
	}

	$config = new Config($locale);
	$config->read();
	$config_data = $config->get($title);
	// 0:Theme Name, 1:Display Name
	foreach($config_data as $x) {
		if (isset($retval[ $x[0] ])) $retval[ $x[0] ]['name'] = $x[1];
	}

	return $retval;
}

function skin_put_config($update, & $list, $master, $locale, $title)
{
	global $_skin_msg;

	if ($update || !is_page($master)) {
			$postdata = '*'.$title."\n\n";
		$postdata .= '|'.$_skin_msg['field_theme_name'].'|'.$_skin_msg['field_use'].'|'.$_skin_msg['field_color'].'|'.$_skin_msg['field_memo']."|h\n";
			foreach($list as $theme=>$val) {
				$postdata .= '|'.$theme.'|'.$val['use'].'|'.$val['color'].'|'.$val['memo']."|\n";
			}
		page_write($master, $postdata);
	}

	// locale
	if ($update || !is_page($locale)) {
		$postdata = '*'.$title."\n\n";
		$postdata .= '|'.$_skin_msg['field_theme_name'].'|'.$_skin_msg['field_display_name']."|h\n";
		foreach($list as $theme=>$val) {
			$postdata .= '|'.$theme.'|'.$val['name']."|\n";
		}
		page_write($locale, $postdata);
	}
}

function skin_set_color($text)
{
	$text = trim($text);
	$style = '';
	while (preg_match('/^(?:(BG)?COLOR\(([#\w]+)\)):(.*)$/',$text, $matches)) {
		if ($matches[2]) {
			$name = $matches[1] ? 'background-color' : 'color';
			$style .= $name . ':' . htmlsc($matches[2]) . ';';
			$text = $matches[3];
		}
	}
	return (empty($style)) ? '' : ' style="'.$style.'"';
}

/* End of file skin.inc.php */
/* Location: ./wiki-common/plugin/skin.inc.php */
