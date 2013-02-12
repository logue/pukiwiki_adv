<?php
/**
 * 目次ブロッククラス
 *
 * @package   PukiWiki\Lib\Renderer\Element
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013 PukiWiki Advance Developers Team
 * @create    2013/01/26
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: ContentsList.php,v 1.0.0 2013/02/12 15:13:00 Logue Exp $
 */
namespace PukiWiki\Lib\Renderer\Element;

use PukiWiki\Lib\Renderer\Element\ElementFactory;
use PukiWiki\Lib\Renderer\Element\ListContainer;
use PukiWiki\Lib\Utility;

class ContentsList extends ListContainer
{
	function __construct($text, $level, $id)
	{
		// Reformatting $text
		// A line started with "\n" means "preformatted" ... X(
		Utility::setHeading($text);
		$text = "\n" . '<a href="#' . $id . '"'. ((IS_MOBILE) ? ' data-ajax="false" data-anchor="' . $id . '"' : '') . '>' . $text . '</a>' . "\n";
		parent::__construct('ul', 'li', '-', str_repeat('-', $level));
		
		$this->insert(ElementFactory::factory('InlineElement', null, $text));
	}

	function setParent(& $parent)
	{
		parent::setParent($parent);
		$step   = $this->level;
		if (isset($parent->parent) && ($parent->parent instanceof ListContainer)) {
			$step  -= $parent->parent->level;
		}
	}
}

/* End of file ContentsList.php */
/* Location: /vendor/PukiWiki/Lib/Renderer/Element/ContentsList.php */