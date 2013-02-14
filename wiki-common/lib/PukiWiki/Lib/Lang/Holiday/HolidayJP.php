<?php

namespace PukiWiki\Lib\Lang\Holiday;

use PukiWiki\Lib\Lang\Holiday\PublicHoliday;

/**
 * 祝日判定クラス(日本用)
 * @abstract
 */
class HolidayJP extends PublicHoliday{
	// 休日の変遷＠行政歴史研究会 を参考に、明治から大正までの対応を行っています
	// http://homepage1.nifty.com/gyouseinet/kyujitsu.htm

	// 第３条１項 国民の祝日
	var $tbl_SpecificDay = array(
		//     M, D, StartYMD, EndYMD  ,Public Holiday Name
		array( 1, 1, 19480720, 99999999,'元日'),
		array( 1, 3, 18731014, 19480719,'元始祭'),
		array( 1, 5, 18731014, 19480719,'新年宴會'),
		array( 1,15, 19480720, 19991231,'成人の日'),
		array( 1,30, 18731014, 19120902,'孝明天皇祭'),
		array( 2,11, 18731014, 19480719,'紀元節'),
		array( 2,11, 19661209, 99999999,'建国記念の日'),
		array( 2,24, 19890224, 19890224,'昭和天皇の大喪の礼'),		// 平成元年法律４号
		array( 4, 3, 18731014, 19480719,'神武天皇祭'),
		array( 4,10, 19590410, 19590410,'皇太子明仁親王の結婚の儀'),	// 昭和３４年法律１６号
		array( 4,29, 19270303, 19480719,'天長節'),
		array( 4,29, 19480720, 19890216,'天皇誕生日'),
		array( 4,29, 19890217, 20061231,'みどりの日'),
		array( 4,29, 20070101, 99999999,'昭和の日'),
		array( 5, 3, 19480720, 99999999,'憲法記念日'),
		array( 5, 4, 20070101, 99999999,'みどりの日'),
		array( 5, 5, 19480720, 99999999,'こどもの日'),
		array( 6, 9, 19930609, 19930609,'皇太子徳仁親王の結婚の儀'),	// 平成５年法律３２号
		array( 7,20, 19960101, 20021231,'海の日'),
		array( 7,30, 19120903, 19270302,'明治天皇祭'),
		array( 8,31, 19120903, 19270302,'天長節'),
		array( 9,15, 19660625, 20021231,'敬老の日'),
		array( 9,17, 18731014, 18790704,'神嘗祭'),
		array(10,10, 19660625, 19991231,'体育の日'),
		array(10,17, 18790705, 19480719,'新嘗祭'),
		array(10,31, 19130716, 19270302,'天長節祝日'),
		array(11, 3, 18731014, 19120902,'天長節'),
		array(11, 3, 19270303, 19480719,'明治節'),
		array(11, 3, 19480720, 99999999,'文化の日'),
		array(11,10, 19150921, 19151116,'即位ノ禮'),			// 大正４年勅令１６１号
		array(11,10, 19280908, 19281116,'即位ノ禮'),			// 昭和３年勅令２２６号
		array(11,12, 19901112, 19901112,'即位礼正殿の儀'),		// 平成２年法律２４号
		array(11,14, 19150921, 19151116,'大嘗祭'),			// 大正４年勅令１６１号
		array(11,14, 19280908, 19281116,'大嘗祭'),			// 昭和３年勅令２２６号
		array(11,23, 18731014, 19480719,'新嘗祭'),
		array(11,23, 19480720, 99999999,'勤労感謝の日'),
		array(12,23, 19890217, 99999999,'天皇誕生日'),
		array(12,25, 19270303, 19480719,'大正天皇祭'),
	);

	var $tbl_Calculation = array(
		//	 月,開始YMD , 終了YMD  , 関数名             ,祝日名称
		array( 3,18780605, 19480719, 'VernalEquinox'    ,'春季皇靈祭'),
		array( 3,19480720, 99999999, 'VernalEquinox'	,'春分の日'),
		array( 9,18780605, 19480719, 'AutumnalEquinox'  ,'秋季皇靈祭'),
		array( 9,19480720, 99999999, 'AutumnalEquinox'  ,'秋分の日'),
	);


