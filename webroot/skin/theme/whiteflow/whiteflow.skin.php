<?php
/**
 * PukiWiki Advance - Yet another WikiWikiWeb clone.
 * $Id: whiteflow.skin.php,v 1.0.1 2014/02/07 18:24:30 Logue Exp $
 *
 * White Flow skin for PukiWiki Advance.
 * Copyright (C)
 *   2012-2014 PukiWiki Advance Developer Team
 *
 * based on 
 *   White flow (http://note.openvista.jp/2007/pukiwiki-skin/)
 *   by leva(http://www.geckdev.org)
 *
 * License is The MIT/X11 License (http://www.opensource.org/licenses/mit-license.php)
 */
$layout_class = $this->is_read ? 'work' : 'display';
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $this->lang; ?>">
	<head prefix="og: http://ogp.me/ns# fb: http://www.facebook.com/2008/fbml">
<?php echo $this->head; ?>
		<link rel="stylesheet" type="text/css" href="<?php echo $this->path; ?>whiteflow.css.php" />
		<title><?php echo $this->title . ' - ' . $this->site_name; ?></title>
	</head>
	<body>
<!-- START #containar-->
		<div id="wide-container" role="document">
<!-- * Ad space *-->
			<?php if ($this->conf['adarea']['header']) echo '<div id="header_ad" class="noprint">' . $this->conf['adarea']['header'] . '</div>'; ?>
<!-- * End Ad space * -->
			<?php echo '<div id="container" class="'. $this->colums .'">'; ?>
<!-- START #header -->
			<header id="header" role="banner">
<?php if (!empty($this->headarea)) { ?>
				<hgroup id="hgroup" style="display:none;">
					<h1 id="title"><a href="<?php echo $this->links['related'] ?>"><?php echo $this->title ?></a></h1>
				</hgroup>
				<?php echo $this->headarea ?>
<?php } else { ?>
				<a href="<?php echo $this->links['top'] ?>" id="logo"><img src="<?php echo $this->conf['logo']['src'] ?>" width="<?php echo $this->conf['logo']['width'] ?>" height="<?php echo $this->conf['logo']['height'] ?>" alt="<?php echo $this->conf['logo']['alt'] ?>" /></a>
				<hgroup id="hgroup">
					<h1 id="title"><a href="<?php echo $this->links['related'] ?>"><?php echo $this->title ?></a></h1>
					<h2 id="description">PukiWiki - Yet another WikiWikiWeb clone.</h2>
				</hgroup>
<?php } ?>
			</header>
<!-- END #header -->

			<div id="additional" class="clearfix">
<?php if (arg_check('read')){ ?>
			<?php echo $this->topicpath; ?>
			<?php echo (!empty($this->lastmodified)) ? '<div id="lastmodified">'.$this->lastmodified.'</div>'."\n" : '' ?>
<?php }else if (!empty($this->menubar)){ ?>
			<nav id="topicpath"><a href="<?php echo $this->links['reload'] ?>"><?php echo $this->strings['skin']['reload'] ?></a></nav>
<?php }else{ ?>
			<nav id="topicpath"><a href="<?php echo $this->links['top'] ?>"><?php echo $this->strings['skin']['top'] ?></a></nav>
<?php } ?>
			</div>

<!-- START #content -->
			<div id="content" class="clearfix">
<!-- START #content > #edit-area -->
				<div id="edit-area" class="<?php echo $layout_class; ?>" role="main">
					<section id="body">
						<?php echo $this->body ?>
					</section>

					<div id="misc" class="display">
<?php if (!empty($this->notes)) { ?>
						<hr />
<!-- * Note * -->
						<aside id="note" role="note">
							<?php echo $this->notes ?>
						</aside>
<!--  End Note -->
<?php } ?>
<?php if (!empty($this->attaches)) { ?>
						<hr />
<!-- * Attach * -->
						<aside id="attach">
							<?php echo $this->attaches ?>
						</aside>
<!--  End Attach -->
<?php } ?>

<?php if (!empty($this->related)) { ?>
						<hr />
<!-- * Related * -->
						<aside id="related">
							<?php echo $this->related ?>
						</aside>
<!--  End Related -->
<?php } ?>

<!-- * Ad space * -->
						<?php if (!empty($this->conf['adarea']['footer'])) echo '<div id="footer_adspace" class="noprint" style="text-align:center;">' . $this->conf['adarea']['footer'] . '</div>'; ?>
<!-- * End Ad space * -->
					</div>
				</div>
<!-- END #content > #edit-area -->

