<?php
/**
 * リスト要素クラス
 *
 * @package   PukiWiki\Renderer\Element
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013-2014 PukiWiki Advance Developers Team
 * @create    2013/01/26
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: ListElement.php,v 1.0.1 2014/03/17 17:30:00 Logue Exp $
 */

namespace PukiWiki\Renderer\Element;

use PukiWiki\Renderer\Element\Element;
use PukiWiki\Renderer\Element\ListContainer;

class ListElement extends Element
{
	public function __construct($level, $head)
	{
		parent::__construct();
		$this->level = $level;
		$this->head  = $head;
	}

	public function canContain(& $obj)
	{
		return (! $obj instanceof ListContainer || ($obj->level > $this->level));
	}

	public function toString()
	{
		return $this->wrap(parent::toString(), $this->head);
	}
}

/* End of file ListElement.php */
/* Location: ./vender/PukiWiki/Lib/Renderer/ListElement.php */