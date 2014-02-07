<?php
/**
 * PukiWiki Advance - Yet another WikiWikiWeb clone.
 * $Id: xxxlogue.css.php,v 2.4.6 2014/02/07 18:08:30 Logue Exp $
 * Copyright (C) 2010-2012,2014 PukiWiki Advance Developers Team
 *               2007-2010 Logue
 *
 * xxxLogue skin for PukiWiki Advance
 *
 * Based on
 *   Xu Yiyang's (http://xuyiyang.com/) Unnamed (http://xuyiyang.com/wordpress-themes/unnamed/)
 *
 * License: GPL v3 or (at your option) any later version
 * http://www.opensource.org/licenses/gpl-3..html
 */

// Error reporting
//error_reporting(0); // Nothing
error_reporting(E_ERROR | E_PARSE); // Avoid E_WARNING, E_NOTICE, etc
//error_reporting(E_ALL); // Show all errors

ini_set('zlib.output_compression', 'Off');

$_SKIN = require_once('xxxlogue.ini.php');

// Style
$menubar   = isset($_GET['menubar'])   ? $_GET['menubar']	: '';
$expire = isset($_GET['expire'])   ? (int)$_GET['expire'] * 86400	: '604800';	// Default is 7 days.
// Send header
header('Content-Type: text/css; charset: UTF-8');
header('Cache-Control: private');
header('Expires: ' .gmdate('D, d M Y H:i:s',time() + $expire) . ' GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s', getlastmod() ) . ' GMT');
@ob_start('ob_gzhandler');
?>
@charset "UTF-8";
/** Generic Tags ***********************************************************************************/
body{
	background:url(<?php echo $_SKIN['image_dir'] ?>bg_body.png) repeat top center #EBEBEB !important;
}

/* anchor tag */
a {
	color:#3578b2 !important;
	text-decoration:none;
}

a:active, a:hover {
	color:#215dc6;
}

a:hover {
	text-decoration:underline;
}

a:visited{
	/* color:#a63d21; */
	color:#5D8BB3;
}

address{
	text-align:left;
	margin:0 1.5% !important;
}

blockquote {
	border-left:3px solid #ccc;
	margin:20px;
	padding-left:10px;
}

dt{
	font-weight:bold;
	color: #333366;
}

dd{
	margin-left:1.5em;
	margin-bottom:.5em;
}

fieldset pre{
	margin:.2em;
}

.noexists {
	background-color:#FFFACC;
}


h1 {
	border-bottom:	2px solid #111166;
	border-left:	10px solid #5555CC;
	padding:		2px 2px 3px 8px;
	margin:			10px 0 10px 0;
}

h2 {
	border-bottom:	2px solid #333388;
	border-left:	8px solid #7777FF;
	padding:		2px 2px 3px 8px;
	margin:			10px 0;
}

h3 {
	border-bottom:	1px solid #bb4444;
	border-left:	6px solid #FF7777;
	padding:		2px 2px 3px 8px;
	margin:			8px 4px;
}

h4 {
	border-bottom:	1px solid #33bb33;
	border-left:	4px solid #21E05D;
	padding:		2px 2px 3px 8px;
	margin:			4px 8px;
}

h5, h6 {
	padding: 2px 2px 3px 8px;
	border-bottom: 1px solid #bb3333;
	border-left: 2px solid #E05D21;
	margin: .5em 0 .5em 0;
}

h1, h2, h3, h4, h5, h6{
	background-color:rgba(255, 255, 255, .9);
}

pre {
	border-top:#DDDDEE 1px solid;
	border-bottom:#888899 1px solid;
	border-left:#DDDDEE 1px solid;
	border-right:#888899 1px solid;
	background-color:#F0F8FF;
	text-shadow: 1px 1px 3px rgba(0,0,0,.3);
	background-color:rgba(240,248,255,.5);
	padding:.5em 1em;
}

small,.small {
	color:#777;
}

