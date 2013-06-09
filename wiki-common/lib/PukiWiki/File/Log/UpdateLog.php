<?php
/**
 * 更新ログファイルクラス
 *
 * @package   PukiWiki\File\Log
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2013 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: UpdateLog.php,v 1.0.0 2013/01/29 19:54:00 Logue Exp $
 */
namespace PukiWiki\File\Log;

use PukiWiki\File\LogFactory;
use PukiWiki\File\LogFile;

/**
 * 更新ログファイルクラス
 */
class UpdateLog extends LogFile{
	/**
	 * コンストラクタ
	 */
	function __construct($page) {
		$this->kind = 'update';
		$this->isWiki = false;
		parent::__construct($page);
	}
	/**
	 * ログを保存する処理をオーバーライドして見做しログを保存
	 */
	public function set($data = '', $keeptimestamp = false){
		if ($this->config['guess_user']['use']){
			LogFactory::factory('guess_user')->set($data);
		}
		parent::set($data, $keeptimestamp);
	}
	/**
	 * 署名を抽出
	 */
	public function getSigunature(){
		$lines = parent::get();
		if (!$lines) return;
		foreach ($lines as $_data) {
			foreach($_data as $line) {
				$field = parent::line2field($line,$name);
				if (empty($field['ua'])) continue;
				$user = parent::guess_user($field['user'],$field['ntlm'],$field['sig']);
				if (empty($user)) continue;
				$sum[$field['ua']][$field['host']][$user] = '';
			}
		}
		return $sum;
	}
}

/* End of file UpdateLog.php */
/* Location: /vendor/PukiWiki/Lib/File/UpdateLog.php */
