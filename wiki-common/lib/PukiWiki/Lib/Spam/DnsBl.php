namespace PukiWiki\Lib\Spam;

class Dnsbl{
	private $dnsbl_hosts = array(
		'dnsbl.spam-champuru.livedoor.com', // スパムちゃんぷるーDNSBL
		'opm.tornevall.org',                // TornevallNET Proxy and Abuse-filtering Project
		'all.rbl.jp',                       // Realtime Blackhole List Japan
	);
	public function __construct($ip, $dnsbl_hosts){
		if (isset($dnsbl_hosts) ){
			$this->dnsbl_hosts = $dnsbl_hosts;
		}
		// IPアドレスを取得
		if (function_exists('dns_get_record')) {
			// 内部関数 dns_get_record 使える場合
			$lookup = dns_get_record($domain, DNS_NS);
			if (!empty($lookup)) {
				foreach ($lookup as $record) {
					$ns_array[] = $record['target'];
				}
				$ns_found = TRUE;
			}
		}
		$this->ip = filter_var($ip, FILTER_VALIDATE_IP) ? $ip : gethostbynamel($ip);
	}
	/**
	 * DNSBLにリストされているか？
	 * @return string リストされていたホスト（リストされていない場合は、
	 */
	public static function is_listed(){
			$reverse_ip = implode('.',array_reverse(explode('.',$this->ip)));
			foreach($dnsbl_hosts as $host){
				if (checkdnsrr($reverse_ip.'.'.$host.'.','A')){
					return $host;
				}
			}
		}
		return false;
	}

	function bitmask ($bit = '')
	{
		$loadbits = 8; // Antal bitar att r臾na med
		for ($i = 0 ; $i < $loadbits ; ++$i) {$arr[] = pow(2,$i); }     // Automatisera bitv舐den
		for ($i = 0 ; $i < count($arr) ; ++$i) {$mask[$i] = ($bit & $arr[$i]) ? '1' : '0';}     // S舩t 1 till de bitv舐den som 舐 p蚶lagna
		return $mask;
	}

	function rblresolve ($ip = '', $rbldomain = '')
	{
		if (!$ip) {return false;}			// No data should return nothing
		if (!$rbldomain) {return false;}	// No rbl = ignore
		$returnthis = explode('.', gethostbyname(implode('.', array_reverse(explode('.', $ip))) . '.' . $rbldomain));		// Not ipv6-compatible!
		if (implode(".", $returnthis) != implode('.', array_reverse(explode('.', $_SERVER['REMOTE_ADDR']))) . '.' . $rbldomain) {
			return $returnthis;
		}
		return false;
	}
}