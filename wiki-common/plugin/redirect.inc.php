<?php
/**
 * リダイレクトプラグイン
 *
 * @copyright   Copyright &copy; 2006,2008, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @version     $Id: redirect.inc.php,v 0.3.1 2012/05/15 20:29:00 upk Exp $
 * @license     http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

use PukiWiki\Renderer\Inline\Inline;
use PukiWiki\Utility;

function plugin_redirect_action()
{
	global $vars;
	if (empty($vars['u'])) return '';

	// 自サイトからのリダイレクトのみ飛ばす
	if (path_check($_SERVER['HTTP_REFERER'],get_script_absuri())) {
		Utility::redirect($vars['u']);
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
	$argv = func_get_args();
	$argc = func_num_args();

	$field = array('caption','url','img');
	for($i=0; $i<$argc; $i++) {
		$$field[$i] = Utility::htmlsc($argv[$i], ENT_QUOTES);
	}

	if (empty($url)) return 'usage: #redirect(caption, url, img)';
	if (empty($caption)) $caption = 'no title';

	if (! empty($img)) {
		$caption = '<img src="'.$img.'" alt="'.$caption.'" title="'.$caption.'" />';
	}
	return Inline::setLink($caption, $url, null, 'noreferer', true);
}

/* End of file redirect.inc.php */
/* Location: ./wiki-common/plugin/redirect.inc.php */
