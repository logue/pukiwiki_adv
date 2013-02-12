<?php
/**
 * 定義文クラス
 *
 * @package   PukiWiki\Lib\Renderer\Element
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013 PukiWiki Advance Developers Team
 * @create    2013/01/26
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: DList.php,v 1.0.0 2013/02/12 15:13:00 Logue Exp $
 */

namespace PukiWiki\Lib\Renderer\Element;

use PukiWiki\Lib\Renderer\Element\ElementFactory;
use PukiWiki\Lib\Renderer\Element\ListElement;

/**
 * : definition1 | description1
 * : definition2 | description2
 * : definition3 | description3
 */
class DList extends ListContainer
{
	function __construct($out)
	{
		parent::__construct('dl', 'dt', ':', $out[0]);
		$this->last = Element::insert(new ListElement($this->level, 'dd'));
		if ( !empty($out[1]) )
			$this->last = $this->last->insert(ElementFactory::factory('InlineElement', null, $out[1]));
	}
}

/* End of file DList.php */
/* Location: /vendor/PukiWiki/Lib/Renderer/Element/DList.php */