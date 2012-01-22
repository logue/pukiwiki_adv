<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: xxxlogue.skin.php,v 2.4.1 2011/09/11 22:59:00 Logue Exp $
// Copyright (C) 2010-2011 PukiWiki Advance Developers Team
//               2007-2010 Logue
//
// PukiWiki Advance xxxLogue skin
//
// Based on
//   Xu Yiyang's (http://xuyiyang.com/) Unnamed (http://xuyiyang.com/wordpress-themes/unnamed/)'
//
// License: GPL v3 or (at your option) any later version
// http://www.opensource.org/licenses/gpl-3.0.html

global $pkwk_dtd, $_SKIN, $is_page, $defaultpage;

if (!defined('DATA_DIR')) { exit; }

if ($title !== $defaultpage) {
	$page_title = $title.' - '.$page_title;
} elseif ($newtitle != '' && $is_read) {
	$page_title = $newtitle.' - '.$page_title;
}

// navibar
$navibar = exist_plugin('suckerfish') ? do_plugin_convert('suckerfish') : null;

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
		<div id="container" role="document">

<!-- ** Navigator ** -->
			<?php echo ($navibar === null) ? (exist_plugin('navibar') ? do_plugin_convert('navibar','top,|,edit,freeze,diff,backup,upload,reload,|,new,list,search,recent,help,|,trackback').'<hr />' :'') : $navibar; ?>
<!--  End Navigator -->

<!-- Header -->
			<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '<header id="header" class="clearfix" role="banner">'."\n" : '<div id="header" class="clearfix" role="banner">')."\n"; ?>
<!-- * Title * -->
				<div id="hgroup">
					<h1 id="title"><?php echo(($newtitle!='' && $is_read)?$newtitle:$page) ?></h1>
					<?php echo ($is_page && exist_plugin_convert('topicpath')) ? do_plugin_convert('topicpath') : ''; ?>
				</div>
<!-- * End Title * -->

<!-- * Ad space *-->
				<?php echo ($_SKIN['adarea']['header']) ? '<div id="header_ad" class="noprint">' . $_SKIN['adarea']['header'] . '</div>' : ''; ?>
<!-- * End Ad space * -->
				<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '</header>' : '</div>')."\n"; ?>
<!-- End Header -->

<?php if (arg_check('read')){ ?><!-- * Shelf * -->
				<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<aside id="shelf">'."\n" : '<div id="shelf">'."\n"; ?>
					<div id="toggle">
						<div id="inner_toggle">
<?php if ($is_page) { ?>
							<div id="shelf_form">
								<p><a href="<?php echo $_LINK['reload'] ?>" id="parmalink" class="small"><?php echo $_LINK['reload'] ?></a></p>
								<?php if (exist_plugin('search')) echo do_plugin_convert('search'); ?>
							</div>
<?php } ?>
<?php if (!empty($attaches)) { ?>
							<?php echo $hr ?>
<!-- * Attach * -->
							<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<aside id="attach" class="clearfix">'."\n" : '<div id="attach">'."\n"; ?>
								<?php echo $attaches ?>
							<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</aside>'."\n" : '</div>'."\n"; ?>
<!--  End Attach -->
<?php } ?>

<?php if (!empty($related)) { ?>
							<?php echo $hr ?>
<!-- * Related * -->
							<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<aside id="related" class="clearfix">'."\n" : '<div id="related">'."\n"; ?>
								<?php echo $related ?>
							<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</aside>'."\n" : '</div>'."\n"; ?>
<!--  End Related -->
<?php } ?>
							<?php echo exist_plugin('toolbar') ? do_plugin_convert('toolbar','reload,|,new,newsub,edit,freeze,source,diff,upload,copy,rename,|,top,list,search,recent,backup,referer,log,|,help,|,rss') : '';?>
						</div>
					</div>
				<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</aside>'."\n" : '</div>'."\n"; ?>
<!-- * End Shelf * --><?php } ?>

<!-- Content -->
				<div id="content">
					<div id="content-top" class="noprint"></div>
					<div id="<?php echo (arg_check('read') && exist_plugin_convert('menu')) ? 'primary-content' : 'single-content'; ?>" role="main">
<!-- * Main Content * -->
						<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<section id="body" class="body">'."\n" : '<div id="body" class="body">'."\n"; ?>
							<?php echo $body ?>
						<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</section>'."\n" : '</div>'."\n"; ?>
<?php if (!empty($notes)) { ?>
						<hr />
<!-- * Note * -->
						<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<aside id="note">'."\n" : '<div id="note">'."\n"; ?>
							<?php echo $notes ?>
						<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</aside>'."\n" : '</div>'."\n"; ?>
<!--  End Note -->
<?php } ?>
						<?php echo ($_SKIN['adarea']['footer']) ? '<hr /><div id="footer_ad" class="noprint">' . $_SKIN['adarea']['footer'] . '</div>' : ''; ?>
<!-- * end Main Content * -->
					</div>

<?php if (arg_check('read') && exist_plugin_convert('menu')){ ?>
<!-- * MenuBar * -->
					<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '<aside id="sidebar" class="noprint clearfix">' : '<div id="sidebar">')."\n"; ?>
						<?php echo do_plugin_convert('menu') ?>
					<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '</aside>'."\n" : '</div>')."\n"; ?>
<!-- * End MenuBar * -->
					<?php echo (!empty($lastmodified)) ? '<div id="lastmodified">Last-modified: '.$lastmodified.'</div>'."\n" : '' ?>
<?php }else{ echo $hr; } ?>
					<address role="contactinfo">Founded by <a href="<?php echo $modifierlink ?>"><?php echo $modifier ?></a></address>
					<div id="content-bottom" class="noprint"><a href="#header">&#x021EA;Top</a></div>
				</div>
<!-- End Content -->
			</div>

<!-- Footer -->
				<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<footer id="footer" class="noprint">'."\n" : '<div id="footer" class="noprint">'."\n"; ?>
					<p>
						<?php echo S_COPYRIGHT ?>. HTML convert time: <?php echo showtaketime() ?> sec.<br />
						<strong>x<sup>x</sup><sub>x</sub>Logue skin v2.4.0 RC</strong> by <a href="http://logue.be/" rel="external">Logue</a> / 
						based on <a href="http://xuyiyang.com/" rel="external">Xuyi Yang</a>'s <a href="http://xuyiyang.com/wordpress-themes/unnamed/" rel="external">Unnamed v1.23</a>.
					</p>
				<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</footer>'."\n" : '</div>'."\n"; ?>
<!-- End Footer -->
			
		<?php echo $pkwk_tags; ?>
	</body>
</html>
