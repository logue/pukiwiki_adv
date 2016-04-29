<?php
/**
 * A SETUP OF A PUBLIC HOLIDAY
 *
 * @author      Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @version     $Id: public_holiday.php,v 2.0 2007/08/05 15:19:00 upk Exp $
 * @license     http://opensource.org/licenses/gpl-license.php GNU Public License (GPL2)
 *
 * Function Name: public_holiday(Year,Month,Day)
 * Return Value : array
 * ['rc']   0:Weekday            (平日)
 *          1:Public holiday     (国民の祝日)
 *          2:Substitute holiday (振替休日)
 *          3:National holiday   (国民の休日)
 * ['name'] Public holiday name  (祝日名称)
 * ['w']    0-6: day of the week
 */

/* *************************************************************************
 * COUNTRY:
 * The country code which applies a public holiday is defined.
 * This is a 2 characters (Alpha) code defined by ISO3166.
 * See: http://www.iso.ch/iso/en/prods-services/iso3166ma/
 * STATE:
 * ONLY USA.
 * FIPS (Federal Information Processing Standards) State Alpha Code
 * See: http://www.itl.nist.gov/fipspubs/fip5-2.htm
 *************************************************************************** */

/*
defined('COUNTRY') or define('COUNTRY','US');
defined('STATE')   or define('STATE','GU');	// Guam
defined('STATE')   or define('STATE','HI');	// Hawaii
*/

/*
 * CLASS: public_holiday
 * public_holiday($y,$m,$d)	constructor						コンストラクタ
 * init($y,$m,$d)		initialize
 * is_holiday()
 * get_rc()
 * set_public_holiday()
 * ph_SpecificDay()		Fixed Public Holiday					固定祝日算出
 * ph_Calculation()		Calculation of a public holiday				祝日の計算 (春分の日・秋分の日計算)
 * ph_HappyMonday()		Move Public Holiday					移動祝日算出
 * ph_SubstituteHoliday()	Substitute holiday					振替休日
 * set_SubstituteHoliday()	Substitute holiday					振替休日
 * ph_NationalHoliday()		Dummy
 * chk_from_to($from,$to)	Applicable period check					適用期間判定
 *
 * CLASS: public_holiday_jp
 * public_holiday_jp($y,$m,$d)	コンストラクタ
 * ph_SubstituteHoliday()	第３条第２項 (振替休日)
 * jp_SubstituteHoliday()	振替休日 ２００７年以降
 * ph_NationalHoliday()		第３条第３項 (国民の休日)
 * jp_NationalHoliday()		国民の休日
 *
 * CLASS: public_holiday_us
 * public_holiday_us($y,$m,$d)	constructor
 * tbl_SpecificDay_State()	Fixed Public Holiday
 * tbl_HappyMonday_State()	Move Public Holiday
 *
 *
 * Function Name		Description
 * public_holiday($y,$m,$d)	A SETUP OF A PUBLIC HOLIDAY				祝日判定(メイン)
 * is_LastDayOfWeek($y,$m,$d)	The last day of the week				最終週判定
 * zeller($y,$m,$d)		Calculation of a day of the week			zellerの公式 (曜日算出)
 * mkdate($y,$m,$d,$offset)	The date of the specified displacement is computed	日付の加減
 * date2jd()			Calculation of the Julius day				日付からジュリアンデートを算出
 * jd2date($jd)			A date is set up from the Julius day			ジュリアンデートから日付を算出
 * VernalEquinox()		Vernal Equinox Day					春分の日 (正式は官報)
 * AutumnalEquinox()		Autumnal Equinox Day					秋分の日 (正式は官報)
 * lastday($y,$m)		End-of-the-month calculation				末日算出
 * LeapYear($y)			Leap year judging					閏年判定
 * iso8601($y,$m,$d,$format='%d-W%02d-%d')
 *				The standard strings for ISO-8601			ISO 8601 標準文字列設定
 * date2WeekDate($y,$m,$d)	Weekdate is calculated					週目算出
*/

