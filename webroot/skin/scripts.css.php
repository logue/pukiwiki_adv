<?php
// PukiWiki Advance Standard CSS.
// $Id: scripts.css.php,v 1.6.17 2013/01/22 14:41:00 Logue Exp $
// Copyright (C) 2010-2013 PukiWiki Advance Developer Team
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
/*! normalize.css v2.1.2 | MIT License | git.io/normalize */
article,aside,details,figcaption,figure,footer,header,hgroup,main,nav,section,summary{display:block}
audio,canvas,video{display:inline-block}
audio:not([controls]){display:none;height:0}
[hidden]{display:none}
html{font-family:sans-serif;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%}
body{margin:0}
a:focus{outline:thin dotted}
a:active,a:hover{outline:0}
h1{font-size:2em;margin:.67em 0}
abbr[title]{border-bottom:1px dotted}
b,strong{font-weight:bold}
dfn{font-style:italic}
hr{-moz-box-sizing:content-box;box-sizing:content-box;height:0}
mark{background:#ff0;color:#000}
code,kbd,pre,samp{font-family:monospace,serif;font-size:1em}
pre{white-space:pre-wrap}
q{quotes:"\201C" "\201D" "\2018" "\2019"}
small{font-size:80%}
sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:baseline}
sup{top:-0.5em}
sub{bottom:-0.25em}
img{border:0}
svg:not(:root){overflow:hidden}
figure{margin:0}
fieldset{border:1px solid silver;margin:0 2px;padding:.35em .625em .75em}
legend{border:0;padding:0}
button,input,select,textarea{font-family:inherit;font-size:100%;margin:0}
button,input{line-height:normal}
button,select{text-transform:none}
button,html input[type="button"],input[type="reset"],input[type="submit"]{-webkit-appearance:button;cursor:pointer}
button[disabled],html input[disabled]{cursor:default}
input[type="checkbox"],input[type="radio"]{box-sizing:border-box;padding:0}
input[type="search"]{-webkit-appearance:textfield;-moz-box-sizing:content-box;-webkit-box-sizing:content-box;box-sizing:content-box}
input[type="search"]::-webkit-search-cancel-button,input[type="search"]::-webkit-search-decoration{-webkit-appearance:none}
button::-moz-focus-inner,input::-moz-focus-inner{border:0;padding:0}
textarea{overflow:auto;vertical-align:top}table{border-collapse:collapse;border-spacing:0}

