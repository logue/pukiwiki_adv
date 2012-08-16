<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: classic.skin.php,v 1.6.4 2011/09/11 22:58:00 Logue Exp $

// PukiWiki Classic Skin for PukiPlus
// Copyright (C)
//   2010-2011 PukiWiki Advance Developers Team
//   2005-2007 Logue (LogueWiki Skin)
//   2002-2005 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
global $pkwk_dtd, $_SKIN, $is_page, $defaultpage;

if (!defined('DATA_DIR')) { exit; }

if ($title != $defaultpage) {
	$page_title = $title.' - '.$page_title;
} elseif ($newtitle != '' && $is_read) {
	$page_title = $newtitle.' - '.$page_title;
}
// navibar
$navibar = exist_plugin('suckerfish') ? do_plugin_convert('suckerfish') : null;

// Output HTML DTD, <html>, and receive content-type
$meta_content_type = (isset($pkwk_dtd)) ? pkwk_output_dtd($pkwk_dtd) : pkwk_output_dtd();
?>
	<head>
		<?php echo $meta_content_type; ?>
		<?php echo $pkwk_head; ?>
		<title><?php echo $page_title; ?></title>
	</head>

	<body>
		<div id="base" role="document">
<!-- *** Header *** -->
			<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '<header id="header" class="clearfix" role="banner">'."\n" : '<div id="header" class="clearfix" role="banner">')."\n"; ?>
<?php if (exist_plugin_convert('headarea') && do_plugin_convert('headarea') != '') { ?>
				<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '<hgroup id="hgroup" style="display:none;">'."\n" : '<div id="hgroup" style="display:none;">')."\n"; ?>
					<h1 id="title"><?php echo(($newtitle!='' && $is_read) ? $newtitle: $page) ?></h1>
					<h2><a href="<?php echo $_LINK['reload'] ?>" id="parmalink"><?php echo $_LINK['reload'] ?></a></h2>
				<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '</hgroup>'."\n" : '</div>')."\n"; ?>
				<?php echo do_plugin_convert('headarea') ?>
<?php } else { ?>
				<a href="<?php echo $_LINK['top'] ?>"><img id="logo" src="<?php echo $_SKIN['logo']['src'] ?>" width="<?php echo $_SKIN['logo']['width'] ?>" height="<?php echo $_SKIN['logo']['height'] ?>" alt="<?php echo $_SKIN['logo']['alt'] ?>" /></a>
				<div id="hgroup">
					<h1 id="title"><?php echo (($newtitle!='' && $is_read) ? $newtitle : $page) ?></h1>
					<?php  echo exist_plugin('topicpath') ? do_plugin_convert('topicpath') : ''; ?>
				</div>
<!-- * Ad space *-->
				<?php if ($_SKIN['adarea']['header']) echo '<div id="ad" class="noprint">' . $_SKIN['adarea']['header'] . '</div>'; ?>
<!-- * End Ad space * -->
				<?php echo (!empty($lastmodified)) ? '<div id="lastmodified">Last-modified: '.$lastmodified.'</div>'."\n" : '' ?>
<?php } ?>
			<?php echo (($pkwk_dtd === PKWK_DTD_HTML_5) ? '</header>' : '</div>')."\n"; ?>
<!-- *** End Header *** -->
			<?php echo ($navibar === null) ? (exist_plugin('navibar') ? do_plugin_convert('navibar','top,|,edit,freeze,diff,backup,upload,reload,|,new,list,search,recent,help,|,login').'<hr />' :'') : $navibar; ?>
<!-- ** Body ** -->
<?php if (arg_check('read') && exist_plugin_convert('menu')) { ?>
			<div class="clearfix">
				<div id="content" class="body">
<?php }else{ ?>
				<div class="body">
<?php } ?>
				<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<section id="body" role="main">'."\n" : '<div id="body" role="main">'."\n"; ?>
					<?php echo $body ?>
				<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</section>'."\n" : '</div>'."\n"; ?>

