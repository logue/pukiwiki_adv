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
		'browse'  => 'PukiWiki\File\Log\BrowseLog',
		'update'  => 'PukiWiki\File\Log\UpdateLog',
		'download'=> 'PukiWiki\File\Log\DownloadLog',
		'check'   => 'PukiWiki\File\Log\CheckLog',
	);

	public static function factory($kind, $page = null){
		if (!in_array($kind, array_keys(self::$classMap))) throw new Exception('$driver = '. $kind . ' is not implemented.');
		return new self::$classMap[$kind]($page);
	}
}