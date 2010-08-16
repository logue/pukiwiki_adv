<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: cmd.inc.php,v 0.0 2010/07/15 10:01:21 Logue Exp $
//
// Command link plugin

function plugin_cmd_inline(){
	
}


function plugin_cmd_keyword($name,$page)
{
	global $_LINK;
	global $do_backup, $trackback, $referer;
	global $function_freeze;
	global $vars;
	global $whatsnew,$whatsdeleted;

	if ($_LINK['reload'] == '') return array();

	if ($page){
		$_page = $page;
	}else{
		$_page  = isset($vars['page']) ? $vars['page'] : '';
	}
	
	$is_read = (arg_check('read') && is_page($_page));
	$is_freeze = is_freeze($_page);

	switch ($name) {
	case 'edit':
	case 'guiedit':
	case 'add':
		if ($is_read && !$is_freeze) return plugin_cmd_link($name);
		break;
	case 'freeze':
	case 'unfreeze':
		if ($is_read && $function_freeze) {
			$name = $is_freeze ? 'unfreeze' : 'freeze';
			return plugin_cmd_link($name);
		}
		break;
	case 'upload':
		if ($is_read && (bool)ini_get('file_uploads') && !$is_freeze && !($_page == $whatsnew || $_page == $whatsdeleted)) return plugin_cmd_link($name);
		break;
	case 'filelist':
		if (arg_check('list') && (bool)ini_get('file_uploads')) return plugin_cmd_link($name);
		break;
	case 'backup':
		if ($do_backup) return plugin_cmd_link($name);
		break;
	case 'brokenlink':
	case 'template':
	case 'source':
		if (!empty($_page)) return plugin_cmd_link($name);
		break;
	case 'trackback':
		if ($trackback) {
			$tbcount = tb_count($_page);
			
			if ($vars['cmd'] == 'list') {
				return plugin_cmd_link($name, 'Trackback list');
			}else{
				return plugin_cmd_link($name, 'Trackback(' . $tbcount . ')');
			}
		}
		break;
	case 'refer':
	case 'skeylist':
	case 'linklist':
		if ($referer) {
			if (!isset($refcount)) $refcount = tb_count($_page,'.ref');
			if ($refcount > 0) return plugin_cmd_link($name);
		}
		break;
	case 'log_login':
		if (log_exist('login',$_page)) return  plugin_cmd_link($name);
		break;
	case 'log_check':
		if (log_exist('check',$_page)) return plugin_cmd_link($name);
		break;
	case 'log_browse':
		if (log_exist('browse',$_page)) return plugin_cmd_link($name);
		break;
	case 'log_update':
		if (log_exist('update',$_page)) return plugin_cmd_link($name);
		break;
	case 'log_down':
		if (log_exist('download',$_page)) return plugin_cmd_link($name);	
		break;
	// case 'new':
	case 'newsub':
	case 'edit':
	case 'guiedit':
	case 'diff':
		if (!$is_read) break;
	default:
		return plugin_cmd_link($name);
		break;
	}
	return array();
}

function plugin_cmd_link($key, $val = '', $x = 20, $y = 20)
{
	global $_LINK, $_LANG;

	$link = $_LINK;
	$lang = $_LANG['skin'];

	if (!isset($link[$key])) { return '<!--LINK NOT FOUND-->'; }
	if (!isset($lang[$key])) { return '<!--LANG NOT FOUND-->'; }

	$text = ($val === '') ? $lang[$key] : $val;
	return '<a class="cmd-'.$key.'" href="' . $link[$key] . '" rel="nofollow">' . $text . '</a>';
}