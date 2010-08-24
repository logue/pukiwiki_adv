<?php

// PukiPlus JavaScript CSS.
// $Id: scripts.css.php,v 1.6.5 2010/07/16 20:57:00 Logue Exp $
// Copyright (C) 2010 PukiWiki Advance Developer Team
//
// JavaScriptで使用するスタイルシート

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

$image_dir = '../image/';

$user_agent = $_SERVER['HTTP_USER_AGENT'];
if (eregi("Opera", $user_agent)){
	$engine = "Opera";
} elseif(eregi("Gecko\/", $user_agent)){
	$engine = "Gecko";
} elseif(eregi("MSIE", $user_agent)){
	$engine = "MSIE";
} elseif (ereg("(KHTML|Konqueror|WebKit)", $user_agent)){
	$engine = "WebKit";
} elseif (eregi("Another_HTML-lint", $user_agent)){
	$engine = "AHL";
} else {
	$engine = "Unknown";
}

flush();
?>
@charset "UTF-8";

/* Customize UI */
img.helper, img.tocpic, map area, a{
	cursor:pointer;
}

.ui-button{
	text-shadow: 0 1px 1px rgba(0,0,0,.3);
	box-shadow: 0 1px 2px rgba(0,0,0,.2);
<?php if ($engine == "Gecko"){ ?>
	-moz-box-shadow:0 1px 2px rgba(0,0,0,.2);
<?php }else if ($engine == "WebKit"){ ?>
	-webkit-box-shadow:0 1px 2px rgba(0,0,0,.2);
<?php } ?>
}

.ui-dialog ul, .ui-dialog dl{
	white-space:nowrap;
}

.ui-widget{
	font-size:10pt !important;
}
/*
input[type='text'], input[type='password'], textarea, select, iframe {
	border:1px solid #AAA;
	margin:0px;
	padding:2px;
	font-family:monospace !important;
	font-size:10pt;
}
*/
input[type='text']:focus, input[type='password']:focus, textarea:focus, select:focus, iframe:focus{
<?php if ($engine == "Gecko"){ ?>
	-moz-box-shadow:0 0 8px #6CF;
<?php }else if ($engine == "WebKit"){ ?>
	-webkit-box-shadow:0 0 8px #6CF;
<?php }else{ ?>
	box-shadow: 0 0 8px #6CF;
<?php } ?>
	border:1px solid #6CF;
}


/* for realedit.js */
#realview_outer {
	border:1px solid #ccc;
	height:200px;
	overflow:scroll;
	display:none;
}

#realview{
	padding:0px 20px;
}
/* Textarea resizer */
div.grippie {
	background:#EEEEEE url(<?php echo $image_dir ?>ajax/grippie.png) no-repeat scroll center 2px;
	border-color:#DDDDDD;
	border-style:solid;
	border-width:0pt 1px 1px;
	cursor:s-resize;
	height:9px;
	overflow:hidden;
}
.resizable-textarea textarea {
	margin-bottom:0pt;
	height: 20%;
}

/* jQueryUI BlockUI */
div#loadingScreen {
	cursor:progress;
	background-position: center center;
	background-repeat: no-repeat;
	background-image: url(<?php echo $image_dir ?>ajax/loading.gif);
}

/* hide the close x on the loading screen */
div.loadingScreenWindow .ui-dialog-titlebar-close {
	display: none;
}

/* Table Sorter */
.even {
	background-color: #3D3D3D;
}
.odd {
	background-color: #6E6E6E;
}
.highlight {
	background-color: #3D3D3D;
	font-weight: bold;
}
th.header {
	background-image: url(<?php echo $image_dir ?>ajax/tablesorter/small.gif);
	background-repeat: no-repeat;
    cursor: pointer;
	height: auto;
}
th.headerSortUp {
	background-image: url(<?php echo $image_dir ?>ajax/tablesorter/small_asc.gif);
	background-color: #3399FF;
	height: auto;
}
th.headerSortDown {
	background-image: url(<?php echo $image_dir ?>ajax/tablesorter/small_desc.gif);
	background-color: #3399FF;
	height: auto;
}

