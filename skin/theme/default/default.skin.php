<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// PukiWiki Plus! skin for PukiWiki Advance.
// Original version by miko and upk.
// Modified by Logue
//
// $Id: default.skin.php,v 1.4.15 2011/04/09 17:52:00 Logue Exp $
//
global $pkwk_dtd, $_SKIN, $is_page, $defaultpage, $sidebar, $headarea, $footarea;

if (!defined('DATA_DIR')) { exit; }

if ($title != $defaultpage) {
	$page_title = $title.' - '.$page_title;
} elseif ($newtitle != '' && $is_read) {
	$page_title = $newtitle.' - '.$page_title;
}

$title_style = '';

if (is_page($headarea) && exist_plugin_convert('headarea')){
	$header = do_plugin_convert('headarea');
	$title_style = "display:none;";
}

if (is_page($footarea) && exist_plugin_convert('footarea')){
	$footer = do_plugin_convert('footarea');
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
	<!-- *** Header *** -->
		<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '<header id="header" class="clearfix">'."\n" : '<div id="header" class="clearfix">')."\n"; ?>
			<a href="<?php echo $modifierlink ?>"  style="<?php echo $title_style; ?>'"><img id="logo" src="<?php echo $_SKIN['logo']['src'] ?>" width="<?php echo $_SKIN['logo']['width'] ?>" height="<?php echo $_SKIN['logo']['height'] ?>" alt="<?php echo $_SKIN['logo']['alt'] ?>" /></a>
			<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '<hgroup id="title" style="'.$title_style.'">'."\n" : '<div id="title" style="'.$title_style.'">')."\n"; ?>
				<h1><?php echo(($newtitle!='' && $is_read) ? $newtitle: $page) ?></h1>
				<h2><a href="<?php echo $_LINK['reload'] ?>" id="parmalink"><?php echo $_LINK['reload'] ?></a></h2>
			<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '</hgroup>'."\n" : '</div>')."\n"; ?>
<!-- * Ad space *-->
			<?php if ($_SKIN['adarea']['header'] && !isset($header)) echo '<div id="ad" class="noprint">' . $_SKIN['adarea']['header'] . '</div>'; ?>
<!-- * End Ad space * -->
			<?php if (isset($header)) echo $header; ?>
			<?php echo (!empty($lastmodified)) ? '<div id="lastmodified">Last-modified: '.$lastmodified.'</div>'."\n" : '' ?>
		<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '</header>' : '</div>')."\n"; ?>
<!-- *** End Header *** -->

		<?php if (exist_plugin('suckerfish')) echo do_plugin_convert('suckerfish'); ?>

		<table class="main">
			<tr>
<?php if (arg_check('read') && exist_plugin_convert('menu')) { ?>
<!-- ** MenuBar ** -->
				<td class="ltable">
					<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<aside id="menubar">'."\n" : '<div id="menubar">'."\n"; ?>
						<?php echo do_plugin_convert('menu') ?>
					<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</aside>'."\n" : '</div>'."\n"; ?>
				</td>
<!-- ** End MenuBar ** -->
<?php } ?>
				<td class="ctable">
					<?php echo ($is_page && exist_plugin_convert('topicpath')) ? do_plugin_convert('topicpath') : ''; ?>
					<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<section id="body">'."\n" : '<div id="body">'."\n"; ?>
						<?php echo $body ?>
					<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</section>'."\n" : '</div>'."\n"; ?>
<?php if (!empty($notes)) { ?>
						<?php echo $hr ?>
<!-- * Note * -->
						<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<aside id="note">'."\n" : '<div id="note">'."\n"; ?>
							<?php echo $notes ?>
						<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</aside>'."\n" : '</div>'."\n"; ?>
<!--  End Note -->
<?php } ?>
				</td>
<?php if (arg_check('read') && exist_plugin_convert('side') && is_page($sidebar))  { ?>
				<td class="rtable">
					<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '<aside id="sidebar">' : '<div id="sidebar">')."\n"; ?>
						<?php echo do_plugin_convert('side') ?>
					<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '</aside>'."\n" : '</div>')."\n"; ?>
				</td>
<?php } ?>
			</tr>
		</table>
		
<?php if (!empty($attaches)) { ?>
		<hr />
<!-- * Attach * -->
		<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<aside id="attach" class="clearfix">'."\n" : '<div id="attach">'."\n"; ?>
			<?php echo $attaches ?>
		<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</aside>'."\n" : '</div>'."\n"; ?>
<!--  End Attach -->
<?php } ?>

<?php if (!empty($related)) { ?>
		<hr />
<!-- * Related * -->
		<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<aside id="related" class="clearfix">'."\n" : '<div id="related">'."\n"; ?>
			<?php echo $related ?>
		<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</aside>'."\n" : '</div>'."\n"; ?>
<!--  End Related -->
<?php } ?>
		<hr />
		<?php echo exist_plugin('toolbar') ? do_plugin_convert('toolbar','reload,|,new,newsub,edit,guiedit,freeze,diff,upload,copy,rename,|,top,list,search,recent,backup,referer,|,help,|,mixirss').'<div class="pkwk-clear"></div>' : '';?>
		<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<footer id="footer">'."\n" : '<div id="footer">'."\n"; ?>
<?php if (isset($footer)) {
	echo $footer;
}else{ ?>
			<div id="qr_code">
				<?php echo exist_plugin_inline('qrcode') ? plugin_qrcode_inline(1,$_LINK['reload']) : ''; ?>
			</div>
			<address>Founded by <a href="<?php echo $modifierlink ?>"><?php echo $modifier ?></a></address>
			<div id="sigunature">
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
