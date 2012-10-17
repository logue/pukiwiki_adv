<?php
// PukiWiki Advance Standard CSS.
// $Id: scripts.css.php,v 1.6.16 2012/09/23 14:41:00 Logue Exp $
// Copyright (C) 2010-2012 PukiWiki Advance Developer Team
//
ini_set('zlib.output_compression', 'Off');

$image_dir = isset($_GET['base']) ? $_GET['base']	: '../image/';
$iconset = isset($_GET['iconset']) ? $_GET['iconset']	: 'default';
$expire = isset($_GET['expire']) ? (int)$_GET['expire'] * 86400	: '604800';	// Default is 7 days.

// Send header
header('Content-Type: text/css; charset: UTF-8');
header('Cache-Control: private');
header('Expires: ' .gmdate('D, d M Y H:i:s',time() + $expire) . ' GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s', getlastmod() ) . ' GMT');
@ob_start('ob_gzhandler');
?>
@charset "UTF-8";
@namespace url(http://www.w3.org/1999/xhtml);

/* HTML5 ✰ Boilerplate */
/* ==|== normalize.css v2.0.1 | MIT License | git.io/normalize ===============*/
article,aside,details,figcaption,figure,footer,header,hgroup,nav,section,summary{display:block}
audio,canvas,video{display:inline-block}
audio:not([controls]){display:none;height:0}
[hidden]{display:none}
html{font-family:sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%}
body{margin:0}
a:focus{outline:thin dotted}
a:active,a:hover{outline:0}
h1{font-size:2em}
abbr[title]{border-bottom:1px dotted}
b,strong{font-weight:bold}
dfn{font-style:italic}
mark{background:#ff0;color:#000}
code,kbd,pre,samp{font-family:monospace,serif;font-size:1em}
pre{white-space:pre;white-space:pre-wrap;word-wrap:break-word}
q{quotes:"\201C" "\201D" "\2018" "\2019"}
small{font-size:80%}
sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:baseline}
sup{top:-0.5em}
sub{bottom:-0.25em}
img{border:0}
svg:not(:root){overflow:hidden}
figure{margin:0}
fieldset{border:1px solid #c0c0c0;margin:0 2px;padding:.35em .625em .75em}
legend{border:0;padding:0}
button,input,select,textarea{font-family:inherit;font-size:100%;margin:0}
button,input{line-height:normal}
button,html input[type="button"],input[type="reset"],input[type="submit"]{-webkit-appearance:button;cursor:pointer}
button[disabled],input[disabled]{cursor:default}
input[type="checkbox"],input[type="radio"]{box-sizing:border-box;padding:0}
input[type="search"]{-webkit-appearance:textfield;-moz-box-sizing:content-box;-webkit-box-sizing:content-box;box-sizing:content-box}
input[type="search"]::-webkit-search-cancel-button,input[type="search"]::-webkit-search-decoration{-webkit-appearance:none}
button::-moz-focus-inner,input::-moz-focus-inner{border:0;padding:0}
textarea{overflow:auto;vertical-align:top}
table{border-collapse:collapse;border-spacing:0}

/* ==|== HTML5 Boilerplate styles - h5bp.com ================================ */
html,button,input,select,textarea{color: #222;}
body{font-size: 13px;line-height: 1.4;}
::-moz-selection, ::selection {background: #b3d4fc;text-shadow: none;}
hr{display: block;height: 1px;border: 0;border-top: 1px solid #ccc;margin: 1em 0;padding: 0;}
img{vertical-align: middle;}
textarea{resize: vertical;}
.chromeframe{margin: 0.2em 0;background: #ccc;color: #000;padding: 0.2em 0;}

/* ==|== PukiWiki Advance Standard Font Set ================================= */

/* Font set */
html .ui-widget{
	font-family: 'Segoe UI', 'Trebuchet MS', Verdana, Arial, Sans-Serif;
}
/* Japanese */
:lang(ja), :lang(ja) .ui-widget{
	font-family: Meiryo, 'メイリオ', 'ヒラギノ角ゴ Pro W3', 'Hiragino Kaku Gothic Pro', Osaka, 'ＭＳＰ ゴシック';
}
/* Korean */
:lang(ko), :lang(ko) .ui-widget{
	font-family: 'AppleGothic', 'Malgun Gothic', '맑은 고딕', Gulim, Dotum, AppleGothic;
}
/* Chinese */
:lang(zh), :lang(zh) .ui-widget{
	font-family: 'Hiragino Sans GB W3', 'STHeiti', 'Apple LiGothic Medium', 'Microsoft YaHei', 'Microsoft JhengHei';
}

/* for Print font */
@media print{
	html {
		font-family: 'Lucida Bright', Century, 'Times New Roman', serif;
	}
	:lang(ja) {
		font-family: 'ヒラギノ明朝 Pro W3', 'Hiragino Mincho Pro', '平成明朝', 'ＭＳ Ｐ明朝', 'MS PMincho', serif;
	}
	:lang(zh) {
		font-family: 'Apple LiSung Light', 'STHeiti Light', 'PMingLiU', 'KaiTi', serif;
	}
	:lang(ko) {
		font-family: '바탕체', Batang, serif;
	}
}

textarea, select, option, input, var, pre, code, .ui-button-text{
	font-family: monospace !important;
}

fieldset {
	border: 1px silver solid;
	border-radius:.5em;
	padding:.5em;
	margin:1em;
}

legend {
	text-indent:.5em;
}

label {
	cursor: pointer;
}

figure{
	padding: 0 auto;
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

/* Table Tags */
.table_wrapper{
	text-align: center;
	width: 100%;
}
.style_table{
	border-spacing:2px;
	padding: 0;
	border: 0;
	text-align: left;
	border-collapse: separate;
	border-spacing: 1px;
	background-color: darkgray;
	min-width:30%;
}
.style_table_center{
	margin: 0 auto;
}
.style_table_left{
	margin:auto auto auto 0;
}
.style_table_right{
	margin:auto 0 auto auto;
}
.style_th{
	background-color: silver;
	padding: 5px;
	margin: 1px;
	white-space: nowrap;
	text-align: center;
}
.style_td{
	background-color: whitesmoke;
	padding: 5px;
	margin: 1px;
	vertical-align: top;
}
.style_td_blank{
	background-color: lightgrey;
}
.style_table tr:nth-child(even) .style_td{
	 background-color:whitesmoke;
}

/* Week and Month */
.style_week, .style_month{
	border: none !important;
}

.full_hr{
	clear: both;
}

.webkit ::-webkit-input-placeholder, .gecko input:-moz-placeholder{
	color: grey;
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

iframe, object{
	background-color: transparent
	border: none;
	margin: 0;
	padding: 0;
	overflow: visible;
}

/* ==|== Tweek Tags ========================================================= */
/* Fix italic font */
i, em, cite, q{
	font-style: normal;
	-webkit-transform: skewX(-15deg);
	-moz-transform: skewX(-15deg);
	-ms-transform: skewX(-15deg);
	-o-transform: skewX(-15deg);
	transform: skewX(-15deg);
}

/* Italic font fix for legacy IE */
.ie6 i, .ie6 em, .ie6 cite, .ie7 i, .ie7 em, .ie7 cite, .ie8 i, .ie8 em, .ie8 cite{
	font-family: 'MS PGothic', suns-serif;
	font-style: italic;
}

/* Fix Gecko Ruby Tag */
.gecko ruby {
	display: inline-table !important;
	text-align: center !important;
	white-space: nowrap !important;
	text-indent: 0 !important;
	margin: 0 !important;
	vertical-align: text-bottom !important;
	line-height: 1 !important;
}

.gecko ruby>rb,.gecko ruby>rbc {
	display: table-row-group !important;
	line-height: 1 !important;
}
.gecko ruby>rt,.gecko ruby>rbc+rtc {
	display: table-header-group !important;
	font-size: 50% !important;
	line-height: 1 !important;
	letter-spacing: 0 !important;
}

.gecko ruby>rbc+rtc+rtc {
	display: table-footer-group !important;
	font-size: 50% !important;
	line-height: 1 !important;
	letter-spacing: 0 !important;
}

.gecko rbc>rb,.gecko rtc>rt {
	display: table-cell !important;
	letter-spacing: 0 !important;
}

.gecko rp {
	display: none !important;
}

/* ==|== Customize UI classes =============================================== */
/* form */

input, textarea, select, button{
	padding: .2em;
	margin: .1em .2em;
	vertical-align: middle;
}
textarea[row="1"]{
	height:1em;
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
	border: 1px solid silver;
	background-color: white;
	box-shadow: none !important;
	background-image: url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIj8%2BPHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZlcnNpb249IjEuMCI%2BPGRlZnM%2BPGxpbmVhckdyYWRpZW50IHgxPSIwIiB5MT0iMCIgeDI9IjAiIHkyPSIxMDAlIiBpZD0iZ3JhZGllbnQiPjxzdG9wIG9mZnNldD0iMCUiIHN0b3AtY29sb3I9IndoaXRlc21va2UiIC8%2BPHN0b3Agb2Zmc2V0PSIxMDAlIiBzdG9wLWNvbG9yPSJ3aGl0ZSIgLz48L2xpbmVhckdyYWRpZW50PjwvZGVmcz48cmVjdCBmaWxsPSJ1cmwoI2dyYWRpZW50KSIgeD0iMCIgeT0iMCIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIvPjwvc3ZnPg%3D%3D');
	background-size: 100% 100%;
}

/* focus */
input[type='text']:focus, input[type='password']:focus, input[type='file']:focus,
input[type='tel']:focus, input[type='url']:focus, input[type='email']:focus,
input[type='datetime']:focus, input[type='date']:focus, input[type='month']:focus,
input[type='week']:focus, input[type='time']:focus, input[type='datetime-local']:focus,
input[type='number']:focus, input[type='range']:focus, input[type='color']:focus,
input[type='search']:focus, textarea:focus, select:focus {
	box-shadow: 0 0 .3em dodgerblue !important;
	border: 1px solid cornflowerblue;
}

/* hover */
input[type='text']:hover, input[type='password']:hover, input[type='file']:hover,
input[type='tel']:hover, input[type='url']:hover, input[type='email']:hover,
input[type='datetime']:hover, input[type='date']:hover, input[type='month']:hover,
input[type='week']:hover, input[type='time']:hover, input[type='datetime-local']:hover,
input[type='number']:hover, input[type='range']:hover, input[type='color']:hover,
input[type='search']:hover, textarea:hover, select:hover {
	border: 1px solid cornflowerblue;
}

/* disabled */
input[type='text'][disabled], input[type='password'][disabled], input[type='file'][disabled],
input[type='tel'][disabled], input[type='url'][disabled], input[type='email'][disabled],
input[type='datetime'][disabled], input[type='date'][disabled], input[type='month'][disabled],
input[type='week'][disabled], input[type='time'][disabled], input[type='datetime-local'][disabled],
input[type='number'][disabled], input[type='range'][disabled], input[type='color'][disabled],
input[type='search'][disabled], textarea[disabled], select[disabled] {
	color: grey;
	border: 1px solid lightgrey;
	background-color: whitesmoke;
	cursor: not-allowed;
	box-shadow: none;
}

/* disabled (hover) */
input[type='text'][disabled]:hover, input[type='password'][disabled]:hover, input[type='file'][disabled]:hover,
input[type='tel'][disabled]:hover, input[type='url'][disabled]:hover, input[type='email'][disabled]:hover,
input[type='datetime'][disabled]:hover, input[type='date'][disabled]:hover, input[type='month'][disabled]:hover,
input[type='week'][disabled]:hover, input[type='time'][disabled]:hover, input[type='datetime-local'][disabled]:hover,
input[type='number'][disabled]:hover, input[type='range'][disabled]:hover, input[type='color'][disabled]:hover,
input[type='search'][disabled]:hover, textarea[disabled]:hover, select[disabled]:hover {
	border: 1px solid lightgrey;
	background-color: whitesmoke;
}

input[disabled]:hover, select[disabled]:hover, textarea[disabled]:hover, option[disabled]:hover{
	box-shadow: none;
}

/* placeholder text color */
::-webkit-input-placeholder { color: grey; }
input:-moz-placeholder, textarea:-moz-placeholder { color: grey; }
input:-ms-placeholder, textarea:-ms-placeholder { color: grey; }
::-ms-input-placeholder { color: grey; }
:-ms-input-placeholder { color: grey; }

/* Require */
input[type='text'][required], input[type='password'][required], input[type='file'][required],
input[type='tel'][required], input[type='url'][required], input[type='email'][required],
input[type='datetime'][required], input[type='date'][required], input[type='month'][required],
input[type='week'][required], input[type='time'][required], input[type='datetime-local'][required],
input[type='number'][required], input[type='range'][required], input[type='color'][required],
input[type='search'][required], textarea[required], select[required] {
	color: maroon;
	border: 1px solid lightpink;
	background-color: lavenderblush;
	background-image: url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIj8%2BPHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZlcnNpb249IjEuMCI%2BPGRlZnM%2BPGxpbmVhckdyYWRpZW50IHgxPSIwIiB5MT0iMCIgeDI9IjAiIHkyPSIxMDAlIiBpZD0iZ3JhZGllbnQiPjxzdG9wIG9mZnNldD0iMCUiIHN0b3AtY29sb3I9ImxhdmVuZGVyYmx1c2giIC8%2BPHN0b3Agb2Zmc2V0PSIxMDAlIiBzdG9wLWNvbG9yPSJ3aGl0ZSIgLz48L2xpbmVhckdyYWRpZW50PjwvZGVmcz48cmVjdCBmaWxsPSJ1cmwoI2dyYWRpZW50KSIgeD0iMCIgeT0iMCIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIvPjwvc3ZnPg%3D%3D');
	background-size: 100% 100%;
}

/* customize jQuery ui widget */
.ui-widget{
	font-size: 100% !important;
}
.ui-widget .ui-widget{
	font-size: 85%;
}
.ui-button{
	text-shadow: 0 1px 1px rgba(0,0,0,.3);
	box-shadow: 0 1px 2px rgba(0,0,0,.2);
}
.ui-widget-header a:link{
	background-color: transparent;
	text-shadow: none;
}
.ui-buttonset .ui-button{
	margin:0 !important;
}

/* Message Box (for Debug and Error message) */
.message_box{
	padding: .7em;
	margin: .5em;
}
.message_box p, .message_box ul{
	margin: 0;
}
.message_box p .ui-icon{
	float: left; margin-right: .3em;
}
.js .no-js{
	display: none;
}

/* ==|== PukiWiki Adv. Misc classes ========================================= */
.underline{
	text-decoration: underline !important;
}
.small1{
	font-size: 77%;
}
.super_index{
	font-weight: bold;
	font-size: 77%;
	vertical-align: super;
}
.jumpmenu{
	font-size: 77%;
	text-align: right;
}

.size1 {
	font-size: xx-small;
}
.size2 {
	font-size: x-small;
}
.size3 {
	font-size: small;
}
.size4 {
	font-size: medium;
}
.size5 {
	font-size: large;
}
.size6 {
	font-size: x-large;
}
.size7 {
	font-size: xx-large;
}

/* html.php/catbody() */
.word0 {
	background-color: #FFFF66;
	text-shadow: none;
	color: black;
}
.word1 {
	background-color: #A0FFFF;
	text-shadow: none;
	color: black;
}
.word2 {
	background-color: #99FF99;
	text-shadow: none;
	color: black;
}
.word3 {
	background-color: #FF9999;
	text-shadow: none;
	color: black;
}
.word4 {
	background-color: #FF66FF;
	text-shadow: none;
	color: black;
}
.word5 {
	background-color: #880000;
	text-shadow: none;
	color: white;
}
.word6 {
	background-color: #00AA00;
	text-shadow: none;
	color: white;
}
.word7 {
	background-color: #886800;
	text-shadow: none;
	color: white;
}
.word8 {
	background-color: #004699;
	text-shadow: none;
	color: white;
}
.word9 {
	background-color: #990099;
	text-shadow: none;
	color: white;
}

/* html.php/edit_form() */
.edit_form { clear: both; }

.edit_form textarea{
	min-width: 95%;
	width: 99%;
	height:260px;
	margin: 0;
}
.ie8 .edit_form textarea{
	width: 780px;
}

/* Note */
.super_index {
	color: red;
	font-weight: bold;
	vertical-align: super;
	line-height: 100%;
}
.note_super {
	font-size: 77% !important;
	color: red !important;
	font-weight: bold;
	vertical-align: super;
	margin-right: .5em;
}

/* xdebug */
.xdebug-error{
	color: black !important;
	font-family: monospace !important;
}

.autosubmit{
	text-align: center;
}
.js .autosubmit input[type="submit"]{
	display: none;
}
/* ==|== PukiWiki Adv. Standard Plugin classes ============================== */
/* aname.inc.php */
.anchor_super {
	height: 8px;
	font-size: 8px !important;
	vertical-align: super;
}

/* amazon.inc.php */
.amazon_img {
	margin: 16px 10px 8px 8px;
	text-align: center;
}
.amazon_imgetc {
	margin: 0 8px 8px 8px;
	text-align:center;
}
.amazon_sub {
	font-size: 93%;
}
.amazon_avail {
	margin-left: 150px;
	font-size: 93%;
}
.amazon_td {
	text-align: center;
	word-wrap: break-word;
}
.amazon_tbl {
	width: 160px;
	font-size: 93%;
	text-align: center;
}

/* attach.inc.php / related.inc.php */
#attach, #related{
	display: block;
	float: none;
}
#attach dl, #related dl{
	margin: 0 1%;
	display: block;
}
#attach dl dt, #related dl dt{
	display: inline;
	font-weight: normal;
	margin: .1em .25em;
}
#attach dl dd, #related dl dd{
	display: inline;
	margin: .1em .25em;
}
.attach_info dl{
	float: left;
	display: block;
	overflow: visible;
}
.attach_info_image{
	right: 1em;
	position: absolute;
	z-index: -1;
}

/* backup.inc.php */
.add_block, .remove_block{
	display: block;
}
.backup_form{
	text-align: center;
}
.add_word, .add_block{
	background-color: #FFFF66;
}
.remove_word, .remove_block{
	background-color: #A0FFFF;
}

/* calendar.inc.php */
.style_calendar{
	width: 13em;
}
.style_calendar a{
	text-decoration: none;
}
.style_calendar a:hover{
	background-color: transparent;
}
.style_calendar a st rong{
	text-decoration:underline;
}
.style_calendar td, .style_calendar th{
	text-align: center;
	font-size: 85%;
}
.style_calendar_navi{
	display: block;
	text-align: center;
	list-style-image: none;
	list-style: none;
	margin: 0;
	padding: 0;
}
.style_calendar_title {
	display: inline;
	float: none;
}
.style_calendar_prev {
	display: inline;
	float: left;
	text-align: left;
}
.style_calendar_next {
	display: inline;
	float: right;
	text-align: right;
}
.style_calendar .style_calendar_day {
	background-color:ghostwhite;
}
.style_calendar .style_calendar_today {
	background-color: lightyellow;
}
.style_calendar .style_calendar_sat {
	background-color: aliceblue;
}
.style_calendar .style_calendar_sun, .style_calendar .style_calendar_holiday {
	background-color: lavenderblush;
}

/* week text color */
.week_sat{
	color: blue;
}
.week_day {
	color: black;
}
.week_sun, .week_holiday {
	color: red;
}
/* Fix English calendar week label */
:lang(en) .style_calendar_week{
	font-family:monospace !important;
	padding: .5em .2em;
}
.style_calendar_post p{
	padding: .25em 1em;
}
.style_calendar_post nav{
	display: none;
}

/* calendar_viewer.inc.php */
.style_calendar_viewer{
	float: left;
	width: 15em;
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
	font-size: 77%;
}

/* diff.inc.php */
.diff_added {
	color: blue;
}
.diff_removed {
	color: red;
}

/* include.inc.php */
.side_label {
	text-align:center;
}

/* navi.inc.php */
.navi {
	text-align: center;
}
.navi ul {
	list-style-image: none;
	list-style: none;
	margin: 0;
	padding: 0 .5em;
}
.navi_none {
	display: inline;
	float: none;
}
.navi_left {
	display: inline;
	float: left;
	text-align: left;
}
.navi_right {
	display: inline;
	float: right;
	text-align: right;
}

/* navibar.inc.php / toolbar.inc.php / topicpath.inc.php / list.inc.php / logview.inc.php / multilang.inc.php */
.navibar ul, .toolbar ul, .topicpath ul, .multilang ul, .page_initial ul{
	margin: 0;
	padding: 0;
	list-style: none;
	display: inline;
}
.navibar ul:before{
	content: '[ ';
}
.navibar ul:after{
	content: ' ]';
}
.navibar li, .toolbar li, .topicpath li, .multilang li, .page_initial li {
	display: inline;
}
.navibar li:not(:last-of-type):after, .page_initial li:not(:last-of-type):after{
	content: ' | ';
}
.topicpath li:not(:last-of-type):after{
	content: ' > '
}
.toolbar ul{
	margin: 0 .2em;
}
.toolbar li{
	margin: 1px;
}
.toolbar li a:hover{
	text-decoration: none !important;
}
.page_initial ul{
	margin: 0 auto;
	text-align:center;
	width:75%;
	display:block;
}
.list_pages, .referer_searchkey_list{
	column-count: 2;
	-moz-column-count: 2;
	-webkit-column-count: 2;
	-o-column-count: 2;
	-ms-column-count: 2;
}
@media only screen and (min-width : 800px) {
	.list_pages, .referer_searchkey_list{
		column-count: 3;
		-moz-column-count: 3;
		-webkit-column-count: 3;
		-o-column-count: 3;
		-ms-column-count: 3;
	}
}
@media only screen and (min-width : 1280px) {
	.list_pages, .referer_searchkey_list{
		column-count: 4;
		-moz-column-count: 4;
		-webkit-column-count: 4;
		-o-column-count: 4;
		-ms-column-count: 4;
	}
}

/* new.inc.php */
.comment_date {
	font-size: x-small;
}
.new1 {
	color:red;
	background-color: transparent;
	font-size: x-small;
}
.new5 {
	color:green;
	background-color: transparent;
	font-size: xx-small;
}

/* note */
#note ul{
	list-style: none;
}

/* ref.inc.php */
.img_margin {
	margin: 0 32px;
}

/* suckerfish.inc.php */
.sf-menu, .sf-menu * {
	margin: 0;
	padding: 0;
	list-style: none;
}
.sf-menu {
	line-height: 1;
}
.sf-menu ul {
	position: absolute;
	top: -999em;
}
.sf-menu ul li {
	width: 100%;
}
.sf-menu li:hover {
	visibility: inherit; /* fixes IE7 'sticky bug' */
}
.sf-menu li {
	float: left;
	position: relative;
}
.sf-menu li:hover ul,
.sf-menu li.sfHover ul {
	left: 0;
	z-index: 99;
}
.sf-menu li:hover li ul,
.sf-menu li.sfHover li ul {
	top: -999em;
}
.sf-menu li li:hover ul,
.sf-menu li li.sfHover ul {
	top: 0;
}
.sf-menu li li:hover li ul,
.sf-menu li li.sfHover li ul {
	top: -999em;
}
.sf-menu li li li:hover ul,
.sf-menu li li li.sfHover ul {
	top: 0;
}

/* tooltip.inc.php */
[aria-describedby='tooltip'] {
	border-bottom: 1px dotted;
	cursor: help;
}

/* vote.inc.php */
.vote_table{
	width:auto;
}
.vote_choise_td {
	padding:0 1em;
}
.vote_count_td {
	text-align:right;
	padding-right:1em;
}
.vote_form_td {
	padding:.1em;
	text-align:center;
}
/* ==|== JavaScript Stylesheet classes ====================================== */
.tocpic{
	display: inline;
	cursor: pointer;
}

/* for realedit.js */
#realview_outer {
	border: 1px solid silver;
	background-color: white;
	padding: .2em;
	margin-bottom: .1em;
	height: 200px;
	display: none;
	width: 99%;
	resize: vertical;
	overflow-y: scroll;
}

#realview{
	padding: .2em;
}

/* jQueryUI BlockUI */
#loading {
	display:none;
	cursor:progress;
}

