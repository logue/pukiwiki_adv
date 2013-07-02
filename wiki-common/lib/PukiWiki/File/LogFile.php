<?php

namespace PukiWiki\File;

use DirectoryIterator;
use Exception;
use PukiWiki\Auth\Auth;
use PukiWiki\Factory;
use PukiWiki\File\AbstractFile;
use PukiWiki\File\DiffFile;
use PukiWiki\File\File;
use PukiWiki\NetBios;
use PukiWiki\Spam\ProxyChecker;
use PukiWiki\Utility;
use PukiWiki\Backup;
use PukiWiki\File\FileFactory;

/**
 * ファイルの読み書きを行うクラス
 */
class LogFile extends AbstractFile{
	/**
	 * ログのキーの保持期間（30日）
	 */
	const LOG_LIFE_TIME = 18144000;
	/**
	 * ログの最大エントリ数（500もいらないと思う）
	 */
	const LOG_MAX_ENTRIES = 500;
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
	protected $kind;
	/**
	 * Wikiページに対するログか？
	 */
	protected $isWiki = true;
	/**
	 * コンストラクタ
	 * @param string $page
	 */
	public function __construct($page = null) {
		if (empty($this->kind)) throw new Exception('class :'.get_called_class().' does not defined $kind value.');
		$this->config = Utility::loadConfig('config-log.ini.php');

		if (!$this->isWiki) {
			if (empty($page)){
				throw new Exception('Page name is missing!');
			}
			// ページ名
			$this->page = $page;

			parent::__construct(self::$dir . $this->kind . '/' . Utility::encode($page) . '.txt');
		}else{
			// Wikiに保存する場合
			parent::__construct(DATA_DIR . Utility::encode($this->config[$this->kind]['file']) . '.txt');
		}
	}
	/**
	 * ファイル一覧を取得
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
	 * ログの存在チェック
	 */
	public function has(){
		if (!$this->config[$this->kind]['use']) return false;
		return parent::has();
	}
	/**
	 * ログ件数
	 */
	public function count(){
		return count(parent::get());
	}
	/**
	 * 共通チェック
	 */
	private function log_common_check()
	{
		global $log_ua;

		// 認証中のユーザ名
		$username = Auth::check_auth();

		// 認証済の場合
		if ($this->config['auth_nolog'] && !empty($username)) return null;

		// タイムスタンプ
		$utime = UTIME;
		// リモートIPを取得
		$ip = Utility::getRemoteIp();

		if (isset($this->config[$this->kind]['nolog_ip'])) {
			// ロギング対象外IP
			foreach ($this->config[$this->kind]['nolog_ip'] as $nolog_ip) {
				if ($ip == $nolog_ip) return null;
			}
		}
		$rc = array();
		$field = self::set_fieldname();

		foreach ($field as $key) {
			switch ($key) {
				case 'ts': // タイムスタンプ (UTIME)
					$rc[$key] = (int)$utime;
					break;
				case 'ip': // IPアドレス
					$rc[$key] = $ip;
					break;
				case 'host': // ホスト名 (FQDN)
					$rc[$key] = gethostbyaddr($ip);
					break;
				case 'auth_api': // 認証API名
					//$obj = new auth_api();
					//$msg = $obj->auth_session_get();
					$rc[$key] = (isset($msg['api']) && ! empty($username)) ? 'plus' : null;
					break;
				case 'local_id':
					$rc[$key] = isset($msg['local_id']) ? null : $msg['local_id'];
					break;
				case 'user': // ユーザ名(認証済)
					$rc[$key] = $username;
					break;
				case 'ntlm': // ユーザ名(NTLM認証)
					if (self::netbios_scope_check($ip,gethostbyaddr($ip))) {
						$obj_nbt = new NetBios($ip);
						$rc[$key] = $obj_nbt->username;
						unset($obj_nbt);
					} else {
						$rc[$key] = null;
					}
					break;
				case 'proxy': // Proxy情報
					$obj_proxy = new ProxyChecker();
					$rc[$key] = $obj_proxy->is_proxy() ? 
						$obj_proxy->get_proxy_info() . '(' . $obj_proxy->get_realip() . ')' :
						null;
					unset($obj_proxy);
					break;
				case 'ua': // ブラウザ情報
					$rc[$key] = $log_ua;
					break;
				case 'del': // 削除フラグ
					// 更新時は、削除されたか？
					$rc[$key] = ($this->kind === 'update' && Factory::Wiki($this->page)->has()) ? null : 'DELETE';
					break;
				case 'sig': // 署名(曖昧)
					$rc[$key] = self::log_set_signature($utime);
					break;
				case 'file': // ファイル名
					$rc[$key] = $this->kind;
					break;
				case 'page':
				case 'cmd':
					$rc[$key] = $this->page;
					break;
			}
		}
		return $rc;
	}
	/**
	 * 署名の特定
	 */
	private function log_set_signature($utime)
	{
		// $utime は、今後、閲覧者の特定などの際にバックアップファイルから
		// 特定することを想定し、含めている。

		if ($this->kind != 'update') return null;

		$diff = new DiffFile($this->page); // 差分ファイル名

		$lines = array();

		if ($diff->has()) {
			// 今回更新行のみ抽出
			foreach($diff->get() as $_src) {
				if (substr($_src,0,1) == '+') $lines[] = substr($_src,1);
			}
		} else {
			// 新規ページの全てが対象
			$lines = Factory::Wiki($page)->get();
		}

		return Auth::get_signature($lines);
	}
	/**
	 * 設定項目名を設定
	 * @static
	 */
	private function set_fieldname()
	{
		$idx = isset(self::$kind_no[$this->kind]) ? self::$kind_no[$this->kind] : 0;

		$rc = array();
		foreach(self::$field as $_field => $sw) {
			if ($sw[$idx] == 0) continue;
			if ($_field == 'page' && !isset($this->config[$this->kind]['file'])) continue;
			$rc[] = $_field;
		}

		return $rc;
	}
	
