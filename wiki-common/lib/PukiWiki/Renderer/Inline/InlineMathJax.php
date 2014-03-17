<?php
/**
 * MathJax変換クラス
 *
 * @package   PukiWiki\Renderer\Inline
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2014 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: MathJax.php,v 1.0.1 2014/03/17 19:26:00 Logue Exp $
 */

namespace PukiWiki\Renderer\Inline;

use PukiWiki\Renderer\Inline\InlinePlugin;

class InlineMathJax extends InlinePlugin
{
	public function __construct($start){
		parent::__construct($start);
	}

	public function getPattern(){
		$this->pattern = '(?<!\\\\)\$((.+?))(?<!\\\\)\$';
		return $this->pattern;
	}

	public function getCount(){
		return 2;
	}

	public function setPattern($arr, $page){
		$this->param = "\n";  // flag which means a call from Link_mathjax#toString
		list($all, $body) = $this->splice($arr);
		return parent::setParam($page, 'mathjax', $body, 'plugin');
	}
}