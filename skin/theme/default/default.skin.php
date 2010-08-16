<?php
/////////////////////////////////////////////////
// PukiPlus - Yet another WikiWikiWeb clone.
//
// PukiWiki Plus! skin
// Original version by miko and upk.
//
// $Id: default.skin.php,v 1.4.14 2010/08/16 20:44:00 Logue Exp $
//
global $notify_from, $menubar, $google_site_verification, $google_api_key;

if (!defined('DATA_DIR')) { exit; }

$theme = 'default';
$theme_path = SKIN_URI.THEME_PLUS_NAME.$theme.'/';
// $site_titleimage = $theme_path.'your.site.logo.png';
$site_titleimage = IMAGE_URI.'pukiwiki_adv.logo.png';
$iphone_icon = IMAGE_URI.'pukiwiki_adv.iphone.png';
$titleimage_alt  = '[PukiWiki Adv.]';
$titleimagesize_w = '80';
$titleimagesize_h = '80';

// Navibar系プラグインでもアイコンを表示する
$showicon     = ture;

// Decide charset for CSS
// $css_charset = 'iso-8859-1';
$css_charset = 'utf-8';
switch(UI_LANG){
	case 'ja_JP': $css_charset = 'Shift_JIS'; break;
}

/*
UI Themes
jQuery(jQuery UI): 
	base, black-tie, blitzer, cupertino, dark-hive, dot-luv, eggplant, excite-bike, flick, hot-sneaks
	humanity, le-frog, mint-choc, overcast, pepper-grinder, redmond, smoothness, south-street,
	start, sunny, swanky-purse, trontastic, ui-darkness, ui-lightness, vader

see also
http://www.devcurry.com/2010/05/latest-jquery-and-jquery-ui-theme-links.html
http://jqueryui.com/themeroller/
*/
$ui_theme = 'redmond';

$files = array(
	/* libraly */
	'swfupload',
	
	/* Use plugins */ 
	'jquery.cookie','jquery.lazyload', 'jquery.query','jquery.scrollTo','jquery.colorbox-min','jquery.a-tools.min',
	'jquery.superfish','jquery.swfupload','jquery.tablesorter','jquery.textarearesizer','jquery.jplayer.min',
	
	/* MUST BE LOAD LAST */
	'skin.original'
);

$debug = true;

// Output HTML DTD, <html>, and receive content-type
$meta_content_type = (isset($pkwk_dtd)) ? pkwk_output_dtd($pkwk_dtd) : pkwk_output_dtd();
?>
	<head profile="http://purl.org/net/ns/metaprof">
		<?php
