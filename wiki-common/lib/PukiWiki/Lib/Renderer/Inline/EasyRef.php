<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: EasyRef.php,v 1.0.0 2012/12/26 09:48:00 Logue Exp $
// Copyright (C)
//   2012 PukiWiki Advance Developers Team
// License: GPL v2 or (at your option) any later version

namespace PukiWiki\Lib\Renderer\Inline;
use PukiWiki\Lib\Renderer\InlineFactory;

// 画像簡易表記 {{param|body}}
// from XpWiki
class EasyRef extends Inline {
	var $pattern;
	var $plain, $param;

	function __construct($start) {
		parent::__construct($start);
	}

	function get_pattern() {
		return
			'\{\{'.
			 '(.*?)'.   // (1) parameter
			 '(?:\|'.
			  '(.*?)'.  // (2) body (optional)
			 ')?'.
			'\}\}';
	}

	function get_count() {
		return 2;
	}

	function set($arr, $page) {
		list ($all, $this->param, $body) = $this->splice($arr);
		$this->param = trim($this->param);
		return parent::setParam($page, 'ref', $body, 'plugin');
	}

	function toString() {
		$body = empty($this->body) ? '' : InlineFactory($this->body);
		return do_plugin_inline($this->name, $this->param, $body);
	}
}

/* End of file EasyRef.php */
/* Location: /vender/PukiWiki/Lib/Renderer/Inline/EasyRef.php */