<?php
/**
 * ログファクトリークラス
 *
 * @package   PukiWiki\File
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013 PukiWiki Advance Developers Team
 * @create    2013/05/13
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: LogFactory.php,v 1.0.0 2013/05/13 15:30:00 Logue Exp $
 */

namespace PukiWiki\File;

use Exception;

/**
 * ログのファクトリークラス
 */
class LogFactory
{
	private function __construct() {}
	private function __clone() {}

	protected static $classMap = array(
		'browse'        => 'PukiWiki\File\Log\BrowseLog',	// 0
		'check'         => 'PukiWiki\File\Log\CheckLog',	// 5
		'cmd'           => 'PukiWiki\File\Log\CommandLog',	// 3
		'download'      => 'PukiWiki\File\Log\DownloadLog',	// 2
		'guess_user'    => 'PukiWiki\File\Log\GuessLog',
		'login'         => 'PukiWiki\File\Log\LoginLog',	// 4
		'update'        => 'PukiWiki\File\Log\UpdateLog'	// 1
	);

	public static function factory($kind, $page = null){
		if (!in_array($kind, array_keys(self::$classMap))) throw new Exception('LogFactory::factory(): $driver = '. $kind . ' is not implemented.');
		return new self::$classMap[$kind]($page);
	}
}