<?php
/**
 * Html Insert Plugin
 *
 * @author	 sonots
 * @license	http://www.gnu.org/licenses/gpl.html GPL v2
 * @link	   http://lsx.sourceforge.jp/?Plugin%2Fhtmlinsert.inc.php
 * @version	$Id: htmlinsert.inc.php,v 1.12.2 2011-02-05 10:55:00 Logue $
 * @package	plugin
 */

class PluginHtmlinsert
{
	public function __construct()
	{
		static $conf = array();
		if (empty($conf)) $conf = array(
			'INSERT_DIR'		 => DATA_HOME . 'htmlinsert',
			'INSERT_PAGE_PREFIX' => ':HTML',
			'SCRIPT_DIR'		 => $this->plugin_dir('htmlinsert') . 'htmlinsert',
			'SCRIPT_PAGE_PREFIX' => ':HTMLSCRIPT',
		);
		static $default_action_options = array(
			'content-type' => 'text/html',
		);
		static $default_options = array(
			'transitional'	 => FALSE,
		);
		static $def = array();
		if (empty($def)) $def = array(
			'freeze'	   => '/^(?:#freeze(?!\w)\s*)+/im', // see edit.inc.php
		);
		$this->conf = &$conf;
		$this->def  = &$def;
		$this->default_action_options = &$default_action_options;
		$this->default_options = &$default_options;

		// init
		$this->action_options = $this->default_action_options;
		$this->options = $this->default_options;
	}

	// static
	private $conf;
	private $def;
	private $default_action_options;
	private $default_options;
	// var
	private $action_options;
	private $options;
	private $error = "";
	private $plugin = "htmlinsert";

	function action()
	{
		global $vars;
		list($page, $variables, $this->action_options) = $this->parse_args_action($vars, $this->default_action_options);
		if ($page == '') { return array('msg'=>$this->plugin, 'body'=> '<p class="alert alert-warning">#' . $this->plugin() . ': No page is specified.</p>'); }
		$source = $this->htmlinsert($page, $variables);
		if ($this->error != "") { return array('msg'=>$this->plugin, 'body'=> '<p class="alert alert-warning">#' . $this->plugin() . ': ' . $this->error . '</p>'); }
		// no skin
		pkwk_common_headers(); 
		if ($this->action_options['content_type'] != '') {
			header('Content-Type: ' . htmlsc($this->action_options['content_type']));
		}
		print $source;
		exit;
	}
	
	function parse_args_action($vars, $default_options)
	{
		$page = $vars['page'];
		$options = $default_options;
		unset($vars['cmd']);  // pukiwiki reserved key
		unset($vars['page']); // 
		foreach ($vars as $key => &$val) {
			if (isset($options[$key])) {
				$options[$key] = $val;
				unset($val);
			} else {
				$val = htmlsc($val);
			}
		}
		return array($page, $vars, $options);
	}
			
	function parse_args($args, $default_options)
	{
		$options = $default_options;
		$page = array_shift($args);
		$vars = array();
		foreach($args as $i => $arg) {
			list($key, $val) = array_pad(explode('=', $arg, 2), 2, TRUE);
			if (isset($options[$key])) {
				$options[$key] = $val;
				unset($args[$i]);
			} else {
				$vars[$key] = htmlsc($val);
			}
		}
		return array($page, $vars, $options);
	}

	function inline()
	{
		$args = func_get_args();
		array_pop($args); // drop {}
		list($page, $variables, $this->options) = $this->parse_args($args, $this->default_options);
		if (! isset($page) || $page == '') { return '<span class="text-warning">&amp;' . $this->plugin() . ': No file or page was specified. </span>'; }
		$source  = $this->htmlinsert($page, $variables);
		if (!empty($this->error)) { return '<span class="text-warning">&amp;' . $this->plugin() . ': ' . $this->error . '</span>'; }
		if ($this->options['transitional']) {
			$this->html_transitional();
		}
		return $source;
	}
	
	function convert()
	{
		$args = func_get_args();
		// multiline argument options
		$end = end($args);
		if (substr($end, -1) == "\r") {
			$body = array_pop($args);
		}
		unset($end);
		if (isset($body)) {
			$args = array_merge($args, explode("\r", $body));
			array_pop($args); // always get empty element at the end
		}
		list($page, $variables, $this->options) = $this->parse_args($args, $this->default_options);
		if (! isset($page) || $page == '') { return '<p class="alert alert-warning">#htmlinsert: No file or page was specified. </p>'; }
		$source  = $this->htmlinsert($page, $variables);
		if ($this->error != "") { return '<p class="alert alert-warning">#htmlinsert: '.$this->error.'</p>'."\n"; }
		if ($this->options['transitional']) {
			$this->html_transitional();
		}
		return $source;
	}
	
	function html_transitional() 
	{
		global $pkwk_dtd; //1.4.4 or above
		global $html_transitional; //1.4.3
		$pkwk_dtd = PKWK_DTD_XHTML_1_0_TRANSITIONAL;
		$html_transitional = 1;
	}

	function htmlinsert($page, $variables)
	{
		if(strpos($page, $this->conf['INSERT_PAGE_PREFIX'] . "/") !== FALSE) {
			$source = $this->get_wikipage($page);
		} else {
			$source = $this->get_localfile($page);
			if ($this->error != "") {
				$source = $this->get_wikipage($this->conf['INSERT_PAGE_PREFIX'] . "/" . $page);
			}
		}
		if ($this->error != "") { return ; }

		if (count($variables) > 0) {
			$source = $this->replace_variables($source, $variables);
			if ($this->error != "") { return ; }
		}
		return $source;
	}


