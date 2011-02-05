<?php
/**
 * PukiWiki Plus! epoch plugin.
 *
 * @copyright   Copyright &copy; 2008, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @version     $Id: epoch.inc.php,v 0.2 2011/02/05 10:49:00 Logue Exp $
 * @license     http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 *  for BugTrack/83
 *
 * &epoch(1234578098);
 * &epoch(1234578098,new);
 * &epoch(1234578098){2006-06-27 (火) 14:10:56};
 * &epoch(1234578098,new){2006-06-27 (火) 14:10:56};
 */

function plugin_epoch_inline()
{
	global $pkwk_dtd;
	list($time) = func_get_args();
	$format = htmlsc(format_date($time));

	if ($pkwk_dtd == PKWK_DTD_HTML_5) {
		return '<time datetime="'.get_date('c',$time).'">'.$format.'</time>';
	}else{
		return $format;
	}
}

?>
