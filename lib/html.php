<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone
// $Id: html.php,v 1.65.42 2011/02/06 13:42:00 Logue Exp $
// Copyright (C)
//   2010-2011 PukiWiki Advance Developers Team <http://pukiwiki.logue.be/>
//   2005-2009 PukiWiki Plus! Team <http://pukiwiki.cafelounge.net/plus/>
//   2002-2007 PukiWiki Developers Team <http://pukiwiki.sourceforge.jp/>
//   2001-2002 Originally written by yu-ji <http://www.hyuki.com/yukiwiki/>
// License: GPL v2 or (at your option) any later version
//
// HTML-publishing related functions
// Plus!NOTE:(policy)not merge official cvs(1.49->1.54)
// Plus!NOTE:(policy)not merge official cvs(1.58->1.59) See Question/181

// Show page-content
function catbody($title, $page, $body)
{
	global $script; // MUST BE SKIN.FILE. Do not delete line.
	global $vars, $arg, $help_page, $hr, $JSON;
	
	global $function_freeze;
	global $search_word_color, $foot_explain, $note_hr;
	
	global $newtitle, $newbase, $language, $use_local_time, $session; // Plus! skin extension
	global $nofollow;
	global $_LANG, $_LINK, $_SKIN;

	global $do_backup;		// Do backup or not
	global $modifier;		// Site administrator's  web page
	global $modifierlink;	// Site administrator's name

	global $_string, $always_menu_displayed;
	
	global $_page, $is_page, $is_read, $is_freeze, $is_readonly, $is_safemode, $is_createpage,$lastmod;

	$_page  = isset($vars['page']) ? $vars['page'] : '';
	$filetime = ($_page !== '') ? get_filetime($_page) : 0; 
	
	// Init flags
	$is_page = (is_pagename($_page) && ! arg_check('backup') && ! is_cantedit($_page));
	$is_read = (arg_check('read') && is_page($_page));
	$is_freeze = is_freeze($_page);
	$is_readonly = auth::check_role('readonly');
	$is_safemode = auth::check_role('safemode');
	$is_createpage = auth::is_check_role(PKWK_CREATE_PAGE);

	if ($lastmod && $is_read){
		pkwk_common_headers($filetime);
	}else{
		pkwk_common_headers();
	}

	if (IS_AJAX || isset($JSON)){
		// JSONで出力
		if (!isset($JSON)){	// $JSON関数が定義されていない場合
			$JSON = array(
				'title'			=> $title,
				'body'			=> $body,
/*
				'is_read'		=> $is_read,
				'is_freeze'		=> $is_freeze,
				'is_page'		=> $is_page,
				'lastmodified'	=> $filetime,
				'page'			=> $_page,
				'taketime'		=> elapsedtime()
*/
			);
		}
		header('Content-Type: application/json; charset=' . CONTENT_CHARSET);
		echo json_encode($JSON);
	}else{
		// Set $_LINK for skin
		$_LINK = getLinkSet($_page);

		// スキン出力
		global $pkwk_dtd, $x_ua_compatible;	// HTML5, XHTML 1.1, XHTML1.0...
		global $page_title;		// Title of this site
	
		global $head_tags, $foot_tags;	// Obsolete
		global $meta_tags, $link_tags, $js_tags, $js_blocks, $css_blocks, $info, $js_init, $js_vars, $modernizr;
		global $keywords, $description, $pkwk_head_js, $google_loader, $ui_theme;
		
		global $google_analytics, $google_api_key, $google_site_verification, $yahoo_site_explorer_id, $bing_webmaster_tool;

		/* Adv. ここから。（あまりいい実装ではない） */
		global $adminpass;
		if ($adminpass == '{x-php-md5}1a1dc91c907325c69271ddf0c944bc72' || $adminpass == ''){
			$body = '<div class="message_box ui-state-error ui-corner-all">'.
				'<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: 0.3em;"></span>'.
				'<strong>'.$_string['warning'].'</strong> '.$_string['changeadminpass'].'</p></div>'."\n".
				$body;
		}
		
		$meta_tags[] = array('name' => 'generator',	'content' => strip_tags(GENERATOR));
		$meta_tags[] = array('name' => 'viewport',	'content' => (isset($viewport) ? $viewport : 'width=device-width; initial-scale=1.0; maximum-scale=1.0;'));
		($modifier !== 'anonymous') ?			$meta_tags[] = array('name' => 'author',					'content' => $modifier) : '';
		(!empty($google_site_verification)) ?	$meta_tags[] = array('name' => 'google-site-verification',	'content' => $google_site_verification) : '';
		(!empty($yahoo_site_explorer_id)) ?		$meta_tags[] = array('name' => 'y_key',						'content' => $yahoo_site_explorer_id) : '';
		(!empty($bing_webmaster_tool)) ?		$meta_tags[] = array('name' => 'msvalidate.01',				'content' => $bing_webmaster_tool) : '';

		if (!isset($shortcut_icon)){ $shortcut_icon = ROOT_URI.'favicon.ico'; }
		
		// Linkタグの生成。scriptタグと異なり、順番が変わっても処理への影響がない。
		// http://www.w3schools.com/html5/tag_link.asp
		$link_tags[] = array('rel'=>'alternate',		'href'=>$_LINK['mixirss'],	'type'=>'application/rss+xml',	'title'=>'RSS');
		$link_tags[] = array('rel'=>'archive',			'href'=>$_LINK['backup'],	'type'=>'text/html',	'title'=>$_LANG['skin']['backup']);
			// see http://www.seomoz.org/blog/canonical-url-tag-the-most-important-advancement-in-seo-practices-since-sitemaps
		$link_tags[] = array('rel'=>'canonical',		'href'=>$_LINK['reload'],	'type'=>'text/html',	'title'=>$_page);
		$link_tags[] = array('rel'=>'contents',			'href'=>$_LINK['menu'],		'type'=>'text/html',	'title'=>$_LANG['skin']['menu']);
		$link_tags[] = array('rel'=>'sidebar',			'href'=>$_LINK['side'],		'type'=>'text/html',	'title'=>$_LANG['skin']['side']);
		$link_tags[] = array('rel'=>'glossary',			'href'=>$_LINK['glossary'],	'type'=>'text/html',	'title'=>$_LANG['skin']['glossary']);
		$link_tags[] = array('rel'=>'help',				'href'=>$_LINK['help'],		'type'=>'text/html',	'title'=>$_LANG['skin']['help']);
		$link_tags[] = array('rel'=>'first',			'href'=>$_LINK['top'],		'type'=>'text/html',	'title'=>$_LANG['skin']['top']);
		$link_tags[] = array('rel'=>'index',			'href'=>$_LINK['list'],		'type'=>'text/html',	'title'=>$_LANG['skin']['list']);
		$link_tags[] = array('rel'=>'search',			'href'=>$_LINK['search'],	'type'=>'text/html',	'title'=>$_LANG['skin']['search']);
		$link_tags[] = array('rel'=>'shortcut icon',	'href'=>$shortcut_icon,		'type'=>'image/vnd.microsoft.icon');

		if ($nofollow || ! $is_read || ! $is_page){
			$meta_tags[] = array('name' => 'robots', 'content' => 'NOINDEX,NOFOLLOW');
		}else{
//			if (empty($description)){ $description = $title.' - '.$page_title; }
			$meta_tags[] = array('name' => 'description', 'content' => $description);
//			if (empty($keywords)) $keywords = 'PukiWiki, PHP, ajax, Advance, CMS, Wiki, Adv., PukiWiki Adv.,PukiWiki Advance';
			$meta_tags[] = array('name' => 'keywords', 'content' => $keywords);
		}
		
//		if ($notify_from !== 'from@example.com') $link_tags[] = array('rev'=>'made',	'href'=>'mailto:'.$notify_from,	'title'=>	'Contact to '.$modifier);

		// JavaScriptタグの組み立て
		$js_init['PAGE']= rawurlencode($_page);
		$js_init['MODIFIED']= $filetime;
		if(isset($google_analytics)){ $js_init['GOOGLE_ANALYTICS'] = $google_analytics; }
		
		// application/xhtml+xml を認識するブラウザではXHTMLとして出力
		if (PKWK_STRICT_XHTML === TRUE && strstr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml') !== false){
			$http_header = 'application/xhtml+xml';
		}else{
			$http_header = 'text/html';
		}

		// JSに渡す定義を展開
		foreach( $js_init as $key=>$val){
			if ($val !== ''){
				$js_vars[] = 'var '.$key.' = "'.$val.'";';
			}
		}
		array_unshift($pkwk_head_js,array('type'=>'text/javascript', 'content'=>join($js_vars,"\n")));
		array_unshift($pkwk_head_js,array('type'=>'text/javascript', 'src'=>'http://www.google.com/jsapi'.((isset($google_api_key)) ? '?key='.$google_api_key : '')));
		unset($js_var, $key, $val);
		/* ヘッダー部分の処理ここまで */
	
		/* ヘッダー部のタグ */
		$pkwk_head = tag_helper('meta',$meta_tags)."\t\t".tag_helper('link',$link_tags);
		
		if (!empty($css_blocks)){
			$pkwk_head .= "\t\t".tag_helper('style',array(array('type'=>'text/css', 'content'=>join("\n",$css_blocks))));
		}
		// Modernizrは、ヘッダー内にないと正常に動作しない
		$pkwk_head .= "\t\t".'<script type="text/javascript" src="'.SKIN_URI.'js/'.$modernizr.'"></script>'."\n";
		
		/* フッター部のタグ */
		$pkwk_tags = tag_helper('script',$pkwk_head_js)."\t\t".tag_helper('script',$js_tags);
		$pkwk_tags .= (!empty($js_blocks)) ? "\t\t".tag_helper('script',array(array('type'=>'text/javascript', 'content'=>join("\n",$js_blocks)))) : '';

		/* 非推奨要素の警告 */
		if (! empty($head_tags)){
			$pkwk_head .= join("\n", $head_tags) ."\n";
			$info[] = '<var>$head_tags</var> is obsolate. Use $meta_tags, $link_tags, $js_tags, $js_blocks, $css_blocks.';
		}
		if (! empty($foot_tags)){
			$pkwk_tags .= join("\n", $foot_tags) ."\n";
			$info[] = '<var>$foot_tags</var> is obsolate. Use $meta_tags, $link_tags, $js_tags, $js_blocks, $css_blocks.';
		}
		
		/* Adv.ここまで */
		
		// Last modification date (string) of the page
		if ($is_read){
			global $attach_link, $related_link;
			
			$lastmodified = get_date('D, d M Y H:i:s T', $filetime). ' ' . get_pg_passage($_page, FALSE);
			if ($pkwk_dtd == PKWK_DTD_HTML_5) {
				$lastmodified = '<time pubdate="pubdate" datetime="'.get_date('c',$filetime).'">'.$lastmodified.'</time>';
			}

			// List of attached files to the page
			$attaches = ($attach_link && (exist_plugin('attach') && do_plugin_init('attach') !== FALSE)) ? attach_filelist() : '';

			$related = ($related_link && (exist_plugin('related') && do_plugin_init('related') !== FALSE)) ? make_related($_page,'dl') : '';

			// List of footnotes
			ksort($foot_explain, SORT_NUMERIC);
			$notes = ! empty($foot_explain) ? '<ul>'.join("\n", $foot_explain).'</ul>' : '';
		}

		// Search words
		if ($search_word_color && isset($vars['word'])) {
			
			$body = '<div class="small">' . $_string['word'] . htmlsc($vars['word']) .
				'</div>' . $hr . "\n" . $body;

			// BugTrack2/106: Only variables can be passed by reference from PHP 5.0.5
			$words = preg_split('/\s+/', $vars['word'], -1, PREG_SPLIT_NO_EMPTY);
			$words = array_splice($words, 0, 10); // Max: 10 words
			$words = array_flip($words);

			$keys = array();
			foreach ($words as $word=>$id) $keys[$word] = strlen($word);
			arsort($keys, SORT_NUMERIC);
			$keys = get_search_words(array_keys($keys), TRUE);
			$id = 0;
			foreach ($keys as $key=>$pattern) {
				$s_key    = htmlsc($key);
				$pattern  = '/' .
					'<textarea[^>]*>.*?<\/textarea>' .	// Ignore textareas
					'|' . '<[^>]*>' .			// Ignore tags
					'|' . '&[^;]+;' .			// Ignore entities
					'|' . '(' . $pattern . ')' .		// $matches[1]: Regex for a search word
					'/sS';
					$decorate_Nth_word = create_function(
						'$matches',
						'return (isset($matches[1])) ? ' .
							'\'<'.(($pkwk_dtd == PKWK_DTD_HTML_5) ? 'mark' : 'strong').' class="word' .
								$id .
							'">\' . $matches[1] . \'</'.(($pkwk_dtd == PKWK_DTD_HTML_5) ? 'mark' : 'strong').'>\' : ' .
							'$matches[0];'
					);
				$body  = preg_replace_callback($pattern, $decorate_Nth_word, $body);
				$notes = isset($notes) ? preg_replace_callback($pattern, $decorate_Nth_word, $notes) : null;
				++$id;
			}
		}
		
		if (DEBUG === true && ! empty($info)){
			$body = '<div class="message_box ui-state-highlight ui-corner-all">'.
					'<p><span class="ui-icon ui-icon-info"></span>'.$_string['debugmode'].'</p>'."\n".
					'<ul>'."\n".
					'<li>'.join("</li>\n<li>",$info).'</li>'."\n".
					'</ul></div>'."\n\n".$body;
		}

/*
		// global $always_menu_displayed;
		$always_menu_displayed = (arg_check('read')) ? true : false;
		$body_menu = $body_side = '';
		if ($always_menu_displayed) {
			if (exist_plugin_convert('menu')) $body_menu = do_plugin_convert('menu');
			if (exist_plugin_convert('side')) $body_side = do_plugin_convert('side');
		}
*/
		header('Content-Type: '.$http_header.'; charset='. CONTENT_CHARSET);
		header('X-UA-Compatible: '.(empty($x_ua_compatible)) ? 'IE=edge' : $x_ua_compatible);	// とりあえずIE8対策
		require(SKIN_FILE);
	}

	global $ob_flag;
	if($ob_flag){
		ob_end_flush();
	}

	exit;
}

