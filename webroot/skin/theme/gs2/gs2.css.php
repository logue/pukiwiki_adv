<?php
// PukiWiki - Yet another WikiWikiWeb clone.
//
// PukiWiki original skin "GS2" 1.5.3
//     by yiza < http://www.yiza.net/ >

// Send header
header('Content-Type: text/css');
// Load settiings


// Color settings
$gs2_color = isset($_GET['gs2color']) ? $_GET['gs2color'] : 'blue';
require ('./gs2_color/pukiwiki_gs2_color_'.$gs2_color.'.php');

// Output CSS ----
?>
@charset "UTF-8";
blockquote { margin-left:32px; }

body {
	margin:0%;
	padding:1%;
	letter-spacing:1px;
}

td, th {
	letter-spacing:1px;
}

h1, h2 {
	font-size:150%;
	padding:3px;
	border-style:solid;
	border-width:3px 3px 6px 20px;
	margin:0px 0px 5px 0px;
}
h3 {
	font-size:140%;
	padding:3px;
	border-style: solid;
	border-width: 1px 1px 5px 12px;
	margin:0px 0px 5px 0px;
}
h4 {
	font-size:130%;
	padding:3px;
	border-style: solid;
	border-width: 0px 6px 1px 7px;
	margin:0px 0px 5px 0px;
}
h5 {
	font-size:120%;
	padding:3px;
	border-style: solid;
	border-width: 0px 0px 1px 6px;
	margin:0px 0px 5px 0px;
}

h6 {
	font-size:110%;
	padding:3px;
	border-style: solid;
	border-width: 0px 5px 1px 0px;
	margin:0px 0px 5px 0px;
}


dt {
	font-weight:bold;
	margin-top:1em;
	margin-left:1em;
}

pre {
	padding:.5em;
	margin-left:1em;
	margin-right:2em;
	white-space:pre;
	word-break:break-all;
	letter-spacing:0px;
}

img {
	border:none;
	vertical-align:middle;
}

ul {
	margin:0px 0px 0px 6px;
	padding:0px 0px 0px 10px;
	line-height:160%;
}

li {
	margin: 3px 0px;
}
/** Layout ****************************************************************************************/
#container {
	width:100%;
	margin: 0 auto;
}

#main > *{
	padding:5px;
}

#menubar, #sidebar{
	display:none;
}
@media screen{
	.two-colums #main {
		width:100%;
		float:right;
		margin-left:-180px;
	}

	.two-colums #main > * {
		margin:0 0 0 190px;
	}

	.two-colums #menubar {
		display:block;
		width:180px;
		float:left;
	}

	.three-colums #wrapper {
		width:100%;
		float:left;
		margin-right:-180px;
	}

	.three-colums #main {
		width:100%;
		float:right;
		margin-left:-180px;
	}

	.three-colums #main > * {
		margin:0 190px;
		padding:5px;
	}

	.three-colums #menubar {
		display:block;
		width:180px;
		float:right;
	}

	.three-colums #sidebar {
		display:block;
		width:180px;
		float:left;
		right:0;
		position:absolute;
	}

	.three-colums #misc {
		width:100%;
		clear:left;
	}
}

#header {
	padding:5px;
	margin:0px 0px 10px 0px;
}

	#logo {
		float:left;
		margin-right:20px;
	}

	#hgroup {
		font-weight: bold;
		letter-spacing: 3px;
		border-style: solid;
		border-width: 2px 4px 4px 2px;
		margin: 5px 5px 5px 90px;
/*
<?php //  if (PKWK_SKIN_SHOW_LOGO == 1) { ?>
		margin: 5px 5px 5px <?php echo( PKWK_SKIN_LOGO_WIDTH + 10 ); ?>px;
<?php  // } else { ?>
		margin: 5px 5px 5px 5px;
<?php //  } ?>
*/
	}
	
	#hgroup h1, #hgroup h2{
		border:none;
		background-color:transparent;
	}

	#hgroup h1{
		font-size: 220%;
	}
	#hgroup h2{
		font-size:93%;
	}

	.search_form
	{
		font-size: 85%;
		padding:2px 8px;
		margin:0px;
		float:right;
	}

	.navibar {
		font-size: 93%;
		padding:2px;
		margin:0px;
	}

	#pageinfo
	{
		font-size: 90%;
		padding:2px;
		margin:0px;
		clear:left;
	}

