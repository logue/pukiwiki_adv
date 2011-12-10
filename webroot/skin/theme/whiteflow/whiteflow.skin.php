<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: whiteflow.css.php,v 1.0.0 2011/12/10 11:38:30 Logue Exp $

// White flow Adv. skin.
// ver 1.0 (2011/12/10) 
// by Logue (http://logue.be/)

// based on 
// White flow (http://note.openvista.jp/2007/pukiwiki-skin/)
// by leva(http://www.geckdev.org)

// License is The MIT/X11 License (http://www.opensource.org/licenses/mit-license.php)
global $pkwk_dtd, $_SKIN, $is_page, $defaultpage;

if (!defined('DATA_DIR')) { exit; }

if ($title != $defaultpage) {
	$page_title = $title.' - '.$page_title;
} elseif ($newtitle != '' && $is_read) {
	$page_title = $newtitle.' - '.$page_title;
}

$layout_class = arg_check('read') ? 'work' : 'display';

// Output HTML DTD, <html>, and receive content-type
$meta_content_type = (isset($pkwk_dtd)) ? pkwk_output_dtd($pkwk_dtd) : pkwk_output_dtd();
?>
	<head>
		<?php echo $meta_content_type; ?>
		<?php echo $pkwk_head; ?>
		<title><?php echo $page_title; ?></title>
	</head>

	<body>
<!-- START #containar-->
		<div id="wide-container">
<!-- * Ad space *-->
			<?php if ($_SKIN['adarea']['header']) echo '<div id="header_ad" class="noprint">' . $_SKIN['adarea']['header'] . '</div>'; ?>
<!-- * End Ad space * -->
			<?php echo '<div id="container" class="'. $layout_class .'">'; ?>
<!-- START #header -->
			<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '<header id="header">'."\n" : '<div id="header">')."\n"; ?>
<?php if (exist_plugin_convert('headarea') && do_plugin_convert('headarea') != '') { ?>
				<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '<hgroup id="hgroup" style="display:none;">'."\n" : '<div id="hgroup" style="display:none;">')."\n"; ?>
					<h1 id="title"><?php echo(($newtitle!='' && $is_read) ? $newtitle: $page) ?></h1>
				<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '</hgroup>'."\n" : '</div>')."\n"; ?>
				<?php echo do_plugin_convert('headarea') ?>
<?php } else { ?>
				<a href="<?php echo $modifierlink ?>" id="logo"><img src="<?php echo $_SKIN['logo']['src'] ?>" width="<?php echo $_SKIN['logo']['width'] ?>" height="<?php echo $_SKIN['logo']['height'] ?>" alt="<?php echo $_SKIN['logo']['alt'] ?>" /></a>
				<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '<hgroup id="hgroup">'."\n" : '<div id="hgroup">')."\n"; ?>
					<h1 id="title"><?php echo (($newtitle!='' && $is_read) ? $newtitle : $page) ?></h1>
					<h2 id="description">PukiWiki - Yet another WikiWikiWeb clone.</h2>
				<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '</hgroup>'."\n" : '</div>')."\n"; ?>
<?php } ?>
			<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '</header>' : '</div>')."\n"; ?>
<!-- END #header -->

			<div id="additional" class="clearfix">
<?php if (arg_check('read')){ ?>
			<?php echo ($is_page && exist_plugin_convert('topicpath')) ? do_plugin_convert('topicpath') : ''; ?>
			<?php echo (!empty($lastmodified)) ? '<div id="lastmodified">'.$lastmodified.'</div>'."\n" : '' ?>
<?php }else if ($is_page){ ?>
			<nav id="topicpath"><a href="<?php echo $_LINK['reload'] ?>"><?php echo $_LANG['skin']['reload'] ?></a></nav>
<?php }else{ ?>
			<nav id="topicpath"><a href="<?php echo $_LINK['top'] ?>"><?php echo $_LANG['skin']['top'] ?></a></nav>
<?php } ?>
			</div>

<!-- START #content -->
			<div id="content" class="clearfix">
<!-- START #content > #edit-area -->
				<div id="edit-area" class="<? echo $layout_class; ?>">
					<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<section id="body">'."\n" : '<div id="body">'."\n"; ?>
						<?php echo $body."\n" ?>
					<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</section>'."\n" : '</div>'."\n"; ?>

					<div id="misc" class="display">
<?php if (!empty($notes)) { ?>
						<hr />
<!-- * Note * -->
						<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<aside id="note">'."\n" : '<div id="note">'."\n"; ?>
							<?php echo $notes ?>
						<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</aside>'."\n" : '</div>'."\n"; ?>
<!--  End Note -->
<?php } ?>
<?php if (!empty($attaches)) { ?>
						<?php echo $hr ?>
<!-- * Attach * -->
						<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<aside id="attach">'."\n" : '<div id="attach">'."\n"; ?>
							<?php echo $attaches ?>
						<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</aside>'."\n" : '</div>'."\n"; ?>
<!--  End Attach -->
<?php } ?>

<?php if (!empty($related)) { ?>
						<?php echo $hr ?>
<!-- * Related * -->
						<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<aside id="related">'."\n" : '<div id="related">'."\n"; ?>
							<?php echo $related ?>
						<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</aside>'."\n" : '</div>'."\n"; ?>
<!--  End Related -->
<?php } ?>
					</div>
				</div>
<!-- END #content > #edit-area -->

