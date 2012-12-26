<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: Inline.php,v 1.0.0 2012/12/18 11:00:00 Logue Exp $
// Copyright (C)
//   2012 PukiWiki Advance Developers Team
// License: GPL v2 or (at your option) any later version
//
// Hyperlink-related functions
namespace PukiWiki\Lib\Renderer\Inline;
use PukiWiki\Lib\Renderer\InlineConverter;
use PukiWiki\Lib\File\FileFactory;
use PukiWiki\Lib\File\WikiFile;
use PukiWiki\Lib\Auth\Auth;

// Base class of inline elements
class Inline
{
	var $start;   // Origin number of parentheses (0 origin)
	var $text;    // Matched string

	var $type;
	var $page;
	var $name;
	var $body;
	var $alias;

	var $redirect;

	// Constructor
	function __construct($start)
	{
		$this->start = $start;
		$this->redirect = (PKWK_USE_REDIRECT) ? get_cmd_uri('redirect',null,null,'u=') : null;	// FIXME
	}

	// Return a regex pattern to match
	function get_pattern() {}

	// Return number of parentheses (except (?:...) )
	function get_count() {}

	// Set pattern that matches
	function set($arr, $page) {}

	function toString() {}

	// Private: Get needed parts from a matched array()
	function splice($arr)
	{
		$count = $this->get_count() + 1;
		$arr   = array_pad(array_splice($arr, $this->start, $count), $count, '');
		$this->text = $arr[0];
		return $arr;
	}

	// Set basic parameters
	function setParam($page, $name, $body, $type = '', $alias = '')
	{
		static $converter = NULL;

		$this->page = $page;
		$this->name = $name;
		$this->body = $body;
		$this->type = $type;
		if (! PKWK_DISABLE_INLINE_IMAGE_FROM_URI &&
			is_url($alias) && preg_match('/\.(gif|png|bmp|jpe?g|svg?z|webp)$/i', $alias)) {
			$alias = '<img src="' . htmlsc($alias) . '" alt="' . $name . '" />';
		} else if ($alias != '') {
			if ($converter === NULL)
				$converter = new InlineConverter(array('Plugin'));

			$alias = make_line_rules($converter->convert($alias, $page));

			// BugTrack/669: A hack removing anchor tags added by AutoLink
			$alias = preg_replace('#</?a[^>]*>#i', '', $alias);
		}
		$this->alias = $alias;

		return TRUE;
	}
	
	// Make hyperlink for the page
	static function make_pagelink($page, $alias = '', $anchor = '', $refer = '', $isautolink = FALSE)
	{
		global $vars, $link_compact, $related, $_symbol_noexists;

		if (empty($page)){
			return '<a href="' . $anchor . '">' . htmlsc($alias) . '</a>';
		}

		$wiki = new WikiFile($page);
		if (! $wiki->has()) {
			$realpages = get_autoaliases(strip_bracket($page));
			foreach ($realpages as $realpage) {
				if (FileFactory::Wiki($realpage)->has()) {
					$page = $realpage;
					break;
				}
			}
		}

		$s_page = htmlsc(strip_bracket($page));
		$s_alias = empty($alias) ? $s_page : $alias;

		if ( empty($page) ) return '<a href="' . $anchor . '">' . $s_alias . '</a>';


		if (! isset($related[$page]) && $page !== $vars['page'] && $wiki->has())
			$related[$page] = $wiki->getTime();

		if ($isautolink || $wiki->has()) {
			return '<a href="' . $wiki->getUri() . $anchor . '" ' .
				(($link_compact === 0) ? 'title="' . $s_page . $wiki->passage(false,true) . '"' : '' ).
				($isautolink ? ' class="autolink"' : '') .'>' . $s_alias . '</a>';
		} else {
			// Dangling link
			if (Auth::check_role('readonly')) return $s_alias; // No dacorations

			$retval = $s_alias . '<a href="' .
				get_cmd_uri('edit', $page, null, (empty($refer) ? null : array('refer'=>$refer)) ) . '" rel="nofollow">' .
				$_symbol_noexists . '</a>';

			if ($link_compact) {
				return $retval;
			} else {
				return '<span class="noexists">' . $retval . '</span>';
			}
		}
	}
	
	function is_inside_uri($anchor){
		global $open_uri_in_new_window_servername;
		static $set_baseuri = true;

		if ($set_baseuri) {
			$set_baseuri = false;
			$open_uri_in_new_window_servername[] = get_baseuri();
		}

		foreach ($open_uri_in_new_window_servername as $servername) {
			if (stristr($anchor, $servername)) {
				return true;
			}
		}
		return false;
	}
}

/* End of file Inline.php */
/* Location: /vender/PukiWiki/Lib/Renderer/Inline/Inline.php */