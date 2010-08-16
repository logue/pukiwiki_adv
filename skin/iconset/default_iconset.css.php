<?php
/**
@prefix : <http://purl.org/net/ns/doas#> .
</skin/iconset/default.css> a :CSSstylesheet;
 :title "Default Iconset";
 :shortdesc "PukiWiki Adv. Default Iconset Stylesheet";
 :created "2007-08-14";
 :release [:revision "2.0.1"; :created "2007-12-25"];
 :author [:name "Logue"; :homepage <http://logue.be/> ];
 :license <http://www.gnu.org/licenses/gpl-2.0.html> .
*/

// PukiWiki Advance default iconset CSS.
// $Id: default_iconset.css.php,v 1.6.5 2010/08/15 20:57:00 Logue Exp $
// Copyright (C) 2010 PukiWiki Advance Developer Team

// Send header
$matches = array();
if(extension_loaded('zlib') && 
	ob_get_length() === FALSE && 
	!ini_get('zlib.output_compression') && 
	ini_get('output_handler') !== 'ob_gzhandler' && 
	ini_get('output_handler') !== 'mb_output_handler'){
	
	// http://jp.php.net/manual/ja/function.ob-gzhandler.php
	ob_start("ob_gzhandler");
}else if(ini_get('zlib.output_compression') &&
	preg_match('/\b(gzip|deflate)\b/i', $_SERVER['HTTP_ACCEPT_ENCODING'], $matches)) {
	// Bug #29350 output_compression compresses everything _without header_ as loadable module
	// http://bugs.php.net/bug.php?id=29350
	header('Content-Encoding: ' . $matches[1]);
	header('Vary: Accept-Encoding');
}
header('Cache-Control: must-revalidate');
header('Expires: ' . gmdate ('D, d M Y H:i:s', time() + 60 * 60 * 24 * 30) . ' GMT');
header('Content-Type: text/css; charset: UTF-8');

$image_dir = '../../image/iconset/default/';

flush();
?>
.pkwk-icon{
	background-repeat: no-repeat;
	background-position: center center;
	color:transparent !important;
	text-indent: -9999px;
	overflow: hidden;
	width: 16px;
	height: 16px;
	padding: 2px;
	margin:2px;
	display:block;
}

.pkwk-icon_splitter{
	text-indent: -9999px;
	overflow: hidden;
	width: 4px;
	height: 16px;
	padding: 2px;
	margin:2px;
	display:block;
}

.pkwk-symbol{
	font-size:8px;
	color:transparent !important;
	text-indent: -9999px;
	overflow: hidden;
	background-repeat: no-repeat;
	background-position: center center;
	width:8px;
	height:8px;
}

.pkwk-icon_linktext{
	background-repeat:no-repeat;
	padding-left:18px;
	height:16px;
	margin:2px;
}


/* commands */
.cmd-add		{ background-image:url("<?php echo $image_dir;?>add.png"); }
.cmd-backup		{ background-image:url("<?php echo $image_dir;?>backup.png"); }
.cmd-brokenlink	{ background-image:url("<?php echo $image_dir;?>brokenlink.png"); }
.cmd-copy		{ background-image:url("<?php echo $image_dir;?>copy.png"); }
.cmd-diff		{ background-image:url("<?php echo $image_dir;?>diff.png"); }
.cmd-edit		{ background-image:url("<?php echo $image_dir;?>edit.png"); }
.cmd-filelist	{ background-image:url("<?php echo $image_dir;?>filelist.png"); }
.cmd-freeze		{ background-image:url("<?php echo $image_dir;?>freeze.png"); }
.cmd-full		{ background-image:url("<?php echo $image_dir;?>full.png"); }
.cmd-guiedit	{ background-image:url("<?php echo $image_dir;?>guiedit.png"); }
.cmd-help		{ background-image:url("<?php echo $image_dir;?>help.png"); }
.cmd-linklist	{ background-image:url("<?php echo $image_dir;?>linklist.png"); }
.cmd-list		{ background-image:url("<?php echo $image_dir;?>list.png"); }
.cmd-log_browse	{ background-image:url("<?php echo $image_dir;?>logview.png"); }
.cmd-log_check	{ background-image:url("<?php echo $image_dir;?>logview.png"); }	/* ! */
.cmd-log_down	{ background-image:url("<?php echo $image_dir;?>logview_download.png"); }
.cmd-log_login	{ background-image:url("<?php echo $image_dir;?>logview.png"); }	/* ! */
.cmd-log_update	{ background-image:url("<?php echo $image_dir;?>logviw_update.png"); }
.cmd-new		{ background-image:url("<?php echo $image_dir;?>new.png"); }
.cmd-newsub		{ background-image:url("<?php echo $image_dir;?>newsub.png"); }
.cmd-print		{ background-image:url("<?php echo $image_dir;?>print.png"); }
.cmd-referer	{ background-image:url("<?php echo $image_dir;?>referer.png"); }
.cmd-rename		{ background-image:url("<?php echo $image_dir;?>rename.png"); }
.cmd-recent		{ background-image:url("<?php echo $image_dir;?>recent.png"); }
.cmd-search		{ background-image:url("<?php echo $image_dir;?>search.png"); }
.cmd-skeylist	{ background-image:url("<?php echo $image_dir;?>searchkeywordlist.png"); }
.cmd-source		{ background-image:url("<?php echo $image_dir;?>source.png"); }
.cmd-template	{ background-image:url("<?php echo $image_dir;?>page.png"); }	/* ! */
.cmd-trackback	{ background-image:url("<?php echo $image_dir;?>trackback.png"); }
.cmd-unfreeze	{ background-image:url("<?php echo $image_dir;?>unfreeze.png"); }
.cmd-upload		{ background-image:url("<?php echo $image_dir;?>attach.png"); }
.cmd-top		{ background-image:url("<?php echo $image_dir;?>top.png"); }

.cmd-rss, .cmd-rdf, .cmd-rss10, .cmd-rss20, .cmd-mixirss{ 
	background-image:url("<?php echo $image_dir;?>feed.png");
}

.cmd-reload, .cmd-reload-rel { 
	background-image:url("<?php echo $image_dir;?>reload.png");
}

/* attach */
.attach-download{ background-image:url("<?php echo $image_dir;?>download.png"); }
.attach-upload	{ background-image:url("<?php echo $image_dir;?>upload.png"); }

/* special page */
.page-top		{ background-image:url("<?php echo $image_dir;?>home.png"); }
.page-recent	{ background-image:url("<?php echo $image_dir;?>recent.png"); }
.page-deleted	{ background-image:url("<?php echo $image_dir;?>delated.png"); }
.page-interwiki	{ background-image:url("<?php echo $image_dir;?>interwiki.png"); }
.page-alias		{ background-image:url("<?php echo $image_dir;?>autoalias.png"); }
.page-menu, .page-side, .page-navigation, .page-head
				{ background-image:url("<?php echo $image_dir;?>page.png"); }	/* ! */

/* symbol */
.symbol-edit	{ background-image:url("<?php echo $image_dir;?>symbol/edit.png"); }
.symbol-guiedit	{ background-image:url("<?php echo $image_dir;?>symbol/guiedit.png"); }
.symbol-attach	{ background-image:url("<?php echo $image_dir;?>symbol/attach.png"); }