<?php if (!empty($notes)) { ?>
				<?php echo $hr ?>
<!-- * Note * -->
				<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<aside id="note" role="note">'."\n" : '<div id="note" role="note">'."\n"; ?>
						<?php echo $notes ?>
				<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</aside>'."\n" : '</div>'."\n"; ?>
<!--  End Note -->
<?php } ?>
<?php if (!empty($attaches)) { ?>
				<?php echo $hr ?>
<!-- * Attach * -->
				<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<aside id="attach" class="clearfix">'."\n" : '<div id="attach">'."\n"; ?>
					<?php echo $attaches ?>
				<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</aside>'."\n" : '</div>'."\n"; ?>
<!--  End Attach -->
<?php } ?>
<?php if (!empty($related)) { ?>
					<?php echo $hr ?>
<!-- * Related * -->
					<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<aside id="related" class="clearfix">'."\n" : '<div id="related">'."\n"; ?>
						<?php echo $related ?>
					<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</aside>'."\n" : '</div>'."\n"; ?>
<!--  End Related -->
<?php } ?>
<!-- * Ad space * -->
					<?php if (!empty($_SKIN['adarea']['footer'])) echo '<div id="footer_adspace" class="noprint" style="text-align:center;">' . $_SKIN['adarea']['footer'] . '</div>'; ?>
<!-- * End Ad space * -->
				</div>
<!-- ** End Body ** -->
<?php if (arg_check('read') && exist_plugin_convert('menu')) { ?>
<!-- ** MenuBar ** -->
				<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<aside id="menubar">'."\n" : '<div id="menubar">'."\n"; ?>
					<?php echo do_plugin_convert('menu') ?>
				<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</aside>'."\n" : '</div>'."\n"; ?>
<!-- ** End MenuBar ** -->
			</div>
<?php } ?>
			<hr />
			<?php echo exist_plugin('toolbar') ? do_plugin_convert('toolbar','reload,|,new,newsub,edit,freeze,source,diff,upload,copy,rename,|,top,list,search,recent,backup,referer,log,|,help,|,rss') : '';?>
<!-- *** Footer *** -->
			<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<footer id="footer" role="contentinfo">'."\n" : '<div id="footer" role="contentinfo">'."\n"; ?>
				<div id="qr_code">
					<?php echo exist_plugin_inline('qrcode') ? plugin_qrcode_inline(1,$_LINK['reload']) : ''; ?>
				</div>
				<address>Founded by <a href="<?php echo $modifierlink ?>"><?php echo $modifier ?></a></address>
				<div id="sigunature">
					<?php echo S_COPYRIGHT ?><br />
					HTML convert time: <?php echo showtaketime() ?> sec.
				</div>
				<div id="banner_box">
					<a href="http://pukiwiki.logue.be/"><img src="<?php echo IMAGE_URI; ?>pukiwiki_adv.banner.png" width="88" height="31" alt="PukiWiki Advance" title="PukiWiki Advance" /></a>
<?php if(! isset($pkwk_dtd) || $pkwk_dtd == PKWK_DTD_HTML_5){ ?>
					<a href="http://validator.w3.org/check/referer"><img src="<?php echo IMAGE_URI ?>html5.png" width="88" height="31" alt="HTML5" title="HTML5" /></a>
<?php }else if ( $pkwk_dtd == PKWK_DTD_XHTML_1_1) { ?>
					<a href="http://validator.w3.org/check/referer"><img src="http://www.w3.org/Icons/valid-xhtml11-blue.png" width="88" height="31" alt="Valid XHTML 1.1" title="Valid XHTML 1.1" /></a>
<?php } ?>
				</div>
			<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</footer>'."\n" : '</div>'."\n"; ?>
<!-- *** End Footer *** -->
		</div>
		<?php echo $pkwk_tags; ?>
	</body>
</html>
