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
 * @version   $Id: Rule.php,v 1.5.0 2013/11/14 18:05:00 Logue Exp $
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
	 * Font Awasomeルール
	 * http://fontawesome.io/icons/
	 */
	private static $fa_rules = array(
		'&amp;\(adjust\);'          => '<span class="fa fa-adjust"></span>',
		'&amp;\(adn\);'             => '<span class="fa fa-adn"></span>',
		'&amp;\(align-center\);'    => '<span class="fa fa-align-center"></span>',
		'&amp;\(align-justify\);'   => '<span class="fa fa-align-justify"></span>',
		'&amp;\(align-left\);'      => '<span class="fa fa-align-left"></span>',
		'&amp;\(align-right\);'     => '<span class="fa fa-align-right"></span>',
		'&amp;\(ambulance\);'       => '<span class="fa fa-ambulance"></span>',
		'&amp;\(anchor\);'          => '<span class="fa fa-anchor"></span>',
		'&amp;\(android\);'         => '<span class="fa fa-android"></span>',
		'&amp;\(angle-down\);'      => '<span class="fa fa-angle-down"></span>',
		'&amp;\(angle-left\);'      => '<span class="fa fa-angle-left"></span>',
		'&amp;\(angle-right\);'     => '<span class="fa fa-angle-right"></span>',
		'&amp;\(angle-angle-up\);'  => '<span class="fa fa-angle-angle-up"></span>',
		'&amp;\(apple\);'           => '<span class="fa fa-apple"></span>',
		'&amp;\(archive\);'         => '<span class="fa fa-archive"></span>',
		'&amp;\(arrow-circle-o-right\);'=> '<span class="fa fa-arrow-right"></span>',
		'&amp;\(arrow-circle-o-left\);' => '<span class="fa fa-arrow-left"></span>',
		'&amp;\(arrow-left\);'      => '<span class="fa fa-arrow-left"></span>',
		'&amp;\(arrow-right\);'     => '<span class="fa fa-arrow-right"></span>',
		'&amp;\(arrow-up\);'        => '<span class="fa fa-arrow-up"></span>',
		'&amp;\(arrow-down\);'      => '<span class="fa fa-arrow-down"></span>',
		'&amp;\(arrow-left\);'      => '<span class="fa fa-arrow-left"></span>',
		'&amp;\(arrow-right\);'     => '<span class="fa fa-arrow-right"></span>',
		'&amp;\(arrow-up\);'        => '<span class="fa fa-arrow-up"></span>',
		'&amp;\(asterisk\);'        => '<span class="fa fa-asterisk"></span>',
		'&amp;\(backward\);'        => '<span class="fa fa-backward"></span>',
		'&amp;\(ban-circle\);'      => '<span class="fa fa-ban-circle"></span>',
		'&amp;\(bar-chart\);'       => '<span class="fa fa-bar-chart"></span>',
		'&amp;\(barcode\);'         => '<span class="fa fa-barcode"></span>',
		'&amp;\(beaker\);'          => '<span class="fa fa-beaker"></span>',
		'&amp;\(beer\);'            => '<span class="fa fa-beer"></span>',
		'&amp;\(bell\);'            => '<span class="fa fa-bell"></span>',
		'&amp;\(bell-alt\);'        => '<span class="fa fa-bell-alt"></span>',
		'&amp;\(bitbucket\);'       => '<span class="fa fa-bitbucket"></span>',
		'&amp;\(bitbucket-sign\);'  => '<span class="fa fa-bitbucket-sign"></span>',
		'&amp;\(bitcoin\);'         => '<span class="fa fa-bitcoin"></span>',
		'&amp;\(bold\);'            => '<span class="fa fa-bold"></span>',
		'&amp;\(bolt\);'            => '<span class="fa fa-bolt"></span>',
		'&amp;\(book\);'            => '<span class="fa fa-book"></span>',
		'&amp;\(bookmark\);'        => '<span class="fa fa-bookmark"></span>',
		'&amp;\(bookmark-empty\);'  => '<span class="fa fa-bookmark-empty"></span>',
		'&amp;\(briefcase\);'       => '<span class="fa fa-briefcase"></span>',
		'&amp;\(btc\);'             => '<span class="fa fa-btc"></span>',
		'&amp;\(bug\);'             => '<span class="fa fa-bug"></span>',
		'&amp;\(building\);'        => '<span class="fa fa-building"></span>',
		'&amp;\(bullhorn\);'        => '<span class="fa fa-bullhorn"></span>',
		'&amp;\(bullseye\);'        => '<span class="fa fa-bullseye"></span>',
		'&amp;\(calendar\);'        => '<span class="fa fa-calendar"></span>',
		'&amp;\(calendar-empty\);'  => '<span class="fa fa-calendar-empty"></span>',
		'&amp;\(camera\);'          => '<span class="fa fa-camera"></span>',
		'&amp;\(caret-down\);'      => '<span class="fa fa-caret-down"></span>',
		'&amp;\(caret-left\);'      => '<span class="fa fa-caret-left"></span>',
		'&amp;\(caret-right\);'     => '<span class="fa fa-caret-right"></span>',
		'&amp;\(caret-up\);'        => '<span class="fa fa-caret-up"></span>',
		'&amp;\(caret-square-o-left\);' => '<span class="fa fa-caret-square-o-left"></span>',
		'&amp;\(certificate\);'     => '<span class="fa fa-certificate"></span>',
		'&amp;\(check\);'           => '<span class="fa fa-check"></span>',
		'&amp;\(check-empty\);'     => '<span class="fa fa-check-empty"></span>',
		'&amp;\(check-minus\);'     => '<span class="fa fa-check-minus"></span>',
		'&amp;\(check-sign\);'      => '<span class="fa fa-check-sign"></span>',
		'&amp;\(chevron-down\);'    => '<span class="fa fa-chevron-down"></span>',
		'&amp;\(chevron-right\);'   => '<span class="fa fa-chevron-right"></span>',
		'&amp;\(chevron-left\);'    => '<span class="fa fa-chevron-left"></span>',
		'&amp;\(chevron-up\);'      => '<span class="fa fa-chevron-up"></span>',
		'&amp;\(chevron-sign-down\);'   => '<span class="fa fa-chevron-sign-down"></span>',
		'&amp;\(chevron-sign-right\);'  => '<span class="fa fa-chevron-sign-right"></span>',
		'&amp;\(chevron-sign-left\);'   => '<span class="fa fa-chevron-sign-left"></span>',
		'&amp;\(chevron-sign-up\);'     => '<span class="fa fa-chevron-sign-up"></span>',
		'&amp;\(circle\);'          => '<span class="fa fa-circle"></span>',
		'&amp;\(circle-arrow-down\);'   => '<span class="fa fa-circle-arrow-down"></span>',
		'&amp;\(circle-arrow-left\);'   => '<span class="fa fa-circle-arrow-left"></span>',
		'&amp;\(circle-arrow-right\);'  => '<span class="fa fa-circle-arrow-right"></span>',
		'&amp;\(circle-arrow-up\);'     => '<span class="fa fa-circle-arrow-up"></span>',
		'&amp;\(circle-blank\);'    => '<span class="fa fa-circle-blank"></span>',
		'&amp;\(cloud\);'           => '<span class="fa fa-cloud"></span>',
		'&amp;\(cloud-download\);'  => '<span class="fa fa-cloud-download"></span>',
		'&amp;\(cloud-upload\);'    => '<span class="fa fa-cloud-upload"></span>',
		'&amp;\(cny\);'             => '<span class="fa fa-cny"></span>',
		'&amp;\(code\);'            => '<span class="fa fa-code"></span>',
		'&amp;\(code-fork\);'       => '<span class="fa fa-code-fork"></span>',
		'&amp;\(coffee\);'          => '<span class="fa fa-coffee"></span>',
		'&amp;\(cog\);'             => '<span class="fa fa-cog"></span>',
		'&amp;\(cogs\);'            => '<span class="fa fa-cogs"></span>',
		'&amp;\(collapse\);'        => '<span class="fa fa-collapse"></span>',
		'&amp;\(collapse-alt\);'    => '<span class="fa fa-collapse-alt"></span>',
		'&amp;\(collapse-top\);'    => '<span class="fa fa-collapse-top"></span>',
		'&amp;\(columns\);'         => '<span class="fa fa-columns"></span>',
		'&amp;\(comment\);'         => '<span class="fa fa-comment"></span>',
		'&amp;\(comment-alt\);'     => '<span class="fa fa-comment-alt"></span>',
		'&amp;\(comments\);'        => '<span class="fa fa-comments"></span>',
		'&amp;\(comments-alt\);'    => '<span class="fa fa-comments-alt"></span>',
		'&amp;\(compass\);'         => '<span class="fa fa-compass"></span>',
		'&amp;\(copy\);'            => '<span class="fa fa-copy"></span>',
		'&amp;\(credit-card\);'     => '<span class="fa fa-credit-card"></span>',
		'&amp;\(crop\);'            => '<span class="fa fa-crop"></span>',
		'&amp;\(css3\);'            => '<span class="fa fa-css3"></span>',
		'&amp;\(cut\);'             => '<span class="fa fa-cut"></span>',
		'&amp;\(dashboard\);'       => '<span class="fa fa-dashboard"></span>',
		'&amp;\(desktop\);'         => '<span class="fa fa-desktop"></span>',
		'&amp;\(dollar\);'          => '<span class="fa fa-dollar"></span>',
		'&amp;\(dot-circle-o\);'    => '<span class="fa fa-dot-circle-o"></span>',
		'&amp;\(double-angle-down\);'   => '<span class="fa fa-double-angle-down"></span>',
		'&amp;\(double-angle-left\);'   => '<span class="fa fa-double-angle-left"></span>',
		'&amp;\(double-angle-right\);'  => '<span class="fa fa-double-angle-right"></span>',
		'&amp;\(double-angle-up\);' => '<span class="fa fa-double-angle-up"></span>',
		'&amp;\(download\);'        => '<span class="fa fa-download"></span>',
		'&amp;\(download-alt\);'    => '<span class="fa fa-download-alt"></span>',
		'&amp;\(dribbble\);'        => '<span class="fa fa-dribbble"></span>',
		'&amp;\(dropbox\);'         => '<span class="fa fa-dropbox"></span>',
		'&amp;\(edit\);'            => '<span class="fa fa-edit"></span>',
		'&amp;\(edit-sign\);'       => '<span class="fa fa-edit-sign"></span>',
		'&amp;\(eject\);'           => '<span class="fa fa-eject"></span>',
		'&amp;\(ellipsis-horizontal\);' => '<span class="fa fa-ellipsis-horizontal"></span>',
		'&amp;\(ellipsis-vertical\);'   => '<span class="fa fa-ellipsis-vertical"></span>',
		'&amp;\(envelope\);'        => '<span class="fa fa-envelope"></span>',
		'&amp;\(envelope-alt\);'    => '<span class="fa fa-envelope-alt"></span>',
		'&amp;\(eraser\);'          => '<span class="fa fa-eraser"></span>',
		'&amp;\(eur\);'             => '<span class="fa fa-eur"></span>',
		'&amp;\(euro\);'            => '<span class="fa fa-euro"></span>',
		'&amp;\(exchange\);'        => '<span class="fa fa-exchange"></span>',
		'&amp;\(exclamation\);'     => '<span class="fa fa-exclamation"></span>',
		'&amp;\(exclamation-sign\);'    => '<span class="fa fa-exclamation-sign"></span>',
		'&amp;\(expand\);'          => '<span class="fa fa-expand"></span>',
		'&amp;\(expand-alt\);'      => '<span class="fa fa-expand-alt"></span>',
		'&amp;\(external-link\);'   => '<span class="fa fa-external-link"></span>',
		'&amp;\(external-link-sign\);'  => '<span class="fa fa-external-link-sign"></span>',
		'&amp;\(eye-close\);'       => '<span class="fa fa-eye-close"></span>',
		'&amp;\(eye-open\);'        => '<span class="fa fa-eye-open"></span>',
		'&amp;\(facebook\);'        => '<span class="fa fa-facebook"></span>',
		'&amp;\(facebook-sign\);'       => '<span class="fa fa-facebook-sign"></span>',
		'&amp;\(facetime-video\);'  => '<span class="fa fa-facetime-video"></span>',
		'&amp;\(fast-backward\);'   => '<span class="fa fa-fast-backward"></span>',
		'&amp;\(fast-forward\);'    => '<span class="fa fa-fast-forward"></span>',
		'&amp;\(female\);'          => '<span class="fa fa-female"></span>',
		'&amp;\(fighter-jet\);'     => '<span class="fa fa-fighter-jet"></span>',
		'&amp;\(file\);'            => '<span class="fa fa-file"></span>',
		'&amp;\(file-alt\);'        => '<span class="fa fa-file-alt"></span>',
		'&amp;\(file-text\);'       => '<span class="fa fa-file-text"></span>',
		'&amp;\(file-text-alt\);'   => '<span class="fa fa-file-text-alt"></span>',
		'&amp;\(film\);'            => '<span class="fa fa-film"></span>',
		'&amp;\(filter\);'          => '<span class="fa fa-filter"></span>',
		'&amp;\(fire\);'            => '<span class="fa fa-fire"></span>',
		'&amp;\(fire-extinguisher\);'   => '<span class="fa fa-fire-extinguisher"></span>',
		'&amp;\(flag\);'            => '<span class="fa fa-flag"></span>',
		'&amp;\(flag-alt\);'        => '<span class="fa fa-flag-alt"></span>',
		'&amp;\(flag-checkered\);'  => '<span class="fa fa-flag-checkered"></span>',
		'&amp;\(flash\);'           => '<span class="fa fa-flash"></span>',
		'&amp;\(folder-close\);'    => '<span class="fa fa-folder-close"></span>',
		'&amp;\(folder-close-alt\);'=> '<span class="fa fa-folder-close-alt"></span>',
		'&amp;\(folder-open\);'     => '<span class="fa fa-folder-open"></span>',
		'&amp;\(folder-open-alt\);' => '<span class="fa fa-folder-open-alt"></span>',
		'&amp;\(font\);'            => '<span class="fa fa-font"></span>',
		'&amp;\(food\);'            => '<span class="fa fa-food"></span>',
		'&amp;\(forward\);'         => '<span class="fa fa-forward"></span>',
		'&amp;\(foursquare\);'      => '<span class="fa fa-foursquare"></span>',
		'&amp;\(frown\);'           => '<span class="fa fa-frown"></span>',
		'&amp;\(fullscreen\);'      => '<span class="fa fa-fullscreen"></span>',
		'&amp;\(gamepad\);'         => '<span class="fa fa-gamepad"></span>',
		'&amp;\(gbp\);'             => '<span class="fa fa-gbp"></span>',
		'&amp;\(gear\);'            => '<span class="fa fa-gear"></span>',
		'&amp;\(gears\);'           => '<span class="fa fa-gears"></span>',
		'&amp;\(gift\);'            => '<span class="fa fa-gift"></span>',
		'&amp;\(github\);'          => '<span class="fa fa-github"></span>',
		'&amp;\(github-alt\);'      => '<span class="fa fa-github-alt"></span>',
		'&amp;\(github-sign\);'     => '<span class="fa fa-github-sign"></span>',
		'&amp;\(gittip\);'          => '<span class="fa fa-gittip"></span>',
		'&amp;\(glass\);'           => '<span class="fa fa-glass"></span>',
		'&amp;\(globe\);'           => '<span class="fa fa-globe"></span>',
		'&amp;\(google-plus\);'     => '<span class="fa fa-google-plus"></span>',
		'&amp;\(google-plus-sign\);'=> '<span class="fa fa-google-plus-sign"></span>',
		'&amp;\(group\);'           => '<span class="fa fa-group"></span>',
		'&amp;\(hand-down\);'       => '<span class="fa fa-hand-down"></span>',
		'&amp;\(hand-left\);'       => '<span class="fa fa-hand-left"></span>',
		'&amp;\(hand-right\);'      => '<span class="fa fa-hand-right"></span>',
		'&amp;\(hand-up\);'         => '<span class="fa fa-hand-up"></span>',
		'&amp;\(hdd\);'             => '<span class="fa fa-hdd"></span>',
		'&amp;\(header\);'          => '<span class="fa fa-header"></span>',
		'&amp;\(heart\);'           => '<span class="fa fa-heart"></span>',
		'&amp;\(heart-empty\);'     => '<span class="fa fa-heart-empty"></span>',
		'&amp;\(home\);'            => '<span class="fa fa-home"></span>',
		'&amp;\(hospital\);'        => '<span class="fa fa-hospital"></span>',
		'&amp;\(h-sign\);'          => '<span class="fa fa-h-sign"></span>',
		'&amp;\(html5\);'           => '<span class="fa fa-html5"></span>',
		'&amp;\(inbox\);'           => '<span class="fa fa-inbox"></span>',
		'&amp;\(indent-left\);'     => '<span class="fa fa-indent-left"></span>',
		'&amp;\(indent-right\);'    => '<span class="fa fa-indent-right"></span>',
		'&amp;\(info\);'            => '<span class="fa fa-info"></span>',
		'&amp;\(info-sign\);'       => '<span class="fa fa-info-sign"></span>',
		'&amp;\(inr\);'             => '<span class="fa fa-inr"></span>',
		'&amp;\(instagram\);'       => '<span class="fa fa-instagram"></span>',
		'&amp;\(italic\);'          => '<span class="fa fa-italic"></span>',
		'&amp;\(jpy\);'             => '<span class="fa fa-jpy"></span>',
		'&amp;\(key\);'             => '<span class="fa fa-key"></span>',
		'&amp;\(krw\);'             => '<span class="fa fa-krw"></span>',
		'&amp;\(keyboard\);'        => '<span class="fa fa-keyboard"></span>',
		'&amp;\(laptop\);'          => '<span class="fa fa-laptop"></span>',
		'&amp;\(leaf\);'            => '<span class="fa fa-leaf"></span>',
		'&amp;\(legal\);'           => '<span class="fa fa-legal"></span>',
		'&amp;\(lemon\);'           => '<span class="fa fa-lemon"></span>',
		'&amp;\(level-down\);'      => '<span class="fa fa-level-down"></span>',
		'&amp;\(level-up\);'        => '<span class="fa fa-level-up"></span>',
		'&amp;\(lightbulb\);'       => '<span class="fa fa-lightbulb"></span>',
		'&amp;\(link\);'            => '<span class="fa fa-link"></span>',
		'&amp;\(linkedin\);'        => '<span class="fa fa-linkedin"></span>',
		'&amp;\(linkedin-sign\);'   => '<span class="fa fa-linkedin-sign"></span>',
		'&amp;\(linux\);'           => '<span class="fa fa-linux"></span>',
		'&amp;\(list\);'            => '<span class="fa fa-list"></span>',
		'&amp;\(list-alt\);'        => '<span class="fa fa-list-alt"></span>',
		'&amp;\(list-ol\);'         => '<span class="fa fa-list-ol"></span>',
		'&amp;\(list-ul\);'         => '<span class="fa fa-list-ul"></span>',
		'&amp;\(location-arrow\);'  => '<span class="fa fa-location-arrow"></span>',
		'&amp;\(lock\);'            => '<span class="fa fa-lock"></span>',
		'&amp;\(long-arrow-down\);' => '<span class="fa fa-long-arrow-down"></span>',
		'&amp;\(long-arrow-left\);' => '<span class="fa fa-long-arrow-left"></span>',
		'&amp;\(long-arrow-right\);'=> '<span class="fa fa-long-arrow-right"></span>',
		'&amp;\(long-arrow-up\);'   => '<span class="fa fa-long-arrow-up"></span>',
		'&amp;\(magic\);'           => '<span class="fa fa-magic"></span>',
		'&amp;\(magnet\);'          => '<span class="fa fa-magnet"></span>',
		'&amp;\(mail-forward\);'    => '<span class="fa fa-mail-forward"></span>',
		'&amp;\(mail-reply\);'      => '<span class="fa fa-mail-reply"></span>',
		'&amp;\(mail-reply-all\);'  => '<span class="fa fa-mail-reply-all"></span>',
		'&amp;\(male\);'            => '<span class="fa fa-male"></span>',
		'&amp;\(map-marker\);'      => '<span class="fa fa-map-marker"></span>',
		'&amp;\(maxcdn\);'          => '<span class="fa fa-maxcdn"></span>',
		'&amp;\(medkit\);'          => '<span class="fa fa-medkit"></span>',
		'&amp;\(meh\);'             => '<span class="fa fa-mmehinus"></span>',
		'&amp;\(microphone\);'      => '<span class="fa fa-microphone"></span>',
		'&amp;\(microphone-off\);'  => '<span class="fa fa-microphone-off"></span>',
		'&amp;\(minus\);'           => '<span class="fa fa-minus"></span>',
		'&amp;\(minus-sign\);'      => '<span class="fa fa-minus-sign"></span>',
		'&amp;\(minus-sign-alt\);'  => '<span class="fa fa-minus-sign-alt"></span>',
		'&amp;\(mobile-phone\);'    => '<span class="fa fa-mobile-phone"></span>',
		'&amp;\(money\);'           => '<span class="fa fa-money"></span>',
		'&amp;\(moon\);'            => '<span class="fa fa-moon"></span>',
		'&amp;\(move\);'            => '<span class="fa fa-move"></span>',
		'&amp;\(music\);'           => '<span class="fa fa-music"></span>',
		'&amp;\(new-window\);'      => '<span class="fa fa-new-window"></span>',
		'&amp;\(off\);'             => '<span class="fa fa-off"></span>',
		'&amp;\(ok\);'              => '<span class="fa fa-ok"></span>',
		'&amp;\(ok-circle\);'       => '<span class="fa fa-ok-circle"></span>',
		'&amp;\(ok-sign\);'         => '<span class="fa fa-ok-sign"></span>',
		'&amp;\(pagelines\);'       => '<span class="fa fa-pagelines"></span>',
		'&amp;\(paperclip\);'       => '<span class="fa fa-paperclip"></span>',
		'&amp;\(paste\);'           => '<span class="fa fa-paste"></span>',
		'&amp;\(pause\);'           => '<span class="fa fa-pause"></span>',
		'&amp;\(pencil\);'          => '<span class="fa fa-pencil"></span>',
		'&amp;\(phone\);'           => '<span class="fa fa-phone"></span>',
		'&amp;\(phone-sign\);'      => '<span class="fa fa-phone-alt"></span>',
		'&amp;\(picture\);'         => '<span class="fa fa-picture"></span>',
		'&amp;\(pinterest\);'       => '<span class="fa fa-pinterest"></span>',
		'&amp;\(pinterest-sign\);'  => '<span class="fa fa-pinterest-sign"></span>',
		'&amp;\(plane\);'           => '<span class="fa fa-plane"></span>',
		'&amp;\(play\);'            => '<span class="fa fa-play"></span>',
		'&amp;\(play-circle\);'     => '<span class="fa fa-play-circle"></span>',
		'&amp;\(play-sign\);'       => '<span class="fa fa-play-sign"></span>',
		'&amp;\(plus\);'            => '<span class="fa fa-plus"></span>',
		'&amp;\(plus-sign\);'       => '<span class="fa fa-plus-sign"></span>',
		'&amp;\(plus-sign-alt\);'   => '<span class="fa fa-plus-sign-alt"></span>',
		'&amp;\(plus-square-o\);'   => '<span class="fa fa-plus-square-o"></span>',
		'&amp;\(power-off\);'       => '<span class="fa fa-power-off"></span>',
		'&amp;\(print\);'           => '<span class="fa fa-print"></span>',
		'&amp;\(pushpin\);'         => '<span class="fa fa-pushpin"></span>',
		'&amp;\(puzzle-piece\);'    => '<span class="fa fa-puzzle-piece"></span>',
		'&amp;\(qrcode\);'          => '<span class="fa fa-qrcode"></span>',
		'&amp;\(question\);'        => '<span class="fa fa-question"></span>',
		'&amp;\(question-sign\);'   => '<span class="fa fa-question-sign"></span>',
		'&amp;\(quote-left\);'      => '<span class="fa fa-quote-left"></span>',
		'&amp;\(quote-right\);'     => '<span class="fa fa-quote-right"></span>',
		'&amp;\(random\);'          => '<span class="fa fa-random"></span>',
		'&amp;\(refresh\);'         => '<span class="fa fa-refresh"></span>',
		'&amp;\(registration-mark\);'   => '<span class="fa fa-registration-mark"></span>',
		'&amp;\(remove\);'          => '<span class="fa fa-remove"></span>',
		'&amp;\(remove-circle\);'   => '<span class="fa fa-remove-circle"></span>',
		'&amp;\(remove-sign\);'     => '<span class="fa fa-remove-sign"></span>',
		'&amp;\(renminbi\);'        => '<span class="fa fa-renminbi"></span>',
		'&amp;\(renren\);'          => '<span class="fa fa-renren"></span>',
		'&amp;\(reorder\);'         => '<span class="fa fa-reorder"></span>',
		'&amp;\(repeat\);'          => '<span class="fa fa-repeat"></span>',
		'&amp;\(reply\);'           => '<span class="fa fa-reply"></span>',
		'&amp;\(reply-all\);'       => '<span class="fa fa-reply-all"></span>',
		'&amp;\(resize-full\);'     => '<span class="fa fa-resize-full"></span>',
		'&amp;\(resize-horizontal\);'   => '<span class="fa fa-resize-horizontal"></span>',
		'&amp;\(resize-small\);'    => '<span class="fa fa-resize-small"></span>',
		'&amp;\(resize-vertical\);' => '<span class="fa fa-resize-vertical"></span>',
		'&amp;\(retweet\);'         => '<span class="fa fa-retweet"></span>',
		'&amp;\(road\);'            => '<span class="fa fa-road"></span>',
		'&amp;\(rocket\);'          => '<span class="fa fa-rocket"></span>',
		'&amp;\(rotate-left\);'     => '<span class="fa fa-rotate-left"></span>',
		'&amp;\(rotate-right\);'    => '<span class="fa fa-rotate-right"></span>',
		'&amp;\(rss\);'             => '<span class="fa fa-rss"></span>',
		'&amp;\(rss-sign\);'        => '<span class="fa fa-rss-sign"></span>',
		'&amp;\(rub\);'             => '<span class="fa fa-rub"></span>',
		'&amp;\(ruble\);'           => '<span class="fa fa-ruble"></span>',
		'&amp;\(rouble\);'          => '<span class="fa fa-rouble"></span>',
		'&amp;\(rupee\);'           => '<span class="fa fa-rupee"></span>',
		'&amp;\(save\);'            => '<span class="fa fa-save"></span>',
		'&amp;\(screenshot\);'      => '<span class="fa fa-screenshot"></span>',
		'&amp;\(search\);'          => '<span class="fa fa-search"></span>',
		'&amp;\(share\);'           => '<span class="fa fa-share"></span>',
		'&amp;\(share-alt\);'       => '<span class="fa fa-share-alt"></span>',
		'&amp;\(share-sign\);'      => '<span class="fa fa-share-sign"></span>',
		'&amp;\(shield\);'          => '<span class="fa fa-shield"></span>',
		'&amp;\(shopping-cart\);'   => '<span class="fa fa-shopping-cart"></span>',
		'&amp;\(signal\);'          => '<span class="fa fa-signal"></span>',
		'&amp;\(signin\);'          => '<span class="fa fa-signin"></span>',
		'&amp;\(signout\);'         => '<span class="fa fa-signout"></span>',
		'&amp;\(sitemap\);'         => '<span class="fa fa-sitemap"></span>',
		'&amp;\(skype\);'           => '<span class="fa fa-skype"></span>',
		'&amp;\(smile\);'           => '<span class="fa fa-smile"></span>',
		'&amp;\(sort\);'            => '<span class="fa fa-sort"></span>',
		'&amp;\(sort-by-alphabet\);'=> '<span class="fa fa-sort-by-alphabet"></span>',
		'&amp;\(sort-by-alphabet-alt\);'    => '<span class="fa fa-sort-by-alphabet-alt"></span>',
		'&amp;\(sort-by-attributes\);'  => '<span class="fa fa-sort-by-attributes"></span>',
		'&amp;\(sort-by-attributes-alt\);'  => '<span class="fa fa-sort-by-attributes-alt"></span>',
		'&amp;\(sort-by-order\);'   => '<span class="fa fa-sort-by-order"></span>',
		'&amp;\(sort-by-order-alt\);'   => '<span class="fa fa-sort-by-order-alt"></span>',
		'&amp;\(sort-down\);'       => '<span class="fa fa-sort-down"></span>',
		'&amp;\(sort-up\);'         => '<span class="fa fa-sort-up"></span>',
		'&amp;\(spinner\);'         => '<span class="fa fa-spinner"></span>',
		'&amp;\(stack-exchange\);'  => '<span class="fa fa-stack-exchange"></span>',
		'&amp;\(stackexchange\);'   => '<span class="fa fa-stackexchange"></span>',
		'&amp;\(star\);'            => '<span class="fa fa-star"></span>',
		'&amp;\(star-empty\);'      => '<span class="fa fa-star-empty"></span>',
		'&amp;\(star-half\);'       => '<span class="fa fa-star-half"></span>',
		'&amp;\(star-half-empty\);' => '<span class="fa fa-star-half-empty"></span>',
		'&amp;\(star-half-full\);'  => '<span class="fa fa-star-half-full"></span>',
		'&amp;\(step-backward\);'   => '<span class="fa fa-step-backward"></span>',
		'&amp;\(step-forward\);'    => '<span class="fa fa-step-forward"></span>',
		'&amp;\(stethoscope\);'     => '<span class="fa fa-stethoscope"></span>',
		'&amp;\(stop\);'            => '<span class="fa fa-stop"></span>',
		'&amp;\(strikethrough\);'   => '<span class="fa fa-strikethrough"></span>',
		'&amp;\(subscript\);'       => '<span class="fa fa-subscript"></span>',
		'&amp;\(suitcase\);'        => '<span class="fa fa-suitcase"></span>',
		'&amp;\(sun\);'             => '<span class="fa fa-sun"></span>',
		'&amp;\(superscript\);'     => '<span class="fa fa-superscript"></span>',
		'&amp;\(table\);'           => '<span class="fa fa-table"></span>',
		'&amp;\(tablet\);'          => '<span class="fa fa-tablet"></span>',
		'&amp;\(tag\);'             => '<span class="fa fa-tag"></span>',
		'&amp;\(tags\);'            => '<span class="fa fa-tags"></span>',
		'&amp;\(tasks\);'           => '<span class="fa fa-tasks"></span>',
		'&amp;\(terminal\);'        => '<span class="fa fa-terminal"></span>',
		'&amp;\(text-height\);'     => '<span class="fa fa-text-height"></span>',
		'&amp;\(text-width\);'      => '<span class="fa fa-text-width"></span>',
		'&amp;\(th\);'              => '<span class="fa fa-th"></span>',
		'&amp;\(th-large\);'        => '<span class="fa fa-th-large"></span>',
		'&amp;\(th-list\);'         => '<span class="fa fa-th-list"></span>',
		'&amp;\(thumbs-down\);'     => '<span class="fa fa-thumbs-down"></span>',
		'&amp;\(thumbs-up\);'       => '<span class="fa fa-thumbs-up"></span>',
		'&amp;\(thumbs-down-alt\);' => '<span class="fa fa-thumbs-down-alt"></span>',
		'&amp;\(thumbs-up-alt\);'   => '<span class="fa fa-thumbs-up-alt"></span>',
		'&amp;\(ticket\);'          => '<span class="fa fa-ticket"></span>',
		'&amp;\(time\);'            => '<span class="fa fa-time"></span>',
		'&amp;\(tint\);'            => '<span class="fa fa-tint"></span>',
		'&amp;\(toggle-left\);'     => '<span class="fa fa-toggle-left"></span>',
		'&amp;\(trash\);'           => '<span class="fa fa-trash"></span>',
		'&amp;\(trello\);'          => '<span class="fa fa-trello"></span>',
		'&amp;\(trophy\);'          => '<span class="fa fa-trophy"></span>',
		'&amp;\(truck\);'           => '<span class="fa fa-truck"></span>',
		'&amp;\(try\);'             => '<span class="fa fa-try"></span>',
		'&amp;\(tumblr\);'          => '<span class="fa fa-tumblr"></span>',
		'&amp;\(tumblr-sign\);'     => '<span class="fa fa-tumblr-sign"></span>',
		'&amp;\(turkish-lira\);'    => '<span class="fa fa-turkish-lira"></span>',
		'&amp;\(twitter\);'         => '<span class="fa fa-twitter"></span>',
		'&amp;\(twitter-sign\);'    => '<span class="fa fa-twitter-sign"></span>',
		'&amp;\(umbrella\);'        => '<span class="fa fa-umbrella"></span>',
		'&amp;\(unchecked\);'       => '<span class="fa fa-unchecked"></span>',
		'&amp;\(underline\);'       => '<span class="fa fa-underline"></span>',
		'&amp;\(undo\);'            => '<span class="fa fa-undo"></span>',
		'&amp;\(unlink\);'          => '<span class="fa fa-unlink"></span>',
		'&amp;\(unlock\);'          => '<span class="fa fa-unlock"></span>',
		'&amp;\(unlock-alt\);'      => '<span class="fa fa-unlock-alt"></span>',
		'&amp;\(upload\);'          => '<span class="fa fa-upload"></span>',
		'&amp;\(usd\);'             => '<span class="fa fa-usd"></span>',
		'&amp;\(user\);'            => '<span class="fa fa-user"></span>',
		'&amp;\(vk\);'              => '<span class="fa fa-vk"></span>',
		'&amp;\(vimeo-square\);'    => '<span class="fa fa-vimeo-square"></span>',
		'&amp;\(volume-down\);'     => '<span class="fa fa-volume-down"></span>',
		'&amp;\(volume-off\);'      => '<span class="fa fa-volume-off"></span>',
		'&amp;\(volume-up\);'       => '<span class="fa fa-volume-up"></span>',
		'&amp;\(warning-sign\);'    => '<span class="fa fa-warning-sign"></span>',
		'&amp;\(weibo\);'           => '<span class="fa fa-weibo"></span>',
		'&amp;\(windows\);'         => '<span class="fa fa-windows"></span>',
		'&amp;\(wheelchair\);'      => '<span class="fa fa-wheelchair"></span>',
		'&amp;\(won\);'             => '<span class="fa fa-won"></span>',
		'&amp;\(wrench\);'          => '<span class="fa fa-wrench"></span>',
		'&amp;\(xing\);'            => '<span class="fa fa-xing"></span>',
		'&amp;\(xing-sign\);'       => '<span class="fa fa-xing-sign"></span>',
		'&amp;\(yen\);'             => '<span class="fa fa-yen"></span>',
		'&amp;\(youtube\);'         => '<span class="fa fa-youtube"></span>',
		'&amp;\(youtube-play\);'    => '<span class="fa fa-youtube-play"></span>',
		'&amp;\(youtube-sign\);'    => '<span class="fa fa-youtube-sign"></span>',
		'&amp;\(zoom-in\);'         => '<span class="fa fa-zoom-in"></span>',
		'&amp;\(zoom-out\);'        => '<span class="fa fa-zoom-out"></span>'
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
			// Font Awasome
			$_line_rules += self::$fa_rules;
			// 絵文字
			$_line_rules += self::$emoji_rules;
		}
		return $_line_rules;
	}
}