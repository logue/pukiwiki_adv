<?php
/**
 * 水平線クラス
 *
 * @package   PukiWiki\Renderer\Element
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013 PukiWiki Advance Developers Team
 * @create    2013/01/26
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: HRule.php,v 1.0.0 2013/02/12 15:13:00 Logue Exp $
 */

namespace PukiWiki\Renderer\Element;

use PukiWiki\Renderer\Element\Element;

/**
 * Horizontal Rule
 */
class HRule extends Element
{
	function __construct(& $root, $text)
	{
		parent::__construct();
	}

	function canContain(& $obj)
	{
		return FALSE;
	}

	function toString()
	{
		return '<hr class="style_hr" />';
	}
}

/* End of file HRule.php */
/* Location: /vendor/PukiWiki/Lib/Renderer/Element/HRule.php */