// PukiWiki - Yet another WikiWikiWeb clone.
// Pukiwiki skin script for jQuery
// Copyright (c)2010 PukiWiki Advance Developer Team
//              2010 Logue <http://logue.be/> All rights reserved.

// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 2 of the License, or
// (at your option) any later version.

// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.

// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.

/*jslint evil: true */

var pukiwiki_skin = {
	meta : {
		"@prefix": "<http://purl.org/net/ns/doas#>",
		"@about": "<skin.js>", a: ":JavaScript",
		 title: "Pukiwiki skin script for jQuery",
		 created: "2008-11-25", release: {revision: "2.2.19", created: "2010-08-24"},
		 author: {name: "Logue", homepage: "<http://logue.be/>"},
		 license: "<http://www.gnu.org/licenses/gpl-2.0.html>"
	},
	init : function(){
		var self = this;
		var protocol = (document.location.protocol == 'https:') ? 'https:' : 'http:';
		$('input, button, select, textarea').attr('disabled','disabled');	// フォームをロック

		// metaタグのGenereterから、Plusかそうでないかを判別
		var generetor = $('meta[name=generator]')[0].content;
		if (generetor.match(/PukiPlus/) || generetor.match(/Advance/)){
			this.plus = true;
			this.image_dir = IMAGE_DIR+'ajax/';	// デフォルト
		}else if (generetor.match(/plus/)){
		//	console.info('Pukiwiki Plus! mode');
			this.plus = true;
			this.image_dir = SKIN_DIR+'theme/'+THEME_NAME+'/';
		}else{
		//	console.info('Pukiwiki Standard mode');
			this.plus = false;
			this.image_dir = SKIN_DIR+this.name+'/image/';	// PukiWiki用
		}

		/* 言語設定 */
		$.i18n(LANG);
		
		/* 非同期通信中はUIをブロック */
		this.blockUI();

		/* Suckerfish（ポップアップメニュー） */
		this.suckerfish('ul#nav');
		
		/* ポップアップ目次 */
		this.linkattrs();
		this.preptoc();

		// インラインウィンドウ
		this.setAnchor();

		/* アシスタント */
		if ($("textarea[name='msg']").length !== 0 && $.query.get('cmd') !== 'guiedit'){
			this.set_editform();
		}
		if ($('input[name=msg]').length !== 0){
			this.assistant();
		}
		
		/* Textarea Resizer */
		if ($("textarea[name='msg']").length !== 0){
			$("textarea[name='msg']").addClass("resizable");
			$('textarea.resizable:not(.processed)').TextAreaResizer();
		}

		/* Glossaly（ツールチップ） */
		this.glossaly();

		/* Table Sorter（テーブル自動ソート） */
		this.tablesorter.counter = 0;	// ページングのDOMのIDで使う。非同期通信した結果でもtablesorterを使うのでグローバル関数に・・・
		this.tablesorter();

/*
		if ($('#cancel').length !== 0){
			$('#cancel').click(function(){
				if ($('#msg').val() !== $('#original').val()) return form_changed()?confirm( $.i18n('pukiwiki', 'cancel')):true;
			});
		}
*/

		/* 上級者モード（Plus!専用） */
		this.adv = $.cookie('pwplus');

		if ($('.attach_form').length !== 0){
			this.set_uploader();
		}
		
		$('form').submit(function(){
			// フォーム送信時にフォームをロックする
			$('input','button','select','textarea').attr('disabled','disabled');
			$('body').css('cursor','wait');
		});

		// IE PNG Fix
		if (!$.support.boxModel) {
			loadScript(SKIN_DIR+'js/iepngfix/iepngfix_tilebg.js');
			$('img[src$=png], .pkwk-icon, .pkwk-symbol, .pkwk-icon_linktext').css({
				'behavior': 'url('+SKIN_DIR+'js/iepngfix/iepngfix.htc)'
			});
			
			/* Lazyload（遅延画像ロード） */
			$("#body img[src!=\.png]").lazyload({ 
				placeholder : this.image_dir+'grey.gif',
				effect : "fadeIn"
			});
		}else{
			/* Lazyload（遅延画像ロード） */
			$("#body img").lazyload({ 
				placeholder : this.image_dir+'grey.gif',
				effect : "fadeIn"
			});
		}
		
		/* バナーボックス */
		$("#banner_box img").fadeTo(200,0.3);
		$("#banner_box img").hover(
			function(){
				$(this).fadeTo(200,1);
			},
			function(){
				$(this).fadeTo(200,0.3);
			}
		);
		
		/* SyntaxHighlighter */
		this.sh();
		
		// フォームロックを解除
		$('input, button, select, textarea').removeAttr('disabled');
		
		// ボタンをjQuery UIのものに
		$("button, input[type=submit], input[type=reset], input[type=button]").button();
		$('input, select, textarea').addClass('ui-widget-content');
	},
	custom : {},	// 消さないこと。（スキン用カスタムネームスペース）
	/* ページを閉じたとき */
	unload : function(){
		$('input, button, select, textarea').attr('disabled','disabled');
		// フォームが変更されている場合
		if ($('#msg').length !== 0 && $('#msg').val() !== $('#original').val() && confirm( $.i18n('pukiwiki', 'unload'))) {
			this.appendChild(document.createElement('input')).setAttribute('name', 'write');
			$('<input name="write" />').appendTo(this);
			this.submit();
			alert( $.i18n('pukiwiki', 'submit'));
		}else{
			$('input, button, select, textarea').removeAttr('disabled');
			return false;
		}
	},
	/* ポップアップメニュー */
	suckerfish : function(target){
		if ($(target).length != 0){
			var superfish_cond = {
				autoArrows:		false,	// if true, arrow mark-up generated automatically = cleaner source code at expense of initialisation performance
				dropShadows:	false
			};
			
			if (typeof(pukiwiki_skin.custom.suckerfish) == 'object'){
				superfish_cond = this.custom.suckerfish;
			}
			$(target).superfish(superfish_cond);
		}
	},
/*
ajaxダイアログ
リンクをパースしてダイアログを生成
prefixにはルートとなるDOMを入れる。（<span class="test"></span>の中なら、span.testとなり、そこ以外は処理しない）
*/
	setAnchor : function(prefix){
		var self = this;	// pukiwiki_skinへのエイリアス

		if (prefix){
			prefix = prefix + ' ';
		}else{
			prefix = '';
		}

		// colorboxの設定
		var colorbox_config = {
			opacity			: '0.8',
			slideshowStart	: '<span class="ui-icon ui-icon-play" style="margin:4px;"></span>',
			slideshowStop	: '<span class="ui-icon ui-icon-stop" style="margin:4px;"></span>',
			current			: $.i18n('colorbox','current'),
			previous		: '<span class="ui-icon ui-icon-circle-arrow-e" style="margin:4px;"></span>',
			next			: '<span class="ui-icon ui-icon-circle-arrow-w" style="margin:4px;"></span>',
			close			: '<span class="ui-icon ui-icon-circle-close" style="margin:4px;"></span>',
			onOpen:function(){
				$('object').css('display','none');	// objectは常に最上部に表示されるため
				// jQueryUI Fix
				$('div#cboxOverlay').addClass('ui-widget-overlay');				// オーバーレイをjQueryUIのものに変更
				$('div#colorbox').addClass('ui-widget-content ui-corner-all');		// colorboxのウィンドウをjQueryUIのモノに変更
				$('div#cboxPrevious').button();
				$('div#cboxNext').button();
				$('div#cboxClose').button();
/*
				function autoResizer() {
					(function(doms) {
						for(var i = 0; i < doms.length; i++) {
							var dom_width = $(doms[i]).width();				// domの幅
							var dom_height = $(doms[i]).height();			// domの高さ
							var window_width = $(window).width();			// ウィンドウの幅
							var window_height = $(window).height();			// ウィンドウの高さ
							var rate_width = dom_width / window_width;		// domサイズと画像表示領域のサイズの比率 (幅)
							var rate_height = dom_height / window_height;	// domサイズと画像表示領域のサイズの比率 (高さ)
							
							if (rate_width >= 1 && rate_height >= 1){
								// width、height共に画面に収まらない場合
								if (rate_width > rate_height){
									// 高さを合わせる
									$(doms[i]).css('width',window_width);
									$(doms[i]).css('height',Math.floor(dom_height / rate_width));
								} else {
									// 幅を合わせる
									$(doms[i]).css('width',Math.floor(dom_height / rate_height));
									$(doms[i]).css('height',window_height);
								}
							} else if (rate_width >= 1 && rate_height < 1){
								// 画像のwidthのみ画面に収まらない場合
								$(doms[i]).css('width',window_width);
								$(doms[i]).css('height',Math.floor(dom_height / rate_height));
							} else if (rate_width < 1 && rate_height >= 1){
								// 画像のheightのみ画面に収まらない場合
								$(doms[i]).css('width',Math.floor(dom_width / rate_width));
								$(doms[i]).css('height',window_height);
							}
						}
						
					})($('div#colorbox #cboxLoadedContent'));
				}
				$(window).bind("resize", autoResizer);
*/
			},
			onClosed:function(){
				$('object').css('display','block');
			}
		};

		// ここから、イベント割り当て
		$(prefix+'a[href]').each(function(){
			var href = $(this).attr('href');

			if (href.match(/\.(jpg|jpeg|gif|png|txt)$/i)){	// 拡張子がcolorboxで読み込める形式の場合
				$(this).colorbox(colorbox_config);
			}else if (href.match(/\.(mp3|ogg|mp4)$/i)){	// 拡張子が音楽の場合
				pukiwiki_skin.music_player(this);
			}else if (href.match(/cmd|plugin/i)){	// cmdやpluginの場合
				// Query Stringをパース。paramsに割り当て
				var params = {};
				var hashes = href.slice(href.indexOf('?') + 1).split('&');
				for(var i = 0; i < hashes.length; i++) { 
					var hash = hashes[i].split('='); 
					try{
						params[hash[0]] = decodeURIComponent(hash[1]);
					}catch(e){}
				}

				// pluginをcmdに統一する
				if (params.plugin){
					params.cmd = params.plugin;
					params.plugin = undefined;
				}
				if (params.cmd){
					if (typeof(params.file) !== 'undefined' && params.pcmd == 'open' || typeof(params.openfile) !== 'undefined'){
						var filename;
						if (params.file){
							filename = params.file;
							$(this).attr('href',SCRIPT+'?cmd='+params.cmd+'&pcmd='+params.pcmd+'&refer='+params.refer+'&age='+params.age+'&file='+filename);
						}else{
							filename = params.openfile;
							$(this).attr('href',SCRIPT+'?cmd='+params.cmd+'&refer='+params.refer+'&openfile='+filename);
						}
						
						if (filename.match(/\.(jpg|jpeg|gif|png|txt)$/i)){
							$(this).colorbox(colorbox_config);
						}else if (filename.match(/\.(mp3|ogg|mp4)$/i)){
							self.music_player(this);
						}
					}else if (params.cmd == 'qrcode'){
						$(this).attr('href',href+'&type=.gif');
						$(this).colorbox(colorbox_config);
					}else if (params.cmd.match(/attach|search|backup|source|newpage|template|freeze|rename|logview|tb|diff/) && params.pcmd !== 'list' || params.help == 'true'){
						if (params.help == 'true'){
							params = {cmd:'read',page:'FormatRule'};
						}
						params.ajax = 'json';

						// ダイアログ描画処理
						$(this).click(function(){
							self.ajax_dialog(params,prefix,function(){
								if (params.cmd == 'attach' && (params.pcmd == 'upload' || params.pcmd == 'info') || params.cmd == 'attachref' || params.cmd=='read' || params.cmd=='backup' && params.age !== ''){
									self.set_uploader(prefix+'div.window');
									self.setAnchor(prefix+'div.window');
								}
							});
							return false;
						});
					}
				}
			}
			
			if (href.match('#')){
				// アンカースクロール＆ハイライト
				$(this).click(function(){
					self.anchor_scroll(href,true);
				});
			}
		});
	},
	// ajaxでダイアログ生成。JSON専用！
	// JSONには必ず、bodyとtitleを入れること！（まぁ、parse関数でbodyとtitleが含まれるオブジェクトにすればいいけど）
	// params		QueryStringをオブジェクトにしたもの
	// prefix		親のDOM名
	// callback		開いた時実行する関数。
	// parse		JSONのパース関数
	ajax_dialog : function(params,prefix,callback,parse){
		var self = this;	// pukiwiki_skinへのエイリアス
		// ダイアログ設定
		var dialog_option = {
			modal: true,
			resizable: true,
			show: 'fade',
			hide: 'fade',
			width: '520px',
			dialogClass: 'ajax_dialog',
			open: function(){
				self.tablesorter(prefix+'div.window');
				self.glossaly(prefix+'div.window');

				if(typeof(callback) == 'function'){ callback(); }

				$(prefix+'button, '+prefix+'input[type=submit], '+prefix+'input[type=reset], '+prefix+'input[type=button]').button();
				$(prefix+'input, '+prefix+'select, '+prefix+'textarea').addClass('ui-widget-content');

				// オーバーレイでウィンドウを閉じる
				var parent = this;
				$('.ui-widget-overlay').click(function(){
					$(parent).dialog('close');
				});
				return false;
			},
			close: function(){
				$(this).remove();
			}
		};
		if ($.browser.msie && $.browser.version > 6){ dialog_option.bgiframe = true; }	// for IE6

		if (params.cmd.match(/logview|source|diff|edit|backup|read/i) || params.help == 'true'){
			dialog_option.width = '90%';
			dialog_option.height = $(window).height()*0.8|0;
		}

		// 非同期転送
		// 先にajax処理を行ない、通信完了したらダイアログの描画。
		// 体感速度的に重くなるが、先にダイアログを描画してしまうと、
		// その中に非同期通信の結果が描画されることになり、
		// ダイアログが下に伸びてしまう。
		// 
		$.ajax({
			dataType: 'json',
			url: SCRIPT,
			data : params,
			type : 'GET',
			cache : true,
			timeout : 30000,//タイムアウト（30秒）
			success : function(data){
				var container = $('<div class="window"></div>');
				if(typeof(parse) == 'function'){ data = parse(data); }
				// 通信成功時
				dialog_option.title = data.title;
				data.body=data.body.replace(/<script[^>]*>[^<]+/ig,'');
				container.html(data.body).dialog(dialog_option);
			},
			error : function(data,status,thrown){
				// エラー発生
				var container = $('<div class="window"></div>');
				var content = [
					'<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">',
					'	<p id="ajax_error"><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>'+status+'</p>',
					'</div>'
				].join("\n");
				dialog_option.title = 'Error!';
//				dialog_option.width = '';
//				dialog_option.height = '120px';
				container.html(content).dialog(dialog_option);
				try{
					$('p#ajax_error').after([
						'<ul>',
						'<li>readyState:'+data.readyState+'</li>',
//						'<li>responseText:'+data.responseText+'</li>',
						'<li>status:'+data.status+'</li>',
						'<li>statusText:'+data.statusText+'</li>',
						'</ul>'
					].join("\n"));
				}catch(e){}
			}
		});
	},
	blockUI : function(){
		// jQueryUI BlockUI
		// http://pure-essence.net/2010/01/29/jqueryui-dialog-as-loading-screen-replace-blockui/
		$('<div id="loadingScreen" title="Loading..."></div>').appendTo('body');
		this.loadingScreen = $("div#loadingScreen");
		this.loadingScreen.dialog({
			autoOpen: false,	// set this to false so we can manually open it
			dialogClass: "loadingScreenWindow",
			closeOnEscape: false,
			draggable: false,
			width: 150,
			height: 150,
			modal: true,
			show:'scale',
			hide: 'explode',
			resizable: false,
			dialogClass: 'blockUI',
			open: function() {
				// scrollbar fix for IE
			//	if ($.browser.msie){ $('body').css('overflow','hidden'); }
			},
			close: function() {
				// reset overflow
				$('body').css('overflow','auto');
			}
		}); // end of dialog
		var self = this;
		$(document)
		.ajaxSend(function(e, xhr, settings) {
			self.loadingScreen.dialog("option", "title", 'Loading...');
		//	if(DEBUG && console){ console.info('load: ',settings.url); }
			$('body').css('cursor','wait');
			self.loadingScreen.dialog('open');
		})
		.ajaxStart(function(){
			self.loadingScreen.dialog("option", "title", 'Start');
		})
		.ajaxSuccess(function(){
			self.loadingScreen.dialog("option", "title", 'Success');
		})
		.ajaxComplete(function() {
			self.loadingScreen.dialog("option", "title", 'Complete');
		})
		.ajaxError(function(){
			self.loadingScreen.dialog("option", "title", 'Error');
		})
		.ajaxStop(function(){
			self.loadingScreen.dialog("option", "title", 'Ready');
			self.loadingScreen.dialog('close');
			$('body').css('cursor','auto');
		});
	},
	iframeWin : function(params){
		if (!params.width){ params.width = 800; }
		if (!params.height){ params.height = 800; }

		var horizontalPadding = 20;
		var verticalPadding = 20;
		$('<iframe id="externalSite" class="externalSite"  frameborder="0" src="' + params.url + '" />')
			.dialog({
				title: params.title,
				width: params.width,
				height: params.height,
				autoOpen: true,
				modal: true,
				resizable: true,
				autoResize: true
			})
			.width(params.width - horizontalPadding)
			.height(params.height - verticalPadding);
	},
/*
	setDialog : function(msg,type,callback){
		var container = $('<div class="dialog"></div>');
		$("#dialog-confirm").dialog({
			resizable: false,
			height:140,
			modal: true,
			buttons: {
				'Delete all items': function() {
					$(this).dialog('close');
				},
				Cancel: function() {
					$(this).dialog('close');
				}
			}
		});
	},
*/
	// アンカースクロール＆ハイライト
	anchor_scroll: function(href,highlight){
		if (href.split('#')[1] == ''){
			$.scrollTo('#header');
		}else if (href !== ''){
			var target = '#'+href.split('#')[1];

			$.scrollTo(
				target,{
					duration: 800,
					axis:"y",
					queue:true,
					onAfter:function(){
						// スクロール後ハイライト
						if (highlight === true){ $(target).effect("highlight",{}, 2000); }
					}
				}
			);
		}
	},
	// ミュージックプレイヤー（拡張子が.mp3や.oggなどといったFlashで再生できるものに限る）
	music_player: function(target){
		$(target).each(function(){
			var file = $(this).attr('href');
			var dialog_option = {
				modal: true,
				resizable: false,
				show: 'fade',
				hide: 'fade',
				title: 'Music Player',
				dialogClass: 'music_player',
				width:'640px',
				open: function(){
					var global_lp = 0;
		
					var self = this;
					$('#jplayer').jPlayer({
						swfPath: SKIN_DIR+'js',
						nativeSupport: true,
						customCssIds: true,
						ready: function (){
							this.element.jPlayer("setFile", file);
							showPlayBtn();
							$(self).dialog('option','title', $('#jplayer').jPlayer('getData','diag').src);
							this.element.jPlayer("play");
						}
					})
					.jPlayer('onProgressChange', function(lp,ppr,ppa,pt,tt) {
				 		var lpInt = lp|0;
				 		var ppaInt = ppa|0;
				 		global_lp = lpInt;

						$('#jplayer_loaderBar').progressbar('option', 'value', lpInt);
				 		$('#jplayer_sliderPlayback').slider('option', 'value', ppaInt);
				 		$('#jplayer_PlayTime').text($.jPlayer.convertTime(pt)); // Default format of 'mm:ss'
						$('#jplayer_TotalTime').text($.jPlayer.convertTime(tt)); // Default format of 'mm:ss'

					})
					.jPlayer('onSoundComplete', function() {
						this.element.jPlayer("play");
						showPauseBtn();
					});

					function showPauseBtn(){
						$('#jplayer_play').fadeOut(function(){
							$("#jplayer_pause").fadeIn();
						});
					}

					function showPlayBtn(){
						$('#jplayer_pause').fadeOut(function(){
							$("#jplayer_play").fadeIn();
						});
					}

					$('#jplayer_play').click(function() {
						$("#jplayer").jPlayer("play");
						showPauseBtn();
						return false;
					});

					$('#jplayer_pause').click(function() {
						$("#jplayer").jPlayer("pause");
						showPlayBtn();
						return false;
					});

					$('#jplayer_stop').click(function() {
						$("#jplayer").jPlayer("stop");
						showPlayBtn();
						return false;
					});


					$('#jplayer_volume-min').click( function() {
						$('#jplayer').jPlayer("volume", 0);
						$('#jplayer_sliderVolume').slider('option', 'value', 0);
						return false;
					});

					$('#jplayer_volume-max').click( function() {
						$('#jplayer').jPlayer("volume", 100);
						$('#jplayer_sliderVolume').slider('option', 'value', 100);
						return false;
					});

					// Slider
					$('#jplayer_sliderPlayback').slider({
						max: 100,
						range: 'min',
						animate: true,

						slide: function(event, ui) {
							$("#jplayer").jPlayer("playHead", ui.value*(100.0/global_lp));
						}
					});

					var volume;
					if ($.cookie('volume')){ 
						volume = $.cookie('volume');
					}else{
						volume = 50;
					}
					$('#jplayer_sliderVolume').slider({
						value : volume,
						max: 100,
						range: 'min',
						animate: true,

						slide: function(event, ui) {
							$("#jplayer").jPlayer("volume", ui.value);
							$.cookie('volume', ui.value,{expires:30,path:'/'});
						}
					});

					$('#jplayer_loaderBar').progressbar();

					//hover states on the static widgets
					$('ul#jplayer_icons li').hover(
						function() { $(this).addClass('ui-state-hover'); },
						function() { $(this).removeClass('ui-state-hover'); }
					);
				},
				close: function(){
					$(this).remove();
				}
			};
			
			if ($.browser.msie && $.browser.version > 6){ dialog_option.bgiframe = true; }// for IE6 
			
			var jplayer_container = [
				'<div id="jplayer"></div>',
				'<div id="jplayer_container">',
				'	<ul id="jplayer_icons" class="ui-widget ui-helper-clearfix">',
				'		<li id="jplayer_play" class="ui-state-default ui-corner-all" title="'+$.i18n('player','play')+'"><span class="ui-icon ui-icon-play"></span></li>',
				'		<li id="jplayer_pause" class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-pause" title="'+$.i18n('player','pause')+'"></span></li>',
				'		<li id="jplayer_stop" class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-stop" title="'+$.i18n('player','stop')+'"></span></li>',
				'		<li id="jplayer_volume-min" class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-volume-off" title="'+$.i18n('player','volume_max')+'"></span></li>',
				'		<li id="jplayer_volume-max" class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-volume-on" title="'+$.i18n('player','volume_min')+'"></span></li>',
				'	</ul>',
				'	<div id="jplayer_sliderVolume" title="'+$.i18n('player','volume')+'"></div>',
				'	<div id="jplayer_bars_holder" title="'+$.i18n('player','seek')+'">',
				'		<div id="jplayer_sliderPlayback"></div>',
				'		<div id="jplayer_loaderBar"></div>',
				'	</div>',
				'</div>',
				'<p><span class="ui-icon ui-icon-clock" style="display:block;float:left;"></span><span id="jplayer_PlayTime">00:00</span>/<span id="jplayer_TotalTime">??:??</span></p>'
			].join("\n");
			
			$(this).click(function(){
				var container = $('<div id="music_player"></div>');
				container.html(jplayer_container).dialog(dialog_option);
				return false;
			});
		});
	},
	/* テーブル自動ソート */
	tablesorter:function(prefix){
		var self = this;
		if (prefix){
			prefix = prefix + ' ';
		}else{
			prefix = '';
		}
		$(prefix+'.style_table, '+prefix+'.attach_table').addClass('tablesorter');
		
		/* デフォルト値 */
		var config = {
			sorter: {
				widthFixed: true,
//				debug:DEBUG
			},
			pager : {
				minimum_lines : 10,
				size:[10,20,30,40],
				location_before:true
			}
		};

		if (typeof(this.custom.tablesorter) == 'object'){
			config = this.custom.tablesorter;
		}

		$(prefix+'.tablesorter').each(function(){
			if ( $('tr',this).length > config.pager.minimum_lines){	// 10行以上の場合ページャーを表示
				// テーブルのページングウィジット
				var pager_id = 'table_pager_'+self.tablesorter.counter;
				
				var pager_widget = [
					'<div class="table_pager_widget ui-helper-clearfix" id="'+pager_id+'">',
					'	<ul class="ui-widget">',
					'		<li class="first ui-state-default ui-corner-all"><span class="ui-icon ui-icon-arrowthickstop-1-w"></span></li>',
					'		<li class="prev ui-state-default ui-corner-all"><span class="ui-icon ui-icon-arrowthick-1-w"></span></li>',
					'		<li><input class="pagedisplay" type="text" disabled="disabled" /></li>',
					'		<li class="next ui-state-default ui-corner-all"><span class="ui-icon ui-icon-arrowthick-1-e"></span></li>',
					'		<li class="last ui-state-default ui-corner-all"><span class="ui-icon ui-icon-arrowthickstop-1-e"></span></li>',
					'		<li><select class="pagesize"></select></li>',
		//			'		<li class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-arrowreturn-1-w"></span></li>',
					'	</ul>',
					'</div>'
				].join("\n");
				
				$(this).tablesorter(config.sorter);
				
				if (config.pager.location_before === true){
					$(this).before(pager_widget);
				}else{
					$(this).after(pager_widget);
				}

				var i = 0;
				while (i < config.pager.size.length){
					$('#'+pager_id+' .pagesize').append($('<option>').attr({ value: config.pager.size[i] }).text(config.pager.size[i]));
					i++;
				}

				// ページャーを生成（ID重複しないようにグローバル変数のpukiwiki_skin.tablesorter.counterをカウンタとして使用
				$(this).tablesorterPager({
					container: $('#'+pager_id),
					positionFixed: false
				});

				$('#'+pager_id).show('clip');

				self.tablesorter.counter++;
			}else{
				
				$(this).tablesorter(config.sorter);
			}
		});

		//hover states on the static widgets
		$(prefix+'.table_pager_widget li.ui-state-default').hover(
			function() { $(this).addClass('ui-state-hover'); },
			function() { $(this).removeClass('ui-state-hover'); }
		);
	},
	/* 独自のGlossaly処理 */
	glossaly: function(prefix){
		if (prefix){
			prefix = prefix + ' ';
		}else{
			prefix = '';
		}

		// http://www.javascriptkit.com/script/script2/ajaxtooltip.shtml
		ajaxtooltip.iebody=(document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body;
		var tooltips=[]; //array to contain references to all tooltip DIVs on the page

		/* タイトル属性がある場合 */
		$(prefix+'*[title]').each(function(index){
			this.content=this.getAttribute('title');	// タイトル属性を取得
			this.titleposition=index+' pos';
			tooltips.push($('<div class="ajaxtooltip"></div>').appendTo('body'));
			var $target=$(this);
			$target.removeAttr('title');	// タイトル属性を削除
			if (this.content !== ''){
				$target.hover(
					function(e){ //onMouseover element
						var $tooltip = tooltips[parseInt(this.titleposition)];
						$tooltip.html(this.content).show();
					},
					function(e){ //onMouseout element
						ajaxtooltip.hideall();
					}
				);
				$target.bind("mousemove", function(e){
					var $tooltip=tooltips[parseInt(this.titleposition)];
					ajaxtooltip.positiontip($tooltip, e);
				});
			}
		});
		
		if (this.plus === true){
			// Plusの場合、Glossaly機能をオーバーライド
			$(prefix+'.tooltip').each(function(index){
				this.titleposition=index+' pos';
				var content=$(this).attr('title');
				tooltips.push($('<div class="ajaxtooltip"></div>').appendTo('body'));
				var $target=$(this);
				$target.removeAttr('title');
				$target.hover(
					function(e){ //onMouseover element
						var $tooltip = tooltips[parseInt(this.titleposition)];
						if (content){
							// title属性が含まれている場合、そのまま表示
							$tooltip.html(this.content).show();
						}else{
							// title属性が含まれていない場合ajax通信
							if (!$tooltip.get(0).loadsuccess){ //first time fetching Ajax content for this tooltip?
								$tooltip.html(ajaxtooltip.loadingHTML).show();
								$.ajax({
									url:SCRIPT,
									type:'get',
									cache: true,
									timeout:2000,
									dataType : 'html',
									global:false,
									data : {
										plugin:'tooltip',
										q:$(this).text()
									},
									success: function(data){
										data = data.replace(/<script[^>]*>[^<]+/ig,'');
										$tooltip.html(data);
										ajaxtooltip.positiontip($tooltip, e);
										ajaxtooltip.showtip($tooltip, e);
										$tooltip.get(0).loadsuccess=true;
									},
									error : function(data){
										$tooltip.html(data);
									}
								});
							}else{
								ajaxtooltip.positiontip($tooltip, e);
								ajaxtooltip.showtip($tooltip, e);
							}
						}
					},
					function(e){ //onMouseout element
						ajaxtooltip.hideall();
					}
				);
				$target.bind("mousemove", function(e){
					var $tooltip=tooltips[parseInt(this.titleposition)];
					ajaxtooltip.positiontip($tooltip, e);
				});
			});

			/* 検索画面 */
			if ($.query.get('cmd') == 'search'){
				$(prefix+'a.linktip').each(function(index){
					this.titleposition=index+' pos';
					tooltips.push($('<div class="ajaxtooltip"></div>').appendTo('body'));
					var $target=$(this);
					$target.removeAttr('title');
					$target.removeAttr('onmouseover');
					$target.removeAttr('onmouseout');
					$target.hover(
						function(e){ //onMouseover element
							var $tooltip=tooltips[parseInt(this.titleposition)];
							if (!$tooltip.get(0).loadsuccess){ //first time fetching Ajax content for this tooltip?
								$tooltip.html(ajaxtooltip.loadingHTML).show();
								$.ajax({
									url:SCRIPT,
									type:'get',
									cache: true,
									timeout:2000,
									dataType : 'html',
									global:false,
									data : {
										cmd:'preview',
										page:$(this).text(),
										word: $.query.get('word')
									},
									success: function(data){
										data = data.replace(/<script[^>]*>[^<]+/ig,'');
										$tooltip.html(data);
										ajaxtooltip.positiontip($tooltip, e);
										ajaxtooltip.showtip($tooltip, e);
										$tooltip.get(0).loadsuccess=true;
									},
									error : function(data){
										$tooltip.html(data);
									}
								});
							}else{
								ajaxtooltip.positiontip($tooltip, e);
								ajaxtooltip.showtip($tooltip, e);
							}
						},
						function(e){ //onMouseout element
	//						var $tooltip=tooltips[parseInt(this.titleposition)];
	//						ajaxtooltip.hidetip($tooltip, e);
							ajaxtooltip.hideall();
						}
					);
					$target.bind("mousemove", function(e){
						var $tooltip=tooltips[parseInt(this.titleposition)];
						ajaxtooltip.positiontip($tooltip, e);
					});
				});
			}
		}
		/*
		if ($.support.leadingWhitespace !== true){
			$('div.ajaxtooltip').css({
				'behavior': 'url('+SKIN_DIR+'js/ie-css3.htc)'
			});
		}
		*/
	},
	set_editform: function(){
		$('input','button','select','textarea').attr('disabled','disabled');
		this.ajax_apx = false;
		this.ajax_count = 0;
		this.ajax_tim = 0;
		var self = this;

		//プレビューボタンを書き換え
		$('input[name=write]').after('<input type="button" name="add_ajax" value="'+$('input[name=preview]').attr('value')+'" accesskey="p" />');
		$('input[name=preview]').remove();
		
		$('input[name=add_ajax]').click(function(){
			$('textarea').attr('disabled', 'disabled');
			// フォームの高さを取得
			// Textarea Resizerで高さが可変になっているため。
			var msg_height = $("textarea#msg").height();
			if (self.ajax_apx) {
				self.ajax_apx = false;
				// realview_outerを消したあと、フォームの高さを２倍にする
				// 同時でない理由はFireFoxで表示がバグるため
				$("div#indicator").animate({height:'0px'});
				$("div#realview_outer").animate({
					height:'toggle'
				},function(){
					$("textarea#msg").animate({height:msg_height*2});
					$('div#indicator').remove();
					$('div#realview').remove();
					$('div#realview_outer').remove();
					$('textarea#previous').remove();
					$('textarea').removeAttr('disabled');
				});
			} else {
				if (!self.ajax_apx){
					// Realedit用のDOMを生成
					$("textarea[name='msg']").before([
						'<div id="indicator" style="text-align:right;"></div>',
						'<div id="realview_outer">',
						'	<div id="realview"></div>',
						'</div>'
					].join("\n")).after(
						'<textarea id="previous" style="display:none;"></textarea>'
					);
					
					$('div#indicator').html('<img src="'+self.image_dir+'spinner.gif" alt="Loading..." />Now Loading...');
					$('div#indicator').animate({height:'20px'});
					$('textarea#previous').val($('textarea#msg').val());
					
					// 初回実行時、realview_outerの大きさを、フォームの大きさに揃える。
					// なお、realview_outerの高さは、フォームの半分とする。
					$("div#realview_outer").css("height",msg_height/2);
					$("div#realview_outer").css("width", $("#msg").width());
					$("div#indicator").css("width", $("#msg").width());
				}
				self.ajax_apx = true;
				
				// フォームの高さを半分にしたあと、realview_outerを表示
				$("textarea#msg").animate({
					height:$(this).height()+$("div#realview_outer").height()
				},function(){
					$("div#realview_outer").animate({ height:'toggle'});
					$('textarea').removeAttr('disabled');
				});
				// このときにフォームの大きさを変更すると、戻したときに恐ろしいことに・・・
				self.realtime_preview();
			}
			return false;
		});
		
		$('textarea[name=msg]').blur(function(){
			self.realtime_preview();
		});

		$('textarea[name=msg]').mouseup(function(){
			if ($(this).val() !== $('textarea#previous').val()){
				self.realtime_preview();
			}
		});

		this.assistant(false);
	},
	realtime_preview : function(){
		var oSource = document.getElementById('msg');
		var source = document.getElementById('msg').value;
		var self = this;
		
		if (this.ajax_apx) {
			$('div#indicator').html('<img src="'+self.image_dir+'spinner.gif" alt="Loading..." />Now Loading...');
			$('textarea#previous').val(source);
			$('textarea').attr('disabled', 'disabled');
			
			if (++this.ajax_count !== 1){ return; }
			
			if (document.selection) {
				var sel = document.selection.createRange();
				sellen = sel.text.length;
				var end = oSource.createTextRange();
				var all = end.text.length;
				// http://pukiwiki.cafelounge.net/plus/?BugTrack%2F191
				try{
					end.moveToPoint(sel.offsetLeft,sel.offsetTop);
					end.moveEnd("textedit");
				}catch(e){}
				endlen = end.text.length;
				sttlen = all - endlen;
			} else if (oSource.setSelectionRange) {
				sttlen = oSource.selectionStart;
				endlen = oSource.value.length - oSource.selectionEnd;
				sellen = oSource.selectionEnd-sttlen;
			}
			finlen = source.lastIndexOf("\n",sttlen);
			source = source.substring(0,finlen) + "\n\n" + '&editmark;' + "\n\n" + source.substring(finlen);

			$.ajax({
				url : SCRIPT,
				type : 'post',
				global:false,
				data : {
					cmd : 'edit',
					realview : 1,
					page : PAGE,
					msg : source,
					type : 'json'
				},
				cache : false,
	//			timeout : 2000,//タイムアウト（２秒）
				dataType : 'json',
				success : function(data){
					var innbox = document.getElementById('realview_outer');
	 				var marker = document.getElementById('editmark');

					if (marker){ innbox.scrollTop = marker.offsetTop - 8; }
					
					if (self.ajax_count==1) {
						self.ajax_count = 0;
					} else {
						self.ajax_count = 0;
						self.realtime_preview();
					}
					$("div#indicator").html('Convert time :'+data.taketime);
					var ret = data.data.replace(/<script[^>]*>[^<]+/ig,'<span class="scripttag" title="Script tag">[SCRIPT]</span>');
					$("div#realview").html(ret);
					$('textarea').removeAttr('disabled');
				},
				error : function(data,status,thrown){
					$("div#realview").html([
						'<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">',
						'	<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>'+status+'</p>',
						'	<ul>',
						'		<li>readyState:'+data.readyState+'</li>',
	//					'		<li>responseText:'+data.responseText+'</li>',
						'		<li>status:'+data.status+'</li>',
						'		<li>statusText:'+data.statusText+'</li>',
						'	</ul>',
						'</div>'].join("\n")
					);
				}
			});
		}
	},
	/* swfuploderのフォームに書き換え */
	set_uploader: function(prefix){
		if (prefix){
			prefix = prefix + ' ';
		}else{
			prefix = '';
		}

		var pass_form;
		if ($("input[name=pass]").length !== 0){
			pass_form = '<label for="pass">Password:</label><input type="password" name="pass" id="pass" size="8" value="" /><br />';
		}

		// swfuploadがスクリプトに渡す値
		
		var params = {
			encode_hint		: $(prefix+'input[name=encode_hint]').val(),	// エンコード判別用
			max_file_size	: $(prefix+'input[name=max_file_size]').val(),// 上限容量
			pcmd			: $(prefix+'input[name=pcmd]').val(),		// プラグインコマンド（通常post）
			plugin			: $(prefix+'input[name=plugin]').val(),		// プラグイン（通常attach）
			refer			: $(prefix+'input[name=refer]').val(),		// 添付先のページ
			ajax:			'json'	// 送信完了時にページをjson化したデーターを読み込む。（スキンの処理を確認せよ）
		};
		
		if (params.plugin == 'attachref'){
			params.attachref_no		= $(prefix+'input[name=attachref_no]').val();
			params.attachref_opt	= $(prefix+'input[name=attachref_opt]').val();
			params.digest			= $(prefix+'input[name=digest]').val();
		}
		
		// デフォルト値
		var config = {	
			// swfuploadの位置
			flash_url : SKIN_DIR+'js/swfupload.swf',
			// アップロード先のURL
			upload_url: SCRIPT,
			// POST時に送られるファイルのフォーム名（重要）
			// attachプラグインでは、attach_fileにアップロードするファイル名が格納される。
			// Flashの仕様上、本来は変更せずにFiledataとすることが望ましい。
			file_post_name :'attach_file',
			// POST時に送るパラメータ
			post_params: params,
			// 上限容量
			file_size_limit : params.max_file_size,
			// ファイルタイプ
			file_types : "*.*",
			// ファイルタイプの説明
			file_types_description : "All Files",
			// 一度にアップロードできるファイルの上限（ファイル選択画面でShiftキーで選択できるファイル数）
			file_upload_limit : 10,	// 変更しないこと
			// キューに入れられる上限
			file_queue_limit : 10,
			
			// デバッグ
			debug: DEBUG,

			// 添付ボタン設定
			button_image_url: IMAGE_DIR+'ajax/swfupload/wdp_buttons_upload_114x29.png',
			button_width : 114,
			button_height : 29,
			// 書き換える場所のID
			button_placeholder_id: "swfupload_button",
			button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,

			moving_average_history_size: 40
		};
		
		if (typeof(this.custom.swfupload) == 'object'){
			config = this.custom.swfupload;
		}
		
		// attachrefの場合、１つのみファイルをアップ可能
		if (params.plugin == 'attachref'){
			config.file_upload_limit=1;
			config.file_queue_limit=1;
		}
		
//		console.dir(params);

//		if ($("input[name=pass]").length !== 0){ params.pass = $("input[name=pass]").val(); }	// パスワード
		
		// 添付画面のテンプレート
		$('.attach_form').html( [
			'<div id="swfupload-control">',
			'	<input type="button" id="swfupload_button" />',
			'	<p id="swfupload-queuestatus" ></p>',
			'	<ol id="swfupload-log" class="ui-widget"></ol>',
			'	<button id="execute">Go</button>',
			'</div>'
		].join("\n"));
		
		$('#execute').button({
			icons : { primary: 'ui-icon-check' },
			disabled:true
		}).click(function(){
			$('#swfupload-control').swfupload('startUpload');
			return false;
		}).css({
			display:'none'
		});

		$('#swfupload-control').swfupload(config)	// ファイル添付のフォームをswfupload.swfに置き換え
		.bind('fileQueued', function(event, file){
			var listitem = [
				'<li id="'+file.id+'" class="ui-widget-content ui-corner-all">',
				'	File: <em>'+file.name+'</em> ('+Math.round(file.size/1024)+' KB) <span class="progressvalue" ></span>',
				'	<div class="progressbar"></div>',
				'	<p class="status"><span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-info"></span>Pending</p>',
				'	<button class="cancel">Close</button>',
				'</li>'
			].join("\n");
	
			$('#swfupload-log').append(listitem);
			
			$('li#'+file.id+' button:first').button({
				icons: {
					primary: 'ui-icon-close'
				}
			}).click(function(){
				var swfu = $.swfupload.getInstance('#swfupload-control');
				swfu.cancelUpload(file.id);
				$('#swfupload-log li#'+file.id).slideUp('fast');
				return false;
			});
			
			$('#swfupload-log li#'+file.id+' .progressbar').progressbar();
			
			$('#execute').button({
				disabled:false
			}).css({
				display:''
			});
		})
		.bind('fileQueueError', function(event, file, errorCode, message){
			var listitem = [
				'<li id="error" class="ui-widget-content ui-state-error ui-corner-all"><em>Error</em>',
				'	<p>',
				'		<span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-alert"></span>',
				'		You have attempted to queue too many files.',
				'	</p>',
				'	<button class="close" >Close</button>',
				'</li>'
			].join("\n");

			$('#swfupload-log').append(listitem);
			
			$('li#error button').button({
				icons: {
					primary: 'ui-icon-close'
				}
			}).click(function(){
				$('#swfupload-log li#error').slideUp('fast');
				return false;
			});
		})
		.bind('fileDialogComplete', function(event, numFilesSelected, numFilesQueued){
			$('#swfupload-queuestatus').text('Files Selected: '+numFilesSelected+' / Queued Files: '+numFilesQueued);
		})
		.bind('uploadStart', function(event, file){
			$('#swfupload-log li#'+file.id).find('p.status').text('Uploading...');
			$('#swfupload-log li#'+file.id).find('span.progressvalue').text('0%');
			$('#swfupload-log li#'+file.id).find('span.cancel').hide();
		})
		.bind('uploadProgress', function(event, file, bytesLoaded){
			//Show Progress
			var percentage=Math.round((bytesLoaded/file.size)*100);
			$('#swfupload-log li#'+file.id+' .progressbar').progressbar({ value: percentage });
			$('#swfupload-log li#'+file.id).find('span.progressvalue').text(percentage+'%');
		})
		.bind('uploadSuccess', function(event, file, serverData){
			if (params.plugin != 'attachref'){
				var ret = eval("(" + serverData + ")");
				var item=$('#swfupload-log  li#'+file.id);
				$('#swfupload-log li#'+file.id+' .progressbar').progressbar({ value: 100 });
				item.find('span.progressvalue').text('100%');
				var pathtofile = '<a href="'+SCRIPT+'?plugin=attach&pcmd=open&refer='+PAGE+'&file='+file.name+'" id="view_'+file.id+'"><span style="float: right; margin-right: 0.3em;" class="ui-icon ui-icon-newwin"></span>view &raquo;</a>';
				item.addClass('success').find('p.status').html(ret.title+' | '+pathtofile);
				$('#swfupload-log li#'+file.id+' a#view_'+file.id).click(function(){
					var href = $(this).attr('href');
/*
					if (href.match(/\.(jpg|jpeg|gif|png)$/i)){
						$(this).colorbox();
					}else if (href.match(/\.(mp3|ogg|m4a)$/i)){
						pukiwiki_skin.music_player(this);
					}else{
*/
						window.open(href);
//					}
					$(prefix).dialog('option', 'close', function(){ location.reload(); });
					return false;
				});
			}else{
				location.reload();
			}
		})
		.bind('uploadComplete', function(event, file){
			// upload has completed, try the next one in the queue
			$(this).swfupload('startUpload');
		});
	},
	helper_image: function(){
		// 拡張ボタン
		this.adv = {
			'ncr':		$.i18n('pukiwiki', 'to_ncr'),
			'br':		'&br;'
		};
		// フェイスマーク
		this.face = {
			'smile' :	'(^^)',
			'bigsmile':	'(^-^',
			'huh':		'(^Q^',
			'oh':		'(..;',
			'wink':		'(^_-',
			'sad':		'(--;',
			'worried':	'(^^;',
			'tear':		'(T-T',
			'heart':	'&heart;'
		};

		var command_palette = {
			url		: '0,0,22,16',
			b		: '24,0,40,16',
			i		: '43,0,59,16',
			u		: '62,0,79,16',
			size	: '81,0,103,16'
		};

		var color_palette = {
			Black	: '0,0,8,8',
			Maroon	: '8,0,16,8',
			Green	: '16,0,24,8',
			Olive	: '24,0,32,8',
			Navy	: '32,0,40,8',
			Purple	: '40,0,48,8',
			Teal	: '48,0,55,8',
			Gray	: '56,0,64,8',
			Silver	: '0,8,8,16',
			Red		: '8,8,16,16',
			Lime	: '16,8,24,16',
			Yellow	: '24,8,32,16',
			Blue	: '32,8,40,16',
			Fuchsia	: '40,8,48,16',
			Aqua	: '48,8,56,16',
			White	: '56,8,64,16'
		};

		// ヘルパーの画像表示処理。class属性によるイベント割り当て
		var setIcon = function(icons, classname){
			var icon = '', helper_image_dir, ext;
			if (classname == 'face'){
				helper_image_dir = 'face/';
				ext = 'png';
			}else if (classname == 'adv'){
				helper_image_dir = 'plus/';
				ext = 'gif';
			}
			for (var i in icons) {
				icon += '<img src="'+IMAGE_DIR+helper_image_dir+i+'.'+ext+'" title="'+icons[i]+'" alt="'+i+'" class="helper '+classname+'" /> ';
			}
			return icon + '&nbsp;';
		};

		var setMap = function(maps, name){
			var ret = '<map id="'+name+'" name="'+name+'">'+"\n";
			for (var map_name in maps) {
				ret += '<area shape="rect" coords="'+maps[map_name]+'" title="'+map_name+'" alt="'+map_name+'" class="helper" />';
			}
			ret +='</map>';
			return ret;
		};

		var str = '';
		str += setMap(command_palette,'map_button') + setMap(color_palette,'map_color');
		str += '<img src="'+IMAGE_DIR+'assistant/buttons.gif" width="103" height="16" usemap="#map_button" tabindex="-1" />&nbsp;';
		if ($.cookie("pwplus") == "on"){ str += setIcon(this.adv,'adv'); }
		str += '<img src="'+IMAGE_DIR+'assistant/colors.gif" width="64" height="16" usemap="#map_color" tabindex="-1" />&nbsp;';
		str += setIcon(this.face,'face');
		return str;
	},
	// 入力アシスタント（assistant.js）
	assistant: function(full){
		var self = this;
		// アシスタント
		// pukiwiki_elemは、pukiwiki_skin.elemになりました。
		
		if (!self.elem) self.elem = $('textarea[name=msg]')[0];
		
		$('input[type=text]').focus(function(e){
			self.elem = this;
		});
		$('textarea').focus(function(e){
			self.elem = this;
		});
		if (typeof init_ctrl_unload == 'function'){ init_ctrl_unload(); }
		
		var str = this.helper_image();
		if (full == 'true'){
			str += '<img src="'+IMAGE_DIR+'iconset/default/hint.png" width="18" height="16" title="hint" alt="hint" class="helper showhint" />';	// ヒント
			
			if ($.cookie("pwplus") == "on"){
				str += '<img src="'+IMAGE_DIR+'iconset/default/symbol/easy.png" width="8" height="8" title="easy" alt="easy" class="helper advswitch" />';	// 通常モードへ
			}else{
				str += '<img src="'+IMAGE_DIR+'iconset/default/symbol/adv.png" width="8" height="8" title="adv" alt="adv" class="helper advswitch" />';	// 拡張モードへ
			}
		}

		$('div.assistant').html(str);	// 挿入

		// イベント割り当て
		// ヒント
		if (full == 'true'){
			$('.showhint').click(function(){
				alert($.i18n('pukiwiki', 'hint_text1'));
				if (self.elem !== null){ self.elem.focus(); }
			});
			// 拡張スイッチ
			$('.advswitch').click(function(){
				var pukiwiki_ans;
				if ($.cookie('pwplus') == "on"){
					$.cookie('pwplus','off',{expires:30,path:'/'});
					pukiwiki_ans = confirm($.i18n('pukiwiki', 'to_easy'));
				}else{
					$.cookie('pwplus','on',{expires:30,path:'/'});
					pukiwiki_ans = confirm($.i18n('pukiwiki', 'to_adv'));
				}
				if (pukiwiki_ans){ window.location.reload(); }
			});
		}
		// スマイリー
		$('.face').click(function(){
			$(self.elem).insertAtCaretPos(self.face[this.alt]);
		});

		// 拡張アイコン
		$('.adv').click(function(){
			var str, ret;
			if (this.alt == 'ncr'){
				var str = $(self.elem).getSelection().text;
				if (str == ''){
					alert( $.i18n('pukiwiki', 'select'));
					return;
				}

				for(var n = 0; n < str.length; n++){
					ret += ("&#"+(str.charCodeAt(n))+";");
				}

				$(self.elem).replaceSelection(ret);
			}else{
				$(self.elem).insertAtCaretPos('&'+this.alt+';');
			}
			self.elem.focus();
		});

		$('map area.helper').click(function(){
			var ret;
			var str = $(self.elem).getSelection().text;
			if (str == '' || !self.elem){
				alert( $.i18n('pukiwiki', 'select'));
				return;
			}
			switch ($(this).attr('alt')){
				case 'size' :
					var default_size = "%";
					var v = prompt($.i18n('pukiwiki', 'fontsize'), default_size);
					if (!v || !v.match(/\d+/)){
						return;
					}
					ret = '&size(' + v + '){' + str + '};';
				break;
				case 'b':	//mikoadded
					ret = "''" + str + "''";
				break;
				case 'i':
					ret = "'''" + str + "'''";
				break;
				case 'u':
					ret = '%%%' + str + '%%%';
				break;
				case 's':
					ret = '%%' + str + '%%';
				case 'url':
				//	var regex = "^s?https?://[-_.!~*'()a-zA-Z0-9;/?:@&=+$,%#]+$";
					var my_link = prompt( $.i18n('pukiwiki', 'url'), 'http://');
					if (my_link !== null) {
						ret = '[[' + str + '>' + my_link + ']]';
					}
				break;
				default:	//mikoadded + changed font -> color
					if (str.match(/^&color\([^\)]*\)\{.*\};$/)){
						ret = str.replace(/^(&color\([^\)]*)(\)\{.*\};)$/,"$1," + v + "$2");
					}else{
						ret = '&color(' + v + '){' + str + '};';
					}
				break;
			}

			$(self.elem).replaceSelection(ret);
		});
		
	},
	CFCheck: function(){
		if (typeof(CFInstall) == 'object' && $('meta[http-equiv=X-UA-Compatible]')[0].content.match(/chrome/)){
			CFInstall.check({
				mode: 'overlay',
				onmissing: function(){
					self.iframeWin({
						title:'Google Chrome Frame',
						width: 820,
						height: 600,
						url: 'http://www.google.com/chromeframe'
					});
				}
			});
		}
	},
	sh : function(prefix){
		if (prefix){
			prefix = prefix + ' ';
		}else{
			prefix = '';
		}
		if (typeof(SyntaxHighlighter) == 'object'){
			$(prefix + '.sh').each(function(){
				$(this).removeClass('sh');
				switch($(this).attr('class')){
					case 'AS3':
					case 'Bash':
					case 'Cpp':
					case 'Csharp':
					case 'Css':
					case 'Delphi':
					case 'Diff':
					case 'Erlang':
					case 'Groovy':
					case 'Java':
					case 'JavaFX':
					case 'JScript':
					case 'Perl':
					case 'Php':
					case 'PowerShell':
					case 'Python':
					case 'Ruby':
					case 'Scala':
					case 'Sql':
					case 'Vb':
					case 'Xml':
						$(this).addClass('brush:'+$(this).attr('class'));
					break;
					default:
						$(this).addClass('brush: Plain');
					break;
				};
			});
			SyntaxHighlighter.all();
		}
	},
	/** Collects link types from head element. */
	linkattrs : function(){
		var links = $('link[rel]');
		for(var i=0,n=links.length; i<n; i++){
			var link = links[i];
			switch(link.rel){
				case 'canonical':
					this.canonical = link.href;
				break;
				case 'next':
					this.next = link.href;
					this.next_title = link.title;
					break;
				case 'prev':
				case 'previous':
					this.prev = link.href;
					this.prev_title = link.title;
					break;
				case 'up':
					this.up = link.href;
					this.up_title = link.title;
					break;
				case 'index':
					this.index = link.href;
					this.index_title = link.title;
					break;
				case 'alternate':
					if(link.hreflang == 'en'){ this.hasEversion = true; }
					break;
				case 'transformation':
					this.grddltrans = link.href;
					hasmeta = link.href;
					break;
				case 'home':
					hashome = true;
					this.home = link.href;
					this.home_title = link.title;
					break;
				case 'search':
					hassearch = true;
					this.search = link.href;
					this.search_title = link.title;
					break;
				case 'meta':
					if(!link.type){ hasmeta = 'meta'; }
					break;
				case 'help':
					this.help = link.href;
					this.help_title = link.title;
					break;
			}
		}
	},
	/** preparation of popup TOC on init() */
	preptoc : function(prefix){
		var lis = this.prepHdngs('#body');
		var self = this;
		this.genTocDiv(lis);
		$(document).keypress(function(elem){
			var key = elem.which;	// 押されたキーのコードを取得
			var key_label = String.fromCharCode(key);	// キーのラベル（ESCキーなどは取得できない）

			if(elem.target.nodeName.match(/(input|textarea)/i)){ return true; }	// inputタグ、textareaタグ内ではキーバインド取得を無効化
//			console.log(key,key_label);
			
			switch(key_label){
				case '?':
					// ?キーが押された場合ヘルプページへ移動
					if(location.href.indexOf("Help") == -1 && confirm("Go to help/search page ?")){
						location.href= pukiwiki_skin.help;
					}else{
						alert("This key should bring you our help, i.e. this page :-)");
					}
				break;
				case '<':
					if (self.prev){ location.href = self.prev; }
				break;
				case 'U':
					if (self.up){ location.href = self.up; }
				break;
				case '>':
					if (self.next){ location.href = self.next; }
				break;
				case 'H':
					if (self.home){ location.href = self.home; }
				break;
				case 'I':
					if (self.index){ location.href = self.index; }
				break;
				case 'S':
					if (self.search){ 
						self.ajax_dialog({
							cmd:'search',
							page:PAGE,
							ajax:'json'
						});
					}
				break;
				case 'R':
					if (self.canonical){ location.href = self.canonical; }
				break;
				case 'A':
					if (PAGE){
						self.ajax_dialog({
							cmd:'attach',
							pcmd:'upload',
							page:PAGE,
							ajax:'json'
						},
						'',
						function(){
							self.set_uploader('div.window');
						});
					}
				break;
				case 'E':
					if (PAGE){ location.href = SCRIPT + '?cmd=edit&page='+PAGE; }
				break;
				case 'F':
					if (PAGE){
						self.ajax_dialog({
							cmd:'freeze',
							page:PAGE,
							ajax:'json'
						});
					}
				break;
				case 'C':
					if (PAGE){
						self.ajax_dialog({
							cmd:'source',
							page:PAGE,
							ajax:'json'
						});
					}
				break;
				case 'B':
					if (PAGE){
						self.ajax_dialog({
							cmd:'backup',
							page:PAGE,
							ajax:'json'
						});
					}
				break;
				case 'D':
					if (PAGE){
						self.ajax_dialog({
							cmd:'diff',
							page:PAGE,
							ajax:'json'
						});
					}
				break;
				case 'N':
					if (PAGE){
						self.ajax_dialog({
							cmd:'newpage',
							page:PAGE,
							ajax:'json'
						});
					}
				break;
				default:
					if(self.toc){
						if (self.toc.style.display !== 'none'){
/*
							if(key == 27 || key == 47){
								_hideToc(); //Esc, slash
							}else if(key >= 48 && key <=57){ //0-9
								key -= 48;
								if(self.nkeylink[key]){
								//	location.href = "#" + self.nkeylink[key];
									self.anchor_scroll(self.nkeylink[key],false);
									_hideToc();
								}
							}
*/
						}else{
							if(key == 47) {
								try{
									self.toc.style.top = ((document.body.scrollTop + document.documentElement.scrollTop) || self.pageYOffset) + "px";
								}catch(e){
									self.toc.style.top = 0;
								}
								self.toc.style.left = 0;
								$(self.toc).fadeIn("fast");	// /キー
							}
						}
					}
				break;
			}
			return false;
		});
	},
	prepHdngs : function(prefix){
		if (prefix){
			prefix = prefix + ' ';
		}else{
			prefix = '';
		}
		var lis = '';
		var hd = $(prefix+'h2');
		var tocs = $(prefix+'.contents ul').removeAttr('class').removeAttr('style');
		var ptocImg = '<img src="'+this.image_dir+'toc.png" class="tocpic" title="Table of Contents of this page" alt="" />';
//		var ptocMsg = "Click heading, and Table of Contents will pop up";
		
		if(tocs.length !== 0){
			// #contentsが呼び出されているときは、その内容をTocに入れる。
			lis = '<ol>'+tocs.html()+'</ol>';
			hd.each(function(index){
				$(this).html($(this).html()+ptocImg);
			});
			// 一応h3にもアイコンいれたほうがいいかな。
			$(prefix+'h3').each(function(index){
				$(this).html($(this).html()+ptocImg);
			});
/*
			$(prefix+'h4').click(function(index){
				self.popToc();
			});
*/
		}else if(hd.length !== 0){
			// 通常時の動作。h2タグのみリスティング
			var li = [];
			hd.each(function(index){
				var xid = $(this).attr('id');
				if (!xid){ $(this).attr('id','_genid_'+index); }
				$(this).html($(this).html()+ptocImg);
				li[index] = '<li><a href="#'+xid+'">'+$(this).text()+'</a></li>';
			});
			lis = '<ol>'+li.join("\n")+'</ol>';
		}else if($.query.get('cmd').match(/list|backup/)){
			// おまけ。一覧ではトップのナビを入れる。
			lis = '<div style="text-align:center;">'+$('#top').html()+'</div>';
			$(prefix+'#top').html($(prefix+'#top').html()+ptocImg);
		}
		return lis;
	},
	/** generate the popup TOC division */
	genTocDiv : function(lis){
		this.toc = document.createElement("div");
		this.toc.id = 'poptoc';

		var tochtml = [
			'<h2><a href="#">'+$('h1.title').text()+'</a><span class="c"> [TOC]</span></h2>',
			lis.replace(/href/g,"tabindex='1' href"),
			'<hr />',
			this.getNaviLink() + '</div>'
		].join('');
		document.body.appendChild(this.toc);
		$(this.toc).html(tochtml);
		$(document).click(this.popToc);
		this.calcObj(this.toc,300);
	},
