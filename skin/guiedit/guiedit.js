//
//	guiedit - PukiWiki Plugin
//
//	License:
//	  GNU General Public License Version 2 or later (GPL)
//	  http://www.gnu.org/licenses/gpl.html
//
//	Copyright (C) 2006-2007 garand
//	PukiWiki : Copyright (C) 2001-2006 PukiWiki Developers Team
//	FCKeditor : Copyright (C) 2003-2007 Frederico Caldeira Knabben
//	PukiWiki Plus! : Copyright (C) 2009 Katsumi Saito
//	PukiWiki Advance : Copyright (C) 2010 PukiWiki Advance Developers Team
//
//	File:
//	  guiedit.js
//	  guiedit プラグインに使用する JavaScript
//


//	FCKeditor のインスタンス
var editor = null;

//	FCKeditor で編集する HTML
var html = '';

//	Ajax 送受信の開始時間
var start_time;

//	ページが読み込まれた時
$(document).ready(function(){
	if (!FCKeditor_IsCompatibleBrowser()) {
		document.location = document.URL + '&text=1';
	}
	
	GetSource();

	var oFCKeditor = new FCKeditor('msg', '100%', 300, 'Normal');
	
	oFCKeditor.BasePath = FCK_PATH;
	oFCKeditor.Config['CustomConfigurationsPath'] = GUIEDIT_PATH + "fckconfig.js";
	oFCKeditor.Config['EditorAreaCSS'] = GUIEDIT_PATH + "editorarea.css.php";
	oFCKeditor.Config['SkinPath'] = GUIEDIT_PATH + "fck_skin/";
	oFCKeditor.Config['PluginsPath'] = GUIEDIT_PATH + "fck_plugins/";
	oFCKeditor.Config['SmileyPath'] = SMILEY_PATH;
	
	oFCKeditor.ReplaceTextarea();
});

//	FCKeditor の作成が完了した時
function FCKeditor_OnComplete(editorInstance) {
	editor = editorInstance;
	
	SetBeforeUnload(true);
	
	if (html) {
		editor.SetHTML(html, true);
		html = '';
	}
}

//	onbeforeunload イベントの設定
function SetBeforeUnload(bEnable) {
	window.onbeforeunload = function () {
		if(bEnable && editor.IsDirty()){
			return '続行すると変更内容が破棄されます。';
		}
		return;
	};
}

//	編集するデータ
function GetSource() {
	$.ajax({
		dataType:'xml',
		type:'POST',
		data: {
			cmd : 'guiedit',
			edit: 1,
			page: PAGE,
			id:$('#edit_form').val()
		},
		success: function(data){
			html = data.documentElement.firstChild.nodeValue;
			if (editor) {
				editor.SetHTML(html, true);
				html = '';
			}
		}
	});
}

//	テンプレート
function Template() {
	$.ajax({
		dataType:'xml',
		type:'POST',
		data: {
			cmd : 'guiedit',
			edit: 1,
			page: PAGE,
			template_page:$('select[name=template_page]').val()
		},
		success: function(data){
			html = data.documentElement.firstChild.nodeValue;
			if (editor) {
				editor.SetHTML(html, true);
				html = '';
			}
		}
	});
}

//	プレビュー
function Preview() {
	var start_time = (new Date()).getTime();
	$.ajax({
		dataType:'xml',
		type:'POST',
		global : false,
		data: {
			cmd : 'guiedit',
			edit: 1,
			page: PAGE,
			msg : editor.GetXHTML(true)
		},
		beforeSend: function(){
			$('#preview_indicator').html('Now Loading...');
			if ($('#preview_indicator').css('display') == 'none'){
				$('#preview_indicator').css('display','block');
				$('#preview_area').css('display','block');
			}
		},
		success: function(data){
			var html = data.documentElement.firstChild.nodeValue;
			var time = ((new Date()).getTime() - start_time) / 1000;
			start_time = 0;
			$('#preview_indicator').html('Convert time : '+time);
			$('#preview_area').html(html);
		}
	});
}

//	ページの更新
function Write() {
	SetBeforeUnload(false);
}
