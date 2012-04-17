<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// PukiWiki Plus! skin for PukiWiki Advance.
// Original version by miko and upk.
// Modified by Logue
//
// $Id: default.skin.php,v 1.4.17 2012/01/09 14:59:00 Logue Exp $
//
global $pkwk_dtd, $_SKIN, $is_page, $defaultpage, $sidebar, $headarea, $footarea;

// Initialize
if (!defined('DATA_DIR')) { exit; }

if ($title != $defaultpage) {
	$page_title = $title.' - '.$page_title;
} elseif ($newtitle != '' && $is_read) {
	$page_title = $newtitle.' - '.$page_title;
}

// モードによって、3カラム、2カラムを切り替える。
if (arg_check('read') && exist_plugin_convert('menu')) {
	$layout_class = (arg_check('read') && exist_plugin_convert('side') && is_page($sidebar) ? 'three-colums' : 'two-colums');
}else{
	$layout_class = '';
}

// Header and Footer
$title_style = '';
$header = '';
if (is_page($headarea) && exist_plugin_convert('headarea')){
	$header = do_plugin_convert('headarea');
	$title_style = "display:none;";
}
$footer = (is_page($footarea) && exist_plugin_convert('footarea')) ? do_plugin_convert('footarea') : '';

// navibar
$navibar = exist_plugin('suckerfish') ? do_plugin_convert('suckerfish') : null;

// start

// Output HTML DTD, <html>, and receive content-type
$meta_content_type = (isset($pkwk_dtd)) ? pkwk_output_dtd($pkwk_dtd) : pkwk_output_dtd();
?>
	<head>
		<?php echo $meta_content_type; ?>
		<?php echo $pkwk_head; ?>
		<title><?php echo $page_title; ?></title>
	</head>
	<body>
		<div id="container" class="<?php echo $layout_class ?>" role="document">
<!-- *** Header *** -->
			<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '<header id="header" role="banner">'."\n" : '<div id="header">')."\n"; ?>
				<a href="<?php echo $_LINK['top'] ?>"  style="<?php echo $title_style; ?>'"><img id="logo" src="<?php echo $_SKIN['logo']['src'] ?>" width="<?php echo $_SKIN['logo']['width'] ?>" height="<?php echo $_SKIN['logo']['height'] ?>" alt="<?php echo $_SKIN['logo']['alt'] ?>" /></a>
				<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '<hgroup id="hgroup" style="'.$title_style.'">'."\n" : '<div id="hgroup" style="'.$title_style.'">')."\n"; ?>
					<h1 id="title"><?php echo(($newtitle!='' && $is_read) ? $newtitle: $page) ?></h1>
					<h2><a href="<?php echo $_LINK['reload'] ?>" id="parmalink"><?php echo $_LINK['reload'] ?></a></h2>
				<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '</hgroup>'."\n" : '</div>')."\n"; ?>
				<?php if ($_SKIN['adarea']['header'] && !isset($header)) echo '<div id="ad" class="noprint">' . $_SKIN['adarea']['header'] . '</div>'; ?>
				<?php if ($header) echo $header; ?>
				<?php echo (!empty($lastmodified)) ? '<div id="lastmodified">Last-modified: '.$lastmodified.'</div>'."\n" : '' ?>
			<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '</header>' : '</div>')."\n"; ?>
<!-- *** End Header *** -->
			<?php echo ($navibar === null) ? (exist_plugin('navibar') ? do_plugin_convert('navibar','top,|,edit,freeze,diff,backup,upload,reload,|,new,list,search,recent,help,|,login').'<hr />' :'') : $navibar; ?>
			<div id="wrapper" class="clearfix">
<!-- Center -->
				<div id="main_wrapper">
					<div id="main" role="main">
						<?php echo ($is_page && exist_plugin_convert('topicpath')) ? do_plugin_convert('topicpath') : ''; ?>
						<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<section id="body">'."\n" : '<div id="body">'."\n"; ?>
							<?php echo $body."\n" ?>
						<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</section>'."\n" : '</div>'."\n"; ?>
