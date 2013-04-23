<?php
/**
 :prefix <http://purl.org/net/ns/doas#> .
 :about "<sukerfish.inc.php>", a: ":PHPScript",
 :shortdesc "Suckerfish Popup Menu for PukiWiki";
 :created "2007-05-25", release: {revision: "1.0.7", created: "2011-12-12"},
 :author [:name "Logue"; :homepage <http://logue.be/> ];
 :license <http://www.gnu.org/licenses/gpl-3.0.html>;
*/
use PukiWiki\Auth\Auth;
// $Id: sukerfish.inc.php,v 1.0.7 2011/12/12 22:26:00 Logue Exp $

// Sukerfish Popup Menu Plugin for PukiWiki.
// Copyright (c)2007-2011 Logue <http://logue.be/> All rights reserved.

// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 2 of the License, or
// (at your option) any later version.

// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.

// You should have received a copy of the GNU General Public License along
// with this program; if not, write to the Free Software Foundation, Inc.,
// 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.

use PukiWiki\Factory;
// Default dom id.
define('PLUGIN_SUKERFISH_DEFAULT_ID', 'nav');

function plugin_suckerfish_convert(){
	$navi_page = plugin_suckerfish_search_navipage();
	if (! empty($navi_page)) return plugin_suckerfish_makehtml($navi_page);
}

function plugin_suckerfish_search_navipage(){
	global $navigation, $vars;
	
	if (!$navigation){
		$navigation = 'Navigation';
	}
	$navi_page = '';
	while (1) {
		if (isset($vars['page'])){
			$navi_page = $vars['page'];
			if (! empty($vars['page'])) $navi_page .= '/';
		}
		$navi_page .= $navigation;
		if ( Factory::Wiki($navi_page)->has()) return $navi_page;
		if (empty($page)) break;
		$page = substr($page,0,strrpos($page,'/'));
	}
	return null;
}

function plugin_suckerfish_makehtml($page)
{
	if (empty($page)) return false;
	$wiki = Factory::Wiki($page);
	if (!$wiki->has()) return false;

	$output = '';
	$before_level = 1;
	$loop = 0;
	foreach ($wiki->get(false) as $line) {
		if ($line == '') continue;
		$head = $line{0};
		$level = strspn($line, $head);
		$line = substr($line, $level);
/*
		foreach (0 as $level) {
			$output .= '	';
		}
*/
		if ($head == '-') {
			
			$item = plugin_suckerfish_to_item($line);
			
			if ($item == '') {
				continue;
			} else {
				$item = '<li>' . $item;
				if ($before_level < $level){
					/* 直前のレベルよりも現在のレベルが高いときは、そのまま<ul>タグを開く */
					$item = "\n<ul>\n" . $item;
				}else if ($before_level > $level){
					/* 直前のレベルと現在のレベルの差の分だけ<ul>タグを閉じる */
					$item = "</li>\n</ul></li>\n" . $item;
				}else if ($loop != 0){
					$item = '</li>' ."\n" . $item;
				}
				$output .= $item;
			}

			$before_level = $level;
			$loop++;
		}
	}
	
	if ($level != 1){
		$output .= "</li></ul>\n</li>\n";
	}
	$output = '<ul class="sf-menu">'."\n".$output.'</ul>';
	
	return '<nav id="navigator">'.$output.'</nav>'."\n";
}

function plugin_suckerfish_to_item($line){
	list($rc,$interurl,$intername,$conv) = plugin_suckerfish_convert_html($line);
	$name = trim($line);
	$interkey = plugin_suckerfish_keyword($name);
	if (isset($interkey) && is_string($interkey)) {
		return $interkey;
	}else{
		if ($rc) {
			$rep = '<a href="' . $interurl . '">' . $intername . '</a>';
			return str_replace('__sukerfish__', $rep, $conv);
		}else{
	//		return '<span class="noexists">' . $name . '</span>';
		}
	}
	/*
	if (isset($interkey['url'])) {
		$str = '<a href="' . $interkey['url'] . '" rel="nofollow">' . $interkey['text'] . '</a>';
	}else{
		if ($rc) {
			$rep = '<a href="' . $interurl . '">' . $intername . '</a>';
			$str = str_replace('__sukerfish__', $rep, $conv);
		}else{
			$str = '<span class="noexists">' . $name . '</span>';
		}
	}
	*/

//	return $str;
}

