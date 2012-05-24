<?php
///////////////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: 180wiki.skin.php,v 0.4.1 2012/05/24 19:42:00 Logue Exp $
//
// 180wiki skin by hrmz <http://180.style.coocan.jp/wiki/>
// Modified by Logue
///////////////////////////////////////////////////////////
global $pkwk_dtd, $_SKIN, $is_page, $defaultpage;

if (!defined('DATA_DIR')) { exit; }

// Output HTML DTD, <html>, and receive content-type
$meta_content_type = (isset($pkwk_dtd)) ? pkwk_output_dtd($pkwk_dtd) : pkwk_output_dtd();
?>
	<head>
		<?php echo $meta_content_type; ?>
		<?php echo $pkwk_head; ?>
		<title><?php echo $title ?> - <?php echo $page_title ?></title>
	</head>
	<body>
		<div id="wrapper" role="document"><!-- ■BEGIN id:wrapper -->
<!-- ◆ Header ◆ ========================================================== -->
			<header id="header" role="banneer">
				<div id="logo"><a href="<?php echo $_LINK['top'] ?>"><?php echo $page_title ?></a></div>
			</header>

<!-- ◆ Content ◆ ========================================================= -->
			<div id="main"><!-- ■BEGIN id:main -->
				<div id="wrap_content"><!-- ■BEGIN id:wrap_content -->
<!-- ◆ anchor ◆ -->
					<div id="navigator"></div>
<!-- ◆ Toolbar ◆ -->
					<?php echo exist_plugin('toolbar') ? do_plugin_convert('toolbar','reload,|,new,newsub,edit,freeze,source,diff,upload,copy,rename,|,top,list,search,recent,backup,referer,log,|,help,|,rss') : '';?>

					<div id="content" role="main"><!-- ■BEGIN id:content -->
						<h1 class="title"><?php echo(($newtitle!='' && $is_read)?$newtitle:$page) ?></h1>
<?php if (isset($lastmodified)) { ?><!-- ■BEGIN id:lastmodified -->
						<div id="lastmodified">Last-modified: <?php echo $lastmodified ?></div>
<?php } ?><!-- □END id:lastmodified -->
						<div id="topicpath"><!-- ■BEGIN id:topicpath -->
							<?php if ($is_page and exist_plugin_convert('topicpath')) { echo do_plugin_convert('topicpath'); } ?>
						</div><!-- □END id:topicpath -->
						<div id="body"><!-- ■BEGIN id:body -->
							<?php echo $body ?>
						</div><!-- □END id:body -->
						<div id="summary"><!-- ■BEGIN id:summary -->
<?php if (isset($notes)) { ?><!-- ■BEGIN id:note -->
							<div id="note">
								<?php echo $notes ?>
							</div>
<?php } ?><!-- □END id:note -->
							<div id="trackback"><!-- ■BEGIN id:trackback -->
								<?php if (exist_plugin('navibar')) echo do_plugin_convert('navibar','trackback,referer'); ?>
							</div><!-- □ END id:trackback -->
<?php if (isset($related)) { ?><!-- ■ BEGIN id:related -->
							<div id="related">
								<?php echo $related ?>
							</div>
<?php } ?><!-- □ END id:related -->
<?php if (isset($attaches)) { ?><!-- ■ BEGIN id:attach -->
							<div id="attach">
								<?php echo $hr ?>
								<?php echo $attaches ?>
							</div>
<?php } ?><!-- □ END id:attach -->
						</div><!-- □ END id:summary -->
					</div><!-- □END id:content -->
				</div><!-- □ END id:wrap_content -->
<!-- ◆sidebar◆ ========================================================== -->
				<div id="wrap_sidebar"><!-- ■BEGIN id:wrap_sidebar -->
					<div id="sidebar">
<?php if (arg_check('read') && exist_plugin_convert('menu')) { ?>
<!-- ■BEGIN id:menubar -->
						<aside id="menubar" >
							<?php echo do_plugin_convert('menu') ?>
						</aside>
<?php } ?><!-- □END id:menubar -->

					</div><!-- □END id:sidebar -->
				</div><!-- □END id:wrap_sidebar -->
			</div><!-- □END id:main -->
<!-- ◆ Footer ◆ ========================================================== -->
			<footer id="footer" role="contuctinfo"><!-- ■BEGIN id:footer -->
				<address>Site admin: <a href="<?php echo $modifierlink ?>"><?php echo $modifier ?></a></address>
				<?php echo S_COPYRIGHT ?>.
				Designed by <a href="http://180.style.coocan.jp/wiki/" rel="product">180.style</a>. 
				HTML convert time: <?php echo showtaketime() ?> sec.
			</footer><!-- □END id:footer -->
<!-- ◆ END ◆ ============================================================= -->
		</div><!-- □END id:wrapper -->
		<?php echo $pkwk_tags; ?>
	</body>
</html>
