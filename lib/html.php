<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone
// $Id: html.php,v 1.65.38 2010/08/23 23:41:00 Logue Exp $
// Copyright (C)
//   2010      PukiWiki Advance Developers Team <http://pukiwiki.logue.be/>
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
	global $vars, $arg, $help_page, $hr;
	global $defaultpage, $whatsnew, $whatsdeleted, $interwiki, $aliaspage, $glossarypage;
	global $menubar, $sidebar, $navigation, $headarea, $footarea, $protect;
	global $attach_link, $related_link, $function_freeze;
	global $search_word_color, $foot_explain, $note_hr;
	global $trackback, $referer;
	global $newtitle, $newbase, $language, $use_local_time, $session; // Plus! skin extension
	global $nofollow;
	global $_LANG, $_LINK, $_IMAGE;

	global $pkwk_dtd, $x_ua_compatible;	// XHTML 1.1, XHTML1.0, HTML 4.01 Transitional...
	global $page_title;		// Title of this site
	global $do_backup;		// Do backup or not
	global $modifier;		// Site administrator's  web page
	global $modifierlink;	// Site administrator's name

	global $skin_file;
	global $_string;
	
	global $head_tags, $foot_tags;	// Obsolete
	
	global $meta_tags, $link_tags, $js_tags, $js_blocks, $css_blocks, $info;
	global $keywords, $description, $google_loader, $ui_theme;

	if (! defined('SKIN_FILE') || ! file_exists(SKIN_FILE) || ! is_readable(SKIN_FILE)) {
		if (! file_exists($skin_file) || ! is_readable($skin_file)) {
			die_message(SKIN_FILE . '(skin file) is not found or not readable.');
		} else {
			define('SKIN_FILE', $skin_file);
			
		}
	}

	$_LINK = $_IMAGE = array();

	$_page  = isset($vars['page']) ? $vars['page'] : '';
	$r_page = rawurlencode($_page);

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
		'linklist'		=> get_cmd_uri('linklist',		$_page),
		'list'			=> get_cmd_uri('list'),
		'log_browse'	=> get_cmd_uri('logview',		$_page,	'',	array('kind'=>'browse')),
		'log_check'		=> get_cmd_uri('logview',		$_page,	'',	array('kind'=>'check')),
		'log_down'		=> get_cmd_uri('logview',		$_page,	'',	array('kind'=>'download')),
		'log_login'		=> get_cmd_uri('logview',		'',		'',	array('kind'=>'login')),
		'log_update'	=> get_cmd_uri('logview',		$_page),
		'new'			=> get_cmd_uri('newpage',		'',		'',	array('refer'=>$_page)),
		'newsub'		=> get_cmd_uri('newpage_subdir','',		'',	array('directory'=>$_page)),
		'print'			=> get_cmd_uri('print',			$_page),
		'referer'		=> get_cmd_uri('referer',		$_page),
		'rename'		=> get_cmd_uri('rename',		'',		'',	array('refer'=>$_page)),
		'search'		=> get_cmd_uri('search'),
		'skeylist'		=> get_cmd_uri('skeylist',		$_page),
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
	if ($trackback) $_LINK['trackback'] = get_cmd_uri('tb','','',array('__mode'=>'view','tb_id'=>tb_get_id($_page)));

	// 1.4.x未満スキンの互換処理は削除

	// Init flags
	$is_page = (is_pagename($_page) && ! arg_check('backup') && ! is_cantedit($_page));
	$is_read = (arg_check('read') && is_page($_page));
	$is_freeze = is_freeze($_page);
	$filetime = get_filetime($_page);

	if ($vars['ajax']=='json'){
		// JSONで出力（仮）
		$obj = array(
			'body'			=> $body,
			'is_read'		=> $is_read,
			'is_freeze'		=> $is_freeze,
			'is_page'		=> $is_page,
			'lastmodified'	=> $filetime,
			'newtitle'		=> $newtitle,
			'page'			=> $_page,
			'taketime'		=> elapsedtime(),
			'title'			=> $title
		);
		pkwk_common_headers();
		header('Content-Type: application/json; charset=' . CONTENT_CHARSET);
		echo json_encode($obj); 
	}else if($vars['ajax'] == true){
		// XMLで出力
		pkwk_common_headers();
		header('Content-Type: text/xml; charset=' . CONTENT_CHARSET);
		echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		echo $body;
	}else{
		$meta_tags[] = array('name' => 'generator',	'content' => strip_tags(GENERATOR));
		$meta_tags[] = array('name' => 'viewport',	'content' => (isset($viewport) ? $viewport : 'width=device-width; initial-scale=1.0; maximum-scale=1.0;'));
		($modifier !== 'anonymous') ?			$meta_tags[] = array('name' => 'author',					'content' =>  $modifier) : '';
		(!empty($google_site_verification)) ?	$meta_tags[] = array('name' => 'google-site-verification',	'content' => $google_site_verification) : '';
		(!empty($yahoo_site_explorer_id)) ?		$meta_tags[] = array('name' => 'y_key',						'content' => $yahoo_site_explorer_id) : '';
		(!empty($bing_siteid)) ?				$meta_tags[] = array('name' => 'msvalidate.01',				'content' => $bing_siteid) : '';

		// テーマで$default_google_loaderが指定されていない場合は、jQueryを読み込む。
		// プラグイン以外で指定する場合は、default_のプリフィックスが付く。
		// スキン側でオーバーライド可能（jQuery以外を指定するケースを考慮するため）
		if(!isset($default_google_loader)){
			$default_google_loader = array(
				'jquery'=>'1.4.2',
				'jqueryui'=>'1.8.4',
				'swfobject'=>'2.2'
			);
			if ($x_ua_compatible == 'chrome=1'){ $default_google_loader['chrome-frame'] = '1.0.2'; }
			if (!isset($ui_theme)) { $ui_theme = 'base'; }
			
			$default_link_tags[] = array(
				'rel'=>'stylesheet',
				'href'=>'http://ajax.googleapis.com/ajax/libs/jqueryui/'.$default_google_loader['jqueryui'].'/themes/'.$ui_theme.'/jquery-ui.css',
				'type'=>'text/css',
				'id'=>'ui-theme');
		}

		if (!isset($shortcut_icon)){ $shortcut_icon = ROOT_URI.'favicon.ico'; }
		
		// Linkタグの生成。scriptタグと異なり、順番が変わっても処理への影響がない。
		// http://www.w3schools.com/html5/tag_link.asp
		$default_link_tags = array_merge(array(
			array('rel'=>'alternate',		'href'=>$_LINK['mixirss'],	'type'=>'application/rss+xml',	'title'=>'RSS'),
			array('rel'=>'archive',			'href'=>$_LINK['backup'],	'type'=>'text/html',	'title'=>$_LANG['skin']['backup']),
			// see http://www.seomoz.org/blog/canonical-url-tag-the-most-important-advancement-in-seo-practices-since-sitemaps
			array('rel'=>'canonical',		'href'=>$_LINK['reload'],	'type'=>'text/html',	'title'=>$_page),
			array('rel'=>'contents',		'href'=>$_LINK['menu'],		'type'=>'text/html',	'title'=>$_LANG['skin']['menu']),
			array('rel'=>'sidebar',			'href'=>$_LINK['side'],		'type'=>'text/html',	'title'=>$_LANG['skin']['side']),
			array('rel'=>'glossary',		'href'=>$_LINK['glossary'],	'type'=>'text/html',	'title'=>$_LANG['skin']['glossary']),
			array('rel'=>'help',			'href'=>$_LINK['help'],		'type'=>'text/html',	'title'=>$_LANG['skin']['help']),
			array('rel'=>'first',			'href'=>$_LINK['top'],		'type'=>'text/html',	'title'=>$_LANG['skin']['top']),
			array('rel'=>'index',			'href'=>$_LINK['list'],		'type'=>'text/html',	'title'=>$_LANG['skin']['list']),
			array('rel'=>'search',			'href'=>$_LINK['search'],	'type'=>'text/html',	'title'=>$_LANG['skin']['search']),
			array('rel'=>'shortcut icon',	'href'=>$shortcut_icon,		'type'=>'image/vnd.microsoft.icon')
		),$default_link_tags);

		if ($nofollow || ! $is_read){
			$meta_tags[] = array('name' => 'robots', 'content' => 'NOINDEX,NOFOLLOW');
		}else{
			if (empty($description)){ $description = $title.' - '.$page_title; }
			$meta_tags[] = array('name' => 'description', 'content' => $description);
			if (empty($keywords)) $keywords = 'PukiWiki, PHP, ajax, Advance, CMS, Wiki, Adv., PukiWiki Adv.,PukiWiki Advance';
			$meta_tags[] = array('name' => 'keywords', 'content' => $keywords);
		}
		
//		if ($notify_from !== 'from@example.com') $link_tags[] = array('rev'=>'made',	'href'=>'mailto:'.$notify_from,	'title'=>	'Contact to '.$modifier);

		foreach ($default_google_loader as $name=>$version){
			$default_js[] = 'google.load("'.$name.'","'.$version.'");';
		}
		
		if (isset($google_loader)){
			foreach ($google_loader as $name=>$version){
				$default_js[] = 'google.load("'.$name.'","'.$version.'");';
			}
		}

		// JavaScriptタグの組み立て
		$js_var = array(
			'SCRIPT'=>get_script_absuri(),
			'PAGE'=>rawurlencode($_page),
			'LANG'=>$language,
			'DEBUG'=>constant('DEBUG'),
			'SKIN_DIR'=>constant('SKIN_URI'),
			'IMAGE_DIR'=>constant('IMAGE_URI'),
			'DEFAULT_LANG'=>constant('DEFAULT_LANG'),
			'THEME_NAME'=>constant('PLUS_THEME'),
//			'SESSION'=>$session
		);
		foreach( $js_var as $key=>$val){
			$default_js[] = 'var '.$key.'="'.$val.'";';
		}
		
		unset($js_var, $key, $val);

		$pkwk_head_js[] = array('type'=>'text/javascript', 'src'=>SKIN_DIR.'js/dd_belatedpng.js', 'IE_flag'=>7);
		$pkwk_head_js[] = array('type'=>'text/javascript', 'src'=>'http://www.google.com/jsapi?key='.$google_api_key);
		$pkwk_head_js[] = array('type'=>'text/javascript', 'content'=>join($default_js,"\n"));
		/* ヘッダー部分の処理ここまで */
	
		$default_js_libs[] = array('type'=>'text/javascript', 'src'=>SKIN_URI.'js/locale.js');
		if (DEBUG === true) {
			// 読み込むsrcディレクトリ内のJavaScript
			$files = array(
				/* libraly */
				'modernizr-1.5.min','swfupload','tzCalculation_LocalTimeZone',
				
				/* Use plugins */ 
				'jquery.cookie','jquery.lazyload', 'jquery.query','jquery.scrollTo','jquery.colorbox-min','jquery.a-tools.min','jquery.superfish',
				'jquery.swfupload','jquery.tablesorter-min','jquery.textarearesizer','jquery.jplayer.min',	/* 'jquery.beautyOfCode-min', */
				
				/* MUST BE LOAD LAST */
				'skin.original'
			);
			
			foreach($files as $script_file) $default_js_libs[] = array('type'=>'text/javascript', 'src'=>SKIN_URI.'js/src/'.$script_file.'.js');

			// yui profiler and profileviewer
/*
			$link_tags[] = array('rel'=>'stylesheet','type'=>'text/css','href'=>SKIN_DIR.'js/profiling/yahoo-profiling.css');
			$default_js_libs[] = array('type'=>'text/javascript', 'src'=>SKIN_DIR.'js/profiling/yahoo-profiling.min.js');
			$default_js_libs[] = array('type'=>'text/javascript', 'src'=>SKIN_URI.'js/profiling/config.js');
*/
			unset($files);
			$info[] = 'This program is running in debug mode. ';
		} else {
			$default_js_libs[] = array('type'=>'text/javascript', 'src'=>SKIN_URI.'js/skin.js.php');
		}
		
		// Google Analytics
		if ($google_analytics) {
			$js_blocks[] = 'var _gaq = _gaq || [];';
			$js_blocks[] = '_gaq.push(["_setAccount", "'.$google_analytics.'"]);';
			$js_blocks[] = '_gaq.push(["_trackPageview"]);';
		}

		/* ヘッダー部のタグ */
		$pkwk_head = tag_helper('meta',$meta_tags)."\t\t".tag_helper('link',$default_link_tags)."\t\t".tag_helper('link',$link_tags);
		
		if (!empty($css_blocks)){
			$pkwk_head .= "\t\t".tag_helper('style',array(array('type'=>'text/css', 'content'=>join("\n",$css_blocks))));
		}
		if ($pkwk_dtd == PKWK_DTD_HTML_5){
			// html5.jsに限り、ヘッダー内にないと正常に動作しない
			$pkwk_head .= "\t\t".'<!--[if lt IE 9]><script type="text/javascript" src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->'."\n";
		}
		
		/* フッター部のタグ */
		$pkwk_tags = tag_helper('script',$pkwk_head_js)."\t\t".tag_helper('script',array_merge($default_js_libs,$js_tags));

		if (!empty($js_blocks)){
			$pkwk_tags .= "\t\t".tag_helper('script',array(array('type'=>'text/javascript', 'content'=>join("\n",$js_blocks))));
		}

		/* 非推奨要素の警告 */
		if (! empty($head_tags)){
			$pkwk_head .= join("\n", $head_tags) ."\n";
			$info[] = '<code>$head_tags</code> is obsolate. Use $meta_tags, $link_tags, $js_tags, $js_blocks, $css_blocks.';
		}
		if (! empty($foot_tags)){
			$pkwk_tags .= join("\n", $foot_tags) ."\n";
			$info[] = '<code>$foot_tags</code> is obsolate. Use $meta_tags, $link_tags, $js_tags, $js_blocks, $css_blocks.';
		}
		// Last modification date (string) of the page
		if ($is_read){
			$lastmodified = ($pkwk_dtd == PKWK_DTD_HTML_5) ? 
				'<time pubdate="pubdate" datetime="'.get_date('c',$filetime).'">'.get_date('D, d M Y H:i:s T', $filetime). ' ' . get_pg_passage($_page, FALSE).'</time>'
				 : get_date('D, d M Y H:i:s T', $filetime). ' ' . get_pg_passage($_page, FALSE);
		}

		// List of attached files to the page
		$attaches = '';
		if ($attach_link && $is_read && exist_plugin_action('attach')) {
			if (do_plugin_init('attach') !== FALSE) {
				$attaches = attach_filelist();
			}
		}
		
		// List of related pages
		$related  = ($related_link && $is_read) ? make_related($_page,'dl') : '';

		// List of footnotes
		ksort($foot_explain, SORT_NUMERIC);
		$notes = ! empty($foot_explain) ? $note_hr . '<ul>'.join("\n", $foot_explain).'</ul>' : '';
		
		// Search words
		if ($search_word_color && isset($vars['word'])) {
			$body = '<div class="small">' . $_string['word'] . htmlspecialchars($vars['word']) .
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
				$s_key    = htmlspecialchars($key);
				$pattern  = '/' .
					'<textarea[^>]*>.*?<\/textarea>' .	// Ignore textareas
					'|' . '<[^>]*>' .			// Ignore tags
					'|' . '&[^;]+;' .			// Ignore entities
					'|' . '(' . $pattern . ')' .		// $matches[1]: Regex for a search word
					'/sS';
				$decorate_Nth_word = create_function(
					'$matches',
					'return (isset($matches[1])) ? ' .
						'\'<strong class="word' .
							$id .
						'">\' . $matches[1] . \'</strong>\' : ' .
						'$matches[0];'
				);
				$body  = preg_replace_callback($pattern, $decorate_Nth_word, $body);
				$notes = preg_replace_callback($pattern, $decorate_Nth_word, $notes);
				++$id;
			}
		}
		
		if ((PKWK_WARNING === true || DEBUG === true) && ! empty($info)){
			$body = '<div style="padding: 0pt 0.7em;" class="ui-state-highlight ui-corner-all">'.
					'<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: 0.3em;"></span>'.
					join("<br />",$info).'</p></div>'.$body;
		}

		// global $always_menu_displayed;
		if (arg_check('read')) $always_menu_displayed = 1;
		$body_menu = $body_side = '';
		if ($always_menu_displayed) {
			if (exist_plugin_convert('menu')) $body_menu = do_plugin_convert('menu');
			if (exist_plugin_convert('side')) $body_side = do_plugin_convert('side');
		}

		pkwk_common_headers();
		header('Content-Type: text/html; charset=' . CONTENT_CHARSET);
		header('ETag: ' . md5(MUTIME));
		require(SKIN_FILE);
	}

	if(extension_loaded('zlib') && 
		ob_get_length() === FALSE && 
		!ini_get('zlib.output_compression') && 
		ini_get('output_handler') !== 'ob_gzhandler' && 
		ini_get('output_handler') !== 'mb_output_handler'){
		
		ob_end_flush();
	}

	exit;
}

