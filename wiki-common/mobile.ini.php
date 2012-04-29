<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: default.ini.php,v 1.25.17 2012/03/31 16:49:00 Logue Exp $
// Copyright (C)
//   2010-2012 PukiWiki Advance Developer Team
//   2005-2006,2009 PukiWiki Plus! Team
//   2003-2005 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// PukiWiki setting file (user agent:mobile)
/////////////////////////////////////////////////
// Skin file
define('IS_MOBILE',true);

/////////////////////////////////////////////////
// メニューバー/サイドバーを常に表示する(1:する 0:しない)
$always_menu_displayed = 0;

/////////////////////////////////////////////////
// 雛形とするページの読み込みを可能にする(1:する 0:しない)
$load_template_func = 0;

/////////////////////////////////////////////////
// 元ページのリンクを自動的に先頭につける(1:つける 0:つけない)
$load_refer_related = 0;

/////////////////////////////////////////////////
// 検索文字列を色分けする(1:する 0:しない)
$search_word_color = 1;

/////////////////////////////////////////////////
// 一覧ページに頭文字インデックスをつける(1:つける 0:つけない)
$list_index = 1;

/////////////////////////////////////////////////
// 特殊シンボル
$_symbol_paraedit		= '<span class="pkwk-symbol symbol-edit" title="Edit here"></span>';
$_symbol_extanchor		= '<span class="pkwk-symbol link_symbol symbol-external" data-uri="$1" title="External Link"></span>';
$_symbol_innanchor		= '<span class="pkwk-symbol link_symbol symbol-internal" data-uri="$1" title="Internal Link"></span>';
$_symbol_attach			= '';

/////////////////////////////////////////////////
// 大・小見出しから目次へ戻るリンクの文字
$top = '';

/////////////////////////////////////////////////
// 添付ファイルの一覧を常に表示する (負担がかかります)
$attach_link = 0;

/////////////////////////////////////////////////
// 関連するページのリンク一覧を常に表示する(負担がかかります)
$related_link = 0;

/////////////////////////////////////////////////
// 水平線のタグ
$hr = '<hr />'."\n";

/////////////////////////////////////////////////
// 脚注機能関連

// 脚注のアンカーに埋め込む本文の最大長
define('PKWK_FOOTNOTE_TITLE_MAX', 16); // Characters

// 脚注のアンカーを相対パスで表示する (0 = 絶対パス)
//  * 相対パスの場合、以前のバージョンのOperaで問題になることがあります
//  * 絶対パスの場合、calendar_viewerなどで問題になることがあります
// (詳しくは: BugTrack/698)
define('PKWK_ALLOW_RELATIVE_FOOTNOTE_ANCHOR', 1);

// 文末の脚注の直前に表示するタグ
$note_hr = '<hr class="note_hr" />'."\n";

/////////////////////////////////////////////////
// WikiName,BracketNameに経過時間を付加する
$show_passage = 1;

/////////////////////////////////////////////////
// リンク表示をコンパクトにする
// * ページに対するハイパーリンクからタイトルを外す
// * Dangling linkのCSSを外す
$link_compact = 0;

/////////////////////////////////////////////////
// フェイスマークを使用する
$usefacemark = 1;

