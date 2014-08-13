/*!
 * PukiWiki Advance - Yet another WikiWikiWeb clone.
 * Pukiwiki skin script for jQuery
 * Copyright (c)2010-2013 PukiWiki Advance Developer Team
 *			  2010	  Logue <http://logue.be/> All rights reserved.
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

// BootstrapとjQueryUIの競合解消
var bootstrapTooltip = $.fn.tooltip.noConflict();
$.fn.bstooltip = bootstrapTooltip;
var bootstrapButton = $.fn.button.noConflict();
$.fn.bsbutton = bootstrapButton;

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
	
	var $body = $(document.body);

	pukiwiki = {
		meta : {
			'@prefix' : '<http://purl.org/net/ns/doas#>',
			'@about' : '<skin.js>', 'a': ':JavaScript',
			'title' : 'Pukiwiki skin script for jQuery',
			'created' : '2008-11-25', 'release': {'revision': '2.5.0', 'created': '2013-01-22'},
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
			}else if (generetor.match(/plus/)){
				// PukiWiki Plus!の場合
				this.image_dir = SKIN_DIR+'theme/'+THEME_NAME+'/';
			}else{
				// PukiWiki用
				this.image_dir = SKIN_DIR+this.name+'/image/';
			}
			if (DEBUG){
				$('#pkwk-info ul').append(
					'<li>JavaScript framework:' + 
					'<a href="http://modernizr.com/">Modernizr</a>: <var>'+Modernizr._version+'</var> / ' +
					'<a href="http://jquery.com/">jQuery</a>: <var>'+$.fn.jquery+'</var> / '+
					'<a href="http://jqueryui.com">jQuery UI</a>: <var>'+$.ui.version+ '</var>.</li>');
			}

			var self = this;
			var protocol = ((document.location.protocol === 'https:') ? 'https:' : 'http:')+'//';
			this.body = this.custom.body ? this.custom.body : '*[role="main"]';
			var href = $('link[rel=canonical]')[0].href;	// 正規化されたURL
			var lang = $('html').attr('lang');

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
				keys: {
					next: [78, 39],
					previous: [80, 37],
					close: [67, 27],
					panorama: [90, null]
				},
				loop: false
			}, this.custom.rlightbox);
			*/
			
			$.extend(true, $.fn.dataTable.defaults, {
				bJQueryUI			: true,
				bAutoWidth			: false,
				sDom				: '<"H"pi>tr<"F"lf>',
				sPaginationType		:'full_numbers',
				oLanguage: {
					sEmptyTable		: $.i18n('dataTable', 'sEmptyTable'),
					sInfo			: $.i18n('dataTable', 'sInfo'),
					sInfoEmpty		: $.i18n('dataTable', 'sInfoEmpty'),
					sInfoFiltered	: $.i18n('dataTable', 'sInfoFiltered'),
//					sInfoPostFix 	: '',
					sInfoThousands	: $.i18n('dataTable', 'sInfoThousands'),
					sLengthMenu		: $.i18n('dataTable', 'sLengthMenu'),
					sLoadingRecords	: $.i18n('dialog', 'loading'),
					sProcessing		: $.i18n('dataTable', 'sProcessing'),
					sSearch			: $.i18n('dataTable', 'sSearch'),
					sUrl			: '',
					sZeroRecords	: $.i18n('dataTable', 'sZeroRecords'),
					oPaginate : {
/*
						sFirst : '<span class="fa fa-fast-backward" title="'+ $.i18n('dialog', 'first') +'"></span>',
						sPrevious : '<span class="fa fa-step-backward" title="'+ $.i18n('dialog', 'prev') +'"></span>',
						sNext : '<span class="fa fa-step-forward" title="'+ $.i18n('dialog', 'next') +'"></span>',
						sLast : '<span class="fa fa-fast-forward" title="'+ $.i18n('dialog', 'last') +'"></span>'
*/
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
			// ソーシャルブックマークのアイコン
			var social_config = $.extend(true,{
				// Hatena
				// http://b.hatena.ne.jp/guide/bbutton
				'hatena' : {
					use : true,
					dom : '<a href="http://b.hatena.ne.jp/entry/" class="hatena-bookmark-button" data-hatena-bookmark-layout="standard-balloon">Hatena</a>',
					script : 'http://b.st-hatena.com/js/bookmark_button.js'
				},
				// Mixi
				// http://developer.mixi.co.jp/connect/mixi_plugin/mixi_check/spec_mixi_check/
				'mixi' : {
					use : false,
					dom : '<a href="http://mixi.jp/share.pl" class="mixi-check-button" data-url="'+href+'" data-button="button-6">Mixi</a>',
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
			}, this.custom.social);

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
					'#000','#030','#060','#090','#0C0','#0F0','#300','#330','#360','#390','#3C0','#3F0','#600','#630','#660','#690','#6C0','#6F0',
					'#003','#033','#063','#093','#0C3','#0F3','#303','#333','#363','#393','#3C3','#3F3','#603','#633','#663','#693','#6C3','#6F3',
					'#006','#036','#066','#096','#0C6','#0F6','#306','#336','#366','#396','#3C6','#3F6','#606','#636','#666','#696','#6C6','#6F6',
					'#009','#039','#069','#099','#0C9','#0F9','#309','#339','#369','#399','#3C9','#3F9','#609','#639','#669','#699','#6C9','#6F9',
					'#00C','#03C','#06C','#09C','#0CC','#0FC','#30C','#33C','#36C','#39C','#3CC','#3FC','#60C','#63C','#66C','#69C','#6CC','#6FC',
					'#00F','#03F','#06F','#09F','#0CF','#0FF','#30F','#33F','#36F','#39F','#3CF','#3FF','#60F','#63F','#66F','#69F','#6CF','#6FF',
					'#900','#930','#960','#990','#9C0','#9F0','#C00','#C30','#C60','#C90','#CC0','#CF0','#F00','#F30','#F60','#F90','#FC0','#FF0',
					'#903','#933','#963','#993','#9C3','#9F3','#C03','#C33','#C63','#C93','#CC3','#CF3','#F03','#F33','#F63','#F93','#FC3','#FF3',
					'#906','#936','#966','#996','#9C6','#9F6','#C06','#C36','#C66','#C96','#CC6','#CF6','#F06','#F36','#F66','#F96','#FC6','#FF6',
					'#909','#939','#969','#999','#9C9','#9F9','#C09','#C39','#C69','#C99','#CC9','#CF9','#F09','#F39','#F69','#F99','#FC9','#FF9',
					'#90C','#93C','#96C','#99C','#9CC','#9FC','#C0C','#C3C','#C6C','#C9C','#CCC','#CFC','#F0C','#F3C','#F6C','#F9C','#FCC','#FFC',
					'#90F','#93F','#96F','#99F','#9CF','#9FF','#C0F','#C3F','#C6F','#C9F','#CCF','#CFF','#F0F','#F3F','#F6F','#F9F','#FCF','#FFF',
					'#111','#222','#333','#444','#555','#666','#777','#888','#999','#A5A5A5','#AAA','#BBB','#C3C3C3','#CCC','#D2D2D2','#DDD','#EEE','#FFF'
				]
			};
			// Sunlight Hiliter
			this.sh_config = $.extend(true,{
				theme : 'default',
				options : {
					lineNumbers: true,
					showMenu: true
				}
			}, this.custom.sh);

			// Suckerfish（ポップアップメニュー
			$('.sf-menu').superfish();

			// ポップアップ目次
			this.preptoc();

			// アシスタント
			if ($('.form-edit').length !== 0 && $.query.get('cmd') !== 'guiedit'){
				this.set_editform();
			}

			if ($('*[name="msg"]').length !== 0){
				$('.plugin-comment-form, .plugin-pcomment-form').after('<div class="assistant ui-corner-all ui-widget-header ui-helper-clearfix"></div>');
				this.assistant(false);
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
			this.social(social_config);

			// 非同期通信中はUIをブロック
			this.blockUI();
			
			// MathJax
			if ($('.mathjax-eq').length !== 0){
				$.getScript('http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS_HTML', function(){
					MathJax.Hub.Config({
						displayAlign: "inherit",
						TeX: {
							Macros: {
								bm: ["\\\\boldsymbol{#1}", 1],
								argmax: ["\\\\mathop{\\\\rm arg\\\\,max}\\\\limits"],
								argmin: ["\\\\mathop{\\\\rm arg\\\\,min}\\\\limits"]
							},
							extensions: ["autobold.js", "color.js"],
							equationNumbers: {
								//autoNumber: "all"
							}
						},
						tex2jax: {
							ignoreClass: ".*",
							processClass: "mathjax-eq"
						}
					});
				});
			}
		},
		// ページを閉じたとき
		unload : function(prefix){
			prefix = (prefix) ? prefix + ' ': '';
			$(':input').attr('disabled','disabled');
			return false;
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
			if (prefix !== undefined && typeof prefix !== 'string'){
				// 超やっつけ仕事！！
				prefix = prefix.selector;
			}
			prefix = (prefix) ? prefix + ' ' : '';

			$(':input').attr('disabled','disabled');

			// 自動サブミット型の設定。
			$(prefix + 'form.autosubmit').change(function(){
				this.submit();
			});
			
			// テキストエリアでタブ入力できるように
			$(prefix + 'textarea').tabby();
			$(prefix + 'textarea[row=1]').autosize();

			// タブ/アコーディオン処理
			$(prefix + 'li[role=tab] a').each(function(){
				var $this = $(this),
					href = $this.attr('href');

				$this.click(function(){
					return false;
				});

				if (href.match('#')){
					$this.data('disableScrolling',true);
				}else if (!href.match('ajax=raw')){
					// タブでリンクが貼られている場合は、ajaxは内部のHTMLを直接出力しなければならない。
					// したがって、明示的に部分的なHTMLを出力する。
					// リンク書き換えはこのスクリプトで行うため、プラグイン開発者はマークアップさえすれば問題ない。
					$this.attr('href',href+'&ajax=raw');
				}
			});
			$(prefix + '.tabs').tabs({
				beforeLoad: function( event, ui ) {
					ui.panel.html('<p id="ajax_error" class="alert alert-info"><span class="fa fa-spinner fa-spin"></span>'+$.i18n('dialog', 'loading')+'</p>');
					ui.jqXHR.global = false;
					ui.jqXHR.error(function() {
						ui.panel.html('<p id="ajax_error" class="alert alert-warning"><span class="fa fa-fa-exclamation-triangle"></span>'+$.i18n('dialog','error_page')+'</p>');
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
								ui.panel.html('<p id="ajax_error" class="alert alert-danger"><span class="fa fa-times-circle"></span>'+$.i18n('dialog','error_page')+'</p>');
							});
						}
					}
				},
				spinner: $.i18n('dialog', 'loading'),
				load:function(event, ui) {
					self.init_dom(ui.panel.id);
				}
			}).removeClass('tabs');

			// アップローダーに進捗状況表示（PHP5.4以降のみ）
			this.setUploaderProgress(prefix);

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
			// 
			this.setRegion(prefix);

			// フォームロックを解除
			$(':input').removeAttr('disabled');

			if(typeof(callback) === 'function'){
				callback();
			}
		},
		setUploaderProgress : function(prefix){
			var progressInterval, $form, $progress, $info;

			$form = $(prefix+'form[enctype="multipart/form-data"]');
			if ($form.children('input[type="file"]').length === 0) return;

			if ($form.length !== 0 && $form.find('.progress_session').length !== 0){
				$progress = $([
					'<div class="help-block hide">',
						'<div class="progress progress-info progress-striped">',
							'<div class="bar"></div>',
						'</div>',
						'<p></p>',
					'</div>'
				].join("\n")),
				
				//$progress.hide();
				$form.append($progress);

				var getProgress = function() {
					// Poll our controller action with the progress id
					$.getJSON(SCRIPT, {cmd : 'attach', pcmd : 'progress'}, function(data){
						if (data.status && !data.status.done) {
							var value = Math.floor((data.status.current / data.status.total) * 100);
							showProgress(value,  $.i18n('uploader', 'uploading'));
						} else {
							showProgress(100, 'Complete!');
							clearInterval(progressInterval);
						}
					});
				}

				var startProgress = function() {
					showProgress(0, 'Starting upload...');
					progressInterval = setInterval(getProgress, 900);
				}

				var showProgress = function(amount, message) {
					$progress.show();
					$progress.width(amount + '%');
					$progress.children('p').html(message);
					if (amount < 100) {
						$progress.children('.progress')
							.addClass('progress-info active')
							.removeClass('progress-success');
					} else {
						$progress.children('.progress')
							.removeClass('progress-info active')
							.addClass('progress-success');
					}
				}

				// Register a 'submit' event listener on the form to perform the AJAX POST
				$form.find('button').click(function(e){
					e.preventDefault();

					if ($form.children('input[type="file"]').val() === ''){
						return;
					}

					// Perform the submit
					//$.fn.ajaxSubmit.debug = true;
					$(this).ajaxSubmit({
						beforeSubmit: function(arr, $form, options) {
							// Notify backend that submit is via ajax
							arr.push({ ajax:true });
						},
						success: function (response, statusText, xhr, $form) {
							clearInterval(progressInterval);
							showProgress(100, 'Complete!');
							$progress.hide();

							// TODO: You'll need to do some custom logic here to handle a successful
							// form post, and when the form is invalid with validation errors.
							if (response.status) {
								// TODO: Do something with a successful form post, like redirect
								// window.location.replace(response.redirect);
							} else {
								// Clear the file input, otherwise the same file gets re-uploaded
								// http://stackoverflow.com/a/1043969
								var fileInput = $form.children('input[type="file"]');
								fileInput.replaceWith( fileInput.val('').clone( true ) );

								// TODO: Do something with these errors
								// showErrors(response.formErrors);
							}
						},
						error: function(a, b, c) {
							// NOTE: This callback is *not* called when the form is invalid.
							// It is called when the browser is unable to initiate or complete the ajax submit.
							// You will need to handle validation errors in the 'success' callback.
							console.log(a, b, c);
						}
					});
					// Start the progress polling
					startProgress();
				});
			}
		},
		// region.inc.php
		// ただし、id属性は使ってない
		setRegion :function(prefix){
			var dom = (prefix) ? $(prefix).find('.plugin-region') : $('.plugin-region');

			var $button = $('<button class="btn btn-default btn-xs pull-left region-btn"><small class="fa fa-plus"></small></button>');
			dom.find('.plugin-region-title').before($button);
			$('.plugin-region-body').hide();

			$('.region-btn').click(function(){
				var $this = $(this),
					$body = $this.next().next();

				$this.attr('disabled','disabled');

				$body.toggle('blind', {}, 500, function(){
					if ($body.data('page') !== null && $body.html() === ''){
						var params = {cmd:'read',ajax:'raw'},
							content = '',
							page_hash = $body.data('page').split('#');
							
						// console.log(page_hash);
						
						params.page = page_hash[0];
						if (page_hash[1]){
							params.id = page_hash[1];
						}
						$this.html('<small class="fa fa-spinner fa-spin"></small>');
						$.ajax({
							global:false,
							dataType: 'text',
							url: SCRIPT,
							data : params,
							type : 'GET',
							cache : true
						}).
						done(function(data){
							// スクリプトタグを無効化
							if (data !== null){
								content = data.replace(/<script[^>]*>[^<]+/ig,'');
							}else{
								content = '<p class="alert alert-warning"><span class="fa fa-warning"></span>Data is Null!</p>';
							}
						}).
						fail(function(data,status){
							// エラー発生
							if (data.status === 401){
								status = $.i18n('dialog','error_auth');
							}else if (status === 'error'){
								status = $.i18n('dialog','error_page');
							}
							content = '<p class="alert alert-warning" id="ajax-error"><span class="fa fa-warning"></span>'+status+'</p>';
							console.error(data);
						}).
						always(function(data){
							$body.html(content);
						});
					};
					if ($body.is(':hidden')){
						$this.html('<small class="fa fa-plus"></small>');
					}else{
						$this.html('<small class="fa fa-minus"></small>');
					}
					$this.removeAttr('disabled');
				});
			});
		},
		// アンカータグの処理
		setAnchor : function(prefix){
			var self = this,	// pukiwikiへのエイリアス
				dom = (prefix) ? $(prefix).find('a') : $('a');

			// 外部リンクアイコン
			$('.fa-external-link, .fa-external-link-square').each(function(){
				var $this = $(this);
				$this.click(function(){
					window.open($this.parent().attr('href'));
					return false;
				});
			});

			dom.each(function(){
				var $this = $(this),	// DOMをキャッシュ
					href = $this.attr('href'),
					rel = $this.attr('rel'),
					ext = href ? href.match(/\.(\w+)$/i) : null,
					isExternal = rel && rel.match(/noreferer|license|product|external/);

				if (!href || $this.data('ajax') === false){
					return;
				}else if (isExternal) {
					$this.click(function(){
						if (rel.match(/noreferer/)){
							// リファラーを消す
							if (ie){
								// for IE
								var subwin = window.open('','','location=yes, menubar=yes, toolbar=yes, status=yes, resizable=yes, scrollbars=yes,'),
									d = subwin.document;
								d.open();
								d.write('<meta http-equiv="refresh" content="0;url='+href+'" />');
								d.close();
							} else if ( !href.match(/data:text\/html;/)){
								
								// for Safari,Chrome,Firefox
								var dataUri ='data:text/html;charset=utf-8,'+encodeURIComponent(
									'<html><head><script type="text/javascript"><!--'+"\n"+
									'document.write(\'<meta http-equiv="refresh" content="0;url='+href+'" />\');'+"\n"+
									'// --></script></head><body></body></html>');
								if (rel.match(/external/)){
									window.open(dataUri);
								}else{
									location.href = dataUri;
								}
							}else{
								window.open(href);
							}
						}else{
							if (ext[1]){
								switch (ext[1]) {
									case 'jpg': case 'jpeg': case 'gif': case'png':
										$this.rlightbox();
									break;
									default :
										window.open(href);
									break;
								}
							}
						}
						return false;
					});
				}else if (href){
					var params = {},
						hashes = href.slice(href.indexOf("?") + 1).split('&'),
						disable_scrolling = ($this.data('disableScrolling') || $this.parent().attr('role')) ? true : false;

					// 外部へのリンクは、rel=externalが付いているものとする。
					// Query Stringをパース。paramsに割り当て
					for(var i = 0; i < hashes.length; i++) {
						var hash = hashes[i].split('=');
						try{
							// スペースが+になってしまう問題を修正
							params[hash[0]] = decodeURIComponent(hash[1]).replace(/\+/g, ' ').replace(/%2F/g, '/');
						}catch(e){}
					}
					
					// 明示的にajaxを無効化するリンクの場合
					if (params.ajax === 'false') return true;
					
					if (href.match('#') && href !== '#'){
						// アンカースクロールを無効化判定
						if (disable_scrolling === false){
							// アンカースクロール
							$this.unbind('click').bind('click', function(){
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
								self._hideToc();
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
									case 'jpg': case 'jpeg': case 'gif': case'png':/* case'svg' : case 'svgz' :*/
										$this.rlightbox();
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
							$this.unbind('click').bind('click', function(){
								params.ajax = 'json';
								self.ajax_dialog(params,prefix,function(){
									if ((params.cmd == 'attach' && params.pcmd.match(/upload|info/i)) || params.cmd.match(/attachref|read|backup/i) && params.age !== ''){
										var dom = $(prefix).find('.window');
										self.init_dom(prefix + ' .window');
									}
								});
								return false;
							});
						}
					}
					
				}
			});
			
			/**
			 * トップに戻るボタン
			 * http://webdesignerwall.com/tutorials/animated-scroll-to-top
			 */
			if ($('#back-top').length === 0){
				var $body = $(document.body), $window = $(window), $back_top;
				// DOMを挿入
				$body.append('<div id="back-top"><a href="#" class="btn btn-primary btn-lg" title="トップへ"><span class="glyphicon glyphicon-arrow-up"></span></a></div>');
				
				$back_top = $('#back-top');
				// 最上部でボタンが表示されるとカッコ悪いので隠す
				$back_top.hide();
				// スクロール量に応じて、フェードで「トップに戻る」ボタンを表示/非表示
				$window.scroll(function () {
					if ($(this).scrollTop() > $('.masthead').height()) {
						// スクロールしているときは、フェードインで表示させる
						$back_top.fadeIn();
					} else {
						// スクロールしていないときは、フェードアウトで隠す
						$back_top.fadeOut();
					}
				});
				// 「トップに戻る」ボタンにイベント割り当て
				
				$('#back-top a')
					.click(function(){
						$('html,body').animate({scrollTop: 0}, 1000);
						return false;
					})
					.fadeTo('fast',0.5)	// 実は、初期値。
					.hover(
						function(){
							// マウスオーバー時にフェードイン（濃くする）
							$(this).fadeTo('normal',1);
						},
						function(){
							// マウスが離れたときにフェードアウト（薄くする）
							$(this).fadeTo('normal',0.5);
						}
					);
			}
		},
		// ajaxでダイアログ生成。JSON専用！
		// JSONには必ず、bodyとtitleを入れること！（まぁ、parse関数でbodyとtitleが含まれるオブジェクトにすればいいけど）
		// params		QueryStringをオブジェクトにしたもの
		// prefix		親のDOM名
		// callback		開いた時実行する関数。
		// parse		JSONのパース関数
		ajax_dialog : function(params, prefix,callback,parse){
			prefix = (prefix) ? $(prefix).find('.window') : $('.window');
			var self = this,	// pukiwikiへのエイリアス
				// ダイアログ設定
				dialog_option = {
					modal: true,
					resizable: true,
					show: 'fade',
					hide: 'fade',
					width: '520px',
					open: function(){
						if(typeof(callback) === 'function'){ callback(); }
						// オーバーレイでウィンドウを閉じる
						self.init_dom(prefix);
						return false;
					},
					close: function(){
						$(this).remove();
					}
				},
				container = $('<div class="window"></div>'),
				content = ''
			;

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
					content = '<p class="alert alert-warning"><span class="fa fa-warning"></span>Data is Null!</p>';
					dialog_option.title = $.i18n('dialog','error');
				}
			}).
			fail(function(data,status){
				// エラー発生
				if (data.status === 401){
					status = $.i18n('dialog','error_auth');
				}else if (data.status === 302){
					document.location.reload();
				}else if (status === 'error'){
					status = $.i18n('dialog','error_page');
					console.error(data);
				}

				dialog_option.title = $.i18n('dialog','error');
				dialog_option.width = 400;
				content = '<p class="alert alert-warning" id="ajax-error"><span class="fa fa-warning"></span>'+status+'</p>';
				content += data.responseText;
			}).
			always(function(data){
				container.html(content).dialog(dialog_option);
				$('*[role="tooltip"]').remove();	// ツールチップが消えないことがあるので・・・

				if (data.status !== 200){
					try{
						$('#ajax-error').after([
							'<div class="alert alert-info">',
							'<ul>',
							'<li>readyState:'+data.readyState+'</li>',
	//						'<li>responseText:'+data.responseText+'</li>',
							'<li>status:'+data.status+'</li>',
							'<li>statusText:'+data.statusText+'</li>',
							'</ul>',
							'</div>'
						].join("\n"));
					}catch(e){}
				}
			});
		},
		blockUI : function(){
			// jQueryUI BlockUI
			// http://pure-essence.net/2010/01/29/jqueryui-dialog-as-loading-screen-replace-blockui/
			var $window = $(window), $activity, $loading;

			if ($('#loading').length === 0){
				$('<div id="loading">'+
					'<div class="ui-widget-overlay"></div>'+
					'<div id="loading-activity"></div>'+
				'</div>')
					.click(function(){
						$(this).fadeOut();
						return false;
					})
					.appendTo('body')
				;
			}

			$activity = $('#loading-activity'), $loading = $('#loading');;
			
			$activity
				.activity({
					segments: 12,
					width: 24,
					space: 8,
					length: 64,
					color: 'black',
					speed: 1,
					zIndex: 9999
				})
				.click(function(){
					$loading.fadeOut();
				})
			;
			

			$window
				.ajaxSend(function(e, xhr, settings) {
					$(':input').attr('disabled','disabled');
					// 画面中央にローディング画像を移動させる
					$activity.css({
						position : 'absolute',
						left : ($window.width() / 2) + $window.scrollLeft() - $activity.width() / 2,
						top : ($window.height() / 2) + $window.scrollTop() - $activity.height() / 2
					})
					$loading.fadeIn();
				})
				.ajaxStop(function(){
					$(':input').removeAttr('disabled');
					$loading.fadeOut();
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
			var sh = (prefix) ? prefix + ' .sh' : '.sh', self = this;
			
			if ($(sh).length !== 0) {
				if (typeof(window.Sunlight) === 'undefined') {
					$.getScript(JS_URI + 'sunlight/sunlight-all-min.js', function(){
						$("head").prepend('<link rel="stylesheet" href="' + JS_URI + 'sunlight/themes/sunlight.' + self.sh_config.theme + '.css' + '" />');
						$.fn.sunlight = function(options) {
							var highlighter = new window.Sunlight.Highlighter(options);
							this.each(function() {
								highlighter.highlightNode(this);
							});
							return this;
						};
						$(sh).sunlight(self.sh_config.config);
					});
				}else{
					$(sh).sunlight(this.sh_config.config);
				}
			}
		},
		dataTable : function(prefix){
			var self = this;
			var $table = (prefix) ? $(prefix).find('.table') : $('.table');
			$table.each(function(){
				var $this = $(this),
					sortable = (typeof($this.data('sortable')) === 'undefined' || $this.data('sortable') === true) ? true : false;
			//	self.setAnchor(this);
				if ($this.find('thead').length !== 0 && sortable){
					var pagenate = (typeof($this.data('pagenate')) === 'undefined' || $this.data('pagenate') === false) ? false : true;
					$this.dataTable({
						bPaginate : pagenate,
						sDom: (pagenate) ? '<"H"pi>tr<"F"lf>' : 'tr',
						aaSorting: ! $this.data('filter') ? [] : $this.data('filter'),
						aoColumnsDefs: [
							{ sType: "natural" },
							{ sType: "natural" },
							{ sType: "natural" },
							null
						]
					});
				}
			});
			$('.fg-toolbar input, .fg-toolbar select').addClass('form-control').css('display','inline-block');
			
		},
		// 検索フォームでサジェスト機能
		suggest: function(prefix){
			var $form = $(prefix ? prefix + ' .suggest' : '.suggest'),
				cache = {},lastXhr, xhr
			;
			if ($form.length !== 0){
				$form.autocomplete({
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
		// 独自のGlossaly処理
		glossaly: function(prefix){
			var glossaries = {};	// ajaxで読み込んだ内容をキャッシュ
			$(document.body).tooltip({
				items: '.glossary, .search-summary, [title]',
				track: true,
				show: function(){
					// ツールチップが複数表示されてしまうのを抑止
					if ( $('[role="tooltip"]').length > 1 ){
						$(this).remove();
					}
				},
				content: function(callback) {
					var $this = $(this);

					if ( $this.is('.glossary, .search-summary') ) {
						$this.removeAttr('title');
						var text = $this.text();
						if (text !== '' && !glossaries[text]){
							// キャッシュがない場合
							var params = $this.is('.search-summary')
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
								data : params
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
						
					}else if ( $this.is('[title]')){
						return $this.attr('title');
					}
				}
			});
		},
		// 入力アシスタント
		assistant: function(normal){
			var self = this,
				i, j, len, 
				emoji_widget, color_widget,
				$msg = $('*[name="msg"]'),
				$emoji,
				$color_palette,
				$hint;

			// アシスタントのウィジット
			$('.assistant').html([
				'<div class="btn-toolbar" role="toolbar">',
					'<div class="btn-group">',
						'<button class="btn btn-default btn-sm replace" title="'+$.i18n('editor','bold')+'" name="b"><span class="fa fa-bold"></button>',
						'<button class="btn btn-default btn-sm replace" title="'+$.i18n('editor','italic')+'" name="i"><span class="fa fa-italic"></button>',
						'<button class="btn btn-default btn-sm replace" title="'+$.i18n('editor','strike')+'" name="s"><span class="fa fa-strikethrough"></button>',
						'<button class="btn btn-default btn-sm replace" title="'+$.i18n('editor','underline')+'" name="u"><span class="fa fa-underline"></span></button>',
						'<button class="btn btn-default btn-sm replace" title="'+$.i18n('editor','code')+'" name="code"><span class="fa fa-code"></span></button>',
						'<button class="btn btn-default btn-sm replace" title="'+$.i18n('editor','quote')+'" name="q"><span class="fa fa-quote-left"></span></button>',
					'</div>',
					'<div class="btn-group">',
						'<button class="btn btn-default btn-sm replace" title="'+$.i18n('editor','link')+'" name="url"><span class="fa fa-link" role="button"></span></button>',
						'<button class="btn btn-default btn-sm replace" title="'+$.i18n('editor','size')+'" name="size"><span class="fa fa-text-height" role="button"></span></button>',
						'<button class="btn btn-default btn-sm insert" title="'+$.i18n('editor','color')+'" name="color">color</button>',
					'</div>',
					'<div class="btn-group">',
						'<button class="btn btn-default btn-sm insert" title="'+$.i18n('editor','emoji')+'" name="emoji"><span class="fa fa-smile-o"></span></button>',
						'<button class="btn btn-default btn-sm insert" title="'+$.i18n('editor','breakline')+'" name="br">⏎</button>',
					'</div>',
					'<button class="btn btn-default btn-sm replace" title="'+$.i18n('editor','ncr')+'" name="ncr">&amp;#</button>',
					'<button class="btn btn-default btn-sm insert" title="'+$.i18n('editor','hint')+'" name="help"><span class="fa fa-question-circle"></span></button>',
					(!normal && Modernizr.localstorage) ? '<button class="btn btn-default btn-sm insert" title="'+$.i18n('editor','flush')+'" name="flush"><span class="fa fa-trash-o"></span></button>': null,
					!normal ? '<button class="btn btn-default btn-sm disabled pull-right hidden" id="indicator"><span class="fa fa-refresh fa-spin"></span></button>' : null,
				'</div>'
			].join("\n"));

			// 絵文字パレットのウィジット
			if ($('#emoji').length === 0){
				$body.append('<div id="emoji"></div>');

				emoji_widget = '<ul class="ui-widget pkwk_widget ui-helper-clearfix">';
				for(i = 0, len = this.assistant_setting.emoji.length; i < len ; i++ ){
					var name =  this.assistant_setting.emoji[i];
					emoji_widget += '<li class="btn btn-default btn-xs" title="'+name+'" name="'+name+'"><span class="emoji emoji-'+name+'"></span></li>';
				}

				emoji_widget += '</ul>';
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
				
				// イベントの割り当て
				$('#emoji').children('ul').children('li').click(function(){
					var str = $msg.getSelection().text, v = '&('+$(this).attr('name')+');';

					$msg.focus();
					if (str === ''){
						$msg.insertAtCaretPos(v);
					}else{
						$msg.replaceSelection(v);
					}
					$emoji.dialog('close');
					return;
				});
			}
			$emoji = $('#emoji');

			// カラーパレットのウィジット
			if ($('#color_palette').length === 0){
				$body.append('<div id="color_palette"></div>');
				
				color_widget = '<ul class="ui-widget pkwk_widget ui-helper-clearfix" id="colors">';
				for(i = 0, len =  this.assistant_setting.color.length; i < len ; i++ ){
					var color = this.assistant_setting.color[i];
					color_widget += '<li class="btn btn-default btn-xs" title="'+color+'" name="'+color+'"><span class="emoji" style="background-color:'+color+';"></span></li>';
					j++;
				}
				color_widget += '</ul>';
				$('#color_palette').dialog({
					title:$.i18n('editor','color'),
					autoOpen:false,
					bgiframe: true,
					width:470,
					height:400,
					show: 'scale',
					hide: 'scale'
				}).html(color_widget);

				// イベントの割り当て
				$('#color_palette').children('ul').children('li').click(function(){
					var ret, str = $msg.getSelection().text, v = $(this).attr('name');

					if (str === ''){
						alert( $.i18n('pukiwiki', 'select'));
						return;
					}

					$msg.focus().replaceSelection(
						str.match(/^&color\([^\)]*\)\{.*\};$/) ? 
						str.replace(/^(&color\([^\)]*)(\)\{.*\};)$/,"$1," + v + "$2") : 
						'&color(' + v + '){' + str + '};'
					);
					$color_palette.dialog('close');
					return;
				});
			}

			// ヒントのウィジット
			if ($('#hint').length === 0){
				$body.append('<div id="hint"></div>');
				
				$('#hint').dialog({
					title:$.i18n('editor','hint'),
					autoOpen:false,
					bgiframe: true,
					width:470,
					show: 'scale',
					hide: 'scale'
				}).html($.i18n('pukiwiki','hint_text1'));
			}

			// ここから、イベント割り当て
			$('.insert').click(function(){
				var ret = '', v = $(this).attr('name');

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
						ret = '&('+v+');';
					break;
				}
				if (ret !== ''){
					$msg.focus();
					if ($msg.getSelection().text === ''){
						$msg.insertAtCaretPos(ret);
					}else{
						$msg.replaceSelection(ret);
					}
				}
				$('*[role="tooltip]').hide();
				return false;
			});

			$('.replace').click(function(){
				var ret = '', str = $msg.getSelection().text, v = $(this).attr('name');

				if (str === ''){
					alert( $.i18n('pukiwiki', 'select'));
					return false;
				}

				switch (v){
					case 'size' :
						var val = prompt($.i18n('pukiwiki', 'fontsize'), '100%');
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
				$msg.focus().replaceSelection(ret);
				return false;
			});
		},
		// 編集画面のフォームを拡張
		set_editform: function(prefix){
			prefix = (prefix) ? prefix + ' ': '';
			// よく使うDOMをキャッシュ
			var $form = $('.form-edit'),
				$msg = $form.find('textarea[name="msg"]'),
				$original = $form.find('textarea[name="original"]'),
				self = this,
				isEnableLocalStorage = false,
				// HTMLエンコード
				htmlsc = function(ch) {
					if (typeof(ch) === 'string'){
						ch = ch.replace(/&/g,'&amp;');
						ch = ch.replace(/\"/g,'&quot;');	// "
						ch = ch.replace(/\'/g,'&#039;');	// '
						ch = ch.replace(/</g,'&lt;');
						ch = ch.replace(/>/g,'&gt;');
					}
					return ch;
				},
				// リアルタイムプレビューの内部処理
				realtime_preview = function(){
					var source = $msg.val(),
						$realview = $('#realview'),
						$indicator = $('#indicator'),
						sttlen, endlen, sellen, finlen;

					if ($realview.is(':visible')) {
						
						$indicator.css('display:block;');

						if (++self.ajax_count !== 1){ return; }
						var finlen = source.lastIndexOf("\n",$msg.getSelection().start);

						$.ajax({
							url : SCRIPT,
							type : 'post',
							global:false,
							data : {
								cmd : 'edit',
								realview : 1,
								page : decodeURI(PAGE),
								// 編集した位置までスクロールさせるための編集マークプラグイン呼び出しを付加
								msg : source.substring(0,finlen) +"\n\n" + '&editmark;' + "\n\n" + source.substring(finlen),
								type : 'json'
							},
							cache : false,
				//			timeout : 2000,//タイムアウト（２秒）
							dataType : 'json',
							beforeSend : function(){
								$msg.attr('disabled', 'disabled');
								$indicator.removeClass('hidden');
								$indicator.html('<span class="fa fa-spinner fa-spin"></span>');
							},
							success : function(data){
								$indicator.html('<span class="fa fa-clock-o"></span>'+data.taketime);
								var ret = data.data.replace(/<script[^>]*>[^<]+/ig,'<div>[SCRIPT]</div>'), $editmark;
								ret = ret.replace(/<form(.*?)>(.*?)<\/form>/ig,'<div>[FORM]</div>');
								$('#realview').html(ret);
								$editmark = $('#editmark')

		//						if ($realview.scrollTop() === 0) {
									// スクロールが0の時エラーになる問題をごまかす
		//							$realview.scrollTop(1);
		//						}
		//						$realview.animate({
		//							scrollTop: $('#editmark').offset().top
		//						});
								if ($editmark.length !== 0 ){
									$realview.scrollTop($editmark.offset().top - $realview.height()-100);
								}

								if (self.ajax_count===1) {
									self.ajax_count = 0;
								} else {
									self.ajax_count = 0;
									realtime_preview();
								}
							},
							error : function(data,status,thrown){
								console.log(data);
								$realview.html([
									'<div class="alert alert-warning">',
										'<p><span class="fa fa-warning-sign"></span>'+$.i18n('pukiwiki','error')+status+'</p>',
										'<ul>',
											'<li>readyState:'+data.readyState+'</li>',
											'<li>responseText:'+data.responseText+'</li>',
											'<li>status:'+data.status+'</li>',
											'<li>statusText:'+data.statusText+'</li>',
										'</ul>',
									'</div>'].join("\n")
								);
							},
							complete: function(){
								$msg.removeAttr('disabled');
							}
						});
					}else{
						$indicator.css('display:hidden;');
					}
				},
				// 差分処理
				diff = function (arr1, arr2, rev) {
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
						for(k = -p	   ; k <  delta; ++k){ snake(k); }
						for(k = delta + p; k >= delta; --k){ snake(k); }
					}
					return ed[delta + offset];
				}
			;

			this.ajax_count = 0;

			if (Modernizr.localstorage){
				var storage = window.localStorage.getItem(PAGE);
				var data = window.JSON.parse(storage);

				if (data){
					// 「タイムスタンプを更新しない」で更新した場合、それを検知する方法がないという致命的問題あり。
					var ask = (MODIFIED > data.modified && data.msg !== $msg.val()) ?
						$.i18n('pukiwiki','info_restore1') : $.i18n('pukiwiki','info_restore2');
					// データーを復元
					if (confirm(ask)){ $msg.val(data.msg); }
				}
				isEnableLocalStorage = true;
			}

			// 差分ボタン
			$form.find('button[name="write"]').after('<button type="submit" name="diff" class="btn btn-default" accesskey="d"><span class="fa fa-eye-slash"></span>' + $.i18n('editor','diff') + '</button>');

			// 簡易差分表示用ダイアログ
			$body.append('<div id="diff"><pre>'+htmlsc($original.val())+'</pre></div>');
			$('#diff').dialog({
				title:$.i18n('editor','diff'),
				autoOpen:false,
				bgiframe: true,
				width:'90%',
				height: $(window).height()*0.8|0,
				show: 'scale',
				hide: 'scale'
			});


			// textareaのイベントリスナ
			$msg
				.unbind('blur').bind('blur', function(){
					// マウスが乗っかった時
					realtime_preview();
				})
				.unbind('mouseup').bind('mouseup', function(){
					// 前の値と異なるとき
					if ($(this).val() !== $original.val()){
						realtime_preview();
					}
				})
				.unbind('keypress').bind('keypress', function(){
					// テキストエリアの内容をlocalStrageにページ名と共に保存
					if (isEnableLocalStorage){
						var d=new Date();
						window.localStorage.setItem(PAGE, JSON.stringify({
							msg : $(this).val(),
							modified: d.getTime()
						}));
					}
				})
				.unbind('change').bind('change', function(){
					// edit.js v 00 -- pukiwikiの編集を支援
					// Copyright(c) 2010 mashiki
					// License: GPL version 3
					// http://mashiki-memo.blogspot.com/2010/12/blog-post.html

					// show_diff(原文,現在の内容（通常$('textarea[name=msg]').value）)
					var _diff = diff(
						$original.val().replace(/^\n+|\n+$/g,'').split("\n"),
						$(this).val().replace(/^\n+|\n+$/g,'').split("\n")
					);
					var d = [],ret = [];
					for (var i=0; d=_diff[i]; ++i) {
						var line = htmlsc(d.arr[d.line]);
						switch(d.edit){
							case '+':
								//ret[i] = '+'+line;
								ret[i] = '<ins class="diff-added">'+line+'</ins>';
							break;
							case '-':
								//ret[i] = '-'+line;
								ret[i] = '<del class="diff-removed">'+line+'</del>';
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
			
			// ボタンクリックで送信処理を行っているため、元々のformは無効化する。
			$form.submit(function() {
				return false;
			});

			// ボタンをクリックした時のイベント
			$form.find('button').click(function(e){
				e.preventDefault();

				var $this = $(this),
					$form = $this.parents('form'),
					postdata = $form.serializeObject(),
					$input = $form.find(':input');

				// フォームを無効化
				$input.attr('disabled', 'disabled');
				
				// TODO:念のため、最新の記事を取ってくる

				switch ($this.attr('name')) {
					case 'cancel' :	// キャンセルボタン
						// ローカルストレージをフラッシュ
						if (isEnableLocalStorage){
							localStorage.removeItem(PAGE);
						}
						location.href = SCRIPT + '?' + PAGE;
						break;
					case 'preview':	// プレビューボタン
						// フォームの高さを取得
						var msg_height = $msg.height(), $realview;

						if ($('#realview').length === 0) {
							// リアルタイムプレビューの表示画面
							$msg.before('<div id="realview" class="form-control" style="display:none;"></div>');
						}
						
						$realview = $('#realview');

						// Textarea Resizerで高さが可変になっているため。
						if ($realview.is(':visible')) {
							//console.log('visible');
							// realview_outerを消したあと、フォームの高さを２倍にする
							// 同時でない理由はFireFoxで表示がバグるため
							$('#realview').animate({
								height:0
							}, function(){
								$msg.animate({height:msg_height*2});
								$('#realview').hide();
								$('#indicator').addClass('hide');
								$input.removeAttr('disabled', 'disabled');
							});
						} else {
							//console.log('invisible');
							// フォームの高さを半分にしたあと、realviewを表示
							$msg.animate({
								height: msg_height/2
							},function(){
								// 初回実行時、realview_outerの大きさを、フォームの大きさに揃える。
								// なお、realview_outerの高さは、フォームの半分とする。
								$('#realview').css('display','block').animate({
									height:msg_height/2
								},function(){
									// 現在のプレビューを出力
									realtime_preview();
									$input.removeAttr('disabled');
								});
							});
						}
						
						break;
					case 'diff' :	// 差分ボタン
						$('#diff').dialog('open');
						$input.removeAttr('disabled');
						break;
					case 'write':
						// 送信ボタンが押された
						postdata.write = true;
						postdata.ajax = 'json';

						// 空更新は無反応
						if ( $original.val() == $msg.val()){
							alert("Void updating");
							$input.removeAttr('disabled');
							return false;
						}
						
						// 管理パスが入力されてない状態でタイムスタンプを更新しないになっている場合は、無反応
						if ( $form.find('input[name="pass"]').length !==0 && (postdata.notimestamp && postdata.pass === '')){
							alert("Password is missing");
							$input.removeAttr('disabled');
							return false;
						}

						// ajaxで保存
						$.ajax({
							type: 'POST',
							url:SCRIPT,
							data: postdata,
							cache: false,
						//	timeout: 1500,
							dataType : 'json'
						}).
						done(function(data){
							console.log(data);
							// たぶん、captichaが入る
							$('title').text(data.title);
							$('[role="main"]').html(data.body);

							// ajaxで送信すると、jsonで結果が返る。
							if (data.posted === true) {
								// ローカルストレージをフラッシュ
								if (isEnableLocalStorage){
									localStorage.removeItem(PAGE);
								}
								// facebookに投稿（未実装）
								if (typeof(FACEBOOK_APPID) !== 'undefined' && !postdata.notimestamp ) {
									if ( postdata.fb-publish === 'true'){
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
												alert($.i18n('pukiwiki','error'));
											}
										});
									}
								}
								// 送信に成功しているので、元のページにジャンプ
								location.href = SCRIPT + '?' + PAGE;
								return false;
							}
							// そうでない場合は、CAPTCHAが表示される・・・ハズ。
						}).
						fail(function(data, status){
							console.log(data);
							if (DEBUG) {
								console.error(data, status);
								if ($('#debug-window').length === 0) $(document.body).append('<div id="debug-window"></div>');
								$('#debug-window').dialog({
									modal: true,
									content: data.statusText
								});
							}
							alert($.i18n('pukiwiki','error'));
						}).
						always(function(data, status){
							// フォームのロックを解除
							$input.removeAttr('disabled');
						});
						break;
				}	// End of switch
				return false;
			});

			// アシスタントのツールバーを前に追加
			$msg.before('<div class="assistant ui-corner-top ui-widget-header ui-helper-clearfix"></div>');
			this.assistant();
		},
		/** preparation of popup TOC on init() */
		preptoc : function(){
			var lis = this.prepHdngs();
			var self = this;
			this.genTocDiv(lis);
		},
		prepHdngs : function(){
			var $root = $('*[role="main"]'),
				$hd = $root.find('h2'),
				$tocs = $root.find('.contents'),
				li = [],
				ret = ''
			;

			$hd.each(function(index){
				var $this = $(this),
					xid = $this.attr('id');

				// 見出しにアイコンを付ける
				$this.html($this.html()+'<span class="pkwk-icon icon-toc" title="Table of Contents of this page" style="cursor:pointer;"></span>');
				// 見出し一覧が出力されていない場合、自動生成
				if($tocs.length === 0){
					if (!xid){
						// IDが存在しない場合、自動生成
						xid = '_genid_'+index;
						$this.attr('id',xid);
					}
					li[index] = '<li><a href="#'+xid+'">'+$this.text()+'</a></li>';
				}
			});
			
			if($tocs.length !== 0){
				// #contentsが呼び出されているときは、その内容をTocに入れる。
				ret = $tocs.html();
			}else if($hd.length !== 0){
				// 通常時の動作。h2タグのみリスティング
				ret = '<ol>'+li.join("\n")+'</ol>';
			}else if($.query.get('cmd').match(/list|backup/) !== -1){
				// おまけ。一覧ではトップのナビを入れる。
				ret = '<div style="text-align:center;">'+$('.page_initial').html()+'</div>';
			}

			return ret;
		},
		/** generate the popup TOC division */
		genTocDiv : function(lis){
			var self = this;
			this.toc = document.createElement('div');
			this.toc.id = 'poptoc';
			$(this.toc)
				.addClass('panel panel-default noprint')
				.css('top',0)
				.css('left',0)
				.css('position','absolute')
				.css('z-index',100)
				.css('display','none')
				.html([
				'<h1><a href="#">'+$('h1#title').text()+'</a><abbr title="Table of Contents">[TOC]</abbr></h1>',
				lis.replace(/href/g,'tabindex="1" href')
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
				.css('display','none')
				.click(function(){
					self._hideToc();
				}
			);
			$('body').append(toc_bg);

			this.calcObj(this.toc,300);

			$('.icon-toc').click(function(elem){
				if ($('#'+self.toc.id).hide()){
					self._dispToc(elem,this,false);
				}else{
					self._hideToc(elem);
				}
			}).css('display','inline-block');

			$('#' + this.toc.id + ' *').click(function(){
				self._hideToc();
			});
			
			$(window).keydown(function(e){
				// スラッシュキー（入力フォーム以外）
				if(!e.target.nodeName.match(/(input|textarea)/i) && e.keyCode === 111){
					if ($('#'+self.toc.id).hide()){
						self._dispToc();
					}else{
						self._hideToc();
					}
					return false;
				}
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
		_dispToc : function(ev,tg){
			var top, doc, scr, 
				h = this.toc.height,
				w = this.toc.width
			;
			
			if (ev && tg) {
				doc = {
					x:ev.clientX + $(window).scrollLeft(),
					y:ev.clientY + $(window).scrollTop()
				};
				scr = {
					x: ev.pageX,
					y: ev.pageY,
					w: $(window).width(),
					h: $(window).height()
				};

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
			}else{
				$(this.toc)
					.css('top', $(window).scrollTop()+'px')
					.css('left','0px');
			}
			$(this.toc).show('slow');
			$('#toc_bg').fadeIn('fast');
		},
		_hideToc : function(ev){
			$(this.toc).hide('slow');
			$('#toc_bg').fadeOut('fast');
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
				var $main = $('[role="main"]');
				var html = [], scripts = [];
				window.___gcfg = {lang: $('html').attr('lang')};	// for Google +1


				html.push('<hr class="noprint" /><ul class="social noprint clearfix list-inline">');
				for (var key in settings) {
					if (settings[key]['use']){
						html.push('<li>'+settings[key]['dom']+'</li>');
						if (settings[key]['script']) { scripts.push(settings[key]['script']); }
					}
				}

				// FaceBookを実行
				if (typeof(FACEBOOK_APPID) !== 'undefined'){
					$('html').attr('xmlns:fb', 'http://www.facebook.com/2008/fbml#');
					$('body').append('<div id="fb-root"></div>');
					html.push('<li><div class="fb-like" data-href="'+href+'" data-layout="button_count" data-send="true" data-width="450" data-show-faces="true"></div></li>');
				}
				html.push('</ul>');
				$main.append(html.join("\n"));

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
					$main.append('<hr class="noprint" /><div class="fb-comments" href="'+href+'" publish_feed="true" numposts="10" migrated="1"></div>')
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
	if (typeof(GOOGLE_ANALYTICS) !== 'undefined' && GOOGLE_ANALYTICS !== false){
		// http://mathiasbynens.be/notes/async-analytics-snippet#universal-analytics
		(function(G,o,O,g,l){
			G.GoogleAnalyticsObject=O;
			G[O]||(G[O]=function(){
				(G[O].q=G[O].q||[]).push(arguments)
			});
			G[O].l=+new Date;g=o.createElement('script'),l=o.scripts[0];
			g.src='//www.google-analytics.com/analytics.js';l.parentNode.insertBefore(g,l)
		}(this,document,'ga'));

		ga('create', GOOGLE_ANALYTICS, 'auto');
		ga('send', 'pageview',{
		});
	}
} )(jQuery, Modernizr, this, this.document );