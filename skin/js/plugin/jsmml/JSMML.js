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

jQuery(document).ready(JSMML.eventInit);
