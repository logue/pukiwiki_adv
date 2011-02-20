<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: list.inc.php,v 1.6.10 2011/02/17 21:43:00 Logue Exp $
//
// IndexPages plugin: Show a list of page names
function plugin_list_action()
{
	global $vars;
//	global $_title_list,$_title_filelist;
	$_title_list = T_('List of pages');
	$_title_filelist = T_('List of page files');

	// Redirected from filelist plugin?
	$filelist = (isset($vars['cmd']) && $vars['cmd']=='filelist');
	if ($filelist) {
		if (! auth::check_role('role_adm_contents'))
			$filelist = TRUE;
		else
		if (! pkwk_login($vars['pass']))
			$filelist = FALSE;
	}

	$listcmd = (isset($vars['listcmd'])) ? $vars['listcmd'] : 'read';

	return array(
		'msg'=>$filelist ? $_title_filelist : $_title_list,
		'body'=>plugin_list_getlist($filelist,$listcmd));
}

// Get a list
function plugin_list_getlist($withfilename = FALSE, $listcmd = 'read')
{
	global $non_list, $whatsnew;

	if (DEBUG === false){
		$pages = array_diff(auth::get_existpages(),array($whatsnew));
		if (!$withfilename)
			$pages = array_diff($pages, preg_grep('/' . $non_list . '/S', $pages));
	}else{
		$pages = array_diff(get_existpages(),array($whatsnew));
	}
	if (empty($pages)) return '';
	$cmd = ($listcmd == 'read' || $listcmd == 'edit') ? $listcmd : 'read';
	return page_list($pages,$cmd,$withfilename);
}
?>