/** determin the size of popup TOC */
	calcObj : function(o, maxw){
		var orgX = self.pageXOffset;
		var orgY = self.pageYOffset;
		o.style.visibility = "hidden";
		o.style.display = "block";
		o.width = o.offsetWidth;
		if(o.width > maxw){
			o.width = maxw;
			if(!jQuery.browser.safari){ o.style.width = maxw + "px"; }	// Safari xhtml+xml
		}
		o.height = o.offsetHeight;
		o.style.display = "none";
		o.style.visibility = "visible";
		if(orgY){ scroll(orgX,orgY); }
	},
/** if the page has prev/next link(s)... */
	getNaviLink : function(){
		var genNavi = function(name,href,title){
			if (href){
				return '<a href="' + href + '" title="' + title + '">'+name+'</a>';
			}else{
				return '<span style="color:gray;">'+name+'</span>';
			}
		};

		var navi = [
 			'&lt;&lt;',
 			genNavi('Prev page', this.prev, this.prev_title),
 			' | ',
 			genNavi('Up',this.up, this.up_title),
 			' | ',
 			genNavi('Next page', this.next, this.next_title),
 			'&gt;&gt;',
 			'<br />',
 			'[ ',
 			genNavi('Home', this.home, this.home_title),
 			' | ',
 			genNavi('Index', this.index, this.index_title),
 			' | ',
 			genNavi('Search', this.search, this.search_title),
 			' | ',
 			genNavi('Help', this.help, this.help_title),
 			' ]'
 		].join('');

		return (navi ?  "<p class='nav'>" + navi + "</p>" : "");
	},
