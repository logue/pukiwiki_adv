<?php
namespace PukiWiki\Config;

class Direct extends ConfigTable
{
	var $_keys = array();	// Used at initialization phase

	function set_key($line)
	{
		$level = strspn($line, '*');
		$this->_keys[$level] = trim(substr($line, $level));
	}

	// Add a line
	function add_value($line)
	{
		$level = strspn($line, '-');
		$arr   = $this->values;
		for ($n = 2; $n <= $level; $n++)
			$arr = $arr[$this->_keys[$n]];
		$arr[] = trim(substr($line, $level));
	}

	function toString($values = NULL, $level = 2)
	{
		$retval = array();
		$root   = ($values === NULL);
		if ($root) {
			$retval[] = join('', $this->before);
			$values = $this->values;
		}
		foreach ($values as $key=>$value) {
			if (is_array($value)) {
				$retval[] = str_repeat('*', $level) . $key
					. $this->toString($value, $level + 1);
			} else {
				$retval[] = str_repeat('-', $level - 1) . $value . "\n";
			}
		}
		if ($root) $retval[] = join('', $this->after);

		return join("\n",$retval);
	}
}