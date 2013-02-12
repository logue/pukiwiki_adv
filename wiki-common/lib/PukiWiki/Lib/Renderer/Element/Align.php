<?php
/**
 * 位置決めクラス
 *
 * @package   PukiWiki\Lib\Renderer\Element
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013 PukiWiki Advance Developers Team
 * @create    2013/01/26
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Align.php,v 1.0.0 2013/02/12 15:13:00 Logue Exp $
 */

namespace PukiWiki\Lib\Renderer\Element;

use PukiWiki\Lib\Renderer\Element\Element;
use PukiWiki\Lib\Renderer\Element\InlineElement;
use PukiWiki\Lib\Renderer\Element\Table;
use PukiWiki\Lib\Renderer\Element\YTable;

/**
 * LEFT: / CENTER: / RIGHT:
 */
class Align extends Element
{
	var $align;

	function __construct($align)
	{
		parent::__construct();
		$this->align = $align;
	}

	function canContain(& $obj)
	{
		if ($obj instanceof Table || $obj instanceof YTable) {
			$obj->align = $this->align;
		}
		return ($obj instanceof InlineElement);
	}

	function toString()
	{
		return $this->wrap(parent::toString(), 'div', ' style="text-align:' . $this->align . '"');
	}
}

/* End of file Align.php */
/* Location: /vendor/PukiWiki/Lib/Renderer/Element/Align.php */