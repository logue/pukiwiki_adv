<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: toolbar.php,v 0.2.15 2011/12/12 21:12:00 Logue Exp $
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
		case 'filelist':
			if ($vars['cmd'] == 'list' && (bool)ini_get('file_uploads')) {
				$ret[] = _toolbar($name);
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
			if ($trackback && !($_page == $whatsnew || $_page == $whatsdeleted)) {
				$tbcount = tb_count($_page);
				if (isset($vars['cmd']) && $vars['cmd'] == 'list') {
					$ret[] = _toolbar($name, 'Trackback list');
				}else{
					$ret[] = _toolbar($name, 'Trackback(' . $tbcount . ')');
				}
			}
			break;
		case 'referer':
		case 'skeylist':
		case 'linklist':
			if ($referer) {
				$ret[] = _toolbar($name);
			}
			break;

		case 'log_login':
			if (log_exist('login',$vars['page'])) {
				$ret[] = _toolbar($name);
			}
			break;
		case 'log_check':
			if (log_exist('check',$vars['page'])) {
				$ret[] = _toolbar($name);
			}
			break;
		case 'log_browse':
			$ret[] = _toolbar($name);
//			if (log_exist('browse',$vars['page'])) {
//				return _toolbar($name);
//			}
			break;
		case 'log_update':
			if (log_exist('update',$vars['page'])) {
				$ret[] = _toolbar($name);
			}
			break;
		case 'log_down':
			if (log_exist('download',$vars['page'])) {
				$ret[] = _toolbar($name);
			}
			break;
		case '|':
			$ret[] = '</ul>'."\n".'<ul>';
			break;
		// case 'new':
		case 'newsub':
		case 'edit':
		case 'guiedit':
			if ($is_read && $function_freeze && !$is_freeze && !($_page == $whatsnew || $_page == $whatsdeleted)) {
				$ret[] = _toolbar($name);
			}
		break;
		case 'diff':
		case 'reload':
			if (!$is_read)
				break;
		default:
			$ret[] = _toolbar($name);
			break;
		}
	}
	$ret[] = '</ul>';
	
	$body = "\n".'<ul>'.join('',$ret).'</ul>'."\n";

	return (($pkwk_dtd == PKWK_DTD_HTML_5) ? '<nav class="toolbar">'.$body.'</nav>' : '<div class="toolbar">'.$body.'</div>')."\n";
}

function _toolbar($key, $x = 20, $y = 20)
{
	global $_LANG, $_LINK;
	$lang  = $_LANG['skin'];
	$link  = $_LINK;

	if (!isset($lang[$key])) { return '<!--LANG NOT FOUND-->'; }
	if (!isset($link[$key])) { return '<!--LINK NOT FOUND-->'; }

	return '<li><a href="' . $link[$key] . '" rel="nofollow" title="'.$lang[$key].'"><span class="pkwk-icon icon-'.$key.'">' . $lang[$key]. '</span></a></li>';
}
?>
