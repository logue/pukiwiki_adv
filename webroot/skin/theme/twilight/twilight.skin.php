<?php
/**
 * PukiWiki Advance - Yet another WikiWikiWeb clone.
 *
 * Twilight v 1.0 
 * by fuyuka88 < https://github.com/fuyuka88 >
 * Modified by Logue
 *
 * $Id: default.skin.php,v 1.0.0 2014/08/31 0:10:00 Logue Exp $
 */
global $vars;
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $this->lang; ?>">
	<head prefix="og: http://ogp.me/ns# fb: http://www.facebook.com/2008/fbml">
<?php echo $this->head; ?>
		<link rel="stylesheet" type="text/css" href="<?php echo $this->path; ?>twilight.css.php" charset="UTF-8" />
		<title><?php echo $this->title . ' - ' . $this->site_name; ?></title>
	</head>

	<body>
		<header id="header">
			<h1 class="title"><a href="<?php echo $this->links['top']?>"><?php echo $this->title ?></a></h1>
		</header>
<?php echo $this->navibar; ?>

<?php if (!empty($this->menubar)){ ?>
		<div id="contents" class="clearfix">
			<aside id="menubar">
				<?php echo $this->menubar ?>
			</aside>
			<div id="body">
				<aside id="bodyNav"><?php echo $this->pluginBlock('navibar','edit,freeze,diff,upload,reload') ?></aside>
				<section role="main"><?php echo $this->body."\n" ?></section>
			</div>
		</div>
		
<?php } else { ?>
		<section id="body" class="nonColumn" role="main">
			<?php echo $this->body."\n" ?>
		</section>
<?php } ?>

<?php if (!empty($this->notes)) { ?>
		<aside id="note" class="footbox clearfix" role="note">
			<?php echo $this->notes ?>
		</aside>
<?php } ?>

<?php if (!empty($this->attaches)) { ?>
		<aside id="attach">
			<?php echo $this->attaches ?>
		</aside>
<?php } ?>

<?php if ( isset($this->lastmodified) ) { ?>
		<div id="lastmodified" class="text-right">Last update on <?php echo $this->lastmodified ?></div>
<?php } ?>

<?php if (!empty($this->related)) { ?>
		<aside id="related" class="footbox">
			<?php echo $this->related ?>
		</aside>
<?php } ?>

		<footer id="footer">
			<address>Site admin: <a href="<?php echo $this->modifierlink ?>"><?php echo $this->modifier ?></a></address>
			<?php echo S_COPYRIGHT ?>.
			Powered by PHP <?php echo PHP_VERSION ?>. Processing time: <var><?php echo $this->proc_time; ?></var> sec.
		</footer>
		<?php echo $this->js; ?>
	</body>
</html>
