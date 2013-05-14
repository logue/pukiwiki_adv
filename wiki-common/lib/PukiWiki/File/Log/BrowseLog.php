<?php
/**
 * 閲覧ログファイルクラス
 *
 * @package   PukiWiki\File\Log
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2013 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: BrowseLog.php,v 1.0.0 2013/01/29 19:54:00 Logue Exp $
 */
namespace PukiWiki\File\Log;

use PukiWiki\File\LogFile;

/**
 * 閲覧ログファイルクラス
 */
class BrowseLog extends LogFile{
	function __construct($page) {
		$this->kind = 'browse';
		$this->isWiki = false;
		parent::__construct($page);
	}
}

/* End of file WikiFile.php */
/* Location: /vendor/PukiWiki/File/Log/BrowseLog.php */
