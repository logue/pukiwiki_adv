<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: classic.ini.php,v 0.0.2 2012/03/31 16:49:30 Logue Exp $
// 
// PukiWiki Adv. Mobile Theme
// Copyright (C)
//   2012 PukiWiki Advance Developer Team

global $pkwk_dtd, $_SKIN, $is_page, $defaultpage, $do_backup, $menubar, $sidebar;

if (!defined('DATA_DIR')) { exit; }
$_SKIN['showicon'] = true;

if ($newtitle != '' && $is_read) {
	$title = $newtitle;
}

(bool) $is_dialog = isset($vars['cmd']) ? preg_match('/attach|freeze|unfreeze|diff|new|upload|log|search|backup|list/', $vars['cmd']) : false;

// Output HTML DTD, <html>, and receive content-type
$meta_content_type = (isset($pkwk_dtd)) ? pkwk_output_dtd($pkwk_dtd) : pkwk_output_dtd();
?>
	<head>
		<?php echo $meta_content_type; ?>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<?php echo $pkwk_head; ?>
		<title><?php echo $page_title; ?></title>
	</head>

	<body>
		<article data-role="page">
			<header data-role="header" role="banner" id="header">
				<h1><?php echo $title ?></h1>
<?php if (!$is_dialog) { ?>
				<a href="#Tool" data-icon="gear" class="ui-btn-right" data-rel="popup" data-position-to="window" data-role="button" data-inline="true" data-transition="pop"><?php echo $_LANG['skin']['tool']; ?></a>
				<?php if (!empty($_SKIN['adarea'])) echo '<div class="adarea"></div>'; ?>
<?php } ?>
<?php if (arg_check('read') ){ ?>
				<nav data-role="navbar">
					<ul>
<?php if (exist_plugin_convert('menu') && is_page($menubar)) { ?>
						<li><a href="#MenuBar"><?php echo $_LANG['skin']['menu']; ?></a></li>
<?php } ?>
<?php if (exist_plugin_convert('side') && is_page($sidebar)) { ?>
						<li><a href="#SideBar"><?php echo $_LANG['skin']['side']; ?></a></li>
<?php } ?>
					</ul>
				</nav>
<?php } ?>
			</header>

			<section role="main" data-role="content" data-theme="c">
				<?php echo $body ?>
			</section>

<?php if (arg_check('read') ){ ?>
	<?php if (exist_plugin_convert('menu') && is_page($menubar)) { ?>
			<aside data-role="panel" id="MenuBar" data-theme="b" data-content-theme="d" data-position="left" data-display="reveal">
				<?php echo do_plugin_convert('menu') ?>
			</aside>
	<?php } ?>
	<?php if (exist_plugin_convert('side') && is_page($sidebar)) { ?>
			<aside data-role="panel" id="SideBar" data-theme="b" data-content-theme="d" data-position="right" data-display="reveal">
				<?php echo do_plugin_convert('side') ?>
			</aside>
	<?php } ?>
<?php } ?>
			<aside data-role="popup" id="Tool">
				<div data-role="header" data-theme="a" class="ui-corner-top">
					<h1><?php echo $_LANG['skin']['tool']; ?></h1>
					<a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a>
				</div>
				<div data-role="content" data-theme="d" class="ui-corner-bottom ui-content">
					<?php
					exist_plugin('navibar');
					echo do_plugin_convert('navibar','top,edit,freeze,diff,backup,upload,reload,new,list,search,recent,help,login'); ?>
				</div>
			</aside>
			<footer data-role="footer" role="contentinfo" id="footer">
				<h4>Founded by <a href="<?php echo $modifierlink ?>"><?php echo $modifier ?></a></h4>
				<h5 style="text-align:center; clear:both; font-size:87%;"><?php if (!$is_dialog) { ?>Powered by <a href="http://pukiwiki.logue.be/"><?php echo GENERATOR ?></a>. <?php } ?>HTML convert time: <?php echo showtaketime() ?> sec.</h5>
			</footer><!-- /footer -->
		</article><!-- /page -->
		

		<?php if (!empty($_SKIN['adarea'])) echo '<div id="adarea_content">' . $_SKIN['adarea'] . '</div>'; ?>
		<?php echo $pkwk_tags; ?>
	</body>
</html>
