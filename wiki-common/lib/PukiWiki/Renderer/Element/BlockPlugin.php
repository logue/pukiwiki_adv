<?php
/**
 * ブロック型プラグインクラス
 *
 * @package   PukiWiki\Renderer\Element
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013-2014 PukiWiki Advance Developers Team
 * @create    2013/01/26
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: BlockPlugin.php,v 1.0.1 2014/03/17 17:20:00 Logue Exp $
 */

namespace PukiWiki\Renderer\Element;

use PukiWiki\Renderer\PluginRenderer;

/**
 *  Block plugin: #something (started with '#')
 */
class BlockPlugin extends Element
{
	protected $name;
	protected $param;

	public function __construct($out)
	{
		parent::__construct();
		list(, $this->name, $this->param) = array_pad($out, 3, null);
	}

	public function canContain(& $obj)
	{
		return FALSE;
	}

	public function toString()
	{
		// Call #plugin
		return PluginRenderer::executePluginBlock($this->name, $this->param);
	}
}

/* End of file BlockPlugin.php */
/* Location: /vendor/PukiWiki/Lib/Renderer/Element/BlockPlugin.php */