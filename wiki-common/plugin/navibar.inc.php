<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
// Copyright (C)
//   2010-2011 PukiWiki Advance Developers Team
//   2009      PukiWiki Plus! Team

// License: GPL v2 or (at your option) any later version
// $Id: navibar.php,v 0.1.19 2011/12/20 20:40:00 Logue Exp $
//

use PukiWiki\Auth\Auth;
function plugin_navibar_convert()
{
	global $do_backup, $trackback, $referer;
	global $function_freeze;
	global $vars;
	global $whatsnew,$whatsdeleted;
/*
	if ($_LINK['reload'] == '') {
		return '<p class="message_box ui-state-error ui-corner-all">#navibar: plugin called from wikipage. skipped.</p>';
	}
*/
	$_page  = isset($vars['page']) ? $vars['page'] : null;
	$is_read = (arg_check('read') && is_page($_page));
	$is_freeze = is_freeze($_page);
	$is_readonly = (Auth::check_role('readonly') || (PKWK_READONLY == Auth::ROLE_AUTH && Auth::get_role_level() > Auth::ROLE_AUTH) ) ? true : false;
	$cmd = isset($vars['cmd']) ? $vars['cmd'] : null;

	$num = func_num_args();
	$args = $num ? func_get_args() : array();

	if (IS_MOBILE){
		$ret[] = '<ul data-role="listview">';
	}else{
		$ret[] = '<ul>';
	}
	while(!empty($args)) {
		$name = array_shift($args);
		switch ($name) {
		case 'freeze':
		case 'unfreeze':
			if ($is_read && $function_freeze && !($_page == $whatsnew || $_page == $whatsdeleted)) {
				if ($is_freeze) {
					$name = 'unfreeze';
				}else{
					$name = 'freeze';
				}
				$ret[] = _navibar($name);
			}
			break;
		case 'list':
			if ($cmd !== 'list'){
				$ret[] = _navibar($name);
			}else{
				$ret[] = _navibar('filelist');
			}
			break;
		case 'backup':
			if ($do_backup) {
				$ret[] = _navibar($name);
			}
			
			break;
		case 'brokenlink':
		case 'template':
		case 'source':
		case 'diff':
		case 'reload':
		case 'copy':
			if ($is_read || !empty($_page) && !($_page == $whatsnew || $_page == $whatsdeleted)) {
				$ret[] = _navibar($name);
			}
			break;
		case 'trackback':
			if ($trackback){
				if (!empty($_page) && !($_page == $whatsnew || $_page == $whatsdeleted)) {
					$ret[] = _navibar($name, 'Trackback(' . tb_count($_page) . ')');
				}else{
			//		$ret[] = _navibar($name, 'Trackback list');
				}
			}
			break;
		case 'referer':
		case 'skeylist':
		case 'linklist':
			if ($referer && !empty($_page)) {
				$ret[] = _navibar($name);
			}
			break;
		
		case 'log':
			if (!empty($_page) && log_exist('login',$vars['page'])) {
				$ret[] = _navibar($name);
			}

		case 'log_login':
			if (!empty($_page) && log_exist('login',$vars['page'])) {
				$ret[] = _navibar($name);
			}
			break;
		case 'log_check':
			if (!empty($_page) && log_exist('check',$vars['page'])) {
				$ret[] = _navibar($name);
			}
			break;
		case 'log_browse':
			if (!empty($_page)){
				$ret[] = _navibar($name);
			}
//			if (log_exist('browse',$vars['page'])) {
//				return _navibar($name);
//			}
			break;
		case 'log_update':
			if (!empty($_page) && log_exist('update',$vars['page'])) {
				$ret[] = _navibar($name);
			}
			break;
		case 'log_down':
			if (!empty($_page) && log_exist('download',$vars['page'])) {
				$ret[] = _navibar($name);
			}
			break;
		case '|':
			if (end($ret) !== '<ul>' ){
				$ret[] = '</ul>';
				$ret[] = "\n";
				$ret[] = !IS_MOBILE ? '<ul>' : '<ul data-role="listview">';
			}
			break;
		case 'attach':
		case 'upload':
			if ($is_read && (bool)ini_get('file_uploads') && !$is_freeze && !($_page == $whatsnew || $_page == $whatsdeleted) && !$is_readonly) {
				$ret[] = _navibar($name);
			}
			break;
		case 'new':
		case 'newsub':
		case 'edit':
		case 'guiedit':
			if (!empty($_page) && $is_read && $function_freeze && !$is_freeze && !($_page == $whatsnew || $_page == $whatsdeleted)  && !$is_readonly) {
				$ret[] = _navibar($name);
			}
		break;
		case 'login':
			$auth_key = Auth::get_user_info();
			if (empty($auth_key['key'])) {
				$ret[] = _navibar($name);
			}
		break;
		default:
			$ret[] = _navibar($name);
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

	if (IS_MOBILE){
		return $body;
	}else{
		return '<nav class="navibar">'.$body.'</nav>' ."\n";
	}
}

function _navibar($key, $alt=null)
{
	global $_LINK, $_LANG, $_SKIN;

	if (!isset($_LANG['skin'][$key])) { return '<!--LANG NOT FOUND-->'; }
	if (!isset($_LINK[$key])) { return '<!--LINK NOT FOUND-->'; }
	$showicon = isset($_SKIN['showicon']) ? $_SKIN['showicon'] : false;
	$lang = isset($alt) ? $alt : $_LANG['skin'][$key];

	return '<li><a href="' . $_LINK[$key] . '" rel="nofollow" >'. ($showicon ? '<span class="pkwk-icon icon-'.$key.'"></span>' : '') . $lang. '</a></li>';
}
/* End of file navibar.inc.php */
/* Location: ./wiki-common/plugin/navibar.inc.php */
