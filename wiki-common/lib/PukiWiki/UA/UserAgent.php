<?php
/*
 * USER-AGENT クラス(ラッパー)
 *
 * @copyright   Copyright &copy; 2004-2006, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @version     $Id: user_agent.cls.php,v 0.3 2006/06/25 00:47:00 upk Exp $
 * @license     http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace PukiWiki\UA;

use PukiWiki\UA\ccTLD;
use PukiWiki\UA\Browsers;
use PukiWiki\UA\Os;
use PukiWiki\UA\Robots;

class UserAgent
{
	var $obj_flag, $obj_os, $obj_browsers, $obj_domain, $obj_robots;

	public function __construct($opt='')
	{
		$this->obj_flag	    = new ccTLD();
		$this->path_flag    = IMAGE_DIR.'icon/flags/';
		$this->obj_os	    = new Os();
		$this->path_os	    = IMAGE_DIR.'icon/os/';
		$this->obj_browsers = new Browsers();
		$this->obj_robots   = new Robots();
	}

	public function get_icon_flag($host)
	{
		// list($code, $name) = $this->obj_flag->get_icon($host);
		return $this->obj_flag->get_icon($host);
	}

	public function get_icon_os($ua)
	{
		return $this->obj_os->get_icon($ua);
	}

	public function get_icon_broeswes($ua)
	{
		return $this->obj_browsers->get_icon($ua);
	}

	public function get_icon_domain($host)
	{
		return (isset($this->obj_domain)) ? $this->obj_domain->get_icon($host) : '';
	}

	public function is_robots($ua)
	{
		list($id,$name) = $this->obj_robots->get_robots_info($ua);
		return (empty($id)) ? FALSE : TRUE;
	}
}

/* End of file user_agent.cls.php */
/* Location: ./wiki-common/lib/ua/user_agent.cls.php */
