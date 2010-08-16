<?php
/**
 :prefix <http://purl.org/net/ns/doas#> .
 :about "<sukerfish.inc.php>", a: ":PHPScript",
 :shortdesc "Suckerfish Popup Menu for PukiWiki";
 :created "2007-05-25", release: {revision: "1.0.2", created: "2010-06-24"},
 :author [:name "Logue"; :homepage <http://logue.be/> ];
 :license <http://www.gnu.org/licenses/gpl-3.0.html>;
*/

// $Id: sukerfish.inc.php,v 1.0.5 2010/08/12 01:55:00 upk Exp $

// Sukerfish Popup Menu Plugin for PukiWiki.
// Copyright (c)2007-2010 Logue <http://logue.be/> All rights reserved.

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


// Default dom id.
define('PLUGIN_SUKERFISH_DEFAULT_ID', 'nav');

function plugin_suckerfish_convert()
{
	global $vars, $hr, $head_tags;

	$page = strip_bracket($vars['page']);

	$navi_page = plugin_suckerfish_search_navipage($page);
	if (! empty($navi_page)){
		return plugin_suckerfish_makehtml($navi_page);
	}else{
		exist_plugin('navibar');
		return do_plugin_convert('navibar','top,|,edit,freeze,diff,backup,upload,reload,|,new,list,search,recent,help,|,trackback') . $hr;
	}
}

function plugin_suckerfish_search_navipage($page)
{
	global $navigation;
	
	if (!$navigation){
		$navigation = 'Navigation';
	}
	while (1) {
		$navi_page = $page;
		if (! empty($page)) $navi_page .= '/';
		$navi_page .= $navigation;
		if (is_page($navi_page)) return $navi_page;
		if (empty($page)) break;
		$page = substr($page,0,strrpos($page,'/'));
	}
	return '';
}

function plugin_suckerfish_makehtml($page)
{
	global $vars;

	$lines = get_source($page);
	convert_html( $lines ); // Processing for prior execution of plug-in.

	$output = '';
	$before_level = 1;
	$loop = 0;
	foreach ($lines as $line) {
		if ($line == '') continue;
		$head = $line{0};
		$level = strspn($line, $head);
		$line = substr($line, $level);

		foreach (0 as $level) {
			$output .= '	';
		}
		if ($head == '-') {
			
			$item = plugin_suckerfish_to_item($line);
			
			if ($item == '') {
				continue;
			} else {
				$item = '	<li>' . $item;
				if ($before_level < $level){
					/* 直前のレベルよりも現在のレベルが高いときは、そのまま<ul>タグを開く */
					$item = "\n	<ul>\n" . $item;
				}else if ($before_level > $level){
					/* 直前のレベルと現在のレベルの差の分だけ<ul>タグを閉じる */
					$item = "</li>\n</ul>\n</li>\n" . $item;
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
	
	return '<div id="navigator2"><ul id="'.PLUGIN_SUKERFISH_DEFAULT_ID.'">'.$output.'</ul></div>';
}

function plugin_suckerfish_to_item($line)
{
	list($rc,$interurl,$intername,$conv) = plugin_suckerfish_convert_html($line);
	$name = trim($line);
	$interkey = plugin_suckerfish_keyword($name);
	if (isset($interkey['url'])) {
		$str = '<a href="' . $interkey['url'] . '" rel="nofollow">' . $interkey['img'] . $interkey['text'] . '</a>';
	}else{
		if ($rc) {
			$rep = '<a href="' . $interurl . '">' . $intername . '</a>';
			$str = str_replace('__sukerfish__', $rep, $conv);
//		}else{
//			$str = '<span class="noexists">' . $name . '</span>';
		}
	}

	return $str;
}

function plugin_suckerfish_convert_html($str)
{
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

	if ( preg_match('#<a class="inn" href="(.*?)" .*?>(.*?)<img src="' . IMAGE_URI . 'plus/inn.png".*?</a>#si', $conv, $regs) )
		return array( TRUE, $regs[1], $regs[2], str_replace($regs[0], '__sukerfish__', $conv) );
	
	if ( preg_match('#<a class="ext" href="(.*?)" .*?>(.*?)<img src="' . IMAGE_URI . 'plus/ext.png".*?</a>#si', $conv, $regs) )
		return array( TRUE, $regs[1], $regs[2], str_replace($regs[0], '__sukerfish__', $conv) );

	// rc, $interurl, $intername, $conv
	return array( FALSE, '', '', $conv );
}

function plugin_suckerfish_keyword($name)
{
	global $_LINK;
	global $do_backup, $trackback, $referer;
	global $function_freeze;
	global $vars;
	global $whatsnew,$whatsdeleted;

	if ($_LINK['reload'] == '') return array();

	$_page  = isset($vars['page']) ? $vars['page'] : '';
	$is_read = (arg_check('read') && is_page($_page));
	$is_freeze = is_freeze($_page);

	switch ($name) {
	case 'edit':
	case 'guiedit':
	case 'add':
		if ($is_read && !$is_freeze) return _suckerfish($name);
		break;
	case 'freeze':
	case 'unfreeze':
		if ($is_read && $function_freeze) {
			$name = $is_freeze ? 'unfreeze' : 'freeze';
			return _suckerfish($name);
		}
		break;
	case 'upload':
		if ($is_read && (bool)ini_get('file_uploads') && !$is_freeze && !($_page == $whatsnew || $_page == $whatsdeleted)) return _suckerfish($name);
		break;
	case 'filelist':
		if (arg_check('list') && (bool)ini_get('file_uploads')) return _suckerfish($name);
		break;
	case 'backup':
		if ($do_backup) return _suckerfish($name);
		break;
	case 'brokenlink':
	case 'template':
	case 'source':
		if (!empty($_page)) return _suckerfish($name);
		break;
	case 'trackback':
		if ($trackback) {
			$tbcount = tb_count($_page);
			
			if ($vars['cmd'] == 'list') {
				return _suckerfish($name, 'Trackback list');
			}else{
				return _suckerfish($name, 'Trackback(' . $tbcount . ')');
			}
		}
		break;
	case 'refer':
	case 'skeylist':
	case 'linklist':
		if ($referer) {
			if (!isset($refcount)) $refcount = tb_count($_page,'.ref');
			if ($refcount > 0) return _suckerfish($name);
		}
		break;
	case 'log_login':
		if (log_exist('login',$_page)) return  _suckerfish($name);
		break;
	case 'log_check':
		if (log_exist('check',$_page)) return _suckerfish($name);
		break;
	case 'log_browse':
		if (log_exist('browse',$_page)) return _suckerfish($name);
		break;
	case 'log_update':
		if (log_exist('update',$_page)) return _suckerfish($name);
		break;
	case 'log_down':
		if (log_exist('download',$_page)) return _suckerfish($name);	
		break;
	// case 'new':
	case 'newsub':
	case 'edit':
	case 'guiedit':
	case 'diff':
		if (!$is_read) break;
	default:
		return _suckerfish($name);
		break;
	}
	return array();
}

function _suckerfish($key, $val = '', $x = 20, $y = 20)
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
