<?php
/**
 * 箇条書きクラス
 *
 * @package   PukiWiki\Lib\Renderer\Element
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013 PukiWiki Advance Developers Team
 * @create    2013/01/26
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: UList.php,v 1.0.0 2013/02/12 15:13:00 Logue Exp $
 */

namespace PukiWiki\Lib\Renderer\Element;

use PukiWiki\Lib\Renderer\Element\ListContainer;

/**
 * - One
 * -- Two
 * --- Three
 */
class UList extends ListContainer
{
	function __construct(& $root, $text)
	{
		parent::__construct('ul', 'li', '-', $text);
	}
}

/* End of file UList.php */
/* Location: /vendor/PukiWiki/Lib/Renderer/Element/UList.php */