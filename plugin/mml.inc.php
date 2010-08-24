<?php
/**
 :prefix <http://purl.org/net/ns/doas#> .
 :about "<mml.inc.php>", a: ":PHPScript",
 :shortdesc "JSMML Player for PukiWiki";
 :created "2008-07-28", release: {revision: "1.2.3", created: "2010-08-24"},
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
	$jsmml_path = JSMML_PATH;
	$play_button = PLAY_BUTTON;
	$pause_button = PAUSE_BUTTON;
	$stop_button = STOP_BUTTON;

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
	
	if (exist_plugin('sh')){
		$ret = do_plugin_convert('sh','plain,nolink,class=mml-source,'.$data);
	}else{
		$ret = '<pre class="mml-source">'.htmlspecialchars($data, ENT_QUOTES, SOURCE_ENCODING).'</pre>';
	}
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
		global $head_tags, $foot_tags;

		$head_tags[] = <<< HTML
<style type="text/css">/*<![CDATA[*/
.mml-player button{
	width:20px;
	height:20px;
}
.mml-progress{
	width:200px;
	height:16px;
}
/*]]>*/</style>
<script type="text/javascript" src="$jsmml_path/JSMML.js"></script>
<script type="text/javascript">
// <![CDATA[
var MMLplayers;
var MMLPlayer = function(playButton, stopButton, pauseButton, mmlSource, mmlCast, mmlProgress) {
	this.mml = new JSMML();
	this.playButton = playButton;
	this.pauseButton= pauseButton;
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
		$(this.playButton).button({
			icons: {
				primary: 'ui-icon-play'
			},
			text: false
		}).click(function() {
			self.playButtonClickHandler.call(self);
		});
		$(this.pauseButton).button({
			icons: {
				primary: 'ui-icon-pause'
			},
			text: false
		}).click(function() {
			self.playButtonClickHandler.call(self);
		}).css('display','none');
		$(this.stopButton).button({
			icons: {
				primary: 'ui-icon-stop'
			},
			text: false
		}).click(function() {
			self.stopButtonClickHandler.call(self);
		});
	},
	playStart: function() {
		this.nowPlaying = true;
		$(this.playButton).button({
			icons: {
				primary: 'ui-icon-pause'
			},
			text: false
		});
		this.mml.play(this.mmlSource);
	},
	playButtonClickHandler: function() {
		if (this.nowPlaying) {
			$(this.playButton).css('display','inline');
			$(this.pauseButton).css('display','none');
			this.nowPlaying = false;
			this.playFinishHandler();
			this.mml.pause();
			this.nowPausing = true;
		} else {
			this.nowPlaying = true;
			$(this.playButton).css('display','none');
			$(this.pauseButton).css('display','inline');
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

$(document).ready(function(){
	$('.mml-source').before('<div class="mml-player"></div>');
	$('.mml-player').each(function(index,nodeOj){
		$(this).html([
			'<table border="0" class="mml-toolbar"><tr>',
			'<td>',
			'	<button class="mml-player-play">Play</button>',
			'	<button class="mml-player-pause">Pause</button>',
			'	<button class="mml-player-stop">Stop</button>',
			'</td>',
			'<td><div class="mml-progress"></div></td>',
			'<td><span class="ui-icon ui-icon-clock" style="float:left;"></span><span class="mml-cast">[00:00 / ??:??]</span></td>',
			'</tr></table>'
		].join(''));
	});
	JSMML.onLoad = function() {
		var mml;
		for (var i = 0; i < $('.mml-player').length; i++) {
			var playButton  = $('.mml-player-play')[i];
			var pauseButton = $('.mml-player-pause')[i];
			var stopButton  = $('.mml-player-stop')[i];
			var mmlSource   = $('.mml-source')[i].childNodes[0].nodeValue;
			var mmlCast     = $('.mml-cast')[i];
			var mmlProgress = $(".mml-progress")[i];
			mml = new MMLPlayer(playButton, stopButton, pauseButton, mmlSource, mmlCast, mmlProgress);
		}
	};
});

JSMML.swfurl = '$jsmml_path/JSMML.swf';
JSMML.init();

// ]]></script>
HTML;
	}
	$mml_count++;
	return $html;
}
?>