//$hol = public_holiday(2002,5,4);
//$hol = public_holiday(2009,9,22);
//print_r($hol);

namespace PukiWiki\Lang\Holiday;

/**
 * 祝日判定クラス
 * @abstract
 */
abstract class PublicHoliday
{
	var $y,$m,$d,$w,$n;
	var $rc = array();
	var $my_name;

	var $tbl_SpecificDay = array();
	var $tbl_Calculation = array();
	var $tbl_HappyMonday = array();
	var $tbl_SubstituteHoliday = array();
	var $tbl_SubstituteHoliday_offset = array();
	var $tbl_NationalHoliday = array();

	public function __construct($y,$m,$d) {
		$this->y = $y;
		$this->m = $m;
		$this->d = $d;
		$this->w = $this->zeller($y,$m,$d);
		$this->n = (int)(($d-1)/7)+1; // Is it equivalent to the n-th time?
		$this->rc['rc'] = 0;
		$this->rc['name'] = '';
		$this->my_name = get_class($this);
	}

	public function isHoliday() { return $this->rc['rc']; }
	public function getRecursive() { return array_merge_recursive($this->rc, array('w'=>$this->w)); }
	public function set()
	{
		static $func = array('ph_SpecificDay','ph_Calculation','ph_HappyMonday','ph_SubstituteHoliday','ph_NationalHoliday');
		foreach($func as $x) {
			call_user_func( array(&$this, $x) );
			if ($this->rc['rc']) return;
		}
	}

	// 固定の祝日
	// Fixed Public Holiday
	protected function ph_SpecificDay()
	{
		foreach($this->tbl_SpecificDay as $x) {
			if ($x[0] > $this->m) return;
			if ($x[0] == $this->m && $x[1] == $this->d) {
				if ($this->chk_from_to($x[2],$x[3])) {
					// In the case of within an object period.
					$this->rc['rc'] = 1;
					$this->rc['name'] = $x[4];
					return;
				}
			}
		}
	}

	// 移動の祝日
	// Calculation of a public holiday
	protected function ph_Calculation()
	{
		foreach($this->tbl_Calculation as $x) {
			if ($x[0] != $this->m) continue;
			if (! $this->chk_from_to($x[1],$x[2])) continue;
			// In the case of within an object period.
			if (function_exists($x[3])) {
				// The function is entrusted when the applicable function is defined.
				if ($this->d == $x[3]($this->y,$this->m,$this->d)) {
					$this->rc['rc'] = 1;
					$this->rc['name'] = $x[4];
					return;
				}
			}
		}
	}

	// Move Public Holiday
	protected function ph_HappyMonday()
	{
		foreach($this->tbl_HappyMonday as $x) {
			if ($x[0] > $this->m) return;
			if ($x[0] != $this->m) continue;
			if (! $this->chk_from_to($x[3],$x[4])) continue;
			// In the case of within an object period.
			if ($this->w != $x[2]) continue;

			if ($this->n == $x[1]) {
				$this->rc['rc'] = 1;
				$this->rc['name'] = $x[5];
				return;
			} elseif ($x[1] == 9) {
				if ($this->isLastDayOfWeek($this->y,$this->m,$this->d)) {
					$this->rc['rc'] = 1;
					$this->rc['name'] = $x[5];
					return;
				}
			}
		}
	}

	// 振替休日
	// Substitute holiday
	protected function ph_SubstituteHoliday()
	{
		foreach($this->tbl_SubstituteHoliday as $x) {
		if (! $this->chk_from_to($x[0],$x[1])) continue;
		// In the case of within an object period.
			if (method_exists($this,$x[2])) {
				if (call_user_func( array(&$this, $x[2]) )) {
					$this->rc['rc'] = 2;
					$this->rc['name'] = $x[3];
					return;
				}
			}
		}
	}

