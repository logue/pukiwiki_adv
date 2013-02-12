<?php
/**
 * 要素ファクトリークラス
 *
 * @package   PukiWiki\Lib\Renderer\Element
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013 PukiWiki Advance Developers Team
 * @create    2013/01/26
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: ElementFactory.php,v 1.0.0 2013/02/12 15:13:00 Logue Exp $
 */
namespace PukiWiki\Lib\Renderer\Element;

use PukiWiki\Lib\Renderer\Element\BlockPlugin;
use PukiWiki\Lib\Renderer\Element\DList;
use PukiWiki\Lib\Renderer\Element\InlineElement;
use PukiWiki\Lib\Renderer\Element\Table;
use PukiWiki\Lib\Renderer\Element\YTable;

class ElementFactory{
	public static function & factory($element, $root, $text){
		if (empty($root)) return self::inline($text);
		switch($element){
			case 'DList':
				return self::dList($root, $text);
				break;
			case 'Table':
				return self::table($root, $text);
				break;
			case 'YTable':
				return self::yTable($root, $text);
				break;
			case 'Plugin':
				return self::plugin($root, $text);
				break;
		}
		// 'InlineElement'
		return self::inline($text);
	}
	private static function & inline($text){
		// Check the first letter of the line
		if (substr($text, 0, 1) == '~') {
			$ret = new Paragraph(' ' . substr($text, 1));
		} else {
			$ret = new InlineElement($text);
		}
		return $ret;
	}
	private static function & dList($root, $text){
		$out = explode('|', ltrim($text), 2);
		if (count($out) < 2) {
			$ret = self::inline($text);
		} else {
			$ret = new DList($out);
		}
		return $ret;
	}
	private static function & table(& $root, $text)
	{
		if (! preg_match('/^\|(.+)\|([hHfFcC]?)$/', $text, $out)) {
			$ret = self::inline($text);
		} else {
			$ret = new Table($out);
		}
		return $ret;
	}
	private static function & yTable(& $root, $text){
		if ($text == ',') {
			$ret = self::inline($text);
		} else {
			$ret = new YTable(explode(',', substr($text, 1)));
		}
		return $ret;
	}
	private static function & plugin(& $root, $text){
		$matches = array();

		if (preg_match('/^#([^\(\{]+)(?:\(([^\r]*)\))?(\{*)/', $text, $matches) && exist_plugin_convert($matches[1])) {
			$len  = strlen($matches[3]);
			$body = array();
			if ($len == 0) {
				$ret = new BlockPlugin($matches);   // Seems legacy block plugin
			} else if (preg_match('/\{{' . $len . '}\s*\r(.*)\r\}{' . $len . '}/', $text, $body)) {
				$matches[2] .= "\r" . $body[1] . "\r";
				$ret = new BlockPlugin($matches);   // Seems multiline-enabled block plugin
			}
		}
		return $ret;
	}
}

/* End of file ElementFactory.php */
/* Location: /vendor/PukiWiki/Lib/Renderer/Element/ElementFactory.php */