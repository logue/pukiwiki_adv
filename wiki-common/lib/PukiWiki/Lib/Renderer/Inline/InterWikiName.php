<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: InterWikiName.php,v 1.0.0 2012/12/18 11:00:00 Logue Exp $
// Copyright (C)
//   2012 PukiWiki Advance Developers Team
// License: GPL v2 or (at your option) any later version

namespace PukiWiki\Lib\Renderer\Inline;
use PukiWiki\Lib\File\FileFactory;
use PukiWiki\Lib\Router;

// InterWikiName-rendered URLs
class InterWikiName extends Inline
{
	const INTERWIKINAME_PATTERN = '/\[((?:(?:https?|ftp|news):\/\/|\.\.?\/)[!~*\'();\/?:\@&=+\$,%#\w.-]*)\s([^\]]+)\]\s?([^\s]*)/';
	const INTERWIKINAME_ICON = '<span class="pkwk-icon icon-interwiki"></span>';
	const INTERWIKINAME_CACHE = 'interwikiname';
	
	var $url    = '';
	var $param  = '';
	var $anchor = '';

	function __construct($start)
	{
		global $cache;
		$this->cache = $cache['wiki'];
		parent::__construct($start);
	}

	function get_pattern()
	{
		$s2 = $this->start + 2;
		$s5 = $this->start + 5;
		return
			'\[\['.                  // open bracket
			'(?:'.
			 '((?:(?!\]\]).)+)>'.    // (1) alias
			')?'.
			'(\[\[)?'.               // (2) open bracket
			'((?:(?!\s|:|\]\]).)+)'. // (3) InterWiki
			'(?<! > | >\[\[ )'.      // not '>' or '>[['
			':'.                     // separator
			'('.                     // (4) param
			 '(\[\[)?'.              // (5) open bracket
			 '(?:(?!>|\]\]).)+'.
			 '(?(' . $s5 . ')\]\])'. // close bracket if (5)
			')'.
			'(?(' . $s2 . ')\]\])'.  // close bracket if (2)
			'\]\]';                  // close bracket
	}

	function get_count()
	{
		return 5;
	}

	function set($arr, $page)
	{
		list(, $alias, , $name, $this->param) = $this->splice($arr);

		$matches = array();
		if (preg_match('/^([^#]+)(#[A-Za-z][\w-]*)$/', $this->param, $matches))
			list(, $this->param, $this->anchor) = $matches;

		$url = self::get_interwiki_url($name, $this->param);
		$this->url = ($url === FALSE) ?
			get_page_uri($name . ':' . $this->param) :
			htmlsc($url);

		return parent::setParam(
			$page,
			htmlsc($name . ':' . $this->param),
			null,
			'InterWikiName',
			empty($alias) ? $name . ':' . $this->param : $alias
		);
	}

	function toString()
	{
		global $nofollow;
		$icon = parent::is_inside_uri($this->url) ? parent::INTERNAL_LINK_ICON : parent::EXTERNAL_LINK_ICON;
		$target = (empty($this->redirect)) ? $this->url : $this->redirect.rawurlencode($this->url);

		return '<a href="' . $target . $this->anchor . '" title="' . $this->name . '" rel="' . ($nofollow === FALSE ? 'external' : 'external nofollow') . '">'. self::INTERWIKINAME_ICON . $this->alias . $icon . '</a>';
	}
	
	// Render an InterWiki into a URL
	private function get_interwiki_url($name, $param)
	{
		global $WikiName, $interwiki;
		static $encode_aliases = array('sjis'=>'SJIS', 'euc'=>'EUC-JP', 'utf8'=>'UTF-8', 'gbk'=>'CP936', 'euckr'=>'EUC-KR', 'big5'=>'BIG5');
		static $interwikinames;

		if (!isset($interwikinames)){
			// キャッシュ処理
			$interwikipage = FileFactory::Wiki($interwiki);
			$cache_meta = $this->cache->getMetadata(self::INTERWIKINAME_CACHE);
			if ($this->cache->hasItem(self::INTERWIKINAME_CACHE) && $cache_meta['mtime'] > $interwikipage->getTime()) {
				$interwikinames = $this->cache->getItem(self::INTERWIKINAME_CACHE);
			}else{
				// キャッシュが存在してなかったり、定義ページより古い場合は生成。
				$interwikinames = $matches = array();
				foreach ($interwikipage->source() as $line)
					if (preg_match(self::INTERWIKINAME_PATTERN, $line, $matches))
						$interwikinames[$matches[2]] = array($matches[1], $matches[3]);
				$this->cache->setItem(self::INTERWIKINAME_CACHE, $interwikinames);
			}
		}

		if (! isset($interwikinames[$name])) return FALSE;

		list($url, $opt) = $interwikinames[$name];

		// Encoding
		switch ($opt) {

			case '':    /* FALLTHROUGH */
			case 'std': // Simply URL-encode the string, whose base encoding is the internal-encoding
				$param = rawurlencode($param);
				break;

			case 'asis': /* FALLTHROUGH */
			case 'raw' : // Truly as-is
				break;

			case 'yw': // YukiWiki
				if (! preg_match('/' . $WikiName . '/', $param))
					$param = '[[' . mb_convert_encoding($param, 'SJIS', SOURCE_ENCODING) . ']]';
				break;

			case 'moin': // MoinMoin
				$param = str_replace('%', '_', rawurlencode($param));
				break;

			default:
				// Alias conversion of $opt
				if (isset($encode_aliases[$opt])) $opt = & $encode_aliases[$opt];

				// Encoding conversion into specified encode, and URLencode
				$param = rawurlencode(mb_convert_encoding($param, $opt, SOURCE_ENCODING));
		}

		// Replace or Add the parameter
		if (strpos($url, '$1') !== FALSE) {
			$url = str_replace('$1', $param, $url);
			//$url = strtr($url, '$1', $param);
		} else {
			$url .= $param;
		}

		$len = strlen($url);
		if ($len > 512) die_message('InterWiki URL too long: ' . $len . ' characters');

		return $url;
	}
}

/* End of file InterWikiName.php */
/* Location: /vender/PukiWiki/Lib/Renderer/Inline/InterWikiName.php */