<?php
// PukiWiki - Yet another WikiWikiWeb clone.
//
// PukiWiki original skin "Twilight" v 1.0
// by fuyuka88 < https://github.com/fuyuka88 >
//
// based PukiWiki default skin
// Copyright (C)
//   2002-2005 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
//
// License: GPL v2 or (at your option) any later version
//

// Send header
header('Content-Type: text/css');
// Colors
$rgb_base1 = 'rgb(62,66,64)';			// Header,h1~3,h5,6
$rgb_base2 = 'rgb(224,214,204)';				// navi,h4
$rgb_background = 'rgb(254,255,244)';	// Background
$rgb_link1 = 'rgb(92,188,13)';				// link
$rgb_link2 = 'rgb(234,118,2)';				// link(visited)
$rgb_main_font = 'rgb(62,66,64)';			// Main font color
$rgb_sub_font = 'rgb(254,255,244)';			// Sub font color(h1 etc.)


// Output CSS ----
?>
@charset "UTF-8";

body {
	color: <?php echo $rgb_main_font?>;
	background-color:<?php echo $rgb_background?>;
}

a:not(.btn):not([role="presentation"]):link {
	color: <?php echo $rgb_link1?>;
	text-decoration:none;
}

a:not(.btn):not([role="presentation"]):active {
	color:<?php echo $rgb_link1?>;
	text-decoration:none;
}

a:not(.btn):not([role="presentation"]):visited {
	color:<?php echo $rgb_link2?>;
	text-decoration:none;
}

a:not(.btn):not([role="presentation"]):hover {
	text-decoration:underline;
}

#header a:not(.btn):not([role="presentation"]){
	color: <?php echo $rgb_sub_font?>;
}
#navigator a:not(.btn):not([role="presentation"]){
	color: <?php echo $rgb_main_font?>;
}
#bodyNav a:not(.btn):not([role="presentation"]){
	color: <?php echo $rgb_sub_font?>;
}
h1 a,h2 a:not(.btn):not([role="presentation"]){
	color: <?php echo $rgb_sub_font?>;
}

h1, h2, h3, h4, h5, h6 {
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
}

h1, h2 {
	font-size: 1.2em;
	color: <?php echo $rgb_sub_font?>;
	background-color: <?php echo $rgb_base1?>;
	padding:.3em;
	border:0px;
	margin:0px 0px .5em 0px;
}
h3 {
	font-size: 1em;
	border-left: 18px solid <?php echo $rgb_base1?>;
	color:inherit;
	padding:.3em;
	margin:0px 0px .5em 0px;
}
h4 {
	border-left: 18px solid <?php echo $rgb_base2?>;
	color:inherit;
	padding:.3em;
	margin:0px 0px .5em 0px;
}
h5, h6 {
	color: <?php echo $rgb_sub_font?>;
	background-color: <?php echo $rgb_base1?>;
 	padding:.3em;
 	border: 0;
 	margin:0px 0px .5em 0px;
}

h1.title {
	font-size: 20px;
	font-weight:bold;
	color: <?php echo $rgb_sub_font?>;
	background-color:transparent;
	padding: 50px 0 0 0;
	border: 0;
	margin: 0;
}
h2.title {
	font-size: 16px;
	font-weight: normal;
	color: <?php echo $rgb_sub_font?>;
	background-color:transparent;
	padding: 5px 0;
	margin: 0;
}

dt {
	font-weight:bold;
	margin-top:1em;
	margin-left:1em;
}

pre {
	border-top:#DDDDEE 1px solid;
	border-bottom:#888899 1px solid;
	border-left:#DDDDEE 1px solid;
	border-right:#888899 1px solid;
	padding:.5em;
	margin-left:1em;
	margin-right:2em;
	white-space:pre;
	color:black;
	background-color:#F0F8FF;
}

p {
	margin: 10px 15px;
}

.table {
	background-color:#ccd5dd;
}
.table th {
	background-color:#EEEEEE;
}
.table td {
	background-color:#EEF5FF;
}
.table thead td,
.table tfoot td {
	color:inherit;
	background-color:#D0D8E0;
}
.table thead th,
.table tfoot th {
	color:inherit;
	background-color:#E0E8F0;
}

.noexists {
	background-color:#FFFACC;
}

.super_index {
	color:#DD3333;
}

.note_super {
	color:#DD3333;
}


/* pukiwiki.skin.php */
#header {
	height: 150px;
	background-color: <?php echo $rgb_base1?>;
	padding: 0 20px;
	margin: 0;
}

#navigator {
	clear:both;
	color: <?php echo $rgb_main_font?>;
	background-color:<?php echo $rgb_base2?>;
	padding: 4px 20px 0;
	margin:0px;
}

#menubar {
	width: 200px;
	padding: 20px;
	margin: 0;
	word-break:break-all;
	font-size:90%;
	overflow:hidden;
	float:left;
}

#body {
	padding: 20px;
	border-left: 1px solid <?php echo $rgb_base2 ?>;
	margin: 0 0 0 240px;
}
#body.nonColumn{
	padding: 20px;
	margin: 0;
	float:none;
}

#bodyNav {
	background-color: <?php echo $rgb_base1 ?>;
	color: <?php echo $rgb_sub_font?>;
	padding: 4px 10px 0;
	margin: 0 0 20px 0;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
}

#note {
	background-color: <?php echo $rgb_base2?>;
}

#attach {
	background-color: <?php echo $rgb_base2?>;
}

#toolbar {
	background-color: <?php echo $rgb_base1?>;
	color: <?php echo $rgb_sub_font?>;
}

#lastmodified {
	background-color: <?php echo $rgb_base1?>;
	color: <?php echo $rgb_sub_font?>;
}

#related {
	background-color: <?php echo $rgb_base1?>;
	color: <?php echo $rgb_sub_font?>;
}

#footer {
	background-color: <?php echo $rgb_base1?>;
	color: <?php echo $rgb_sub_font?>;
}

#banner {
	float:right;
	margin-top:24px;
}

#preview {
	color:inherit;
	background-color:#F5F8FF;
}

#logo {
	float:left;
	margin-right:20px;
}

#pkwk-info .panel-body:after{
	display:block;
	content: none;
}

/* calendar*.inc.php */
.style_calendar {
	padding:0px;
	border:0px;
	margin:3px;
	color:inherit;
	background-color:#CCD5DD;
	text-align:center;
}
.style_td_caltop {
	padding:5px;
	margin:1px;
	color:inherit;
	background-color:#EEF5FF;
	font-size:80%;
	text-align:center;
}
.style_td_today {
	padding:5px;
	margin:1px;
	color:inherit;
	background-color:#FFFFDD;
	text-align:center;
}
.style_td_sat {
	padding:5px;
	margin:1px;
	color:inherit;
	background-color:#DDE5FF;
	text-align:center;
}
.style_td_sun {
	padding:5px;
	margin:1px;
	color:inherit;
	background-color:#FFEEEE;
	text-align:center;
}
.style_td_blank {
	padding:5px;
	margin:1px;
	color:inherit;
	background-color:#EEF5FF;
	text-align:center;
}
.style_td_day {
	padding:5px;
	margin:1px;
	color:inherit;
	background-color:#EEF5FF;
	text-align:center;
}
.style_td_week {
	padding:5px;
	margin:1px;
	color:inherit;
	background-color:#DDE5EE;
	font-size:80%;
	font-weight:bold;
	text-align:center;
}