	// Substitute holiday
	protected function set_SubstituteHoliday()
	{
		$offset = 0;
		foreach($this->tbl_SubstituteHoliday_offset as $x) {
			if ($x[0] == $this->w) {
				$offset = $x[1];
				break;
			}
		}
		if ($offset == 0) return false;

		$x = $this->mkdate($this->y,$this->m,$this->d,$offset);
		$obj = new $this->my_name($x['y'],$x['m'],$x['d']);
		$obj->ph_SpecificDay();
		$obj->ph_Calculation();
		$rc = $obj->rc['rc'];
		unset($obj);
		return ($rc) ? true : false;
	}

	protected function ph_NationalHoliday() { return; }

	/* --------------------------------------------------------------------------------- */
	// Applicable period check
	// chk_from_to($y,$m,$d,$x[3],$x[4]);
	protected function chk_from_to($from,$to) {
		$chk  = ( $this->y*10000) + ( $this->m*100) +  $this->d;
		return ($from <= $chk && $chk <= $to) ? true : false;
	}
	
	/**
	 * The last day of the week
	 * @param int $y
	 * @param int $m
	 * @param int $d
	 * @return boolean
	 */
	private function isLastDayOfWeek($y,$m,$d)
	{
		// 翌週の同曜日が翌月ならば最終と判断
		$x = $this->mkdate($y,$m,$d,7);
		if ($m == $x['m']) return false;
		return true;
	}
	/**
	 * Calculation of a day of the week
	 * @param type $y
	 * @param int $m
	 * @param type $d
	 * @return type
	 */
	private function zeller($y,$m,$d)
	{
		// It corresponds till 1583-3999 year.
		// January and February are the previous year.
		// It processes as 13 or 14 months.
		if ($m < 3) {
			$y--;
			$m += 12;
		}
		$d += $y + floor($y/4) - floor($y/100) + floor($y/400) + floor(2.6*$m+1.6);
		return ($d%7);
	}

	/**
	*  The date of the specified displacement is computed.
	* 
	* @param type $y
	* @param type $m
	* @param type $d
	* @param type $offset
	* @return array
	*/
	protected function mkdate($y,$m,$d,$offset)
	{
		$rc = array();
		$jd = $this->date2jd($y,$m,$d) + $offset;
		@list($rc['y'],$rc['m'],$rc['d']) = $this->jd2date($jd);
		return $rc;
	}

	/**
	 * Calculation of the Julius day
	 * @return type
	 */
	private function date2jd() {
		@list($y,$m,$d,$h,$i,$s) = func_get_args();

		if( $m < 3.0 ) {
			$y -= 1.0;
			$m += 12.0;
		}

		$jd  = (int)( 365.25 * $y );
		$jd += (int)( $y / 400.0 );
		$jd -= (int)( $y / 100.0 );
		$jd += (int)( 30.59 * ( $m-2.0 ) );
		$jd += 1721088;
		$jd += $d;

		$t  = $s / 3600.0;
		$t += $i /60.0;
		$t += $h;
		$t /= 24.0;

		$jd += $t;
		return( $jd );
	}
	/**
	 * A date is set up from the Julius day.
	 * @param type $jd
	 * @return type
	 */
	 function jd2date($jd)
	{
		$x0 = (int)( $jd+68570.0);
		$x1 = (int)( $x0/36524.25 );
		$x2 = $x0 - (int)( 36524.25*$x1 + 0.75 );
		$x3 = (int)( ( $x2+1 )/365.2425 );
		$x4 = $x2 - (int)( 365.25*$x3 )+31.0;
		$x5 = (int)( (int)($x4) / 30.59 );
		$x6 = (int)( (int)($x5) / 11.0 );

		$time[2] = $x4 - (int)( 30.59*$x5 );
		$time[1] = $x5 - 12*$x6 + 2;
		$time[0] = 100*( $x1-49 ) + $x3 + $x6;

		// Compensation on February 30
		if ($time[1] == 2 && $time[2] > 28) {
			if ($time[0] % 100 == 0 && $time[0] % 400 == 0) {
				$time[2] = 29;
			} elseif ($time[0] % 4 == 0) {
				$time[2] = 29;
			} else {
				$time[2] = 28;
			}
		}

		$tm = 86400.0*( $jd - (int)( $jd ) );
		$time[3] = (int)( $tm/3600.0 );
		$time[4] = (int)( ($tm - 3600.0*$time[3]) / 60.0 );
		$time[5] = (int)( $tm - 3600.0*$time[3] - 60*$time[4] );

		return $time;
	}