#contents {
	padding:12px;
}

	.footbox
	{
		clear:both;
		padding:3px;
		margin:6px 1px 1px 1px;
		font-size:90%;
		line-height:180%;
	}


.toolbar {
	padding:0px;
	margin-bottom:10px;
	text-align:right;
}

#footer {
	clear: both;
	font-size:80%;
	padding:0px;
	margin:16px 0px 0px 0px;
}

#qrcode {
	float:left;
	margin:0px 10px 0px 10px;
}

	.sidebox {
		word-break:break-all;
		overflow:hidden;
		letter-spacing: 0.5px;
	}

	.sidebox ul li {
		line-height:160%;
	}
	
	.sidebox h1 ,
	.sidebox h2 ,
	.sidebox h3 ,
	.sidebox h4 ,
	.sidebox h5 {
		font-size: 120%;
		background-image: none;
		margin-top:10px;
	}
	
	.sidebox .anchor_super,
	.sidebox .jumpmenu {
		display:none;
	}
	
	.sidebox td {
		padding:0px;
	}

#footer{
	clear:both;
}
	#signature{
		float:left;
	}

.noexists {
	color:#000000;
	background-color:#FFFACC;
}

.small { font-size:85%; }

.super_index {
	color:#DD3333;
	background-color:inherit;
	font-weight:bold;
	font-size:60%;
	vertical-align:super;
}

a.note_super {
	color:#DD3333;
	background-color:inherit;
	font-weight:bold;
	font-size:70%;
}

.jumpmenu {
	font-size:70%;
	text-align:right;
}

hr.full_hr {
	border-style:ridge;
	border-width:1px 0px;
}

#hgroup + .navibar{
	display:block;
	float:right;
}

#pageinfo{
	clear:left;
}

.navibar ul:after, .navibar ul:before{
	content: '';
}

/* pukiwiki.skin.php */
#preview {
	color:inherit;
	background-color:<?php echo SKIN_CSS_CTS_BGCOLOR; ?>;
}



.style_td_today {
	background-color:#CCFFDD;
}
.style_td_sat {
	background-color:#DDE5FF;
}
.style_td_sun {
	background-color:#FFEEEE;
}
.style_td_caltop,
.style_td_week {
	background-color:<?php echo SKIN_CSS_BGCOLOR; ?>;
}

/* calendar_viewer.inc.php */
.calendar_viewer {
	color:inherit;
	background-color:inherit;
	margin-top:20px;
	margin-bottom:10px;
	padding-bottom:10px;
}
.calendar_viewer_left {
	color:inherit;
	background-color:inherit;
	float:left;
}
.calendar_viewer_right {
	color:inherit;
	background-color:inherit;
	float:right;
}

/* clear.inc.php */
.clear {
	margin:0px;
	clear:both;
}

/* counter.inc.php */
.counter { font-size:90%; }

/* diff.inc.php */
.diff_added {
	color:blue;
	background-color:inherit;
}

.diff_removed {
	color:red;
	background-color:inherit;
}

/****/

body{
	color:<?php echo SKIN_CSS_FGCOLOR; ?>;
	background-color:<?php echo SKIN_CSS_BGCOLOR; ?>;
}

a:link {
	color:<?php echo SKIN_CSS_A_LINK; ?>;
}

a:active {
	color:<?php echo SKIN_CSS_A_ACTIVE; ?>;
}

a:visited {
	color:<?php echo SKIN_CSS_A_VISITED; ?>;
}

a:hover {
	color:<?php echo SKIN_CSS_A_HOVER; ?>;
}
hr {
	border-color:<?php echo SKIN_CSS_PRE_BDCOLOR; ?>;
}
h1, h2 {
	color:<?php echo SKIN_CSS_H2_FGCOLOR; ?>;
	background-color:<?php echo SKIN_CSS_H2_BGCOLOR; ?>;
	border-color:<?php echo SKIN_CSS_H2_BDCOLOR ?>;
}

h3{
	color:<?php echo SKIN_CSS_H3_FGCOLOR ?>;
	background-color:<?php echo SKIN_CSS_H3_BGCOLOR; ?>;
	border-color:<?php echo SKIN_CSS_H3_BDCOLOR; ?>;
}