<?php if (arg_check('read')){ ?>
<!-- START #content > #menu -->
				<aside id="sidebar" class="clearfix" role="navigation">
					<div id="page-menu" class="clearfix">
<!-- ■BEGIN id:page_action -->
						<h3><?php echo $this->strings['skin']['edit'] ?></h3>
						<ul class="sf-menu sf-vertical">
<?php if (!empty($this->menubar)) { ?>
							<li><a href="<?php echo $this->links['top'] ?>"><span class="pkwk-icon icon-top"></span><?php echo $this->strings['skin']['top'] ?></a></li>
							<li><a href="<?php echo $this->links['reload'] ?>"><span class="pkwk-icon icon-reload"></span><?php echo $this->strings['skin']['reload'] ?></a></li>
							<li><a href="<?php echo $this->links['new'] ?>"><span class="pkwk-icon icon-new"></span><?php echo $this->strings['skin']['new'] ?></a>
								<ul>
									<li><a href="<?php echo $this->links['newsub'] ?>"><span class="pkwk-icon icon-newsub"></span><?php echo $this->strings['skin']['newsub'] ?></a></li>
									<li><a href="<?php echo $this->links['rename'] ?>"><span class="pkwk-icon icon-rename"></span><?php echo $this->strings['skin']['rename'] ?></a></li>
								</ul>
							</li>
							<li><a href="<?php echo $this->links['edit'] ?>"><span class="pkwk-icon icon-edit"></span><?php echo $this->strings['skin']['edit'] ?></a>
								<ul>
<?php global $function_freeze ?>
<?php   if ($this->is_read and $function_freeze) { ?>
<?php     if ($this->is_freeze) { ?>
									<li><a href="<?php echo $this->links['unfreeze'] ?>"><span class="pkwk-icon icon-unfreeze"></span><?php echo $this->strings['skin']['unfreeze'] ?></a></li>
<?php     } else { ?>
									<li><a href="<?php echo $this->links['freeze'] ?>"><span class="pkwk-icon icon-freeze"></span><?php echo $this->strings['skin']['freeze'] ?></a></li>
<?php     } ?>
<?php   } ?>
<?php   if ((bool)ini_get('file_uploads')) { ?>
									<li><a href="<?php echo $this->links['upload'] ?>"><span class="pkwk-icon icon-upload"></span><?php echo $this->strings['skin']['upload'] ?></a></li>
<?php   } ?>
									<li><a href="<?php echo $this->links['source'] ?>"><span class="pkwk-icon icon-source"></span><?php echo $this->strings['skin']['source'] ?></a></li>
									<li><a href="<?php echo $this->links['diff'] ?>"><span class="pkwk-icon icon-diff"></span><?php echo $this->strings['skin']['diff'] ?></a></li>
									<li><a href="<?php echo $this->links['backup'] ?>"><span class="pkwk-icon icon-backup"></span><?php echo $this->strings['skin']['backup'] ?></a></li>
								</ul>
							</li>
<?php } ?>
							<li><a href="<?php echo $this->links['search'] ?>"><span class="pkwk-icon icon-search"></span><?php echo $this->strings['skin']['search'] ?></a></li>
							<li><a href="<?php echo $this->links['list'] ?>"><span class="pkwk-icon icon-list"></span><?php echo $this->strings['skin']['list'] ?></a>
								<ul>
<?php if (arg_check('list')) { ?>
									<li><a href="<?php echo $this->links['filelist'] ?>"><span class="pkwk-icon icon-filelist"></span><?php echo $this->strings['skin']['filelist'] ?></a></li>
<?php } ?>
									<li><a href="<?php echo $this->links['recent'] ?>"><span class="pkwk-icon icon-recent"></span><?php echo $this->strings['skin']['recent'] ?></a></li>
									<li><a href="<?php echo $this->links['referer'] ?>"><span class="pkwk-icon icon-referer"></span><?php echo $this->strings['skin']['referer'] ?></a></li>
									<li><a href="<?php echo $this->links['log'] ?>"><span class="pkwk-icon icon-log"></span><?php echo $this->strings['skin']['log'] ?></a></li>
								</ul>
							</li>
							<li><a href="<?php echo $this->links['login'] ?>"><span class="pkwk-icon icon-login"></span><?php echo $this->strings['skin']['login'] ?></a></li>
						</ul>
					</div>

<?php if (!empty($this->menubar)){ ?>
					<hr />
					<div id="menubar">
						<?php echo $this->menubar; ?>
					</div>
<?php } ?>
				</aside>
<!-- END #content > #menu -->
			</div>
<!-- END #content -->
<?php } ?>

<!-- START #footer -->
			<footer id="footer" role="contentinfo">
				<?php if (!empty($this->footarea)){
					echo $this->footarea;
				}else { // or In this skin?>
					<ul id="signature">
						<li><address>Site admin: <a href="<?php echo $this->modifierlink ?>"><?php echo $this->modifier ?></a></address></li>
						<li><strong>White flow Adv.</strong> based on <a href="http://note.openvista.jp/" rel="external">leva</a>&apos;s <a href="http://note.openvista.jp/2007/pukiwiki-skin/" rel="external"><strong>White flow</strong></a>.</li>
						<li><?php echo S_COPYRIGHT;?></li>
						<li>HTML convert time: <?php echo $this->proc_time; ?> sec.</li>
					</ul>
				<?php } ?>
<!-- END #footer -->
			</footer>
<!-- #END #container -->
		</div>
		<?php echo $this->js; ?>
		<script type="text/javascript" src="<?php echo $this->path; ?>whiteflow.js" defer="defer"></script>
	</body>
</html>
