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

$body_menu = do_plugin_convert('menu');
$body_side = do_plugin_convert('side');

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
	</head>
	<body>
		<div class="navbar navbar-inverse navbar-fixed-top" role="banner">
			<div class="navbar-inner">
				<div class="container-fluid">
					<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse" href="#">
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
								<a data-toggle="dropdown" href="#"><?php echo $_LANG['skin']['site'] ?><b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="<?php echo $_LINK['top'] ?>"><i class="icon-home"></i><?php echo $_LANG['skin']['top'] ?></a></li>
									<li><a href="<?php echo $_LINK['new'] ?>"><i class="icon-plus"></i><?php echo $_LANG['skin']['new'] ?></a></li>
								</ul>
							</li>
							<li class="dropdown">
								<a  data-toggle="dropdown" href="#"><?php echo $_LANG['skin']['page'] ?><b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="<?php echo $_LINK['edit'] ?>"><i class="icon-pencil"></i><?php echo $_LANG['skin']['edit'] ?></a></li>
<?php   if ($is_read and $function_freeze) { ?>
<?php     if ($is_freeze) { ?>
									<li><a href="<?php echo $_LINK['unfreeze'] ?>"><i class="icon-wrench"></i><?php echo $_LANG['skin']['unfreeze'] ?></a></li>
<?php     } else { ?>
									<li><a href="<?php echo $_LINK['freeze'] ?>"><i class="icon-lock"></i><?php echo $_LANG['skin']['freeze'] ?></a></li>
<?php     } ?>
									<li><a href="<?php echo $_LINK['diff'] ?>"><i class="icon-th-list"></i><?php echo $_LANG['skin']['diff'] ?></a></li>
									<li><a href="<?php echo $_LINK['copy'] ?>"><i class="icon-tags"></i><?php echo $_LANG['skin']['copy'] ?></a></li>
									<li><a href="<?php echo $_LINK['rename'] ?>"><i class="icon-tag"></i><?php echo $_LANG['skin']['rename'] ?></a></li>
<?php   } ?>

									<li><a href="<?php echo $_LINK['source'] ?>"><i class="icon-leaf"></i><?php echo $_LANG['skin']['source'] ?></a></li>
								</ul>
							</li>
<?php } ?>
							<li class="dropdown">
								<a href="#" data-toggle="dropdown"><?php echo $_LANG['skin']['tool'] ?> <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="<?php echo $_LINK['login'] ?>"><i class="icon-off"></i><?php echo $_LANG['skin']['login'] ?></a></li>
<?php if ($do_backup) { ?>
									<li><a href="<?php echo $_LINK['backup'] ?>"><i class="icon-folder-open"></i><?php echo $_LANG['skin']['backup'] ?></a></li>
<?php } ?>
<?php   if ((bool)ini_get('file_uploads')) { ?>
									<li><a href="<?php echo $_LINK['upload'] ?>"><i class="icon-upload"></i><?php echo $_LANG['skin']['upload'] ?></a></li>
<?php   } ?>
								</ul>
							</li>
							
							<li class="dropdown">
								<a href="#" data-toggle="dropdown"><?php echo $_LANG['skin']['list'] ?> <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="<?php echo $_LINK['list'] ?>"><i class="icon-list"></i><?php echo $_LANG['skin']['list'] ?></a></li>
									<li><a href="<?php echo $_LINK['search'] ?>"><i class="icon-search"></i><?php echo $_LANG['skin']['search'] ?></a></li>
									<li><a href="<?php echo $_LINK['recent'] ?>"><i class="icon-time"></i><?php echo $_LANG['skin']['recent'] ?></a></li>
									<li><a href="<?php echo $_LINK['log'] ?>"><i class="icon-book"></i><?php echo $_LANG['skin']['log'] ?></a></li>
								</ul>
							</li>
							<li><a href="<?php echo $_LINK['help'] ?>"><?php echo $_LANG['skin']['help'] ?></a></li>
						</ul>
						<form class="navbar-search left" action="<?php echo get_script_uri(); ?>">
							<input type="hidden" name="cmd" value="search">
							<input type="text" class="search-query span2" placeholder="<?php echo $_LANG['skin']['search'] ?>">
						</form>
					</div><!-- /.nav-collapse -->
				</div>
			</div><!-- /navbar-inner -->
		</div><!-- /navbar -->
		<header class="jumbotron subhead" id="overview">
			<div class="container">
				<h1><?php echo(($newtitle!='' && $is_read) ? $newtitle: $page) ?></h1>
				<p class="lead"><?php echo ($is_page && exist_plugin_convert('topicpath')) ? do_plugin_convert('topicpath') : ''; ?></p>
			</div>
		</header>

		<div class="container">
			<div class="row-fluid">
<?php if ($layout_class == 'two-colums')  { ?>
				<div class="span3"><div id="menubar" class="well hidden-phone"><?php echo $body_menu; ?></div></div>
				<div class="span9">
					<div id="body" role="main"><?php echo $body ?></div>
				</div>
<?php }else if ($layout_class == 'three-colums')  { ?>
				<div class="span2"><div id="menubar" class="well hidden-phone"><?php echo $body_menu; ?></div></div>
				<div class="span8">
					<div id="body" role="main"><?php echo $body ?></div>
				</div>
				<div class="span2"><div id="sidebar" class="well hidden-phone"><?php echo $body_side; ?></div></div>
<?php }else{ ?>
				<div class="span12">
					<div id="body" role="main"><?php echo $body ?></div>
				</div>
<?php } ?>
			</div>

			<div class="row-fluid">
<?php if (isset($notes) && !empty($notes)): ?>
				<div id="note">
					<?php echo $notes ?>
				</div>
<?php endif; ?>

<?php if (isset($attaches) && !empty($attaches)): ?>
				<div id="attach">
					<?php echo $hr ?>
					<?php echo $attaches ?>
				</div>
<?php endif; ?>
<?php echo $hr ?>
				
<?php if (exist_plugin('toolbar')): ?>
				<?php echo do_plugin_convert('toolbar','reload,|,new,newsub,edit,freeze,diff,upload,copy,rename,|,top,list,search,recent,backup,refer,|,help,|,mixirss'); ?>
<?php endif; ?>
			</div>

<?php if (isset($lastmodified)): ?>
			<div id="lastmodified" class="row-fluid">
				<p class="pull-right">Last-modified: <?php echo $lastmodified ?></p>
			</div>
<?php endif; ?>

<?php if (isset($related)): ?>
			<div id="related" class="row-fluid">
				<?php echo $related ?>
			</div>
<?php endif; ?>
		</div>
		
		<footer class="footer row-fluid">
			<address>Founded by <a href="<?php echo $modifierlink ?>"><?php echo $modifier ?></a></address>
			<?php echo exist_plugin_inline('qrcode') ? plugin_qrcode_inline(1,$_LINK['reload']) : ''; ?>
			<div id="sigunature">
				<?php echo S_COPYRIGHT;?><br />
				HTML convert time: <?php echo showtaketime() ?> sec. <br />
				Squawker skin by <a href="http://logue.be">Logue</a> based on <a href="http://twitter.github.com/bootstrap/">Twitter Bootstrap.
			</div>
		</footer>
		<?php echo $pkwk_tags; ?>
	</body>
</html>
