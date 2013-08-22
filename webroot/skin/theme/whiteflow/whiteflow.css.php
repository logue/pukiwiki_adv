<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: whiteflow.css.php,v 1.0.3 2012/10/17 15:44:30 Logue Exp $

// White Flow Adv. skin CSS
// ver 1.0 (2012/10/17)
// by Logue (http://logue.be/)

// based on
// White flow (http://note.openvista.jp/2007/pukiwiki-skin/)
// by leva(http://www.geckodev.org/)

// License: X11/MIT License
// http://www.opensource.org/licenses/mit-license.php

//error_reporting(0); // Nothing
error_reporting(E_ERROR | E_PARSE); // Avoid E_WARNING, E_NOTICE, etc
ini_set('zlib.output_compression', 'Off');

$expire = isset($_GET['expire'])   ? (int)$_GET['expire'] * 86400	: '604800';	// Default is 7 days.
$menu   = isset($_GET['menu'])   ? $_GET['menu']	: '';

// Read config
$_SKIN = include('whiteflow.ini.php');

// Send header
header('Content-Type: text/css; charset: UTF-8');
header('Cache-Control: private');
header('Expires: ' .gmdate('D, d M Y H:i:s',time() + $expire) . ' GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s', getlastmod() ) . ' GMT');
@ob_start('ob_gzhandler');
?>
@charset "UTF-8";
/** 1, general.css ********************************************************************************/
/* General elements */

body{
	background-color:	#dfdfdf;
}

a:link{
	color: #325989;
	text-decoration: none;
}

a:hover,
a:visited:hover{
	color: #a2b000;
	text-decoration: underline;
}

a:visited{
	color: #2f267d;
	text-decoration: none;
}

p a{
	margin: 0 2px;
}

ul, ol {
	padding-left:1.5em;
	margin:0.5em 2em;
}

dt{
	font-weight:bold;
}

dd{
	margin-left:1.5em;
	margin-bottom:0.5em;
}

fieldset pre{
	margin:0.2em;
}

q {
	quotes: '「' '」' "『" "』";}

q::before{
	content: open-quote;
}

q::after{
	content: close-quote;
}

blockquote{
	padding: 0.5em;
	margin: 0.1em 0.1em 0.1em 0.5em;
	background-color: whitesmoke;
	border: silver 1px solid;
	border-left: silver 5px solid;
}
/*
thead .style_td,
tfoot .style_td,
thead .style_th,
tfoot .style_th {
	color: white;
	background: #91afc7 url(<?php echo $_SKIN['image_dir'] ?>th.bg.png) bottom left repeat-x;
}
*/
.table{
	background-color: #ccd5dd;
}

.table th{
	border: 1px solid black;
	color: white;
	background: #bbb;
}

.table tr td{
	border: 1px solid grey;
	background-color: whitesmoke;
}

.table tr:nth-child(even) td{
	 background-color: aliceblue;
}

.table tr .td:hover{
	background-color: floralwhite;
}

.table td.blank, .table td.blank:hover{
	border: 1px solid darkgrey;
	background-color: gainsbol;
}

pre{
	color: #444;
	background-color: gainsboro;
	padding: 15px;
	line-height: 170%;
	border: 1px solid #ccc;
	overflow: auto;
}


.accesskey{
	font-size: 85%;
	font-family: monospace;
	margin: 0 4px;
	text-decoration: none;
}


.accesskey kbd{
	margin: 0 2px;
	text-decoration: underline;
}


/** 2, framework.css ******************************************************************************/
/* Framework overview */

#edit-area.display {
	margin: 10px;
}
#edit-area.work {
	width:100%;
	margin:0 -260px 0 20px;
	float:left;
}

#edit-area.work > div ,#edit-area.work > section{
	margin-right:260px;
}
#sidebar {
	background-color: whitesmoke;
	border: solid gainsboro;
	border-width: 0 1px;
	width: 180px;
	margin:5px 20px 5px 0;
	float:right;
}

#header {
	border-top: 1px solid #c3c3c3;
	height: 105px;
	overflow: hidden;
	padding: 0 15px 0 0;
}
#container {
	background: white url(<?php echo $_SKIN['image_dir'] ?>shadow.left.png) left top repeat-y;
	border-top: 1px solid #d1d1d1;
	margin: 0 35px;
	padding-left: 4px;
}
#content,
#footer,
#additional,
#header {
	margin-right: auto;
	margin-left: auto;
	background: transparent url(<?php echo $_SKIN['image_dir'] ?>shadow.right.png) right top repeat-y;
}