/////////////////////////////////////////////////
// ユーザ定義ルール
//
//  正規表現で記述してください。?(){}-*./+\$^|など
//  は \? のようにクォートしてください。
//  前後に必ず / を含めてください。行頭指定は ^ を頭に。
//  行末指定は $ を後ろに。
//
/////////////////////////////////////////////////
// ユーザ定義ルール(コンバート時に置換)
$line_rules = array(
	'COLOR\(([^\(\)]*)\){([^}]*)}'						=> '<span style="color:$1">$2</span>',
	'SIZE\(([^\(\)]*)\){([^}]*)}'						=> '<span style="font-size:$1px">$2</span>',
	'COLOR\(([^\(\)]*)\):((?:(?!COLOR\([^\)]+\)\:).)*)'	=> '<span style="color:$1">$2</span>',
	'SIZE\(([^\(\)]*)\):((?:(?!SIZE\([^\)]+\)\:).)*)'	=> '<span class="size$1">$2</span>',
	'SUP{([^}]*)}'										=> '<sup>$1</sup>',
	'SUB{([^}]*)}'										=> '<sub>$1</sub>',
	'LANG\(([^\(\)]*)\):((?:(?!LANG\([^\)]+\)\:).)*)'	=> '<bdi lang="$1">$2</bdi>',
	'LANG\(([^\(\)]*)\){([^}]*)}'						=> '<bdi lang="$1">$2</bdi>',
	'%%%(?!%)((?:(?!%%%).)*)%%%'						=> '<ins>$1</ins>',
	'%%(?!%)((?:(?!%%).)*)%%'							=> '<del>$1</del>',
	'@@@(?!@)((?:(?!@@).)*)@@@'							=> '<q>$1</q>',
	'@@(?!@)((?:(?!@@).)*)@@'							=> '<code>$1</code>',
	'___(?!@)((?:(?!@@).)*)___'							=> '<s>$1</s>',
	'__(?!@)((?:(?!@@).)*)__'							=> '<span class="underline">$1</span>',
	"&#039;&#039;&#039;(?!&#039;)((?:(?!&#039;&#039;&#039;).)*)&#039;&#039;&#039;" => '<em>$1</em>',
	"&#039;&#039;(?!&#039;)((?:(?!&#039;&#039;).)*)&#039;&#039;" => '<strong>$1</strong>',
);

/////////////////////////////////////////////////
// フェイスマーク定義ルール(コンバート時に置換)
// $usefacemark = 1ならフェイスマークが置換されます
// 文章内にXDなどが入った場合にfacemarkに置換されてしまうので
// 必要のない方は $usefacemarkを0にしてください。

// TypePad 絵文字アイコン画像 by Six Apart Ltd is licensed under a Creative Commons 表示 2.1 日本 License.
// Permissions beyond the scope of this license may be available at http://start.typepad.jp/typecast/. 