/* Caution! Ensure accessibility in print and other media types... */
@media projection, screen { /* Use class for showing/hiding tab content, so that visibility can be better controlled in different media types... */
	.tabs-hide {
		display: none;
	}
}

/* Hide useless elements in print layouts... */
@media print {
	.tabs-nav {
		display: none;
	}
}

/* Skin */
.tabs-nav {
	list-style: none;
	margin: 0;
	padding: 0 0 0 4px;
}
.tabs-nav:after { /* clearing without presentational markup, IE gets extra treatment */
	display: block;
	clear: both;
	content: " ";
}
.tabs-nav li {
	float: left;
	margin: 0 0 0 1px;
	min-width: 84px; /* be nice to Opera */
}
.tabs-nav a, .tabs-nav a span {
	display: block;
	padding: 0 10px;
	background: url(<?php echo $image_dir ?>ajax/tablesorter/tab.png) no-repeat;
}
.tabs-nav a {
	position: relative;
	top: 1px;
	z-index: 2;
	padding-left: 0;
	color: #27537a;
	font-size: 12px;
	font-weight: bold;
	line-height: 1.2;
	text-align: center;
	text-decoration: none;
	white-space: nowrap; /* required in IE 6 */	
}
.tabs-nav .tabs-selected a {
	color: #000;
}
.tabs-nav .tabs-selected a, .tabs-nav a:hover, .tabs-nav a:focus, .tabs-nav a:active {
	background-position: 100% -150px;
	outline: 0; /* prevent dotted border in Firefox */
}
.tabs-nav a, .tabs-nav .tabs-disabled a:hover, .tabs-nav .tabs-disabled a:focus, .tabs-nav .tabs-disabled a:active {
	background-position: 100% -100px;
}
.tabs-nav a span {
	width: 64px; /* IE 6 treats width as min-width */
	min-width: 64px;
	height: 18px; /* IE 6 treats height as min-height */
	min-height: 18px;
	padding-top: 6px;
	padding-right: 0;
}
*>.tabs-nav a span { /* hide from IE 6 */
	width: auto;
	height: auto;
}
.tabs-nav .tabs-selected a span {
	padding-top: 7px;
}
.tabs-nav .tabs-selected a span, .tabs-nav a:hover span, .tabs-nav a:focus span, .tabs-nav a:active span {
	background-position: 0 -50px;
}
.tabs-nav a span, .tabs-nav .tabs-disabled a:hover span, .tabs-nav .tabs-disabled a:focus span, .tabs-nav .tabs-disabled a:active span {
	background-position: 0 0;
}
.tabs-nav .tabs-selected a:link, .tabs-nav .tabs-selected a:visited, .tabs-nav .tabs-disabled a:link, .tabs-nav .tabs-disabled a:visited { /* @ Opera, use pseudo classes otherwise it confuses cursor... */
	cursor: text;
}
.tabs-nav a:hover, .tabs-nav a:focus, .tabs-nav a:active { /* @ Opera, we need to be explicit again here now... */
	cursor: pointer;
}
.tabs-nav .tabs-disabled {
	opacity: .4;
}
.tabs-container {
	border-top: 1px solid #97a5b0;
	padding: 1em 8px;
	background: #fff; /* declare background color for container to avoid distorted fonts in IE while fading */
}
.tabs-loading em {
	padding: 0 0 0 20px;
	background: url(<?php echo $image_dir ?>ajax/loading.gif) no-repeat 0 50%;
}
/**************************************************************************************************/
// Superfish

