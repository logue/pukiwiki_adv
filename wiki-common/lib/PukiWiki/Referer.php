<?php
/**
 * リンク元クラス
 *
 * @package   PukiWiki
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2013 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Referer.php,v 1.0.0 2013/03/23 09:30:00 Logue Exp $
 */
namespace PukiWiki;

use PukiWiki\File\FileFactory;
use PukiWiki\Config\Config;
use PukiWiki\Router;
use PukiWiki\Utility;
use Zend\Http\ClientStatic;
use Zend\Dom\Query;

/**
 * リンク元クラス
 */
class Referer{
	/**
	 * リファラーのブロックリスト
	 */
	const CONFIG_REFERER_BL = 'plugin/referer/BlackList';
	/**
	 * リファラーのホワイトリスト
	 */
	const CONFIG_REFERER_WL = 'plugin/referer/WhiteList';
	/**
	 * リファラーのキーの保持期間（１年）
	 */
	const REFERER_LIFE_TIME = 31536000;
	/**
	 * バン対象にするリファラーの回数
	 */
	const REFFRER_BAN_COUNT = 3;
	/**
	 * リファラースパムログ
	 */
	const REFERER_SPAM_LOG = 'ref_spam.log';

	private $referer, $page, $file;
	/**
	 * コンストラクタ
	 */
	public function __construct($page){
		global $_SERVER;
		$this->referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
		$this->page = $page;
		// 以下はSplFileInfoの派生クラス
		$this->file = FileFactory::Referer($this->page);
	}
	/**
	 * リファラーを保存
	 */
	public function set(){
		global $referer;
		// リファラーが空の場合処理しない
		if (! $this->referer || empty($this->referer) ) return;

		// 自分自身を除外
		$parse_url = parse_url($this->referer);
		if ($parse_url === FALSE || !isset($parse_url['host']) || $parse_url['host'] == $_SERVER['HTTP_HOST']) return;

/*
		if ( stristr($parse_url['host'], 'google') !== FALSE && ($parse_url['path'] === '/url' && $parse_url['path'] === '/search') ){
			// Googleのリファラーがログ容量を浪費するため。
			parse_str($parse_url['query'], $q);
			$url = $parse_url['scheme'] . '://'. $parse_url['host'] . ( ($q['q'] !== '') ? '/search?q='.$q['q'] : '');
		}else{
			// Blocking SPAM
			if ($use_spam_check['referer']){
				if (SpamCheck($parse_url['host'])) return TRUE;
				if (is_refspam($url) === true) return TRUE;
			}
		}
*/

		// URLをキーとする
		$d_url = rawurldecode($this->referer);

		$lines = array();

		// 改行ごと分割した状態でデーターを読み込む
		foreach ($this->file->get() as $line){
			$entries = explode("\t",$line);
			$lines[rawurldecode($entries[3])] = $entries;
		}
		unset($entries);

		if (! isset($lines[$d_url])) {
			// セットされていない場合
			$lines[$d_url] = array(
				0,              // [0]: Last update date
				UTIME,          // [1]: Creation date
				0,              // [2]: Reference counter
				$this->referer, // [3]: Referer header
				0               // [4]: Enable / Disable flag (1 = enable)
			);
		}

		// 日時を更新
		$lines[$d_url][0] = UTIME;
		// カウンタを更新
		$lines[$d_url][2]++;

		// 書き込むテキストを生成
		$ret = array();
		foreach ($lines as $line=>$data) {
			if ($data[0] <= UTIME - self::REFERER_LIFE_TIME){
				// 一定期間過ぎた古いリファラーは保存しない
				continue;
			}
			
			// データーを分割
			$str = trim(join("\t", $data));
			if (!empty($str)) $ret[] = $str;
		}

		return $this->file->set($ret);
	}
	/**
	 * リファラーを取得
	 */
	public function get(){
		if (!$this->file->has()) return array();
		$result = array();
		foreach ($this->file->get() as $line) {
			$data = explode("\t", $line);
			$result[rawurldecode($data[1])] = $data;
		}
		return $result;
	}
	/**
	 * 個数
	 */
	public function count(){
		if (!$this->file->has()) return 0;
		return count($this->get());
	}
	/**
	 * リンク元にアクセスして自サイトへのアドレスが存在するかのチェック
	 * @return boolean
	 */
	private function is_not_valid_referer(){
		static $condition;
		// 本来は正規化されたアドレスでチェックするべきだろうが、
		// めんどうだからスクリプトのアドレスを含むかでチェック
		// global $vars;
		// $script = get_page_absuri(isset($vars['page']) ? $vars['page'] : '');

		if (empty($condition)){
			$parse_url = Router::get_script_uri();
			$condition = $parse_url['host'].$parse_url['path'];	// QueryStringは評価しない。
		}

		$response = ClientStatic::get($this->referer);
		if (! $response->isSuccess()){
			return true;
		}
		$dom = new Query($response->getBody());
		$results = $dom->execute('a[href=^"'.$condition.'"]');

		foreach($results as $element){	// hrefがhttpから始まるaタグを走査
			if (preg_match('/'.$condition.'/i', $element->href) !== 0){
				return false;
				break;
			}
		}
		return true;
	}

