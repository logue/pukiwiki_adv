<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: list.inc.php,v 1.6.11 2011/09/25 15:54:00 Logue Exp $
//
// IndexPages plugin: Show a list of page names

defined('PKWK_SITEMAPS_XML') or define('PKWK_SITEMAPS_XML', 'sitemaps.xml');

function plugin_list_action()
{
	global $vars, $whatsnew;
	$_title_list = T_('List of pages');
	$_title_filelist = T_('List of page files');
	
	$listcmd = isset($vars['listcmd']) ? $vars['listcmd'] : 'read';
	$type = isset($vars['type']) ? $vars['type'] : '';
	$pages = array_diff(auth::get_existpages(), array($whatsnew));

	$buffer = array();
	switch($type) {
		case 'json' :
			if (isset($vars['term'])){
				// 酷い実装だ・・・。
				foreach($pages as $page){
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
			$exists = file_exists(CACHE_DIR.PKWK_SITEMAPS_XML);

			if (!$exists || @filemtime(CACHE_DIR.PKWK_SITEMAPS_XML) < filemtime(CACHE_DIR.PKWK_MAXSHOW_CACHE)){
				$buffer[] = '<?xml version="1.0" encoding="UTF-8"?>';
				$buffer[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
				foreach ($pages as $page){
					$buffer[] = "\t".'<url>';
					$buffer[] = "\t\t".'<loc>'.get_page_uri($page).'</loc>';
					$buffer[] = "\t\t".'<lastmod>'.get_date('Y-m-d\TH:i:s\Z', get_filetime($page)).'</lastmod>';
					$buffer[] = "\t\t".'<priority>0.5</priority>';
					$buffer[] = "\t".'</url>';
				}
				$buffer[] = '</urlset>';
				$data = join("\n",$buffer);
				
				pkwk_touch_file(CACHE_DIR.PKWK_SITEMAPS_XML);
				$fp = fopen(CACHE_DIR.PKWK_SITEMAPS_XML, 'wb');
				if ($fp === false) return false;
				@flock($fp, LOCK_EX);
				rewind($fp);
				fwrite($fp, $data);
				fflush($fp);
				ftruncate($fp, ftell($fp));
				@flock($fp, LOCK_UN);
				fclose($fp);
			}else{
				$fp = fopen(CACHE_DIR.PKWK_SITEMAPS_XML, 'rb');
				if ($fp === false) return array();
				@flock($fp, LOCK_SH);
				$data = fread($fp, filesize(CACHE_DIR.PKWK_SITEMAPS_XML));
				@flock($fp, LOCK_UN);
				if(! fclose($fp)) return array();
			}
				
			header("Content-Type: application/xml; charset=".CONTENT_CHARSET);
			echo $data;
			exit();
		break;
		default:
			// Redirected from filelist plugin?
			$filelist = (isset($vars['cmd']) && $vars['cmd']=='filelist');
			if ($filelist) {
				if (! auth::check_role('role_adm_contents'))
					$filelist = TRUE;
				else
				if (! pkwk_login($vars['pass']))
					$filelist = FALSE;
			}
			
			$cmd = ($listcmd == 'read' || $listcmd == 'edit') ? $listcmd : 'read';
			$ret = page_list($pages,$cmd,$filelist);
		break;
	}

	return array(
		'msg'=>$filelist ? $_title_filelist : $_title_list,
		'body'=>$ret
	);
}
/* End of file list.inc.php */
/* Location: ./wiki-common/plugin/list.inc.php */