	/**
	 * ログ記入
	 * @param $value 未使用（何も入れないこと）
	 * @param $keeptimestamp 未使用（何も入れないこと）
	 */
	public function set($value = '', $keeptimestamp = false){
		// 設定
		$config = $this->config[$this->kind];
		
		// ログを取らない場合書き込まない
		if (!$config['use']) return;
		// 書き込むデーターを取得
		$rc = self::log_common_check();
		// ない場合終了
		if (empty($rc)) return;
		// ログを読み込む
		$lines = self::get(false);
		// 行数
		$count = count($lines);

		// 更新するキーを取得
		if (! empty($config['updtkey'])) {
			// 最低限記録するキー
			$mustkey = isset($config['mustkey']) ? $config['mustkey'] : 0;
			// 保存するキーを定義から取得
			$_key = explode(':',$config['updtkey']);
			// 設定項目名を取得
			$name = self::set_fieldname();

			// 更新フラグ
			$set = false;
			// 行の分解
			for($i=0;$i<$count;$i++) {
				// ログの１行を配列に変換した後、項目名を付与する
				// $line = array(
				//     'ts' => 000000,
				//     'ip' => 127.0.0.1
				//     ...
				// );
				// みたいな感じになる
				$line = self::line2field($lines[$i],$name);

				if (isset($line['ts']) && $line['ts'] <= UTIME - self::LOG_LIFE_TIME){
					// 一定期間過ぎたエントリは削除
					unset($line);
					continue;
				}

				// 行書き換えフラグ
				$sw_update = true;

				// 列の分解
				foreach($_key as $idx) {
					// 書き込む前のデーターと異なっていた場合
					if (isset($data[$idx]) && isset($fld[$idx]) && $data[$idx] != $line[$idx]) {
						$sw_update = false;
						break;
					}
/*
					if (empty($rc[$idx]) || empty($line[$idx])) {
						$sw_update = false;
						break;
					}
					if ($rc[$idx] !== $line[$idx]) {
						$sw_update = false;
						break;
					}
*/
				}
				
				if ($sw_update) {
					// 書き換え
					$lines[$i] = self::array2table($data);
					$set = true;
					break;
				}
			}

			unset($i);

			// 追記するデーター
			if (! $set) {
				if ($mustkey) {
					if (self::log_mustkey_check($_key,$data)) {
						$lines[] = self::array2table($data);
					}
				} else {
					$lines[] = self::array2table($data);
				}
			}
		} else {
			// 新規データー
			$lines[] = self::array2table( $data );
		}

		// 配列の長さ制限
		if ( $count > self::LOG_MAX_ENTRIES) {
			$i = 0;
			// 古いエントリから削除するため配列を反転
			foreach (array_reverse($lines) as $line){
				if ($i > self::LOG_MAX_ENTRIES) break;
				$ret[] = $line;
				$i++;
			}
			// 戻す
			$lines = array_reverse($ret);
		}

		// 保存（空行は削除）
		parent::set(array_filter($lines));
	}
	/**
	 * ログファイルを読む
	 */
	public function get($join = false, $legacy = false){
		if ( !$this->isFile() ) return false;
		if ( !$this->isReadable() )
			Utility::dieMessage(sprintf('File <var>%s</var> is not readable.', Utility::htmlsc($this->filename)));

		$name = self::get_log_field($this->kind);
		
		// ファイルの読み込み
		$file = $this->openFile('r');
		// ロック
		$file->flock(LOCK_SH);
		// 巻き戻し（要るの？）
		$file->rewind();
		// 初期値
		$result = array();
		// 1行毎ファイルを読む
		while (!$file->eof()) {
			$line = $file->fgets();
			$result[] = self::line2field($line, $name);
		}
		// アンロック
		$file->flock(LOCK_UN);
		// 念のためオブジェクトを開放
		unset($file);

		rsort($result); // 逆順にソート(最新順になる)

		// 出力
		return $result;
	}
	/**
	 * ログの必須キーチェック
	 */
	private static function log_mustkey_check($key,$data)
	{
		foreach($key as $idx) {
			if (empty($data[$idx])) return false;
		}
		return true;
	}
	/**
	 * 配列データを PukiWiki 表形式データに変換
	 * @static
	 */
	private static function array2table($data)
	{
		$rc = '';
		foreach ($data as $x1) {
			$rc .= '|'.$x1;
		}
		$rc .= '|';
		return $rc;
	}