function showtaketime(){
	// http://pukiwiki.sourceforge.jp/dev/?BugTrack2%2F251
	$longtaketime = getmicrotime() - MUTIME;
	return sprintf('%01.03f', $longtaketime);
}

// Show 'edit' form
function edit_form($page, $postdata, $digest = FALSE, $b_template = TRUE)
{
	global $script, $vars, $rows, $cols, $hr, $function_freeze;
	global $load_template_func, $load_refer_related;
	global $notimeupdate;
	global $_button, $_string;
	global $ctrl_unload;

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
			$s_page = htmlspecialchars($_page);
			$pages[$_page] = '		<option value="' . $s_page . '">' .$s_page . '</option>'."\n";
		}
		ksort($pages, SORT_STRING);
		$s_pages  = join("\n", $pages);
		$template = <<<EOD
<div class="template_form">
	<select name="template_page" class="template">
		<option value="">-- {$_button['template']} --</option>
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
	$s_page      = htmlspecialchars($page);
	$s_digest    = htmlspecialchars($digest);
	$s_postdata  = htmlspecialchars($refer . $postdata);
	$s_original  = isset($vars['original']) ? htmlspecialchars($vars['original']) : $s_postdata;
	$s_id        = isset($vars['id']) ? htmlspecialchars($vars['id']) : '';
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
		$add_notimestamp .= '&nbsp;';
	}
	$refpage = isset($vars['refpage']) ? htmlspecialchars($vars['refpage']) : '';
	$add_assistant = edit_form_assistant();

	$body = <<<EOD
