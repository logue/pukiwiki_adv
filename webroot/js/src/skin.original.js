/*!
 * PukiWiki Advance - Yet another WikiWikiWeb clone.
 * Pukiwiki skin script for jQuery
 * Copyright (c)2010-2013 PukiWiki Advance Developer Team
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

/* jslint evil: false, browser: true */
/* Implied global: $, jQuery, Modernizr, window, document, SCRIPT, LANG, DEBUG, SKIN_DIR, IMAGE_URI, DEFAULT_LANG, THEME_NAME, PAGE, MODIFIED, GOOGLE_ANALYTICS, FB, FACEBOOK_APPID */

var pukiwiki = {};

// Bigscope
(function ($, Modernizr, window, document) {
	'use strict';

	// Avoid `console` errors in browsers that lack a console.
	var method;
	var noop = function noop() {};
	var methods = [
		'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
		'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
		'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
		'timeStamp', 'trace', 'warn'
	];
	var length = methods.length;
	var console = (window.console = window.console || {});

	while (length--) {
		method = methods[length];

		// Only stub undefined methods.
		if (!console[method]) {
			console[method] = noop;
		}
	}

	if (DEBUG) {
		window.console.debugMode = true;
		var D1 = new Date();
	}

	// Detect IE
	var ie = (function (){
		var v = 3,
		div = document.createElement('div'),
		all = div.getElementsByTagName('i');
		while (
			div.innerHTML = '<!--[if gt IE ' + (++v) + ']><i></i><![endif]-->',
			all[0]
		){};
		return v > 4 ? v : undefined;
	}());

	var isMobile = navigator.userAgent.match(/iphone|ipod|ipad|android/i) ? true : false;

	// オーバーライド用
	var pkwkInit = [], pkwkBeforeInit = [], pkwkUnload = [], pkwkBeforeUnload = [], pkwkAjaxLoad = [];

	if (!$) { throw "pukiwiki: jQuery does not included."; }
	if (!$.ui) { throw "pukiwiki: jQueryUI does not included."; }

	pukiwiki = {
		meta : {
			'@prefix' : '<http://purl.org/net/ns/doas#>',
			'@about' : '<skin.js>', 'a': ':JavaScript',
			'title' : 'Pukiwiki skin script for jQuery',
			'created' : '2008-11-25', 'release': {'revision': '2.2.31', 'created': '2013-01-22'},
			'author' : {'name': 'Logue', 'homepage': '<http://logue.be/>'},
			'license' : '<http://www.gnu.org/licenses/gpl-2.0.html>'
		},
		custom : {},	// 消さないこと。（スキン用カスタムネームスペース）
		isPage : (typeof(PAGE) !== 'undefined' && !$.query.get('cmd') && !PAGE.match(/^:|FormatRules|RecentChanges|RecentDeleted|InterWikiName|AutoAliasName|MenuBar|SideBar|Navigation|Glossary/i)) ? true : false,
		href : $('link[rel=canonical]')[0].href,
		init : function(){
			// metaタグのGenereterから、Plusかそうでないかを判別
			var generetor = $('meta[name=generator]')[0].content;
			if (generetor.match(/[PukiPlus|Advance]/)){
				this.image_dir = IMAGE_URI+'ajax/';	// デフォルト
				this.plus = true;
				this.adv = true;
			}else if (generetor.match(/plus/)){
				// PukiWiki Plus!の場合
				this.image_dir = SKIN_DIR+'theme/'+THEME_NAME+'/';
				this.plus = true;
				this.adv = false;
			}else{
				// PukiWiki用
				this.image_dir = SKIN_DIR+this.name+'/image/';
				this.plus = false;
				this.adv = false;
			}
			if (DEBUG){
				$('.message_box ul').append(
					'<li>JavaScript framework:' + 
					'<a href="http://modernizr.com/">Modernizr</a>: <var>'+Modernizr._version+'</var> / ' +
					'<a href="http://jquery.com/">jQuery</a>: <var>'+$.fn.jquery+'</var> / '+
					'<a href="http://jqueryui.com">jQuery UI</a>: <var>'+$.ui.version+ '</var>.</li>');
			}

			var self = this;
			var protocol = ((document.location.protocol === 'https:') ? 'https:' : 'http:')+'//';
			this.body = this.custom.body ? this.custom.body : '*[role="main"]';

			// 言語設定
			$.i18n(LANG);

			// スキン設定をオーバーライド
			$.extend(true, $.fn.superfish.defaults, {
				autoArrows:		false,	// if true, arrow mark-up generated automatically = cleaner source code at expense of initialisation performance
				dropShadows:	false
			}, this.custom.suckerfish);
			/*
			$.extend(true, $.ui.rlightbox.options, {
				animationSpeed: "fast",
				setPrefix: "lb",
				showMap: true,
				counterDelimiter: " / ",
				videoWidth: 640,
				videoHeight: 385,
				errorMessage: "Oh dear! Something went wrong! If the problem still appears let the page’s admin know. Would you like to try again or reject the content?",
				againButtonLabel: "Try again",
				rejectButtonLabel: "Reject this content",
				overwriteTitle: false,
				keys: {
					next: [78, 39],
					previous: [80, 37],
					close: [67, 27],
					panorama: [90, null]
				},
				loop: false
			}, this.custom.rlightbox);
			*/
			$.extend(true, $.beautyOfCode.settings, {
				theme: 'Default',
				brushes: ['Plain', 'Diff'],
				useScriptTags : false,
				baseUrl:JS_URI+'syntaxhighlighter/',
				config: {
					strings : {
						expandSource				: $.i18n('sh', 'expandSource'),
						viewSource					: $.i18n('sh', 'viewSource'),
						copyToClipboard				: $.i18n('sh', 'copyToClipboard'),
						copyToClipboardConfirmation : $.i18n('sh', 'copyToClipboardConfirmation'),
						print						: $.i18n('sh', 'print'),
						noBrush						: $.i18n('sh', 'noBrush'),
						brushNotHtmlScript			: $.i18n('sh', 'brushNotHtmlScript')
					}
				}
			}, this.custom.syntaxhighlighter);
			
			$.extend(true, $.fn.dataTable.defaults, {
				bJQueryUI	: true,
				bAutoWidth	: false,
				sDom		: '<"H"pi>tr<"F"lf>',
				sPaginationType: 'full_numbers',
				oLanguage: {
					sEmptyTable		: $.i18n('dataTable', 'sEmptyTable'),
					sInfo			: $.i18n('dataTable', 'sInfo'),
					sInfoEmpty		: $.i18n('dataTable', 'sInfoEmpty'),
					sInfoFiltered	: $.i18n('dataTable', 'sInfoFiltered'),
					sInfoPostFix 	: '',
					sInfoThousands	: $.i18n('dataTable', 'sInfoThousands'),
					sLengthMenu		: $.i18n('dataTable', 'sLengthMenu'),
					sLoadingRecords	: $.i18n('dialog', 'loading'),
					sProcessing		: $.i18n('dataTable', 'sProcessing'),
					sSearch			: $.i18n('dataTable', 'sSearch'),
					sUrl			: '',
					sZeroRecords	: $.i18n('dataTable', 'sZeroRecords'),
					oPaginate : {
						//sFirst : '<span class="ui-icon ui-icon-arrowthickstop-1-w" title="'+ $.i18n('dialog', 'first') +'"></span>',
						//sPrevious : '<span class="ui-icon ui-icon-arrowthick-1-w" title="'+ $.i18n('dialog', 'prev') +'"></span>',
						//sNext : '<span class="ui-icon ui-icon-arrowthick-1-e" title="'+ $.i18n('dialog', 'next') +'"></span>',
						//sLast : '<span class="ui-icon ui-icon-arrowthickstop-1-e" title="'+ $.i18n('dialog', 'last') +'"></span>'
						sFirst		: $.i18n('dialog', 'first'),
						sPrevious	: $.i18n('dialog', 'prev'),
						sNext		: $.i18n('dialog', 'next'),
						sLast		: $.i18n('dialog', 'last')
					},
					oAria: {
						sSortAscending	: $.i18n('dataTable', 'sSortAscending'),
						sSortDescending	: $.i18n('dataTable', 'sSortDescending')
					}
				}
			});

			this.assistant_setting = {
				// 絵文字の定義
				// https://github.com/take-yu/JSEmoji/blob/master/mt-static/plugins/JSEmoji/js/emoji.js
				emoji : [
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
				],
				// パレット設定（横18,縦13で、Dreamweaver風パレット配列）
				color : [
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
				]
			};

			// HTML5サポート
			this.enableHTML5();

			// Lazyload（遅延画像ロード）
			// IE6では、pngの場合アルファチャンネルが効かなくなるため、拡張子がpngのときは処理しない
			$('*[role=main] ' + ((ie < 6) ? 'img[src!$=.png]' : 'img')).lazyload({
				placeholder : this.image_dir+'grey.gif',
				effect : "fadeIn"
			});

			// Suckerfish（ポップアップメニュー
			$('.sf-menu').superfish();

			// ポップアップ目次
			this.linkattrs();
			this.preptoc(this.body);

			// アシスタント
			if ($('.edit_form').length !== 0 && $.query.get('cmd') !== 'guiedit'){
				this.set_editform();
			}

			if ($('*[name="msg"]').length !== 0){
				$('.comment_form').append('<div class="assistant ui-corner-all ui-widget-header ui-helper-clearfix"></div>');
				this.assistant();
			}

			// フォームを初期化
			this.init_dom();

			// バナーボックス
			$('#banner_box img').fadeTo(200,0.3);
			$('#banner_box img').hover(
				function(){
					$(this).fadeTo(200,1);
				},
				function(){
					$(this).fadeTo(200,0.3);
				}
			);
			this.social();

			// 非同期通信中はUIをブロック
			this.blockUI(document);
		},
		// ページを閉じたとき
		unload : function(prefix){
//			this.loadingScreen.dialog('open');
			prefix = (prefix) ? prefix + ' ': '';
			$('input, select, textarea').attr('disabled','disabled');

			// フォームが変更されている場合
			if ( $(prefix + '#msg').val() !== $(prefix + '#original' ).val()) {
				if ( confirm( $.i18n('pukiwiki', 'unload')) ) {
					this.appendChild(document.createElement('input')).setAttribute('name', 'write');
					$('<input name="write" />').appendTo(this);
					$(prefix + 'form').submit();
					alert( $.i18n('pukiwiki', 'submit'));
				}else{
//					this.loadingScreen.dialog('close');
				}
			}

			$('#jplayer').jPlayer('destroy');	// jPlayerを開放
			return false;
		},
		// HTML5の各種機能をJavaScriptで有効化するための処理
		enableHTML5 : function(prefix){
			prefix = (prefix) ? prefix + ' ': '';

			// IE互換処理
			if (ie <= 6) {
				// Fix Background flicker
				try{ document.execCommand('BackgroundImageCache', false, true); }catch(e){}
				// IE PNG Fix
				$.ajax({
					type: 'GET',
					global : false,
					url: JS_URI+'iepngfix/iepngfix_tilebg.js',
					dataType: 'script'
				});
				$('img[src$=png], .pkwk-icon, .pkwk-symbol').css({
					'behavior': 'url('+JS_URI+'iepngfix/iepngfix.htc)'
				});
			}

			if (ie <= 7) {
				$('.navibar ul').before('[ ').after(' ] ');
			}
			if (ie <= 8){
				$('.navibar li:not(:last-child)').after(' | ');
				$('.topicpath li:not(:last-child)').after(' > ');
			}

			// Canvas実装
			Modernizr.load({
				test:Modernizr.canvas,
				nope:JS_URI+'excanvas.compiled.js'
			});

			// Placeholder属性のサポート
			if (!Modernizr.input.placeholder){
				console.log("Placeholder");
				$(prefix + '*[placeholder]').each(function () {
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

			// require属性のサポート
			if (!Modernizr.input.required){
				$(prefix + '[required]').each(function () {
					var input = $(this),
					required = input.attr('required');

					input.parents('form').submit(function () {
						if (input.val() === '') {
							return false;
						}
					});
				});
			}
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
			},
			ajax_load:function(func){
				pkwkAjaxLoad.push( func );
			}
		},
		// DOMの初期化
		init_dom : function(prefix, callback){
			var self = this;
			prefix = (prefix) ? prefix + ' ' : '';

			$(':input').attr('disabled','disabled');

			// 自動サブミット型の設定。
			$('form.autosubmit').change(function(){
				this.submit();
			});
			
			// テキストエリアでタブ入力できるように
			$(prefix + 'textarea').tabby();
			$(prefix + 'textarea[row=1]').autosize();

			// buttonタグは、data要素で処理をカスタマイズ
			$(prefix + 'button').each(function(){
				var $this = $(this);
				var data = $this.data();

				$this.button({
					text: data.text ? data.text : true,
					label: data.label ? data.label : this.value,
					icons: {
						primary : data.iconsPrimary,
						secondary: data.iconsSecondary
					}
				});
			});
			$(prefix + '.button').each(function(){
				var $this = $(this);
				var data = $this.data();
				//console.log(data.text);

				$this.button({
					text: data.text,
				//	label: data.label ? data.label : (data.text === true) ? $this.innerText : null,
					icons: {
						primary : data.iconsPrimary,
						secondary: data.iconsSecondary
					}
				});
			}).removeClass(prefix + 'button');

			// タブ/アコーディオン処理
			$(prefix + 'li[role=tab] a').each(function(){
				var $this = $(this);
				var href = $this.attr('href');
				$this.click(function(){
					return false;
				});

				if (href.match('#')){
					$this.data('disableScrolling',true);
				}else if (!href.match('ajax=raw')){
					// タブでリンクが貼られている場合は、ajaxは内部のHTMLを直接出力しなければならない。
					// したがって、明示的に部分的なHTMLを出力する。（/lib/html.phpを見よ）
					// リンク書き換えはこのスクリプトで行うため、プラグイン開発者はマークアップさえすれば問題ない。
					$this.attr('href',href+'&ajax=raw');
				}
			});
			$(prefix + '.tabs').tabs({
				beforeLoad: function( event, ui ) {
					ui.panel.html([
						'<div class="ui-state-highlight ui-corner-all">',
							'<p id="ajax_error"><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>'+$.i18n('dialog', 'loading')+'</p>',
						'</div>'
					].join("\n"));
					ui.jqXHR.global = false;
					ui.jqXHR.error(function() {
						ui.panel.html([
							'<div class="ui-state-error ui-corner-all">',
								'<p id="ajax_error"><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>'+$.i18n('dialog','error_page')+'</p>',
							'</div>'
						].join("\n"));
					});
				},
				load : function( event, ui ) {
					self.init_dom(ui.panel.id);
				}
			}).removeClass('tabs');
			$(prefix + '.accordion').accordion({
				ajaxOptions: {
					global:false,
					ajaxOptions: {
						beforeSend: function( event, ui ) {
							ui.panel.html($.i18n('dialog','loading'));
							ui.jqXHR.error(function() {
								ui.panel.html([
									'<div class="ui-state-error ui-corner-all">',
										'<p id="ajax_error"><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>'+$.i18n('dialog','error_page')+'</p>',
									'</div>'
								].join("\n"));
							});
						}
					}
				},
				spinner: $.i18n('dialog', 'loading'),
				load:function(event, ui) {
					self.init_dom('#' + ui.panel.id);
				}
			}).removeClass('tabs');

			// アップローダーに進捗状況表示（PHP5.4以降のみ）
			var $form = $(prefix+'form[enctype="multipart/form-data"]');
			if ($form.length !== 0 && $form.children('.progress_session').length !== 0){
				// Holds the id from set interval
				var interval_id = 0;
				var $progress = $('<div style="width:400px;display:block-inline;"></div>').progressbar().hide();
				$form.children('input[type="submit"]').before($progress);
	
				$form.submit(function(e){
					$form.children('input[type="submit"]').hide('blind');
					$progress.show('blind');
					// フォームに記入されているかを確認
					if ($form.children('input[type="file"]').val() == ''){
						e.preventDefault();
						return;
					}
					interval_id = setInterval(function() {
						$.getJSON(SCRIPT, {cmd : 'attach', pcmd : 'progress'}, function(data){
							console.dir(data);
							//if there is some progress then update
							if(data){
								$progress.progressbar({
									value: Math.round(100 * (data['bytes_processed'] / data['content_length']))
								});
								//$('#progress').val(data.bytes_processed / data.content_length);
								console.log('Uploading '+ Math.round((data.bytes_processed / data.content_length)*100) + '%');
							}else{
								//When there is no data the upload is complete
								$progress.progressbar({
									value: 100
								});
								clearInterval(interval_id);
								$progress.hide('blind');
								$form.children('input[type="submit"]').show('blind');
								console.log('Complete');
							}
						})
					}, 200);
					$(this).ajaxSubmit();
					e.preventDefault();
				});
			}

			// ダイアログ
			this.setAnchor(prefix);
			// シンタックスハイライト
			this.sh(prefix);
			// サジェスト（ページ名を提案）
			this.suggest(prefix);
			// Bad Behavior
			this.bad_behavior(prefix);
			// 用語集
			this.glossaly(prefix);
			// テーブルソート
			this.dataTable(prefix);

			this.set_widget_btn(prefix);

			// フォームロックを解除
			$(':input').removeAttr('disabled');
			// ボタンをjQuery UIのものに
			$(prefix + 'input[type=submit], '+prefix + 'input[type=reset], '+prefix + 'input[type=button]').button();

			$(prefix + '.buttonset').buttonset().removeClass('buttonset');

			if(typeof(callback) === 'function'){
				callback();
			}
		},
		set_widget_btn : function(prefix){
			prefix = (prefix) ? prefix + ' ' : '';
			// hover states on the static widgets
			$(prefix + '.pkwk_widget .ui-state-default').hover(function() {
				$(this).addClass('ui-state-hover');
			},function() {
				$(this).removeClass('ui-state-hover');
			}).mousedown(function() {
				$(this).addClass('ui-state-active');
			}).mouseout(function() {
				$(this).removeClass('ui-state-active');
			});
		},
		// アンカータグの処理
		setAnchor : function(prefix){
			var self = this;	// pukiwikiへのエイリアス

			$('.link_symbol').each(function(){
				var $this = $(this);
				$this.click(function(){
					window.open($this.parent().attr('href'));
					return false;
				});
			});

			$(prefix + 'a').each(function(){
				var $this = $(this);	// DOMをキャッシュ
				var href = $this.attr('href');
				if (!href) return;
				var ext = href.match(/\.(\w+)$/i);
				
				var rel = $this.attr('rel') ? $this.attr('rel') : null;
				if ($this.data('ajax') === false){
					return;
				}

				if (rel && rel.match(/noreferer|license|product|external/)){

					if (rel.match(/noreferer/)){
						$this.click(function(){
							// for IE
							if (ie){
								var subwin = window.open('','','location=yes, menubar=yes, toolbar=yes, status=yes, resizable=yes, scrollbars=yes,');
								var d = subwin.document;
								d.open();
								d.write('<meta http-equiv="refresh" content="0;url='+href+'" />');
								d.close();
							}
							// for Safari,Chrome,Firefox
							else{
								if ( href.match(/data:text\/html;charset=utf-8/) !== true ){
									var html = [
										'<html><head><script type="text/javascript"><!--',
										'document.write(\'<meta http-equiv="refresh" content="0;url='+url+'" />\');',
										'// --><'+'/script></head><body></body></html>'
									];
									$this.attr('href', 'data:text/html;charset=utf-8,'+encodeURIComponent(html.join("\n")));
								}
							}
							return false;
						});
					}else {
						$this.click(function(){
							if (ext[1]){
								switch (ext[1]) {
									case 'jpg': case 'jpeg': case 'gif': case'png':
										$this.rlightbox();
									break;
									case 'mp3':  case 'm4a': case 'm4v':
									case 'webma': case 'webmv': case 'wav':
									case 'oga': case 'ogv' : case 'fla': case 'flv':
										self.media_player(this, ext[1]);
									break;
									default :
										window.open(href);
									break;
								}
							}else{
								window.open(href);
							}
							return false;
						});
					}
				}else if (href){
					// 外部へのリンクは、rel=externalが付いているものとする。
					// Query Stringをパース。paramsに割り当て
					var params = {};
					var hashes = href.slice(href.indexOf("?") + 1).split('&');
					for(var i = 0; i < hashes.length; i++) {
						var hash = hashes[i].split('=');
						try{
							// スペースが+になってしまう問題を修正
							params[hash[0]] = decodeURIComponent(hash[1]).replace(/\+/g, ' ').replace(/%2F/g, '/');
						}catch(e){}
					}
					if (href.match('#') && href !== '#'){
						// アンカースクロールを無効化判定
						var disable_scrolling = ($this.data('disableScrolling') || $this.parent().attr('role')) ? true : false;

						if (disable_scrolling === false){
							// アンカースクロール
							$this.click(function(){
								var $body;
								if ($(window).scrollTop() === 0) {
									// スクロールが0の時エラーになる問題をごまかす
									$(window).scrollTop(1);
								}
								if ( $('html').scrollTop() > 0 ) {
									$body = $('html');
								} else if ( $('body').scrollTop() > 0 ) {
									$body = $('body');
								}else{
									return;
								}
								$body.stop().animate({
									scrollTop: $(href).offset().top
								},{
									duration : 800
								});
								return false;
							});
						}
					}else if (params.ajax === 'raw'){
						return false;
					}else if (params.cmd){
						// PukiWiki Adv.のプラグイン関連の処理
						if (params.pcmd == 'open' || params.openfile !== undefined){
							// 添付ファイルを開く（refによる呼び出しの場合とattachによる呼び出しの場合とでQueryStringが異なるのがやっかいだ）
							var filename;

							// アドレスの最後がファイル名になるようにする
							if (params.file){
								filename = params.file;
								$this.attr('href',SCRIPT+'?cmd='+params.cmd+'&pcmd='+params.pcmd+'&refer='+params.refer+'&age='+params.age+'&file='+filename);
							}else{
								filename = params.openfile;
								$this.attr('href',SCRIPT+'?cmd='+params.cmd+'&refer='+params.refer+'&openfile='+filename);
							}
							ext = filename.match(/\.(\w+)$/i);
							
							if (ext){
								switch (ext[1].toLowerCase()) {
									case 'jpg': case 'jpeg': case 'gif': case'png': // case'svg' : case 'svgz' :
										$this.rlightbox();
									break;
									case 'mp3':  case 'm4a': case 'm4v':
									case 'webma': case 'webmv': case 'wav':
									case 'oga': case 'ogv' : case 'fla': case 'flv':
										self.media_player(this, ext[1]);
									break;
								}
							}
						}else if (params.cmd == 'qrcode'){
							// QRcodeの場合
							$this.attr('href',href+'&type=.gif');
							$this.rlightbox();
						}else if (
							(params.cmd.match(/search|newpage|template|rename/i)) ||
							params.help === 'true' ||
							(params.cmd === 'attach' &&  params.pcmd !== 'list') ||
							(params.cmd === 'tb' && params.tb_id !== undefined) ||
							(params.cmd === 'table_edit2' ) ||
							(params.cmd.match(/attach|source|template|freeze|rename|diff|referer|logview|related/i) && typeof(params.page) !== 'undefined')
						){
							// その他の主要なプラグインは、インラインウィンドウで表示
							if (params.help == 'true'){
								params = {cmd:'read', page:'FormatRule'};
							}

							// ダイアログ描画処理
							$this.click(function(){
								params.ajax = 'json';
								self.ajax_dialog(params,prefix,function(){
									if ((params.cmd == 'attach' && params.pcmd.match(/upload|info/i)) || params.cmd.match(/attachref|read|backup/i) && params.age !== ''){
										self.init_dom(prefix + ' .window');
									}
								});
								return false;
							});
						}
					}
					
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
			prefix = (prefix) ? prefix + ' .window' : '.window';
			var self = this;	// pukiwikiへのエイリアス
			// ダイアログ設定
			var dialog_option = {
				modal: true,
				resizable: true,
				show: 'fade',
				hide: 'fade',
				width: '520px',
				dialogClass: 'ajax_dialog',
				bgiframe : (ie >= 6) ? true : false,	// for IE6
				open: function(){
					if(typeof(callback) === 'function'){ callback(); }

					// オーバーレイでウィンドウを閉じる
					var parent = this;
					self.init_dom(prefix);
					return false;
				},
				close: function(){
					$(this).remove();
				}
			};

			if (params.pcmd === 'upload'){
				dialog_option.width = '90%';
			}

			if (params.cmd.match(/logview|source|diff|edit|read|referer/i) || params.help === 'true'){
				dialog_option.width = '90%';
				dialog_option.height = $(window).height()*0.8|0;
			}

			// 非同期転送
			// 先にajax処理を行ない、通信完了したらダイアログの描画。
			// 体感速度的に重くなるが、先にダイアログを描画してしまうと、
			// その中に非同期通信の結果が描画されることになり、
			// ダイアログが下に伸びてしまう。
			var container = $('<div class="window"></div>');
			var content = '';
			$.ajax({
				dataType: 'json',
				url: SCRIPT,
				data : params,
				type : 'GET',
				cache : true
			}).
			done(function(data){
				// 通信成功時
				
				if (typeof(parse) === 'function') { data = parse(data); }

				// スクリプトタグを無効化
				if (data !== null){
						content = data.body.replace(/<script[^>]*>[^<]+/ig,'');
						dialog_option.title = data.title;
				}else{
					content = [
						'<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">',
							'<p id="ajax_error"><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>Data is Null!</p>',
						'</div>'
					].join("\n");
					dialog_option.title = $.i18n('dialog','error');
				}
			}).
			fail(function(data,status,thrown){
				// エラー発生
				var container = $('<div class="window"></div>');
				if (data.status === 401){
					status = $.i18n('dialog','error_auth');
				}else if (data.status === 302){
					document.location.reload();
				}else if (status === 'error'){
					status = $.i18n('dialog','error_page');
				}

				content = [
					'<div class="ui-state-error ui-corner-all">',
						'<p id="ajax_error"><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>'+status+'</p>',
					'</div>'
				].join("\n");
				dialog_option.title = $.i18n('dialog','error');
				dialog_option.width = 400;
				container.html(content).dialog(dialog_option);
				if (data.status === 500){
					try{
						$('#ajax_error').after([
							'<ul>',
							'<li>readyState:'+data.readyState+'</li>',
	//						'<li>responseText:'+data.responseText+'</li>',
							'<li>status:'+data.status+'</li>',
							'<li>statusText:'+data.statusText+'</li>',
							'</ul>'
						].join("\n"));
					}catch(e){}
				}
			}).
			always(function(){
				container.html(content).dialog(dialog_option);
			});
		},
		blockUI : function(dom){
			// jQueryUI BlockUI
			// http://pure-essence.net/2010/01/29/jqueryui-dialog-as-loading-screen-replace-blockui/
			var loading_widget = [
				'<div id="loading">',
					'<div class="ui-widget-overlay" style="position:fixed;"></div>',
					'<div id="loading_activity"></div>',
				'</div>'
			].join('');
			$(loading_widget).appendTo('body');
			$('#loading_activity').activity({segments: 12, width: 24, space: 8, length:64, color: 'black', speed: 1, zIndex: 9999});
			var $loading = $('#loading_activity');

			$(dom)
				.ajaxSend(function(e, xhr, settings) {
					// 画面中央にローディング画像を移動させる
					$loading.css({
						position : 'absolute',
						left : ($(window).width() / 2) + $(window).scrollLeft() - $loading.width() / 2,
						top : ($(window).height() / 2) + $(window).scrollTop() - $loading.height() / 2
					});
					$('#loading').fadeIn();
				})
				.ajaxStop(function(){
					$('#loading').fadeOut();
					var f;
					while( f = pkwkAjaxLoad.shift() ){
						if( f !== null ){
							f();
						}
					}
				}
			);
		},
		// Syntax Hilighter
		sh : function(prefix){
			var self = this;

			// シンタックスハイライトするDOMを取得
			var sh = (prefix) ? prefix + ' .sh' : '.sh';

			if ($(sh).length !== 0){
				// SyntaxHilighterを実行
				$.beautyOfCode.beautifyAll({target:sh});
			}
		},
		dataTable : function(prefix){
			var self = this;
			var table = (prefix) ? prefix + ' .style_table' : '.style_table';
			$(table).each(function(){
				var $this = $(this);
				var sortable = (typeof($this.data('sortable')) === 'undefined' || $this.data('sortable') === true) ? true : false;
				if ($this.find('thead').length !== 0 && sortable){
					var pagenate = (typeof($this.data('pagenate')) === 'undefined' || $this.data('pagenate') === false) ? false : true;
					$this.dataTable({
						bPaginate : pagenate,
						sDom: (pagenate) ? '<"H"pi>tr<"F"lf>' : 'tr',
						aaSorting: ! $this.data('filter') ? [] : $this.data('filter')
					});
				}
			});
		},
		// 検索フォームでサジェスト機能
		suggest: function(prefix){
			var form = (prefix) ? prefix + ' .suggest' : '.suggest';
			if ($(form).length !== 0){
				var cache = {},lastXhr, xhr;
				$(form).autocomplete({
					minLength: 4,
					source: function( request, response ) {
						var term = request.term;
						if ( term in cache ) {
							response( cache[ term ] );
							return;
						}
						lastXhr = $.ajax({
							url: SCRIPT,
							data: {
								cmd: 'list',
								term: term,
								type:'json'
							},
							method: 'get',
							dataType: 'json',
							global: false
						}).
						done(function( data, status, xhr ) {
							cache[ term ] = data;
							if ( xhr === lastXhr ) {
								response( data );
							}
						});
					}
				});
			}
		},
		// ミュージックプレイヤー（拡張子が.mp3や.oggなどといったFlashで再生できるものに限る）
		media_player: function(target, ext){
			var jplayer_container = [
				'<div id="jplayer"></div>',
				'<div id="jp-container">',
					'<ul class="ui-widget pkwk_widget">',
						'<li class="jp-play ui-button ui-state-default ui-corner-all" title="' + $.i18n('player','play') + '"><span class="ui-icon ui-icon-play"></span></li>',
						'<li class="jp-video-play ui-button ui-state-default ui-corner-all" title="' + $.i18n('player','play') + '"><span class="ui-icon ui-icon-video"></span></li>',
						'<li class="jp-pause ui-button ui-state-default ui-corner-all" title="' + $.i18n('player','pause') + '"><span class="ui-icon ui-icon-pause"></span></li>',
						'<li class="jp-stop ui-button ui-state-default ui-corner-all" title="' + $.i18n('player','stop') + '"><span class="ui-icon ui-icon-stop"></span></li>',
						'<li><div class="jp-cast">',
							'<span class="ui-icon ui-icon-clock" style="display:inline-block">Cast:</span>',
							'<span class="jp-current-time">00:00</span>/<span class="jp-duration">??:??</span>',
						'</div></li>',
						'<li style="width:300px;"></li>',
						'<li class="jp-full-screen ui-button ui-state-default ui-corner-all" title="Full screen"><span class="ui-icon ui-icon-arrow-4-diag"></span></li>',
						'<li class="jp-mute ui-button ui-state-default ui-corner-all" title="' + $.i18n('player','volume_min') + '"><span class="ui-icon ui-icon-volume-off"></span></li>',
						'<li class="jp-unmute ui-button ui-state-default ui-corner-all" title="' + $.i18n('player','volume_max') + '"><span class="ui-icon ui-icon-volume-on"></span></li>',
						'<li style="width:200px;"></li>',
					'</ul>',
					'<div class="jp-bars" title="' + $.i18n('player','seek') + '">',
						'<div class="jp-playback"></div>',
					//	'<div class="jp-loader"></div>',
					'</div>',
					'<div title="' + $.i18n('player','volume') + '" class="jp-volume"></div>',
				'</div>'
			].join("\n");

			$(target).each(function(){
				var file = $(this).attr('href');
				var media = {};
				media[ext] = file;
				var dialog_option = {
					modal: true,
					resizable: false,
					show: 'fade',
					hide: 'fade',
					title: 'Music Player',
					dialogClass: 'music_player',
					width:'680px',
					bgiframe : (ie > 6) ? true : false,	// for IE6
					open: function(){
						var self = this;
						// Local copy of jQuery selectors, for performance.
						var	$jPlayer = $('#jplayer'),
							$playback = $('#jp-container .jp-playback'),
							$loader = $('#jp-container .jp-loader'),
							$play = $('#jp-container .jp-play'),
							$pause = $('#jp-container .jp-pause')
						;
						
						$playback.slider({range: 'min',animate:true});

						// Instance jPlayer
						$jPlayer.jPlayer({
							swfPath: SKIN_DIR,
							cssSelectorAncestor: '#jp-container',
							supplied: ext,
							wmode: 'window',
							volume: ($.cookie('volume')) ? $.cookie('volume') : 100,
							ready: function (event) {
								$(this).jPlayer('setMedia',media);
								$(self).dialog('option','title', file);
								// Slider
								$playback.slider({
									max: parseInt(event.jPlayer.status.duration),
									value: parseInt(event.jPlayer.status.currentTime),
									slide: function(event, ui) {
										$jPlayer.jPlayer('play', ui.value);
									}
								});
								$('#jp-container .jp-volume').slider({
									value : ($.cookie('volume')) ? $.cookie('volume') : 100,
									min: 0,
									max: 100,
									range: 'min',
									animate: true,
									slide: function(event, ui) {
										$jPlayer.jPlayer('volume', ui.value * 0.01);
										$.cookie('volume', ui.value, {expires:30, path:'/'});
									}
								});
								
								$play.click();
							},
							load: function(event){
								$play.click(function() {
									$jPlayer.jPlayer('play');
									return false;
								});
								$pause.click(function() {
									$jPlayer.jPlayer('pause');
									return false;
								});
								$('#jp-container .jp-stop').click(function() {
									$jPlayer.jPlayer('stop');
									return false;
								});

							},
							timeupdate: function(event) {
								$playback.slider('option', 'value', parseInt(event.jPlayer.status.currentTime));
								$playback.slider('option', 'max',  parseInt(event.jPlayer.status.duration));
							},
							play: function(event) {
								$play.fadeOut(function(){
									$pause.fadeIn();
								});
								$playback.slider('option', 'max',  parseInt(event.jPlayer.status.duration));
							},
							pause: function(event) {
								$pause.fadeOut(function(){
									$play.fadeIn();
								});
							},
							ended: function(event) {
								$pause.fadeOut(function(){
									$play.fadeIn();
								});
							}
						});
						$jPlayer.jPlayer('volume', ($.cookie('volume')) ? $.cookie('volume') : 100);
						//hover states on the static widgets
						$('#jp-content ul li').hover(
							function() { $(this).addClass('ui-state-hover'); },
							function() { $(this).removeClass('ui-state-hover'); }
						);
						return false;
					},
					close: function(){
						$(this).remove();
					}
				};

				$(this).click(function(){
					var container = $('<div id="music_player"></div>');
					container.html(jplayer_container).dialog(dialog_option);
					return false;
				});
			});
		},
		// 独自のGlossaly処理
		glossaly: function(prefix){
			var glossaries = {};	// ajaxで読み込んだ内容をキャッシュ
			$(document).tooltip({
				items: '[aria-describedby], [title]',
				track: true,
				content: function(callback) {
					var $this = $(this);
					if ( $this.is('[aria-describedby]') ) {
						$this.removeAttr('title');
						var aria = $this.attr('aria-describedby');	// aria-describedby要素が他のjQueryUIのウィジットで使われてたorz...
						if (aria === 'linktip' || aria === 'tooltip') {
							//console.log($this.attr('aria-describedby'));
							var text = $this.text();
							if (text !== '' && !glossaries[text]){
								// キャッシュがない場合
								var params = ($this.attr('aria-describedby') == 'linktip')
								 ? {
									// リンク先の要約文
									cmd : 'preview',
									page : text,
									word : $.query.get('word'),
									cache : true
								} : {
									// 用語集
									cmd: 'tooltip',
									q : text,
									cache : true
								};
								
								$.ajax({
									url:SCRIPT,
									type:'GET',
									cache: true,
									timeout:2000,
									dataType : 'xml',
									global:false,
									data : params,
									async:false,
									beforeSend: function(){
										$('body').css('cursor','wait');
									},
									complete: function(){
										$('body').css('cursor','auto');
									}
								}).done(function(data){
									if (data.documentElement.textContent) {
										glossaries[text] = data.documentElement.textContent;
										callback(data.documentElement.textContent);
									}
								}).always(function(data){
									if (data.responseText) {
										glossaries[text] = data.responseText;
										callback(data.responseText);
									}
								});
							}else{
								// キャッシュから内容を呼び出す
								return glossaries[text];
							}
						}
						
					}else if ( $this.is('[title]')){
						return $this.attr('title');
					}
					// ツールチップが複数表示されてしまうのを抑止
					if ( $('[role="tooltip"]').length > 2 ){
						$('[role="tooltip"]').remove();
					}
				}
			});
		},
		// 入力アシスタント
		assistant: function(full){
			var i, len;

			// アシスタントのウィジット
			$('.assistant').html([
				'<ul class="ui-widget pkwk_widget" role="widget">',
					'<li class="replace ui-button ui-state-default ui-corner-left" title="'+$.i18n('editor','bold')+'" name="b" role="button"><strong>b</strong></li>',
					'<li class="replace ui-button ui-state-default" title="'+$.i18n('editor','italic')+'" name="i" style="font-weight:normal;" role="button"><em>i</em></li>',
					'<li class="replace ui-button ui-state-default" title="'+$.i18n('editor','strike')+'" name="s" style="font-weight:normal;" role="button"><strike>s</strike></li>',
					'<li class="replace ui-button ui-state-default" title="'+$.i18n('editor','underline')+'" name="u" style="font-weight:normal;" role="button"><span class="underline">u</span></li>',
					'<li class="replace ui-button ui-state-default" title="'+$.i18n('editor','code')+'" name="code" style="font-weight:normal;" role="button"><span class="ui-icon ui-icon-script"></span></li>',
					'<li class="replace ui-button ui-state-default ui-corner-right" title="'+$.i18n('editor','quote')+'" name="q" style="font-weight:normal;" role="button"><span class="ui-icon ui-icon-comment"></span></li>',
					'<li class="replace ui-button ui-state-default ui-corner-left" title="'+$.i18n('editor','link')+'" name="url"><span class="ui-icon ui-icon-link" role="button"></span></li>',
					'<li class="replace ui-button ui-state-default" title="'+$.i18n('editor','size')+'" name="size" role="button">size</li>',
					'<li class="insert ui-button ui-state-default ui-corner-right" title="'+$.i18n('editor','color')+'" name="color" role="button">color</li>',
					'<li class="insert ui-button ui-state-default ui-corner-left" title="'+$.i18n('editor','emoji')+'" name="emoji" role="button">☺</li>',
					'<li class="insert ui-button ui-state-default ui-corner-right" title="'+$.i18n('editor','breakline')+'" name="br" role="button">⏎</li>',
					'<li class="replace ui-button ui-state-default ui-corner-all" title="'+$.i18n('editor','ncr')+'" name="ncr" role="button">&amp;#</li>',
					'<li class="insert ui-button ui-state-default ui-corner-all" title="'+$.i18n('editor','hint')+'" name="help" role="button"><span class="ui-icon ui-icon ui-icon-lightbulb"></span></li>',
					(Modernizr.localstorage) ? '<li class="insert ui-state-default ui-corner-all" title="'+$.i18n('editor','flush')+'" name="flush" role="button"><span class="ui-icon ui-icon-trash"></span></li>': null,
					'<li class="ui-widget-content ui-corner-all" style="float:right; width:auto;display:none;font-weight:normal;" id="indicator"></li>',
				'</ul>'
			].join("\n"));

			// 絵文字パレットのウィジット
			var emoji_widget = '<ul class="ui-widget pkwk_widget ui-helper-clearfix">';
			for(i = 0, len = this.assistant_setting.emoji.length; i < len ; i++ ){
				var name =  this.assistant_setting.emoji[i];
				emoji_widget += '<li class="ui-button ui-state-default ui-corner-all" title="'+name+'" name="'+name+'"><span class="emoji emoji-'+name+'"></span></li>';
			}
			emoji_widget += '</ul>';
			$(document.body).append('<div id="emoji"></div>');
			$('#emoji').dialog({
				title:$.i18n('editor','emoji'),
				autoOpen:false,
				bgiframe: true,
				minWidth:410,
				height:410,
				minHeight:410,
				show: 'scale',
				hide: 'scale'
			}).html(emoji_widget);

			// カラーパレットのウィジット
			var color_widget = '<ul class="ui-widget pkwk_widget ui-helper-clearfix" id="colors">', j=0;
			for(i = 0, len =  this.assistant_setting.color.length; i < len ; i++ ){
				var color = this.assistant_setting.color[i];
				color_widget += '<li class="ui-button ui-state-default" title="'+color+'" name="'+color+'"><span class="emoji" style="background-color:'+color+';"></span></li>';
				j++;
			}
			color_widget += '</ul>';
			$(document.body).append('<div id="color_palette"></div>');
			$('#color_palette').dialog({
				title:$.i18n('editor','color'),
				autoOpen:false,
				bgiframe: true,
				width:470,
				height:400,
				show: 'scale',
				hide: 'scale'
			}).html(color_widget);

			// ヒントのウィジット
			$(document.body).append('<div id="hint"></div>');
			$('#hint').dialog({
				title:$.i18n('editor','hint'),
				autoOpen:false,
				bgiframe: true,
				width:470,
				show: 'scale',
				hide: 'scale'
			}).html($.i18n('pukiwiki','hint_text1'));

			// ここから、イベント割り当て
			if (!this.elem){ this.elem = $('*[name=msg]')[0]; }
			var self = this;
			// アシスタント

			$('*[name=msg]').focus(function(e){
				self.elem = this;
				self.selection = $(this).getSelection().text;
				return;
			});

			$('.insert').click(function(){
				var ret = '';
				var elem = $(self.elem);
				var str = elem.getSelection().text;
				var v = $(this).attr('name');

				switch (v){
					case 'help' :
						$('#hint').dialog('open');
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
						if (Modernizr.localstorage && confirm($.i18n('pukiwiki','flush_restore')) === true){
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
				return false;
			});

			$('.replace').click(function(){
				var ret = '';
				var $elem = $(self.elem);
				var str = $elem.getSelection().text;
				var v = $(this).attr('name');

				if (str === ''|| !$elem){
					alert( $.i18n('pukiwiki', 'select'));
					return false;
				}

				$elem.focus();

				switch (v){
					case 'size' :
						var default_size = '100%';
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
				}
				$elem.replaceSelection(ret);
				// console.log(str);
				return false;
			});

			$('#emoji ul li').click(function(){
				var $elem = $(self.elem);
				var str = $elem.getSelection().text;
				var v = '&('+$(this).attr('name')+');';;

				if (str === ''){
					$elem.insertAtCaretPos(v);
				}else{
					$elem.replaceSelection(v);
				}
				$elem.focus();
		//		$('#emoji').dialog('close');
				return;
			});
			$('#color_palette ul li').click(function(){
				var ret;
				var $elem = $(self.elem);
				var str = $elem.getSelection().text;
				var v = $(this).attr('name');

				if (str === ''|| !$elem){
					alert( $.i18n('pukiwiki', 'select'));
					return;
				}

				if (str.match(/^&color\([^\)]*\)\{.*\};$/)){
					$elem.replaceSelection(str.replace(/^(&color\([^\)]*)(\)\{.*\};)$/,"$1," + v + "$2"));
				}else{
					$elem.replaceSelection('&color(' + v + '){' + str + '};');
				}
				$('#color_palette').dialog('close');
				$elem.focus();

				return;
			});
		},
		// 編集画面のフォームを拡張
		set_editform: function(prefix){
			prefix = (prefix) ? prefix + ' ': '';
			var self = this;
			var isEnableLocalStorage = false;
			var original_text = $('#original').val();	// オリジナルのテキストをキャッシュ
			
			// HTMLエンコード
			var htmlsc = function(ch) {
				if (typeof(ch) === 'string'){
					ch = ch.replace(/&/g,"&amp;");
					ch = ch.replace(/\"/g,"&quot;");
					ch = ch.replace(/\'/g,"&#039;");
					ch = ch.replace(/</g,"&lt;");
					ch = ch.replace(/>/g,"&gt;");
				}
				return ch;
			};
			
			// 簡易差分表示用ダイアログ
			$(document.body).append('<div id="diff"></div>');
			$('#diff').dialog({
				title:$.i18n('editor','diff'),
				autoOpen:false,
				bgiframe: true,
				width:'90%',
				height: $(window).height()*0.8|0,
				show: 'scale',
				hide: 'scale'
			});
			$('#diff').html('<pre>'+htmlsc(original_text)+'</pre>');

			// JSON.parseが使えるかのチェック
			Modernizr.load({
				test:window.JSON,
				nope: JS_URI+'json2.js',
				complete: function () {
					// テキストエリアの内容をlocalStrageから取得
					if (Modernizr.localstorage){
						var msg = $(prefix + '.edit_form textarea[name=msg]').val();
						var storage = window.localStorage.getItem(PAGE);
						var data = window.JSON.parse(storage);

						if (data){
							// 「タイムスタンプを更新しない」で更新した場合、それを検知する方法がないという致命的問題あり。
							var ask = (MODIFIED > data.modified && data.msg !== msg) ?
								$.i18n('pukiwiki','info_restore1') : $.i18n('pukiwiki','info_restore2');

							// データーを復元
							if (confirm(ask)){ $(prefix + '.edit_form textarea[name=msg]').val(data.msg); }
						}

						isEnableLocalStorage = true;
					}
				}
			});

	//		$('.edit_form input','.edit_form button','.edit_form select','.edit_form textarea').attr('disabled','disabled');

			this.ajax_apx = false;
			this.ajax_count = 0;
			this.ajax_tim = 0;

			var $form = $(prefix + 'form .edit_form');

			$form.children('input[name="write"]')
				.after(
					// 簡易差分表示ボタンを追加
					$('<input type="button" name="view_diff" value="' + $.i18n('editor','diff') + '" accesskey="d" />')
					.button()
					.click(function() {
						$('#diff').dialog('open');
					})
				)
				.after(
					// プレビューボタンを書き換え
					'<input type="button" name="add_ajax" value="' + $('.edit_form input[name=preview]').attr('value') + '" accesskey="p" />'
				);

			// オリジナルのプレビューボタンを削除
			$form.children('input[name=preview]').remove();
			
			// アシスタントのツールバーを前に追加
			$form.prepend('<div class="assistant ui-corner-top ui-widget-header ui-helper-clearfix"></div>');
			
			// リアルタイムプレビューの表示画面
			$form.children('textarea[name="msg"]').before('<div id="realview" style="display:none;"><div></div></div><textarea id="previous" style="display:none;"></textarea>');

			// よく使うDOMをキャッシュ
			var $indicator = $form.children('#indicator'),
				$msg = $form.children('textarea[name="msg"]'),
				msg_height = $msg.height(),
				$original = $form.children('textarea[name="original"]'),
				$previous = $form.children('#previous'),
				$realview = $form.children('#realview'),
				$textarea = $form.children('textarea');
			
			// リアルタイムプレビューの内部処理
			var realtime_preview = function(){
				var oSource = document.getElementById('msg');
				var source = $msg.val();
				var sttlen, endlen, sellen, finlen;

				if (self.real_preview_mode) {
					$indicator.html('');
					$indicator.activity({segments: 8, width:2, space: 0, length: 3, color: 'black'});
					$previous.val(source);
					$textarea.attr('disabled', 'disabled');

					if (++self.ajax_count !== 1){ return; }
					var finlen = source.lastIndexOf("\n",$msg.getSelection().start);

					$.ajax({
						url : SCRIPT,
						type : 'post',
						global:false,
						data : {
							cmd : 'edit',
							realview : 1,
							page : PAGE,
							// 編集した位置までスクロールさせるための編集マークプラグイン呼び出しを付加
							msg : source.substring(0,finlen) +"\n\n" + '&editmark;' + "\n\n" + source.substring(finlen),
							type : 'json'
						},
						cache : false,
			//			timeout : 2000,//タイムアウト（２秒）
						dataType : 'json'
					}).
					done(function(data){
						var $holder = $realview.children('div');
						$indicator.html('<span class="ui-icon ui-icon-clock" style="float:left;"></span>'+data.taketime);
						var ret = data.data.replace(/<script[^>]*>[^<]+/ig,'<span class="scripttag" title="Script tag">[SCRIPT]</span>');
						$holder.html(data.data);

						/*
						console.log($holder.children('#editmark').offset().top);
						if ($holder.scrollTop() === 0) {
							// スクロールが0の時エラーになる問題をごまかす
							$holder.scrollTop(1);
						}
						$holder.animate({
							scrollTop: $holder.children('#editmark').offset().top-4
						});
						*/
						
						var marker = document.getElementById('editmark');
						if (marker){ document.getElementById('realview').scrollTop = marker.offsetTop-4; }
						

						if (self.ajax_count===1) {
							self.ajax_count = 0;
						} else {
							self.ajax_count = 0;
							realtime_preview();
						}
						$textarea.removeAttr('disabled');
					}).
					fail(function(data,status,thrown){
						$realview.children('div').html([
							'<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">',
								'<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>'+$.i18n('pukiwiki','error')+status+'</p>',
								'<ul>',
									'<li>readyState:'+data.readyState+'</li>',
									'<li>responseText:'+data.responseText+'</li>',
									'<li>status:'+data.status+'</li>',
									'<li>statusText:'+data.statusText+'</li>',
								'</ul>',
							'</div>'].join("\n")
						);
					});
				}
			};

			$realview.height(msg_height/2);
			// プレビューボタンが押された時の処理
			$form.children('input[name=add_ajax]').click(function(){
				$textarea.attr('disabled', 'disabled');
				// フォームの高さを取得
				// Textarea Resizerで高さが可変になっているため。

				if (self.real_preview_mode === true) {
					// もとに戻す
					self.real_preview_mode = false;
					// realview_outerを消したあと、フォームの高さを２倍にする
					// 同時でない理由はFireFoxで表示がバグるため
					$realview.animate({
						height:0
					}, function(){
						$msg.animate({height:msg_height});
						$realview.hide();
						$indicator.hide('slow');
						$textarea.removeAttr('disabled');
					});
				} else {
					self.real_preview_mode = true;
					$indicator.activity({segments: 8, width:2, space: 0, length: 3, color: 'black'});
					$indicator.show();
					// フォームの高さを半分にしたあと、realviewを表示
					$msg.animate({
						height: msg_height/2
					},function(){
						$realview.animate({ height:msg_height/2});
						// Realedit用のDOMを生成
						$realview.show();
						// 初回実行時、realview_outerの大きさを、フォームの大きさに揃える。
						// なお、realview_outerの高さは、フォームの半分とする。
						$textarea.removeAttr('disabled');
					});
					// 現在のプレビューを出力
					realtime_preview();
				}
				return false;
			});

			// textareaのイベントリスナ
			$msg
				.blur(function(){
					// マウスが乗っかった時
					realtime_preview();
				})
				.mouseup(function(){
					// 前の値と異なるとき
					if ($(this).val() !== $original.val()){
						realtime_preview();
					}
				})
				.keypress(function(elem){
					// テキストエリアの内容をlocalStrageにページ名と共に保存
					if (isEnableLocalStorage){
						var d=new Date();
						window.localStorage.setItem(PAGE, JSON.stringify({
							msg : $(this).val(),
							modified: d.getTime()
						}));
					}
				})
				.change(function(){
					// edit.js v 00 -- pukiwikiの編集を支援
					// Copyright(c) 2010 mashiki
					// License: GPL version 3
					// http://mashiki-memo.blogspot.com/2010/12/blog-post.html

					// show_diff(原文,現在の内容（通常$('textarea[name=msg]').value）)
					var diff = self.diff(
						original_text.replace(/^\n+|\n+$/g,'').split("\n"),
						$(this).val().replace(/^\n+|\n+$/g,'').split("\n")
					);
					var d = [],ret = [];
					for (var i=0; d=diff[i]; ++i) {
						var line = htmlsc(d.arr[d.line]);
						switch(d.edit){
							case '+':
								//ret[i] = '+'+line;
								ret[i] = '<ins class="diff_added">'+line+'</ins>';
							break;
							case '-':
								//ret[i] = '-'+line;
								ret[i] = '<del class="diff_removed">'+line+'</del>';
							break;
							default:
								//ret[i] = ' '+line;
								ret[i] = line;
							break;
						}
					}
					$('#diff').html('<pre>'+ret.join("\n")+'</pre>');
				})
			;

			// 送信イベント時の処理
			$form.submit(function(e){
				var postdata = $(this).serializeObject();	// フォームの内容をサニタイズ
				postdata.ajax = 'json';
				// ローカルストレージをフラッシュ
				if (isEnableLocalStorage){
					localStorage.removeItem(PAGE);
				}
				
				// 空更新は無反応
				console.log($form.children('textarea[name="original"]').val());
				console.log($form.children('textarea[name="msg"]').val());
				
				if ( $original.val() == $msg.val() ){
					console.error("Void updating");
					e.preventDefault();
					return;
				}
				
				// 管理パスが入力されてない状態でタイムスタンプを更新しないになっている場合は、無反応
				if ( $form.children('input[name="pass"]').length !==0 && ($form.children('checkbox[name="notimestamp"]').is(':checked') && $form.children('input[name="pass"]') === '')){
					console.error("Password missing");
					e.preventDefault();
					return;
				}

				if (typeof(FACEBOOK_APPID) !== 'undefined'  && ! $form.children('checkbox[name="notimestamp"]').is(':checked') ) {
					if ( $form.children('checkbox[name="fb_publish"]') === true){
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
								$(prefix + 'input, button, select, textarea').removeAttr('disabled');
								alert($.i18n('pukiwiki','error'));
							}
						});
					}
				}

				/*
				console.log(postdata);
				$.ajax({
					url:SCRIPT,
					type:'POST',
					data: postdata,
					cache: false,
					dataType : 'json',
					success : function(data){
						// localStrageをフラッシュ（キャンセルボタンを押した場合も）

						//console.log(data.body);
						
					},
					error : function(data){
						$(prefix + 'input, button, select, textarea').removeAttr('disabled');
						alert($.i18n('pukiwiki','error'));
					}
				});
				*/

				$(this).ajaxSubmit();
				e.preventDefault();
			});

			this.assistant(false);
		},
		diff : function (arr1, arr2, rev) {
			var len1=arr1.length,
				len2=arr2.length;
			// len1 <= len2でなければひっくり返す
			if (!rev && len1>len2){
				return this.diff(arr2, arr1, true);
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
				for(k = -p	   ; k <  delta; ++k){ snake(k); }
				for(k = delta + p; k >= delta; --k){ snake(k); }
			}
			return ed[delta + offset];
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
						this.next_title = link.title ? link.title : $.i18n('dialog', 'next');
						break;
					case 'prev':
					case 'previous':
						this.prev = link.href;
						this.prev_title = link.title ? link.title : $.i18n('dialog', 'previous');
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
						break;
					case 'home':
						this.home = link.href;
						this.home_title = link.title;
						break;
					case 'search':
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
			$(document).keypress(function(elem){
				var key = elem.which;	// 押されたキーのコードを取得
				var key_label = String.fromCharCode(key);	// キーのラベル（ESCキーなどは取得できない）

				if(elem.target.nodeName.match(/(input|textarea)/i) !== -1){
					if (key === 27){return false;}
					return true;	// inputタグ、textareaタグ内ではキーバインド取得を無効化（ただしESCキーは除外）
				}

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
									self._hideToc(); //Esc, slash
	/*
								}else if(key >= 48 && key <=57){ //0-9
									key -= 48;
									if(self.nkeylink[key]){
									//	location.href = '#' + self.nkeylink[key];
										self.anchor_scroll(self.nkeylink[key],false);
										_hideToc();
									}
	*/
								}
							}else{
								if(key === 47) {
									$(self.toc)
										.css('top',$(window).scrollTop() + 'px')
										.css('left',$(window).scrollLeft() + 'px')
										.fadeIn('fast');	// /キー

								}
							}
						}
					break;
				}
			});
		},
		prepHdngs : function(prefix){
			prefix = (prefix) ? prefix + ' ': '';
			var lis = '';
			var hd = $(prefix + 'h2');
			var tocs = $(prefix + '.contents ul').removeAttr('class').removeAttr('style');

	//		var ptocImg = '<img src="'+this.image_dir+'toc.png" class="tocpic" title="Table of Contents of this page" alt="Toc" />';
	//		var ptocMsg = 'Click heading, and Table of Contents will pop up';
			var ptocImg = '<span class="pkwk-icon icon-toc" title="Table of Contents of this page" style="display:none; cursor:pointer;"></span>';
			var self = this;
			if(tocs.length !== 0){
				// #contentsが呼び出されているときは、その内容をTocに入れる。
				lis = '<ol>'+tocs.html()+'</ol>';
				hd.each(function(index){
					$(this).html($(this).html()+ptocImg);
				});
				// 一応h3にもアイコンいれたほうがいいかな。
				$(prefix + 'h3').each(function(index){
					$(this).html($(this).html()+ptocImg);
				});
	/*
				$(prefix + 'h4').click(function(index){
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
				$(prefix + '#top').html($(prefix + '#top').html()+ptocImg);
			}

			return lis;
		},
		/** generate the popup TOC division */
		genTocDiv : function(lis){
			var self = this;
			this.toc = document.createElement('div');
			this.toc.id = 'poptoc';
			$(this.toc)
				.addClass('ui-widget ui-corner-all noprint')
				.css('top',0)
				.css('left',0)
				.css('position','absolute')
				.css('z-index',100)
				.css('display','none')
				.html([
				'<h1><a href="#">'+$('h1#title').text()+'</a><abbr title="Table of Contents">[TOC]</abbr></h1>',
				lis.replace(/href/g,'tabindex="1" href'),
				this.getNaviLink()
			].join(''))
				.click(function(){
					self._hideToc();
				}
			);
			$('body').append(this.toc);

			var toc_bg = document.createElement('div');
			toc_bg.id='toc_bg';
			$(toc_bg)
				.addClass('ui-widget-overlay')
				.css('top',0)
				.css('left',0)
				.css('width','100%')
				.css('height','100%')
				.css('position','fixed')
				.css('z-index',99)
				.css('display','none')
				.click(function(){
					self._hideToc();
				}
			);
			$('body').append(toc_bg);

			this.calcObj(this.toc,300);

			$('.icon-toc').click(function(elem){
				self._dispToc(elem,this,false);
			}).css('display','inline-block');

			$('#poptoc *').click(function(){
				self._hideToc();
			});
		},
	/** determin the size of popup TOC */
		calcObj : function(o, maxw){
			var orgX = self.pageXOffset;
			var orgY = self.pageYOffset;

			o.style.visibility = 'hidden';
			o.style.display = 'block';
			o.width = o.offsetWidth;
			if(o.width > maxw){
				o.width = maxw;
				// if(!$.browser.safari){ o.style.width = maxw + 'px'; }	// Safari xhtml+xml
			}
			o.height = o.offsetHeight;
			o.style.display = 'none';
			o.style.visibility = 'visible';
			if(orgY){ scroll(orgX,orgY); }
		},
	/** if the page has prev/next link(s)... */
		getNaviLink : function(){
			var genNavi = function(name,href,title){
				if (href){
					return '<a href="' + href + '" title="' + title + '">'+name+'</a>';
				}else{
					return '<span style="color:gray; cursor: not-allowed;">'+name+'</span>';
				}
			};
			var navi = [
				'&lt;&lt;',
				genNavi('Prev page', this.prev, this.prev_title),
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

			return (navi ?  '<p class="nav">' + navi + '</p>' : '');
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
		//-- click event handlers ----
	/** Show/hide TOC on click event according to its location */
		popToc : function(ev){
			var tg;
			if (!ev){ ev = event; }
	/*
			tg = (window.event) ? ev.srcElement : ev.target;
			if(ev.altKey){
				pukiwiki._dispToc(ev,tg,0);
			}else{
				pukiwiki._hideToc(ev);
			}
	*/
			$('.icon-toc').click(function(){
				pukiwiki._dispToc(ev,this,0);
			});

			$('#poptoc' , '#poptoc' + ' a', '#toc_bg').click(function(){
				pukiwiki._hideToc();
			});
		},
		_dispToc : function(ev,tg,isKey){

			var doc = {
				x:ev.clientX + $(window).scrollLeft(),
				y:ev.clientY + $(window).scrollTop()
			};
			var scr = {
				x: ev.pageX,
				y: ev.pageY,
				w: $(window).width(),
				h: $(window).height()
			};

			var h = this.toc.height;
			var w = this.toc.width;
			var top;

			if(scr.h < h){
				$(this.toc)
					.css('height', scr.h + 'px')
					.css('overflow', 'auto');
				top = (doc.y - scr.y);
			}else{
				top = ((scr.h - scr.y > h) ? doc.y : ((scr.y > h) ? (doc.y - h) :((scr.y < scr.h/2) ? (doc.y - scr.y) : (doc.y + scr.h - scr.y - h))));
			}

			$(this.toc)
				.css('top', scr.y + 'px')
				.css('left',((scr.x < scr.w - w) ? scr.x : (scr.x - w)) + 'px');

			if(isKey){
				$(this.toc).fadeIn('fast');
				this.setCurPos(tg,type);
			}else{
				$(this.toc).show('slow');
			}
			$('#toc_bg').fadeIn('fast');

		},
		_hideToc : function(ev){
			$('#toc_bg').fadeOut('fast');
			$(this.toc).fadeOut('fast');
		},
	// Bad Behavior
		bad_behavior: function(prefix){
			prefix = (prefix) ? prefix + ' ': '';

			if (typeof(BH_NAME) !== 'undefined' && typeof(BH_VALUE) !== 'undefined'){
				$(prefix + 'form').append('<input type="hidden" name="'+BH_NAME+'" value="'+BH_VALUE+'" />');
			}
		},
		social : function(settings){
			if (pukiwiki.isPage){
				var href = $('link[rel=canonical]')[0].href;	// 正規化されたURL
				var lang = $('html').attr('lang');
				var html = [], scripts = [];

				var social = $.extend({},{
					// Hatena
					// http://b.hatena.ne.jp/guide/bbutton
					'hatena' : {
						use : true,
						dom : '<a href="http://b.hatena.ne.jp/entry/" class="hatena-bookmark-button" data-hatena-bookmark-layout="standard">Hatena</a>',
						script : 'http://b.st-hatena.com/js/bookmark_button_wo_al.js'
					},
					// Mixi
					// http://developer.mixi.co.jp/connect/mixi_plugin/mixi_check/spec_mixi_check/
					'mixi' : {
						use : false,
						dom : '<a href="http://mixi.jp/share.pl" class="mixi-check-button" data-url="'+href+'" data-button="button-1">Mixi</a>',
						script : 'http://static.mixi.jp/js/share.js'
					},
					// Google +1 button
					// http://www.google.com/intl/ja/webmasters/+1/button/index.html
					'google+1' : {
						use : true,
						dom : '<div class="g-plusone" data-size="medium">Google+1</div>',
						script: 'https://apis.google.com/js/plusone.js'
					},
					// Tweet Button
					// https://twitter.com/about/resources/buttons
					'twitter' : {
						use : true,
						dom : '<a href="https://twitter.com/share" class="twitter-share-button" data-lang="' + lang+'">Tweet</a>',
						script : 'http://platform.twitter.com/widgets.js'
					},
					// Gree
					// https://developer.gree.net/connect/plugins/sf
					'gree' : {
						use : false,
						dom : '<iframe src="http://share.gree.jp/share?url='+encodeURIComponent(href)+'&amp;type=1&amp;height=20" scrolling="no" frameborder="0" marginwidth="0" marginheight="0" style="border:none; overflow:hidden; width:100px; height:20px;" allowTransparency="true"></iframe>'
					},
					// Tumblr
					// http://www.tumblr.com/docs/ja/share_button
					'tumblr' : {
						use : false,
						dom : '<a href="http://www.tumblr.com/share" title="Share on Tumblr" style="display:inline-block; text-indent:-9999px; overflow:hidden; width:81px; height:20px; background:url(\'http://platform.tumblr.com/v1/share_1.png\') top left no-repeat transparent;"></a>'
					}
				}, settings);

				html.push('<hr class="noprint" /><ul class="social noprint clearfix">');
				for (var key in social) {
					if (social[key]['use']){
						html.push('<li>'+social[key]['dom']+'</li>');
						if (social[key]['script']) { scripts.push(social[key]['script']); }
					}
				}

				// FaceBookを実行
				if (typeof(FACEBOOK_APPID) !== 'undefined'){
					$('html').attr('xmlns:fb', 'http://www.facebook.com/2008/fbml#');
					$('body').append('<div id="fb-root"></div>');
					html.push('<li><div class="fb-like" data-href="'+href+'" data-layout="button_count" data-send="true" data-width="450" data-show-faces="true"></div></li>');
				}
				html.push('</ul>');
				$('#body').append(html.join("\n"));

				for (var i = 0; i < scripts.length; i ++) {
					$.getScript(scripts[i]);
				}
				if (typeof(FACEBOOK_APPID) !== 'undefined'){
					$.getScript('http://'+ 'connect.facebook.net/' + LANG + '/all.js', function() {
						FB.init({
							appId: FACEBOOK_APPID,
							status	: true, // check login status
							cookie	: true, // enable cookies to allow the server to access the session
							xfbml	: true, // parse XFBML
							oauth	: true
						});
						FB.Event.subscribe('auth.login', function() {
							window.location.reload();
						});
					});
					$('#body').append('<hr class="noprint" /><div class="fb-comments" href="'+href+'" publish_feed="true" numposts="10" migrated="1"></div>')
				}
			}
		}
	};

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

/*************************************************************************************************/
	// onLoad/onUnload
	$(document).ready(function(){
		var f;
		while( f = pkwkBeforeInit.shift() ){
			if( f !== null ){
				f();
			}
		}
		f = null;

		pukiwiki.init();

		tzCalculation_LocalTimeZone(location.host,false);
		while( f = pkwkInit.shift() ){
			if( f !== null ){
				f();
			}
		}
		pukiwiki.set_widget_btn();

		if (DEBUG){
			var D2 = new Date();
			console.info('Finish. (Process Time :',D2 - D1,'ms)');
		}
	});

	$(window).unload(function(){
/*
		var f;
		while( f = pkwkBeforeUnload.shift() ){
			if( f !== null ){
				f();
			}
		}
		f = null;
*/
		pukiwiki.unload();
/*
		while( f == pkwkUnload.shift()) {
			if( f !== null ){
				f();
			}
		}
*/
	});
	if (typeof(GOOGLE_ANALYTICS) !== 'undefined'){
		window._gaq = [['_setAccount',GOOGLE_ANALYTICS],['_trackPageview'],['_trackPageLoadTime']];
		Modernizr.load({
			load: ('https:' == location.protocol ? '//ssl' : '//www') + '.google-analytics.com/ga.js'
		});
	}
} )(jQuery, Modernizr, this, this.document );
