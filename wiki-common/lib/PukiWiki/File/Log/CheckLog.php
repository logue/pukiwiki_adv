<?php
/**
 * チェックログファイルクラス
 *
 * @package   PukiWiki\File\Log
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013 PukiWiki Advance Developers Team
 * @create    2013/05/14
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: CheckLog.php,v 1.0.0 2013/05/14 17:10:00 Logue Exp $
 */
namespace PukiWiki\File\Log;

use PukiWiki\File\File;
use PukiWiki\File\LogFile;

/**
 * チェックログファイルクラス
 */
class CheckLog extends LogFile{
	function __construct($page) {
		$this->kind = 'check';
		$this->isWiki = false;
		parent::__construct($page);
	}
}

/* End of file CheckLog.php */
/* Location: /vendor/PukiWiki/Lib/File/CheckLog.php */
