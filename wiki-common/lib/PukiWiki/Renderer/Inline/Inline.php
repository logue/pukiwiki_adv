<?php
/**
 * インライン要素クラス
 *
 * @package   PukiWiki\Renderer\Inline
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2013 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Inline.php,v 1.0.0 2013/01/29 19:54:00 Logue Exp $
 */

namespace PukiWiki\Renderer\Inline;

use PukiWiki\Renderer\InlineConverter;
use PukiWiki\Renderer\Inline\AutoAlias;
use PukiWiki\Renderer\Inline\Glossary;
use PukiWiki\Auth\Auth;
use PukiWiki\Router;
use PukiWiki\Utility;
use PukiWiki\Factory;

/**
 * インライン要素パースクラス
 */
abstract class Inline
{
	// 内部リンク
	const INTERNAL_LINK_ICON = '<span class="pkwk-symbol link_symbol symbol-internal" title="Internal Link"></span>';
	// 外部リンク
	const EXTERNAL_LINK_ICON = '<span class="pkwk-symbol link_symbol symbol-external" title="External Link"></span>';
	// 見つからないページのリンク
	const NOEXISTS_STRING = '<span class="noexists">%s</span>';
	// imgタグに展開する拡張子のパターン
	const IMAGE_EXTENTION_PATTERN = '/\.(gif|png|bmp|jpe?g|svg?z|webp)$/i';
	// audioタグで展開する拡張子のパターン
	const AUDIO_EXTENTION_PATTERN = '/\.(mp3|ogg|m4a)$/i';
	// videoタグで展開する拡張子のパターン
	const VIDEO_EXTENTION_PATTERN = '/\.(mp4|webm)$/i';

	var $start;   // Origin number of parentheses (0 origin)
	var $text;    // Matched string

	var $type;
	var $page;
	var $name;
	var $body;
	var $alias;

	var $redirect;

	/**
	 * コンストラクタ
	 * @param string $start
	 */
	public function __construct($start)
	{
		$this->start = $start;
	}
	/**
	 * Wikiのパース用正規表現を取得
	 * @return string
	 */
	public function getPattern() {}

	/**
	 * 正規表現の(?: ...)などで帰ってくる値
	 * @return int
	 */
	public function getCount() {}

	/**
	 * マッチするパターンを設定
	 * @param type $arr
	 * @param type $page
	 */
	public function setPattern($arr, $page) {}

	/**
	 * 文字列化（インライン要素として帰ってくる
	 */
	public function __toString() {}

	// Private: Get needed parts from a matched array()
	public function splice($arr)
	{
		$count = $this->getCount() + 1;
		$arr   = array_pad(array_splice($arr, $this->start, $count), $count, '');
		$this->text = $arr[0];
		return $arr;
	}

	// Set basic parameters
	public function setParam($page, $name, $body, $type = '', $alias = '')
	{
		static $converter;

		$this->page = $page;
		$this->name = $name;
		$this->body = $body;
		$this->type = $type;
		if (! empty($alias) ) {
			if ($converter === NULL)
				$converter = new InlineConverter(array('InlinePlugin'));

			$alias = $converter->convert($alias, $page);

			$alias = self::setLineRules($alias);

			// BugTrack/669: A hack removing anchor tags added by AutoLink
			$alias = Utility::htmlsc(preg_replace('#</?a[^>]*>#i', '', $alias));
		}
		$this->alias = $alias;

		return TRUE;
	}

	// User-defined rules (convert without replacing source)
	public static function setLineRules($str){
		global $line_rules;
		static $pattern, $replace;

		if (! isset($pattern)) {
			$pattern = array_map(create_function('$a',
				'return \'/\' . $a . \'/\';'), array_keys($line_rules));
			$replace = array_values($line_rules);
			unset($line_rules);
		}

		return preg_replace($pattern, $replace, $str);
	}

