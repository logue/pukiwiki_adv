// PukiWiki - Yet another WikiWikiWeb clone.
// classic skin script.
// Copyright (c)2010-2011 PukiWiki Advance Developers Team

// $Id: default.js,v 1.0.5 2011/09/25 17:48:08 Logue Exp$

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
	if(!jQuery) { alert("pukiwiki: jQuery not included."); }
	if(!pukiwiki) { alert("pukiwiki: pukiwiki common script is not included. please check skin.js exsists."); }
	// スキンスクリプトのinitが実行される前に実行される関数
	pukiwiki.register.before_init( function(){
		var home = $('link[rel=home]')[0].href;
		var href = $('link[rel=canonical]')[0].href;
		var lang = $('html').attr('lang');

		if (pukiwiki.isPage){
			$('#body').append([
				'<hr class="noprint" /><ul class="social noprint clearfix">',
				// Hatena
				'<li><a href="http://b.hatena.ne.jp/entry/" class="hatena-bookmark-button" data-hatena-bookmark-layout="standard"></a></li>',
				// Mixi
				'<li><a href="http://mixi.jp/share.pl" class="mixi-check-button" data-url="'+href+'"></a></li>',
				// Gree
				'<li><iframe src="http://share.gree.jp/share?url='+encodeURIComponent(href)+'&amp;type=1&amp;height=20" scrolling="no" frameborder="0" marginwidth="0" marginheight="0" style="border:none; overflow:hidden; width:100px; height:20px;" allowTransparency="true"></iframe></li>',
				// Tweet Button
				// http://twitter.com/about/resources/tweetbutton
				'<li><iframe src="http://platform.twitter.com/widgets/tweet_button.html?lang='+lang+'" allowtransparency="true" frameborder="0" scrolling="no" style="width:200px; height:20px;"></iframe></li>',
				// Tumblr
				// http://www.tumblr.com/docs/ja/share_button
				'<li><a href="http://www.tumblr.com/share" title="Share on Tumblr" style="display:inline-block; text-indent:-9999px; overflow:hidden; width:81px; height:20px; background:url(\'http://platform.tumblr.com/v1/share_1.png\') top left no-repeat transparent;"></a></li>',
				// Google +1 button
				// http://www.google.com/intl/ja/webmasters/+1/button/index.html
				'<li><div id="plusone"></div></li>',
				// Facebook Like button
				// http://developers.facebook.com/docs/reference/plugins/like/
				(typeof(FACEBOOK_APPID) !== 'undefined') ? '<li><div class="fb-like" data-href="'+href+'" data-layout="button_count"  data-send="true" data-width="400" data-show-faces="true"></div></li>' : '',
			'</ul>',
			(typeof(FACEBOOK_APPID) !== 'undefined') ? '<hr class="noprint" /><div class="noprint" style="margin-left:2em;"><div class="fb-comments" href="'+href+'" publish_feed="true" width="650" numposts="10" migrated="1"></div></div>' : ''
			].join("\n"));

			$.getScript('http://b.st-hatena.com/js/bookmark_button_wo_al.js');
			$.getScript('http://platform.twitter.com/widgets.js');
			$.getScript('http://static.mixi.jp/js/share.js');

			$.getScript('http://apis.google.com/js/plusone.js',function(){
				gapi.plusone.render( document.getElementById('plusone'),{ lang: lang, parsetags:'explicit', size:'medium', 'count':'true' });
			});
		}
	});

	// スキンスクリプトのinitが実行された前に実行される関数
	pukiwiki.register.init( function(){
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