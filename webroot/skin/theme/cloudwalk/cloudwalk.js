/**
 * PukiWiki - Yet another WikiWikiWeb clone.
 * xxxloggue skin script.
 * Copyright (c)2010,2014 PukiWiki Advance Developers Team
 *
 * $Id: cloudwalk.js,v 1.0.4 2014/02/07 18:07:00 Logue Exp$
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

pukiwiki.register.init( function(){
	// IE9未満判定
	if ($.support.leadingWhitespace !== true){
		// 末尾にこれを追加しないとborder-radiusが反映されない。
		$('<div class="bar" style="display:none;"></div>').appendTo('body');
		
		// border-radiusをIEでも有効にする（ie-css3.htcを読み込ませる）
		$('.bar,#navigator,#nav,#header,#footer').css({
			'behavior': 'url('+JS_URI+'ie-css3.htc)'
		});
	}
});
