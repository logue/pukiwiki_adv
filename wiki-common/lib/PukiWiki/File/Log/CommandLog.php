<?php
/**
 * コマンドログファイルクラス
 *
 * @package   PukiWiki\File\Log
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013 PukiWiki Advance Developers Team
 * @create    2013/05/14
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: CommandLog.php,v 1.0.0 2013/05/14 19:54:00 Logue Exp $
 */
namespace PukiWiki\File\Log;

use PukiWiki\File\LogFile;

/**
 * コマンドログファイルクラス
 */
class CommandLog extends LogFile{
	function __construct($page) {
		$this->kind = 'cmd';
		$this->isWiki = true;
		parent::__construct($page);
	}
}

/* End of file CommandLog.php */
/* Location: /vendor/PukiWiki/File/Log/CommandLog.php */
