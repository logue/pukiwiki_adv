// PukiWiki - Yet another WikiWikiWeb clone.
// xxxloggue skin script.
// Copyright (c)2010 PukiWiki Advance Developers Team

// $Id: default.js,v 1.0.0 2010/08/23 14:35:00 Logue Exp$

// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.

// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.

var colorset = ['blue',		'green',		'orange'];
var ui_theme = ['redmond',	'south-street',	'ui-lightness'];
// var symbol = '&#x25fc;';
var symbol = '■';
var default_set_num = 0;

pukiwiki_skin.custom = {
	// スキンスクリプトのinitが実行される前に実行される関数
	before_init : function(){
		// クッキーが定義されていないときは、blueとし、クッキーに保存
		if (!$.cookie('pkwk-colorset')){ $.cookie('pkwk-colorset',default_set_num,{expires:30,path:'/'}); }
		document.getElementById('coloring').href = SKIN_DIR+'theme/'+THEME_NAME+'/'+colorset[$.cookie('pkwk-colorset')]+'.css';
		document.getElementById('ui-theme').href = 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.4/themes/'+ui_theme[$.cookie('pkwk-colorset')]+'/jquery-ui.css';

		// カラーセットのリンクボタンを生成
		var buffer = '';
		$('#header').before('<p id="colorset" style="float:right; font-size:12px;"></p>');
		for (var n=0; n<colorset.length; n++){
			buffer += '<span style="color:'+colorset[n]+';cursor:pointer;" id="colorset-'+n+'">'+symbol+'</span>&nbsp;';
		}
		$('#colorset').html('Color: '+buffer);
	},
	// スキンスクリプトのinitが実行された前に実行される関数
	init : function(){
		// カラーセットのリンクボタンにイベント割り当て
		$('#colorset span').click(function(){
			var no = this.id.split('-')[1];
			document.getElementById('coloring').href = SKIN_DIR+'theme/'+THEME_NAME+'/'+colorset[no]+'.css';
			document.getElementById('ui-theme').href = 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.4/themes/'+ui_theme[no]+'/jquery-ui.css';
			$.cookie('pkwk-colorset',no,{expires:30,path:'/'});
		});
	},
	// スキンスクリプトのunloadが実行される前に実行される関数
	before_unload : function(){
	},
	// スキンスクリプトのinitが実行された後に実行される関数
	unload: function(){
	},
	// Superfish設定
	// http://users.tpg.com.au/j_birch/plugins/superfish/#options
//	suckerfish : { }
}