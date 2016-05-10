<?php
/**
 * カンマ区切りのテーブルクラス
 *
 * @package   PukiWiki\Renderer\Element
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013-2014 PukiWiki Advance Developers Team
 * @create    2013/01/26
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: YTable.php,v 1.0.1 2014/03/17 18:35:00 Logue Exp $
 */

namespace PukiWiki\Renderer\Element;

use PukiWiki\Renderer\Element\Element;
use PukiWiki\Renderer\InlineFactory;

/**
 * , cell1  , cell2  ,  cell3
 * , cell4  , cell5  ,  cell6
 * , cell7  ,   right,==
 * ,left          ,==,  cell8
 */
class YTable extends Element
{
	protected $col;	// Number of columns

	public $align = 'center';

	// TODO: Seems unable to show literal '==' without tricks.
	//       But it will be imcompatible.
	// TODO: Why toString() or toXHTML() here
	public function __construct($row = array('cell1 ', ' cell2 ', ' cell3'))
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
			$text = preg_match("/\S+/", $_value[$i]) ? InlineFactory::factory($_value[$i]) : '';
			$class = ((empty($text) || !preg_match("/\S+/", $text))) ? 'blank-cell' : '';
			$align = $_align[$i] ? ' style="text-align:' . $_align[$i] . '"' : '';
			$str[] = '<td class="'.$class.'"' . $align . $colspan . '>' . $text . '</td>';
			unset($_value[$i], $_align[$i], $text);
		}

		$this->col        = $col;
		$this->elements[] = implode('', $str);
	}

	public function canContain(& $obj)
	{
		return ($obj instanceof self) && ($obj->col == $this->col);
	}

	public function insert(& $obj)
	{
		$this->elements[] = $obj->elements[0];
		return $this;
	}

	public function toString()
	{
		$rows = '';
		foreach ($this->elements as $str) {
			$rows .= "\n" . '<tr>' . $str . '</tr>' . "\n";
		}
		$rows = $this->wrap($rows, 'table', ' class="table table-bordered table_' . $this->align . '" data-pagenate="false"');
		return $this->wrap($rows, 'div', ' class="table_wrapper"');
	}
}

/* End of file YTable.php */
/* Location: /vendor/PukiWiki/Lib/Renderer/Element/YTable.php */