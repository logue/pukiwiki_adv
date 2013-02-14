<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone
// $Id: html.php,v 1.65.48 2012/01/08 08:31:00 Logue Exp $
// Copyright (C)
//   2010-2012 PukiWiki Advance Developers Team <http://pukiwiki.logue.be/>
//   2005-2009 PukiWiki Plus! Team <http://pukiwiki.cafelounge.net/plus/>
//   2002-2007 PukiWiki Developers Team <http://pukiwiki.sourceforge.jp/>
//   2001-2002 Originally written by yu-ji <http://www.hyuki.com/yukiwiki/>
// License: GPL v2 or (at your option) any later version
//
// HTML-publishing related functions
// Plus!NOTE:(policy)not merge official cvs(1.49->1.54)
// Plus!NOTE:(policy)not merge official cvs(1.58->1.59) See Question/181

use Zend\Http\Response;
use PukiWiki\Lib\File\FileFactory;
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
	global $_page, $is_page, $is_read, $is_freeze, $is_readonly, $is_safemode, $is_createpage, $lastmod;

	if (isset($vars['page']) && !empty($vars['page'])){
		$_page = $vars['page'];
	}

	if (!empty($_page)){
		$wiki = FileFactory::Wiki($_page);
		// Init flags
		$is_page = ($wiki->isValied() && $wiki->isEditable() && ! arg_check('backup'));
		$is_read = (arg_check('read') && $wiki->isReadable());
		$is_freeze = $wiki->isFreezed();
		$filetime = $wiki->time();
	}

	
	$is_readonly = auth::check_role('readonly');
	$is_safemode = auth::check_role('safemode');
	$is_createpage = auth::is_check_role(PKWK_CREATE_PAGE);

	pkwk_common_headers(($lastmod && $is_read) ? $filetime : 0);
	if (IS_AJAX && !IS_MOBILE){
		$ajax = isset($vars['ajax']) ? $vars['ajax'] : 'raw';
		switch ($ajax) {
			case 'json':
	 			// JSONで出力
	 			if (!isset($JSON)){
					// $JSON関数が定義されていない場合
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
				echo Zend\Json\Json::encode($JSON);
			break;
			case 'xml':
				header('Content-Type: application/xml; charset=' . CONTENT_CHARSET);
				echo '<?xml version="1.0" encoding="'.CONTENT_CHARSET.'" ?>'."\n";
				echo $body;
			break;
			default:
			case 'raw':
				header('Content-Type: text/html; charset=' . CONTENT_CHARSET);
				echo $body;
			break;
		}
	}else{
		// Set $_LINK for skin
		$_LINK = getLinkSet($_page);

		// スキン出力
		global $pkwk_dtd, $x_ua_compatible;	// HTML5, XHTML 1.1, XHTML1.0...
		global $page_title;		// Title of this site

		global $head_tags, $foot_tags;	// Obsolete
		global $meta_tags, $link_tags, $js_tags, $js_blocks, $css_blocks, $info, $js_init, $js_vars, $modernizr;
		global $keywords, $description, $pkwk_head_js, $google_loader, $ui_theme;

		// Adv. ここから。（あまりいい実装ではない）

		// application/xhtml+xml を認識するブラウザではXHTMLとして出力
		$http_header = (PKWK_STRICT_XHTML === TRUE && strstr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml') !== false) ? 'application/xhtml+xml' : 'text/html';

		$meta_tags[] = array('name' => 'generator',	'content' => strip_tags(GENERATOR));
		if (IS_MOBILE){
			$meta_tags[] = array('name' => 'viewport',	'content' => 'width=device-width, initial-scale=1');
		}else{
			global $google_analytics, $google_api_key, $google_site_verification, $yahoo_site_explorer_id, $bing_webmaster_tool, $shortcut_icon;
			$meta_tags[] = array('http-equiv'	=> 'X-UA-Compatible',			'content'	=> $x_ua_compatible);
			($modifier !== 'anonymous') ?			$meta_tags[] = array('name' => 'author',					'content' => $modifier) : '';
			(!empty($google_site_verification)) ?	$meta_tags[] = array('name' => 'google-site-verification',	'content' => $google_site_verification) : '';
			(!empty($yahoo_site_explorer_id)) ?		$meta_tags[] = array('name' => 'y_key',						'content' => $yahoo_site_explorer_id) : '';
			(!empty($bing_webmaster_tool)) ?		$meta_tags[] = array('name' => 'msvalidate.01',				'content' => $bing_webmaster_tool) : '';

			if (!isset($shortcut_icon)){ $shortcut_icon = ROOT_URI.'favicon.ico'; }

			// Linkタグの生成。scriptタグと異なり、順番が変わっても処理への影響がない。
			// http://www.w3schools.com/html5/tag_link.asp
			$link_tags[] = array('rel'=>'alternate',		'href'=>$_LINK['mixirss'],	'type'=>'application/rss+xml',	'title'=>'RSS');
				// see http://www.seomoz.org/blog/canonical-url-tag-the-most-important-advancement-in-seo-practices-since-sitemaps
			$link_tags[] = array('rel'=>'canonical',		'href'=>$_LINK['reload'],	'type'=>$http_header,	'title'=>$_page);
			$link_tags[] = array('rel'=>'contents',			'href'=>$_LINK['menu'],		'type'=>$http_header,	'title'=>$_LANG['skin']['menu']);
			$link_tags[] = array('rel'=>'sidebar',			'href'=>$_LINK['side'],		'type'=>$http_header,	'title'=>$_LANG['skin']['side']);
			$link_tags[] = array('rel'=>'glossary',			'href'=>$_LINK['glossary'],	'type'=>$http_header,	'title'=>$_LANG['skin']['glossary']);
			$link_tags[] = array('rel'=>'help',				'href'=>$_LINK['help'],		'type'=>$http_header,	'title'=>$_LANG['skin']['help']);
			$link_tags[] = array('rel'=>'home',				'href'=>$_LINK['top'],		'type'=>$http_header,	'title'=>$_LANG['skin']['top']);
			$link_tags[] = array('rel'=>'index',			'href'=>$_LINK['list'],		'type'=>$http_header,	'title'=>$_LANG['skin']['list']);
			$link_tags[] = array('rel'=>'search',			'href'=>$_LINK['opensearch'],'type'=>'application/opensearchdescription+xml',	'title'=>$page_title.$_LANG['skin']['search']);
			$link_tags[] = array('rel'=>'search',			'href'=>$_LINK['search'],	'type'=>$http_header,	'title'=>$_LANG['skin']['search']);
			$link_tags[] = array('rel'=>'sitemap',			'href'=>$_LINK['sitemap'],	'type'=>$http_header,	'title'=>'Sitemap');
			$link_tags[] = array('rel'=>'shortcut icon',	'href'=>$shortcut_icon,		'type'=>'image/vnd.microsoft.icon');

			if ($nofollow || ! $is_read || ! $is_page || check_non_list($_page) ){
				$meta_tags[] = array('name' => 'robots', 'content' => 'NOINDEX,NOFOLLOW');
			}else{
				// The Open Graph Protocol
				// http://ogp.me/
				$desc = (!empty($description)) ? $description : mb_strimwidth(preg_replace("/[\r\n]/" ,' ' ,strip_htmltag($body)) ,0 ,256 ,'...');
				$logo = (!empty($_SKIN['logo']['src'])) ? $_SKIN['logo']['src'] : IMAGE_URI.'pukiwiki_adv.logo.png';
				if (!empty($description)){ $meta_tags[] =  array('name' => 'description', 'content' => $description); }
				if (!empty($keywords)){ $meta_tags[] =  array('name' => 'keywords', 'content' => $keywords); }
				$meta_tags[] = array('property' => 'og:title',			'content' => $_page);
				$meta_tags[] = array('property' => 'og:locale ',		'content' => LANG);
				$meta_tags[] = array('property' => 'og:type',			'content' => 'website');
				$meta_tags[] = array('property' => 'og:url',			'content' => $_LINK['reload']);
				$meta_tags[] = array('property' => 'og:image',			'content' => $logo);
				$meta_tags[] = array('property' => 'og:site_name',		'content' => $page_title);
				$meta_tags[] = array('property' => 'og:description',	'content' => $desc);
				$meta_tags[] = array('property' => 'og:updated_time',	'content' => $filetime);

				global $fb;
				if (isset($fb)){
					$meta_tags[] = array('property' => 'fb:app_id', 'content' => $fb->getAppId());
				}
			}

	//		if ($notify_from !== 'from@example.com') $link_tags[] = array('rev'=>'made',	'href'=>'mailto:'.$notify_from,	'title'=>	'Contact to '.$modifier);

			global $adminpass;
			if ($adminpass == '{x-php-md5}1a1dc91c907325c69271ddf0c944bc72' || $adminpass == '' ){
				$body = '<div class="message_box ui-state-error ui-corner-all">'.
					'<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: 0.3em;"></span>'.
					'<strong>'.$_string['warning'].'</strong> '.$_string['changeadminpass'].'</p></div>'."\n".
					$body;
			}
		}

		// JavaScriptタグの組み立て
		if (isset($_page)){
			$js_init['PAGE']= rawurlencode($_page);
			$js_init['MODIFIED']= $filetime;
		}
		if(isset($google_analytics)){ $js_init['GOOGLE_ANALYTICS'] = $google_analytics; }

		
		/* ヘッダー部分の処理ここまで */

		/* ヘッダー部のタグ */
		$pkwk_head = tag_helper('meta',$meta_tags)."\t\t".tag_helper('link',$link_tags);
		if (!empty($css_blocks)){
			$pkwk_head .= "\t\t".tag_helper('style',array(array('type'=>'text/css', 'content'=>join("\n",$css_blocks))));
		}

		if (!IS_MOBILE) {
			// Modernizrは、ヘッダー内にないと正常に動作しない
			$pkwk_head .= "\t\t".'<script type="text/javascript" src="'.JS_URI.$modernizr.'"></script>'."\n";

			/* 非推奨要素の警告 */
			if (! empty($head_tags)){
				$pkwk_head .= join("\n", $head_tags) ."\n";
				$info[] = '<var>$head_tags</var> is obsolate. Use $meta_tags, $link_tags, $js_tags, $js_blocks, $css_blocks.';
			}
			if (! empty($foot_tags)){
				$pkwk_tags .= join("\n", $foot_tags) ."\n";
				$info[] = '<var>$foot_tags</var> is obsolate. Use $meta_tags, $link_tags, $js_tags, $js_blocks, $css_blocks.';
			}
			
		}else{
			$js_init['JQUERY_MOBILE_VER'] = JQUERY_MOBILE_VER;
		}

		/* フッター部のタグ */
		// JSに渡す定義を展開
		foreach( $js_init as $key=>$val){
			if ($val !== ''){
				$js_vars[] = 'var '.$key.' = "'.$val.'";';
			}
		}
		if (is_array($pkwk_head_js)){
			array_unshift($pkwk_head_js,array('type'=>'text/javascript', 'content'=>join($js_vars,"\n")));
		}
		unset($js_var, $key, $val);
		$pkwk_tags = tag_helper('script',$pkwk_head_js)."\t\t".tag_helper('script',$js_tags);
		$pkwk_tags .= (!empty($js_blocks)) ? "\t\t".tag_helper('script',array(array('type'=>'text/javascript', 'content'=>join("\n",$js_blocks)))) : '';

		/* Adv.ここまで */

		// Last modification date (string) of the page
		if ($is_read){
			global $attach_link, $related_link;

			$lastmodified = get_date('D, d M Y H:i:s T', $filetime) . ' ' . FileFactory::Wiki($_page)->passage();
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

		if (DEBUG === true && ! empty($info) && !IS_MOBILE){
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
		//header('Content-Type: '.$http_header.'; charset='. CONTENT_CHARSET);
		//@header('X-UA-Compatible: '.(empty($x_ua_compatible)) ? 'IE=edge' : $x_ua_compatible);	// とりあえずIE8対策

		global $headers;
		$headers['Content-Type'] = $http_header . '; charset='. CONTENT_CHARSET;

		include(SKIN_FILE);
	}
	exit;
}

function getLinkSet($_page){
	global $defaultpage, $whatsnew, $whatsdeleted, $interwiki, $aliaspage, $glossarypage;
	global $menubar, $sidebar, $navigation, $headarea, $footarea, $protect;

	global $trackback, $referer;

	// Set $_LINK for skin
	$_LINK = array(
		'search'		=> get_cmd_uri('search'),
		'opensearch'	=> get_cmd_uri('search',	null,	null,	array('format'=>'xml')),
		'list'			=> get_cmd_uri('list'),
		'filelist'		=> get_cmd_uri('filelist'),

		'sitemap'		=> get_cmd_absuri('list', 	null,	'type=sitemap'),
		'rss'			=> get_cmd_absuri('mixirss'),
		'rdf'			=> get_cmd_absuri('rss',	null,	'ver=1.0'),
		'rss10'			=> get_cmd_absuri('rss',	null,	'ver=1.0'), // Same as 'rdf'
		'rss20'			=> get_cmd_absuri('rss',	null,	'ver=2.0'),
		'mixirss'		=> get_cmd_absuri('mixirss'), 		// Same as 'rdf' for mixi

		'read'			=> get_page_uri($_page),
		'reload'		=> get_page_absuri($_page), // 本当は、get_script_uri でいいけど、絶対パスでないと、スキンに影響が出る
		'reload_rel'	=> get_page_uri($_page),

		'login'			=> get_cmd_uri('login', $_page),
		'logout'		=> get_cmd_uri('login', $_page, null, array('action'=>'logout') ),

		/* Special Page */
		'help'			=> get_cmd_uri('help'),
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

	if (empty($_page)) {
		$_LINK = array_merge($_LINK,array(
			'add'			=> get_cmd_uri('add'),
			'backup'		=> get_cmd_uri('backup'),
			'copy'			=> get_cmd_uri('template'),
			'log'			=> get_cmd_uri('logview'),
			'log_browse'	=> get_cmd_uri('logview',	null,	null,	array('kind'=>'browse')),
			'log_check'		=> get_cmd_uri('logview',	null,	null,	array('kind'=>'check')),
			'log_down'		=> get_cmd_uri('logview',	null,	null,	array('kind'=>'download')),
			'log_login'		=> get_cmd_uri('logview',	null,	null,	array('kind'=>'login')),
			'log_update'	=> get_cmd_uri('logview'),
			'new'			=> get_cmd_uri('newpage'),
			'newsub'		=> get_cmd_uri('newpage_subdir'),
			'rename'		=> get_cmd_uri('rename'),
			'upload_list'	=> get_cmd_uri('attach',	null,	null,	array('pcmd'=>'list'))
		));
	}else{
		$_LINK = array_merge($_LINK,array(
			'add'			=> get_cmd_uri('add',			$_page),
			'backup'		=> get_cmd_uri('backup',		$_page),
			'brokenlink'	=> get_cmd_uri('brokenlink',	$_page),
			'copy'			=> get_cmd_uri('template',		null,		null,	array('refer'=>$_page)),
			'diff'			=> get_cmd_uri('diff',			$_page),
			'edit'			=> get_cmd_uri('edit',			$_page),
			'freeze'		=> get_cmd_uri('freeze',		$_page),
			'full'			=> get_cmd_uri('print',			$_page).'&amp;nohead&amp;nofoot',
			'guiedit'		=> get_cmd_uri('guiedit',		$_page),

			'log'			=> get_cmd_uri('logview',		$_page),
			'log_browse'	=> get_cmd_uri('logview',		$_page,	null,	array('kind'=>'browse')),
			'log_check'		=> get_cmd_uri('logview',		$_page,	null,	array('kind'=>'check')),
			'log_down'		=> get_cmd_uri('logview',		$_page,	null,	array('kind'=>'download')),
			'log_login'		=> get_cmd_uri('logview',		null,	null,	array('kind'=>'login')),
			'log_update'	=> get_cmd_uri('logview',		$_page),
			'new'			=> get_cmd_uri('newpage',		null,	null,	array('refer'=>$_page)),
			'newsub'		=> get_cmd_uri('newpage_subdir',null,	null,	array('directory'=>$_page)),
			'print'			=> get_cmd_uri('print',			$_page),
			'rename'		=> get_cmd_uri('rename',		null,	null,	array('refer'=>$_page)),

			'source'		=> get_cmd_uri('source',		$_page),

			'unfreeze'		=> get_cmd_uri('unfreeze',		$_page),
			'upload'		=> get_cmd_uri('attach',		$_page,	null,	array('pcmd'=>'upload')), // link rel="alternate" にも利用するため absuri にしておく

			'template'		=> get_cmd_uri('template',		null,	null,	array('refer'=>$_page))
		));
	}

	if ($referer){
		$_LINK['referer']	= !empty($_page) ? get_cmd_uri('referer',	$_page) : get_cmd_uri('referer');
	}
	if ($trackback){
		$_LINK['trackback'] = (!empty($_page)) ?
			get_cmd_uri('tb',null,null,array('__mode'=>'view','tb_id'=>tb_get_id($_page))) :
			get_cmd_uri('tb',null,null,array('__mode'=>'view'));
	}
	return $_LINK;
}

// Show 'edit' form
function edit_form($page, $postdata, $digest = FALSE, $b_template = TRUE)
{
	global $script, $vars, $hr, $function_freeze, $session;
	global $load_template_func, $load_refer_related;
	global $notimeupdate;
	global $_button, $_string;

//	global $x_ua_compatible;
	$w = new PukiWiki\Lib\File\WikiFile($page);

	// Newly generate $digest or not
	if ($digest === FALSE) $digest = $w->digest();

	$refer = $template = $addtag = $add_top = $add_ajax = null;

	$checked_top  = isset($vars['add_top'])     ? ' checked="checked"' : '';
	$checked_time = isset($vars['notimestamp']) ? ' checked="checked"' : '';
	$pages = DEBUG ? get_existpages() : auth::get_existpages();

	if($load_template_func && $b_template) {
		foreach($pages as $_page) {
			$_w = new WikiFile($_page);
			if (! $_w->isEditable() || $_w->isHidden())
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
	$addtag      = isset($vars['add']) ? '<input type="hidden" name="add" value="true" />' : '';

	// BugTrack/95 fix Problem: browser RSS request with session
	// $_SESSION[$s_ticket] = md5(get_ticket() . $digest);
	// $_SESSION['origin' . $s_ticket] = md5(get_ticket() . str_replace("\r", '', $s_original));

	$session->offsetSet($s_ticket,md5(get_ticket() . $digest));
	$session->offsetSet('origin'.$s_ticket, md5(get_ticket() . str_replace("\r", '', $s_original)));

	$add_notimestamp = '';
	if ($notimeupdate != 0 && is_page($page)) {
		// enable 'do not change timestamp'
		$add_notimestamp = <<<EOD
	<input type="checkbox" name="notimestamp" id="_edit_form_notimestamp" value="true"$checked_time />
	<label for="_edit_form_notimestamp" data-inline="true">{$_button['notchangetimestamp']}</label>
EOD;
		if ($notimeupdate == 2 && auth::check_role('role_adm_contents')) {
			// enable only administrator
			$add_notimestamp .= '<input type="password" name="pass" size="12" />';
		}
	}
	if (IS_MOBILE){
		$form = join("\n",array(
			'<input type="submit" id="btn_submit" name="write" value="'.$_button['update'].'" data-icon="check" data-inline="true" data-theme="b" />',
			'<input type="submit" id="btn_preview" name="preview" value="'.$_button['preview'].'" accesskey="p" data-icon="gear" data-inline="true" data-theme="e" />',
			'<input type="submit" id="btn_cancel" name="cancel" value="'.$_button['cancel'].'" accesskey="c" data-icon="delete" data-inline="true" />',
			($notimeupdate == 2 && auth::check_role('role_adm_contents')) ? '<div data-role="fieldcontain">' : '',
			($notimeupdate != 0 && is_page($page)) ? '<input type="checkbox" name="notimestamp" id="_edit_form_notimestamp" value="true" $checked_time />'.
				'<label for="_edit_form_notimestamp" data-inline="true">'.$_button['notchangetimestamp'].'</label>' : null,
			($notimeupdate == 2 && auth::check_role('role_adm_contents')) ? '<input type="password" name="pass" size="12"  data-inline="true" /></div>' : '',
			isset($vars['add']) ? "\t\t".'<input type="checkbox" name="add_top" value="true"' .$checked_top . ' /><label for="add_top">' . $_button['addtop'] . '</label>' : null
		));

EOD;
	}else{
		$form = <<<EOD
		<input type="submit" id="btn_submit" name="write" value="{$_button['update']}" accesskey="s" data-icon="check" data-inline="true" data-theme="b" />
		$add_top
		<input type="submit" id="btn_preview" name="preview" value="{$_button['preview']}" accesskey="p" data-icon="gear" data-inline="true" data-theme="e" />
		$add_notimestamp
		<input type="submit" id="btn_cancel" name="cancel" value="{$_button['cancel']}" accesskey="c" data-icon="delete" data-inline="true" />
EOD;
	}
	$refpage = isset($vars['refpage']) ? htmlsc($vars['refpage']) : '';

	$body = <<<EOD
<form action="$script" method="post" id="form">
	<input type="hidden" name="cmd"    value="edit" />
	<input type="hidden" name="page"   value="$s_page" />
	<input type="hidden" name="id"     value="$s_id" />
	<textarea id="original" name="original" rows="1" cols="1" style="display:none">$s_original</textarea>
	<div class="edit_form">
$template
$addtag
		<textarea name="msg" id="msg" rows="20" rows="80">$s_postdata</textarea>
		$form
	</div>
</form>

EOD;
	if (isset($vars['help'])) {
		$body .= $hr . catrule();
	} else {
		$body .= '<ul><li><a href="'.get_cmd_uri('edit',$r_page,'','help=true').'" id="FormatRule">' . $_string['help'] . '</a></li></ul>';
	}
	return $body;
}

// make related() moved to related.inc.php



// Check HTTP header()s were sent already, or
// there're blank lines or something out of php blocks '
function pkwk_headers_sent()
{
	global $_string;
	if (defined('PKWK_OPTIMISE')) return;

	$file = $line = '';

	if (headers_sent($file, $line)){
		die_message(sprintf($_string['header_sent'],htmlsc($file),$line));
	}else{
		// buffer all upcoming output - make sure we care about compression:
		if(!DEBUG){
			if (! @ob_start("ob_gzhandler")){
				@ob_start();
			}
		}
	}
}

/**
	@brief Output common HTTP headers

	@param modified 最終更新日時（秒）
	@param expire 有効期限（秒）
	@return なし
*/
use PukiWiki\Lib\Lang\Lang;
function pkwk_common_headers($modified = 0, $expire = 604800){
	global $lastmod, $vars, $response, $headers;
	if (! defined('PKWK_OPTIMISE')) pkwk_headers_sent();
	$response = new Response();

	// RFC2616
	// http://sonic64.com/2004-02-06.html
	$vary = Lang::getLanguageHeaderVary();
	if (preg_match('/\b(gzip|deflate|compress)\b/i', $_SERVER['HTTP_ACCEPT_ENCODING'], $matches)) {
		$vary .= ',Accept-Encoding';
	}
	$headers['Vary'] = $vary;

	// HTTP access control
	// JSON脆弱性対策（Adv.では外部にAjax APIを提供することを考慮しない）
	// https://developer.mozilla.org/ja/HTTP_Access_Control
	$headers['Access-Control-Allow-Origin'] = get_script_uri();

	// Content Security Policy
	// https://developer.mozilla.org/ja/Security/CSP/Using_Content_Security_Policy
	// header('X-Content-Security-Policy: allow "self" "inline-script";  img-src *; media-src *;');
	// IEの自動MIME type判別機能を無効化する
	// http://msdn.microsoft.com/ja-jp/ie/dd218497.aspx
	$headers['X-Content-Type-Options'] = 'nosniff';

	// クリックジャッキング対策
	// https://developer.mozilla.org/ja/The_X-FRAME-OPTIONS_response_header
	$headers['X-Frame-Options'] = 'SameDomain';

	// XSS脆弱性対策（これでいいのか？）
	// http://msdn.microsoft.com/ja-jp/ie/dd218482
	$headers['X-XSS-Protection'] = DEBUG ? '0' :'1;mode=block';

	if ($modified !== 0){
		// 最終更新日（秒で）が指定されていない場合動的なページとみなす。
		// PHPで条件付きGETとかEtagとかでパフォーマンス向上
		// http://firegoby.theta.ne.jp/archives/1730
		$last_modified = gmdate('D, d M Y H:i:s', $modified);

		$headers['Cache-Control'] = 'private';
		$headers['Expires'] = gmdate('D, d M Y H:i:s',time() + $expire) . ' GMT';
		$headers['Last-Modified'] = $last_modified;
		$headers['ETag'] = md5($last_modified);

		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ) {
			if ($_SERVER['HTTP_IF_MODIFIED_SINCE'] == $last_modified) {
				$response->setStatusCode(Response::STATUS_CODE_304);
				$response->renderStatusLine();
				exit;
			}
		}
		if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
			if (preg_match("/{$etag}/", $_SERVER['HTTP_IF_NONE_MATCH'])) {
				$response->setStatusCode(Response::STATUS_CODE_304);
				$response->renderStatusLine();
				exit;
			}
		}

//		header('If-Modified-Since: ' . $last_modified );

	}else{
		// PHPで動的に生成されるページはキャシュすべきではない
		$headers['Cache-Control'] = $headers['Pragma'] = 'no-cache';
		$headers['Expires'] = 'Sat, 26 Jul 1997 05:00:00 GMT';
	}
	$response->getHeaders()->addHeaders($headers);
	$response->setStatusCode(Response::STATUS_CODE_200);
	//pr($response->renderStatusLine());

}

function pkwk_common_suffixes($length = ''){
	// flush all output
	/*
	if(!DEBUG){
		@ob_end_flush();
	}
	*/
	flush();
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
	global $suffix, $browser, $info;
	$version = '';

	if (isset($called)) die('pkwk_output_dtd() already called. Why?');
	$called = TRUE;

	$option = '';
	switch($pkwk_dtd){
	case PKWK_DTD_HTML_5:
		$type    = false;
		break;

	case PKWK_DTD_XHTML_1_1:
		$version = '1.1' ;
		$dtd     = 'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd';
		$type    = PKWK_DTD_TYPE_XHTML;
		break;

	case PKWK_DTD_XHTML_1_0_STRICT:
		$version = '1.0' ;
		$option  = 'Strict';
		$dtd     = 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd';
		$type    = PKWK_DTD_TYPE_XHTML;
		break;

	case PKWK_DTD_XHTML_1_0_TRANSITIONAL:
		$version = '1.0' ;
		$option  = 'Transitional';
		$dtd     = 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd';
		$type    = PKWK_DTD_TYPE_XHTML;
		break;

	case PKWK_DTD_XHTML_BASIC_1_0:
		$version = '1.0' ;
		$option  = 'Basic';
		$dtd     = 'http://www.w3.org/TR/xhtml-basic/xhtml-basic10.dtd';
		$type    = PKWK_DTD_TYPE_XHTML;
		break;

	default:
		die('DTD not specified or invalid DTD');
		break;
	}

	$charset = htmlsc($charset);

	// Output XML or not
	if ($type === PKWK_DTD_TYPE_XHTML || PKWK_STRICT_XHTML === true){
		echo '<?xml version="1.0" encoding="' . CONTENT_CHARSET . '" ?' . '>' . "\n";
	}

	if ($pkwk_dtd !== PKWK_DTD_HTML_5){
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

	// HTML+RDFa 1.1 Support for RDFa in HTML4 and HTML5
	// http://www.w3.org/TR/rdfa-in-html/
	echo '<html version="HTML+RDFa 1.1"';
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
	unset($lang_code);

	if (!IS_MOBILE){
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		$info[] = $user_agent;
		if(preg_match_all('/MSIE ([\.\d]+)/',$user_agent,$matches)){
			// IE
			$browser = 'ie ie'.substr($matches[1][0],0,1);
		} else if (preg_match('/Gecko/', $user_agent) && preg_match('/(Firefox|Netscape?6)\/([\.\d]+)/', $user_agent,$matches)){
			// Gecko (FireFox)
			$browser = 'gecko '.strtolower($matches[1]).substr($matches[2][0],0,1);
		} else if (preg_match('/(Presto)\/([\.\d]+)/', $user_agent, $matches)){
			// Opera
			$browser = strtolower($matches[1]).' '.strtolower($matches[1]).substr($matches[2],0,1);
		} else if (preg_match('/(WebKit|KHTML|Konqueror)/', $user_agent)){
			// Safari
			$browser = 'webkit';
		} else if (preg_match('/(Nintendo|Sony|Dreamcast|NetFront)/', $user_agent)){
			// ゲーム機はNetFront扱い
			$browser = 'netfront';
		}
		unset($matches);

		global $facebook;
		if (isset($facebook)){
			echo ' xmlns:fb="http://www.facebook.com/2008/fbml"';
		}

		echo ' xmlns:og="http://ogp.me/ns#"';
	}
	echo ' class="no-js '.$browser.'">' . "\n"; // <html>

	if ($pkwk_dtd == PKWK_DTD_HTML_5 || IS_MOBILE){
		$meta_tags[] = array('charset'	=> CONTENT_CHARSET);
	}else{
		if (!isset($x_ua_compatible)) $x_ua_compatible = 'IE=edge';
		$meta_tags = array(
			array('http-equiv'	=> 'content-type',				'content'	=> 'text/html; charset='.CONTENT_CHARSET),
			array('http-equiv'	=> 'content-language',			'content'	=> $lang_code),
			array('http-equiv'	=> 'content-style-type',		'content'	=> 'text/css'),
			array('http-equiv'	=> 'content-script-type',		'content'	=> 'text/javascript'),
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
				$content = ($tagname !== 'style') ? "/".'*<![CDATA[*'."/\n".$val."\n/".'*]]>*'.'/' : '//<![CDATA['. "\n". $val . "\n".'//]]>';
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

/* End of file html.php */
/* Location: ./wiki-common/lib/html.php */