/* Tooltip */
#tooltip{
	color: black;
	font-size: 93%;
	text-shadow: white 1px 1px 0;
	position: absolute; /*leave this alone*/
	display: none; /*leave this alone*/
	min-width: 16px;
	min-height: 16px;
	max-width: 400px;
	text-align: left;
	left: 0; /*leave this alone*/
	top: 0; /*leave this alone*/
	border: 1px solid gray;
	padding: .3em;
	opacity: .8;
	border-radius: .3em;
	background-color: white;
	box-shadow: 3px 3px 5px rgba(0, 0, 0, .6);
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
	-moz-opacity:.8;
	-moz-border-radius: .3em;
	-moz-box-shadow: 3px 3px 5px rgba(0, 0, 0, .6);
	background: -moz-linear-gradient(top, white, gainsboro);
}

.webkit #tooltip{
	-webkit-border-radius: .3em;
	-webkit-box-shadow: 3px 3px 5px rgba(0, 0, 0, .6);
	background: -webkit-gradient(linear, left top, left bottom, from(white), to(gainsboro));
}

.presto #tooltip{
	background: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxkZWZzPjxsaW5lYXJHcmFkaWVudCBpZD0iZ3JhZGllbnQiIHgxPSIwJSIgeTE9IjAlIiB4Mj0iMCUiIHkyPSIxMDAlIj48c3RvcCBvZmZzZXQ9IjAlIiBzdHlsZT0ic3RvcC1jb2xvcjpyZ2JhKDI1NSwyNTUsMjU1LDEpOyIgLz48c3RvcCBvZmZzZXQ9IjEwMCUiIHN0eWxlPSJzdG9wLWNvbG9yOnJnYmEoMjIwLDIyMCwyMjAsMSk7IiAvPjwvbGluZWFyR3JhZGllbnQ+PC9kZWZzPjxyZWN0IGZpbGw9InVybCgjZ3JhZGllbnQpIiBoZWlnaHQ9IjEwMCUiIHdpZHRoPSIxMDAlIiAvPjwvc3ZnPg==') !important;
}

