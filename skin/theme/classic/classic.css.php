<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: classic.css.php,v 2.6.0 beta 2011/02/21 00:23:30 Logue Exp $

// PukiWiki Classic Skin
// Copyright (C)
//   2010-2011 PukiWiki Advance Developer Team
//   2005-2010 Logue
//   2002-2006 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji

//error_reporting(0); // Nothing
error_reporting(E_ERROR | E_PARSE); // Avoid E_WARNING, E_NOTICE, etc

// Media
$media   = isset($_GET['media'])   ? $_GET['media']	: '';
if ($media != 'print') $media = 'screen';
$debug = false;

// Style
$menubar   = isset($_GET['menubar'])   ? $_GET['menubar']	: '';

// Send header
header('Content-Type: text/css; charset: UTF-8');
?>
/**************************************************************************************************/
/* anchor tag */
a {
	color:#215dc6 !important;
	background-color:inherit;
	text-decoration:none;
}

a:active {
	color:#215dc6 !important;
	background-color:#CCDDEE;
}

a:visited {
	/* color:#a63d21; */
	color:#215dc6;
	background-color:inherit;
	text-decoration:none;
}

a:hover {
	color:#215dc6;
	background-color:#CCDDEE;
	text-decoration:underline;
}

body{
	padding:10px;
}

blockquote{
	padding: 0.5em;
	margin: 0.1em 0.1em 0.1em 0.5em;
	background-color: #F0F8FF;
	border: #CCDDFF 1px solid;
	border-left: #CCDDFF 5px solid;
}

dt{
	font-weight:bold;
}

dd{
	margin-left:1.5em;
	margin-bottom:0.5em;
}

dd{
	margin-left:1em;
}

del{
	color:grey;
}

h1,h2,h3,h4,h5,h6{
	padding:.2em .5em;
	margin:0px 0px .2em 0px;
}

h1, h2 {
	background-color:#DDEEFF;
}

h3 {
	border-bottom:  3px solid #DDEEFF;
	border-top:     1px solid #DDEEFF;
	border-left:   10px solid #DDEEFF;
	border-right:   5px solid #DDEEFF;
	background-color:#FFFFFF;
}

h4 {
	border-bottom:  1px solid #DDEEFF;
	border-left:   18px solid #DDEEFF;
	background-color:#FFFFFF;
}

h5, h6 {
	background-color:#DDEEFF;
}

p, pre, dl{
	margin:0.5em 1.5em;
}

pre {
	padding:0.5em 1em;
	border-width: thin;
	border-style: solid;
	border-color: #DDDDEE #888899 #888899 #DDDDEE;
	background-color:#F0F8FF;
}

q{
	border:1px dotted #999;
}

ul, ol, dl {
	padding-left:1.5em;
}

ul, ol{
	margin:0.5em 2em;
}

fieldset pre{
	margin:0.2em;
}

/** Misc ******************************************************************************************/
.noexists {
	background-color:#FFFACC;
}

/* Table Tags */
thead .style_td,
tfoot .style_td {
	background-color:#D0D8E0;
}
thead .style_th,
tfoot .style_th {
	background-color:#E0E8F0;
}

.style_table{
	background-color:#ccd5dd;
}

.style_th{
	background-color:#EEEEEE;
}

.style_td{
	background-color:#EEF5FF;
}

.style_td_blank{
	background-color:#E3EAF6;
}

/* html.php/catbody() */
.word0 {
	background-color:#FFFF66;
	color:black;
}
.word1 {
	background-color:#A0FFFF;
	color:black;
}
.word2 {
	background-color:#99FF99;
	color:black;
}
.word3 {
	background-color:#FF9999;
	color:black;
}
.word4 {
	background-color:#FF66FF;
	color:black;
}
.word5 {
	background-color:#880000;
	color:white;
}
.word6 {
	background-color:#00AA00;
	color:white;
}
.word7 {
	background-color:#886800;
	color:white;
}
.word8 {
	background-color:#004699;
	color:white;
}
.word9 {
	background-color:#990099;
	color:white;
}

#poptoc {
	border:gray thin outset;
	background-color:lightyellow;
	max-width:25em;
	min-width:18em;
	opacity:0.9;
	overflow:visible;
	padding:0.5em;
	position:absolute;
	text-align:left;
	text-shadow: white 1px 1px 0px;
	width:22em;
	z-index:1;
}

.ie6 #poptoc, .ie7 #poptoc, .ie8 #poptoc {
	filter:
		progid:DXImageTransform.Microsoft.Alpha(opacity=90)
		progid:DXImageTransform.Microsoft.Gradient(GradientType=0,StartColorStr=ivory,EndColorStr=lemonchiffon) !important;
}

.webkit #poptoc{
	background: -webkit-gradient(linear, left top, left bottom, from(ivory), to(lemonchiffon));
}

.gecko #poptoc{
	-moz-opacity:0.9;
	background: -moz-linear-gradient(top, ivory, lemonchiffon);
}

