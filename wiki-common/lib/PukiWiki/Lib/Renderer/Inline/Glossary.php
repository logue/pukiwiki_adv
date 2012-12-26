<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: Glossary.php,v 1.0.0 2012/12/18 11:00:00 Logue Exp $
// Copyright (C)
//   2012 PukiWiki Advance Developers Team
// License: GPL v2 or (at your option) any later version

namespace PukiWiki\Lib\Renderer\Inline;
use PukiWiki\Lib\File\WikiFile;

// Glossary
class Glossary extends Inline
{
	var $forceignorepages = array();
	var $auto;
	var $auto_a; // alphabet only

	function __construct($start)
	{
		global $autoglossary, $cache;

		parent::__construct($start);
		if (! $autoglossary){
			return;
		}else{
			list($auto, $auto_a, $forceignorepages) = $cache['wiki']->getItem(PKWK_GLOSSARY_REGEX_CACHE);
			$this->auto = $auto;
			$this->auto_a = $auto_a;
			$this->forceignorepages = $forceignorepages;
		}
	}
	function get_pattern()
	{
		return isset($this->auto) ? '(' . $this->auto . ')' : FALSE;
	}
	function get_count()
	{
		return 1;
	}
	function set($arr,$page)
	{
		list($name) = $this->splice($arr);
		// Ignore words listed
		if (in_array($name,$this->forceignorepages))
		{
			return FALSE;
		}
		return parent::setParam($page,$name,null,'pagename',$name);
	}
	function toString()
	{
		global $autoglossary;
		if (!$autoglossary) return $this->name;
		return self::make_tooltips($this->name);
	}
	
	function make_tooltips($term){
		global $glossarypage;
		static $tooltip_initialized = FALSE;
		if (!exist_plugin('tooltip')) { return FALSE; }
		if (!$tooltip_initialized) {
			if (do_plugin_init('tooltip') === FALSE) { return FALSE; }
			$tooltip_initialized = TRUE;
		}

		$glossary = plugin_tooltip_get_glossary($term,$glossarypage,FALSE);
		if ( $glossary === FALSE ) {
			$glossary = plugin_tooltip_get_page_title($term);
			if ( $glossary === FALSE ) $glossary = '';
		}
		$s_term = str_replace("'", "\\'", htmlsc($term));
		$s_glossary = htmlsc($glossary);

		$page = strip_bracket($term);
		$wiki = new WikiFile($page);
		if (! $wiki->has() ) {
			return '<abbr aria-describedby="tooltip" title="' . $s_glossary . '">' . $term . '</abbr>';
		}
		return '<a href="' . $wiki->getUri() . '" title="' . $s_glossary. $wiki->passage(false) . '" aria-describedby="tooltip">' . $term . '</a>';
	}
}

class Glossary_Alphabet extends Glossary
{
	function __construct($start)
	{
		parent::__construct($start);
	}
	function get_pattern()
	{
		return isset($this->auto_a) ? '(' . $this->auto_a . ')' : FALSE;
	}
}

/* End of file Glossary.php */
/* Location: /vender/PukiWiki/Lib/Renderer/Inline/Glossary.php */