	/**
	 * Referer元spamかのチェック
	 * @return boolean
	 */
	private function is_refspam(){
		global $open_uri_in_new_window_servername;

		// リファラーをパース
		$parse_url = parse_url($this->referer);

		// フラグ
		$is_refspam = true;	// リファラースパムか？
		$hit_bl = false;	// ブラックリストに入っているか？
		$BAN = false;		// バンするか？

		$condition = $parse_url['host'].$parse_url['path'];

		// ドメインは小文字にする。（ドメインの大文字小文字は区別しないのと、strposとstriposで速度に倍ぐらい違いがあるため）
		// 独自ドメインでない場合を考慮してパス（/~hoge/）を評価する。
		// QueryString（?aa=bb）は評価しない。

		// ホワイトリストに入っている場合はチェックしない
		$WhiteList = new Config(CONFIG_REFERER_WL);
		$WhiteList->read();
		$WhiteListLines = $WhiteList->get('WhiteList');
		foreach (array_merge($open_uri_in_new_window_servername, $WhiteListLines) as $WhiteListLine){
	//		if (preg_match('/'.$WhiteListLine[0].'/i', $condition) !== 0){
			if (stripos($condition, $WhiteListLine[0]) !== false){
				$is_refspam = false;
				break;
			}
		}

		if ($is_refspam !== false){
			$NewBlackListLine = array();
			// ブラックリストを確認
			$BlackList = new Config(CONFIG_REFERER_BL);
			$BlackList->read();

			$BlackListLines = $BlackList->get('BlackList');
			// |~referer|~count|~ban|h

			foreach ($BlackListLines as $BlackListLine){
	//			if (preg_match('/'.$BlackListLine[0].'/i', $condition) !== 0){
				if (stripos($condition, $BlackListLine[0]) !== false){
					// 過去に同じリファラーからアクセスがあった場合
					$BlackListLine[1]++;
					if ($BlackListLine[2] == 1 || $BlackListLine[1] <= self::REFFRER_BAN_COUNT){
						// バンフラグが立っている場合か、しきい値を超えた場合バン
						$BAN = true;
						// わざと反応を遅らせる
						sleep(2);
					}
					$hit_bl = true;
					$is_refspam = true;
				}
				$NewBlackListLine[] = array($BlackListLine[0],$BlackListLine[1],$BlackListLine[2]);
			}

			// ブラックリストにヒットしなかった場合
			if ($hit_bl === false){
				// リファラーにサイトへのアドレスが存在するかを確認
				$is_refspam = $this->is_not_valid_referer();

				if ($is_refspam === true){
					// 存在しない場合はスパムリストに追加
					$NewBlackListLine[] = array($condition,1,0);
				}else{
					// 存在した場合はホワイトリストに追加
	//				$WhiteListLines[] = array($condition);
	//				$WhiteList->put('WhiteList',$WhiteListLines);
	//				$WhiteList->write();
				}
			}
			// ブラックリストを更新
			$BlackList->set('BlackList',$NewBlackListLine);
			$BlackList->write();

			unset($BlackList,$BlackListLines,$BlackListLine,$NewBlackListLine, $hit_bl);
			unset($WhiteList,$WhiteListLines,$WhiteListLine);

			if ($is_refspam === true || $BAN === true){
				// スパムだった場合、ログに環境を保存する。
				$log = array(
					UTIME,
					$url,
					$_SERVER['HTTP_USER_AGENT'],
					$_SERVER['REMOTE_ADDR']
				);
				error_log( join("\t",$lines) . "\n", 3, CACHE_DIR . self::REFERER_SPAM_LOG);
				Utility::dieMessage('Spam Protection','Spam Protection', 500);
			}
		}
		return $is_refspam;
	}
}