#footer {
	background: #aaa url(<?php echo $_SKIN['image_dir'] ?>shadow.right.png) right top repeat-y;
	color: white;
	clear: both;
	padding: 0 4px 0 0;
}
/** 3, parts.header.css ***************************************************************************/

#additional {
	border: solid lightgrey;
	border-width: 1px 0;
	font-size: 95%;
	font-weight: normal;
	padding: 7px 1em;
/*	text-align: right;	*/
}

.topicpath {
	float:left;
}

.topicpath a {
	color: #555;
	text-decoration: none;
}
.topicpath a:hover {
	font-weight: bold;
}

#lastmodified {
	padding-left: 18px;
	font-size: 93%;
	background: transparent url(<?php echo $_SKIN['image_dir'] ?>update.png) no-repeat;
	float: right;
	white-space: nowrap;
}
/*
#lastmodified span{
	display: none;
}
#lastmodified:hover span {
	border-radius: 4px;
	background-color: white;
	color: black;
	display: block;
	font-size: 90%;
	left: 60px;
	padding: 3px 7px;
	position: absolute;
	top: 8px;
}
*/
#header #selection {
	height: 80px;
	margin: 12px 10px 0 515px;
	text-align: right;
}
#header #selection h2 {
	font-size: 90%;
	padding-bottom: 7px;
}
#header #selection h2::before {
	content: url(<?php echo $_SKIN['image_dir'] ?>status.png);
	margin-right: 7px;
}
#header div #popular {
	padding-left: 4%;
}
#header div #popular select option .counter,
#header div h3 {
	display: none !important;
}
#header div #recent {
	padding-left: 8%;
}
#header div #wrap {
	white-space: nowrap;
}
#header div #wrap div {
	float: left;
	width: 44%;
}
#header div div select {
	font-size: 85%;
	width: 100%;
}
#header div div select optgroup {
	font-size: 90%;
	padding: 2px;
	width: 50%;
}
#header div div select optgroup option,
#header div div select option:disabled {
	margin-left: 1em;
	padding: 2px;
}

#hgroup {
	padding: 20px 0 0 10px;
	float: left;
	display: block;
	height: 80px;
}

#hgroup h1 {
	font-weight: bold;
	font-size: 197%;
	background-color: transparent;
	border: none;
	margin: 0;
	padding: 0;
}

#hgroup h2 {
	font-weight: normal;
	font-size: 85%;
	border: none;
	margin: 0;
	padding: 0;
}

#logo {
	display: block;
	background: transparent url(<?php echo $_SKIN['image_dir'] ?>title.png) left top no-repeat;
	float: left;
	padding: 10px 10px 0 40px;
}

#logo:hover {
	background-image: url(<?php echo $_SKIN['image_dir'] ?>title.master.png);
}
/** 4, parts.menu.css *****************************************************************************/
#sidebar{
	padding: 10px;
}

#page-menu{
	margin-bottom: 20px;
}

#sidebar h3 {
	background: transparent url(<?php echo $_SKIN['image_dir'] ?>sitemenu.png) center left no-repeat;
	font-size: 110%;
	padding-left: 30px;
}

#sidebar ul a:hover{
	background-color: #5796e9 !important;
	color: white;
}
#sidebar ul a[href="#header"] {
	background: white url(<?php echo $_SKIN['image_dir'] ?>arrow2.png) 98% 50% no-repeat;
}
#sidebar ul li li:hover a[href="#header"] {
	background: #5796e9 url(<?php echo $_SKIN['image_dir'] ?>arrow3.png) 98% 50% no-repeat;
}

#sidebar ul li .noexists a{
	display:inline;
	background-color:transparent;
}
/** 5, parts.edit-area.css ***********************************************************************/
/* #content > #edit-area - main contents */

#body {
	counter-reset: article;
}

