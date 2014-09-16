<?php
/**
 * PukiWiki Advance - Yet another WikiWikiWeb clone.
 *
 * $Id: 180wiki.skin.php,v 0.4.2 2014/02/07 18:37:00 Logue Exp $
 *
 * 180wiki skin by hrmz <http://180.style.coocan.jp/wiki/>
 * Modified by Logue
 */
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $this->lang; ?>">
	<head prefix="og: http://ogp.me/ns# fb: http://www.facebook.com/2008/fbml">
<?php echo $this->head; ?>
		<link rel="stylesheet" type="text/css" href="<?php echo $this->path; ?>180wiki.css.php" />
		<title><?php echo $this->title . ' - ' . $this->site_name; ?></title>
	</head>
	<body>
		<div id="wrapper" role="document"><!-- ■BEGIN id:wrapper -->
<!-- ◆ Header ◆ ========================================================== -->
			<header id="header" role="banneer">
				<div id="logo"><a href="<?php echo $this->links['top'] ?>"><?php echo $this->site_name ?></a></div>
			</header>

<!-- ◆ Content ◆ ========================================================= -->
			<div id="main"><!-- ■BEGIN id:main -->
				<div id="wrap_content"><!-- ■BEGIN id:wrap_content -->
<!-- ◆ anchor ◆ -->
					<div id="navigator"><?php echo $this->toolbar; ?></div>
<!-- ◆ Toolbar ◆ -->
					<div id="content" role="main"><!-- ■BEGIN id:content -->
						<h1 class="title"><a href="<?php echo $this->links['related'] ?>"><?php echo $this->title ?></a></h1>
<?php if (isset($this->lastmodified)) { ?><!-- ■BEGIN id:lastmodified -->
						<?php echo (!empty($this->lastmodified)) ? '<div id="lastmodified">Last-modified: '.$this->lastmodified.'</div>'."\n" : '' ?>
<?php } ?><!-- □END id:lastmodified -->
						<div id="topicpath"><!-- ■BEGIN id:topicpath -->
							<?php echo $this->topicpath; ?>
						</div><!-- □END id:topicpath -->
						<div id="body"><!-- ■BEGIN id:body -->
							<?php echo $this->body ?>
						</div><!-- □END id:body -->
						<div id="summary"><!-- ■BEGIN id:summary -->
<?php if (isset($this->notes)) { ?><!-- ■BEGIN id:note -->
							<div id="note">
								<?php echo $this->notes ?>
							</div>
<?php } ?><!-- □END id:note -->
							<div id="trackback"><!-- ■BEGIN id:trackback -->
								<?php $this->navibar; ?>
							</div><!-- □ END id:trackback -->
<?php if (isset($this->related)) { ?><!-- ■ BEGIN id:related -->
							<div id="related">
								<?php echo $this->related ?>
							</div>
<?php } ?><!-- □ END id:related -->
<?php if (isset($this->attaches)) { ?><!-- ■ BEGIN id:attach -->
							<hr />
							<div id="attach">
								<?php echo $this->attaches ?>
							</div>
<?php } ?><!-- □ END id:attach -->
						</div><!-- □ END id:summary -->
					</div><!-- □END id:content -->
				</div><!-- □ END id:wrap_content -->
<!-- ◆sidebar◆ ========================================================== -->
				<div id="wrap_sidebar"><!-- ■BEGIN id:wrap_sidebar -->
					<div id="sidebar">
<?php if ($this->is_read && !empty($this->menubar))  { ?>
<!-- ■BEGIN id:menubar -->
						<aside id="menubar" >
							<?php echo $this->menubar ?>
						</aside>
<?php } ?><!-- □END id:menubar -->

					</div><!-- □END id:sidebar -->
				</div><!-- □END id:wrap_sidebar -->
			</div><!-- □END id:main -->
<!-- ◆ Footer ◆ ========================================================== -->
			<footer id="footer" role="contuctinfo"><!-- ■BEGIN id:footer -->
				<address>Site admin: <a href="<?php echo $this->modifierlink ?>"><?php echo $this->modifier ?></a></address>
				<?php echo S_COPYRIGHT ?>.
				Designed by <a href="http://180xz.com/">180.style</a>. 
				Processing time: <var><?php echo $this->proc_time; ?></var> sec.
			</footer><!-- □END id:footer -->
<!-- ◆ END ◆ ============================================================= -->
		</div><!-- □END id:wrapper -->
		<?php echo $this->js; ?>
	</body>
</html>
