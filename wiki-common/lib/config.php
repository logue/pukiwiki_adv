<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: config.php,v 1.7.2 2012/05/29 17:34:00 Logue Exp $
// Copyright (C)
//               2010-2012 PukiWiki Advance Developers Team
//               2003-2007 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Parse a PukiWiki page as a configuration page

/*
 * $obj = new Config('plugin/plugin_name/')
 * $obj->read();
 * $array = $obj->get($title);
 * $array[] = array(4, 5, 6);		// Add - directly
 * $obj->add($title, array(4, 5, 6));	// Add - method of Config object
 * $array = array(1=>array(1, 2, 3));		// Replace - directly
 * $obj->put($title, array(1=>array(1, 2, 3));	// Replace - method of Config object
 * $obj->put_values($title, NULL);	// Delete
 * $obj->write();
 */

// Fixed prefix of configuration-page's name
define('PKWK_CONFIG_PREFIX', ':config/');

// Configuration-page manager
class Config
{
	var $name, $page; // Page name
	var $objs = array();

	function __construct($name)
	{
		$this->name = $name;
		$this->page = PKWK_CONFIG_PREFIX . $name;
		$this->wikidata = new WikiFile($this->page);
//		$this->modified = get_filetime(PKWK_CONFIG_PREFIX . $name);
//		$this->cache_prefix = 'conf-'.encode($this->name);
	}

	// Load the configuration-page
	function read()
	{
		if (! is_page($this->page)) return FALSE;

		$this->objs = array();
		$obj        = new ConfigTable('');
		$matches = array();

//		if ($this->cacheCheck('') === false){
			foreach ($this->wikidata->source() as $line) {
				if ($line == '') continue;

				$head  = $line{0};	// The first letter
				$level = strspn($line, $head);

				if ($level > 3) {
					$obj->add_line($line);

				} else if ($head == '*') {
					// Cut fixed-heading anchors
					$line = preg_replace('/^(\*{1,3}.*)\[#[A-Za-z][\w-]+\](.*)$/', '$1$2', $line);

					if ($level == 1) {
						$this->objs[$obj->title] = $obj;
						$obj = new ConfigTable($line);
					} else {
						if (! $obj instanceof ConfigTable_Direct)
							$obj = new ConfigTable_Direct('', $obj);
						$obj->set_key($line);
					}

				} else if ($head == '-' && $level > 1) {
					if (! $obj instanceof ConfigTable_Direct)
						$obj = new ConfigTable_Direct('', $obj);
					$obj->add_value($line);

				} else if ($head == '|' && preg_match('/^\|(.+)\|\s*$/', $line, $matches)) {
					// Table row
					if (! $obj instanceof ConfigTable_Sequential)
						$obj = new ConfigTable_Sequential('', $obj);
					// Trim() each table cell
					$obj->add_value(array_map('trim', explode('|', $matches[1])));
				} else {
					$obj->add_line($line);
				}
			}
			$this->objs[$obj->title] = $obj;
//			$this->cacheWrite($obj->title, $obj);
//		}else{
//			$this->objs[$obj->title] = $this->cacheRead($obj->title);
//		}

		return TRUE;
	}

	// Get an array
	function get($title)
	{
		$obj = $this->get_object($title);
		return $obj->values;
	}

	// Set an array (Override)
	function put($title, $values)
	{
		$obj         = $this->get_object($title);
		$obj->values = $values;
	}

	// Add a line
	function add($title, $value)
	{
		$obj = $this->get_object($title);
		$obj->values[] = $value;
	}

	// Get an object (or create it)
	function get_object($title)
	{
		if (! isset($this->objs[$title])){
//			if ($this->cacheCheck($title) === false){
				$this->objs[$title] = new ConfigTable('*' . trim($title) . "\n");
//				$this->cacheWrite($title, $this->objs[$title]);
//			}else{
//				$this->objs[$title] = $this->cacheRead($title);
//			}
		}
		return $this->objs[$title];
	}

