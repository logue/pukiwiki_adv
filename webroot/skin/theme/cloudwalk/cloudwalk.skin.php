<?php
/////////////////////////////////////////////////
// PukiPlus - Yet another WikiWikiWeb clone.
//
// $Id: cloudwalk.skin.php,v 1.2.2 2011/09/11 22:57:00 Logue Exp$
// Original is ari-
// PukiWiki Advance edition by Logue

// Prohibit direct access
if (!defined('DATA_DIR')) { exit; }

global $pkwk_dtd, $_SKIN, $is_page, $defaultpage, $trackback, $referer;

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
		<div id="wrapper"><!-- ■BEGIN id:wrapper -->
<!-- ◆ Header ◆ ========================================================== -->
			<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '<header id="header" class="clearfix">'."\n" : '<div id="header" class="clearfix">')."\n"; ?>
				<h1 id="logo"><a href="<?php echo $_LINK['top'] ?>"><?php echo $page_title ?></a></h1>
			<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '</header>' : '</div>')."\n"; ?>
<!-- ◆ Navigator ◆ ======================================================= -->
			<?php if (exist_plugin('suckerfish')) echo do_plugin_convert('suckerfish'); ?>
<!-- ◆ Content ◆ ========================================================= -->
			<div id="main"><!-- ■BEGIN id:main -->
				<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '<section id="content" class="clearfix">'."\n" : '<div id="content" class="clearfix">')."\n"; ?><!-- ■BEGIN id:content -->
					<hgroup class="hgroup">
						<h1 id="title"><?php echo(($newtitle!='' && $is_read) ? $newtitle : $page) ?></h1>
<?php if ($lastmodified != '') { ?>
<!-- ■BEGIN id:lastmodified -->
						<h2 id="lastmodified">Last-modified: <?php echo $lastmodified ?></h2>
<?php } ?><!-- □END id:lastmodified -->
					</hgroup>
					<div id="body"><!-- ■BEGIN id:body -->
<?php echo $body ?>
					</div><!-- □END id:body -->
					<div id="summary"><!-- ■BEGIN id:summary -->
<?php if (!empty($notes)) { ?>
<!-- ■BEGIN id:note -->
					<?php echo $hr ?>
					<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<aside id="note">'."\n" : '<div id="note">'."\n"; ?>
						<?php echo $notes ?>
					<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</aside>'."\n" : '</div>'."\n"; ?>
<!-- □END id:note -->
<?php } ?>
						<div id="trackback">
<!-- ■BEGIN id:trackback -->
<?php if ($trackback) { ?>
							<a href="<?php echo $_LINK['trackback'] ?>"><span class="pkwk-icon icon-trackback"></span><?php echo $_LANG['skin']['trackback'].'('.tb_count($_page).')' ?></a> |
<?php } ?>
<?php if ($referer) { ?>
							<a href="<?php echo $_LINK['referer'] ?>"><span class="pkwk-icon icon-referer"></span><?php echo $_LANG['skin']['referer'] ?></a>
<?php } ?>

						</div>
<!-- □ END id:trackback -->
<?php if (!empty($related)) { ?>
<!-- ■ BEGIN id:related -->
						<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<aside id="related">'."\n" : '<div id="related">'."\n"; ?>
							<?php echo $related ?>
						<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</aside>'."\n" : '</div>'."\n"; ?>
<!-- □ END id:related -->
<?php } ?>
<?php if (!empty($attaches)) { ?>
						<hr />
<!-- ■ BEGIN id:attach -->
						<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<aside id="attach">'."\n" : '<div id="attach">'."\n"; ?>
							<?php echo $attaches ?>
						<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</aside>'."\n" : '</div>'."\n"; ?>
<!-- □ END id:attach -->
<?php } ?>
					</div><!-- □ END id:summary -->
				<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '</section>' : '</div>')."\n"; ?><!-- □END id:content -->
<!-- ◆sidebar◆ ========================================================== -->
				<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '<aside id="sidebar">'."\n" : '<div id="content">')."\n"; ?>
					<div id="search_form" class="bar"><!-- ■BEGIN id:search_form -->
						<h2><?php echo $_LANG['skin']['search'] ?></h2>
						<?php if (exist_plugin('search')) echo do_plugin_convert('search'); ?>
					</div><!-- END id:search_form -->
					<div id="page_action" class="bar"><!-- ■BEGIN id:page_action -->
						<h2><?php echo $_LANG['skin']['edit'] ?></h2>
						<ul>