<form action="$script" method="post" id="form">
	<input type="hidden" name="cmd"    value="edit" />
	<input type="hidden" name="page"   value="$s_page" />
	<input type="hidden" name="digest" value="$s_digest" />
	<input type="hidden" name="ticket" value="$s_ticket" />
	<input type="hidden" name="id"     value="$s_id" />
	<div class="edit_form">
$template
$addtag
		<textarea name="msg" id="msg" rows="$rows" cols="$cols">$s_postdata</textarea>
		$add_assistant
		<input type="submit" name="write" value="{$_button['update']}" accesskey="s" />
		$add_top
		<input type="submit" name="preview" value="{$_button['preview']}" accesskey="p" />
		$add_notimestamp
		<input type="submit" id="cancel" name="cancel" value="{$_button['cancel']}" accesskey="c" />
	</div>
	<textarea id="original" name="original" rows="1" cols="1" style="display:none">$s_original</textarea>
</form>

EOD;
	if (isset($vars['help'])) {
		$body .= $hr . catrule();
	} else {
		$body .= '<ul><li><a href="'.get_cmd_uri('edit',$r_page,'','help=true').'">' . $_string['help'] . '</a></li></ul>';
	}
	return $body;
}
// Input Assistant
// Plus!とのプラグイン互換性のため
function edit_form_assistant(){
	return '<div class="assistant"></div>';
}

