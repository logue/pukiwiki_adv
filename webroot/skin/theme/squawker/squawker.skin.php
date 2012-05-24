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
global $pkwk_dtd, $_SKIN, $is_page, $defaultpage, $sidebar, $headarea, $footarea;

// Initialize
if (!defined('DATA_DIR')) { exit; }

$site_name = $page_title;
if ($title != $defaultpage) {
	$page_title = $title.' - '.$page_title;
} elseif ($newtitle != '' && $is_read) {
	$page_title = $newtitle.' - '.$page_title;
}

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
$footer = (is_page($footarea) && exist_plugin_convert('footarea')) ? do_plugin_convert('footarea') : '';

// navibar
$navibar = exist_plugin('suckerfish') ? do_plugin_convert('suckerfish') : null;

// start

// Output HTML DTD, <html>, and receive content-type
$meta_content_type = (isset($pkwk_dtd)) ? pkwk_output_dtd($pkwk_dtd) : pkwk_output_dtd();
?>
	<head>
		<?php echo $meta_content_type; ?>
		<?php echo $pkwk_head; ?>
		<title><?php echo $page_title; ?></title>
<style type="text/css">/* <[CDATA[ */
@media (min-width: 980px) {
body {
	padding-top: 60px;
	padding-bottom: 40px;
}
.sidebar-nav {
	padding: 9px 0;
}
}
/* ]]> */</style>
	</head>
	<body>
		<header class="navbar navbar-fixed-top" role="banner">
			<div class="navbar-inner">
				<div class="container">
					<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</a>
					<a class="brand" href="<?php echo $_LINK['top'] ?>"><?php echo $site_name ?></a>
					<div class="nav-collapse">
						<ul class="nav">
							<li><a href="<?php echo $_LINK['reload'] ?>"><?php echo $_LANG['skin']['reload'] ?></a></li>
<?php if ($is_page) { ?>
							<li class="dropdown">
								<a data-toggle="dropdown" href="#"><?php echo $_LANG['skin']['new'] ?><b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="<?php echo $_LINK['new'] ?>"><?php echo $_LANG['skin']['new'] ?></a></li>
									<li><a href="<?php echo $_LINK['newsub'] ?>"><?php echo $_LANG['skin']['newsub'] ?></a></li>
									<li><a href="<?php echo $_LINK['rename'] ?>"><?php echo $_LANG['skin']['rename'] ?></a></li>
								</ul>
							</li>
							<li class="dropdown">
								<a  data-toggle="dropdown" href="#"><?php echo $_LANG['skin']['edit'] ?><b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="<?php echo $_LINK['edit'] ?>"><?php echo $_LANG['skin']['edit'] ?></a></li>
<?php   if ($is_read and $function_freeze) { ?>
<?php     if ($is_freeze) { ?>
									<li><a href="<?php echo $_LINK['unfreeze'] ?>"><?php echo $_LANG['skin']['unfreeze'] ?></a></li>
<?php     } else { ?>
									<li><a href="<?php echo $_LINK['freeze'] ?>"><?php echo $_LANG['skin']['freeze'] ?></a></li>
<?php     } ?>
<?php   } ?>
<?php   if ((bool)ini_get('file_uploads')) { ?>
									<li><a href="<?php echo $_LINK['upload'] ?>"><?php echo $_LANG['skin']['upload'] ?></a></li>
<?php   } ?>
									<li><a href="<?php echo $_LINK['source'] ?>"><?php echo $_LANG['skin']['source'] ?></a></li>
									<li><a href="<?php echo $_LINK['diff'] ?>"><?php echo $_LANG['skin']['diff'] ?></a></li>
<?php if ($do_backup) { ?>
									<li><a href="<?php echo $_LINK['backup'] ?>"><?php echo $_LANG['skin']['backup'] ?></a></li>
<?php } ?>
								</ul>
							</li>
<?php } ?>
							<li class="dropdown">
								<a href="#" data-toggle="dropdown"><?php echo $_LANG['skin']['list'] ?> <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="<?php echo $_LINK['list'] ?>"><?php echo $_LANG['skin']['list'] ?></a></li>
									<li><a href="<?php echo $_LINK['recent'] ?>"><?php echo $_LANG['skin']['recent'] ?></a></li>
									<li><a href="<?php echo $_LINK['referer'] ?>"><?php echo $_LANG['skin']['referer'] ?></a></li>
									<li><a href="<?php echo $_LINK['skeylist'] ?>"><?php echo $_LANG['skin']['skeylist'] ?></a></li>
									<li><a href="<?php echo $_LINK['linklist'] ?>"><?php echo $_LANG['skin']['linklist'] ?></a></li>
									<li><a href="<?php echo $_LINK['log'] ?>"><?php echo $_LANG['skin']['log'] ?></a></li>
								</ul>
							</li>
							<li><a href="<?php echo $_LINK['login'] ?>"><?php echo $_LANG['skin']['login'] ?></a></li>
						</ul>
						<form class="navbar-search left" action="<?php echo get_script_uri(); ?>">
							<input type="hidden" name="cmd" value="search">
							<input type="text" class="search-query span2" placeholder="<?php echo $_LANG['skin']['search'] ?>">
						</form>
					</div><!-- /.nav-collapse -->
				</div>
			</div><!-- /navbar-inner -->
		</header><!-- /navbar -->

