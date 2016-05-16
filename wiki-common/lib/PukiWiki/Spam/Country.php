<?php
namespace PukiWiki\Spam;

class Countrty{
	public static function getCountry($allow_countory, $deny_countory){
		// Block countory via Geolocation
		$country_code = false;
		if (isset($_SERVER['HTTP_CF_IPCOUNTRY'])){
			// CloudFlareを使用している場合、そちらのGeolocationを読み込む
			// https://www.cloudflare.com/wiki/IP_Geolocation
			$country_code = $_SERVER['HTTP_CF_IPCOUNTRY'];
		}else if (isset($_SERVER['GEOIP_COUNTRY_CODE'])){
			// サーバーが$_SERVER['GEOIP_COUNTRY_CODE']を出力している場合
			// Apache : http://dev.maxmind.com/geoip/mod_geoip2
			// nginx : http://wiki.nginx.org/HttpGeoipModule
			// cherokee : http://www.cherokee-project.com/doc/config_virtual_servers_rule_types.html
			$country_code = $_SERVER['GEOIP_COUNTRY_CODE'];
		}else if (function_exists('geoip_db_avail') && geoip_db_avail(GEOIP_COUNTRY_EDITION) && function_exists('geoip_region_by_name')) {
			// それでもダメな場合は、phpのgeoip_region_by_name()からGeolocationを取得
			// http://php.net/manual/en/function.geoip-region-by-name.php
			$geoip = geoip_region_by_name(REMOTE_ADDR);
			$country_code = $geoip['country_code'];
			if (DEBUG) {
				$info[] = (!empty($geoip['country_code']) ) ?
					'GeoIP is usable. Your country code from IP is inferred <var>'.$geoip['country_code'].'</var>.' :
					'GeoIP is NOT usable. Maybe database is not installed. Please check <a href="http://www.maxmind.com/app/installation?city=1" rel="external">GeoIP Database Installation Instructions</a>';
			}
		}else if (function_exists('apache_note')) {
			// Apacheの場合
			$country_code = apache_note('GEOIP_COUNTRY_CODE');
		}

		if (DEBUG){
			// 使用可能かをチェック
			$info[] = isset($country_code) && !empty($country_code) ?
				'Your country code from IP is inferred <var>' . $country_code . '</var>.' :
				'Seems Geolocation is not available. <var>' . $deny_countory . '</var> value and <var>' . $allow_countory . '</var> value is ignoled.';
		}

		return $country_code;
	}
	public static function check($allow_countory, $deny_countory){
		$country = self::getCountry($allow_countory, $deny_countory);
		if (DEBUG && $country === false) {
			$info[] = 'Sorry, Your server does not available Geolocation. <var>' . $deny_countory . '</var> value and <var>' . $allow_countory . '</var> value is ignoled.';
		}
		if (!empty($deny_countory) && in_array($this->country, $deny_countory)) {
			die('Sorry, access from your country(' . $geoip['country_code'] . ') is prohibited.');
			exit;
		}
		if (!empty($allow_countory) && !in_array($this->country, $allow_countory)) {
			die('Sorry, access from your country(' . $geoip['country_code'] . ') is prohibited.');
			exit;
		}
	}
}