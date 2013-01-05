<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: Note.php,v 1.0.0 2013/01/05 15:46:00 Logue Exp $
// Copyright (C)
//   2012-2013 PukiWiki Advance Developers Team
// License: GPL v2 or (at your option) any later version

namespace PukiWiki\Lib\Renderer\Inline;
use PukiWiki\Lib\Renderer\InlineFactory;
use PukiWiki\Lib\Router;

// Footnotes
class Note extends Inline
{
	function __construct($start)
	{
		parent::__construct($start);
	}

	function get_pattern()
	{
		return
			'\(\('.
			 '((?:(?R)|(?!\)\)).)*)'.	// (1) note body
			'\)\)';
	}

	function get_count()
	{
		return 1;
	}

	function set($arr, $page)
	{
		global $foot_explain, $vars;
		static $note_id = 0;

		list(, $body) = $this->splice($arr);

		// Recover of notes(miko)
		if ($foot_explain === array()) { $note_id = 0; }

		if (PKWK_ALLOW_RELATIVE_FOOTNOTE_ANCHOR) {
			$script = '';
		} else {
			$script = get_page_uri($page);
		}

		$id   = ++$note_id;
		$note = InlineFactory::factory($body);
		$page = isset($vars['page']) ? rawurlencode($vars['page']) : '';

		// Footnote
		$foot_explain[$id] = '<li id="notefoot_' . $id . '"><a href="' .
			$script . '#notetext_' . $id . '" class="note_super">*' .
			$id . '</a>' . $note . '</li>';

		if (!IS_MOBILE){
			// A hyperlink, content-body to footnote
			if (! is_numeric(PKWK_FOOTNOTE_TITLE_MAX) || PKWK_FOOTNOTE_TITLE_MAX <= 0) {
				$title = '';
			} else {
				$title = strip_tags($note);
				$count = mb_strlen($title, SOURCE_ENCODING);
				$title = mb_substr($title, 0, PKWK_FOOTNOTE_TITLE_MAX, SOURCE_ENCODING);
				$abbr  = (mb_strlen($title) < $count) ? '...' : '';
				$title = ' title="' . $title . $abbr . '"';
			}
			$name = '<a id="notetext_' . $id . '" href="' . $script .
				'#notefoot_' . $id . '" class="note_super"' . $title .
				'>*' . $id . '</a>';
		}else{
			// モバイルは、ツールチップで代用
			$name = '<span class="note_super" aria-describedby="tooltip" data-msgtext="' . strip_tags($note). '">*' . $id . '</span>';
		}

		return parent::setParam($page, $name, $body);
	}

	function toString()
	{
		return $this->name;
	}
}

/* End of file Note.php */
/* Location: /vender/PukiWiki/Lib/Renderer/Inline/Note.php */
