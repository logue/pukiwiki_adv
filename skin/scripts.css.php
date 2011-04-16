<?php
// PukiWiki Advance Standard CSS.
// $Id: scripts.css.php,v 1.6.8 2011/04/03 19:44:00 Logue Exp $
// Copyright (C) 2010-2011 PukiWiki Advance Developer Team
//
// 基本スタイルシート

// Send header
header('Content-Type: text/css; charset: UTF-8');
$image_dir = '../image/';
flush();
?>
@charset "UTF-8";
/*  HTML5 ✰ Boilerplate  */

html, body, div, span, object, iframe,
h1, h2, h3, h4, h5, h6, p, blockquote, pre,
abbr, address, cite, code, del, dfn, em, img, ins, kbd, q, samp,
small, strong, sub, sup, var, b, i, dl, dt, dd, ol, ul, li,
fieldset, form, label, legend,
table, caption, tbody, tfoot, thead, tr, th, td,
article, aside, canvas, details, figcaption, figure,
footer, header, hgroup, menu, nav, section, summary,
time, mark, audio, video {
	margin: 0;
	padding: 0;
	border: 0;
	font-size: 100%;
	font: inherit;
	vertical-align: baseline;
}

article, aside, details, figcaption, figure,
footer, header, hgroup, menu, nav, section {
	display: block;
}

