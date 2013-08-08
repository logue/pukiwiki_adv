<?php
namespace PukiWiki\Config;

class Sequential extends ConfigTable
{
	// Add a line
	function add_value($value)
	{
		$this->values[] = (count($value) == 1) ? $value[0] : $value;
	}

	function toString($values = NULL, $level = 2)
	{
		$retval[] = trim(join('', $this->before));
		if (is_array($this->values)) {
			foreach ($this->values as $value) {
				$value   = is_array($value) ? join('|', $value) : $value;
				$retval[] = '|' . trim($value) . '|';
			}
		}
		$retval[] = join('', $this->after);
		return join("\n",$retval);
	}
}
