<?php
/**
 * 時刻クラス
 *
 * @package   PukiWiki\Time
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2013 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Time.php,v 1.0.0 2013/01/29 19:54:00 Logue Exp $
 */

namespace PukiWiki;

use PukiWiki\Lang\AcceptLanguage;
use PukiWiki\TimeZone;

class Time{
	public $utime, $mutime, $zone, $zonetime;
	/**
	 * 基準となる時刻を設定する
	 * @global type $language
	 * @global type $use_local_time
	 * @return array
	 */
	public static function init() {
		global $language, $use_local_time;

		if ($use_local_time) {
			list($zone, $zonetime) = self::setTimeZone( DEFAULT_LANG );
		} else {
			list($zone, $zonetime) = self::setTimeZone( $language );
			list($l_zone, $l_zonetime) = self::getTimeZoneLocal();
			if ($l_zonetime != '' && $zonetime != $l_zonetime) {
				$zone = $l_zone;
				$zonetime = $l_zonetime;
			}
		}
		/*
		$this->utime = time();
		$this->mutime = self::getMicroTime();
		$this->zone = $zone;
		$this->zonetime = $zonetime;
		 */

		foreach(array('UTIME'=>time(),'MUTIME'=>self::getMicroTime(),'ZONE'=>$zone,'ZONETIME'=>$zonetime) as $key => $value ){
			defined($key) or define($key,$value);
		}
		return array($zone, $zonetime);
	}
	public static function getMicroTime(){
		list($usec, $sec) = explode(' ', microtime());
		return ((float)$sec + (float)$usec);
	}
	/**
	 * 言語からTimeZoneを指定
	 * @param string $lang 言語
	 * @return array
	 */
	public static function setTimeZone($lang='')
	{
		if (empty($lang)) {
			return array('UTC', 0);
		}
		$l = AcceptLanguage::splitLocaleStr( $lang );

		// When the name of a country is uncertain (国名が不明な場合)
		if (empty($l[2])) {
			$obj_l2c = new Lang2Country();
			$l[2] = $obj_l2c->getLang2Country($l[1]);
			if (empty($l[2])) {
				return array('UTC', 0);
			}
		}

		$obj = new TimeZone();
		$obj->set_datetime(UTIME); // Setting at judgment time. (判定時刻の設定)
		$obj->set_country($l[2]); // The acquisition country is specified. (取得国を指定)

		// With the installation country in case of the same
		// 設置者の国と同一の場合
		if ($lang == DEFAULT_LANG) {
			if (defined('DEFAULT_TZ_NAME')) {
				$obj->set_tz_name(DEFAULT_TZ_NAME);
			}
		}

		list($zone, $zonetime) = $obj->get_zonetime();

		if ($zonetime == 0 || empty($zone)) {
			return array('UTC', 0);
		}

		return array($zone, $zonetime);
	}
	/**
	 * 時差を取得
	 * @param string $format
	 * @param int $timestamp
	 * @return string
	 */
	private static function getZoneTimeOffset($zonetime){
		$pm = ($zonetime < 0) ? '-' : '+';
		$zonetime = abs($zonetime);
		(int)$h = $zonetime / 3600;
		$m = $zonetime - ($h * 3600);
		return sprintf('%s%02d%02d', $pm,$h,$m);
	}
	/**
	 * 時差を考慮した時刻を取得
	 */
	public static function getZoneTimeDate($format, $timestamp = NULL) {
		/*
		 * $format で指定される T を ZONE で置換したいが、
		 * date 関数での書式指定文字となってしまう可能性を回避するための事前処理
		 */
		$l = strlen(ZONE);
		$zone = '';
		for($i=0;$i<$l;$i++) {
			$zone .= '\\'.substr(ZONE,$i,1);
		}

		$format = str_replace('\T','$$$',$format); // \T の置換は除く
		$format = str_replace('T',$zone,$format);
		$format = str_replace('$$$','\T',$format); // \T に戻す

		$time = ZONETIME + (($timestamp !== NULL) ? $timestamp : UTIME);
		$str = gmdate($format, $time);
		if (ZONETIME == 0) return $str;

		$zonetime = self::getZoneTimeOffset(ZONETIME);
		return str_replace('+0000', $zonetime, $str);
	}
	/**
	 * ローカルのTimeZoneを取得
	 * @return array
	 */
	private static function getTimeZoneLocal()
	{
		if (! isset($_COOKIE['timezone'])) return array('','');

		$tz = trim($_COOKIE['timezone']);

		$offset = substr($tz,0,1);
		switch ($offset) {
			case '-':
			case '+':
				$tz = substr($tz,1);
				break;
			default:
				$offset = '+';
		}

		$h = substr($tz,0,2);
		$i = substr($tz,2,2);

		$zonetime = ($h * 3600) + ($i * 60);
		$zonetime = ($offset == '-') ? $zonetime * -1 : $zonetime;

		return array($offset.$tz, $zonetime);
	}
	/**
	 * 日時を出力
	 * @param int $val 時間（マイクロ秒にて）
	 * @param 
	 */
	public static function format($time, $quote = FALSE, $format = null)
	{
		global $date_format, $time_format, $_labels;

		$time += ZONETIME;
		$wday = date('w', $time);

		$week   = $_labels['week'][$wday];

		if ($wday == 0) {
			// Sunday
			$style = 'week_sun';
		} else if ($wday == 6) {
			// Saturday
			$style = 'week_sat';
		}else{
			$style = 'week_day';
		}
		if (!isset($format)){
			$date = date($date_format, $time) .
				'(<abbr class="' . $style . '" title="' . $week[1]. '">'. $week[0] . '</abbr>)' .
				gmdate($time_format, $time);
		}else{
			$month  = $_labels['month'][date('n', $time)];
			$month_short = $month[0];
			$month_long = $month[1];


			$date = str_replace(
				array(
					date('M', $time),	// 月。3 文字形式。
					date('l', $time),	// 曜日。フルスペル形式。
					date('D', $time)		// 曜日。3文字のテキスト形式。
				),
				array(
					'<abbr class="month" title="' . $month[1]. '">'. $month[0] . '</abbr>',
					$week[1],
					'(<abbr class="' . $style . '" title="' . $week[1]. '">'. $week[0] . '</abbr>)'
				),
				gmdate($format, $time)
			);
		}

		return $quote ? '(' . $date . ')' : $date;
	}
	/**
	 * ページ作成の所要時間を計算
	 * @return string
	 */
	public static function getTakeTime(){
		// http://pukiwiki.sourceforge.jp/dev/?BugTrack2%2F251
		return sprintf('%01.03f', self::getMicroTime() - $_SERVER['REQUEST_TIME']);
	}
	/**
	 * 経過時間を取得
	 * @return string
	 */
	public static function passage($time){
		static $units = array('m'=>60, 'h'=>24, 'd'=>1);
		$_time = max(0, (MUTIME - $time) / 60); // minutes

		foreach ($units as $unit=>$card) {
			if ($time < $card) break;
			$_time /= $card;
		}
		return floor($_time) . $unit;
	}
}