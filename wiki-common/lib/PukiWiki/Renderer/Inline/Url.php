<?php
/**
 * URL変換クラス
 *
 * @package   PukiWiki\Renderer\Inline
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2013 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Url.php,v 1.0.0 2013/01/29 19:54:00 Logue Exp $
 */

namespace PukiWiki\Renderer\Inline;

use PukiWiki\Utility;

// URLs
class Url extends Inline
{
	public function __construct($start)
	{
		parent::__construct($start);
	}

	public function getPattern()
	{
		$s1 = $this->start + 1;
		return
			'(\[\['.                // (1) open bracket
			 '((?:(?!\]\]).)+)'.    // (2) alias
			 '(?:>|:)'.
			')?'.
			'('.                    // (3) scheme
			 '(?:(?:https?|ftp|news|site):\/\/|mailto:)'.
			')'.
			'([\w.-]+@)?'.          // (4) mailto name
			'([^\/"<>\s]+|\/)'.     // (5) host
			'('.                    // (6) URI
			 '[\w\/\@\$()!?&%#:;.,~\'=*+-]*'.
			')'.
			'(?(' . $s1 . ')\]\])'; // close bracket
	}

	public function getCount()
	{
		return 6;
	}

	public function setPattern($arr, $page)
	{
		list (,$bracket, $alias, $scheme, $mail, $host, $uri) = $this->splice($arr);
		$this->has_bracket = (substr($bracket, 0, 2) === '[[');
		$this->host = $host;
		if (extension_loaded('intl') && $host !== '/' && preg_match('/[^A-Za-z0-9.-]/', $host)) {
			$host = idn_to_ascii($host);
		}
		$name = $scheme . $mail . $host;
		// https?:/// -> $this->cont['ROOT_URL']
		$name = preg_replace('#^(?:site:|https?:/)//#', ROOT_URI, $name) . $uri;
		if (!$alias) {
			// Punycode化されたドメインかを判別
			$alias = (extension_loaded('intl') && strtolower(substr($host, 0, 4)) === 'xn--') ?
				($scheme . $mail . idn_to_utf8($host) . $uri)
				: $name;
			if (strpos($alias, '%') !== FALSE) {
				$alias = mb_convert_encoding(rawurldecode($alias), SOURCE_ENCODING , 'AUTO');
			}
		}
		$this->alias = $alias;
		return parent::setParam($page, Utility::htmlsc($name), '', ($mail ? 'mailto' : 'url'), $alias);
	}

	public function __toString()
	{
		global $nofollow;
		$target = (empty($this->redirect)) ? $this->name : $this->redirect.rawurlencode($this->name);
		return parent::setLink($this->alias, $target, $this->name, $nofollow === FALSE ? '' : 'nofollow');
	}
}

/* End of file Url.php */
/* Location: /vender/PukiWiki/Lib/Renderer/Inline/Url.php */