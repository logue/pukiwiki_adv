<?php
/////////////////////////////////////////////////
// PukiPlus - Yet another WikiWikiWeb clone.
//
// PukiWiki Plus! skin
// Original version by miko and upk.
//
// $Id: default.skin.php,v 1.4.14 2010/08/16 20:44:00 Logue Exp $
//
global $pkwk_dtd;

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

if ($title != $defaultpage) {
	$page_title = $title.' - '.$page_title;
} elseif ($newtitle != '' && $is_read) {
	$page_title = $newtitle.' - '.$page_title;
}

// Output HTML DTD, <html>, and receive content-type
$meta_content_type = (isset($pkwk_dtd)) ? pkwk_output_dtd($pkwk_dtd) : pkwk_output_dtd();
?>
	<head>
		<?php echo $meta_content_type; ?>
		<?php echo $pkwk_meta; ?>
		<link rel="shortcut icon" href="<?php echo ROOT_URI ?>favicon.ico" type="image/x-icon" />
		<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.4/themes/<?php echo $ui_theme; ?>/jquery-ui.css" type="text/css" id="ui-theme" />
		<link rel="stylesheet" href="<?php echo SKIN_URI ?>scripts.css.php" type="text/css" media="screen" />
		<link rel="stylesheet" href="<?php echo SKIN_URI ?>iconset/default_iconset.css.php" type="text/css" media="screen" id="iconset" />
		<link rel="stylesheet" href="<?php echo $theme_path; ?>default.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="<?php echo $theme_path; ?>blue.css" type="text/css" media="screen" id="coloring" />
		<link rel="stylesheet" href="<?php echo $theme_path; ?>print.css" type="text/css" media="print" />
		<title><?php echo $page_title; ?></title>
		<?php echo $pkwk_head; ?>
		<script type="text/javascript" src="<?php echo $theme_path; ?>default.js"></script>
	</head>
<?php flush(); ?>
	<body>
		<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<header id="header">'."\n" : '<div id="header">'."\n"; ?>
<?php if (exist_plugin_convert('headarea') && do_plugin_convert('headarea') != '') { ?>
			<h1 style="display:none;"><?php echo(($newtitle!='' && $is_read) ? $newtitle: $page) ?></h1>
			<?php echo do_plugin_convert('headarea') ?>
<?php } else { ?>
			<a href="<?php echo $modifierlink ?>"><img id="logo" src="<?php echo $site_titleimage ?>" width="<?php echo $titleimagesize_w ?>" height="<?php echo $titleimagesize_h ?>" alt="<?php echo $titleimage_alt ?>" title="<?php echo $titleimage_alt ?>" /></a>
			<h1 class="title"><?php echo(($newtitle!='' && $is_read)?$newtitle:$page) ?></h1>
<?php } ?>
		<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</header>'."\n" : '</div>'."\n"; ?>
		<?php echo (!empty($lastmodified)) ? '<div id="lastmodified">Last-modified: '.$lastmodified.'</div>'."\n" : '' ?>
		<?php if (exist_plugin('suckerfish')) echo do_plugin_convert('suckerfish'); ?>
		<div>
			<table class="main">
				<tr>
<?php if (!empty($body_menu)) { ?>
					<td class="ltable">
						<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<section id="menubar">'."\n" : '<div id="menubar">'; ?>
						<?php echo $body_menu; ?>
						<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</section>'."\n" : '</div>'."\n"; ?>
					</td>
<?php } ?>
					<td class="ctable">
						<?php echo ($is_page and exist_plugin_convert('topicpath')) ?do_plugin_convert('topicpath') : ''; ?>
						<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<section id="body">'."\n" : '<div id="body">'."\n"; ?>
							<?php echo $body ?>
						<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</section>'."\n" : '</div>'."\n"; ?>
					</td>
<?php if (!empty($body_side)) { ?>
					<td class="rtable">
						<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<section id="sidebar">' : '<div id="sidebar">'; ?>
							<?php echo $body_side; ?>
						<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</section>'."\n" : '</div>'."\n"; ?>
					</td>
<?php } ?>
				</tr>
			</table>
		</div>
<?php
echo ($notes) ? '<div id="note">'.$notes.'</div>'."\n".$hr : '';	// note
echo ($attaches) ? '<div id="attach">'.$attaches.'</div>'."\n".'<hr style="clear:both;" />'."\n" : '';	// attach
echo (!$notes && !$attaches) ? $hr : '';
$footarea = (exist_plugin_convert('footarea')) ? do_plugin_convert('footarea') : '';
if (!empty($footarea)) {
	echo $footarea."\n";
	unset($footarea);
} else {
	echo exist_plugin('toolbar') ? do_plugin_convert('toolbar','reload,|,new,newsub,edit,guiedit,freeze,diff,upload,copy,rename,|,top,list,search,recent,backup,referer,|,help,|,mixirss').'<div class="pkwk-clear"></div>' : '';
	echo ($related) ?'<div id="related">'.$related.'</div>'."\n" : '';
?>
		<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<footer id="footer">'."\n" : '<div id="footer">'."\n"; ?>
			<div id="qr_code">
				<?php echo exist_plugin_inline('qrcode') ? plugin_qrcode_inline(2,get_script_absuri().'?'.str_replace('%', '%25', $r_page)) : ''; ?>
			</div>
			<div id="sigunature">
				<address>Founded by <a href="<?php echo $modifierlink ?>"><?php echo $modifier ?></a></address><br />
				<?php echo S_COPYRIGHT ?><br />
<?php if (DEBUG) {
	echo 'Powered by PHP '.PHP_VERSION.' ';
	echo (substr(php_sapi_name(), 0, 3) == 'cgi') ? 'CGI' : 'MODULE';
	echo ' mode.';
	echo ini_get('safe_mode') ? ' (SAFE)' : '';
} ?>
				HTML convert time: <?php echo showtaketime() ?> sec. 
			</div>
			<div id="banner_box">
				<a href="http://pukiplus.logue.be/"><img src="<?php echo IMAGE_URI; ?>pukiwiki_adv.banner.png" width="88" height="31" alt="PukiWiki Advance" title="PukiWiki Advance" /></a>
<?php	if (! isset($pkwk_dtd) || $pkwk_dtd == PKWK_DTD_XHTML_1_1) { ?>
				<a href="http://validator.w3.org/check/referer"><img src="http://www.w3.org/Icons/valid-xhtml11-blue" width="88" height="31" alt="Valid XHTML 1.1" title="Valid XHTML 1.1" /></a>
<?php	} else if ($pkwk_dtd >= PKWK_DTD_XHTML_1_0_FRAMESET) {  ?>
				<a href="http://validator.w3.org/check/referer"><img src="http://www.w3.org/Icons/valid-xhtml10-blue" width="88" height="31" alt="Valid XHTML 1.0" title="Valid XHTML 1.0" /></a>
<?php	} else if ($pkwk_dtd >= PKWK_DTD_HTML_4_01_FRAMESET) {  ?>
				<a href="http://validator.w3.org/check/referer"><img src="http://www.w3.org/Icons/valid-html40-blue" width="88" height="31" alt="Valid HTML 4.0" title="Valid HTML 4.0" /></a>
<?php	} ?>
			</div>
<?php } ?>
		<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</footer>'."\n" : '</div>'."\n"; ?>
<?php echo $foot_tag ?>
	</body>
</html>
