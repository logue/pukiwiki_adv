<?php
/**
 * 段落クラス
 *
 * @package   PukiWiki\Lib\Renderer\Element
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013 PukiWiki Advance Developers Team
 * @create    2013/01/26
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Paragraph.php,v 1.0.0 2013/02/12 15:13:00 Logue Exp $
 */

namespace PukiWiki\Lib\Renderer\Element;

use PukiWiki\Lib\Renderer\Element\Element;
use PukiWiki\Lib\Renderer\Element\ElementFactory;
use PukiWiki\Lib\Renderer\Element\InlineElement;

/**
 * Paragraph: blank-line-separated sentences
 */
class Paragraph extends Element
{
	var $param;

	function __construct($text, $param = '')
	{
		parent::__construct();
		$this->param = $param;
		if (empty($text)) return;

		if (substr($text, 0, 1) == '~')
			$text = ' ' . substr($text, 1);

		$this->insert(ElementFactory::factory('Inline', null, $text));
	}

	function canContain(& $obj)
	{
		//return is_a($obj, 'Inline');
		return ($obj instanceof InlineElement);
	}

	function toString()
	{
		return $this->wrap(parent::toString(), 'p', $this->param);
	}
}

/* End of file Paragraph.php */
/* Location: ./vender/PukiWiki/Lib/Renderer/Paragraph.php */