/*** ESSENTIAL STYLES ***/
.sf-menu, .sf-menu * {
	margin:			0;
	padding:		0;
	list-style:		none;
}
.sf-menu {
	line-height:	1.0;
}
.sf-menu ul {
	position:		absolute;
	top:			-999em;
	width:			10em; /* left offset of submenus need to match (see below) */
}
.sf-menu ul li {
	width:			100%;
}
.sf-menu li:hover {
	visibility:		inherit; /* fixes IE7 'sticky bug' */
}
.sf-menu li {
	float:			left;
	position:		relative;
}
.sf-menu a {
	display:		block;
	position:		relative;
}
.sf-menu li:hover ul,
.sf-menu li.sfHover ul {
	left:			0;
	top:			2.5em; /* match top ul list item height */
	z-index:		99;
}
ul.sf-menu li:hover li ul,
ul.sf-menu li.sfHover li ul {
	top:			-999em;
}
ul.sf-menu li li:hover ul,
ul.sf-menu li li.sfHover ul {
	left:			10em; /* match ul width */
	top:			0;
}
ul.sf-menu li li:hover li ul,
ul.sf-menu li li.sfHover li ul {
	top:			-999em;
}
ul.sf-menu li li li:hover ul,
ul.sf-menu li li li.sfHover ul {
	left:			10em; /* match ul width */
	top:			0;
}

/*** arrows **/
.sf-menu a.sf-with-ul {
	padding-right: 	2.25em;
	min-width:		1px; /* trigger IE7 hasLayout so spans position accurately */
}
.sf-sub-indicator {
	position:		absolute;
	display:		block;
	right:			.75em;
	top:			1.05em; /* IE6 only */
	width:			10px;
	height:			10px;
	text-indent: 	-999em;
	overflow:		hidden;
	background:		url('<?php echo $image_dir ?>ajax/arrows-ffffff.png') no-repeat -10px -100px; /* 8-bit indexed alpha png. IE6 gets solid image only */
}
a > .sf-sub-indicator {  /* give all except IE6 the correct values */
	top:			.8em;
	background-position: 0 -100px; /* use translucent arrow for modern browsers*/
}
/* apply hovers to modern browsers */
a:focus > .sf-sub-indicator,
a:hover > .sf-sub-indicator,
a:active > .sf-sub-indicator,
li:hover > a > .sf-sub-indicator,
li.sfHover > a > .sf-sub-indicator {
	background-position: -10px -100px; /* arrow hovers for modern browsers*/
}

/* point right for anchors in subs */
.sf-menu ul .sf-sub-indicator { background-position:  -10px 0; }
.sf-menu ul a > .sf-sub-indicator { background-position:  0 0; }
/* apply hovers to modern browsers */
.sf-menu ul a:focus > .sf-sub-indicator,
.sf-menu ul a:hover > .sf-sub-indicator,
.sf-menu ul a:active > .sf-sub-indicator,
.sf-menu ul li:hover > a > .sf-sub-indicator,
.sf-menu ul li.sfHover > a > .sf-sub-indicator {
	background-position: -10px 0; /* arrow hovers for modern browsers*/
}

/*** shadows for all but IE6 ***/
.sf-shadow ul {
	background:	url('<?php echo $image_dir ?>ajax/shadow.png') no-repeat bottom right;
	padding: 0 8px 9px 0;
<?php if ($engine == "Gecko"){ ?>
	-moz-border-radius-bottomleft: 17px;
	-moz-border-radius-topright: 17px;
<?php }else if ($engine == "WebKit"){ ?>
	-webkit-border-top-right-radius: 17px;
	-webkit-border-bottom-left-radius: 17px;
<?php } ?>
}
.sf-shadow ul.sf-shadow-off {
	background: transparent;
}

/*** adding sf-vertical in addition to sf-menu creates a vertical menu ***/
.sf-vertical, .sf-vertical li {
	width:	10em;
}
/* this lacks ul at the start of the selector, so the styles from the main CSS file override it where needed */
.sf-vertical li:hover ul,
.sf-vertical li.sfHover ul {
	left:	10em; /* match ul width */
	top:	0;
}

/*** alter arrow directions ***/
.sf-vertical .sf-sub-indicator { background-position: -10px 0; } /* IE6 gets solid image only */
.sf-vertical a > .sf-sub-indicator { background-position: 0 0; } /* use translucent arrow for modern browsers*/

