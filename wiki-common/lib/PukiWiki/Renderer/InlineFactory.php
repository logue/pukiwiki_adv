<?php
/**
 * インライン変換ファクトリークラス
 *
 * @package   PukiWiki\Renderer
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2013 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: InlineFactory.php,v 1.0.1 2014/05/30 19:57:00 Logue Exp $
 */
namespace PukiWiki\Renderer;

use PukiWiki\Renderer\InlineConverter;
use PukiWiki\Renderer\InlineConverterEx;

class InlineFactory{
	public static function factory($string, $page = '', $is_guiedit = false){
		global $vars;
		static $converter, $converter_ex;

		if (is_array($string)) $string = join("\n", $string);	// ポカミス用

		if ($is_guiedit) {
			// GuiEdit用
			if (!isset($converter_ex)) $converter_ex = new InlineConverterEx();
			return $converter_ex->convert($line);
		}
		if (!isset($converter)) $converter = new InlineConverter();
		$clone = $converter->getClone($converter);

		return $clone->convert($string, !isset($vars['page']) ? $page : $vars['page']);
	}
}

/* End of file InlineFactory.php */
/* Location: ./vender/PukiWiki/Lib/Renderer/InlineFactory.php */