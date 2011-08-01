// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: locale.js,v 0.0.3 2010/07/30 15:04:00 Logue Exp $

// Pukiwiki skin script for jQuery
// Copyright (c)2010-2011 PukiWiki Advance Developer Team
//			  2010	  Logue <http://logue.be/> All rights reserved.

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

/**************************************************************************************************/

$.i18n('en_US.pukiwiki', {
	copyed		: 'It copied it onto the clipboard.',
	select		: 'Please select the range of the object.',
	fontsize	: 'Size of character ( It specifies it with % or pt[omit]. ):',
	to_ncr		: 'It converts it into the numeric character reference.',
	hint		: [
		'The color selected first becomes the color specification and the character color and the color selected next become the background colors.',
		'',
		'After processing the range of the selection, the range remains selecting it.',
		'Please input it after moving the cursor with [ → ] key when you continuously input the character.',
		'',
		'[ &# ] button converts the selection character string into the numeric character reference.'
	].join('\n'),
	inline1		: 'Please input the plugin name. [ & is omitted ]',
	inline2 	: 'Please input the parameter. [ ( )Inside ]',
	inline3 	: 'Please input the text. [ { }Inside ]',
	link 		: 'Please input the character that sets the link.',
	url			: 'Please input URL the link ahead.',
	elem		: 'Please select the processed object.',
	unload		: 'Do you submit your change ?',
	cancel		: 'Do you discard your change ?',
	submit		: 'Your change was sent. Check please!'
});

$.i18n('en_US.markitup', {
	Heading1		: 'Heading level 1',
	Heading2		: 'Heading level 2',
	Heading3		: 'Heading level 3',
	List			: 'Bulleted list',
	List_Numeric	: 'Numeric lists',
	Definition		: 'Definitions',
	Definition_word	: 'Word',
	Definition_desc	: 'Description',
	Quote			: 'Quote',
	Horizontal_Line	: 'Horizontal Line',
	Newline			: 'Newline',
	Link			: 'Link',
	File			: 'File',
	Bold			: 'Bold',
	Italic			: 'Italic',
	Strike			: 'Stroke through',
	Left			: 'Left',
	Center			: 'Center',
	Right			: 'Right'
});

$.i18n('en_US.dialog',{
	ok		: 'OK',
	cancel	: 'Cancel',
	close	: 'Close',
	yes		: 'Yes',
	no		: 'No',
	next	: 'Next',
	prev	: 'Previous',
	error	: 'Error',
});

$.i18n('en_US.player',{
	play		: 'Play',
	pasue		: 'Pause',
	stop		: 'Stop',
	volume		: 'Volume',
	volume_max	: 'Max Volume',
	volume_min	: 'Mute',
	seek		: 'Seek',
	fullscreen	: 'Full Screen'
});
/**************************************************************************************************/

$.i18n('ja_JP.pukiwiki', {
	copyed		: 'クリップボードにコピーしました。',
	select		: '対象範囲を選択してください。',
	fontsize	: '文字の大きさ ( % または pt[省略可] で指定)：',
	to_ncr		: '数値文字参照へ変換',
	hint_text1	: [
		'色指定は、最初に選択した色が文字色、次に選択した色が背景色になります。',
		'',
		'選択範囲を処理後は、その範囲が選択したままになっています。',
		'続けて文字を入力する場合は、[ → ]キーでカーソルを移動してから入力してください。',
		'',
		'-- +α(アドバンスモード) --',
		'',
		'[ &# ] ボタンは、選択文字列を数値文字参照に変換します。'
	].join('\n'),
	hint_text2	: '表示範囲が先頭に戻ってしまい、処理した範囲が見えなくなった時は、[ ESC ]キーを押してみてください。',
	to_easy		: 'イージーモードに変更しました。',
	to_adv		: 'アドバンスモードに変更しました。',
	reload		: [
		'リロード後に有効になります。',
		'',
		'今すぐリロードしますか？'
	].join('\n'),
	inline1		: 'プラグイン名を入力してください。[ ＆ は省く ]',
	inline2 	: 'パラメーターを入力してください。[ ( )内 ]',
	inline3 	: '本文を入力してください。[ { }内 ]',
	link 		: 'リンクを設定する文字を入力してください。',
	url			: 'リンク先のURLを入力してください。',
	elem		: '処理をする対象を選択してください。',
	unload		: '変更をサーバーに反映しますか？',
	cancel		: '変更を破棄しますか？',
	submit		: '変更をサーバーに送信しました。確認してください。'
});