/* hover arrow direction for modern browsers*/
.sf-vertical a:focus > .sf-sub-indicator,
.sf-vertical a:hover > .sf-sub-indicator,
.sf-vertical a:active > .sf-sub-indicator,
.sf-vertical li:hover > a > .sf-sub-indicator,
.sf-vertical li.sfHover > a > .sf-sub-indicator {
	background-position: -10px 0; /* arrow hovers for modern browsers*/
}

/*** adding the class sf-navbar in addition to sf-menu creates an all-horizontal nav-bar menu ***/
.sf-navbar {
	background:		#BDD2FF;
	height:			2.5em;
	padding-bottom:	2.5em;
	position:		relative;
}
.sf-navbar li {
	background:		#AABDE6;
	position:		static;
}
.sf-navbar a {
	border-top:		none;
}
.sf-navbar li ul {
	width:			44em; /*IE6 soils itself without this*/
}
.sf-navbar li li {
	background:		#BDD2FF;
	position:		relative;
}
.sf-navbar li li ul {
	width:			13em;
}
.sf-navbar li li li {
	width:			100%;
}
.sf-navbar ul li {
	width:			auto;
	float:			left;
}
.sf-navbar a, .sf-navbar a:visited {
	border:			none;
}
.sf-navbar li.current {
	background:		#BDD2FF;
}
.sf-navbar li:hover,
.sf-navbar li.sfHover,
.sf-navbar li li.current,
.sf-navbar a:focus, .sf-navbar a:hover, .sf-navbar a:active {
	background:		#BDD2FF;
}
.sf-navbar ul li:hover,
.sf-navbar ul li.sfHover,
ul.sf-navbar ul li:hover li,
ul.sf-navbar ul li.sfHover li,
.sf-navbar ul a:focus, .sf-navbar ul a:hover, .sf-navbar ul a:active {
	background:		#D1DFFF;
}
ul.sf-navbar li li li:hover,
ul.sf-navbar li li li.sfHover,
.sf-navbar li li.current li.current,
.sf-navbar ul li li a:focus, .sf-navbar ul li li a:hover, .sf-navbar ul li li a:active {
	background:		#E6EEFF;
}
ul.sf-navbar .current ul,
ul.sf-navbar ul li:hover ul,
ul.sf-navbar ul li.sfHover ul {
	left:			0;
	top:			2.5em; /* match top ul list item height */
}
ul.sf-navbar .current ul ul {
	top: 			-999em;
}

.sf-navbar li li.current > a {
	font-weight:	bold;
}

/*** point all arrows down ***/
/* point right for anchors in subs */
.sf-navbar ul .sf-sub-indicator { background-position: -10px -100px; }
.sf-navbar ul a > .sf-sub-indicator { background-position: 0 -100px; }
/* apply hovers to modern browsers */
.sf-navbar ul a:focus > .sf-sub-indicator,
.sf-navbar ul a:hover > .sf-sub-indicator,
.sf-navbar ul a:active > .sf-sub-indicator,
.sf-navbar ul li:hover > a > .sf-sub-indicator,
.sf-navbar ul li.sfHover > a > .sf-sub-indicator {
	background-position: -10px -100px; /* arrow hovers for modern browsers*/
}

/*** remove shadow on first submenu ***/
.sf-navbar > li > ul {
	background: transparent;
	padding: 0;
<?php if ($engine == "Gecko"){ ?>
	-moz-border-radius-bottomleft: 0px;
	-moz-border-radius-topright: 0px;
<?php }else if ($engine == "WebKit"){ ?>
	-webkit-border-top-right-radius: 0px;
	-webkit-border-bottom-left-radius: 0px;
<?php } ?>
}

/***********************************************
* Ajax Tooltip script- by JavaScript Kit (www.javascriptkit.com)
* This notice must stay intact for usage
* Visit JavaScript Kit at http://www.javascriptkit.com/ for this script and 100s more
***********************************************/
.tooltip, .linktip, .ajaxtooltip{
	 color: #006565;
	 border-style: none none dotted none;
	 border-width: medium medium 1px medium;
	 cursor: help;
}

.tooltip:hover{
	background-color: #e1ffe4;
}

