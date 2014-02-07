<?php
/**
 * PukiWiki Advance - Yet another WikiWikiWeb clone.
 * $Id: mobile.ini.php,v 0.0.4 2014/02/05 19:02:30 Logue Exp $
 *
 * PukiWiki Adv. Mobile Theme
 * Copyright (C)
 *   2012-2014 PukiWiki Advance Developer Team
 */
(bool) $is_dialog = isset($vars['cmd']) ? preg_match('/attach|freeze|unfreeze|diff|new|upload|log|search|backup|list/', $vars['cmd']) : false;
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $this->lang; ?>">
	<head>
		<?php echo $this->head; ?>
		<link rel="stylesheet" type="text/css" href="<?php echo $this->path; ?>mobile.css.php" />
		<title><?php echo $this->site_name; ?></title>
	</head>

	<body>
		<article data-role="page">
			<header data-role="header" role="banner" data-position="fixed" data-add-back-btn="true">
				<h1><?php echo $this->title ?></h1>
<?php if (!$is_dialog) { ?>
				<a href="#toolbar" data-icon="gear" class="ui-btn-right" data-rel="popup" data-position-to="window" data-role="button" data-inline="true"  data-transition="slideup"><?php echo $this->strings['skin']['tool']; ?></a>
<?php } ?>
				<?php if (!empty($this->conf['adarea'])) echo '<div class="adarea"></div>'; ?>
<?php if (arg_check('read') ){ ?>
				<nav data-role="navbar">
					<ul>
<?php if (!empty($this->menubar)) { ?>
						<li><a href="#menubar"><?php echo $this->strings['skin']['menu']; ?></a></li>
<?php } ?>
<?php if (!empty($this->sidebar)) { ?>
						<li><a href="#sidebar" data-inline="true"><?php echo $this->strings['skin']['side']; ?></a></li>
<?php } ?>
					</ul>
				</nav>
<?php } ?>
			</header>

			<main role="main" data-role="content" data-theme="c">
				<?php echo $this->body ?>
			</main>

<?php if (arg_check('read') ){ ?>
	<?php if (!empty($this->menubar)) { ?>
			<aside data-role="panel" id="menubar" data-theme="b" data-content-theme="d" data-position="left" data-display="reveal">
				<?php echo $this->menubar ?>
			</aside>
	<?php } ?>
	<?php if (!empty($this->sidebar)) { ?>
			<aside data-role="panel" id="sidebar" data-theme="b" data-content-theme="d" data-position="right" data-display="reveal">
				<?php echo $this->sidebar ?>
			</aside>
	<?php } ?>
<?php } ?>
			<aside data-role="popup" id="toolbar">
				<div data-role="header" data-theme="a" class="ui-corner-top">
					<h1><?php echo $this->strings['skin']['tool']; ?></h1>
					<a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a>
				</div>
				<div data-role="content" data-theme="d" class="ui-corner-bottom ui-content">
					<?php echo $this->navibar ?>
				</div>
			</aside>
			<footer data-role="footer" role="contentinfo" data-position="fixed">
				<h4>Founded by <a href="<?php echo $this->modifierlink ?>"><?php echo $this->modifier ?></a></h4>
				<p class="text-center h6">Processing time: <var><?php echo $this->proc_time; ?></var> sec.</p>
			</footer><!-- /footer -->
		</article><!-- /page -->

		<?php if (!empty($this->conf['adarea'])) echo '<div id="adarea_content">' . $this->conf['adarea'] . '</div>'; ?>
		<?php echo $this->js; ?>
	</body>
</html>