$.i18n('ja_JP.markitup', {
	Heading1		: '大見出し',
	Heading2		: '中見出し',
	Heading3		: '小見出し',
	List			: '番号なしリスト',
	List_Numeric	: '番号付きリスト',
	Definition		: '定義リスト',
	Definition_word	: '定義語',
	Definition_desc	: '説明',
	Quote			: '引用',
	Horizontal_Line	: '区切り線',
	Newline			: '改行',
	Link			: 'リンク',
	File			: 'ファイル',
	Bold			: '太字',
	Italic			: '斜体',
	Strike			: '打ち消し線',
	Left			: '左寄せ',
	Center			: '中央寄せ',
	Right			: '右寄せ'
});

$.i18n('ja_JP.dialog',{
	ok		: 'OK',
	cancel	: 'キャンセル',
	close	: '閉じる',
	yes		: 'はい',
	no		: 'いいえ',
	error	: 'エラー'
});

$.i18n('ja_JP.player',{
	play		: '再生',
	pasue		: '一時停止',
	stop		: '停止',
	volume		: '音量',
	volume_max	: '最大音量',
	volume_min	: 'ミュート',
	seek		: 'シーク',
	fullscreen	: '全画面'
});

$.i18n('ja_JP.colorbox',{
	slideshowStart	: 'スライドショー開始',
	slideshowStop	: 'スライドショー停止',
	current			: '{current}枚目（全{total}枚）',
	previous		: '←前の画像へ',
	next			: '次の画像へ→',
	close			: '閉じる'
});
/**************************************************************************************************/

$.i18n('ko_KR.pukiwiki', {
	copyed		: '클립보드에 카피했습니다',
	select		: '대상 범위를 선택해 주세요.',
	fontsize	: '문자의 크기 (% 또는 pt[생략가능] 으로 지정):',
	to_ncr		: '수치 문자 참조에 변환',
	hint_text1	: [
		'색지정은, 최초로 선택한 색이 문자색, 다음에 선택한 색이 배경색이 됩니다.',
		'',
		'선택 범위를 처리 후는, 그 범위가 선택한 채로 있습니다.',
		'계속해 문자를 입력하는 경우는,[ → ]키로 커서를 이동하고 나서 입력해 주세요.',
		'',
		'-- +α(어드밴스 모드) --',
		'',
		'[ &# ] 버튼은, 선택 문자열을 수치 문자 참조로 변환합니다.'
	].join('\n'),
	hint_text2	: '표시 범위가 선두로 돌아와 버려, 처리한 범위가 안보이게 되었을 때는,[ ESC ]키를 눌러 보세요.',
	to_easy		: '이지 모드로 변경했습니다.',
	to_adv		: '어드밴스 모드로 변경했습니다.',
	reload		: [
		'리로드 후에 유효하게 됩니다.',
		'',
		'금방 리로드 합니까?'
	].join('\n'),
	inline1		: '플러그 인명을 입력해 주세요.[ ＆ 는 생략한다 ]',
	inline2 	: '파라미터를 입력해 주세요.[ ( ) 안 ]',
	inline3 	: '본문을 입력해 주세요.[ { } 안 ]',
	link 		: '링크를 설정하는 문자를 입력해 주세요.',
	url			: '링크처의 URL를 입력해 주세요.',
	elem		: '처리를 하는 대상을 선택해 주세요.',
	unload		: '변경을 서버에 반영합니까?', 
	cancel		: '변경을 파기합니까?', 
	submit		: '변경을 서버에 송신했습니다.확인해 주세요.' 
});

/**************************************************************************************************/
$.i18n('zh_TW.pukiwiki', {
	copyed		: '剪貼板複製。',
	select		: '請選擇對象範圍。',
	fontsize	: '文字的大小 ( % 又 pt[省略可]指定):',
	to_ncr		: '變換向數值文字參照',
	hint_text1	: [
		'顏色指定，在最初時變成為選擇了的顏色文字顏色，其次選擇了的顏色背景顏色。',
		'',
		'處理後，那個範圍選擇了的著變成選擇範圍。',
		'如果繼續輸入文字用、[→]鑰匙移動光標之後請輸入。',
		'',
		'-- +α(擴張方式) --',
		'',
		'[ &# ] 按鈕，數值文字參照轉換選擇字符串。'
	].join('\n'),
	hint_text2	: '表示範圍前頭回來了，處理了的範圍看不見了的時候，請[試著按ESC ]鑰匙。',
	to_easy		: '變更了為簡單方式。',
	to_adv		: '變更了為擴張方式。',
	reload		: [
		'對再讀包含在內後變得有效。',
		'',
		'現在馬上再讀包含在內做嗎？'
	].join('\n'),
	inline1		: '請輸入插件名。[ ＆ 省卻 ]',
	inline2 	: '請輸入參數。[ ( )內側 ]',
	inline3		: '請輸入本文。[ { }內側 ]',
	link 		: '請輸入設定鏈接的文字。',
	url			: '請輸入鏈接處的URL。',
	elem		: '請選擇做處理的對象。',
	unload		: '服務器反映變更嗎？', 
	cancel		: '廢棄變更嗎？', 
	submit		: '服務器發送了變更。請確認一下。' 
});


