<?php
/**
 * ブロック型プラグインクラス
 *
 * @package   PukiWiki\Renderer\Element
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013 PukiWiki Advance Developers Team
 * @create    2013/01/26
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: BlockPlugin.php,v 1.0.0 2013/02/12 15:13:00 Logue Exp $
 */

namespace PukiWiki\Renderer\Element;

/**
 *  Block plugin: #something (started with '#')
 */
class BlockPlugin extends Element
{
	var $name;
	var $param;

	function __construct($out)
	{
		parent::__construct();
		list(, $this->name, $this->param) = array_pad($out, 3, null);
	}

	function canContain(& $obj)
	{
		return FALSE;
	}

	function toString()
	{
		// Call #plugin
		return do_plugin_convert($this->name, $this->param);
	}
}

/* End of file BlockPlugin.php */
/* Location: /vendor/PukiWiki/Lib/Renderer/Element/BlockPlugin.php */