	/**
	 * PukiWiki 表形式データかの判定
	 * @static
	 */
	private static function is_table($line)
	{
		$x = trim($line);
		if (substr($x,0,1) !== '|') return FALSE;
		if (substr($x,-1)  !== '|') return FALSE;
		return TRUE;
	}

	/**
	 * PukiWiki 表形式データを配列データに変換
	 * @static
	 */
	private static function table2array($x)
	{
		if (!self::is_table($x)) return array();
		return explode('|', substr($x,1,-1));
	}

	/**
	 * ログの１行を配列に変換した後、項目名を付与する
	 * @static
	 */
	public static function line2field($line,$name)
	{
		$_fld = self::table2array($line);
		$i = 0;
		$rc = array();
		foreach($name as $_name) {
			if (substr($_name,0,1) === '@') continue;

			$rc[$_name] = isset($_fld[$i]) ? $_fld[$i] : null;
			$i++;
		}
		return $rc;
	}
	/**
	 * NetBIOS の適用範囲決定
	 */
	private function netbios_scope_check($ip,$host)
	{
		static $ip_pattern = '/^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})(?:\/(.+))?$/';

		if (!$this->config['auth_netbios']['use']) return FALSE;

		$l_ip = ip2long($ip);
		$valid = (is_long($l_ip) and long2ip($l_ip) == $ip); // valid ip address

		$matches = array();
		foreach ($this->config['auth_netbios']['scope'] as $network)
		{
			if ($valid and preg_match($ip_pattern,$network,$matches))
			{
				$l_net = ip2long($matches[1]);
				$mask = array_key_exists(2,$matches) ? $matches[2] : 32;
				$mask = is_numeric($mask) ?
					pow(2,32) - pow(2,32 - $mask) : // "10.0.0.0/8"
					ip2long($mask);                 // "10.0.0.0/255.0.0.0"
				if (($l_ip & $mask) == $l_net) return TRUE;
			} else {
				if (preg_match('/'.preg_quote($network,'/').'/',$host)) return FALSE;
			}
		}
		return FALSE;
	}