function plugin_suckerfish_convert_html($str){
	
	$conv = preg_replace(
		array("'<p>'si","'</p>'si"),
		array('',''),
		convert_html( array($str) )
	);

	// $regs[0] - HIT Strings
	// $regs[1] - URL String
	// $regs[2] - LinkName
	
	if ( preg_match('#<a href="(.*?)"[^>]*>(.*?)</a>#si', $conv, $regs) )
		return array( TRUE, $regs[1], $regs[2], str_replace($regs[0], '__sukerfish__', $conv) );
/*
	// シンボルアイコンを削除
	if ( preg_match('#<span class="pkwk-symbol (.*?)" .*?></span>#si', $conv, $regs) )
		return array( TRUE, $regs[1], $regs[2], str_replace($regs[0], '__sukerfish__', $conv) );
*/
	// rc, $interurl, $intername, $conv
	return array( FALSE, '', '', $conv );
}

function plugin_suckerfish_keyword($name){
	global $do_backup, $trackback, $referer;
	global $function_freeze;
	global $vars;
	
	// $is_read = (arg_check('read') && is_page($vars['page']));
	$is_read = is_page($vars['page']);
	$is_readonly = Auth::check_role('readonly');
	$is_safemode = Auth::check_role('safemode');
	$is_createpage = Auth::is_check_role(PKWK_CREATE_PAGE);

	$num = func_num_args();
	$args = $num ? func_get_args() : array();

	switch ($name) {
		case 'freeze':
			if ($is_readonly) break;
			if (!$is_read) break;
			if ($function_freeze) {
				if (!is_freeze($vars['page'])) {
					$name = 'freeze';
				} else {
					$name = 'unfreeze';
				}
				return _suckerfish($name);
			}
			break;
		case 'upload':
			if ($is_readonly) break;
			if (!$is_read) break;
			if ($function_freeze && is_freeze($vars['page'])) break;
			if ((bool)ini_get('file_uploads')) {
				return _suckerfish($name);
			}
			break;
		case 'filelist':
			if (arg_check('list')) {
				return _suckerfish($name);
			}
			break;
		case 'backup':
			if ($do_backup) {
				return _suckerfish($name);
			}
			break;
		case 'trackback':
			if ($trackback) {
				$tbcount = tb_count($vars['page']);
				if ($tbcount > 0) {
					return _suckerfish($name);
				} else if (!$is_read) {
					return _suckerfish($name);
				}
			}
			break;
		case 'referer':
			if ($referer) {
				return _suckerfish($name);
			}
			break;
		case 'rss':
		case 'mixirss':
			return _suckerfish($name);
			break;
		case 'diff':
			if (!$is_read) break;
			if ($is_safemode) break;
			return _suckerfish($name);
			break;
		case 'edit':
		case 'guiedit':
			if (!$is_read) break;
			if ($is_readonly) break;
			if ($function_freeze && is_freeze($vars['page'])) break;
			return _suckerfish($name);
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
			return _suckerfish($name);
			break;
	}
	return array();
}

function _suckerfish($key, $val = '')
{
	global $_LINK, $_LANG, $_SKIN;
	if (!isset($_LANG['skin'][$key])) { return null; }
	if (!isset($_LINK[$key])) { return null; }
	$showicon = (isset($_SKIN['showicon'])) ? $_SKIN['showicon'] : false;

	return '<a href="' . $_LINK[$key] . '" rel="nofollow" >'. ($showicon ? '<span class="pkwk-icon icon-'.$key.'"></span>' : '') . $_LANG['skin'][$key]. '</a>';
}
/* End of file suckerfish.inc.php */
/* Location: ./wiki-common/plugin/suckerfish.inc.php */
