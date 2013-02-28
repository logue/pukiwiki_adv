<?php
/**
 * 更新ログファイルクラス
 *
 * @package   PukiWiki\File
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2013 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: WikiFile.php,v 1.0.0 2013/01/29 19:54:00 Logue Exp $
 */
namespace PukiWiki\File\Log;

use PukiWiki\File\LogFile;

/**
 * 更新ログファイルクラス
 */
class UpdateLog extends LogFile{
	public static $kind = 'update';
}

/* End of file WikiFile.php */
/* Location: /vender/PukiWiki/Lib/File/WikiFile.php */
