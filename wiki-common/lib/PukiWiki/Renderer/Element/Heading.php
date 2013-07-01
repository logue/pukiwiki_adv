<?php
/**
 * 見出しクラス
 *
 * @package   PukiWiki\Renderer\Element
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013 PukiWiki Advance Developers Team
 * @create    2013/01/26
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Heading.php,v 1.0.0 2013/02/12 15:13:00 Logue Exp $
 */

namespace PukiWiki\Renderer\Element;

use PukiWiki\Renderer\Element\Element;
use PukiWiki\Renderer\Element\ElementFactory;
use PukiWiki\Text\Rules;

/**
 * * Heading1
 * ** Heading2
 * *** Heading3
 */
class Heading extends Element
{
	var $level;
	var $id;
	var $msg_top;
	var $text;

	function __construct(& $root, $text)
	{
		parent::__construct();

		$this->text = $text;
		$this->level = min(3, strspn($text, '*'));
		list($text, $this->msg_top, $this->id) = $root->getAnchor($text, $this->level);
		$this->insert(ElementFactory::factory('InlineElement', null, $text));
		$this->level++; // h2,h3,h4
	}

	function & insert(& $obj)
	{
		parent::insert($obj);
		return $this->last = & $this;
	}

	function canContain(& $obj)
	{
		return FALSE;
	}

	function toString()
	{
		$fixed_anchor = Rules::getHeading($this->text, FALSE);
		$id = (empty($fixed_anchor)) ? $this->id : $fixed_anchor;

		return $this->msg_top .  $this->wrap(parent::toString(),
			'h' . $this->level, ' id="' . $id . '"');
	}
}

/* End of file Heading.php */
/* Location: /vendor/PukiWiki/Lib/Renderer/Element/Heading.php */