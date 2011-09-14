/*!
 * jQuery beautyOfCode Plugin for HTML5
 * Copyright 2011 Logue
 * Version: 0.2.1
 * Site: http://logue.be/
 * Source: https://bitbucket.org/logue/beautyofcode-for-html5/
 * License: Apache License, Version 2.0; you may not use this file except in compliance with the License.
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Based on
 * jQuery beautyOfCode Plugin 0.2
 * Copyright 2010 Lars Corneliussen
 */

$.beautyOfCode = {
	settings: {
		// should the syntax highlighter and brushes
		// be loaded dynamically
		autoLoad: true,
		// the base url to alex' hosted sources
		// NOTICE : This fork does not compatible with 3.x.
		// http://alexgorbatchev.com/wiki/SyntaxHighlighter:Hosting
		baseUrl: 'http://alexgorbatchev.com.s3.amazonaws.com/pub/sh/2.1.382/',
		// the baseurl for the hosted scripts
		scripts: 'scripts/',
		// the baseurl for the hosted styles
		styles: 'styles/',
		// themes from http://alexgorbatchev.com/wiki/SyntaxHighlighter:Themes
		theme: 'Default',
		// the brushes that should be loaded - case sensitive!
		// http://alexgorbatchev.com/wiki/SyntaxHighlighter:Brushes
		brushes: ['Plain'],
		// overrides for configurations and defaults
		// http://alexgorbatchev.com/wiki/SyntaxHighlighter:Configuration
		config: {
			debug : false
		},
		defaults: {},
		// function to be called, when all scripts are loaded
		ready: function() {
			$.beautyOfCode.beautifyAll();
		}
	},

	init: function(settings) {
		settings = $.extend({},
		$.beautyOfCode.settings, settings);

		if (!settings.config.clipboardSwf)
		settings.config.clipboardSwf = settings.baseUrl + settings.scripts + 'clipboard.swf';

		$(document).ready(function() {
			if (!settings.autoLoad) {
				settings.ready();
			}
			else {
				$.beautyOfCode.utils.loadCss(settings.baseUrl + settings.styles + 'shCore.css');
				$.beautyOfCode.utils.loadCss(settings.baseUrl + settings.styles + 'shTheme' + settings.theme + '.css', 'shTheme');

				var scripts = new Array();
				scripts.push(settings.baseUrl + settings.scripts + 'shCore.js');
				$.each(settings.brushes,
				function(i, item) {
					scripts.push(settings.baseUrl + settings.scripts + 'shBrush' + $.beautyOfCode.utils.getBrush(item) + ".js");
				});

				$.beautyOfCode.utils.loadAllScripts(
				scripts,
				function() {
					if (settings && settings.config)
					$.extend(SyntaxHighlighter.config, settings.config);

					if (settings && settings.defaults)
					$.extend(SyntaxHighlighter.defaults, settings.defaults);

					settings.ready();
				});
			}
		});
	},

	beautifyAll: function(settings) {
		settings = $.extend({},
		$.beautyOfCode.settings, settings);

		var $sh = $('pre.code, code.code');
		var brushes = new Array();
		var sh_doms = new Array();

		// DOMを走査し、使用するbrushのリストを作成
		$sh.each(function(){
			var $this = $(this);
			// DOMをキャッシュ（ready時に使用）
			sh_doms.push($this);

			// 読み込むbrushを取得
			var brush = self.utils.getBrush( $this.data().brush );

			// 非同期通信で読み込んだDOMにも反映させるため、
			// 重複してbrushを読み込まないようにする
			if ($.inArray(brush, settings.brushes) === -1){
				settings.brushes.push(brush);
			}
		});
		settings.ready = function() {
			// SyntaxHilighterを反映させる
			for (var i = 0; i <= sh_doms.length-1; i++){
				var data = sh_doms[i].data();
				sh_doms[i].beautifyCode(data.brush);
			}
		};
		// SyntaxHilighterを実行
		$.beautyOfCode.init(settings);
	},
	utils: {
		loadScript: function(url, complete) {
			$.ajax({
				url: url,
				complete: function() {
					complete();
				},
				type: 'GET',
				dataType: 'script',
				cache: true
			});
		},
		loadAllScripts: function(urls, complete) {
			if (!urls || urls.length == 0)
			{
				complete();
				return;
			}
			var first = urls[0];
			$.beautyOfCode.utils.loadScript(
				first,
				function() {
					$.beautyOfCode.utils.loadAllScripts(
						urls.slice(1, urls.length),
						complete
					);
				}
			);

		},
		loadCss: function(url, id) {
			var headNode = $("head")[0];
			if (url && headNode)
			{
				var styleNode = document.createElement('link');
				styleNode.setAttribute('rel', 'stylesheet');
				styleNode.setAttribute('href', url);
				if (id) styleNode.id = id;
				
				headNode.appendChild(styleNode);
			}
		},
		addCss: function(css, id) {
			var headNode = $("head")[0];
			if (css && headNode)
			{
				var styleNode = document.createElement('style');

				styleNode.setAttribute('type', 'text/css');

				if (id) styleNode.id = id;
				if (styleNode.styleSheet) {
					// for IE	
					styleNode.styleSheet.cssText = css;
				}
				else {
					$(styleNode).text(css);
				}
				headNode.appendChild(styleNode);
			}
		},
		addCssForBrush: function(brush, highlighter) {
			if (brush.isCssInitialized)
				return;

			$.beautyOfCode.utils.addCss(highlighter.Style);

			brush.isCssInitialized = true;
		},
		getBrush : function(brush_name){
			var alias = {
				'AppleScript'	: ['applescript'],
				'AS3'			: ['actionscript3', 'as3', 'as'],
				'Bash'			: ['bash', 'shell'],
				'ColdFusion'	: ['coldfusion', 'cf'],
				'Cpp'			: ['cpp', 'c', 'h', 'hpp'],
				'CSharp'		: ['c#', 'c-sharp', 'csharp', 'cs'],
				'Css'			: ['css'],
				'Delphi'		: ['delphi', 'pascal', 'pas'],
				'Diff'			: ['diff', 'patch'],
				'Erlang'		: ['erl', 'erlang'],
				'Groovy'		: ['groovy'],
				'Java'			: ['java'],
				'JavaFX'		: ['jfx', 'javafx'],
				'JScript'		: ['js', 'jscript', 'javascript', 'json'],
				'Perl'			: ['perl', 'pl'],
				'Php'			: ['php'],
				'Plain'			: ['text', 'plain'],
				'Python'		: ['py', 'python'],
				'Ruby'			: ['ruby', 'rails', 'ror', 'rb'],
				'Sass'			: ['sass', 'scss'],
				'Scala'			: ['scala'],
				'Sql'			: ['sql'],
				'Vb'			: ['vb', 'vbnet'],
				'Xml'			: ['xml', 'xhtml', 'xslt', 'html']
			};

			for (var brush in alias){
				for (var i = 0; i <= alias[brush].length-1; i++){
					if (alias[brush][i] == brush_name){
						return brush;
					}
				}
			}

			return 'Plain';
		}
	}
};

