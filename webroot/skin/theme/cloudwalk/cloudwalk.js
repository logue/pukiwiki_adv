// PukiWiki - Yet another WikiWikiWeb clone.
// clowdwalk skin border-radius fix for IE less than 9
// Copyright (c)2010-2011 PukiWiki Advance Developer Team

// $Id: cloudwalk.js,v 1.0.2 2011/05/29 00:02:00 Logue Exp$

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

pukiwiki.register.init( function(){
	// IE9未満判定
	if ($.support.leadingWhitespace !== true){
		// 末尾にこれを追加しないとborder-radiusが反映されない。
		$('<div class="bar" style="display:none;"></div>').appendTo('body');
		
		// border-radiusをIEでも有効にする（ie-css3.htcを読み込ませる）
		$('.bar,#navigator,#nav,#header,#footer').css({
			'behavior': 'url('+SKIN_DIR+'js/ie-css3.htc)'
		});
	}
});
