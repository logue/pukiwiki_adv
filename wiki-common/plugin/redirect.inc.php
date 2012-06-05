<?php
/**
 * リダイレクトプラグイン
 *
 * @copyright   Copyright &copy; 2006,2008, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @version     $Id: redirect.inc.php,v 0.3.1 2012/05/15 20:29:00 upk Exp $
 * @license     http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

function plugin_redirect_action()
{
	global $vars;
	if (empty($vars['u'])) return '';

	// 自サイトからのリダイレクトのみ飛ばす
	if (path_check($_SERVER['HTTP_REFERER'],get_script_absuri())) {
		// 以下の方法は、NG です。
		// <a href="javascript:location.replace('URL');">Caption</a>
		//header('Location: ' . $vars['u'] );
		//die();
		$time = 0;
		echo <<<EOD
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<meta http-equiv="Refresh" content="$time;URL={$vars['u']}" />
		<title>Auto Redirect</title>
	</head>
	<body>
		<article><a href="{$vars['u']}">Please click here.</a></article>
	</body>
</html>
EOD;
		die();
	}

	return '';
}

function plugin_redirect_inline()
{
	$args = func_get_args();
	array_pop($args);
	return call_user_func_array('plugin_redirect_convert', $args);
}

function plugin_redirect_convert()
{
	global $script;

	$argv = func_get_args();
	$argc = func_num_args();

	$field = array('caption','url','img');
	for($i=0; $i<$argc; $i++) {
		$$field[$i] = htmlspecialchars($argv[$i], ENT_QUOTES);
	}

	if (empty($url)) return 'usage: #redirect(caption, url, img)';
	if (empty($caption)) $caption = 'no title';

	if (! empty($img)) {
		$caption = '<img src="'.$img.'" alt="'.$caption.'" title="'.$caption.'" />';
	}
	$anchor = '<a href="' . get_cmd_uri('redirect', null, null, array('u'=>$url)) . '" rel="nofollow">' . $caption . '</a>';

	return open_uri_in_new_window($anchor);
}

/* End of file redirect.inc.php */
/* Location: ./wiki-common/plugin/redirect.inc.php */