//-- click event handlers ----
/** Show/hide TOC on click event according to its location */
	popToc : function(ev){
		var tg;
		if(window.event){
			ev = event;
			tg = ev.srcElement;
		}else if(ev){
			tg = ev.target;
		}

		if(ev.altKey){
			_dispToc(ev,tg,0);
		}else if(tg.className=='tocpic' || tg.className=='snum'){
			_dispToc(ev,tg,2);
		}else{
			_hideToc();
//			var dv = document.defaultView;
//			var x = tg, mp="";
		}
	},
	// popuptocでクリックした項目をハイライトさせる
	setCurPos : function(tg,type){
		var tid = (type==1) ? tg.getAttribute("id") :
			(tg.parentNode.getAttribute("id") ? tg.parentNode.getAttribute("id") :
				(tg.parentNode.firstChild.getAttribute ? tg.parentNode.firstChild.getAttribute("id") :''));
		return tid;
	},
	nodeText : function(m){
/*
 * Get text from child nodes in DOM.
 * @param	m : target node.
 * @return	text in the node m.
 * @access	public
 */
		if(typeof(m) !== 'object'){
			return m;
		}
		var res;
		for(var i=0,n=m.childNodes;i<n.length;i++){
			if(n.item(i).nodeType == 3){
				res += n.item(i).data;
			}else if(n.item(i).nodeType == 1){
				res += this.nodeText(n.item(i));
			}
		}
		res = res.replace(/^\s*/,"");
		return res.replace(/\s*$/,"");
	}
};
/**************************************************************************************************/
// kanzaki.jsユーティリティ関数
/** keyboard event handler */
/**
 * Get event coordinates relative to current document
 * @access : public
 * @param	e : event
 * @return	the coordinates as Object
 */
