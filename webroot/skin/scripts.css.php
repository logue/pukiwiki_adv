<?php
// PukiWiki Advance Standard CSS.
// $Id: scripts.css.php,v 1.6.10 2011/09/11 22:54:00 Logue Exp $
// Copyright (C) 2010-2011 PukiWiki Advance Developer Team
//

// Send header
header('Content-Type: text/css; charset: UTF-8');
$image_dir = '../image/';
flush();
?>
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

/* Font set */
body{
	font-family: 'Segoe UI', 'Trebuchet MS', Verdana, Arial, Sans-Serif;
}
/* Japanese */
:lang(ja){
	font-family: Meiryo, 'メイリオ', 'ヒラギノ角ゴ Pro W3', 'Hiragino Mincho Pro W3', Osaka, 'ＭＳＰ ゴシック';
}
/* Korean */
:lang(ko), :lang(ko) .ui-widget{
	font-family: 'AppleGothic', 'Malgun Gothic', '맑은 고딕', Gulim, Dotum, AppleGothic;
}
/*  Chinese */
:lang(zh), :lang(zh) .ui-widget{
	font-family: 'Hiragino Sans GB W3', 'STHeiti', 'Apple LiGothic Medium', 'Microsoft YaHei', 'Microsoft JhengHei';
}

/* for Print font */
@media print{
	body {
		font-family: "Lucida Bright", Century, "Times New Roman", serif;
	}
	:lang(ja) {
		font-family: "ヒラギノ明朝 Pro W3", 'Hiragino Mincho Pro W3', "平成明朝", 'ＭＳ Ｐ明朝', 'MS PMincho', serif;
	}
	:lang(zh) {
		font-family: 'Apple LiSung Light', 'STHeiti Light', 'PMingLiU', 'KaiTi', serif;
	}
	:lang(ko) {
		font-family: '바탕체', Batang, serif;
	}
}

pre, code, kbd, samp, textarea, select, option, input, var{
	font-family: 'Consolas', 'Bitstream Vera Sans Mono', 'Courier New', Courier, monospace;
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
}
/* for Emoji */
@font-face {
	font-family: Symbola;
	src: url('<?php echo $image_dir ?>emoji/Symbola.php');
}

/* head Tag */
h1 {
	font-size: 197%;
}
h2 {
	font-size: 167%;
}

h3 {
	font-size: 146.5%;
}

h4 {
	font-size: 123.1%;
}

