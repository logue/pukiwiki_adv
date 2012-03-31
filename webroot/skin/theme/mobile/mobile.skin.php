<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: classic.ini.php,v 0.0.2 2012/03/31 16:49:30 Logue Exp $
// 
// PukiWiki Adv. Mobile Theme
// Copyright (C)
//   2012 PukiWiki Advance Developer Team

global $pkwk_dtd, $_SKIN, $is_page, $defaultpage, $do_backup;

if (!defined('DATA_DIR')) { exit; }

if ($newtitle != '' && $is_read) {
	$title = $newtitle;
}

(bool) $is_dialog = isset($vars['cmd']) ? preg_match('/attach|freeze|unfreeze|diff|new|upload|log|search|backup|list/', $vars['cmd']) : false;

// Output HTML DTD, <html>, and receive content-type
$meta_content_type = (isset($pkwk_dtd)) ? pkwk_output_dtd($pkwk_dtd) : pkwk_output_dtd();
?>
	<head>
		<?php echo $meta_content_type; ?>
		<?php echo $pkwk_head; ?>
		<title><?php echo $page_title; ?></title>
	</head>
<?php flush(); ?>
	<body>
		<article data-role="page">
			<header data-role="header" role="banner" id="header">
				<h1><?php echo $title ?></h1>
<?php if (!$is_dialog) { ?>
				<a href="<?php echo $_LINK['search'] ?>" data-icon="search" data-rel="dialog" class="ui-btn-right"><?php echo $_LANG['skin']['search'] ?></a>
<?php	if ($is_page) { ?>
				<nav data-role="navbar">
					<ul>
						<li><a href="<?php echo $_LINK['new'] ?>" data-rel="dialog"><?php echo $_LANG['skin']['new'] ?></a></li>
<?php		if ($is_freeze) { ?>
						<li><a href="<?php echo $_LINK['unfreeze'] ?>" data-rel="dialog"><?php echo $_LANG['skin']['unfreeze'] ?></a></li>
<?php		} else { ?>
<?php			if ($vars['cmd'] !== 'edit') { ?>
						<li><a href="<?php echo $_LINK['edit'] ?>" data-transition="flip" data-rel="page"><?php echo $_LANG['skin']['edit'] ?></a></li>
<?php			} ?>
						<li><a href="<?php echo $_LINK['freeze'] ?>" data-rel="dialog"><?php echo $_LANG['skin']['freeze'] ?></a></li>
<?php		} ?>
						<li><a href="<?php echo $_LINK['diff'] ?>" data-transition="flip"><?php echo $_LANG['skin']['diff'] ?></a></li>
					</ul>
				</nav>
				<?php if (!empty($_SKIN['adarea'])) echo '<div id="adarea"></div>'; ?>
<?php	} ?>
<?php } ?>
			</header>

<?php if (arg_check('read') && exist_plugin_convert('menu')) { ?>
			<div data-role="content" class="two-colums">
				<section class="content-primary" role="main">
					<?php echo $body ?>
				</section>
				<aside class="content-secondary">
					<div data-role="collapsible" data-collapsed="true" data-theme="b" data-content-theme="d">
						<h2>メニュー</h2>
						<?php echo do_plugin_convert('menu') ?>
					</div>
				</aside>
			</div>
<?php }else{ ?>
			<section data-role="content" role="main">
				<?php echo $body ?>
			</section>
<?php } ?>

			<footer data-role="footer" role="contentinfo" id="footer">
<?php if (!$is_dialog) { ?>
				<nav data-role="navbar">
					<ul>
<?php	if ($title != $defaultpage) { ?>
						<li><a href="<?php echo $_LINK['top'] ?>" data-transition="fade"><?php echo $_LANG['skin']['top'] ?></a></li>
<?php	} ?>
						<li><a href="<?php echo $_LINK['list'] ?>" data-transition="flip"><?php echo $_LANG['skin']['list'] ?></a></li>
<?php	if ($is_page && (bool)ini_get('file_uploads')) { ?>
						<li><a href="<?php echo $_LINK['upload'] ?>" data-rel="dialog"><?php echo $_LANG['skin']['upload'] ?></a></li>
<?php	} ?>
<?php if ($do_backup){ ?>
						<li><a href="<?php echo $_LINK['backup'] ?>" data-transition="flip"><?php echo $_LANG['skin']['backup'] ?></a></li>
<?php	} ?>
					</ul>
				</nav>
				<h4>Founded by <a href="<?php echo $modifierlink ?>"><?php echo $modifier ?></a></h4>
<?php } ?>
				<h5 style="text-align:center; clear:both; font-size:87%;"><?php if (!$is_dialog) { ?>Powered by <a href="http://pukiwiki.logue.be/"><?php echo GENERATOR ?></a>. <?php } ?>HTML convert time: <?php echo showtaketime() ?> sec.</h5>
			</footer><!-- /footer -->
		</article><!-- /page -->

		<?php if (!empty($_SKIN['adarea'])) echo '<div id="adarea_content">' . $_SKIN['adarea'] . '</div>'; ?>
		<?php echo $pkwk_tags; ?>
	</body>
</html>
