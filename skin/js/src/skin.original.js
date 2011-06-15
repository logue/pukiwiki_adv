/*!
 * PukiWiki Advance - Yet another WikiWikiWeb clone.
 * Pukiwiki skin script for jQuery
 * Copyright (c)2010-2011 PukiWiki Advance Developer Team
 *              2010      Logue <http://logue.be/> All rights reserved.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/* jslint evil: false */
/* Implied global: $, document, SCRIPT, LANG, DEBUG, SKIN_DIR, IMAGE_DIR, DEFAULT_LANG, THEME_NAME, PAGE, MODIFIED, GOOGLE_ANALYTICS, FB, FACEBOOK_APPID */

// Cache global function
var _document = document;
var _window = window;
var _ua = navigator.userAgent;

if( self.location !== top.location ){ top.location = self.location; }

// オーバーライド用
var pkwkInit = pkwkBeforeInit = pkwkUnload = pkwkBeforeUnload = new Array(0);

var pukiwiki = {
	meta : {
		'@prefix': '<http://purl.org/net/ns/doas#>',
		'@about': '<skin.js>', a: ':JavaScript',
		 title: 'Pukiwiki skin script for jQuery',
		 created: '2008-11-25', release: {revision: '2.2.21', created: '2011-05-10'},
		 author: {name: 'Logue', homepage: '<http://logue.be/>'},
		 license: '<http://www.gnu.org/licenses/gpl-2.0.html>'
	},
	skin : {
	},	// 消さないこと。（スキン用カスタムネームスペース）
	init : function(){
		var self = this;
		var href = $('link[rel=canonical]')[0].href;
		var protocol = ((_document.location.protocol === 'https:') ? 'https:' : 'http:')+'//';
		this.body = this.skin.body ? this.skin.body : '#body';
		
		$(':input').attr('disabled','disabled');	// フォームをロック
/*
		$(this.body).append('<div id="social"></div>');
		'<iframe frameborder="0" scrolling="no" class="twitter-share-button twitter-count-none" tabindex="0" allowtransparency="true" src="http://platform0.twitter.com/widgets/tweet_button.html?_=1306413923607&amp;count=none&amp;lang=en&amp;text='+urlencode($('title').text())+'&amp;url='+href+'" style="width: 55px; height: 20px;" title="Twitter For Websites: Tweet Button"></iframe>
		'<iframe height="20" frameborder="0" width="50" scrolling="no" class="hatena-bookmark-button-frame" title="このエントリーをはてなブックマークに追加" style="width: 50px; height: 20px;"></iframe>
*/

		// FaceBookを実行
		if (typeof(FACEBOOK_APPID) !== 'undefined'){
			$('body').append('<div id="fb-root"></div>');
			$(this.body).append('<fb:login-button>Login with Facebook</fb:login-button>');
			
			$.getScript(protocol+'connect.facebook.net/'+LANG+'/all.js', function() {
				FB.init({
					appId: FACEBOOK_APPID,
					status	: true, // check login status
					cookie	: true, // enable cookies to allow the server to access the session
					xfbml	: true  // parse XFBML
				});
				FB.Event.subscribe('auth.login', function() {
					_window.location.reload();
				});
				self.fb = true;
			});
		}else{
			this.fb = false;
		}

		if(_ua.indexOf("MSIE 6")>=0){
			// Fix Background flicker
			try{ _document.execCommand('BackgroundImageCache', false, true); }catch(e){}
		}

		// metaタグのGenereterから、Plusかそうでないかを判別
		var generetor = $('meta[name=generator]')[0].content;
		if (generetor.match(/[PukiPlus|Advance]/)){
			this.image_dir = IMAGE_DIR+'ajax/';	// デフォルト
		}else if (generetor.match(/plus/)){
		//	console.info('Pukiwiki Plus! mode');
			this.image_dir = SKIN_DIR+'theme/'+THEME_NAME+'/';
		}else{
		//	console.info('Pukiwiki Standard mode');
			this.image_dir = SKIN_DIR+this.name+'/image/';	// PukiWiki用
		}
		
		// HTML5サポート\
		this.enableHTML5();

		// 言語設定
		$.i18n(LANG);
		
		// 非同期通信中はUIをブロック
		this.blockUI();

		// Suckerfish（ポップアップメニュー
		this.suckerfish('.sf-menu');
		
		// ポップアップ目次
		this.linkattrs();
		this.preptoc(this.body);

		// インラインウィンドウ
		this.setAnchor();

		// アシスタント
		if ($("textarea[name='msg']").length !== 0 && $.query.get('cmd') !== 'guiedit'){
			this.set_editform();
		}
		if ($('input[name=msg]').length !== 0){
			$('.comment_form').append('<div class="assistant ui-corner-all ui-widget-header ui-helper-clearfix"></div>');
			this.assistant();
		}


		if ($.query.get('cmd') === 'list'){
			$('#page_list').jstree({
				plugins:['html_data', 'themeroller' ],
			});
		}


		// Textarea Resizer
		if ($("textarea[name='msg']").length !== 0 && $.query.get('cmd') !== 'guiedit'){
			$("textarea[name='msg']").addClass("resizable");
			$('textarea.resizable:not(.processed)').TextAreaResizer();
		}
		
		// テキストエリアでタブ入力できるように
		$("textarea").tabby();

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

		// 添付フォームをswfuploadに
		if ($('.attach_form').length !== 0){
			this.set_uploader();
		}

		// IE PNG Fix
		if (!$.support.boxModel) {
			$.ajax({
				type: "GET",
				global : false,
				url: SKIN_DIR+'js/iepngfix/iepngfix_tilebg.js',
				dataType: "script"
			});
			$('img[src$=png], .pkwk-icon, .pkwk-symbol, .pkwk-icon_linktext').css({
				'behavior': 'url('+SKIN_DIR+'js/iepngfix/iepngfix.htc)'
			});
			
			/* Lazyload（遅延画像ロード） */
			$(this.body+"img[src!$=.png]").lazyload({ 
				placeholder : this.image_dir+'grey.gif',
				effect : "fadeIn"
			});
		}else{
			/* Lazyload（遅延画像ロード） */
			$(this.body+'img').lazyload({ 
				placeholder : this.image_dir+'grey.gif',
				effect : "fadeIn"
			});
		}
		
		// バナーボックス
		$("#banner_box img").fadeTo(200,0.3);
		$("#banner_box img").hover(
			function(){
				$(this).fadeTo(200,1);
			},
			function(){
				$(this).fadeTo(200,0.3);
			}
		);
		
		// SyntaxHighlighter
		this.sh();
		
		// Glossaly（ツールチップ）
		this.glossaly();
		
		this.bad_behavior();
		
		// アンカーがURLに含まれていた場合（アニメーションする必要はあるのだろうか？）
		if (location.hash){
			this.anchor_scroll(location.hash,true);
		}

		// 二重送信防止
//		$('form').disableOnSubmit();
/*
		if (typeof(this.custom.init) === 'object'){
			var f2;
			while(f2 === this.custom.init.shift() ){
				f2();
			}
		}
*/
		// ボタンをjQuery UIのものに
		$('button, input[type=submit], input[type=reset], input[type=button]').button();
		
		// hover states on the static widgets
		$('.ui-state-default').hover(function() {
			$(this).addClass('ui-state-hover');
		},function() {
			$(this).removeClass('ui-state-hover');
		}).mousedown(function() {
			$(this).addClass('ui-state-active');
		}).mouseout(function() {
			$(this).removeClass('ui-state-active');
		});
		
		// フォームロックを解除
		$(':input').removeAttr('disabled');
		$('.ui-button').button('option', 'disabled', false);
		
		if (typeof(FACEBOOK_APPID) !== 'undefined' && !$.query.get('cmd') && !PAGE.match(/^:|FormatRules|RecentChanges|RecentDeleted|InterWikiName|AutoAliasName|MenuBar|SideBar|Navigation|Glossary/i)){
			$(this.body).append('<hr /><div style="margin-left:2em;"><fb:like send="true" font="arial" show_faces="true" href="'+href+'"></fb:like><fb:comments href="'+href+'" publish_feed="true" width="650" numposts="10" migrated="1" ></fb:comments></div>');
		}
	},
	// ページを閉じたとき
	unload : function(prefix){
//		this.loadingScreen.dialog('open');
		if (prefix){
			prefix = prefix + ' ';
		}else{
			prefix = '';
		}
		/*
		if (typeof(this.custom.before_unload) === 'object'){
			var f;
			while(f === this.custom.before_unload.shift() ){
				try{
					f();
				}catch(e){}
			}
		}
		*/

		// フォームが変更されている場合
		if ($(prefix+'#msg').val() !== $(prefix+'#original').val() && confirm( $.i18n('pukiwiki', 'unload'))) {
			this.appendChild(_document.createElement('input')).setAttribute('name', 'write');
			$('<input name="write" />').appendTo(this);
			$(prefix+'form').submit();
			alert( $.i18n('pukiwiki', 'submit'));
		}else{
//			this.loadingScreen.dialog('close');
		}
		return false;
	},
	// HTML5の各種機能をJavaScriptで有効化するための処理
	enableHTML5 : function(prefix){
		if (prefix){
			prefix = prefix + ' ';
		}else{
			prefix = '';
		}
		
		// Placeholder属性のサポート
		if (!Modernizr.input.placeholder){
			$(prefix+'[placeholder]').each(function () {
				var input = $(this),
				placeholderText = input.attr('placeholder'),
				placeholderColor = 'Gray',
				defaultColor = input.css('color');

				input. 
					focus(function () { 
						if (input.val() === placeholderText) { 
							input.val('').css('color', defaultColor); 
						}
					}).
					blur(function () {
						if (input.val() === '') {
							input.val(placeholderText).css('color', placeholderColor);
						} else if (input.val() === placeholderText) {
							input.css('color', placeholderColor);
						}
					}).
					blur().
					parents('form').
					submit(function () {
						if (input.val() === placeholderText) {
							input.val('');
						}
					});
			});
		}

		// rel="noreferer"のサポート
		// WebKitのNightly版でサポートされているが、JavaScriptで実装されているのかを確認する方法がわからないため常に実行
		$(prefix+'a[rel*=noreferer]').click(function () {
			// http://logic.moo.jp/memo.php/archive/569
			var url = $(this).attr('href');
			
			// for IE
			if (_ua.indexOf('MSIE',0) !== -1){
				var blank_flag = 0;
				var d;
				if ( $(this).attr('target') === '_blank'){
					var subwin = _window.open('','','location=yes, menubar=yes, toolbar=yes, status=yes, resizable=yes, scrollbars=yes,');
					d = subwin.document;
				}else{
					d = _document;
				}
				d.open();
				d.write('<meta http-equiv="refresh" content="0;url='+url+'">');
				d.close();
				return false;
			}
			// for Safari,Chrome,Firefox
			else{
				if ( url.match(/data:text\/html;charset=utf-8/) !== true ){
					var html = [
						'<html><head><script type="text/javascript"><!--',
						'document.write(\'<meta http-equiv="refresh" content="0;url='+url+'" />\');',
						'// --><'+'/script></head><body></body></html>'
					];
					$(this).attr('href', 'data:text/html;charset=utf-8,'+encodeURIComponent(html.join("\n")));
				}
			}
		});
	},
	// オーバーライド関数
	register:{
		init:function(func){
			pkwkInit.push( func );
		},
		before_init:function(func){
			pkwkBeforeInit.push( func );
		},
		unload:function(func){
			pkwkUnload.push( func );
		},
		before_unload:function(func){
			pkwkBeforeUnload.push( func );
		}
	},
	// ポップアップメニュー
	suckerfish : function(target){
		var superfish_cond = {
			autoArrows:		false,	// if true, arrow mark-up generated automatically = cleaner source code at expense of initialisation performance
			dropShadows:	false
		};
		
		if (typeof(pukiwiki.skin.suckerfish) === 'object'){
			superfish_cond = this.skin.suckerfish;
		}
		$(target).superfish(superfish_cond);
	},
/*
ajaxダイアログ
リンクをパースしてダイアログを生成
prefixにはルートとなるDOMを入れる。（<span class="test"></span>の中なら、span.testとなり、そこ以外は処理しない）
*/
	setAnchor : function(prefix){
		var self = this;	// pukiwikiへのエイリアス

		if (prefix){
			prefix = prefix + ' ';
		}else{
			prefix = '';
		}
		
		$(prefix+'.tabs').tabs();

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
				// jQueryUI Fix
				$(prefix+'#cboxOverlay').addClass('ui-widget-overlay');				// オーバーレイをjQueryUIのものに変更
				$(prefix+'#colorbox').addClass('ui-widget-content ui-corner-all');		// colorboxのウィンドウをjQueryUIのモノに変更
				$(prefix+'#cboxPrevious').button();
				$(prefix+'#cboxNext').button();
				$(prefix+'#cboxClose').button();
			}
		};