// Related pages
// Related pages
function make_related($page, $tag = '')
{
	global $vars, $rule_related_str, $related_str;
	global $_ul_left_margin, $_ul_margin, $_list_pad_str;

	$links = links_get_related($page);

	if ($tag) {
		ksort($links, SORT_STRING);	// Page name, alphabetical order
	} else {
		arsort($links, SORT_NUMERIC);	// Last modified date, newer
	}

	$_links = array();
	foreach ($links as $page=>$lastmod) {
		if (check_non_list($page)) continue;

		$s_page   = htmlspecialchars($page);
		$passage  = get_passage($lastmod);
/*
		$_links[] = ($tag) ?
			'<a href="' . get_page_uri($page) . '" title="' .
			$s_page . ' ' . $passage . '">' . $s_page . '</a>' :
			'<a href="' . get_page_uri($page) . '">' .
			$s_page . '</a>' . $passage;
*/
		$_links[] = 
			'<a href="' . get_page_uri($page) . '">' .
			$s_page . '</a>' . $passage;
	}
	if (empty($_links)) return ''; // Nothing

	if ($tag == 'p') { // From the line-head
		$margin = $_ul_left_margin + $_ul_margin;
		$style  = sprintf($_list_pad_str, 1, $margin, $margin);
		$retval =  "\n" .
			'<ul' . $style . '>' . "\n" .
			'<li>' . join("</li>\n<li>", $_links) . '</li>' . "\n" .
			'</ul>' . "\n";
	}else if ($tag == 'dl') {
		$retval =  "\n" .
			'<dl>'."\n".
			'<dt>Links: </dt>' . "\n" .
			'<dd>' . join("</dd>\n<dd>", $_links) . '</dd>' . "\n" .
			'</dl>' . "\n";
	} else if ($tag) {
		$retval = join("</li>\n<li>", $_links);
	} else {
		$retval = join("\n ", $_links);
	}

	return $retval;
}

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
	return '<a href="' . get_cmd_uri('related',$page) . '">' . htmlspecialchars($page) . '</a> ';
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
	if (version_compare(PHP_VERSION, '4.3.0', '>=')) {
		if (headers_sent($file, $line))
		    die_message('Headers already sent at ' .
		    	htmlspecialchars($file) .
			' line ' . $line . '.');
	} else {
		if (headers_sent())
			die_message('Headers already sent.');
	}
}