#tooltip p{
	margin: 0px;
	padding: 0px;
}

/* popup toc */
#poptoc{
	background-color: lightyellow;
	background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZlcnNpb249IjEuMCI%2BDQo8ZGVmcz4NCjxsaW5lYXJHcmFkaWVudCBpZD0iZ3JhZGllbnQiIHgxPSIwIiB5MT0iMCIgeDI9IjAiIHkyPSIxMDAlIj4NCjxzdG9wIG9mZnNldD0iMCUiIHN0eWxlPSJzdG9wLWNvbG9yOml2b3J5OyIvPg0KPHN0b3Agb2Zmc2V0PSIxMDAlIiBzdHlsZT0ic3RvcC1jb2xvcjpsaWdodHllbGxvdzsiLz4NCjwvbGluZWFyR3JhZGllbnQ%2BDQo8L2RlZnM%2BDQo8cmVjdCB4PSIwIiB5PSIwIiBmaWxsPSJ1cmwoI2dyYWRpZW50KSIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgLz4NCjwvc3ZnPg0K');
	border: gray thin outset;
	display: none;
	font-size: 93%;
	max-width: 25em;
	min-width: 18em;
	opacity: .9;
	padding: .5em;
	position: absolute;
	text-shadow: white 1px 1px 0;
	z-index: 1;
	color: black;
}
#poptoc h1{
	font-size: small;
	font-weight: normal;
	padding: .3em;
	margin: 0;
	text-align: center;
}
#poptoc h1 a{
	text-decoration: none;
}
#poptoc h1 img{
	margin-bottom: -3px;
	margin-right: 2px;
}
#poptoc .nav{
	text-align: center;
}
#poptoc > ul, #poptoc > ol{
	margin: 0 0 0 1em;
}
.ie6 #poptoc, .ie7 #poptoc, .ie8 #poptoc {
	filter:
		progid:DXImageTransform.Microsoft.Alpha(opacity=90)
		progid:DXImageTransform.Microsoft.Gradient(GradientType=0,StartColorStr=ivory,EndColorStr=lemonchiffon) !important;
}
#poptoc a{
	color: blue !important;
	cursor: pointer;
}
#poptoc a:hover{
	text-shadow: none;
	background-color: #ccc;
}
#poptoc h1{
	color: navy;
	background-color: honeydew;
	font-size: small;
	font-weight: normal;
	padding: .3em;
	margin: 0;
	text-align: center;
	border: silver solid 1px;
	display: block;
}
#poptoc h1 a{color:navy; text-decoration:none;}
#poptoc h1 img {margin-bottom:-3px; margin-right: 2px;}
#poptoc .nav {text-indent:0em;border-top:1px gray solid; padding-top:0.2em;text-align:center; white-space: nowrap; }
#poptoc a.here{color: black; background: #EEEEEE; text-decoration: none; border:1px dotted gray;}

.tocpic {
	display: inline;
	cursor: pointer;
}
.hrefp, .topic {
	vertical-align: text-bottom;
}

/* ui-lightbox */
#ui-lightbox, #ui-lightbox-panorama-icon, #ui-lightbox-content-container,
#ui-lightbox-content, #ui-lightbox-content > *, #ui-lightbox-arrow,
#ui-lightbox-arrow > span, #ui-lightbox-bottombar, #ui-lightbox-title-wrapper,
#ui-lightbox-title, #ui-lightbox-bottombar-bottom, #ui-lightbox-button-prev,
#ui-lightbox-button-prev > span, #ui-lightbox-counter, #ui-lightbox-button-next,
#ui-lightbox-button-next > span, #ui-lightbox-button-close,
#ui-lightbox-button-close > span, #ui-lightbox-map, #ui-lightbox-map-viewport,
#ui-lightbox-overlay {
	margin: 0;
	padding: 0;
}

#ui-lightbox {
	font-size: 62.5%;
	padding: 5px;
	position: fixed;
	z-index: 9999;
	width: auto;
	height: auto;
}

#ui-lightbox-content-container {
	position: relative;
}

#ui-lightbox-content {
	border: 0;
	position: relative;
	width: 20px;
	height: 20px;
}

#ui-lightbox-content > * {
	display: block;
	position: absolute;
	z-index: 100;
}

#ui-lightbox-arrow {
	cursor: pointer;
	display: block;
	position: absolute;
	top: 50%;
	margin-top: -8px;
	z-index: 101;
}

.ui-lightbox-arrow-next {
	border-right: 0;
	right: 0;
}

.ui-lightbox-arrow-prev {
	border-left: 0;
	left: 0;
}

#ui-lightbox-panorama-icon {
	cursor: pointer;
	height: 32px;
	left: 20px;
	width: 32px;
	position: absolute;
	top: 20px;
	z-index: 110;
}

.ui-lightbox-panorama-icon-expand {
	background: url('<?php echo $image_dir ?>ajax/panorama.png') top left no-repeat;
}

.ui-lightbox-panorama-icon-expand-hover {
	background: url('<?php echo $image_dir ?>ajax/panorama.png') bottom left no-repeat;
}

.ui-lightbox-panorama-icon-shrink {
	background: url('<?php echo $image_dir ?>ajax/panorama.png') top right no-repeat;
}

.ui-lightbox-panorama-icon-shrink-hover {
	background: url('<?php echo $image_dir ?>ajax/panorama.png') bottom right no-repeat;
}

.ui-lightbox-loader {
	background: url('<?php echo $image_dir ?>ajax/loader.gif') center center no-repeat;
}

#ui-lightbox-bottombar {
	margin-top: 5px;
	padding: 5px;
	height: 40px;
	position: relative;
}

#ui-lightbox-bottombar > p {
	margin-right: 20px;
	height: 20px;
	line-height: 20px;
}

#ui-lightbox-bottombar-bottom {
	text-align: left;
}

#ui-lightbox-title-wrapper {
	font-size: 14px;
	height: 20px;
	overflow: hidden;
	text-align: left;
}

#ui-lightbox-counter {
	font-size: 9px;
	line-height: 20px;
	vertical-align: middle;
}

#ui-lightbox-separator {
	line-height: 20px;
	padding: 0 2px;
	vertical-align: middle;
}

#ui-lightbox-button-prev, #ui-lightbox-button-next, #ui-lightbox-button-play {
	display: inline-block;
	line-height: 20px;
	vertical-align: middle;
}

#ui-lightbox-button-close {
	line-height: 20px;
	position: absolute;
	top: 17px;
	right: 5px;
}

.ui-lightbox-button {
	cursor: pointer;
}

.ui-lightbox-button.ui-state-highlight {
	border-style: none;
	background: none;
}

#ui-lightbox-map {
	background-color: black;
	border: 1px solid white;
	filter:Alpha(Opacity=20);
	height: 100px;
	opacity: .30;
	position: fixed;
	right: 20px;
	top: 20px;
	width: 150px;
	z-index: 10000;
}

#ui-lightbox-map-viewport {
	border: 1px solid white;
	left: -1px; /*prevent from overlapping the map border*/
	position: absolute;
	top: -1px; /*prevent from overlapping the map border*/
}

#ui-lightbox-overlay {
	border: 0;
	position: fixed;
}

#ui-lightbox-error {
	background: url('<?php echo $image_dir ?>ajax/error_bg.png') repeat left top;
}

#ui-lightbox-error-message {
	color: #ffffff;
	font-size: 14px;
	line-height: 1.5;
	margin-bottom: 21px;
	padding-top: 274px;
	text-align: center;
}

#ui-lightbox-error-footer {
	text-align: center;
}

#ui-lightbox-error-footer > button {
	margin-right: 15px;
}

.ui-lightbox-error-icon-sign {
	background: url('<?php echo $image_dir ?>ajax/error_sign.png') no-repeat center 226px;		 
}

/* jPlayer */
#jp-container {
	position:relative;
	padding:20px 0;
}

#jp-container .jp-volume {
	position: relative;
	left: 520px;
	width: 100px;
	top: -.75em;
}

#jp-container .jp-bars {
	position: relative;
	left :160px;
	top: .25em;
	width: 280px;
}
#jp-container .ui-slider-handle{
	height: 1.6em;
}
#jp-container .jp-cast {
	padding-top: .5em;
}
#jp-container .jp-bars .jp-playback {
	height: 1em;;
}

/*
 * Table
 */
.fg-toolbar {
	margin: 0 auto;
}
.fg-toolbar, .fg-toolbar +.dataTable {
	width:100%;
}

.dataTable thead tr:last-child th{
	border-bottom: 1px solid black;
	cursor: pointer;
}

.dataTable tfoot tr:first-child th{
	border-top: 1px solid black;
}

