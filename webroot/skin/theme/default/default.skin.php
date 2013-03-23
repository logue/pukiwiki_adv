<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// PukiWiki Plus! skin for PukiWiki Advance.
// Original version by miko and upk.
// Modified by Logue
//
// $Id: default.skin.php,v 1.4.18 2012/05/03 21:35:00 Logue Exp $
//
global $pkwk_head, $newtitle, $page_title,$_LINK, $is_page, $defaultpage, $sidebar, $headarea, $footarea;

// Initialize
if (!defined('DATA_DIR')) { exit; }

// モードによって、3カラム、2カラムを切り替える。
if (arg_check('read') && exist_plugin_convert('menu')) {
	$layout_class = (arg_check('read') && exist_plugin_convert('side') && is_page($sidebar) ? 'three-colums' : 'two-colums');
}else{
	$layout_class = '';
}

// Header and Footer
$title_style = '';
$header = '';
if (is_page($headarea) && exist_plugin_convert('headarea')){
	$header = do_plugin_convert('headarea');
	$title_style = "display:none;";
}
$footer = is_page($footarea) ? do_plugin_convert('footarea') : '';

// navibar
$navibar = do_plugin_convert('suckerfish');
if (empty($navibar)) $navibar = do_plugin_convert('navibar','top,|,edit,freeze,diff,backup,upload,reload,|,new,list,search,recent,help,|,login'). '<hr />';
// start
// Output HTML DTD, <html>, and receive content-type
?>
<!doctype html>
<head prefix="og: http://ogp.me/ns# fb: http://www.facebook.com/2008/fbml">
<?php echo $this->head; ?>
<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/<?php echo JQUERY_UI_VER; ?>/themes/redmond/jquery-ui.min.css" />
<link rel="stylesheet" type="text/css" href="<?php echo SKIN_URI . 'scripts.css.php?base=' . urlencode(IMAGE_URI) ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo SKIN_URI . THEME_PLUS_NAME . PLUS_THEME . '/'. PLUS_THEME . '.css.php'; ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo SKIN_URI . THEME_PLUS_NAME . PLUS_THEME . '/blue.css'; ?>"id="coloring" />
<title><?php echo $this->title . ' - ' . $this->page_title; ?></title>
</head>
	<body>
		<div id="container" class="<?php echo $layout_class ?>" role="document">
<!-- *** Header *** -->
			<header id="header" role="banner">
				<a href="<?php echo $_LINK['top'] ?>"  style="<?php echo $title_style; ?>"><img id="logo" src="<?php echo $this->conf['logo']['src'] ?>" width="<?php echo $this->conf['logo']['width'] ?>" height="<?php echo $this->conf['logo']['height'] ?>" alt="<?php echo $this->conf['logo']['alt'] ?>" /></a>
				<hgroup id="hgroup" style="<?php echo $title_style ?>">
					<h1 id="title"><?php echo $this->title ?></h1>
					<h2><a href="<?php echo $_LINK['reload'] ?>" id="parmalink"><?php echo $_LINK['reload'] ?></a></h2>
				</hgroup>
				<?php if ($this->conf['adarea']['header'] && !isset($header)) echo '<div id="ad" class="noprint">' . $this->conf['adarea']['header'] . '</div>'; ?>
				<?php //if ($this->header) echo $this->header; ?>
<!-- * Ad space *-->
				<?php if ($this->conf['adarea']['header']) echo '<div id="ad" class="noprint">' . $this->conf['adarea']['header'] . '</div>'; ?>
<!-- * End Ad space * -->
				<?php echo (!empty($this->lastmodified)) ? '<div id="lastmodified">Last-modified: '.$this->lastmodified.'</div>'."\n" : '' ?>
			</header>
<!-- *** End Header *** -->

			<?php echo $navibar; ?>
			<div id="wrapper" class="clearfix">
<!-- Center -->
				<div id="main_wrapper">
					<div id="main" role="main">
						<?php echo $this->is_page ? do_plugin_convert('topicpath') : ''; ?>
						<section id="body">
							<?php echo $this->body."\n" ?>
						</section>
<?php if (!empty($notes)) { ?>
						<hr />
						<!-- * Note * -->
						<aside id="note" role="note">
							<?php echo $this->notes."\n" ?>
						</aside>
						<!--  End Note -->
<?php } ?>
						<?php if (!empty($this->conf['adarea']['footer'])) echo '<div id="footer_adspace" class="noprint" style="text-align:center;">' . $this->conf['adarea']['footer'] . '</div>'; ?>
					</div>
				</div>

<?php if ($layout_class == 'three-colums' || $layout_class == 'two-colums')  { ?>
<!-- Left -->
				<aside id="menubar" role="navigation">
					<?php echo do_plugin_convert('menu')."\n" ?>
				</aside>
<?php } ?>

<?php if ($layout_class == 'three-colums')  { ?>
<!-- Right -->
				<aside id="sidebar" role="navigation">
					<?php echo do_plugin_convert('side')."\n" ?>
				</aside>
<?php } ?>
			</div>
			<div id="misc">
<?php if (!empty($attaches)) { ?>
				<hr />
				<!-- * Attach * -->
				<aside id="attach">
					<?php echo $attaches ?>
				</aside>
				<!--  End Attach -->
<?php } ?>
<?php if (!empty($related)) { ?>
				<!-- * Related * -->
				<hr />
				<aside id="related">
					<?php echo $related ?>
				</aside>
				<!--  End Related -->
<?php } ?>
				<hr />
				<?php echo exist_plugin('toolbar') ? do_plugin_convert('toolbar','reload,|,new,newsub,edit,freeze,source,diff,upload,copy,rename,|,top,list,search,recent,backup,referer,log,|,help,|,rss') : '';?>
			</div>

			<footer id="footer" class="clearfix" role="contentinfo">
<?php if ($footer) {
echo $footer;
}else{ ?>
				<div id="qr_code">
					<?php echo exist_plugin_inline('qrcode') ? plugin_qrcode_inline(1,$_LINK['reload']) : ''; ?>
				</div>
				<address>Founded by <a href="<?php echo $this->modifierlink ?>"><?php echo $this->modifier ?></a></address>
				<div id="sigunature">
					Powered by <a href="http://pukiwiki.logue.be/" rel="external"><?php echo GENERATOR ?></a>. HTML convert time: <?php echo showtaketime() ?> sec. <br />
					Original Theme Design by <a href="http://pukiwiki.cafelounge.net/plus/" rel="external">PukiWiki Plus!</a> Team.
				</div>
				<div id="banner_box">
					<a href="http://pukiwiki.logue.be/" rel="external"><img src="<?php echo IMAGE_URI; ?>pukiwiki_adv.banner.png" width="88" height="31" alt="PukiWiki Advance" title="PukiWiki Advance" /></a>
					<a href="http://validator.w3.org/check/referer" rel="external"><img src="<?php echo IMAGE_URI ?>html5.png" width="88" height="31" alt="HTML 5" title="HTML5" /></a>
				</div>
<?php } ?>
			</footer>
		</div>
		<?php echo $this->js; ?>
	</body>
</html>
