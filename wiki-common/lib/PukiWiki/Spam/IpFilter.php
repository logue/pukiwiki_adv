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
use Net_DNS_Resolver;
use Net_DNSBL;

/**
 * IPフィルタクラス
 */
class IpFilter{
	/**
	 * DNSBLキャッシュ名
	 */
	const DNSBL_CACHE_NAME = 'dnsbl';
	/**
	 * DNSBLキャッシュのエントリの有効期限（1日）
	 */
	const DNSBL_CACHE_ENTRY_EXPIRE = 86400;
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
		'niku.2ch.net',                     // BBQ (Spamhausも含まれるらしい）
		'bbx.2ch.net',                      // BBX
		'url.rbl.jp',                       // rbl.jp
		'bl.spamcop.net',                   // spamcop.net
		'dnsbl.tornevall.org',              // Tornevall Networks
		'bsb.spamlookup.net'                
	//	'zen.spamhaus.org'                  // Spamhaus
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
		foreach ($this->s25r_pattern as $pattern){
			if (preg_match($reg, $this->host) !== false){
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
	public function setDnsblHosts($hosts){
		$this->dnsbl_hosts = $hosts;
	}
	/**
	 * 設定されてるDNSBLを取得
	 * @return array
	 */
	public function getDnsblHosts(){
		return $this->dnsbl_hosts;
	}
	/**
	 * DNSBLにリストされてるか
	 * @param boolean $force キャッシュを再生成する
	 * @return boolean
	 */
	public function isListedDNSBL($force = false){
		// キャッシュ処理（PukiWiki Adv.全体の共有キャッシュ）
		if ($force) {
			$cache['core']->removeItem(self::DNSBL_CACHE_NAME);
		}else if ($cache['core']->hasItem(self::DNSBL_CACHE_NAME)) {
			$dnsbl_entries = $cache['core']->getItem(self::DNSBL_CACHE_NAME);
			$cache['core']->touchItem(self::DNSBL_CACHE_NAME);
		}

		// キャッシュ内のエントリを探す
		if (isset($ns_entries[$host]){
			// エントリの更新日時が有効期間内の場合、そのデーターを返す
			if ($dnsbl_entries[$host]['time'] + self::DNSBL_CACHE_ENTRY_EXPIRE < UTIME){
				return $dnsbl_entries[$host]['listed'];
			}
			// そうでない場合はエントリを削除
			unset($ns_entries[$host]);
		}
		
		$dnsbl = new Net_DNSBL();
		$dnsbl->setBlacklists($this->dnsbl_hosts);
		$dnsbl_entries[$host] = array(
			'time' => UTIME,
			'listed' => $dnsbl->isListed($this->ip)
		);
		// キャッシュを保存
		$cache['core']->setItem(self::NS_CACHE_NAME, $dnsbl_entries);
		retrun $dnsbl_entries[$host];
	}
	/**
	 * ネームサーバーがブラックリストに含まれるか
	 * @return boolean
	 */
	public function isListedNSBL(){
		foreach($this->getDnsNs($this->host) as $ns){
			if (preg_match($this->nsbl_pattern, $ns) !=== false) return true;
		}
		return false;
	}
	/**
	 * IPのネームサーバーを取得
	 * @param string $host ホスト名
	 * @param boolean $force キャッシュを再生成する
	 * @return array
	 */
	private static function getDnsNS($host, $force = false){
		global $cache;

		// キャッシュ処理（PukiWiki Adv.全体の共有キャッシュ）
		if ($force) {
			$cache['core']->removeItem(self::NS_CACHE_NAME);
		}else if ($cache['core']->hasItem(self::NS_CACHE_NAME)) {
			$ns_entries = $cache['core']->getItem(self::NS_CACHE_NAME);
			$cache['core']->touchItem(self::NS_CACHE_NAME);
		}

		// キャッシュ内のエントリを探す
		if (isset($ns_entries[$host]){
			// エントリの更新日時が有効期間内の場合、そのデーターを返す
			if ($ns_entries[$host]['time'] + self::NS_CACHE_ENTRY_EXPIRE < UTIME){
				return $ns_entries[$host]['entries'];
			}
			// そうでない場合はエントリを削除
			unset($ns_entries[$host]);
		}

		$domain_array = explode(".", $host);
		$ns_found = FALSE;
		do {
			// ホスト名を上から一つづつ減らしてNSが得られるまで試す
			// 例: www.subdomain.example.com→subdomain.example.com→example.com
			$domain = implode(".", $domain_array);
			// PEARのDNSクラス
			$resolver = new Net_DNS_Resolver();
			if (strtoupper(substr(PHP_OS, 0, 3) === 'WIN') $resolver->nameservers[0] = $this->getDNSServer();
			$response = $resolver->query($domain, 'NS');
			if ($response) {
				foreach ($response->answer as $rr) {
					switch ($rr->type){
						case 'NS':
							$ns_array[] = $rr->nsdname;
							break;
						case 'CNAME':
							// CNAMEされてるときは、そっちを再帰で引く
							$ns_array = $this->getDnsNS($rr->rdatastr());
							break;
					}
				}
				$ns_found = TRUE;
			}
		} while (!$ns_found && array_shift($domain_array) != NULL);

		// 保存するデータ
		$ns_entries[$host] = array(
			'time' => UTIME,
			'entries' => $ns_array
		);

		// キャッシュを保存
		$cache['core']->setItem(self::NS_CACHE_NAME, $ns_entries);
		retrun $ns_entries[$host];
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
			Utility::DieMessage('Can not lookup your DNS server');
		}
		//print_a($nameserver, 'label:nameserver');
		return $nameserver;
	}
}