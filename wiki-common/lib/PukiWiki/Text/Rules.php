<?php
/**
 * テキストルールクラス
 *
 * @package   PukiWiki\Text
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013 PukiWiki Advance Developers Team
 * @create    2013/02/02
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Rule.php,v 1.0.0 2013/02/02 17:28:00 Logue Exp $
 **/

namespace PukiWiki\Text;

use PukiWiki\Utility;

class Rules{
	private static $default_rules = array(
		// 実体参照パターンおよびシステムで使用するパターンを$line_rulesに加える
		// XHTML5では&lt;、&gt;、&amp;、&quot;と、&apos;のみ使える。
		// http://www.w3.org/TR/html5/the-xhtml-syntax.html
		'&amp;(#[0-9]+|#x[0-9a-f]+|(?=[a-zA-Z0-9]{2,8})(?:apos|amp|lt|gt|quot));' => '&$1;',
		// 行末にチルダは改行
		"\r" => "<br />\n",
		// PukiWiki Adv.標準書式
		'COLOR\(([^\(\)]*)\){([^}]*)}'                      => '<span style="color:$1">$2</span>',
		'SIZE\(([^\(\)]*)\){([^}]*)}'                       => '<span style="font-size:$1px">$2</span>',
		'COLOR\(([^\(\)]*)\):((?:(?!COLOR\([^\)]+\)\:).)*)' => '<span style="color:$1">$2</span>',
		'SIZE\(([^\(\)]*)\):((?:(?!SIZE\([^\)]+\)\:).)*)'   => '<span class="size$1">$2</span>',
		'SUP{([^}]*)}'                                      => '<sup>$1</sup>',
		'SUB{([^}]*)}'                                      => '<sub>$1</sub>',
		'LANG\(([^\(\)]*)\):((?:(?!LANG\([^\)]+\)\:).)*)'   => '<bdi lang="$1">$2</bdi>',
		'LANG\(([^\(\)]*)\){([^}]*)}'                       => '<bdi lang="$1">$2</bdi>',
		'%%%(?!%)((?:(?!%%%).)*)%%%'                        => '<ins>$1</ins>',
		'%%(?!%)((?:(?!%%).)*)%%'                           => '<del>$1</del>',
		'@@@(?!@)((?:(?!@@).)*)@@@'                         => '<q>$1</q>',
		'@@(?!@)((?:(?!@@).)*)@@'                           => '<code>$1</code>',
		'___(?!@)((?:(?!@@).)*)___'                         => '<s>$1</s>',
		'__(?!@)((?:(?!@@).)*)__'                           => '<span class="underline">$1</span>',
		// htmlsc関数対策。'を&#39;に変えてしまうため。
		"&#039;&#039;&#039;(?!&#039;)((?:(?!&#039;&#039;&#039;).)*)&#039;&#039;&#039;" => '<em>$1</em>',
		"&#039;&#039;(?!&#039;)((?:(?!&#039;&#039;).)*)&#039;&#039;" => '<strong>$1</strong>'
	);
	private static $emoji_rules = array(
		// text is Unicode6.0
		// http://ja.wikipedia.org/wiki/I%E3%83%A2%E3%83%BC%E3%83%89%E7%B5%B5%E6%96%87%E5%AD%97
		// http://www.unicode.org/charts/PDF/U1F300.pdf
		// Docomo standard emoji
		'&amp;\(sun\);'             => '<span class="emoji emoji-sun">☀</span>',	// F89F
		'&amp;\(cloud\);'           => '<span class="emoji emoji-cloud">☁</span>',	// F8A0
		'&amp;\(rain\);'            => '<span class="emoji emoji-rain">☂</span>',
		'&amp;\(snow\);'            => '<span class="emoji emoji-snow">☃</span>',
		'&amp;\(thunder\);'         => '<span class="emoji emoji-thunder">⚡</span>',
		'&amp;\(typhoon\);'         => '<span class="emoji emoji-typhoon">🌀</span>',
		'&amp;\(mist\);'            => '<span class="emoji emoji-mist">🌁</span>',
		'&amp;\(sprinkle\);'        => '<span class="emoji emoji-sprinkle">🌂</span>',
		'&amp;\(aries\);'           => '<span class="emoji emoji-ariels">♈</span>',
		'&amp;\(taurus\);'          => '<span class="emoji emoji-taurus">♉</span>',
		'&amp;\(gemini\);'			=> '<span class="emoji emoji-gemini">♊</span>',
		'&amp;\(cancer\);'			=> '<span class="emoji emoji-cancer">♋</span>',
		'&amp;\(leo\);'				=> '<span class="emoji emoji-leo">♌</span>',
		'&amp;\(virgo\);'			=> '<span class="emoji emoji-virgo">♍</span>',
		'&amp;\(libra\);'			=> '<span class="emoji emoji-libra">♎</span>',
		'&amp;\(scorpius\);'		=> '<span class="emoji emoji-scorpius">♏</span>',
		'&amp;\(sagittarius\);'		=> '<span class="emoji emoji-sagittarius">♐</span>',
		'&amp;\(capricornus\);'		=> '<span class="emoji emoji-capricornus">♑</span>',
		'&amp;\(aquarius\);'		=> '<span class="emoji emoji-aquarius">♒</span>',
		'&amp;\(pisces\);'			=> '<span class="emoji emoji-pisces">♓</span>',
		'&amp;\(sports\);'			=> '<span class="emoji emoji-sports">🎽</span>',
		'&amp;\(baseball\);'		=> '<span class="emoji emoji-baseball">⚾</span>',
		'&amp;\(golf\);'			=> '<span class="emoji emoji-golf">⛳</span>',
		'&amp;\(tennis\);'			=> '<span class="emoji emoji-teniss">🎾</span>',
		'&amp;\(soccer\);'			=> '<span class="emoji emoji-soccker">⚽</span>',
		'&amp;\(ski\);'				=> '<span class="emoji emoji-ski">🎿</span>',
		'&amp;\(basketball\);'		=> '<span class="emoji emoji-basketball">🏀</span>',
		'&amp;\(motorsports\);'		=> '<span class="emoji emoji-motersports">🏁</span>',
		'&amp;\(pocketbell\);'		=> '<span class="emoji emoji-pocketbell">📟</span>',
		'&amp;\(train\);'			=> '<span class="emoji emoji-train">🚃</span>',
		'&amp;\(subway\);'			=> '<span class="emoji emoji-subway">Ⓜ</span>',
		'&amp;\(bullettrain\);'		=> '<span class="emoji emoji-bullettrain">🚄</span>',
		'&amp;\(car\);'				=> '<span class="emoji emoji-car">🚗</span>',
		'&amp;\(rvcar\);'			=> '<span class="emoji emoji-rvcar">🚙</span>',
		'&amp;\(bus\);'				=> '<span class="emoji emoji-bus">🚌</span>',
		'&amp;\(ship\);'			=> '<span class="emoji emoji-ship">🚢</span>',
		'&amp;\(airplane\);'		=> '<span class="emoji emoji-airplane">✈</span>',
		'&amp;\(house\);'			=> '<span class="emoji emoji-horse">🏠</span>',
		'&amp;\(building\);'		=> '<span class="emoji emoji-building">🏢</span>',
		'&amp;\(postoffice\);'		=> '<span class="emoji emoji-postoffice">🏣</span>',
		'&amp;\(hospital\);'		=> '<span class="emoji emoji-hospital">🏥</span>',
		'&amp;\(bank\);'			=> '<span class="emoji emoji-bank">🏦</span>',
		'&amp;\(atm\);'				=> '<span class="emoji emoji-atm">🏧</span>',
		'&amp;\(hotel\);'			=> '<span class="emoji emoji-hotel">🏨</span>',
		'&amp;\(24hours\);'			=> '<span class="emoji emoji-24hours">🏪</span>',
		'&amp;\(gasstation\);'		=> '<span class="emoji emoji-gasstation">⛽</span>',
		'&amp;\(parking\);'			=> '<span class="emoji emoji-parking">🅿</span>',
		'&amp;\(signaler\);'		=> '<span class="emoji emoji-signaler">🚥</span>',
		'&amp;\(toilet\);'			=> '<span class="emoji emoji-toilet">🚻</span>',
		'&amp;\(restaurant\);'		=> '<span class="emoji emoji-restaurant">🍴</span>',
		'&amp;\(cafe\);'			=> '<span class="emoji emoji-cafe">☕</span>',
		'&amp;\(bar\);'				=> '<span class="emoji emoji-bar">🍸</span>',
		'&amp;\(beer\);'			=> '<span class="emoji emoji-beer">🍺</span>',
		'&amp;\(fastfood\);'		=> '<span class="emoji emoji-fastfood">🍔</span>',
		'&amp;\(boutique\);'		=> '<span class="emoji emoji-boutique">👠</span>',
		'&amp;\(hairsalon\);'		=> '<span class="emoji emoji-hairsalon">✂</span>',
		'&amp;\(karaoke\);'			=> '<span class="emoji emoji-karaoke">🎤</span>',
		'&amp;\(movie\);'			=> '<span class="emoji emoji-movie">🎥</span>',
		'&amp;\(upwardright\);'		=> '<span class="emoji emoji-upwardright">↗</span>',
		'&amp;\(carouselpony\);'	=> '<span class="emoji emoji-carouselpony">🎠</span>',
		'&amp;\(music\);'			=> '<span class="emoji emoji-music">🎧</span>',
		'&amp;\(art\);'				=> '<span class="emoji emoji-art">🎨</span>',
		'&amp;\(drama\);'			=> '<span class="emoji emoji-drama">🎩</span>',
		'&amp;\(event\);'			=> '<span class="emoji emoji-event">🎪</span>',
		'&amp;\(ticket\);'			=> '<span class="emoji emoji-ticket">🎫</span>',
		'&amp;\(smoking\);'			=> '<span class="emoji emoji-smoking">🚬</span>',
		'&amp;\(nosmoking\);'		=> '<span class="emoji emoji-nosmoking">🚭</span>',
		'&amp;\(camera\);'			=> '<span class="emoji emoji-camera">📷</span>',
		'&amp;\(bag\);'				=> '<span class="emoji emoji-bag">👜</span>',
		'&amp;\(book\);'			=> '<span class="emoji emoji-book">📖</span>',
		'&amp;\(ribbon\);'			=> '<span class="emoji emoji-ribbon">🎀</span>',
		'&amp;\(present\);'			=> '<span class="emoji emoji-present">🎁</span>',
		'&amp;\(birthday\);'		=> '<span class="emoji emoji-birthday">🎂</span>',
		'&amp;\(telephone\);'		=> '<span class="emoji emoji-telephone">☎</span>',
		'&amp;\(mobilephone\);'		=> '<span class="emoji emoji-mobilephone">📱</span>',
		'&amp;\(memo\);'			=> '<span class="emoji emoji-memo">📝</span>',
		'&amp;\(tv\);'				=> '<span class="emoji emoji-tv">📺</span>',
		'&amp;\(game\);'			=> '<span class="emoji emoji-game">🎮</span>',
		'&amp;\(cd\);'				=> '<span class="emoji emoji-cd">💿</span>',
		'&amp;\(heart\);'			=> '<span class="emoji emoji-heart">♥</span>',
		'&amp;\(spade\);'			=> '<span class="emoji emoji-spade">♠</span>',
		'&amp;\(diamond\);'			=> '<span class="emoji emoji-diamond">♦</span>',
		'&amp;\(club\);'			=> '<span class="emoji emoji-club">♣</span>',
		'&amp;\(eye\);'				=> '<span class="emoji emoji-eye">👀</span>',
		'&amp;\(ear\);'				=> '<span class="emoji emoji-ear">👂</span>',
		'&amp;\(rock\);'			=> '<span class="emoji emoji-rock">✊</span>',
		'&amp;\(scissors\);'		=> '<span class="emoji emoji-scissors">✌</span>',
		'&amp;\(paper\);'			=> '<span class="emoji emoji-paper">✋</span>',
		'&amp;\(downwardright\);'	=> '<span class="emoji emoji-downwardright">↘</span>',
		'&amp;\(upwardleft\);'		=> '<span class="emoji emoji-upwardleft">↖</span>',
		'&amp;\(foot\);'			=> '<span class="emoji emoji-foot">👣</span>',
		'&amp;\(shoe\);'			=> '<span class="emoji emoji-shoe">👟</span>',
		'&amp;\(eyeglass\);'		=> '<span class="emoji emoji-eyeglass">👓</span>',
		'&amp;\(wheelchair\);'		=> '<span class="emoji emoji-wheelchair">♿</span>',	// F8FC
		'&amp;\(newmoon\);'			=> '<span class="emoji emoji-newmoon">🌔</span>',	// F940
		'&amp;\(moon1\);'			=> '<span class="emoji emoji-moon1">🌔</span>',
		'&amp;\(moon2\);'			=> '<span class="emoji emoji-moon2">🌓</span>',
		'&amp;\(moon3\);'			=> '<span class="emoji emoji-moon3">🌙</span>',
		'&amp;\(fullmoon\);'		=> '<span class="emoji emoji-fullmoon">🌕</span>',
		'&amp;\(dog\);'				=> '<span class="emoji emoji-dog">🐶</span>',
		'&amp;\(cat\);'				=> '<span class="emoji emoji-cat">🐱</span>',
		'&amp;\(yacht\);'			=> '<span class="emoji emoji-yacht">⛵</span>',
		'&amp;\(xmas\);'			=> '<span class="emoji emoji-xmas">🎄</span>',
		'&amp;\(downwardleft\);'	=> '<span class="emoji emoji-downwardleft">↙</span>',
		'&amp;\(phoneto\);'			=> '<span class="emoji emoji-phoneto">📲</span>',
		'&amp;\(mailto\);'			=> '<span class="emoji emoji-mailto">📩</span>',
		'&amp;\(faxto\);'			=> '<span class="emoji emoji-faxto">📠</span>',
		'&amp;\(info01\);'			=> '<span class="emoji emoji-info01"></span>',
		'&amp;\(info02\);'			=> '<span class="emoji emoji-info02"></span>',
		'&amp;\(mail\);'			=> '<span class="emoji emoji-mail">✉</span>',
		'&amp;\(by-d\);'			=> '<span class="emoji emoji-by-d"></span>',
		'&amp;\(d-point\);'			=> '<span class="emoji emoji-d-point"></span>',
		'&amp;\(yen\);'				=> '<span class="emoji emoji-yen">💴</span>',
		'&amp;\(free\);'			=> '<span class="emoji emoji-free">🆓</span>',
		'&amp;\(id\);'				=> '<span class="emoji emoji-id">🆔</span>',
		'&amp;\(key\);'				=> '<span class="emoji emoji-key">🔑</span>',
		'&amp;\(enter\);'			=> '<span class="emoji emoji-enter">↩</span>',
		'&amp;\(clear\);'			=> '<span class="emoji emoji-clear">🆑</span>',
		'&amp;\(search\);'			=> '<span class="emoji emoji-search">🔍</span>',
		'&amp;\(new\);'				=> '<span class="emoji emoji-new">🆕</span>',
		'&amp;\(flag\);'			=> '<span class="emoji emoji-flag">🚩</span>',
		'&amp;\(freedial\);'		=> '<span class="emoji emoji-freedial"></span>',
		'&amp;\(sharp\);'			=> '<span class="emoji emoji-sharp">#⃣</span>',
		'&amp;\(mobaq\);'			=> '<span class="emoji emoji-mobaq"></span>',
		'&amp;\(one\);'				=> '<span class="emoji emoji-one">1⃣</span>',
		'&amp;\(two\);'				=> '<span class="emoji emoji-two">2⃣</span>',
		'&amp;\(three\);'			=> '<span class="emoji emoji-three">3⃣</span>',
		'&amp;\(four\);'			=> '<span class="emoji emoji-four">4⃣</span>',
		'&amp;\(five\);'			=> '<span class="emoji emoji-five">5⃣</span>',
		'&amp;\(six\);'				=> '<span class="emoji emoji-six">6⃣</span>',
		'&amp;\(seven\);'			=> '<span class="emoji emoji-seven">7⃣</span>',
		'&amp;\(eight\);'			=> '<span class="emoji emoji-eight">8⃣</span>',
		'&amp;\(nine\);'			=> '<span class="emoji emoji-nine">9⃣</span>',
		'&amp;\(zero\);'			=> '<span class="emoji emoji-zero">0⃣</span>',
		'&amp;\(ok\);'				=> '<span class="emoji emoji-ok">🆗</span>',
		'&amp;\(heart01\);'			=> '<span class="emoji emoji-heart01">❤</span>',
		'&amp;\(heart02\);'			=> '<span class="emoji emoji-heart02">💓</span>',
		'&amp;\(heart03\);'			=> '<span class="emoji emoji-heart03">💔</span>',
		'&amp;\(heart04\);'			=> '<span class="emoji emoji-heart04">💕</span>',
		'&amp;\(happy01\);'			=> '<span class="emoji emoji-happy01">😃</span>',
		'&amp;\(angry\);'			=> '<span class="emoji emoji-angry">😠</span>',
		'&amp;\(despair\);'			=> '<span class="emoji emoji-despair">😞</span>',
		'&amp;\(sad\);'				=> '<span class="emoji emoji-sad">😖</span>',
		'&amp;\(wobbly\);'			=> '<span class="emoji emoji-wobbly">😵</span>',
		'&amp;\(up\);'				=> '<span class="emoji emoji-up">⤴</span>',
		'&amp;\(note\);'			=> '<span class="emoji emoji-note">🎵</span>',
		'&amp;\(spa\);'				=> '<span class="emoji emoji-spa">♨</span>',
		'&amp;\(cute\);'			=> '<span class="emoji emoji-cute">💠</span>',
		'&amp;\(kissmark\);'		=> '<span class="emoji emoji-kissmark">💋</span>',
		'&amp;\(shine\);'			=> '<span class="emoji emoji-shine">✨</span>',
		'&amp;\(flair\);'			=> '<span class="emoji emoji-flair">💡</span>',
		'&amp;\(annoy\);'			=> '<span class="emoji emoji-annoy">💢</span>',
		'&amp;\(punch\);'			=> '<span class="emoji emoji-punch">👊</span>',
		'&amp;\(bomb\);'			=> '<span class="emoji emoji-bomb">💣</span>',
		'&amp;\(notes\);'			=> '<span class="emoji emoji-notes">🎶</span>',
		'&amp;\(down\);'			=> '<span class="emoji emoji-down">⤵</span>',
		'&amp;\(sleepy\);'			=> '<span class="emoji emoji-sleepy">💤</span>',
		'&amp;\(sign01\);'			=> '<span class="emoji emoji-sign01">❗</span>',
		'&amp;\(sign02\);'			=> '<span class="emoji emoji-sign02">⁉</span>',
		'&amp;\(sign03\);'			=> '<span class="emoji emoji-sign03">‼</span>',
		'&amp;\(impact\);'			=> '<span class="emoji emoji-impact">💥</span>',
		'&amp;\(sweat01\);'			=> '<span class="emoji emoji-sweat01">💦</span>',
		'&amp;\(sweat02\);'			=> '<span class="emoji emoji-sweat02">💧</span>',
		'&amp;\(dash\);'			=> '<span class="emoji emoji-dash">💨</span>',
		'&amp;\(sign04\);'			=> '<span class="emoji emoji-sign04">〰</span>',
		'&amp;\(sign05\);'			=> '<span class="emoji emoji-sign05">➰</span>',
		'&amp;\(slate\);'			=> '<span class="emoji emoji-slate">👕</span>',
		'&amp;\(pouch\);'			=> '<span class="emoji emoji-pouch">👛</span>',
		'&amp;\(pen\);'				=> '<span class="emoji emoji-pen">💄</span>',
		'&amp;\(shadow\);'			=> '<span class="emoji emoji-shadow">👤</span>',
		'&amp;\(chair\);'			=> '<span class="emoji emoji-chair">💺</span>',
		'&amp;\(night\);'			=> '<span class="emoji emoji-night">🌃</span>',
		'&amp;\(soon\);'			=> '<span class="emoji emoji-soon">🔜</span>',
		'&amp;\(on\);'				=> '<span class="emoji emoji-on">🔛</span>',
		'&amp;\(end\);'				=> '<span class="emoji emoji-end">🔚</span>',
		'&amp;\(clock\);'			=> '<span class="emoji emoji-clock">⏰</span>',
		// Docomo Extend emoji
		'&amp;\(appli01\);'			=> '<span class="emoji emoji-appli01"></span>',
		'&amp;\(appli02\);'			=> '<span class="emoji emoji-appli02"></span>',
		'&amp;\(t-shirt\);'			=> '<span class="emoji emoji-t-shirt">👕</span>',	// F9B3
		'&amp;\(moneybag\);'		=> '<span class="emoji emoji-moneybag">👛</span>',
		'&amp;\(rouge\);'			=> '<span class="emoji emoji-rouge">💄</span>',
		'&amp;\(denim\);'			=> '<span class="emoji emoji-denim">👖</span>',
		'&amp;\(snowboard\);'		=> '<span class="emoji emoji-snowboard">🏂</span>',
		'&amp;\(bell\);'			=> '<span class="emoji emoji-bell">🔔</span>',
		'&amp;\(door\);'			=> '<span class="emoji emoji-door">🚪</span>',
		'&amp;\(dollar\);'			=> '<span class="emoji emoji-dollar">💰</span>',
		'&amp;\(pc\);'				=> '<span class="emoji emoji-pc">💻</span>',
		'&amp;\(loveletter\);'		=> '<span class="emoji emoji-loveletter">💌</span>',
		'&amp;\(wrench\);'			=> '<span class="emoji emoji-wrench">🔧</span>',
		'&amp;\(pencil\);'			=> '<span class="emoji emoji-pencil">✏</span>',
		'&amp;\(crown\);'			=> '<span class="emoji emoji-crown">👑</span>',
		'&amp;\(ring\);'			=> '<span class="emoji emoji-ring">💍</span>',	// F9C0
		'&amp;\(sandclock\);'		=> '<span class="emoji emoji-sandclock">⏳</span>',
		'&amp;\(bicycle\);'			=> '<span class="emoji emoji-bicycle">🚲</span>',
		'&amp;\(japanesetea\);'		=> '<span class="emoji emoji-japanesetea">🍵</span>',
		'&amp;\(watch\);'			=> '<span class="emoji emoji-watch">⌚</span>',
		'&amp;\(think\);'			=> '<span class="emoji emoji-think">😔</span>',
		'&amp;\(confident\);'		=> '<span class="emoji emoji-confident">😌</span>',
		'&amp;\(coldsweats01\);'	=> '<span class="emoji emoji-coldsweats01">😅</span>',
		'&amp;\(coldsweats02\);'	=> '<span class="emoji emoji-coldsweats02">😓</span>',
		'&amp;\(pout\);'			=> '<span class="emoji emoji-pout">😡</span>',
		'&amp;\(gawk\);'			=> '<span class="emoji emoji-gawk">😒</span>',
		'&amp;\(lovely\);'			=> '<span class="emoji emoji-lovely">😍</span>',
		'&amp;\(good\);'			=> '<span class="emoji emoji-good">👍</span>',
		'&amp;\(bleah\);'			=> '<span class="emoji emoji-bleah">😜</span>',
		'&amp;\(wink\);'			=> '<span class="emoji emoji-wink">😉</span>',
		'&amp;\(happy02\);'			=> '<span class="emoji emoji-happy02">😆</span>',
		'&amp;\(bearing\);'			=> '<span class="emoji emoji-bearing">😣</span>',	// F9D0
		'&amp;\(catface\);'			=> '<span class="emoji emoji-catface">😏</span>',
		'&amp;\(crying\);'			=> '<span class="emoji emoji-crying">😭</span>',
		'&amp;\(weep\);'			=> '<span class="emoji emoji-weep">😢</span>',
		'&amp;\(ng\);'				=> '<span class="emoji emoji-ng">🆖</span>',
		'&amp;\(clip\);'			=> '<span class="emoji emoji-clip">📎</span>',
		'&amp;\(copyright\);'		=> '<span class="emoji emoji-copyright">©</span>',
		'&amp;\(tm\);'				=> '<span class="emoji emoji-tm">™</span>',
		'&amp;\(run\);'				=> '<span class="emoji emoji-run">🏃</span>',
		'&amp;\(secret\);'			=> '<span class="emoji emoji-secret">㊙</span>',
		'&amp;\(recycle\);'			=> '<span class="emoji emoji-recycle">♻</span>',
		'&amp;\(r-mark\);'			=> '<span class="emoji emoji-r-mark">®</span>',
		'&amp;\(danger\);'			=> '<span class="emoji emoji-danger">⚠</span>',
		'&amp;\(ban\);'				=> '<span class="emoji emoji-ban">🈲</span>',
		'&amp;\(empty\);'			=> '<span class="emoji emoji-empty">🈳</span>',
		'&amp;\(pass\);'			=> '<span class="emoji emoji-pass">🈴</span>',
		'&amp;\(full\);'			=> '<span class="emoji emoji-full">🈵</span>',
		'&amp;\(leftright\);'		=> '<span class="emoji emoji-leftright">↔</span>',
		'&amp;\(updown\);'			=> '<span class="emoji emoji-updown">↕</span>',
		'&amp;\(school\);'			=> '<span class="emoji emoji-school">🏫</span>',
		'&amp;\(wave\);'			=> '<span class="emoji emoji-wave">🌊</span>',
		'&amp;\(fuji\);'			=> '<span class="emoji emoji-fuji">🗻</span>',
		'&amp;\(clover\);'			=> '<span class="emoji emoji-clover">🍀</span>',
		'&amp;\(cherry\);'			=> '<span class="emoji emoji-cherry">🍒</span>',
		'&amp;\(tulip\);'			=> '<span class="emoji emoji-tulip">🌷</span>',
		'&amp;\(banana\);'			=> '<span class="emoji emoji-banana">🍌</span>',
		'&amp;\(apple\);'			=> '<span class="emoji emoji-apple">🍎</span>',
		'&amp;\(bud\);'				=> '<span class="emoji emoji-bud">🌱</span>',
		'&amp;\(maple\);'			=> '<span class="emoji emoji-maple">🍁</span>',
		'&amp;\(cherryblossom\);'	=> '<span class="emoji emoji-cherryblossom">🌸</span>',
		'&amp;\(riceball\);'		=> '<span class="emoji emoji-riceball">🍙</span>',
		'&amp;\(cake\);'			=> '<span class="emoji emoji-cake">🍰</span>',
		'&amp;\(bottle\);'			=> '<span class="emoji emoji-bottle">🍶</span>',
		'&amp;\(noodle\);'			=> '<span class="emoji emoji-noodle">🍜</span>',
		'&amp;\(bread\);'			=> '<span class="emoji emoji-bread">🍞</span>',
		'&amp;\(snail\);'			=> '<span class="emoji emoji-snail">🐌</span>',
		'&amp;\(chick\);'			=> '<span class="emoji emoji-chick">🐤</span>',
		'&amp;\(penguin\);'			=> '<span class="emoji emoji-penguin">🐧</span>',
		'&amp;\(fish\);'			=> '<span class="emoji emoji-fish">🐟</span>',
		'&amp;\(delicious\);'		=> '<span class="emoji emoji-delicious">😋</span>',
		'&amp;\(smile\);'			=> '<span class="emoji emoji-smile">😁</span>',
		'&amp;\(horse\);'			=> '<span class="emoji emoji-horse">🐴</span>',
		'&amp;\(pig\);'				=> '<span class="emoji emoji-pig">🐷</span>',
		'&amp;\(wine\);'			=> '<span class="emoji emoji-wine">🍷</span>',
		'&amp;\(shock\);'			=> '<span class="emoji emoji-shock">😱</span>'
	);
	/**
	 * 設定を読み込む
	 */
	private static function init(){
		static $rules;
		if (!isset($rules)) $rules = Utility::loadConfig('rules.ini.php');
		return $rules;
	}
	/**
	 * ソースをシステム（rules.ini.phpなど）で定義されているルールに基づいて自動修正
	 * @param array $source ソース
	 * @return string
	 */
	public static function make_str_rules($source){
		// Modify original text with user-defined / system-defined rules
		global $fixed_heading_anchor;
		$rules = self::init();

		$lines = explode("\n", $source);
		$count = count($lines);

		$modify    = TRUE;
		$multiline = 0;
		$matches   = array();
		for ($i = 0; $i < $count; $i++) {
			$line = & $lines[$i]; // Modify directly

			// Ignore null string and preformatted texts
			if ( empty($line) || $line{0} == ' ' || $line{0} == "\t") continue;

			// Modify this line?
			if ($modify) {
				if ($multiline === 0 && preg_match('/#[^{]*(\{\{+)\s*$/', $line, $matches)) {
					// Multiline convert plugin start
					$modify    = FALSE;
					$multiline = strlen($matches[1]); // Set specific number
				}
			} else {
				if ($multiline !== 0 && preg_match('/^\}{' . $multiline . '}\s*$/', $line)) {
					// Multiline convert plugin end
					$modify    = TRUE;
					$multiline = 0;
				}
			}
			if ($modify === FALSE) continue;

			// Replace with $str_rules
			foreach ($rules['str'] as $pattern => $replacement)
				$line = preg_replace('/' . $pattern . '/', $replacement, $line);

			// Adding fixed anchor into headings
			if ($fixed_heading_anchor && preg_match('/^(\*{1,3}.*?)(?:\[#([A-Za-z][\w-]*)\]\s*)?$/', $line, $matches) &&
					(! isset($matches[2]) || empty($matches[2]) )) {
				// Generate unique id
				$anchor = Zend\Math\Rand::getString(7,'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
				$line = rtrim($matches[1]) . ' [#' . $anchor . ']';
			}
		}

		// Multiline part has no stopper
		if ($modify === FALSE && $multiline !== 0) $lines[] = str_repeat('}', $multiline);

		return join("\n", $lines);
	}
	/**
	 * 他のページを読み込むときに余計なものを取り除く
	 * @param string $str
	 * @return string
	 */
	public static function replaceFilter($str){
		static $patternf, $replacef;
		if (!isset($patternf)) {
			$rules = self::init();
			$patternf = array_map(create_function('$a','return "/$a/";'), array_keys($rules['filter']));
			$replacef = array_values($rules['filter']);
			unset($filter_rules);
		}
		return preg_replace($patternf, $replacef, $str);
	}
	public static function getLineRules(){
		global $usedatetime;
		static $_line_rules;

		if (!isset($_line_rules)){
			$rules = self::init();
			// 日時置換ルールを$line_rulesに加える
			$_line_rules = self::$default_rules;
			if ($usedatetime) $_line_rules += $rules['datetime'];
			$_line_rules += self::$emoji_rules;
		}
		return $_line_rules;
	}
}