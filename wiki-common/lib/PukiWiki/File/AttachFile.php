<?php
/**
 * 添付ファイル
 *
 * @package   PukiWiki
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2014 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: AttachFile.php,v 1.0.0 2014/01/20 10:31:00 Logue Exp $
 */

namespace PukiWiki\File;

use PukiWiki\Utility;

/**
 * 添付ファイルクラス
 */
class AttachFile extends AbstractFile{
	public static $dir = UPLOAD_DIR;
	public static $pattern = '/^((?:[0-9A-F]{2})+)_((?:[0-9A-F]{2})+)(?:\.([0-9|log]+))?$/';

	protected static $listing_pattern = '/^((?:[0-9A-F]{2})+)_((?:[0-9A-F]{2})+)$/';

	public $filename;
	

	/**
	 * コンストラクタ
	 * @param string $page ページ名
	 * @param string $file ファイル名
	 * @param int $age バックアップの世代
	 */
	public function __construct($page, $file, $age = 0)
	{
		parent::__construct(self::$dir . Utility::encode($page) . '_' . Utility::encode($file) . ($age !== 0 ? $age : ''));
	}
	/**
	 * file一覧
	 */
	public static function getPages($pattern = ''){
		$ret = array();
		// 継承元のクラス名を取得（PHPは、__CLASS__で派生元のクラス名が取得できない）
		$class =  get_called_class();
		// クラスでディレクトリが定義されていないときは処理しない。(AuthFile.phpなど）
		if ( empty($class::$dir)) return array();

		foreach (self::exists() as $file) {
			$matches = array();
			if (preg_match(self::$pattern, $file, $matches)){
				if (!isset($matches[3])) $matches[3] = 0;
				if ($matches[3] === 'log') continue;
				$ret[Utility::decode($matches[1])][][$matches[3]] = Utility::decode($matches[2]);
			}
		}
		return $ret;
	}
}