echo $meta_content_type;
if (!empty($google_site_verification)) echo "\t\t".'<meta name="google-site-verification" content="'.$google_site_verification.'" />'."\n";
if (!empty($yahoo_site_explorer_id)) echo "\t\t".'<meta name="y_key" content="'.$yahoo_site_explorer_id.'" />'."\n";
if (!empty($bing_siteid)) echo "\t\t".'<meta name="msvalidate.01" content="'.$bing_siteid.'" />'."\n";
?>
<?php if ($modifier !== 'anonymous') { ?>
		<meta name="author" content="<?php echo $modifier ?>" />
		<link rel="author" href="<?php echo $modifierlink ?>" title="<?php echo $modifier ?>" />
<?php } ?>
<?php if ($notify_from !== 'from@example.com') { ?>
		<meta name="reply-to" content="mailto:<?php echo $notify_from ?>" />
		<link rev="made" href="mailto:<?php echo $notify_from ?>" title="Contact to <?php echo $modifier ?>" />
<?php } ?>
<?php
if ($nofollow || ! $is_read) echo ' <meta name="robots" content="NOINDEX,NOFOLLOW" />'."\n";
if ($title == $defaultpage) {
	echo '		<title>'.$page_title.'</title>'."\n";
} elseif ($newtitle != '' && $is_read) {
	echo '		<title>'.$newtitle.' - '.$page_title.'</title>'."\n";
} else {
	echo '		<title>'.$title.' - '.$page_title.'</title>'."\n";
}
?>
		<link rel="alternate" href="<?php echo $_LINK['mixirss'] ?>" type="application/rss+xml" title="RSS" />
		<link rel="canonical" href="<?php echo $_LINK['reload'] ?>" />
		<link rel="contents" href="<?php echo $_LINK['menu'] ?>" title="<?php echo $menubar; ?>" />
		<link rel="glossary" href="<?php echo $_LINK['glossary'] ?>" title="<?php echo $glossarypage; ?>" />
		<link rel="help" href="<?php echo $_LINK['help'] ?>" title="<?php echo $_LANG['skin']['help'] ?>" />
		<link rel="home" href="<?php echo $_LINK['top'] ?>" title="<?php echo $title ?>" />
		<link rel="index" href="<?php echo $_LINK['list']?>" title="<?php echo $_LANG['skin']['list'] ?>" />
		<link rel="search" href="<?php echo $_LINK['search'] ?>" title="<?php echo $_LANG['skin']['search'] ?>" />
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
		<link rel="stylesheet" href="<?php echo SKIN_URI ?>scripts.css.php" type="text/css" media="screen" charset="UTF-8" />
		<link rel="stylesheet" href="<?php echo SKIN_URI ?>iconset/default_iconset.css.php" type="text/css" media="screen" charset="UTF-8" />
		<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.4/themes/<?php echo $ui_theme; ?>/jquery-ui.css" type="text/css" />
		<link rel="stylesheet" href="<?php echo $theme_path; ?>default.css" type="text/css" media="screen" charset="<?php echo $css_charset ?>" />
		<link rel="stylesheet" href="<?php echo $theme_path; ?>blue.css" type="text/css" media="screen" charset="<?php echo $css_charset ?>" />
		<link rel="stylesheet" href="<?php echo $theme_path; ?>print.css" type="text/css" media="print" charset="<?php echo $css_charset ?>" />
		<script type="text/javascript" src="http://www.google.com/jsapi<?php if ($google_api_key) echo '?key='.$google_api_key; ?>"></script>
		<script type="text/javascript">
//<![CDATA[
google.load("jquery", "1.4.2");
google.load("jqueryui", "1.8.4");
google.load("swfobject", "2.2");
<?php
echo 'var MODIFIED = "'.get_filetime($r_page).'";'."\n";
if ($r_page) echo 'var PAGE = "'.$r_page.'";'."\n";
if (exist_plugin_convert('js_init')) echo do_plugin_convert('js_init');
?>
var SCRIPT = "<?php echo $script; ?>";
<?php if ($google_analytics) { ?>
// Google Analytics
var _gaq = _gaq || [];
_gaq.push(['_setAccount', '<?php echo $google_analytics; ?>']);
_gaq.push(['_trackPageview']);
<?php } ?>
//]]>
		</script>
		<script type="text/javascript" src="<?php echo SKIN_URI; ?>js/locale.js"></script>
<?php 
echo $head_tag;
if ($debug) {
	foreach($files as $script_file) echo ' <script type="text/javascript" src="'.SKIN_URI.'js/src/'.$script_file.'.js"></script>'."\n";
} else {
	echo ' <script type="text/javascript" src="'.SKIN_URI.'js/skin.js.php"></script>'."\n";
}
?>
<?php if ($js_block) echo ' <script type="text/javascript">'."\n".'//<![CDATA['."\n".$js_block.'//]]></script>'."\n"; ?>
	</head>
<?php flush(); ?>
	<body>
<?php if (exist_plugin_convert('headarea') && do_plugin_convert('headarea') != '') { ?>
		<div id="header">
			<h1 style="display:none;"><?php echo(($newtitle!='' && $is_read)?$newtitle:$page) ?></h1>
			<?php echo do_plugin_convert('headarea') ?>
		</div>
<?php } else { ?>
		<div id="header">
			<a href="<?php echo $modifierlink ?>"><img id="logo" src="<?php echo $site_titleimage ?>" width="<?php echo $titleimagesize_w ?>" height="<?php echo $titleimagesize_h ?>" alt="<?php echo $titleimage_alt ?>" title="<?php echo $titleimage_alt ?>" /></a>
			<h1 class="title"><?php echo(($newtitle!='' && $is_read)?$newtitle:$page) ?></h1>
		</div>
<?php if ($lastmodified) { ?>
		<div id="lastmodified">Last-modified: <?php echo $lastmodified ?></div>
<?php } else { ?>
		<span>&nbsp;</span>
<?php } ?>
<?php
if (exist_plugin('suckerfish')) {
	echo do_plugin_convert('suckerfish');
} else if (exist_plugin('navibar2')) {
	echo do_plugin_convert('navibar2');
} else if (exist_plugin('navibar')) {
	echo do_plugin_convert('navibar','top,|,edit,freeze,diff,backup,upload,reload,|,new,list,search,recent,help,|,trackback',TRUE);
	echo $hr;
}
?>
<?php } ?>
		<div class="main">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
