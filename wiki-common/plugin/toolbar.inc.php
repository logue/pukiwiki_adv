<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: toolbar.php,v 0.2.16 2011/12/20 20:42:00 Logue Exp $
// Copyright (C)
//    2011      PukiWiki Advance Developers Team
//    2005,2007-2009 PukiWiki Plus! Team
// License: GPL v2
//

function plugin_toolbar_convert()
{
	global $_LINK, $pkwk_dtd;
	global $do_backup, $trackback, $referer;
	global $function_freeze;
	global $vars;
	global $whatsnew,$whatsdeleted;

	if ($_LINK['reload'] == '') {
		return '#navibar: plugin called from wikipage. skipped.';
	}

	$_page  = isset($vars['page']) ? $vars['page'] : '';
	$is_read = (arg_check('read') && is_page($_page));
	$is_freeze = is_freeze($_page);

	$num = func_num_args();
	$args = $num ? func_get_args() : array();

	$ret[] = '<ul role="toolbar">';
	while(!empty($args)) {
		$name = array_shift($args);
		switch ($name) {
		case 'freeze':
		case 'unfreeze':
			if ($is_read && $function_freeze) {
				if ($is_freeze) {
					$name = 'unfreeze';
				}else{
					$name = 'freeze';
				}
				$ret[] = _toolbar($name);
			}
			break;
		case 'upload':
			if ($is_read && (bool)ini_get('file_uploads') && !$is_freeze && !($_page == $whatsnew || $_page == $whatsdeleted)) {
				$ret[] = _toolbar($name);
			}
			break;
		case 'list':
			if ($vars['cmd'] !== 'list'){
				$ret[] = _toolbar($name);
			}else{
				$ret[] = _toolbar('filelist');
			}
			break;
		case 'backup':
			if ($do_backup) {
				$ret[] = _toolbar($name);
			}
			
			break;
		case 'brokenlink':
		case 'template':
		case 'source':
			if (!empty($_page)) {
				$ret[] = _toolbar($name);
			}
			break;
		case 'trackback':
			if ($trackback){
				if (!empty($_page) && !($_page == $whatsnew || $_page == $whatsdeleted)) {
					$ret[] = _toolbar($name, 'Trackback(' . tb_count($_page) . ')');
				}else{
			//		$ret[] = _toolbar($name, 'Trackback list');
				}
			}
			break;
		case 'referer':
		case 'skeylist':
		case 'linklist':
			if ($referer && !empty($_page)) {
				$ret[] = _toolbar($name);
			}
			break;
		case 'log_login':
			if (!empty($_page) && log_exist('login',$vars['page'])) {
				$ret[] = _toolbar($name);
			}
			break;
		case 'log_check':
			if (!empty($_page) && log_exist('check',$vars['page'])) {
				$ret[] = _toolbar($name);
			}
			break;
		case 'log':
		case 'log_browse':
			if (!empty($_page)){
				$ret[] = _toolbar($name);
			}
//			if (log_exist('browse',$vars['page'])) {
//				return _toolbar($name);
//			}
			break;
		case 'log_update':
			if (!empty($_page) && log_exist('update',$vars['page'])) {
				$ret[] = _toolbar($name);
			}
			break;
		case 'log_down':
			if (!empty($_page) && log_exist('download',$vars['page'])) {
				$ret[] = _toolbar($name);
			}
			break;
		case '|':
			if (end($ret) !== '<ul>' ){
				$ret[] = '</ul>';
				$ret[] = "\n";
				$ret[] = '<ul role="toolbar">';
			}
			break;
		// case 'new':
		case 'newsub':
		case 'edit':
		case 'guiedit':
			if (!empty($_page) && $is_read && $function_freeze && !$is_freeze && !($_page == $whatsnew || $_page == $whatsdeleted)) {
				$ret[] = _toolbar($name);
			}
		break;
		case 'diff':
		case 'reload':
		case 'copy':
			if (!$is_read || empty($_page))
				break;
		default:
			$ret[] = _toolbar($name);
			break;
		}
		
	}
	if (end($ret) === '<ul>'){
		array_pop($ret);
		array_pop($ret);
	}else{
		$ret[] = '</ul>';
	}
	$body = "\n".join('',$ret)."\n";

	return (($pkwk_dtd == PKWK_DTD_HTML_5) ? '<nav class="toolbar">'.$body.'</nav>' : '<div class="toolbar">'.$body.'</div>')."\n";
}

function _toolbar($key, $alt=null)
{
	global $_LANG, $_LINK;
	$lang  = $_LANG['skin'];
	$link  = $_LINK;

	if (!isset($lang[$key])) { return '<!--LANG NOT FOUND-->'; }
	if (!isset($link[$key])) { return '<!--LINK NOT FOUND-->'; }

	return '<li><a href="' . $link[$key] . '" rel="nofollow" title="'.$lang[$key].'"><span class="pkwk-icon icon-'.$key.'">' . $lang[$key]. '</span></a></li>';
}
?>
