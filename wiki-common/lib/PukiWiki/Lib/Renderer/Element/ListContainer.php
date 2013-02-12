<?php
/**
 * リストコンテナクラス
 *
 * @package   PukiWiki\Lib\Renderer\Element
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013 PukiWiki Advance Developers Team
 * @create    2013/01/26
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: ListContainer.php,v 1.0.0 2013/02/12 15:13:00 Logue Exp $
 */

namespace PukiWiki\Lib\Renderer\Element;

use PukiWiki\Lib\Renderer\Element\Element;
use PukiWiki\Lib\Renderer\Element\ElementFactory;
use PukiWiki\Lib\Renderer\Element\ListElement;

/**
 * Lists (UL, OL, DL)
 */
class ListContainer extends Element
{
	var $tag;
	var $tag2;
	var $level;
	var $style;

	function __construct($tag, $tag2, $head, $text)
	{
		parent::__construct();
		$this->tag   = $tag;
		$this->tag2  = $tag2;
		$this->level = min(3, strspn($text, $head));
		$text = ltrim(substr($text, $this->level));

		parent::insert(new ListElement($this->level, $tag2));
		if ( !empty($text) )
			$this->last = $this->last->insert(ElementFactory::factory('InlineElement', null, $text));
	}

	function canContain(& $obj)
	{
		//return (! is_a($obj, 'ListContainer')
		return !($obj instanceof self)
			|| ($this->tag == $obj->tag && $this->level == $obj->level);
	}

	function setParent(& $parent)
	{
		parent::setParent($parent);

		$step = $this->level;
		if (isset($parent->parent) && ($parent->parent instanceof self))
			$step -= $parent->parent->level;
	}

	function insert(& $obj)
	{
		$classname = get_class($this);
		if (! $obj instanceof $classname )
			return $this->last = $this->last->insert($obj);

		// Break if no elements found (BugTrack/524)
		if (count($obj->elements) == 1 && empty($obj->elements[0]->elements))
			return $this->last->parent; // up to ListElement

		// Move elements
		foreach(array_keys($obj->elements) as $key)
			parent::insert($obj->elements[$key]);

		return $this->last;
	}

	function toString()
	{
		return $this->wrap(parent::toString(), $this->tag, $this->style);
	}
}

/* End of file ListContainer.php */
/* Location: ./vender/PukiWiki/Lib/Renderer/ListContainer.php */