var _eventDocPos = function(e){
	var p = {};
	if(jQuery.browser.opera){
		p.x = e.clientX + document.body.scrollLeft;
		p.y = e.clientY + document.body.scrollTop;
	}else if(jQuery.browser.msie){	// if(e.x){
		if (jQuery.browser.msie.version < 8){
			p.x = e.x + document.body.scrollLeft + document.documentElement.scrollLeft;
			p.y = e.y + document.body.scrollTop + document.documentElement.scrollTop;
		}else{
			p.x = e.x;
			p.y = e.y;
		}
	}else{
		p.x = e.pageX;
		p.y = e.pageY;
	}
	return p;
};

/**
 * Get event coordinates relative to the screen
 * @access : public
 * @param	e : event
 * @return	the coordinates as Object
 */
var _eventScrPos = function(e){
	var p = {};
	if($.browser.msie){//if(e.x){
		p.x = e.x;
		p.y = e.y;
		p.w = document.body.clientWidth;
		p.h = document.documentElement.clientHeight ? document.documentElement.clientHeight : document.body.clientHeight;
	}else if($.browser.safari){
		p.x = e.screenX;
		p.y = self.innerHeight - self.screenY - e.screenY;
		p.w = self.innerWidth;
		p.h = self.innerHeight;
	}else{
		p.x = e.clientX;
		p.y = e.clientY;
		p.w = self.innerWidth;
		p.h = self.innerHeight;
	}
	return p;
};

