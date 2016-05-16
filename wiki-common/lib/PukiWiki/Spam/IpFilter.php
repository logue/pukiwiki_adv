<?php
/**
 * IPによるスパムフィルター
 * 参考：spam_filter.php（http://miasa.info/index.php?%C8%FE%CB%E3Wiki%A4%C7%A5%B7%A5%B9%A5%C6%A5%E0%C5%AA%A4%CB%BD%A4%C0%B5%A4%B7%A4%C6%A4%A4%A4%EB%C5%C0）
 *
 * @package   PukiWiki\Spam\IpFilter
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013 PukiWiki Advance Developers Team
 * @create    2013/05/29
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: IpFilter.php,v 1.0.0 2013/05/29 18:58:00 Logue Exp $
 */

namespace PukiWiki\Spam;

use PukiWiki\Utility;

/**
 * IPフィルタクラス
 */
class IpFilter{
	/**
	 * DNSBLキャッシュ名
	 */
	const BL_CACHE_NAME = 'dnsbl';
	/**
	 * DNSBLキャッシュのエントリの有効期限（1日）
	 */
	const BL_CACHE_ENTRY_EXPIRE = 86400;
	/**
	 * DNSキャッシュ名
	 */
	const NS_CACHE_NAME = 'nameserver';
	/**
	 * NSキャッシュのエントリの有効期限（30日）
	 */
	const NS_CACHE_ENTRY_EXPIRE = 2592000;
	/**
	 * IPアドレスの正規表現
	 */
	const IP_MATCH_PATTERN = '/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/';
	/**
	 * S25Rの正規表現
	 * @url http://www.gabacho-net.jp/anti-spam/anti-spam-system.html
	 */
	private static $s25r_pattern = array(
		// 1,逆引きFQDNの最下位（左端）の名前が、数字以外の文字列で分断された二つ以上の数字列を含む
		'/^[^.]*[0-9][^0-9.]+[0-9].*\./',
		// 2,逆引きFQDNの最下位の名前が、5個以上連続する数字を含む
		'/^[^.]*[0-9]{5}/',
		// 3,逆引きFQDNの上位3階層を除き、最下位または下位から2番目の名前が数字で始まる
		'/^([^.]+\.)?[0-9][^.]*\.[^.]+\..+\.[a-z]/',
		// 4,逆引きFQDNの最下位の名前が数字で終わり、かつ下位から2番目の名前が、1個のハイフンで分断された二つ以上の数字列を含む
		'/^[^.]*[0-9]\.[^.]*[0-9]-[0-9]/',
		// 5,逆引きFQDNが5階層以上で、下位2階層の名前がともに数字で終わる
		'/^[^.]*[0-9]\.[^.]*[0-9]\.[^.]+\..+\./',
		// 6,逆引きFQDNの最下位の名前が「dhcp」、「dialup」、「ppp」、またはDSL系の名前で始まり、かつ数字を含む
		'/^(dhcp|dialup|ppp|[achrsvx]?dsl)[^.]*\.*(dynamic)*[0-9]/',    //
		// ブラックリスト
		//  a, 末端ホスト名が十六進番号を含む
		//  b, 末端ホスト名が、番号を表す英字を含む
		//  c, メール中継サーバであるかのようなFQDNである
		//  d, ドメインを代表するメールサーバがスパムを送信する
		'/\.(internetdsl|adsl|sdi)\.tpnet\.pl$/',
		'/^user.+\.mindspring\.com$/',
		'/^[0-9a-f]{8}\.(.+\.)?virtua\.com\.br$/',
		'/\.catv\.broadband\.hu$/',
		'/[0-9a-f]{4}\.[a-z]+\.pppool\.de$/',
		'/\.dip[0-9]+\.t-ipconnect\.de$/',
		'/\.dip\.t-dialin\.net$/',
		'/\.dyn\.optonline\.net$/',
		'/\.(adsl|cable)\.wanadoo\.nl$/',
		'/\.ipt\.aol\.com$/'
	);
	/**
	 * デフォルトのDNSBLリスト（先頭に.入れないこと）
	 */
	private $dnsbl_hosts = array(
		'all.rbl.jp',                       // rbl.jp
		'bl.spamcop.net',                   // spamcop.net
		'dnsbl.tornevall.org',              // Tornevall Networks
		'bsb.spamlookup.net'
	);
	/**
	 * ネームサーバーのブラックリスト
	 */
	private $nsbl_pattern = '/(\.dnsfamily\.com|\.xinnet\.cn|\.xinnetdns\.com|\.bigwww\.com|\.4everdns\.com|\.myhostadmin\.net|\.dns\.com\.cn|\.hichina\.com|\.cnmsn\.net|\.focusdns\.com|\.cdncenter\.com|\.cnkuai\.cn|\.cnkuai\.com|\.cnolnic\.com|\.dnspod\.net|\.mywebserv\.com|216\.195\.58\.5[0-9])/i';
	/**
	 * コンストラクタ
	 * @param $ip_host IPアドレスもしくはホスト
	 */
	public function __construct($ip_host = null){
		// 入力がIPか？
		$is_ip = preg_match(self::IP_MATCH_PATTERN, $ip_host);
		// IPアドレス（空欄時は、アクセスしてきたIPを指定）
		$this->ip = empty($ip) ? Utility::getRemoteIp() : (!$is_ip ? gethostbyname($ip_host) : $ip_host);
		// ホスト名で判断する
		$this->host = $is_ip ? gethostbyaddr($this->ip) : $this->ip;
	}
	/**
	 * 動的IP（S25R）であるか
	 * @return boolean;
	 */
	public function isS25R(){
		// 逆引きできないホスト（ルール0に相当）
		if ($this->ip === $this->host) return true;
		
		// 正規表現でチェック
		foreach (self::$s25r_pattern as $pattern){
			if (preg_match($pattern, $this->host)){
				return true;
			}
		}
		return false;
	}
	/**
	 * DNSBLをセット
	 * @param array $hosts DNSBLホスト
	 * @return void
	 */
	public function setBlHosts($hosts){
		$this->dnsbl_hosts = $hosts;
	}
	/**
	 * 設定されてるDNSBLを取得
	 * @return array
	 */
	public function getBlHosts(){
		return $this->dnsbl_hosts;
	}
	/**
	 * DNSBLにリストされてるか
	 * @param boolean $force キャッシュを再生成する
	 * @return string
	 */
	public function checkHost($force = false){
		global $cache;
		$bl_entries = array();

		// キャッシュ処理（PukiWiki Adv.全体の共有キャッシュ）
		if ($force) {
			$cache['core']->removeItem(self::BL_CACHE_NAME);
		}else if ($cache['core']->hasItem(self::BL_CACHE_NAME)) {
			// キャッシュからデーターを取得
			$bl_entries = $cache['core']->getItem(self::BL_CACHE_NAME);
			// キャッシュのタイムスタンプを更新
			$cache['core']->touchItem(self::BL_CACHE_NAME);
		}

		// キャッシュ内のエントリを探す
		if (isset($bl_entries[$this->ip])) {
			// エントリの更新日時が有効期間内の場合、そのデーターを返す
			if ($bl_entries[$this->ip]['time'] + self::BL_CACHE_ENTRY_EXPIRE < UTIME){
				return $bl_entries[$this->ip]['listed'];
			}
			// そうでない場合はエントリを削除
			unset($bl_entries[$this->ip]);
		}

		$bl_entries[$this->ip] = array(
			'time' => UTIME,	// チェックした時間のタイムスタンプ
			'listed' => $this->lookupBl()	// ルックアップ（falseか、判定を受けたDNSBLホスト）
		);
		// キャッシュを保存
		$cache['core']->setItem(self::BL_CACHE_NAME, $bl_entries);
		return $bl_entries[$this->ip]['listed'];
	}
	/**
	 * ネームサーバーがブラックリストに含まれるか
	 * @return boolean
	 */
	public function checkHostNs(){
		return preg_match($this->nsbl_pattern, $this->getDnsNS()) ? true : false;
	}
	/**
	 * DNSBLにIPを渡してヒットした場合、そのDNSBLを返す
	 * @return string
	 */
	private function lookupBl(){
		$listed = false;
		// IPをひっくり返す
		$rip = join('.',array_reverse(explode('.',$this->ip)));
		
		foreach ($this->dnsbl_hosts as $dns) {
			// DNSBLのホストに先ほどのひっくり返したIPをつけて、DNSアドレスを取得
			// 1.0.0.127.dnsbl.net A
			if (function_exists("checkdnsrr")) {
				if (checkdnsrr($rip.'.'.$dns . '.',"A")) {
					$listed .= $dns . ' ';
				}
			}else if (substr(PHP_OS, 0, 3) === 'WIN') {
				$result = '';
				@exec('nslookup -type=A ' . $rip . '.' . $dns . '.', $result);
				foreach ($result as $line) {
					if (strstr($line, $dns)) {
						$listed .= $rip . '.' . $dns . ' ';
					}
				}
			}
		}
		return $listed;
/*
		foreach ($this->dnsbl_hosts as $dns) {
			$lookup = implode('.', array_reverse(explode('.', $this->ip))) . '.' . $dns;
			$result = gethostbyname($lookup);
			if ($result !== $lookup) {
				return $dns;
			}
		}
		return false;
*/
	}
	/**
	 * IPのネームサーバーを取得
	 * @param string $host ホスト名
	 * @param boolean $force キャッシュを再生成する
	 * @return array
	 */
	private static function getDnsNS($force = false){
		global $cache;
		$ns_entries = array();

		// キャッシュ処理（PukiWiki Adv.全体の共有キャッシュ）
		if ($force) {
			$cache['core']->removeItem(self::NS_CACHE_NAME);
		}else if ($cache['core']->hasItem(self::NS_CACHE_NAME)) {
			$ns_entries = $cache['core']->getItem(self::NS_CACHE_NAME);
			$cache['core']->touchItem(self::NS_CACHE_NAME);
		}

		// キャッシュ内のエントリを探す
		if (isset($ns_entries[$this->host])) {
			// エントリの更新日時が有効期間内の場合、そのデーターを返す
			if ($ns_entries[$this->host]['time'] + self::NS_CACHE_ENTRY_EXPIRE < UTIME){
				return $ns_entries[$this->host]['entries'];
			}
			// そうでない場合はエントリを削除
			unset($ns_entries[$this->host]);
		}

		$domain_array = explode(".", $this->host);
		$ns_found = FALSE;
		do {
			// ホスト名を上から一つづつ減らしてNSが得られるまで試す
			// 例: www.subdomain.example.com→subdomain.example.com→example.com
			$lookup = dns_get_record(implode(".", $domain_array), DNS_NS);

			if (!empty($lookup)) {
				foreach ($lookup as $record) {
					$ns_array[] = $record['target'];
				}
				$ns_found = TRUE;
			}
		} while (!$ns_found && array_shift($domain_array) != NULL);

		// 保存するデータ
		$ns_entries[$this->host] = array(
			'time' => UTIME,
			'entries' => $ns_array
		);

		// キャッシュを保存
		$cache['core']->setItem(self::NS_CACHE_NAME, $ns_entries);
		return $ns_entries[$this->host];
	}
	/**
	 * Windows XP SP2, Vista SP1でDNSサーバーを取得する
	 * @return string
	 */
	function getDNSServer(){
		@exec('ipconfig /all', $ipconfig);
		//print_a($ipconfig, 'label:nameserver');
		foreach ($ipconfig as $line) {
			if (preg_match('/\s*DNS .+:\s+([\d\.]+)$/', $line, $nameservers)) {
				$nameserver = $nameservers[1];
			}
		}
		if (empty($nameserver)) {
			Utility::dieMessage('Can not lookup your DNS server');
		}
		//print_a($nameserver, 'label:nameserver');
		return $nameserver;
	}
}