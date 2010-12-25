<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// PukiWiki Plus! skin for PukiWiki Advance.
// Original version by miko and upk.
// Modified by Logue
//
// $Id: default.skin.php,v 1.4.14 2010/08/16 20:44:00 Logue Exp $
//
global $pkwk_dtd, $_SKIN;

if (!defined('DATA_DIR')) { exit; }

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
		<?php echo $pkwk_head; ?>
		<title><?php echo $page_title; ?></title>
	</head>
<?php flush(); ?>
	<body>
		<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '<header id="header">'."\n" : '<div id="header">')."\n"; ?>
<?php if (exist_plugin_convert('headarea') && do_plugin_convert('headarea') != '') { ?>
			<h1 style="display:none;"><?php echo(($newtitle!='' && $is_read) ? $newtitle: $page) ?></h1>
			<div style="display:none;"><a href="<?php echo $_LINK['reload'] ?>" id="parmalink"><?php echo $_LINK['reload'] ?></a></div>
			<?php echo do_plugin_convert('headarea') ?>
<?php } else { ?>
			<a href="<?php echo $modifierlink ?>"><img id="logo" src="<?php echo $_SKIN['logo'] ?>" width="<?php echo $_SKIN['logo_w'] ?>" height="<?php echo $_SKIN['logo_h'] ?>" alt="<?php echo $_SKIN['logo_alt'] ?>" title="<?php echo $_SKIN['logo_alt'] ?>" /></a>
			<h1 class="title"><?php echo(($newtitle!='' && $is_read)?$newtitle:$page) ?></h1>
			<div class="small"><a href="<?php echo $_LINK['reload'] ?>" id="parmalink"><?php echo $_LINK['reload'] ?></a></div>
<?php } ?>
		<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '</header>' : '</div>')."\n"; ?>
		<?php echo (!empty($lastmodified)) ? '<div id="lastmodified">Last-modified: '.$lastmodified.'</div>'."\n" : '' ?>
		<?php if (exist_plugin('suckerfish')) echo do_plugin_convert('suckerfish'); ?>
		<table class="main">
			<tr>
<?php if (!empty($body_menu)) { ?>
				<td class="ltable">
					<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '<section id="menubar">'."\n" : '<div id="menubar">')."\n"; ?>
					<?php echo $body_menu; ?>
					<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '</section>'."\n" : '</div>')."\n"; ?>
				</td>
<?php } ?>
				<td class="ctable">
					<?php echo ($is_page and exist_plugin_convert('topicpath')) ? do_plugin_convert('topicpath') : ''; ?>
					<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<section id="body">'."\n" : '<div id="body">'."\n"; ?>
						<?php echo $body ?>
					<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</section>'."\n" : '</div>'."\n"; ?>
				</td>
<?php if (!empty($body_side)) { ?>
				<td class="rtable">
					<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '<section id="sidebar">' : '<div id="sidebar">')."\n"; ?>
						<?php echo $body_side; ?>
					<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '</section>'."\n" : '</div>')."\n"; ?>
				</td>
<?php } ?>
			</tr>
		</table>
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
				<?php echo exist_plugin_inline('qrcode') ? plugin_qrcode_inline(1,get_script_absuri().'?'.str_replace('%', '%25', $r_page)) : ''; ?>
			</div>
			<div id="sigunature">
				<address>Founded by <a href="<?php echo $modifierlink ?>"><?php echo $modifier ?></a></address><br />
				Powered by <?php echo GENERATOR ?>. HTML convert time: <?php echo showtaketime() ?> sec. <br />
				Theme Design by <a href="http://pukiwiki.cafelounge.net/plus/">PukiWiki Plus!</a> Team.
			</div>
			<div id="banner_box">
				<a href="http://pukiwiki.logue.be/"><img src="<?php echo IMAGE_URI; ?>pukiwiki_adv.banner.png" width="88" height="31" alt="PukiWiki Advance" title="PukiWiki Advance" /></a>
			</div>
<?php } ?>
		<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</footer>'."\n" : '</div>'."\n"; ?>
		<?php echo $pkwk_tags; ?>
	</body>
</html>
