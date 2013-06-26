<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: classic.skin.php,v 1.5.5 2012/08/10 14:32:00 Logue Exp $

// PukiWiki GS2 Skin for PukiWiki Adv.
// Copyright (C)
//   2011-2012 PukiWiki Advance Developers Team
// based on "GS2" v1.5.3
//   by yiza < http://www.yiza.net/ >
ini_set('zlib.output_compression', 'Off');

$image_dir = isset($_GET['base'])   ? $_GET['base']	: '../image/';
$expire = isset($_GET['expire'])   ? (int)$_GET['expire'] * 86400	: '604800';	// Default is 7 days.
$gs2_color = isset($_GET['gs2color']) ? $_GET['gs2color'] : 'blue';

require('gs2_color/pukiwiki_gs2_color_'.$gs2_color.'.php');

// Send header
header('Content-Type: text/css; charset: UTF-8');
header('Cache-Control: private');
header('Expires: ' .gmdate('D, d M Y H:i:s',time() + $expire) . ' GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s', getlastmod() ) . ' GMT');
@ob_start('ob_gzhandler');

// Color settings
require ('./gs2_color/pukiwiki_gs2_color_'.$gs2_color.'.php');

// Output CSS ----
?>
@charset "UTF-8";

/** Color setting **/

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
	background-image: url('data:image/svg+xml;base64,<?php echo base64_encode('<?xml version="1.0"?><svg xmlns="http://www.w3.org/2000/svg" version="1.0"><defs><linearGradient x1="0" y1="0" x2="0" y2="100%" id="gradient"><stop offset="0%" stop-color="'.SKIN_CSS_BOX_BGCOLOR.'" /><stop offset="100%" stop-color="'.SKIN_CSS_BGCOLOR.'" /></linearGradient></defs><rect fill="url(#gradient)" x="0" y="0" width="100%" height="100%"/></svg>'); ?>');
	background-color: <?php echo SKIN_CSS_BGCOLOR; ?>;
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

.style_table tr:nth-child(even) .style_td{
	 background-color:<?php echo SKIN_CSS_CTS_BGCOLOR2; ?>;
}

#header{
	background-color: <?php echo SKIN_CSS_BOX_BGCOLOR; ?>;
	border: 2px solid <?php echo SKIN_CSS_BOX_BDCOLOR; ?>;
	text-shadow: 0 1px 0 <?php echo SKIN_CSS_SHADOW_COLOR; ?>;
}

#hgroup{
	color:<?php echo SKIN_CSS_H1_FGCOLOR ?>;
	background-color: <?php echo SKIN_CSS_H1_BGCOLOR; ?>;
	border-color: <?php echo SKIN_CSS_H1_BDCOLOR; ?>;
}

#content{
	background-color:<?php echo SKIN_CSS_CTS_BGCOLOR; ?>;
	border:3px solid <?php echo SKIN_CSS_CTS_BDCOLOR; ?>;
}

.footbox{
	border:dotted 1px <?php echo SKIN_CSS_BOX_BDCOLOR; ?>;
	background-color: <?php echo SKIN_CSS_BOX_BGCOLOR; ?>;
}

.sidebox h1,
.sidebox h2,
.sidebox h3,
.sidebox h4,
.sidebox h5{
	border: 2px solid <?php echo SKIN_CSS_BOX_BDCOLOR; ?>;
	background-color: <?php echo SKIN_CSS_BOX_BGCOLOR; ?>;
}

/* calendar.inc.php */
.style_calendar {
	background-color:<?php echo SKIN_CSS_BOX_BDCOLOR; ?>;
}

/** start layout setting **/

blockquote { margin-left:32px; }

body {
	margin:0;
	padding:1%;
	letter-spacing:1px;
}

td, th {
	letter-spacing:1px;
}

h1, h2 {
	font-size:153.8%;
	padding:3px;
	border-style:solid;
	border-width:3px 3px 6px 20px;
	margin:0 0 5px 0;
}
h3 {
	font-size:138.5%;
	padding:3px;
	border-style: solid;
	border-width: 1px 1px 5px 12px;
	margin:0 0 5px 0;
}
h4 {
	font-size:131%;
	padding:3px;
	border-style: solid;
	border-width: 0 6px 1px 7px;
	margin:0 0 5px 0;
}
h5 {
	font-size:123.1%;
	padding:3px;
	border-style: solid;
	border-width: 0 0 1px 6px;
	margin:0 0 5px 0;
}

