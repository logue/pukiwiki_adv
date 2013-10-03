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
use PukiWiki\Renderer\RendererDefines;
use PukiWiki\Renderer\InlineFactory;
use Zend\Math\Rand;

class Rules{
	/**
	 * デフォルトのテキストルール
	 */
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
	/**
	 * Glyphiconルール
	 * http://getbootstrap.com/components/#glyphicons
	 */
	private static $glypicon_rules = array(
		'&amp;\(adjust\);'          => '<span class="glyphicon glyphicon-adjust"></span>',
		'&amp;\(align-center\);'    => '<span class="glyphicon glyphicon-align-center"></span>',
		'&amp;\(align-justify\);'   => '<span class="glyphicon glyphicon-align-justify"></span>',
		'&amp;\(align-left\);'      => '<span class="glyphicon glyphicon-align-left"></span>',
		'&amp;\(align-right\);'     => '<span class="glyphicon glyphicon-align-right"></span>',
		'&amp;\(arrow-down\);'      => '<span class="glyphicon glyphicon-arrow-down"></span>',
		'&amp;\(arrow-left\);'      => '<span class="glyphicon glyphicon-arrow-left"></span>',
		'&amp;\(arrow-right\);'     => '<span class="glyphicon glyphicon-arrow-right"></span>',
		'&amp;\(arrow-up\);'        => '<span class="glyphicon glyphicon-arrow-up"></span>',
		'&amp;\(asterisk\);'        => '<span class="glyphicon glyphicon-asterisk"></span>',
		'&amp;\(backward\);'        => '<span class="glyphicon glyphicon-backward"></span>',
		'&amp;\(ban-circle\);'      => '<span class="glyphicon glyphicon-ban-circle"></span>',
		'&amp;\(barcode\);'         => '<span class="glyphicon glyphicon-barcode"></span>',
		'&amp;\(bell\);'            => '<span class="glyphicon glyphicon-bell"></span>',
		'&amp;\(bold\);'            => '<span class="glyphicon glyphicon-bold"></span>',
		'&amp;\(book\);'            => '<span class="glyphicon glyphicon-book"></span>',
		'&amp;\(bookmark\);'        => '<span class="glyphicon glyphicon-bookmark"></span>',
		'&amp;\(briefcase\);'       => '<span class="glyphicon glyphicon-briefcase"></span>',
		'&amp;\(bullhorn\);'        => '<span class="glyphicon glyphicon-bullhorn"></span>',
		'&amp;\(calendar\);'        => '<span class="glyphicon glyphicon-calendar"></span>',
		'&amp;\(camera\);'          => '<span class="glyphicon glyphicon-camera"></span>',
		'&amp;\(certificate\);'     => '<span class="glyphicon glyphicon-certificate"></span>',
		'&amp;\(check\);'           => '<span class="glyphicon glyphicon-check"></span>',
		'&amp;\(chevron-down\);'    => '<span class="glyphicon glyphicon-chevron-down"></span>',
		'&amp;\(chevron-left\);'    => '<span class="glyphicon glyphicon-chevron-left"></span>',
		'&amp;\(chevron-up\);'      => '<span class="glyphicon glyphicon-chevron-up"></span>',
		'&amp;\(circle-arrow-down\);'   => '<span class="glyphicon glyphicon-circle-arrow-down"></span>',
		'&amp;\(circle-arrow-left\);'   => '<span class="glyphicon glyphicon-circle-arrow-left"></span>',
		'&amp;\(circle-arrow-right\);'  => '<span class="glyphicon glyphicon-circle-arrow-right"></span>',
		'&amp;\(circle-arrow-up\);'     => '<span class="glyphicon glyphicon-circle-arrow-up"></span>',
		'&amp;\(cloud\);'           => '<span class="glyphicon glyphicon-cloud"></span>',
		'&amp;\(cloud-download\);'  => '<span class="glyphicon glyphicon-cloud-download"></span>',
		'&amp;\(cloud-upload\);'    => '<span class="glyphicon glyphicon-cloud-upload"></span>',
		'&amp;\(cog\);'             => '<span class="glyphicon glyphicon-cog"></span>',
		'&amp;\(collapse-down\);'   => '<span class="glyphicon glyphicon-collapse-down"></span>',
		'&amp;\(collapse-up\);'     => '<span class="glyphicon glyphicon-collapse-up"></span>',
		'&amp;\(comment\);'         => '<span class="glyphicon glyphicon-comment"></span>',
		'&amp;\(compressed\);'      => '<span class="glyphicon glyphicon-compressed"></span>',
		'&amp;\(copyright-mark\);'  => '<span class="glyphicon glyphicon-copyright-mark"></span>',
		'&amp;\(credit-card\);'     => '<span class="glyphicon glyphicon-credit-card"></span>',
		'&amp;\(cutlery\);'         => '<span class="glyphicon glyphicon-cutlery"></span>',
		'&amp;\(dashboard\);'       => '<span class="glyphicon glyphicon-dashboard"></span>',
		'&amp;\(download\);'        => '<span class="glyphicon glyphicon-download"></span>',
		'&amp;\(download-alt\);'    => '<span class="glyphicon glyphicon-download-alt"></span>',
		'&amp;\(earphone\);'        => '<span class="glyphicon glyphicon-earphone"></span>',
		'&amp;\(edit\);'            => '<span class="glyphicon glyphicon-adjust"></span>',
		'&amp;\(eject\);'           => '<span class="glyphicon glyphicon-eject"></span>',
		'&amp;\(envelope\);'        => '<span class="glyphicon glyphicon-envelope"></span>',
		'&amp;\(euro\);'            => '<span class="glyphicon glyphicon-euro"></span>',
		'&amp;\(exclamation\);'     => '<span class="glyphicon glyphicon-exclamation"></span>',
		'&amp;\(expand\);'          => '<span class="glyphicon glyphicon-expand"></span>',
		'&amp;\(export\);'          => '<span class="glyphicon glyphicon-export"></span>',
		'&amp;\(eye-close\);'       => '<span class="glyphicon glyphicon-eye-close"></span>',
		'&amp;\(eye-open\);'        => '<span class="glyphicon glyphicon-eye-open"></span>',
		'&amp;\(facetime-video\);'  => '<span class="glyphicon glyphicon-facetime-video"></span>',
		'&amp;\(fast-backward\);'   => '<span class="glyphicon glyphicon-fast-backward"></span>',
		'&amp;\(fast-forward\);'    => '<span class="glyphicon glyphicon-fast-forward"></span>',
		'&amp;\(file\);'            => '<span class="glyphicon glyphicon-file"></span>',
		'&amp;\(film\);'            => '<span class="glyphicon glyphicon-film"></span>',
		'&amp;\(filter\);'          => '<span class="glyphicon glyphicon-filter"></span>',
		'&amp;\(fire\);'            => '<span class="glyphicon glyphicon-fire"></span>',
		'&amp;\(flag\);'            => '<span class="glyphicon glyphicon-flag"></span>',
		'&amp;\(flash\);'           => '<span class="glyphicon glyphicon-flash"></span>',
		'&amp;\(floppy-disk\);'     => '<span class="glyphicon glyphicon-floppy-disk"></span>',
		'&amp;\(floppy-open\);'     => '<span class="glyphicon glyphicon-floppy-open"></span>',
		'&amp;\(floppy-remove\);'   => '<span class="glyphicon glyphicon-floppy-remove"></span>',
		'&amp;\(floppy-save\);'     => '<span class="glyphicon glyphicon-floppy-save"></span>',
		'&amp;\(floppy-saved\);'    => '<span class="glyphicon glyphicon-floppy-saved"></span>',
		'&amp;\(folder-close\);'    => '<span class="glyphicon glyphicon-folder-close"></span>',
		'&amp;\(folder-open\);'     => '<span class="glyphicon glyphicon-folder-open"></span>',
		'&amp;\(font\);'            => '<span class="glyphicon glyphicon-font"></span>',
		'&amp;\(forward\);'         => '<span class="glyphicon glyphicon-forward"></span>',
		'&amp;\(fullscreen\);'      => '<span class="glyphicon glyphicon-fullscreen"></span>',
		'&amp;\(gbp\);'             => '<span class="glyphicon glyphicon-gbp"></span>',
		'&amp;\(gift\);'            => '<span class="glyphicon glyphicon-gift"></span>',
		'&amp;\(glass\);'           => '<span class="glyphicon glyphicon-glass"></span>',
		'&amp;\(globe\);'           => '<span class="glyphicon glyphicon-globe"></span>',
		'&amp;\(hand-down\);'       => '<span class="glyphicon glyphicon-hand-down"></span>',
		'&amp;\(hand-left\);'       => '<span class="glyphicon glyphicon-hand-left"></span>',
		'&amp;\(hand-right\);'      => '<span class="glyphicon glyphicon-hand-right"></span>',
		'&amp;\(hand-up\);'         => '<span class="glyphicon glyphicon-hand-up"></span>',
		'&amp;\(hd-video\);'        => '<span class="glyphicon glyphicon-hd-video"></span>',
		'&amp;\(hdd\);'             => '<span class="glyphicon glyphicon-hdd"></span>',
		'&amp;\(header\);'          => '<span class="glyphicon glyphicon-header"></span>',
		'&amp;\(headphones\);'      => '<span class="glyphicon glyphicon-headphones"></span>',
		'&amp;\(heart\);'           => '<span class="glyphicon glyphicon-heart"></span>',
		'&amp;\(heart-empty\);'     => '<span class="glyphicon glyphicon-heart-empty"></span>',
		'&amp;\(home\);'            => '<span class="glyphicon glyphicon-home"></span>',
		'&amp;\(import\);'          => '<span class="glyphicon glyphicon-import"></span>',
		'&amp;\(inbox\);'           => '<span class="glyphicon glyphicon-inbox"></span>',
		'&amp;\(indent-left\);'     => '<span class="glyphicon glyphicon-indent-left"></span>',
		'&amp;\(indent-right\);'    => '<span class="glyphicon glyphicon-indent-right"></span>',
		'&amp;\(info-sign\);'       => '<span class="glyphicon glyphicon-info-sign"></span>',
		'&amp;\(italic\);'          => '<span class="glyphicon glyphicon-italic"></span>',
		'&amp;\(leaf\);'            => '<span class="glyphicon glyphicon-leaf"></span>',
		'&amp;\(link\);'            => '<span class="glyphicon glyphicon-link"></span>',
		'&amp;\(list\);'            => '<span class="glyphicon glyphicon-list"></span>',
		'&amp;\(list-alt\);'        => '<span class="glyphicon glyphicon-list-alt"></span>',
		'&amp;\(lock\);'            => '<span class="glyphicon glyphicon-lock"></span>',
		'&amp;\(log-in\);'          => '<span class="glyphicon glyphicon-log-in"></span>',
		'&amp;\(log-out\);'         => '<span class="glyphicon glyphicon-log-out"></span>',
		'&amp;\(magnet\);'          => '<span class="glyphicon glyphicon-magnet"></span>',
		'&amp;\(map-marker\);'      => '<span class="glyphicon glyphicon-map-marker"></span>',
		'&amp;\(minus\);'           => '<span class="glyphicon glyphicon-minus"></span>',
		'&amp;\(minus-sign\);'      => '<span class="glyphicon glyphicon-minus-sign"></span>',
		'&amp;\(move\);'            => '<span class="glyphicon glyphicon-move"></span>',
		'&amp;\(music\);'           => '<span class="glyphicon glyphicon-music"></span>',
		'&amp;\(new-window\);'      => '<span class="glyphicon glyphicon-new-window"></span>',
		'&amp;\(off\);'             => '<span class="glyphicon glyphicon-off"></span>',
		'&amp;\(ok\);'              => '<span class="glyphicon glyphicon-ok"></span>',
		'&amp;\(ok-circle\);'       => '<span class="glyphicon glyphicon-ok-circle"></span>',
		'&amp;\(ok-sign\);'         => '<span class="glyphicon glyphicon-ok-sign"></span>',
		'&amp;\(open\);'            => '<span class="glyphicon glyphicon-open"></span>',
		'&amp;\(paperclip\);'       => '<span class="glyphicon glyphicon-paperclip"></span>',
		'&amp;\(pause\);'           => '<span class="glyphicon glyphicon-pause"></span>',
		'&amp;\(pencil\);'          => '<span class="glyphicon glyphicon-pencil"></span>',
		'&amp;\(phone\);'           => '<span class="glyphicon glyphicon-phone"></span>',
		'&amp;\(phone-alt\);'       => '<span class="glyphicon glyphicon-phone-alt"></span>',
		'&amp;\(picture\);'         => '<span class="glyphicon glyphicon-picture"></span>',
		'&amp;\(plane\);'           => '<span class="glyphicon glyphicon-plane"></span>',
		'&amp;\(play\);'            => '<span class="glyphicon glyphicon-play"></span>',
		'&amp;\(play-circle\);'     => '<span class="glyphicon glyphicon-play-circle"></span>',
		'&amp;\(plus\);'            => '<span class="glyphicon glyphicon-plus"></span>',
		'&amp;\(plus-sign\);'       => '<span class="glyphicon glyphicon-plus-sign"></span>',
		'&amp;\(print\);'           => '<span class="glyphicon glyphicon-print"></span>',
		'&amp;\(pushpin\);'         => '<span class="glyphicon glyphicon-pushpin"></span>',
		'&amp;\(qrcode\);'          => '<span class="glyphicon glyphicon-qrcode"></span>',
		'&amp;\(question-sign\);'   => '<span class="glyphicon glyphicon-question-sign"></span>',
		'&amp;\(random\);'          => '<span class="glyphicon glyphicon-random"></span>',
		'&amp;\(record\);'          => '<span class="glyphicon glyphicon-record"></span>',
		'&amp;\(refresh\);'         => '<span class="glyphicon glyphicon-refresh"></span>',
		'&amp;\(registration-mark\);'   => '<span class="glyphicon glyphicon-registration-mark"></span>',
		'&amp;\(remove\);'          => '<span class="glyphicon glyphicon-remove"></span>',
		'&amp;\(remove-circle\);'   => '<span class="glyphicon glyphicon-remove-circle"></span>',
		'&amp;\(remove-sign\);'     => '<span class="glyphicon glyphicon-remove-sign"></span>',
		'&amp;\(repeat\);'          => '<span class="glyphicon glyphicon-repeat"></span>',
		'&amp;\(resize-full\);'     => '<span class="glyphicon glyphicon-resize-full"></span>',
		'&amp;\(resize-horizontal\);'   => '<span class="glyphicon glyphicon-resize-horizontal"></span>',
		'&amp;\(resize-small\);'    => '<span class="glyphicon glyphicon-resize-small"></span>',
		'&amp;\(resize-vertical\);' => '<span class="glyphicon glyphicon-resize-vertical"></span>',
		'&amp;\(retweet\);'         => '<span class="glyphicon glyphicon-retweet"></span>',
		'&amp;\(road\);'            => '<span class="glyphicon glyphicon-road"></span>',
		'&amp;\(save\);'            => '<span class="glyphicon glyphicon-save"></span>',
		'&amp;\(saved\);'           => '<span class="glyphicon glyphicon-saved"></span>',
		'&amp;\(screenshot\);'      => '<span class="glyphicon glyphicon-screenshot"></span>',
		'&amp;\(sd-video\);'        => '<span class="glyphicon glyphicon-sd-video"></span>',
		'&amp;\(search\);'          => '<span class="glyphicon glyphicon-search"></span>',
		'&amp;\(send\);'            => '<span class="glyphicon glyphicon-send"></span>',
		'&amp;\(share\);'           => '<span class="glyphicon glyphicon-share"></span>',
		'&amp;\(share-alt\);'       => '<span class="glyphicon glyphicon-share-alt"></span>',
		'&amp;\(shopping-cart\);'   => '<span class="glyphicon glyphicon-shopping-cart"></span>',
		'&amp;\(signal\);'          => '<span class="glyphicon glyphicon-signal"></span>',
		'&amp;\(sort\);'            => '<span class="glyphicon glyphicon-sort"></span>',
		'&amp;\(sort-by-alphabet\);'=> '<span class="glyphicon glyphicon-sort-by-alphabet"></span>',
		'&amp;\(sort-by-alphabet-alt\);'    => '<span class="glyphicon glyphicon-sort-by-alphabet-alt"></span>',
		'&amp;\(sort-by-attributes\);'  => '<span class="glyphicon glyphicon-sort-by-attributes"></span>',
		'&amp;\(sort-by-attributes-alt\);'  => '<span class="glyphicon glyphicon-sort-by-attributes-alt"></span>',
		'&amp;\(sort-by-order\);'   => '<span class="glyphicon glyphicon-sort-by-order"></span>',
		'&amp;\(sort-by-order-alt\);'   => '<span class="glyphicon glyphicon-sort-by-order-alt"></span>',
		'&amp;\(sound-5-1\);'       => '<span class="glyphicon glyphicon-sound-5-1"></span>',
		'&amp;\(sound-6-1\);'       => '<span class="glyphicon glyphicon-sound-6-1"></span>',
		'&amp;\(sound-7-1\);'       => '<span class="glyphicon glyphicon-sound-7-1"></span>',
		'&amp;\(sound-dolby\);'     => '<span class="glyphicon glyphicon-sound-dolby"></span>',
		'&amp;\(sound-stereo\);'    => '<span class="glyphicon glyphicon-sound-stereo"></span>',
		'&amp;\(star\);'            => '<span class="glyphicon glyphicon-star"></span>',
		'&amp;\(star-empty\);'      => '<span class="glyphicon glyphicon-star-empty"></span>',
		'&amp;\(stats\);'           => '<span class="glyphicon glyphicon-stats"></span>',
		'&amp;\(step-backward\);'   => '<span class="glyphicon glyphicon-step-backward"></span>',
		'&amp;\(step-forward\);'    => '<span class="glyphicon glyphicon-step-forward"></span>',
		'&amp;\(stop\);'            => '<span class="glyphicon glyphicon-stop"></span>',
		'&amp;\(subtitles\);'       => '<span class="glyphicon glyphicon-subtitles"></span>',
		'&amp;\(tag\);'             => '<span class="glyphicon glyphicon-tag"></span>',
		'&amp;\(tags\);'            => '<span class="glyphicon glyphicon-tags"></span>',
		'&amp;\(tasks\);'           => '<span class="glyphicon glyphicon-tasks"></span>',
		'&amp;\(text-height\);'     => '<span class="glyphicon glyphicon-text-height"></span>',
		'&amp;\(text-width\);'      => '<span class="glyphicon glyphicon-text-width"></span>',
		'&amp;\(th\);'              => '<span class="glyphicon glyphicon-th"></span>',
		'&amp;\(th-large\);'        => '<span class="glyphicon glyphicon-th-large"></span>',
		'&amp;\(th-list\);'         => '<span class="glyphicon glyphicon-th-list"></span>',
		'&amp;\(thumbs-down\);'     => '<span class="glyphicon glyphicon-thumbs-down"></span>',
		'&amp;\(thumbs-up\);'       => '<span class="glyphicon glyphicon-thumbs-up"></span>',
		'&amp;\(time\);'            => '<span class="glyphicon glyphicon-time"></span>',
		'&amp;\(tint\);'            => '<span class="glyphicon glyphicon-tint"></span>',
		'&amp;\(tower\);'           => '<span class="glyphicon glyphicon-tower"></span>',
		'&amp;\(transfer\);'        => '<span class="glyphicon glyphicon-transfer"></span>',
		'&amp;\(trash\);'           => '<span class="glyphicon glyphicon-trash"></span>',
		'&amp;\(tree-conifer\);'    => '<span class="glyphicon glyphicon-tree-conifer"></span>',
		'&amp;\(tree-deciduous\);'  => '<span class="glyphicon glyphicon-tree-deciduous"></span>',
		'&amp;\(unchecked\);'       => '<span class="glyphicon glyphicon-unchecked"></span>',
		'&amp;\(upload\);'          => '<span class="glyphicon glyphicon-upload"></span>',
		'&amp;\(usd\);'             => '<span class="glyphicon glyphicon-usd"></span>',
		'&amp;\(user\);'            => '<span class="glyphicon glyphicon-user"></span>',
		'&amp;\(volume-down\);'     => '<span class="glyphicon glyphicon-volume-down"></span>',
		'&amp;\(volume-off\);'      => '<span class="glyphicon glyphicon-volume-off"></span>',
		'&amp;\(volume-up\);'       => '<span class="glyphicon glyphicon-volume-up"></span>',
		'&amp;\(warning-sign\);'    => '<span class="glyphicon glyphicon-warning-sign"></span>',
		'&amp;\(wrench\);'          => '<span class="glyphicon glyphicon-wrench"></span>',
		'&amp;\(zoom-in\);'         => '<span class="glyphicon glyphicon-zoom-in"></span>',
		'&amp;\(zoom-out\);'        => '<span class="glyphicon glyphicon-zoom-out"></span>'
	);
	/**
	 * 絵文字
	 */
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
	 * 見出しの固有IDのマッチパターン
	 */
	const HEADING_ID_PATTERN = '/^(\*{1,3})(.*?)(?:\[#([A-Za-z0-9][\w-]*)\]\s*)?$/m';
	/**
	 * 見出しのIDの生成で使用出来る文字
	 */
	const HEADING_ID_ACCEPT_CHARS = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
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
			$line = self::setHeading($line);
		}

		// Multiline part has no stopper
		if ($modify === FALSE && $multiline !== 0) $lines[] = str_repeat('}', $multiline);

		return join("\n", $lines);
	}
	/**
	 * 見出しのIDを作る
	 * @param string $str 入力文字列
	 * @param string $id 見出しのID
	 * @return string
	 */
	public static function setHeading($line, $id = null)
	{
		$matches = array();
		if (preg_match(self::HEADING_ID_PATTERN, $line, $matches) && (! isset($matches[3]) || empty($matches[3]) )) {
			// 7桁のランダム英数字をアンカー名として表題の末尾に付加
			$line = rtrim($matches[1] . $matches[2]) . ' [#' . (empty($id) ?  Rand::getString(7 ,self::HEADING_ID_ACCEPT_CHARS) : $id) . ']';
		}
		return $line;
	}
	/**
	 * 見出しからIDを取得
	 * @param string $str 入力文字列
	 * @param boolean $strip 見出し編集用のアンカーを削除する
	 * @return string
	 */
	public static function getHeading(& $str, $strip = TRUE)
	{
		// Cut fixed-heading anchors
		$id = '';
		$matches = array();
		if (preg_match(self::HEADING_ID_PATTERN, $str, $matches)) {	// 先頭が*から始まってて、なおかつ[#...]が存在する
			$str = $matches[2];
			$id  = isset($matches[3]) ? $matches[3] : null;
		} else {
			$str = preg_replace('/^\*{0,3}/', '', $str);
		}

		// Cut footnotes and tags
		if ($strip === TRUE)
			$str = Utility::stripHtmlTags(
				InlineFactory::factory(preg_replace('/'.RendererDefines::NOTE_PATTERN.'/x', '', $str))
			);

		return $id;
	}
	/**
	 * 見出しIDを削除
	 * @param string $str
	 * @return string
	 */
	public static function removeHeading($str){
		return preg_replace_callback(
			self::HEADING_ID_PATTERN,
			function ($matches){
				return $matches[2];
			},
			$str
		);
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
	/**
	 * テキストのルール設定
	 * @return array
	 */
	public static function getLineRules(){
		global $usedatetime;
		static $_line_rules;

		if (!isset($_line_rules)){
			$rules = self::init();
			// 日時置換ルールを$line_rulesに加える
			$_line_rules = self::$default_rules;
			if ($usedatetime) $_line_rules += $rules['datetime'];
			// Glyphicon
			$_line_rules += self::$glypicon_rules;
			// 絵文字
			$_line_rules += self::$emoji_rules;
		}
		return $_line_rules;
	}
}