<!-- Center -->
		<div class="container-fluid">
			<div class="row-fluid">
<?php if ($layout_class == 'two-colums')  { ?>
				<aside class="span3">
					<div class="well sidebar-nav">
						<?php echo preg_replace('/<ul/','<ul class="nav nav-list"', do_plugin_convert('menu'))."\n" ?>
					</div>
				</aside>
<?php } ?>
				<article class="<?php echo ($layout_class == 'two-colums') ? 'span9' : '' ?>">
					<hgroup>
						<h1><?php echo $title ?></h1>
						<?php echo (!empty($lastmodified)) ? '<div id="lastmodified">Last-modified: '.$lastmodified.'</div>'."\n" : '' ?>
					</hgroup>
					<hr />
					<div id="main" role="main">
						<?php echo ($is_page && exist_plugin_convert('topicpath')) ? do_plugin_convert('topicpath') : ''; ?>
						<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<section id="body">'."\n" : '<div id="body">'."\n"; ?>
									<?php echo $body."\n" ?>
						<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</section>'."\n" : '</div>'."\n"; ?>
		<?php if (!empty($notes)) { ?>
						<hr />
						<!-- * Note * -->
						<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '<aside id="note" role="note">'."\n" : '<div id="note" role="note">'."\n"; ?>
									<?php echo $notes."\n" ?>
						<?php echo ($pkwk_dtd === PKWK_DTD_HTML_5) ? '</aside>'."\n" : '</div>'."\n"; ?>
						<!--  End Note -->
		<?php } ?>
						<?php if (!empty($_SKIN['adarea']['footer'])) echo '<div id="footer_adspace" class="noprint" style="text-align:center;">' . $_SKIN['adarea']['footer'] . '</div>'; ?>
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
						<aside id="related" role="navigation">
							<?php echo $related ?>
						</aside>
						<!--  End Related -->
<?php } ?>
					</div>
				</div>
			</article>
			
			<hr />
			<?php echo exist_plugin('toolbar') ? do_plugin_convert('toolbar','reload,|,new,newsub,edit,freeze,source,diff,upload,copy,rename,|,top,list,search,recent,backup,referer,log,|,help,|,rss') : '';?>

			<footer id="footer" class="clearfix" role="contentinfo">
				<div id="qr_code"><?php echo exist_plugin_inline('qrcode') ? plugin_qrcode_inline(1,$_LINK['reload']) : ''; ?></div>
				<address>Founded by <a href="<?php echo $modifierlink ?>"><?php echo $modifier ?></a></address>
				<div id="sigunature">
					Powered by <a href="http://pukiwiki.logue.be/" rel="product"><?php echo GENERATOR ?></a>. HTML convert time: <?php echo showtaketime() ?> sec. <br />
					Original Theme Design by <a href="http://pukiwiki.cafelounge.net/plus/">PukiWiki Plus!</a> Team.
				</div>
				<div id="banner_box">
					<a href="http://pukiwiki.logue.be/"><img src="<?php echo IMAGE_URI; ?>pukiwiki_adv.banner.png" width="88" height="31" alt="PukiWiki Advance" title="PukiWiki Advance" /></a>
					<a href="http://validator.w3.org/check/referer"><img src="<?php echo IMAGE_URI ?>html5.png" width="88" height="31" alt="HTML 5" title="HTML5" /></a>
				</div>
			</footer>
		<?php echo $pkwk_tags; ?>
	</body>
</html>
