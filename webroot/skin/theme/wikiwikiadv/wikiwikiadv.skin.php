<?php
/**
 * PukiWiki - Yet another WikiWikiWeb clone.
 *
 * WIKIWIKI Adv. Theme.
 * by Logue
 * Inspired from wikiwiki.jp default skin.
 *
 */
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $this->lang; ?>">
	<head prefix="og: http://ogp.me/ns# fb: http://www.facebook.com/2008/fbml">
<?php echo $this->head; ?>
		<link rel="stylesheet" type="text/css" href="<?php echo $this->path; ?>wikiwikiadv.css" />
		<title><?php echo $this->title . ' - ' . $this->site_name; ?></title>
	</head>
	<body>
		<?php if ($this->conf['adarea']['header']) echo '<div id="header_adspace" class="noprint">' . $this->conf['adarea']['header'] . '</div>'; ?>
		<div id="container" class="<?php echo $this->colums ?>" role="document">
<!-- *** Header *** -->
			<header id="header" role="banner">
<?php if (empty($this->headarea)){ ?>
				<a href="<?php echo $this->links['top'] ?>"><img id="logo" src="<?php echo $this->conf['logo']['src'] ?>" width="<?php echo $this->conf['logo']['width'] ?>" height="<?php echo $this->conf['logo']['height'] ?>" alt="<?php echo $this->conf['logo']['alt'] ?>" /></a>
				<hgroup id="hgroup">
					<h1><?php echo $this->site_name; ?></h1>
					<h2 id="description">PukiWiki - Yet another WikiWikiWeb clone.</h2>
				</hgroup>
<?php }else{ ?>
				<h1 id="title" style="display:none;"><?php echo $this->title ?></h1>
				<?php echo $this->headarea; ?>
<?php } ?>
				<?php if ($this->conf['adarea']['header'] && !isset($header)) echo '<div id="ad" class="noprint pull-right">' . $this->conf['adarea']['header'] . '</div>'; ?>
				
			</header>
<!-- *** End Header *** -->
			<div id="naviframe" class="clearfix">
				<?php echo $this->pluginBlock('navibar','top,new,edit,upload,login'); ?>
				<?php echo $this->pluginBlock('toolbar','list,recent,diff,backup,freeze,help');?>
				<?php echo $this->pluginBlock('search'); ?>
			</div>
			<div id="wrapper" class="clearfix">
				<div id="main_wrapper">
<!-- Center -->
					<div id="main">
						<?php echo $this->topicpath; ?>
						<hgroup id="title">
							<h1><a href="<?php echo $this->links['related'] ?>"><?php echo $this->title ?></a></h1>
							<?php echo (!empty($this->lastmodified)) ? '<h2 id="lastmodified">Last-modified: '.$this->lastmodified.'</h2>'."\n" : '' ?>
						</hgroup>

						<div id="body">
							<section role="main">
								<?php echo $this->body."\n" ?>
							</section>
<?php if (!empty($this->notes)) { ?>
							<hr />
<!-- * Note * -->
							<aside id="note" role="note">
								<?php echo $this->notes."\n" ?>
							</aside>
<!--  End Note -->
<?php } ?>
						</div>

						<?php if (!empty($this->conf['adarea']['footer'])) echo '<div id="footer_adspace" class="noprint" style="text-align:center;">' . $this->conf['adarea']['footer'] . '</div>'; ?>
					</div>
<?php if ($this->colums === 'three-colums')  { ?>
<!-- Left -->
				<aside id="menubar" role="navigation">
					<?php echo $this->menubar ?>
				</aside>
<?php } ?>

				</div>


<?php if ($this->colums === 'two-colums')  { ?>
<!-- Left -->
				<aside id="menubar" role="navigation">
					<?php echo $this->menubar ?>
				</aside>
<?php } ?>

<?php if ($this->colums === 'three-colums')  { ?>
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
				<ul>
					<li><address>Founded by <a href="<?php echo $this->modifierlink ?>"><?php echo $this->modifier ?></a></address></li>
					<li>Powered by <a href="http://pukiwiki.logue.be/" rel="product"><?php echo GENERATOR ?></a>.</li>
					<li>Processing time: <var><?php echo $this->proc_time; ?></var> sec.</li>
					<li class="f_right"><a href="<?php echo $this->links['rss'] ?>"><span class="pkwk-icon icon-rss"><?php echo $this->strings['skin']['rss'] ?></span></a></li>
				<ul>
			</footer>
		</div>
		<?php echo $this->js; ?>
	</body>
</html>
