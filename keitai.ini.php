<?php
// PukiWiki Plus! - Yet another WikiWikiWeb clone.
// $Id: keitai.ini.php,v 1.26.8 2011/05/10 23:44:00 Logue Exp $
// Copyright (C)
//   2011      PukiWiki Advance Developers Team
//   2005,2009 PukiWiki Plus! Team
//   2002-2006,2011 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// PukiWiki setting file (Cell phones, PDAs and other thin clients)

/////////////////////////////////////////////////
// 携帯・PDA専用のページを初期ページとして指定する

// $defaultpage = 'm';

/////////////////////////////////////////////////
// スキンファイルの場所
define('SKIN_FILE', add_skindir('keitai'));

/////////////////////////////////////////////////
// 雛形とするページの読み込みを可能にする
$load_template_func = 0;

/////////////////////////////////////////////////
// 検索文字列を色分けする
$search_word_color = 0;

/////////////////////////////////////////////////
// 一覧ページに頭文字インデックスをつける
$list_index = 0;

/////////////////////////////////////////////////
// 大・小見出しから目次へ戻るリンクの文字
$top = '';

/////////////////////////////////////////////////
// 添付ファイルの一覧を常に表示する (負担がかかります)
// ※keitaiスキンにはこの一覧を表示する機能がありません
$attach_link = 0;

/////////////////////////////////////////////////
// 関連するページのリンク一覧を常に表示する(負担がかかります)
// ※keitaiスキンにはこの一覧を表示する機能がありません
$related_link = 0;

// リンク一覧の区切り文字
// ※上同
$related_str = "\n ";

// (#relatedプラグインが表示する) リンク一覧の区切り文字
$rule_related_str = "</li>\n<li>";

/////////////////////////////////////////////////
// 水平線のタグ
$hr = '<hr>';

/////////////////////////////////////////////////
// 脚注機能関連

// 脚注のアンカーに埋め込む本文の最大長
define('PKWK_FOOTNOTE_TITLE_MAX', 0); // Characters

// 脚注のアンカーを相対パスで表示する (0 = 絶対パス)
//  * 相対パスの場合、以前のバージョンのOperaで問題になることがあります
//  * 絶対パスの場合、calendar_viewerなどで問題になることがあります
// (詳しくは: BugTrack/698)
define('PKWK_ALLOW_RELATIVE_FOOTNOTE_ANCHOR', 1);

// 文末の注釈の直前に表示するタグ
$note_hr = '<hr>';

/////////////////////////////////////////////////
// WikiName,BracketNameに経過時間を付加する
$show_passage = 0;

/////////////////////////////////////////////////
// リンク表示をコンパクトにする
// * ページに対するハイパーリンクからタイトルを外す
// * Dangling linkのCSSを外す
$link_compact = 1;

/////////////////////////////////////////////////
// フェイスマークを絵文字に変換する (※i-mode, Vodafone, EzWebなど携帯電話限定)
$usefacemark = 1;

/////////////////////////////////////////////////
// accesskey (SKINで使用)
$accesskey = 'accesskey';

/////////////////////////////////////////////////
// $scriptを短縮
if (preg_match('#([^/]+)$#', $script, $matches)) {
	$script = $matches[1];
}

/////////////////////////////////////////////////
// ブラウザ調整前のデフォルト値

// max_size (SKINで使用)
$max_size = 5;	// SKINで使用, KByte

// cols: テキストエリアのカラム数 rows: 行数
$cols = 22; $rows = 5;	// i_mode


/////////////////////////////////////////////////
// ブラウザに合わせた調整

$ua_name  = $user_agent['name'];
$ua_vers  = $user_agent['vers'];
$ua_agent = $user_agent['agent'];
$matches  = array();

// Browser-name only
switch ($ua_name) {

	// NetFront / Compact NetFront
	//   DoCoMo Net For MOBILE: ｉモード対応HTMLの考え方: ユーザエージェント
	//   http://www.nttdocomo.co.jp/mc-user/i/tag/imodetag.html
	//   DDI POCKET: 機種ラインナップ: AirH"PHONE用ホームページの作成方法
	//   http://www.ddipocket.co.jp/p_s/products/airh_phone/homepage.html
	case 'NetFront':
	case 'CNF':
	case 'DoCoMo':
	case 'Opera': // Performing CNF compatible
		if (preg_match('#\b[cC]([0-9]+)\b#', $ua_agent, $matches)) {
			$max_size = $matches[1];	// Cache max size
		}
		$cols = 22; $rows = 5;	// i_mode
		break;

	// Vodafone (ex. J-PHONE)
	// ボーダフォンライブ！向けウェブコンテンツ開発ガイド [概要編] (Version 1.2.0 P13)
	// http://www.dp.j-phone.com/dp/tool_dl/download.php?docid=110
	// 技術資料: ユーザーエージェントについて
	// http://www.dp.j-phone.com/dp/tool_dl/web/useragent.php
	case 'J-PHONE':
		$matches = array("");
		preg_match('/^([0-9]+)\./', $user_agent['vers'], $matches);
		switch($matches[1]){
		case '3': $max_size =   6; break; // C type: lt   6000bytes
		case '4': $max_size =  12; break; // P type: lt  12Kbytes
		case '5': $max_size = 200; break; // W type: lt 200Kbytes
		}
		$cols = 24; $rows = 20;
		break;

	// UP.Browser
	case 'UP.Browser':
		// UP.Browser for KDDI cell phones
		// http://www.au.kddi.com/ezfactory/tec/spec/xhtml.html ('About 9KB max')
		// http://www.au.kddi.com/ezfactory/tec/spec/4_4.html (User-agent strings)
		if (preg_match('#^KDDI#', $ua_agent)) $max_size =  9;
		break;
}

// Browser-name + version
switch ("$ua_name/$ua_vers") {
	// Restriction For imode:
	//  http://www.nttdocomo.co.jp/mc-user/i/tag/s2.html
	case 'DoCoMo/2.0':	$max_size = min($max_size, 30); break;
}


/////////////////////////////////////////////////
// ユーザ定義ルール
//
//  正規表現で記述してください。?(){}-*./+\$^|など
//  は \? のようにクォートしてください。
//  前後に必ず / を含めてください。行頭指定は ^ を頭に。
//  行末指定は $ を後ろに。

// ユーザ定義ルール(コンバート時に置換)
$line_rules = array(
	'COLOR\(([^\(\)]*)\){([^}]*)}'	=> '<font color="$1">$2</font>',
	'SIZE\(([^\(\)]*)\){([^}]*)}'	=> '$2',	// Disabled
	'COLOR\(([^\(\)]*)\):((?:(?!COLOR\([^\)]+\)\:).)*)'	=> '<font color="$1">$2</font>',
	'SIZE\(([^\(\)]*)\):((?:(?!SIZE\([^\)]+\)\:).)*)'	=> '$2', // Disabled
	'%%%(?!%)((?:(?!%%%).)*)%%%'	=> '<ins>$1</ins>',
	'%%(?!%)((?:(?!%%).)*)%%'	=> '<del>$1</del>',
	"&#039;&#039;&#039;(?!&#039;)((?:(?!&#039;&#039;&#039;).)*)&#039;&#039;&#039;" => '<em>$1</em>',
	"&#039;&#039;(?!&#039;)((?:(?!&#039;&#039;).)*)&#039;&#039;" => '<strong>$1</strong>',
);


/////////////////////////////////////////////////
// 携帯電話にあわせたフェイスマーク

// $usefacemark = 1ならフェイスマークが置換されます
// 文章内に' XD'などがあった場合にfacemarkに置換されてしまうため、
// 必要のない方は $usefacemarkを0にしてください。

// Browser-name only
$facemark_rules = array();

