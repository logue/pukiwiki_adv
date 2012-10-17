<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: classic.css.php,v 2.6.3 2012/03/11 22:26:30 Logue Exp $

// PukiWiki Classic Skin
// Copyright (C)
//   2010-2012 PukiWiki Advance Developer Team
//   2005-2010 Logue
//   2002-2006 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji

//error_reporting(0); // Nothing
error_reporting(E_ERROR | E_PARSE); // Avoid E_WARNING, E_NOTICE, etc
ini_set('zlib.output_compression', 'Off');
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
/**************************************************************************************************/
/* anchor tag */
a {
	color:#215dc6;
	background-color:inherit;
	text-decoration:none;
}

a:active {
	color:#215dc6 !important;
}

a:visited {
	color:#a63d21;
	/* color:#215dc6; */
}

a:hover {
	color:#215dc6;
	background-color:#CCDDEE;
	text-decoration:underline;
}

.ui-widget a{
	color:#215dc6 !important;
}

body{
	padding:10px;
}

blockquote{
	padding: 0.5em;
	margin: 0.1em 0.1em 0.1em 1.5em;
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

del{
	color:grey;
}

h1,h2,h3,h4,h5,h6{
	padding:.2em .5em;
	margin: 0 0 .2em 0;
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

p, pre, dl, form, .table_wrapper{
	margin:.5em 1.5em;
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

.style_table tr:nth-child(even) .style_td{
	background-color:#EFf5ff;
}

/** Skin Stylesheet *******************************************************************************/
#hgroup h1, #hgroup h2{
	background-color:transparent;
	margin:0px;
}

#title, #title a {
	font-family: "Lucida Bright", Century, "Times New Roman", serif;
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
	margin: 0;
	padding: 0;
	background-color: transparent;
}

#header .style_table .style_td
{
	margin: 0;
	padding: 0;
	background-color: transparent;
}

#logo {
	float:left;
	margin-right:20px;
}

/* title */
#hgroup {
	display:block;
	float:left;
	margin:1.5% 0 0 0;
}

#hgroup h1 {
	font-size: 246.2%;
	font-weight:bold;
	padding:0;
}

#hgroup h2, #hgroup .topicpath {
	font-weight:normal;
	font-size: 93%;
	display:block;
}

#lastmodified {
	clear:both;
	font-size:83%;
	float:right;
}

.navibar {
	display:block;
	clear:both;
	padding:4px 0 0 0;
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
	margin: 0 0 0 .5em;
	padding: 0 0 0 .5em;
}

#menubar ul li { line-height:110%; }

#menubar h4 { font-size:114%; }

#content {
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
	margin: 0;
	padding: 0;
	background-color: transparent;
}

#footer .style_table .style_td
{
	margin: 0;
	padding: 0;
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
	background-color:#CCD5DD !important;;
}
.style_calendar_top {
	background-color:#EEF5FF !important;
}
.style_calendar_today {
	background-color:#FFFFDD !important;;
}
.style_calendar_sat {
	background-color:#DDE5FF !important;;
}
.style_calendar_sun, .style_calendar_holiday {
	background-color:#FFEEEE !important;;
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

.sf-menu a:active  {
	text-decoration: none;
}

.sf-menu .noexists {
	color:white;
	background-color:transparent;
}

/* toolbar.inc.php */
.toolbar{
	display:block;
	clear:right;
	padding:0px;
	margin:0px 1%;
	float:right;
	white-space: nowrap;
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

.social li{
	display:inline;
}

/** Print Setting *********************************************************************************/
@media print {
	#hgroup h2, #header img, #navibar, #menubar, #poptoc, #banner_box, #attach, #toolbar,
	#sigunature{
		display:none !important;
		visibility: hidden !important;
	}

	#hgroup{
		float:none;
		clear:both;
		width:100%;
	}

	#lastmodified{
		text-align:right;
	}

	address{
		float:left;
	}

	#content{
		clear:both;
		display:block;
		width:100%;
	}

	#footer{
		clear:both;
	}
}
<?php
@ob_end_flush();
/* End of file classic.css.php */
/* Location: ./webroot/skin/theme/classic/classic.css.php */