<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: ref.inc.php,v 1.56.6 2014/03/09 19:11:00 Logue Exp $
// Copyright (C)
//   2010-2012, 2015 PukiWiki Advance Developers Team
//   2002-2006, 2011 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// Image refernce plugin
// Include an attached image-file as an inline-image

use \SplFileInfo;
use \SplFileObject;
use PukiWiki\Attach;
use PukiWiki\Renderer\Header;
use PukiWiki\Renderer\Inline\Inline;
use PukiWiki\Renderer\Inline\InterWikiName;
use PukiWiki\Renderer\RendererDefines;
use PukiWiki\Router;
use PukiWiki\Text\Rules;
use PukiWiki\Utility;
use Zend\Http\Response;

/////////////////////////////////////////////////
// Default settings

// Horizontal alignment
define('PLUGIN_REF_DEFAULT_ALIGN', 'left'); // 'left', 'center', 'right', 'justify'

// NOT RECOMMENDED: getimagesize($uri) for proper width/height
define('PLUGIN_REF_URL_GET_IMAGE_SIZE', FALSE); // FALSE, TRUE

/////////////////////////////////////////////////

// Usage (a part of)
define('PLUGIN_REF_USAGE', '([pagename/]attached-file-name[,parameters, ... ][,title])');

function plugin_ref_inline()
{
	// NOTE: Already "$aryargs[] = & $body" at plugin.php
	if (func_num_args() == 1) {
		return Utility::htmlsc('&ref(): Usage:' . PLUGIN_REF_USAGE . ';');
	}

	$params = plugin_ref_body(func_get_args());
	if (isset($params['_error'])) {
		return '<span class="text-warning">' . Utility::htmlsc('#ref(): ERROR: ' . $params['_error']) . '</span>' . "\n";
	}
	if (! isset($params['_body'])) {
		return '<span class="text-warning">' . Utility::htmlsc('#ref(): ERROR: No _body') . '</span>' . "\n";
	}

	return $params['_body'];
}

function plugin_ref_convert()
{
	if (! func_num_args()) {
		return '<div class="alert alert-warning">' . Utility::htmlsc('#ref(): Usage:' . PLUGIN_REF_USAGE) . '</div>' . "\n";
	}

	$params = plugin_ref_body(func_get_args());
	if (isset($params['_error'])) {
		return '<div class="alert alert-warning">' . Utility::htmlsc('#ref(): ERROR: ' . $params['_error']) . '</div>' . "\n";
	}
	if (! isset($params['_body'])) {
		return '<div class="alert alert-warning">' . Utility::htmlsc('#ref(): ERROR: No _body') . '</div>' . "\n";
	}

	$class = array();
	if ($params['around']) {
		$class[] = ($params['_align'] == 'right') ? 'pull-right' : 'pull-left';
	} else {
		$class[] = 'text-' . $params['_align'];
	}
	if ($params['rounded']){
		$class[] = ' img-rounded';
	}else if ($params['circle']){
		$class[] = ' img-circle';
	}
	if ($params['thumbnail']){
		$class[] = ' img-thumbnail';
	}

	return '<figure class="'.join(' ', $class).'">'.$params['_body'].'</figure>'."\n";
}

