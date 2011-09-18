// PukiWiki - Yet another WikiWikiWeb clone.
// xxxloggue skin script.
// Copyright (c)2010-2011 PukiWiki Advance Developers Team

// $Id: default.js,v 1.0.4 2011/09/12 00:22:08 Logue Exp$

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

(function($, pukiwiki, window, document, undef){
	var colorset = [
		// [css file name, ui_theme name]
		['blue',	'redmond'],
		['green',	'south-street'],
		['orange',	'ui-lightness'],
		['red',		'blitzer']
	];
	// var symbol = '&#x25fc;';
	var symbol = '■';
	var default_set_num = 0;

	// スキンスクリプトのinitが実行される前に実行される関数
	pukiwiki.register.before_init( function(){
		// クッキーが定義されていないときは、blueとし、クッキーに保存
		if (!$.cookie('pkwk-colorset')){
			$.cookie('pkwk-colorset', default_set_num,{expires:30,path:'/'});
		}
		var color = colorset[$.cookie('pkwk-colorset')];
		document.getElementById('coloring').href = SKIN_DIR+'theme/'+THEME_NAME+'/'+color[0]+'.css';
		document.getElementById('ui-theme').href = 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/themes/'+color[1]+'/jquery-ui.css';

		// カラーセットのリンクボタンを生成
		var buffer = '';
		$('#header').before('<p id="colorset" style="text-align:right; font-size:12px;" class="noprint"></p>');
		for (var n=0; n<colorset.length; n++){
			buffer += '<span style="color:'+colorset[n][0]+';cursor:pointer;" id="colorset-'+n+'">'+symbol+'</span>&#xa0;';
		}
		$('#colorset').html('Color: '+buffer);
		var home = $('link[rel=home]')[0].href;
		var href = $('link[rel=canonical]')[0].href;
		var lang = $('html').attr('lang');

		if (pukiwiki.isPage){
			$('#body').append([
				'<hr class="noprint" /><ul class="social noprint clearfix">',
				// Hatena
				'<li><a href="http://b.hatena.ne.jp/entry/" class="hatena-bookmark-button" data-hatena-bookmark-layout="standard"></a></li>',
				// Mixi
				'<li><a href="http://mixi.jp/share.pl" class="mixi-check-button" data-url="'+href+'">mixi check</a></li>',
				// Gree
				'<li><iframe src="http://share.gree.jp/share?url='+encodeURIComponent(href)+'&amp;type=1&amp;height=20" scrolling="no" frameborder="0" marginwidth="0" marginheight="0" style="border:none; overflow:hidden; width:100px; height:20px;" allowTransparency="true"></iframe></li>',
				// Tweet Button
				// http://twitter.com/about/resources/tweetbutton
				'<li><iframe allowtransparency="true" frameborder="0" scrolling="no" src="//platform.twitter.com/widgets/tweet_button.html?lang='+lang+'" style="width:130px; height:20px;"></iframe></li>',
			//	'<a href="https://twitter.com/share" class="twitter-share-button" data-count="horizontal" data-via="logue256" data-lang="ja">ツイート</a>',
				// Google +1 button
				// http://www.google.com/intl/ja/webmasters/+1/button/index.html
				'<li><iframe width="100%" scrolling="no" frameborder="0" title="+1" vspace="0" tabindex="-1" style="position: static; left: 0pt; top: 0pt; width: 90px; margin: 0px; border-style: none; height: 20px; visibility: visible;" src="https://plusone.google.com/u/0/_/+1/fastbutton?url='+encodeURIComponent(href)+'&amp;size=medium&amp;count=true&amp;annotation=&amp;hl='+lang+'&amp;jsh=r%3Bgc%2F23803279-4555db52#id=I2_1316315799965&amp;parent='+encodeURIComponent(home)+'&amp;rpctoken=478620406&amp;_methods=onPlusOne%2C_ready%2C_close%2C_open%2C_resizeMe" name="I2_1316315799965" marginwidth="0" marginheight="0" id="I2_1316315799965" hspace="0" allowtransparency="true"></iframe></li>',
				// Tumblr
				// http://www.tumblr.com/docs/ja/share_button
				'<li><a href="http://www.tumblr.com/share" title="Share on Tumblr" style="display:inline-block; text-indent:-9999px; overflow:hidden; width:81px; height:20px; background:url(\'http://platform.tumblr.com/v1/share_1.png\') top left no-repeat transparent;">Share on Tumblr</a></li>',
				// Facebook Like button
				// http://developers.facebook.com/docs/reference/plugins/like/
				(typeof(FACEBOOK_APPID) !== 'undefined') ? '<li><div class="fb-like" data-href="'+href+'" data-layout="button_count"  data-send="true" data-width="400" data-show-faces="true"></div></li>' : '',
			'</ul>'].join("\n"));
			
			//'<hr /><div class="noprint" style="margin-left:2em;"><div class="fb-comments" href="'+href+'" publish_feed="true" width="650" numposts="10" migrated="1"></div></div>'
			$.getScript('http://b.st-hatena.com/js/bookmark_button_wo_al.js');
			$.getScript('http://platform.twitter.com/widgets.js');
			$.getScript('http://static.mixi.jp/js/share.js');

		}
	});

	// スキンスクリプトのinitが実行された前に実行される関数
	pukiwiki.register.init( function(){
		// カラーセットのリンクボタンにイベント割り当て
		$('#colorset span').click(function(){
			var n = this.id.split('-')[1];
			document.getElementById('coloring').href = SKIN_DIR+'theme/'+THEME_NAME+'/'+colorset[n][0]+'.css';
			document.getElementById('ui-theme').href = 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/themes/'+colorset[n][1]+'/jquery-ui.css';
			$.cookie('pkwk-colorset',n,{expires:30,path:'/'});
		});
	});

/*
	// PukiWiki Advance オーバーライド設定
	pukiwiki.custom = {
		// Superfish設定
		// http://users.tpg.com.au/j_birch/plugins/superfish/#options
		suckerfish : { }
	};
*/
} )(jQuery, pukiwiki, this, this.document );