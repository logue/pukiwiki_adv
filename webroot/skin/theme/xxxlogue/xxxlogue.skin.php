<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: xxxlogue.skin.php,v 2.4.2 2012/07/09 07:55:00 Logue Exp $
// Copyright (C) 2010-2012 PukiWiki Advance Developers Team
//               2007-2010 Logue
//
// PukiWiki Advance xxxLogue skin
//
// Based on
//   Xu Yiyang's (http://xuyiyang.com/) Unnamed (http://xuyiyang.com/wordpress-themes/unnamed/)'
//
// License: GPL v3 or (at your option) any later version
// http://www.opensource.org/licenses/gpl-3.0.html
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head prefix="og: http://ogp.me/ns# fb: http://www.facebook.com/2008/fbml">
<?php echo $this->head; ?>
		<link rel="stylesheet" type="text/css" href="<?php echo $this->path; ?>xxxlogue.css.php" />
		<title><?php echo $this->title . ' - ' . $this->site_name; ?></title>
	</head>
	<body>
		<div id="container" role="document">
<!-- ** Navigator ** -->
			<?php echo $this->navibar; ?>
<!--  End Navigator -->
<!-- Header -->
			<header id="header" class="clearfix" role="banner">
<!-- * Title * -->
				<div id="hgroup" role="banner">
					<h1 id="title"><a href="<?php echo $this->links['related'] ?>"><?php echo $this->title ?></a></h1>
					<?php echo $this->topicpath; ?>
				</div>
<!-- * End Title * -->
<!-- * Ad space *-->
				<?php echo ($this->conf['adarea']['header']) ? '<div id="header_ad" class="noprint">' . $this->conf['adarea']['header'] . '</div>' : ''; ?>
<!-- * End Ad space * -->
			</header>
<!-- End Header -->
<?php if (arg_check('read')){ ?><!-- * Shelf * -->
			<aside id="shelf">
				<div id="toggle">
					<div id="inner_toggle">
<?php if (!empty($this->menubar)) { ?>
						<div id="shelf_form">
							<p><a href="<?php echo $this->links['reload'] ?>" id="parmalink" class="small"><?php echo $this->links['reload'] ?></a></p>
							<?php echo $this->pluginBlock('search'); ?>
						</div>
<?php } ?>
<?php if (!empty($this->attaches)) { ?>
						<hr />
<!-- * Attach * -->
						<aside id="attach" class="clearfix">
							<?php echo $this->attaches ?>
						</aside>
<!--  End Attach -->
<?php } ?>

<?php if (!empty($this->related)) { ?>
						<hr />
<!-- * Related * -->
						<aside id="related" class="clearfix">
							<?php echo $this->related ?>
						</aside>
<!--  End Related -->
<?php } ?>
						<?php echo $this->toolbar;?>
					</div>
				</div>
			</aside>
<!-- * End Shelf * --><?php } ?>

<!-- Content -->
			<div id="content">
				<div id="content-top" class="noprint"></div>
				<div id="<?php echo (arg_check('read') && !empty($this->menubar)) ? 'primary-content' : 'single-content'; ?>" role="main">
<!-- * Main Content * -->
					<section id="body" class="body" role="main">
						<?php echo $this->body ?>
					</section>
<?php if (!empty($this->notes)) { ?>
					<hr />
<!-- * Note * -->
					<aside id="note">
						<?php echo $this->notes ?>
					</aside>
<!--  End Note -->
<?php } ?>
					<?php echo ($this->conf['adarea']['footer']) ? '<hr /><div id="footer_ad" class="noprint">' . $this->conf['adarea']['footer'] . '</div>' : ''; ?>
<!-- * end Main Content * -->
				</div>

<?php if (arg_check('read') && !empty($this->menubar)){ ?>
<!-- * MenuBar * -->
				<aside id="sidebar" class="noprint clearfix"  role="navigation">
					<?php echo $this->menubar ?>
				</aside>
<!-- * End MenuBar * -->
				<?php echo (!empty($this->lastmodified)) ? '<div id="lastmodified">Last-modified: '.$this->lastmodified.'</div>'."\n" : '' ?>
<?php }else{ echo '<hr />'; } ?>
				<address role="contactinfo">Founded by <a href="<?php echo $this->modifierlink ?>"><?php echo $this->modifier ?></a></address>
				<div id="content-bottom" class="noprint"><a href="#header">&#x021EA;Top</a></div>
			</div>
<!-- End Content -->
		</div>

<!-- Footer -->
		<footer id="footer" class="noprint" role="contactinfo">
			<p>
				<?php echo S_COPYRIGHT ?>. HTML convert time: <?php echo $this->proc_time; ?> sec.<br />
				<strong>x<sup>x</sup><sub>x</sub>Logue skin v2.5.0</strong> by <a href="http://logue.be/" rel="external">Logue</a> / 
				based on <a href="http://xuyiyang.com/" rel="external">Xuyi Yang</a>&apos;s <a href="http://xuyiyang.com/wordpress-themes/unnamed/" rel="external">Unnamed v1.23</a>.
			</p>
		</footer>
<!-- End Footer -->
		<?php echo $this->js; ?>
		<script type="text/javascript" src="<?php echo $this->path; ?>xxxlogue.js" />
	</body>
</html>
