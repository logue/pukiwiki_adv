// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: locale.js,v 0.0.3 2010/08/25 22:06:00 Logue Exp $

// Pukiwiki skin script for jQuery
// Copyright (c)2010-2011 PukiWiki Advance Developer Team
//			  2010	  Logue <http://logue.be/> All rights reserved.

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
		'[ ☺ ]	button opens emoji palette.',
		'[ ⏎ ]	button inserts a line break.',
		'[ &amp;# ]	button converts the selection character string into the numeric character reference.',
		'[ <span class="ui-icon ui-icon-trash" style="display: inline-block;"></span> ] button flush edits.'
	].join('<br />\n'),
	inline1		: 'Please input the plugin name. [ & is omitted ]',
	inline2 	: 'Please input the parameter. [ ( )Inside ]',
	inline3 	: 'Please input the text. [ { }Inside ]',
	link 		: 'Please input the character that sets the link.',
	url			: 'Please input URL the link ahead.',
	elem		: 'Please select the processed object.',
	unload		: 'Do you submit your change ?',
	cancel		: 'Do you discard your change ?',
	submit		: 'Your change was sent. Check please!',
	error		: 'An unexpected error has occurred: ',

	info_restore1	: 'A page is newer than the data that have been saved.\nDo you want to restore?',
	info_restore2	: 'Do you want to restore the data that is stored in the browser?',
	flush_restore	: 'Do you want to flush your edits in the past?',
});

$.i18n('en_US.editor',{
	bold		: 'Bold',
	italic		: 'Italic',
	strike		: 'Strike words',
	underline	: 'Underline',
	code		: 'Code',
	quote		: 'Quote',
	link		: 'Link',
	size		: 'Size',
	color		: 'Color',
	emoji		: 'Emoji',
	breakline	: 'br',
	ncr			: 'to Numeric character reference',
	hint		: 'Hint',
	flush		: 'Flush local storage'
});

$.i18n('en_US.dialog',{
	ok			: 'OK',
	cancel		: 'Cancel',
	close		: 'Close',
	yes			: 'Yes',
	no			: 'No',
	first		: 'First',
	next		: 'Next',
	prev		: 'Previous',
	last		: 'Last',
	reload		: 'Reload',
	print		: 'Print',
	
	loading		: 'Now Loading...',
	start		: 'Start',
	success		: 'Success',
	complete	: 'Complete',
	ready		: 'Ready',
	error		: 'Error'
});

$.i18n('en_US.sh',{
	expandSource				: 'show source',
	viewSource					: 'view source',
	copyToClipboard 			: 'copy to clipboard',
	copyToClipboardConfirmation : 'The code is in your clipboard now',
	print						: 'print',
	help						: '?',
	noBrush						: 'Can\'t find brush for: ',
	brushNotHtmlScript			: 'Brush wasn\'t configured for html-script option: '
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
		'[ ☺ ]	ボタンは、絵文字パレットを開きます。',
		'[ ⏎ ]	ボタンは、改行を入れます。',
		'[ &amp;# ]	ボタンは、選択文字列を数値文字参照に変換します。',
		'[ <span class="ui-icon ui-icon-trash" style="display: inline-block;"></span> ]は、このページの送信前の編集内容をフラッシュします。'
	].join('<br />\n'),
	hint_text2	: '表示範囲が先頭に戻ってしまい、処理した範囲が見えなくなった時は、[ ESC ]キーを押してみてください。',
	inline1		: 'プラグイン名を入力してください。[ &は省く ]',
	inline2 	: 'パラメーターを入力してください。[ ( )内 ]',
	inline3 	: '本文を入力してください。[ { }内 ]',
	link 		: 'リンクを設定する文字を入力してください。',
	url			: 'リンク先のURLを入力してください。',
	elem		: '処理をする対象を選択してください。',
	unload		: '変更をサーバーに反映しますか？',
	cancel		: '変更を破棄しますか？',
	submit		: '変更をサーバーに送信しました。確認してください。',
	error		: '予期せぬエラーが発生しました：',
	
	info_restore1	: 'ページは、保存されているデーターよりも新しいようです。\n復元してもよろしいですか？',
	info_restore2	: '過去に編集したデーターがあるようです。復元しますか？',
	flush_restore	: '編集内容をフラッシュしてもよろしいですか？',
});

$.i18n('ja_JP.editor',{
	bold		: 'ボールド',
	italic		: 'イタリック',
	strike		: '字消し',
	underline	: '下線',
	code		: 'コード',
	quote		: '引用',
	link		: 'リンク挿入',
	size		: 'サイズ',
	color		: '文字色',
	emoji		: '絵文字',
	breakline	: '改行',
	ncr			: '数値参照文字に変換',
	hint		: 'ヒント',
	flush		: 'Local Storageをフラッシュ'
});

