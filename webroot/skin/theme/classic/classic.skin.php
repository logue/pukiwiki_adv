<?php
/**
 * PukiWiki Advance - Yet another WikiWikiWeb clone.
 * $Id: classic.skin.php,v 1.6.5 2014/02/07 18:28:00 Logue Exp $
 *
 * PukiWiki Classic Skin for PukiWiki Advance
 * Copyright (C)
 *   2010-2014 PukiWiki Advance Developers Team
 *   2005-2007 Logue (LogueWiki Skin)
 *   2002-2005 PukiWiki Developers Team
 *   2001-2002 Originally written by yu-ji
 */
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $this->lang; ?>">
	<head prefix="og: http://ogp.me/ns# fb: http://www.facebook.com/2008/fbml">
<?php echo $this->head; ?>
		<link rel="stylesheet" type="text/css" href="<?php echo $this->path; ?>classic.css.php" />
		<title><?php echo $this->title . ' - ' . $this->site_name; ?></title>
	</head>
	<body>
		<div id="base" role="document">
<!-- *** Header *** -->
			<header id="header" class="clearfix" role="banner">
<?php if (!empty($this->headarea)){ ?>
				<div id="hgroup" style="display:none;">
					<h1 id="title"><?php echo $this->title ?></h1>
					<h2><a href="<?php echo $this->links['reload'] ?>"><?php echo $this->links['reload'] ?></a></h2>
				</div>
				<?php echo $this->headarea ?>
<?php } else { ?>
				<a href="<?php echo $this->links['top'] ?>"><img id="logo" src="<?php echo $this->conf['logo']['src'] ?>" width="<?php echo $this->conf['logo']['width'] ?>" height="<?php echo $this->conf['logo']['height'] ?>" alt="<?php echo $this->conf['logo']['alt'] ?>" /></a>
				<div id="hgroup">
					<h1 id="title"><a href="<?php echo $this->links['related'] ?>"><?php echo $this->title ?></a></h1>
					<?php echo $this->topicpath; ?>
				</div>
<!-- * Ad space *-->
				<?php if ($this->conf['adarea']['header']) echo '<div id="ad" class="noprint">' . $this->conf['adarea']['header'] . '</div>'; ?>
<!-- * End Ad space * -->
				<?php echo (!empty($this->lastmodified)) ? '<div id="lastmodified">Last-modified: '.$this->lastmodified.'</div>'."\n" : '' ?>
<?php } ?>
			</header>
<!-- *** End Header *** -->
			<?php echo !empty($view->navigation) ? $view->navigation : $this->navibar . '<hr />'; ?>
<!-- ** Body ** -->
<?php if (!empty($this->menubar))  { ?>
			<div class="clearfix">
				<div id="content" class="body">
<?php }else{ ?>
				<div class="body">
<?php } ?>
				<section id="body" role="main">
					<?php echo $this->body ?>
				</section>

<?php if (!empty($this->notes)) { ?>
				<hr />
<!-- * Note * -->
				<aside id="note" role="note">
						<?php echo $this->notes ?>
				</aside>
<!--  End Note -->
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
<!-- * Ad space * -->
					<?php if (!empty($this->conf['adarea']['footer'])) echo '<div id="footer_adspace" class="noprint" style="text-align:center;">' . $this->conf['adarea']['footer'] . '</div>'; ?>
<!-- * End Ad space * -->
				</div>
<!-- ** End Body ** -->
<?php if (!empty($this->menubar))  { ?>
<!-- ** MenuBar ** -->
				<aside id="menubar">
					<?php echo $this->menubar ?>
				</aside>
<!-- ** End MenuBar ** -->
			</div>
<?php } ?>
			<hr />
			<?php echo $this->toolbar;?>
<!-- *** Footer *** -->
			<footer id="footer" role="contentinfo">
				<div id="qr_code">
					<?php echo $this->pluginBlock('qrcode'); ?>
				</div>
				<address>Founded by <a href="<?php echo $this->modifierlink ?>" rel="contact"><?php echo $this->modifier ?></a></address>
				<div id="sigunature">
					<?php echo S_COPYRIGHT ?><br />
					Processing time: <var><?php echo $this->proc_time; ?></var> sec.
				</div>
				<div id="banner_box">
					<a href="http://pukiwiki.logue.be/"><img src="<?php echo IMAGE_URI; ?>pukiwiki_adv.banner.png" width="88" height="31" alt="PukiWiki Advance" title="PukiWiki Advance" /></a>
					<a href="http://validator.w3.org/check/referer"><img src="<?php echo IMAGE_URI ?>html5.png" width="88" height="31" alt="HTML5" title="HTML5" /></a>
				</div>
			</footer>
<!-- *** End Footer *** -->
		</div>
		<?php echo $this->js; ?>
	</body>
</html>
