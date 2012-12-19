<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: RendererFactory.php,v 1.0.0 2012/12/18 11:00:00 Logue Exp $
// Copyright (C)
//   2012 PukiWiki Advance Developers Team
// License: GPL v2 or (at your option) any later version

namespace PukiWiki\Lib\Renderer;
use PukiWiki\Lib\Renderer\Element\Body;

class RendererFactory{
	public static function factory($lines){
		static $id;
		$body = new Body(++$id);
		$body->parse($lines);
		return $body->toString();
	}
}

/* End of file RendererFactory.php */
/* Location: ./vender/PukiWiki/Lib/Renderer/RendererFactory.php */