/** display on mouseclick */
var _dispToc = function(ev,tg,type){
	var doc = _eventDocPos(ev);
	var scr = _eventScrPos(ev);
	var h = pukiwiki_skin.toc.height;
	var w = pukiwiki_skin.toc.width;
	if(scr.h < pukiwiki_skin.toc.height){
		pukiwiki_skin.toc.style.height = scr.h + "px";
		pukiwiki_skin.toc.style.overflow = "auto";
		pukiwiki_skin.toc.style.top = (doc.y - scr.y) + "px";
	}else{
//		if(!pukiwiki_skin.isSafari){ pukiwiki_skin.toc.style.height = h + "px"; }	//@@ Safari xhtml+xml
		pukiwiki_skin.toc.style.top = ((scr.h - scr.y > h) ? doc.y + "px" :
			((scr.y > h) ? (doc.y - h) + "px" :
				((scr.y < scr.h/2) ? (doc.y - scr.y) + "px" :
					(doc.y + scr.h - scr.y - h) + "px")));
	}
	pukiwiki_skin.toc.style.left = ((scr.x < scr.w - w) ? doc.x + "px" :
		(doc.x - w) + "px");
	if(type) pukiwiki_skin.setCurPos(tg,type);
	$(pukiwiki_skin.toc).fadeIn("fast");
};