/** Misc ******************************************************************************************/
.noexists {
	background-color:#FFFACC;
	text-shadow:none;
}

/* Table Tags */
.table{
	background-color:#ccd5dd;
}
.table th{
	background-color:#EEEEEE;
}
.table td{
	background-color:#EEF5FF;
}
.table td.blank{
	background-color:#E3EAF6;
}
.table tr:nth-child(even) td{
	background-color:#EFf5ff;
}
.table thead td,
.table tfoot td {
	background-color:#D0D8E0;
}
.table thead th,
.table tfoot th {
	background-color:#E0E8F0;
}

/** Skin Stylesheet *******************************************************************************/
#hgroup {
	padding: 5px 0 5px 20px;
	text-shadow: black 1px 1px 1px;
	display: block;
	float: left;
}

#hgroup h1, #hgroup h2 {
	background-color: transparent;
	padding: 0;
	border: 0;
}

#hgroup h1 {
	font-weight: bold;
}

#hgroup h2 {
	font-weight: normal;
	font-size: 85%;
}

#topicpath{
	line-height: 100% !important;
}

/* title(serif) font set */
#title, #title a {
	font-family: "Lucida Bright", Century, "Times New Roman", serif;
	line-height:100% !important;
}
:lang(ja) #title, :lang(ja) #title a {
	font-family: 'ヒラギノ明朝 Pro W6', 'Hiragino Mincho Pro W6', 'HGP明朝E', '平成明朝', 'ＭＳ Ｐ明朝', 'MS PMincho' !important;
}
:lang(ko) #title, :lang(ko) #title a {
	font-family: '바탕체', 'Batang' !important;
}
:lang(zh) #title, :lang(zh) #title a {
	font-family: 'STSong', 'STFangsong', 'NSimSun', 'SimSun', 'FangSong', '細明體', '宋体' !important;
}

#container{
	width: 970px;
}

#header{
	width: 960px;
	height: 78px;
}

#toggle {
	width: 960px;
}

#content-top, #content-bottom {
	width: 958px;
	height: 15px;
}

#content {
	width: 958px;
}

#primary-content {
<?php   	if ($sidebar == 'left') { ?>
	float: right;
<?php   	} else { ?>
	float: left;
<?php   	} ?>
	width: 750px;
}

#sidebar {
	width: 190px;
	text-align: left;
<?php   	if ($sidebar == 'left') { ?>
	padding: 0 0 0 1%;
	float: left;
<?php   	} else { ?>
	padding: 0 1% 0 0;
	float: right;
<?php   	} ?>
}

#footer{
	height: 64px;
	width: 970px;
}

/* Container */
#container {
	position: relative;
	margin: 0 auto;
	background: url(<?php echo $_SKIN['image_dir'] ?>bg_content.png) repeat-y center top transparent;
}

/* Header and Navigation */
#header {
	margin: 0 auto;
	color: gainsboro;
	vertical-align: middle;
	background: url(<?php echo $_SKIN['image_dir'] ?>bg_header.png) transparent repeat top center;
	overflow: hidden;
}

@media screen{
	#header a, #header a:link, #header a:active, #header a:visited {
		color: white !important;
	}
	#header a:hover{
		background-color: none;
	}
}

#header #header_ad {
	float: right;
	margin: 6px;
}

/* Shelf */
#shelf {
	width: 100%;
}

#toggle {
	background-color: black;
	color: #CCC;
	display: none;
	list-style: none;
	margin: 0 auto !important;
	overflow: hidden;
	text-shadow: none;
}

#toggle h2, #toggle dt{
	color: #fff;
	font-weight: 400;
	margin: 15px 2px 0;
}

#toggle > ul {
	list-style: none;
	margin: 10px 0;
}