$.i18n('ja_JP.dialog',{
	ok			: 'OK',
	cancel		: 'キャンセル',
	close		: '閉じる',
	yes			: 'はい',
	no			: 'いいえ',
	first		: '最初へ',
	prev		: '前へ',
	next		: '次へ',
	last		: '最後へ',
	reload		: 'リロード',
	clipboard	: 'クリップボードへコピー',
	print		: '印刷',
	
	loading		: '読み込み中…。',
	start		: '開始',
	success		: '成功',
	complete	: '完了',
	ready		: '準備完了',
	error		: 'エラー'
});

$.i18n('ja_JP.sh',{
	expandSource				: 'ソースを全体化',
	viewSource					: 'ソースを表示',
	copyToClipboard 			: 'クリップボードへコピー',
	copyToClipboardConfirmation : 'コードはクリップボードにコピーされました。',
	print						: '印刷',
	help						: 'ヘルプ',
	noBrush						: '以下の言語のBrushスクリプトが見つかりませんでした：',
	brushNotHtmlScript			: 'Brushの設定がありません：'
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
		'[ ☺ ]	버튼은 이모티콘 팔레트를 엽니다.',
		'[ ⏎ ]	버튼은 줄 바꿈합니다.',
		'[ &amp;# ]	버튼은, 선택 문자열을 수치 문자 참조로 변환합니다.',
		'[ <span class="ui-icon ui-icon-trash" style="display: inline-block;"></span> ]이 페이지를 보내기 전에 편집 내용을 플러시합니다.'
	].join('\n'),
	hint_text2	: '표시 범위가 선두로 돌아와 버려, 처리한 범위가 안보이게 되었을 때는,[ ESC ]키를 눌러 보세요.',
	to_easy		: '이지 모드로 변경했습니다.',
	to_adv		: '어드밴스 모드로 변경했습니다.',
	reload		: [
		'리로드 후에 유효하게 됩니다.',
		'',
		'금방 리로드 합니까?'
	].join('<br />\n'),
	inline1		: '플러그 인명을 입력해 주세요.[ ＆ 는 생략한다 ]',
	inline2 	: '파라미터를 입력해 주세요.[ ( ) 안 ]',
	inline3 	: '본문을 입력해 주세요.[ { } 안 ]',
	link 		: '링크를 설정하는 문자를 입력해 주세요.',
	url			: '링크처의 URL를 입력해 주세요.',
	elem		: '처리를 하는 대상을 선택해 주세요.',
	unload		: '변경을 서버에 반영합니까?', 
	cancel		: '변경을 파기합니까?',
	submit		: '변경을 서버에 송신했습니다.확인해 주세요.',
	error		: '예기치 않은 오류가 발생했습니다: ',
	
	info_restore1	: '페이지는 저장되는 데이터보다 새로운 것 같습니다.\n복원 하시겠습니까?',
	info_restore2	: '과거에 편집한 데이터가있는 것 같습니다. 복원 하시겠습니까?',
	flush_restore	: '편집 내용을 플러시하고시겠습니까?',
});

$.i18n('ko_KR.editor',{
	bold		: '굵게',
	italic		: '이탤릭체',
	strike		: '자 지우고',
	underline	: '밑줄',
	code		: '코드',
	quote		: '인용',
	link		: '링크 삽입',
	size		: '크기',
	color		: '글자색',
	emoji		: '이모티콘',
	breakline	: '줄',
	ncr			: '수치 참조 문자 변환',
	help		: '팁',
	flush		: 'Local Storage를 플래시'
});

$.i18n('ko_KR.dialog',{
	ok			: '확인',
	cancel		: '취소',
	close		: '닫기',
	yes			: '예',
	no			: '아니오',
	first		: '처음에',
	prev		: '이전',
	next		: '다음',
	last		: '마지막',
	reload		: '새로고침',
	clipboard	: '클립 보드에 복사',
	print 		: '인쇄',
	
	loading		: '지금 중…。',
	start		: '시작',
	success		: '성공',
	complete	: '완료',
	ready		: '준비',
	error		: '오류'
});

$.i18n('ko_KR.sh',{
	expandSource				: '전체가',
	viewSource					: '소스보기',
	copyToClipboard 			: '클립 보드에 복사',
	copyToClipboardConfirmation : '코드는 클립 보드에 복사되었습니다. ',
	print						: '인쇄',
	help						: '도움말',
	noBrush						: '다음 언어 Brush 스크립트를 찾을 수 없습니다 :',
	brushNotHtmlScript			: 'Brush 설정이 없습니다 :'
});