// http://kokogiko.s3.amazonaws.com/pict/pictgram_convert.html
switch ($ua_name) {

	// Graphic icons for imode HTML 4.0, with Shift-JIS text output
	// http://www.nttdocomo.co.jp/mc-user/i/tag/emoji/e1.html
	// http://www.nttdocomo.co.jp/mc-user/i/tag/emoji/list.html
	case 'DoCoMo':

		$facemark_rules = array(
			// Docomo Standard Emoticon
			'&amp;\(sun\);'			=> '&#xE63E;',
			'&amp;\(cloud\);'			=> '&#xE63F;',
			'&amp;\(rain\);'			=> '&#xE640;',
			'&amp;\(snow\);'			=> '&#xE641;',
			'&amp;\(thunder\);'		=> '&#xE642;',
			'&amp;\(typhoon\);'		=> '&#xE643;',
			'&amp;\(mist\);'			=> '&#xE644;',
			'&amp;\(sprinkle\);'		=> '&#xE645;',
			'&amp;\(aries\);'			=> '&#xE646;',
			'&amp;\(taurus\);'		=> '&#xE647;',
			'&amp;\(gemini\);'		=> '&#xE648;',
			'&amp;\(cancer\);'		=> '&#xE649;',
			'&amp;\(leo\);'			=> '&#xE64A;',
			'&amp;\(virgo\);'			=> '&#xE64B;',
			'&amp;\(libra\);'			=> '&#xE64C;',
			'&amp;\(scorpius\);'		=> '&#xE64D;',
			'&amp;\(sagittarius\);'	=> '&#xE64E;',
			'&amp;\(capricornus\);'	=> '&#xE64F;',
			'&amp;\(aquarius\);'		=> '&#xE650;',
			'&amp;\(pisces\);'		=> '&#xE651;',
			'&amp;\(sports\);'		=> '&#xE652;',
			'&amp;\(baseball\);'		=> '&#xE653;',
			'&amp;\(golf\);'			=> '&#xE654;',
			'&amp;\(tennis\);'		=> '&#xE655;',
			'&amp;\(soccer\);'		=> '&#xE656;',
			'&amp;\(ski\);'			=> '&#xE657;',
			'&amp;\(basketball\);'	=> '&#xE658;',
			'&amp;\(motorsports\);'	=> '&#xE659;',
			'&amp;\(pocketbell\);'	=> '&#xE65A;',
			'&amp;\(train\);'			=> '&#xE65B;',
			'&amp;\(subway\);'		=> '&#xE65C;',
			'&amp;\(bullettrain\);'	=> '&#xE65D;',
			'&amp;\(car\);'			=> '&#xE65E;',
			'&amp;\(rvcar\);'			=> '&#xE65F;',
			'&amp;\(bus\);'			=> '&#xE660;',
			'&amp;\(ship\);'			=> '&#xE661;',
			'&amp;\(airplane\);'		=> '&#xE662;',
			'&amp;\(house\);'			=> '&#xE663;',
			'&amp;\(building\);'		=> '&#xE664;',
			'&amp;\(postoffice\);'	=> '&#xE665;',
			'&amp;\(hospital\);'		=> '&#xE666;',
			'&amp;\(bank\);'			=> '&#xE667;',
			'&amp;\(atm\);'			=> '&#xE668;',
			'&amp;\(hotel\);'			=> '&#xE669;',
			'&amp;\(24hours\);'		=> '&#xE66A;',
			'&amp;\(gasstation\);'	=> '&#xE66B;',
			'&amp;\(parking\);'		=> '&#xE66C;',
			'&amp;\(signaler\);'		=> '&#xE66D;',
			'&amp;\(toilet\);'		=> '&#xE66E;',
			'&amp;\(restaurant\);'	=> '&#xE66F;',
			'&amp;\(cafe\);'			=> '&#xE670;',
			'&amp;\(bar\);'			=> '&#xE671;',
			'&amp;\(beer\);'			=> '&#xE672;',
			'&amp;\(fastfood\);'		=> '&#xE673;',
			'&amp;\(boutique\);'		=> '&#xE674;',
			'&amp;\(hairsalon\);'		=> '&#xE675;',
			'&amp;\(karaoke\);'		=> '&#xE676;',
			'&amp;\(movie\);'			=> '&#xE677;',
			'&amp;\(upwardright\);'	=> '&#xE678;',
			'&amp;\(carouselpony\);'	=> '&#xE679;',
			'&amp;\(music\);'			=> '&#xE67A;',
			'&amp;\(art\);'			=> '&#xE67B;',
			'&amp;\(drama\);'			=> '&#xE67C;',
			'&amp;\(event\);'			=> '&#xE67D;',
			'&amp;\(ticket\);'		=> '&#xE67E;',
			'&amp;\(smoking\);'		=> '&#xE67F;',
			'&amp;\(nosmoking\);'		=> '&#xE680;',
			'&amp;\(camera\);'		=> '&#xE681;',
			'&amp;\(bag\);'			=> '&#xE682;',
			'&amp;\(book\);'			=> '&#xE683;',
			'&amp;\(ribbon\);'		=> '&#xE684;',
			'&amp;\(present\);'		=> '&#xE685;',
			'&amp;\(birthday\);'		=> '&#xE686;',
			'&amp;\(telephone\);'		=> '&#xE687;',
			'&amp;\(mobilephone\);'	=> '&#xE688;',
			'&amp;\(memo\);'			=> '&#xE689;',
			'&amp;\(tv\);'			=> '&#xE68A;',
			'&amp;\(game\);'			=> '&#xE68B;',
			'&amp;\(cd\);'			=> '&#xE68C;',
			'&amp;\(heart\);'			=> '&#xE68D;',
			'&amp;\(spade\);'			=> '&#xE68E;',
			'&amp;\(diamond\);'		=> '&#xE68F;',
			'&amp;\(club\);'			=> '&#xE690;',
			'&amp;\(eye\);'			=> '&#xE691;',
			'&amp;\(ear\);'			=> '&#xE692;',
			'&amp;\(rock\);'			=> '&#xE693;',
			'&amp;\(scissors\);'		=> '&#xE694;',
			'&amp;\(paper\);'			=> '&#xE695;',
			'&amp;\(downwardright\);'	=> '&#xE696;',
			'&amp;\(upwardleft\);'	=> '&#xE697;',
			'&amp;\(foot\);'			=> '&#xE698;',
			'&amp;\(shoe\);'			=> '&#xE699;',
			'&amp;\(eyeglass\);'		=> '&#xE69A;',
			'&amp;\(wheelchair\);'	=> '&#xE69B;',
			'&amp;\(newmoon\);'		=> '&#xE69C;',
			'&amp;\(moon1\);'			=> '&#xE69D;',
			'&amp;\(moon2\);'			=> '&#xE69E;',
			'&amp;\(moon3\);'			=> '&#xE69F;',
			'&amp;\(fullmoon\);'		=> '&#xE6A0;',
			'&amp;\(dog\);'			=> '&#xE6A1;',
			'&amp;\(cat\);'			=> '&#xE6A2;',
			'&amp;\(yacht\);'			=> '&#xE6A3;',
			'&amp;\(xmas\);'			=> '&#xE6A4;',
			'&amp;\(downwardleft\);'	=> '&#xE6A5;',
			'&amp;\(phoneto\);'		=> '&#xE6CE;',	
			'&amp;\(mailto\);'		=> '&#xE6CF;',
			'&amp;\(faxto\);'			=> '&#xE6D0;',
			'&amp;\(info01\);'		=> '&#xE6D1;',
			'&amp;\(info02\);'		=> '&#xE6D2;',
			'&amp;\(mail\);'			=> '&#xE6D3;',
			'&amp;\(by-d\);'			=> '&#xE6D4;',
			'&amp;\(d-point\);'		=> '&#xE6D5;',
			'&amp;\(yen\);'			=> '&#xE6D6;',
			'&amp;\(free\);'			=> '&#xE6D7;',
			'&amp;\(id\);'			=> '&#xE6D8;',
			'&amp;\(key\);'			=> '&#xE6D9;',
			'&amp;\(enter\);'			=> '&#xE6DA;',
			'&amp;\(clear\);'			=> '&#xE6DB;',
			'&amp;\(search\);'		=> '&#xE6DC;',
			'&amp;\(new\);'			=> '&#xE6DD;',
			'&amp;\(flag\);'			=> '&#xE6DE;',
			'&amp;\(freedial\);'		=> '&#xE6DF;',
			'&amp;\(sharp\);'			=> '&#xE6E0;',
			'&amp;\(mobaq\);'			=> '&#xE6E1;',
			'&amp;\(one\);'			=> '&#xE6E2;',
			'&amp;\(two\);'			=> '&#xE6E3;',
			'&amp;\(three\);'			=> '&#xE6E4;',
			'&amp;\(four\);'			=> '&#xE6E5;',
			'&amp;\(five\);'			=> '&#xE6E6;',
			'&amp;\(six\);'			=> '&#xE6E7;',
			'&amp;\(seven\);'			=> '&#xE6E8;',
			'&amp;\(eight\);'			=> '&#xE6E9;',
			'&amp;\(nine\);'			=> '&#xE6EA;',
			'&amp;\(zero\);'			=> '&#xE6EB;',
			'&amp;\(ok\);'			=> '&#xE70B;',
			'&amp;\(heart01\);'		=> '&#xE6EC;',
			'&amp;\(heart02\);'		=> '&#xE6ED;',
			'&amp;\(heart03\);'		=> '&#xE6EE;',
			'&amp;\(heart04\);'		=> '&#xE6EF;',
			'&amp;\(happy01\);'		=> '&#xE6F0;',
			'&amp;\(angry\);'			=> '&#xE6F1;',
			'&amp;\(despair\);'		=> '&#xE6F2;',
			'&amp;\(sad\);'			=> '&#xE6F3;',
			'&amp;\(wobbly\);'		=> '&#xE6F4;',
			'&amp;\(up\);'			=> '&#xE6F5;',
			'&amp;\(note\);'			=> '&#xE6F6;',
			'&amp;\(spa\);'			=> '&#xE6F7;',
			'&amp;\(cute\);'			=> '&#xE6F8;',
			'&amp;\(kissmark\);'		=> '&#xE6F9;',
			'&amp;\(shine\);'			=> '&#xE6FA;',
			'&amp;\(flair\);'			=> '&#xE6FB;',
			'&amp;\(annoy\);'			=> '&#xE6FC;',
			'&amp;\(punch\);'			=> '&#xE6FD;',
			'&amp;\(bomb\);'			=> '&#xE6FE;',
			'&amp;\(notes\);'			=> '&#xE6FF;',
			'&amp;\(down\);'			=> '&#xE700;',
			'&amp;\(sleepy\);'		=> '&#xE701;',
			'&amp;\(sign01\);'		=> '&#xE702;',
			'&amp;\(sign02\);'		=> '&#xE703;',
			'&amp;\(sign03\);'		=> '&#xE704;',
			'&amp;\(impact\);'		=> '&#xE705;',
			'&amp;\(sweat01\);'		=> '&#xE706;',
			'&amp;\(sweat02\);'		=> '&#xE707;',
			'&amp;\(dash\);'			=> '&#xE708;',
			'&amp;\(sign04\);'		=> '&#xE709;',
			'&amp;\(sign05\);'		=> '&#xE70A;',
			'&amp;\(slate\);'			=> '&#xE6AC;',
			'&amp;\(pouch\);'			=> '&#xE6AD;',
			'&amp;\(pen\);'			=> '&#xE6AE;',
			'&amp;\(shadow\);'		=> '&#xE6B1;',
			'&amp;\(chair\);'			=> '&#xE6B2;',
			'&amp;\(night\);'			=> '&#xE6B3;',
			'&amp;\(soon\);'			=> '&#xE6B7;',
			'&amp;\(on\);'			=> '&#xE6B8;',
			'&amp;\(end\);'			=> '&#xE6B9;',
			'&amp;\(clock\);'			=> '&#xE6BA;',
			// Docomo Extend Emoticon
			'&amp;\(appli01\);'		=> '&#xE70C;',
			'&amp;\(appli02\);'		=> '&#xE70D;',
			'&amp;\(t-shirt\);'		=> '&#xE70F;',
			'&amp;\(moneybag\);'		=> '&#xE70F;',
			'&amp;\(rouge\);'			=> '&#xE710;',
			'&amp;\(denim\);'			=> '&#xE711;',
			'&amp;\(snowboard\);'		=> '&#xE712;',
			'&amp;\(bell\);'			=> '&#xE713;',
			'&amp;\(door\);'			=> '&#xE714;',
			'&amp;\(dollar\);'		=> '&#xE715;',
			'&amp;\(pc\);'			=> '&#xE716;',
			'&amp;\(loveletter\);'	=> '&#xE717;',
			'&amp;\(wrench\);'		=> '&#xE718;',
			'&amp;\(pencil\);'		=> '&#xE719;',
			'&amp;\(crown\);'			=> '&#xE71A;',
			'&amp;\(ring\);'			=> '&#xE71B;',
			'&amp;\(sandclock\);'		=> '&#xE71C;',
			'&amp;\(bicycle\);'		=> '&#xE71D;',
			'&amp;\(japanesetea\);'	=> '&#xE71E;',
			'&amp;\(watch\);'			=> '&#xE71F;',
			'&amp;\(think\);'			=> '&#xE720;',
			'&amp;\(confident\);'		=> '&#xE721;',
			'&amp;\(coldsweats01\);'	=> '&#xE722;',
			'&amp;\(coldsweats02\);'	=> '&#xE723;',
			'&amp;\(pout\);'			=> '&#xE724;',
			'&amp;\(gawk\);'			=> '&#xE725;',
			'&amp;\(lovely\);'		=> '&#xE726;',
			'&amp;\(good\);'			=> '&#xE727;',
			'&amp;\(bleah\);'			=> '&#xE728;',
			'&amp;\(wink\);'			=> '&#xE729;',
			'&amp;\(happy02\);'		=> '&#xE72A;',
			'&amp;\(bearing\);'		=> '&#xE72B;',
			'&amp;\(catface\);'		=> '&#xE72C;',
			'&amp;\(crying\);'		=> '&#xE72D;',
			'&amp;\(weep\);'			=> '&#xE72E;',
			'&amp;\(ng\);'			=> '&#xE72F;',
			'&amp;\(clip\);'			=> '&#xE730;',
			'&amp;\(copyright\);'		=> '&#xE731;',
			'&amp;\(tm\);'			=> '&#xE732;',
			'&amp;\(run\);'			=> '&#xE733;',
			'&amp;\(secret\);'		=> '&#xE734;',
			'&amp;\(recycle\);'		=> '&#xE735;',
			'&amp;\(r-mark\);'		=> '&#xE736;',
			'&amp;\(danger\);'		=> '&#xE737;',
			'&amp;\(ban\);'			=> '&#xE738;',
			'&amp;\(empty\);'			=> '&#xE739;',
			'&amp;\(pass\);'			=> '&#xE73A;',
			'&amp;\(full\);'			=> '&#xE73B;',
			'&amp;\(leftright\);'		=> '&#xE73C;',
			'&amp;\(updown\);'		=> '&#xE73D;',
			'&amp;\(school\);'		=> '&#xE73E;',
			'&amp;\(wave\);'			=> '&#xE73F;',
			'&amp;\(fuji\);'			=> '&#xE740;',
			'&amp;\(clover\);'		=> '&#xE741;',
			'&amp;\(cherry\);'		=> '&#xE742;',
			'&amp;\(tulip\);'			=> '&#xE743;',
			'&amp;\(banana\);'		=> '&#xE744;',
			'&amp;\(apple\);'			=> '&#xE745;',
			'&amp;\(bud\);'			=> '&#xE746;',
			'&amp;\(maple\);'			=> '&#xE747;',
			'&amp;\(cherryblossom\);'	=> '&#xE748;',
			'&amp;\(riceball\);'		=> '&#xE749;',
			'&amp;\(cake\);'			=> '&#xE74A;',
			'&amp;\(bottle\);'		=> '&#xE74B;',
			'&amp;\(noodle\);'		=> '&#xE74C;',
			'&amp;\(bread\);'			=> '&#xE74D;',
			'&amp;\(snail\);'			=> '&#xE74E;',
			'&amp;\(chick\);'			=> '&#xE74F;',
			'&amp;\(penguin\);'		=> '&#xE750;',
			'&amp;\(fish\);'			=> '&#xE751;',
			'&amp;\(delicious\);'		=> '&#xE752;',
			'&amp;\(smile\);'			=> '&#xE753;',
			'&amp;\(horse\);'			=> '&#xE754;',
			'&amp;\(pig\);'			=> '&#xE755;',
			'&amp;\(wine\);'			=> '&#xE756;',
			'&amp;\(shock\);'			=> '&#xE757;'
		);
	break;

    // Graphic icons for Vodafone (ex. J-PHONE) cell phones
    // http://www.dp.j-phone.com/dp/tool_dl/web/picword_top.php
    case 'J-PHONE':

		$facemark_rules = array(
		// Docomo Standard Emoticon
			'&amp;\(sun\);'			=> '&#xE04A;',
			'&amp;\(cloud\);'			=> '&#xE049;',
			'&amp;\(rain\);'			=> '&#xE04B;',
			'&amp;\(snow\);'			=> '&#xE048;',
			'&amp;\(thunder\);'		=> '&#xE13D;',
			'&amp;\(typhoon\);'		=> '&#xEF41;',
			'&amp;\(mist\);'			=> '[Mist]',
			'&amp;\(sprinkle\);'		=> '&#xE43C;',
			'&amp;\(aries\);'			=> '&#xE240;',
			'&amp;\(taurus\);'		=> '&#xE241;',
			'&amp;\(gemini\);'		=> '&#xE242;',
			'&amp;\(cancer\);'		=> '&#xE243;',
			'&amp;\(leo\);'			=> '&#xE244;',
			'&amp;\(virgo\);'			=> '&#xE245;',
			'&amp;\(libra\);'			=> '&#xE246;',
			'&amp;\(scorpius\);'		=> '&#xE247;',
			'&amp;\(sagittarius\);'	=> '&#xE248;',
			'&amp;\(capricornus\);'	=> '&#xE249;',
			'&amp;\(aquarius\);'		=> '&#xE24A;',
			'&amp;\(pisces\);'		=> '&#xE115;',
			'&amp;\(sports\);'		=> '&#xE733;',
			'&amp;\(baseball\);'		=> '&#xE016;',
			'&amp;\(golf\);'			=> '&#xE014;',
			'&amp;\(tennis\);'		=> '&#xE015;',
			'&amp;\(soccer\);'		=> '&#xE018;',
			'&amp;\(ski\);'			=> '&#xE013;',
			'&amp;\(basketball\);'	=> '&#xE42A;',
			'&amp;\(motorsports\);'	=> '&#xE132;',
			'&amp;\(pocketbell\);'	=> '[pocketbell]',
			'&amp;\(train\);'			=> '&#xE01E;',
			'&amp;\(subway\);'		=> '&#xE434;',
			'&amp;\(bullettrain\);'	=> '&#xE01F;',
			'&amp;\(car\);'			=> '&#xE01B;',
			'&amp;\(rvcar\);'			=> '&#xE42E;',
			'&amp;\(bus\);'			=> '&#xE159;',
			'&amp;\(ship\);'			=> '&#xE202;',
			'&amp;\(airplane\);'		=> '&#xE01D;',
			'&amp;\(house\);'			=> '&#xE036;',
			'&amp;\(building\);'		=> '&#xE038;',
			'&amp;\(postoffice\);'	=> '&#xE153;',
			'&amp;\(hospital\);'		=> '&#xE155;',
			'&amp;\(bank\);'			=> '&#xE14D;',
			'&amp;\(atm\);'			=> '&#xE154;',
			'&amp;\(hotel\);'			=> '&#xE158;',
			'&amp;\(24hours\);'		=> '&#xE156;',
			'&amp;\(gasstation\);'	=> '&#xE03A;',
			'&amp;\(parking\);'		=> '&#xE14F;',
			'&amp;\(signaler\);'		=> '&#xE14E;',
			'&amp;\(toilet\);'		=> '&#xE151;',
			'&amp;\(restaurant\);'	=> '&#xE043;',
			'&amp;\(cafe\);'			=> '&#xE045;',
			'&amp;\(bar\);'			=> '&#xE044;',
			'&amp;\(beer\);'			=> '&#xE047;',
			'&amp;\(fastfood\);'		=> '&#xE120;',
			'&amp;\(boutique\);'		=> '&#xE13E;',
			'&amp;\(hairsalon\);'		=> '&#xE313;',
			'&amp;\(karaoke\);'		=> '&#xE03C;',
			'&amp;\(movie\);'			=> '&#xE03D;',
			'&amp;\(upwardright\);'	=> '&#xE236;',
			'&amp;\(carouselpony\);'	=> '&#xE124;',
			'&amp;\(music\);'			=> '&#xE30A;',
			'&amp;\(art\);'			=> '&#xE502;',
			'&amp;\(drama\);'			=> '&#xE503;',
			'&amp;\(event\);'			=> '&#xE506;',
			'&amp;\(ticket\);'		=> '&#xE125;',
			'&amp;\(smoking\);'		=> '&#xE30E;',
			'&amp;\(nosmoking\);'		=> '&#xE208;',
			'&amp;\(camera\);'		=> '&#xE008;',
			'&amp;\(bag\);'			=> '&#xE323;',
			'&amp;\(book\);'			=> '&#xE148;',
			'&amp;\(ribbon\);'		=> '&#xE314;',
			'&amp;\(present\);'		=> '&#xE112;',
			'&amp;\(birthday\);'		=> '&#xE34B;',
			'&amp;\(telephone\);'		=> '&#xE009;',
			'&amp;\(mobilephone\);'	=> '&#xE00A;',
			'&amp;\(memo\);'			=> '&#xE301;',
			'&amp;\(tv\);'			=> '&#xE12A;',
			'&amp;\(game\);'			=> '&#xE12B;',
			'&amp;\(cd\);'			=> '&#xE126;',
			'&amp;\(heart\);'			=> '&#xE20C;',
			'&amp;\(spade\);'			=> '&#xE20E;',
			'&amp;\(diamond\);'		=> '&#xE20D;',
			'&amp;\(club\);'			=> '&#xE20F;',
			'&amp;\(eye\);'			=> '&#xE419;',
			'&amp;\(ear\);'			=> '&#xE41B;',
			'&amp;\(rock\);'			=> '&#xE010;',
			'&amp;\(scissors\);'		=> '&#xE011;',
			'&amp;\(paper\);'			=> '&#xE012;',
			'&amp;\(downwardright\);'	=> '&#xE238;',
			'&amp;\(upwardleft\);'	=> '&#xE237;',
			'&amp;\(foot\);'			=> '&#xE536;',
			'&amp;\(shoe\);'			=> '&#xE007;',
			'&amp;\(eyeglass\);'		=> '[Eyeglass]',
			'&amp;\(wheelchair\);'	=> '&#xE20A;',
			'&amp;\(newmoon\);'		=> '●',
			'&amp;\(moon1\);'			=> '&#xE04C;',
			'&amp;\(moon2\);'			=> '&#xE04C;',
			'&amp;\(moon3\);'			=> '&#xE04C;',
			'&amp;\(fullmoon\);'		=> '○',
			'&amp;\(dog\);'			=> '&#xE052;',
			'&amp;\(cat\);'			=> '&#xE04F;',
			'&amp;\(yacht\);'			=> '&#xE01C;',
			'&amp;\(xmas\);'			=> '&#xE033;',
			'&amp;\(downwardleft\);'	=> '&#xE239;',
			'&amp;\(phoneto\);'		=> '&#xE104;',	//
			'&amp;\(mailto\);'		=> '&#xE103;',	//
			'&amp;\(faxto\);'			=> '&#xE00B;',
			'&amp;\(info01\);'		=> '[imode]',
			'&amp;\(info02\);'		=> '[imode]',
			'&amp;\(mail\);'			=> '&#xE103;',
			'&amp;\(by-d\);'			=> '[by-d]',
			'&amp;\(d-point\);'		=> '[d-point]',
			'&amp;\(yen\);'			=> '[yen]',
			'&amp;\(free\);'			=> '[Free]',
			'&amp;\(id\);'			=> '&#xE229;',
			'&amp;\(key\);'			=> '&#xE03F;',
			'&amp;\(enter\);'			=> '&#xE23D;',
			'&amp;\(clear\);'			=> '[CL]',
			'&amp;\(search\);'		=> '&#xE114;',
			'&amp;\(new\);'			=> '&#xE212;',
			'&amp;\(flag\);'			=> '[flag]',
			'&amp;\(freedial\);'		=> '&#xE211;',
			'&amp;\(sharp\);'			=> '&#xE210;',
			'&amp;\(mobaq\);'			=> '[Q]',
			'&amp;\(one\);'			=> '&#xE21C;',
			'&amp;\(two\);'			=> '&#xE21D;',
			'&amp;\(three\);'			=> '&#xE21E;',
			'&amp;\(four\);'			=> '&#xE21F;',
			'&amp;\(five\);'			=> '&#xE220;',
			'&amp;\(six\);'			=> '&#xE221;',
			'&amp;\(seven\);'			=> '&#xE222;',
			'&amp;\(eight\);'			=> '&#xE223;',
			'&amp;\(nine\);'			=> '&#xE224;',
			'&amp;\(zero\);'			=> '&#xE225;',
			'&amp;\(ok\);'			=> '&#xE226;',
			'&amp;\(heart01\);'		=> '&#xE022;',
			'&amp;\(heart02\);'		=> '&#xE328;',
			'&amp;\(heart03\);'		=> '&#xE023;',
			'&amp;\(heart04\);'		=> '&#xE327;',
			'&amp;\(happy01\);'		=> '&#xE415;',
			'&amp;\(angry\);'			=> '&#xE059;',
			'&amp;\(despair\);'		=> '&#xE058;',
			'&amp;\(sad\);'			=> '&#xE407;',
			'&amp;\(wobbly\);'		=> '&#xE406;',
			'&amp;\(up\);'			=> '&#xE232;',
			'&amp;\(note\);'			=> '&#xE03E;',
			'&amp;\(spa\);'			=> '&#xE123;',
			'&amp;\(cute\);'			=> '&#xE303;',
			'&amp;\(kissmark\);'		=> '&#xE003;',
			'&amp;\(shine\);'			=> '&#xE32E;',
			'&amp;\(flair\);'			=> '&#xE10F;',
			'&amp;\(annoy\);'			=> '&#xE334;',
			'&amp;\(punch\);'			=> '&#xE00D;',
			'&amp;\(bomb\);'			=> '&#xE311;',
			'&amp;\(notes\);'			=> '&#xE326;',
			'&amp;\(down\);'			=> '&#xE233;',
			'&amp;\(sleepy\);'		=> '&#xE13C;',
			'&amp;\(sign01\);'		=> '&#xE021;',
			'&amp;\(sign02\);'		=> '&#xE020;',
			'&amp;\(sign03\);'		=> '&#xE337;',
			'&amp;\(impact\);'		=> '&#xE335;',
			'&amp;\(sweat01\);'		=> '&#xE331;',
			'&amp;\(sweat02\);'		=> '&#xE331;',
			'&amp;\(dash\);'			=> '&#xE330;',
			'&amp;\(sign04\);'		=> '[iAppli]',
			'&amp;\(sign05\);'		=> '[iAppli]',
			'&amp;\(slate\);'			=> '[slate]',
			'&amp;\(pouch\);'			=> '[Poach]',
			'&amp;\(pen\);'			=> '&#xE301;',
			'&amp;\(shadow\);'		=> '&#xE001;',
			'&amp;\(chair\);'			=> '&#xE11F;',
			'&amp;\(night\);'			=> '&#xE44B;',
			'&amp;\(soon\);'			=> '[soon]',
			'&amp;\(on\);'			=> '[On]',
			'&amp;\(end\);'			=> '[End]',
			'&amp;\(clock\);'			=> '&#xE026;',
			// Docomo Extend Emoticon
/*
			'&amp;\(appli01\);'		=> '&#xE70C;',
			'&amp;\(appli02\);'		=> '&#xE70D;',
			'&amp;\(t-shirt\);'		=> '&#xE70F;',
			'&amp;\(moneybag\);'		=> '&#xE70F;',
			'&amp;\(rouge\);'			=> '&#xE710;',
			'&amp;\(denim\);'			=> '&#xE711;',
			'&amp;\(snowboard\);'		=> '&#xE712;',
			'&amp;\(bell\);'			=> '&#xE713;',
			'&amp;\(door\);'			=> '&#xE714;',
			'&amp;\(dollar\);'		=> '&#xE715;',
			'&amp;\(pc\);'			=> '&#xE716;',
			'&amp;\(loveletter\);'	=> '&#xE717;',
			'&amp;\(wrench\);'		=> '&#xE718;',
			'&amp;\(pencil\);'		=> '&#xE719;',
			'&amp;\(crown\);'			=> '&#xE71A;',
			'&amp;\(ring\);'			=> '&#xE71B;',
			'&amp;\(sandclock\);'		=> '&#xE71C;',
			'&amp;\(bicycle\);'		=> '&#xE71D;',
			'&amp;\(japanesetea\);'	=> '&#xE71E;',
			'&amp;\(watch\);'			=> '&#xE71F;',
			'&amp;\(think\);'			=> '&#xE720;',
			'&amp;\(confident\);'		=> '&#xE721;',
			'&amp;\(coldsweats01\);'	=> '&#xE722;',
			'&amp;\(coldsweats02\);'	=> '&#xE723;',
			'&amp;\(pout\);'			=> '&#xE724;',
			'&amp;\(gawk\);'			=> '&#xE725;',
			'&amp;\(lovely\);'		=> '&#xE726;',
			'&amp;\(good\);'			=> '&#xE727;',
			'&amp;\(bleah\);'			=> '&#xE728;',
			'&amp;\(wink\);'			=> '&#xE729;',
			'&amp;\(happy02\);'		=> '&#xE72A;',
			'&amp;\(bearing\);'		=> '&#xE72B;',
			'&amp;\(catface\);'		=> '&#xE72C;',
			'&amp;\(crying\);'		=> '&#xE72D;',
			'&amp;\(weep\);'			=> '&#xE72E;',
			'&amp;\(ng\);'			=> '&#xE72F;',
			'&amp;\(clip\);'			=> '&#xE730;',
			'&amp;\(copyright\);'		=> '&#xE731;',
			'&amp;\(tm\);'			=> '&#xE732;',
			'&amp;\(run\);'			=> '&#xE733;',
			'&amp;\(secret\);'		=> '&#xE734;',
			'&amp;\(recycle\);'		=> '&#xE735;',
			'&amp;\(r-mark\);'		=> '&#xE736;',
			'&amp;\(danger\);'		=> '&#xE737;',
			'&amp;\(ban\);'			=> '&#xE738;',
			'&amp;\(empty\);'			=> '&#xE739;',
			'&amp;\(pass\);'			=> '&#xE73A;',
			'&amp;\(full\);'			=> '&#xE73B;',
			'&amp;\(leftright\);'		=> '&#xE73C;',
			'&amp;\(updown\);'		=> '&#xE73D;',
			'&amp;\(school\);'		=> '&#xE73E;',
			'&amp;\(wave\);'			=> '&#xE73F;',
			'&amp;\(fuji\);'			=> '&#xE740;',
			'&amp;\(clover\);'		=> '&#xE741;',
			'&amp;\(cherry\);'		=> '&#xE742;',
			'&amp;\(tulip\);'			=> '&#xE743;',
			'&amp;\(banana\);'		=> '&#xE744;',
			'&amp;\(apple\);'			=> '&#xE745;',
			'&amp;\(bud\);'			=> '&#xE746;',
			'&amp;\(maple\);'			=> '&#xE747;',
			'&amp;\(cherryblossom\);'	=> '&#xE748;',
			'&amp;\(riceball\);'		=> '&#xE749;',
			'&amp;\(cake\);'			=> '&#xE74A;',
			'&amp;\(bottle\);'		=> '&#xE74B;',
			'&amp;\(noodle\);'		=> '&#xE74C;',
			'&amp;\(bread\);'			=> '&#xE74D;',
			'&amp;\(snail\);'			=> '&#xED83;',
			'&amp;\(chick\);'			=> '&#xEFB9;',
			'&amp;\(penguin\);'		=> '&#xEFB5;',
			'&amp;\(fish\);'			=> '&#xEF72;',
			'&amp;\(delicious\);'		=> '&#xECA1;',
			'&amp;\(smile\);'			=> '&#xED85;',
			'&amp;\(horse\);'			=> '&#xEFB1;',
			'&amp;\(pig\);'			=> '&#xEFB7;',
			'&amp;\(wine\);'			=> '&#xEF9A;',
			'&amp;\(shock\);'			=> '&#xF0F5;'
*/
		);
	break;

    case 'UP.Browser':
    	$facemark_rules = array(
			// Docomo Standard Emoticon
			'&amp;\(sun\);'			=> '&#xEF60;',
			'&amp;\(cloud\);'			=> '&#xEF65;',
			'&amp;\(rain\);'			=> '&#xEF64;',
			'&amp;\(snow\);'			=> '&#xEF5D;',
			'&amp;\(thunder\);'		=> '&#xEF5F;',
			'&amp;\(typhoon\);'		=> '&#xEF41;',
			'&amp;\(mist\);'			=> '&#xF0B5;',
			'&amp;\(sprinkle\);'		=> '&#xECBC;',
			'&amp;\(aries\);'			=> '&#xEF67;',
			'&amp;\(taurus\);'		=> '&#xEF68;',
			'&amp;\(gemini\);'		=> '&#xEF69;',
			'&amp;\(cancer\);'		=> '&#xEF6A;',
			'&amp;\(leo\);'			=> '&#xEF6B;',
			'&amp;\(virgo\);'			=> '&#xEF6C;',
			'&amp;\(libra\);'			=> '&#xEF6D;',
			'&amp;\(scorpius\);'		=> '&#xEF6E;',
			'&amp;\(sagittarius\);'	=> '&#xEF6F;',
			'&amp;\(capricornus\);'	=> '&#xEF70;',
			'&amp;\(aquarius\);'		=> '&#xEF71;',
			'&amp;\(pisces\);'		=> '&#xEF72;',
			'&amp;\(sports\);'		=> '&#xE733;',
			'&amp;\(baseball\);'		=> '&#xEF93;',
			'&amp;\(golf\);'			=> '&#xF0B6;',
			'&amp;\(tennis\);'		=> '&#xEF90;',
			'&amp;\(soccer\);'		=> '&#xEC80;',
			'&amp;\(ski\);'			=> '&#xF0B7;',
			'&amp;\(basketball\);'	=> '&#xEF92;',
			'&amp;\(motorsports\);'	=> '&#xF0B8;',
			'&amp;\(pocketbell\);'	=> '&#xF0B8;',
			'&amp;\(train\);'			=> '&#xEF8E;',
			'&amp;\(subway\);'		=> '&#xF0EC;',
			'&amp;\(bullettrain\);'	=> '&#xEF89;',
			'&amp;\(car\);'			=> '&#xEF8A;',
			'&amp;\(rvcar\);'			=> '&#xEF8A;',
			'&amp;\(bus\);'			=> '&#xEF88;',
			'&amp;\(ship\);'			=> '&#xEC55;',
			'&amp;\(airplane\);'		=> '&#xEF8C;',
			'&amp;\(house\);'			=> '&#xEF84;',
			'&amp;\(building\);'		=> '&#xEF86;',
			'&amp;\(postoffice\);'	=> '&#xEC51;',
			'&amp;\(hospital\);'		=> '&#xEC52;',
			'&amp;\(bank\);'			=> '&#xEF83;',
			'&amp;\(atm\);'			=> '&#xEF7B;',
			'&amp;\(hotel\);'			=> '&#xEC54;',
			'&amp;\(24hours\);'		=> '&#xEF7C;',
			'&amp;\(gasstation\);'	=> '&#xF08E;',
			'&amp;\(parking\);'		=> '&#xEF7E;',
			'&amp;\(signaler\);'		=> '&#xEF42;',
			'&amp;\(toilet\);'		=> '&#xEF7D;',
			'&amp;\(restaurant\);'	=> '&#xEF85;',
			'&amp;\(cafe\);'			=> '&#xF0B4;',
			'&amp;\(bar\);'			=> '&#xEF9B;',
			'&amp;\(beer\);'			=> '&#xEF9C;',
			'&amp;\(fastfood\);'		=> '&#xEFAF;',
			'&amp;\(boutique\);'		=> '&#xEFF3;',
			'&amp;\(hairsalon\);'		=> '&#xEFEF;',
			'&amp;\(karaoke\);'		=> '&#xEFDC;',
			'&amp;\(movie\);'			=> '&#xEFF0;',
			'&amp;\(upwardright\);'	=> '&#xF071;',
			'&amp;\(carouselpony\);'	=> '&#xEF45;',
			'&amp;\(music\);'			=> '&#xEFE1;',
			'&amp;\(art\);'				=> '&#xF0B9;',
			'&amp;\(drama\);'			=> '&#xECC9;',
			'&amp;\(event\);'			=> '&#xF0BB;',
			'&amp;\(ticket\);'			=> '&#xEF76;',
			'&amp;\(smoking\);'			=> '&#xEF55;',
			'&amp;\(nosmoking\);'		=> '&#xEF56;',
			'&amp;\(camera\);'			=> '&#xEFEE;',
			'&amp;\(bag\);'				=> '&#xEF74;',
			'&amp;\(book\);'			=> '&#xEF77;',
			'&amp;\(ribbon\);'			=> '&#xF0BC;',
			'&amp;\(present\);'			=> '&#xEFA8;',
			'&amp;\(birthday\);'		=> '&#xF0BD;',
			'&amp;\(telephone\);'		=> '&#xF0B3;',
			'&amp;\(mobilephone\);'		=> '&#xF0A5;',
			'&amp;\(memo\);'			=> '&#xF086;',
			'&amp;\(tv\);'				=> '&#xEFDB;',
			'&amp;\(game\);'			=> '&#xEF9F;',
			'&amp;\(cd\);'				=> '&#xEFE5;',
			'&amp;\(heart\);'			=> '&#xEC78;',
			'&amp;\(spade\);'			=> '&#xF0BE;',
			'&amp;\(diamond\);'			=> '&#xF0BF;',
			'&amp;\(club\);'			=> '&#xF0C0;',
			'&amp;\(eye\);'				=> '&#xF0C1;',
			'&amp;\(ear\);'				=> '&#xF0C2;',
			'&amp;\(rock\);'			=> '&#xED88;',
			'&amp;\(scissors\);'		=> '&#xF0C3;',
			'&amp;\(paper\);'			=> '&#xF0C4;',
			'&amp;\(downwardright\);'	=> '&#xF069;',
			'&amp;\(upwardleft\);'		=> '&#xF068;',
			'&amp;\(foot\);'			=> '&#xECEB;',
			'&amp;\(shoe\);'			=> '&#xF0E7;',
			'&amp;\(eyeglass\);'		=> '&#xEFD7;',
			'&amp;\(wheelchair\);'		=> '&#xEF57;',
			'&amp;\(newmoon\);'			=> '&#xF0C5;',
			'&amp;\(moon1\);'			=> '&#xF0C6;',
			'&amp;\(moon2\);'			=> '&#xF0C7;',
			'&amp;\(moon3\);'			=> '&#xEF5E;',
			'&amp;\(fullmoon\);'		=> '&#xEF61;',
			'&amp;\(dog\);'				=> '&#xEFBA;',
			'&amp;\(cat\);'				=> '&#xEFB4;',
			'&amp;\(yacht\);'			=> '&#xEF8D;',
			'&amp;\(xmas\);'			=> '&#xEFA2;',
			'&amp;\(downwardleft\);'	=> '&#xF072;',
			'&amp;\(phoneto\);'			=> '&#xF0DF;',	//
			'&amp;\(mailto\);'			=> '&#xED66;',	//
			'&amp;\(faxto\);'			=> '&#xEFF9;',
			'&amp;\(info01\);'			=> '[imode]',
			'&amp;\(info02\);'			=> '[imode]',
			'&amp;\(mail\);'			=> '&#xEFFA;',
			'&amp;\(by-d\);'			=> '[by-d]',
			'&amp;\(d-point\);'			=> '[d-point]',
			'&amp;\(yen\);'				=> '&#xF09A;',
			'&amp;\(free\);'			=> '&#xF095;',
			'&amp;\(id\);'				=> '&#xEC5B;',
			'&amp;\(key\);'				=> '&#xEFF2;',
			'&amp;\(enter\);'			=> '&#xF079;',
			'&amp;\(clear\);'			=> '&#xF0C8;',
			'&amp;\(search\);'			=> '&#xEFF1;',
			'&amp;\(new\);'				=> '&#xF0E5;',
			'&amp;\(flag\);'			=> '&#xECED;',
			'&amp;\(freedial\);'		=> '[free dial]',
			'&amp;\(sharp\);'			=> '&#xED89;',
			'&amp;\(mobaq\);'			=> '&#xF048;',
			'&amp;\(one\);'				=> '&#xEFFB;',
			'&amp;\(two\);'				=> '&#xEFFC;',
			'&amp;\(three\);'			=> '&#xF040;',
			'&amp;\(four\);'			=> '&#xF041;',
			'&amp;\(five\);'			=> '&#xF042;',
			'&amp;\(six\);'				=> '&#xF043;',
			'&amp;\(seven\);'			=> '&#xF044;',
			'&amp;\(eight\);'			=> '&#xF045;',
			'&amp;\(nine\);'			=> '&#xF046;',
			'&amp;\(zero\);'			=> '&#xF0C9;',
			'&amp;\(ok\);'				=> '&#xF0CA;',
			'&amp;\(heart01\);'			=> '&#xF0B2;',
			'&amp;\(heart02\);'			=> '&#xED79;',
			'&amp;\(heart03\);'			=> '&#xEF4F;',
			'&amp;\(heart04\);'			=> '&#xEF50;',
			'&amp;\(happy01\);'			=> '&#xEF49;',
			'&amp;\(angry\);'			=> '&#xEF4A;',
			'&amp;\(despair\);'			=> '&#xEC97;',
			'&amp;\(sad\);'				=> '&#xEC97;',
			'&amp;\(wobbly\);'			=> '&#xF0CB;',
			'&amp;\(up\);'				=> '&#xECEE;',
			'&amp;\(note\);'			=> '&#xF0EE;',
			'&amp;\(spa\);'				=> '&#xEF95;',
			'&amp;\(cute\);'			=> '&#xEC67;',
			'&amp;\(kissmark\);'		=> '&#xEFC4;',
			'&amp;\(shine\);'			=> '&#xEC7E;',
			'&amp;\(flair\);'			=> '&#xEF4E;',
			'&amp;\(annoy\);'			=> '&#xEFBE;',
			'&amp;\(punch\);'			=> '&#xEFCC;',
			'&amp;\(bomb\);'			=> '&#xEF52;',
			'&amp;\(notes\);'			=> '&#xEFDE;',
			'&amp;\(down\);'			=> '&#xECEF;',
			'&amp;\(sleepy\);'			=> '&#xEF4D;',
			'&amp;\(sign01\);'			=> '&#xECF0;',
			'&amp;\(sign02\);'			=> '&#xECF1;',
			'&amp;\(sign03\);'			=> '&#xE704;',
			'&amp;\(impact\);'			=> '&#xF0CD;',
			'&amp;\(sweat01\);'			=> '&#xF0CE;',
			'&amp;\(sweat02\);'			=> '&#xEFBF;',
			'&amp;\(dash\);'			=> '&#xEFCD;',
			'&amp;\(sign04\);'			=> '[iAppli]',
			'&amp;\(sign05\);'			=> '[iAppli]',
			'&amp;\(slate\);'			=> '[slate]',
			'&amp;\(pouch\);'			=> '&#xEFA0;',
			'&amp;\(pen\);'				=> '&#xF0DA;',
			'&amp;\(shadow\);'			=> '&#xEFD5;',
			'&amp;\(chair\);'			=> '[chair]',
			'&amp;\(night\);'			=> '&#xECC5;',
			'&amp;\(soon\);'			=> '&#xF06E;',
			'&amp;\(on\);'				=> '&#xED7E;',
			'&amp;\(end\);'				=> '&#xF06F;',
			'&amp;\(clock\);'			=> '&#xF0B1;',
			// Docomo Extend Emoticon
/*
			'&amp;\(appli01\);'		=> '&#xE70C;',
			'&amp;\(appli02\);'		=> '&#xE70D;',
			'&amp;\(t-shirt\);'		=> '&#xE70F;',
			'&amp;\(moneybag\);'		=> '&#xE70F;',
			'&amp;\(rouge\);'			=> '&#xE710;',
			'&amp;\(denim\);'			=> '&#xE711;',
			'&amp;\(snowboard\);'		=> '&#xE712;',
			'&amp;\(bell\);'			=> '&#xE713;',
			'&amp;\(door\);'			=> '&#xE714;',
			'&amp;\(dollar\);'		=> '&#xE715;',
			'&amp;\(pc\);'			=> '&#xE716;',
			'&amp;\(loveletter\);'	=> '&#xE717;',
			'&amp;\(wrench\);'		=> '&#xE718;',
			'&amp;\(pencil\);'		=> '&#xE719;',
			'&amp;\(crown\);'			=> '&#xE71A;',
			'&amp;\(ring\);'			=> '&#xE71B;',
			'&amp;\(sandclock\);'		=> '&#xE71C;',
			'&amp;\(bicycle\);'		=> '&#xE71D;',
			'&amp;\(japanesetea\);'	=> '&#xE71E;',
			'&amp;\(watch\);'			=> '&#xE71F;',
			'&amp;\(think\);'			=> '&#xE720;',
			'&amp;\(confident\);'		=> '&#xE721;',
			'&amp;\(coldsweats01\);'	=> '&#xE722;',
			'&amp;\(coldsweats02\);'	=> '&#xE723;',
			'&amp;\(pout\);'			=> '&#xE724;',
			'&amp;\(gawk\);'			=> '&#xE725;',
			'&amp;\(lovely\);'		=> '&#xE726;',
			'&amp;\(good\);'			=> '&#xE727;',
			'&amp;\(bleah\);'			=> '&#xE728;',
			'&amp;\(wink\);'			=> '&#xE729;',
			'&amp;\(happy02\);'		=> '&#xE72A;',
			'&amp;\(bearing\);'		=> '&#xE72B;',
			'&amp;\(catface\);'		=> '&#xE72C;',
			'&amp;\(crying\);'		=> '&#xE72D;',
			'&amp;\(weep\);'			=> '&#xE72E;',
			'&amp;\(ng\);'			=> '&#xE72F;',
			'&amp;\(clip\);'			=> '&#xE730;',
			'&amp;\(copyright\);'		=> '&#xE731;',
			'&amp;\(tm\);'			=> '&#xE732;',
			'&amp;\(run\);'			=> '&#xE733;',
			'&amp;\(secret\);'		=> '&#xE734;',
			'&amp;\(recycle\);'		=> '&#xE735;',
			'&amp;\(r-mark\);'		=> '&#xE736;',
			'&amp;\(danger\);'		=> '&#xE737;',
			'&amp;\(ban\);'			=> '&#xE738;',
			'&amp;\(empty\);'			=> '&#xE739;',
			'&amp;\(pass\);'			=> '&#xE73A;',
			'&amp;\(full\);'			=> '&#xE73B;',
			'&amp;\(leftright\);'		=> '&#xE73C;',
			'&amp;\(updown\);'			=> '&#xE73D;',
			'&amp;\(school\);'			=> '&#xE73E;',
			'&amp;\(wave\);'			=> '&#xE73F;',
			'&amp;\(fuji\);'			=> '&#xE740;',
			'&amp;\(clover\);'			=> '&#xE741;',
			'&amp;\(cherry\);'			=> '&#xE742;',
			'&amp;\(tulip\);'			=> '&#xE743;',
			'&amp;\(banana\);'			=> '&#xE744;',
			'&amp;\(apple\);'			=> '&#xE745;',
			'&amp;\(bud\);'				=> '&#xE746;',
			'&amp;\(maple\);'			=> '&#xE747;',
			'&amp;\(cherryblossom\);'	=> '&#xE748;',
			'&amp;\(riceball\);'		=> '&#xE749;',
			'&amp;\(cake\);'			=> '&#xE74A;',
			'&amp;\(bottle\);'			=> '&#xE74B;',
			'&amp;\(noodle\);'			=> '&#xE74C;',
			'&amp;\(bread\);'			=> '&#xE74D;',
			'&amp;\(snail\);'			=> '&#xED83;',
			'&amp;\(chick\);'			=> '&#xEFB9;',
			'&amp;\(penguin\);'			=> '&#xEFB5;',
			'&amp;\(fish\);'			=> '&#xEF72;',
			'&amp;\(delicious\);'		=> '&#xECA1;',
			'&amp;\(smile\);'			=> '&#xED85;',
			'&amp;\(horse\);'			=> '&#xEFB1;',
			'&amp;\(pig\);'				=> '&#xEFB7;',
			'&amp;\(wine\);'			=> '&#xEF9A;',
			'&amp;\(shock\);'			=> '&#xF0F5;'
*/
		);
	}
	break;

}

