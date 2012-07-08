<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// WIKIWIKI Adv. Theme.
// by Logue
// Inspired from wikiwiki.jp default skin.
//
// $Id: wikiwikiadv.skin.php,v 1.0.1 2012/07/09 07:55:00 Logue Exp $
//
global $pkwk_dtd, $_SKIN, $is_page, $defaultpage, $sidebar, $headarea, $footarea;

// Initialize
if (!defined('DATA_DIR')) { exit; }

$site_name = $page_title;
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
			<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '<header id="header" role="banner">'."\n" : '<div id="header" role="banner">')."\n"; ?>
				<a href="<?php echo $_LINK['top'] ?>"  style="<?php echo $title_style; ?>'"><img id="logo" src="<?php echo $_SKIN['logo']['src'] ?>" width="<?php echo $_SKIN['logo']['width'] ?>" height="<?php echo $_SKIN['logo']['height'] ?>" alt="<?php echo $_SKIN['logo']['alt'] ?>" /></a>
				<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '<hgroup id="hgroup" style="'.$title_style.'">'."\n" : '<div id="hgroup" style="'.$title_style.'">')."\n"; ?>
				<h1><?php echo $site_name; ?></h1>
				<h2 id="description">PukiWiki - Yet another WikiWikiWeb clone.</h2>
				<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '</hgroup>'."\n" : '</div>')."\n"; ?>
				<?php if ($_SKIN['adarea']['header'] && !isset($header)) echo '<div id="ad" class="noprint">' . $_SKIN['adarea']['header'] . '</div>'; ?>
				<?php if ($header) echo $header; ?>
				
			<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '</header>' : '</div>')."\n"; ?>
<!-- *** End Header *** -->
			<div id="naviframe" class="clearfix">
				<?php echo exist_plugin('navibar') ? do_plugin_convert('navibar','top,new,edit,upload,login') : ''; ?>
				<?php echo exist_plugin('toolbar') ? do_plugin_convert('toolbar','list,recent,diff,source,backup,freeze,rename,help') : '';?>
				<?php if (exist_plugin('search')) echo do_plugin_convert('search'); ?>
			</div>
			<?php echo ($is_page && exist_plugin_convert('topicpath')) ? do_plugin_convert('topicpath') : ''; ?>
			<hr />
			<div id="wrapper" class="clearfix">
<!-- Center -->
				<div id="main_wrapper">
					<div id="main" role="main">
						<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '<hgroup id="title" style="'.$title_style.'">'."\n" : '<div id="title" style="'.$title_style.'">')."\n"; ?>
							<h1><?php echo (($newtitle!='' && $is_read) ? $newtitle: $title) ?></h1>
							<?php echo (!empty($lastmodified)) ? '<h2 id="lastmodified">Last-modified: '.$lastmodified.'</h2>'."\n" : '' ?>
						<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '</hgroup>'."\n" : '</div>')."\n"; ?>
						
						<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<section id="body" role="main">'."\n" : '<div id="body" role="main">'."\n"; ?>
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
				<?php if ($footer) echo '<hr />'."\n".$footer; ?>
				<hr />
				<?php echo exist_plugin('toolbar') ? do_plugin_convert('toolbar','new,edit,upload,list,search,recent,diff,backup,freeze,copy,rename,|,log,referer,trackback') : '';?>
			</div>

			<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<footer id="footer" class="clearfix" role="contentinfo">'."\n" : '<div id="footer" class="clearfix">'."\n"; ?>
				<ul>
					<li><address>Founded by <a href="<?php echo $modifierlink ?>"><?php echo $modifier ?></a></address></li>
					<li>Powered by <a href="http://pukiwiki.logue.be/" rel="product"><?php echo GENERATOR ?></a>.</li>
					<li>HTML convert time: <?php echo showtaketime() ?> sec. </li>
					<li class="f_right"><a href="<?php echo $_LINK['rss'] ?>"><span class="pkwk-icon icon-rss"><?php echo $_LANG['skin']['rss'] ?></span></a></li>
				<ul>
			<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</footer>'."\n" : '</div>'."\n"; ?>
		</div>
		<?php echo $pkwk_tags; ?>
	</body>
</html>
