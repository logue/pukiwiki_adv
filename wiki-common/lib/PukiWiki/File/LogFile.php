<?php
/**
 * ログファイルクラス
 *
 * @package   PukiWiki\File
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013 PukiWiki Advance Developers Team
 * @create    2013/01/30
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: TrackBackFile.php,v 1.0.0 2013/01/30 19:54:00 Logue Exp $
 */

namespace PukiWiki\File;

use SplFileInfo;
use PukiWiki\Factory;
use PukiWiki\File\FileUtility;
use PukiWiki\Utility;
use PukiWiki\Diff;

class LogFile extends SplFileInfo{
	/**#@+
	 * 宣言
	 */
	// 拡張子
	const EXT = '.txt';
	// 格納ディレクトリ
	const DIR = LOG_DIR;
	// ファイル名のパターン
	const FILENAME_PATTERN = '/^((?:[0-9A-F]{2})+).txt$/';
	/**#@-*/

	private $page, $id;

	/**
	 * コンストラクタ
	 * @param string $page
	 */
	public function __construct($page, $kind) {
		global $log;
		if (empty($page)){
			throw new \Exception('Page name is missing!');
		}
		$this->config = $log;
		$this->kind = $kind;
		parent::__construct(self::DIR . $this->kind . '/' . Utility::encode($page) . self::EXT);
	}
	/**
	 * ファイルが存在するか
	 * @return boolean
	 */
	public function has(){
		if (! $this->config[$this->kind]['use']) return false;
		
		return $this->isFile();
	}