<?php
/**
 * 整形済みテキストクラス
 *
 * @package   PukiWiki\Renderer\Element
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013-2014 PukiWiki Advance Developers Team
 * @create    2013/01/26
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Pre.php,v 1.0.1 2014/03/17 17:32:00 Logue Exp $
 */
namespace PukiWiki\Renderer\Element;

use PukiWiki\Renderer\Element\Element;
use PukiWiki\Utility;

/**
 * ' 'Space-beginning sentence
 */
class Pre extends Element
{
	public function __construct(& $root, $text)
	{
		global $preformat_ltrim;
		parent::__construct();
		$this->elements[] = Utility::htmlsc(
			(! $preformat_ltrim || empty($text) || $text{0} != ' ') ? $text : substr($text, 1));
	}

	public function canContain(& $obj)
	{
		return ($obj instanceof self);
	}

	public function insert(& $obj)
	{
		$this->elements[] = $obj->elements[0];
		return $this;
	}

	public function toString()
	{
		return $this->wrap(join("\n", $this->elements), 'pre');
	}
}

/* End of file Pre.php */
/* Location: /vendor/PukiWiki/Lib/Renderer/Element/Pre.php */