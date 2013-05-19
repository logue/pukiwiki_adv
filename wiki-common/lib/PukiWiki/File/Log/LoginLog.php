<?php
/**
 * ログインログファイルクラス
 *
 * @package   PukiWiki\File\Log
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013 PukiWiki Advance Developers Team
 * @create    2013/05/14
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: LoginLog.php,v 1.0.0 2013/05/14 16:34:00 Logue Exp $
 */
namespace PukiWiki\File\Log;

use PukiWiki\File\LogFile;

/**
 * ログインログファイルクラス
 */
class LoginLog extends LogFile{
	function __construct($x) {
		$this->kind = 'login';
		$this->isWiki = true;
		parent::__construct();
	}
}

/* End of file CommandLog.php */
/* Location: /vendor/PukiWiki/File/Log/LoginLog.php */
