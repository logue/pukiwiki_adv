<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: cmd.inc.php,v 0.0 2010/07/15 10:01:21 Logue Exp $
//
// Command link plugin

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
	$pagename = false;
	if ($num == 1){
		$_page = strip_tags($page);
	}else if ($num == 2){
		$_page = $args[1];
		$pagename = true;
	}
	
	// $is_read = (arg_check('read') && is_page($_page));
	$is_read = is_page($_page);
	$is_readonly = auth::check_role('readonly');
	$is_safemode = auth::check_role('safemode');
	$is_createpage = auth::is_check_role(PKWK_CREATE_PAGE);
	$is_editable = is_editable($_page);
	
	switch ($name) {
		case 'add':
		case 'filelist':
			if (arg_check('list')) {
				return plugin_cmd_link($name, $_page, $pagename);
			}
			break;
		case 'backup':
			if ($do_backup) {
				return plugin_cmd_link($name, $_page, $pagename);
			}
			break;
		case 'trackback':
			if ($trackback) {
				$tbcount = tb_count($_page);
				if ($tbcount > 0) {
					$cmd = $name;
				}
			}
			break;
		case 'referer':
			if ($referer) {
				return plugin_cmd_link($name, $_page, $pagename);
			}
			break;
		case 'rss':
		case 'mixirss':
			return plugin_cmd_link($name, $_page, $pagename);
			break;
		case 'freeze':
			if ($is_readonly) break;
			if (!$is_read) break;
			if ($function_freeze) {
				if (!is_freeze($_page)) {
					$name = 'freeze';
				} else {
					$name = 'unfreeze';
				}
				return plugin_cmd_link($name, $_page, $pagename);
			}
			break;
		case 'upload':
			if ($is_readonly) break;
			if (!$is_read) break;
			if ($function_freeze && is_freeze($_page)) break;
			if ((bool)ini_get('file_uploads')) {
				return plugin_cmd_link($name, $_page, $pagename);
			}
			break;
		case 'diff':
			if (!$is_read) break;
			if ($is_safemode) break;
		case 'edit':
		case 'guiedit':
			if (!$is_read) break;
			if ($is_readonly) break;
			if ($function_freeze && is_freeze($_page)) break;
			return plugin_cmd_link($name, $_page, $pagename);
			break;
		case 'new':
		case 'newsub':
			if ($is_createpage) break;
		case 'rename':
		case 'copy':
			if ($is_readonly) break;
		case 'reload':
		case 'print':
		case 'full':
			if (!$is_read) break;
		default:
			return plugin_cmd_link($name, $_page, $pagename);
			break;
	}
	return;
}

function plugin_cmd_link($key, $_page, $pagename)
{
	global $_LANG, $page;
	$link = getLinkSet($_page);
	$text = (($pagename == false) ? $_LANG['skin'][$key] : $_LANG['skin'][$key].':'.$_page);

	return '<a class="pkwk-icon_linktext cmd-'.$key.'" href="' . $link[$key] . '" rel="nofollow">' . $text . '</a>';
}