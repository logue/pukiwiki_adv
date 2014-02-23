<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: list.inc.php,v 1.6.12 2014/02/23 17:10:00 Logue Exp $
//
// IndexPages plugin: Show a list of page names

use PukiWiki\Auth\Auth;
use PukiWiki\Factory;
use PukiWiki\Listing;
use PukiWiki\Renderer\Header;
use PukiWiki\Time;
use Zend\Json\Json;

function plugin_list_init(){
	$messages = array(
		'_list_messages' => array(
			'title_list' => T_('List of pages'),
			'title_filelist' => T_('List of page files')
		)
	);
	set_plugin_messages($messages);
}

function plugin_list_action()
{
	global $vars, $_list_messages;

	$listcmd = isset($vars['listcmd']) ? $vars['listcmd'] : 'read';
	$type = isset($vars['type']) ? $vars['type'] : '';

	$buffer = array();
	switch($type) {
		case 'json':
			$pages = Listing::pages();
			$headers = Header::getHeaders('application/json');
			// インクリメンタルサーチ向け
			foreach($pages as $page){
				$wiki = Factory::Wiki($page);
				if ($wiki->isHidden()) continue;
				if (isset($vars['term'])){
					if (preg_match('/'.$vars['term'].'/', $page)){
						$buffer[] = $page;
					}
				}else{
					$buffer[] = $page;
				}
			}
			unset($wiki);
			Header::writeResponse($headers, 200, Json::encode($buffer));
			exit;
		case 'sitemap' :
			// サイトマップ
			$pages = Listing::pages();
			$headers = Header::getHeaders('application/xml');
			$buffer[] = '<?xml version="1.0" encoding="UTF-8"?>';
			$buffer[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
			foreach (Listing::pages() as $page){
				
				$wiki = Factory::Wiki($page);
				if ($wiki->isHidden()) continue;
				$buffer[] = '<url>';
				$buffer[] = '<loc>'.$wiki->uri().'</loc>';
				$buffer[] = '<lastmod>'.Time::getZoneTimeDate('Y-m-d\TH:i:s\Z', $wiki->time()).'</lastmod>';
				$buffer[] = '<priority>0.5</priority>';
				$buffer[] = '</url>';
			}
			unset($wiki);
			$buffer[] = '</urlset>';
			Header::writeResponse($headers, 200, join("\n",$buffer));
			exit;
		break;
		case 'filelist' :
			// ファイル一覧
			if (! Auth::check_role('role_contents_admin'))
				$filelist = TRUE;
			else
			if (! pkwk_login($vars['pass']))
				$filelist = FALSE;

			return array(
				'msg'=>$_list_messages['title_filelist'],
				'body'=>FileUtility::getListing(DATA_DIR, 'read', $filelist)
			);
		break;
	}

	return array(
		'msg'=>$_list_messages['title_list'],
		'body'=> Listing::get('wiki', ($listcmd == 'read' || $listcmd == 'edit' ? $listcmd : 'read'))
	);
}
/* End of file list.inc.php */
/* Location: ./wiki-common/plugin/list.inc.php */
