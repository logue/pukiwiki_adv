<?php
/**
 * フットノート変換クラス
 *
 * @package   PukiWiki\Renderer\Inline
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2013 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Note.php,v 1.0.0 2013/01/29 19:54:00 Logue Exp $
 */

namespace PukiWiki\Renderer\Inline;

use PukiWiki\Renderer\InlineFactory;
use PukiWiki\Factory;

// Footnotes
class Note extends Inline
{
	const FOOTNOTE_TITLE_MAX = 16;
	const ALLOW_RELATIVE_FOOTNOTE_ANCHOR = true;

	public function __construct($start)
	{
		parent::__construct($start);
	}

	public function getPattern()
	{
		return
			'\(\('.
			 '((?:(?R)|(?!\)\)).)*)'.	// (1) note body
			'\)\)';
	}

	public function getCount()
	{
		return 1;
	}

	public function setPattern($arr, $page)
	{
		global $foot_explain, $vars;
		static $note_id = 0;

		list(, $body) = $this->splice($arr);

		// Recover of notes(miko)
		if ($foot_explain === array()) { $note_id = 0; }

		$script = !self::ALLOW_RELATIVE_FOOTNOTE_ANCHOR ? Factory::Wiki($page)->uri() : null;

		$id   = ++$note_id;
		$note = InlineFactory::factory($body);
		$page = isset($vars['page']) ? rawurlencode($vars['page']) : '';

		// Footnote
		$foot_explain[$id] = '<li id="notefoot_' . $id . '"><a href="' .
			$script . '#notetext_' . $id . '" class="note_super">*' .
			$id . '</a>' . $note . '</li>';

		if (!IS_MOBILE){
			// A hyperlink, content-body to footnote
			if (! is_numeric(self::FOOTNOTE_TITLE_MAX) || self::FOOTNOTE_TITLE_MAX <= 0) {
				$title = '';
			} else {
				$title = strip_tags($note);
				$count = mb_strlen($title, SOURCE_ENCODING);
				$title = mb_substr($title, 0, self::FOOTNOTE_TITLE_MAX, SOURCE_ENCODING);
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

	public function toString()
	{
		return $this->name;
	}
}

/* End of file Note.php */
/* Location: /vender/PukiWiki/Lib/Renderer/Inline/Note.php */
