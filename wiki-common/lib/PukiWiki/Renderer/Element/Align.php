<?php
/**
 * 位置決めクラス
 *
 * @package   PukiWiki\Renderer\Element
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013-2014 PukiWiki Advance Developers Team
 * @create    2013/01/26
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Align.php,v 1.0.1 2014/03/17 15:06:00 Logue Exp $
 */

namespace PukiWiki\Renderer\Element;

use PukiWiki\Renderer\Element\Element;
use PukiWiki\Renderer\Element\InlineElement;
use PukiWiki\Renderer\Element\Table;
use PukiWiki\Renderer\Element\YTable;

/**
 * LEFT: / CENTER: / RIGHT: / JUSTIFY:
 */
class Align extends Element
{
	protected $align;

	public function __construct($align)
	{
		parent::__construct();
		$this->align = $align;
	}

	public function canContain(& $obj)
	{
		if ($obj instanceof Table || $obj instanceof YTable) {
			$obj->align = $this->align;
		}
		return ($obj instanceof InlineElement);
	}

	public function toString()
	{
		if (empty($this->align)) return $this->wrap(parent::toString(), 'div');
		$align = strtolower($this->align);
		
		return $this->wrap(parent::toString(), 'div', ' class="text-' . $this->align . '"');
	}
}

/* End of file Align.php */
/* Location: /vendor/PukiWiki/Lib/Renderer/Element/Align.php */