#body h2:before {
	color: gainsboro;
	content: "#" counter(article, decimal);
	counter-increment: article;
	font-family: "Courier New", Courier, monospace;
	font-size: 360%;
	letter-spacing: -5px;
	margin-right: -0.35em;
	vertical-align: top;
}
#body h2 {
    border-color: gainsboro;
    border-style: solid;
    border-width: 1px 0;
    color: #444;
    height: 1.6em;
    line-height: 100%;
    margin: 30px 0;
    overflow: hidden;
    padding: 15px 30px 0 30px;
    white-space: nowrap;
}

#body h3 {
	background: transparent url(<?php echo $_SKIN['image_dir'] ?>wiki.png) 90% -12px no-repeat;
	border: solid gainsboro;
	border-width: 1px 0;
	color: #444;
	height: 1.4em;
	line-height: 100%;
	margin: 30px 0;
	overflow: hidden;
	padding: 10px 0 5px 5px;
}
#body h3 a {
	color: black;
}
#body h4 {
	background: white url(<?php echo $_SKIN['image_dir'] ?>star.png) left center no-repeat;
	margin: 10px 0;
	padding: 10px 0 0 40px;
	height: 33px;
}
#body h5 {
	border: solid 1px gainsboro;
	color: darkgray;
	margin: 10px 0;
}
#body h6 {
	border: solid gainsboro;
	color: darkgray;
	margin: 10px;
}

#body p, #body pre{
	margin 1em 0;
	padding .5em;
}

#content ul > li {
	list-style-image: url(<?php echo $_SKIN['image_dir'] ?>arrow.png);
}
#content ul > li:hover {
	list-style-image: url(<?php echo $_SKIN['image_dir'] ?>arrow.hover.png);
}

#signature li, #signature li:hover{
	list-style-image: none !important;
}

/* comment.inc.php, pcomment.inc.php */

#body form + dl{
	font-size:90%;
}

#body hr{
	display: none;
}

#body hr + h3{
	padding-left: 3em;
	background: white url(<?php echo $_SKIN['image_dir'] ?>comments.png) 20px -20px no-repeat;
}

#body hr + ::before{
	content: "";
}

#body p .pagename::before{
	content: "Page: ";
}

#body p .pagename{
	font-size: 90%;
	padding: 3px;
	background-color: gainsboro;
	border: 1px solid darkgray;
}

#misc dl {
	counter-reset: ollist;
	font-size: 90%;
	list-style: none;
	padding-left: 2em;
}
#misc dl > dd {
	counter-increment: ollist;
}
#misc dl > dd::before {
	background-color: darkgray;
	border: 1px solid #333;
	color: white;
	content: counters(ollist, ".");
	font-family: monospace;
	font-size: 93%;
	font-weight: bold;
	margin: 0 0.6em 0 0;
	padding: 1px 3px;
	width: 1em;
}
#misc dl > dd:hover::before {
	background-color: #395989;
}

/** Ex, selection.right.css ********************************************************************/
/* Framework overview */
#sidebar{
	word-break:break-all;
}

#sidebar #menubar > *{
	padding:0 0 0 1em;
}

#sidebar #menubar h2, #sidebar #menubar h3, #sidebar #menubar h4, #sidebar #menubar h5{
	padding-left:0;
}
#sidebar #menubar ul, #sidebar #menubar ol{
	margin:0 0 0 .5em;
	padding: 0 0 0 1em;
}

#sidebar #menubar ul li .noexists, #menubar ul li a {
	display:block;
}

#sidebar #menubar ul li .noexists a{
	background-color:transparent;
}

#sidebar #menubar .recent_list, #sidebar .popular_list{
	margin:0 0 0 .5em;
	padding:0 0 0 .5em;
	word-wrap:break-word;
}


#sidebar h1, #sidebar h2, #sidebar h3 {
	font-size:138.5%;
}

#sidebar h4, #sidebar h5, #sidebar h6{
	font-size:123.1%;
}

#sidebar #menubar ul.menu{
	list-style-type: none;
	list-style-position: outside;
	margin: 0;
	padding: 0;
}

#sidebar #menubar ul.menu li{
	padding: 0 0.3em;
	margin: 0;
	border-bottom: 1px dotted #FFFCEE;
}

#sidebar #menubar ul.menu li a{
	text-decoration: none;
	display:block;
	padding:0.1em 0.3em;
	margin:0px -0.3em;
}

