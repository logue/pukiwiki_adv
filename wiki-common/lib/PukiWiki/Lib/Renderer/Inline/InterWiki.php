<?php
/**
 * InterWiki変換クラス
 *
 * @package   PukiWiki\Lib\Renderer\Inline
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2013 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: InterWiki.php,v 1.0.0 2013/01/29 19:54:00 Logue Exp $
 */
namespace PukiWiki\Lib\Renderer\Inline;

/**
 * URLs (InterWiki definition on "InterWikiName")
 */
class InterWiki extends Inline
{
	function __construct($start)
	{
		parent::__construct($start);
	}

	function getPattern()
	{
		return
		'\['.       // open bracket
		'('.        // (1) url
		 '(?:(?:https?|ftp|news):\/\/|\.\.?\/)[!~*\'();\/?:\@&=+\$,%#\w.-]*'.
		')'.
		'\s'.
		'([^\]]+)'. // (2) alias
		'\]';       // close bracket
	}

	function getCount()
	{
		return 2;
	}

	function set($arr, $page)
	{
		list(, $name, $alias) = $this->splice($arr);
		return parent::setParam($page, htmlsc($name), null, 'url', $alias);
	}

	function toString()
	{
		global $nofollow;
		$rel = 'external' . ($nofollow === TRUE) ? ' nofollow': '';
		$target = empty($this->redirect) ? $this->name : $this->redirect.rawurlencode($this->name);

		if (extension_loaded('intl')){
			// Fix punycode URL
			$purl = parse_url($target);
			$url = preg_replace('/'.$purl['host'].'/', idn_to_ascii($purl['host']), $target);
		}else{
			$url = $target;
		}

		return parent::setLink($this->alias, $url, $this->name, $rel);
	}
}

/* End of file InterWiki.php */
/* Location: /vender/PukiWiki/Lib/Renderer/Inline/InterWiki.php */
