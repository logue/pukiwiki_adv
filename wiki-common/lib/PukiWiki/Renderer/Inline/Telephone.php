<?php
/**
 * 電話番号変換クラス
 *
 * @package   PukiWiki\Renderer\Inline
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2014 PukiWiki Advance Developers Team
 * @create    2014/04/24
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Telephone.php,v 1.0.0 2014/04/24 23:27:00 Logue Exp $
 */

namespace PukiWiki\Renderer\Inline;

use PukiWiki\Renderer\RendererDefines;

// tel: URL schemes
class Telephone extends Inline
{
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
			'tel:(([0-9]+-?)?[0-9]+-?[0-9]+)'. // (2) telephone
			'(?(' . $s1 . ')\]\])';	        // close bracket if (1)
	}

	public function getCount()
	{
		return 2;
	}

	public function setPattern($arr, $page)
	{
		list (, $alias, $tel) = $this->splice($arr);
		$name = $orginalname = $tel;
		return parent :: setParam($page, $name, '', 'tel', $alias === '' ? $orginalname : $alias);
	}

	public function __toString()
	{
		return '<a href="tel:' . $this->name . '" rel="nofollow">' . RendererDefines::TELEPHONE_ICON . $this->alias . '</a>';
	}
}

/* End of file Telephone.php */
/* Location: /vendor/PukiWiki/Lib/Renderer/Inline/Telephone.php */