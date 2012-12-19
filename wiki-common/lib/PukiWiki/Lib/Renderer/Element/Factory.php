<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: convert_html.php,v 1.0 2012/10/30 12:02:00 Logue Exp $
// Copyright (C)
//   2010-2012 PukiWiki Advance Developers Team
//   2005-2008 PukiWiki Plus! Team
//   2002-2005, 2007,2011 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// function 'convert_html()', wiki text parser
// and related classes-and-functions
namespace PukiWiki\Lib\Renderer\Element;

use PukiWiki\Lib\Renderer\Element\Inline;
use PukiWiki\Lib\Renderer\Element\DList;
use PukiWiki\Lib\Renderer\Element\Table;
use PukiWiki\Lib\Renderer\Element\YTable;
use PukiWiki\Lib\Renderer\Element\Div;

class Factory{
	public static function & factory($element, $root = '', $text){
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
			case 'Div':
				return self::div($root, $text);
				break;
		}
		return self::inline($text);
	}
	private static function & inline($text){
		// Check the first letter of the line
		if (substr($text, 0, 1) == '~') {
			$ret = new Paragraph(' ' . substr($text, 1));
		} else {
			$ret = new Inline($text);
		}
		return $ret;
	}
	private static function & dList($root, $text){
		$out = explode('|', ltrim($text), 2);
		if (count($out) < 2) {
			$ret = $this->inline($text);
		} else {
			$ret = new DList($out);
		}
		return $ret;
	}
	private static function & table(& $root, $text)
	{
		if (! preg_match('/^\|(.+)\|([hHfFcC]?)$/', $text, $out)) {
			$ret = $this->inline($text);
		} else {
			$ret = new Table($out);
		}
		return $ret;
	}
	private static function & yTable(& $root, $text){
		if ($text == ',') {
			$ret = $this->inline($text);
		} else {
			$ret = new YTable(explode(',', substr($text, 1)));
		}
		return $ret;
	}
	private static function & div(& $root, $text){
		$matches = array();

		if (preg_match('/^#([^\(\{]+)(?:\(([^\r]*)\))?(\{*)/', $text, $matches) && exist_plugin_convert($matches[1])) {
			$len  = strlen($matches[3]);
			$body = array();
			if ($len == 0) {
				$ret = new Div($matches); // Seems legacy block plugin
			} else if (preg_match('/\{{' . $len . '}\s*\r(.*)\r\}{' . $len . '}/', $text, $body)) {
				$matches[2] .= "\r" . $body[1] . "\r";
				$ret = new Div($matches); // Seems multiline-enabled block plugin
			}
		}
		return $ret;
	}
}