/* Hack calendar */
#sidebar #menubar .style_calendar{
	width:150px;
	height:150px;
	padding:0px;
	margin:1px;
}

#sidebar .style_calendar td,
#menubar .style_calendar th, #sidebar .style_calendar th{
	padding:1px;
	margin:1px;
}


#container #body.display{
	padding:		0px 200px 15px 15px;
}

/** 6, parts.footer.css **************************************************************************/
/* #footer - includes license and so on */

#footer a{
	color: white;}

#footer ul{
	padding: 20px 20px 20px 45px;
	margin: 0;
	font-size: 90%;
	list-style: none none inside;
	border-top: 4px double white;
	background: transparent url(<?php echo $_SKIN['image_dir'] ?>shadow.bottom.png) left bottom repeat-x;}

#footer ul li.inquiry,
#footer ul li.request,
#footer ul li.help{
	display: inline;}

#footer ul li.help + li{
	margin-top: 5px;}

#footer ul li.help + li,
#footer ul li.help + li + li{
	font-size: 90%;}

#footer ul li.inquiry::after,
#footer ul li.request::after{
	content: "|";
	margin: 0 10px;}

#footer #validxhtml{
	float:right;
	padding:30px;
}

/** Popup Toc *************************************************************************************/
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
	text-shadow: white 1px 1px 0;
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
#poptoc a.here{color: black; background: gainsboro; text-decoration: none; border:1px dotted gray;}

.tocpic {
	display:inline;
	cursor:pointer;
}

.hrefp, .topic {
	vertical-align:text-bottom;
}

/* contents.inc.php */
.contents {
	background        : #f6f7fe url(<?php echo $_SKIN['image_dir'] ?>toc.png) 90% -12px no-repeat;
	border            : 1px solid white;
	margin            : 25px 7.5% 45px 15px;
	outline           : 1px solid #ccc;
	padding           : 10px 10px 15px 30px;
}

.contents > ul li,
.contents > ul li:hover {
	list-style        : none none inside !important;
}

.contents > ul {
	counter-reset     : toc;
}
.contents > ul > li {
	counter-increment : toc;
	margin            : 0;
	padding           : 0;
}
.contents > ul > li::before {
	content           : counters(toc, ".") ".";
}
.contents > ul > li:hover::before {
	color             : red;
}
.contents > ul li:hover {
	list-style        : none none inside;
}
.contents ul {
	list-style        : none none inside;
	margin            : 0;
	padding           : 0;
}
.contents ul ul {
	background        : transparent url(<?php echo $_SKIN['image_dir'] ?>tree/28.png) 0 repeat-y;
	list-style-type   : none;
	padding-left      : 15px;
}
.contents ul ul li {
	background        : transparent url(<?php echo $_SKIN['image_dir'] ?>tree/268.png) 0 no-repeat;
	margin-left       : -15px;
	padding-left      : 30px;
}
.contents ul ul li:last-child {
	background        : transparent url(<?php echo $_SKIN['image_dir'] ?>tree/68.png) 0 no-repeat;
}

/* suckerfish.inc.php */
.sf-menu, .sf-menu * {
	margin: 0;
	padding: 0;
	list-style: none;
}
.sf-menu {
	line-height: 1.0;
}
.sf-menu ul {
	position: absolute;
	top: -999em;
	width: 200px; /* left offset of submenus need to match (see below) */
}
.sf-menu ul li {
	width: 194px;
}
.sf-menu li:hover {
	visibility: inherit; /* fixes IE7 'sticky bug' */
}
.sf-menu li {
	float: left;
	position: relative;
}
.sf-menu a {
	display: block;
	text-indent: 1em;
	position: relative;
}
.sf-menu li:hover ul,
.sf-menu li.sfHover ul {
	left: 0;
	top: 2.5em; /* match top ul list item height */
	z-index: 99;
}
ul.sf-menu li:hover li ul,
ul.sf-menu li.sfHover li ul {
	top: -999em;
}
ul.sf-menu li li:hover ul,
ul.sf-menu li li.sfHover ul {
	left: 200px; /* match ul width */
	top: 0;
}
ul.sf-menu li li:hover li ul,
ul.sf-menu li li.sfHover li ul {
	top: -999em;
}
ul.sf-menu li li li:hover ul,
ul.sf-menu li li li.sfHover ul {
	left: 180px; /* match ul width */
	top: 0;
}

