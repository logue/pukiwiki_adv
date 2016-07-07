<?php
/**
 * レンダラー定義クラス
 *
 * @package   PukiWiki\Renderer
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2016 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: RendererDefines.php,v 1.0.1 2016/07/07 17:27:00 Logue Exp $
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
	const INTERNAL_LINK_ICON = '<i class="fa fa-external-link-square" title="Internal Link" aria-hidden="true"></i>';
	/**
	 * 外部リンクのアイコン
	 */
	const EXTERNAL_LINK_ICON = '<i class="fa fa-external-link" title="External Link" aria-hidden="true"></i>';
	/**
	 * メールリンクのアイコン
	 */
	const MAILTO_ICON = '<i class="fa fa-envelope" title="mailto:" aria-hidden="true"></i>';
	/**
	 * 電話番号リンクのアイコン
	 */
	const TELEPHONE_ICON = '<i class="fa fa-phone" title="tel:" aria-hidden="true"></i>';
	/**
	 * InterWikiNameのアイコン
	 */
	const INTERWIKINAME_ICON = '<i class="fa fa-globe" title="InterWikiName" aria-hidden="true"></i>';
	/**
	 * 部分編集リンクのアイコン
	 */
	const PARTIAL_EDIT_LINK_ICON = '<i class="fa fa-pencil" title="Edit here" aria-hidden="true"></i>';
	/**
	 * 見つからないページのリンク
	 */
	const NOEXISTS_STRING = '<span class="noexists">%s</span>';
	/**
	 * imgタグに展開する拡張子のパターン
	 */
	const IMAGE_EXTENTION_PATTERN = '/\.(gif|png|bmp|jpe?g|svg?z|webp|ico)$/i';
	/**
	 * audioタグで展開する拡張子のパターン
	 */
	const AUDIO_EXTENTION_PATTERN = '/\.(mp3|ogg|m4a)$/i';
	/**
	 * videoタグで展開する拡張子のパターン
	 */
	const VIDEO_EXTENTION_PATTERN = '/\.(mp4|webm)$/i';
	/**
	 * ノートのアイコン
	 */
	const FOOTNOTE_ANCHOR_ICON = '<i class="fa fa-thumb-tack" aria-hidden="true"></i>';
}

/* End of file RendererDefines.php */
/* Location: ./vendor/PukiWiki/Lib/Renderer/RendererDefines.php */