$.i18n('zh_TW.markitup', {
	Heading1		: '標題一',
	Heading2		: '標題二',
	Heading3		: '標題三',
	List			: '條列',
	List_Numeric	: '數字條列',
	Definition		: '定義',
	Definition_word	: '定義',
	Definition_desc	: '描述',
	Quote			: '引用文字',
	Horizontal_Line	: '水平線',
	Newline			: '換行',
	Link			: '連結',
	File			: '附件的名稱',
	Bold			: '粗體字',
	Italic			: '斜體字',
	Strike			: '刪除線',
	Left			: '靠左對齊',
	Center			: '置中對齊',
	Right			: '靠右對齊'
});

/**************************************************************************************************/
$.i18n('zh_CN.pukiwiki', {
	copyed		: '剪贴板复制。',
	select		: '请选择对象范围。',
	fontsize	: '文字的大小 ( % 又 pt[省略可]指定):',
	to_ncr		: '变换向数值文字参照',
	hint_text1	: [
		'颜色指定，在最初时变成为选择了的颜色文字颜色，其次选择了的颜色背景颜色。',
		'',
		'处理后，那个范围选择了的着变成选择范围。',
		'如果继续输入文字用、[→]钥匙移动光标之后请输入。',
		'',
		'-- +α(扩张方式) --',
		'',
		'[ &# ] 按钮，数值文字参照转换选择字符串。'
	].join('\n'),
	hint_text2	: '表示范围前头回来了，处理了的范围看不见了的时候，请[试着按ESC ]钥匙。',
	to_easy		: '变更了为简单方式。',
	to_adv		: '变更了为扩张方式。',
	reload		: [
		'对再读包含在内后变得有效。',
		'',
		'现在马上再读包含在内做吗？'
	].join("\n"),
	inline1		: '请输入插件名。[ ＆ 省却 ]',
	inline2 	: '请输入参数。[ ( )内侧 ]',
	inline3 	: '请输入本文。[ { }内侧 ]',
	link 		: '请输入设定链接的文字。',
	url			: '请输入链接处的URL。',
	elem		: '请选择做处理的对象。',
	unload		: '服务器反映变更吗？', 
	cancel		: '废弃变更吗？', 
	submit		: '服务器发送了变更。请确认一下。' 
});

/**************************************************************************************************/
// 言語設定
$.i18n(LANG);

/* 互換命令 */
var pukiwiki_msg_copyed = $.i18n('pukiwiki', 'copyed');
var pukiwiki_msg_select = $.i18n('pukiwiki', 'select');
var pukiwiki_msg_fontsize = $.i18n('pukiwiki', 'fontsize');
var pukiwiki_msg_to_ncr = $.i18n('pukiwiki', 'to_ncr');
var pukiwiki_msg_winie_hint_text = $.i18n('pukiwiki', 'hint_text1');
var pukiwiki_msg_gecko_hint_text = pukiwiki_msg_winie_hint_text + $.i18n('pukiwiki', 'hint_text2');
var pukiwiki_msg_to_easy =  $.i18n('pukiwiki', 'to_easy') + "\n" +  $.i18n('pukiwiki', 'reload');
var pukiwiki_msg_to_adv =  $.i18n('pukiwiki', 'to_adv') + "\n" +  $.i18n('pukiwiki', 'reload');
var pukiwiki_msg_inline1 =  $.i18n('pukiwiki', 'inline1');
var pukiwiki_msg_inline2 =  $.i18n('pukiwiki', 'inline2');
var pukiwiki_msg_inline3 =  $.i18n('pukiwiki', 'inline3');
var pukiwiki_msg_link =  $.i18n('pukiwiki', 'link');
var pukiwiki_msg_url =  $.i18n('pukiwiki', 'url');
var pukiwiki_msg_elem =  $.i18n('pukiwiki', 'elem');
var pukiwiki_msg_unload =  $.i18n('pukiwiki', 'unload');
var pukiwiki_msg_cancel =  $.i18n('pukiwiki', 'cancel');
var pukiwiki_msg_submit =  $.i18n('pukiwiki', 'submit');