/** close TOC */
function _hideToc(){
	$(pukiwiki_skin.toc).fadeOut("fast");
}
/**************************************************************************************************/
//Ajax Tooltip script: By JavaScript Kit: http://www.javascriptkit.com
//Last update (July 10th, 08): Modified tooltip to follow mouse, added Ajax "loading" message.

var ajaxtooltip = {
	fadeeffect: [true, 300], //enable Fade? [true/false, duration_milliseconds]
	useroffset: [10, 10], //additional x and y offset of tooltip from mouse cursor, respectively
	loadingHTML: '<img src="'+IMAGE_DIR+'ajax/spinner.gif" alt="Loading..." />',

	positiontip:function($tooltip, e){
		var docwidth=(window.innerWidth)? window.innerWidth-15 : ajaxtooltip.iebody.clientWidth-15;
		var docheight=(window.innerHeight)? window.innerHeight-18 : ajaxtooltip.iebody.clientHeight-15;
		var twidth=$tooltip.get(0).offsetWidth;
		var theight=$tooltip.get(0).offsetHeight;
		var tipx=e.pageX+this.useroffset[0];
		var tipy=e.pageY+this.useroffset[1];
		tipx=(e.clientX+twidth>docwidth)? tipx-twidth-(2*this.useroffset[0]) : tipx; //account for right edge
		tipy=(e.clientY+theight>docheight)? tipy-theight-(2*this.useroffset[0]) : tipy; //account for bottom edge
		$tooltip.css({left: tipx, top: tipy});
	},

	showtip:function($tooltip, e){
		if (this.fadeeffect[0]){
			$tooltip.hide().fadeIn(this.fadeeffect[1]);
		}else{
			$tooltip.show();
		}
	},

	hidetip:function($tooltip, e){
		if (this.fadeeffect[0]){
			$tooltip.fadeOut(this.fadeeffect[1]);
		}else{
			$tooltip.hide();
		}
	},
	
	hideall:function(){
		if (this.fadeeffect[0]){
			$('.ajaxtooltip').fadeOut(this.fadeeffect[1]);
		}else{
			$('.ajaxtooltip').hide();
		}
	}
};
/*************************************************************************************************/
// default.jsのオーバーライド
function pukiwiki_area_highlite(id,mode){
	if (mode){
		document.getElementById(id).className = "area_on";
	}else{
		document.getElementById(id).className = "area_off";
	}
}