blockquote, q { quotes: none; }
blockquote:before, blockquote:after,
q:before, q:after { content: ""; content: none; }
ins { /* background-color: #ff9; color: #000; */ text-decoration: none; }
mark { /* background-color: #ff9; color: #000; */ font-style: italic; font-weight: bold; }
del { text-decoration: line-through; }
abbr[title], dfn[title] { border-bottom: 1px dotted; cursor: help; }
table { border-collapse: collapse; border-spacing: 0; }
hr { display: block; height: 1px; border: 0; border-top: 1px solid #ccc; margin: 1em 0; padding: 0; }
input, select { vertical-align: middle; }

body, .ui-widget { font:13px/1.231 sans-serif; *font-size:small; }
select, input, textarea, button { font:99% sans-serif; }
pre, code, kbd, samp { font-family: monospace, sans-serif; }

/* html { overflow-y: scroll; } */
a:hover, a:active { outline: none; }
ul, ol { margin-left: 2em; }
ol { list-style-type: decimal; }
nav ul, nav li { margin: 0; list-style:none; list-style-image: none; }
small { font-size: 85%; }
strong, th { font-weight: bold; }
/* td { vertical-align: top; } */
sub, sup { font-size: 75%; line-height: 0; position: relative; }
sup { top: -0.5em; }
sub { bottom: -0.25em; }

pre { white-space: pre; white-space: pre-wrap; word-wrap: break-word; padding: 15px; }
textarea { overflow: auto; } 
.ie6 legend, .ie7 legend { margin-left: -7px; } 
input[type="radio"] { vertical-align: baseline; }
input[type="checkbox"] { vertical-align: baseline; }
.ie6 input { vertical-align: text-bottom; }
label, input[type="button"], input[type="submit"], input[type="image"], button { cursor: pointer; }
button, input, select, textarea { margin: 0; }
input:valid, textarea:valid   {  }
input:invalid, textarea:invalid { border-radius: 1px; -moz-box-shadow: 0px 0px 5px red; -webkit-box-shadow: 0px 0px 5px red; box-shadow: 0px 0px 5px red; }
.no-boxshadow input:invalid, .no-boxshadow textarea:invalid { background-color: #f0dddd; }


::-moz-selection{ background: #5E99FF; color:#fff; text-shadow: none; }
::selection { background:#5E99FF; color:#fff; text-shadow: none; }
a:link { -webkit-tap-highlight-color: #FF5E99; }
button {  width: auto; overflow: visible; }
.ie7 img { -ms-interpolation-mode: bicubic; }

body, select, input, textarea { color: #444; }
h1, h2, h3, h4, h5, h6 { font-weight: bold; }
a, a:active, a:visited { color: #607890; }
a:hover { color: #036; }


.ir { display: block; text-indent: -999em; overflow: hidden; background-repeat: no-repeat; text-align: left; direction: ltr; }
.hidden { display: none; visibility: hidden; }
.visuallyhidden { border: 0; clip: rect(0 0 0 0); height: 1px; margin: -1px; overflow: hidden; padding: 0; position: absolute; width: 1px; }
.visuallyhidden.focusable:active,
.visuallyhidden.focusable:focus { clip: auto; height: auto; margin: 0; overflow: visible; position: static; width: auto; }
.invisible { visibility: hidden; }
.clearfix:before, .clearfix:after { content: "\0020"; display: block; height: 0; overflow: hidden; }
.clearfix:after { clear: both; }
.clearfix { zoom: 1; }

/** PukiWiki Advance Standard CSS Set *************************************************************/

/* Font set */
@media screen{
	body{
		font-family: 'Segoe UI', 'Trebuchet MS', Verdana, Arial, Sans-Serif;
	}

	pre, code, kbd, samp, textarea, select, option, input, var{
		font-family: 'Consolas', 'Bitstream Vera Sans Mono', 'Courier New', Courier, monospace;
	}

	/* Japanese */
	:lang(ja), :lang(ja) .ui-widget{
		font-family: Meiryo, 'メイリオ', 'ヒラギノ角ゴ Pro W3', 'Hiragino Mincho Pro W3', Osaka, 'ＭＳＰ ゴシック';
		line-height:137%;
	}

	:lang(ja) pre,
	:lang(ja) code,
	:lang(ja) kbd,
	:lang(ja) samp,
	:lang(ja) textarea,
	:lang(ja) select,
	:lang(ja) option,
	:lang(ja) input,
	:lang(ja) var{
		font-family: 'Osaka−等幅', 'ＭＳ ゴシック', 'MS Gothic' !important;
		line-height:123% !important;
	}

	/* Korean */
	:lang(ko), :lang(ko) .ui-widget{
		font-family: 'AppleGothic', 'Malgun Gothic', '맑은 고딕', Gulim, Dotum, AppleGothic;
	}
	:lang(ko) pre,
	:lang(ko) code,
	:lang(ko) kbd,
	:lang(ko) samp,
	:lang(ko) textarea,
	:lang(ko) select,
	:lang(ko) option,
	:lang(ko) input,
	:lang(ko) var{
		font-family: GulimChe !important;
	}

	/*  Chinese */
	:lang(zh), :lang(zh) .ui-widget{
		font-family: 'Hiragino Sans GB W3', 'STHeiti', 'Apple LiGothic Medium', 'Microsoft YaHei', 'Microsoft JhengHei';
		line-height:137%;
	}
	:lang(zh) pre,
	:lang(zh) code,
	:lang(zh) kbd,
	:lang(zh) samp,
	:lang(zh) textarea,
	:lang(zh) select,
	:lang(zh) option,
	:lang(zh) input,
	:lang(zh) var{
		font-family: 'SimHei', '蒙納黑體', monospace;
		line-height:123% !important;
	}
}

/* for Print font */
@media print{
	body {
		font-family: "Lucida Bright", Century, "Times New Roman", serif;
	}
	:lang(ja) body {
		font-family: "ヒラギノ明朝 Pro W3", 'Hiragino Mincho Pro W3', "平成明朝", 'ＭＳ Ｐ明朝', 'MS PMincho', serif;
	}
	:lang(zh) body {
		font-family: 'Apple LiSung Light', 'STHeiti Light', 'PMingLiU', 'KaiTi', serif;
	}
	:lang(ko) body{
		font-family: '바탕체', Batang, serif;
	}
}

/** Browser Hack **********************************************************************************/
/* head Tag */
h1 {
	font-size: 197%;
}
h2 {
	font-size: 174%;
}

h3 {
	font-size: 153.9%;
}

h4 {
	font-size: 131%;
}

h5 {
	font-size: 116%;
}

h6 {
	font-size: 100%
}

fieldset {
	border: 1px silver solid;
	border-radius:0.5em;
	padding:0.5em;
	margin:1em;
}

.gecko fieldset{
	-moz-border-radius:0.5em;
}

legend {
	text-indent:0.5em;
}

figure{
	margin: 0 auto;
	text-align:center;
}

figcaption{
	font-size:93%;
	text-align:center;
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

/* List tags */
ul.list1 { list-style-type:disc; }
ul.list2 { list-style-type:circle; }
ul.list3 { list-style-type:square; }
ol.list1 { list-style-type:decimal; }
ol.list2 { list-style-type:lower-roman; }
ol.list3 { list-style-type:lower-alpha; }

/* Fix italic font */
i, em, cite, q{
	font-style: normal;
	transform: skewX(-15deg);
}
.gecko i, .gecko em, .gecko cite, .gecko q{
	-moz-transform: skewX(-15deg);
}
.ie i, .ie em, .ie cite, .ie q{
	-ms-transform: skewX(-15deg);
}
.presto i, .presto em, .presto cite, .presto q{
	-o-transform: skewX(-15deg);
}
.webkit i, .webkit em, .webkit cite, .webkit q{
	-webkit-transform: skewX(-15deg);
}

/* Italic font fix for legacy IE */
.ie6 i, .ie6 em, .ie6 cite, .ie7 i, .ie7 em, .ie7 cite, .ie8 i, .ie8 em, .ie8 cite{
	font-family: 'MS PGothic', suns-serif;
	font-style: italic;
}

/* Fix Gecko Ruby Tag */
.gecko ruby {
	display:inline-table !important;
	text-align:center !important;
	white-space:nowrap !important;
	text-indent:0 !important;
	margin:0 !important;
	vertical-align:text-bottom !important;
	line-height:1 !important;
}

.gecko ruby>rb,.gecko ruby>rbc {
	display:table-row-group !important;
	line-height:1.0 !important;
}
.gecko ruby>rt,.gecko ruby>rbc+rtc {
	display:table-header-group !important;
	font-size:50% !important;
	line-height:1.0 !important;
	letter-spacing:0 !important;
}

.gecko ruby>rbc+rtc+rtc {
	display:table-footer-group !important;
	font-size:50% !important;
	line-height:1.0 !important;
	letter-spacing:0 !important;
}

.gecko rbc>rb,.gecko rtc>rt {
	display:table-cell !important;
	letter-spacing:0 !important;
}

.gecko rp {
	display:none !important;
}

/* Fix font setting */
a{
	font-size: inherit;
	font-family: inherit;
	color: inherit;
	background-color:inherit;
}

ins, del{
	font: inherit !important;
}

a img {
	vertical-align:bottom;
	background-color:transparent !important;
}

a img.pkwk-symbol{
	vertical-align: baseline;
}

.full_hr{
	clear:both;
}

.webkit ::-webkit-input-placeholder, .gecko input:-moz-placeholder{
	color:grey;
}

.ie textarea{
	overflow: auto;
}
/** Customize UI **********************************************************************************/
.helper, .tocpic, map area, a{
	cursor:pointer;
}

/* form */
input[type='text'], input[type='password'], textarea, select{
	border:1px solid silver;
	background-color:white;
	padding:0.2em;
	margin:0.1em;
	line-height:100%;
}

/* gradient */
.gecko input[type='text'], .gecko input[type='password'], .gecko textarea, .gecko select{
	background: -moz-linear-gradient(top,  whitesmoke, white);
}

.webkit input[type='text'], .webkit input[type='password'], .webkit textarea, select{
	background: -webkit-gradient(linear, left top, left bottom, from(whitesmoke), to(white));
}

.presto input[type='text'], .presto input[type='password'], .presto textarea, .presto select{
	background-image:url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZlcnNpb249IjEuMCI%2BDQo8ZGVmcz4NCjxsaW5lYXJHcmFkaWVudCBpZD0iZ3JhZGllbnQiIHgxPSIwIiB5MT0iMCIgeDI9IjAiIHkyPSIxMDAlIj4NCjxzdG9wIG9mZnNldD0iMCUiIHN0eWxlPSJzdG9wLWNvbG9yOndoaXRlc21va2U7Ii8%2BDQo8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0eWxlPSJzdG9wLWNvbG9yOndoaXRlOyIvPg0KPC9saW5lYXJHcmFkaWVudD4NCjwvZGVmcz4NCjxyZWN0IHg9IjAiIHk9IjAiIGZpbGw9InVybCgjZ3JhZGllbnQpIiB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiAvPg0KPC9zdmc%2B");
}

.ie input[type='text'], .ie input[type='password'], .ie textarea, .ie select{
	-ms-filter:"progid:DXImageTransform.Microsoft.Gradient(GradientType=0,StartColorStr=whitesmoke,EndColorStr=white)";
	padding:0.25em 0.2em;
}

/* focus */
input[type='text']:focus, input[type='password']:focus, textarea:focus, select:focus {
	box-shadow: 0px 0px 3px dodgerblue;
	border:1px solid cornflowerblue;
}
.ie9 input[type='text']:focus, .ie9 input[type='password']:focus, .ie9 textarea:focus, .ie9 select:focus {
	box-shadow: 0px 0px 5px dodgerblue;
}
.webkit input[type='text']:focus, .webkit input[type='password']:focus, .webkit textarea:focus, .webkit select:focus {
	-webkit-box-shadow:0px 0px 3px dodgerblue;
}

.gecko input[type='text']:focus, .gecko input[type='password']:focus, .gecko textarea:focus, .gecko select:focus {
	-moz-box-shadow:0px 0px 3px dodgerblue;
}

/* hover */
input[type='text']:hover, input[type='password']:hover, textarea:hover, select:hover {
	border:1px solid cornflowerblue;
}

/* disabled */
input[type='text'][disabled], input[type='password'][disabled], textarea[disabled], select[disabled]{
	color:grey;
	border:1px solid lightgrey;
	background-color:whitesmoke;
	cursor: not-allowed;
	box-shadow: none;
}

input[type='text'][disabled]:hover, input[type='password'][disabled]:hover, textarea[disabled]:hover, select[disabled]:hover{
	border:1px solid lightgrey;
	background-color:whitesmoke;
}

input[disabled]:hover, select[disabled]:hover, textarea[disabled]:hover, option[disabled]:hover{
	box-shadow: none;
}

/* Fix jQueryUI widgets font size */
.ui-widget-content{
	font: inherit !important;
	font-size:93% !important;
}

.ui-button{
	text-shadow: 0 1px 1px rgba(0,0,0,.3);
	box-shadow: 0 1px 2px rgba(0,0,0,.2);
	margin:0.2em !important;
	padding: 0.1em 0.5em !important;
}
.ui-button-text-icon-primary .ui-button-icon-primary, .ui-button-text-icons .ui-button-icon-primary, .ui-button-icons-only .ui-button-icon-primary {
    left: 0 !important;
}
.ui-button-text{
	font-size:100%;
	padding: 0 0.2em 0 1.2em !important;
}

.gecko .ui-button{
	-moz-box-shadow:0 1px 2px rgba(0,0,0,.2);
}
.webkit .ui-button{
	-webkit-box-shadow:0 1px 2px rgba(0,0,0,.2);
}
.presto .ui-button, .ie .ui-button{
	padding: 0.2em 0.5em !important;
}

/* Message Box (for Debug and Error message) */
.message_box{
	padding: 0.7em;
	margin:0.5em;
}

.message_box p, .message_box ul{
	margin:0;
}

.message_box p .ui-icon{
	float: left; margin-right: 0.3em;
}
/** Misc ****************************************************************************************/
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
	text-indent:0px;
	display:inline;
}
.size2 {
	font-size:x-small;
	text-indent:0px;
	display:inline;
}
.size3 {
	font-size:small;
	text-indent:0px;
	display:inline;
}
.size4 {
	font-size:medium;
	text-indent:0px;
	display:inline;
}
.size5 {
	font-size:large;
	text-indent:0px;
	display:inline;
}
.size6 {
	font-size:x-large;
	text-indent:0px;
	display:inline;
}
.size7 {
	font-size:xx-large;
	text-indent:0px;
	display:inline;
}

/* html.php/edit_form() */
.edit_form { clear:both; }

.edit_form textarea{
	width:95%;
	min-width:99%;
	resize: vertical;
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
	margin-right:0.5em;
}

/** Plugin Configure ******************************************************************************/
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

.img_margin{

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
	
/* Fix English calendar week label */
:lang(en) .style_calendar_week{
	font-family:monospace !important;
	padding:0.5em 0.2em;
}

.style_calendar_post p{
	padding: 0.25em 1em;
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

/* counter.inc.php */
.counter {
	font-size:77%;
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
	list-style-image: none;
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

/* suckerfish.inc.php */
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
.sf-menu li:hover ul,
.sf-menu li.sfHover ul {
	left:			0;
	z-index:		99;
}
ul.sf-menu li:hover li ul,
ul.sf-menu li.sfHover li ul {
	top:			-999em;
}
ul.sf-menu li li:hover ul,
ul.sf-menu li li.sfHover ul {
	top:			0;
}
ul.sf-menu li li:hover li ul,
ul.sf-menu li li.sfHover li ul {
	top:			-999em;
}
ul.sf-menu li li li:hover ul,
ul.sf-menu li li li.sfHover ul {
	top:			0;
}

/* tooltip.inc.php */
.tooltip, .linktip {
	border-bottom: 1px dotted;
}

/* vote.inc.php */
.vote_table{
	vertical-align:middle;
}

/* pukiwiki extend anchor */
.ext, .inn{
	margin-left: 2px;
	vertical-align: baseline;
}

/** JavaScript Stylesheet set *********************************************************************/
.no-js{
	display:none;
}

/* Table Sorter */
th.header {
	background-image: url(<?php echo $image_dir ?>ajax/tablesorter/small.gif);
	cursor: pointer;
	background-repeat: no-repeat;
	background-position: center left;
	padding-left: 20px;
	border-right: 1px solid #dad9c7;
	margin-left: -1px;
}

th.headerSortUp {
	background-image: url(<?php echo $image_dir ?>ajax/tablesorter/small_asc.gif);
}

th.headerSortDown {
	background-image: url(<?php echo $image_dir ?>ajax/tablesorter/small_asc.gif);
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
.grippie {
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
#loadingScreen {
	cursor:progress;
	background-position: center center;
	background-repeat: no-repeat;
	background-image: url(<?php echo $image_dir ?>ajax/loading.gif);
}

/* hide the close x on the loading screen */
.loadingScreenWindow .ui-dialog-titlebar-close {
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

/**************************************************************************************************/
/* Tooltip */
#tooltip{
	color:black;
	font-size:93%;
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
	padding: 0.3em;
	opacity:0.8;
	border-radius: 0.3em;
	background-color:white;
	box-shadow: 3px 3px 5px rgba(0, 0, 0, 0.6);
	z-index: 9999;
}

.ie6 #tooltip, .ie7 #tooltip, .ie8 #tooltip{
	filter:
		alpha(opacity=80)
		progid:DXImageTransform.Microsoft.Gradient(GradientType=0,StartColorStr=white,EndColorStr=gainsboro)
		progid:DXImageTransform.Microsoft.Shadow(Strength=5, Direction=135, Color='#333333') !important;
}

.ie9 #tooltip{
	-ms-filter:"progid:DXImageTransform.Microsoft.Gradient(GradientType=0,StartColorStr=white,EndColorStr=gainsboro)";
}

.gecko #tooltip{
	-moz-opacity:0.8;
	-moz-border-radius: 0.3em;
	-moz-box-shadow: 3px 3px 5px rgba(0, 0, 0, 0.6);
	background: -moz-linear-gradient(top, white, gainsboro);
}

.webkit #tooltip{
	-webkit-border-radius: 0.3em;
	-webkit-box-shadow: 3px 3px 5px rgba(0, 0, 0, 0.6);
	background: -webkit-gradient(linear, left top, left bottom, from(white), to(gainsboro));
}

.presto #tooltip{
	background: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxkZWZzPjxsaW5lYXJHcmFkaWVudCBpZD0iZ3JhZGllbnQiIHgxPSIwJSIgeTE9IjAlIiB4Mj0iMCUiIHkyPSIxMDAlIj48c3RvcCBvZmZzZXQ9IjAlIiBzdHlsZT0ic3RvcC1jb2xvcjpyZ2JhKDI1NSwyNTUsMjU1LDEpOyIgLz48c3RvcCBvZmZzZXQ9IjEwMCUiIHN0eWxlPSJzdG9wLWNvbG9yOnJnYmEoMjIwLDIyMCwyMjAsMSk7IiAvPjwvbGluZWFyR3JhZGllbnQ+PC9kZWZzPjxyZWN0IGZpbGw9InVybCgjZ3JhZGllbnQpIiBoZWlnaHQ9IjEwMCUiIHdpZHRoPSIxMDAlIiAvPjwvc3ZnPg==') !important;
}

#tooltip p{
	margin:0px;
	padding:0px;
}

.ie #tooltip p{
	-ms-filter:"progid:DXImageTransform.Microsoft.DropShadow(color=gainsboro,offx=1,offy=1)";
}

/**************************************************************************************************/
/* popup toc */
#poptoc{
	font-size:93%;
	min-width:18em;
	max-width:25em;
	z-index:1;
	position:absolute;
	display:none;
}
#poptoc h1{
	font-size:small;
	font-weight:normal;
	padding:0.3em;
	margin:0;
	text-align:center;
}
#poptoc h1 a{
	text-decoration:none;
}
#poptoc h1 img{
	margin-bottom:-3px;
	margin-right: 2px;
}
#poptoc .nav{
	text-align:center;
}

