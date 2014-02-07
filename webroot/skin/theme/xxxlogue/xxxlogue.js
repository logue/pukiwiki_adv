/**
 * PukiWiki - Yet another WikiWikiWeb clone.
 * xxxloggue skin script.
 * Copyright (c)2010,2014 PukiWiki Advance Developers Team
 *
 * $Id: xxxlogue.js,v 2.3.1 2014/02/07 18:07:00 Logue Exp$
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
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

pukiwiki.skin = {
	// Superfish設定
	// http://users.tpg.com.au/j_birch/plugins/superfish/#options
	suckerfish : {
		autoArrows:		false,	// if true, arrow mark-up generated automatically = cleaner source code at expense of initialisation performance
		dropShadows:	false,
		speed:			'fast',
		animation:		{height:'show'}
	}
}
pukiwiki.register.init(function(){
	// shelf
	$('#content-top').html('<a id="shelf_link" class="noprint" style="cursor:pointer;">Open/Close</a>');
	$('a#shelf_link').click(function(){
		$('#toggle').animate({height: 'toggle'});
	});
	
	var href = $('link[rel=canonical]')[0].href;
	$('#shelf_form').after([
		'<div class="pull-right">',
		// Tweet Button
		// http://twitter.com/about/resources/tweetbutton
		'<a class="twitter-share-button" data-count="vertical" data-lang="'+$('html').attr('lang')+'"></a>',
		// Google +1 button
		// http://www.google.com/webmasters/+1/button/index.html
		'<div class="g-plusone" data-size="tall" data-count="true"></div>',
		// Facebook Like button
		// http://developers.facebook.com/docs/reference/plugins/like/
		(typeof(FACEBOOK_APPID) !== 'undefined') ? '<fb:like href="'+href+'" send="false" layout="box_count" width="50" show_faces="true" font=""></fb:like>' : '',
		'</div>',
		'<div class="clearfix"></div>'].join("\n")
	);
	
	if (typeof(FACEBOOK_APPID) !== 'undefined'){ $('#body').append('<hr /><div style="margin-left:2em;"><fb:comments href="'+href+'" publish_feed="true" width="650" numposts="10" migrated="1" ></fb:comments></div>'); }
});