	/**
	* Vernal Equinox Day
	* @return type
	*/
	private function VernalEquinox()
	{
		@list($y) = func_get_args();
		if ($y < 1980)
			$a = 20.8357;
		elseif ($y < 2100)
			$a = 20.8431;
		elseif ($y < 2151)
			$a = 21.8510;
		$b = $y - 1980;
		return (int)($a+0.242194 * $b - (int)($b/4));
	}

	/**
	* Autumnal Equinox Day
	* @return type
	*/
	private function AutumnalEquinox()
	{
		@list($y) = func_get_args();
		if ($y < 1980)
			$a = 23.2588;
		elseif ($y < 2100)
			$a = 23.2488;
		elseif ($y < 2151)
			$a = 24.2488;
		$b = $y - 1980;
		return (int)($a+0.242194 * $b - (int)($b/4));
	}

	/**
	* 末日の算出
	* End-of-the-month calculation
	* @param type $y
	* @param type $m
	* @return int
	*/
	private function lastday($y,$m)
	{
		$last = array(31,28,31,30,31,30,31,31,30,31,30,31);
		if ($this->LeapYear($y)) $last[1] = 29;
		return $last[$m-1];
	}

	/**
	* 閏年判定
	* Leap year judging
	* @param type $y
	* @return boolean
	*/
	private function LeapYear($y)
	{
		if (($y%400) == 0) return true;
		if (($y%100) == 0) return false;
		if (($y%4)	== 0) return true;
		return false;
	}

	private function iso8601($y,$m,$d,$format='%d-W%02d-%d')
	{
		list($cy,$cw,$cz) = $this->date2WeekDate($y,$m,$d);
		return sprintf($format,$cy,$cw,$cz);
	}

	private function date2WeekDate($y,$m,$d)
	{
		$jd = $this->date2jd($y,$m,$d);
		$cz = zeller($y,$m,$d);
		if ($cz == 0) $cz = 7;

		if ($m == 12) {
			// 翌年の1/1
			$w = $this->zeller($y+1,1,1);
			$offset = ($w == 0) ? 7 : $w;
			// 翌年の1/1が木までの場合
			// 2:火 3:水 4:木
			if ($offset < 5 && $offset > 1) {
				// 12月の最終週は、翌年に属する可能性がある
				$jd_y0101 = $this->date2jd($y+1,1,1);
				$seq = $jd_y0101 - $jd;
				// 月火水までなら翌年
				if ($seq < $offset) return array($y-1,'01', $cz);
			}
		}

		$w = zeller($y,1,1);
		// $offset = 1:月 2:火 3:水 4:木 5:金 6:土 7:日
		$offset = ($w == 0) ? 7 : $w;
		$jd_y0101 = $this->date2jd($y,1,1);
		$week = ceil(($jd - $jd_y0101 + $offset) / 7);

		// 1/1が 金土日の場合は、前年に属するので計算結果から1週減らす
		if ($offset > 4) $week--;
		// 計算したい日が結果ゼロ週の場合は、前年週を再算出
		if ($week == 0) return $this->date2WeekDate($y-1,12,31);
		return array($y,str_pad($week ,2, '0', STR_PAD_LEFT), $cz);
	}
}

/* End of file public_holiday.php */
/* Location: ./wiki-common/lib/public_holiday.php */