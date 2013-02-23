<?php
/**
 * 引用ブロッククラス
 *
 * @package   PukiWiki\Renderer\Element
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013 PukiWiki Advance Developers Team
 * @create    2013/01/26
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Blockquote.php,v 1.0.0 2013/02/12 15:13:00 Logue Exp $
 */

namespace PukiWiki\Renderer\Element;

use PukiWiki\Renderer\Element\Element;
use PukiWiki\Renderer\Element\ElementFactory;
use PukiWiki\Renderer\Element\InlineElement;
use PukiWiki\Renderer\Element\Paragraph;

/**
 * > Someting cited
 * > like E-mail text
 */
class Blockquote extends Element
{
	var $level;

	function __construct(& $root, $text)
	{
		parent::__construct();

		$head = substr($text, 0, 1);
		$this->level = min(3, strspn($text, $head));
		$text = ltrim(substr($text, $this->level));

		if ($head === '<') { // Blockquote close
			$level       = $this->level;
			$this->level = 0;
			$this->last  = $this->end($root, $level);
			if (! empty($text))
				$this->last = $this->last->insert(ElementFactory::factory('InlineElement', null, $text));
		} else {
			$this->insert(ElementFactory::factory('InlineElement', null, $text));
		}
	}

	function canContain(& $obj)
	{
		return (! is_a($obj, get_class($this)) || $obj->level >= $this->level);
	}

	function insert(& $obj)
	{
		if (!is_object($obj)) return;

		// BugTrack/521, BugTrack/545
		if ($obj instanceof InlineElement)
			return parent::insert($obj->toPara(' class="style_blockquote"'));

		if ( $obj instanceof self && $obj->level == $this->level && count($obj->elements)) {
			$obj = & $obj->elements[0];
			if ($this->last instanceof Paragraph && count($obj->elements))
				$obj = & $obj->elements[0];
		}
		return parent::insert($obj);
	}

	function toString()
	{
		return $this->wrap(parent::toString(), 'blockquote');
	}

	function & end(& $root, $level)
	{
		$parent = & $root->last;

		while (is_object($parent)) {
			if ($parent instanceof self && $parent->level == $level)
				return $parent->parent;
			$parent = & $parent->parent;
		}
		return $this;
	}
}

/* End of file Blockquote.php */
/* Location: /vendor/PukiWiki/Lib/Renderer/Element/Blockquote.php */