function getLinkSet($_page){
	global $defaultpage, $whatsnew, $whatsdeleted, $interwiki, $aliaspage, $glossarypage;
	global $menubar, $sidebar, $navigation, $headarea, $footarea, $protect;
	
	global $trackback, $referer;

	// Set $_LINK for skin
	$_LINK = array(
		'add'			=> get_cmd_uri('add',			$_page),
		'backup'		=> get_cmd_uri('backup',		$_page),
		'brokenlink'	=> get_cmd_uri('brokenlink',	$_page),
		'copy'			=> get_cmd_uri('template',		'',		'',	array('refer'=>$_page)),
		'diff'			=> get_cmd_uri('diff',			$_page),
		'edit'			=> get_cmd_uri('edit',			$_page),
		'filelist'		=> get_cmd_uri('filelist'),
		'freeze'		=> get_cmd_uri('freeze',		$_page),
		'full'			=> get_cmd_uri('print',			$_page).'&amp;nohead&amp;nofoot',
		'guiedit'		=> get_cmd_uri('guiedit',		$_page),
		'help'			=> get_cmd_uri('help'),
		
		'list'			=> get_cmd_uri('list'),
		'log_browse'	=> get_cmd_uri('logview',		$_page,	'',	array('kind'=>'browse')),
		'log_check'		=> get_cmd_uri('logview',		$_page,	'',	array('kind'=>'check')),
		'log_down'		=> get_cmd_uri('logview',		$_page,	'',	array('kind'=>'download')),
		'log_login'		=> get_cmd_uri('logview',		'',		'',	array('kind'=>'login')),
		'log_update'	=> get_cmd_uri('logview',		$_page),
		'new'			=> get_cmd_uri('newpage',		'',		'',	array('refer'=>$_page)),
		'newsub'		=> get_cmd_uri('newpage_subdir','',		'',	array('directory'=>$_page)),
		'print'			=> get_cmd_uri('print',			$_page),
		'rename'		=> get_cmd_uri('rename',		'',		'',	array('refer'=>$_page)),
		'search'		=> get_cmd_uri('search'),
		'source'		=> get_cmd_uri('source',		$_page),
		'template'		=> get_cmd_uri('template',		'',		'',	array('refer'=>$_page)),
		'unfreeze'		=> get_cmd_uri('unfreeze',		$_page),
		'upload'		=> get_cmd_uri('attach',		$_page,	'',	array('pcmd'=>'upload')), // link rel="alternate" にも利用するため absuri にしておく
		
		'rss'			=> get_cmd_absuri('rss'),
		'rdf'			=> get_cmd_absuri('rss',		'',		'ver=1.0'),
		'rss10'			=> get_cmd_absuri('rss',		'',		'ver=1.0'), // Same as 'rdf'
		'rss20'			=> get_cmd_absuri('rss',		'',		'ver=2.0'),
		'mixirss'		=> get_cmd_absuri('mixirss'), // Same as 'rdf' for mixi
		
		'read'			=> get_page_uri($_page),
		'reload'		=> get_page_absuri($_page), // 本当は、get_script_uri でいいけど、絶対パスでないと、スキンに影響が出る
		'reload_rel'	=> get_page_uri($_page),

		/* Special Page */
		'top'			=> get_page_uri($defaultpage),
		'recent'		=> get_page_uri($whatsnew),
		'deleted'		=> get_page_uri($whatsdeleted),
		'interwiki'		=> get_page_uri($interwiki),
		'alias'			=> get_page_uri($aliaspage),
		'glossary'		=> get_page_uri($glossarypage),
		'menu'			=> get_page_uri($menubar),
		'side'			=> get_page_uri($sidebar),
		'navigation'	=> get_page_uri($navigation),
		'head'			=> get_page_uri($headarea),
		'foot'			=> get_page_uri($footarea),
		'protect'		=> get_page_uri($protect)
	);
	
	if ($referer){
		$_LINK['referer']	= get_cmd_uri('referer',		$_page);
		$_LINK['linklist']	= get_cmd_uri('linklist',		$_page);
		$_LINK['skeylist']	= get_cmd_uri('skeylist',		$_page);
	}
	if ($trackback){
		$_LINK['trackback'] = get_cmd_uri('tb','','',array('__mode'=>'view','tb_id'=>tb_get_id($_page)));
	}
	return $_LINK;
}