.ajaxtooltip{
	color:black;
	font-size:12px;
	text-shadow: white 1px 1px 0px;
	position: absolute; /*leave this alone*/
	display: none; /*leave this alone*/
	min-width:16px;
	min-height:16px;
	max-width:400px;
	text-align:left;
	left: 0; /*leave this alone*/
	top: 0; /*leave this alone*/
	border: 1px solid gray;
/*	border-width: 1px 2px 2px 1px; */
	padding: 3px;
	opacity:0.9;
	border-radius: 3px;
	background-color:gainsboro;
	box-shadow: 3px 3px 5px rgba(0, 0, 0, 0.6);
	z-index: 9999;
<?php if ($engine == "MSIE"){ ?>
	filter:
		alpha(opacity=90)
		progid:DXImageTransform.Microsoft.Gradient(GradientType=0,StartColorStr=white,EndColorStr=gainsboro)
		progid:DXImageTransform.Microsoft.Shadow(Strength=5, Direction=135, Color='#333333') !important;
<?php }else if ($engine == "Gecko"){ ?>
	-moz-opacity:0.9;
	-moz-border-radius: 3px;
	-moz-box-shadow: 3px 3px 5px rgba(0, 0, 0, 0.6);
	background: -moz-linear-gradient(top, white, gainsboro);
<?php }else if ($engine == "WebKit"){ ?>
	-webkit-border-radius: 3px;
	-webkit-box-shadow: 3px 3px 5px rgba(0, 0, 0, 0.6);
	background: -webkit-gradient(linear, left top, left bottom, from(white), to(gainsboro));
<?php }else{ ?>
	background-image:url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAADICAYAAAAp8ov1AAAAkUlEQVR42n3ESRaDIBAFQO9/RhWcUHFAAcEBMNkli36/FpW9X9mvlBIoxkgXQgA9zwO675vuui7QeZ4g7z3IOUd3HAfIWgsyxtBprUH7voO2bQMppejWdQUtywKa55lumiaQlBI0jiNoGAa6vu9BQghQ13V0bduCmqYB1XUNqqqKjnMOYoyByrKkK4oClOf5Xx9LJfP7/AuymAAAAABJRU5ErkJggg==");
	background-repeat:repeat-x;
<?php } ?>
}
.ajaxtooltip p{
	margin:0px;
	padding:0px;
}
/*
	ColorBox Core Style
	The following rules are the styles that are consistant between themes.
	Avoid changing this area to maintain compatability with future versions of ColorBox.
*/
#colorbox, #cboxOverlay, #cboxWrapper{position:absolute; top:0; left:0; z-index:9999; overflow:hidden;}
#cboxOverlay{position:fixed; width:100%; height:100%;}
#cboxMiddleLeft, #cboxBottomLeft{clear:left;}
#cboxContent{position:relative; overflow:hidden;}
#cboxLoadedContent{overflow:auto;}
#cboxLoadedContent iframe{display:block; width:100%; height:100%; border:0;}
#cboxTitle{margin:0;}
#cboxLoadingOverlay, #cboxLoadingGraphic{position:absolute; top:0; left:0; width:100%;}
#cboxPrevious, #cboxNext, #cboxClose, #cboxSlideshow{cursor:pointer;}

/* 
	Example user style
	The following rules are ordered and tabbed in a way that represents the
	order/nesting of the generated HTML, so that the structure easier to understand.
*/

#colorbox{}
#cboxTopLeft{width:5px; height:5px;}
#cboxTopRight{width:5px; height:5px;}
#cboxBottomLeft{width:5px; height:5px;}
#cboxBottomRight{width:5px; height:5px;}
#cboxMiddleLeft{width:5px;}
#cboxMiddleRight{width:5px;}
#cboxTopCenter{height:5px;}
#cboxBottomCenter{height:5px;}
#cboxLoadedContent{margin-bottom:28px;}
#cboxTitle{position:absolute; bottom:4px; left:0; text-align:center; width:100%; color:#949494;}
#cboxCurrent{position:absolute; bottom:4px; left:58px; color:#949494;}
#cboxSlideshow{position:absolute; bottom:4px; right:30px; color:#0092ef;}
#cboxPrevious{position:absolute; bottom:0; left:0px;}
#cboxNext{position:absolute; bottom:0; left:27px;}
#cboxClose{position:absolute; bottom:0; right:0;}
#cboxLoadingOverlay{background:url(<?php echo $image_dir ?>ajax/colorbox/loading_background.png) center center no-repeat;}
#cboxLoadingGraphic{background:url(<?php echo $image_dir ?>ajax/colorbox/loading.gif) center center no-repeat;}


