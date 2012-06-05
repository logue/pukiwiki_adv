<?php
/**
 * PukiWiki Plus! epoch plugin.
 *
 * @copyright   Copyright &copy; 2008, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @version     $Id: epoch.inc.php,v 0.3 2011/02/05 10:49:00 Logue Exp $
 * @license     http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 *  for BugTrack/83
 *
 * &epoch(1234578098);
 * &epoch(1234578098,[class name, such as 'comment_date']);
 */

// 1day = 86400;

function plugin_epoch_inline()
{
	global $pkwk_dtd;
	$args = func_get_args();

	if (func_num_args() > 2){
		return '&new(utime[,class]);';
	}
	$value = explode(",",$args[0]);
	
	$format = format_date($value[0]);
	$passaage = get_passage($value[0]);
	
	$class = (!empty($value[1])) ? $value[1] : 'epoch';

	if ($pkwk_dtd == PKWK_DTD_HTML_5) {
		$ret = '<time datetime="'.get_date('c',$value[0]).'" class="'.$class.'" title="'.$passaage.'">'.$format.'</time>';
	}else{
		$ret = '<small class="'.$class.'" title="'.$passaage.'">'.$format.'</small>';
	}
	if (!empty($value[1])){
		$erapse = MUTIME - $value[0];
		
		if ($erapse < 432000){
			$ret .= ' <span class="';
			if ($erapse < 86400){
				$ret .= 'new1';
			}else{
				$ret .= 'new5';
			}
			$ret .= '">New</span>';
		}
	}
	
	return $ret;
}

/* End of file epoch.inc.php */
/* Location: ./wiki-common/plugin/epoch.inc.php */
