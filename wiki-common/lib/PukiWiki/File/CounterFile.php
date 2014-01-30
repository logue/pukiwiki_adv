<?php
/**
 * カウンターファイルクラス
 *
 * @package   PukiWiki\File
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013 PukiWiki Advance Developers Team
 * @create    2013/06/01
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: CounteriFile.php,v 1.0.0 2013/06/01 19:54:00 Logue Exp $
 */
namespace PukiWiki\File;

use Exception;
use PukiWiki\Utility;

/**
 * カウンタークラス
 */
class CounterFile extends AbstractFile{
	public static $dir = COUNTER_DIR;
	public static $pattern = '/^((?:[0-9A-F]{2})+)\.count$/';

	/**
	 * コンストラクタ
	 * @param string $page
	 */
	public function __construct($page = null) {

		if (empty($page)){
			throw new Exception('CounterFile::__construct(): Page name is missing!');
		}
		if (!is_string($page)){
			throw new Exception('CounterFile::__construct(): Page name must be string!');
		}
		$this->page = $page;
		parent::__construct(self::$dir . Utility::encode($page) . '.count');
	}
}

/* End of file CounterFile.php */
/* Location: /vendor/PukiWiki/Lib/File/CounterFile.php */