$.fn.beautifyCode = function(brush, params) {
	var saveBrush = brush;
	var saveParams = params;

	// iterate all elements
	this.each(function(i, item) {
		var $item = $(item);

		// Load from data- attribute
		var data = $item.data();
		
		// set param from data- attribute
		var elementParams = {
			'class-name'	: data.className,
			'first-line'	: data.firstLine,
			'tab-size'		: data.tabSize,
			'smart-tabs'	: data.smartTabs,
			'ruler'			: data.ruler,
			'gutter'		: data.gutter,
			'highlight'		: data.highlight,
			'toolbar'		: data.toolbar,
			'collapse'		: data.collapse ,
			'auto-links'	: data.autoLinks,
			'light'			: data.light,
			'wrap-lines'	: data.warpLines,
			'html-script'	: data.htmlScript
		};

		// set brush
		var brush = data.brush ? data.brush : 'plain';
		
		// text node
		var code = $item.text();
		
		// Fix IE < 9 brake line Bug
		if($.browser.msie && $.browser.version > 9){
			code = code.replace(/\x0D\x0A|\x0D|\x0A/g,'\n\r');
		}

		var params = $.extend({},
		SyntaxHighlighter.defaults, saveParams, elementParams);

		// Instantiate a brush
		if (params['html-script'] == true)
		{
			highlighter = new SyntaxHighlighter.HtmlScript(brush);
		}
		else
		{
			var brush = SyntaxHighlighter.utils.findBrush(brush);

			if (brush)
				highlighter = new brush();
			else
				return;
		}

		// i'm not sure if this is still neccessary
		$.beautyOfCode.utils.addCssForBrush(brush, highlighter);

		//highlighter.highlight($item.html(), params);
		highlighter.highlight(code, params);
		highlighter.source = item;

		$item.replaceWith(highlighter.div);
	});
};