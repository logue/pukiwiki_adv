<?php
/**
 * 要素ファクトリークラス
 *
 * @package   PukiWiki\Renderer\Element
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013-2014 PukiWiki Advance Developers Team
 * @create    2013/01/26
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: ElementFactory.php,v 1.0.2 2014/07/05 20:02:00 Logue Exp $
 */
namespace PukiWiki\Renderer\Element;

use PukiWiki\Renderer\Element\BlockPlugin;
use PukiWiki\Renderer\Element\DList;
use PukiWiki\Renderer\Element\InlineElement;
use PukiWiki\Renderer\Element\Table;
use PukiWiki\Renderer\Element\YTable;
use PukiWiki\Renderer\PluginRenderer;

class ElementFactory{
	public static function & factory($element, $root, $text, $is_guiedit = false){
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
				return self::plugin($root, $text, $is_guiedit);
				break;
		}
		// 'InlineElement'
		return self::inline($text, $is_guiedit);
	}
	private static function & inline($text, $is_guiedit = false){
		// Check the first letter of the line
		if (substr($text, 0, 1) === '~') {
			$ret = new Paragraph(' ' . substr($text, 1), $is_guiedit);
		} else {
			$ret = new InlineElement($text, $is_guiedit);
		}
		return $ret;
	}
	private static function & dList($root, $text, $is_guiedit = false){
		$out = explode('|', ltrim($text), 2);
		if (count($out) < 2) {
			$ret = self::inline($text, $is_guiedit);
		} else {
			$ret = new DList($out);
		}
		return $ret;
	}
	private static function & table(& $root, $text, $is_guiedit = false)
	{
		if (! preg_match('/^\|(.+)\|([hHfFcC]?)$/', $text, $out)) {
			$ret = self::inline($text, $is_guiedit);
		} else {
			$ret = new Table($out);
		}
		return $ret;
	}
	private static function & yTable(& $root, $text, $is_guiedit = false){
		if ($text === ',') {
			$ret = self::inline($text, $is_guiedit);
		} else {
			$ret = new YTable(explode(',', substr($text, 1)));
		}
		return $ret;
	}
	private static function & plugin(& $root, $text, $is_guiedit = false){
		$matches = array();

		if (preg_match('/^#([^\(\{]+)(?:\(([^\r]*)\))?(\{*)/', $text, $matches) && PluginRenderer::hasPluginMethod($matches[1], 'convert')) {
			
			$len  = strlen($matches[3]);
			$body = array();
			if ($len === 0) {
				if ($is_guiedit){
					$ret = new BlockPluginDummy($matches);
				}else{
					$ret = new BlockPlugin($matches);   // Seems legacy block plugin
				}
			} else if (preg_match('/\{{' . $len . '}\s*\r(.*)\r\}{' . $len . '}/', $text, $body)) {
				$matches[2] .= "\r" . $body[1] . "\r";
				if ($is_guiedit){
					$ret = new BlockPluginDummy($matches);
				}else{
					$ret = new BlockPlugin($matches);   // Seems multiline-enabled block plugin
				}
			}
		}
		return $ret;
	}
	private static function & pluginDummy(& $root, $text){
		$matches = array();

		if (preg_match('/^#([^\(\{]+)(?:\(([^\r]*)\))?(\{*)/', $text, $matches) && PluginRenderer::hasPluginMethod($matches[1], 'convert')) {
			$len  = strlen($matches[3]);
			$body = array();
			if ($len === 0) {
				$ret = new BlockPluginDummy($matches);   // Seems legacy block plugin
			} else if (preg_match('/\{{' . $len . '}\s*\r(.*)\r\}{' . $len . '}/', $text, $body)) {
				$matches[2] .= "\r" . $body[1] . "\r";
				$ret = new BlockPluginDummy($matches);   // Seems multiline-enabled block plugin
			}
		}
		return $ret;
	}
}

/* End of file ElementFactory.php */
/* Location: /vendor/PukiWiki/Lib/Renderer/Element/ElementFactory.php */