<?php if (arg_check('read')){ ?>
<!-- START #content > #menu -->
				<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<aside id="sidebar" class="clearfix">'."\n" : '<div id="sidebar" class="clearfix">'."\n"; ?>
					<div id="page-menu" class="clearfix">
<!-- ■BEGIN id:page_action -->
						<h3><?php echo $_LANG['skin']['edit'] ?></h3>
						<ul class="sf-menu sf-vertical">
<?php if ($is_page) { ?>
							<li><a href="<?php echo $_LINK['top'] ?>"><span class="pkwk-icon icon-top"></span><?php echo $_LANG['skin']['top'] ?></a></li>
							<li><a href="<?php echo $_LINK['reload'] ?>"><span class="pkwk-icon icon-reload"></span><?php echo $_LANG['skin']['reload'] ?></a></li>
							<li><a href="<?php echo $_LINK['new'] ?>"><span class="pkwk-icon icon-new"></span><?php echo $_LANG['skin']['new'] ?></a>
								<ul>
									<li><a href="<?php echo $_LINK['newsub'] ?>"><span class="pkwk-icon icon-newsub"></span><?php echo $_LANG['skin']['newsub'] ?></a></li>
								</ul>
							</li>
							<li><a href="<?php echo $_LINK['edit'] ?>"><span class="pkwk-icon icon-edit"></span><?php echo $_LANG['skin']['edit'] ?></a>
								<ul>
<?php   if ($is_read and $function_freeze) { ?>
<?php     if ($is_freeze) { ?>
									<li><a href="<?php echo $_LINK['unfreeze'] ?>"><span class="pkwk-icon icon-unfreeze"></span><?php echo $_LANG['skin']['unfreeze'] ?></a></li>
<?php     } else { ?>
									<li><a href="<?php echo $_LINK['freeze'] ?>"><span class="pkwk-icon icon-freeze"></span><?php echo $_LANG['skin']['freeze'] ?></a></li>
<?php     } ?>
<?php   } ?>
<?php   if ((bool)ini_get('file_uploads')) { ?>
									<li><a href="<?php echo $_LINK['upload'] ?>"><span class="pkwk-icon icon-upload"></span><?php echo $_LANG['skin']['upload'] ?></a></li>
<?php   } ?>
									<li><a href="<?php echo $_LINK['source'] ?>"><span class="pkwk-icon icon-source"></span><?php echo $_LANG['skin']['source'] ?></a></li>
									<li><a href="<?php echo $_LINK['diff'] ?>"><span class="pkwk-icon icon-diff"></span><?php echo $_LANG['skin']['diff'] ?></a></li>
<?php if ($do_backup) { ?>
									<li><a href="<?php echo $_LINK['backup'] ?>"><span class="pkwk-icon icon-backup"></span><?php echo $_LANG['skin']['backup'] ?></a></li>
<?php } ?>
								</ul>
							</li>
<?php } ?>
							<li><a href="<?php echo $_LINK['search'] ?>"><span class="pkwk-icon icon-search"></span><?php echo $_LANG['skin']['search'] ?></a></li>
							<li><a href="<?php echo $_LINK['list'] ?>"><span class="pkwk-icon icon-list"></span><?php echo $_LANG['skin']['list'] ?></a>
								<ul>
<?php if (arg_check('list')) { ?>
									<li><a href="<?php echo $_LINK['filelist'] ?>"><span class="pkwk-icon icon-filelist"></span><?php echo $_LANG['skin']['filelist'] ?></a></li>
<?php } ?>
									<li><a href="<?php echo $_LINK['recent'] ?>"><span class="pkwk-icon icon-recent"></span><?php echo $_LANG['skin']['recent'] ?></a></li>
									<li><a href="<?php echo $_LINK['referer'] ?>"><span class="pkwk-icon icon-referer"></span><?php echo $_LANG['skin']['referer'] ?></a></li>
									<li><a href="<?php echo $_LINK['skeylist'] ?>"><span class="pkwk-icon icon-skeylist"></span><?php echo $_LANG['skin']['skeylist'] ?></a></li>
									<li><a href="<?php echo $_LINK['linklist'] ?>"><span class="pkwk-icon icon-linklist"></span><?php echo $_LANG['skin']['linklist'] ?></a></li>
								</ul>
							</li>
						</ul>
					</div>

<?php if (exist_plugin_convert('menu')){ ?>
					<hr />
					<div id="menubar">
						<?php echo do_plugin_convert('menu'); ?>
					</div>
<?php } ?>
				<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</aside>'."\n" : '</div>'."\n"; ?>
<!-- END #content > #menu -->
			</div>
<!-- END #content -->
<?php } ?>

<!-- START #footer -->
			<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<footer id="footer">'."\n" : '<div id="footer">'."\n"; ?>
				<?php if (exist_plugin_convert('footarea') && do_plugin_convert('footarea') != ''){
					echo do_plugin_convert('footarea');
				}else { // or In this skin?>
						<ul id="signature">
							<li><address>Site admin: <a href="<?php echo $modifierlink ?>"><?php echo $modifier ?></a></address></li>
							<li><strong>White flow Adv.</strong> based on <a href="http://note.openvista.jp/" rel="external">leva</a>'s <a href="http://note.openvista.jp/2007/pukiwiki-skin/" rel="external"><strong>White flow</strong></a>.</li>
							<li><?php echo S_COPYRIGHT;?></li>
							<li>HTML convert time: <?php echo showtaketime() ?> sec.</li>
						</ul>
					<?php } ?>
				</div>
<!-- END #footer -->
			<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</footer>'."\n" : '</div>'."\n"; ?>
<!-- #END #container -->
		</div>
<!-- * Ad space * -->
		<?php if ($_SKIN['adarea']['footer']) echo '<div id="footer_ad" class="noprint">' . $_SKIN['adarea']['footer'] . '</div>'; ?>
<!-- * End Ad space * -->
		<?php echo $pkwk_tags; ?>
	</body>
</html>
