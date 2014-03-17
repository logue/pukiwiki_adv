<?php
/**
 * 汎用ファイルクラス
 *
 * @package   PukiWiki\File
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013 PukiWiki Advance Developers Team
 * @create    2013/05/13
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: File.php,v 1.0.0 2013/05/13 17:44:00 Logue Exp $
 */
namespace PukiWiki\File;

use Exception;

/**
 * 汎用ファイルクラス
 */
class File extends AbstractFile{
	/**
	 * サニタイズは無効化
	 */
	const FILE_SANITIZE = false;
	/**
	 * コンストラクタ
	 * @param string $file ファイル名
	 */
	public function __construct($file) {
		if (empty($file)){
			throw new Exception('File::__construct(): file name is missing!');
		}
		parent::__construct($file);
	}
	public static function getPages($pattern = ''){
		throw new Exception('File::getPages(): File class does not supported getPages().');
	}
	public static function exists($force = false, $clearOnly = false){
		throw new Exception('File::exists(): File class does not supported exsists().');
	}
}