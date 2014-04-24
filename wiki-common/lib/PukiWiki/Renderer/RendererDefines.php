<?php
/**
 * レンダラー定義クラス
 *
 * @package   PukiWiki\Renderer
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2014 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: RendererDefines.php,v 1.0.0 2014/01/30 14:43:00 Logue Exp $
 */
namespace PukiWiki\Renderer;

/**
 * レンダラ―定義クラス
 */
class RendererDefines{
	/**
	 * InterWikiNameのマッチパターン
	 */
	const INTERWIKINAME_PATTERN = '(\[\[)?((?:(?!\s|:|\]\]).)+):(.+)(?(1)\]\])';
	/**
	 * WikiNameのマッチパターン
	 */
	//const WIKINAME_PATTERN ='(?:[A-Z][a-z]+){2,}(?!\w)';
	// \c3\9f through \c3\bf correspond to \df through \ff in ISO8859-1
	const WIKINAME_PATTERN = '(?:[A-Z](?:[a-z]|\\xc3[\\x9f-\\xbf])+(?:[A-Z](?:[a-z]|\\xc3[\\x9f-\\xbf])+)+)(?!\w)';
	/**
	 * BracketNameのマッチパターン
	 */
	const BRACKETNAME_PATTERN = '(?!\s):?[^\r\n\t\f\[\]<>#&":]+:?(?<!\s)';
	/**
	 * 注釈のパターン
	 */
	const NOTE_PATTERN = '\(\(((?:(?>(?:(?!\(\()(?!\)\)(?:[^\)]|$)).)+)|(?R))*)\)\)';
	/**
	 * 内部リンクのアイコン
	 */
	const INTERNAL_LINK_ICON = '<span class="fa fa-external-link-square" title="Internal Link"></span>';
	/**
	 * 外部リンクのアイコン
	 */
	const EXTERNAL_LINK_ICON = '<span class="fa fa-external-link" title="External Link"></span>';
	/**
	 * メールリンクのアイコン
	 */
	const MAILTO_ICON = '<span class="fa fa-envelope" title="mailto:"></span>';
	/**
	 * 電話番号リンクのアイコン
	 */
	const TELEPHONE_ICON = '<span class="fa fa-phone" title="tel:"></span>';
	/**
	 * InterWikiNameのアイコン
	 */
	const INTERWIKINAME_ICON = '<span class="fa fa-globe" title="InterWikiName"></span>';
	/**
	 * 部分編集リンクのアイコン
	 */
	const PARTIAL_EDIT_LINK_ICON = '<span class="fa fa-pencil" title="Edit here"></span>';
	/**
	 * 見つからないページのリンク
	 */
	const NOEXISTS_STRING = '<span class="noexists">%s</span>';
	/**
	 * imgタグに展開する拡張子のパターン
	 */
	const IMAGE_EXTENTION_PATTERN = '/\.(gif|png|bmp|jpe?g|svg?z|webp)$/i';
	/**
	 * audioタグで展開する拡張子のパターン
	 */
	const AUDIO_EXTENTION_PATTERN = '/\.(mp3|ogg|m4a)$/i';
	/**
	 * videoタグで展開する拡張子のパターン
	 */
	const VIDEO_EXTENTION_PATTERN = '/\.(mp4|webm)$/i';
}

/* End of file RendererDefines.php */
/* Location: ./vendor/PukiWiki/Lib/Renderer/RendererDefines.php */