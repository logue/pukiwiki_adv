<?php
/**
 * テーブルクラス
 *
 * @package   PukiWiki\Renderer\Element
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013-2014 PukiWiki Advance Developers Team
 * @create    2013/01/26
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Table.php,v 1.0.1 2014/03/17 18:32:00 Logue Exp $
 */

namespace PukiWiki\Renderer\Element;

use PukiWiki\Renderer\Element\Element;
use PukiWiki\Renderer\Element\TableCell;

/**
 * | title1 | title2 | title3 |
 * | cell1  | cell2  | cell3  |
 * | cell4  | cell5  | cell6  |
 */
class Table extends Element
{
	protected $type;
	protected $types;
	protected $col;   // number of column
	public $align = 'center';
	protected static $parts = array(
		'h'=>'thead',
		'f'=>'tfoot',
		''=>'tbody'
	);

	public function __construct($out)
	{
		parent::__construct();

		$cells       = explode('|', $out[1]);
		$this->col   = count($cells);
		$this->type  = strtolower($out[2]);
		$this->types = array($this->type);
		$is_template = $this->type === 'c';
		$row = array();
		foreach ($cells as $cell){
			$row[] = new TableCell($cell, $is_template);
		}
		$this->elements[] = $row;
	}

	public function canContain(& $obj)
	{
		return ($obj instanceof self) && ($obj->col === $this->col);
	}

	public function insert(& $obj)
	{
		$this->elements[] = $obj->elements[0];
		$this->types[]    = $obj->type;
		return $this;
	}

	public function toString()
	{
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
			$row = $this->elements[$nrow];
			if ($this->types[$nrow] === 'c')
				$stylerow = $row;
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
		foreach (static::$parts as $type => $part)
		{
			$part_string = '';
			foreach (array_keys($this->elements) as $nrow) {
				if ($this->types[$nrow] != $type)
					continue;
				$row        = $this->elements[$nrow];
				$row_string = '';
				foreach (array_keys($row) as $ncol)
					$row_string .= $row[$ncol]->toString();
				$part_string .= $this->wrap($row_string, 'tr');
			}
			$string .= $this->wrap($part_string, $part);
		}
		$string = $this->wrap($string, 'table', ' class="table table-bordered table_' . $this->align . '" data-pagenate="false"');

		return $this->wrap($string, 'div', ' class="table-wrapper"');
	}
}

/* End of file Table.php */
/* Location: /vendor/PukiWiki/Lib/Renderer/Element/Table.php */