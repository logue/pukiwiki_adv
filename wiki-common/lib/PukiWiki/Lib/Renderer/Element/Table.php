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

// | title1 | title2 | title3 |
// | cell1  | cell2  | cell3  |
// | cell4  | cell5  | cell6  |
class Table extends Element
{
	var $type;
	var $types;
	var $col; // number of column
	var $align = 'center';

	function __construct($out)
	{
		parent::__construct();

		$cells       = explode('|', $out[1]);
		$this->col   = count($cells);
		$this->type  = strtolower($out[2]);
		$this->types = array($this->type);
		$is_template = ($this->type == 'c');
		$row = array();
		foreach ($cells as $cell)
			$row[] = new TableCell($cell, $is_template);
		$this->elements[] = $row;
	}

	function canContain(& $obj)
	{
		return ($obj instanceof Table) && ($obj->col === $this->col);
	}

	function & insert(& $obj)
	{
		$this->elements[] = $obj->elements[0];
		$this->types[]    = $obj->type;
		return $this;
	}

	function toString()
	{
		static $parts = array('h'=>'thead', 'f'=>'tfoot', ''=>'tbody');

		// Set rowspan (from bottom, to top)
		for ($ncol = 0; $ncol < $this->col; $ncol++) {
			$rowspan = 1;
			foreach (array_reverse(array_keys($this->elements)) as $nrow) {
				$row = & $this->elements[$nrow];
				if ($row[$ncol]->rowspan === 0) {
					++$rowspan;
					continue;
				}
				$row[$ncol]->rowspan = $rowspan;
				// Inherits row type
				while (--$rowspan)
					$this->types[$nrow + $rowspan] = $this->types[$nrow];
				$rowspan = 1;
			}
		}

		// Set colspan and style
		$stylerow = NULL;
		foreach (array_keys($this->elements) as $nrow) {
			$row = & $this->elements[$nrow];
			if ($this->types[$nrow] === 'c')
				$stylerow = & $row;
			$colspan = 1;
			foreach (array_keys($row) as $ncol) {
				if ($row[$ncol]->colspan === 0) {
					++$colspan;
					continue;
				}
				$row[$ncol]->colspan = $colspan;
				if (!is_null($stylerow)) {
					$row[$ncol]->setStyle($stylerow[$ncol]->style);
					// Inherits column style
					while (--$colspan)
						$row[$ncol - $colspan]->setStyle($stylerow[$ncol]->style);
				}
				$colspan = 1;
			}
		}

		// toString
		$string = '';
		foreach ($parts as $type => $part)
		{
			$part_string = '';
			foreach (array_keys($this->elements) as $nrow) {
				if ($this->types[$nrow] != $type)
					continue;
				$row        = & $this->elements[$nrow];
				$row_string = '';
				foreach (array_keys($row) as $ncol)
					$row_string .= $row[$ncol]->toString();
				$part_string .= $this->wrap($row_string, 'tr');
			}
			$string .= $this->wrap($part_string, $part);
		}
		$string = $this->wrap($string, 'table', ' class="style_table style_table_' . $this->align . '" data-pagenate="false" ');

		return $this->wrap($string, 'div', ' class="table_wrapper"');
	}
}