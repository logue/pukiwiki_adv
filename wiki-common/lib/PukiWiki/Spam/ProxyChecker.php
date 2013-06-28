<?php
/**
 * PukiWiki Plus! Proxy判定クラス
 *
 * @copyright
 *   Copyright &copy; 2012 PukiWiki Advance Developers Team
 *                    2004-2007, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @version	$Id: proxy.cls.php,v 0.8.1 2012/08/27 09:57:00 Logue Exp $
 * @license	http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace PukiWiki\Spam;

//require_once(LIB_DIR . 'spamplus.php');

/**
 * Proxy関連クラス
 * @abstract
 */
class ProxyChecker
{
	var $proxy = array(
		// 取得値は、上から下へ上書きする。下ほど有用。
		// 0:KEY, 1:Prox判定利用, 2:IP取得利用
		// ***** IP アドレス取得 *****
		array('HTTP_X_FORWARDED_FOR',	1,1), // プロキシサーバ経由の生IP
		array('HTTP_SP_HOST',			1,1), // ホスト情報
		array('HTTP_CLIENT_IP',			1,1),
		array('HTTP_FORWARDED',			1,1), // プロキシサーバの情報や生IP
		array('HTTP_PC_REMOTE_ADDR',	1,1),
		array('REMOTE_ADDR',            0,1),
		array('HTTP_CF_CONNECTING_IP',	0,1), // CloudFlare経由のアクセスの場合のIPアドレス
		// ***** PROXY 判定専用 *****
		array('HTTP_CACHE_INFO',		1,0), // プロキシサーバのキャッシュ情報
		array('HTTP_IF_MODIFIED_SINCE',	1,0), // プロキシサーバに接続した時間の情報
		array('HTTP_PROXY_CONNECTION',	1,0), // プロキシ関係の情報
		array('HTTP_VIA',				1,0), // プロキシの種類・バージョン等
		array('HTTP_XONNECTION',		1,0),
		array('HTTP_XROXY_CONNECTION',	1,0),
		array('HTTP_X_LOCKING',			1,0), // IPアドレス・REFERERなどの情報
		array('HTTP_X_TE',				1,0),
		// array('HTTP_HOST',			1,0), // ホスト情報 (仮に追加 09/28)
		// ***** 未使用 *****
		//array('HTTP_CACHE_CONTROL',	0,0), // プロキシサーバへのコントロール情報
		//array('HTTP_PRAGMA',			0,0),
	);

	/**
	 * Proxy経由かのチェック
	 */
	function is_proxy()
	{
		if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) return 0;	// CloudFlareはProxy扱いしない
		foreach ($this->proxy as $x) {
			if (!$x[1]) continue; // Proxy判定利用
			if (isset($_SERVER[$x[0]])) return 1;
		}
		return 0;
	}

	/**
	 * Real IPアドレスを戻す
	 * プライベートアドレスの場合もある
	 */
	function get_realip()
	{
		if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) return $_SERVER['HTTP_CF_CONNECTING_IP'];
		foreach ($this->proxy as $x) {
			if (!$x[2]) continue; // IP取得利用
			$rc = '';
			if (isset($_SERVER[$x[0]])) {
				$rc = trim($_SERVER[$x[0]]);
			}
			if (empty($rc)) continue;
			if (! is_ipaddr($rc)) continue;		// IPアドレス体系か？
			if (! is_localIP($rc)) return $rc;	// プライベートな生IPを取得してもあまり意味がない
		}
		return '';
	}

	/**
	 * Proxy経由かのチェック
	 */
	function get_proxy_info()
	{
		$rc = '';
		if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) return '(CloudFlare)';
		foreach ($this->proxy as $x) {
			if (!$x[1]) continue; // Proxy判定利用
			if (isset($_SERVER[$x[0]])) {
				$rc .= '('.$x[0].':'.$_SERVER[$x[0]].')';
			}
		}
		return $rc;
	}
}

function is_localIP($ip)
{
	static $localIP = array('127.0.0.0/8','10.0.0.0/8','172.16.0.0/12','192.168.0.0/16');
	if (is_ipaddr($ip) === FALSE) return FALSE;
	return ip_scope_check($ip,$localIP);
}

function is_ipaddr($ip)
{
	$valid = ip2long($ip);
	return ($valid == -1 || $valid == FALSE) ? FALSE : $valid;
}

function proxy_get_real_ip()
{
	$obj = new ProxyChecker();
	return $obj->get_realip();
}

function is_proxy()
{
	$obj = new ProxyChecker();
	$ip = $obj->get_realip();
	if (!empty($ip) && MyNetCheck($ip)) return false;
	return $obj->is_proxy();
}

function MyNetCheck($ip)
{
	global $log_common, $log_ua;

	$config = new Config(CONFIG_SPAM_WL_PRIVATE_NET);
	$config->read();
	$private_ip = $config->get('IP');
	$dynm_host = $config->get('DYNAMIC_HOST');
	// $hosts = $config->get('HOST');
	unset($config);

	$dynm_ip = array();
	foreach($dynm_host as $host){
		$tmp = gethostbyname($host);
		if ($host == $tmp) continue; // IPが求まらない
		$dynm_ip[] = $tmp;
	}
	unset($tmp);

	$obj = new IPBL();

	if (! empty($log_common['nolog_ip'])) {
		$obj->setMyNetList( array(array_merge($private_ip, $log_common['nolog_ip'], $dynm_ip)) );
	} else {
		$obj->setMyNetList( array(array_merge($private_ip, $dynm_ip)) );
	}

	$hosts = (! is_array($ip)) ? array($ip) : $ip;

	foreach($hosts as $host) {
		$obj->setName($host);
		if ($obj->isMyNet()) return true;
	}
	return false;
}

// IP の判定
function ip_scope_check($ip,$networks)
{
	// $l_ip = ip2long( ip2arrangement($ip) );
	$l_ip = ip2long($ip);
	foreach($networks as $network) {
		$range = explode('/', $network);
		$l_network = ip2long( ip2arrangement($range[0]) );
		// $l_network = ip2long( $range[0] );
		if (empty($range[1])) $range[1] = 32;
		$subnetmask = pow(2,32) - pow(2,32 - $range[1]);
		if (($l_ip & $subnetmask) == $l_network) return TRUE;
	}
	return FALSE;
}

// ex. ip=192.168.101.1 from=192.168.0.0 to=192.168.211.12
function ip_range_check($ip,$from,$to)
{
	if (empty($to)) return ip_scope_check($ip,array($from));
		$l_ip = ip2long($ip);
		$l_from = ip2long( ip2arrangement($from) );
		$l_to = ip2long( ip2arrangement($to) );
		return ($l_from <= $l_ip && $l_ip <= $l_to);
}

// ex. 10 -> 10.0.0.0, 192.168 -> 192.168.0.0
function ip2arrangement($ip)
{
	$x = explode('.', $ip);
	if (count($x) == 4) return $ip;
	for($i=0;$i<4;$i++) { if (empty($x[$i])) $x[$i] =0; }
	return sprintf('%d.%d.%d.%d',$x[0],$x[1],$x[2],$x[3]);
}

// 予約されたドメイン
function is_ReservedTLD($host)
{
	// RFC2606
	static $ReservedTLD = array('example' =>'','invalid' =>'','localhost'=>'','test'=>'',);
	$x = array_reverse(explode('.', strtolower($host) ));
	return (isset($ReservedTLD[$x[0]])) ? TRUE : FALSE;
}

/* End of file proxy.cls.php */
/* Location: ./wiki-common/lib/proxy.cls.php */
