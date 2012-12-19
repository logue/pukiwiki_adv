<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone
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
use PukiWiki\Lib\Renderer\Element\Factory;

// Body
class Body extends Element
{
	var $id;
	var $count = 0;
	var $contents;
	var $contents_last;
	var $classes = array(
		'-' => 'UList',
		'+' => 'OList',
		'>' => 'Blockquote',
		'<' => 'Blockquote');
	var $factories = array(
		':' => 'DList',
		'|' => 'Table',
		',' => 'YTable',
		'#' => 'Div');

	function __construct($id)
	{
		$this->id            = $id;
		$this->contents      = new Element();
		$this->contents_last = $this->contents;
		parent::__construct();
	}

	function parse($lines)
	{
		$this->last = & $this;
		$matches = array();

		while (! empty($lines)) {
			$line = array_shift($lines);

			// Escape comments
			if (substr($line, 0, 2) == '//') continue;

			// Extend TITLE by miko
			if (preg_match('/^(TITLE):(.*)$/',$line,$matches))
			{
				global $newtitle;
				static $newbase;
				if (empty($newbase)) {
					$newbase = trim(strip_htmltag(convert_html($matches[2])));
					// For BugTrack/132.
					$newtitle = htmlsc($newbase);
				}
				continue;
			}

			if (preg_match('/^(LEFT|CENTER|RIGHT):(.*)$/', $line, $matches)) {
				// <div style="text-align:...">
				$this->last = $this->last->add(new Align(strtolower($matches[1])));
				if (empty($matches[2])) continue;
				$line = $matches[2];
			}

			$line = rtrim($line, "\r\n");

			// Empty
			if ( empty($line) ) {
				$this->last = & $this;
				continue;
			}

			// Horizontal Rule
			if (substr($line, 0, 4) == '----') {
				$this->insert(new HRule($this, $line));
				continue;
			}
/*
			// Multiline-enabled block plugin
			if (! PKWKEXP_DISABLE_MULTILINE_PLUGIN_HACK &&
			    preg_match('/^#[^{]+(\{\{+)\s*$/', $line, $matches)) {
				$len = strlen($matches[1]);
				$line .= "\r"; // Delimiter
				while (! empty($lines)) {
					$next_line = preg_replace("/[\r\n]*$/", '', array_shift($lines));
					if (preg_match('/\}{' . $len . '}/', $next_line)) {
						$line .= $next_line;
						break;
					} else {
						$line .= $next_line .= "\r"; // Delimiter
					}
				}
			}
*/
			// The first character
			$head = $line{0};

			// Heading
			if ($head === '*') {
				$this->insert(new Heading($this, $line));
				continue;
			}

			// Pre
			if ($head === ' ' || $head === "\t") {
				$this->last = $this->last->add(new Pre($this, $line));
				continue;
			}

			// CPre (Plus!)
			if (substr($line,0,2) === '# ' or substr($line,0,2) == "#\t") {
				$this->last = &$this->last->add(new CPre($this,$line));
				continue;
			}

			// Line Break
			if (substr($line, -1) === '~')
				$line = substr($line, 0, -1) . "\r";

			// Other Character
			if (isset($this->classes[$head]) && gettype($this->last) === 'object') {
				$classname  = 'PukiWiki\Lib\Renderer\Element\\' . $this->classes[$head];
				$this->last = $this->last->add(new $classname($this, $line));
				continue;
			}

			// Other Character
			if (isset($this->factories[$head]) && gettype($this->last) === 'object') {
				$this->last = $this->last->add(Factory::factory($this->factories[$head], $this, $line));
				continue;
				//$factoryname = 'Factory_' . $this->factories[$head];
				//$this->last  = $this->last->add($factoryname($this, $line));
				//continue;
			}

			// Default
			if (gettype($this->last) === 'object'){
				$this->last = $this->last->add(Factory::factory('Inline', null, $line));
			}
		}
	}

	function getAnchor($text, $level)
	{
		global $top, $_symbol_anchor;
		global $fixed_heading_edited;	// Plus!

		// Heading id (auto-generated)
		$autoid = 'content_' . $this->id . '_' . $this->count;
		$this->count++;
		$anchor = '';

		// Heading id (specified by users)
		$id = make_heading($text, FALSE); // Cut fixed-anchor from $text
		if (empty($id)) {
			// Not specified
			$id     = $autoid;
		} else {
			//$anchor = ' &aname(' . $id . ',super,full){' . $_symbol_anchor . '};';
			//if ($fixed_heading_edited) $anchor .= " &edit(,$id);";
			if ($fixed_heading_edited) $anchor = " &edit(,$id);";
		}

		$text = ' ' . $text;

		// Add 'page contents' link to its heading
		//$this->contents_last = $this->contents_last->add(new Contents_UList($text, $level, $id));

		// Add heding
		return array($text . $anchor, $this->count > 1 ? "\n" . $top : '', $autoid);
	}

	function insert(& $obj)
	{
		if ($obj instanceof Inline) $obj = & $obj->toPara();
		return parent::insert($obj);
	}

	function toString()
	{
		$text = parent::toString();

		// #contents
		return  preg_replace_callback('/<#_contents_>/',
			array($this, 'replace_contents'), $text). "\n";
	}

	function replace_contents($arr)
	{
		$contents  = '<div class="contents" id="contents_' . $this->id . '">' . "\n" .
			$this->contents->toString() .
				'</div>' . "\n";
		return $contents;
	}
}