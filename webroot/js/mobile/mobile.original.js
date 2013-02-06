/*!
 * PukiWiki Advance - Yet another WikiWikiWeb clone.
 * Pukiwiki Mobile script for jQuery mobile
 * Copyright (c)2012 PukiWiki Advance Developer Team
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

var pukiwiki = {};
var JQUERY_MOBILE_VER = '1.3.0-beta.1';
//var JQUERY_MOBILE_VER = 'latest';
(function ($, window, document) {
	'use strict';
	var pkwkInit = [], pkwkBeforeInit = [], pkwkUnload = [], pkwkBeforeUnload = [];
	
	if (!$) { throw "pukiwiki: jQuery not included."; }

	if (DEBUG) {
		// usage: log('inside coolFunc', this, arguments);
		// paulirish.com/2009/log-a-lightweight-wrapper-for-consolelog/
		window.log = function f(){
			log.history = log.history || [];	// store logs to an array for reference
			log.history.push(arguments);
			if(this.console) {
				var args = arguments, newarr;
				args.callee = args.callee.caller;
				newarr = [].slice.call(args);
				if (typeof console.log === 'object') log.apply.call(console.log, console, newarr);
				else console.log.apply(console, newarr);
			}
		};
		// make it safe to use console.log always
		(function(a){function b(){}for(var c="assert,count,debug,dir,dirxml,error,exception,group,groupCollapsed,groupEnd,info,log,markTimeline,profile,profileEnd,time,timeEnd,trace,warn".split(","),d;!!(d=c.pop());){a[d]=a[d]||b;}})
		(function(){try{console.log();return window.console;}catch(a){return (window.console={});}}());
		window.console.debugMode = true;
		var D1 = new Date();
	}

	pukiwiki = {
		// 初期設定
		config: {
			tablesorter: {
				counter: 0,
				sorter: {
//					debug	: DEBUG,
					useUI	: true,
					cssUI	: {
						widget		: '',
						header		: 'ui-btn-up-b',
						hover		: 'ui-btn-hover-b',
						icon		: 'ui-icon ui-icon-arrow-d ui-icon-shadow',
						iconBoth	: 'ui-icon-triangle-2-n-s',
						iconDesc	: 'ui-icon-arrow-u',
						iconAsc		: 'ui-icon-arrow-d'
					}
				},
				pager : {
					minimum_lines	: 5,
					size			: [10, 25, 50, 75, 100],
					location_before	: true,
					positionFixed	: false
				}
			},
			syntaxhighlighter: {
				theme: 'Default',
				brushes: ['Plain', 'Diff'],
				config: {
					useScriptTags : false
				}
			}
		},
		init : function(){
			if (DEBUG){
				console.info('PukiWiki Advance Mobile Debug mode. \nUsing jQuery: ',$.fn.jquery,' / jQuery mobile: ',$.mobile.version);
			}
			this.image_dir = IMAGE_URI+'ajax/';	// デフォルト
			
			// 言語設定
			$.i18n(LANG);

			// Navigation
			$.mobile.page.prototype.options.backBtnText = $.i18n('dialog', 'back');
			$.mobile.page.prototype.options.addBackBtn = true;
			$.mobile.page.prototype.options.backBtnTheme = 'a';
			
			// Page
			$.mobile.page.prototype.options.headerTheme = 'a';		// Page header only
			$.mobile.page.prototype.options.contentTheme = 'c';
			$.mobile.page.prototype.options.footerTheme = 'a';
			
			// Listviews
			$.mobile.listview.prototype.options.headerTheme = 'a';	// Header for nested lists
			$.mobile.listview.prototype.options.theme = 'c';		// List items / content
			$.mobile.listview.prototype.options.dividerTheme = 'b';	// List divider
			
			$.mobile.listview.prototype.options.splitTheme   = 'c';
			$.mobile.listview.prototype.options.countTheme   = 'c';
			$.mobile.listview.prototype.options.filterTheme = 'c';
			$.mobile.listview.prototype.options.filterPlaceholder = $.i18n('dialog', 'filter');

			// Dialog
			$.mobile.dialog.prototype.options.theme = 'd';
			$.mobile.dialog.prototype.options.closeBtnText = $.i18n('dialog', 'close');
			
			// selectmenu
			$.mobile.selectmenu.prototype.options.menuPageTheme = 'b';
			$.mobile.selectmenu.prototype.options.overlayTheme = 'b';
			$.mobile.selectmenu.prototype.options.closeText = $.i18n('dialog', 'close');
			
			// Messages
			$.mobile.loadingMessage = $.i18n('dialog', 'loading');
			$.mobile.loadingMessageTheme = 'a';
			$.mobile.pageLoadErrorMessage = $.i18n('dialog', 'error_page');
			$.mobile.pageLoadErrorMessageTheme = 'e';
		},
		init_dom : function(prefix, callback){
			var self = this;
			prefix = (prefix) ? prefix + ' ' : '*[role="main"] ';
			
			$(prefix + 'img')
			//	.lazyload({ 
			//		placeholder : this.image_dir+'grey.gif',
			//		effect : 'fadeIn'
			//	})
				.bind('taphold',function(){
					window.open($(this).attr('src'));
				})
			;
			
			$(prefix +'#contents ul').listview();

			// 用語集
			this.glossaly(prefix);
			// シンタックスハイライト
			//this.sh(prefix);
			// Bad Behavior
			this.bad_behavior(prefix);
			// Table Sorter（テーブル自動ソート）
			this.tablesorter(prefix);
			
			// アンカースクロール
			$(prefix+'a').each(function(){
				var $this = $(this);	// DOMをキャッシュ
				var href = $this.attr('href');
				var ajax = $this.data('ajax') ? $this.data('ajax') : 'true';
				
				if ( href.match(/^#\w+?/) ){
					// jQuery Mobileで使われるハッシュは、#と、#&で始まる文字列である。
					// 一方、PukiWikiで使われるアンカー用ハッシュは常に英数字である。
					// このため、ローカルリンクの判別は、先頭の文字を\w（A-Za-z0-9_）から判断すればいいと思われる。
					$this.data('ajax', false);
					$this.on('tap', function(){
						self.scrollTo(href);
						return false;
					});
				}
			});
			
			$('.to_header').on('tap', function(){
				self.scrollTo('.ui-page-active header');
			});
			$('.to_footer').on('tap', function(){
				self.scrollTo('.ui-page-active footer');
			});
		},
		scrollTo: function(target){
			var $body;
			var $window = $(window);
			if ($window.scrollTop() === 0) {
				// スクロールが0の時エラーになる問題をごまかす
				$window.scrollTop(1);
			}
			if ( $('html').scrollTop() > 0 ) {
				$body = $('html');
			} else if ( $('body').scrollTop() > 0 ) {
				$body = $('body');
			}else{
				return;
			}
			$body.stop().animate({
				scrollTop: $(target).offset().top
			},{
				duration : 800
			});
		},
		// テーブル自動ソート
		tablesorter:function(prefix){
			var self = this;
			prefix = (prefix) ? prefix + ' ': '';
			
			/* デフォルト値 */
			var config = this.config.tablesorter;
			var tablesorter_widget = function(id){
				return [
					'<div class="table_pager_widget ui-helper-clearfix" id="'+id+'">',
						'<div class="ui-corner-all ui-controlgroup ui-controlgroup-horizontal">',
				//			'<a href="#" class="first">' + $.i18n('dialog','first') + '</a>',
							'<a href="#" class="prev" data-role="button" data-icon="arrow-l" data-iconpos="notext">' + $.i18n('dialog','prev') + '</a>',
							'<input class="pagedisplay" type="text" disabled="disabled" size="8" />',
							'<a href="#" class="next" data-role="button" data-icon="arrow-r" data-iconpos="notext">' + $.i18n('dialog','next') + '</a>',
				//			'<a href="#" class="last">' + $.i18n('dialog','last') + '</a>',
							'<select class="pagesize"></select>',
						'</div>',
					'</div>'
				].join("\n");
			}

			$('.style_table').each(function(elem){
				var table = this;
				var $this = $(this);
				var backup = $this.clone();
				var data = $this.data();
				var config = self.config.tablesorter;
				var sortable = (typeof($this.data('sortable')) === 'undefined' || $this.data('sortable') === true) ? true : false;
					
				if ($this.find('thead').length !== 0 && sortable){
					if ($('tr',this).length > config.pager.minimum_lines && $('thead',this).length !== 0){	// 10行以上の場合ページャーを表示
						// テーブルのページングウィジット
						var pager_id = 'table_pager_'+config.counter;

						// data-属性を使って動作をカスタマイズ
						config.sorter.headers = data.headers;
						config.sorter.sortList = data.sortList;
						config.sorter.parsers = data.parsers;

						$this.tablesorter(config.sorter);
						
						if (config.pager.location_before === true){
							$this.before(tablesorter_widget(pager_id));
						}else{
							$this.after(tablesorter_widget(pager_id));
						}

						var i = 0;
						while (i < config.pager.size.length){
							$('#'+pager_id+' .pagesize').append($('<option>').attr({ value: config.pager.size[i] }).text(config.pager.size[i]));
							i++;
						}

						// ページャーを生成（ID重複しないようにグローバル変数のpukiwiki.tablesorter.counterをカウンタとして使用
						$this.tablesorterPager({
							container: $('#'+pager_id),
							positionFixed: false,
							onthrough: function(e){
								self.glossaly(e);
							}
						});

						$('#'+pager_id).show('clip');
						config.counter++;
					}else{
						$this.tablesorter(config.sorter);
					}
					// ２重に実行されるのを抑止
					$this.data('enabled', true)
				}
			});
		},
		glossaly: function(){
			// ツールチップの処理
			// タップではマウスオーバーを表現できないため、タップした時に、吹き出しを出すという処理とする。
			// 噴出しの処理は、jQuery Mobileのローディングメッセージを流用。
			$('[aria-describedby="tooltip"]:not(a)').each(function(){
				var $this = $(this);
				var msgtext = $this.data('msgtext') ? $this.data('msgtext') :
					$this.attr('title') ? $this.attr('title') : '';
				$this.removeAttr('title');

				$this.bind('tap',function(){
					$.mobile.showPageLoadingMsg();
					if (msgtext === ''){
						var tip;
						$.ajax({
							url:SCRIPT,
							type:'GET',
							cache: true,
							timeout:2000,
							dataType : 'xml',
							data : {
								cmd:'tooltip',
								q:$this.text(),
								cache:true
							},
							async:false,
							success : function(data){
								tip = data.documentElement.textContent;
								$.mobile.hidePageLoadingMsg();
							},
							complete: function(XMLHttpRequest, textStatus){
								msgtext = tip;
								$this.data('msgtext', tip);	// data属性に、テキストを保存。
								$.mobile.showPageLoadingMsg($.mobile.page.prototype.options.headerTheme, tip, true);
								setTimeout(function () { $.mobile.hidePageLoadingMsg(); }, 3000);
							}
						});
					}else{
						$.mobile.showPageLoadingMsg($.mobile.page.prototype.options.headerTheme, msgtext, true);
						setTimeout(function () { $.mobile.hidePageLoadingMsg(); }, 3000);
					}
				});
			});
		},
		// Syntax Hilighter
		sh : function(prefix){
			var self = this;
			
			// シンタックスハイライトするDOMを取得
			var sh = (prefix) ? prefix + ' .sh' : '.sh';

			if ($(sh).length !== 0){
				// ロケール設定
				this.config.syntaxhighlighter = {
					baseUrl:JS_URI+'syntaxhighlighter/',
					target: sh,
					config:{
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
				};
				// SyntaxHilighterを実行
				$.beautyOfCode.beautifyAll(this.config.syntaxhighlighter);
			}
		},
		// Bad Behavior
		bad_behavior: function(prefix){
			prefix = (prefix) ? prefix + ' ': '';

			if (typeof(BH_NAME) !== 'undefined' && typeof(BH_VALUE) !== 'undefined'){
				$(prefix + 'form').append('<input type="hidden" name="'+BH_NAME+'" value="'+BH_VALUE+'" />');
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
			}
		}
	};
	
	$(document).ready(function(){
		var filename = 'jquery.mobile-' + JQUERY_MOBILE_VER + (DEBUG ? '' : '.min');
		if (JQUERY_MOBILE_VER !== 'latest'){
			$("head").append('<link rel="stylesheet" href="http://code.jquery.com/mobile/'+JQUERY_MOBILE_VER+'/'+filename+'.css" />');
			$.getScript('http://code.jquery.com/mobile/'+JQUERY_MOBILE_VER+'/'+filename+'.js', function(){
				$('html').fadeIn('fast');	// スクリプトとCSSが読み込まれた段階で、ページを表示。
				$('html').css('display','block');	// Firefox対策
			});
		}else{
			$("head").append('<link rel="stylesheet" href="http://code.jquery.com/mobile/latest/jquery.mobile.css" />');
			$.getScript('http://code.jquery.com/mobile/latest/jquery.mobile.js', function(){
				$('html').fadeIn('fast');	// スクリプトとCSSが読み込まれた段階で、ページを表示。
				$('html').css('display','block');	// Firefox対策
			});
		}
	});

	$(document).bind('mobileinit', function(){
		var $page = $('[data-role="page"]');
		if (DEBUG){
			var D2 = new Date();
			console.info('jQuery mobile loaded. (Process Time :',D2 - D1,'ms)');
		}
		pukiwiki.init();
		// ページが読み込まれた時のイベント

		// Google Analytics
		if (typeof(GOOGLE_ANALYTICS) !== 'undefined'){
			window._gaq = [['_setAccount',GOOGLE_ANALYTICS],['_trackPageview'],['_trackPageLoadTime']];
			$.getScript(('https:' == location.protocol ? '//ssl' : '//www') + '.google-analytics.com/ga.js');
		}

		$page
			.on('pageload',function(event, ui){
				var f;
				while( f = pkwkBeforeInit.shift() ){
					if( f !== null ){
						f();
					}
				}
				f = null;
			})
			.on('pageshow',function(event, ui){
				pukiwiki.init_dom();
				var f;
				while( f = pkwkInit.shift() ){
					if( f !== null ){
						f();
					}
				}
				f = null;
				if (window._gaq){
					_gaq.push(['_trackPageview', $.mobile.activePage.jqmData('url')]);
				}
			})
		;
		
		// for Adspace
		var $adarea_content = $('#adarea_content');	// 広告領域を取得
		if ($adarea_content.length !== 0){	// 広告領域が存在する場合
			// $('[data-role="header"]').append('<div id="adarea"></div>');	// あえて、スクリプトで広告表示領域を置かない
			//var $ads_top = $('#google_ads_frame');
			var ads_top = $('#adarea_content').find('iframe');	// 広告の部分のソースをキャッシュする
			$(ads_top).appendTo('.adarea');	// 初回読み込み時に広告領域を広告表示領域に代入
			$adarea_content.remove();	// 元々の広告領域を削除（GoogleのTOSに複数設置できない規定があるため。）

			$page.on('pagehide', function(event, ui) {
				// ページが読み込まれるたびに、広告表示領域に広告を代入
				$(ads_top).appendTo('.adarea');
			});
		}

		if (DEBUG){
			var D3 = new Date();
			console.info('Finish. (Process Time :',D3 - D2,'ms / Total :',D3 - D1,'ms)');
		}
	});
} )(jQuery, this, this.document );