#toggle > ul li {
	float: left;
	width: 21%;
	background: transparent;
	margin-left: 1.8% !important;
	padding: 0 4px 6px 4px;
	display: inline;
	width: 100%;
	margin: 2px 0;
	padding: 0 8px;
}
#toggle > ul li a {
	display: block;
	background: transparent;
	color: #fff;
	white-space: nowrap;
	padding: 2px;
	border-top: 1px solid #3465A4;
	border-bottom: 1px solid #3465A4;
	text-decoration: none;
}

#toggle > ul li a:link, #toggle ul li a:visited{
	color: #fff;
}

#toggle > ul li a:hover {
	border-top: 1px solid #fc0;
	border-bottom: 1px solid #fc0;
}

#toggle li.tags {
	width: 45%;
}
#toggle li.tags a:hover {
	background:none;
	text-decoration:underline;
}

#toggle .toolbar{
	margin-bottom: 5px;
	clear: both;
	float: right;
}

#toggle #shelf_form{
	float: left;
}

#toggle .toolbar a:hover{
	background-color: grey;
}

#toggle #inner_toggle {
	padding:10px;
}

/* Primary Cotent and Entries */
#body {
	width: auto;
	padding: 0 2%;
/*	overflow:hidden; */
}

#footer_ad{
	text-align: center;
}

#lastmodified{
	text-align: right;
	background: url(<?php echo $_SKIN['image_dir'] ?>bg_meta.png) no-repeat top center;
}

/* Sidebar */
#sidebar {
	display: block;
	font-size: 93%;
	text-shadow: 1px 1px 0 white;
}

#sidebar h2, #sidebar h3, #sidebar h4, #sidebar h5{
	font-weight: normal;
	padding: 4px 2px;
	margin: 4px 0;
	font-size: 123.1%;
	border-width: 1px 1px 1px 10px;
	border-style: solid;
}

#sidebar h2 {
	border-color: #aabbff;
}

#sidebar h3 {
	border-color: #aaffbb;
}

#sidebar h4 {
	border-color: #ffaabb;
}

#sidebar h5 {
	border-color: #bbaaff;
}

#sidebar ul {
	margin: 0 0 0 .7em;
	padding: 0 0 0 .7em;
}

/* Hack calendar */
#sidebar .style_calendar{
	width: 150px !important;
	height: 150px !important;
	padding: 0;
	margin: 1px auto;
}

#toggle ul {
	margin: 0;
	padding: 0 0 10px;
}

/* Footer */
#footer {
	text-shadow: black 1px 1px 1px;
	clear: both;
	padding: .1em .5em;
	margin: auto;
	background: url(<?php echo $_SKIN['image_dir'] ?>bg_footer.png) no-repeat top center transparent;
	color: gainsboro;
	text-align: center;
	font-size: 85%;
}


#footer strong, #footer a:hover{
	color: white;
	background-color: transparent;
}

/* Miscellaneous */
#content {
	display: block;
	margin: auto;
	text-shadow: 1px 1px 0 white;
/*	filter: DropShadow(Color=white, OffX=1, OffY=1);	*/
	background-color: rgba(255, 255, 255, .9);
}

.ie6 #content, .ie7 #content, .ie8 #content {
	background: url(<?php echo $_SKIN['image_dir'] ?>bg_white.png) repeat center top transparent !important;
}

#content-top {
	background: url(<?php echo $_SKIN['image_dir'] ?>bg_content_top.png) transparent repeat-x top center;
}

#single-content{
	overflow: hidden;
}

#content-bottom {
	background: url('<?php echo $_SKIN['image_dir'] ?>bg_content_bottom.png') repeat-x bottom left;
}

#content-top, #content-bottom {
	font-size: 85%;
	text-align: right;
	display: block;
	clear: both;
	margin: 0 auto;
}

#content-top a, #content-bottom a {
	padding: 0 1em;
}

.ui-widget{
	font-size:inherit !important;
}

.ui-widget h1 {
	padding: 2px 2px 3px 8px;
	margin: 10px 0 10px 0;
	border-left:   8px solid #7777FF;
	border-bottom: 2px solid #333388;
}