$.i18n('ko_KR.player',{
	play		: '재생',
	pasue		: '일시 정지',
	stop		: '정지',
	volume		: '볼륨',
	volume_max	: '최대 볼륨',
	volume_min	: '음소거',
	seek		: '검색',
	fullscreen	: '전체 화면'
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
		'[ ☺ ]	繪文字按鈕打開調色板。',
		'[ ⏎ ]	按鈕插入一個換行符。',
		'[ &amp;# ] 按鈕，數值文字參照轉換選擇字符串。',
		'[ <span class="ui-icon ui-icon-trash" style="display: inline-block;"></span> ] 按鈕刷新編輯。'
	].join('<br />\n'),
	hint_text2	: '表示範圍前頭回來了，處理了的範圍看不見了的時候，請[試著按ESC ]鑰匙。',
	reload		: [
		'對再讀包含在內後變得有效。',
		'',
		'現在馬上再讀包含在內做嗎？'
	].join('\n'),
	inline1		: '請輸入插件名。[ & 省卻 ]',
	inline2 	: '請輸入參數。[ ( )內側 ]',
	inline3		: '請輸入本文。[ { }內側 ]',
	link 		: '請輸入設定鏈接的文字。',
	url			: '請輸入鏈接處的URL。',
	elem		: '請選擇做處理的對象。',
	unload		: '服務器反映變更嗎？', 
	cancel		: '廢棄變更嗎？', 
	submit		: '服務器發送了變更。請確認一下。',
	error		: '一個意外的錯誤：',
	
	info_restore1	: '一個新的頁面比數據已保存\n是否要恢復？',
	info_restore2	: '你要恢復的數據存儲在瀏覽器？',
	flush_restore	: '你要刷新你的編輯在過去？'
});

$.i18n('zh_TW.dialog',{
	ok			: '確定',
	cancel		: '取消',
	close		: '關閉',
	yes			: '是',
	no			: '否',
	first		: '首頁',
	prev		: '前一頁',
	next		: '下一頁',
	last		: '末頁',
	reload		: '刷新',
	clipboard	: '複製到剪貼板',
	print		: '打印',
	
	loading		: '載入中…。',
	start		: '開始',
	success		: '成功',
	complete	: '完成',
	ready		: '準備好',
	error		: '錯誤'
});

$.i18n('zh_TW.sh',{
	expandSource				: '或整個源',
	viewSource					: '查看源代碼',
	copyToClipboard 			: '複製到剪貼板',
	copyToClipboardConfirmation : '該代碼已經被複製到剪貼板。',
	print						: '打印',
	help						: '幫助',
	noBrush						: 'Brush腳本找不到下列語言：',
	brushNotHtmlScript			: '沒有設置Brush：'
});

$.i18n('zh_TW.player',{
	play		: '播放',
	pasue		: '暫停',
	stop		: '停止',
	volume		: '音量',
	volume_max	: '最響亮',
	volume_min	: '靜音',
	seek		: '求',
	fullscreen	: '全屏'
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
		'[ ☺ ]	按钮打开绘文字调色板。',
		'[ ⏎ ]	按钮插入一个换行符。',
		'[ &amp;# ] 按钮，数值文字参照转换选择字符串。',
		'[ <span class="ui-icon ui-icon-trash" style="display: inline-block;"></span> ] 编辑按钮冲水。'
	].join('<br />\n'),
	hint_text2	: '表示范围前头回来了，处理了的范围看不见了的时候，请[试着按ESC ]钥匙。',
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
	submit		: '服务器发送了变更。请确认一下。',
	error		: '一个意外的错误：',
	
	info_restore1	: '一个页面比已保存的数据更新。\n您要恢复？',
	info_restore2	: '你要恢复的数据存储在浏览器中中的吗？',
	flush_restore	: '你要刷新在过去，您的编辑？'
});

$.i18n('zh_CN.dialog',{
	ok			: '确定',
	cancel		: '取消',
	close		: '关闭',
	yes			: '是',
	no			: '否',
	first		: '首页',
	prev		: '前一页',
	next		: '下一页',
	last		: '末页',
	reload		: '重载',
	clipboard	: '复制到剪贴板',
	print		: '打印',
	
	loading		: '载入中…。',
	start		: '开始',
	success		: '成功',
	complete	: '完成',
	ready		: '准备好',
	error		: '错误'
});

$.i18n('zh_CN.sh',{
	expandSource				: '或整个源',
	viewSource					: '查看源代码',
	copyToClipboard 			: '复制到剪贴板',
	copyToClipboardConfirmation : '该代码已经被复制到剪贴板。',
	print						: '打印',
	help						: '帮助',
	noBrush						: 'Brush脚本找不到下列语言：',
	brushNotHtmlScript			: '没有设置Brush：'
});

$.i18n('zh_CN.player',{
	play		: '播放',
	pasue		: '暂停',
	stop		: '停止',
	volume		: '音量',
	volume_max	: '最响亮',
	volume_min	: '静音',
	seek		: '求',
	fullscreen	: '全屏'
});
/**************************************************************************************************/
// 言語設定
$.i18n(LANG);
