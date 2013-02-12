<?php
/**
 * インライン要素クラス
 *
 * @package   PukiWiki\Lib\Renderer\Element
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013 PukiWiki Advance Developers Team
 * @create    2013/01/26
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: HRule.php,v 1.0.0 2013/02/12 15:13:00 Logue Exp $
 */

namespace PukiWiki\Lib\Renderer\Element;

use PukiWiki\Lib\Renderer\Element\Element;
use PukiWiki\Lib\Renderer\Element\Paragraph;
use PukiWiki\Lib\Renderer\InlineFactory;

/**
 * Inline elements
 */
class InlineElement extends Element
{
	function __construct($text)
	{
		parent::__construct();
		$this->elements[] = trim((substr($text, 0, 1) === "\n") ?
			$text : InlineFactory::factory($text));
	}

	function insert(& $obj)
	{
		$this->elements[] = $obj->elements[0];
		return $this;
	}

	function canContain(& $obj)
	{
		return ($obj instanceof self);
	}

	function toString()
	{
		global $line_break;
		// 改行を<br />に変換するか？
		return join(($line_break ? '<br />' . "\n" : "\n"), $this->elements);
	}

	function & toPara($class = '')
	{
		$obj = new Paragraph(null, $class);
		$obj->insert($this);
		return $obj;
	}
}

/* End of file Inline.php */
/* Location: /vendor/PukiWiki/Lib/Renderer/Element/Inline.php */