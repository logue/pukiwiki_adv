<?php
/**
 * MathJax Plugin for PukiWiki
 * $Id: mathjax.inc.php,v 0.02 2013/02/17 20:20 abicky Exp $
 *
 * @author     abicky
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link       https://github.com/abicky/mathjax_for_pukiwiki
 * @version    0.02
 */

namespace PukiWiki\Renderer;

/**
 * MathJaxクラス
 */
class MathJax
{
	const MATHJAX_URL = 'http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS_HTML';
	const MATHJAX_USAGE = '#mathjax(tex mathmatical expression) or #mathjax(css styles)';
	const MATHJAX_INLINE_USAGE = '&amp;mathjax([css styles]){[tex mathmatical expression]};';
	const MATHJAX_DEFAULT_ALIGN = 'left';  // 'left', 'center', 'right'
	const MATHJAX_CONF = '
MathJax.Hub.Config({
    displayAlign: "inherit",
    TeX: {
        Macros: {
            bm: ["\\\\boldsymbol{#1}", 1],
            argmax: ["\\\\mathop{\\\\rm arg\\\\,max}\\\\limits"],
            argmin: ["\\\\mathop{\\\\rm arg\\\\,min}\\\\limits"],
        },
        extensions: ["autobold.js", "color.js"],
        equationNumbers: {
             //autoNumber: "all"
        }
    },
    tex2jax: {
        ignoreClass: ".*",
        processClass: "mathjax-eq"
    }
});
';

	private static $_is_initialized = false;
	private static $_style = '';
	private static $_inline_style = '';
	// CSS rarely includes these symbols and LaTex equations frequently include them
	private static $_eq_symbols = array('\\', '_', '^', '=');

	public static function inline($args)
	{
		$eq = array_pop($args);
		if ($eq ||
			self::_is_equation($args)) {  // to show usage
			if (count($args) == 1 && $args[0] == "\n") {
				// called from Link_mathjax#toString, so make an empty array
				$args = array();
			}
			return self::_generate_inline_equation($eq, $args);
		} else {
			self::_set_inline_style($args);
			return;
		}
	}

	public static function block($args)
	{
		if (self::_is_equation($args)) {
			$eq = implode(',', $args);
			return self::_generate_equation($eq);
		} else {
			self::_set_style($args);
			return;
		}
	}

	public static function make_style($args)
	{
		$style = implode(';', $args);
		if (strpos($style, "text-align") === false) {
			$style .= ';text-align:' . self::MATHJAX_DEFAULT_ALIGN . ';';
		}
		return $style;
	}

	public static function make_inline_style($args)
	{
		return implode(';', $args);
	}

	private static function _generate_equation($eq)
	{
		if ($eq) {
			$eq = ltrim($eq);
			if (substr($eq, 0, 1) != '\\' ||
				(strpos($eq, '\\[') !== 0 && strpos($eq, '\\begin') !== 0)) {
				$text = "\\[ $eq \\]";
			} else {
				$text = $eq;
			}
			$style = self::$_style;
		} else {
			$text = 'usage:' . self::MATHJAX_USAGE;
			$style = 'color: red;';
		}
		return self::_make_tag('div', $text, $style, 'img_margin');
	}

	private static function _generate_inline_equation($eq, $args)
	{
		if ($eq) {
			$text = "\\( $eq \\)";
			$style = empty($args) ? self::$_inline_style : self::make_inline_style($args);
		} else {
			$text = 'usage: ' . self::MATHJAX_INLINE_USAGE;
			$style = 'color: red;';
		}
		return self::_make_tag('span', $text, $style);
	}

	private static function _set_style($args)
	{
		self::$_style = self::make_style($args);
	}

	private static function _set_inline_style($args)
	{
		self::$_inline_style = self::make_inline_style($args);
	}

	private static function _make_tag($tag_name, $text, $style, $class = '')
	{
		return '<'.$tag_name.' class="mathjax-eq '. $class . '" style="'.$style.'">' .$text . '</' . $tag_name .'>';
	}

	private static function _is_equation($args)
	{
		if (empty($args)) {
			// empty means reset of styles
			return false;
		}

		// check only the first argument
		if (strpos($args[0], ':') !== false && !self::_has_eq_symbol($args[0])) {
			return false;
		}

		return true;
	}

	private static function _has_eq_symbol($str)
	{
		foreach (self::$_eq_symbols as $symbol) {
			if (strpos($str, $symbol) !== false) {
				return true;
			}
		}
		return false;
	}

	// for compatibility to tex plugin
	private static function _extract_equation($args)
	{
		$is_array = is_array($args);
		if ($is_array) {
			$args = implode($args, ',');
		}
		// extract equation ('$foo $ bar$' -> 'foo $ bar')
		preg_match('/^\$(.*)\$.*?(.*)/', ltrim($args), $matches);
		$eq = $matches[1];
		$args = $matches[2];
		if ($is_array) {
			$args = explode(',', $args);
		}
		return array($eq, $args);
	}
}