$facemark_rules = array(
	// text is Unicode6.0
	// http://ja.wikipedia.org/wiki/I%E3%83%A2%E3%83%BC%E3%83%89%E7%B5%B5%E6%96%87%E5%AD%97
	// http://www.unicode.org/charts/PDF/U1F300.pdf
	// Docomo standard emoji
	'&amp;\(sun\);'				=> '☀',	// F89F
	'&amp;\(cloud\);'			=> '☁',	// F8A0
	'&amp;\(rain\);'			=> '☂',
	'&amp;\(snow\);'			=> '☃',
	'&amp;\(thunder\);'			=> '⚡',
	'&amp;\(typhoon\);'			=> '🌀',
	'&amp;\(mist\);'			=> '🌁',
	'&amp;\(sprinkle\);'		=> '🌂',
	'&amp;\(aries\);'			=> '♈',
	'&amp;\(taurus\);'			=> '♉',
	'&amp;\(gemini\);'			=> '♊',
	'&amp;\(cancer\);'			=> '♋',
	'&amp;\(leo\);'				=> '♌',
	'&amp;\(virgo\);'			=> '♍',
	'&amp;\(libra\);'			=> '♎',
	'&amp;\(scorpius\);'		=> '♏',
	'&amp;\(sagittarius\);'		=> '♐',
	'&amp;\(capricornus\);'		=> '♑',
	'&amp;\(aquarius\);'		=> '♒',
	'&amp;\(pisces\);'			=> '♓',
	'&amp;\(sports\);'			=> '🎽',
	'&amp;\(baseball\);'		=> '⚾',
	'&amp;\(golf\);'			=> '⛳',
	'&amp;\(tennis\);'			=> '🎾',
	'&amp;\(soccer\);'			=> '⚽',
	'&amp;\(ski\);'				=> '🎿',
	'&amp;\(basketball\);'		=> '🏀',
	'&amp;\(motorsports\);'		=> '🏁',
	'&amp;\(pocketbell\);'		=> '📟',
	'&amp;\(train\);'			=> '🚃',
	'&amp;\(subway\);'			=> 'Ⓜ',
	'&amp;\(bullettrain\);'		=> '🚄',
	'&amp;\(car\);'				=> '🚗',
	'&amp;\(rvcar\);'			=> '🚙',
	'&amp;\(bus\);'				=> '🚌',
	'&amp;\(ship\);'			=> '🚢',
	'&amp;\(airplane\);'		=> '✈',
	'&amp;\(house\);'			=> '🏠',
	'&amp;\(building\);'		=> '🏢',
	'&amp;\(postoffice\);'		=> '🏣',
	'&amp;\(hospital\);'		=> '🏥',
	'&amp;\(bank\);'			=> '🏦',
	'&amp;\(atm\);'				=> '🏧',
	'&amp;\(hotel\);'			=> '🏨',
	'&amp;\(24hours\);'			=> '🏪',
	'&amp;\(gasstation\);'		=> '⛽',
	'&amp;\(parking\);'			=> '🅿',
	'&amp;\(signaler\);'		=> '🚥',
	'&amp;\(toilet\);'			=> '🚻',
	'&amp;\(restaurant\);'		=> '🍴',
	'&amp;\(cafe\);'			=> '☕',
	'&amp;\(bar\);'				=> '🍸',
	'&amp;\(beer\);'			=> '🍺',
	'&amp;\(fastfood\);'		=> '🍔',
	'&amp;\(boutique\);'		=> '👠',
	'&amp;\(hairsalon\);'		=> '✂',
	'&amp;\(karaoke\);'			=> '🎤',
	'&amp;\(movie\);'			=> '🎥',
	'&amp;\(upwardright\);'		=> '↗',
	'&amp;\(carouselpony\);'	=> '🎠',
	'&amp;\(music\);'			=> '🎧',
	'&amp;\(art\);'				=> '🎨',
	'&amp;\(drama\);'			=> '🎩',
	'&amp;\(event\);'			=> '🎪',
	'&amp;\(ticket\);'			=> '🎫',
	'&amp;\(smoking\);'			=> '🚬',
	'&amp;\(nosmoking\);'		=> '🚭',
	'&amp;\(camera\);'			=> '📷',
	'&amp;\(bag\);'				=> '👜',
	'&amp;\(book\);'			=> '📖',
	'&amp;\(ribbon\);'			=> '🎀',
	'&amp;\(present\);'			=> '🎁',
	'&amp;\(birthday\);'		=> '🎂',
	'&amp;\(telephone\);'		=> '☎',
	'&amp;\(mobilephone\);'		=> '📱',
	'&amp;\(memo\);'			=> '📝',
	'&amp;\(tv\);'				=> '📺',
	'&amp;\(game\);'			=> '🎮',
	'&amp;\(cd\);'				=> '💿',
	'&amp;\(heart\);'			=> '♥',
	'&amp;\(spade\);'			=> '♠',
	'&amp;\(diamond\);'			=> '♦',
	'&amp;\(club\);'			=> '♣',
	'&amp;\(eye\);'				=> '👀',
	'&amp;\(ear\);'				=> '👂',
	'&amp;\(rock\);'			=> '✊',
	'&amp;\(scissors\);'		=> '✌',
	'&amp;\(paper\);'			=> '✋',
	'&amp;\(downwardright\);'	=> '↘',
	'&amp;\(upwardleft\);'		=> '↖',
	'&amp;\(foot\);'			=> '👣',
	'&amp;\(shoe\);'			=> '👟',
	'&amp;\(eyeglass\);'		=> '👓',
	'&amp;\(wheelchair\);'		=> '♿',	// F8FC
	'&amp;\(newmoon\);'			=> '🌔',	// F940
	'&amp;\(moon1\);'			=> '🌔',
	'&amp;\(moon2\);'			=> '🌓',
	'&amp;\(moon3\);'			=> '🌙',
	'&amp;\(fullmoon\);'		=> '🌕',
	'&amp;\(dog\);'				=> '🐶',
	'&amp;\(cat\);'				=> '🐱',
	'&amp;\(yacht\);'			=> '⛵',
	'&amp;\(xmas\);'			=> '🎄',
	'&amp;\(downwardleft\);'	=> '↙',
	'&amp;\(phoneto\);'			=> '📲',
	'&amp;\(mailto\);'			=> '📩',
	'&amp;\(faxto\);'			=> '📠',
	'&amp;\(info01\);'			=> '',
	'&amp;\(info02\);'			=> '',
	'&amp;\(mail\);'			=> '✉',
	'&amp;\(by-d\);'			=> '',
	'&amp;\(d-point\);'			=> '',
	'&amp;\(yen\);'				=> '💴',
	'&amp;\(free\);'			=> '🆓',
	'&amp;\(id\);'				=> '🆔',
	'&amp;\(key\);'				=> '🔑',
	'&amp;\(enter\);'			=> '↩',
	'&amp;\(clear\);'			=> '🆑',
	'&amp;\(search\);'			=> '🔍',
	'&amp;\(new\);'				=> '🆕',
	'&amp;\(flag\);'			=> '🚩',
	'&amp;\(freedial\);'		=> '',
	'&amp;\(sharp\);'			=> '#⃣',
	'&amp;\(mobaq\);'			=> '',
	'&amp;\(one\);'				=> '1⃣',
	'&amp;\(two\);'				=> '2⃣',
	'&amp;\(three\);'			=> '3⃣',
	'&amp;\(four\);'			=> '4⃣',
	'&amp;\(five\);'			=> '5⃣',
	'&amp;\(six\);'				=> '6⃣',
	'&amp;\(seven\);'			=> '7⃣',
	'&amp;\(eight\);'			=> '8⃣',
	'&amp;\(nine\);'			=> '9⃣',
	'&amp;\(zero\);'			=> '0⃣',
	'&amp;\(ok\);'				=> '🆗',
	'&amp;\(heart01\);'			=> '❤',
	'&amp;\(heart02\);'			=> '💓',
	'&amp;\(heart03\);'			=> '💔',
	'&amp;\(heart04\);'			=> '💕',
	'&amp;\(happy01\);'			=> '😃',
	'&amp;\(angry\);'			=> '😠',
	'&amp;\(despair\);'			=> '😞',
	'&amp;\(sad\);'				=> '😖',
	'&amp;\(wobbly\);'			=> '😵',
	'&amp;\(up\);'				=> '⤴',
	'&amp;\(note\);'			=> '🎵',
	'&amp;\(spa\);'				=> '♨',
	'&amp;\(cute\);'			=> '💠',
	'&amp;\(kissmark\);'		=> '💋',
	'&amp;\(shine\);'			=> '✨',
	'&amp;\(flair\);'			=> '💡',
	'&amp;\(annoy\);'			=> '💢',
	'&amp;\(punch\);'			=> '👊',
	'&amp;\(bomb\);'			=> '💣',
	'&amp;\(notes\);'			=> '🎶',
	'&amp;\(down\);'			=> '⤵',
	'&amp;\(sleepy\);'			=> '💤',
	'&amp;\(sign01\);'			=> '❗',
	'&amp;\(sign02\);'			=> '⁉',
	'&amp;\(sign03\);'			=> '‼',
	'&amp;\(impact\);'			=> '💥',
	'&amp;\(sweat01\);'			=> '💦',
	'&amp;\(sweat02\);'			=> '💧',
	'&amp;\(dash\);'			=> '💨',
	'&amp;\(sign04\);'			=> '〰',
	'&amp;\(sign05\);'			=> '➰',
	'&amp;\(slate\);'			=> '👕',
	'&amp;\(pouch\);'			=> '👛',
	'&amp;\(pen\);'				=> '💄',
	'&amp;\(shadow\);'			=> '👤',
	'&amp;\(chair\);'			=> '💺',
	'&amp;\(night\);'			=> '🌃',
	'&amp;\(soon\);'			=> '🔜',
	'&amp;\(on\);'				=> '🔛',
	'&amp;\(end\);'				=> '🔚',
	'&amp;\(clock\);'			=> '⏰',
	// Docomo Extend emoji
	'&amp;\(appli01\);'			=> '',
	'&amp;\(appli02\);'			=> '',
	'&amp;\(t-shirt\);'			=> '👕',	// F9B3
	'&amp;\(moneybag\);'		=> '👛',
	'&amp;\(rouge\);'			=> '💄',
	'&amp;\(denim\);'			=> '👖',
	'&amp;\(snowboard\);'		=> '🏂',
	'&amp;\(bell\);'			=> '🔔',
	'&amp;\(door\);'			=> '🚪',
	'&amp;\(dollar\);'			=> '💰',
	'&amp;\(pc\);'				=> '💻',
	'&amp;\(loveletter\);'		=> '💌',
	'&amp;\(wrench\);'			=> '🔧',
	'&amp;\(pencil\);'			=> '✏',
	'&amp;\(crown\);'			=> '👑',
	'&amp;\(ring\);'			=> '💍',	// F9C0
	'&amp;\(sandclock\);'		=> '⏳',
	'&amp;\(bicycle\);'			=> '🚲',
	'&amp;\(japanesetea\);'		=> '🍵',
	'&amp;\(watch\);'			=> '⌚',
	'&amp;\(think\);'			=> '😔',
	'&amp;\(confident\);'		=> '😌',
	'&amp;\(coldsweats01\);'	=> '😅',
	'&amp;\(coldsweats02\);'	=> '😓',
	'&amp;\(pout\);'			=> '😡',
	'&amp;\(gawk\);'			=> '😒',
	'&amp;\(lovely\);'			=> '😍',
	'&amp;\(good\);'			=> '👍',
	'&amp;\(bleah\);'			=> '😜',
	'&amp;\(wink\);'			=> '😉',
	'&amp;\(happy02\);'			=> '😆',
	'&amp;\(bearing\);'			=> '😣',	// F9D0
	'&amp;\(catface\);'			=> '😏',
	'&amp;\(crying\);'			=> '😭',
	'&amp;\(weep\);'			=> '😢',
	'&amp;\(ng\);'				=> '🆖',
	'&amp;\(clip\);'			=> '📎',
	'&amp;\(copyright\);'		=> '©',
	'&amp;\(tm\);'				=> '™',
	'&amp;\(run\);'				=> '🏃',
	'&amp;\(secret\);'			=> '㊙',
	'&amp;\(recycle\);'			=> '♻',
	'&amp;\(r-mark\);'			=> '®',
	'&amp;\(danger\);'			=> '⚠',
	'&amp;\(ban\);'				=> '🈲',
	'&amp;\(empty\);'			=> '🈳',
	'&amp;\(pass\);'			=> '🈴',
	'&amp;\(full\);'			=> '🈵',
	'&amp;\(leftright\);'		=> '↔',
	'&amp;\(updown\);'			=> '↕',
	'&amp;\(school\);'			=> '🏫',
	'&amp;\(wave\);'			=> '🌊',
	'&amp;\(fuji\);'			=> '🗻',
	'&amp;\(clover\);'			=> '🍀',
	'&amp;\(cherry\);'			=> '🍒',
	'&amp;\(tulip\);'			=> '🌷',
	'&amp;\(banana\);'			=> '🍌',
	'&amp;\(apple\);'			=> '🍎',
	'&amp;\(bud\);'				=> '🌱',
	'&amp;\(maple\);'			=> '🍁',
	'&amp;\(cherryblossom\);'	=> '🌸',
	'&amp;\(riceball\);'		=> '🍙',
	'&amp;\(cake\);'			=> '🍰',
	'&amp;\(bottle\);'			=> '🍶',
	'&amp;\(noodle\);'			=> '🍜',
	'&amp;\(bread\);'			=> '🍞',
	'&amp;\(snail\);'			=> '🐌',
	'&amp;\(chick\);'			=> '🐤',
	'&amp;\(penguin\);'			=> '🐧',
	'&amp;\(fish\);'			=> '🐟',
	'&amp;\(delicious\);'		=> '😋',
	'&amp;\(smile\);'			=> '😁',
	'&amp;\(horse\);'			=> '🐴',
	'&amp;\(pig\);'				=> '🐷',
	'&amp;\(wine\);'			=> '🍷',
	'&amp;\(shock\);'			=> '😱'
);

/////////////////////////////////////////////////
// クッキーを使用できないアドレス
// (通常、デスクトップでは存在しない)
$use_trans_sid_address = array(
);

/////////////////////////////////////////////////
?>