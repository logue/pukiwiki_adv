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
use PukiWiki\Renderer\RendererDefines;
use PukiWiki\Text\Rules;
use PukiWiki\Utility;
use Zend\Http\Response;

/////////////////////////////////////////////////
// Default settings

// Horizontal alignment
define('PLUGIN_REF_DEFAULT_ALIGN', 'left'); // 'left', 'center', 'right', 'justify'

// NOT RECOMMENDED: getimagesize($uri) for proper width/height
define('PLUGIN_REF_URL_GET_IMAGE_SIZE', FALSE); // FALSE, TRUE

// DANGER, DO NOT USE THIS: Allow direct access to UPLOAD_DIR
define('PLUGIN_REF_DIRECT_ACCESS', FALSE); // FALSE or TRUE
// - This is NOT option for acceralation but old and compatible.
// - Apache: UPLOAD_DIR/.htaccess will prohibit this usage.
// - Browsers: This usage contains any proper mime-type, so
//   some ones will not show proper result. And may cause XSS.

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

	$class = '';
	if (isset($params['around'])) {
		$class .= ($params['_align'] == 'right') ? 'pull-right' : 'pull-left';
	} else {
		$class .= 'text-' . $params['_align'];
	}

	return '<figure class="'.$class.'">'.$params['_body'].'</figure>'."\n";
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
//		'wrap'   => FALSE, // Wrap the output with table ...
//		'nowrap' => FALSE, //   or not
		'around' => FALSE, // Text wrap around or not
		'noicon' => FALSE, // Suppress showing icon
		'noimg'  => FALSE, // Suppress showing image
		
		'nolink' => FALSE, // Suppress link to image itself
		'zoom'   => FALSE, // Lock image width/height ratio as the original said

		// Adv.
		'borderd'   => FALSE,   // 枠をつける
		'rounded'   => FALSE,   // 角を丸くする
		'circle'    => FALSE,   // 丸い画像にする
		'novideo'  => FALSE,    // ビデオを展開しない
		'noaudio'  => FALSE,    // 音声を展開しない

		// Flags and values
		'_align' => PLUGIN_REF_DEFAULT_ALIGN,
		'_size'  => FALSE, // Image size specified
		//'_w'     => 0,     // Width
		//'_h'     => 0,     // Height
		//'_%'     => 0,     // Percentage
		//'_title' => null,  // Reserved
		//'_body   => null,  // Reserved
		'_error' => null,  // Reserved,
		'_class' => ''
	);

	// [Page_name/maybe-separated-with/slashes/]AttachedFileName.sfx or URI
	$name    = array_shift($args);
	$is_url  = Utility::isUri($name);

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
			$params['_error'] = 'File not found: "' .
				$name . '" at page "' . $page . '"';
			return $params;
		}
	}

	// パラメータを取得
	$params = ref_check_args($args, $params);

	// 画像
	$seems_image = (! isset($params['noimg']) && preg_match(RendererDefines::IMAGE_EXTENTION_PATTERN, $name));
	// ビデオ
	$seems_video = (! isset($params['novideo']) && preg_match(RendererDefines::VIDEO_EXTENTION_PATTERN, $name));
	// 音声
	$seems_audio = (! isset($params['noaudio']) && preg_match(RendererDefines::AUDIO_EXTENTION_PATTERN, $name));

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

		if ($seems_image && PLUGIN_REF_URL_GET_IMAGE_SIZE && (bool)ini_get('allow_url_fopen')) {
			// PLUGIN_REF_URL_GET_IMAGE_SIZEが有効でかつallow_url_fopenが使用可能の場合、HTTP越しに画像サイズを取得する
			$size = @getimagesize($name);
			if (is_array($size)) {
				$width  = $size[0];
				$height = $size[1];
			}
		}
	} else {
		// Count downloads with attach plugin
		$url = get_cmd_uri('attach', null, null, array('refer'=>$page, 'openfile'=>$name));
		$url2 = '';
		$params['_title'] = $name;

		if ($seems_image || $seems_video || $seems_audio) {
			// URI for in-line image output
			$url2 = $url;
			if (PLUGIN_REF_DIRECT_ACCESS) {
				$url = $file; // Try direct-access, if possible
			} else {
				// With ref plugin (faster than attach)
				$url = get_cmd_uri('ref', $page, null, array('src'=>$name));
			}
			if ($seems_image) {
				$size = @getimagesize($file);
				if (is_array($size)) {
					$width  = $size[0];
					$height = $size[1];
				}
			}
		}
	}

	$s_title = isset($params['_title']) ? Inline::setLineRules(Utility::htmlsc($params['_title'])) : '';
	$s_info  = '';

	if ($seems_image) {
		// 画像
		
		if (ref_check_size($width, $height, $params) && isset($params['_w']) && isset($params['_h'])) {
			// サイズ
			$s_info = 'width="'  . Utility::htmlsc($params['_w']) .
			        '" height="' . Utility::htmlsc($params['_h']) . '" ';
		}
		$body = '<img src="' . $url   . '" ' .
			'alt="'      . $s_title . '" ' .
			'title="'    . $s_title . '" ' .
			'class="'    . $params['_class'] . '" ' .
			$s_info . '/>';
		if (! isset($params['nolink']) && $url2) {
			$params['_body'] =
				'<a href="' . $url2 . '" title="' . $s_title . '"'. ((IS_MOBILE) ? ' data-ajax="false"' : '') . '>' . "\n" .
				$body . "\n" . '</a>';
		} else {
			$params['_body'] = $body;
		}
	} else if ($seems_video) {
		// ビデオ
		if (ref_check_size($width, $height, $params) &&
		    isset($params['_w']) && isset($params['_h'])) {
			$s_info = 'width="'  . Utility::htmlsc($params['_w']) .
			        '" height="' . Utility::htmlsc($params['_h']) . '" ';
		}
		$body = '<video src="' . $url   . '" ' .
			'alt="'      . $s_title . '" ' .
			'title="'    . $s_title . '" ' .
			'class="'    . $params['_class'] . '" ' .
			$s_info . '/>';
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

function ref_check_args($args, $params)
{
	if (! is_array($args) || ! is_array($params)) return;

	$_args   = array();
	$_title  = array();
	$matches = array();

	if (isset($_args['rounded'])){
		$params['_class'] = 'img-rounded';
	}else if (isset($_args['circle'])){
		$params['_class'] = 'img-circle';
	}
	if (isset($_args['thumbnail'])){
		$params['_class'] .= ' img-thumbnail';
	}

	foreach ($args as $arg) {
		$hit = FALSE;
		if (! empty($arg) && ! preg_match('/^_/', $arg)) {
			$larg = strtolower($arg);
			foreach (array_keys($params) as $key) {
				if (strpos($key, $larg) === 0) {
					$hit          = TRUE;
					$params[$key] = TRUE;
					break;
				}
			}
		}
		if (! $hit) $_args[] = $arg;
	}
	

	foreach ($_args as $arg) {
		if (preg_match('/^([0-9]+)x([0-9]+)$/', $arg, $matches)) {
			$params['_size'] = TRUE;
			$params['_w']    = intval($matches[1]);
			$params['_h']    = intval($matches[2]);
		} else if (preg_match('/^([0-9.]+)%$/', $arg, $matches) && $matches[1] > 0) {
			$params['_%']    = intval($matches[1]);
		} else {
			$_title[] = $arg;
		}
	}
	unset($_args);
	$params['_title'] = join(',', $_title);
	unset($_title);
	foreach(array_keys($params) as $key) {
		if (! preg_match('/^_/', $key) && empty($params[$key])) {
			unset($params[$key]);
		}
	}

	foreach (array('right', 'left', 'center') as $align) {
		if (isset($params[$align])) {
			$params['_align'] = $align;
			unset($params[$align]);
			break;
		}
	}
	
	return $params;
}

function ref_check_size($width = 0, $height = 0, & $params)
{
	if (! is_array($params)) return FALSE;

	$width   = intval($width);
	$height  = intval($height);
	$_width  = isset($params['_w']) ? intval($params['_w']) : 0;
	$_height = isset($params['_h']) ? intval($params['_h']) : 0;

	if (isset($params['_size'])) {
		if ($width == 0 && $height == 0) {
			$width  = $_width;
			$height = $_height;
		} else if (isset($params['zoom'])) {
			$_w = $_width  ? $width  / $_width  : 0;
			$_h = $_height ? $height / $_height : 0;
			$zoom = max($_w, $_h);
			if ($zoom) {
				$width  = $width  / $zoom;
				$height = $height / $zoom;
			}
		} else {
			$width  = $_width  ? $_width  : $width;
			$height = $_height ? $_height : $height;
		}
	}

	if (isset($params['_%'])) {
		$width  = $width  * $params['_%'] / 100;
		$height = $height * $params['_%'] / 100;
	}

	$params['_w'] = intval($width);
	$params['_h'] = intval($height);

	return ($params['_w'] && $params['_h']);
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