.presto #poptoc{
	background-image:url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZlcnNpb249IjEuMCI%2BDQo8ZGVmcz4NCjxsaW5lYXJHcmFkaWVudCBpZD0iZ3JhZGllbnQiIHgxPSIwIiB5MT0iMCIgeDI9IjAiIHkyPSIxMDAlIj4NCjxzdG9wIG9mZnNldD0iMCUiIHN0eWxlPSJzdG9wLWNvbG9yOml2b3J5OyIvPg0KPHN0b3Agb2Zmc2V0PSIxMDAlIiBzdHlsZT0ic3RvcC1jb2xvcjpsaWdodHllbGxvdzsiLz4NCjwvbGluZWFyR3JhZGllbnQ%2BDQo8L2RlZnM%2BDQo8cmVjdCB4PSIwIiB5PSIwIiBmaWxsPSJ1cmwoI2dyYWRpZW50KSIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgLz4NCjwvc3ZnPg0K');
}
.ie9 #poptoc {
	-ms-filter: "progid:DXImageTransform.Microsoft.Gradient(GradientType=0,StartColorStr=ivory,EndColorStr=lemonchiffon)" !important;
}

#poptoc a{
	color:blue !important;
	cursor:pointer; 
}
#poptoc a:hover{
	text-shadow: none;
	background-color:#ccc;
}
#poptoc h1{
	color:navy;
	background-color:honeydew;
	font-size:small;
	font-weight:normal;
	padding:0.3em;
	margin:0;
	text-align:center;
	border:silver solid 1px;
	display:block;
}
#poptoc h1 a{color:navy; text-decoration:none;}
#poptoc h1 img {margin-bottom:-3px; margin-right: 2px;}
#poptoc .nav {text-indent:0em;border-top:1px gray solid; padding-top:0.2em;text-align:center; white-space: nowrap; }
#poptoc a.here{color: black; background: #EEEEEE; text-decoration: none; border:1px dotted gray;}

.tocpic {
	display:inline;
	cursor:pointer;
}

.hrefp, .topic {
	vertical-align:text-bottom;
}

/** Skin Stylesheet *******************************************************************************/
#title h1, #title h2{
	background-color:transparent;
	margin:0px;
}

#title h1, #title h1 a {
	font-family: "Lucida Bright", Century, "Times New Roman", serif;
}
:lang(ja) #title h1, :lang(ja) #title h1 a {
	font-family: 'ヒラギノ明朝 Pro W6', 'Hiragino Mincho Pro W6', 'HGP明朝E', '平成明朝', 'ＭＳ Ｐ明朝', 'MS PMincho' !important;
}
:lang(ko) #title h1, :lang(ko) #title h1 a {
	font-family: '바탕체', 'Batang' !important;
}
:lang(zh) #title h1, :lang(zh) #title h1 a {
	font-family: 'STSong', 'STFangsong', 'NSimSun', 'SimSun', 'FangSong', '細明體', '宋体' !important;
}

#base{
	margin-right: auto;
	margin-left: auto;
}

#header {
	display:block;
}

/* headarea extend */
#header .style_table
{
	background-color: transparent;
}

#header .style_table .style_th
{
	margin: 0px;
	padding: 0px;
	background-color: transparent;
}

#header .style_table .style_td
{
	margin: 0px;
	padding: 0px;
	background-color: transparent;
}

#logo {
	float:left;
	margin-right:20px;
}

#title {
	display:block;
	float:left;
	vertical-align: baseline;
	margin:1.5% 0px 0px 0px;
}

#title h1, #title h2 {
	background-color:transparent;
	line-height:126%;
	padding: 0px;
	border: 0px;
}
/* title */
#title h1 {
	font-size: 242.2%;
	font-weight:bold;
}

#title h2 {
	font-weight:normal;
	font-size: 85%;
}

#lastmodified {
	clear:both;
	font-size:83%;
	float:right;
}

#navigator {
	display:block;
	clear:both;
	padding:4px 0px 0px 0px;
	margin:0px;
}

#main{
	padding:5px;
}


#ad {
	float:right;
	margin:8px;
}

#menubar {
	word-break:break-all;
	font-size:93%;
	overflow:hidden;
	width:19%;
<?php   if ($menubar == 'right') { ?>
	float:right;
<?php   } else { ?>
	float:left;
<?php   } ?>
}

#menubar ul {
	margin:0px 0px 0px .5em;
	padding:0px 0px 0px .5em;
}

#menubar ul li { line-height:110%; }

#menubar h4 { font-size:114%; }

#body {
	width:80%;
<?php   if ($menubar == 'right') { ?>
	float:left;
<?php   } else { ?>
	float:right;
<?php   } ?>
}

#note {
	padding:0px;
	margin:0px;
}

#footer{
	clear:both;
}

#footer .style_table
{
	background-color: transparent;
}

