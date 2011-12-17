<?php
// PukiWiki - Yet another WikiWikiWeb clone.
//
// PukiWiki original skin "GS2" 1.5.3
//     by yiza < http://www.yiza.net/ >
global $pkwk_dtd, $_SKIN, $is_page, $defaultpage, $sidebar, $headarea, $footarea;

// ------------------------------------------------------------
// Code start

// Prohibit direct access
if (! defined('UI_LANG')) die('UI_LANG is not set');
if (! isset($_LANG)) die('$_LANG is not set');
if (! defined('PKWK_READONLY')) die('PKWK_READONLY is not set');

$lang  = & $_LANG['skin'];
$link  = & $_LINK;
$image = & $_IMAGE['skin'];
$rw    = ! PKWK_READONLY;

// ------------------------------------------------------------
// Output

if (!defined('DATA_DIR')) { exit; }

if ($title != $defaultpage) {
	$page_title = $title.' - '.$page_title;
} elseif ($newtitle != '' && $is_read) {
	$page_title = $newtitle.' - '.$page_title;
}
if (arg_check('read') && exist_plugin_convert('menu')) {
	$layout_class = (arg_check('read') && exist_plugin_convert('side') && is_page($sidebar) ? 'three-colums' : 'two-colums');
}else{
	$layout_class = '';
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
		<div id="container" class="<?php echo $layout_class ?>" role="document">
<!--Header-->
		<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '<header id="header" class="clearfix">'."\n" : '<div id="header" class="clearfix">')."\n"; ?>
<!-- Header/Search -->
			<?php if ($_SKIN['search_form'] && exist_plugin('search')) echo do_plugin_convert('search'); ?>
			<?php echo (exist_plugin('navibar') ? do_plugin_convert('navibar','top,reload,new,list,search,recent,help') :'') ?>
			<a href="<?php echo $modifierlink ?>"><img id="logo" src="<?php echo $_SKIN['logo']['src'] ?>" width="<?php echo $_SKIN['logo']['width'] ?>" height="<?php echo $_SKIN['logo']['height'] ?>" alt="<?php echo $_SKIN['logo']['alt'] ?>" /></a>
			<div id="hgroup">
				<?php echo ($is_page && exist_plugin_convert('topicpath')) ? do_plugin_convert('topicpath') : ''; ?>
				<h1><?php echo (($newtitle!='' && $is_read) ? $newtitle : $page) ?></h1>
			</div>
<?php if( $_SKIN['show_navibar'] && $is_page) { ?>
			<?php echo (exist_plugin('navibar') ? do_plugin_convert('navibar','edit,freeze,copy,diff,backup,attach,trackback,referer') :'') ?>
			<div id="pageinfo">Last update on <?php echo $lastmodified ?></div>
<?php } // $_SKIN['show_navibar'] ?>

		<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '</header>' : '</div>')."\n"; ?>

		<div id="wrapper" class="clearfix">
			<div id="main" role="main">
				<div id="contents">
					<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<section>'."\n" : '<div>'."\n"; ?>
						<?php echo $body."\n" ?>
					<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</section>'."\n" : '</div>'."\n"; ?>

<?php if (!empty($notes)) { ?>
<!-- * Note * -->
					<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<aside id="note" class="footbox">'."\n" : '<div id="note" class="footbox">'."\n"; ?>
						<?php echo $notes."\n" ?>
					<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</aside>'."\n" : '</div>'."\n"; ?>
<!--  End Note -->
<?php } ?>
<?php if (!empty($attaches)) { ?>
<!-- * Attach * -->
					<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<aside id="attach" class="footbox">'."\n" : '<div id="attach" class="footbox">'."\n"; ?>
						<?php echo $attaches ?>
					<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</aside>'."\n" : '</div>'."\n"; ?>
<!--  End Attach -->
<?php } ?>
<?php if (!empty($related)) { ?>
<!-- * related * -->
					<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<aside id="related" class="footbox">'."\n" : '<div id="related" class="footbox">'."\n"; ?>
						<?php echo $related ?>
					<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</aside>'."\n" : '</div>'."\n"; ?>
<!--  End related -->
<?php } ?>
				</div>
				<?php if (!empty($_SKIN['adarea']['footer'])) echo '<div id="footer_adspace" class="noprint" style="text-align:center;">' . $_SKIN['adarea']['footer'] . '</div>'; ?>
			</div>

<?php if ($layout_class == 'three-colums' || $layout_class == 'two-colums')  { ?>
<!-- Left -->
			<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<aside id="menubar" class="sideboxr">'."\n" : '<div id="menubar" class="sideboxr">'."\n"; ?>
				<?php echo do_plugin_convert('menu')."\n" ?>
			<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</aside>'."\n" : '</div>'."\n"; ?>
<?php } ?>

<?php if ($layout_class == 'three-colums')  { ?>
<!-- Right -->
			<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '<aside id="sidebar" class="sideboxr">' : '<div id="sidebar" class="sideboxr">')."\n"; ?>
				<?php echo do_plugin_convert('side')."\n" ?>
			<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '</aside>'."\n" : '</div>')."\n"; ?>
<?php } ?>
		</div>

		<?php if ($_SKIN['show_toolbar'] && exist_plugin('toolbar')) echo do_plugin_convert('toolbar','top,edit,freeze,diff,backup,upload,copy,rename,reload,|,new,list,search,recent,|,help,|,mixirss'); ?>

		<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<footer id="footer">'."\n" : '<div id="footer">'."\n"; ?>
<?php if ($_SKIN['qrcode']) { ?>
			<div id="qrcode">
				<?php echo exist_plugin_inline('qrcode') ? plugin_qrcode_inline(1,$_LINK['reload']) : ''; ?>
			</div>
<?php } ?>
			<div id="signature">
				<?php echo S_COPYRIGHT ?>.<br />
				<strong>GS2 Skin</strong> designed by <a href="http://www.yiza.net/" rel="external">yiza</a> / Adv. version by <a href="http://logue.be/" rel="external">Logue</a>.<br />
				HTML convert time: <?php echo showtaketime() ?> sec.
			</div>
<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</footer>'."\n" : '</div>'."\n"; ?>

		<?php echo $pkwk_tags; ?>
	</body>
</html>