	/**
	 * ログの表示指示項目の設定
	 * @static
	 */
	public function get_view_field()
	{
		$rc = self::set_fieldname($this->kind);

		// 認証済の判定
		$user = Auth::check_auth();

		$kind_view = empty($user) ? 'guest' : 'view';

		$chk = array();
		if (isset($this->config[$this->kind][$kind_view])){
			if ($this->config[$this->kind][$kind_view] == 'all'){
				return $rc;
			}else{
				$tmp = explode(':', $this->config[$this->kind][$kind_view]);

				// 妥当性チェック
				foreach($tmp as $_tmp) {
					$sw = 0;
					foreach($rc as $_name) {
						if ($_name == $_tmp) {
							$sw = 1;
							break;
						}
					}
					if (!$sw) continue;
					$chk[] = $_tmp;
				}
				unset($tmp, $sw);
			}
		}
		return $chk;
	}

	/**
	 * ログに書き出している項目のみ抽出する
	 * @static
	 */
	public function get_log_field()
	{
		// 全項目名を取得
		$all = self::set_fieldname($this->kind);
		$rc = array();
		foreach ($all as $field) {
			if (substr($field,0,1) == '@') continue; // 表示項目は除去
			$rc[] = $field;
		}
		return $rc;
	}

	/**
	 * 更新日時のバックアップデータの世代を確定する
	 * @static
	 */
	public static function get_backup_age($update_time,$update=true)
	{
		static $_page, $backup;

		//if (!isset($backup_page)) $backup_page = get_backup($page);
		//if (count($backup_page) == 0) return -1; // 存在しない
		if (!isset($backup)) $backup = new Backup($this->page);

		// 初回バックアップ作成は、文書生成日時となる
		$create_date = $backup->getBackup(1)->time;
		if ($update_time == $create_date) return 1;

		$match = -1;
		foreach ($backup->getBackup() as $age => $val)
		{
			if ($val['real'] == $update_time) {
				$match = $age;
			} elseif (! $update && $val['real'] < $update_time) {
				$match = $age;
			}
		}
		$match++; // ヒットした次が書き込んだ内容(バックアップなため)
		if ($age < $match) return 0; // カレント(diffを読む)
		if ($match > 0) return $match;
		return -1; // 存在しない(一致したものが存在しない)
	}
	/**
	 * 差分ファイルの存在確認
	 * @static
	 */
	public static function diff_exist()
	{
		return FileFactory::factory('diff',$this->page)->has();
	}
	/**
	 * ユーザを推測する
	 * @static
	 */
	public static function guess_user($user,$ntlm,$sig) {
		if (!empty($user)) return $user; // 署名ユーザ
		if (!empty($ntlm)) return $ntlm; // NTLM認証ユーザ
		if (!empty($sig))  return $sig;  // 本人の署名
		return null;
	}
	/**
	 * ホスト名のチェック (a と b のチェック)
	 * $level で指定された階層までで比較する
	 * @static
	 */
	public function check_host($a,$b,$level)
	{
		$tbl_a = array_reverse( explode('.',$a) );
		$ctr_a = count($tbl_a);
		$tbl_b = array_reverse( explode('.',$b) );
		$ctr_b = count($tbl_b);

		$max   = max($ctr_a, $ctr_b);
		$loop  = min($ctr_a, $ctr_b);

		$sw    = TRUE;
		for ($i=0; $i<$loop; $i++) {
			if ($tbl_a[$i] != $tbl_b[$i]) {
				$sw = FALSE;
				break;
			}
		}

		if ($i != $max) $sw = FALSE; // 打ち切り対応
		if ($sw) return array(TRUE,$i);
		if ($level == 0) return array(FALSE,$i); // 完全一致
		if ($level > $max) return array(TRUE,$i);
		// 指定レベルよりも一致している場合は真
		return ($i >= $level) ? array(TRUE,$i) : array(FALSE,$i);
	}
}