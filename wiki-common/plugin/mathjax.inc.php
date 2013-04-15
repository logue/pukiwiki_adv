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

use PukiWiki\Renderer\MathJax;

function plugin_mathjax_inline()
{
    return MathJax::inline(func_get_args());
}

function plugin_mathjax_convert()
{
    return MathJax::block(func_get_args());
}