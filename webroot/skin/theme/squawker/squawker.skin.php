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

// Initialize
if (!defined('DATA_DIR')) { exit; }

// Output HTML DTD, <html>, and receive content-type
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head prefix="og: http://ogp.me/ns# fb: http://www.facebook.com/2008/fbml">
<?php echo $this->head; ?>
		<link rel="stylesheet" type="text/css" href="<?php echo $this->path; ?>css/squawker.css.php?base=<?php echo urlencode(IMAGE_URI); ?>" />
		<title><?php echo $this->title . ' - ' . $this->site_name; ?></title>
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
					<a class="brand" href="<?php echo $this->links['top'] ?>"><?php echo $this->site_name ?></a>
					<div class="nav-collapse">
						<ul class="nav">
							<li><a href="<?php echo $this->links['reload'] ?>"><?php echo $this->lang['skin']['reload'] ?></a></li>
<?php if ($this->is_page) { ?>
							<li class="dropdown">
								<a data-toggle="dropdown" href="#"><?php echo $this->lang['skin']['site'] ?><b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="<?php echo $this->links['top'] ?>"><i class="icon-home"></i><?php echo $this->lang['skin']['top'] ?></a></li>
									<li><a href="<?php echo $this->links['new'] ?>"><i class="icon-plus"></i><?php echo $this->lang['skin']['new'] ?></a></li>
								</ul>
							</li>
							<li class="dropdown">
								<a  data-toggle="dropdown" href="#"><?php echo $this->lang['skin']['page'] ?><b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="<?php echo $this->links['edit'] ?>"><i class="icon-pencil"></i><?php echo $this->lang['skin']['edit'] ?></a></li>
<?php global $function_freeze; ?>
<?php   if ($this->is_read and $function_freeze) { ?>
<?php     if ($this->is_freeze) { ?>
									<li><a href="<?php echo $this->links['unfreeze'] ?>"><i class="icon-wrench"></i><?php echo $this->lang['skin']['unfreeze'] ?></a></li>
<?php     } else { ?>
									<li><a href="<?php echo $this->links['freeze'] ?>"><i class="icon-lock"></i><?php echo $this->lang['skin']['freeze'] ?></a></li>
<?php     } ?>
									<li><a href="<?php echo $this->links['diff'] ?>"><i class="icon-th-list"></i><?php echo $this->lang['skin']['diff'] ?></a></li>
									<li><a href="<?php echo $this->links['copy'] ?>"><i class="icon-tags"></i><?php echo $this->lang['skin']['copy'] ?></a></li>
									<li><a href="<?php echo $this->links['rename'] ?>"><i class="icon-tag"></i><?php echo $this->lang['skin']['rename'] ?></a></li>
<?php   } ?>

									<li><a href="<?php echo $this->links['source'] ?>"><i class="icon-leaf"></i><?php echo $this->lang['skin']['source'] ?></a></li>
								</ul>
							</li>
<?php } ?>
							<li class="dropdown">
								<a href="#" data-toggle="dropdown"><?php echo $this->lang['skin']['tool'] ?> <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="<?php echo $this->links['login'] ?>"><i class="icon-off"></i><?php echo $this->lang['skin']['login'] ?></a></li>
									<li><a href="<?php echo $this->links['backup'] ?>"><i class="icon-folder-open"></i><?php echo $this->lang['skin']['backup'] ?></a></li>
<?php   if ((bool)ini_get('file_uploads')) { ?>
									<li><a href="<?php echo $this->links['upload'] ?>"><i class="icon-upload"></i><?php echo $this->lang['skin']['upload'] ?></a></li>
<?php   } ?>
								</ul>
							</li>
							
							<li class="dropdown">
								<a href="#" data-toggle="dropdown"><?php echo $this->lang['skin']['list'] ?> <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="<?php echo $this->links['list'] ?>"><i class="icon-list"></i><?php echo $this->lang['skin']['list'] ?></a></li>
									<li><a href="<?php echo $this->links['search'] ?>"><i class="icon-search"></i><?php echo $this->lang['skin']['search'] ?></a></li>
									<li><a href="<?php echo $this->links['recent'] ?>"><i class="icon-time"></i><?php echo $this->lang['skin']['recent'] ?></a></li>
									<li><a href="<?php echo $this->links['log'] ?>"><i class="icon-book"></i><?php echo $this->lang['skin']['log'] ?></a></li>
								</ul>
							</li>
							<li><a href="<?php echo $this->links['help'] ?>"><?php echo $this->lang['skin']['help'] ?></a></li>
						</ul>
						<form class="navbar-search left" action="<?php echo get_script_uri(); ?>">
							<input type="hidden" name="cmd" value="search">
							<input type="text" class="search-query span2" placeholder="<?php echo $this->lang['skin']['search'] ?>">
						</form>
					</div><!-- /.nav-collapse -->
				</div>
			</div><!-- /navbar-inner -->
		</div><!-- /navbar -->
		<header class="jumbotron subhead" id="overview">
			<div class="container">
				<h1><a href="<?php echo $this->links['related'] ?>"><?php echo $this->title ?></a></h1>
				<p class="lead"><?php echo $this->topicpath; ?></p>
			</div>
		</header>

		<div class="container">
			<div class="row-fluid">
<?php if ($this->colums == 'two-colums')  { ?>
				<div class="span3"><div id="menubar" class="well hidden-phone"><?php echo $this->menubar; ?></div></div>
				<div class="span9">
					<div id="body" role="main"><?php echo $this->body ?></div>
				</div>
<?php }else if ($this->colums == 'three-colums')  { ?>
				<div class="span2"><div id="menubar" class="well hidden-phone"><?php echo $this->menubar; ?></div></div>
				<div class="span8">
					<div id="body" role="main"><?php echo $this->body ?></div>
				</div>
				<div class="span2"><div id="sidebar" class="well hidden-phone"><?php echo $this->sidebar; ?></div></div>
<?php }else{ ?>
				<div class="span12">
					<div id="body" role="main"><?php echo $this->body ?></div>
				</div>
<?php } ?>
			</div>

			<div class="row-fluid">
<?php if (!empty($this->notes)): ?>
				<div id="note">
					<?php echo $this->notes ?>
				</div>
<?php endif; ?>

<?php if ( !empty($this->attaches)): ?>
				<div id="attach">
					<hr />
					<?php echo $this->attaches ?>
				</div>
<?php endif; ?>
				<hr />
				<?php echo $this->toolbar; ?>
			</div>

<?php if (isset($this->lastmodified)): ?>
			<div id="lastmodified" class="row-fluid">
				<p class="pull-right">Last-modified: <?php echo $this->lastmodified ?></p>
			</div>
<?php endif; ?>

<?php if (isset($this->related)): ?>
			<div id="related" class="row-fluid">
				<?php echo $this->related ?>
			</div>
<?php endif; ?>
		</div>
		
		<footer class="footer row-fluid">
			<address>Founded by <a href="<?php echo $this->modifierlink ?>"><?php echo $this->modifier ?></a></address>
			<?php echo $this->pluginInline('qrcode',$this->links['reload']); ?>
			<div id="sigunature">
				<?php echo S_COPYRIGHT;?><br />
				Processing time: <var><?php echo $this->proc_time; ?></var> sec.<br />
				Squawker skin by <a href="http://logue.be">Logue</a> based on <a href="http://twitter.github.com/bootstrap/">Twitter Bootstrap</a>.
			</div>
		</footer>
		<?php echo $this->js; ?>
		<script type="text/javascript" src="<?php echo $this->path; ?>squawker.js.php" defer="defer"></script>
	</body>
</html>
