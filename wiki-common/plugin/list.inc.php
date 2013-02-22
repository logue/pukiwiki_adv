<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: list.inc.php,v 1.6.11 2011/09/25 15:54:00 Logue Exp $
//
// IndexPages plugin: Show a list of page names
use PukiWiki\Lib\Auth\Auth;
use PukiWiki\Lib\Factory;
use PukiWiki\Lib\File\FileUtility;

defined('PKWK_SITEMAPS_CACHE') or define('PKWK_SITEMAPS_CACHE', 'sitemaps');

function plugin_list_action()
{
	global $vars, $whatsnew;
	$_title_list = T_('List of pages');
	$_title_filelist = T_('List of page files');

	$listcmd = isset($vars['listcmd']) ? $vars['listcmd'] : 'read';
	$type = isset($vars['type']) ? $vars['type'] : '';

	$buffer = array();
	switch($type) {
		case 'json' :
			// インクリメンタルサーチ向け
			if (isset($vars['term'])){
				// 酷い実装だ・・・。
				foreach(FileUtility::getExists() as $page){
					if (preg_match('/^'.$vars['term'].'/', $page)){
						$buffer[] = $page;
					}
				}
			}else{
				$buffer = $pages;
			}
			header("Content-Type: application/json; charset=".CONTENT_CHARSET);
			echo json_encode($buffer);
			exit();
		case 'sitemap' :

			$buffer[] = '<?xml version="1.0" encoding="UTF-8"?>';
			$buffer[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
			foreach (FileUtility::getExists() as $page){
				$wiki = WikiFactory::Wiki($page);
				if ($wiki->isHidden()) continue;
				$buffer[] = '<url>';
				$buffer[] = '<loc>'.$wiki->get_uri().'</loc>';
				$buffer[] = '<lastmod>'.get_date('Y-m-d\TH:i:s\Z', $wiki->getTime()).'</lastmod>';
				$buffer[] = '<priority>0.5</priority>';
				$buffer[] = '</url>';
			}
			$buffer[] = '</urlset>';
			$data = join("",$buffer);

			header("Content-Type: application/xml; charset=".CONTENT_CHARSET);
			echo $data;
			exit();
		break;
		case 'filelist' :
			if (! Auth::check_role('role_adm_contents'))
				$filelist = TRUE;
			else
			if (! pkwk_login($vars['pass']))
				$filelist = FALSE;

			return array(
				'msg'=>$_title_filelist,
				'body'=>FileUtility::getListing(DATA_DIR, 'read', $filelist)
			);
		break;
	}

	return array(
		'msg'=>$_title_list,
		'body'=> FileUtility::getListing(DATA_DIR, ($listcmd == 'read' || $listcmd == 'edit' ? $listcmd : 'read'))
	);
}
/* End of file list.inc.php */
/* Location: ./wiki-common/plugin/list.inc.php */