// Output common HTTP headers
function pkwk_common_headers(){
	if (! PKWK_OPTIMISE) pkwk_headers_sent();

	if(PKWK_ZLIB_LOADABLE_MODULE == true && $compress != false) {
		$matches = array();
		if(extension_loaded('zlib') && 
			ob_get_length() === FALSE && 
			!ini_get('zlib.output_compression') && 
			ini_get('output_handler') !== 'ob_gzhandler' && 
			ini_get('output_handler') !== 'mb_output_handler'){	// mb_output_handlerとかち合うらしいので、その場合は弾く。http://pukiwiki.sourceforge.jp/dev/?%BB%A8%C3%CC%2F11
			
			// http://jp.php.net/manual/ja/function.ob-gzhandler.php
			ob_start('ob_gzhandler');
		}else if(ini_get('zlib.output_compression') &&
			preg_match('/\b(gzip|deflate)\b/i', $_SERVER['HTTP_ACCEPT_ENCODING'], $matches)) {
			// Bug #29350 output_compression compresses everything _without header_ as loadable module
			// http://bugs.php.net/bug.php?id=29350
			header('Content-Encoding: ' . $matches[1]);
			$vary = get_language_header_vary();
			if (! empty($vary)) $vary .= ',';
			header('Vary: '.$vary.' Accept-Encoding');
		}
	}
	// PHPで動的に生成されるページはキャシュすべきではない
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
	header('Pragma: no-cache');
}

