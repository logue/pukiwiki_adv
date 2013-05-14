<?php
/**
 * Wikiファイルクラス
 *
 * @package   PukiWiki\File
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2013 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: WikiFile.php,v 1.0.0 2013/01/29 19:54:00 Logue Exp $
 */
namespace PukiWiki\File;

use Exception;
use PukiWiki\Utility;

/**
 * Wikiページクラス
 */
class WikiFile extends AbstractFile{
	public static $dir = DATA_DIR;
	public static $pattern = '/^((?:[0-9A-F]{2})+)\.txt$/';

	/**
	 * コンストラクタ
	 * @param string $page
	 */
	public function __construct($page = null) {

		if (empty($page)){
			throw new Exception('Page name is missing!');
		}
		if (!is_string($page)){
			throw new Exception('Page name must be string!');
		}
		$this->page = $page;
		parent::__construct(self::$dir . Utility::encode($page) . '.txt');
	}
}

/* End of file WikiFile.php */
/* Location: /vender/PukiWiki/Lib/File/WikiFile.php */
