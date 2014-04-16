/**
 * PukiWiki - Yet another WikiWikiWeb clone.
 * xxxloggue skin script.
 * Copyright (c)2010,2014 PukiWiki Advance Developers Team
 *
 * $Id: default.js,v 1.0.5 2014/02/07 18:07:00 Logue Exp$
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
	
	var default_theme = $('#ui-theme').attr('href');

	// スキンスクリプトのinitが実行される前に実行される関数
	pukiwiki.register.before_init(function(){
		// クッキーが定義されていないときは、blueとし、クッキーに保存

		var color = colorset[$.cookie('pkwk-colorset')];
		$('#coloring').attr('href', SKIN_DIR+'theme/default/'+color[0]+'.css');
		$('#ui-theme').attr('href', COMMON_URI+'css/jquery-ui/themes/'+color[1]+'/jquery-ui.css');

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
			$('#ui-theme').attr('href',COMMON_URI+'css/jquery-ui/themes/'+colorset[n][1]+'/jquery-ui.css');
			$.cookie('pkwk-colorset',n,{expires:30,path:'/'});
		});
	});

} )(jQuery, pukiwiki);