// DTD definitions
define('PKWK_DTD_HTML_5',                 18); // HTML5
define('PKWK_DTD_XHTML_1_1',              17); // Strict only
define('PKWK_DTD_XHTML_1_0',              16); // Strict
define('PKWK_DTD_XHTML_1_0_STRICT',       16);
define('PKWK_DTD_XHTML_1_0_TRANSITIONAL', 15);
define('PKWK_DTD_XHTML_1_0_FRAMESET',     14);
define('PKWK_DTD_XHTML_BASIC_1_0',        11);
define('PKWK_DTD_HTML_4_01',               3); // Strict
define('PKWK_DTD_HTML_4_01_STRICT',        3);
define('PKWK_DTD_HTML_4_01_TRANSITIONAL',  2);
define('PKWK_DTD_HTML_4_01_FRAMESET',      1);

define('PKWK_DTD_TYPE_XHTML',        1);
define('PKWK_DTD_TYPE_HTML',         0);

// Output HTML DTD, <html> start tag. Return content-type.
function pkwk_output_dtd($pkwk_dtd = PKWK_DTD_XHTML_1_1, $charset = CONTENT_CHARSET)
{
	static $called;
	global $x_ua_compatible, $suffix;

	if (isset($called)) die('pkwk_output_dtd() already called. Why?');
	$called = TRUE;

	$type = PKWK_DTD_TYPE_XHTML;
	$option = '';
	switch($pkwk_dtd){
	case PKWK_DTD_HTML_5:
		$type    = PKWK_DTD_TYPE_HTML;
		$suffix  = '/';
		break;

	case PKWK_DTD_XHTML_1_1:
		$version = '1.1' ;
		$dtd     = 'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd';
		$suffix  = '/';
		break;

	case PKWK_DTD_XHTML_1_0_STRICT:
		$version = '1.0' ;
		$option  = 'Strict';
		$dtd     = 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd';
		$suffix  = '/';
		break;

	case PKWK_DTD_XHTML_1_0_TRANSITIONAL:
		$version = '1.0' ;
		$option  = 'Transitional';
		$dtd     = 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd';
		$suffix  = '/';
		break;

	case PKWK_DTD_XHTML_BASIC_1_0:
		$version = '1.0' ;
		$option  = 'Basic';
		$dtd     = 'http://www.w3.org/TR/xhtml-basic/xhtml-basic10.dtd';
		$suffix  = '/';
		break;

	case PKWK_DTD_HTML_4_01_STRICT:
		$type    = PKWK_DTD_TYPE_HTML;
		$version = '4.01';
		$dtd     = 'http://www.w3.org/TR/html4/strict.dtd';
		$suffix  = '';
		break;

	case PKWK_DTD_HTML_4_01_TRANSITIONAL:
		$type    = PKWK_DTD_TYPE_HTML;
		$version = '4.01';
		$option  = 'Transitional';
		$dtd     = 'http://www.w3.org/TR/html4/loose.dtd';
		$suffix  = '';
		break;

	default:
		die('DTD not specified or invalid DTD');
		break;
	}

	$charset = htmlspecialchars($charset);
	

	// Output XML or not
	if ($type == PKWK_DTD_TYPE_XHTML) {
		// for IEPatch: for W3C standard rendering
//		if (!(CONTENT_CHARSET == 'UTF-8' && UA_NAME == 'MSIE')) {
			echo '<?xml version="1.0" encoding="' . CONTENT_CHARSET . '" ?' . '>' . "\n";
//		}
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
	$lang_code = str_replace('_','-',LANG); // RFC3066
	echo '<html';
	if ($type == PKWK_DTD_TYPE_XHTML) {
		echo ' xmlns="http://www.w3.org/1999/xhtml"'; // dir="ltr" /* LeftToRight */
		echo ' xml:lang="' . $lang_code . '"';
		if ($version == '1.0') echo ' lang="' . $lang_code . '"'; // Only XHTML 1.0
	} else {
		echo ' lang="' . $lang_code . '"'; // HTML
	}
	echo '>' . "\n"; // <html>
	unset($lang_code);
	
	if ($pkwk_dtd == PKWK_DTD_HTML_5){
		$meta_tags[] = array('charset'	=> CONTENT_CHARSET);
	}else{
		if (!isset($x_ua_compatible)) $x_ua_compatible = 'IE=edge';
		$meta_tags = array(
			array('http-equiv'	=> 'content-language',		'content'	=> 'text/html; charset='.CONTENT_CHARSET),
			array('http-equiv'	=> 'content-style-type',	'content'	=> 'text/css'),
			array('http-equiv'	=> 'content-script-type',	'content'	=> 'text/javascript'),
			array('http-equiv'	=> 'X-UA-Compatible',		'content'	=> $x_ua_compatible),
			array('http-equiv'	=> 'X-Frame-Options',		'content'	=> 'deny')
		);
	}
	return tag_helper('meta',$meta_tags);
}

/* タグヘルパー */
function tag_helper($tagname,$tags,$suffix=' /'){
	foreach ($tags as $tag) {
		foreach( $tag as $key=>$val){
			if ($key == 'content' && ($tagname == 'script' || $tagname == 'style')){
				if ($tagname == 'script'){
					$content = "\n".'//<![CDATA['."\n".$val."\n".'//]]>';
				}else{
					$content = "/".'*<![CDATA[*'."/\n".$val."\n/".'*]]>*'.'/';
				}
			}else if($key == 'IE_flag'){
				$IE_flag = $val;
			}else{
				$tag_contents[] = $key.'="'.htmlspecialchars($val).'"';
			}
		}
		$tag_content = join(' ',$tag_contents);
		if ($tagname == 'script' || $tagname == 'style'){
			if (empty($content)){
				$ret = '<'.$tagname.' '.$tag_content.'></'.$tagname.'>';
			}else{
				$ret = '<'.$tagname.' '.$tag_content.'>'.$content.'</'.$tagname.'>';
			}
		}else{
			$ret = '<'.$tagname.' '.$tag_content.$suffix.'>';
		}
		
		if ($IE_flag){ 
			$out[] = '<!--[if lte IE '.$IE_flag.']>'.$ret.'<![endif]-->'; 
		}else{
			$out[] = $ret;
		}
		unset($tag_contents,$tag_content,$key,$val,$content,$IE_flag,$ret);
	}
	unset($tag);
	return join("\n\t\t",$out)."\n";
}
?>
