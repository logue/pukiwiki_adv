<?php
/**
 * メールアドレス変換クラス
 *
 * @package   PukiWiki\Renderer\Inline
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2013 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Mailto.php,v 1.0.0 2013/01/29 19:54:00 Logue Exp $
 */

namespace PukiWiki\Renderer\Inline;

// mailto: URL schemes
class Mailto extends Inline
{
	const MAILTO_ICON = '<span class="pkwk-icon icon-mail" title="Mail">mailto:</span>';

	public function __construct($start)
	{
		parent::__construct($start);
	}

	public function getPattern()
	{
		$s1 = $this->start + 1;
		return
			'(?:'.
			 '\[\['.
			 '((?:(?!\]\]).)+)(?:>|:)'.     // (1) alias
			')?'.
			'([\w.-]+@)'.                   // (2) toname
			'([^\/"<>\s]+\.[A-Za-z0-9-]+)'. // (3) host
			'(?(' . $s1 . ')\]\])';	        // close bracket if (1)
	}

	public function getCount()
	{
		return 3;
	}

	public function setPattern($arr, $page)
	{
		list (, $alias, $toname, $host) = $this->splice($arr);
		$name = $orginalname = $toname . $host;
		if (extension_loaded('intl')) {
			// 国際化ドメイン対応
			if (preg_match('/[^A-Za-z0-9.-]/', $host)) {
				$name = $toname . idn_to_ascii($host);
			} else if (!$alias && strtolower(substr($host, 0, 4)) === 'xn--') {
				$orginalname = $toname . idn_to_utf8($host);
			}
		}
		return parent :: setParam($page, $name, '', 'mailto', $alias === '' ? $orginalname : $alias);
	}

	public function __toString()
	{
		return '<a href="mailto:' . $this->name . '" rel="nofollow">' . self::MAILTO_ICON . $this->alias . '</a>';
	}
}

/* End of file Mailto.php */
/* Location: /vendor/PukiWiki/Lib/Renderer/Inline/Mailto.php */