<?php

namespace PukiWiki\File;

use DirectoryIterator;
use Exception;
use PukiWiki\File\File;
use PukiWiki\Utility;

/**
 * ファイルの読み書きを行うクラス
 */
abstract class LogFile extends File{
	/**
	 * ログの種類
	 */
	protected static $kind_no = array(
	//  'default'   => 0,
		'update'    => 1,
		'download'  => 2,
		'cmd'       => 3,
		'login'     => 4,
		'check'     => 5,
	);
	/**
	 * ログの表示フィールド定義
	 * 先頭 @ の項目は、ログには保存されていない項目(表示用)
	 */
	protected static $field = array(
		// 定義順は、デフォルト(all)表示順
		// idx                    0  1  2  3  4  5
		'ts'            => array( 1, 1, 1, 1, 1, 1),    // タイムスタンプ (UTIME)
		'@diff'         => array( 0, 1, 0, 0, 0, 0),    // 差分内容
		'@guess_diff'   => array( 0, 1, 0, 0, 0, 1),    // 推測差分
		'ip'            => array( 1, 1, 1, 1, 1, 1),    // IPアドレス
		'host'          => array( 1, 1, 1, 1, 1, 1),    // ホスト名 (FQDN)
		'@guess'        => array( 1, 1, 1, 0, 0, 0),    // 推測
		'auth_api'      => array( 0, 0, 0, 0, 1, 1),    // 認証API
		'user'          => array( 1, 1, 1, 1, 1, 1),    // ユーザ名(認証済)
		'ntlm'          => array( 1, 1, 1, 1, 0, 0),    // ユーザ名(NTLM認証)
		'proxy'         => array( 1, 1, 1, 1, 0, 0),    // Proxy情報
		'ua'            => array( 1, 1, 1, 1, 1, 1),    // ブラウザ情報 (USER-AGENT)
		'del'           => array( 0, 1, 0, 0, 0, 0),    // 削除フラグ
		'sig'           => array( 0, 1, 0, 0, 0, 0),    // 署名(曖昧)
		'file'          => array( 0, 0, 1, 0, 0, 0),    // ファイル名
		'cmd'           => array( 0, 0, 0, 1, 0, 0),    // コマンド名
		'page'          => array( 1, 1, 1, 0, 0, 0),    // ページ名
		'local_id'      => array( 0, 0, 0, 0, 1, 0),    // OpenIDの場合のみ設定される
	);
	/**
	 * 対象ディレクトリ
	 */
	public static $dir = LOG_DIR;
	/**
	 * ログの種類
	 */
	public static $kind;
	/**
	 * コンストラクタ
	 * @param string $page
	 */
	public function __construct($page) {
		
		if (empty($page)){
			throw new Exception('Page name is missing!');
		}
		global $log;
		$this->config = $log;
		$this->page = $page;
		$class = get_called_class();
		$this->kind = $class::$kind;
		if (empty($class::$kind)) throw new Exception('class :'.$class.' does not defined $kind value.');
		parent::__construct(self::$dir . $this->kind . '/' . Utility::encode($page) . '.txt');
	}
	/**
	 * ファイル一覧を取得
	 * ※静的メソッドで呼び出すこと。キャッシュはここでは実装しない
	 * @return array
	 */
	public static function exists($pattern = ''){
		// 継承元のクラス名を取得（PHPは、__CLASS__で派生元のクラス名が取得できない）
		$class =  get_called_class();
		$dir = self::$dir . $class::$log_dir;
		// クラスでディレクトリが定義されていないときは処理しない。(AuthFile.phpなど）
		if ( empty($dir)) return array();
		// パターンが指定されていない場合は、クラスで定義されているデフォルトのパターンを使用
		if ( empty($pattern) ) $pattern = $class::$pattern;
		// 継承元のクラスの定数をパラメーターとして与える
		foreach (new DirectoryIterator($dir) as $fileinfo) {
			$filename = $fileinfo->getFilename();
			$matches = array();
			if ($fileinfo->isFile() && preg_match($pattern, $filename, $matches)){
				$ret[] = Utility::decode($matches[1]);
			}
		}
		return $ret;
	}
	/**
	 * 共通チェック
	 */
	function log_common_check($parm)
	{
		global $log;
		global $log_ua;

		$username = Auth::check_auth();


		// 認証済の場合
		if ($log['auth_nolog'] && !empty($username)) return '';

		$utime = UTIME;
		$ip = Utility::getRemoteIp();

		if (isset($log[$this->kind]['nolog_ip'])) {
			// ロギング対象外IP
			foreach ($log[$this->kind]['nolog_ip'] as $nolog_ip) {
				if ($ip == $nolog_ip) return '';
			}
		}
		unset($obj_log);
		$rc = array();
		$field = self::set_fieldname($this->kind);

		foreach ($field as $key) {
			switch ($key) {
				case 'ts': // タイムスタンプ (UTIME)
					$rc[$key] = $utime;
					break;
				case 'ip': // IPアドレス
					$rc[$key] = $ip;
					break;
				case 'host': // ホスト名 (FQDN)
					$rc[$key] = self::ip2host($ip);
					break;
				case 'auth_api': // 認証API名
					//$obj = new auth_api();
					//$msg = $obj->auth_session_get();
					$rc[$key] = (isset($msg['api']) && ! empty($username)) ? 'plus' : '';
					break;
				case 'local_id':
					$rc[$key] = isset($msg['local_id']) ? '' : $msg['local_id'];
					break;
				case 'user': // ユーザ名(認証済)
					$rc[$key] = $username;
					break;
				case 'ntlm': // ユーザ名(NTLM認証)
					if (self::netbios_scope_check($ip,$hostname)) {
						$obj_nbt = new NetBios($ip);
						$rc[$key] = $obj_nbt->username;
						unset($obj_nbt);
					} else {
						$rc[$key] = '';
					}
					break;
				case 'proxy': // Proxy情報
					$obj_proxy = new ProxyChecker();
					$rc[$key] = $obj_proxy->is_proxy() ? 
						$obj_proxy->get_proxy_info() . '(' . $obj_proxy->get_realip() . ')' :
						'';
					unset($obj_proxy);
					break;
				case 'ua': // ブラウザ情報
					$rc[$key] = $log_ua;
					break;
				case 'del': // 削除フラグ
					// 更新時は、削除されたか？
					$rc[$key] = ($kind === 'update' && Factory::Wiki($page)->has()) ? '' : 'DELETE';
					break;
				case 'sig': // 署名(曖昧)
					$rc[$key] = Log::log_set_signature($kind,$page,$utime);
					break;
				case 'file': // ファイル名
					$rc[$key] = $parm;
					break;
				case 'page':
				case 'cmd':
					$rc[$key] = $page;
					break;
			}
		}
		return $rc;
	}
	/**
	 * 設定項目名を設定
	 * @static
	 */
	static function set_fieldname()
	{
		global $log;

		$idx = isset(self::$kind_no[self::$kind]) ? self::$kind_no[self::$kind] : 0;

		$rc = array();
		foreach(self::$field as $_field => $sw) {
			if ($sw[$idx] == 0) continue;
			if ($_field == 'page' && !isset($log[$kind]['file'])) continue;
			$rc[] = $_field;
		}

		return $rc;
	}
}