<?php
global $body_menu,$body_side;
if (!empty($body_menu)) { ?>
					<td class="ltable" valign="top"><div id="menubar"><?php echo $body_menu; ?></div></td>
<?php }
?>
					<td class="ctable" valign="top">
<?php if ($is_page and exist_plugin_convert('topicpath')) { echo do_plugin_convert('topicpath'); } ?>
						<div id="body"><?php echo $body ?></div>
					</td>
<?php if (!empty($body_side)) { ?>
					<td class="rtable" valign="top"><div id="sidebar"><?php echo $body_side; ?></div></td>
<?php } ?>
				</tr>
			</table>
		</div>
<?php
if ($notes){ echo '<div id="note">'.$notes.'</div>'."\n".$hr;}	// note
if ($attaches){ echo '<div id="attach">'.$attaches.'</div>'."\n".'<hr style="clear:both;" />'."\n";}	// attach
if (!$notes && !$sttaches){ echo $hr; }
$footarea = (exist_plugin_convert('footarea')) ? do_plugin_convert('footarea') : '';
if (!empty($footarea)) {
	echo $footarea."\n";
        unset($footarea);
} else {
	if (exist_plugin('toolbar')) echo do_plugin_convert('toolbar','reload,|,new,newsub,edit,guiedit,freeze,diff,upload,copy,rename,|,top,list,search,recent,backup,referer,|,help,|,mixirss').'<br style="clear:both;" />';
	if ($related) echo '<div id="related"><span class="plugin_title">Link:</span>'.$related.'</div><br style="clear:both;" />'."\n";
?>
		<div id="footer">
			<div id="qr_code">
				<?php if (exist_plugin_inline('qrcode')) echo plugin_qrcode_inline(2,get_script_absuri().'?'.str_replace('%', '%25', $r_page)); ?>
			</div>
			<div id="sigunature">
				<address>Founded by <a href="<?php echo $modifierlink ?>"><?php echo $modifier ?></a></address><br />
				<?php echo S_COPYRIGHT ?><br />
				HTML convert time: <?php echo showtaketime() ?> sec.
			</div>
			<div id="banner_box">
				<a href="http://pukiplus.sf.net/"><img src="<?php echo IMAGE_URI; ?>pukiwiki_adv.banner.png" width="88" height="31" alt="PukiWiki Advance" title="PukiWiki Advance" /></a>
<?php	if (! isset($pkwk_dtd) || $pkwk_dtd == PKWK_DTD_XHTML_1_1) { ?>
				<a href="http://validator.w3.org/check/referer"><img src="http://www.w3.org/Icons/valid-xhtml11-blue" width="88" height="31" alt="Valid XHTML 1.1" title="Valid XHTML 1.1" /></a>
<?php	} else if ($pkwk_dtd >= PKWK_DTD_XHTML_1_0_FRAMESET) {  ?>
				<a href="http://validator.w3.org/check/referer"><img src="http://www.w3.org/Icons/valid-xhtml10-blue" width="88" height="31" alt="Valid XHTML 1.0" title="Valid XHTML 1.0" /></a>
<?php	} else if ($pkwk_dtd >= PKWK_DTD_HTML_4_01_FRAMESET) {  ?>
				<a href="http://validator.w3.org/check/referer"><img src="http://www.w3.org/Icons/valid-html40-blue" width="88" height="31" alt="Valid HTML 4.0" title="Valid HTML 4.0" /></a>
<?php	} ?>
			</div>
<?php } ?>
		</div>
<?php if ($use_local_time && exist_plugin_convert('tz')) echo do_plugin_convert('tz'); ?>
<?php echo $foot_tag ?>
<?php if (exist_plugin('google_analytics')) echo google_analytics_put_code(); ?>
	</body>
</html>