.ui-widget h2 {
	padding: 2px 2px 3px 8px;
	margin: 10px 0 10px 0;
	border-left:   8px solid #7777FF;
	border-bottom: 2px solid #333388;
}

.ui-widget h3 {
	border-bottom:  1px solid #bb4444;
	border-left:   10px solid #FF7777;
	padding: 2px 2px 4px 8px;
	margin: 8px 1em 8px 0;
}

.ui-widget h4 {
	border-bottom:  1px solid #33bb33;
	border-left:   10px solid #21E05D;
	padding: 2px 4px 4px 8px;
	margin: 8px 0 8px 0;
}

.ui-widget h5, .ui-widget h6 {
	padding: 2px 4px 2px 8px;
 	border: 0;
 	margin: .5em 0 .5em 0;
}

.ui-state-highlight, .ui-state-error, .wikicolor{
	text-shadow: none;
}

/* for realedit.js */
#realview_outer {
	float: left;
	z-index: 10;
	overflow: scroll;
	display: none;
	background: white url(<?php echo $_SKIN['image_dir'] ?>bg_input.jpg) repeat-x left top;
}

#realview {
	width: 100%;
}

/** Plugin Stylesheet ****************************************************************************/
/* attach.inc.php */
#attach{
	margin: 0 1%;
}

#related{
	font-size: 93%;
}

#attach, #related{
	clear: both;
	margin: 10px 0;
}

#attach dl, #related dl{
	display: block;
}

#attach dl dt, #related dl dt{
	margin-left: .5em;
	margin-top: 0;
	float: left;
	font-weight: normal;
}

#attach dl dd, #related dl dd{
	margin-left: .5em;
	padding: .1em;
	display: block;
	float: left;
}

/* backup.inc.php */
.add_word, .add_block{
	background-color: #FFFF66;
}

.remove_word, .remove_block{
	background-color: #A0FFFF;
}

/* calendar.inc.php */
.style_calendar {
	background-color: #CCD5DD;
}
.style_calendar_top {
	background-color: #EEF5FF !important;
}
.style_calendar_today {
	background-color: #FFFFDD;
}
.style_calendar_sat {
	background-color: #DDE5FF;
}
.style_calendar_sun, .style_calendar_holiday {
	background-color: #FFEEEE;
}
.style_calendar_blank {

}
.style_calendar_day {

}
.style_calendar_week {
	background-color: #DDE5EE;
}

.style_calendar tbody .style_td:hover{
	background-color: #DDFFFF;
}

/* week text color */
.week_sat{
	color :blue;
}
.week_day {
/*	color:black;	*/
}
.week_sun, .week_holiday {
	color :red;
}

/* color.inc.php */
.wikicolor{
	text-shadow: none;
}

/* diff.inc.php */
.diff_added {
	color: blue;
}

.diff_removed {
	color: red;
}

/* new.inc.php */
.new1{
	color: red;
	background-color: transparent;
}
.new5{
	color: green;
	background-color: transparent;
}

/* suckerfish.inc.php / navivar.inc.php */
.sf-menu, #container > .navibar {
	width:			960px;
	background:		url(<?php echo $_SKIN['image_dir'] ?>bg_nav.png) repeat-x left top transparent;
	height:			32px;
	display:		block;
	clear:			both;
	white-space:	nowrap;
	line-height:	1.0;
	font-size:		93%;
	text-shadow:	black 1px 1px 1px;
	margin: 0 auto !important;
	padding: 0;
	color:#CCC;
}
#container > .navibar + hr{
	display: none;
}
#container > .navibar ul:after, #container > .navibar ul:before, #container > .navibar li:after{
	content: ''
}
#container > .navibar ul, #container > .navibar li {
	padding: 0 .5em;
}
.sf-menu  * {
	margin: 0;
	padding: 0;
	list-style: none;
}

.ie6 .sf-menu ul, .ie7 .sf-menu ul, .ie8 .sf-menu ul{
	background:url(<?php echo $_SKIN['image_dir'] ?>bg_nav_ul.png) repeat left top transparent !important;
}

