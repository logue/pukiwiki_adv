<?php
/**
 * 番号付きリスト要素クラス
 *
 * @package   PukiWiki\Renderer\Element
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013 PukiWiki Advance Developers Team
 * @create    2013/01/26
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: OList.php,v 1.0.0 2013/02/12 15:13:00 Logue Exp $
 */

namespace PukiWiki\Renderer\Element;

use PukiWiki\Renderer\Element\ListContainer;

/**
 * + One
 * + Two
 * + Three
 */
class OList extends ListContainer
{
	function __construct(& $root, $text)
	{
		parent::__construct('ol', 'li', '+', $text);
	}
}

/* End of file OList.php */
/* Location: ./vender/PukiWiki/Lib/Renderer/OList.php */