// Helper function.
function pukiwiki_show_fontset_img(){
	pukiwiki_skin.assistant();
}

// Common Plus function.
function open_uri(href, frame){
	if (!frame){
		return false;
	}
	window.open(href, frame);
	return false;
}
/** PukiWiki Plus! Assistant Scripts **************************************************************/
function pukiwiki_pos(){
	if ($.browser.msie){
		var et = document.activeElement.type;
		if (!(et == "text" || et == "textarea")){
			return;
		}
		
		var r=document.selection.createRange();
		self.elem = document.activeElement;
		if (et == "text"){
			r.moveEnd("textedit");
			pukiwiki_crl =r.text.length;
		}else if (et == "textarea"){
			pukiwiki_rngx=r.offsetLeft;
			pukiwiki_rngy=r.offsetTop;
			pukiwiki_scrx=document.body.scrollLeft;
			pukiwiki_scry=document.body.scrollTop;
		}
	}else{
		return;
	}
}

function loadScript(url,callback){
	var script = document.createElement('script'),done = false;
	
	script.src = url;
	script.type = 'text/javascript';
	script.async = true;
	script.language = 'javascript';
	script.onload = script.onreadystatechange = function(){
		if (!done && (!this.readyState || this.readyState == 'loaded' || this.readyState == 'complete')){
			done = true;

			if (typeof(callback) == 'function'){ callback(); }
			
			// Handle memory leak in IE
			script.onload = script.onreadystatechange = null;
			script.parentNode.removeChild(script);
		}
	};
	
	// sync way of adding script tags to the page
	document.body.appendChild(script);
}

