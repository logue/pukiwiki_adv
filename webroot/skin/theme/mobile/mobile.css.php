<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: classic.css.php,v 0.0.1 2012/01/08 10:06:30 Logue Exp $
// 
// PukiWiki Adv. Mobile Theme
// Copyright (C)
//   2012 PukiWiki Advance Developer Team

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
@charset "UTF-8";
@namespace url(http://www.w3.org/1999/xhtml);

/* HTML5 ✰ Boilerplate
 * ==|== normalize ==========================================================
 */

article, aside, details, figcaption, figure, footer, header, hgroup, nav, section { display: block; }
audio, canvas, video { display: inline-block; *display: inline; *zoom: 1; }
audio:not([controls]) { display: none; }
[hidden] { display: none; }

html { font-size: 100%; overflow-y: scroll; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
body { margin: 0; font-size: 13px; line-height: 1.4; }
body, button, input, select, textarea { font-family: sans-serif; color: #222; }

::-moz-selection { background: #57a1fe; color: #fff; text-shadow: none; }
::selection { background: #57a1fe; color: #fff; text-shadow: none; }

a { color: #00e; }
a:visited { color: #551a8b; }
a:hover { color: #06e; }
a:focus { outline: thin dotted; }
a:hover, a:active { outline: 0; }

abbr[title] { border-bottom: 1px dotted; }
b, strong { font-weight: bold; }
blockquote { margin: 1em 40px; }
dfn { font-style: italic; }
hr { display: block; height: 1px; border: 0; border-top: 1px solid #ccc; margin: 1em 0; padding: 0; }
ins { background: #ff9; color: #000; text-decoration: none; }
mark { background: #ff0; color: #000; font-style: italic; font-weight: bold; }
pre, code, kbd, samp { font-family: monospace; _font-family: 'courier new', monospace; font-size: 1em; }
pre { white-space: pre; white-space: pre-wrap; word-wrap: break-word; }
q { quotes: none; }
q:before, q:after { content: ""; content: none; }
small { font-size: 85%; }
sub, sup { font-size: 75%; line-height: 0; position: relative; vertical-align: baseline; }
sup { top: -0.5em; }
sub { bottom: -0.25em; }
ul, ol { margin: 1em 0; padding: 0 0 0 40px; }
dd { margin: 0 0 0 40px; }
nav ul, nav ol { list-style: none; list-style-image: none; margin: 0; padding: 0; }
img { border: 0; -ms-interpolation-mode: bicubic; vertical-align: middle; }
svg:not(:root) { overflow: hidden; }
figure { margin: 0; }

form { margin: 0; }
fieldset { border: 0; margin: 0; padding: 0; }
label { cursor: pointer; }
legend { border: 0; *margin-left: -7px; padding: 0; }
button, input, select, textarea { font-size: 100%; margin: 0; vertical-align: baseline; *vertical-align: middle; }
button, input { line-height: normal; }
button, input[type="button"], input[type="reset"], input[type="submit"] { cursor: pointer; -webkit-appearance: button; *overflow: visible; }
input[type="checkbox"], input[type="radio"] { box-sizing: border-box; }
input[type="search"] { -webkit-appearance: textfield; -moz-box-sizing: content-box; -webkit-box-sizing: content-box; box-sizing: content-box; }
input[type="search"]::-webkit-search-decoration { -webkit-appearance: none; }
button::-moz-focus-inner, input::-moz-focus-inner { border: 0; padding: 0; }
textarea { overflow: auto; vertical-align: top; resize: vertical; }
input:valid, textarea:valid {  }
input:invalid, textarea:invalid { background-color: #f0dddd; }

table { border-collapse: collapse; border-spacing: 0; }
td { vertical-align: top; }

/* ==|== PukiWiki Advance Standard Font Set ================================= */

pre, code, kbd, samp, textarea, select, option, input, var{
	font-family: monospace !important;
}
dt{
	font-weight:bold;
}

figure{
	margin: 0 auto;
	text-align:center;
}

figcaption{
	font-size:93%;
	text-align:center;
}

pre{
	border-top:silver 1px solid;
	border-bottom:grey 1px solid;
	border-left:silver 1px solid;
	border-right:grey 1px solid;
	background-color:whitesmoke;
	padding:0.5em 1em;
	margin:.5em;
	text-shadow: 1px 1px 3px rgba(0,0,0,.3);
}

q{
	border:1px dotted #999;
}

/* Table Tags */
.style_table{
	border-spacing:2px;
	padding:0px;
	border:0px;
	margin:0.1em auto;
	text-align:left;
	border-collapse:separate;
	border-spacing:1px;
	background-color:silver;
	text-shadow:none;
	text-shadow: none;
}

.style_th{
	background-color:lightgrey;
	padding:5px;
	margin:1px;
	text-align:center;
}
.style_td{
	background-color:whitesmoke;
	padding:5px;
	margin:1px;
}

.style_td_blank{
	background-color:gainsboro;
}
/* Week and Month */
.style_week, .style_month{
	border:none !important;
}

/* ==|== PukiWiki Adv. Misc classes ========================================= */
.underline{
	text-decoration: underline !important;
}
.small1{
	font-size:77%;
}
.super_index{
	font-weight:bold;
	font-size:77%;
	vertical-align:super;
}
.jumpmenu{
	font-size:77%;
	text-align:right;
}

.size1 {
	font-size:xx-small;
}
.size2 {
	font-size:x-small;
}
.size3 {
	font-size:small;
}
.size4 {
	font-size:medium;
}
.size5 {
	font-size:large;
}
.size6 {
	font-size:x-large;
}
.size7 {
	font-size:xx-large;
}

/* html.php/catbody() */
.word0 {
	background-color:#FFFF66;
	text-shadow:none;
	color:black;
}
.word1 {
	background-color:#A0FFFF;
	text-shadow:none;
	color:black;
}
.word2 {
	background-color:#99FF99;
	text-shadow:none;
	color:black;
}
.word3 {
	background-color:#FF9999;
	text-shadow:none;
	color:black;
}
.word4 {
	background-color:#FF66FF;
	text-shadow:none;
	color:black;
}
.word5 {
	background-color:#880000;
	text-shadow:none;
	color:white;
}
.word6 {
	background-color:#00AA00;
	text-shadow:none;
	color:white;
}
.word7 {
	background-color:#886800;
	text-shadow:none;
	color:white;
}
.word8 {
	background-color:#004699;
	text-shadow:none;
	color:white;
}
.word9 {
	background-color:#990099;
	text-shadow:none;
	color:white;
}

/* html.php/edit_form() */
.edit_form { clear:both; }

.edit_form textarea{
	width:95%;
	min-width:99%;
	resize: vertical;
	margin:0;
}

.ie8 .edit_form textarea{
	width:780px;
}

/* Note */
.super_index {
	color:red;
	font-weight:bold;
	vertical-align:super;
	line-height:100%;
}

.note_super {
	font-size:77% !important;
	color:red !important;
	font-weight:bold;
	vertical-align:super;
	margin-right:.5em;
}

/* ==|== PukiWiki Adv. Standard Plugin classes ============================== */
/* aname.inc.php */
.anchor_super {
	height:8px;
	font-size:8px !important;
	vertical-align:super;
}

/* amazon.inc.php */
.amazon_img {
	margin:16px 10px 8px 8px;
	text-align:center;
}
.amazon_imgetc {
	margin:0px 8px 8px 8px;
	text-align:center;
}
.amazon_sub {
	font-size:93%;
}
.amazon_avail {
	margin-left:150px;
	font-size:93%;
}
.amazon_td {
	text-align:center;
	word-wrap: break-word;
}
.amazon_tbl {
	width:160px;
	font-size:93%;
	text-align:center;
}

/* attach.inc.php / related.inc.php */
#attach, #related{
	display:block;
	float:none;
}
#attach dl, #related dl{
	margin:0 1%;
	display:block;
}
#attach dl dt, #related dl dt{
	display:inline;
	font-weight:normal;
	margin: .1em .25em;
}
#attach dl dd, #related dl dd{
	display:inline;
	margin: .1em .25em;
}
.attach_info dl{
	float:left;
	display:block;
	overflow:visible;
}
.attach_info_image{
	right:1em;
	position:absolute;
	z-index:-1;
}

/* backup.inc.php */
.add_block, .remove_block{
	display:block;
}
.backup_form{
	text-align:center;
}
.add_word, .add_block{
	background-color:#FFFF66;
}
.remove_word, .remove_block{
	background-color:#A0FFFF;
}

/* calendar.inc.php */
.style_calendar{
	width:13em;
}
.style_calendar a{
	text-decoration:none;
}
.style_calendar a:hover{
	background-color:transparent;
}
.style_calendar a strong{
	text-decoration:underline;
}
.style_calendar td, .style_calendar th{ 
	text-align:center;
	font-size:85%;
}
.style_calendar_navi{
	display:block;
	text-align:center;
	list-style-image: none;
	list-style:none;
	margin: 0;
	padding:0;
}
.style_calendar_title {
	display:inline;
	float:none;
}
.style_calendar_prev {
	display:inline;
	float:left;
	text-align:left;
}
.style_calendar_next {
	display:inline;
	float:right;
	text-align:right;
}
/* week text color */
.week_sat{
	color:blue;
}
.week_day {
	color:black;
}
.week_sun, .week_holiday {
	color:red;
}
/* Fix English calendar week label */
:lang(en) .style_calendar_week{
	font-family:monospace !important;
	padding:.5em .2em;
}
.style_calendar_post p{
	padding: .25em 1em;
}
.style_calendar_post nav{
	display:none;
}

/* calendar_viewer.inc.php */
.style_calendar_viewer{
	float:left;
	width:15em;
}
.style_calendar_post{
	margin-left: 15em;
}

/* color.inc.php */
.wikicolor{
	text-shadow:none;
}

/* counter.inc.php */
.counter {
	font-size:77%;
}

/* diff.inc.php */
.diff_added {
	color:blue;
}
.diff_removed {
	color:red;
}

/* include.inc.php */
.side_label {
	text-align:center;
}

/* navi.inc.php */
.navi {
	display:block;
	list-style-image: none;
	list-style:none;
	margin: 0 !important;
	padding:0 !important;
	text-align:center;
}
.navi_none {
	display:inline;
	float:none;
}
.navi_left {
	display:inline;
	float:left;
	text-align:left;
}
.navi_right {
	display:inline;
	float:right;
	text-align:right;
}

/* new.inc.php */
.comment_date {
	font-size:x-small;
}
.new1 {
	color:red;
	background-color:transparent;
	font-size:x-small;
}
.new5 {
	color:green;
	background-color:transparent;
	font-size:xx-small;
}

/* note */
#note ul{
	list-style:none;
}

/* ref.inc.php */
.img_margin {
	margin: 0 32px;
}

/* clear.inc.php */
.clear {
	margin:0px;
	clear:both;
}

/* counter.inc.php */
.counter { font-size:77%; }

/* tooltip.inc.php */
.tooltip, .linktip {
	border-bottom: 1px dotted;
}

/* vote.inc.php */
.vote_table{
	vertical-align:middle;
}
.vote_label {
	background-color:#FFCCCC;
}
.vote_td1 {
	background-color:#DDE5FF;
}
.vote_td2 {
	background-color:#EEF5FF;
}

/* ==|== jQuery Mobile Override classes ============================== */
.content-secondary .ui-listview {
	margin : 0 -15px !important;
}
@media all and (min-width: 650px){
	.two-colums{
		margin:0;
		padding:0 !important;
	}
	
	.two-colums .content-secondary {
		border-right:1px #CCC solid;
		text-align: left;
		float: left;
		width: 30%;
		background: none;
	}
	.two-colums .content-primary {
		width: 69%;
		float: right;
		margin:-10px;
	}
	/* fix up the collapsibles - expanded on desktop */
	.two-colums .content-secondary .ui-collapsible {
		margin: 0;
		padding: 0;
	}
	.two-colums .content-secondary .ui-collapsible-heading {
		display: none;
	}
	.two-colums .content-secondary .ui-collapsible-contain {
		margin:0;
	}
	.two-colums .content-secondary .ui-collapsible-content {
		display: block;
		margin: 0;
		padding: 0;
	}
	.two-colums .type-interior .content-secondary .ui-li-divider {
		padding-top: 1em;
		padding-bottom: 1em;
	}
	.two-colums .type-interior .content-secondary {
		margin: 0;
		padding: 0;
	}
	.two-colums .content-secondary .ui-listview {
		margin : 0 !important;
	}
}