h5 {
	font-size: 108%;
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

/* List tags */
ul.list1 { list-style-type:disc; }
ul.list2 { list-style-type:circle; }
ul.list3 { list-style-type:square; }
ol.list1 { list-style-type:decimal; }
ol.list2 { list-style-type:lower-roman; }
ol.list3 { list-style-type:lower-alpha; }

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

.full_hr{
	clear:both;
}

.webkit ::-webkit-input-placeholder, .gecko input:-moz-placeholder{
	color:grey;
}

.ie textarea{
	overflow: auto;
}

fieldset > *, blockquote > *, dd > *{
	margin: auto 0 !important;
}

summary{
	display:block;
}

.helper, .tocpic, map area, a{
	cursor:pointer;
}

iframe, object{
	background-color: transparent
	border: none;
	margin:0;
	padding:0;
	overflow:visible;
}

/* ==|== Tweek Tags ========================================================= */
/* Fix italic font */
i, em, cite, q{
	font-style: normal;
	transform: skewX(-15deg);
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

/* Fix Modernizr cheking dom */
#modernizr {
	position:absolute;
	z-index:-999;
}
.xdebug-error{
	color:black !important;
	font-family:monospace !important;
}
/* ==|== Customize UI classes =============================================== */
/* form */
input, textarea, select, button{
	padding:.2em;
	margin:.1em .2em;
	vertical-align:middle;
}

/* Remove outline color from Safari and Chrome. */
input:focus, textarea:focus, select:focus, button:focus{
	outline: medium none !important;
}

input[type='text'], input[type='password'], input[type='file'],
input[type='tel'], input[type='url'], input[type='email'], 
input[type='datetime'], input[type='date'], input[type='month'], 
input[type='week'], input[type='time'], input[type='datetime-local'], 
input[type='number'], input[type='range'], input[type='color'], 
input[type='search'], textarea, select {
	border:1px solid silver;
	background-color:white;
	box-shadow:none !important;
	background-image:url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZlcnNpb249IjEuMCI%2BDQo8ZGVmcz4NCjxsaW5lYXJHcmFkaWVudCBpZD0iZ3JhZGllbnQiIHgxPSIwIiB5MT0iMCIgeDI9IjAiIHkyPSIxMDAlIj4NCjxzdG9wIG9mZnNldD0iMCUiIHN0eWxlPSJzdG9wLWNvbG9yOndoaXRlc21va2U7Ii8%2BDQo8c3RvcCBvZmZzZXQ9IjEwMCUiIHN0eWxlPSJzdG9wLWNvbG9yOndoaXRlOyIvPg0KPC9saW5lYXJHcmFkaWVudD4NCjwvZGVmcz4NCjxyZWN0IHg9IjAiIHk9IjAiIGZpbGw9InVybCgjZ3JhZGllbnQpIiB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiAvPg0KPC9zdmc%2B");
}

/* focus */
input[type='text']:focus, input[type='password']:focus, input[type='file']:focus,
input[type='tel']:focus, input[type='url']:focus, input[type='email']:focus,
input[type='datetime']:focus, input[type='date']:focus, input[type='month']:focus, 
input[type='week']:focus, input[type='time']:focus, input[type='datetime-local']:focus, 
input[type='number']:focus, input[type='range']:focus, input[type='color']:focus, 
input[type='search']:focus, textarea:focus, select:focus {
	box-shadow: 0 0 .3em dodgerblue !important;
	border:1px solid cornflowerblue;
}

/* hover */
input[type='text']:hover, input[type='password']:hover, input[type='file']:hover,
input[type='tel']:hover, input[type='url']:hover, input[type='email']:hover, 
input[type='datetime']:hover, input[type='date']:hover, input[type='month']:hover, 
input[type='week']:hover, input[type='time']:hover, input[type='datetime-local']:hover, 
input[type='number']:hover, input[type='range']:hover, input[type='color']:hover, 
input[type='search']:hover, textarea:hover, select:hover {
	border:1px solid cornflowerblue;
}

/* disabled */
input[type='text'][disabled], input[type='password'][disabled], input[type='file'][disabled],
input[type='tel'][disabled], input[type='url'][disabled], input[type='email'][disabled], 
input[type='datetime'][disabled], input[type='date'][disabled], input[type='month'][disabled], 
input[type='week'][disabled], input[type='time'][disabled], input[type='datetime-local'][disabled], 
input[type='number'][disabled], input[type='range'][disabled], input[type='color'][disabled], 
input[type='search'][disabled], textarea[disabled], select[disabled] {
	color:grey;
	border:1px solid lightgrey;
	background-color:whitesmoke;
	cursor: not-allowed;
	box-shadow: none;
}

input[type='text'][disabled]:hover, input[type='password'][disabled]:hover, input[type='file'][disabled]:hover,
input[type='tel'][disabled]:hover, input[type='url'][disabled]:hover, input[type='email'][disabled]:hover, 
input[type='datetime'][disabled]:hover, input[type='date'][disabled]:hover, input[type='month'][disabled]:hover, 
input[type='week'][disabled]:hover, input[type='time'][disabled]:hover, input[type='datetime-local'][disabled]:hover, 
input[type='number'][disabled]:hover, input[type='range'][disabled]:hover, input[type='color'][disabled]:hover, 
input[type='search'][disabled]:hover, textarea[disabled]:hover, select[disabled]:hover {
	border:1px solid lightgrey;
	background-color:whitesmoke;
}

input[disabled]:hover, select[disabled]:hover, textarea[disabled]:hover, option[disabled]:hover{
	box-shadow: none;
}

::-webkit-input-placeholder	{ color:grey; }
input:-moz-placeholder, textarea:-moz-placeholder { color:grey; }
input:-ms-placeholder, textarea:-ms-placeholder { color:grey; }
::-ms-input-placeholder	{ color:grey; }
:-ms-input-placeholder	{ color:grey; }

/* Require */
input[type='text'][required], input[type='password'][required], input[type='file'][required],
input[type='tel'][required], input[type='url'][required], input[type='email'][required], 
input[type='datetime'][required], input[type='date'][required], input[type='month'][required], 
input[type='week'][required], input[type='time'][required], input[type='datetime-local'][required], 
input[type='number'][required], input[type='range'][required], input[type='color'][required], 
input[type='search'][required], textarea[required], select[required] {
	border:1px solid lightpink;
	background-color:lavenderblush;
}

/* customize jQuery ui widget */
.ui-widget{
	font-size:100% !important;
}

.ui-widget .ui-widget{
	font-size:85%;
}

.ui-button{
	text-shadow: 0 1px 1px rgba(0,0,0,.3);
	box-shadow: 0 1px 2px rgba(0,0,0,.2);
}

input.ui-button{
	padding:.2em .7em !important;
}

.ui-icon{
	display: inline-block;
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

/* Table sorter */
.table_pager_widget{
	display:none;
}

.table_pager_widget input.pagedisplay{
	width:50px;
}

.table_pager_widget select.pagesize{
	width:80px;
}

/* PukiWiki Generic Widgets */

.pkwk_widget{
	padding:2px;
	margin:0;
	line-height:100%;
	list-style: none;
}
.pkwk_widget .ui-progressbar{
	height:20px;
	padding:0 !important;
}
.pkwk_widget .ui-state-default, .pkwk_widget .ui-widget-content{
	padding:2px;
	min-width:18px;
	min-height:18px;
}

.pkwk_widget li{
	cursor: pointer;
	float: left;
	text-align:center;
	margin:2px 0;
	font-size:93%;
}

.pkwk_widget .ui-corner-left{
	margin-left:2px;
}
.pkwk_widget .ui-corner-right{
	margin-right:2px;
}
.pkwk_widget .ui-corner-all{
	margin:2px;
}

.color{
	display: inline-block;
	width: 8px;
	height: 8px;
	line-height:100%;
	color: transparent;
}

#colors.pkwk_widget li{
	margin:0;
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
	margin-right:0.5em;
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

/* navibar.inc.php / toolbar.inc.php / topicpath.inc.php */
.navibar ul, .toolbar ul, .topicpath ul{
	list-style:none;
	display:inline;
}
.navibar ul:before{
	content:'[ ';
}
.navibar ul:after{
	content:' ]';
}
.navibar li, .toolbar li, .topicpath li{
	display: inline;
}
.navibar li:after{
	content:' | ';
}
.topicpath li:after{
	content : ' > '
}
.navibar li:last-child:after, .topicpath li:last-child:after {
	content:'';
}
.toolbar ul{
	margin:0 .2em;
}
.toolbar li{
	margin: 1px;
}
.toolbar li a:hover{
	text-decoration:none !important;
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
	-moz-border-radius-bottomleft: 17px;
	-moz-border-radius-topright: 17px;
	-webkit-border-top-right-radius: 17px;
	-webkit-border-bottom-left-radius: 17px;
}
.sf-shadow ul.sf-shadow-off {
	background: transparent;
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

/* ==|== JavaScript Stylesheet classes ====================================== */
.no-js{
	display:none;
}

/* for realedit.js */
#realview_outer {
	border:1px solid silver;
	background-color:white;
	padding:.2em;
	margin-bottom:.1em;
	height:200px;
	display:none;
	width:99%;
	resize: vertical;
	overflow-y: scroll;
}

#realview{
	padding:0.2em;
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

/* Table Sorter */
th.ui-state-hover {
	cursor: pointer;
}

/* Fix jQueryUI Icon */
th .ui-icon {
	float:right;
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

#tooltip > *{
	margin:0px;
	padding:0px;
}

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
#poptoc > ul, #poptoc > ol{
	margin:0 0 0 1em;
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

/* jPlayer */
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

/* Swfupload */

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

/* ==|== ui icon classes ==================================================== */
.pkwk-icon{
	display: inline-block;
	width: 16px;
	height: 16px;
	line-height:100%;
	background: transparent url('<?php echo $image_dir ?>iconset/default.png') -1000px -1000px no-repeat;
	vertical-align: middle;
	margin:0 2px;
	text-align:center;
	text-shadow:none;
	color: transparent;
}

.icon-new			{ background-position: 0px 0px; }
.icon-page			{ background-position: -18px 0px; }
.icon-add			{ background-position: -36px 0px; }
.icon-copy			{ background-position: -54px 0px; }
.icon-newsub		{ background-position: -72px 0px; }
.icon-edit			{ background-position: -90px 0px; }
.icon-diff			{ background-position: -108px 0px; }

.icon-source		{ background-position: 0px -18px; }
.icon-rename		{ background-position: -18px -18px; }
.icon-upload		{ background-position: -36px -18px; }
.icon-backup		{ background-position: -54px -18px; }
.icon-reload		{ background-position: -72px -18px; }
.icon-freeze		{ background-position: -90px -18px; }
.icon-unfreeze		{ background-position: -108px -18px; }

.icon-pdf			{ background-position: 0px -36px; }
.icon-list			{ background-position: -18px -36px; }
.icon-filelist		{ background-position: -36px -36px; }
.icon-skeylist		{ background-position: -54px -36px; }
.icon-broken		{ background-position: -72px -36px; }
.icon-referer		{ background-position: -90px -36px; }
.icon-linklist		{ background-position: -108px -36px; }

.icon-download		{ background-position: 0px -54px; }
.icon-search		{ background-position: -18px -54px; }
.icon-log_browse	{ background-position: -36px -54px; }
.icon-log_check		{ background-position: -54px -54px; }
.icon-log_down		{ background-position: -72px -54px; }
.icon-log_login		{ background-position: -90px -54px; }
.icon-log_update	{ background-position: -108px -54px; }

.icon-top			{ background-position: 0px -72px; }
.icon-home			{ background-position: -18px -72px; }
.icon-recent		{ background-position: -36px -72px; }
.icon-help			{ background-position: -54px -72px; }
.icon-alias			{ background-position: -72px -72px; }
.icon-glossary		{ background-position: -90px -72px; }
.icon-deleted		{ background-position: -108px -72px; }

.icon-rss			{ background-position: 0px -90px; }
.icon-opml			{ background-position: -18px -90px; }
.icon-share			{ background-position: -36px -90px; }
.icon-canonical		{ background-position: -54px -90px; }
.icon-trackback		{ background-position: -72px -90px; }
.icon-tags			{ background-position: -90px -90px; }
.icon-toc			{ background-position: -108px -90px; }

.icon-interwiki		{ background-position: 0px -108px; }
.icon-mail			{ background-position: -18px -108px; }

.pkwk-symbol{
	display: inline-block;
	width: 10px;
	height: 10px;
	margin:0 2px;
	padding:1px;
	line-height:100%;
	background: transparent url('<?php echo $image_dir ?>iconset/default.png') -1000px -1000px no-repeat;
	vertical-align: middle;
	text-align:center;
	text-shadow:none;
	color: transparent;
}

.symbol-edit		{ background-position: -54px -114px; }
.symbol-attach		{ background-position: -72px -114px; }
.symbol-external	{ background-position: -90px -114px; }
.symbol-internal	{ background-position: -108px -114px; }

/* ==|== emoji classes ====================================================== */
.emoji{
	font-family:Symbola;
	display: inline-block;
	width: 16px;
	height: 16px;
	line-height:100%;
	background: transparent url('<?php echo $image_dir ?>emoji/emoji.png') -1000px -1000px no-repeat;
	vertical-align: middle;
	text-align:center;
	text-shadow:none;
	color: transparent;
}

.emoji-sun{ background-position: 0px 0px;}
.emoji-cloud{ background-position: -18px 0px;}
.emoji-rain{ background-position: -36px 0px;}
.emoji-snow{ background-position: -54px 0px;}
.emoji-thunder{ background-position: -72px 0px;}
.emoji-typhoon{background-position: -90px 0px;}
.emoji-mist{background-position: -108px 0px;}
.emoji-sprinkle{background-position: -126px 0px;}
.emoji-aries{background-position: -144px 0px;}
.emoji-taurus{background-position: -162px 0px;}
.emoji-gemini{background-position: -180px 0px;}
.emoji-cancer{background-position: -198px 0px;}
.emoji-leo{background-position: -216px 0px;}
.emoji-virgo{background-position: -234px 0px;}
.emoji-libra{background-position: 0px -18px;}
.emoji-scorpius{background-position: -18px -18px;}
.emoji-sagittarius{background-position: -36px -18px;}
.emoji-capricornus{background-position: -54px -18px;}
.emoji-aquarius{background-position: -72px -18px;}
.emoji-pisces{background-position: -90px -18px;}
.emoji-sports{background-position: -108px -18px;}
.emoji-baseball{background-position: -126px -18px;}
.emoji-golf{background-position: -144px -18px;}
.emoji-tennis{background-position: -162px -18px;}
.emoji-soccer{background-position: -180px -18px;}
.emoji-ski{background-position: -198px -18px;}
.emoji-basketball{background-position: -216px -18px;}
.emoji-motorsports{background-position: -234px -18px;}
.emoji-pocketbell{background-position: 0px -36px;}
.emoji-train{background-position: -18px -36px;}
.emoji-subway{background-position: -36px -36px;}
.emoji-bullettrain{background-position: -54px -36px;}
.emoji-car{background-position: -72px -36px;}
.emoji-rvcar{background-position: -90px -36px;}
.emoji-bus{background-position: -108px -36px;}
.emoji-ship{background-position: -126px -36px;}
.emoji-airplane{background-position: -144px -36px;}
.emoji-house{background-position: -162px -36px;}
.emoji-building{background-position: -180px -36px;}
.emoji-postoffice{background-position: -198px -36px;}
.emoji-hospital{background-position: -216px -36px;}
.emoji-bank{background-position: -234px -36px;}
.emoji-atm{background-position: 0px -54px;}
.emoji-hotel{background-position: -18px -54px;}
.emoji-cvs{background-position: -36px -54px;}
.emoji-gasstation{background-position: -54px -54px;}
.emoji-parking{background-position: -72px -54px;}
.emoji-signaler{background-position: -90px -54px;}
.emoji-toilet{background-position: -108px -54px;}
.emoji-restaurant{background-position: -126px -54px;}
.emoji-cafe{background-position: -144px -54px;}
.emoji-bar{background-position: -162px -54px;}
.emoji-beer{background-position: -180px -54px;}
.emoji-fastfood{background-position: -198px -54px;}
.emoji-boutique{background-position: -216px -54px;}
.emoji-hairsalon{background-position: -234px -54px;}
.emoji-karaoke{background-position: 0px -72px;}
.emoji-movie{background-position: -18px -72px;}
.emoji-upwardright{background-position: -36px -72px;}
.emoji-carouselpony{background-position: -54px -72px;}
.emoji-music{background-position: -72px -72px;}
.emoji-art{background-position: -90px -72px;}
.emoji-drama{background-position: -108px -72px;}
.emoji-event{background-position: -126px -72px;}
.emoji-ticket{background-position: -144px -72px;}
.emoji-smoking{background-position: -162px -72px;}
.emoji-nosmoking{background-position: -180px -72px;}
.emoji-camera{background-position: -198px -72px;}
.emoji-bag{background-position: -216px -72px;}
.emoji-book{background-position: -234px -72px;}
.emoji-ribbon{background-position: 0px -90px;}
.emoji-present{background-position: -18px -90px;}
.emoji-birthday{background-position: -36px -90px;}
.emoji-telephone{background-position: -54px -90px;}
.emoji-mobilephone{background-position: -72px -90px;}
.emoji-memo{background-position: -90px -90px;}
.emoji-tv{background-position: -108px -90px;}
.emoji-game{background-position: -126px -90px;}
.emoji-cd{background-position: -144px -90px;}
.emoji-heart{background-position: -162px -90px;}
.emoji-spade{background-position: -180px -90px;}
.emoji-diamond{background-position: -198px -90px;}
.emoji-club{background-position: -216px -90px;}
.emoji-eye{background-position: -234px -90px;}
.emoji-ear{background-position: 0px -108px;}
.emoji-rock{background-position: -18px -108px;}
.emoji-scissors{background-position: -36px -108px;}
.emoji-paper{background-position: -54px -108px;}
.emoji-downwardright{background-position: -72px -108px;}
.emoji-upwardleft{background-position: -90px -108px;}
.emoji-foot{background-position: -108px -108px;}
.emoji-shoe{background-position: -126px -108px;}
.emoji-eyeglass{background-position: -144px -108px;}
.emoji-wheelchair{background-position: -162px -108px;}
.emoji-newmoon{background-position: -180px -108px;}
.emoji-moon1{background-position: -198px -108px;}
.emoji-moon2{background-position: -216px -108px;}
.emoji-moon3{background-position: -234px -108px;}
.emoji-fullmoon{background-position: 0px -126px;}
.emoji-dog{background-position: -18px -126px;}
.emoji-cat{background-position: -36px -126px;}
.emoji-yacht{background-position: -54px -126px;}
.emoji-xmas{background-position: -72px -126px;}
.emoji-downwardleft{background-position: -90px -126px;}
.emoji-phoneto{background-position: -108px -126px;}
.emoji-mailto{background-position: -126px -126px;}
.emoji-faxto{background-position: -144px -126px;}
.emoji-info01{background-position: -162px -126px;}
.emoji-info02{background-position: -180px -126px;}
.emoji-mail{background-position: -198px -126px;}
.emoji-by-d{background-position: -216px -126px;}
.emoji-d-point{background-position: -234px -126px;}
.emoji-yen{background-position: 0px -144px;}
.emoji-free{background-position: -18px -144px;}
.emoji-id{background-position: -36px -144px;}
.emoji-key{background-position: -54px -144px;}
.emoji-enter{background-position: -72px -144px;}
.emoji-clear{background-position: -90px -144px;}
.emoji-search{background-position: -108px -144px;}
.emoji-new{background-position: -126px -144px;}
.emoji-flag{background-position: -144px -144px;}
.emoji-freedial{background-position: -162px -144px;}
.emoji-sharp{background-position: -180px -144px;}
.emoji-mobaq{background-position: -198px -144px;}
.emoji-one{background-position: -216px -144px;}
.emoji-two{background-position: -234px -144px;}
.emoji-three{background-position: 0px -162px;}
.emoji-four{background-position: -18px -162px;}
.emoji-five{background-position: -36px -162px;}
.emoji-six{background-position: -54px -162px;}
.emoji-seven{background-position: -72px -162px;}
.emoji-eight{background-position: -90px -162px;}
.emoji-nine{background-position: -108px -162px;}
.emoji-zero{background-position: -126px -162px;}
.emoji-ok{background-position: -144px -162px;}
.emoji-heart01{background-position: -162px -162px;}
.emoji-heart02{background-position: -180px -162px;}
.emoji-heart03{background-position: -198px -162px;}
.emoji-heart04{background-position: -216px -162px;}
.emoji-happy01{background-position: -234px -162px;}
.emoji-angry{background-position: 0px -180px;}
.emoji-despair{background-position: -18px -180px;}
.emoji-sad{background-position: -36px -180px;}
.emoji-wobbly{background-position: -54px -180px;}
.emoji-up{background-position: -72px -180px;}
.emoji-note{background-position: -90px -180px;}
.emoji-spa{background-position: -108px -180px;}
.emoji-cute{background-position: -126px -180px;}
.emoji-kissmark{background-position: -144px -180px;}
.emoji-shine{background-position: -162px -180px;}
.emoji-flair{background-position: -180px -180px;}
.emoji-annoy{background-position: -198px -180px;}
.emoji-punch{background-position: -216px -180px;}
.emoji-bomb{background-position: -234px -180px;}
.emoji-notes{background-position: 0px -198px;}
.emoji-down{background-position: -18px -198px;}
.emoji-sleepy{background-position: -36px -198px;}
.emoji-sign01{background-position: -54px -198px;}
.emoji-sign02{background-position: -72px -198px;}
.emoji-sign03{background-position: -90px -198px;}
.emoji-impact{background-position: -108px -198px;}
.emoji-sweat01{background-position: -126px -198px;}
.emoji-sweat02{background-position: -144px -198px;}
.emoji-dash{background-position: -162px -198px;}
.emoji-sign04{background-position: -180px -198px;}
.emoji-sign05{background-position: -198px -198px;}
.emoji-slate{background-position: -216px -198px;}
.emoji-pouch{background-position: -234px -198px;}
.emoji-pen{background-position: 0px -216px;}
.emoji-shadow{background-position: -18px -216px;}
.emoji-chair{background-position: -36px -216px;}
.emoji-night{background-position: -54px -216px;}
.emoji-soon{background-position: -72px -216px;}
.emoji-on{background-position: -90px -216px;}
.emoji-end{background-position: -108px -216px;}
.emoji-clock{background-position: -126px -216px;}
.emoji-appli01{background-position: -144px -216px;}
.emoji-appli02{background-position: -162px -216px;}
.emoji-t-shirt{background-position: -180px -216px;}
.emoji-moneybag{background-position: -198px -216px;}
.emoji-rouge{background-position: -216px -216px;}
.emoji-denim{background-position: -234px -216px;}
.emoji-snowboard{background-position: 0px -234px;}
.emoji-bell{background-position: -18px -234px;}
.emoji-door{background-position: -36px -234px;}
.emoji-dollar{background-position: -54px -234px;}
.emoji-pc{background-position: -72px -234px;}
.emoji-loveletter{background-position: -90px -234px;}
.emoji-wrench{background-position: -108px -234px;}
.emoji-pencil{background-position: -126px -234px;}
.emoji-crown{background-position: -144px -234px;}
.emoji-ring{background-position: -162px -234px;}
.emoji-sandclock{background-position: -180px -234px;}
.emoji-bicycle{background-position: -198px -234px;}
.emoji-japanesetea{background-position: -216px -234px;}
.emoji-watch{background-position: -234px -234px;}
.emoji-think{background-position: 0px -252px;}
.emoji-confident{background-position: -18px -252px;}
.emoji-coldsweats01{background-position: -36px -252px;}
.emoji-coldsweats02{background-position: -54px -252px;}
.emoji-pout{background-position: -72px -252px;}
.emoji-gawk{background-position: -90px -252px;}
.emoji-lovely{background-position: -108px -252px;}
.emoji-good{background-position: -126px -252px;}
.emoji-bleah{background-position: -144px -252px;}
.emoji-wink{background-position: -162px -252px;}
.emoji-happy02{background-position: -180px -252px;}
.emoji-bearing{background-position: -198px -252px;}
.emoji-catface{background-position: -216px -252px;}
.emoji-crying{background-position: -234px -252px;}
.emoji-weep{background-position: 0px -270px;}
.emoji-ng{background-position: -18px -270px;}
.emoji-clip{background-position: -36px -270px;}
.emoji-copyright{background-position: -54px -270px;}
.emoji-tm{background-position: -72px -270px;}
.emoji-run{background-position: -90px -270px;}
.emoji-secret{background-position: -108px -270px;}
.emoji-recycle{background-position: -126px -270px;}
.emoji-r-mark{background-position: -144px -270px;}
.emoji-danger{background-position: -162px -270px;}
.emoji-ban{background-position: -180px -270px;}
.emoji-empty{background-position: -198px -270px;}
.emoji-pass{background-position: -216px -270px;}
.emoji-full{background-position: -234px -270px;}
.emoji-leftright{background-position: 0px -288px;}
.emoji-updown{background-position: -18px -288px;}
.emoji-school{background-position: -36px -288px;}
.emoji-wave{background-position: -54px -288px;}
.emoji-fuji{background-position: -72px -288px;}
.emoji-clover{background-position: -90px -288px;}
.emoji-cherry{background-position: -108px -288px;}
.emoji-tulip{background-position: -126px -288px;}
.emoji-banana{background-position: -144px -288px;}
.emoji-apple{background-position: -162px -288px;}
.emoji-bud{background-position: -180px -288px;}
.emoji-maple{background-position: -198px -288px;}
.emoji-cherryblossom{background-position: -216px -288px;}
.emoji-riceball{background-position: -234px -288px;}
.emoji-cake{background-position: 0px -306px;}
.emoji-bottle{background-position: -18px -306px;}
.emoji-noodle{background-position: -36px -306px;}
.emoji-bread{background-position: -54px -306px;}
.emoji-snail{background-position: -72px -306px;}
.emoji-chick{background-position: -90px -306px;}
.emoji-penguin{background-position: -108px -306px;}
.emoji-fish{background-position: -126px -306px;}
.emoji-delicious{background-position: -144px -306px;}
.emoji-smile{background-position: -162px -306px;}
.emoji-horse{background-position: -180px -306px;}
.emoji-pig{background-position: -198px -306px;}
.emoji-wine{background-position: -216px -306px;}
.emoji-shock{background-position: -234px -306px;}

.ie8 .emoji, .ie7 .emoji, .ie6 .emoji,
.ie8 .pkwk-icon, .ie7 .pkwk-icon, .ie6 .pkwk-icon {
	text-indent:-9999px;
	overflow:hidden;
}

/* ==|== non-semantic helper classes ======================================== */
.ir { display: block; border: 0; text-indent: -999em; overflow: hidden; background-color: transparent; background-repeat: no-repeat; text-align: left; direction: ltr; *line-height: 0; }
.ir br { display: none; }
.hidden { display: none !important; visibility: hidden; }
.visuallyhidden { border: 0; clip: rect(0 0 0 0); height: 1px; margin: -1px; overflow: hidden; padding: 0; position: absolute; width: 1px; }
.visuallyhidden.focusable:active, .visuallyhidden.focusable:focus { clip: auto; height: auto; margin: 0; overflow: visible; position: static; width: auto; }
.invisible { visibility: hidden; }
.clearfix:before, .clearfix:after { content: ""; display: table; }
.clearfix:after { clear: both; }
.clearfix { zoom: 1; }

/* ==|== print styles ======================================================= */
@media print {
	* { background: transparent !important; color: black !important; box-shadow:none !important; text-shadow: none !important; filter:none !important; -ms-filter: none !important; } /* Black prints faster: h5bp.com/s */
	a, a:visited { text-decoration: underline !important; }
	a[href]:after { content: " (" attr(href) ")"; }
	abbr[title]:after { content: " (" attr(title) ")"; }
	.ir a:after, a[href^="javascript:"]:after, a[href^="#"]:after { content: ""; }
	pre, blockquote { border: 1px solid #999; page-break-inside: avoid; }
	thead { display: table-header-group; }
	tr, img { page-break-inside: avoid; }
	img { max-width: 100% !important; }
	@page { margin: 0.5cm; }
	p, h2, h3 { orphans: 3; widows: 3; }
	h2, h3 { page-break-after: avoid; }

	form, nav, .pkwk-icon, .pkwk-symbol,
	.navibar, .toolbar, .navi, .message_box, .noprint, .tocpic, .sf-menu,.pkwk_widget,
	.style_calendar_prev, .style_calendar_next, .pkwk-symbol, #poptoc,  #toolbar, .ui-dialog, #topicpath{
		display:none !important;
		visibility: hidden !important;
	}
	
	.note_super {
		color:grey !important;
	}
	
	.emoji{
		font-family:Symbola !important;
		color:black;
		display: inline;
		text-indent:0px;
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