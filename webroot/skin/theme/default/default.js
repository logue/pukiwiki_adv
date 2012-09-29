// PukiWiki - Yet another WikiWikiWeb clone.
// xxxloggue skin script.
// Copyright (c)2010-2012 PukiWiki Advance Developers Team

// $Id: default.js,v 1.0.5 2012/09/21 16:12:13 Logue Exp$

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

(function($, pukiwiki){
	if(!jQuery) { alert("pukiwiki: jQuery not included."); }
	if(!pukiwiki) { alert("pukiwiki: pukiwiki common script is not included. please check skin.js exsists."); }

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
	pukiwiki.register.before_init(function(){
		// クッキーが定義されていないときは、blueとし、クッキーに保存
		if (!$.cookie('pkwk-colorset')){
			$.cookie('pkwk-colorset', default_set_num,{expires:30,path:'/'});
		}
		var color = colorset[$.cookie('pkwk-colorset')];
		$('#coloring').attr('href', SKIN_DIR+'theme/default/'+color[0]+'.css');
		$('#ui-theme').attr('href','http://ajax.aspnetcdn.com/ajax/jquery.ui/'+$.ui.version+'/themes/'+color[1]+'/jquery-ui.css');

		// カラーセットのリンクボタンを生成
		var buffer = '';
		$('#header').before('<p id="colorset" style="float:right; font-size:12px;" class="noprint"></p>');
		for (var n=0; n<colorset.length; n++){
			buffer += '<span style="color:'+colorset[n][0]+';cursor:pointer;" id="colorset-'+n+'">'+symbol+'</span>&#xa0;';
		}
		$('#colorset').html('Color: '+buffer);
	});

	// スキンスクリプトのinitが実行された前に実行される関数
	pukiwiki.register.init(function(){
		// カラーセットのリンクボタンにイベント割り当て
		$('#colorset span').click(function(){
			var n = this.id.split('-')[1];
			$('#coloring').attr('href', SKIN_DIR+'theme/'+THEME_NAME+'/'+colorset[n][0]+'.css');
			$('#ui-theme').attr('href','http://ajax.aspnetcdn.com/ajax/jquery.ui/'+$.ui.version+'/themes/'+colorset[n][1]+'/jquery-ui.css');
			$.cookie('pkwk-colorset',n,{expires:30,path:'/'});
		});
	});

} )(jQuery, pukiwiki);