/////////////////////////////////////////////////
// クッキーを使用できないアドレス
// (通常は、携帯電話のアドレス)
$use_trans_sid_address = array(
	//
	// DoCoMo 2008/10/10 http://www.nttdocomo.co.jp/service/imode/make/content/ip/
	//
	'210.153.84.0/24',
	'210.136.161.0/24',
	'210.153.86.0/24',
	'124.146.174.0/24',	// add 2008/10/10
	'124.146.175.0/24',	// add 2008/10/10
	'210.153.87.0/24',	// full browser
	//
	// KDDIau 2008/12/11 http://www.au.kddi.com/ezfactory/tec/spec/ezsava_ip.html
	//
	'210.230.128.224/28',
	'61.117.0.128/25',
	'61.117.1.128/25',
	'218.222.1.0/25',
	'121.111.227.160/27',
	'218.222.1.128/28',
	'218.222.1.144/28',
	'218.222.1.160/28',
	'61.202.3.64/28',
	'61.117.1.0/28',
	'219.108.158.0/27',
	'219.125.146.0/28',
	'61.117.2.32/29',
	'61.117.2.40/29',
	'219.108.158.40/29',
	'219.125.148.0/25',
	'222.5.63.0/25',
	'222.5.63.128/25',
	'222.5.62.128/25',
	'59.135.38.128/25',
	'219.108.157.0/25',
	'219.125.145.0/25',
	'121.111.231.0/25',
	'121.111.227.0/25',
	'118.152.214.192/26',
	'118.159.131.0/25',
	'118.159.133.0/25',
	'219.125.148.160/27',
	'219.125.148.192/27',
	'222.7.56.0/27',
	'222.7.56.32/27',
	'222.7.56.96/27',
	'222.7.56.128/27',
	'222.7.56.192/27',
	'222.7.56.224/27',
	'222.7.57.64/27',
	'222.7.57.96/27',
	'222.7.57.128/27',
	'222.7.57.160/27',
	'222.7.57.192/27',
	'222.7.57.224/27',
	'219.125.151.128/27',
	'219.125.151.160/27',
	'219.125.151.192/27',
	'222.7.57.32/27',
	'121.111.231.160/27',
	//
	// Vodafone 2006/06/02 http://developers.softbankmobile.co.jp/dp/tech_svc/web/ip.php
	//
	'202.179.204.0/24',
	'210.146.7.192/26',
	'210.146.60.192/26',
	'210.151.9.128/26',
	'210.169.176.0/24',
	'210.175.1.128/25',
	'210.228.189.0/24',
	'211.8.159.128/25',
	// Willcom 2006/11/20 http://www.willcom-inc.com/ja/service/contents_service/club_air_edge/for_phone/ip/
	//                    http://www.willcom-inc.com/ja/service/contents_service/create/center_info/index.html
	'61.198.129.0/24',
//	'61.198.130.0/24', //del 06/11/20
	'61.198.132.0/24', //add 08/10/02
	'61.198.133.0/24', //add 08/10/02
	'61.198.134.0/24', //add 08/10/02
	'61.198.135.0/24', //add 08/10/02
	'61.198.136.0/24', //add 08/10/02
	'61.198.137.0/24', //add 08/10/02
	'61.198.140.0/24',
	'61.198.141.0/24',
	'61.198.142.0/24',
	'61.198.160.0/24', //add 08/10/02
	'61.198.161.0/24',
	'61.198.162.0/24', //add 08/10/02
	'61.198.164.0/24', //add 08/10/02
	'61.198.165.0/24', //add 06/11/13
	'61.198.166.0/24', //add 06/11/13
	'61.198.168.0/24', //add 06/11/13
	'61.198.169.0/24', //add 06/11/13
	'61.198.170.0/24', //add 06/11/13
	'61.198.171.0/24', //add 08/10/02
	'61.198.174.0/24', //add 08/10/02
	'61.198.175.0/24', //add 08/10/02
	'61.198.248.0/24', //add 06/11/13
	'61.198.249.0/24',
	'61.198.250.0/24',
	'61.198.251.0/24', //add 08/10/02
	'61.198.253.0/24',
	'61.198.254.0/24',
	'61.198.255.0/24',
	'61.198.163.0/24',
	'61.204.0.0/24',
	'61.204.2.0/24',
	'61.204.3.0/25',
	'61.204.4.0/24',
	'61.204.5.0/24',
	'61.204.6.0/25',
	'125.28.0.0/21',
	'125.28.8.0/24',
	'125.28.11.0/24',
	'125.28.12.0/24',
	'125.28.13.0/24',
	'125.28.14.0/24',
	'125.28.16.0/24',  //add 06/11/13
	'125.28.17.0/24',  //add 06/11/13
	'210.168.246.0/24',
	'210.168.247.0/24',
	'210.169.92.0/24', //add 08/10/02
	'210.169.93.0/24', //add 08/10/02
	'210.169.94.0/24', //add 08/10/02
	'210.169.95.0/24', //add 08/10/02
	'210.169.96.0/24', //add 08/10/02
	'210.169.97.0/24', //add 08/10/02
	'210.169.98.0/24', //add 08/10/02
	'210.169.99.0/24', //add 08/10/02
	'211.18.232.0/24',
	'211.18.233.0/24',
	'211.18.234.0/24', //add 06/11/13
	'211.18.235.0/24',
	'211.18.236.0/24',
	'211.18.237.0/24',
	'211.18.238.0/24',
	'211.18.239.0/24',
	'219.108.0.0/21',
	'219.108.8.0/24',  //add 06/11/13
	'219.108.9.0/24',  //add 06/11/13
	'219.108.10.0/24', //add 06/11/13
	'219.108.14.0/24',
//	'219.108.15.0/24', //del 06/11/20
	'221.119.0.0/21',
	'221.119.8.0/24',
	'221.119.9.0/24',
	// jig browser
	'210.143.108.0/24',
);

unset($matches, $ua_name, $ua_vers, $ua_agent, $special_rules);

?>
