<?php
/**
 * Wikiファイルクラス
 *
 * @package   PukiWiki\Lib\File
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2013 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: WikiFile.php,v 1.0.0 2013/01/29 19:54:00 Logue Exp $
 */
namespace PukiWiki\Lib\File;

use PukiWiki\Lib\Utility;


/**
 * Wikiページクラス
 */
class WikiFile extends File{
	/**#@+
	 * 宣言
	 */
	// 拡張子
	const EXT = '.txt';
	// 格納ディレクトリ
	const DIR = DATA_DIR;
	// ページ名として使用可能な文字
	const VALIED_PAGENAME_PATTERN = '/^(?:[\x00-\x7F]|(?:[\xC0-\xDF][\x80-\xBF])|(?:[\xE0-\xEF][\x80-\xBF][\x80-\xBF]))+$/';
	// ページ名に含めることができない文字
	const INVALIED_PAGENAME_PATTERN = '/[%|=|&|?|#|\r|\n|\0|\@|\t|;|\$|+|\\|\[|\]|\||^|{|}]/';
	// ファイル名のパターン
	const FILENAME_PATTERN = '/^((?:[0-9A-F]{2})+).txt$/';
	// 投稿ログ
	const POST_LOG_FILENAME = 'postlog.log';
	// 投稿内容のロギングを行う（デバッグ用）
	const POST_LOGGING = false;
	/**#@-*/

	/**
	 * コンストラクタ
	 * @param string $page
	 */
	public function __construct($page) {

		if (empty($page)){
			throw new \Exception('Page name is missing!');
		}
		if (!is_string($page)){
			throw new \Exception('Page name must be string!');
		}
		$this->page = $page;
		parent::__construct(self::DIR . Utility::encode($page) . self::EXT);
	}
}

/* End of file WikiFile.php */
/* Location: /vender/PukiWiki/Lib/File/WikiFile.php */
