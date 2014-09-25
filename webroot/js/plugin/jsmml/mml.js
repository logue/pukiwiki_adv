/*
 * JSMML
 * Author: Yuichi Tateno
 * http://rails2u.com/
 *
 * Modified by Logue
 * http://logue.be/
 *
 * The MIT Licence.
 */
var JSMML = (function() {
	return function(swfurl) {
		if (typeof(dojo) == 'object'){
			this.mmlPlayer = dojo.byId(JSMML.mmlID);
		}else{
			this.mmlPlayer = document.getElementById(JSMML.mmlID);
		}
		this.initialize.call(this);
	}
})();

JSMML.meta = {
	"@prefix": "<http://purl.org/net/ns/doas#>",
	"@about": "<JSMML.js>", a: ":JavaScript",
	 title: "FLMML for JavaScript",
	 created: "2007-10-06", release: {revision: "1.2.3", created: "2010-06-15"},
	 contributor: {name: "Yuichi Tateno", homepage: "<http://rails2u.com/>"},
	 author: {name: "Logue", homepage: "<http://logue.be/>"},
	 license: "<http://www.opensource.org/licenses/mit-license.php>",
	 dependencies: 'JSMML.swf, dojo, jQuery, SWFObjects'
};

JSMML.VESION = JSMML.meta.release.revision;
JSMML.setSWFVersion = function(v) { JSMML.SWF_VERSION = v };
JSMML.SWF_VERSION = 'JSMML is not loaded, yet.';
JSMML.toString = function() {
	return 'JSMML VERSION: ' + JSMML.VESION + ', SWF_VERSION: ' + JSMML.SWF_VERSION;
};

JSMML.swfurl = 'JSMML.swf';
JSMML.mmlID = 'jsmml';
JSMML.containerID = JSMML.mmlID+'_container';
JSMML.onLoad = function() {};
JSMML.loaded = false;
JSMML.instances = {};
JSMML.notRunning = 'JSMML is not running!<br />Please execute this in HTTP protocol.'

JSMML.init = function(swfurl) {
	var JSMML_params = {
		swfLiveConnect:true,
		bgcolor:'#FFFFFF',
		quality:'high',
		allowScriptAccess:'always',
		style:'display:inline;'
	};
	var swfname = (swfurl ? swfurl : JSMML.swfurl) + '?' + (new Date()).getTime();
	
	if (! document.getElementById(JSMML.containerID)) {
		$('body').append('<div id="'+JSMML.containerID+'"></div>');
	}

	if (!document.location.protocol.match(/http/i)){
		jQuery('#'+JSMML.containerID).html(JSMML.notRunning);
	}

	// init
	swfobject.embedSWF(
		swfname,
		JSMML.containerID,
		1,
		1,
		'10.0.0',
		'',
		'', 
		JSMML_params,
		{id:JSMML.mmlID}
	);
}

// call from swf
JSMML.initASFinish = function() {
	JSMML.loaded = true;
	JSMML.onLoad();
}

JSMML.eventInit = function() {
	JSMML.init();
}

JSMML.prototype = {
	initialize: function() {
		this.onFinish = function() {};
		this.pauseNow = false;
	},
	uNum: function() {
		if (!this._uNum) {
			this._uNum = this.mmlPlayer._create();
			JSMML.instances[this._uNum] = this;
		}
		return this._uNum;
	},
	play: function(_mml) {
		if (!_mml && this.pauseNow) {
			this.mmlPlayer._play(this.uNum());
		} else {
			if (_mml) this.score = _mml;
			this.mmlPlayer._play(this.uNum(), this.score);
		}
		this.pauseNow = false;
	},
	stop: function() {
		this.mmlPlayer._stop(this.uNum());
	},
	pause: function() {
		this.pauseNow = true;
		this.mmlPlayer._pause(this.uNum());
	},
	destroy: function() {
		this.mmlPlayer._destroy(this.uNum());
		delete JSMML.instances[this.uNum()];
	},

	/* Add */
	isPlaying: function(){
		return this.mmlPlayer._isPlaying(this.uNum());
	},
	isPaused: function(){
		return this.mmlPlayer._isPaused(this.uNum());
	},
	setMasterVolume: function(volume){
		return this.mmlPlayer._setMasterVolume(this.uNum(),volume);
	},
	getWarnings: function(){
		return this.mmlPlayer._getWarnings(this.uNum());
	},
	getTotalMSec: function(){
		return this.mmlPlayer._getTotalMSec(this.uNum());
	},
	getTotalTimeStr: function(){
		return this.mmlPlayer._getTotalTimeStr(this.uNum());
	},
	getNowMSec: function(){
		return this.mmlPlayer._getNowMSec(this.uNum());
	},
	getNowTimeStr: function(){
		return this.mmlPlayer._getNowTimeStr(this.uNum());
	}
};

// JSMML for PukiWiki Adv.
// by Logue

var MMLPlayer = function(playButton, stopButton, mmlSource, mmlCast, mmlProgress) {
	this.mml = new JSMML();
	this.playButton = playButton;
	this.stopButton = stopButton;
	this.mmlSource  = mmlSource;
	this.mmlCast    = mmlCast;
	this.mmlProgress= mmlProgress;
	this.nowPlaying = false;
	this.nowPausing = false;
	var self = this;
	this.mml.onFinish = function() { self.playFinishHandler.call(self); };
	this.setButton();
	$(window).unload(self.mml.unload);
}

MMLPlayer.prototype = {
	setButton: function() {
		this.thread = '';
		var self = this;
		$(this.mmlProgress).progressbar().css({'width':'15em'});
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
				if (self.mmlProgress !== 'false'){
					$(self.mmlProgress).progressbar('value',self.mml.getNowMSec()/self.mml.getTotalMSec()*100);
				}
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
	'<div class="mml-player ui-helper-clearfix noprint">',
	'	<ul class="ui-widget pkwk_widget">',
	'		<li class="mml-player-play ui-button ui-state-default ui-corner-all"><span class="ui-icon ui-icon-play">Play/Pause</span></li>',
	'		<li class="mml-player-stop ui-button ui-state-default ui-corner-all"><span class="ui-icon ui-icon-stop">Stop</span></li>',
	'		<li class="mml-progress"></li>',
	'		<li class="ui-corner-all"><span class="ui-icon ui-icon-clock" style="float:left;">Cast:</span><span class="mml-cast">[00:00 / ??:??]</span></li>',
	'	</ul>',
	'</div>'
].join("\n");

pukiwiki.register.init(function(){
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
	};
});

JSMML.swfurl = JS_URI+'plugin/jsmml/JSMML.swf';
JSMML.init();