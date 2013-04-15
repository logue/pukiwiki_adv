<?php
/**
 * PukiWiki Plus! epoch plugin.
 *
 * @copyright   Copyright &copy; 2008, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @version     $Id: epoch.inc.php,v 0.4 2012/10/11 09:09:00 Logue Exp $
 * @license     http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 *  for BugTrack/83
 *
 * &epoch(1234578098);
 * &epoch(1234578098,[class name, such as 'comment_date']);
 */

// 1day = 86400;
use PukiWiki\Utility;
use PukiWiki\Time;

function plugin_epoch_inline()
{
	$value = func_get_args();
	$args = func_num_args();

	if ($args > 3){
		return '&epoch(utime[,class]);';
	}
	
	$array = explode(',',$value[0]);
	
	$format = Time::format($array[0]);
	$passaage = Time::passage($array[0]);
	
	$class = (!empty($array[1])) ? $array[1] : 'epoch';


	$ret = '<time datetime="'.get_date('c',$value[0]).'" class="'.$class.'" title="'.$passaage.'">'.$format.'</time>';
	
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
