<?php
/**
 * ブロック要素クラス
 *
 * @package   PukiWiki\Renderer\Element
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013-2014 PukiWiki Advance Developers Team
 * @create    2013/01/26
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Element.php,v 1.0.1 2014/03/17 17:34:00 Logue Exp $
 */

namespace PukiWiki\Renderer\Element;

/**
 * Block elements
 */
class Element
{
	protected $parent;
	protected $elements;    // References of childs
	protected $last;        // Insert new one at the back of the $last

	public function __construct()
	{
		$this->elements = array();
		$this->last     = & $this;
	}

	public function setParent(& $parent)
	{
		$this->parent = & $parent;
	}

	public function add(& $obj)
	{
		if ($this->canContain($obj)) {
			return $this->insert($obj);
		} else {
			return $this->parent->add($obj);
		}
	}

	public function insert(& $obj)
	{
		if (gettype($obj) === 'object'){
			$obj->setParent($this);
			$this->elements[] = $obj;

			$this->last = & $obj->last;
		}
		return $this->last;
	}

	public function canContain(& $obj)
	{
		return TRUE;
	}

	public function wrap($string, $tag, $param = '', $canomit = TRUE)
	{
		return ($canomit && empty($string)) ? '' :
			'<' . $tag . $param . '>' . $string . '</' . $tag . '>';
	}

	public function toString()
	{
		$ret = array();
		foreach (array_keys($this->elements) as $key)
			$ret[] = $this->elements[$key]->toString();
		return join("\n", $ret);
	}

	public function dump($indent = 0)
	{
		$ret = str_repeat(' ', $indent) . get_class($this) . "\n";
		$indent += 2;
		foreach (array_keys($this->elements) as $key) {
			$ret .= is_object($this->elements[$key]) ?
				$this->elements[$key]->dump($indent) : null;
				//str_repeat(' ', $indent) . $this->elements[$key];
		}
		return $ret;
	}
}

/* End of file Element.php */
/* Location: /vendor/PukiWiki/Lib/Renderer/Element/Element.php */