// Show 'edit' form
function edit_form($page, $postdata, $digest = FALSE, $b_template = TRUE)
{
	global $script, $vars, $rows, $cols, $hr, $function_freeze;
	global $load_template_func, $load_refer_related;
	global $notimeupdate;
	global $_button, $_string;
	
//	global $x_ua_compatible;

	// Newly generate $digest or not
	if ($digest === FALSE) $digest = md5(get_source($page, TRUE, TRUE));

	$refer = $template = $addtag = $add_top = $add_ajax = '';

	$checked_top  = isset($vars['add_top'])     ? ' checked="checked"' : '';
	$checked_time = isset($vars['notimestamp']) ? ' checked="checked"' : '';

	if(isset($vars['add'])) {
		$addtag  = '<input type="hidden" name="add" value="true" />';
		$add_top = '<input type="checkbox" name="add_top" value="true"' .
			$checked_top . ' /><span class="small">' .
			$_button['addtop'] . '</span>';
	}

	if($load_template_func && $b_template) {
		$pages  = array();
		foreach(auth::get_existpages() as $_page) {
			if (is_cantedit($_page) || check_non_list($_page))
				continue;
			$s_page = htmlsc($_page);
			$pages[$_page] = '		<option value="' . $s_page . '">' .$s_page . '</option>'."\n";
		}
		ksort($pages, SORT_STRING);
		$s_pages  = join("\n", $pages);
		$template = <<<EOD
<div class="template_form">
	<select name="template_page" class="template">
		<option value="" disabled="disabled" selected="selected">-- {$_button['template']} --</option>
$s_pages
	</select>
	<input type="submit" name="template" value="{$_button['load']}" accesskey="r" />
</div>

EOD;
		if ($load_refer_related) {
			if (isset($vars['refer']) && $vars['refer'] != '')
				$refer = '[[' . strip_bracket($vars['refer']) . ']]' . "\n\n";
		}
	}

	$r_page      = rawurlencode($page);
	$s_page      = htmlsc($page);
	$s_digest    = htmlsc($digest);
	$s_postdata  = htmlsc($refer . $postdata);
	$s_original  = isset($vars['original']) ? htmlsc($vars['original']) : $s_postdata;
	$s_id        = isset($vars['id']) ? htmlsc($vars['id']) : '';
	$b_preview   = isset($vars['preview']); // TRUE when preview
	$s_ticket    = md5(MUTIME);

	if (function_exists('pkwk_session_start') && pkwk_session_start() != 0) {
		// BugTrack/95 fix Problem: browser RSS request with session
		$_SESSION[$s_ticket] = md5(get_ticket() . $digest);
		$_SESSION['origin' . $s_ticket] = md5(get_ticket() . str_replace("\r", '', $s_original));
	}

	$add_notimestamp = '';
	if ($notimeupdate != 0 && is_page($page)) {
		// enable 'do not change timestamp'
		$add_notimestamp = <<<EOD
	<input type="checkbox" name="notimestamp" id="_edit_form_notimestamp" value="true"$checked_time />
	<label for="_edit_form_notimestamp" class="small">{$_button['notchangetimestamp']}</label>
EOD;
		if ($notimeupdate == 2 && auth::check_role('role_adm_contents')) {
			// enable only administrator
			$add_notimestamp .= '<input type="password" name="pass" size="12" />';
		}
	}
	$refpage = isset($vars['refpage']) ? htmlsc($vars['refpage']) : '';

	$body = <<<EOD
<form action="$script" method="post" id="form">
	<input type="hidden" name="cmd"    value="edit" />
	<input type="hidden" name="page"   value="$s_page" />
	<input type="hidden" name="digest" value="$s_digest" />
	<input type="hidden" name="ticket" value="$s_ticket" />
	<input type="hidden" name="id"     value="$s_id" />
	<textarea id="original" name="original" rows="1" cols="1" style="display:none">$s_original</textarea>
	<div class="edit_form">
$template
$addtag
		<textarea name="msg" id="msg" rows="$rows" cols="$cols">$s_postdata</textarea>
		<input type="submit" id="btn_submit" name="write" value="{$_button['update']}" accesskey="s" />
		$add_top
		<input type="submit" id="btn_preview" name="preview" value="{$_button['preview']}" accesskey="p" />
		$add_notimestamp
		<input type="submit" id="btn_cancel" name="cancel" value="{$_button['cancel']}" accesskey="c" />
	</div>
</form>

EOD;
	if (isset($vars['help'])) {
		$body .= $hr . catrule();
	} else {
		$body .= '<ul><li><a href="'.get_cmd_uri('edit',$r_page,'','help=true').'">' . $_string['help'] . '</a></li></ul>';
	}
	return $body;
}