	function get_localfile($filename)
	{
		if(preg_match("#^\.\./|/\.\./#", $filename)) {
			$this->error .= "You must not specify upper directory like $filename. ";
			return;
		}
		$localnames = array();
		$localnames[] = $this->conf['INSERT_DIR'] . "/" . $filename;
		$localnames[] = $this->conf['SCRIPT_DIR'] . "/" . $filename;
		foreach ($localnames as $localname) {
			if (! file_exists($localname)) {
				$this->error .=  "Localfile, <var>$localname</var>, does not exist. ";
			} else {
				if(! is_readable($localname)) {
					$this->error .=  "Localfile, <var>$localname</var>, exists but is not readable. ";
				} else {
					$this->error = "";
					break;
				}
			}
		}
		return @file_get_contents($localname);
	}
  
	function get_wikipage($page)
	{
		if (! is_page($page)) {
			$this->error .=  "Wiki page, <var>$page</var>, does not exist. ";
			return;
		} elseif (! (PKWK_READONLY > 0 or $this->is_edit_auth($page) or is_freeze($page))) {
			$this->error .=  "Wiki page, <var>$page</var>, must be edit_authed or frozen or whole system must be <var>PKWK_READONLY</var>.";
			return;
		}

		$lines = get_source($page);
		if (is_freeze($page)) {
			// remove #freeze
			if(preg_match($this->def['freeze'], $lines[0])) {
				array_shift($lines);
			}
		}
		$source = join('', $lines);
		$this->error = "";
		return $source;
	}
	 
	function replace_variables($body, $variables)
	{
		preg_match_all('/\$\{(?:(raw|enc|utf8|euc|sjis|jis):)?([^=}]+)=([^}]*)\}/', $body, $matches, PREG_PATTERN_ORDER);
		$search = &$matches[0];
		$encs   = &$matches[1];
		$keys   = &$matches[2];
		$values = &$matches[3];
		foreach ($variables as $key => $val) {
			if (($idx = array_search($key, $keys)) !== FALSE) {
				$values[$idx] = $val;
			} else {
				$this->error = "No such a htmlinsert variable, <var>$key</var>, in the specified page. ";
				return;
			}
		}
		foreach ($values as $idx => &$value) {
			switch ($encs[$idx]) {
			case 'enc':
				$value = rawurlencode($value);
				break;
			case 'utf8':
				$value = mb_convert_encoding($value, 'UTF-8', SOURCE_ENCODING);
				$value = rawurlencode($value);
				break;
			case 'euc':
				$value = mb_convert_encoding($value, 'EUC', SOURCE_ENCODING);
				$value = rawurlencode($value);
				break;
			case 'sjis':
				$value = mb_convert_encoding($value, 'SJIS', SOURCE_ENCODING);
				$value = rawurlencode($value);
				break;
			case 'jis':
				$value = mb_convert_encoding($value, 'JIS', SOURCE_ENCODING);
				$value = rawurlencode($value);
				break;
			case '':
			case 'raw':
			default:
				break;
			}
		}
		return str_replace($search, $values, $body);	
	}
	
	// PukiWiki Plus Wrapper
	function plugin_dir($plugin_name)
	{
		static $plugin_dir;
		if (isset($plugin_dir)) return $plugin_dir;

		$p_dirs = defined('EXT_PLUGIN_DIR') ? 
			array(EXT_PLUGIN_DIR, PLUGIN_DIR) : array(PLUGIN_DIR);
		foreach ($p_dirs as $p_dir) {
			if (file_exists($p_dir . $plugin_name . '.inc.php')) {
				$plugin_dir = $p_dir;
				break;
			}
		}
		return $plugin_dir;
	}
	
	// PukiWiki API extension
	function is_edit_auth($page, $user = '')
	{
		global $edit_auth, $edit_auth_pages, $auth_method_type;
		if (! $edit_auth) {
			return FALSE;
		}
		// Checked by:
		$target_str = '';
		if ($auth_method_type == 'pagename') {
			$target_str = $page; // Page name
		} else if ($auth_method_type == 'contents') {
			$target_str = join('', get_source($page)); // Its contents
		}
		
		foreach($edit_auth_pages as $regexp => $users) {
			if (preg_match($regexp, $target_str)) {
				if ($user == '' || in_array($user, explode(',', $users))) {
					return TRUE;
				}
			}
		}
		return FALSE;
	}
}

///////////////////////

function plugin_htmlinsert_common_init()
{
	global $plugin_htmlinsert;
	if (class_exists('PluginHtmlinsertUnitTest')) {
		$plugin_htmlinsert = new PluginHtmlinsertUnitTest();
	} elseif (class_exists('PluginHtmlinsertUser')) {
		$plugin_htmlinsert = new PluginHtmlinsertUser();
	} else {
		$plugin_htmlinsert = new PluginHtmlinsert();
	}
}

function plugin_htmlinsert_action()
{
	global $plugin_htmlinsert; plugin_htmlinsert_common_init();
	return $plugin_htmlinsert->action();
}

function plugin_htmlinsert_inline()
{
	global $plugin_htmlinsert; plugin_htmlinsert_common_init();
	$args = func_get_args();
	return  call_user_func_array(array(&$plugin_htmlinsert, 'inline'), $args);
}

function plugin_htmlinsert_convert()
{
	global $plugin_htmlinsert; plugin_htmlinsert_common_init();
	$args = func_get_args();
	return call_user_func_array(array(&$plugin_htmlinsert, 'convert'), $args);
}

/* End of file htmlinsert.inc.php */
/* Location: ./wiki-common/plugin/htmlinsert.inc.php */