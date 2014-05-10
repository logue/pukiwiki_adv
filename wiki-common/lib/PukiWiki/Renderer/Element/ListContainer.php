<?php
/**
 * リストコンテナクラス
 *
 * @package   PukiWiki\Renderer\Element
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013-2014 PukiWiki Advance Developers Team
 * @create    2013/01/26
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: ListContainer.php,v 1.0.1 2014/03/17 17:30:00 Logue Exp $
 */

namespace PukiWiki\Renderer\Element;

use PukiWiki\Renderer\Element\Element;
use PukiWiki\Renderer\Element\ElementFactory;
use PukiWiki\Renderer\Element\ListElement;

/**
 * Lists (UL, OL, DL)
 */
class ListContainer extends Element
{
	protected $tag = 'ul';
	protected $tag2 = 'li';
	public $level = 1;
	protected $style;

	public function __construct($tag, $tag2, $head, $text)
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

	public function canContain(& $obj)
	{
		//return ! is_a($obj, 'ListContainer')
		return !($obj instanceof self)
			|| ($this->tag === $obj->tag && $this->level === $obj->level);
	}

	public function setParent(& $parent)
	{
		parent::setParent($parent);

		$step = $this->level;
		if (isset($parent->parent) && ($parent->parent instanceof self))
			$step -= $parent->parent->level;
	}

	public function insert(& $obj)
	{
		$classname = get_class($this);
		if (! $obj instanceof $classname && $this->level > 3)
			return $this->last = $this->last->insert($obj);
		
		// Break if no elements found (BugTrack/524)
		if (count($obj->elements) === 1 && empty($obj->elements[0]->elements))
			return $this->last->parent; // up to ListElement

		// Move elements
		foreach(array_keys($obj->elements) as $key)
			parent::insert($obj->elements[$key]);

		return $this->last;
	}

	public function toString()
	{
		return $this->wrap(parent::toString(), $this->tag, $this->style);
	}
}

/* End of file ListContainer.php */
/* Location: ./vendor/PukiWiki/Lib/Renderer/ListContainer.php */