// make related() moved to related.inc.php

// User-defined rules (convert without replacing source)
function make_line_rules($str){
	global $line_rules;
	static $pattern, $replace;

	if (! isset($pattern)) {
		$pattern = array_map(create_function('$a',
			'return \'/\' . $a . \'/\';'), array_keys($line_rules));
		$replace = array_values($line_rules);
		unset($line_rules);
	}

	return preg_replace($pattern, $replace, $str);
}

// Remove all HTML tags(or just anchor tags), and WikiName-speific decorations
function strip_htmltag($str, $all = TRUE)
{
	global $_symbol_noexists;
	static $noexists_pattern;

	if (! isset($noexists_pattern))
		$noexists_pattern = '#<span class="noexists">([^<]*)<a[^>]+>' .
			preg_quote($_symbol_noexists, '#') . '</a></span>#';

	// Strip Dagnling-Link decoration (Tags and "$_symbol_noexists")
	$str = preg_replace($noexists_pattern, '$1', $str);

	if ($all) {
		// All other HTML tags
		return preg_replace('#<[^>]+>#', '', $str);
	} else {
		// All other anchor-tags only
		return preg_replace('#<a[^>]+>|</a>#i', '', $str);
	}
}

// Remove AutoLink marker with AutoLink itself
function strip_autolink($str)
{
	return preg_replace('#<!--autolink--><a [^>]+>|</a><!--/autolink-->#', '', $str);
}

