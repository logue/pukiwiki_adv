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

function plugin_suckerfish_convert(){
	global $vars, $hr, $suckerfish_count, $js_tags;
	
	if(!$suckerfish_count){
		$js_tags[] = array('type'=>'text/javascript', 'src'=>SKIN_DIR.'js/plugin/jquery.superfish.js');
		$suckerfish_count++;
	}
	
	$page = strip_bracket($vars['page']);

	$navi_page = plugin_suckerfish_search_navipage($page);
	if (! empty($navi_page)){
		return plugin_suckerfish_makehtml($navi_page);
	}else{
		exist_plugin('navibar');
		return do_plugin_convert('navibar','top,|,edit,freeze,diff,backup,upload,reload,|,new,list,search,recent,help,|,trackback') . $hr;
	}
}

function plugin_suckerfish_search_navipage($page){
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
	return null;
}

function plugin_suckerfish_makehtml($page)
{
	global $vars,$pkwk_dtd;

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
	
//	return '<div id="navigator2">'."\n".'<ul id="'.PLUGIN_SUKERFISH_DEFAULT_ID.'">'."\n".$output.'</ul></div>';
	return (($pkwk_dtd === PKWK_DTD_HTML_5) ? '<nav id="navigator">'.$output.'</nav>'."\n" : '<div id="navigator">'.$output.'</div>')."\n";
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

	// 内部リンクアイコンを削除
	if ( preg_match('#<a class="inn" href="(.*?)" .*?>(.*?)<img src="' . IMAGE_URI . 'iconset/default/symbol/inn.png".*?</a>#si', $conv, $regs) )
		return array( TRUE, $regs[1], $regs[2], str_replace($regs[0], '__sukerfish__', $conv) );
	
	// リンクアイコンを削除
	if ( preg_match('#<a class="ext" href="(.*?)" .*?>(.*?)<img src="' . IMAGE_URI . 'iconset/default/symbol/ext.png".*?</a>#si', $conv, $regs) )
		return array( TRUE, $regs[1], $regs[2], str_replace($regs[0], '__sukerfish__', $conv) );

	// rc, $interurl, $intername, $conv
	return array( FALSE, '', '', $conv );
}

function plugin_suckerfish_keyword($name){
	global $do_backup, $trackback, $referer;
	global $function_freeze;
	global $vars;
	
	// $is_read = (arg_check('read') && is_page($vars['page']));
	$is_read = is_page($vars['page']);
	$is_readonly = auth::check_role('readonly');
	$is_safemode = auth::check_role('safemode');
	$is_createpage = auth::is_check_role(PKWK_CREATE_PAGE);

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
	global $_LINK, $_LANG, $_SKIN, $showicon;

	$lang  = $_LANG['skin'];
	$link  = $_LINK;

//	if (!isset($lang[$key])) { return '<!--LANG NOT FOUND-->'; }
//	if (!isset($link[$key])) { return '<!--LINK NOT FOUND-->'; }
	
	if (!isset($lang[$key])) { return null; }
	if (!isset($link[$key])) { return null; }

	if ($_SKIN['showicon']){
		return '<a href="' . $link[$key] . '" rel="nofollow" class="pkwk-icon_linktext cmd-'.$key.'">' . $lang[$key]. '</a>';
	}else{
		return '<a href="' . $link[$key] . '" rel="nofollow">' . $lang[$key]. '</a>';
	}
}
?>