.dataTable .odd .sorting_1 { color: black; background-color: #D3D6FF; }
.dataTable .odd .sorting_2 { color: black; background-color: #DADCFF; }
.dataTable .odd .sorting_3 { color: black; background-color: #E0E2FF; }
.dataTable .even td.sorting_1 { color: black; background-color: #EAEBFF; }
.dataTable .even td.sorting_2 { color: black; background-color: #F2F3FF; }
.dataTable .even td.sorting_3 { color: black; background-color: #F9F9FF; }



/*
 * Page length menu
 */
.dataTables_length {
	float: left;
}

/*
 * Filter
 */
.dataTables_filter {
	float: right;
	text-align: right;
}

/*
 * Table information
 */
.dataTables_info {
	float: left;
	white-space: nowrap;
}

/*
 * Pagination
 */
.dataTables_paginate {
	float: right;
	text-align: right;
}

/* Two button pagination - previous / next */
.paginate_disabled_previous, .paginate_enabled_previous, .paginate_disabled_next, .paginate_enabled_next {
	height: 22px;
	width: 19px;
	margin-left: 3px;
	float: left;
}

/* Full number pagination */
.paging_full_numbers {
	height: 22px;
}

.paging_full_numbers .paginate_button, .paging_full_numbers span.paginate_active {
	border: 1px solid #aaa;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
	padding: 2px 5px;
	margin: 0 3px;
	cursor: pointer;
	*cursor: hand;
}

/*
 * Processing indicator
 */
.dataTables_processing {
	position: absolute;
	top: 50%;
	left: 50%;
	width: 250px;
	height: 30px;
	margin-left: -125px;
	margin-top: -15px;
	padding: 14px 0 2px 0;
	border: 1px solid #ddd;
	text-align: center;
	color: #999;
	font-size: 14px;
	background-color: white;
}

/*
 * Scrolling
 */
.dataTables_scroll {
	clear: both;
}

/* Fix dataTables Pagenate */
.dataTables_paginate a{
	padding:2px;
}
.fg-toolbar{
	padding:2px;
}
.DataTables_sort_icon{
	float:right;
	display:inline-block;
}

/* Fix Modernizr cheking dom */
#modernizr {
	position: absolute;
	z-index: -999;
}

/* google search */
#goog-fixurl ul { list-style: none; padding: 0; margin: 0; }
#goog-fixurl form { margin: 0; }
#goog-wm-qt, #goog-wm-sb { border: 1px solid #bbb; font-size: 16px; line-height: normal; vertical-align: top; color: #444; border-radius: 2px; }
#goog-wm-qt { width: 220px; height: 20px; padding: 5px; margin: 5px 10px 0 0; box-shadow: inset 0 1px 1px #ccc; }
#goog-wm-sb { display: inline-block; height: 32px; padding: 0 10px; margin: 5px 0 0; white-space: nowrap; cursor: pointer; background-color: #f5f5f5; background-image: -webkit-linear-gradient(rgba(255,255,255,0), #f1f1f1); background-image: -moz-linear-gradient(rgba(255,255,255,0), #f1f1f1); background-image: -ms-linear-gradient(rgba(255,255,255,0), #f1f1f1); background-image: -o-linear-gradient(rgba(255,255,255,0), #f1f1f1); -webkit-appearance: none; -moz-appearance: none; appearance: none; *overflow: visible; *display: inline; *zoom: 1; }
#goog-wm-sb:hover, #goog-wm-sb:focus { border-color: #aaa; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1); background-color: #f8f8f8; }
#goog-wm-qt:focus, #goog-wm-sb:focus { border-color: #105cb6; outline: 0; color: #222; }

/* PukiWiki Generic Widgets */
.pkwk_widget{
	padding: 2px;
	margin: 0;
	line-height: 100%;
	list-style: none;
}
.pkwk_widget .ui-progressbar{
	height: 20px;
	padding:0 !important;
}
.pkwk_widget .ui-state-default, .pkwk_widget .ui-widget-content{
	padding: 2px;
	min-width: 18px;
	min-height: 18px;
}
.pkwk_widget li{
	cursor: pointer;
	float: left;
	text-align: center;
	margin: 2px 0;
	font-size: 93%;
}
.pkwk_widget .ui-corner-left{
	margin-left: 2px;
}
.pkwk_widget .ui-corner-right{
	margin-right: 2px;
}
.pkwk_widget .ui-corner-all{
	margin: 2px;
}

/* palette */
.color{
	display: inline-block;
	width: 8px;
	height: 8px;
	line-height: 100%;
	color: transparent;
}
#colors.pkwk_widget li{
	margin: 0;
}

/* WAI-ARIA */
ul[role=tablist]{
	margin: 0 auto;
	text-align: center;
	padding: 0;
	list-style: none;
}
li[role=tab]{
	display: inline;
	padding: 0 .25em;
}
.no-js ul[role=tablist]{
	width: 75%;
}
.no-js li[role=tab]{
	font-weight: bold;
}
.no-js li[role=tab]:after{
	content: '| ';
	font-weight: normal;
}
.no-js li[role=tab]:last-child:after{
	content: '';
}

.js fieldset[role=tabpanel]	{
	border: 0;
	margin: 0;
	padding: 1em 1.4em;
}
.js [role=tabpanel] legend{
	display: none;
}

/* social */
.social{
	list-style: none;
}

.social li{
	display: block;
	padding: 0 0.1em;
	float: left;
}

.fb-comments{
	width: 650px;
	padding-left: 3em;
}
/* ==|== ui icon classes ==================================================== */
.pkwk-icon{
	display: inline-block;
	width: 16px;
	height: 16px;
	line-height: 100%;
	background: transparent url('<?php echo $image_dir ?>iconset/<?php echo $iconset ?>.png') -1000px -1000px no-repeat;
	vertical-align: middle;
	margin:0 2px;
	text-align: center;
	text-shadow: none;
	color: transparent;
}

.icon-new			{ background-position: 0 0; }
.icon-page			{ background-position: -18px 0; }
.icon-add			{ background-position: -36px 0; }
.icon-copy			{ background-position: -54px 0; }
.icon-newsub		{ background-position: -72px 0; }
.icon-edit			{ background-position: -90px 0; }
.icon-diff			{ background-position: -108px 0; }

.icon-source		{ background-position: 0 -18px; }
.icon-rename		{ background-position: -18px -18px; }
.icon-upload		{ background-position: -36px -18px; }
.icon-backup		{ background-position: -54px -18px; }
.icon-reload		{ background-position: -72px -18px; }
.icon-freeze		{ background-position: -90px -18px; }
.icon-unfreeze		{ background-position: -108px -18px; }

.icon-pdf			{ background-position: 0 -36px; }
.icon-list			{ background-position: -18px -36px; }
.icon-filelist		{ background-position: -36px -36px; }
.icon-login			{ background-position: -54px -36px; }
.icon-logout		{ background-position: -72px -36px; }
.icon-referer		{ background-position: -90px -36px; }
/*
.icon-linklist		{ background-position: -108px -36px; }
*/

.icon-download		{ background-position: 0 -54px; }
.icon-search		{ background-position: -18px -54px; }
.icon-log			{ background-position: -36px -54px; }
/*
.icon-log_check		{ background-position: -54px -54px; }
.icon-log_down		{ background-position: -72px -54px; }
.icon-log_login		{ background-position: -90px -54px; }
.icon-log_update	{ background-position: -108px -54px; }
*/

.icon-top			{ background-position: 0 -72px; }
.icon-home			{ background-position: -18px -72px; }
.icon-recent		{ background-position: -36px -72px; }
.icon-help			{ background-position: -54px -72px; }
.icon-alias			{ background-position: -72px -72px; }
.icon-glossary		{ background-position: -90px -72px; }
.icon-deleted		{ background-position: -108px -72px; }

.icon-rss			{ background-position: 0 -90px; }
.icon-opml			{ background-position: -18px -90px; }
.icon-share			{ background-position: -36px -90px; }
.icon-canonical		{ background-position: -54px -90px; }
.icon-trackback		{ background-position: -72px -90px; }
.icon-tags			{ background-position: -90px -90px; }
.icon-toc			{ background-position: -108px -90px; }

.icon-interwiki		{ background-position: 0 -108px; }
.icon-mail			{ background-position: -18px -108px; }

.pkwk-symbol{
	display: inline-block;
	width: 8px;
	height: 8px;
	margin:0 2px;
	padding:1px;
	line-height:100%;
	background: transparent url('<?php echo $image_dir ?>iconset/default.png') -1000px -1000px no-repeat;
	vertical-align: middle;
	text-align:center;
	text-shadow:none;
	color: transparent;
}
.symbol-add			{ background-position: -36px -114px; }
.symbol-edit		{ background-position: -54px -114px; }
.symbol-attach		{ background-position: -72px -114px; }
.symbol-external	{ background-position: -90px -114px; }
.symbol-internal	{ background-position: -108px -114px; }

/* ==|== emoji classes ====================================================== */
.emoji{
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

.emoji-sun{ background-position: 0 0;}
.emoji-cloud{ background-position: -18px 0;}
.emoji-rain{ background-position: -36px 0;}
.emoji-snow{ background-position: -54px 0;}
.emoji-thunder{ background-position: -72px 0;}
.emoji-typhoon{background-position: -90px 0;}
.emoji-mist{background-position: -108px 0;}
.emoji-sprinkle{background-position: -126px 0;}
.emoji-aries{background-position: -144px 0;}
.emoji-taurus{background-position: -162px 0;}
.emoji-gemini{background-position: -180px 0;}
.emoji-cancer{background-position: -198px 0;}
.emoji-leo{background-position: -216px 0;}
.emoji-virgo{background-position: -234px 0;}
.emoji-libra{background-position: 0 -18px;}
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
.emoji-pocketbell{background-position: 0 -36px;}
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
.emoji-atm{background-position: 0 -54px;}
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
.emoji-karaoke{background-position: 0 -72px;}
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
.emoji-ribbon{background-position: 0 -90px;}
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
.emoji-ear{background-position: 0 -108px;}
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
.emoji-fullmoon{background-position: 0 -126px;}
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
.emoji-yen{background-position: 0 -144px;}
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
.emoji-three{background-position: 0 -162px;}
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
.emoji-angry{background-position: 0 -180px;}
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
.emoji-notes{background-position: 0 -198px;}
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
.emoji-pen{background-position: 0 -216px;}
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
.emoji-snowboard{background-position: 0 -234px;}
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
.emoji-think{background-position: 0 -252px;}
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
.emoji-weep{background-position: 0 -270px;}
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
.emoji-leftright{background-position: 0 -288px;}
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
.emoji-cake{background-position: 0 -306px;}
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

/* ==|== flag classes ====================================================== */
.flag{
	display: inline-block;
	width: 16px;
	height: 12px;
	background: transparent url('<?php echo $image_dir ?>plugin/flag.png') -1000px -1000px no-repeat;
	text-shadow:none;
	color: transparent;
}
.flag-ad{ background-position: 0 0; }
.flag-ae{ background-position: -17px 0; }
.flag-af{ background-position: -34px 0; }
.flag-ag{ background-position: -51px 0; }
.flag-ai{ background-position: -68px 0; }
.flag-al{ background-position: -85px 0; }
.flag-am{ background-position: -102px 0; }
.flag-an{ background-position: -119px 0; }
.flag-ao{ background-position: -136px 0; }
.flag-ar{ background-position: -153px 0; }
.flag-as{ background-position: -170px 0; }
.flag-at{ background-position: -187px 0; }
.flag-au{ background-position: -204px 0; }
.flag-aw{ background-position: -221px 0; }
.flag-ax{ background-position: -238px 0; }
.flag-az{ background-position: -255px 0; }
.flag-ba{ background-position: -272px 0; }
.flag-bb{ background-position: -289px 0; }
.flag-bd{ background-position: -306px 0; }
.flag-be{ background-position: -323px 0; }
.flag-bf{ background-position: -340px 0; }
.flag-bg{ background-position: -357px 0; }
.flag-bh{ background-position: -374px 0; }
.flag-bi{ background-position: -391px 0; }
.flag-bj{ background-position: -408px 0; }
.flag-bm{ background-position: -425px 0; }
.flag-bn{ background-position: -442px 0; }
.flag-bo{ background-position: -459px 0; }
.flag-br{ background-position: -476px 0; }
.flag-bs{ background-position: -493px 0; }
.flag-bt{ background-position: -510px 0; }
.flag-bv{ background-position: -527px 0; }
.flag-bw{ background-position: -544px 0; }
.flag-by{ background-position: -561px 0; }
.flag-bz{ background-position: -578px 0; }
.flag-ca{ background-position: -595px 0; }
.flag-catalonia{ background-position: -612px 0; }
.flag-cc{ background-position: -629px 0; }
.flag-cd{ background-position: -646px 0; }
.flag-cf{ background-position: -663px 0; }
.flag-cg{ background-position: -680px 0; }
.flag-ch{ background-position: -697px 0; }
.flag-ci{ background-position: -714px 0; }
.flag-ck{ background-position: -731px 0; }
.flag-cl{ background-position: -748px 0; }
.flag-cm{ background-position: -765px 0; }
.flag-cn{ background-position: -782px 0; }
.flag-co{ background-position: -799px 0; }
.flag-cr{ background-position: -816px 0; }
.flag-cs{ background-position: -833px 0; }
.flag-cu{ background-position: -850px 0; }
.flag-cv{ background-position: -867px 0; }
.flag-cx{ background-position: -884px 0; }
.flag-cy{ background-position: -901px 0; }
.flag-cz{ background-position: -918px 0; }
.flag-de{ background-position: -935px 0; }
.flag-dj{ background-position: -952px 0; }
.flag-dk{ background-position: -969px 0; }
.flag-dm{ background-position: -986px 0; }
.flag-do{ background-position: -1003px 0; }
.flag-dz{ background-position: -1020px 0; }
.flag-ec{ background-position: -1037px 0; }
.flag-ee{ background-position: -1054px 0; }
.flag-eg{ background-position: -1071px 0; }
.flag-eh{ background-position: -1088px 0; }
.flag-en{ background-position: -1105px 0; }
.flag-er{ background-position: -1122px 0; }
.flag-es{ background-position: -1139px 0; }
.flag-et{ background-position: -1156px 0; }
.flag-eu{ background-position: -1173px 0; }
.flag-fam{ background-position: -1190px 0; }
.flag-fi{ background-position: -1207px 0; }
.flag-fj{ background-position: -1224px 0; }
.flag-fk{ background-position: -1241px 0; }
.flag-fm{ background-position: -1258px 0; }
.flag-fo{ background-position: -1275px 0; }
.flag-fr{ background-position: -1292px 0; }
.flag-ga{ background-position: -1309px 0; }
.flag-gb{ background-position: -1326px 0; }
.flag-gd{ background-position: -1343px 0; }
.flag-ge{ background-position: -1360px 0; }
.flag-gf{ background-position: -1377px 0; }
.flag-gh{ background-position: -1394px 0; }
.flag-gi{ background-position: -1411px 0; }
.flag-gl{ background-position: -1428px 0; }
.flag-gm{ background-position: -1445px 0; }
.flag-gn{ background-position: -1462px 0; }
.flag-gp{ background-position: -1479px 0; }
.flag-gq{ background-position: -1496px 0; }
.flag-gr{ background-position: -1513px 0; }
.flag-gs{ background-position: -1530px 0; }
.flag-gt{ background-position: -1547px 0; }
.flag-gu{ background-position: -1564px 0; }
.flag-gw{ background-position: -1581px 0; }
.flag-gy{ background-position: -1598px 0; }
.flag-hk{ background-position: -1615px 0; }
.flag-hm{ background-position: -1632px 0; }
.flag-hn{ background-position: -1649px 0; }
.flag-hr{ background-position: -1666px 0; }
.flag-ht{ background-position: -1683px 0; }
.flag-hu{ background-position: -1700px 0; }
.flag-id{ background-position: -1717px 0; }
.flag-ie{ background-position: -1734px 0; }
.flag-il{ background-position: -1751px 0; }
.flag-in{ background-position: -1768px 0; }
.flag-io{ background-position: -1785px 0; }
.flag-iq{ background-position: -1802px 0; }
.flag-ir{ background-position: -1819px 0; }
.flag-is{ background-position: -1836px 0; }
.flag-it{ background-position: -1853px 0; }
.flag-jm{ background-position: -1870px 0; }
.flag-jo{ background-position: -1887px 0; }
.flag-jp{ background-position: -1904px 0; }
.flag-ke{ background-position: -1921px 0; }
.flag-kg{ background-position: -1938px 0; }
.flag-kh{ background-position: -1955px 0; }
.flag-ki{ background-position: -1972px 0; }
.flag-km{ background-position: -1989px 0; }
.flag-kn{ background-position: 0 -12px; }
.flag-kp{ background-position: -17px -12px; }
.flag-kr{ background-position: -34px -12px; }
.flag-kw{ background-position: -51px -12px; }
.flag-ky{ background-position: -68px -12px; }
.flag-kz{ background-position: -85px -12px; }
.flag-la{ background-position: -102px -12px; }
.flag-lb{ background-position: -119px -12px; }
.flag-lc{ background-position: -136px -12px; }
.flag-li{ background-position: -153px -12px; }
.flag-lk{ background-position: -170px -12px; }
.flag-lr{ background-position: -187px -12px; }
.flag-ls{ background-position: -204px -12px; }
.flag-lt{ background-position: -221px -12px; }
.flag-lu{ background-position: -238px -12px; }
.flag-lv{ background-position: -255px -12px; }
.flag-ly{ background-position: -272px -12px; }
.flag-ma{ background-position: -289px -12px; }
.flag-mc{ background-position: -306px -12px; }
.flag-md{ background-position: -323px -12px; }
.flag-me{ background-position: -340px -12px; width: 16px; height: 12px; }
.flag-mg{ background-position: -357px -12px; }
.flag-mh{ background-position: -374px -12px; }
.flag-mk{ background-position: -391px -12px; }
.flag-ml{ background-position: -408px -12px; }
.flag-mm{ background-position: -425px -12px; }
.flag-mn{ background-position: -442px -12px; }
.flag-mo{ background-position: -459px -12px; }
.flag-mp{ background-position: -476px -12px; }
.flag-mq{ background-position: -493px -12px; }
.flag-mr{ background-position: -510px -12px; }
.flag-ms{ background-position: -527px -12px; }
.flag-mt{ background-position: -544px -12px; }
.flag-mu{ background-position: -561px -12px; }
.flag-mv{ background-position: -578px -12px; }
.flag-mw{ background-position: -595px -12px; }
.flag-mx{ background-position: -612px -12px; }
.flag-my{ background-position: -629px -12px; }
.flag-mz{ background-position: -646px -12px; }
.flag-na{ background-position: -663px -12px; }
.flag-nc{ background-position: -680px -12px; }
.flag-ne{ background-position: -697px -12px; }
.flag-nf{ background-position: -714px -12px; }
.flag-ng{ background-position: -731px -12px; }
.flag-ni{ background-position: -748px -12px; }
.flag-nl{ background-position: -765px -12px; }
.flag-no{ background-position: -782px -12px; }
.flag-np{ background-position: -799px -12px; }
.flag-nr{ background-position: -816px -12px; }
.flag-nu{ background-position: -833px -12px; }
.flag-nz{ background-position: -850px -12px; }
.flag-om{ background-position: -867px -12px; }
.flag-pa{ background-position: -884px -12px; }
.flag-pe{ background-position: -901px -12px; }
.flag-pf{ background-position: -918px -12px; }
.flag-pg{ background-position: -935px -12px; }
.flag-ph{ background-position: -952px -12px; }
.flag-pk{ background-position: -969px -12px; }
.flag-pl{ background-position: -986px -12px; }
.flag-pm{ background-position: -1003px -12px; }
.flag-pn{ background-position: -1020px -12px; }
.flag-pr{ background-position: -1037px -12px; }
.flag-ps{ background-position: -1054px -12px; }
.flag-pt{ background-position: -1071px -12px; }
.flag-pw{ background-position: -1088px -12px; }
.flag-py{ background-position: -1105px -12px; }
.flag-qa{ background-position: -1122px -12px; }
.flag-re{ background-position: -1139px -12px; }
.flag-ro{ background-position: -1156px -12px; }
.flag-rs{ background-position: -1173px -12px; }
.flag-ru{ background-position: -1190px -12px; }
.flag-rw{ background-position: -1207px -12px; }
.flag-sa{ background-position: -1224px -12px; }
.flag-sb{ background-position: -1241px -12px; }
.flag-sc{ background-position: -1258px -12px; }
.flag-scotland{ background-position: -1275px -12px; }
.flag-sd{ background-position: -1292px -12px; }
.flag-se{ background-position: -1309px -12px; }
.flag-sg{ background-position: -1326px -12px; }
.flag-sh{ background-position: -1343px -12px; }
.flag-si{ background-position: -1360px -12px; }
.flag-sj{ background-position: -1377px -12px; }
.flag-sk{ background-position: -1394px -12px; }
.flag-sl{ background-position: -1411px -12px; }
.flag-sm{ background-position: -1428px -12px; }
.flag-sn{ background-position: -1445px -12px; }
.flag-so{ background-position: -1462px -12px; }
.flag-sr{ background-position: -1479px -12px; }
.flag-st{ background-position: -1496px -12px; }
.flag-sv{ background-position: -1513px -12px; }
.flag-sy{ background-position: -1530px -12px; }
.flag-sz{ background-position: -1547px -12px; }
.flag-tc{ background-position: -1564px -12px; }
.flag-td{ background-position: -1581px -12px; }
.flag-tf{ background-position: -1598px -12px; }
.flag-tg{ background-position: -1615px -12px; }
.flag-th{ background-position: -1632px -12px; }
.flag-tj{ background-position: -1649px -12px; }
.flag-tk{ background-position: -1666px -12px; }
.flag-tl{ background-position: -1683px -12px; }
.flag-tm{ background-position: -1700px -12px; }
.flag-tn{ background-position: -1717px -12px; }
.flag-to{ background-position: -1734px -12px; }
.flag-tr{ background-position: -1751px -12px; }
.flag-tt{ background-position: -1768px -12px; }
.flag-tv{ background-position: -1785px -12px; }
.flag-tw{ background-position: -1802px -12px; }
.flag-tz{ background-position: -1819px -12px; }
.flag-ua{ background-position: -1836px -12px; }
.flag-ug{ background-position: -1853px -12px; }
.flag-um{ background-position: -1870px -12px; }
.flag-us{ background-position: -1887px -12px; }
.flag-uy{ background-position: -1904px -12px; }
.flag-uz{ background-position: -1921px -12px; }
.flag-va{ background-position: -1938px -12px; }
.flag-vc{ background-position: -1955px -12px; }
.flag-ve{ background-position: -1972px -12px; }
.flag-vg{ background-position: -1989px -12px; }
.flag-vi{ background-position: 0 -25px; }
.flag-vn{ background-position: -17px -25px; }
.flag-vu{ background-position: -34px -25px; }
.flag-wales{ background-position: -51px -25px; }
.flag-wf{ background-position: -68px -25px; }
.flag-ws{ background-position: -85px -25px; }
.flag-ye{ background-position: -102px -25px; }
.flag-yt{ background-position: -119px -25px; }
.flag-za{ background-position: -136px -25px; }
.flag-zm{ background-position: -153px -25px; }
.flag-zw{ background-position: -170px -25px; }

/* ==|== os classes ======================================================== */
.os{
	display: inline-block;
	width: 16px;
	height: 16px;
	background: transparent url('<?php echo $image_dir ?>plugin/os.png') -1000px -1000px no-repeat;
	text-shadow:none;
	color: transparent;
}
.os-aix{ background-position: 0 0; width: 16px; height: 16px; }
.os-amigaos{ background-position: -17px 0; width: 16px; height: 16px; }
.os-apple{ background-position: -34px 0; width: 16px; height: 16px; }
.os-atari{ background-position: -51px 0; width: 16px; height: 16px; }
.os-beos{ background-position: -68px 0; width: 16px; height: 16px; }
.os-blackberry{ background-position: -85px 0; width: 16px; height: 16px; }
.os-bsd{ background-position: -102px 0; width: 16px; height: 16px; }
.os-bsddflybsd{ background-position: -119px 0; width: 16px; height: 16px; }
.os-bsdfreebsd{ background-position: -136px 0; width: 16px; height: 16px; }
.os-bsdi{ background-position: -153px 0; width: 14px; height: 14px; }
.os-bsdkfreebsd{ background-position: -168px 0; width: 15px; height: 16px; }
.os-bsdnetbsd{ background-position: -184px 0; width: 16px; height: 16px; }
.os-bsdopenbsd{ background-position: -201px 0; width: 16px; height: 16px; }
.os-commodore{ background-position: -218px 0; width: 16px; height: 16px; }
.os-cpm{ background-position: -235px 0; width: 16px; height: 16px; }
.os-debian{ background-position: -252px 0; width: 16px; height: 16px; }
.os-digital{ background-position: -269px 0; width: 14px; height: 14px; }
.os-dos{ background-position: -284px 0; width: 16px; height: 16px; }
.os-dreamcast{ background-position: -301px 0; width: 16px; height: 16px; }
.os-freebsd{ background-position: -318px 0; width: 16px; height: 16px; }
.os-gnu{ background-position: -335px 0; width: 16px; height: 16px; }
.os-hpux{ background-position: -352px 0; width: 16px; height: 16px; }
.os-ibm{ background-position: -369px 0; width: 16px; height: 16px; }
.os-imode{ background-position: -386px 0; width: 16px; height: 16px; }
.os-inferno{ background-position: -403px 0; width: 16px; height: 16px; }
.os-ios{ background-position: -420px 0; width: 16px; height: 16px; }
.os-iphone{ background-position: -437px 0; width: 16px; height: 16px; }
.os-irix{ background-position: -454px 0; width: 16px; height: 16px; }
.os-j2me{ background-position: -471px 0; width: 16px; height: 16px; }
.os-java{ background-position: -488px 0; width: 16px; height: 16px; }
.os-kfreebsd{ background-position: -505px 0; width: 15px; height: 16px; }
.os-linux{ background-position: -521px 0; width: 16px; height: 16px; }
.os-linuxandroid{ background-position: -538px 0; width: 16px; height: 16px; }
.os-linuxasplinux{ background-position: -555px 0; width: 16px; height: 16px; }
.os-linuxcentos{ background-position: -572px 0; width: 16px; height: 16px; }
.os-linuxdebian{ background-position: -589px 0; width: 16px; height: 16px; }
.os-linuxfedora{ background-position: -606px 0; width: 16px; height: 16px; }
.os-linuxgentoo{ background-position: -623px 0; width: 15px; height: 16px; }
.os-linuxmandr{ background-position: -639px 0; width: 16px; height: 16px; }
.os-linuxpclinuxos{ background-position: -656px 0; width: 16px; height: 16px; }
.os-linuxredhat{ background-position: -673px 0; width: 16px; height: 16px; }
.os-linuxsuse{ background-position: -690px 0; width: 16px; height: 16px; }
.os-linuxubuntu{ background-position: -707px 0; width: 16px; height: 16px; }
.os-linuxvine{ background-position: -724px 0; width: 16px; height: 16px; }
.os-linuxzenwalk{ background-position: -741px 0; width: 16px; height: 16px; }
.os-mac{ background-position: -758px 0; width: 16px; height: 16px; }
.os-macintosh{ background-position: -775px 0; width: 16px; height: 16px; }
.os-macosx{ background-position: -792px 0; width: 16px; height: 16px; }
.os-netbsd{ background-position: -809px 0; width: 16px; height: 16px; }
.os-netware{ background-position: -826px 0; width: 16px; height: 16px; }
.os-next{ background-position: -843px 0; width: 16px; height: 16px; }
.os-openbsd{ background-position: -860px 0; width: 16px; height: 16px; }
.os-os2{ background-position: -877px 0; width: 16px; height: 16px; }
.os-osf{ background-position: -894px 0; width: 16px; height: 16px; }
.os-palmos{ background-position: -911px 0; width: 16px; height: 16px; }
.os-psp{ background-position: -928px 0; width: 16px; height: 16px; }
.os-qnx{ background-position: -945px 0; width: 16px; height: 16px; }
.os-riscos{ background-position: -962px 0; width: 16px; height: 16px; }
.os-sco{ background-position: -979px 0; width: 16px; height: 16px; }
.os-sunos{ background-position: -996px 0; width: 16px; height: 16px; }
.os-syllable{ background-position: -1013px 0; width: 16px; height: 16px; }
.os-symbian{ background-position: -1030px 0; width: 16px; height: 16px; }
.os-unix{ background-position: -1047px 0; width: 16px; height: 16px; }
.os-unknown{ background-position: -1064px 0; width: 16px; height: 16px; }
.os-vms{ background-position: -1081px 0; width: 16px; height: 16px; }
.os-webtv{ background-position: -1098px 0; width: 16px; height: 16px; }
.os-wii{ background-position: -1115px 0; width: 16px; height: 16px; }
.os-win{ background-position: -1132px 0; width: 16px; height: 16px; }
.os-win16{ background-position: -1149px 0; width: 16px; height: 16px; }
.os-win2000{ background-position: -1166px 0; width: 16px; height: 15px; }
.os-win2003{ background-position: -1183px 0; width: 16px; height: 16px; }
.os-win2008{ background-position: -1200px 0; width: 16px; height: 16px; }
.os-win7{ background-position: -1217px 0; width: 16px; height: 16px; }
.os-win95{ background-position: -1234px 0; width: 16px; height: 14px; }
.os-win98{ background-position: -1251px 0; width: 16px; height: 14px; }
.os-wince{ background-position: -1268px 0; width: 16px; height: 16px; }
.os-winlong{ background-position: -1285px 0; width: 16px; height: 16px; }
.os-winme{ background-position: -1302px 0; width: 16px; height: 16px; }
.os-winnt{ background-position: -1319px 0; width: 16px; height: 14px; }
.os-winunknown{ background-position: -1336px 0; width: 16px; height: 16px; }
.os-winvista{ background-position: -1353px 0; width: 16px; height: 16px; }
.os-winxbox{ background-position: -1370px 0; width: 16px; height: 16px; }
.os-winxp{ background-position: -1387px 0; width: 16px; height: 16px; }

/* ==|== browser classes ====================================================== */
.browser{
	display: inline-block;
	width: 16px;
	height: 16px;
	background: transparent url('<?php echo $image_dir ?>plugin/browser.png') -1000px -1000px no-repeat;
	text-shadow:none;
	color: transparent;
}
.browser-abilon{ background-position: 0 0; width: 16px; height: 16px; }
.browser-adobe{ background-position: -17px 0; width: 16px; height: 16px; }
.browser-akregator{ background-position: -34px 0; width: 16px; height: 16px; }
.browser-alcatel{ background-position: -51px 0; width: 16px; height: 16px; }
.browser-amaya{ background-position: -68px 0; width: 16px; height: 16px; }
.browser-amigavoyager{ background-position: -85px 0; width: 16px; height: 16px; }
.browser-analogx{ background-position: -102px 0; width: 16px; height: 16px; }
.browser-android{ background-position: -119px 0; width: 16px; height: 16px; }
.browser-apt{ background-position: -136px 0; width: 16px; height: 16px; }
.browser-avant{ background-position: -153px 0; width: 16px; height: 16px; }
.browser-aweb{ background-position: -170px 0; width: 16px; height: 16px; }
.browser-bpftp{ background-position: -187px 0; width: 16px; height: 16px; }
.browser-bytel{ background-position: -204px 0; width: 16px; height: 16px; }
.browser-chimera{ background-position: -221px 0; width: 16px; height: 16px; }
.browser-chrome{ background-position: -238px 0; width: 16px; height: 16px; }
.browser-cyberdog{ background-position: -255px 0; width: 16px; height: 16px; }
.browser-da{ background-position: -272px 0; width: 16px; height: 16px; }
.browser-dillo{ background-position: -289px 0; width: 16px; height: 16px; }
.browser-doris{ background-position: -306px 0; width: 16px; height: 16px; }
.browser-dreamcast{ background-position: -323px 0; width: 16px; height: 16px; }
.browser-ecatch{ background-position: -340px 0; width: 16px; height: 16px; }
.browser-encompass{ background-position: -357px 0; width: 16px; height: 16px; }
.browser-epiphany{ background-position: -374px 0; width: 16px; height: 16px; }
.browser-ericsson{ background-position: -391px 0; width: 16px; height: 16px; }
.browser-feeddemon{ background-position: -408px 0; width: 16px; height: 16px; }
.browser-feedreader{ background-position: -425px 0; width: 16px; height: 16px; }
.browser-firefox{ background-position: -442px 0; width: 16px; height: 16px; }
.browser-flashget{ background-position: -459px 0; width: 16px; height: 16px; }
.browser-flock{ background-position: -476px 0; width: 16px; height: 16px; }
.browser-fpexpress{ background-position: -493px 0; width: 16px; height: 16px; }
.browser-fresco{ background-position: -510px 0; width: 14px; height: 14px; }
.browser-freshdownload{ background-position: -525px 0; width: 16px; height: 16px; }
.browser-frontpage{ background-position: -542px 0; width: 16px; height: 16px; }
.browser-galeon{ background-position: -559px 0; width: 16px; height: 16px; }
.browser-getright{ background-position: -576px 0; width: 16px; height: 16px; }
.browser-gnome{ background-position: -593px 0; width: 16px; height: 16px; }
.browser-gnus{ background-position: -610px 0; width: 16px; height: 16px; }
.browser-gozilla{ background-position: -627px 0; width: 16px; height: 16px; }
.browser-hotjava{ background-position: -644px 0; width: 16px; height: 16px; }
.browser-httrack{ background-position: -661px 0; width: 16px; height: 16px; }
.browser-ibrowse{ background-position: -678px 0; width: 16px; height: 16px; }
.browser-icab{ background-position: -695px 0; width: 16px; height: 16px; }
.browser-icecat{ background-position: -712px 0; width: 16px; height: 16px; }
.browser-iceweasel{ background-position: -729px 0; width: 16px; height: 16px; }
.browser-java{ background-position: -746px 0; width: 16px; height: 16px; }
.browser-jetbrains_omea{ background-position: -763px 0; width: 16px; height: 16px; }
.browser-kmeleon{ background-position: -780px 0; width: 16px; height: 16px; }
.browser-konqueror{ background-position: -797px 0; width: 16px; height: 16px; }
.browser-leechget{ background-position: -814px 0; width: 16px; height: 16px; }
.browser-lg{ background-position: -831px 0; width: 16px; height: 16px; }
.browser-lotusnotes{ background-position: -848px 0; width: 16px; height: 16px; }
.browser-lynx{ background-position: -865px 0; width: 16px; height: 16px; }
.browser-macweb{ background-position: -882px 0; width: 16px; height: 16px; }
.browser-mediaplayer{ background-position: -899px 0; width: 16px; height: 16px; }
.browser-motorola{ background-position: -916px 0; width: 16px; height: 16px; }
.browser-mozilla{ background-position: -933px 0; width: 16px; height: 16px; }
.browser-mplayer{ background-position: -950px 0; width: 16px; height: 16px; }
.browser-msie{ background-position: -967px 0; width: 16px; height: 16px; }
.browser-multizilla{ background-position: -984px 0; width: 16px; height: 16px; }
.browser-ncsa_mosaic{ background-position: -1001px 0; width: 16px; height: 16px; }
.browser-neon{ background-position: -1018px 0; width: 16px; height: 16px; }
.browser-netnewswire{ background-position: -1035px 0; width: 16px; height: 16px; }
.browser-netpositive{ background-position: -1052px 0; width: 16px; height: 16px; }
.browser-netscape{ background-position: -1069px 0; width: 16px; height: 16px; }
.browser-netshow{ background-position: -1086px 0; width: 16px; height: 16px; }
.browser-newsfire{ background-position: -1103px 0; width: 16px; height: 16px; }
.browser-newsgator{ background-position: -1120px 0; width: 16px; height: 16px; }
.browser-newzcrawler{ background-position: -1137px 0; width: 16px; height: 16px; }
.browser-nokia{ background-position: -1154px 0; width: 16px; height: 16px; }
.browser-notavailable{ background-position: -1171px 0; width: 14px; height: 14px; }
.browser-omniweb{ background-position: -1186px 0; width: 16px; height: 16px; }
.browser-opera{ background-position: -1203px 0; width: 16px; height: 16px; }
.browser-panasonic{ background-position: -1220px 0; width: 16px; height: 16px; }
.browser-pdaphone{ background-position: -1237px 0; width: 16px; height: 16px; }
.browser-philips{ background-position: -1254px 0; width: 16px; height: 16px; }
.browser-phoenix{ background-position: -1271px 0; width: 16px; height: 16px; }
.browser-pluck{ background-position: -1288px 0; width: 16px; height: 16px; }
.browser-pulpfiction{ background-position: -1305px 0; width: 16px; height: 16px; }
.browser-real{ background-position: -1322px 0; width: 16px; height: 16px; }
.browser-rss{ background-position: -1339px 0; width: 16px; height: 16px; }
.browser-rssbandit{ background-position: -1356px 0; width: 16px; height: 16px; }
.browser-rssowl{ background-position: -1373px 0; width: 16px; height: 16px; }
.browser-rssreader{ background-position: -1390px 0; width: 16px; height: 16px; }
.browser-rssxpress{ background-position: -1407px 0; width: 16px; height: 16px; }
.browser-safari{ background-position: -1424px 0; width: 16px; height: 16px; }
.browser-sagem{ background-position: -1441px 0; width: 16px; height: 16px; }
.browser-samsung{ background-position: -1458px 0; width: 16px; height: 16px; }
.browser-seamonkey{ background-position: -1475px 0; width: 16px; height: 16px; }
.browser-sharp{ background-position: -1492px 0; width: 16px; height: 16px; }
.browser-sharpreader{ background-position: -1509px 0; width: 16px; height: 16px; }
.browser-shrook{ background-position: -1526px 0; width: 16px; height: 16px; }
.browser-siemens{ background-position: -1543px 0; width: 16px; height: 16px; }
.browser-sony{ background-position: -1560px 0; width: 16px; height: 16px; }
.browser-staroffice{ background-position: -1577px 0; width: 16px; height: 16px; }
.browser-subversion{ background-position: -1594px 0; width: 16px; height: 16px; }
.browser-teleport{ background-position: -1611px 0; width: 16px; height: 16px; }
.browser-trium{ background-position: -1628px 0; width: 16px; height: 16px; }
.browser-unknown{ background-position: -1645px 0; width: 16px; height: 16px; }
.browser-w3c{ background-position: -1662px 0; width: 16px; height: 16px; }
.browser-webcopier{ background-position: -1679px 0; width: 16px; height: 16px; }
.browser-webreaper{ background-position: -1696px 0; width: 16px; height: 16px; }
.browser-webtv{ background-position: -1713px 0; width: 16px; height: 16px; }
.browser-webzip{ background-position: -1730px 0; width: 16px; height: 16px; }
.browser-winxbox{ background-position: -1747px 0; width: 16px; height: 16px; }
.browser-wizz{ background-position: -1764px 0; width: 16px; height: 16px; }

/* ==|== Fluid grid system (from Twitter Bootstrap) ========================= */
.row {
	margin-left: -20px;
	*zoom: 1;
}
.row:before,
.row:after {
	display: table;
	content: "";
	line-height: 0;
}
.row:after {
	clear: both;
}
[class*="span"] {
	float: left;
	min-height: 1px;
	margin-left: 20px;
}
.span12 {
	width: 940px;
}
.span11 {
	width: 860px;
}
.span10 {
	width: 780px;
}
.span9 {
	width: 700px;
}
.span8 {
	width: 620px;
}
.span7 {
	width: 540px;
}
.span6 {
	width: 460px;
}
.span5 {
	width: 380px;
}
.span4 {
	width: 300px;
}
.span3 {
	width: 220px;
}
.span2 {
	width: 140px;
}
.span1 {
	width: 60px;
}
.offset12 {
	margin-left: 980px;
}
.offset11 {
	margin-left: 900px;
}
.offset10 {
	margin-left: 820px;
}
.offset9 {
	margin-left: 740px;
}
.offset8 {
	margin-left: 660px;
}
.offset7 {
	margin-left: 580px;
}
.offset6 {
	margin-left: 500px;
}
.offset5 {
	margin-left: 420px;
}
.offset4 {
	margin-left: 340px;
}
.offset3 {
	margin-left: 260px;
}
.offset2 {
	margin-left: 180px;
}
.offset1 {
	margin-left: 100px;
}
.row-fluid {
	width: 100%;
	*zoom: 1;
}
.row-fluid:before,
.row-fluid:after {
	display: table;
	content: "";
	line-height: 0;
}
.row-fluid:after {
	clear: both;
}
.row-fluid [class*="span"] {
	display: block;
	width: 100%;
	min-height: 30px;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	float: left;
	margin-left: 2.127659574468085%;
	*margin-left: 2.074468085106383%;
}
.row-fluid [class*="span"]:first-child {
	margin-left: 0;
}
.row-fluid .span12 {
	width: 100%;
	*width: 99.94680851063829%;
}
.row-fluid .span11 {
	width: 91.48936170212765%;
	*width: 91.43617021276594%;
}
.row-fluid .span10 {
	width: 82.97872340425532%;
	*width: 82.92553191489361%;
}
.row-fluid .span9 {
	width: 74.46808510638297%;
	*width: 74.41489361702126%;
}
.row-fluid .span8 {
	width: 65.95744680851064%;
	*width: 65.90425531914893%;
}
.row-fluid .span7 {
	width: 57.44680851063829%;
	*width: 57.39361702127659%;
}
.row-fluid .span6 {
	width: 48.93617021276595%;
	*width: 48.88297872340425%;
}
.row-fluid .span5 {
	width: 40.42553191489362%;
	*width: 40.37234042553192%;
}
.row-fluid .span4 {
	width: 31.914893617021278%;
	*width: 31.861702127659576%;
}
.row-fluid .span3 {
	width: 23.404255319148934%;
	*width: 23.351063829787233%;
}
.row-fluid .span2 {
	width: 14.893617021276595%;
	*width: 14.840425531914894%;
}
.row-fluid .span1 {
	width: 6.382978723404255%;
	*width: 6.329787234042553%;
}
.row-fluid .offset12 {
	margin-left: 104.25531914893617%;
	*margin-left: 104.14893617021275%;
}
.row-fluid .offset12:first-child {
	margin-left: 102.12765957446808%;
	*margin-left: 102.02127659574467%;
}
.row-fluid .offset11 {
	margin-left: 95.74468085106382%;
	*margin-left: 95.6382978723404%;
}
.row-fluid .offset11:first-child {
	margin-left: 93.61702127659574%;
	*margin-left: 93.51063829787232%;
}
.row-fluid .offset10 {
	margin-left: 87.23404255319149%;
	*margin-left: 87.12765957446807%;
}
.row-fluid .offset10:first-child {
	margin-left: 85.1063829787234%;
	*margin-left: 84.99999999999999%;
}
.row-fluid .offset9 {
	margin-left: 78.72340425531914%;
	*margin-left: 78.61702127659572%;
}
.row-fluid .offset9:first-child {
	margin-left: 76.59574468085106%;
	*margin-left: 76.48936170212764%;
}
.row-fluid .offset8 {
	margin-left: 70.2127659574468%;
	*margin-left: 70.10638297872339%;
}
.row-fluid .offset8:first-child {
	margin-left: 68.08510638297872%;
	*margin-left: 67.9787234042553%;
}
.row-fluid .offset7 {
	margin-left: 61.70212765957446%;
	*margin-left: 61.59574468085106%;
}
.row-fluid .offset7:first-child {
	margin-left: 59.574468085106375%;
	*margin-left: 59.46808510638297%;
}
.row-fluid .offset6 {
	margin-left: 53.191489361702125%;
	*margin-left: 53.085106382978715%;
}
.row-fluid .offset6:first-child {
	margin-left: 51.063829787234035%;
	*margin-left: 50.95744680851063%;
}
.row-fluid .offset5 {
	margin-left: 44.68085106382979%;
	*margin-left: 44.57446808510638%;
}
.row-fluid .offset5:first-child {
	margin-left: 42.5531914893617%;
	*margin-left: 42.4468085106383%;
}
.row-fluid .offset4 {
	margin-left: 36.170212765957444%;
	*margin-left: 36.06382978723405%;
}
.row-fluid .offset4:first-child {
	margin-left: 34.04255319148936%;
	*margin-left: 33.93617021276596%;
}
.row-fluid .offset3 {
	margin-left: 27.659574468085104%;
	*margin-left: 27.5531914893617%;
}
.row-fluid .offset3:first-child {
	margin-left: 25.53191489361702%;
	*margin-left: 25.425531914893618%;
}
.row-fluid .offset2 {
	margin-left: 19.148936170212764%;
	*margin-left: 19.04255319148936%;
}
.row-fluid .offset2:first-child {
	margin-left: 17.02127659574468%;
	*margin-left: 16.914893617021278%;
}
.row-fluid .offset1 {
	margin-left: 10.638297872340425%;
	*margin-left: 10.53191489361702%;
}
.row-fluid .offset1:first-child {
	margin-left: 8.51063829787234%;
	*margin-left: 8.404255319148938%;
}
@media (max-width: 767px) {
	.row-fluid {
		width: 100%;
	}
	.row {
		margin-left: 0;
	}
	[class*="span"],
	.row-fluid [class*="span"] {
		float: none;
		display: block;
		width: 100%;
		margin-left: 0;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
	}
	.span12,
	.row-fluid .span12 {
		width: 100%;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
	}
}
@media (min-width: 768px) and (max-width: 979px) {
	.row {
		margin-left: -20px;
		*zoom: 1;
	}
	.row:before,
	.row:after {
		display: table;
		content: "";
		line-height: 0;
	}
	.row:after {
		clear: both;
	}
	.span12 {
		width: 724px;
	}
	.span11 {
		width: 662px;
	}
	.span10 {
		width: 600px;
	}
	.span9 {
		width: 538px;
	}
	.span8 {
		width: 476px;
	}
	.span7 {
		width: 414px;
	}
	.span6 {
		width: 352px;
	}
	.span5 {
		width: 290px;
	}
	.span4 {
		width: 228px;
	}
	.span3 {
		width: 166px;
	}
	.span2 {
		width: 104px;
	}
	.span1 {
		width: 42px;
	}
	.offset12 {
		margin-left: 764px;
	}
	.offset11 {
		margin-left: 702px;
	}
	.offset10 {
		margin-left: 640px;
	}
	.offset9 {
		margin-left: 578px;
	}
	.offset8 {
		margin-left: 516px;
	}
	.offset7 {
		margin-left: 454px;
	}
	.offset6 {
		margin-left: 392px;
	}
	.offset5 {
		margin-left: 330px;
	}
	.offset4 {
		margin-left: 268px;
	}
	.offset3 {
		margin-left: 206px;
	}
	.offset2 {
		margin-left: 144px;
	}
	.offset1 {
		margin-left: 82px;
	}
	.row-fluid {
		width: 100%;
		*zoom: 1;
	}
	.row-fluid:before,
	.row-fluid:after {
		display: table;
		content: "";
		line-height: 0;
	}
	.row-fluid:after {
		clear: both;
	}
	.row-fluid [class*="span"] {
		display: block;
		width: 100%;
		min-height: 30px;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
		float: left;
		margin-left: 2.7624309392265194%;
		*margin-left: 2.709239449864817%;
	}
	.row-fluid [class*="span"]:first-child {
		margin-left: 0;
	}
	.row-fluid .span12 {
		width: 100%;
		*width: 99.94680851063829%;
	}
	.row-fluid .span11 {
		width: 91.43646408839778%;
		*width: 91.38327259903608%;
	}
	.row-fluid .span10 {
		width: 82.87292817679558%;
		*width: 82.81973668743387%;
	}
	.row-fluid .span9 {
		width: 74.30939226519337%;
		*width: 74.25620077583166%;
	}
	.row-fluid .span8 {
		width: 65.74585635359117%;
		*width: 65.69266486422946%;
	}
	.row-fluid .span7 {
		width: 57.18232044198895%;
		*width: 57.12912895262725%;
	}
	.row-fluid .span6 {
		width: 48.61878453038674%;
		*width: 48.56559304102504%;
	}
	.row-fluid .span5 {
		width: 40.05524861878453%;
		*width: 40.00205712942283%;
	}
	.row-fluid .span4 {
		width: 31.491712707182323%;
		*width: 31.43852121782062%;
	}
	.row-fluid .span3 {
		width: 22.92817679558011%;
		*width: 22.87498530621841%;
	}
	.row-fluid .span2 {
		width: 14.3646408839779%;
		*width: 14.311449394616199%;
	}
	.row-fluid .span1 {
		width: 5.801104972375691%;
		*width: 5.747913483013988%;
	}
	.row-fluid .offset12 {
		margin-left: 105.52486187845304%;
		*margin-left: 105.41847889972962%;
	}
	.row-fluid .offset12:first-child {
		margin-left: 102.76243093922652%;
		*margin-left: 102.6560479605031%;
	}
	.row-fluid .offset11 {
		margin-left: 96.96132596685082%;
		*margin-left: 96.8549429881274%;
	}
	.row-fluid .offset11:first-child {
		margin-left: 94.1988950276243%;
		*margin-left: 94.09251204890089%;
	}
	.row-fluid .offset10 {
		margin-left: 88.39779005524862%;
		*margin-left: 88.2914070765252%;
	}
	.row-fluid .offset10:first-child {
		margin-left: 85.6353591160221%;
		*margin-left: 85.52897613729868%;
	}
	.row-fluid .offset9 {
		margin-left: 79.8342541436464%;
		*margin-left: 79.72787116492299%;
	}
	.row-fluid .offset9:first-child {
		margin-left: 77.07182320441989%;
		*margin-left: 76.96544022569647%;
	}
	.row-fluid .offset8 {
		margin-left: 71.2707182320442%;
		*margin-left: 71.16433525332079%;
	}
	.row-fluid .offset8:first-child {
		margin-left: 68.50828729281768%;
		*margin-left: 68.40190431409427%;
	}
	.row-fluid .offset7 {
		margin-left: 62.70718232044199%;
		*margin-left: 62.600799341718584%;
	}
	.row-fluid .offset7:first-child {
		margin-left: 59.94475138121547%;
		*margin-left: 59.838368402492065%;
	}
	.row-fluid .offset6 {
		margin-left: 54.14364640883978%;
		*margin-left: 54.037263430116376%;
	}
	.row-fluid .offset6:first-child {
		margin-left: 51.38121546961326%;
		*margin-left: 51.27483249088986%;
	}
	.row-fluid .offset5 {
		margin-left: 45.58011049723757%;
		*margin-left: 45.47372751851417%;
	}
	.row-fluid .offset5:first-child {
		margin-left: 42.81767955801105%;
		*margin-left: 42.71129657928765%;
	}
	.row-fluid .offset4 {
		margin-left: 37.01657458563536%;
		*margin-left: 36.91019160691196%;
	}
	.row-fluid .offset4:first-child {
		margin-left: 34.25414364640884%;
		*margin-left: 34.14776066768544%;
	}
	.row-fluid .offset3 {
		margin-left: 28.45303867403315%;
		*margin-left: 28.346655695309746%;
	}
	.row-fluid .offset3:first-child {
		margin-left: 25.69060773480663%;
		*margin-left: 25.584224756083227%;
	}
	.row-fluid .offset2 {
		margin-left: 19.88950276243094%;
		*margin-left: 19.783119783707537%;
	}
	.row-fluid .offset2:first-child {
		margin-left: 17.12707182320442%;
		*margin-left: 17.02068884448102%;
	}
	.row-fluid .offset1 {
		margin-left: 11.32596685082873%;
		*margin-left: 11.219583872105325%;
	}
	.row-fluid .offset1:first-child {
		margin-left: 8.56353591160221%;
		*margin-left: 8.457152932878806%;
	}
}
@media (min-width: 1200px) {
	.row {
		margin-left: -30px;
		*zoom: 1;
	}
	.row:before,
	.row:after {
		display: table;
		content: "";
		line-height: 0;
	}
	.row:after {
		clear: both;
	}
	[class*="span"] {
		float: left;
		min-height: 1px;
		margin-left: 30px;
	}
	.span12 {
		width: 1170px;
	}
	.span11 {
		width: 1070px;
	}
	.span10 {
		width: 970px;
	}
	.span9 {
		width: 870px;
	}
	.span8 {
		width: 770px;
	}
	.span7 {
		width: 670px;
	}
	.span6 {
		width: 570px;
	}
	.span5 {
		width: 470px;
	}
	.span4 {
		width: 370px;
	}
	.span3 {
		width: 270px;
	}
	.span2 {
		width: 170px;
	}
	.span1 {
		width: 70px;
	}
	.offset12 {
		margin-left: 1230px;
	}
	.offset11 {
		margin-left: 1130px;
	}
	.offset10 {
		margin-left: 1030px;
	}
	.offset9 {
		margin-left: 930px;
	}
	.offset8 {
		margin-left: 830px;
	}
	.offset7 {
		margin-left: 730px;
	}
	.offset6 {
		margin-left: 630px;
	}
	.offset5 {
		margin-left: 530px;
	}
	.offset4 {
		margin-left: 430px;
	}
	.offset3 {
		margin-left: 330px;
	}
	.offset2 {
		margin-left: 230px;
	}
	.offset1 {
		margin-left: 130px;
	}
	.row-fluid {
		width: 100%;
		*zoom: 1;
	}
	.row-fluid:before,
	.row-fluid:after {
		display: table;
		content: "";
		line-height: 0;
	}
	.row-fluid:after {
		clear: both;
	}
	.row-fluid [class*="span"] {
		display: block;
		width: 100%;
		min-height: 30px;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
		float: left;
		margin-left: 2.564102564102564%;
		*margin-left: 2.5109110747408616%;
	}
	.row-fluid [class*="span"]:first-child {
		margin-left: 0;
	}
	.row-fluid .span12 {
		width: 100%;
		*width: 99.94680851063829%;
	}
	.row-fluid .span11 {
		width: 91.45299145299145%;
		*width: 91.39979996362975%;
	}
	.row-fluid .span10 {
		width: 82.90598290598291%;
		*width: 82.8527914166212%;
	}
	.row-fluid .span9 {
		width: 74.35897435897436%;
		*width: 74.30578286961266%;
	}
	.row-fluid .span8 {
		width: 65.81196581196582%;
		*width: 65.75877432260411%;
	}
	.row-fluid .span7 {
		width: 57.26495726495726%;
		*width: 57.21176577559556%;
	}
	.row-fluid .span6 {
		width: 48.717948717948715%;
		*width: 48.664757228587014%;
	}
	.row-fluid .span5 {
		width: 40.17094017094017%;
		*width: 40.11774868157847%;
	}
	.row-fluid .span4 {
		width: 31.623931623931625%;
		*width: 31.570740134569924%;
	}
	.row-fluid .span3 {
		width: 23.076923076923077%;
		*width: 23.023731587561375%;
	}
	.row-fluid .span2 {
		width: 14.52991452991453%;
		*width: 14.476723040552828%;
	}
	.row-fluid .span1 {
		width: 5.982905982905983%;
		*width: 5.929714493544281%;
	}
	.row-fluid .offset12 {
		margin-left: 105.12820512820512%;
		*margin-left: 105.02182214948171%;
	}
	.row-fluid .offset12:first-child {
		margin-left: 102.56410256410257%;
		*margin-left: 102.45771958537915%;
	}
	.row-fluid .offset11 {
		margin-left: 96.58119658119658%;
		*margin-left: 96.47481360247316%;
	}
	.row-fluid .offset11:first-child {
		margin-left: 94.01709401709402%;
		*margin-left: 93.91071103837061%;
	}
	.row-fluid .offset10 {
		margin-left: 88.03418803418803%;
		*margin-left: 87.92780505546462%;
	}
	.row-fluid .offset10:first-child {
		margin-left: 85.47008547008548%;
		*margin-left: 85.36370249136206%;
	}
	.row-fluid .offset9 {
		margin-left: 79.48717948717949%;
		*margin-left: 79.38079650845607%;
	}
	.row-fluid .offset9:first-child {
		margin-left: 76.92307692307693%;
		*margin-left: 76.81669394435352%;
	}
	.row-fluid .offset8 {
		margin-left: 70.94017094017094%;
		*margin-left: 70.83378796144753%;
	}
	.row-fluid .offset8:first-child {
		margin-left: 68.37606837606839%;
		*margin-left: 68.26968539734497%;
	}
	.row-fluid .offset7 {
		margin-left: 62.393162393162385%;
		*margin-left: 62.28677941443899%;
	}
	.row-fluid .offset7:first-child {
		margin-left: 59.82905982905982%;
		*margin-left: 59.72267685033642%;
	}
	.row-fluid .offset6 {
		margin-left: 53.84615384615384%;
		*margin-left: 53.739770867430444%;
	}
	.row-fluid .offset6:first-child {
		margin-left: 51.28205128205128%;
		*margin-left: 51.175668303327875%;
	}
	.row-fluid .offset5 {
		margin-left: 45.299145299145295%;
		*margin-left: 45.1927623204219%;
	}
	.row-fluid .offset5:first-child {
		margin-left: 42.73504273504273%;
		*margin-left: 42.62865975631933%;
	}
	.row-fluid .offset4 {
		margin-left: 36.75213675213675%;
		*margin-left: 36.645753773413354%;
	}
	.row-fluid .offset4:first-child {
		margin-left: 34.18803418803419%;
		*margin-left: 34.081651209310785%;
	}
	.row-fluid .offset3 {
		margin-left: 28.205128205128204%;
		*margin-left: 28.0987452264048%;
	}
	.row-fluid .offset3:first-child {
		margin-left: 25.641025641025642%;
		*margin-left: 25.53464266230224%;
	}
	.row-fluid .offset2 {
		margin-left: 19.65811965811966%;
		*margin-left: 19.551736679396257%;
	}
	.row-fluid .offset2:first-child {
		margin-left: 17.094017094017094%;
		*margin-left: 16.98763411529369%;
	}
	.row-fluid .offset1 {
		margin-left: 11.11111111111111%;
		*margin-left: 11.004728132387708%;
	}
	.row-fluid .offset1:first-child {
		margin-left: 8.547008547008547%;
		*margin-left: 8.440625568285142%;
	}
}

[class*="span"] .style_table {
	margin-bottom:1em;
}

[class*="span"] .style_calendar_viewer {
	float:none;
	width:auto;
}
[class*="span"] form {
	margin:0;
}
/* ==|== Helper classes ===================================================== */
.ir {background-color: transparent;border: 0;overflow: hidden;*text-indent: -9999px;}
.ir:before {content: "";display: block;width: 0;height: 100%;}

.hidden {display: none !important;visibility: hidden;}
.visuallyhidden {border: 0;clip: rect(0 0 0 0);height: 1px;margin: -1px;overflow: hidden;padding: 0;position: absolute;width: 1px;}
.visuallyhidden.focusable:active,.visuallyhidden.focusable:focus {clip: auto;height: auto;margin: 0;overflow: visible;position: static;width: auto;}

.invisible {visibility: hidden;}

.clearfix:before,.clearfix:after {content: " ";display: table;}
.clearfix:after {clear: both;}
.clearfix {*zoom: 1;}

/* ==|== print styles ======================================================= */
@media print {
	* { background: transparent !important; color: black !important; box-shadow:none !important; text-shadow: none !important; filter:none !important; -ms-filter: none !important; }
	a, a:visited { text-decoration: underline; }
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
	.style_calendar_prev, .style_calendar_next, .pkwk-symbol, #poptoc,	#toolbar, .ui-dialog, #topicpath{
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
		text-indent:0;
	}
	
	p,
	h2,
	h3 {
		orphans: 3;
		widows: 3;
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
		page-break-after: avoid;
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
<?php
@ob_end_flush();

/* End of file scripts.css.php */
/* Location: ./webroot/skin/scripts.css.php */