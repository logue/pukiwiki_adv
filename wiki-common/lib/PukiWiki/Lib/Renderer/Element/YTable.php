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

// , cell1  , cell2  ,  cell3
// , cell4  , cell5  ,  cell6
// , cell7  ,        right,==
// ,left          ,==,  cell8
class YTable extends Element
{
	var $col;	// Number of columns
	var $align = 'center';

	// TODO: Seems unable to show literal '==' without tricks.
	//       But it will be imcompatible.
	// TODO: Why toString() or toXHTML() here
	function __construct($row = array('cell1 ', ' cell2 ', ' cell3'))
	{
		parent::__construct();

		$str = array();
		$col = count($row);

		$matches = $_value = $_align = array();
		foreach($row as $cell) {
			if (preg_match('/^(\s+)?(.+?)(\s+)?$/', $cell, $matches)) {
				if ($matches[2] == '==') {
					// Colspan
					$_value[] = FALSE;
					$_align[] = FALSE;
				} else {
					$_value[] = $matches[2];
					if ( empty($matches[1]) ) {
						$_align[] = '';	// left
					} else if (isset($matches[3])) {
						$_align[] = 'center';
					} else {
						$_align[] = 'right';
					}
				}
			} else {
				$_value[] = $cell;
				$_align[] = '';
			}
		}

		for ($i = 0; $i < $col; $i++) {
			if ($_value[$i] === FALSE) continue;
			$colspan = 1;
			while (isset($_value[$i + $colspan]) && $_value[$i + $colspan] === FALSE) ++$colspan;
			$colspan = ($colspan > 1) ? ' colspan="' . $colspan . '"' : '';
			$text = preg_match("/\s+/", $_value[$i]) ? '' : make_link($_value[$i]);
			$class = ((empty($text) || !preg_match("/\S+/", $text))) ? 'style_td_blank' : 'style_td';
			$align = $_align[$i] ? ' style="text-align:' . $_align[$i] . '"' : '';
			$str[] = '<td class="'.$class.'"' . $align . $colspan . '>' . $text . '</td>';
			unset($_value[$i], $_align[$i], $text);
		}

		$this->col        = $col;
		$this->elements[] = implode('', $str);
	}

	function canContain(& $obj)
	{
		return ($obj instanceof YTable) && ($obj->col == $this->col);
	}

	function & insert(& $obj)
	{
		$this->elements[] = $obj->elements[0];
		return $this;
	}

	function toString()
	{
		$rows = '';
		foreach ($this->elements as $str) {
			$rows .= "\n" . '<tr class="style_tr">' . $str . '</tr>' . "\n";
		}
		$rows = $this->wrap($rows, 'table', ' class="style_table style_table_' . $this->align . '"');
		return $this->wrap($rows, 'div', ' class="table_wrapper"');
	}
}