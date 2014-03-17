<?php
/**
 * 段落クラス
 *
 * @package   PukiWiki\Renderer\Element
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013-2014 PukiWiki Advance Developers Team
 * @create    2013/01/26
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Paragraph.php,v 1.0.1 2014/03/17 17:31:00 Logue Exp $
 */

namespace PukiWiki\Renderer\Element;

use PukiWiki\Renderer\Element\Element;
use PukiWiki\Renderer\Element\ElementFactory;
use PukiWiki\Renderer\Element\InlineElement;

/**
 * Paragraph: blank-line-separated sentences
 */
class Paragraph extends Element
{
	protected $param;

	public function __construct($text, $param = '')
	{
		parent::__construct();
		$this->param = $param;
		if (empty($text)) return;

		if (substr($text, 0, 1) == '~')
			$text = ' ' . substr($text, 1);

		$this->insert(ElementFactory::factory('Inline', null, $text));
	}

	public function canContain(& $obj)
	{
		//return is_a($obj, 'Inline');
		return ($obj instanceof InlineElement);
	}

	public function toString()
	{
		return $this->wrap(parent::toString(), 'p', $this->param);
	}
}

/* End of file Paragraph.php */
/* Location: ./vendor/PukiWiki/Lib/Renderer/Paragraph.php */