/**************************************************************************************************/
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

#cboxTopLeft{width:5px; height:5px;}
#cboxTopRight{width:5px; height:5px;}
#cboxBottomLeft{width:5px; height:5px;}
#cboxBottomRight{width:5px; height:5px;}
#cboxMiddleLeft{width:5px;}
#cboxMiddleRight{width:5px;}
#cboxTopCenter{height:5px;}
#cboxBottomCenter{height:5px;}
#cboxLoadedContent{margin-bottom:28px;}
#cboxTitle{position:absolute; bottom:4px; left:0; text-align:center; width:100%;}
#cboxCurrent{position:absolute; bottom:4px; left:58px;}
#cboxSlideshow{position:absolute; bottom:4px; right:30px;}
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

#jplayer_icons {
	margin: 0;
	padding: 0
}
#jplayer_icons li {
	margin: 2px;
	position: relative;
	padding: 4px 0;
	cursor: pointer;
	float: left;
	list-style: none;
}
#jplayer_icons .ui-icon {
	float: left;
	margin: 0 4px;
}

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
.table_pager_widget{
	display:none;
}

.table_pager_widget ul li{
	margin: 2px;
	cursor: pointer;
	float: left;
	list-style: none;
}

.table_pager_widget ul li.ui-state-default{
	height:1.2em;
	width:1.2em;
	padding:2px;
}