/*
function autoResizer() {
	(function(doms) {
		for(var i = 0; i < doms.length; i++) {
			var dom_width = $(doms[i]).width();				// domの幅
			var dom_height = $(doms[i]).height();			// domの高さ
			var window_width = $(_window).width();			// ウィンドウの幅
			var window_height = $(_window).height();			// ウィンドウの高さ
			var rate_width = dom_width / window_width;		// domサイズと画像表示領域のサイズの比率 (幅)
			var rate_height = dom_height / window_height;	// domサイズと画像表示領域のサイズの比率 (高さ)
			var resized_width = 
			
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
			} else if (rate_width >= 1 && rate_height =< 1){
				// 画像のwidthのみ画面に収まらない場合
				$(doms[i]).css('width',window_width);
				$(doms[i]).css('height',Math.floor(dom_height / rate_height));
			} else if (rate_width =< 1 && rate_height >= 1){
				// 画像のheightのみ画面に収まらない場合
				$(doms[i]).css('width',Math.floor(dom_width / rate_width));
				$(doms[i]).css('height',window_height);
			}
		}
		
	})($(prefix+'#colorbox #cboxLoadedContent'));
}
$(_window).bind("resize", autoResizer);
*/
		// ここから、イベント割り当て
		// ここから、イベント割り当て
		$(prefix+'a[href]').each(function(){
			var $this = $(this);
			var href = $(this).attr('href');

			if (href.match(/\.(jpg|jpeg|gif|png|txt)$/i)){	// 拡張子がcolorboxで読み込める形式の場合
				$this.colorbox(colorbox_config);
				// $this.rlightbox();
			}else if (href.match(/\.(mp3|ogg|mp4)$/i)){	// 拡張子が音楽の場合
				self.music_player(this);
			}else if (href.match(/cmd|plugin/i)){	// cmdやpluginの場合
				// Query Stringをパース。paramsに割り当て
				var params = {};
				var hashes = href.slice(href.indexOf('?') + 1).split('&');
				for(var i = 0; i < hashes.length; i++) { 
					var hash = hashes[i].split('='); 
					try{
						params[hash[0]] = decodeURIComponent(hash[1]).replace(/\+/g, ' ').replace(/%2F/g, '/');
					}catch(e){}
				}

				// pluginをcmdに統一する
				if (params.plugin){
					params.cmd = params.plugin;
					params.plugin = undefined;
				}

				if (params.cmd){
					if (typeof(params.file) !== 'undefined' && params.pcmd == 'open' || typeof(params.openfile) !== 'undefined'){
						// 添付ファイルを開く（refによる呼び出しの場合とattachによる呼び出しの場合とでQueryStringが異なるのがやっかいだ）
						var filename;
						if (params.file){
							filename = params.file;
							$this.attr('href',SCRIPT+'?cmd='+params.cmd+'&pcmd='+params.pcmd+'&refer='+params.refer+'&age='+params.age+'&file='+filename);
						}else{
							filename = params.openfile;
							$this.attr('href',SCRIPT+'?cmd='+params.cmd+'&refer='+params.refer+'&openfile='+filename);
						}
						
						if (filename.match(/\.(jpg|jpeg|gif|png|txt)$/i)){
							$this.colorbox(colorbox_config);
						//	$this.rlightbox();
						}else if (filename.match(/\.(mp3|ogg|mp4)$/i)){
							self.music_player(this);
						}
					}else if (params.cmd == 'qrcode'){
						// QRcodeの場合（たぶん使われない）
						$this.attr('href',href+'&type=.gif');
						$this.colorbox(colorbox_config);
						//$(this).rlightbox();
					}else if (params.cmd.match(/attach|search|backup|source|newpage|template|freeze|rename|logview|tb|diff|referer|linklist|skeylist/i) && (params.pcmd !== 'list' || params.help === 'true')){
						// その他の主要なプラグインは、インラインウィンドウで表示
						if (params.help == 'true'){
							params = {cmd:'read', page:'FormatRule'};
						}

						// ダイアログ描画処理
						$this.click(function(){
							self.ajax_dialog(params,prefix,function(){
								if ((params.cmd == 'attach' && params.pcmd.match(/upload|info/i)) || params.cmd.match(/attachref|read|backup/i) && params.age !== ''){
									self.bad_behavior(prefix+'div.window');
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
		var self = this;	// pukiwikiへのエイリアス
		// ダイアログ設定
		var dialog_option = {
			modal: true,
			resizable: true,
			show: 'fade',
			hide: 'fade',
			width: '520px',
			dialogClass: 'ajax_dialog',
			bgiframe : ($.browser.msie && $.browser.version > 6) ? true : false,	// for IE6 
			open: function(){
				$(prefix+':input').attr('disabled','disabled');
				self.tablesorter(prefix+'.window');
				self.glossaly(prefix+'.window');

				if(typeof(callback) === 'function'){ callback(); }

				$(prefix+'button, '+prefix+'input[type=submit], '+prefix+'input[type=reset], '+prefix+'input[type=button]').button();

				$(prefix+'form').disableOnSubmit();	// 二重送信防止
				// オーバーレイでウィンドウを閉じる
				var parent = this;
				$(prefix+'.ui-widget-overlay').click(function(){
					$(parent).dialog('close');
				});
				$(prefix+':input').removeAttr('disabled');
				$(prefix+'.ui-button').button('option', 'disabled', false);
				return false;
			},
			close: function(){
				$(this).remove();
			}
		};
		
		if (params.pcmd === 'upload'){
			dialog_option.width = '90%';
		}

		if (params.cmd.match(/logview|source|diff|edit|backup|read|referer/i) || params.help === 'true'){
			dialog_option.width = '90%';
			dialog_option.height = $(_window).height()*0.8|0;
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
				if(typeof(parse) === 'function'){ data = parse(data); }
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
						'<p id="ajax_error"><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>'+status+'</p>',
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
		this.loadingScreen = $("#loadingScreen");
		this.loadingScreen.dialog({
			autoOpen: false,	// set this to false so we can manually open it
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
				$('body').css('cursor','wait');
				ScrollLock.lock();
			},
			close: function() {
				// reset overflow
				// $('body').css('overflow','auto');
				$('body').css('cursor','auto');
				ScrollLock.unlock();
			}
		}); // end of dialog
		var self = this;
		$(_document)
		.ajaxSend(function(e, xhr, settings) {
			self.loadingScreen.dialog("option", "title", 'Loading...');
		//	if(DEBUG && console){ console.info('load: ',settings.url); }
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
		if (href.split('#')[1] === ''){
			$.scrollTo('#header');
		}else if (href !== ''){
			var target = href.split('#')[1];
			
			if (!target.match(/tab/)){
				$.scrollTo(
					'#'+target,{
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
				bgiframe : ($.browser.msie && $.browser.version > 6) ? true : false,	// for IE6 
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
			
			var jplayer_container = [
				'<div id="jplayer"></div>',
				'<div id="jplayer_container">',
					'<ul id="jplayer_icons" class="ui-widget ui-helper-clearfix">',
						'<li id="jplayer_play" class="ui-button ui-state-default ui-corner-all" title="'+$.i18n('player','play')+'"><span class="ui-icon ui-icon-play"></span></li>',
						'<li id="jplayer_pause" class="ui-button ui-state-default ui-corner-all"><span class="ui-icon ui-icon-pause" title="'+$.i18n('player','pause')+'"></span></li>',
						'<li id="jplayer_stop" class="ui-button ui-state-default ui-corner-all"><span class="ui-icon ui-icon-stop" title="'+$.i18n('player','stop')+'"></span></li>',
						'<li id="jplayer_volume-min" class="ui-button ui-state-default ui-corner-all"><span class="ui-icon ui-icon-volume-off" title="'+$.i18n('player','volume_max')+'"></span></li>',
						'<li id="jplayer_volume-max" class="ui-button ui-state-default ui-corner-all"><span class="ui-icon ui-icon-volume-on" title="'+$.i18n('player','volume_min')+'"></span></li>',
					'</ul>',
					'<div id="jplayer_sliderVolume" title="'+$.i18n('player','volume')+'"></div>',
					'<div id="jplayer_bars_holder" title="'+$.i18n('player','seek')+'">',
						'<div id="jplayer_sliderPlayback"></div>',
						'<div id="jplayer_loaderBar"></div>',
					'</div>',
				'</div>',
				'<p><span class="ui-icon ui-icon-clock" style="display:block;float:left;">Cast:</span><span id="jplayer_PlayTime">00:00</span>/<span id="jplayer_TotalTime">??:??</span></p>'
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
				widthFixed: false
//				debug:DEBUG
			},
			pager : {
				minimum_lines : 10,
				size:[10,25,50,75,100],
				location_before:true,
				positionFixed: false
			}
		};

		if (typeof(this.skin.tablesorter) === 'object'){
			config = this.skin.tablesorter;
		}

		$(prefix+'.tablesorter').each(function(elem){
			var table = this;
			var backup = $(this).clone();
			
			if ( $('tr',this).length > config.pager.minimum_lines && $('thead',this).length !== 0){	// 10行以上の場合ページャーを表示
				// テーブルのページングウィジット
				var pager_id = 'table_pager_'+self.tablesorter.counter;
				
				var pager_widget = [
					'<div class="table_pager_widget ui-helper-clearfix" id="'+pager_id+'">',
						'<ul class="ui-widget pkwk_widget">',
							'<li class="first ui-button ui-state-default ui-corner-all"><span class="ui-icon ui-icon-arrowthickstop-1-w"></span></li>',
							'<li class="prev ui-button ui-state-default ui-corner-all"><span class="ui-icon ui-icon-arrowthick-1-w"></span></li>',
							'<li><input class="pagedisplay" type="text" disabled="disabled" /></li>',
							'<li class="next ui-button ui-state-default ui-corner-all"><span class="ui-icon ui-icon-arrowthick-1-e"></span></li>',
							'<li class="last ui-button ui-state-default ui-corner-all"><span class="ui-icon ui-icon-arrowthickstop-1-e"></span></li>',
							'<li><select class="pagesize"></select></li>',
							'<li class="reload ui-button ui-state-default ui-corner-all"><span class="ui-icon ui-icon-refresh"></span></li>',
						'</ul>',
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
				// リセットボタン
				$('#'+pager_id+' .reload').click(function(){
					backup.clone().insertAfter(table);
					$(table).remove();
					$('#'+pager_id).remove();
					$(table).tablesorter(config.sorter);
					if (config.pager.location_before === true){
						$(table).before(pager_widget);
					}else{
						$(table).after(pager_widget);
					}
					$(table).tablesorterPager(config.pager);
					
					var i = 0;
					while (i < config.pager.size.length){
						$('#'+pager_id+' .pagesize').append($('<option>').attr({ value: config.pager.size[i] }).text(config.pager.size[i]));
						i++;
					}
					
					//Load Tool Tips for links inside the tab container
					self.glossaly(table);
				});

				// ページャーを生成（ID重複しないようにグローバル変数のpukiwiki.tablesorter.counterをカウンタとして使用
				$(this).tablesorterPager({
					container: $('#'+pager_id),
					positionFixed: false
				});

				//hover states on the static widgets
				$('#'+pager_id+'.pkwk_widget .ui-state-default').hover(function() {
					$(this).addClass('ui-state-hover');
				},function() {
					$(this).removeClass('ui-state-hover');
				}).mousedown(function() {
					$(this).addClass('ui-state-active');
				}).mouseout(function() {
					$(this).removeClass('ui-state-active');
				});
				
				$('#'+pager_id).show('clip');
				self.tablesorter.counter++;
			}else{
				$(this).tablesorter(config.sorter);
			}
			
		});

		
	},
	/* 独自のGlossaly処理 */
	glossaly: function(prefix){
		var self = this;
		if (prefix){
			prefix = prefix + ' ';
		}else{
			prefix = '';
		}

		/* タイトル属性がある場合 */
		$(prefix+'*[title]').tooltip({
			bodyHandler: function() { 
				return $(this).context.tooltipText; 
			},
			track: true,
			delay: 0,
			showURL: false
		}).hover(
			function(){
				$('body').css('cursor','help');
			},function(){
				$('body').css('cursor','auto');
			}
		);
		if (this.plus === true){
			// Plusの場合、Glossaly機能をオーバーライド
			$(prefix+'.tooltip').hover(
				function(){
					$('body').css('cursor','wait');
					self.getGlossary(this,{
						cmd:'tooltip',
						q:$(this).text(),
						cache:true
					});
				},
				function(){
					$('body').css('cursor','auto');
				}
			);

			/* 検索画面 */
			if ($.query.get('cmd') === 'search'){
				$(prefix+'.linktip').hover(
					function(){
						$('body').css('cursor','wait');
						self.getGlossary(this,{
							cmd:'preview',
							page:$(this).text(),
							word: $.query.get('word'),
							cache:true
						});
					},
					function(){
						$('body').css('cursor','auto');
					}
				);
			}
		}
		
		if ($.support.leadingWhitespace !== true){
			$(prefix+'.tooltip').css({
				'behavior': 'url('+SKIN_DIR+'js/ie-css3.htc)'
			});
		}
		
	},
	// jquery.tooltip.jsのajax化
	getGlossary: function(target,params,tooltip_opts){
		var text;
		$.ajax({
			url:SCRIPT,
			type:'GET',
			cache: true,
			timeout:2000,
			dataType : 'html',
			global:false,
			data : params,
			async:false,
			beforeSend: function(){
				$('body').css('cursor','wait');
				
			},
			success: function(data){
				text = data;
			},
			complete : function(XMLHttpRequest, textStatus){
				$('body').css('cursor','help');
				$(target).tooltip({
					bodyHandler: function() { 
						return text.replace(/<script[^>]*>[^<]+/ig,'');	// <script>タグを無効化
					}, 
					track: true,
					delay: 0,
					showURL: false
				});
			}
		});
	},
	set_editform: function(prefix){
		if (prefix){
			prefix = prefix + ' ';
		}else{
			prefix = '';
		}
		var localstorage = false;
		// テキストエリアの内容をlocalStrageから取得
		if (Modernizr.localstorage){
			var msg = $(prefix+'.edit_form textarea[name=msg]').val();
			var storage = _window.localStorage.getItem(PAGE);
			var data = JSON.parse(storage);

			if (data){
				// 「タイムスタンプを更新しない」で更新した場合、それを検知する方法がないという致命的問題あり。
				var ask = (MODIFIED > data.modified && data.msg !== msg) ? 
					'過去に編集したデーターよりもページが新しいようです。復元しますか？' : 
					'過去に編集したデーターがあるようです。復元しますか？';

				// データーを復元
				if (confirm(ask)){ $(prefix+'.edit_form textarea[name=msg]').val(data.msg); }
			}
			
			localstorage = true;
		}

		$('.edit_form input','.edit_form button','.edit_form select','.edit_form textarea').attr('disabled','disabled');
		
		this.ajax_apx = false;
		this.ajax_count = 0;
		this.ajax_tim = 0;
		var self = this;

		// プレビューボタンを書き換え
		$(prefix+'.edit_form input[name=write]').after('<input type="button" name="add_ajax" value="'+$('.edit_form input[name=preview]').attr('value')+'" accesskey="p" />');
		$(prefix+'.edit_form input[name=preview]').remove();
		
		$(prefix+'.edit_form').prepend('<div class="assistant ui-corner-top ui-widget-header ui-helper-clearfix"></div>');
		
		// プレビューボタンが押された時の処理
		$(prefix+'.edit_form input[name=add_ajax]').click(function(){
			$('textarea').attr('disabled', 'disabled');
			// フォームの高さを取得
			// Textarea Resizerで高さが可変になっているため。
			var msg_height = $(".edit_form textarea[name='msg']").height();
			if (self.ajax_apx) {
				self.ajax_apx = false;
				// realview_outerを消したあと、フォームの高さを２倍にする
				// 同時でない理由はFireFoxで表示がバグるため
				$(prefix+".edit_form #realview_outer").animate({
					height:'toggle'
				},function(){
					$(prefix+".edit_form textarea[name='msg']").animate({height:msg_height*2});
					$(prefix+'.edit_form #realview').remove();
					$(prefix+'.edit_form #realview_outer').remove();
					$(prefix+'.edit_form #previous').remove();
					$(prefix+'.edit_form textarea').removeAttr('disabled');
					$(prefix+'.edit_form #indicator').hide().fadeOut();
				});
			} else {
				if (!self.ajax_apx){
					// Realedit用のDOMを生成
					$(prefix+".edit_form textarea[name='msg']").before([
						'<div id="realview_outer">',
							'<div id="realview"></div>',
						'</div>'
					].join("\n")).after(
						'<textarea id="previous" style="display:none;"></textarea>'
					);
					$(prefix+'.edit_form #indicator').show().fadeIn();
					$(prefix+'.edit_form #indicator').html('<img src="'+self.image_dir+'spinner.gif" alt="Loading..." />Now Loading...');
					$(prefix+'.edit_form #previous').val($('textarea#msg').val());
					
					// 初回実行時、realview_outerの大きさを、フォームの大きさに揃える。
					// なお、realview_outerの高さは、フォームの半分とする。
					$(prefix+".edit_form #realview_outer").css("height",msg_height/2);
					$(prefix+'.edit_form #indicator').html('');
				}
				self.ajax_apx = true;
				
				// フォームの高さを半分にしたあと、realview_outerを表示
				$(prefix+".edit_form textarea[name='msg']").animate({
					height:$(this).height()+$(".edit_form #realview_outer").height()
				},function(){
					$(prefix+".edit_form #realview_outer").animate({ height:'toggle'});
					$(prefix+'.edit_form textarea').removeAttr('disabled');
				});
				// このときにフォームの大きさを変更すると、戻したときに恐ろしいことに・・・
				self.realtime_preview();
			}
			return false;
		});
		
		$(prefix+'.edit_form textarea[name=msg]').blur(function(){
			self.realtime_preview();
		});

		$(prefix+'.edit_form textarea[name=msg]').mouseup(function(){
			if ($(this).val() !== $('textarea#previous').val()){
				self.realtime_preview();
			}
		});
		
		$(prefix+'.edit_form textarea[name=msg]').keypress(function(elem){
			// テキストエリアの内容をlocalStrageにページ名と共に保存
			if (Modernizr.localstorage){
				var d=new Date();
				_window.localStorage.setItem(PAGE,JSON.stringify({
					msg : $(this).val(),
					modified: d.getTime()
				}));
			}
		});
		// 送信イベント時の処理
		$(prefix+'form').submit(function(e){
/*
			
			var postdata = $(this).serializeObject();	// フォームの内容をサニタイズ
			postdata.ajax = 'json';
*/
			// ローカルストレージをフラッシュ
			if (Modernizr.localstorage){
				localStorage.removeItem(PAGE);
			}

			if ($('#_edit_form_notimestamp:checked') !== true && $('#fb_publish:checked') === true){
				$.ajax({
					url:'https://graph.facebook.com/bbeckford/feed',
					type:'post',
					data: {
						method: 'stream.publish',
						message: PAGE + "\n" + $('link[rel=canonical]')[0].href
					},
					cache: false,
					dataType : 'json',
					success : function(data){
						
						
						console.log(data.body);
					},
					error : function(data){
						$(prefix+'input, button, select, textarea').removeAttr('disabled');
						alert('よきせぬエラーが発生しました。');
					}
				});
			}
/*
			$.ajax({
				url:SCRIPT,
				type:'post',
				data: postdata,
				cache: false,
				dataType : 'json',
				success : function(data){
					// localStrageをフラッシュ（キャンセルボタンを押した場合も）
					
					console.log(data.body);
				},
				error : function(data){
					$(prefix+'input, button, select, textarea').removeAttr('disabled');
					alert('よきせぬエラーが発生しました。');
				}
			});
			$.ajax({
				url : SCRIPT,
				type : 'post',
				data: {
					cmd : 'recaptcha',
					action : 'status'
				},
				cache : false,
				success: function(res){
					if (res.responseText == 1){
						return true;
					}else{
						

			return false;	// Submitで直接送信しないようにする
*/
		});

		this.assistant(false);
	},
	show_diff: function(original,source){
		// edit.js v 00 -- pukiwikiの編集を支援
		// Copyright(c) 2010 mashiki
		// License: GPL version 3
		// http://mashiki-memo.blogspot.com/2010/12/blog-post.html

		// show_diff(原文,現在の内容（通常$('msg').value）)
		if (msg===org){
			return false;
		} else {
			return diff(
				original.replace(/^\n+|\n+$/g,'').split("\n"),
				source.replace(/^\n+|\n+$/g,'').split("\n")
			);
		}
	},
	diff : function (arr1, arr2, rev) {
		var len1=arr1.length,
			len2=arr2.length;
		// len1 <= len2でなければひっくり返す
		if (!rev && len1>len2){
			return diff(arr2, arr1, true);
		}
		// 変数宣言及び配列初期化
		var k, p,
			offset=len1+1,
			delta =len2-len1,
			fp=[], ed=[];
		// snake
		var snake = function(k){
			var x, y, e0, o, i,
				y1=fp[k-1+offset],
				y2=fp[k+1+offset];
			if (y1>=y2) { // 経路選択
				y = y1+1;
				x = y-k;
				e0 = ed[k-1+offset];
				o = {edit:rev?'-':'+',arr:arr2, line:y-1};
			} else {
				y = y2;
				x = y-k;
				e0 = ed[k+1+offset];
				o = {edit:rev?'+':'-',arr:arr1, line:x-1};
			}
			// 選択した経路を保存
			if (o.line>=0){ ed[k+offset] = e0.concat(o); }

			var max = len1-x>len2-y?len1-x:len2-y;
			for (i=0; i<max && arr1[x+i]===arr2[y+i]; ++i) {
				// 経路追加
				ed[k+offset].push({edit:'=', arr:arr1, line:x+i});
			}
			fp[k + offset] = y+i;
			return true;
		};
		for (p=0; p<len1+len2+3; ++p) {
			fp[p] = -1;
			ed[p] = [];
		}
		// メインの処理
		for (p=0; fp[delta + offset] !== len2; p++) {
			for(k = -p       ; k <  delta; ++k){ snake(k); }
			for(k = delta + p; k >= delta; --k){ snake(k); }
		}
		return ed[delta + offset];
	},
	realtime_preview : function(prefix){
		if (prefix){
			prefix = prefix + ' ';
		}else{
			prefix = '';
		}
		var oSource = _document.getElementById('msg');
		var source = _document.getElementById('msg').value;
		var self = this;
		
		if (this.ajax_apx) {
//			$(prefix+'#indicator').show().fadeOut();
			$(prefix+'#indicator').html('<img src="'+self.image_dir+'spinner.gif" alt="Loading..." />Now Loading...');
			$(prefix+'textarea#previous').val(source);
			$(prefix+'textarea').attr('disabled', 'disabled');
			
			if (++this.ajax_count !== 1){ return; }
			
			if (_document.selection) {
				var sel = _document.selection.createRange();
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
					var innbox = _document.getElementById('realview_outer');
					var marker = _document.getElementById('editmark');

					if (marker){ innbox.scrollTop = marker.offsetTop - 8; }
					
					if (self.ajax_count===1) {
						self.ajax_count = 0;
					} else {
						self.ajax_count = 0;
						self.realtime_preview();
					}
					$(prefix+'#indicator').html('<span class="ui-icon ui-icon-clock" style="float:left;"></span>'+data.taketime);
					var ret = data.data.replace(/<script[^>]*>[^<]+/ig,'<span class="scripttag" title="Script tag">[SCRIPT]</span>');
					$(prefix+'#realview').html(ret);
					$(prefix+'textarea').removeAttr('disabled');
				},
				error : function(data,status,thrown){
					$("#realview").html([
						'<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">',
							'<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>'+status+'</p>',
							'<ul>',
								'<li>readyState:'+data.readyState+'</li>',
	//							'<li>responseText:'+data.responseText+'</li>',
								'<li>status:'+data.status+'</li>',
								'<li>statusText:'+data.statusText+'</li>',
							'</ul>',
						'</div>'].join("\n")
					);
				}
			});
		}
	},
	// 入力アシスタント
	assistant: function(full){
		var i, len;
		// 絵文字の定義
		// https://github.com/take-yu/JSEmoji/blob/master/mt-static/plugins/JSEmoji/js/emoji.js
		var emojiList = [
			'sun', 'cloud', 'rain', 'snow', 'thunder', 'typhoon', 'mist', 'sprinkle', 'aries', 'taurus', 'gemini', 'cancer', 'leo', 'virgo', 
			'libra', 'scorpius', 'sagittarius', 'capricornus', 'aquarius', 'pisces', 'sports', 'baseball', 'golf', 'tennis', 'soccer', 'ski', 'basketball', 'motorsports', 
			'pocketbell', 'train', 'subway', 'bullettrain', 'car', 'rvcar', 'bus', 'ship', 'airplane', 'house', 'building', 'postoffice', 'hospital', 'bank', 
			'atm', 'hotel', 'cvs', 'gasstation', 'parking', 'signaler', 'toilet', 'restaurant', 'cafe', 'bar', 'beer', 'fastfood', 'boutique', 'hairsalon', 
			'karaoke', 'movie', 'upwardright', 'carouselpony', 'music', 'art', 'drama', 'event', 'ticket', 'smoking', 'nosmoking', 'camera', 'bag', 'book', 
			'ribbon', 'present', 'birthday', 'telephone', 'mobilephone', 'memo', 'tv', 'game', 'cd', 'heart', 'spade', 'diamond', 'club', 'eye', 
			'ear', 'rock', 'scissors', 'paper', 'downwardright', 'upwardleft', 'foot', 'shoe', 'eyeglass', 'wheelchair', 'newmoon', 'moon1', 'moon2', 'moon3', 
			'fullmoon', 'dog', 'cat', 'yacht', 'xmas', 'downwardleft', 'phoneto', 'mailto', 'faxto', 'info01', 'info02', 'mail', 'by-d', 'd-point', 
			'yen', 'free', 'id', 'key', 'enter', 'clear', 'search', 'new', 'flag', 'freedial', 'sharp', 'mobaq', 'one', 'two', 
			'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'zero', 'ok', 'heart01', 'heart02', 'heart03', 'heart04', 'happy01', 
			'angry', 'despair', 'sad', 'wobbly', 'up', 'note', 'spa', 'cute', 'kissmark', 'shine', 'flair', 'annoy', 'punch', 'bomb', 
			'notes', 'down', 'sleepy', 'sign01', 'sign02', 'sign03', 'impact', 'sweat01', 'sweat02', 'dash', 'sign04', 'sign05', 'slate', 'pouch', 
			'pen', 'shadow', 'chair', 'night', 'soon', 'on', 'end', 'clock', 'appli01', 'appli02', 't-shirt', 'moneybag', 'rouge', 'denim', 
			'snowboard', 'bell', 'door', 'dollar', 'pc', 'loveletter', 'wrench', 'pencil', 'crown', 'ring', 'sandclock', 'bicycle', 'japanesetea', 'watch', 
			'think', 'confident', 'coldsweats01', 'coldsweats02', 'pout', 'gawk', 'lovely', 'good', 'bleah', 'wink', 'happy02', 'bearing', 'catface', 'crying', 
			'weep', 'ng', 'clip', 'copyright', 'tm', 'run', 'secret', 'recycle', 'r-mark', 'danger', 'ban', 'empty', 'pass', 'full', 
			'leftright', 'updown', 'school', 'wave', 'fuji', 'clover', 'cherry', 'tulip', 'banana', 'apple', 'bud', 'maple', 'cherryblossom','riceball', 
			'cake', 'bottle', 'noodle', 'bread', 'snail', 'chick', 'penguin', 'fish', 'delicious', 'smile', 'horse', 'pig', 'wine', 'shock'
		];
		
		// パレット設定（横18,縦13で、Dreamweaver風パレット配列）
		var colorList = [
			'#000000','#003300','#006600','#009900','#00CC00','#00FF00','#330000','#333300','#336600','#339900','#33CC00','#33FF00','#660000','#663300','#666600','#669900','#66CC00','#66FF00',
			'#000033','#003333','#006633','#009933','#00CC33','#00FF33','#330033','#333333','#336633','#339933','#33CC33','#33FF33','#660033','#663333','#666633','#669933','#66CC33','#66FF33',
			'#000066','#003366','#006666','#009966','#00CC66','#00FF66','#330066','#333366','#336666','#339966','#33CC66','#33FF66','#660066','#663366','#666666','#669966','#66CC66','#66FF66',
			'#000099','#003399','#006699','#009999','#00CC99','#00FF99','#330099','#333399','#336699','#339999','#33CC99','#33FF99','#660099','#663399','#666699','#669999','#66CC99','#66FF99',
			'#0000CC','#0033CC','#0066CC','#0099CC','#00CCCC','#00FFCC','#3300CC','#3333CC','#3366CC','#3399CC','#33CCCC','#33FFCC','#6600CC','#6633CC','#6666CC','#6699CC','#66CCCC','#66FFCC',
			'#0000FF','#0033FF','#0066FF','#0099FF','#00CCFF','#00FFFF','#3300FF','#3333FF','#3366FF','#3399FF','#33CCFF','#33FFFF','#6600FF','#6633FF','#6666FF','#6699FF','#66CCFF','#66FFFF',
			'#990000','#993300','#996600','#999900','#99CC00','#99FF00','#CC0000','#CC3300','#CC6600','#CC9900','#CCCC00','#CCFF00','#FF0000','#FF3300','#FF6600','#FF9900','#FFCC00','#FFFF00',
			'#990033','#993333','#996633','#999933','#99CC33','#99FF33','#CC0033','#CC3333','#CC6633','#CC9933','#CCCC33','#CCFF33','#FF0033','#FF3333','#FF6633','#FF9933','#FFCC33','#FFFF33',
			'#990066','#993366','#996666','#999966','#99CC66','#99FF66','#CC0066','#CC3366','#CC6666','#CC9966','#CCCC66','#CCFF66','#FF0066','#FF3366','#FF6666','#FF9966','#FFCC66','#FFFF66',
			'#990099','#993399','#996699','#999999','#99CC99','#99FF99','#CC0099','#CC3399','#CC6699','#CC9999','#CCCC99','#CCFF99','#FF0099','#FF3399','#FF6699','#FF9999','#FFCC99','#FFFF99',
			'#9900CC','#9933CC','#9966CC','#9999CC','#99CCCC','#99FFCC','#CC00CC','#CC33CC','#CC66CC','#CC99CC','#CCCCCC','#CCFFCC','#FF00CC','#FF33CC','#FF66CC','#FF99CC','#FFCCCC','#FFFFCC',
			'#9900FF','#9933FF','#9966FF','#9999FF','#99CCFF','#99FFFF','#CC00FF','#CC33FF','#CC66FF','#CC99FF','#CCCCFF','#CCFFFF','#FF00FF','#FF33FF','#FF66FF','#FF99FF','#FFCCFF','#FFFFFF',
			'#111111','#222222','#333333','#444444','#555555','#666666','#777777','#888888','#999999','#A5A5A5','#AAAAAA','#BBBBBB','#C3C3C3','#CCCCCC','#D2D2D2','#DDDDDD','#EEEEEE','#FFFFFF'
		];
		
		// アシスタントのウィジット
		$('.assistant').html([
			'<ul class="ui-widget pkwk_widget">',
				'<li class="replace ui-button ui-state-default ui-corner-left" title="Bold" name="b"><strong>b</strong></li>',
				'<li class="replace ui-button ui-state-default" title="Italic" name="i" style="font-weight:normal;"><em>i</em></li>',
				'<li class="replace ui-button ui-state-default" title="Strike" name="s" style="font-weight:normal;"><strike>s</strike></li>',
				'<li class="replace ui-button ui-state-default" title="Underline" name="u" style="font-weight:normal;"><span class="underline">u</span></li>',
				'<li class="replace ui-button ui-state-default" title="Code" name="code" style="font-weight:normal;"><span class="ui-icon ui-icon-script"></span></li>',
				'<li class="replace ui-button ui-state-default ui-corner-right" title="Q" name="q" style="font-weight:normal;"><span class="ui-icon ui-icon-comment"></span></li>',
				'<li class="replace ui-button ui-state-default ui-corner-left" title="Link" name="url"><span class="ui-icon ui-icon-link"></span></li>',
				'<li class="replace ui-button ui-state-default" title="Size" name="size">size</li>',
				'<li class="insert ui-button ui-state-default ui-corner-right" title="Color" name="color">color</li>',
				'<li class="insert ui-button ui-state-default ui-corner-left" title="Emoji" name="emoji">☺</li>',
				'<li class="insert ui-button ui-state-default ui-corner-right" title="br" name="br">⏎</li>',
				'<li class="replace ui-button ui-state-default ui-corner-all" title="ncr" name="ncr">&amp;#</li>',
				'<li class="insert ui-button ui-state-default ui-corner-all" title="Help" name="help"><span class="ui-icon ui-icon-help"></span></li>',
				(typeof(Modernizr.localstorage) !== 'undefined') ? '<li class="insert ui-state-default ui-corner-all" title="Flush Storage" name="flush"><span class="ui-icon ui-icon-trash"></span></li>': null,
				'<li class="ui-widget-content ui-corner-all" style="float:right; width:auto;display:none;font-weight:normal;" id="indicator"></li>',
			'</ul>'
		].join("\n"));
		
		// 絵文字パレットのウィジット
		var emoji_widget = '<ul class="ui-widget pkwk_widget ui-helper-clearfix">';
		for(i = 0, len = emojiList.length; i < len ; i++ ){
			var name = emojiList[i];
			emoji_widget += '<li class="ui-button ui-state-default ui-corner-all" title="'+name+'" name="'+name+'"><span class="emoji emoji-'+name+'"></span></li>';
		}
		emoji_widget += '</ul>';
		$(_document.body).append('<div id="emoji"></div>');
		$('#emoji').dialog({
			title:'Emoji',
			autoOpen:false,
			bgiframe: true,
			minWidth:410,
			height:410,
			minHeight:410,
			show: "scale",
			hide: "scale",
			buttons:{
				Close:function(){$(this).dialog('close');}
			}
		}).html(emoji_widget);

		// カラーパレットのウィジット
		var color_widget = '<ul class="ui-widget pkwk_widget ui-helper-clearfix" id="colors">', j=0;
		for(i = 0, len = colorList.length; i < len ; i++ ){
			var color = colorList[i];
			color_widget += '<li class="ui-button ui-state-default" title="'+color+'" name="'+color+'"><span class="emoji" style="background-color:'+color+';"></span></li>';
			j++;
		}
		color_widget += '</ul>';
		$(_document.body).append('<div id="color_palette"></div>');
		$('#color_palette').dialog({
			title:'Palette',
			autoOpen:false,
			bgiframe: true,
			width:470,
			height:400,
			show: "scale",
			hide: "scale"
		}).html(color_widget);
		
		// ここから、イベント割り当て
		if (!this.elem){ this.elem = $('*[name=msg]')[0]; }
		var self = this;
		// アシスタント

		$('*[name=msg]').focus(function(e){
			self.elem = this;
		});

		$('.insert').click(function(){
			var ret = '';
			var elem = $(self.elem);
			var str = elem.getSelection().text;
			var v = $(this).attr('name');

			switch (v){
				case 'help' :
					alert($.i18n('pukiwiki', 'hint_text1'));
				break;
				case 'br':
					ret = '&br;'+"\n";
				break;
				case 'emoji' :
					$('#emoji').dialog('open');
				break;
				case 'color' :
					$('#color_palette').dialog('open');
				break;
				case 'flush' :
					if (Modernizr.localstorage && confirm('Flush local strage?') === true){
						localStorage.removeItem(PAGE);
					}
				break;
				default:
					ret = '&('+$(this).attr('name')+');';
				break;
			}
			if (ret !== ''){
				if (str === ''){
					elem.insertAtCaretPos(ret);
				}else{
					elem.replaceSelection(ret);
				}
				elem.focus();
			}
			return;
		});
		$('.replace').click(function(){
			var ret = '';
			var elem = $(self.elem);
			var str = elem.getSelection().text;
			var v = $(this).attr('name');
			
			if (str === ''|| !elem){
				alert( $.i18n('pukiwiki', 'select'));
				return;
			}

			switch (v){
				case 'size' :
					var default_size = "100%";
					var val = prompt($.i18n('pukiwiki', 'fontsize'), default_size);
					if (!val || !val.match(/\d+/)){
						return;
					}
					ret = '&size(' + val + '){' + str + '};';
				break;
				case 'ncr':
					var i, len;
					for(i = 0, len = str.length; i < len ; i++ ){
						ret += ("&#"+(str.charCodeAt(i))+";");
					}
				break;
				case 'b':	//mikoadded
					ret = "''" + str + "''";
				break;
				case 'i':
					ret = "'''" + str + "'''";
				break;
				case 'u':
					ret = '__' + str + '__';
				break;
				case 's':
					ret = '%%' + str + '%%';
				break;
				case 'code' :
					ret = '@@' + str + '@@';
				break;
				case 'q' :
					ret = '@@@' + str + '@@@';
				break;
				
				case 'url':
				//	var regex = "^s?https?://[-_.!~*'()a-zA-Z0-9;/?:@&=+$,%#]+$";
					var my_link = prompt( $.i18n('pukiwiki', 'url'), 'http://');
					if (my_link !== null) {
						ret = '[[' + str + '>' + my_link + ']]';
					}
				break;
/*				default:
					if (str.match(/^&color\([^\)]*\)\{.*\};$/)){
						ret = str.replace(/^(&color\([^\)]*)(\)\{.*\};)$/,"$1," + v + "$2");
					}else{
						ret = '&color(' + v + '){' + str + '};';
					}
				break;
*/
			}
			elem.replaceSelection(ret);
			elem.focus();
			return;
		});
		
		
		
		$('#emoji ul li').click(function(){
			var elem = $(self.elem);
			var str = elem.getSelection().text;
			var v = '&('+$(this).attr('name')+');';;

			if (str === ''){
				elem.insertAtCaretPos(v);
			}else{
				elem.replaceSelection(v);
			}
			self.elem.focus();
	//		$('#emoji').dialog('close');
			return;
		});
		$('#color_palette ul li').click(function(){
			var ret;
			var elem = $(self.elem);
			var str = elem.getSelection().text;
			var v = $(this).attr('name');
			
			if (str === ''|| !elem){
				alert( $.i18n('pukiwiki', 'select'));
				return;
			}
			
			if (str.match(/^&color\([^\)]*\)\{.*\};$/)){
				elem.replaceSelection(str.replace(/^(&color\([^\)]*)(\)\{.*\};)$/,"$1," + v + "$2"));
			}else{
				elem.replaceSelection('&color(' + v + '){' + str + '};');
			}
			$('#color_palette').dialog('close');
			self.elem.focus();
			
			return;
		});
	},
	/* swfuploderのフォームに書き換え */
	set_uploader: function(prefix){
		if (prefix){
			prefix = prefix + ' ';
		}else{
			prefix = '';
		}

		var pass_form;
		if ($(prefix+'input[name=pass]').length !== 0){
			pass_form = '<label for="pass">Password:</label><input type="password" name="pass" id="pass" size="8" value="" /><br />';
		}

		// swfuploadがスクリプトに渡す値
		
		var params = {
			max_file_size	: $(prefix+'input[name=max_file_size]').val(),// 上限容量
			pcmd			: $(prefix+'input[name=pcmd]').val(),		// プラグインコマンド（通常post）
			plugin			: $(prefix+'input[name=plugin]').val(),		// プラグイン（通常attach）
			refer			: $(prefix+'input[name=refer]').val(),		// 添付先のページ
			ajax			: 'json' // 送信完了時にページをjson化したデーターを読み込む。（スキンの処理を確認せよ）
		};
		
		if (params.plugin === 'attachref'){
			params.attachref_no		= $(prefix+'input[name=attachref_no]').val();
			params.attachref_opt	= $(prefix+'input[name=attachref_opt]').val();
			params.digest			= $(prefix+'input[name=digest]').val();
		}
		
		// デフォルト値
		var config = (typeof(this.skin.swfupload) !== 'object') ? {	
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
		} : this.skin.swfupload;
		
		// attachrefの場合、１つのみファイルをアップ可能
		if (params.plugin === 'attachref'){
			config.file_upload_limit=1;
			config.file_queue_limit=1;
		}

		params.pass = ($("input[name=pass]").length !== 0) ? $("input[name=pass]").val(): '';	// パスワード
		params.encode_hint = ($("input[name=encode_hint]").length !== 0) ? $(prefix+'input[name=encode_hint]').val() : '';	// エンコード判別用

//		console.dir(params);

		// 添付画面のテンプレート
		$(prefix+'.attach_form').html( [
			'<div id="swfupload-control">',
				'<input type="button" id="swfupload_button" />',
				'<p id="swfupload-queuestatus" ></p>',
				'<ol id="swfupload-log" class="ui-widget"></ol>',
				'<button id="execute">Go</button>',
			'</div>'
		].join("\n"));
		
		$(prefix+'#execute').button({
			icons : { primary: 'ui-icon-check' },
			disabled:true
		}).click(function(){
			$('#swfupload-control').swfupload('startUpload');
			return false;
		}).css({
			display:'none'
		});

		$(prefix+'#swfupload-control').swfupload(config)	// ファイル添付のフォームをswfupload.swfに置き換え
		.bind('fileQueued', function(event, file){
			var listitem = [
				'<li id="'+file.id+'" class="ui-widget-content ui-corner-all">',
					'File: <em>'+file.name+'</em> ('+Math.round(file.size/1024)+' KB) <span class="progressvalue" ></span>',
					'<div class="progressbar"></div>',
					'<p class="status"><span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-info"></span>Pending</p>',
					'<button class="cancel">Close</button>',
				'</li>'
			].join("\n");
	
			$(prefix+'#swfupload-log').append(listitem);
			
			$(prefix+'li#'+file.id+' button:first').button({
				icons: {
					primary: 'ui-icon-close'
				}
			}).click(function(){
				var swfu = $.swfupload.getInstance('#swfupload-control');
				swfu.cancelUpload(file.id);
				$('#swfupload-log li#'+file.id).slideUp('fast');
				return false;
			});
			
			$(prefix+'#swfupload-log li#'+file.id+' .progressbar').progressbar();
			
			$(prefix+'#execute').button({
				disabled:false
			}).css({
				display:''
			});
		})
		.bind('fileQueueError', function(event, file, errorCode, message){
			var listitem = [
				'<li id="error" class="ui-widget-content ui-state-error ui-corner-all"><em>Error</em>',
					'<p>',
						'<span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-alert"></span>',
						'You have attempted to queue too many files.',
					'</p>',
					'<button class="close" >Close</button>',
				'</li>'
			].join("\n");

			$(prefix+'#swfupload-log').append(listitem);
			
			$(prefix+'li#error button').button({
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
			if (params.plugin !== 'attachref'){
				var ret = eval("(" + serverData + ")");
				var item=$(prefix+'#swfupload-log li#'+file.id);
				$(prefix+'#swfupload-log li#'+file.id+' .progressbar').progressbar({ value: 100 });
				item.find('.progressvalue').text('100%');
				var pathtofile = '<a href="'+SCRIPT+'?plugin=attach&pcmd=open&refer='+PAGE+'&file='+file.name+'" id="view_'+file.id+'"><span style="float: right; margin-right: 0.3em;" class="ui-icon ui-icon-newwin"></span>view &raquo;</a>';
				item.addClass('success').find('p.status').html(ret.title+' | '+pathtofile);
				$(prefix+'#swfupload-log li#'+file.id+' a#view_'+file.id).click(function(){
					var href = $(this).attr('href');
/*
					if (href.match(/\.(jpg|jpeg|gif|png)$/i) !== -1){
						$(this).colorbox();
					}else if (href.match(/\.(mp3|ogg|m4a)$/i) !== -1){
						pukiwiki.music_player(this);
					}else{
*/
						_window.open(href);
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
	CFCheck: function(){
		if (typeof(CFInstall) === 'object' || $('meta[http-equiv=X-UA-Compatible]')[0].content.match(/chrome/) !== -1){
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
		if (typeof(SyntaxHighlighter) === 'object'){
			SyntaxHighlighter.config.clipboardSwf = SKIN_DIR+'js/syntaxhighlighter/scripts/clipboard.swf';
			SyntaxHighlighter.all();
		}
	},
	/** Collects link types from head element. */
	linkattrs : function(){
		var i, len;
		var links = $('link[rel]');
		for(i = 0, len = links.length; i < len ; i++ ){
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
					if(link.hreflang === 'en'){ this.hasEversion = true; }
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
	preptoc : function(dom){
		var lis = this.prepHdngs(dom);
		var self = this;
		this.genTocDiv(lis);
		$(_document).keypress(function(elem){
			var key = elem.which;	// 押されたキーのコードを取得
			var key_label = String.fromCharCode(key);	// キーのラベル（ESCキーなどは取得できない）

			if(elem.target.nodeName.match(/(input|textarea)/i) !== -1){
				if (key === 27){return false;}
				return true;	// inputタグ、textareaタグ内ではキーバインド取得を無効化（ただしESCキーは除外）
			}

			//	console.log(key,key_label);
			
			switch(key_label){
				case '?':
					// ?キーが押された場合ヘルプページへ移動
					if(location.href.indexOf("Help") === -1 && confirm("Go to help/search page ?")){
						location.href= pukiwiki.help;
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
							if(key === 27 || key === 47){
								pukiwiki._hideToc(); //Esc, slash
/*
							}else if(key >= 48 && key <=57){ //0-9
								key -= 48;
								if(self.nkeylink[key]){
								//	location.href = "#" + self.nkeylink[key];
									self.anchor_scroll(self.nkeylink[key],false);
									_hideToc();
								}
*/
							}
						}else{
							if(key === 47) {
								$(self.toc).css('top',$(_window).scrollTop() + 'px').css('left',$(_window).scrollLeft() + 'px').fadeIn("fast");	// /キー
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
		
		var ptocImg = '<img src="'+this.image_dir+'toc.png" class="tocpic" title="Table of Contents of this page" alt="Toc" />';
//		var ptocMsg = "Click heading, and Table of Contents will pop up";
		var self = this;
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
				self.popToc(this);
			});
*/
		}else if(hd.length !== 0){
			// 通常時の動作。h2タグのみリスティング
			var li = [];
			hd.each(function(index){
				var $this = $(this);
				var xid = $this.attr('id');
				if (!xid){ $this.attr('id','_genid_'+index); }
				$this.html($this.html()+ptocImg);
				li[index] = '<li><a href="#'+xid+'">'+$this.text()+'</a></li>';
			});
			lis = '<ol>'+li.join("\n")+'</ol>';
		}else if($.query.get('cmd').match(/list|backup/) !== -1){
			// おまけ。一覧ではトップのナビを入れる。
			lis = '<div style="text-align:center;">'+$('#top').html()+'</div>';
			$(prefix+'#top').html($(prefix+'#top').html()+ptocImg);
		}
	
		return lis;
	},
	/** generate the popup TOC division */
	genTocDiv : function(lis){
		this.toc = _document.createElement("div");
		this.toc.id = 'poptoc';
		$(this.toc)
			.addClass('ui-widget ui-corner-all noprint')
			.css('top',0)
			.css('left',0)
			.css('position','absolute')
			.css('z-index',1)
			.html([
			'<h1><a href="#">'+$('h1#title').text()+'</a><abbr title="Table of Contents">[TOC]</abbr></h1>',
			lis.replace(/href/g,'tabindex="1" href'),
			this.getNaviLink()
		].join(''));

		_document.body.appendChild(this.toc);
		$(_document).click(this.popToc);
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
			if(!$.browser.safari){ o.style.width = maxw + "px"; }	// Safari xhtml+xml
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
		].join("");

		return (navi ?  "<p class='nav'>" + navi + "</p>" : "");
	},
//-- click event handlers ----
/** Show/hide TOC on click event according to its location */
	popToc : function(ev){
		var tg;
		if (!ev){ ev = event; }
		tg = (_window.event) ? ev.srcElement : ev.target;
		if(ev.altKey){
			pukiwiki._dispToc(ev,tg,0);
		}else if(tg.className === 'tocpic'){
			pukiwiki._dispToc(ev,tg,1);
		}else{
			pukiwiki._hideToc(ev);
		}
	},
	// popuptocでクリックした項目をハイライトさせる
	setCurPos : function(tg,type){
		var pn = tg.parentNode;
		var tid = (type ===1) ? tg.getAttribute("id") :
			(pn.getAttribute("id") ? pn.getAttribute("id") :
				(pn.firstChild.getAttribute ? pn.firstChild.getAttribute("id") :''));
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
		var res, i, len, n = m.childNodes;
		for(i = 0, len = n.length; i < len ; i++ ){
			if(n.item(i).nodeType === 3){
				res += n.item(i).data;
			}else if(n.item(i).nodeType === 1){
				res += this.nodeText(n.item(i));
			}
		}
		res = res.replace(/^\s*/,"");
		return res.replace(/\s*$/,"");
	},
	_dispToc : function(ev,tg,type){
		var doc = {
			x:ev.clientX + $(_window).scrollLeft(),
			y:ev.clientY + $(_window).scrollTop()
		};
		var scr = {
			x: ev.pageX,
			y: ev.pageY,
			w: $(_window).width(),
			h: $(_window).height()
		};

		var h = this.toc.height;
		var w = this.toc.width;
		var top;

		if(scr.h < h){
			$(this.toc).css('height', scr.h + "px").css('overflow', 'auto');
			top = (doc.y - scr.y);
		}else{
			top = ((scr.h - scr.y > h) ? doc.y : ((scr.y > h) ? (doc.y - h) :((scr.y < scr.h/2) ? (doc.y - scr.y) : (doc.y + scr.h - scr.y - h))));
		}
		$(this.toc).css('top', top + "px").css('left',((scr.x < scr.w - w) ? doc.x : (doc.x - w) )+ 'px');
		if(type){ pukiwiki.setCurPos(tg,type); }
		$(this.toc).fadeIn("fast");
	},
	_hideToc : function(ev){
		$(this.toc).fadeOut("fast");
	},
// Bad Behavior
	bad_behavior: function(prefix){
		if (prefix){
			prefix = prefix + ' ';
		}else{
			prefix = '';
		}

		if (typeof(BH_NAME) !== 'undefined' && typeof(BH_VALUE) !== 'undefined'){
			$(prefix+'form').append('<input type="hidden" name="'+BH_NAME+'" value="'+BH_VALUE+'" />');
		}
	}
};

// Common Plus function.
function open_uri(href, frame){
	if (!frame){
		return false;
	}
	_window.open(href, frame);
	return false;
}

// $().serializeArrayをJSONにする
$.fn.serializeObject = function(){
	var o = {};
	var a = this.serializeArray();
	$.each(a, function() {
		if (o[this.name]) {
			if (!o[this.name].push) {
				o[this.name] = [o[this.name]];
			}
			o[this.name].push(this.value || '');
		} else {
			o[this.name] = this.value || '';
		}
	});
	return o;
};

/*
 * jQuery Disable On Submit Plugin
 * http://www.evanbot.com/article/jquery-disable-on-submit-plugin/13
 *
 * Copyright (c) 2009 Evan Byrne (http://www.evanbot.com)
 * Modified by Logue
 */
$.fn.disableOnSubmit = function(disableList){
	var $list = (disableList === null) ? 'input[type=submit],input[type=button],input[type=reset],button' : disableList;

	// Makes sure button is enabled at start
	$(this).find($list).removeAttr('disabled');

	$(this).submit(function(){
		$(this).find($list).attr('disabled','disabled');
	});
	return this;
};

/* jquery Scroll Lock
 * http://tockri.blog78.fc2.com/blog-entry-117.html
 */
var ScrollLock = {
	lock: function() {
		var $win = $(_window);
		var vp = {
			width: $win.width(),
			height: $win.height(),
			top: _document.body.scrollTop  || _document.documentElement.scrollTop,
			left: _document.body.scrollLeft || _document.documentElement.scrollLeft
		};
		ScrollLock.viewport = vp;
		var rect = [
			vp.top + "px",
			vp.left + vp.width + "px",
			vp.top + vp.height + "px",
			vp.left + "px"
		];
		$("body").css({
			clip: "rect(" + rect.join(",") + ")",
			position:"absolute",
			overflow:"hidden",
			top:-vp.top,
			left:0
		});
	},
	unlock: function() {
		$("body").css({
			//clip: "",
			position:"static",
			overflow:"",
			top:0,
			left:0
		});
		var vp = ScrollLock.viewport;
		_window.scrollTo(vp.left, vp.top);
	}
};

/*************************************************************************************************/
// ブラウザの設定
var $buoop = {
	vs:{				// browser versions to notify
		i:7,			// IE
		f:3,			// FF
		o:11.00,		// Opera
		s:5,			// Safari
		n:9
	},
	reminder: 1,		// atfer how many hours should the message reappear
	onshow: function(){	// callback function after the bar has appeared
					
	},
	l: LANG,			// set a language for the message, e.g. "en"
//	test: DEBUG,		// true = always show the bar (for testing)
	newwindow: false	// open link in new window/tab
};
if (DEBUG){
	$buoop.text = 'When the version of a browser is old, the text which presses for renewal of a browser here is displayed.';
}else if ((_document.compatMode || "") !== "CSS1Compat"){
	$buoop.text = 'Please set native rendering mode.';
}

// onLoad/onUnload
$(_document).ready(function(){
	$.getScript('http://browser-update.org/update.js');
	
	// Google Analytics
	if (typeof(GOOGLE_ANALYTICS) !== 'undefined'){
		$.ajaxGA.init(GOOGLE_ANALYTICS, true);
	}

	var f;
	while( f = pkwkBeforeInit.shift() ){
		if( f != null ){
			f();
		}
	}
	f = null;

	pukiwiki.init();

	tzCalculation_LocalTimeZone(location.host,false);
	while( f = pkwkInit.shift() ){
		if( f != null ){
			f();
		}
	}
});

$(_window).unload(function(){
	var f;
	while( f = pkwkBeforeUnload.shift() ){
		if( f != null ){
			f();
		}
	}
	f = null;

	pukiwiki.unload();
	while( f = pkwkUnload.shift() ){
		if( f != null ){
			f();
		}
	}
});

// usage: log('inside coolFunc',this,arguments);
// paulirish.com/2009/log-a-lightweight-wrapper-for-consolelog/
_window.log = function(){
	log.history = log.history || [];	// store logs to an array for reference
	log.history.push(arguments);
	if(this.console){
		console.log( Array.prototype.slice.call(arguments) );
	}
};
(function (con) {
	// the dummy function
	function dummy() {};

	for(var methods = ['error','info','log','warn'], func; func = methods.pop();) {
		con[func] = con[func] || dummy;
	}
}(_window.console=_window.console = {}));

// catch all _document.write() calls
(function(doc){
	var write = doc.write;
	doc.write = function(q){
		log('document.write(): ',arguments);
		if (/docwriteregexwhitelist/.test(q)){ write.apply(doc,arguments); }
	};
})(_document);