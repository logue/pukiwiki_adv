<?php
/**
 * 目次ブロッククラス
 *
 * @package   PukiWiki\Renderer\Element
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013 PukiWiki Advance Developers Team
 * @create    2013/01/26
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: ContentsList.php,v 1.0.0 2013/02/12 15:13:00 Logue Exp $
 */
namespace PukiWiki\Renderer\Element;

use PukiWiki\Renderer\Element\ElementFactory;
use PukiWiki\Renderer\Element\ListContainer;

class ContentsList extends ListContainer
{
	public function __construct($text, $level, $id)
	{
		$_text = '[[' . strip_tags($text) . '>#' . $id . ']]';
		parent::__construct('ul', 'li', $level, $_text);
		parent::insert(ElementFactory::factory('InlineElement', null, $_text));
	}

	public function setParent(& $parent)
	{
		parent::setParent($parent);
		$step   = $this->level;
		if (isset($parent->parent) && ($parent->parent instanceof parent)) {
			$step  -= $parent->parent->level;
		}
	}
}

/* End of file ContentsList.php */
/* Location: /vendor/PukiWiki/Lib/Renderer/Element/ContentsList.php */