// Make a backlink. searching-link of the page name, by the page name, for the page name
function make_search($page)
{
	return '<a href="' . get_cmd_uri('related',$page) . '">' . htmlsc($page) . '</a> ';
}

// Make heading string (remove heading-related decorations from Wiki text)
function make_heading(& $str, $strip = TRUE)
{
	global $NotePattern;

	// Cut fixed-heading anchors
	$id = '';
	$matches = array();
	if (preg_match('/^(\*{0,3})(.*?)\[#([A-Za-z][\w-]+)\](.*?)$/m', $str, $matches)) {
		$str = $matches[2] . $matches[4];
		$id  = & $matches[3];
	} else {
		$str = preg_replace('/^\*{0,3}/', '', $str);
	}

	// Cut footnotes and tags
	if ($strip === TRUE)
		$str = strip_htmltag(make_link(preg_replace($NotePattern, '', $str)));

	return $id;
}

// Separate a page-name(or URL or null string) and an anchor
// (last one standing) without sharp
function anchor_explode($page, $strict_editable = FALSE)
{
	$pos = strrpos($page, '#');
	if ($pos === FALSE) return array($page, '', FALSE);

	// Ignore the last sharp letter
	if ($pos + 1 == strlen($page)) {
		$pos = strpos(substr($page, $pos + 1), '#');
		if ($pos === FALSE) return array($page, '', FALSE);
	}

	$s_page = substr($page, 0, $pos);
	$anchor = substr($page, $pos + 1);

	if ($strict_editable === TRUE &&  preg_match('/^[a-z][a-f0-9]{7}$/', $anchor)) {
		return array ($s_page, $anchor, TRUE); // Seems fixed-anchor
	} else {
		return array ($s_page, $anchor, FALSE);
	}
}

