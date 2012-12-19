<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: convert_html.php,v 1.0 2012/10/30 12:02:00 Logue Exp $
// Copyright (C)
//   2010-2012 PukiWiki Advance Developers Team
//   2005-2008 PukiWiki Plus! Team
//   2002-2005, 2007,2011 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// function 'convert_html()', wiki text parser
// and related classes-and-functions
namespace PukiWiki\Lib\Renderer\Element;
use PukiWiki\Lib\Renderer\Element\Factory;

class TableCell extends Element
{
	var $tag = 'td'; // {td|th}
	var $colspan = 1;
	var $rowspan = 1;
	var $style; // is array('width'=>, 'align'=>...);

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
				$this->style[$name] = $name . ':' . htmlsc($matches[3]) . ';';
				$text = array_pop($matches);
			} else if ($matches[4]) {
				$this->style['size'] = 'font-size:' . htmlsc($matches[4]) . 'px;';
				$text = array_pop($matches);
			} else if ($matches[5]){
				$this->lang = $matches[6];
				$text = array_pop($matches);
			}
		}
		if ($is_template && is_numeric($text))
			$this->style['width'] = 'width:' . $text . 'px;';

		if (preg_match("/\S+/", $text) == false){
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
			$obj = ($obj instanceof Paragraph) ? $obj->elements[0] : Factory::factory('Div', $this, $text);
		} else {
			$obj = Factory::factory('Inline', null, $text);
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

		if($this->is_blank == true && $this->tag == 'td'){
			$param = ' class="style_td_blank"';
		}else{
			$param = ' class="style_' . $this->tag . '"';
		}
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