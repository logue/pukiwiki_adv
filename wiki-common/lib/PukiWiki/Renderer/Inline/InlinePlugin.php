<?php
/**
 * インライン型プラグイン変換クラス
 *
 * @package   PukiWiki\Renderer\Inline
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2013 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Plugin.php,v 1.0.0 2013/01/29 19:54:00 Logue Exp $
 */

namespace PukiWiki\Renderer\Inline;

use PukiWiki\Renderer\InlineFactory;
use PukiWiki\Utility;

// Inline plugins
class InlinePlugin extends Inline
{
	var $pattern;
	var $plain,$param;

	public function __construct($start)
	{
		parent::__construct($start);
	}

	public function getPattern()
	{
		$this->pattern =
			'&'.
			 '('.        // (1) plain
			  '(\w+)'.   // (2) plugin name
			   '(?:'.
			   '\('.
			    '((?:(?!\)[;{]).)*)'. // (3) parameter
			   '\)'.
			  ')?'.
			 ')';
		return $this->pattern .
			 '(?:'.
			  '\{'.
			   '((?:(?R)|(?!};).)*)'. // (4) body
			  '\}'.
			 ')?'.
			';';
	}

	public function getCount()
	{
		return 4;
	}

	public function setPattern($arr, $page)
	{
		list($all, $this->plain, $name, $this->param, $body) = $this->splice($arr);

		// Re-get true plugin name and patameters (for PHP 4.1.2)
		$matches = array();
		if (preg_match('/^' . $this->pattern . '/x', $all, $matches)
			&& $matches[1] != $this->plain)
			list(, $this->plain, $name, $this->param) = $matches;

		return parent::setParam($page, $name, $body, 'plugin');
	}

	public function __toString()
	{
		$body = (empty($this->body)) ? null : InlineFactory::factory($this->body);
		$str = FALSE;

		// Try to call the plugin
		if (exist_plugin_inline($this->name))
			$str = do_plugin_inline($this->name, $this->param, $body);

		if ($str !== FALSE) {
			return $str; // Succeed
		} else {
			// No such plugin, or Failed
			$body = (empty($body) ? '' : '{' . $body . '}') . ';';
			return parent::setLineRules(Utility::htmlsc('&' . $this->plain) . $body);
		}
	}
}

/* End of file Plugin.php */
/* Location: /vender/PukiWiki/Lib/Renderer/Inline/Plugin.php */