// Check HTTP header()s were sent already, or
// there're blank lines or something out of php blocks
function pkwk_headers_sent()
{
	if (PKWK_OPTIMISE) return;

	$file = $line = '';

	if (headers_sent($file, $line)){
		die_message(sprintf(T_('Headers already sent at %s, line: %s.'),'<var>'.htmlsc($file).'</var>','<var>'.$line.'</var>'));
	}
}

/**
	@brief Output common HTTP headers

	@param modified 最終更新日時（秒）
	@param expire 有効期限（秒）
	@param compress 圧縮をするかしないか（refなどで二重圧縮されるのを防ぐ）
	@return なし
*/
function pkwk_common_headers($modified = 0, $expire = 0, $compress = true){
	global $lastmod, $vars;
	if (! PKWK_OPTIMISE) pkwk_headers_sent();

	if ($modified !== 0){
		// 最終更新日（秒で）が指定されていない場合動的なページとみなす。
		// PHPで条件付きGETとかEtagとかでパフォーマンス向上
		// http://firegoby.theta.ne.jp/archives/1730
		$last_modified = gmdate('D, d M Y H:i:s T', $modified);
		$etag = md5($last_modified);
		
		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ) {
			if ($_SERVER['HTTP_IF_MODIFIED_SINCE'] == $last_modified) {
				header('HTTP/1.1 304 Not Modified');
				exit;
			}
		}
		if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
			if (preg_match("/{$etag}/", $_SERVER['HTTP_IF_NONE_MATCH'])) {
				header('HTTP/1.1 304 Not Modified');
				exit;
			}
		}
		
		header('ETag: "'.$etag.'"');
		header('Last-Modified: ' . $last_modified );
