<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: classic.ini.php,v 0.0.1 2012/03/04 12:32:30 Logue Exp $
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
			<header data-role="header" role="banner">
				<h1><?php echo $title ?></h1>
<?php if (!$is_dialog) { ?>
				<a href="<?php echo $_LINK['search'] ?>" data-icon="search" data-rel="dialog" class="ui-btn-right"><?php echo $_LANG['skin']['search'] ?></a>
<?php	if ($is_page) { ?>
				<nav data-role="navbar" data-iconpos="left">
					<ul>
<?php	if ($title != $defaultpage) { ?>
						<li><a href="<?php echo $_LINK['top'] ?>" data-transition="fade" data-icon="home"><?php echo $_LANG['skin']['top'] ?></a></li>
<?php	} ?>
						
<?php		if ($is_freeze) { ?>
						<li><a href="<?php echo $_LINK['unfreeze'] ?>" data-rel="dialog"><?php echo $_LANG['skin']['unfreeze'] ?></a></li>
<?php		} else { ?>
<?php			if ($vars['cmd'] !== 'edit') { ?>
						<li><a href="<?php echo $_LINK['edit'] ?>" data-transition="flip" data-rel="page" data-icon="gear"><?php echo $_LANG['skin']['edit'] ?></a></li>
<?php			} ?>
						<li><a href="<?php echo $_LINK['freeze'] ?>" data-rel="dialog"><?php echo $_LANG['skin']['freeze'] ?></a></li>
<?php		} ?>
						<li><a href="<?php echo $_LINK['diff'] ?>" data-transition="flip"><?php echo $_LANG['skin']['diff'] ?></a></li>
						<li><a href="<?php echo $_LINK['list'] ?>" data-transition="flip" data-icon="star"><?php echo $_LANG['skin']['list'] ?></a></li>
					</ul>
				</nav>
<?php	} ?>
<?php } ?>
			</header>

<?php if (arg_check('read') && exist_plugin_convert('menu')) { ?>
			<div data-role="content" class="two-colums">
				<section class="content-primary" role="main">
					<?php echo $body ?>
				</section>
				<aside class="content-secondary">
					<div class="ui-collapsible ui-collapsible-collapsed" data-theme="b" data-collapsed="true" data-role="collapsible">
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

			<footer data-role="footer" class="footer-docs" role="contentinfo">
<?php if (!$is_dialog) { ?>
				<nav data-role="navbar" data-iconpos="left">
					<ul>
						<li><a href="<?php echo $_LINK['new'] ?>" data-rel="dialog" data-icon="plus"><?php echo $_LANG['skin']['new'] ?></a></li>
<?php	if ($is_page && (bool)ini_get('file_uploads')) { ?>
						<li><a href="<?php echo $_LINK['upload'] ?>" data-rel="dialog" data-icon="gear"><?php echo $_LANG['skin']['upload'] ?></a></li>
<?php	} ?>
<?php if ($do_backup){ ?>
						<li><a href="<?php echo $_LINK['backup'] ?>" data-transition="flip" data-icon="back"><?php echo $_LANG['skin']['backup'] ?></a></li>
<?php	} ?>
						<li><a href="<?php echo $_LINK['recent'] ?>" data-transition="flip"><?php echo $_LANG['skin']['recent'] ?></a></li>
						<!--li><a href="<?php echo $_LINK['log'] ?>" data-rel="dialog" data-icon="grid"><?php echo $_LANG['skin']['log'] ?></a></li-->
					</ul>
				</nav>
				<h4>Founded by <a href="<?php echo $modifierlink ?>"><?php echo $modifier ?></a></h4>
<?php } ?>
				<h5 style="text-align:center; clear:both; font-size:87%;"><?php if (!$is_dialog) { ?>Powered by <a href="http://pukiwiki.logue.be/"><?php echo GENERATOR ?></a>. <?php } ?>HTML convert time: <?php echo showtaketime() ?> sec.</h5>
			</footer><!-- /footer -->
		</article><!-- /page -->

		<?php echo $pkwk_tags; ?>
	</body>
</html>