var $buoop = {
	vs:{
		i:8,		// IE
		f:3,		// FF
		o:10.01,	// Opera
		s:5,		// Safari
		n:9
	}
};

/*************************************************************************************************/
// onLoad/onUnload
$(document).ready(function($){
	// フレームハイジャック対策
	if( self !== top ){ top.location = self.location; }

	$.ajax({
		type: "GET",
		global : false,
		url: 'http://browser-update.org/update.js',
		dataType: "script"
	});

	if (typeof(pukiwiki_skin.custom) == 'object'){
		if( typeof(pukiwiki_skin.custom.before_init) == 'function'){
			pukiwiki_skin.custom.before_init();
		}
		pukiwiki_skin.init();
		if (typeof(pukiwiki_skin.custom.init) == 'function'){
			pukiwiki_skin.custom.init();
		}
	}else{
		pukiwiki_skin.init();
	}

	// Google Analyticsを実行
	// http://www.google.com/support/analytics/bin/answer.py?answer=174090
	if (typeof(_gaq) !== 'undefined'){
		$.ajax({
			type: "GET",
			global : false,
			url: ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js',
			dataType: "script"
		});
		/*
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		*/
	}

	tzCalculation_LocalTimeZone(location.host,false);
});

$(window).unload(function(){
	if (typeof(pukiwiki_skin.custom) == 'object'){
		if( typeof(pukiwiki_skin.custom.before_unload) == 'function'){
			pukiwiki_skin.custom.before_unload();
		}
		pukiwiki_skin.unload();
		if (typeof(pukiwiki_skin.custom.unload) == 'function'){
			pukiwiki_skin.custom.unload();
		}
	}else{
		pukiwiki_skin.unload();
	}
});