<?php
// PukiPlus common Scripts module - Yet another WikiWikiWeb clone.
// $Id: module.skin.php,v 2.2.7 2010/07/05 23:58:00 upk Exp $


/*
UI Themes
jQuery(jQuery UI): 
	base, black-tie, blitzer, cupertino, dark-hive, dot-luv, eggplant, excite-bike, flick, hot-sneaks
	humanity, le-frog, mint-choc, overcast, pepper-grinder, redmond, smoothness, south-street,
	start, sunny, swanky-purse, trontastic, ui-darkness, ui-lightness, vader
*/
$ui_theme = 'base';

$files = array(
	/* swfupload */
	'swfupload',
	/* Use plugins */ 
	'jquery.colorbox', 'jquery.cookie','jquery.lazyload', 'jquery.query','jquery.scrollTo',
	'jquery.superfish','jquery.swfupload','jquery.tablesorter','jquery.textarearesizer', 'jquery.jplayer.min',
	
	/* MUST BE LOAD LAST */
	'skin.original'
);

$debug = false;
?>
		<script type="text/javascript" src="http://www.google.com/jsapi"></script>
		<script type="text/javascript">
//<![CDATA[
google.load("jquery", "1.4.2");
google.load("jqueryui", "1.8.4");
google.load("swfobject", "2.2");
<?php
if ($lastmodified) echo 'var MODIFIED = "'.get_filetime($r_page).'";'."\n";
if ($r_page) echo 'var PAGE = "'.$r_page.'";'."\n";
if (exist_plugin_convert('js_init')) echo do_plugin_convert('js_init');
?>
var SCRIPT = "<?php echo $script ?>";
var THEME_NAME = "<?php echo PLUS_THEME; ?>";
//]]></script>
		<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/<?php echo $ui_theme; ?>/jquery-ui.css" type="text/css" />
		<script type="text/javascript" src="<?php echo SKIN_URI; ?>js/locale.js"></script>
<?php 
if ($use_local_time && exist_plugin_convert('tz')) echo "\t\t".'<script type="text/javascript" src="'.SKIN_URI.'tzCalculation_LocalTimeZone.js"></script>'."\n";
echo $head_tag;
if ($debug == true){
	foreach ($files as $script_file) {
		echo "\t\t".'<script type="text/javascript" src="'.SKIN_URI.'js/src/'.$script_file.'.js"></script>'."\n";
	}
}else{
	echo "\t\t".'<script type="text/javascript" src="'.SKIN_URI.'js/skin.js.php"></script>'."\n";
}
?>
