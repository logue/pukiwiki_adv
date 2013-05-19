<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: classic.ini.php,v 0.0.2 2012/03/31 16:49:30 Logue Exp $
// 
// PukiWiki Adv. Mobile Theme
// Copyright (C)
//   2012 PukiWiki Advance Developer Team
(bool) $is_dialog = isset($vars['cmd']) ? preg_match('/attach|freeze|unfreeze|diff|new|upload|log|search|backup|list/', $vars['cmd']) : false;

// Output HTML DTD, <html>, and receive content-type
?>
<!doctype html>
<html>
	<head>
		<?php echo $this->head; ?>
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<title><?php echo $this->site_name; ?></title>
	</head>

	<body>
		<article data-role="page">
			<header data-role="header" role="banner" id="header">
				<h1><?php echo $this->title ?></h1>
<?php if (!$is_dialog) { ?>
				<a href="#Tool" data-icon="gear" class="ui-btn-right" data-rel="popup" data-position-to="window" data-role="button" data-inline="true" data-transition="pop"><?php echo $this->lang['skin']['tool']; ?></a>
				<?php if (!empty($this->conf['adarea'])) echo '<div class="adarea"></div>'; ?>
<?php } ?>
<?php if (arg_check('read') ){ ?>
				<nav data-role="navbar">
					<ul>
<?php if (!empty($this->menubar)) { ?>
						<li><a href="#MenuBar"><?php echo $this->lang['skin']['menu']; ?></a></li>
<?php } ?>
<?php if (!empty($this->sidebar)) { ?>
						<li><a href="#SideBar"><?php echo $this->lang['skin']['side']; ?></a></li>
<?php } ?>
					</ul>
				</nav>
<?php } ?>
			</header>

			<section role="main" data-role="content" data-theme="c">
				<?php echo $this->body ?>
			</section>

<?php if (arg_check('read') ){ ?>
	<?php if (!empty($this->menubar)) { ?>
			<aside data-role="panel" id="MenuBar" data-theme="b" data-content-theme="d" data-position="left" data-display="reveal">
				<?php echo $this->menubar ?>
			</aside>
	<?php } ?>
	<?php if (!empty($this->sidebar)) { ?>
			<aside data-role="panel" id="SideBar" data-theme="b" data-content-theme="d" data-position="right" data-display="reveal">
				<?php echo $this->sidebar ?>
			</aside>
	<?php } ?>
<?php } ?>
			<aside data-role="popup" id="Tool">
				<div data-role="header" data-theme="a" class="ui-corner-top">
					<h1><?php echo $this->lang['skin']['tool']; ?></h1>
					<a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a>
				</div>
				<div data-role="content" data-theme="d" class="ui-corner-bottom ui-content">
					<?php echo $this->navibar ?>
				</div>
			</aside>
			<footer data-role="footer" role="contentinfo" id="footer">
				<h4>Founded by <a href="<?php echo $this->modifierlink ?>"><?php echo $this->modifier ?></a></h4>
				<h5 style="text-align:center; clear:both; font-size:87%;"><?php if (!$is_dialog) { ?>Powered by <a href="http://pukiwiki.logue.be/"><?php echo GENERATOR ?></a>. <?php } ?>HTML convert time: <?php echo showtaketime() ?> sec.</h5>
			</footer><!-- /footer -->
		</article><!-- /page -->
		

		<?php if (!empty($_SKIN['adarea'])) echo '<div id="adarea_content">' . $_SKIN['adarea'] . '</div>'; ?>
		<?php echo $this->js; ?>
	</body>
</html>
