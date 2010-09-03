<?php
/**
 :prefix <http://purl.org/net/ns/doas#> .
 :about "<mml.inc.php>", a: ":PHPScript",
 :shortdesc "JSMML Player for PukiWiki";
 :created "2008-07-28", release: {revision: "1.2.4", created: "2010-09-03"},
 :author [:name "Logue"; :homepage <http://logue.be/> ];
 :license <http://www.gnu.org/licenses/gpl-3.0.html>;
*/
// JSMML for PukiWiki.
// Copyright (c)2008-2010 Logue <http://logue.be/> All rights reserved.

// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.

// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.

define(JSMML_PATH, SKIN_URI . 'js/plugin/jsmml');

function plugin_mml_convert(){
	global $script, $mml_count;
	$lang = null;

	if ($mml_count == '') $mml_count = 0;

	// 因数読み込み
	$value = func_get_args();	#中身
	$args = func_num_args();	#因数の数

	if ($args == 0){
		return '#mml() USAGE:#mml(title){mmldata}';
	// mabimmlとの互換処理。（っているのか？）
	}else if ($args == 1){
		// #mml(mml)
		$data = $value[0];
	}else if ($args == 2){
		// #mml(名前){MML}
		$title = $value[0];
		if ($title == "reverse"){
			$data = plugin_mml_octave_reverse($data);
			$title = '';
		}
		$data = $value[1];
	}else{
		// #mml(名前,楽器){MML}	//Mabinogi互換処理（もしくはオクターブが逆転）
		$title = $value[0];
		$inst = $value[1];
		$data = $value[$args-1];
		//オクターブ反転
		$data = plugin_mml_mabimml2mml($data);
	}

	// Mabinogi用のMMLが入力されていた場合（mabimml.inc.phpとの互換処理）
	if (ereg('MML@', $data) || ereg('mml@', $data)){
		$data = plugin_mml_mabimml2mml($inst,$data);
	}

	$encorded_data = htmlspecialchars($data, ENT_QUOTES, SOURCE_ENCODING);
	
	$ret = '<pre class="mml-source">'.$encorded_data.'</pre>';
	if($title){
		$html = <<<HTML
<fieldset>
	<legend class="mml-title">$title</legend>
	$ret
</fieldset>
HTML;
	}else{
		$html = $ret;
	}
	
	if ($mml_count == 0){
		global $js_tags, $js_blocks, $css_blocks;

		$jsmml_path = JSMML_PATH;
		$js_tags[] = array('type'=>'text/javascript', 'src'=>$jsmml_path.'/jsmml.js');
		$js_blocks[] = <<<JAVASCRIPT
var MMLplayers;
var MMLPlayer = function(playButton, stopButton, mmlSource, mmlCast, mmlProgress) {
	this.mml = new JSMML();
	this.playButton = playButton;
	this.stopButton = stopButton;
	this.mmlSource  = mmlSource;
	this.mmlCast    = mmlCast;
	this.mmlProgress= mmlProgress;
	this.nowPlaying = false;
	this.nowPausing = false;
	this.instances = this;
	var self = this;
	this.mml.onFinish = function() { self.playFinishHandler.call(self); };
	this.setButton();
	$(window).unload(self.mml.unload);
}

MMLPlayer.prototype = {
	setButton: function() {
		this.thread = '';
		var self = this;
		$(this.mmlProgress).progressbar();
		$(this.playButton).click(function() {
			self.playButtonClickHandler.call(self);
		});
		$(this.stopButton).click(function() {
			self.stopButtonClickHandler.call(self);
		});
	},
	playStart: function() {
		this.nowPlaying = true;
		$(this.playButton).html('<span class="ui-icon ui-icon-pause"></span>');
		$(this.pauseButton).css('display','inline');
		this.mml.play(this.mmlSource);
	},
	playButtonClickHandler: function() {
		if (this.nowPlaying) {
			$(this.playButton).html('<span class="ui-icon ui-icon-play"></span>');
			this.nowPlaying = false;
			this.playFinishHandler();
			this.mml.pause();
			this.nowPausing = true;
		} else {
			this.nowPlaying = true;
			$(this.playButton).html('<span class="ui-icon ui-icon-pause"></span>');
			if (this.nowPausing) {
			    this.nowPausing = false;
			    this.mml.play();
			} else {
			    this.mml.play(this.mmlSource);
			}
			var self = this;
			this.thread = window.setInterval(function(){
				$(self.mmlCast).text('['+self.mml.getNowTimeStr()+' / '+self.mml.getTotalTimeStr()+']');
				if (self.mmlProgress !== 'false'){ $(self.mmlProgress).progressbar('value',self.mml.getNowMSec()/self.mml.getTotalMSec()*100);}
			},200);
		}
	},
	stopButtonClickHandler: function() {
		this.playFinishHandler();
		$(this.mmlProgress).progressbar('value',0);
		this.mml.stop();
	},
	playFinishHandler: function() {
		this.nowPlaying = false;
		window.clearInterval(this.thread);
		$(this.playButton).css('display','inline');
		$(this.pauseButton).css('display','none');
	},
	unload : function(){
		this.mml.destroy();
	}
};

var mml_player_widget = [
	'<div class="mml-player ui-helper-clearfix">',
	'	<ul class="ui-widget">',
	'		<li class="mml-player-play ui-state-default ui-corner-all"><span class="ui-icon ui-icon-play"></span></li>',
	'		<li class="mml-player-stop ui-state-default ui-corner-all"><span class="ui-icon ui-icon-stop"></span></li>',
	'		<li class="mml-progress"></li>',
	'		<li><span class="ui-icon ui-icon-clock" style="float:left;"></span><span class="mml-cast">[00:00 / ??:??]</span></li>',
	'	</ul>',
	'</div>'
].join("\\n");

$(document).ready(function(){
	$('.mml-source').before(mml_player_widget);
	JSMML.onLoad = function() {
		var mml;
		for (var i = 0; i < $('.mml-player').length; i++) {
			var playButton  = $('.mml-player-play')[i];
			var stopButton  = $('.mml-player-stop')[i];
			var mmlSource   = $('.mml-source')[i].childNodes[0].nodeValue;
			var mmlCast     = $('.mml-cast')[i];
			var mmlProgress = $(".mml-progress")[i];
			mml = new MMLPlayer(playButton, stopButton, mmlSource, mmlCast, mmlProgress);
		}
		$('.mml-player li.ui-state-default').hover(
			function() { $(this).addClass('ui-state-hover'); },
			function() { $(this).removeClass('ui-state-hover'); }
		).show('clip');
	};
});

JSMML.swfurl = '$jsmml_path/JSMML.swf';
JSMML.init();
JAVASCRIPT;
		$css_blocks[] = <<< CSS
div.mml-player ul{
	margin:1%;
	padding:0px;
}

div.mml-player ul li{
	display:none;
}

div.mml-player ul li{
	margin: 2px;
	cursor: pointer;
	display:block;
	float: left;
	list-style: none;
	height:1.2em;
}

div.mml-player ul li.ui-state-default{
	width:1.2em;
}

div.mml-player ul li.mml-progress{
	width:15em;
}

CSS;
	}
	$mml_count++;
	return $html;
}
?>