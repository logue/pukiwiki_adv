<?php
/**
 * PukiWiki Advance - Yet another WikiWikiWeb clone.
 *
 * $Id: cloudwalk.skin.php,v 1.2.5 2014/09/09 22:36:00 Logue Exp$
 * Original is ari-
 */
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $this->lang; ?>">
	<head prefix="og: http://ogp.me/ns# fb: http://www.facebook.com/2008/fbml">
<?php echo $this->head; ?>
		<link rel="stylesheet" type="text/css" href="<?php echo $this->path; ?>cloudwalk.css.php" />
		<title><?php echo $this->title . ' - ' . $this->site_name; ?></title>
	</head>
	<body>
		
		<div id="wrapper" role="document"><!-- ■BEGIN id:wrapper -->
<!-- ◆ Header ◆ ========================================================== -->
			<header id="header" class="clearfix" role="banner">
				<h1 id="logo"><a href="<?php echo $this->links['top'] ?>"><?php echo $this->site_name ?></a></h1>
			</header>
<!-- ◆ Navigator ◆ ======================================================= -->
			<?php echo $this->navigation ? $this->navigation : $this->navibar ?>
<!-- ◆ Content ◆ ========================================================= -->
			<div id="main"><!-- ■BEGIN id:main -->
				<section id="content" class="clearfix"><!-- ■BEGIN id:content -->
					<hgroup class="hgroup">
						<h1 id="title"><?php echo $this->title ?></h1>
						<?php echo (!empty($this->lastmodified)) ? '<h2 id="lastmodified">Last-modified: '.$this->lastmodified.'</h2>'."\n" : '' ?>
					</hgroup>
					<div id="body" role="main"><!-- ■BEGIN id:body -->
<?php echo $this->body ?>
					</div><!-- □END id:body -->
					<div id="summary"><!-- ■BEGIN id:summary -->
<?php if (!empty($this->notes)) { ?>
<!-- ■BEGIN id:note -->
						<hr />>
						<aside id="note" role="note">
							<?php echo $this->notes ?>
						</aside>
<!-- □END id:note -->
<?php } ?>
<!-- ■BEGIN id:trackback -->
						<div id="trackback">
							<?php echo $this->pluginBlock('navibar','referer'); ?>
						</div>
<!-- □ END id:trackback -->
<?php if (!empty($this->related)) { ?>
<!-- ■ BEGIN id:related -->
						<aside id="related">
							<?php echo $this->related ?>
						</aside>
<!-- □ END id:related -->
<?php } ?>
<?php if (!empty($attaches)) { ?>
						<hr />
<!-- ■ BEGIN id:attach -->
						<aside id="attach">
							<?php echo $this->attaches ?>
						</aside>
<!-- □ END id:attach -->
<?php } ?>
					</div><!-- □ END id:summary -->
				</section><!-- □END id:content -->
<!-- ◆sidebar◆ ========================================================== -->
				<aside id="sidebar">
					<div id="search_form" class="bar"><!-- ■BEGIN id:search_form -->
						<h2><?php echo $this->strings['skin']['search'] ?></h2>
						<?php echo $this->pluginBlock('search'); ?>
					</div><!-- END id:search_form -->
					<div id="page_action" class="bar"><!-- ■BEGIN id:page_action -->
						<h2><?php echo $this->strings['skin']['edit'] ?></h2>
						<?php echo $this->pluginBlock('navibar', 'top,reload,new,edit,freeze,upload,diff,list,search,recent,backup,help,login'); ?>
					</div><!-- □END id:page_action -->
<?php if (!empty($this->menubar)) { ?>
					<div id="menubar" class="bar">
						<?php echo $this->menubar; ?>
					</div>
<?php } ?><!-- □END id:menubar -->

				</aside><!-- □END id:sidebar -->
			</div><!-- □END id:main -->
<!-- ◆ Footer ◆ ========================================================== -->
			<aside id="footer" role="contentinfo"><!-- ■BEGIN id:footer -->
				<div id="copyright"><!-- ■BEGIN id:copyright -->
					<address>Founded by <a href="<?php echo $this->modifierlink ?>"><?php echo $this->modifier ?></a></address>
					<?php echo S_COPYRIGHT ?><br />
					Processing time: <var><?php echo $this->proc_time; ?></var> sec.
				</div><!-- □END id:copyright -->
			</footer><!-- □END id:footer -->
<!-- ◆ END ◆ ============================================================= -->
		</div><!-- □END id:wrapper -->
<?php echo $this->js; ?>
	</body>
</html>
