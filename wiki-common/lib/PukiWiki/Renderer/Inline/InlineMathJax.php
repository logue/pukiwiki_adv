<?php
/**
 * MathJax変換クラス
 *
 * @package   PukiWiki\Renderer\Inline
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2013 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: MathJax.php,v 1.0.0 2013/04/15 17:48:00 Logue Exp $
 */

namespace PukiWiki\Renderer\Inline;

use PukiWiki\Renderer\Inline\InlinePlugin;

class InlineMathJax extends InlinePlugin
{
	function __construct($start){
		parent::__construct($start);
	}

	function getPattern(){
		$this->pattern = '(?<!\\\\)\$((.+?))(?<!\\\\)\$';
		return $this->pattern;
	}

	function getCount(){
		return 2;
	}

	function setPattern($arr, $page){
		list($all, $this->plain, $this->param) = $this->splice($arr);

		$matches = array();
		return parent::setParam($page, 'tex', '', 'plugin');
	}
}