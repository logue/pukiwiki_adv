<?php
/**
 * tzCalculation_LocalTimeZone Plugin
 *
 * @copyright   Copyright &copy; 2006,2008, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @version	$Id: tz.inc.php,v 0.3.1 2010/08/15 15:41:00 Logue Exp $
 * @license	http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link	http://www.desisoftsystems.com/white-papers/timeZoneCalculation/
 */
function plugin_tz_convert(){
	global $use_local_time, $head_tags, $foot_tags;

	if ($use_local_time) return '';
	if (isset($_COOKIE['timezone'])) return '';
	$url = parse_url( get_script_absuri() );

	if (empty($url['host'])) return '';
	
	$head_tags[] = '<script type="text/javascript" src="'.SKIN_URI.'js/plugin/tzCalculation_LocalTimeZone.js"></script>';
	$foot_tags[] =  <<<EOD
<script type="text/javascript">
//<![CDATA[
	tzCalculation_LocalTimeZone ('{$url['host']}',false);
//]]></script>
EOD;
}

?>
