<?php
/**
 * テーブルのセルクラス
 *
 * @package   PukiWiki\Lib\Renderer\Element
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013 PukiWiki Advance Developers Team
 * @create    2013/01/26
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: TableCell.php,v 1.0.0 2013/02/12 15:13:00 Logue Exp $
 */

namespace PukiWiki\Lib\Renderer\Element;

use PukiWiki\Lib\Renderer\Element\Element;
use PukiWiki\Lib\Renderer\Element\ElementFactory;
use PukiWiki\Lib\Renderer\Element\Paragraph;
use PukiWiki\Lib\Utility;

class TableCell extends Element
{
	var $tag = 'td';    // {td|th}
	var $colspan = 1;
	var $rowspan = 1;
	var $style;         // is array('width'=>, 'align'=>...);

	function __construct($text, $is_template = FALSE)
	{
		parent::__construct();
		$this->style = $matches = array();
		$this->is_blank = false;

		// 必ず$matchesの末尾の配列にテキストの内容が入るのでarray_popの返り値を使用する方法に変更。
		// もうすこし、マシな実装方法ないかな・・・。12/05/03
		while (preg_match('/^(?:(LEFT|CENTER|RIGHT)|(BG)?COLOR\(([#\w]+)\)|SIZE\((\d+)\)|LANG\((\w+2)\)):(.*)$/', $text, $matches)) {
			if ($matches[1]) {
				$this->style['align'] = 'text-align:' . strtolower($matches[1]) . ';';
				$text = array_pop($matches);
			} else if ($matches[3]) {
				$name = $matches[2] ? 'background-color' : 'color';
				$this->style[$name] = $name . ':' . Utility::htmlsc($matches[3]) . ';';
				$text = array_pop($matches);
			} else if ($matches[4]) {
				$this->style['size'] = 'font-size:' . Utility::htmlsc($matches[4]) . 'px;';
				$text = array_pop($matches);
			} else if ($matches[5]){
				$this->lang = $matches[6];
				$text = array_pop($matches);
			}
		}
		if ($is_template && is_numeric($text))
			$this->style['width'] = 'width:' . $text . 'px;';

		if (preg_match("/\S+/", $text) === false){
			// セルが空だったり、空白文字しか残らない場合は、空欄のセルとする。（HTMLではタブやスペースも削除）
			$text = '';
			$this->is_blank = true;
		} else if ($text == '>') {
			$this->colspan = 0;
		} else if ($text == '~') {
			$this->rowspan = 0;
		} else if (substr($text, 0, 1) == '~') {
			$this->tag = 'th';
			$text      = substr($text, 1);
		}

		if (!empty($text) && $text{0} === '#') {
			// Try using Div class for this $text
			$obj = ($obj instanceof Paragraph) ? $obj->elements[0] : ElementFactory::factory('Div', $this, $text);
		} else {
			$obj = ElementFactory::factory('InlineElement', null, $text);
		}

		$this->insert($obj);
	}

	function setStyle(& $style)
	{
		foreach ($style as $key=>$value)
			if (! isset($this->style[$key]))
				$this->style[$key] = $value;
	}

	function toString()
	{
		if ($this->rowspan == 0 || $this->colspan == 0) return '';

		$param = ' class="style_' . ($this->is_blank == true && $this->tag == 'td' ? 'td_blank' :  $this->tag) . '"';
		
		if ($this->rowspan > 1)
			$param .= ' rowspan="' . $this->rowspan . '"';
		if ($this->colspan > 1) {
			$param .= ' colspan="' . $this->colspan . '"';
			unset($this->style['width']);
		}
		if (! empty($this->lang))
			$param .= ' lang="' . $this->lang . '"';

		if (! empty($this->style))
			$param .= ' style="' . join(' ', $this->style) . '"';

		return $this->wrap(parent::toString(), $this->tag, $param, FALSE);
	}
}

/* End of file TableCell.php */
/* Location: /vendor/PukiWiki/Lib/Renderer/Element/TableCell.php */