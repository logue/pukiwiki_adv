<?php
/**
 * PukiWiki Advance - Yet another WikiWikiWeb clone.
 *
 * PukiWiki Plus! skin for PukiWiki Advance.
 * Original version by miko and upk.
 * Modified by Logue
 *
 * $Id: default.skin.php,v 1.4.19 2014/02/07 18:45:00 Logue Exp $
 */
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $this->lang; ?>">
	<head prefix="og: http://ogp.me/ns# fb: http://www.facebook.com/2008/fbml">
<?php echo $this->head; ?>
		<link rel="stylesheet" type="text/css" href="<?php echo $this->path; ?>default.css.php" />
		<link rel="stylesheet" type="text/css" href="<?php echo $this->path; ?>blue.css" id="coloring" />
		<title><?php echo $this->title . ' - ' . $this->site_name; ?></title>
	</head>
	<body>
		<div id="container" class="<?php echo $this->colums ?>" role="document">
<!-- *** Header *** -->
			<header id="header" role="banner">
<?php if (empty($this->headarea)){ ?>
				<a href="<?php echo $this->links['top'] ?>"><img id="logo" src="<?php echo $this->conf['logo']['src'] ?>" width="<?php echo $this->conf['logo']['width'] ?>" height="<?php echo $this->conf['logo']['height'] ?>" alt="<?php echo $this->conf['logo']['alt'] ?>" /></a>
				<div id="hgroup">
					<h1 id="title"><a href="<?php echo $this->links['related'] ?>"><?php echo $this->title ?></a></h1>
					<?php if (!empty($this->links)) { ?><h2><a href="<?php echo $this->links['reload'] ?>" id="parmalink"><?php echo $this->links['reload'] ?></a></h2><?php } ?>
				</div>
<!-- * Ad space *-->
				<?php if ($this->conf['adarea']['header']) echo '<div id="ad" class="noprint">' . $this->conf['adarea']['header'] . '</div>'; ?>
<!-- * End Ad space * -->
<?php }else{ ?>
				<h1 id="title" style="display:none;"><?php echo $this->title ?></h1>
				<?php echo $this->headarea; ?>
<?php } ?>
				<?php echo (!empty($this->lastmodified)) ? '<div id="lastmodified">Last-modified: '.$this->lastmodified.'</div>'."\n" : '' ?>
			</header>
<!-- *** End Header *** -->

			<?php echo !empty($view->navigation) ? $view->navigation : $this->navibar . '<hr />'; ?>

			<div id="wrapper" class="clearfix">
<!-- Center -->
				<div id="main_wrapper">
					<div id="main" role="main">
						<nav id="topicpath">
							<?php echo $this->topicpath; ?>
						</nav>
						<section id="body">
							<?php echo $this->body."\n" ?>
						</section>
<?php if (!empty($notes)) { ?>
						<hr />
						<!-- * Note * -->
						<aside id="note" role="note">
							<?php echo $this->notes."\n" ?>
						</aside>
						<!--  End Note -->
<?php } ?>
						<?php if (!empty($this->conf['adarea']['footer'])) echo '<div id="footer_adspace" class="noprint" style="text-align:center;">' . $this->conf['adarea']['footer'] . '</div>'; ?>
					</div>
				</div>

<?php if ($this->colums == 'three-colums' || $this->colums == 'two-colums')  { ?>
<!-- Left -->
				<aside id="menubar" role="navigation">
					<?php echo $this->menubar ?>
				</aside>
<?php } ?>

<?php if ($this->colums == 'three-colums')  { ?>
<!-- Right -->
				<aside id="sidebar" role="navigation">
					<?php echo $this->sidebar ?>
				</aside>
<?php } ?>
			</div>
			<div id="misc">
<?php if (!empty($this->attaches)) { ?>
				<hr />
				<!-- * Attach * -->
				<aside id="attach">
					<?php echo $this->attaches ?>
				</aside>
				<!--  End Attach -->
<?php } ?>
<?php if (!empty($this->related)) { ?>
				<!-- * Related * -->
				<hr />
				<aside id="related">
					<?php echo $this->related ?>
				</aside>
				<!--  End Related -->
<?php } ?>
				<hr />
				<?php echo $this->toolbar ?>
			</div>

			<footer id="footer" class="clearfix" role="contentinfo">
<?php if (!empty($this->footarea)) { ?>
				<?php echo $this->footarea; ?>
<?php }else{ ?>
				<div id="qr_code">
					<?php echo $this->pluginBlock('qrcode'); ?>
				</div>
				<address>Founded by <a href="<?php echo $this->modifierlink ?>" rel="contact"><?php echo $this->modifier ?></a></address>
				<div id="sigunature">
					Powered by <a href="http://pukiwiki.logue.be/" rel="external"><?php echo GENERATOR ?></a>.
					Processing time: <var><?php echo $this->proc_time; ?></var> sec.<br />
					Original Theme Design by <a href="http://pukiwiki.cafelounge.net/plus/" rel="external">PukiWiki Plus!</a> Team.
				</div>
				<div id="banner_box">
					<a href="http://pukiwiki.logue.be/" rel="external"><img src="<?php echo IMAGE_URI; ?>pukiwiki_adv.banner.png" width="88" height="31" alt="PukiWiki Advance" title="PukiWiki Advance" /></a>
					<a href="http://validator.w3.org/check/referer" rel="external"><img src="<?php echo IMAGE_URI ?>html5.png" width="88" height="31" alt="HTML 5" title="HTML5" /></a>
				</div>
<?php } ?>
			</footer>
		</div>
		<?php echo $this->js; ?>
		<script type="text/javascript" src="<?php echo $this->path; ?>default.js" defer="defer"></script>
	</body>
</html>