//		header('If-Modified-Since: ' . $last_modified );
		header('Cache-control: must-revalidate; max-age=60');
		header('Expires: '.gmdate('D, d M Y H:i:s', time() + $expire).' GMT');
	}else{
		// PHPで動的に生成されるページはキャシュすべきではない
		header('Cache-Control: no-cache');
		header('Pragma: no-cache');
		header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
	}

	$vary = get_language_header_vary();
	if(PKWK_ZLIB_LOADABLE_MODULE === true && $compress !== false) {
		$matches = array();
		// どうも、ob_gzhandler関連は動作が不安定だ・・・。
/*
		if(extension_loaded('zlib') && 
			ob_get_length() === FALSE && 
			!ini_get('zlib.output_compression') && 
			ini_get('output_handler') !== 'ob_gzhandler' && 
			ini_get('output_handler') !== 'mb_output_handler'){	// mb_output_handlerとかち合うらしいので、その場合は弾く。http://pukiwiki.sourceforge.jp/dev/?%BB%A8%C3%CC%2F11
			
			global $ob_flag;
			$ob_flag = false;
			
			// http://jp.php.net/manual/ja/function.ob-gzhandler.php
			ob_start('ob_gzhandler');
			$ob_flag = true;
		}else
*/
		if(ini_get('zlib.output_compression') &&
			preg_match('/\b(gzip|deflate)\b/i', $_SERVER['HTTP_ACCEPT_ENCODING'], $matches)) {
			// Bug #29350 output_compression compresses everything _without header_ as loadable module
			// http://bugs.php.net/bug.php?id=29350
			header('Content-Encoding: ' . $matches[1]);
			$vary .= ', Accept-Encoding';
		}
	}
	
	// RFC2616
	header('Vary: '.$vary);
	header('Access-Control-Allow-Origin: '.get_script_uri());	// JSON脆弱性対策（Adv.では外部にAjax APIを提供することを考慮しない）
	header('X-XSS-Protection: '.((DEBUG) ? '0' :'1;mode=block') );	// XSS脆弱性対策（これでいいのか？）
	header('X-Content-Type-Options: nosniff');	// IEの自動MIME type判別機能を無効化する
	header('X-Frame-Options: SameDomain');	// クリックジャッキング対策
}

//////////////////////////////////////////////////
// DTD definitions
// Adv. does not support HTML4.x

define('PKWK_DTD_HTML_5',                 50); // HTML5(XHTML5)
define('PKWK_DTD_XHTML_1_1',              17); // Strict only
define('PKWK_DTD_XHTML_1_0',              16); // Strict
define('PKWK_DTD_XHTML_1_0_STRICT',       16);
define('PKWK_DTD_XHTML_1_0_TRANSITIONAL', 15);
define('PKWK_DTD_XHTML_1_0_FRAMESET',     14);
define('PKWK_DTD_XHTML_BASIC_1_0',        11);

define('PKWK_DTD_TYPE_XHTML',        1);
define('PKWK_DTD_TYPE_HTML',         0);