<?php if (!empty($notes)) { ?>
						<hr />
						<!-- * Note * -->
						<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<aside id="note" role="note">'."\n" : '<div id="note" role="note">'."\n"; ?>
							<?php echo $notes."\n" ?>
						<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</aside>'."\n" : '</div>'."\n"; ?>
						<!--  End Note -->
<?php } ?>
						<?php if (!empty($_SKIN['adarea']['footer'])) echo '<div id="footer_adspace" class="noprint" style="text-align:center;">' . $_SKIN['adarea']['footer'] . '</div>'; ?>
					</div>
				</div>

<?php if ($layout_class == 'three-colums' || $layout_class == 'two-colums')  { ?>
<!-- Left -->
				<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<aside id="menubar" role="navigation">'."\n" : '<div id="menubar" role="navigation">'."\n"; ?>
					<?php echo do_plugin_convert('menu')."\n" ?>
				<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</aside>'."\n" : '</div>'."\n"; ?>
<?php } ?>

<?php if ($layout_class == 'three-colums')  { ?>
<!-- Right -->
				<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '<aside id="sidebar" role="navigation">' : '<div id="sidebar" role="navigation">')."\n"; ?>
					<?php echo do_plugin_convert('side')."\n" ?>
				<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '</aside>'."\n" : '</div>')."\n"; ?>
<?php } ?>
			</div>
			<div id="misc">
<?php if (!empty($attaches)) { ?>
				<hr />
				<!-- * Attach * -->
				<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<aside id="attach">'."\n" : '<div id="attach">'."\n"; ?>
					<?php echo $attaches ?>
				<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</aside>'."\n" : '</div>'."\n"; ?>
				<!--  End Attach -->
<?php } ?>
<?php if (!empty($related)) { ?>
				<!-- * Related * -->
				<hr />
				<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<aside id="related" role="navigation">'."\n" : '<div id="related" role="navigation">'."\n"; ?>
					<?php echo $related ?>
				<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</aside>'."\n" : '</div>'."\n"; ?>
				<!--  End Related -->
<?php } ?>
				<hr />
				<?php echo exist_plugin('toolbar') ? do_plugin_convert('toolbar','reload,|,new,newsub,edit,freeze,source,diff,upload,copy,rename,|,top,list,search,recent,backup,referer,log,|,help,|,rss') : '';?>
			</div>

			<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<footer id="footer" class="clearfix" role="contentinfo">'."\n" : '<div id="footer" class="clearfix" role="contentinfo">'."\n"; ?>
<?php if ($footer) {
echo $footer;
}else{ ?>
				<div id="qr_code">
					<?php echo exist_plugin_inline('qrcode') ? plugin_qrcode_inline(1,$_LINK['reload']) : ''; ?>
				</div>
				<address>Founded by <a href="<?php echo $modifierlink ?>"><?php echo $modifier ?></a></address>
				<div id="sigunature">
					Powered by <a href="http://pukiwiki.logue.be/" rel="product"><?php echo GENERATOR ?></a>. HTML convert time: <?php echo showtaketime() ?> sec. <br />
					Original Theme Design by <a href="http://pukiwiki.cafelounge.net/plus/">PukiWiki Plus!</a> Team.
				</div>
				<div id="banner_box">
					<a href="http://pukiwiki.logue.be/"><img src="<?php echo IMAGE_URI; ?>pukiwiki_adv.banner.png" width="88" height="31" alt="PukiWiki Advance" title="PukiWiki Advance" /></a>
					<a href="http://validator.w3.org/check/referer"><img src="<?php echo IMAGE_URI ?>html5.png" width="88" height="31" alt="HTML 5" title="HTML5" /></a>
				</div>
<?php } ?>
			<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</footer>'."\n" : '</div>'."\n"; ?>
		</div>
		<?php echo $pkwk_tags; ?>
	</body>
</html>
