<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: classic.ini.php,v 0.0.1 2012/01/08 10:06:30 Logue Exp $
// 
// PukiWiki Adv. Mobile Theme
// Copyright (C)
//   2012 PukiWiki Advance Developer Team

global $pkwk_dtd, $_SKIN, $is_page, $defaultpage;

if (!defined('DATA_DIR')) { exit; }

if ($newtitle != '' && $is_read) {
	$title = $newtitle;
}

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
		<article data-role="page" class="type-interior">
			<header data-role="header" role="banner" data-theme="<?php echo $_SKIN['ui_theme']; ?>">
				<h1><?php echo $title ?></h1>
				<a href="javascript:history.back(-1);" data-icon="back" data-iconpos="notext" data-direction="reverse" class="ui-btn-left jqm-back">Back</a>
				<a href="<?php echo $_LINK['search'] ?>" data-icon="search" data-iconpos="notext" data-direction="reverse" data-rel="dialog" class="ui-btn-right jqm-search">Search</a>
			</header><!-- /header -->

			<section role="main" data-role="content" class="content-primary" data-theme="<?php echo $_SKIN['ui_theme']; ?>">
				<?php echo $body ?>
			</section>

			<footer data-role="footer"  class="footer-docs" role="contentinfo" data-theme="<?php echo $_SKIN['ui_theme']; ?>">
				<a href="<?php echo $_LINK['top'] ?>" data-icon="home" data-iconpos="notext" data-direction="reverse" class="ui-btn-left jqm-back">Home</a>
				<h4>Founded by <a href="<?php echo $modifierlink ?>"><?php echo $modifier ?></a></h4>
				<p style="text-align:center; clear:both; font-size:87%;">Powered by <a href="http://pukiwiki.logue.be/"><?php echo GENERATOR ?></a>. HTML convert time: <?php echo showtaketime() ?> sec.</p>
			</footer><!-- /footer -->
		</article><!-- /page -->
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.6.4.min.js"></script>
		<script type="text/javascript" src="http://code.jquery.com/mobile/1.0/jquery.mobile-1.0.min.js"></script>
	</body>
</html>