// Common function
function plugin_ref_body($args)
{
	global $vars;

	$page = isset($vars['page']) ? $vars['page'] : '';

	$params = array(
		// Options
		'left'   => FALSE, // Align
		'center' => FALSE, //      Align
		'right'  => FALSE, //           Align
		'justify'=> FALSE, // A   l   i   g   n
		'around' => FALSE, // Text wrap around or not
		'noicon' => FALSE, // Suppress showing icon
		'noimg'  => FALSE, // Suppress showing image
		
		'nolink' => FALSE, // Suppress link to image itself
		'zoom'   => FALSE, // Lock image width/height ratio as the original said

		// Adv.
		'rounded'   => FALSE,   // 角を丸くする
		'circle'    => FALSE,   // 丸い画像にする
		'thumbnail' => FALSE,   // 枠をつける
		'novideo'   => FALSE,    // ビデオを展開しない
		'noaudio'   => FALSE,    // 音声を展開しない

		// Flags and values
		'_align' => PLUGIN_REF_DEFAULT_ALIGN,
		'_size'  => True, // Image size specified
		'_w'     => 0,     // Width
		'_h'     => 0,     // Height
		'_%'     => 0,     // Percentage
		'_title' => null,  // Reserved
		'_body'  => null,  // Reserved
		'_error' => null,  // Reserved,
		'_class' => ''
	);

	// [Page_name/maybe-separated-with/slashes/]AttachedFileName.sfx or URI
	$name    = array_shift($args);	// ファイル名
	
	
	// 第一引数が InterWiki か
	if(Utility::isInterWiki($name)) {
		preg_match('/^' . RendererDefines::INTERWIKINAME_PATTERN . '$/', $name, $intermatch);
		$intername = $intermatch[2];
		$interparam = $intermatch[3];
		$interurl = InterWikiName::getInterWikiUrl($intername,$interparam);
		if ($interurl !== FALSE) {
			$name = $interurl;
		}
	}
	$is_url  = Utility::isUri($name);	// アドレスか？
	
	// 画像
	$seems_image = (! $params['noimg'] && preg_match(RendererDefines::IMAGE_EXTENTION_PATTERN, $name));
	// ビデオ
	$seems_video = (! $params['novideo'] && preg_match(RendererDefines::VIDEO_EXTENTION_PATTERN, $name));
	// 音声
	$seems_audio = (! $params['noaudio'] && preg_match(RendererDefines::AUDIO_EXTENTION_PATTERN, $name));

	$file    = ''; // Path to the attached file
	$is_file = FALSE;

	if(! $is_url) {
		if (! is_dir(UPLOAD_DIR)) {
			$params['_error'] = 'UPLOAD_DIR is not found.';
			return $params;
		}

		$matches = array();
		if (preg_match('#^(.+)/([^/]+)$#', $name, $matches)) {
			// Page_name/maybe-separated-with/slashes and AttachedFileName.sfx
			// #ref(ページ名/ファイル名)　新形式の表記
			if ($matches[1] === '.' || $matches[1] === '..') {
				// 相対パスでの指定
				$matches[1] .= '/'; // Restore relative paths
			}
			// ファイル名を取得
			$name    = $matches[2]; // AttachedFileName.sfx
			$page    = Utility::getPageName($matches[1], $page); // strip is a compat
		} else if (isset($args[0]) && !empty($args[0]) && ! isset($params[$args[0]])) {
			// Is the second argument a page-name or a path-name? (compat)
			// #ref(ファイル名,ページ名)　古い形式の表記
			$_page = array_shift($args);	// 引用元のページ名

			// Looks like WikiName, or double-bracket-inserted pagename? (compat)
			$is_bracket_bracket = preg_match('/^(' . RendererDefines::WIKINAME_PATTERN . '|\[\[' . RendererDefines::BRACKETNAME_PATTERN . '\]\])$/', $_page);

			$page   = Utility::getPageName(Utility::stripBracket($_page), $page); // strip is a compat

			$a = new Attach($_page, $name);

			if (! $is_bracket_bracket || ! $a->has()) {
				// Promote new design
				if ($is_file && is_file(UPLOAD_DIR . encode($page) . '_' . encode($name))) {
					// Because of race condition NOW
					$params['_error'] =
						'The same file name "' . $name . '" at both page: "' .
						$page . '" and "' .  $_page .
						'". Try ref(pagename/filename) to specify one of them';
				} else {
					// Because of possibility of race condition, in the future
					$params['_error'] =
						'The style ref(filename,pagename) is ambiguous ' .
						'and become obsolete. ' .
						'Please try ref(pagename/filename)';
				}
				return $params;
			}

			$page = $_page; // Suppose it
		}
		
		// Attachオブジェクトを生成
		$a = new Attach($page, $name);

		if (! $a->has()) {
			$params['_error'] = 'File not found: "' . Utility::htmlsc($name) . '" at page "' . Utility::htmlsc($page) . '"';
			return $params;
		}
	}
	
	
	
	// 残りの引数の処理
	if (! empty($args)) {
		$keys = array_keys($params);
		$params['_done'] = false;
		foreach($args as $val) {
			list($_key, $_val) = array_pad(explode(':', $val, 2), 2, TRUE);
			$_key = trim(strtolower($_key));
			if (is_string($_val)) $_val = trim($_val);
			if (in_array($_key, $keys) && $params['_done'] !== TRUE) {
				$params[$_key] = $_val;    // Exist keys
			} elseif ($val != '') {
				$params['_args'][] = $val; // Not exist keys, in '_args'
			}
		}
	}

	$width = $height = 0;
	$url   = $url2   = '';

	if ($is_url) {
		// 外部リンクの場合
		$url  = $name;
		$url2 = $name;
		if (PKWK_DISABLE_INLINE_IMAGE_FROM_URI) {
			//$params['_error'] = 'PKWK_DISABLE_INLINE_IMAGE_FROM_URI prohibits this';
			//return $params;
			$params['_body'] = '<a href="' . $url . '" rel="external">' . $s_url . '</a>';
			return $params;
		}
		$matches = array();
		$params['_title'] = preg_match('#([^/]+)$#', $url, $matches) ? $matches[1] : $url;
	} else {
		// Wikiの添付ファイルの場合
		// Count downloads with attach plugin
		$url = Router::get_cmd_uri('attach', null, null, array('refer'=>$page, 'openfile'=>$name));
		$url2 = '';
		$params['_title'] = $name;

		if ($seems_image || $seems_video || $seems_audio) {
			// URI for in-line image output
			$url2 = $url;

			// With ref plugin (faster than attach)
			$url = Router::get_cmd_uri('ref', $page, null, array('src'=>$name));

			if ($seems_image) {
				// 画像の場合は、getimagesizeでサイズを読み取る
				$size = getimagesize($a->path);
				if (is_array($size)) {
					$params['_w'] = $size[0];
					$params['_h'] = $size[1];
				}
			}
		}
	}
	
	// 拡張パラメータをチェック
	if (! empty($params['_args'])) {
		$_title = array();
		foreach ($params['_args'] as $arg) {
			if (preg_match('/^([0-9]+)x([0-9]+)$/', $arg, $matches)) {
				$params['_size'] = TRUE;
				$params['_w'] = $matches[1];
				$params['_h'] = $matches[2];
			} else if (preg_match('/^([0-9.]+)%$/', $arg, $matches) && $matches[1] > 0) {
				$params['_%'] = $matches[1];
			} else {
				$_title[] = $arg;
			}
		}
	}
	foreach (array('right', 'left', 'center', 'justify') as $align) {
		if (isset($params[$align])) {
			$params['_align'] = $align;
			unset($params[$align]);
			break;
		}
	}

	$s_title = isset($params['_title']) ? Inline::setLineRules(Utility::htmlsc($params['_title'])) : '';
	$s_info  = '';

	if ($seems_image || $seems_video) {
		// 指定されたサイズを使用する
		
		$info = '';
		if ($width === 0 && $height === 0) {
			$width  = $params['_w'];
			$height = $params['_h'];
		}
	
		if ($params['_size']) {
			if ($params['zoom']) {
				$_w = $params['_w'] ? $width  / $params['_w'] : 0;
				$_h = $params['_h'] ? $height / $params['_h'] : 0;
				$zoom = max($_w, $_h);
				if ($zoom) {
					$width  = (int)($width  / $zoom);
					$height = (int)($height / $zoom);
				}
			} else {
				$width  = $params['_w'] ? $params['_w'] : $width;
				$height = $params['_h'] ? $params['_h'] : $height;
			}
		}
		if ($params['_%']) {
			$width  = (int)($width  * $params['_%'] / 100);
			$height = (int)($height * $params['_%'] / 100);
		}
		$info = $width && $height ? 'width="' . $width . '" height="' . $height .'" ' : '';
		
		if ($seems_image) {
			$body = '<img src="' . $url   . '" ' .
				'alt="'      . $s_title . '" ' .
				'title="'    . $s_title . '" ' .
				'class="'    . $params['_class'] . '" ' .
				$info . '/>';
		}else if ($seems_video) {
			$body = '<video src="' . $url   . '" ' .
				'alt="'      . $s_title . '" ' .
				'title="'    . $s_title . '" ' .
				'class="'    . $params['_class'] . '" ' .
				$s_info . '/>';
		}
		if (! isset($params['nolink']) && $url2) {
			$params['_body'] =
				'<a href="' . $url2 . '" title="' . $s_title . '"'. ((IS_MOBILE) ? ' data-ajax="false"' : '') . '>' . "\n" .
				$body . "\n" . '</a>';
		} else {
			$params['_body'] = $body;
		}
	} else if ($seems_audio) {
		// 音声
		$body = '<audio src="' . $url   . '" ' .
			'alt="'      . $s_title . '" ' .
			'title="'    . $s_title . '" ' .
			'class="'    . $params['_class'] . '" />';
		if (! isset($params['nolink']) && $url2) {
			$params['_body'] =
				'<a href="' . $url2 . '" title="' . $s_title . '"'. ((IS_MOBILE) ? ' data-ajax="false"' : '') . '>' . "\n" .
				$body . "\n" . '</a>';
		} else {
			$params['_body'] = $body;
		}
	} else {
		// リンクを貼り付ける
		if (! $is_url && $is_file) {
			$s_info = Utility::htmlsc(get_date('Y/m/d H:i:s', filemtime($file) /*- LOCALZONE*/) .
				' ' . sprintf('%01.1f', round(filesize($file) / 1024, 1)) . 'KB');
		}
		$params['_body'] = '<a href="' . $url . '" title="' . $s_info . '"'. ((IS_MOBILE) ? ' data-ajax="false"' : '') . '>' .
			(isset($params['noicon']) ? '' : '<span class="fa fa-download"></span>') . $s_title . '</a>';
	}

	return $params;
}

// Output an image (fast, non-logging <==> attach plugin)
function plugin_ref_action()
{
	global $vars, $use_sendfile_header;

	$usage = 'Usage: cmd=ref&amp;page=page_name&amp;src=attached_image_name';

	if (! isset($vars['page']) || ! isset($vars['src']))
		return array('msg' => 'Invalid argument', 'body' => $usage);

	$page     = $vars['page'];
	$filename = $vars['src'] ;

	$attach = new Attach($page, $filename);
	$attach->render();
	exit;
}
/* End of file ref.inc.php */
/* Location: ./wiki-common/plugin/ref.inc.php */