/**************************************************************************************************/
#jplayer_container {
	position:relative;
	padding:20px 0px;
}

ul#jplayer_icons {margin: 0; padding: 0;}
ul#jplayer_icons li {margin: 2px; position: relative; padding: 4px 0; cursor: pointer; float: left;  list-style: none;}
ul#jplayer_icons span.ui-icon {float: left; margin: 0 4px;}

ul#jplayer_icons #jplayer_volume-min {
	margin:2px 140px 2px 350px;
}

#jplayer_sliderVolume {
	position:absolute;
	top:30px;
	left:450px;
	width:110px;
	height:.4em;
}


#jplayer_sliderVolume .ui-slider-handle {
	height:.8em;
	width:.8em;
}

#jplayer_bars_holder {
	position:absolute;
	top:27px;
	left:80px;
	width:300px;
}

#jplayer_sliderPlayback .ui-slider-handle {
	height:1.6em;
}

#jplayer_loaderBar.ui-progressbar {
	height:.2em;
	border:0;
}
/**************************************************************************************************/
div.table_pager_widget{
	display:none;
}

div.table_pager_widget ul li{
	margin: 2px;
	cursor: pointer;
	float: left;
	list-style: none;
}

div.table_pager_widget ul li.ui-state-default{
	height:1.2em;
	width:1.2em;
	padding:2px;
}

div.table_pager_widget button{
	height:1.2em;
	width:1.2em;
	padding:2px;
}

div.table_pager_widget input.pagedisplay{
	width:50px;
}

div.table_pager_widget select.pagesize{
	width:80px;
}
/**************************************************************************************************/

#swfupload-control p{ margin:10px 5px; font-size:0.9em; }
#swfupload-log{ margin:0; padding:0; width:500px;}
#swfupload-log li{ list-style-position:inside; margin:2px; padding:10px; font-size:12px; position:relative;}
#swfupload-log li .progressbar{ height:5px;}
#swfupload-log li p{ margin:0; line-height:18px; }
#swfupload-log li.success{ border:1px solid #339933; background:#ccf9b9; }

/* mediaplayer.inc.php */
/* for MediaPlayer */
.mediaplayerbutton
{
	margin:2px 2px;
	width:24px;
}

.playercontainer
{
	border:solid 1px #333;
	width:320px;
	text-align:center;
	vertical-align:middle;
	position:relative;
}

.videosplash
{
	position:expression('absolute');
	display:block;
}

.player
{
	display:none;
	display:expression(PlayerSupported(this)?'block':'none');
	background-color:Black;
	font-size:0px;
}

.controlstable
{
	width:320px;
	margin:0px;
	background-image:url(<?php echo $image_dir ?>player/base.gif);
/*	background-repeat:no-repeat;	*/
}

table.controlstable tr td
{
	background-color:transparent;
}

.controlstablenoscript
{
	display:expression(PlayerSupported(this)?'none':'block');
	margin:0px;
	background-image:url(<?php echo $image_dir ?>player/base.gif);
	background-repeat:no-repeat;
}

.slider{
	background-image:url(<?php echo $image_dir ?>player/playbar.gif);
	background-repeat:no-repeat;
	background-position:center center;
}

.indicator,.downloadindicator{
	width:0px;
	height:3px;
	margin-left:1px;
	margin-top:2px;
}

.indicatorhandle{
	margin-top:2px;
}

<?php if(extension_loaded('zlib')){ob_end_flush();}?>