/* ==|== HTML5 Boilerplate styles - h5bp.com ================================ */
html,button,input,select,textarea{color:#222}
body{font-size:13px;line-height:1.4}
::-moz-selection{background:#b3d4fc;text-shadow:none}
::selection{background:#b3d4fc;text-shadow:none}
hr{display:block;height:1px;border:0;border-top:1px solid #ccc;margin:1em 0;padding:0}
audio,canvas,img,video{vertical-align:middle}
fieldset{border:0;margin:0;padding:0}
textarea{resize:vertical}
.browsehappy{margin:.2em 0;background:#ccc;color:#000;padding:.2em 0}

/* ==|== PukiWiki Advance Standard Font Set ================================= */
*,
*:before,
*:after {
  -webkit-box-sizing: border-box;
     -moz-box-sizing: border-box;
          box-sizing: border-box;
}

html {
  font-size: 62.5%;
  -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
}

body {
  font-size: 14px;
  line-height: 1.428571429;
  color: #333333;
  background-color: #ffffff;
}

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
	font-size: inherit;
	line-height: inherit;
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

a {
  color: #428bca;
  text-decoration: none;
}

a:hover,
a:focus {
  color: #2a6496;
  text-decoration: underline;
}

a:focus {
  outline: thin dotted #333;
  outline: 5px auto -webkit-focus-ring-color;
  outline-offset: -2px;
}

img {
  vertical-align: middle;
}

.img-responsive {
  display: block;
  height: auto;
  max-width: 100%;
}

.img-rounded {
  border-radius: 6px;
}

.img-thumbnail {
  display: inline-block;
  height: auto;
  max-width: 100%;
  padding: 4px;
  line-height: 1.428571429;
  background-color: #ffffff;
  border: 1px solid #dddddd;
  border-radius: 4px;
  -webkit-transition: all 0.2s ease-in-out;
          transition: all 0.2s ease-in-out;
}

.img-circle {
  border-radius: 50%;
}

hr {
  margin-top: 20px;
  margin-bottom: 20px;
  border: 0;
  border-top: 1px solid #eeeeee;
}

.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0 0 0 0);
  border: 0;
}

p {
  margin: 0 0 10px;
}

ul,
ol {
  margin-top: 0;
  margin-bottom: 10px;
}

ul ul,
ol ul,
ul ol,
ol ol {
  margin-bottom: 0;
}

.list-unstyled {
  padding-left: 0;
  list-style: none;
}

.list-inline {
  padding-left: 0;
  list-style: none;
}

.list-inline > li {
  display: inline-block;
  padding-right: 5px;
  padding-left: 5px;
}

dl {
  margin-bottom: 20px;
}

dt,
dd {
  line-height: 1.428571429;
}

dt {
  font-weight: bold;
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

/* Table Tags */
.table_wrapper{
	text-align: center;
	width: 100%;
}
.table{
	border-spacing:2px;
	padding: 0;
	border: 0;
	text-align: left;
	border-collapse: separate;
	border-spacing: 1px;
	background-color: darkgray;
	min-width:30%;
}
.table_center{
	margin: 0 auto;
}
.table_left{
	margin:auto auto auto 0;
}
.table_right{
	margin:auto 0 auto auto;
}
.table th{
	background-color: silver;
	padding: 5px;
	margin: 1px;
	white-space: nowrap;
	text-align: center;
}
.table td{
	padding: 5px;
	margin: 1px;
	vertical-align: top;
}
.table td.blank-cell{
	background-color: lightgrey;
}
.table tr:nth-child(even) td{
	 background-color:white;
}
.table tr:nth-child(odd) td{
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
/* form (from Twitter Bootstrap v3.0) */

input[type="search"] {
  -webkit-box-sizing: border-box;
     -moz-box-sizing: border-box;
          box-sizing: border-box;
}

input[type="radio"],
input[type="checkbox"] {
  margin: 4px 0 0;
  margin-top: 1px \9;
  /* IE8-9 */

  line-height: normal;
}

input[type="file"] {
  display: block;
}

select[multiple],
select[size] {
  height: auto;
}

select optgroup {
  font-family: inherit;
  font-size: inherit;
  font-style: inherit;
}

input[type="file"]:focus,
input[type="radio"]:focus,
input[type="checkbox"]:focus {
  outline: thin dotted #333;
  outline: 5px auto -webkit-focus-ring-color;
  outline-offset: -2px;
}

input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
  height: auto;
}

.form-control:-moz-placeholder {
  color: #999999;
}

.form-control::-moz-placeholder {
  color: #999999;
}

.form-control:-ms-input-placeholder {
  color: #999999;
}

.form-control::-webkit-input-placeholder {
  color: #999999;
}

.form-control {
  display: block;
  width: 100%;
  height: 34px;
  padding: 6px 12px;
  font-size: 14px;
  line-height: 1.428571429;
  color: #555555;
  vertical-align: middle;
  background-color: #ffffff;
  border: 1px solid #cccccc;
  border-radius: 4px;
  -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
          box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
  -webkit-transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
          transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
}

.form-control:focus {
  border-color: #66afe9;
  outline: 0;
  -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px rgba(102, 175, 233, 0.6);
          box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px rgba(102, 175, 233, 0.6);
}

.form-control[disabled],
.form-control[readonly],
fieldset[disabled] .form-control {
  cursor: not-allowed;
  background-color: #eeeeee;
}

textarea.form-control {
  height: auto;
}

.form-group {
  margin-bottom: 15px;
}

.radio,
.checkbox {
  display: block;
  min-height: 20px;
  padding-left: 20px;
  margin-top: 10px;
  margin-bottom: 10px;
  vertical-align: middle;
}

.radio label,
.checkbox label {
  display: inline;
  margin-bottom: 0;
  font-weight: normal;
  cursor: pointer;
}

.radio input[type="radio"],
.radio-inline input[type="radio"],
.checkbox input[type="checkbox"],
.checkbox-inline input[type="checkbox"] {
  float: left;
  margin-left: -20px;
}

.radio + .radio,
.checkbox + .checkbox {
  margin-top: -5px;
}

.radio-inline,
.checkbox-inline {
  display: inline-block;
  padding-left: 20px;
  margin-bottom: 0;
  font-weight: normal;
  vertical-align: middle;
  cursor: pointer;
}

.radio-inline + .radio-inline,
.checkbox-inline + .checkbox-inline {
  margin-top: 0;
  margin-left: 10px;
}

input[type="radio"][disabled],
input[type="checkbox"][disabled],
.radio[disabled],
.radio-inline[disabled],
.checkbox[disabled],
.checkbox-inline[disabled],
fieldset[disabled] input[type="radio"],
fieldset[disabled] input[type="checkbox"],
fieldset[disabled] .radio,
fieldset[disabled] .radio-inline,
fieldset[disabled] .checkbox,
fieldset[disabled] .checkbox-inline {
  cursor: not-allowed;
}

.input-sm {
  height: 30px;
  padding: 5px 10px;
  font-size: 12px;
  line-height: 1.5;
  border-radius: 3px;
}

select.input-sm {
  height: 30px;
  line-height: 30px;
}

textarea.input-sm {
  height: auto;
}

.input-lg {
  height: 45px;
  padding: 10px 16px;
  font-size: 18px;
  line-height: 1.33;
  border-radius: 6px;
}

select.input-lg {
  height: 45px;
  line-height: 45px;
}

textarea.input-lg {
  height: auto;
}

input, textarea, select, button{
	padding: .2em;
	margin: .1em .2em;
	vertical-align: middle;
}

textarea[row="1"]{
	height:2.5em;
}

/* Remove outline color from Safari and Chrome. */
input:focus, textarea:focus, select:focus, button:focus{
	outline: medium none !important;
}

@media (min-width: 768px) {
  .form-inline .form-group {
    display: inline-block;
    margin-bottom: 0;
    vertical-align: middle;
  }
  .form-inline .form-control {
    display: inline-block;
  }
  .form-inline .radio,
  .form-inline .checkbox {
    display: inline-block;
    padding-left: 0;
    margin-top: 0;
    margin-bottom: 0;
  }
  .form-inline .radio input[type="radio"],
  .form-inline .checkbox input[type="checkbox"] {
    float: none;
    margin-left: 0;
  }
}

.input-group {
  position: relative;
  display: table;
  border-collapse: separate;
}

.input-group.col {
  float: none;
  padding-right: 0;
  padding-left: 0;
}

.input-group .form-control {
  width: 100%;
  margin-bottom: 0;
}

.input-group-lg > .form-control,
.input-group-lg > .input-group-addon,
.input-group-lg > .input-group-btn > .btn {
  height: 45px;
  padding: 10px 16px;
  font-size: 18px;
  line-height: 1.33;
  border-radius: 6px;
}

select.input-group-lg > .form-control,
select.input-group-lg > .input-group-addon,
select.input-group-lg > .input-group-btn > .btn {
  height: 45px;
  line-height: 45px;
}

textarea.input-group-lg > .form-control,
textarea.input-group-lg > .input-group-addon,
textarea.input-group-lg > .input-group-btn > .btn {
  height: auto;
}

.input-group-sm > .form-control,
.input-group-sm > .input-group-addon,
.input-group-sm > .input-group-btn > .btn {
  height: 30px;
  padding: 5px 10px;
  font-size: 12px;
  line-height: 1.5;
  border-radius: 3px;
}

select.input-group-sm > .form-control,
select.input-group-sm > .input-group-addon,
select.input-group-sm > .input-group-btn > .btn {
  height: 30px;
  line-height: 30px;
}

textarea.input-group-sm > .form-control,
textarea.input-group-sm > .input-group-addon,
textarea.input-group-sm > .input-group-btn > .btn {
  height: auto;
}

.input-group-addon,
.input-group-btn,
.input-group .form-control {
  display: table-cell;
}

.input-group-addon:not(:first-child):not(:last-child),
.input-group-btn:not(:first-child):not(:last-child),
.input-group .form-control:not(:first-child):not(:last-child) {
  border-radius: 0;
}

.input-group-addon,
.input-group-btn {
  width: 1%;
  white-space: nowrap;
  vertical-align: middle;
}

.input-group-addon {
  padding: 6px 12px;
  font-size: 14px;
  font-weight: normal;
  line-height: 1;
  text-align: center;
  background-color: #eeeeee;
  border: 1px solid #cccccc;
  border-radius: 4px;
}

.input-group-addon.input-sm {
  padding: 5px 10px;
  font-size: 12px;
  border-radius: 3px;
}

.input-group-addon.input-lg {
  padding: 10px 16px;
  font-size: 18px;
  border-radius: 6px;
}

.input-group-addon input[type="radio"],
.input-group-addon input[type="checkbox"] {
  margin-top: 0;
}

.input-group .form-control:first-child,
.input-group-addon:first-child,
.input-group-btn:first-child > .btn,
.input-group-btn:first-child > .dropdown-toggle,
.input-group-btn:last-child > .btn:not(:last-child):not(.dropdown-toggle) {
  border-top-right-radius: 0;
  border-bottom-right-radius: 0;
}

.input-group-addon:first-child {
  border-right: 0;
}

.input-group .form-control:last-child,
.input-group-addon:last-child,
.input-group-btn:last-child > .btn,
.input-group-btn:last-child > .dropdown-toggle,
.input-group-btn:first-child > .btn:not(:first-child) {
  border-bottom-left-radius: 0;
  border-top-left-radius: 0;
}

.input-group-addon:last-child {
  border-left: 0;
}

.input-group-btn {
  position: relative;
  white-space: nowrap;
}

.input-group-btn > .btn {
  position: relative;
}

.input-group-btn > .btn + .btn {
  margin-left: -4px;
}

.input-group-btn > .btn:hover,
.input-group-btn > .btn:active {
  z-index: 2;
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
.alert {
  padding: 15px;
  margin-bottom: 20px;
  border: 1px solid transparent;
  border-radius: 4px;
}

.alert h4 {
  margin-top: 0;
  color: inherit;
}

.alert .alert-link {
  font-weight: bold;
}

.alert > p,
.alert > ul {
  margin-bottom: 0;
}

.alert > p + p {
  margin-top: 5px;
}

.alert-dismissable {
  padding-right: 35px;
}

.alert-dismissable .close {
  position: relative;
  top: -2px;
  right: -21px;
  color: inherit;
}

.alert-success {
  color: #468847;
  background-color: #dff0d8;
  border-color: #d6e9c6;
}

.alert-success hr {
  border-top-color: #c9e2b3;
}

.alert-success .alert-link {
  color: #356635;
}

.alert-info {
  color: #3a87ad;
  background-color: #d9edf7;
  border-color: #bce8f1;
}

.alert-info hr {
  border-top-color: #a6e1ec;
}

.alert-info .alert-link {
  color: #2d6987;
}

.alert-warning {
  color: #c09853;
  background-color: #fcf8e3;
  border-color: #fbeed5;
}

.alert-warning hr {
  border-top-color: #f8e5be;
}

.alert-warning .alert-link {
  color: #a47e3c;
}

.alert-danger {
  color: #b94a48;
  background-color: #f2dede;
  border-color: #eed3d7;
}

.alert-danger hr {
  border-top-color: #e6c1c7;
}

.alert-danger .alert-link {
  color: #953b39;
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
[aria-describedby='tooltip'], [aria-describedby='linktip'] {
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
#realview {
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

/* jQueryUI BlockUI */
#loading {
	display:none;
	cursor:progress;
}

/* Tooltip */
[role=tooltip] p{
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

/* ==|== Glyphicon icon (from Twitter Bootstrap v3.0) ========================== */

@font-face {
  font-family: 'Glyphicons Halflings';
  src: url('../fonts/glyphicons-halflings-regular.eot');
  src: url('../fonts/glyphicons-halflings-regular.eot?#iefix') format('embedded-opentype'), url('../fonts/glyphicons-halflings-regular.woff') format('woff'), url('../fonts/glyphicons-halflings-regular.ttf') format('truetype'), url('../fonts/glyphicons-halflings-regular.svg#glyphicons-halflingsregular') format('svg');
}

.glyphicon {
  position: relative;
  top: 1px;
  display: inline-block;
  font-family: 'Glyphicons Halflings';
  -webkit-font-smoothing: antialiased;
  font-style: normal;
  font-weight: normal;
  line-height: 1;
}

.glyphicon-asterisk:before {
  content: "\2a";
}

.glyphicon-plus:before {
  content: "\2b";
}

.glyphicon-euro:before {
  content: "\20ac";
}

.glyphicon-minus:before {
  content: "\2212";
}

.glyphicon-cloud:before {
  content: "\2601";
}

.glyphicon-envelope:before {
  content: "\2709";
}

.glyphicon-pencil:before {
  content: "\270f";
}

.glyphicon-glass:before {
  content: "\e001";
}

.glyphicon-music:before {
  content: "\e002";
}

.glyphicon-search:before {
  content: "\e003";
}

.glyphicon-heart:before {
  content: "\e005";
}

.glyphicon-star:before {
  content: "\e006";
}

.glyphicon-star-empty:before {
  content: "\e007";
}

.glyphicon-user:before {
  content: "\e008";
}

.glyphicon-film:before {
  content: "\e009";
}

.glyphicon-th-large:before {
  content: "\e010";
}

.glyphicon-th:before {
  content: "\e011";
}

.glyphicon-th-list:before {
  content: "\e012";
}

.glyphicon-ok:before {
  content: "\e013";
}

.glyphicon-remove:before {
  content: "\e014";
}

.glyphicon-zoom-in:before {
  content: "\e015";
}

.glyphicon-zoom-out:before {
  content: "\e016";
}

.glyphicon-off:before {
  content: "\e017";
}

.glyphicon-signal:before {
  content: "\e018";
}

.glyphicon-cog:before {
  content: "\e019";
}

.glyphicon-trash:before {
  content: "\e020";
}

.glyphicon-home:before {
  content: "\e021";
}

.glyphicon-file:before {
  content: "\e022";
}

.glyphicon-time:before {
  content: "\e023";
}

.glyphicon-road:before {
  content: "\e024";
}

.glyphicon-download-alt:before {
  content: "\e025";
}

.glyphicon-download:before {
  content: "\e026";
}

.glyphicon-upload:before {
  content: "\e027";
}

.glyphicon-inbox:before {
  content: "\e028";
}

.glyphicon-play-circle:before {
  content: "\e029";
}

.glyphicon-repeat:before {
  content: "\e030";
}

.glyphicon-refresh:before {
  content: "\e031";
}

.glyphicon-list-alt:before {
  content: "\e032";
}

.glyphicon-flag:before {
  content: "\e034";
}

.glyphicon-headphones:before {
  content: "\e035";
}

.glyphicon-volume-off:before {
  content: "\e036";
}

.glyphicon-volume-down:before {
  content: "\e037";
}

.glyphicon-volume-up:before {
  content: "\e038";
}

.glyphicon-qrcode:before {
  content: "\e039";
}

.glyphicon-barcode:before {
  content: "\e040";
}

.glyphicon-tag:before {
  content: "\e041";
}

.glyphicon-tags:before {
  content: "\e042";
}

.glyphicon-book:before {
  content: "\e043";
}

.glyphicon-print:before {
  content: "\e045";
}

.glyphicon-font:before {
  content: "\e047";
}

.glyphicon-bold:before {
  content: "\e048";
}

.glyphicon-italic:before {
  content: "\e049";
}

.glyphicon-text-height:before {
  content: "\e050";
}

.glyphicon-text-width:before {
  content: "\e051";
}

.glyphicon-align-left:before {
  content: "\e052";
}

.glyphicon-align-center:before {
  content: "\e053";
}

.glyphicon-align-right:before {
  content: "\e054";
}

.glyphicon-align-justify:before {
  content: "\e055";
}

.glyphicon-list:before {
  content: "\e056";
}

.glyphicon-indent-left:before {
  content: "\e057";
}

.glyphicon-indent-right:before {
  content: "\e058";
}

.glyphicon-facetime-video:before {
  content: "\e059";
}

.glyphicon-picture:before {
  content: "\e060";
}

.glyphicon-map-marker:before {
  content: "\e062";
}

.glyphicon-adjust:before {
  content: "\e063";
}

.glyphicon-tint:before {
  content: "\e064";
}

.glyphicon-edit:before {
  content: "\e065";
}

.glyphicon-share:before {
  content: "\e066";
}

.glyphicon-check:before {
  content: "\e067";
}

.glyphicon-move:before {
  content: "\e068";
}

.glyphicon-step-backward:before {
  content: "\e069";
}

.glyphicon-fast-backward:before {
  content: "\e070";
}

.glyphicon-backward:before {
  content: "\e071";
}

.glyphicon-play:before {
  content: "\e072";
}

.glyphicon-pause:before {
  content: "\e073";
}

.glyphicon-stop:before {
  content: "\e074";
}

.glyphicon-forward:before {
  content: "\e075";
}

.glyphicon-fast-forward:before {
  content: "\e076";
}

.glyphicon-step-forward:before {
  content: "\e077";
}

.glyphicon-eject:before {
  content: "\e078";
}

.glyphicon-chevron-left:before {
  content: "\e079";
}

.glyphicon-chevron-right:before {
  content: "\e080";
}

.glyphicon-plus-sign:before {
  content: "\e081";
}

.glyphicon-minus-sign:before {
  content: "\e082";
}

.glyphicon-remove-sign:before {
  content: "\e083";
}

.glyphicon-ok-sign:before {
  content: "\e084";
}

.glyphicon-question-sign:before {
  content: "\e085";
}

.glyphicon-info-sign:before {
  content: "\e086";
}

.glyphicon-screenshot:before {
  content: "\e087";
}

.glyphicon-remove-circle:before {
  content: "\e088";
}

.glyphicon-ok-circle:before {
  content: "\e089";
}

.glyphicon-ban-circle:before {
  content: "\e090";
}

.glyphicon-arrow-left:before {
  content: "\e091";
}

.glyphicon-arrow-right:before {
  content: "\e092";
}

.glyphicon-arrow-up:before {
  content: "\e093";
}

.glyphicon-arrow-down:before {
  content: "\e094";
}

.glyphicon-share-alt:before {
  content: "\e095";
}

.glyphicon-resize-full:before {
  content: "\e096";
}

.glyphicon-resize-small:before {
  content: "\e097";
}

.glyphicon-exclamation-sign:before {
  content: "\e101";
}

.glyphicon-gift:before {
  content: "\e102";
}

.glyphicon-leaf:before {
  content: "\e103";
}

.glyphicon-eye-open:before {
  content: "\e105";
}

.glyphicon-eye-close:before {
  content: "\e106";
}

.glyphicon-warning-sign:before {
  content: "\e107";
}

.glyphicon-plane:before {
  content: "\e108";
}

.glyphicon-random:before {
  content: "\e110";
}

.glyphicon-comment:before {
  content: "\e111";
}

.glyphicon-magnet:before {
  content: "\e112";
}

.glyphicon-chevron-up:before {
  content: "\e113";
}

.glyphicon-chevron-down:before {
  content: "\e114";
}

.glyphicon-retweet:before {
  content: "\e115";
}

.glyphicon-shopping-cart:before {
  content: "\e116";
}

.glyphicon-folder-close:before {
  content: "\e117";
}

.glyphicon-folder-open:before {
  content: "\e118";
}

.glyphicon-resize-vertical:before {
  content: "\e119";
}

.glyphicon-resize-horizontal:before {
  content: "\e120";
}

.glyphicon-hdd:before {
  content: "\e121";
}

.glyphicon-bullhorn:before {
  content: "\e122";
}

.glyphicon-certificate:before {
  content: "\e124";
}

.glyphicon-thumbs-up:before {
  content: "\e125";
}

.glyphicon-thumbs-down:before {
  content: "\e126";
}

.glyphicon-hand-right:before {
  content: "\e127";
}

.glyphicon-hand-left:before {
  content: "\e128";
}

.glyphicon-hand-up:before {
  content: "\e129";
}

.glyphicon-hand-down:before {
  content: "\e130";
}

.glyphicon-circle-arrow-right:before {
  content: "\e131";
}

.glyphicon-circle-arrow-left:before {
  content: "\e132";
}

.glyphicon-circle-arrow-up:before {
  content: "\e133";
}

.glyphicon-circle-arrow-down:before {
  content: "\e134";
}

.glyphicon-globe:before {
  content: "\e135";
}

.glyphicon-tasks:before {
  content: "\e137";
}

.glyphicon-filter:before {
  content: "\e138";
}

.glyphicon-fullscreen:before {
  content: "\e140";
}

.glyphicon-dashboard:before {
  content: "\e141";
}

.glyphicon-heart-empty:before {
  content: "\e143";
}

.glyphicon-link:before {
  content: "\e144";
}

.glyphicon-phone:before {
  content: "\e145";
}

.glyphicon-usd:before {
  content: "\e148";
}

.glyphicon-gbp:before {
  content: "\e149";
}

.glyphicon-sort:before {
  content: "\e150";
}

.glyphicon-sort-by-alphabet:before {
  content: "\e151";
}

.glyphicon-sort-by-alphabet-alt:before {
  content: "\e152";
}

.glyphicon-sort-by-order:before {
  content: "\e153";
}

.glyphicon-sort-by-order-alt:before {
  content: "\e154";
}

.glyphicon-sort-by-attributes:before {
  content: "\e155";
}

.glyphicon-sort-by-attributes-alt:before {
  content: "\e156";
}

.glyphicon-unchecked:before {
  content: "\e157";
}

.glyphicon-expand:before {
  content: "\e158";
}

.glyphicon-collapse-down:before {
  content: "\e159";
}

.glyphicon-collapse-up:before {
  content: "\e160";
}

.glyphicon-log-in:before {
  content: "\e161";
}

.glyphicon-flash:before {
  content: "\e162";
}

.glyphicon-log-out:before {
  content: "\e163";
}

.glyphicon-new-window:before {
  content: "\e164";
}

.glyphicon-record:before {
  content: "\e165";
}

.glyphicon-save:before {
  content: "\e166";
}

.glyphicon-open:before {
  content: "\e167";
}

.glyphicon-saved:before {
  content: "\e168";
}

.glyphicon-import:before {
  content: "\e169";
}

.glyphicon-export:before {
  content: "\e170";
}

.glyphicon-send:before {
  content: "\e171";
}

.glyphicon-floppy-disk:before {
  content: "\e172";
}

.glyphicon-floppy-saved:before {
  content: "\e173";
}

.glyphicon-floppy-remove:before {
  content: "\e174";
}

.glyphicon-floppy-save:before {
  content: "\e175";
}

.glyphicon-floppy-open:before {
  content: "\e176";
}

.glyphicon-credit-card:before {
  content: "\e177";
}

.glyphicon-transfer:before {
  content: "\e178";
}

.glyphicon-cutlery:before {
  content: "\e179";
}

.glyphicon-header:before {
  content: "\e180";
}

.glyphicon-compressed:before {
  content: "\e181";
}

.glyphicon-earphone:before {
  content: "\e182";
}

.glyphicon-phone-alt:before {
  content: "\e183";
}

.glyphicon-tower:before {
  content: "\e184";
}

.glyphicon-stats:before {
  content: "\e185";
}

.glyphicon-sd-video:before {
  content: "\e186";
}

.glyphicon-hd-video:before {
  content: "\e187";
}

.glyphicon-subtitles:before {
  content: "\e188";
}

.glyphicon-sound-stereo:before {
  content: "\e189";
}

.glyphicon-sound-dolby:before {
  content: "\e190";
}

.glyphicon-sound-5-1:before {
  content: "\e191";
}

.glyphicon-sound-6-1:before {
  content: "\e192";
}

.glyphicon-sound-7-1:before {
  content: "\e193";
}

.glyphicon-copyright-mark:before {
  content: "\e194";
}

.glyphicon-registration-mark:before {
  content: "\e195";
}

.glyphicon-cloud-download:before {
  content: "\e197";
}

.glyphicon-cloud-upload:before {
  content: "\e198";
}

.glyphicon-tree-conifer:before {
  content: "\e199";
}

.glyphicon-tree-deciduous:before {
  content: "\e200";
}

.glyphicon-briefcase:before {
  content: "\1f4bc";
}

.glyphicon-calendar:before {
  content: "\1f4c5";
}

.glyphicon-pushpin:before {
  content: "\1f4cc";
}

.glyphicon-paperclip:before {
  content: "\1f4ce";
}

.glyphicon-camera:before {
  content: "\1f4f7";
}

.glyphicon-lock:before {
  content: "\1f512";
}

.glyphicon-bell:before {
  content: "\1f514";
}

.glyphicon-bookmark:before {
  content: "\1f516";
}

.glyphicon-fire:before {
  content: "\1f525";
}

.glyphicon-wrench:before {
  content: "\1f527";
}

/* ==|== Panel (form Twitter Bootstrap v3.0 ================================= */
.panel {
  margin-bottom: 20px;
  background-color: #ffffff;
  border: 1px solid transparent;
  border-radius: 4px;
  -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
          box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
}

.panel-body {
  padding: 15px;
}

.panel-body:before,
.panel-body:after {
  display: table;
  content: " ";
}

.panel-body:after {
  clear: both;
}

.panel-body:before,
.panel-body:after {
  display: table;
  content: " ";
}

.panel-body:after {
  clear: both;
}

.panel > .list-group {
  margin-bottom: 0;
}

.panel > .list-group .list-group-item {
  border-width: 1px 0;
}

.panel > .list-group .list-group-item:first-child {
  border-top-right-radius: 0;
  border-top-left-radius: 0;
}

.panel > .list-group .list-group-item:last-child {
  border-bottom: 0;
}

.panel-heading + .list-group .list-group-item:first-child {
  border-top-width: 0;
}

.panel > .table {
  margin-bottom: 0;
}

.panel > .panel-body + .table {
  border-top: 1px solid #dddddd;
}

.panel-heading {
  padding: 10px 15px;
  border-bottom: 1px solid transparent;
  border-top-right-radius: 3px;
  border-top-left-radius: 3px;
}

.panel-title {
  margin-top: 0;
  margin-bottom: 0;
  font-size: 16px;
}

.panel-title > a {
  color: inherit;
}

.panel-footer {
  padding: 10px 15px;
  background-color: #f5f5f5;
  border-top: 1px solid #dddddd;
  border-bottom-right-radius: 3px;
  border-bottom-left-radius: 3px;
}

.panel-group .panel {
  margin-bottom: 0;
  overflow: hidden;
  border-radius: 4px;
}

.panel-group .panel + .panel {
  margin-top: 5px;
}

.panel-group .panel-heading {
  border-bottom: 0;
}

.panel-group .panel-heading + .panel-collapse .panel-body {
  border-top: 1px solid #dddddd;
}

.panel-group .panel-footer {
  border-top: 0;
}

.panel-group .panel-footer + .panel-collapse .panel-body {
  border-bottom: 1px solid #dddddd;
}

.panel-default {
  border-color: #dddddd;
}

.panel-default > .panel-heading {
  color: #333333;
  background-color: #f5f5f5;
  border-color: #dddddd;
}

.panel-default > .panel-heading + .panel-collapse .panel-body {
  border-top-color: #dddddd;
}

.panel-default > .panel-footer + .panel-collapse .panel-body {
  border-bottom-color: #dddddd;
}

.panel-primary {
  border-color: #428bca;
}

.panel-primary > .panel-heading {
  color: #ffffff;
  background-color: #428bca;
  border-color: #428bca;
}

.panel-primary > .panel-heading + .panel-collapse .panel-body {
  border-top-color: #428bca;
}

.panel-primary > .panel-footer + .panel-collapse .panel-body {
  border-bottom-color: #428bca;
}

.panel-success {
  border-color: #d6e9c6;
}

.panel-success > .panel-heading {
  color: #468847;
  background-color: #dff0d8;
  border-color: #d6e9c6;
}

.panel-success > .panel-heading + .panel-collapse .panel-body {
  border-top-color: #d6e9c6;
}

.panel-success > .panel-footer + .panel-collapse .panel-body {
  border-bottom-color: #d6e9c6;
}

.panel-warning {
  border-color: #fbeed5;
}

.panel-warning > .panel-heading {
  color: #c09853;
  background-color: #fcf8e3;
  border-color: #fbeed5;
}

.panel-warning > .panel-heading + .panel-collapse .panel-body {
  border-top-color: #fbeed5;
}

.panel-warning > .panel-footer + .panel-collapse .panel-body {
  border-bottom-color: #fbeed5;
}

.panel-danger {
  border-color: #eed3d7;
}

.panel-danger > .panel-heading {
  color: #b94a48;
  background-color: #f2dede;
  border-color: #eed3d7;
}

.panel-danger > .panel-heading + .panel-collapse .panel-body {
  border-top-color: #eed3d7;
}

.panel-danger > .panel-footer + .panel-collapse .panel-body {
  border-bottom-color: #eed3d7;
}

.panel-info {
  border-color: #bce8f1;
}

.panel-info > .panel-heading {
  color: #3a87ad;
  background-color: #d9edf7;
  border-color: #bce8f1;
}

.panel-info > .panel-heading + .panel-collapse .panel-body {
  border-top-color: #bce8f1;
}

.panel-info > .panel-footer + .panel-collapse .panel-body {
  border-bottom-color: #bce8f1;
}

.panel {
  -webkit-box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
          box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.panel-default > .panel-heading {
  background-image: -webkit-gradient(linear, left 0%, left 100%, from(#f5f5f5), to(#e8e8e8));
  background-image: -webkit-linear-gradient(top, #f5f5f5, 0%, #e8e8e8, 100%);
  background-image: -moz-linear-gradient(top, #f5f5f5 0%, #e8e8e8 100%);
  background-image: linear-gradient(to bottom, #f5f5f5 0%, #e8e8e8 100%);
  background-repeat: repeat-x;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#fff5f5f5', endColorstr='#ffe8e8e8', GradientType=0);
}

.panel-primary > .panel-heading {
  background-image: -webkit-gradient(linear, left 0%, left 100%, from(#428bca), to(#357ebd));
  background-image: -webkit-linear-gradient(top, #428bca, 0%, #357ebd, 100%);
  background-image: -moz-linear-gradient(top, #428bca 0%, #357ebd 100%);
  background-image: linear-gradient(to bottom, #428bca 0%, #357ebd 100%);
  background-repeat: repeat-x;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff428bca', endColorstr='#ff357ebd', GradientType=0);
}

.panel-success > .panel-heading {
  background-image: -webkit-gradient(linear, left 0%, left 100%, from(#dff0d8), to(#d0e9c6));
  background-image: -webkit-linear-gradient(top, #dff0d8, 0%, #d0e9c6, 100%);
  background-image: -moz-linear-gradient(top, #dff0d8 0%, #d0e9c6 100%);
  background-image: linear-gradient(to bottom, #dff0d8 0%, #d0e9c6 100%);
  background-repeat: repeat-x;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffdff0d8', endColorstr='#ffd0e9c6', GradientType=0);
}

.panel-info > .panel-heading {
  background-image: -webkit-gradient(linear, left 0%, left 100%, from(#d9edf7), to(#c4e3f3));
  background-image: -webkit-linear-gradient(top, #d9edf7, 0%, #c4e3f3, 100%);
  background-image: -moz-linear-gradient(top, #d9edf7 0%, #c4e3f3 100%);
  background-image: linear-gradient(to bottom, #d9edf7 0%, #c4e3f3 100%);
  background-repeat: repeat-x;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffd9edf7', endColorstr='#ffc4e3f3', GradientType=0);
}

.panel-warning > .panel-heading {
  background-image: -webkit-gradient(linear, left 0%, left 100%, from(#fcf8e3), to(#faf2cc));
  background-image: -webkit-linear-gradient(top, #fcf8e3, 0%, #faf2cc, 100%);
  background-image: -moz-linear-gradient(top, #fcf8e3 0%, #faf2cc 100%);
  background-image: linear-gradient(to bottom, #fcf8e3 0%, #faf2cc 100%);
  background-repeat: repeat-x;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#fffcf8e3', endColorstr='#fffaf2cc', GradientType=0);
}

.panel-danger > .panel-heading {
  background-image: -webkit-gradient(linear, left 0%, left 100%, from(#f2dede), to(#ebcccc));
  background-image: -webkit-linear-gradient(top, #f2dede, 0%, #ebcccc, 100%);
  background-image: -moz-linear-gradient(top, #f2dede 0%, #ebcccc 100%);
  background-image: linear-gradient(to bottom, #f2dede 0%, #ebcccc 100%);
  background-repeat: repeat-x;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#fff2dede', endColorstr='#ffebcccc', GradientType=0);
}

/* ==|== Wells (form Twitter Bootstrap v3.0 ================================= */
.well {
  min-height: 20px;
  padding: 19px;
  margin-bottom: 20px;
  background-color: #f5f5f5;
  border: 1px solid #e3e3e3;
  border-radius: 4px;
  -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
          box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
}

.well blockquote {
  border-color: #ddd;
  border-color: rgba(0, 0, 0, 0.15);
}

.well-lg {
  padding: 24px;
  border-radius: 6px;
}

.well-sm {
  padding: 9px;
  border-radius: 3px;
}

.well {
  background-image: -webkit-gradient(linear, left 0%, left 100%, from(#e8e8e8), to(#f5f5f5));
  background-image: -webkit-linear-gradient(top, #e8e8e8, 0%, #f5f5f5, 100%);
  background-image: -moz-linear-gradient(top, #e8e8e8 0%, #f5f5f5 100%);
  background-image: linear-gradient(to bottom, #e8e8e8 0%, #f5f5f5 100%);
  background-repeat: repeat-x;
  border-color: #dcdcdc;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffe8e8e8', endColorstr='#fff5f5f5', GradientType=0);
  -webkit-box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 0 rgba(255, 255, 255, 0.1);
          box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 0 rgba(255, 255, 255, 0.1);
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
  margin-right: -15px;
  margin-left: -15px;
}

.row:before,
.row:after {
  display: table;
  content: " ";
}

.row:after {
  clear: both;
}

.row:before,
.row:after {
  display: table;
  content: " ";
}

.row:after {
  clear: both;
}

.col-xs-1,
.col-xs-2,
.col-xs-3,
.col-xs-4,
.col-xs-5,
.col-xs-6,
.col-xs-7,
.col-xs-8,
.col-xs-9,
.col-xs-10,
.col-xs-11,
.col-xs-12,
.col-sm-1,
.col-sm-2,
.col-sm-3,
.col-sm-4,
.col-sm-5,
.col-sm-6,
.col-sm-7,
.col-sm-8,
.col-sm-9,
.col-sm-10,
.col-sm-11,
.col-sm-12,
.col-md-1,
.col-md-2,
.col-md-3,
.col-md-4,
.col-md-5,
.col-md-6,
.col-md-7,
.col-md-8,
.col-md-9,
.col-md-10,
.col-md-11,
.col-md-12,
.col-lg-1,
.col-lg-2,
.col-lg-3,
.col-lg-4,
.col-lg-5,
.col-lg-6,
.col-lg-7,
.col-lg-8,
.col-lg-9,
.col-lg-10,
.col-lg-11,
.col-lg-12 {
  position: relative;
  min-height: 1px;
  padding-right: 15px;
  padding-left: 15px;
}

.col-xs-1,
.col-xs-2,
.col-xs-3,
.col-xs-4,
.col-xs-5,
.col-xs-6,
.col-xs-7,
.col-xs-8,
.col-xs-9,
.col-xs-10,
.col-xs-11 {
  float: left;
}

.col-xs-1 {
  width: 8.333333333333332%;
}

.col-xs-2 {
  width: 16.666666666666664%;
}

.col-xs-3 {
  width: 25%;
}

.col-xs-4 {
  width: 33.33333333333333%;
}

.col-xs-5 {
  width: 41.66666666666667%;
}

.col-xs-6 {
  width: 50%;
}

.col-xs-7 {
  width: 58.333333333333336%;
}

.col-xs-8 {
  width: 66.66666666666666%;
}

.col-xs-9 {
  width: 75%;
}

.col-xs-10 {
  width: 83.33333333333334%;
}

.col-xs-11 {
  width: 91.66666666666666%;
}

.col-xs-12 {
  width: 100%;
}

@media (min-width: 768px) {
  .container {
    max-width: 750px;
  }
  .col-sm-1,
  .col-sm-2,
  .col-sm-3,
  .col-sm-4,
  .col-sm-5,
  .col-sm-6,
  .col-sm-7,
  .col-sm-8,
  .col-sm-9,
  .col-sm-10,
  .col-sm-11 {
    float: left;
  }
  .col-sm-1 {
    width: 8.333333333333332%;
  }
  .col-sm-2 {
    width: 16.666666666666664%;
  }
  .col-sm-3 {
    width: 25%;
  }
  .col-sm-4 {
    width: 33.33333333333333%;
  }
  .col-sm-5 {
    width: 41.66666666666667%;
  }
  .col-sm-6 {
    width: 50%;
  }
  .col-sm-7 {
    width: 58.333333333333336%;
  }
  .col-sm-8 {
    width: 66.66666666666666%;
  }
  .col-sm-9 {
    width: 75%;
  }
  .col-sm-10 {
    width: 83.33333333333334%;
  }
  .col-sm-11 {
    width: 91.66666666666666%;
  }
  .col-sm-12 {
    width: 100%;
  }
  .col-sm-push-1 {
    left: 8.333333333333332%;
  }
  .col-sm-push-2 {
    left: 16.666666666666664%;
  }
  .col-sm-push-3 {
    left: 25%;
  }
  .col-sm-push-4 {
    left: 33.33333333333333%;
  }
  .col-sm-push-5 {
    left: 41.66666666666667%;
  }
  .col-sm-push-6 {
    left: 50%;
  }
  .col-sm-push-7 {
    left: 58.333333333333336%;
  }
  .col-sm-push-8 {
    left: 66.66666666666666%;
  }
  .col-sm-push-9 {
    left: 75%;
  }
  .col-sm-push-10 {
    left: 83.33333333333334%;
  }
  .col-sm-push-11 {
    left: 91.66666666666666%;
  }
  .col-sm-pull-1 {
    right: 8.333333333333332%;
  }
  .col-sm-pull-2 {
    right: 16.666666666666664%;
  }
  .col-sm-pull-3 {
    right: 25%;
  }
  .col-sm-pull-4 {
    right: 33.33333333333333%;
  }
  .col-sm-pull-5 {
    right: 41.66666666666667%;
  }
  .col-sm-pull-6 {
    right: 50%;
  }
  .col-sm-pull-7 {
    right: 58.333333333333336%;
  }
  .col-sm-pull-8 {
    right: 66.66666666666666%;
  }
  .col-sm-pull-9 {
    right: 75%;
  }
  .col-sm-pull-10 {
    right: 83.33333333333334%;
  }
  .col-sm-pull-11 {
    right: 91.66666666666666%;
  }
  .col-sm-offset-1 {
    margin-left: 8.333333333333332%;
  }
  .col-sm-offset-2 {
    margin-left: 16.666666666666664%;
  }
  .col-sm-offset-3 {
    margin-left: 25%;
  }
  .col-sm-offset-4 {
    margin-left: 33.33333333333333%;
  }
  .col-sm-offset-5 {
    margin-left: 41.66666666666667%;
  }
  .col-sm-offset-6 {
    margin-left: 50%;
  }
  .col-sm-offset-7 {
    margin-left: 58.333333333333336%;
  }
  .col-sm-offset-8 {
    margin-left: 66.66666666666666%;
  }
  .col-sm-offset-9 {
    margin-left: 75%;
  }
  .col-sm-offset-10 {
    margin-left: 83.33333333333334%;
  }
  .col-sm-offset-11 {
    margin-left: 91.66666666666666%;
  }
}

@media (min-width: 992px) {
  .container {
    max-width: 970px;
  }
  .col-md-1,
  .col-md-2,
  .col-md-3,
  .col-md-4,
  .col-md-5,
  .col-md-6,
  .col-md-7,
  .col-md-8,
  .col-md-9,
  .col-md-10,
  .col-md-11 {
    float: left;
  }
  .col-md-1 {
    width: 8.333333333333332%;
  }
  .col-md-2 {
    width: 16.666666666666664%;
  }
  .col-md-3 {
    width: 25%;
  }
  .col-md-4 {
    width: 33.33333333333333%;
  }
  .col-md-5 {
    width: 41.66666666666667%;
  }
  .col-md-6 {
    width: 50%;
  }
  .col-md-7 {
    width: 58.333333333333336%;
  }
  .col-md-8 {
    width: 66.66666666666666%;
  }
  .col-md-9 {
    width: 75%;
  }
  .col-md-10 {
    width: 83.33333333333334%;
  }
  .col-md-11 {
    width: 91.66666666666666%;
  }
  .col-md-12 {
    width: 100%;
  }
  .col-md-push-0 {
    left: auto;
  }
  .col-md-push-1 {
    left: 8.333333333333332%;
  }
  .col-md-push-2 {
    left: 16.666666666666664%;
  }
  .col-md-push-3 {
    left: 25%;
  }
  .col-md-push-4 {
    left: 33.33333333333333%;
  }
  .col-md-push-5 {
    left: 41.66666666666667%;
  }
  .col-md-push-6 {
    left: 50%;
  }
  .col-md-push-7 {
    left: 58.333333333333336%;
  }
  .col-md-push-8 {
    left: 66.66666666666666%;
  }
  .col-md-push-9 {
    left: 75%;
  }
  .col-md-push-10 {
    left: 83.33333333333334%;
  }
  .col-md-push-11 {
    left: 91.66666666666666%;
  }
  .col-md-pull-0 {
    right: auto;
  }
  .col-md-pull-1 {
    right: 8.333333333333332%;
  }
  .col-md-pull-2 {
    right: 16.666666666666664%;
  }
  .col-md-pull-3 {
    right: 25%;
  }
  .col-md-pull-4 {
    right: 33.33333333333333%;
  }
  .col-md-pull-5 {
    right: 41.66666666666667%;
  }
  .col-md-pull-6 {
    right: 50%;
  }
  .col-md-pull-7 {
    right: 58.333333333333336%;
  }
  .col-md-pull-8 {
    right: 66.66666666666666%;
  }
  .col-md-pull-9 {
    right: 75%;
  }
  .col-md-pull-10 {
    right: 83.33333333333334%;
  }
  .col-md-pull-11 {
    right: 91.66666666666666%;
  }
  .col-md-offset-0 {
    margin-left: 0;
  }
  .col-md-offset-1 {
    margin-left: 8.333333333333332%;
  }
  .col-md-offset-2 {
    margin-left: 16.666666666666664%;
  }
  .col-md-offset-3 {
    margin-left: 25%;
  }
  .col-md-offset-4 {
    margin-left: 33.33333333333333%;
  }
  .col-md-offset-5 {
    margin-left: 41.66666666666667%;
  }
  .col-md-offset-6 {
    margin-left: 50%;
  }
  .col-md-offset-7 {
    margin-left: 58.333333333333336%;
  }
  .col-md-offset-8 {
    margin-left: 66.66666666666666%;
  }
  .col-md-offset-9 {
    margin-left: 75%;
  }
  .col-md-offset-10 {
    margin-left: 83.33333333333334%;
  }
  .col-md-offset-11 {
    margin-left: 91.66666666666666%;
  }
}

@media (min-width: 1200px) {
  .container {
    max-width: 1170px;
  }
  .col-lg-1,
  .col-lg-2,
  .col-lg-3,
  .col-lg-4,
  .col-lg-5,
  .col-lg-6,
  .col-lg-7,
  .col-lg-8,
  .col-lg-9,
  .col-lg-10,
  .col-lg-11 {
    float: left;
  }
  .col-lg-1 {
    width: 8.333333333333332%;
  }
  .col-lg-2 {
    width: 16.666666666666664%;
  }
  .col-lg-3 {
    width: 25%;
  }
  .col-lg-4 {
    width: 33.33333333333333%;
  }
  .col-lg-5 {
    width: 41.66666666666667%;
  }
  .col-lg-6 {
    width: 50%;
  }
  .col-lg-7 {
    width: 58.333333333333336%;
  }
  .col-lg-8 {
    width: 66.66666666666666%;
  }
  .col-lg-9 {
    width: 75%;
  }
  .col-lg-10 {
    width: 83.33333333333334%;
  }
  .col-lg-11 {
    width: 91.66666666666666%;
  }
  .col-lg-12 {
    width: 100%;
  }
  .col-lg-push-0 {
    left: auto;
  }
  .col-lg-push-1 {
    left: 8.333333333333332%;
  }
  .col-lg-push-2 {
    left: 16.666666666666664%;
  }
  .col-lg-push-3 {
    left: 25%;
  }
  .col-lg-push-4 {
    left: 33.33333333333333%;
  }
  .col-lg-push-5 {
    left: 41.66666666666667%;
  }
  .col-lg-push-6 {
    left: 50%;
  }
  .col-lg-push-7 {
    left: 58.333333333333336%;
  }
  .col-lg-push-8 {
    left: 66.66666666666666%;
  }
  .col-lg-push-9 {
    left: 75%;
  }
  .col-lg-push-10 {
    left: 83.33333333333334%;
  }
  .col-lg-push-11 {
    left: 91.66666666666666%;
  }
  .col-lg-pull-0 {
    right: auto;
  }
  .col-lg-pull-1 {
    right: 8.333333333333332%;
  }
  .col-lg-pull-2 {
    right: 16.666666666666664%;
  }
  .col-lg-pull-3 {
    right: 25%;
  }
  .col-lg-pull-4 {
    right: 33.33333333333333%;
  }
  .col-lg-pull-5 {
    right: 41.66666666666667%;
  }
  .col-lg-pull-6 {
    right: 50%;
  }
  .col-lg-pull-7 {
    right: 58.333333333333336%;
  }
  .col-lg-pull-8 {
    right: 66.66666666666666%;
  }
  .col-lg-pull-9 {
    right: 75%;
  }
  .col-lg-pull-10 {
    right: 83.33333333333334%;
  }
  .col-lg-pull-11 {
    right: 91.66666666666666%;
  }
  .col-lg-offset-0 {
    margin-left: 0;
  }
  .col-lg-offset-1 {
    margin-left: 8.333333333333332%;
  }
  .col-lg-offset-2 {
    margin-left: 16.666666666666664%;
  }
  .col-lg-offset-3 {
    margin-left: 25%;
  }
  .col-lg-offset-4 {
    margin-left: 33.33333333333333%;
  }
  .col-lg-offset-5 {
    margin-left: 41.66666666666667%;
  }
  .col-lg-offset-6 {
    margin-left: 50%;
  }
  .col-lg-offset-7 {
    margin-left: 58.333333333333336%;
  }
  .col-lg-offset-8 {
    margin-left: 66.66666666666666%;
  }
  .col-lg-offset-9 {
    margin-left: 75%;
  }
  .col-lg-offset-10 {
    margin-left: 83.33333333333334%;
  }
  .col-lg-offset-11 {
    margin-left: 91.66666666666666%;
  }
}


/* ==|== Button (from Twitter Bootstrap) ==================================== */

.btn {
  display: inline-block;
  padding: 6px 12px;
  margin-bottom: 0;
  font-size: 14px;
  font-weight: normal;
  line-height: 1.428571429;
  text-align: center;
  white-space: nowrap;
  vertical-align: middle;
  cursor: pointer;
  border: 1px solid transparent;
  border-radius: 4px;
  -webkit-user-select: none;
     -moz-user-select: none;
      -ms-user-select: none;
       -o-user-select: none;
          user-select: none;
}

.btn:focus {
  outline: thin dotted #333;
  outline: 5px auto -webkit-focus-ring-color;
  outline-offset: -2px;
}

.btn:hover,
.btn:focus {
  color: #333333;
  text-decoration: none;
}

.btn:active,
.btn.active {
  background-image: none;
  outline: 0;
  -webkit-box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
          box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
}

.btn.disabled,
.btn[disabled],
fieldset[disabled] .btn {
  pointer-events: none;
  cursor: not-allowed;
  opacity: 0.65;
  filter: alpha(opacity=65);
  -webkit-box-shadow: none;
          box-shadow: none;
}

.btn-default {
  color: #333333;
  background-color: #ffffff;
  border-color: #cccccc;
}

.btn-default:hover,
.btn-default:focus,
.btn-default:active,
.btn-default.active,
.open .dropdown-toggle.btn-default {
  color: #333333;
  background-color: #ebebeb;
  border-color: #adadad;
}

.btn-default:active,
.btn-default.active,
.open .dropdown-toggle.btn-default {
  background-image: none;
}

.btn-default.disabled,
.btn-default[disabled],
fieldset[disabled] .btn-default,
.btn-default.disabled:hover,
.btn-default[disabled]:hover,
fieldset[disabled] .btn-default:hover,
.btn-default.disabled:focus,
.btn-default[disabled]:focus,
fieldset[disabled] .btn-default:focus,
.btn-default.disabled:active,
.btn-default[disabled]:active,
fieldset[disabled] .btn-default:active,
.btn-default.disabled.active,
.btn-default[disabled].active,
fieldset[disabled] .btn-default.active {
  background-color: #ffffff;
  border-color: #cccccc;
}

.btn-primary {
  color: #ffffff;
  background-color: #428bca;
  border-color: #357ebd;
}

.btn-primary:hover,
.btn-primary:focus,
.btn-primary:active,
.btn-primary.active,
.open .dropdown-toggle.btn-primary {
  color: #ffffff;
  background-color: #3276b1;
  border-color: #285e8e;
}

.btn-primary:active,
.btn-primary.active,
.open .dropdown-toggle.btn-primary {
  background-image: none;
}

.btn-primary.disabled,
.btn-primary[disabled],
fieldset[disabled] .btn-primary,
.btn-primary.disabled:hover,
.btn-primary[disabled]:hover,
fieldset[disabled] .btn-primary:hover,
.btn-primary.disabled:focus,
.btn-primary[disabled]:focus,
fieldset[disabled] .btn-primary:focus,
.btn-primary.disabled:active,
.btn-primary[disabled]:active,
fieldset[disabled] .btn-primary:active,
.btn-primary.disabled.active,
.btn-primary[disabled].active,
fieldset[disabled] .btn-primary.active {
  background-color: #428bca;
  border-color: #357ebd;
}

.btn-warning {
  color: #ffffff;
  background-color: #f0ad4e;
  border-color: #eea236;
}

.btn-warning:hover,
.btn-warning:focus,
.btn-warning:active,
.btn-warning.active,
.open .dropdown-toggle.btn-warning {
  color: #ffffff;
  background-color: #ed9c28;
  border-color: #d58512;
}

.btn-warning:active,
.btn-warning.active,
.open .dropdown-toggle.btn-warning {
  background-image: none;
}

.btn-warning.disabled,
.btn-warning[disabled],
fieldset[disabled] .btn-warning,
.btn-warning.disabled:hover,
.btn-warning[disabled]:hover,
fieldset[disabled] .btn-warning:hover,
.btn-warning.disabled:focus,
.btn-warning[disabled]:focus,
fieldset[disabled] .btn-warning:focus,
.btn-warning.disabled:active,
.btn-warning[disabled]:active,
fieldset[disabled] .btn-warning:active,
.btn-warning.disabled.active,
.btn-warning[disabled].active,
fieldset[disabled] .btn-warning.active {
  background-color: #f0ad4e;
  border-color: #eea236;
}

.btn-danger {
  color: #ffffff;
  background-color: #d9534f;
  border-color: #d43f3a;
}

.btn-danger:hover,
.btn-danger:focus,
.btn-danger:active,
.btn-danger.active,
.open .dropdown-toggle.btn-danger {
  color: #ffffff;
  background-color: #d2322d;
  border-color: #ac2925;
}

.btn-danger:active,
.btn-danger.active,
.open .dropdown-toggle.btn-danger {
  background-image: none;
}

.btn-danger.disabled,
.btn-danger[disabled],
fieldset[disabled] .btn-danger,
.btn-danger.disabled:hover,
.btn-danger[disabled]:hover,
fieldset[disabled] .btn-danger:hover,
.btn-danger.disabled:focus,
.btn-danger[disabled]:focus,
fieldset[disabled] .btn-danger:focus,
.btn-danger.disabled:active,
.btn-danger[disabled]:active,
fieldset[disabled] .btn-danger:active,
.btn-danger.disabled.active,
.btn-danger[disabled].active,
fieldset[disabled] .btn-danger.active {
  background-color: #d9534f;
  border-color: #d43f3a;
}

.btn-success {
  color: #ffffff;
  background-color: #5cb85c;
  border-color: #4cae4c;
}

.btn-success:hover,
.btn-success:focus,
.btn-success:active,
.btn-success.active,
.open .dropdown-toggle.btn-success {
  color: #ffffff;
  background-color: #47a447;
  border-color: #398439;
}

.btn-success:active,
.btn-success.active,
.open .dropdown-toggle.btn-success {
  background-image: none;
}

.btn-success.disabled,
.btn-success[disabled],
fieldset[disabled] .btn-success,
.btn-success.disabled:hover,
.btn-success[disabled]:hover,
fieldset[disabled] .btn-success:hover,
.btn-success.disabled:focus,
.btn-success[disabled]:focus,
fieldset[disabled] .btn-success:focus,
.btn-success.disabled:active,
.btn-success[disabled]:active,
fieldset[disabled] .btn-success:active,
.btn-success.disabled.active,
.btn-success[disabled].active,
fieldset[disabled] .btn-success.active {
  background-color: #5cb85c;
  border-color: #4cae4c;
}

.btn-info {
  color: #ffffff;
  background-color: #5bc0de;
  border-color: #46b8da;
}

.btn-info:hover,
.btn-info:focus,
.btn-info:active,
.btn-info.active,
.open .dropdown-toggle.btn-info {
  color: #ffffff;
  background-color: #39b3d7;
  border-color: #269abc;
}

.btn-info:active,
.btn-info.active,
.open .dropdown-toggle.btn-info {
  background-image: none;
}

.btn-info.disabled,
.btn-info[disabled],
fieldset[disabled] .btn-info,
.btn-info.disabled:hover,
.btn-info[disabled]:hover,
fieldset[disabled] .btn-info:hover,
.btn-info.disabled:focus,
.btn-info[disabled]:focus,
fieldset[disabled] .btn-info:focus,
.btn-info.disabled:active,
.btn-info[disabled]:active,
fieldset[disabled] .btn-info:active,
.btn-info.disabled.active,
.btn-info[disabled].active,
fieldset[disabled] .btn-info.active {
  background-color: #5bc0de;
  border-color: #46b8da;
}

.btn-link {
  font-weight: normal;
  color: #428bca;
  cursor: pointer;
  border-radius: 0;
}

.btn-link,
.btn-link:active,
.btn-link[disabled],
fieldset[disabled] .btn-link {
  background-color: transparent;
  -webkit-box-shadow: none;
          box-shadow: none;
}

.btn-link,
.btn-link:hover,
.btn-link:focus,
.btn-link:active {
  border-color: transparent;
}

.btn-link:hover,
.btn-link:focus {
  color: #2a6496;
  text-decoration: underline;
  background-color: transparent;
}

.btn-link[disabled]:hover,
fieldset[disabled] .btn-link:hover,
.btn-link[disabled]:focus,
fieldset[disabled] .btn-link:focus {
  color: #999999;
  text-decoration: none;
}

.btn-lg {
  padding: 10px 16px;
  font-size: 18px;
  line-height: 1.33;
  border-radius: 6px;
}

.btn-sm,
.btn-xs {
  padding: 5px 10px;
  font-size: 12px;
  line-height: 1.5;
  border-radius: 3px;
}

.btn-xs {
  padding: 1px 5px;
}

.btn-block {
  display: block;
  width: 100%;
  padding-right: 0;
  padding-left: 0;
}

.btn-block + .btn-block {
  margin-top: 5px;
}

input[type="submit"].btn-block,
input[type="reset"].btn-block,
input[type="button"].btn-block {
  width: 100%;
}

.btn-default,
.btn-primary,
.btn-success,
.btn-info,
.btn-warning,
.btn-danger {
  text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.2);
  -webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.15), 0 1px 1px rgba(0, 0, 0, 0.075);
          box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.15), 0 1px 1px rgba(0, 0, 0, 0.075);
}

.btn-default:active,
.btn-primary:active,
.btn-success:active,
.btn-info:active,
.btn-warning:active,
.btn-danger:active,
.btn-default.active,
.btn-primary.active,
.btn-success.active,
.btn-info.active,
.btn-warning.active,
.btn-danger.active {
  -webkit-box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
          box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
}

.btn:active,
.btn.active {
  background-image: none;
}

.btn-default {
  text-shadow: 0 1px 0 #fff;
  background-image: -webkit-gradient(linear, left 0%, left 100%, from(#ffffff), to(#e6e6e6));
  background-image: -webkit-linear-gradient(top, #ffffff, 0%, #e6e6e6, 100%);
  background-image: -moz-linear-gradient(top, #ffffff 0%, #e6e6e6 100%);
  background-image: linear-gradient(to bottom, #ffffff 0%, #e6e6e6 100%);
  background-repeat: repeat-x;
  border-color: #e0e0e0;
  border-color: #ccc;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffffff', endColorstr='#ffe6e6e6', GradientType=0);
}

.btn-default:active,
.btn-default.active {
  background-color: #e6e6e6;
  border-color: #e0e0e0;
}

.btn-primary {
  background-image: -webkit-gradient(linear, left 0%, left 100%, from(#428bca), to(#3071a9));
  background-image: -webkit-linear-gradient(top, #428bca, 0%, #3071a9, 100%);
  background-image: -moz-linear-gradient(top, #428bca 0%, #3071a9 100%);
  background-image: linear-gradient(to bottom, #428bca 0%, #3071a9 100%);
  background-repeat: repeat-x;
  border-color: #2d6ca2;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff428bca', endColorstr='#ff3071a9', GradientType=0);
}

.btn-primary:active,
.btn-primary.active {
  background-color: #3071a9;
  border-color: #2d6ca2;
}

.btn-success {
  background-image: -webkit-gradient(linear, left 0%, left 100%, from(#5cb85c), to(#449d44));
  background-image: -webkit-linear-gradient(top, #5cb85c, 0%, #449d44, 100%);
  background-image: -moz-linear-gradient(top, #5cb85c 0%, #449d44 100%);
  background-image: linear-gradient(to bottom, #5cb85c 0%, #449d44 100%);
  background-repeat: repeat-x;
  border-color: #419641;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff5cb85c', endColorstr='#ff449d44', GradientType=0);
}

.btn-success:active,
.btn-success.active {
  background-color: #449d44;
  border-color: #419641;
}

.btn-warning {
  background-image: -webkit-gradient(linear, left 0%, left 100%, from(#f0ad4e), to(#ec971f));
  background-image: -webkit-linear-gradient(top, #f0ad4e, 0%, #ec971f, 100%);
  background-image: -moz-linear-gradient(top, #f0ad4e 0%, #ec971f 100%);
  background-image: linear-gradient(to bottom, #f0ad4e 0%, #ec971f 100%);
  background-repeat: repeat-x;
  border-color: #eb9316;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#fff0ad4e', endColorstr='#ffec971f', GradientType=0);
}

.btn-warning:active,
.btn-warning.active {
  background-color: #ec971f;
  border-color: #eb9316;
}

.btn-danger {
  background-image: -webkit-gradient(linear, left 0%, left 100%, from(#d9534f), to(#c9302c));
  background-image: -webkit-linear-gradient(top, #d9534f, 0%, #c9302c, 100%);
  background-image: -moz-linear-gradient(top, #d9534f 0%, #c9302c 100%);
  background-image: linear-gradient(to bottom, #d9534f 0%, #c9302c 100%);
  background-repeat: repeat-x;
  border-color: #c12e2a;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffd9534f', endColorstr='#ffc9302c', GradientType=0);
}

.btn-danger:active,
.btn-danger.active {
  background-color: #c9302c;
  border-color: #c12e2a;
}

.btn-info {
  background-image: -webkit-gradient(linear, left 0%, left 100%, from(#5bc0de), to(#31b0d5));
  background-image: -webkit-linear-gradient(top, #5bc0de, 0%, #31b0d5, 100%);
  background-image: -moz-linear-gradient(top, #5bc0de 0%, #31b0d5 100%);
  background-image: linear-gradient(to bottom, #5bc0de 0%, #31b0d5 100%);
  background-repeat: repeat-x;
  border-color: #2aabd2;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff5bc0de', endColorstr='#ff31b0d5', GradientType=0);
}

.btn-info:active,
.btn-info.active {
  background-color: #31b0d5;
  border-color: #2aabd2;
}

/* ==|== Helper classes ===================================================== */

small {
  font-size: 85%;
}

cite {
  font-style: normal;
}

.text-muted {
  color: #999999;
}

.text-primary {
  color: #428bca;
}

.text-warning {
  color: #c09853;
}

.text-danger {
  color: #b94a48;
}

.text-success {
  color: #468847;
}

.text-info {
  color: #3a87ad;
}

.text-left {
  text-align: left;
}

.text-right {
  text-align: right;
}

.text-center {
  text-align: center;
}

.ir{background-color:transparent;border:0;overflow:hidden;text-indent:-9999px}
.ir:before{content:"";display:block;width:0;height:150%}

.hidden{display:none!important;visibility:hidden}
.visuallyhidden{border:0;clip:rect(0000);height:1px;overflow:hidden;position:absolute;width:1px;margin:-1px;padding:0}
.visuallyhidden.focusable:active,.visuallyhidden.focusable:focus{clip:auto;height:auto;overflow:visible;position:static;width:auto;margin:0}

.invisible{visibility:hidden}
.clearfix:before,
.clearfix:after {
  display: table;
  content: " ";
}

.clearfix:after {
  clear: both;
}

.pull-right {
  float: right !important;
}

.pull-left {
  float: left !important;
}

.hide {
  display: none !important;
}

.show {
  display: block !important;
}

.invisible {
  visibility: hidden;
}

.text-hide {
  font: 0/0 a;
  color: transparent;
  text-shadow: none;
  background-color: transparent;
  border: 0;
}

.affix {
  position: fixed;
}

@-ms-viewport {
  width: device-width;
}

@media screen and (max-width: 400px) {
  @-ms-viewport {
    width: 320px;
  }
}

.hidden {
  display: none !important;
  visibility: hidden !important;
}
/* ==|== print styles ======================================================= */
@media print {
	*{background:transparent!important;color:#000!important;box-shadow:none!important;text-shadow:none!important}
	a,a:visited{text-decoration:underline}
	a[href]:after{content:" (" attr(href) ")"}
	abbr[title]:after{content:" (" attr(title) ")"}
	.ir a:after,a[href^=javascript:]:after,a[href^=#]:after{content:""}
	pre,blockquote{border:1px solid #999;page-break-inside:avoid}
	thead{display:table-header-group}
	tr,img{page-break-inside:avoid}
	img{max-width:100%!important}
	@page{margin:.5cm}
	p,h2,h3{orphans:3;widows:3}
	h2,h3{page-break-after:avoid}

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