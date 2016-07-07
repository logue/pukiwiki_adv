<?php
/**
 * 定義文クラス
 *
 * @package   PukiWiki\Renderer\Element
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013-2014,2016 PukiWiki Advance Developers Team
 * @create    2013/01/26
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: DList.php,v 1.0.1 2016/07/07 18:10:00 Logue Exp $
 */

namespace PukiWiki\Renderer\Element;

use PukiWiki\Renderer\Element\ElementFactory;
use PukiWiki\Renderer\Element\ListElement;

/**
 * : definition1 | description1
 * : definition2 | description2
 * : definition3 | description3
 */
class DList extends ListContainer
{
	public function __construct($out)
	{
		parent::__construct('dl', 'dt', ':', $out[0]);
		$element = new ListElement($this->level, 'dd');
		$this->last = Element::insert($element);
		if ( !empty($out[1]) )
			$this->last = $this->last->insert(ElementFactory::factory('InlineElement', null, $out[1]));
	}
}

/* End of file DList.php */
/* Location: /vendor/PukiWiki/Lib/Renderer/Element/DList.php */