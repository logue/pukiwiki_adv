<?php
/**
 * 簡易表記
 *
 * @package   PukiWiki\Renderer\Inline
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2014 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: EasyRef.php,v 1.0.1 2014/03/17 19:23:00 Logue Exp $
 */

namespace PukiWiki\Renderer\Inline;

use PukiWiki\Renderer\PluginRenderer;
use PukiWiki\Renderer\InlineFactory;

/**
 * 簡易表記 {{param|body}}
 * from XpWiki
 */
class EasyRef extends Inline {
	protected $pattern;
	protected $plain, $param;
	public function __construct($start) {
		parent::__construct($start);
	}
	public function getPattern() {
		return
			'\{\{'.
			 '(.*?)'.   // (1) parameter
			 '(?:\|'.
			  '(.*?)'.  // (2) body (optional)
			 ')?'.
			'\}\}';
	}
	public function getCount() {
		return 2;
	}
	public function setPattern($arr, $page) {
		list (, $this->param, $body) = $this->splice($arr);
		$this->param = trim($this->param);
		return parent::setParam($page, 'ref', $body, 'plugin');
	}
	public function __toString() {
		$body = empty($this->body) ? '' : InlineFactory::factory($this->body);
		return PluginRenderer::executePluginInline($this->name, $this->param, $body);
	}
}

/* End of file EasyRef.php */
/* Location: /vendor/PukiWiki/Lib/Renderer/Inline/EasyRef.php */