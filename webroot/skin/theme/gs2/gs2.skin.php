<?php
/**
 * PukiWiki Advance - Yet another WikiWikiWeb clone.
 *
 * PukiWiki original skin "GS2" 1.5.3
 *     by yiza < http://www.yiza.net/ >
 * Adv. Edition by Logue
 */
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $this->lang; ?>">
	<head prefix="og: http://ogp.me/ns# fb: http://www.facebook.com/2008/fbml">
<?php echo $this->head; ?>
		<link rel="stylesheet" type="text/css" href="<?php echo $this->path; ?>gs2.css.php?color=<?php echo $this->conf['color'] ?>" />
		<title><?php echo $this->title . ' - ' . $this->site_name; ?></title>
	</head>

	<body>
		<div id="container" class="<?php echo $this->colums ?>" role="document">
<!--Header-->
		<header id="header" class="clearfix" role="banner">
<!-- Header/Search -->
			<div class="clearfix">
				<div class="pull-left">
					<?php echo $this->pluginBlock('navibar','top,reload,new,list,search,recent,help,login') ?>
				</div>
<?php if ($this->conf['search_form'] == true) { ?>
				<div class="pull-right">
					<?php echo $this->pluginBlock('search'); ?>
				</div>
<?php } ?>
			</div>
			<?php echo isset($this->conf['logo']) ? '<a id="logo" href="' . $this->links['top'] . '"><img src="' . $this->conf['logo']['src'] . '" width="' . $this->conf['logo']['width'] . '" height="' . $this->conf['logo']['height'] . '" alt="' . $this->conf['logo']['alt'] . '" /></a>' : ''; ?>
			<div id="hgroup">
				<h1><a href="<?php echo $this->links['related'] ?>"><?php echo $this->title ?></a></h1>
			</div>
			<?php echo ($this->conf['show_navibar'] === true && $this->is_read) ? $this->pluginBlock('navibar','edit,freeze,copy,diff,backup,upload,trackback,referer') :'' ?>
<?php if ( isset($this->lastmodified) ) { ?>
			<div id="pageinfo" class="text-right">Last update on <?php echo $this->lastmodified ?></div>
<?php } ?>
		</header>
		<div id="wrapper" class="clearfix">
			<div id="main_wrapper">
				<div id="main" role="main">
					
					<div id="content">
						<?php echo $this->topicpath; ?>
						<section id="body" role="main">
							<?php echo $this->body."\n" ?>
						</section>
<?php if (!empty($this->notes)) { ?>
<!-- * Note * -->
						<aside id="note" class="footbox" role="note">
							<?php echo $this->notes."\n" ?>
						</aside>
<!--  End Note -->
<?php } ?>
<?php if (!empty($this->attaches)) { ?>
<!-- * Attach * -->
						<aside id="attach" class="footbox">
							<?php echo $this->attaches ?>
						</aside>
<!--  End Attach -->
<?php } ?>
<?php if (!empty($this->related)) { ?>
<!-- * related * -->
						<aside id="related" class="footbox">
							<?php echo $this->related ?>
						</aside>
<!--  End related -->
<?php } ?>
					</div>
					<?php echo $this->toolbar; ?>
				</div>
				<?php if (!empty($this->conf['adarea']['footer'])) echo '<div id="footer_adspace" class="noprint" style="text-align:center;">' . $this->conf['adarea']['footer'] . '</div>'; ?>
			</div>

<?php if ($this->colums == 'three-colums' || $this->colums == 'two-colums')  { ?>
<!-- Left -->
			<aside id="menubar" class="sidebox"  role="navigation">
				<?php echo do_plugin_convert('menu')."\n" ?>
				<?php echo ($this->conf['counter'] === true && exist_plugin('counter')) ? '<p>Total:' . $this->pluginInline('total') . ' / Today:' . $this->pluginInline('today').'</p>'."\n" : ''; ?>
			</aside>
<?php } ?>
<?php if (!empty($this->sidebar))  { ?>
<!-- Right -->
			<aside id="sidebar" class="sidebox" role="navigation">
				<?php echo $this->sidebar ?>
			</aside>
<?php } ?>
		</div>
		<footer id="footer" role="contentinfo">
<?php if ($this->conf['qrcode']) { ?>
			<div id="qrcode">
				<?php echo $this->pluginInline('qrcode',$this->links['reload']); ?>
			</div>
<?php } ?>
			<div id="signature">
				<address>Site admin: <a href="<?php echo $this->modifierlink ?>"><?php echo $this->modifier ?></a></address>
				<?php echo S_COPYRIGHT ?>.<br />
				<strong>GS2 Skin</strong> designed by <a href="http://www.yiza.net/" rel="external">yiza</a> / Adv. version by <a href="http://logue.be/" rel="external">Logue</a>.<br />
				Processing time: <var><?php echo $this->proc_time; ?></var> sec.
			</div>
		</footer>
		<?php echo $this->js; ?>
	</body>
</html>
