<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: classic.css.php,v 0.0.2 2012/03/31 16:49:30 Logue Exp $
// 
// PukiWiki Adv. Mobile Theme
// Copyright (C)
//   2012 PukiWiki Advance Developer Team

//error_reporting(0); // Nothing
error_reporting(E_ERROR | E_PARSE); // Avoid E_WARNING, E_NOTICE, etc

$image_dir = isset($_GET['base'])   ? $_GET['base']	: '../image/';
$expire = isset($_GET['expire'])   ? (int)$_GET['expire'] * 86400	: '604800';	// Default is 7 days.
// Send header
header('Content-Type: text/css; charset: UTF-8');
header('Cache-Control: private');
header('Expires: ' .gmdate('D, d M Y H:i:s',time() + $expire) . ' GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s', getlastmod() ) . ' GMT');
ob_start('ob_gzhandler');
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
body, button, input, select, textarea { color: #222; }

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

html{
	display:none;
}

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

.ui-btn-up-a .ui-btn-text{
	color: white !important;
}

[aria-describedby="tooltip"]{
	cursor:help;
	border-bottom: 1px dotted;
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


textarea, select, option, input, var, pre, code{
	font-family: monospace !important;
}
.ui-controlgroup label{
	font-size:100% !important;
}

#adarea {
	text-align:center;
	width:100%;
}

#adarea *{
	margin: 0 auto;
}

#adarea_content{
	display:none;
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

/* ==|== ui icon classes ==================================================== */
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


/* ==|== non-semantic helper classes ======================================== */
.ir { display: block; border: 0; text-indent: -999em; overflow: hidden; background-color: transparent; background-repeat: no-repeat; text-align: left; direction: ltr; *line-height: 0; }
.ir br { display: none; }
.hidden { display: none !important; visibility: hidden; }
.visuallyhidden { border: 0; clip: rect(0 0 0 0); height: 1px; margin: -1px; overflow: hidden; padding: 0; position: absolute; width: 1px; }
.visuallyhidden.focusable:active, .visuallyhidden.focusable:focus { clip: auto; height: auto; margin: 0; overflow: visible; position: static; width: auto; }
.invisible { visibility: hidden; }
.clearfix:before, .clearfix:after { content: ""; display: table; }
.clearfix:after { clear: both; }
.clearfix { *zoom: 1; }

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
		width: 28%;
		background: none;
	}
	.two-colums .content-primary {
		padding: 0 5px;
		width: 70%;
		float: right;
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
<?php
ob_end_flush();

/* End of file mobile.ini.php */
/* Location: ./webroot/skin/theme/mobile/mobile.ini.php */