h6 {
	font-size:110%;
	padding:3px;
	border-style: solid;
	border-width: 0 5px 1px 0;
	margin:0 0 5px 0;
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
	line-height:161.6%;
}
/** Framework *************************************************************************************/
#container {
	width:100%;
	margin: 0 auto;
}

#main_wrapper #main{
	padding:5px;
	margin:0 1.5em;
}

#menubar, #sidebar{
	display:none;
}
@media screen{
	.two-colums #main_wrapper {
		width:100%;
		float:right;
		margin-left:-180px;
	}

	.two-colums #main_wrapper #main {
		margin:0 0 0 180px;
	}

	.two-colums #menubar {
		display:block;
		width:180px;
		float:right;
	}

	.three-colums #wrapper {
		width:100%;
		float:left;
		margin-right:-180px;
	}

	.three-colums #main_wrapper {
		width:100%;
		float:right;
		margin-left:-180px;
	}

	.three-colums #main_wrapper #main {
		margin:0 180px;
		padding:0 5px;
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

/** Layout ****************************************************************************************/
#header {
	padding:5px;
	margin:0 0 10px 0;
}

	#hgroup {
		display:block;
		font-weight: bold;
		letter-spacing: 3px;
		border-style: solid;
		border-width: 2px 4px 4px 2px;
		margin: 5px;
	}
	
	#logo {
		float:left;
		margin-right: 20px;
	}
	
	
	#logo + #hgroup{
		margin-left: 90px;
	}
	
	#hgroup h1, #hgroup h2{
		border: none;
		background-color: transparent;
	}

	#hgroup h1{
		font-size: 197%;
	}
	#hgroup h2, #hgroup .topicpath{
		font-size: 93%;
	}

	.search_form
	{
		font-size: 85%;
		padding:2px 8px;
		margin:0;
		float:right;
	}

	.navibar {
		font-size: 85%;
		padding:2px;
		margin:0;
	}

	#pageinfo
	{
		font-size: 77%;
		padding:2px;
		margin:0;
		clear:left;
	}

#content {
	padding: 12px;
}

	.footbox
	{
		clear:both;
		padding:3px;
		margin:6px 1px 1px 1px;
		font-size:93%;
		line-height:182%;
	}


.toolbar {
	padding:0px;
	margin-bottom:10px;
	text-align:right;
}

#footer {
	clear: both;
	font-size:85%;
	padding:0;
	margin:16px 0 0 0;
}

#qrcode {
	float:left;
	margin:0 10px;
}

	.sidebox {
		font-size:93%;
		word-break:break-all;
		overflow:hidden;
		letter-spacing: .5px;
	}

	.sidebox ul li {
		line-height:161.6%;
	}
	
	.sidebox h1 ,
	.sidebox h2 ,
	.sidebox h3 ,
	.sidebox h4 ,
	.sidebox h5 {
		font-size: 116%;
		background-image: none;
		margin-top:10px;
	}
	
	.sidebox ul{
		margin:0;
		padding:0 0.5em;
	}
	
	.sidebox .anchor_super,
	.sidebox .jumpmenu {
		display:none;
	}
	
	.sidebox td {
		padding:0;
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

.small { font-size:77%; }

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
	font-size:77%;
}

.jumpmenu {
	font-size:77%;
	text-align:right;
}

hr.full_hr {
	border-style:ridge;
	border-width:1px 0;
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

@media print{
	#logo, #navigator, #topbox, #leftbox, #leftbox2, #rightbox, #footer,
	#toolbar, #attach, #related, a.note_super, .jumpmenu, .anchor_super,
	.search_form, .pageinfo{
		display:none;
	}
	#centerbox, #centerbox_noright, #centerbox_noright2{
		width:100%;
	}
	#logo + #hgroup{
		margin-left: 0px;
	}
	#header, #content{
		border:none;
		margin:0;
		padding:0;
	}
	
	#content{
		clear:both;
	}
	
	.title{
		border:none;
		margin:5px;
		padding:0;
	}
}
<?php
@ob_end_flush();
/* End of file gs2.css.php */
/* Location: ./webroot/skin/theme/gs2/gs2.css.php */