h4{
	color:<?php echo SKIN_CSS_H4_FGCOLOR; ?>;
	background-color:<?php echo SKIN_CSS_H4_BGCOLOR; ?>;
	border-color:<?php echo SKIN_CSS_H4_BDCOLOR; ?>;
}

h5{
	color:<?php echo SKIN_CSS_H5_FGCOLOR; ?>;
	background-color:<?php echo SKIN_CSS_H5_BGCOLOR; ?>;
	border-color:<?php echo SKIN_CSS_H5_BDCOLOR; ?>;
}

h6{
	color:<?php echo SKIN_CSS_H6_FGCOLOR; ?>;
	background-color:<?php echo SKIN_CSS_H6_BGCOLOR; ?>;
	border-color:<?php echo SKIN_CSS_H6_BDCOLOR; ?>;
}

pre{
	border:<?php echo SKIN_CSS_PRE_BDCOLOR; ?> 1px solid;
	color:<?php echo SKIN_CSS_FGCOLOR; ?>;
	background-color:<?php echo SKIN_CSS_PRE_BGCOLOR; ?>;
}

input[type='text'], input[type='password'], input[type='file'],
input[type='tel'], input[type='url'], input[type='email'], 
input[type='datetime'], input[type='date'], input[type='month'], 
input[type='week'], input[type='time'], input[type='datetime-local'], 
input[type='number'], input[type='range'], input[type='color'], 
input[type='search'], textarea, select {
	color:<?php echo SKIN_CSS_FGCOLOR; ?>;
	background-color:<?php echo SKIN_CSS_BGCOLOR; ?>;
	background-image:none;
	border: 1px solid <?php echo SKIN_CSS_BOX_BDCOLOR; ?>;
}

.style_table{
	background-color:<?php echo SKIN_CSS_BGCOLOR; ?>;
}

thead td.style_td,
tfoot td.style_td {
	background-color:<?php echo SKIN_CSS_BGCOLOR; ?>;
}
thead th.style_th,
tfoot th.style_th {
	background-color:<?php echo SKIN_CSS_BOX_BGCOLOR; ?>;
}
.style_table {
	background-color:<?php echo SKIN_CSS_BOX_BDCOLOR; ?>;
}
.style_th{
	background-color:<?php echo SKIN_CSS_BGCOLOR; ?>;
}
.style_td, .style_td_blank{
	background-color:<?php echo SKIN_CSS_CTS_BGCOLOR; ?>;
}

#header{
	background-color: <?php echo SKIN_CSS_BOX_BGCOLOR; ?>;
	border: 2px solid <?php echo SKIN_CSS_BOX_BDCOLOR; ?>;
}

#hgroup{
	color:<?php echo SKIN_CSS_H1_FGCOLOR ?>;
	background-color: <?php echo SKIN_CSS_H1_BGCOLOR; ?>;
	border-color: <?php echo SKIN_CSS_H1_BDCOLOR; ?>;
}

#contents{
	background-color:<?php echo SKIN_CSS_CTS_BGCOLOR; ?>;
	border:3px solid <?php echo SKIN_CSS_CTS_BDCOLOR; ?>;
}

.footbox{
	border:dotted 1px <?php echo SKIN_CSS_BOX_BDCOLOR; ?>;
	background-color: <?php echo SKIN_CSS_BOX_BGCOLOR; ?>;
}

.sidebox h1 ,
.sidebox h2 ,
.sidebox h3 ,
.sidebox h4 ,
.sidebox h5 {
	border: 2px solid <?php echo SKIN_CSS_BOX_BDCOLOR; ?>;
	background-color: <?php echo SKIN_CSS_BOX_BGCOLOR; ?>;
}



/* calendar*.inc.php */
.style_calendar {
	background-color:<?php echo SKIN_CSS_BOX_BDCOLOR; ?>;
}
@media print{
	#logo, #navigator, #topbox, #leftbox, #leftbox2, #rightbox, #footer,
	#toolbar, #attach, #related, a.note_super, .jumpmenu, .anchor_super,
	.search_form, .pageinfo{
		display:none;
	}
	#centerbox, #centerbox_noright, #centerbox_noright2{
		width:100%;
	}
	
	#header, #contents{
		border:none;
		margin:0;
		padding:0;
	}
	
	#contents{
		clear:both;
	}
	
	.title{
		border:none;
		margin:5px;
		padding:0;
	}
}

