<?php
/////////////////////////////////////////////////
// PukiPlus.
// Copyright (C)
//   2010      PukiPlus Team
//   2009      PukiWiki Plus! Team

// License: GPL v2 or (at your option) any later version
// $Id: navibar.php,v 0.1.17 2010/07/06 00:06:00 Logue Exp $
//
function plugin_navibar_convert()
{
	global $_LINK;
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
	$body = '';
	$line = '';

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
				if ($body != '' && $oldname != '|') { $body .= ' | '; }
				$body .= _navigator($name);
			}
			break;
		case 'upload':
			if ($is_read && (bool)ini_get('file_uploads') && !$is_freeze && !($_page == $whatsnew || $_page == $whatsdeleted)) {
				if ($body != '' && $oldname != '|') { $body .= ' | '; }
				$body .= _navigator($name);
			}
			break;
		case 'filelist':
			if (arg_check('list') && (bool)ini_get('file_uploads')) {
				if ($body != '' && $oldname != '|') { $body .= ' | '; }
				$body .= _navigator($name);
			}
			break;
		case 'backup':
			if ($do_backup) {
				if ($body != '' && $oldname != '|') { $body .= ' | '; }
				$body .= _navigator($name);
			}
			
			break;
		case 'brokenlink':
		case 'template':
		case 'source':
			if (!empty($_page)) {
				if ($body != '' && $oldname != '|') { $body .= ' | '; }
				$body .= _navigator($name);
			}
			break;
		case 'trackback':
			if ($trackback && !($_page == $whatsnew || $_page == $whatsdeleted)) {
				if ($body != '' && $oldname != '|') { $body .= ' | '; }
				$tbcount = tb_count($_page);
				if ($tbcount > 0) {
					$body .= _navigator($name, 'Trackback(' . $tbcount . ')');
				} else if ($is_read) {
					$body .= 'no Trackback';
				} else if (isset($vars['cmd']) && $vars['cmd'] == 'list') {
					$body .= _navigator($name, 'Trackback list');
				}
			}
			break;
		case 'referer':
		case 'skeylist':
		case 'linklist':
			if ($referer) {
				if ($body != '' && $oldname != '|') { $body .= ' | '; }
				$body .= _navigator($name);
			}
			break;

		case 'log_login':
			if (log_exist('login',$vars['page'])) {
				if ($body != '' && $oldname != '|') { $body .= ' | '; }
				$body .= _navigator($name);
			}
			break;
		case 'log_check':
			if (log_exist('check',$vars['page'])) {
				if ($body != '' && $oldname != '|') { $body .= ' | '; }
				$body .= _navigator($name);
			}
			break;
		case 'log_browse':
			if ($body != '' && $oldname != '|') { $body .= ' | '; }
			$body .= _navigator($name);
//			if (log_exist('browse',$vars['page'])) {
//				return _navigator($name);
//			}
			break;
		case 'log_update':
			if (log_exist('update',$vars['page'])) {
				if ($body != '' && $oldname != '|') { $body .= ' | '; }
				$body .= _navigator($name);
			}
			break;
		case 'log_down':
			if (log_exist('download',$vars['page'])) {
				if ($body != '' && $oldname != '|') { $body .= ' | '; }
				$body .= _navigator($name);
			}
			break;
		case '|':
			if ( trim($body) != '' ) {
				$line .= '[ ' . $body . ' ]' . "\n\n";
				$body = '';
			}
			break;
		// case 'new':
		case 'newsub':
		case 'edit':
		case 'guiedit':
			if ($is_read && $function_freeze && !$is_freeze && !($_page == $whatsnew || $_page == $whatsdeleted)) {
				if ($body != '' && $oldname != '|') { $body .= ' | '; }
				$body .= _navigator($name);
			}
		break;
		case 'diff':
			if (!$is_read)
				break;
		default:
			if ($body != '' && $oldname != '|') { $body .= ' | '; }
			$body .= _navigator($name);
			break;
		}
		$oldname = $name;
		$body .= ' ';
	}

	if ( trim($body) != '' ) {
		$line .= '[ ' . $body . ' ]' . "\n\n";
		$body = '';
	}
	return '<div id="navigator">'. $line . '</div>';
}

function _navigator($key, $val = '')
{
	global $_LINK, $_LANG, $showicon;

	$lang  = $_LANG['skin'];
	$link  = $_LINK;

	if (!isset($lang[$key])) { return '<!--LANG NOT FOUND-->'; }
	if (!isset($link[$key])) { return '<!--LINK NOT FOUND-->'; }

	if ($showicon){
		return '<a href="' . $link[$key] . '" rel="nofollow" class="pkwk-icon_linktext cmd-'.$key.'">' . $lang[$key]. '</a>';
	}else{
		return '<a href="' . $link[$key] . '" rel="nofollow">' . $lang[$key]. '</a>';
	}
}
?>