.sf-menu {
	float: left;
	margin:0;
	padding:0;

}
.sf-menu a {
	text-decoration:none;
}
.sf-menu a, .sf-menu a:visited  { /* visited pseudo selector so IE6 applies text colour*/
	color: #13a;
}

.sf-menu li {
	color: black;
	padding: 3px;
	margin-left: 0;
	list-style-image :none !important;
}

.sf-menu li a{
	color: #3c618e;
}
.sf-menu li li {
	background: white;
}
.sf-menu li li li {
	background: white;
}
.sf-menu li:hover, .sf-menu li.sfHover,
.sf-menu a:focus, .sf-menu a:hover, .sf-menu a:active {
	background-color: #5796e9;
	color: white !inportant;
	opacity:1;
}
/*** adding sf-vertical in addition to sf-menu creates a vertical menu ***/
.sf-vertical, .sf-vertical li {
	width:	180px;
}
/* this lacks ul at the start of the selector, so the styles from the main CSS file override it where needed */
.sf-vertical li:hover ul,
.sf-vertical li.sfHover ul {
	left: -200px; /* match ul width */
	top: -11px;	/* padding-top + border-width */

	padding: 10px 0;
	border: 1px solid #afafaf;
	background-color: white;
	opacity:.95;
	border-top-left-radius: 10px;
	border-bottom-right-radius: 10px;
}

/* search.inc.php */
.search_form {
	background-color: #f1f1f1;
	border: 1px solid white;
	font-size: 90%;
	margin: 35px 25px 35px 0;
	outline: 1px solid #cacaca;
	padding-right: 20px;
	white-space: nowrap;
}
.search_form {
	background: transparent url(<?php echo $_SKIN['image_dir'] ?>search.png) 20px 0 no-repeat;
	margin: 20px 0;
	padding-left: 70px;
}
.search_form input[type=search] {
	height: auto;
	margin: 3px 10px 0 0;
	min-width: 50%;
	padding: 3px;
}
.search_form p {
	margin: 15px 0 10px 0;
}

/* note.inc.php */
#note {
	background-image:url(<?php echo $_SKIN['image_dir'] ?>note.png);
	background-position: top right;
	background-repeat:no-repeat;
}

#note dl > dd::before {
	display: none;
}

#note dt{
	padding-left: 3em;
}

#note dt::before{
	content: "";
}

#note dl{
	font-size: 90%;
}

#note dl a.note_super{
	border: 1px solid #333;
	background-color: #aaa;
	color: white;
	padding: 0 5px;
	font-family: monospace;
}

#note dl a.note_super:hover{
	background: #325989;
	text-decoration: none;
}
@media screen{
	#wide-container{
		min-width:780px;
	}
}
@media print{
	#wide-container,
	#container{
		width:99%;
		border:none;
		padding: 0;
		margin: 0;
	}

	#hgroup h2, #header img, .navibar, #menubar, #sidebar, #poptoc, #footer, #misc, #toolbar, #logo
	#sigunature{
		display:none !important;
		visibility: hidden !important;
	}

	#header{
		border: none;
		margin: 0;
		padding: 0;
		height: auto;
	}

	#hgroup{
		margin: 0;
		padding: 0;
		float: none;
		clear: both;
	}
	#hgroup h2{
		display: none;
	}
	#hgroup h1{
		width: 100%;
		display: block;
	}

	#additional, #body h2, #body h3, #body h4, #body h5, #body h6{
		height: auto;
		margin: 0;
		padding: 0;
	}
	#additional{
		border: none;
	}
	#lastmodified:before{
		content: 'Last modified :';
	}
	#lastmodified{
		margin: 0;
		padding: 0;
	}

	#body h2:before, #logo:after{
		display: none;
	}
/*
	{
		padding: 0;
		margin: 0;
		height: auto;
	}
*/
	#content{
		clear: both;
		width: 100%;
	}

	#edit-area{
		clear: both;
		margin: 0;
		padding: 0;
		float: none;
	}

	#edit-area.work > div ,#edit-area.work > section{
		margin: 0;
		padding: 0;
	}
}
<?php
@ob_end_flush();

/* End of file whiteflow.css.php */
/* Location: ./webroot/skin/theme/whiteflow/whiteflow.css.php */