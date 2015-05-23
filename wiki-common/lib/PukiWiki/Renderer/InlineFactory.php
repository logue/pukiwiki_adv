<?php
/**
 * インライン変換ファクトリークラス
 *
 * @package   PukiWiki\Renderer
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2014 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: InlineFactory.php,v 1.0.1 2014/09/03 19:08:00 Logue Exp $
 */
namespace PukiWiki\Renderer;

use PukiWiki\Renderer\InlineConverter;
use PukiWiki\Renderer\InlineConverterEx;

class InlineFactory{
	private static $converter, $converter_ex;
	public static function factory($string, $page = '', $is_guiedit = false){
		global $vars;

		if (is_array($string)) $string = join("\n", $string);	// ポカミス用

		if ($is_guiedit) {
			// GuiEdit用
			if (!isset(self::$converter_ex)) $converter_ex = new InlineConverterEx();
			return $converter_ex->convert($line);
		}
		if (!isset(self::$converter)) self::$converter = new InlineConverter();
		$clone = self::$converter->getClone(self::$converter);

		return $clone->convert($string, isset($vars['page']) ? $vars['page'] : $page );
	}
}

/* End of file InlineFactory.php */
/* Location: ./vendor/PukiWiki/Lib/Renderer/InlineFactory.php */