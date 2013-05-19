<?php
/**
 * 更新ログファイルクラス
 *
 * @package   PukiWiki\File\Log
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2013 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: UpdateLog.php,v 1.0.0 2013/01/29 19:54:00 Logue Exp $
 */
namespace PukiWiki\File\Log;

use PukiWiki\File\LogFactory;
use PukiWiki\File\LogFile;

/**
 * 更新ログファイルクラス
 */
class UpdateLog extends LogFile{
	function __construct($page) {
		$this->kind = 'update';
		$this->isWiki = false;
		parent::__construct($page);
	}

	public function set($data){
		if ($this->config['guess_user']['use']){
			LogFactory::factory('guess')->set($data);
		}
		parent::set($data);
	}
}

/* End of file UpdateLog.php */
/* Location: /vendor/PukiWiki/Lib/File/UpdateLog.php */
