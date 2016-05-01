<?php
/**
 * インライン要素クラス
 *
 * @package   PukiWiki\Renderer\Inline
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2015 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Inline.php,v 1.0.2 2015/06/09 20:44:00 Logue Exp $
 */

namespace PukiWiki\Renderer\Inline;

use PukiWiki\Renderer\RendererDefines;
use PukiWiki\Renderer\InlineConverter;
use PukiWiki\Renderer\Inline\AutoAlias;
use PukiWiki\Renderer\Inline\Glossary;
use PukiWiki\Auth\Auth;
use PukiWiki\Router;
use PukiWiki\Utility;
use PukiWiki\Factory;
use PukiWiki\Text\Rules;

/**
 * インライン要素パースクラス
 */
abstract class Inline
{
	protected $start;   // Origin number of parentheses (0 origin)
	protected $text;    // Matched string

	public $type;
	protected $page;
	public $name;
	protected $body;
	protected $alias;

	protected $redirect;

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
			if ($converter === NULL){
				$converter = new InlineConverter(array('InlinePlugin'));
			}

			$alias = $converter->convert($alias, $page);

			$alias = self::setLineRules($alias);

			// BugTrack/669: A hack removing anchor tags added by AutoLink
			$alias = preg_replace('#</?a[^>]*>#i', '', $alias);
		}
		$this->alias = $alias;

		return TRUE;
	}

	// User-defined rules (convert without replacing source)
	public static function setLineRules($str){
		static $pattern, $replace;
		$line_rules = Rules::getLineRules();

		if (! isset($pattern)) {
			$pattern = array_map(function($a){
				return '/' . $a . '/';
			}, array_keys($line_rules));
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

		$wiki = Factory::Wiki($page);
		if (! $wiki->has()) {
			// ページが存在しない場合は、AutoAliasから該当ページが存在するかを確認する
			foreach (AutoAlias::getAutoAliasDict() as $aliaspage=>$realpage) {
				if (Factory::Wiki($realpage)->has()) {
					// リンクをエイリアス先に
					return self::setLink($page, $aliaspage);
				}
			}
		}else if (! isset($related[$page]) && isset($vars['page']) && $page !== $vars['page']) {
			$related[$page] = $wiki->time();
		}

		$s_page = Utility::htmlsc($page);
		$anchor_name = empty($alias) ? $page : $alias;
		$anchor_name = str_replace('&amp;#039;', '\'', $anchor_name);	// 'が&#039;になってしまう問題をとりあえず解消

		if ($isautolink || $wiki->has()) {
			// ページが存在する場合
			// 用語集にページ名と同じワードが含まれていた場合
			$glossary = Glossary::getGlossary($page);

			if (!empty($glossary)){
				// AutoGlossray
				return '<abbr class="glossary" title="' . $glossary . ' ' . $wiki->passage(false,true) .'">' .
					'<a href="' . $wiki->uri() . '"' . ($isautolink === true ? ' class="autolink"' : '') . '>' . $anchor_name  . '</a></abbr>';
			}
			return '<a href="' . $wiki->uri() . $anchor . '" ' . 
				(!$link_compact ? 'title="' . $s_page . ' ' . $wiki->passage(false,true) . '"' : '' ).
				($isautolink === true ? ' class="autolink"' : '') . '>' . $anchor_name  . '</a>';
		} else {
			// Dangling link
			if (Auth::check_role('readonly')) return Utility::htmlsc($alias); // No dacorations

			$retval = $anchor_name . '<a href="' . $wiki->uri('edit', (empty($refer) ? null : array('refer'=>$refer)) ) . '" rel="nofollow">' .$_symbol_noexists . '</a>';

			return ($link_compact) ? $retval : sprintf(RendererDefines::NOEXISTS_STRING, $retval);
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
	
		// rel = "*"を生成
		$rels[] = 'external';
		if (!empty($rel)){
			$rels[] = $rel;
		}
		if ($is_redirect){
			$rels[] = 'nofollow';
		}
		$ext_rel = join(" ", $rels);

		// メディアファイル
		if (! PKWK_DISABLE_INLINE_IMAGE_FROM_URI && Utility::isUri($uri)) {
			if (preg_match(RendererDefines::IMAGE_EXTENTION_PATTERN, $uri)) {
				// 画像の場合
				$term = '<img src="' . $_uri . '" alt="' . $_term . '" '.$_tooltip.' />';
			}else{
				// 音声／動画の場合
				$anchor = '<a href="' . $href . '" rel="' . (self::isInsideUri($uri) ? $rel : $ext_rel) . '"'.$_tooltip.'>' . $_term  .'</a>';
				// 末尾のアイコン
				$icon = self::isInsideUri($uri) ?
					'<a href="' . $href . '" rel="' . $rel . '">' . RendererDefines::INTERNAL_LINK_ICON .'</a>' :
					'<a href="' . $href . '" rel="' . $ext_rel . '">' . RendererDefines::EXTERNAL_LINK_ICON . '</a>';
				
				if (preg_match(RendererDefines::VIDEO_EXTENTION_PATTERN, $uri)) {
					return '<video src="' . $_uri . '" alt="'.$_term.'" controls="controls"'. $_tooltip . '>' . $anchor . '</video>' . $icon;
				}else if (preg_match(RendererDefines::AUDIO_EXTENTION_PATTERN, $uri)) {
					return '<audio src="' . $_uri . '" alt="'.$_term.'" controls="controls"'. $_tooltip . '>' . $anchor . '</audio>' . $icon;
				}
			}
			
		}
		// リンクを出力
		return self::isInsideUri($uri) ?
			'<a href="' . $href . '" rel="' . $rel . '"' . $_tooltip . '>' . $term . RendererDefines::INTERNAL_LINK_ICON .'</a>' :
			'<a href="' . $href . '" rel="' . $ext_rel . '"' . $_tooltip . '>' . $term . RendererDefines::EXTERNAL_LINK_ICON . '</a>';
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
/* Location: /vendor/PukiWiki/Lib/Renderer/Inline/Inline.php */