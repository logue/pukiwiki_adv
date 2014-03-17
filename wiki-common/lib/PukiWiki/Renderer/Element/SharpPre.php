<?php
/**
 * #が先頭に来るタイプの整形済みテキストクラス
 *
 * @package   PukiWiki\Renderer\Element
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013-2014 PukiWiki Advance Developers Team
 * @create    2013/01/26
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Body.php,v 1.0.1 2014/03/17 17:48:00 Logue Exp $
 */
namespace PukiWiki\Renderer\Element;

use PukiWiki\Renderer\Element\Element;
use PukiWiki\Renderer\Element\SharpPre;
use PukiWiki\Renderer\InlineFactory;

/**
 * ' 'Space-beginning sentence with color(started with '# ')
 * ' 'Space-beginning sentence with color
 * ' 'Space-beginning sentence with color
 */
class SharpPre extends Element
{
	public function __construct(&$root,$text)
	{
		global $preformat_ltrim;

		parent::__construct();
		if (substr($text, 0, 2) === '# ') $text = substr($text,1);
		$this->elements[] = (!$preformat_ltrim || empty($text) || substr($text, 0, 1) !== ' ') ? $text : substr($text,1);
	}
	public function canContain(&$obj)
	{
		return ($obj instanceof self);
	}
	public function insert(&$obj)
	{
		$this->elements[] = $obj->elements[0];
		return $this;
	}
	public function toString()
	{
		static $saved_glossary, $saved_autolink, $make_link;
		global $glossary, $autolink;
		// 変換されるのを防ぐため、一時的にGlossaryとAutoLinkを無効化する
		$saved_glossary = $glossary;
		$saved_autolink = $autolink;
		$glossary = FALSE;
		$autolink = FALSE;
		// 変換処理
		$ret = InlineFactory::factory($this->elements);
		// GlossaryとAutoLinkをもとに戻す
		$autolink = $saved_autolink;
		$glossary = $saved_glossary;
		return $this->wrap($ret,'pre');
	}
}

/* End of file SharpPre.php */
/* Location: /vendor/PukiWiki/Lib/Renderer/Element/SharpPre.php */