.table_pager_widget button{
	height:1.2em;
	width:1.2em;
	padding:2px;
}

.table_pager_widget input.pagedisplay{
	width:50px;
}

.table_pager_widget select.pagesize{
	width:80px;
}
/**Swfupload **************************************************************************************/

#swfupload-control p{
	margin:10px 5px;
}
#swfupload-log{
	margin:0; padding:0; width:500px;
}
#swfupload-log li{
	list-style-position:inside;
	margin:2px;
	padding:10px;
	position:relative;
}
#swfupload-log li .progressbar{
	height:5px;
}
#swfupload-log li p{
	margin:0;
}
#swfupload-log li.success{
	border:1px solid #339933;
	background:#ccf9b9;
}

/** Print Setting *********************************************************************************/
@media print {
	* { background: transparent !important; color: black !important; text-shadow: none !important; filter:none !important;
	-ms-filter: none !important; }
	a, a:link, a:visited { color: #444 !important; text-decoration: underline; }
	a[rel=external]:after { content: " (" attr(href) ")"; color: #666 !important; }
	abbr[title]:after { content: " (" attr(title) ")"; }
	.ir a:after, a[href^="javascript:"]:after, a[href^="#"]:after { content: ""; }
	pre, blockquote { border: 1px solid #999; page-break-inside: avoid; }
	thead { display: table-header-group; }
	tr, img { page-break-inside: avoid; }
	@page { margin: 0.5cm; }
	p, h2, h3 { orphans: 3; widows: 3; }
	h2, h3{ page-break-after: avoid; }

	.navigator, .toolbar, .navi, .message_box, .noprint, .tocpic, .sf-menu,
	.style_calendar_prev, .style_calendar_next, .pkwk-symbol, #poptoc,  #toolbar, .ui-dialog{
		display:none !important;
		visibility: hidden !important;
	}

	h1,h2,h3,h4,h5,h6{
		margin:0;
		padding:.3em .3em .15em .5em !important;
		border-left:8px solid !important;
		border-bottom:1px solid !important;
		float:none !important;
		width:100% !important;
	}
	
	h1{
		border-color:white white black dimgray !important;
	}
	
	h2, h3{
		border-color:white white dimgray grey !important;
	}

	h4, h5{
		border-color:silver silver grey darkgray !important;
	}
	
	h6{
		border-color:silver silver darkgray silver !important;
	}
	
	h1 a[href]:after, #qr_code a[href]:after, a.anchor_super[href]:after, .noexists a[href]:after{ content: ""; }
	
	.style_week[title]:after { content: ""; }

	.style_table,
	.style_th,
	.style_td {
		border-collapse: collapse;
		border-spacing: 0;
	}
	.style_table {
		padding: 5px;
		border: 1px solid #333333;
	}
	.style_th,
	.style_td{
		padding: 3px;
		font-size: 90%;
	}
	.style_th {
		border-bottom: 1px solid #333333;
	}
	.style_td {
		border: 1px dotted #333333;
	}
}

<?php if(extension_loaded('zlib')){ob_end_flush();}?>