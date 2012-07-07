<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: deleted.inc.php,v 1.6.3 2010/12/26 16:50:00 Logue Exp $
//
// Show deleted (= Exists in BACKUP_DIR or DIFF_DIR but not in DATA_DIR)
// page list to clean them up
//
// Usage:
//   index.php?cmd=deleted[&file=on]
//   index.php?cmd=deleted&dir=diff[&file=on]

function plugin_deleted_init()
{
	global $_string;
	$msg = array(
		'_deleted_msg' => array(
			'title_collision'		=> T_('On updating $1, a collision has occurred.'),
			'title_withfilename'	=> $_string['updated'],
			'no_such_setting'		=> T_('No such setting: Choose backup or diff')
		)
	);
	set_plugin_messages($msg);
}

function plugin_deleted_action()
{
	global $vars, $_deleted_msg;

	$dir = isset($vars['dir']) ? $vars['dir'] : 'backup';
	$withfilename  = isset($vars['file']);

	$_DIR['diff'  ]['dir'] = DIFF_DIR;
	$_DIR['diff'  ]['ext'] = '.txt';
	$_DIR['backup']['dir'] = BACKUP_DIR;
	$_DIR['backup']['ext'] = BACKUP_EXT; // .gz or .txt
	//$_DIR['cache' ]['dir'] = CACHE_DIR; // No way to delete them via web browser now
	//$_DIR['cache' ]['ext'] = '.ref';
	//$_DIR['cache' ]['ext'] = '.rel';

	if (! isset($_DIR[$dir]))
		return array('msg'=>'Deleted plugin', 'body'=>$_deleted_msg['no_such_setting']);

	$deleted_pages  = array_diff(
		auth::get_existpages($_DIR[$dir]['dir'], $_DIR[$dir]['ext']),
		auth::get_existpages());

	if ($withfilename) {
		$retval['msg'] = $_deleted_msg['title_withfilename'];
	} else {
		$retval['msg'] = $_deleted_msg['title_collision'];
	}
	$retval['body'] = page_list($deleted_pages, $dir, $withfilename);

	return $retval;
}
/* End of file deleted.inc.php */
/* Location: ./wiki-common/plugin/deleted.inc.php */
