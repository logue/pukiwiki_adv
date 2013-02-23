<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: cmd.inc.php,v 0.0 2010/07/15 10:01:21 Logue Exp $
//
// Command link plugin
use PukiWiki\Auth\Auth;

function plugin_cmd_init(){

}

function plugin_cmd_inline(){
	global $page;

	global $do_backup, $trackback, $referer;
	global $function_freeze;
	global $vars;
	global $whatsnew,$whatsdeleted;

	$num = func_num_args()-1;
	$args = $num ? func_get_args() : array();
	array_pop($args);

	$name = $args[0];
	$pagename = '';
	if ($num == 1){
		$_page = strip_tags($page);
	}else if ($num == 2){
		$_page = $args[1];
		$pagename = true;
	}


	return;
}

function plugin_cmd_link($name, $page){
	$is_readonly = Auth::check_role('readonly');
	$is_safemode = Auth::check_role('safemode');
	$is_createpage = Auth::is_check_role(PKWK_CREATE_PAGE);
	if (isset($page)){
		$page  = isset($vars['page']) ? $vars['page'] : '';
	}
	// $is_read = (arg_check('read') && is_page($_page));
	$is_read = is_page($page);
	$is_editable = is_editable($page);

	switch ($name) {
		case 'freeze':
		case 'unfreeze':
			if ($is_read && $function_freeze) {
				if ($is_freeze) {
					$name = 'unfreeze';
				}else{
					$name = 'freeze';
				}
				return plugin_cmd_getlink($name);
			}
			break;
		case 'upload':
			if ($is_read && (bool)ini_get('file_uploads') && !$is_freeze && !($_page == $whatsnew || $_page == $whatsdeleted)) {
				return plugin_cmd_getlink($name);
			}
			break;
		case 'list':
			if ($vars['cmd'] !== 'list'){
				return plugin_cmd_getlink($name);
			}else if( (bool)ini_get('file_uploads')) {
				return plugin_cmd_getlink('filelist');
			}
			break;
		case 'backup':
			if ($do_backup) {
				return plugin_cmd_getlink($name);
			}

			break;
		case 'brokenlink':
		case 'template':
		case 'source':
			if (!empty($_page)) {
				return plugin_cmd_getlink($name);
			}
			break;
		case 'trackback':
			if ($trackback && !($_page == $whatsnew || $_page == $whatsdeleted)) {
				$tbcount = tb_count($_page);
				if (isset($vars['cmd']) && $vars['cmd'] == 'list') {
					return plugin_cmd_getlink($name, 'Trackback list');
				}else{
					return plugin_cmd_getlink($name, 'Trackback(' . $tbcount . ')');
				}
			}
			break;
		case 'referer':
		case 'skeylist':
		case 'linklist':
			if ($referer) {
				return plugin_cmd_getlink($name);
			}
			break;

		case 'log_login':
			if (log_exist('login',$vars['page'])) {
				return plugin_cmd_getlink($name);
			}
			break;
		case 'log_check':
			if (log_exist('check',$vars['page'])) {
				return plugin_cmd_getlink($name);
			}
			break;
		case 'log_browse':
			return plugin_cmd_getlink($name);
//			if (log_exist('browse',$vars['page'])) {
//				return plugin_cmd_getlink($name);
//			}
			break;
		case 'log_update':
			if (log_exist('update',$vars['page'])) {
				return plugin_cmd_getlink($name);
			}
			break;
		case 'log_down':
			if (log_exist('download',$vars['page'])) {
				return plugin_cmd_getlink($name);
			}
			break;
		case '|':
			return '</ul>'."\n".'<ul>';
			break;
		// case 'new':
		case 'newsub':
		case 'edit':
		case 'guiedit':
			if ($is_read && $function_freeze && !$is_freeze && !($_page == $whatsnew || $_page == $whatsdeleted)) {
				return plugin_cmd_getlink($name);
			}
		break;
		case 'full':
		case 'print':
		case 'diff':
		case 'reload':
		case 'copy':
			if (!$is_read)
				break;
		default:
			return plugin_cmd_getlink($name);
			break;
	}
}

function plugin_cmd_getlink($key, $page, $flag)
{
	global $_LANG, $_LINK;
	$links = getLinkSet($page);

	if ($flag['icon'] === true){
		$icon = ($flag['name'] === true) ?
			'<span class="pkwk-icon icon-'.$key.'"></span>' : '<span class="pkwk-icon icon-'.$key.'" title="'.$_LANG['skin'][$key].'"></span>';
	}else{
		$icon = '';
	}
	$link = ($flag['name'] === true) ?
		'<a href="' . $links[$key] . '" rel="nofollow" >' . $icon . $_LANG['skin'][$key]. '</a>' :
		'<a href="' . $links[$key] . '" rel="nofollow" >' . $icon . '</a>';

	if ($flag['inline'] === true){
		return $link;
	}else{
		return '<li>'.$link.'</li>';
	}
}