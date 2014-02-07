<?php
/**
 * PukiWiki Advance - Yet another WikiWikiWeb clone.
 * $Id: squawker.skin.php,v 0.0.5 2014/02/05 19:18:30 Logue Exp $
 *
 * PukiWiki Adv. Mobile Theme
 * Copyright (C)
 *   2012-2014 PukiWiki Advance Developer Team
 */
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $this->lang; ?>">
	<head prefix="og: http://ogp.me/ns# fb: http://www.facebook.com/2008/fbml">
<?php echo $this->head; ?>
		<title><?php echo $this->title . ' - ' . $this->site_name; ?></title>
		<link rel="stylesheet" type="text/css" href="<?php echo $this->path; ?>css/squawker.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo $this->path; ?>css/custom-theme/jquery-ui-1.10.3.theme.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo $this->path; ?>css/custom-theme/jquery-ui-1.10.3.custom.css" />
	</head>
	<body>
		<div id="wrap">
			<div class="navbar navbar-inverse navbar-fixed-top">
				<div class="container">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<a class="navbar-brand" href="<?php echo $this->links['top'] ?>"><?php echo $this->site_name ?></a>
					</div>
					<div class="collapse navbar-collapse">
						<ul class="nav navbar-nav">
							<li><a href="<?php echo $this->links['reload'] ?>"><?php echo $this->strings['skin']['reload'] ?></a></li>
<?php if ($this->is_page) { ?>
							<li class="dropdown">
								<a data-toggle="dropdown" href="#"><?php echo $this->strings['skin']['site'] ?><b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="<?php echo $this->links['top'] ?>"><i class="glyphicon glyphicon-home"></i><?php echo $this->strings['skin']['top'] ?></a></li>
									<li><a href="<?php echo $this->links['new'] ?>"><i class="glyphicon glyphicon-plus"></i><?php echo $this->strings['skin']['new'] ?></a></li>
								</ul>
							</li>
							<li class="dropdown">
								<a  data-toggle="dropdown" href="#"><?php echo $this->strings['skin']['page'] ?><b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="<?php echo $this->links['edit'] ?>"><i class="glyphicon glyphicon-pencil"></i><?php echo $this->strings['skin']['edit'] ?></a></li>
<?php global $function_freeze; ?>
<?php   if ($this->is_read and $function_freeze) { ?>
<?php     if ($this->is_freeze) { ?>
									<li><a href="<?php echo $this->links['unfreeze'] ?>"><i class="glyphicon glyphicon-wrench"></i><?php echo $this->strings['skin']['unfreeze'] ?></a></li>
<?php     } else { ?>
									<li><a href="<?php echo $this->links['freeze'] ?>"><i class="glyphicon glyphicon-lock"></i><?php echo $this->strings['skin']['freeze'] ?></a></li>
<?php     } ?>
									<li><a href="<?php echo $this->links['diff'] ?>"><i class="glyphicon glyphicon-th-list"></i><?php echo $this->strings['skin']['diff'] ?></a></li>
									<li><a href="<?php echo $this->links['copy'] ?>"><i class="glyphicon glyphicon-tags"></i><?php echo $this->strings['skin']['copy'] ?></a></li>
									<li><a href="<?php echo $this->links['rename'] ?>"><i class="glyphicon glyphicon-tag"></i><?php echo $this->strings['skin']['rename'] ?></a></li>
<?php   } ?>

									<li><a href="<?php echo $this->links['source'] ?>"><i class="glyphicon glyphicon-leaf"></i><?php echo $this->strings['skin']['source'] ?></a></li>
								</ul>
							</li>
<?php } ?>
							<li class="dropdown">
								<a href="#" data-toggle="dropdown"><?php echo $this->strings['skin']['tool'] ?> <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="<?php echo $this->links['login'] ?>"><i class="glyphicon glyphicon-off"></i><?php echo $this->strings['skin']['login'] ?></a></li>
									<li><a href="<?php echo $this->links['backup'] ?>"><i class="glyphicon glyphicon-folder-open"></i><?php echo $this->strings['skin']['backup'] ?></a></li>
<?php   if ((bool)ini_get('file_uploads') && isset($this->links['upload'])) { ?>
									<li><a href="<?php echo $this->links['upload'] ?>"><i class="glyphicon glyphicon-upload"></i><?php echo $this->strings['skin']['upload'] ?></a></li>
<?php   } ?>
								</ul>
							</li>
							
							<li class="dropdown">
								<a href="#" data-toggle="dropdown"><?php echo $this->strings['skin']['list'] ?> <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="<?php echo $this->links['list'] ?>"><i class="glyphicon glyphicon-list"></i><?php echo $this->strings['skin']['list'] ?></a></li>
									<li><a href="<?php echo $this->links['search'] ?>"><i class="glyphicon glyphicon-search"></i><?php echo $this->strings['skin']['search'] ?></a></li>
									<li><a href="<?php echo $this->links['recent'] ?>"><i class="glyphicon glyphicon-time"></i><?php echo $this->strings['skin']['recent'] ?></a></li>
									<li><a href="<?php echo $this->links['log'] ?>"><i class="glyphicon glyphicon-book"></i><?php echo $this->strings['skin']['log'] ?></a></li>
								</ul>
							</li>
							<li><a href="<?php echo $this->links['help'] ?>"><?php echo $this->strings['skin']['help'] ?></a></li>
						</ul>
					</div><!-- /.nav-collapse -->
				</div>
			</div><!-- /navbar -->

			<header class="jumbotron">
				<div class="container">
					<h1><a href="<?php echo $this->links['related'] ?>"><?php echo $this->title ?></a></h1>
				</div>
			</header>

			<div class="container">
				<div class="row">
<?php if ($this->colums == 'two-colums')  { ?>
					<div class="col-sm-3"><div id="menubar" class="well hidden-phone"><?php echo $this->menubar; ?></div></div>
					<div class="col-sm-9">
						<div id="body" role="main"><?php echo $this->body ?></div>
					</div>
<?php }else if ($this->colums == 'three-colums')  { ?>
					<div class="col-sm-2"><div id="menubar" class="well hidden-phone"><?php echo $this->menubar; ?></div></div>
					<div class="col-sm-8">
						<div id="body" role="main"><?php echo $this->body ?></div>
					</div>
					<div class="col-sm-2"><div id="sidebar" class="well hidden-phone"><?php echo $this->sidebar; ?></div></div>
<?php }else{ ?>
					<div class="col-sm-12">
						<?php echo $this->topicpath; ?>
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
				<div id="lastmodified" class="clear">
					<p class="text-right">Last-modified: <?php echo $this->lastmodified ?></p>
				</div>
<?php endif; ?>

<?php if (isset($this->related)): ?>
				<div id="related" class="row-fluid">
					<?php echo $this->related ?>
				</div>
<?php endif; ?>
			</div>
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
	</body>
</html>
