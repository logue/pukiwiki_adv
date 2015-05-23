<?php
/**
 * フットノート変換クラス
 *
 * @package   PukiWiki\Renderer\Inline
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2013,2015 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Note.php,v 1.0.1 2015/05/20 18:58:00 Logue Exp $
 */

namespace PukiWiki\Renderer\Inline;

use PukiWiki\Renderer\InlineFactory;
use PukiWiki\Renderer\RendererDefines;
use PukiWiki\Factory;

// Footnotes
class Note extends Inline
{
	/**
	 * title属性に入れる説明文の文字数
	 */
	const FOOTNOTE_TITLE_MAX = 16;
	/**
	 * 説明文のリンクを相対パスにする。（ページ内リンクのみにする）
	 */
	const ALLOW_RELATIVE_FOOTNOTE_ANCHOR = true;
	/**
	 * 説明文のID
	 */
	private static $note_id = 0;
	/**
	 * コンストラクタ
	 */
	public function __construct($start)
	{
		parent::__construct($start);
	}
	/**
	 * マッチパターン
	 */
	public function getPattern()
	{
		return
			'\(\('.
			 '((?>(?=\(\()(?R)|(?!\)\)).)*)'.	// (1) note body
			'\)\)';
	}
	/**
	 * 要素の数
	 */
	public function getCount()
	{
		return 1;
	}

	public function setPattern($arr, $page)
	{
		global $foot_explain, $vars;

		list(, $body) = $this->splice($arr);

		// Recover of notes(miko)
		if (count($foot_explain) === 0) {
			self::$note_id = 0;
		}

		$script = !self::ALLOW_RELATIVE_FOOTNOTE_ANCHOR ? Factory::Wiki($page)->uri() : '';

		$id   = ++self::$note_id;
		$note = InlineFactory::factory($body);
		$page = isset($vars['page']) ? rawurlencode($vars['page']) : null;

		// Footnote
		$foot_explain[$id] = '<li id="notefoot_' . $id . '">' . 
			'<a href="' . $script . '#notetext_' . $id . '" class="note_super">' . RendererDefines::FOOTNOTE_ANCHOR_ICON . $id . '</a>' . $note . 
			'</li>';

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
			$name = '<a id="notetext_' . $id . '" href="' . $script . '#notefoot_' . $id . '" class="note_super"' . $title . '>' . RendererDefines::FOOTNOTE_ANCHOR_ICON . $id . '</a>';
		}else{
			// モバイルは、ツールチップで代用
			$name = '<span class="note_super" aria-describedby="tooltip" data-msgtext="' . strip_tags($note). '">' . RendererDefines::FOOTNOTE_ANCHOR_ICON . $id . '</span>';
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
