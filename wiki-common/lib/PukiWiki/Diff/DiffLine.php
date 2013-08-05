<?php

namespace PukiWiki\Diff;

class DiffLine
{
	private $text;
	private $status;

	public function __construct($text)
	{
		$this->text   = $text . "\n";
		$this->status = array();
	}

	public function compare($obj)
	{
		return $this->text == $obj->text;
	}

	public function set($key, $status)
	{
		$this->status[$key] = $status;
	}

	public function get($key)
	{
		return isset($this->status[$key]) ? $this->status[$key] : '';
	}

	public function merge($obj)
	{
		$this->status += $obj->status;
	}

	public function text()
	{
		return $this->text;
	}
}