	function write()
	{

		$this->wikidata->write($this->toString());
		//page_write($this->page, $this->toString());
	}

	function toString()
	{
		$retval = '';
		foreach ($this->objs as $title=>$obj)
			$retval .= $obj->toString();
		return $retval;
	}
/*
	function cacheCheck($title){
		global $memcache;
		if ($title === ''){
			$cache_file = CACHE_DIR.$this->cache.PKWK_DAT_EXTENTION;
		}else{
			$this->cache_name[$title] = $this->cache.'-'.encode($title).PKWK_DAT_EXTENTION;
			$cache_file = CACHE_DIR.$this->cache_name[$title];
		}

		if (file_exists($cache_file) && !($this->modified > getlastmod($cache_file) )) {
			return true;
		}else{
			return false;
		}
	}
	function cacheWrite($title, $obj){
		global $memcache;

		if ($title === ''){
			$cache_file = $this->cache.PKWK_DAT_EXTENTION;
		}else{
			if ( !isset($this->cache_name[$title]) ) {
				$this->cache_name[$title] = $this->cache.'-'.encode($title).PKWK_DAT_EXTENTION;
			}
			$cache_file = $this->cache_name[$title];
		}

		pkwk_touch_file(CACHE_DIR.$cache_file);
		$fp = fopen(CACHE_DIR.$cache_file, 'wb');
		if ($fp === false) return false;
		@flock($fp, LOCK_EX);
		rewind($fp);
		$ret = fwrite($fp, serialize($obj));
		fflush($fp);
		ftruncate($fp, ftell($fp));
		@flock($fp, LOCK_UN);
		fclose($fp);
		if ($memcache !== null){
			$ret = update_memcache(MEMCACHE_PREFIX.$cache_file, $obj);
		}
		return $ret;
	}

	function cacheRead($title){
		global $memcache;

		if ( !isset($this->cache_name[$title]) ) {
			$this->cache_name[$title] = $this->cache.'-'.encode($title).PKWK_DAT_EXTENTION;
		}

		$cache_file = ($title === '') ? $this->cache.PKWK_DAT_EXTENTION : $this->cache_name[$title];

		$ret = ($memcache !== null) ?
			$memcache->get(MEMCACHE_PREFIX.$cache_file) : false;

		if ($ret === false){
			// キャッシュ読み込み
			$fp = fopen(CACHE_DIR.$cache_file, 'rb');
			if ($fp === false) return array();
			@flock($fp, LOCK_SH);
			$ret = unserialize( fread($fp, filesize(CACHE_DIR.$cache_file)) );
			@flock($fp, LOCK_UN);
			if(! fclose($fp)) return array();
		}
		return $ret;
	}
*/
}

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

class ConfigTable_Sequential extends ConfigTable
{
	// Add a line
	function add_value($value)
	{
		$this->values[] = (count($value) == 1) ? $value[0] : $value;
	}

	function toString($values = NULL, $level = 2)
	{
		$retval = join('', $this->before);
		if (is_array($this->values)) {
			foreach ($this->values as $value) {
				$value   = is_array($value) ? join('|', $value) : $value;
				$retval .= '|' . $value . '|' . "\n";
			}
		}
		$retval .= join('', $this->after);
		return $retval;
	}
}

class ConfigTable_Direct extends ConfigTable
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
		$retval = '';
		$root   = ($values === NULL);
		if ($root) {
			$retval = join('', $this->before);
			$values = $this->values;
		}
		foreach ($values as $key=>$value) {
			if (is_array($value)) {
				$retval .= str_repeat('*', $level) . $key . "\n";
				$retval .= $this->toString($value, $level + 1);
			} else {
				$retval .= str_repeat('-', $level - 1) . $value . "\n";
			}
		}
		if ($root) $retval .= join('', $this->after);

		return $retval;
	}
}

/* End of file config.php */
/* Location: ./wiki-common/lib/config.php */