// Output HTML DTD, <html> start tag. Return content-type.
function pkwk_output_dtd($pkwk_dtd = PKWK_DTD_HTML_5, $charset = CONTENT_CHARSET)
{
	static $called;
	global $x_ua_compatible, $suffix, $browser, $info;
	$version = '';

	if (isset($called)) die('pkwk_output_dtd() already called. Why?');
	$called = TRUE;

	$type = PKWK_DTD_TYPE_XHTML;
	$option = '';
	switch($pkwk_dtd){
	case PKWK_DTD_HTML_5:
		break;

	case PKWK_DTD_XHTML_1_1:
		$version = '1.1' ;
		$dtd     = 'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd';
		break;

	case PKWK_DTD_XHTML_1_0_STRICT:
		$version = '1.0' ;
		$option  = 'Strict';
		$dtd     = 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd';
		break;

	case PKWK_DTD_XHTML_1_0_TRANSITIONAL:
		$version = '1.0' ;
		$option  = 'Transitional';
		$dtd     = 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd';
		break;

	case PKWK_DTD_XHTML_BASIC_1_0:
		$version = '1.0' ;
		$option  = 'Basic';
		$dtd     = 'http://www.w3.org/TR/xhtml-basic/xhtml-basic10.dtd';
		break;

	default:
		die('DTD not specified or invalid DTD');
		break;
	}

	$charset = htmlsc($charset);

	// Output XML or not
	if ($type == PKWK_DTD_TYPE_XHTML && $pkwk_dtd !== PKWK_DTD_HTML_5 ){
		echo '<?xml version="1.0" encoding="' . CONTENT_CHARSET . '" ?' . '>' . "\n";
	}

	if ($pkwk_dtd != PKWK_DTD_HTML_5){
		// Output doctype
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD ' .
			($type == PKWK_DTD_TYPE_XHTML ? 'XHTML' : 'HTML') . ' ' .
			$version .
			($option != '' ? ' ' . $option : '') .
			'//EN" "' .
			$dtd .
			'">' . "\n";
	}else{
		echo '<!DOCTYPE html>'."\n";
	}

	// Output <html> start tag
	$lang_code = substr(str_replace('_','-',LANG),0,2); // RFC3066
	
	echo '<html';	
	if ($type != PKWK_DTD_TYPE_XHTML || $pkwk_dtd == PKWK_DTD_HTML_5) {
		if($pkwk_dtd == PKWK_DTD_HTML_5){
			echo ' xmlns="http://www.w3.org/1999/xhtml"';
		}
		echo ' lang="' . $lang_code . '"'; // HTML
	} else {
		echo ' xmlns="http://www.w3.org/1999/xhtml"'; // dir="ltr" /* LeftToRight */
		echo ' xml:lang="' . $lang_code . '"';
		if ($version == '1.0') echo ' lang="' . $lang_code . '"'; // Only XHTML 1.0
	}

	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	$info[] = $user_agent;
	if(preg_match_all('/MSIE ([\.\d]+)/',$user_agent,$matches)){
		// IE
		$browser = 'ie ie'.substr($matches[1][0],0,1);
		if (substr($matches[1][0],0,1) <= 9){
			$x_ua_compatible = 'IE=edge'; // force to IE9
		}
	} else if (preg_match('/Gecko/', $user_agent) && preg_match("/(Firefox|Netscape?6)\/([\.\d]+)/", $user_agent,$matches)){
		// Gecko (FireFox)
		$browser = 'gecko '.strtolower($matches[1]).substr($matches[2][0],0,1);
	} else if (preg_match("/(Presto)\/([\.\d]+)/", $user_agent, $matches)){
		// Opera
		$browser = strtolower($matches[1]).' '.strtolower($matches[1]).substr($matches[2],0,1);
	} else if (preg_match("/(WebKit|KHTML|Konqueror)/", $user_agent)){
		// Safari
		$browser = "webkit";
	} else if (preg_match("/(Nintendo|Sony|Dreamcast|NetFront)/", $user_agent)){
		// ゲーム機はNetFront扱い
		$browser = 'netfront';
	}
	unset($matches);
	
	global $facebook;
	if (isset($facebook)){
		echo ' xmlns:fb="http://www.facebook.com/2008/fbml"';
	}
	
	echo ' class="no-js '.$browser.'">' . "\n"; // <html>
	unset($lang_code);
	
	if ($pkwk_dtd == PKWK_DTD_HTML_5){
		$meta_tags[] = array('charset'	=> CONTENT_CHARSET);
	}else{
		if (!isset($x_ua_compatible)) $x_ua_compatible = 'IE=edge';
		$meta_tags = array(
			array('http-equiv'	=> 'content-type',				'content'	=> 'text/html; charset='.CONTENT_CHARSET),
			array('http-equiv'	=> 'content-language',			'content'	=> $lang_code),
			array('http-equiv'	=> 'content-style-type',		'content'	=> 'text/css'),
			array('http-equiv'	=> 'content-script-type',		'content'	=> 'text/javascript'),
			array('http-equiv'	=> 'X-UA-Compatible',			'content'	=> $x_ua_compatible),
			array('http-equiv'	=> 'X-Frame-Options',			'content'	=> 'deny')
		);
	}
	return tag_helper('meta',$meta_tags);
}

// タグヘルパー
function tag_helper($tagname,$tags){
	$out = array();
	foreach ($tags as $tag) {
		// linkタグで、rel属性やtype属性がない場合スタイルシートとする。
		if ($tagname == 'link' && (empty($tags['rel'])) ){
			$tags['rel'] = 'stylesheet';
		}
		
		if (isset($tags['rel']) && $tags['rel'] == 'stylesheet'){
			$tags['type'] = 'text/css';
		}
		
		// scriptタグでtypeが省略されていた場合JavaScriptとする。
		if ($tagname == 'script' && empty($tags['type'])){
			$tags['type'] = 'text/javascript';
		}

		// タグをパース
		foreach( $tag as $key=>$val){
			$IE_flag = '';
			if ($key == 'content' && ($tagname == 'script' || $tagname == 'style')){
				// CDATA内はエンコードする必要が無い・・・ハズ
				$content = "/".'*<![CDATA[*'."/\n".$val."\n/".'*]]>*'.'/';
			}else if($key == 'IE_flag'){
				$IE_flag = $val;
			}else{
				$tag_contents[] = $key.'="'.$val.'"';
			}
		}
		unset($tag, $key, $val);
		// タグの属性を結合
		$tag_content = join(' ',$tag_contents);
		if ($tagname == 'script' || $tagname == 'style'){
			if (empty($content)){
				$ret = '<'.$tagname.' '.$tag_content.'></'.$tagname.'>';
			}else{
				$ret = '<'.$tagname.' '.$tag_content.'>'.$content.'</'.$tagname.'>';
			}
		}else{
			$ret = '<'.$tagname.' '.$tag_content.' />';
		}
		
		if ($IE_flag){ 
			$out[] = '<!--[if lte IE '.$IE_flag.']>'.$ret.'<![endif]-->'; 
		}else{
			$out[] = $ret;
		}
		unset($tag_contents,$tag_content,$key,$val,$content,$IE_flag,$ret);
	}
	
	return join("\n\t\t",$out)."\n";
}
?>
