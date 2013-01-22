<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: Inline.php,v 1.0.0 2013/01/05 15:46:00 Logue Exp $
// Copyright (C)
//   2012-2013 PukiWiki Advance Developers Team
// License: GPL v2 or (at your option) any later version
//
// Hyperlink-related functions
namespace PukiWiki\Lib\Renderer\Inline;
use PukiWiki\Lib\Renderer\InlineConverter;
use PukiWiki\Lib\File\FileFactory;
use PukiWiki\Lib\Auth\Auth;
use PukiWiki\Lib\Router;

abstract class Inline
{
	const INTERNAL_LINK_ICON = '<span class="pkwk-symbol link_symbol symbol-internal" title="Internal Link"></span>';
	const EXTERNAL_LINK_ICON = '<span class="pkwk-symbol link_symbol symbol-external" title="External Link"></span>';

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
		$this->redirect = (PKWK_USE_REDIRECT) ? get_cmd_uri('redirect',null,null,'u=') : null;	// FIXME
	}

	/**
	 * Wikiのパース用正規表現を取得
	 */
	public function get_pattern() {}

	/**
	 * 正規表現の(?: ...)などで帰ってくる値
	 */
	public function get_count() {}

	// Set pattern that matches
	public function set($arr, $page) {}

	/**
	 * 文字列化（インライン要素として帰ってくる
	 */
	public function toString() {}

	// Private: Get needed parts from a matched array()
	public function splice($arr)
	{
		$count = $this->get_count() + 1;
		$arr   = array_pad(array_splice($arr, $this->start, $count), $count, '');
		$this->text = $arr[0];
		return $arr;
	}

	// Set basic parameters
	public function setParam($page, $name, $body, $type = '', $alias = '')
	{
		static $converter = NULL;

		$this->page = $page;
		$this->name = $name;
		$this->body = $body;
		$this->type = $type;
		if (! PKWK_DISABLE_INLINE_IMAGE_FROM_URI &&
			is_url($alias) && preg_match('/\.(gif|png|bmp|jpe?g|svg?z|webp)$/i', $alias)) {
			$alias = '<img src="' . htmlsc($alias) . '" alt="' . $name . '" />';
		} else if (! empty($alias) ) {
			if ($converter === NULL)
				$converter = new InlineConverter(array('Plugin'));

			$alias = $converter->convert($alias, $page);
			$alias = self::make_line_rules($alias);

			// BugTrack/669: A hack removing anchor tags added by AutoLink
			$alias = preg_replace('#</?a[^>]*>#i', '', $alias);
		}
		$this->alias = $alias;

		return TRUE;
	}

	// User-defined rules (convert without replacing source)
	public static function make_line_rules($str){
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

	// Make hyperlink for the page
	public static function make_pagelink($page, $alias = '', $anchor = '', $refer = '', $isautolink = FALSE)
	{
		global $vars, $link_compact, $related, $_symbol_noexists;

		if (empty($page)){
			// ページ内リンク
			return '<a href="' . $anchor . '">' . htmlsc($alias) . '</a>';
		}

		$page = strip_bracket($page);
		$wiki = FileFactory::Wiki($page);
		if (! $wiki->has()) {
			$realpages = get_autoaliases(strip_bracket($page));
			foreach ($realpages as $realpage) {
				if (FileFactory::Wiki($realpage)->has()) {
					$page = $realpage;
					break;
				}
			}
		}else if (! isset($related[$page]) && $page !== $vars['page']) {
			$related[$page] = $wiki->getTime();
		}

		$s_page = htmlsc($page);
		$s_alias = htmlsc(empty($alias) ? $page : $alias);

		if ($isautolink || $wiki->has()) {
			// ページが存在する場合
			$glossary = Glossary::get_glossary_dict($page, true);
			if (!empty($glossary)){
				// AutoGlossray
				$s_page = $glossary;
			}
			return '<a href="' . $wiki->getUri() . $anchor . '" ' .
				($link_compact === 0 ? 'title="' . $s_page . ' ' . $wiki->passage(false,true) . '"' : '' ).
	//			($isautolink ? ' class="autolink"' : '') .
				(!empty($glossary) ? 'aria-describedby="tooltip"' : '') .
				'>' . $s_alias . '</a>';
		} else {
			// Dangling link
			if (Auth::check_role('readonly')) return $s_alias; // No dacorations

			$retval = $s_alias . '<a href="' .
				get_cmd_uri('edit', $page, null, (empty($refer) ? null : array('refer'=>$refer)) ) . '" rel="nofollow">' .
				$_symbol_noexists . '</a>';

			return ($link_compact) ? $retval : '<span class="noexists">' . $retval . '</span>';
		}
	}
	// 外部リンク
	public static function make_link($term, $uri, $tooltip, $rel = ''){
		return self::is_inside_uri($uri) ?
			'<a href="' . $uri . '" title="'.htmlsc($tooltip).'" rel="' . $rel . '">'.htmlsc($term) . self::INTERNAL_LINK_ICON .'</a>' :
			'<a href="' . $uri . '" title="'.htmlsc($tooltip).'" rel="' . (!empty($rel) ? $rel.' ' : '') . 'external">'.htmlsc($term) . self::EXTERNAL_LINK_ICON . '</a>';
	}
	// 外部リンクか内部リンクの判定
	protected static function is_inside_uri($uri){
		global $open_uri_in_new_window_servername;
		static $set_baseuri = true;

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