	// 平成１０年法律１４１号
	// 平成１３年法律５９号
	var $tbl_HappyMonday = array(
		//    月,週,曜日, 開始YMD , 終了YMD , 祝日名称
		array( 1, 2,   1, 20000101, 99999999, '成人の日'),
		array( 7, 3,   1, 20030101, 99999999, '海の日'),
		array( 9, 3,   1, 20030101, 99999999, '敬老の日'),
		array(10, 2,   1, 20000101, 99999999, '体育の日'),
	);

	// 第３条２項 振替休日
	var $tbl_SubstituteHoliday = array(
		//     開始YMD, 終了YMD , method name            , 祝日名称
		array(19730421, 20061231, 'set_SubstituteHoliday', '振替休日'),
		array(20070101, 99999999, 'jp_SubstituteHoliday' , '振替休日'),
	);

	var $tbl_SubstituteHoliday_offset = array(
		// 曜日,Offset
		array(1, -1), // Is Sunday a public holiday if it is Monday?
	);

	// 第３条３項 国民の休日
	var $tbl_NationalHoliday = array(
		//    開始YMD , 終了YMD , 祝日名称
		array(19851227, 99999999,'国民の休日'),
	);

	// ONLY JAPAN.
	// 振替休日(第３条第２項)
	// ２００７年から
	// 「国民の祝日」が日曜日に当たるときは、その日後においてその日に最も近い「国民の祝日」でない日を休日とする。
	function jp_SubstituteHoliday(){
		if ($this->w == 0) return false;

		$rc = false;
		$offset = 0;
		while(1) {
			$offset--;
			$x = parent::mkdate($this->y,$this->m,$this->d,$offset);

			$obj = new $this->my_name($x['y'],$x['m'],$x['d']);
			$obj->ph_SpecificDay();
			$obj->ph_Calculation();
			if ($obj->rc['rc'] == 0) break; // 平日なら終了

			if ($obj->w == 0) {
				$rc = true; // 日曜日が祝日
				break;
			}
			unset($obj);
		}

		return $rc;
	}

	function ph_NationalHoliday()
	{
		foreach($this->tbl_NationalHoliday as $x) {
			if (! $this->chk_from_to($x[0],$x[1])) continue;
			// In the case of within an object period.
			if ($this->jp_NationalHoliday()) {
				$this->rc['name'] = $x[2];
				$this->rc['rc']	= 3;
				return;
			}
		}
	}

	// 国民の休日(第３条第３項) ２００６年まで
	// その前日及び翌日が「国民の祝日」である日（日曜日にあたる日及び前項に規定する休日にあたる日を除く。）は、休日とする。
	// 国民の休日(第３条第３項) ２００７年から
	// その前日及び翌日が「国民の祝日」である日（「国民の祝日」でない日に限る。）は、休日とする。
	function jp_NationalHoliday()
	{
		// 本当は、２００７年からは条件対象外ではあるが、他ロジックからそのまま。
		if ($this->w == 0) return false; // It is on the day except Sunday.

		// 「国民の祝日」でない日に限る。
		// この部分に関しては、この method を呼ぶ前に、
		// ph_SpecificDay, ph_Calculation, ph_HappyMonday, ph_SubstituteHoliday
		// を判定済みという前提で回避。-> set_public_holiday()

		foreach(array(-1,1) as $offset) {
			$x = parent::mkdate($this->y,$this->m,$this->d,$offset);
			$obj = new $this->my_name($x['y'],$x['m'],$x['d']);
			$obj->ph_SpecificDay();
			$obj->ph_HappyMonday();
			$obj->ph_Calculation();
			if ($obj->rc['rc'] == 0) return false;
			unset($obj);
		}
		return true;
	}

}