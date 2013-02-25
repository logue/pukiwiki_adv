<?php
namespace PukiWiki\Config;

// Class holds array values
class ConfigTable
{
	var $title  = '';	// Table title
	var $before = array();	// Page contents (except table ones)
	var $after  = array();	// Page contents (except table ones)
	var $values = array();	// Table contents

	function __construct($title, $obj = NULL)
	{
		if ($obj !== NULL) {
			$this->title  = $obj->title;
			$this->before = array_merge($obj->before, $obj->after);
		} else {
			$this->title  = trim(substr($title, strspn($title, '*')));
			$this->before[] = $title;
		}
	}

	// Add an  explanation
	function add_line($line)
	{
		$this->after[] = $line;
	}

	function toString($values = NULL, $level = 2)
	{
		return join('', $this->before) . join('', $this->after);
	}
}