<?php if ($is_page) { ?>
							<li><a href="<?php echo $_LINK['reload'] ?>"><span class="pkwk-icon icon-reload"></span><?php echo $_LANG['skin']['reload'] ?></a></li>
							<li><a href="<?php echo $_LINK['new'] ?>"><span class="pkwk-icon icon-new"></span><?php echo $_LANG['skin']['new'] ?></a></li>
							<li><a href="<?php echo $_LINK['edit'] ?>"><span class="pkwk-icon icon-edit"></span><?php echo $_LANG['skin']['edit'] ?></a></li>
<?php   if ($is_read and $function_freeze) { ?>
<?php     if ($is_freeze) { ?>
							<li><a href="<?php echo $_LINK['unfreeze'] ?>"><span class="pkwk-icon icon-unfreeze"></span><?php echo $_LANG['skin']['unfreeze'] ?></a></li>
<?php     } else { ?>
							<li><a href="<?php echo $_LINK['freeze'] ?>"><span class="pkwk-icon icon-freeze"></span><?php echo $_LANG['skin']['freeze'] ?></a></li>
<?php     } ?>
<?php   } ?>
<?php   if ((bool)ini_get('file_uploads')) { ?>
							<li><a href="<?php echo $_LINK['upload'] ?>"><span class="pkwk-icon icon-upload"></span><?php echo $_LANG['skin']['upload'] ?></a></li>
<?php   } ?>
							<li><a href="<?php echo $_LINK['diff'] ?>"><span class="pkwk-icon icon-diff"></span><?php echo $_LANG['skin']['diff'] ?></a></li>
<?php } ?>
							<li><a href="<?php echo $_LINK['list'] ?>"><span class="pkwk-icon icon-list"></span><?php echo $_LANG['skin']['list'] ?></a></li>
<?php if (arg_check('list')) { ?>
							<li><a href="<?php echo $_LINK['filelist'] ?>"><span class="pkwk-icon icon-filelist"></span><?php echo $_LANG['skin']['filelist'] ?></a></li>
<?php } ?>
							<li><a href="<?php echo $_LINK['search'] ?>"><span class="pkwk-icon icon-search"></span><?php echo $_LANG['skin']['search'] ?></a></li>
							<li><a href="<?php echo $_LINK['recent'] ?>"><span class="pkwk-icon icon-recent"></span><?php echo $_LANG['skin']['recent'] ?></a></li>
<?php if ($do_backup) { ?>
							<li><a href="<?php echo $_LINK['backup'] ?>"><span class="pkwk-icon icon-backup"></span><?php echo $_LANG['skin']['backup'] ?></a></li>
<?php } ?>
							<li><a href="<?php echo $_LINK['help'] ?>"><span class="pkwk-icon icon-help"></span><?php echo $_LANG['skin']['help'] ?></a></li>
						</ul>
					</div><!-- □END id:page_action -->
<?php global $body_menu; ?>
<?php if (!empty($body_menu)) { ?>
					<div id="menubar" class="bar">
						<?php echo $body_menu; ?>
					</div>
<?php } ?><!-- □END id:menubar -->

				<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '</aside>' : '</div>')."\n"; ?><!-- □END id:sidebar -->
			</div><!-- □END id:main -->
<!-- ◆ Footer ◆ ========================================================== -->
			<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<aside id="footer">'."\n" : '<div id="footer">'."\n"; ?><!-- ■BEGIN id:footer -->
				<div id="copyright"><!-- ■BEGIN id:copyright -->
					<address>Founded by <a href="<?php echo $modifierlink ?>"><?php echo $modifier ?></a></address>
					<?php echo S_COPYRIGHT ?><br />
					HTML convert time: <?php echo showtaketime() ?> sec.
				</div><!-- □END id:copyright -->
			<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '</footer>' : '</div>')."\n"; ?><!-- □END id:footer -->
<!-- ◆ END ◆ ============================================================= -->
		</div><!-- □END id:wrapper -->
<?php echo $pkwk_tags; ?>
	</body>
</html>
