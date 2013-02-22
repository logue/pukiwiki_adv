<?php
/**
 * Wiki名クラス
 *
 * @package   PukiWiki\Lib\Renderer\Inline
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2013 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: WikiName.php,v 1.0.0 2013/01/29 19:54:00 Logue Exp $
 */

namespace PukiWiki\Lib\Renderer\Inline;

use PukiWiki\Lib\Renderer\RendererDefines;

// WikiNames
class WikiName extends Inline
{
	public function __construct($start)
	{
		parent::__construct($start);
	}

	public function getPattern()
	{
		global $nowikiname;
		return $nowikiname ? FALSE : '(' . RendererDefines::WIKINAME_PATTERN . ')';
	}

	public function getCount()
	{
		return 1;
	}

	public function setPattern($arr, $page)
	{
		list($name) = $this->splice($arr);
		return parent::setParam($page, $name, null, 'pagename', $name);
	}

	public function __toString()
	{
		return parent::setAutoLink(
			$this->name,
			$this->alias,
			null,
			$this->page
		);
	}
}

/* End of file WikiName.php */
/* Location: /vendor/PukiWiki/Lib/Renderer/Inline/WikiName.php */