.sf-menu ul{
	border:			1px solid #333;
	background-color:rgba(32, 32, 32, .6);
	width: 175px; /* left offset of submenus need to match (see below) */
}

.sf-menu ul li {
	width: 155px;
}

.sf-menu li:hover ul,
.sf-menu li.sfHover ul {
	left: 0;
	top: 32px; /* match top ul list item height */
}

.sf-menu li li:hover ul,
.sf-menu li li.sfHover ul,
.sf-menu li li li:hover ul,
.sf-menu li li li.sfHover ul {
	left:			192px; /* match ul width */
}

.sf-menu li, #container > .navibar li{
	line-height: 32px;
}
.sf-menu li{
	padding: 0 10px;
}
.sf-menu li li, #nav li li li{
	line-height: 22px;
}

.sf-menu li:hover, .sf-menu li.sfHover, #container > .navibar li:hover{
	background-color: rgba(16, 16, 16, .6);
}

ie6 .sf-menu li:hover, ie6 .sf-menu li.sfHover,
ie7 .sf-menu li:hover, ie7 .sf-menu li.sfHover,
ie8 .sf-menu li:hover, ie8 .sf-menu li.sfHover{
	background: #333;
}

.sf-menu li ul li:hover, .sf-menu li ul li.sfHover,
.sf-menu a:focus, .sf-menu a:hover, .sf-menu a:active {
	text-decoration: none;
}

/* Link color */
.sf-menu a, #container > .navibar a {
	color: white !important;
	text-decoration: none;
}
.sf-menu a:visited, #container > .navibar a:visited { /* visited pseudo selector so IE6 applies text colour*/
	text-decoration: none;
}

.sf-menu a:active, #container > .navibar a:active {
	text-decoration: none;
}

.sf-menu a:hover, #container > .navibar a:hover {
	background-color:transparent;
}
.sf-menu .noexists {
	background-color:transparent;
}

#lastmodified {
	clear:both;
	padding: 0;
	margin: 0;
	font-size: 93%;
}

#preview {
	color:inherit;
}

/* note.inc.php */
#note{
	clear:both;
	padding: 0;
	font-size: 85%;
}

#note ul{
	padding: 0;
	list-style-type:none;
}

/* toolbar.inc.php */

/* attach.inc.php & related.inc.php */
#attach{
	margin-bottom: .2em;
}

#related{
	font-size:93%;
	padding: 0;
	margin:16px 0 0 0;
}

/* vote.inc.php */
.vote_label {
	background-color:#FFCCCC;
}
.vote_td1 {
	background-color:#DDE5FF;
}
.vote_td2 {
	background-color:#EEF5FF;
}

/* tooltip.inc.php */
.tooltip, .linktip{
	color: #006565;
}

.tooltip:hover, .linktip:hover{
	background-color: #e1ffe4;
}

/** Print Setting *********************************************************************************/
@media print {
	body{
		background:white !important;
	}
	#container, #body, #header, #content, #content-top, #content-bottom, #primary-content, #sidebar, #footer{
		width:auto;
		height:auto;
		clear:both;
		display:block;
		margin: 0;
		padding: 0;
	}

	#hgroup h2, .navibar, #menubar, #poptoc, #attach, .toolbar, .sf-menu,
	#shelf, #toggle, #sidebar, #content-top, #content-bottom{
		display:none !important;
		visibility: hidden !important;
	}

	#hgroup, h1, h2, h3, h4, h5, h6{
		margin: 0;
		padding: 1px;
		width: 100%;
	}

	#title h1, #title h1 a{
		color: black;
	}

	#lastmodified{
		text-align: right;
	}

	address{
		float: left;
	}
}
<?php
@ob_end_flush();

/* End of file xxxlogue.ini.php */
/* Location: ./webroot/skin/theme/xxxlogue/xxxlogue.css.php */