#footer .style_table .style_th
{
	margin: 0px;
	padding: 0px;
	background-color: transparent;
}

#footer .style_table .style_td
{
	margin: 0px;
	padding: 0px;
	background-color: transparent;
}

#qr_code{
	display:block;
	float:left;
}

#banner_box{
	display:block;
	float:right;
	white-space:nowrap;
}

#banner_box a:hover{
	background-color:transparent !important;
	text-decoration:none;
}

#sigunature{
	float:left;
	white-space:nowrap;
	font-size:77%;
}

/** Plugin Stylesheet ****************************************************************************/

/* attach.inc.php & related.inc.php */
#attach{
	margin-bottom:0.2em;
}

#related{
	font-size:93%;
}

/* backup.inc.php */
.add_word, .add_block{
	background-color:#FFFF66;
}

.remove_word, .remove_block{
	background-color:#A0FFFF;
}

/* calendar.inc.php */
.style_calendar {
	background-color:#CCD5DD;
}
.style_calendar_top {
	background-color:#EEF5FF !important;
}
.style_calendar_today {
	background-color:#FFFFDD;
}
.style_calendar_sat {
	background-color:#DDE5FF;
}
.style_calendar_sun, .style_calendar_holiday {
	background-color:#FFEEEE;
}
.style_calendar_blank {
	
}
.style_calendar_day {
	
}
.style_calendar_week {
	background-color:#DDE5EE;
}

.style_calendar tbody .style_td:hover{
	background-color: #DDFFFF;
}

/* week text color */
.week_sat{
	color:blue;
}
.week_day {
/*	color:black;	*/
}
.week_sun, .week_holiday {
	color:red;
}

/* color.inc.php */
.wikicolor{
	text-shadow:none;
}

/* diff.inc.php */
.diff_added {
	color:blue;
}

.diff_removed {
	color:red;
}

/* new.inc.php */
.new1{
	color:red;
	background-color:transparent;
}
.new5{
	color:green;
	background-color:transparent;
}

/* note.inc.php */
#note{
	clear:both;
	padding:0px;
	font-size: 85%;
}

#note ul{
	padding:0px;
	list-style-type:none;
}

/* suckerfish.inc.php */
.sf-menu {
	/* These settings are recommended to specify an absolute value. */
	font-size:13px !important;
	line-height:16px !important;
	height:24px;
	margin: .5em 0em;
	
	color: white !important;
	
	text-shadow:none;
	width: 100%;
	
	clear:both;
	white-space: nowrap;
	border: 1px RoyalBlue solid;
	background-color: CornflowerBlue;
}

.sf-menu ul{
	background-color: CornflowerBlue;
	border: 1px RoyalBlue solid;
	width: 171px; /* left offset of submenus need to match (see below) */
}

.sf-menu ul li {
	width: 12em;
}

.sf-menu li:hover ul,
.sf-menu li.sfHover ul {
	left:			-1px;
	top:			23px; /* match top ul list item height */
}

.sf-menu li li:hover ul,
.sf-menu li li.sfHover ul,
.sf-menu li li li:hover ul,
.sf-menu li li li.sfHover ul {
	left:			12em; /* match ul width */
}

.sf-menu li {
	border: 1px CornflowerBlue solid;
	padding: 2px 0.5em;
}

.sf-menu li:hover, .sf-menu li.sfHover{
	background-color: lightsteelblue;
	border: 1px white solid;
}

.sf-menu li ul li:hover, .sf-menu li ul li.sfHover,
.sf-menu a:focus, .sf-menu a:hover, .sf-menu a:active {
	color: midnightblue !important;
	background-color: lightsteelblue;
	text-decoration: none;
}

/* Link color */
.sf-menu a {
	color: white !important;
	font-weight: bold;
	text-decoration: none;
}
.sf-menu a:visited  { /* visited pseudo selector so IE6 applies text colour*/
	text-decoration: none;
}

.sf-menu a:active  {
	text-decoration: none;
}

.sf-menu .noexists {
	color:white;
	background-color:transparent;
}

/* toolbar.inc.php */
#toolbar{
	display:block;
	clear:both;
	padding:0px;
	margin:0px 1%;
	float:right;
	white-space: nowrap;
}

#toolbar .pkwk-icon, #toolbar .pkwk-icon_splitter{
	float:left;
}

/* tooltip.inc.php */
.tooltip, .linktip{
	color: teal;
}

.tooltip:hover, .linktip:hover{
	background-color: honeydew;
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

/** Print Setting *********************************************************************************/
@media print {
	#title h2, #header img, #navigator, #menubar, #poptoc, #banner_box, #attach, #toolbar,
	#sigunature{
		display:none !important;
		visibility: hidden !important;
	}

	#title{
		float:none;
		clear:both;
	}

	#lastmodified{
		text-align:right;
	}

	address{
		float:left;
	}

	#body{
		clear:both;
		display:block;
		width:100%;
	}
	
	#footer{
		clear:both;
	}
}