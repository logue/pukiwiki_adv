<?php
/**
 * 目次ブロッククラス
 *
 * @package   PukiWiki\Renderer\Element
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013-2014 PukiWiki Advance Developers Team
 * @create    2013/01/26
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: ContentsList.php,v 1.0.1 2014/05/12 20:04:00 Logue Exp $
 */
namespace PukiWiki\Renderer\Element;

use PukiWiki\Renderer\Element\ElementFactory;
use PukiWiki\Renderer\Element\ListContainer;

class ContentsList extends ListContainer
{
	public function __construct($text, $level, $id)
	{
		$_text = str_repeat('-', $level) . '[[' . strip_tags($text) . '>#' . $id . ']]';
		parent::__construct('ul', 'li', '-', $_text);
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