	/**
	 * ページの自動リンクを作成
	 * @global type $vars
	 * @global type $link_compact
	 * @global type $related
	 * @global type $_symbol_noexists
	 * @param string $page ページ名
	 * @param string $alias リンクの名前
	 * @param string $anchor ページ内アンカー（アドレスの#以降のテキスト）
	 * @param string $refer リンク元
	 * @param boolean $isautolink 自動リンクか？
	 * @return string
	 */
	public static function setAutoLink($page, $alias = '', $anchor = '', $refer = '', $isautolink = FALSE)
	{
		global $vars, $link_compact, $related, $_symbol_noexists, $related;

		if (empty($page)){
			// ページ内リンク
			return '<a href="' . $anchor . '">' . Utility::htmlsc($alias) . '</a>';
		}

		$page = Utility::stripBracket($page);

		$wiki = Factory::Wiki($page);
		if (! $wiki->has()) {
			// ページが存在しない場合は、AutoAliasから該当ページが存在するかを確認する
			foreach (AutoAlias::getAutoAliasDict() as $aliaspage=>$realpage) {
				if (Factory::Wiki($realpage)->has()) {
					// リンクをエイリアス先に
					return self::setLink($page, $aliaspage);
				}
			}
		}else if (! isset($related[$page]) && $page !== $vars['page']) {
			$related[$page] = $wiki->time();
		}

		$s_page = Utility::htmlsc($page);
		$s_alias = Utility::htmlsc(empty($alias) ? $page : $alias);

		if ($isautolink || $wiki->has()) {
			// ページが存在する場合
			// 用語集にページ名と同じワードが含まれていた場合
			$glossary = Glossary::getGlossary($page);
			if (!empty($glossary)){
				// AutoGlossray
				$s_page = $glossary;
			}
			return '<a href="' . $wiki->uri() . $anchor . '" ' .
				($link_compact === 0 ? 'title="' . $s_page . ' ' . $wiki->passage(false,true) . '"' : '' ).
	//			($isautolink ? ' class="autolink"' : '') .
				(!empty($glossary) ? 'aria-describedby="tooltip"' : '') .
				'>' . $s_alias . '</a>';
		} else {
			// Dangling link
			if (Auth::check_role('readonly')) return $s_alias; // No dacorations

			$retval = $s_alias . '<a href="' . $wiki->uri('edit', (empty($refer) ? null : array('refer'=>$refer)) ) . '" rel="nofollow">' .$_symbol_noexists . '</a>';

			return ($link_compact) ? $retval : sprintf(self::NOEXISTS_STRING, $retval);
		}
	}
	/**
	 * リンクを作成（厳密にはimgタグ、audioタグ、videoタグにも使用するが）
	 * @param string $term リンクの名前
	 * @param string $uri リンク先
	 * @param string $tooltip title属性の内容
	 * @param string $rel リンクのタイプ
	 * @return string
	 */
	public static function setLink($term, $uri, $tooltip='', $rel = '', $is_redirect = PKWK_USE_REDIRECT){
		$_uri = Utility::htmlsc($uri);
		$href = $is_redirect ? Router::get_cmd_uri('redirect',null,null,array('u'=>$uri)) : $_uri;

		$_term = Utility::htmlsc($term);
		$_tooltip = !empty($tooltip) ? ' title="' . Utility::htmlsc($tooltip) . '"' : '';
		$ext_rel = (!empty($rel) ? $rel.' ' : '') . 'external' . $is_redirect ? ' nofollow' : '';

		// メディアファイル
		if (! PKWK_DISABLE_INLINE_IMAGE_FROM_URI && Utility::isUri($uri)) {
			if (preg_match(self::IMAGE_EXTENTION_PATTERN, $uri)) {
				// 画像の場合
				$term = '<img src="' . $_uri . '" alt="' . $_term . '" '.$_tooltip.' />';
			}else{
				// 音声／動画の場合
				$anchor = '<a href="' . $href . '" rel="' . (self::isInsideUri($uri) ? $rel : $ext_rel) . '"'.$_tooltip.'>' . $_term  .'</a>';
				// 末尾のアイコン
				$icon = self::isInsideUri($uri) ?
					'<a href="' . $href . '" rel="' . $rel . '">' . self::INTERNAL_LINK_ICON .'</a>' :
					'<a href="' . $href . '" rel="' . $ext_rel . '">' . self::EXTERNAL_LINK_ICON . '</a>';
				if (preg_match(self::VIDEO_EXTENTION_PATTERN, $uri)) {
					return '<video src="' . $_uri . '" alt="'.$_term.'" controls="controls"'. $_tooltip . '>' . $anchor . '</video>' . $icon;
				}else if (preg_match(self::AUDIO_EXTENTION_PATTERN, $uri)) {
					return '<audio src="' . $_uri . '" alt="'.$_term.'" controls="controls"'. $_tooltip . '>' . $anchor . '</audio>' . $icon;
				}
			}
			
		}
		// リンクを出力
		return self::isInsideUri($uri) ?
			'<a href="' . $href . '" rel="' . $rel . '"' . $_tooltip . '>' . $term . self::INTERNAL_LINK_ICON .'</a>' :
			'<a href="' . $href . '" rel="' . $ext_rel . '"' . $_tooltip . '>' . $term . self::EXTERNAL_LINK_ICON . '</a>';
	}
	/**
	 * 外部リンクか内部リンクの判定
	 * @global type $open_uri_in_new_window_servername
	 * @staticvar boolean $set_baseuri
	 * @param type $uri アドレス
	 * @return boolean
	 */
	protected static function isInsideUri($uri){
		global $open_uri_in_new_window_servername;
		static $set_baseuri;

		if ($set_baseuri) {
			$set_baseuri = false;
			$open_uri_in_new_window_servername[] = Router::get_baseuri();
		}

		foreach ($open_uri_in_new_window_servername as $servername) {
			if (stristr($uri, $servername)) {
				return true;
			}
		}
		return false;
	}
}

/* End of file Inline.php */
/* Location: /vender/PukiWiki/Lib/Renderer/Inline/Inline.php */