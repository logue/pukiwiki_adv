<?php
/**
 * テーブルのセルクラス
 *
 * @package   PukiWiki\Renderer\Element
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2014 PukiWiki Advance Developers Team
 * @create    2014/05/30
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: BlockPluginDummy.php,v 1.0.0 2014/05/30 20:54:00 Logue Exp $
 */
namespace PukiWiki\Renderer\Element;

use PukiWiki\Renderer\GuiEdit\Ref;

/**
 * Block plugin (for GuiEdit): #something (started with '#')
 */
class BlockPluginDummy extends Element
{
	protected $text;
	protected $name;
	protected $param;

	public function __construct($out)
	{
		parent::__construct();
		list(, $this->name, $this->param, $this->text) = array_pad($out, 4, '');
	}

	public function canContain(& $obj)
	{
		return FALSE;
	}

	public function toString()
	{
		switch ($this->name) {
			case 'br':
				return '<br />'."\n";
			case 'hr':
				return '<hr />';
			case 'pagebreak':
				return '<div style="page-break-after: always;"></div>';
			case 'ref':
				$param = ($this->param != '') ? explode(',', $this->param) : array();
				return Ref::convert($param);
		}
		
		if ($this->text) {
			$this->text = preg_replace("/\r/", "<br />", $this->text);
		}
		
		$inner = "#$this->name" . ($this->param ? "($this->param)" : '') . $this->text;
		
		return $this->wrap($inner, 'div', ' class="bg-primary" contenteditable="false"' . $style);
	}
}