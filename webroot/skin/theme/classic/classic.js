// PukiWiki - Yet another WikiWikiWeb clone.
// xxxloggue skin script.
// Copyright (c)2010 PukiWiki Advance Developers Team

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

// スキンスクリプトのinitが実行される前に実行される関数
pukiwiki.register.before_init( function(){
	var href = $('link[rel=canonical]')[0].href;
	$('#hgroup').after([
		'<div style="float:right;" class="noprint">',
		// Tweet Button
		// http://twitter.com/about/resources/tweetbutton
		'<a class="twitter-share-button" data-count="vertical" data-lang="'+$('html').attr('lang')+'"></a>',
		// Google +1 button
		// http://www.google.com/webmasters/+1/button/index.html
		'<div class="g-plusone" data-size="tall" data-count="true"></div>',
		// Facebook Like button
		// http://developers.facebook.com/docs/reference/plugins/like/
		(typeof(FACEBOOK_APPID) !== 'undefined') ? '<fb:like href="'+href+'" send="false" layout="box_count" width="50" show_faces="true" font=""></fb:like>' : '',
		'</div>'].join("\n")
	);
	
	if (typeof(FACEBOOK_APPID) !== 'undefined'){ $('#body').append('<hr /><div style="margin-left:2em;" class="noprint"><fb:comments href="'+href+'" publish_feed="true" width="650" numposts="10" migrated="1" ></fb:comments></div>'); }
});

// スキンスクリプトのinitが実行された前に実行される関数
pukiwiki.register.init( function(){
	// カラーセットのリンクボタンにイベント割り当て
	$('#colorset span').click(function(){
		var n = this.id.split('-')[1];
		document.getElementById('coloring').href = SKIN_DIR+'theme/'+THEME_NAME+'/'+colorset[n][0]+'.css';
		document.getElementById('ui-theme').href = 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/themes/'+colorset[n][1]+'/jquery-ui.css';
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
