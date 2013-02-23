<?php

namespace PukiWiki\Lang\Holiday;

use PukiWiki\Lang\Holiday\PublicHoliday;

/**
 * 祝日判定クラス(ＵＳ用)
 * @abstract
 */
class HolidayUS extends PublicHoliday
{
	var $tbl_SpecificDay = array(
		//     M, D, StartYMD, EndYMD  , Public Holiday Name
		array( 1, 1, 00000000, 99999999, 'New Year\'s Day'),
		array( 7, 4, 17760704, 99999999, 'Independence Day'),
		array(11,11, 19181111, 19531231, 'Veterans Day'),
		array(11,11, 19540101, 99999999, 'Veterans Day'),
		array(12,25, 00000000, 99999999, 'Christmas Day'),
	);
	var $tbl_Calculation = array();
	var $tbl_HappyMonday = array(
		array( 1, 3, 1, 00000000, 99999999, 'Birthday of Martin Luther King'),
		array( 2, 3, 1, 00000000, 99999999, 'President\'s Day'),
		array( 5, 9, 1, 00000000, 99999999, 'Memorial Day'),		// Last Monday
		array( 9, 1, 1, 00000000, 99999999, 'Labor Day'),
		array(10, 2, 1, 00000000, 99999999, 'Columbus Day'),
		array(11, 9, 4, 18631101, 19401231, 'Thanksgiving Day'),	// Last Thursday
		array(11, 4, 4, 19410101, 99999999, 'Thanksgiving Day'),
	);

	var $tbl_SubstituteHoliday = array(
		//    StartYMD, EndYMD  ,  method name           ,  祝日名称
		array(19680000, 99999999, 'set_SubstituteHoliday', 'Substitute holiday'),
	);
		  var $tbl_SubstituteHoliday_offset = array(
		array(1, -1),
		array(5,  1), // Is Saturday a public holiday if it is Friday?
	);

	function __construct($y,$m,$d)
	{
		parent::__construct($y,$m,$d);
		$this->tbl_SpecificDay_State();
		$this->tbl_HappyMonday_State();
	}

	function tbl_SpecificDay_State()
	{
		switch (STATE) {
		case 'GU': // Guam
			$tmp = array(
				array( 7,21, 00000000, 99999999,'Liberation Day'),
				array(11, 1, 00000000, 99999999,'All Souls Day'),
				array(12, 8, 00000000, 99999999,'Immaculate Conception'),
			);
			$this->tbl_SpecificDay = array_merge_recursive($this->tbl_SpecificDay, $tmp);
			break;
		case 'HI': // Hawaii
			$tmp = array(
				array( 3,26, 18710326, 99999999,'Prince Kuhio day'),
				array( 6,11, 18720611, 99999999,'King Kamehameha Day'),
			);
			$this->tbl_SpecificDay = array_merge_recursive($this->tbl_SpecificDay, $tmp);
			break;
		}
	}

	function tbl_HappyMonday_State()
	{
		switch (STATE) {
		case 'GU': // Guam
			$tmp = array(
				//	  M,No,WEEK,  Start,		End, Public Holiday Name
				array( 3, 1, 1, 00000000, 99999999, 'Discovery Day'),
			);
			$this->tbl_HappyMonday = array_merge_recursive($this->tbl_HappyMonday, $tmp);
			break;
		case 'HI': // Hawaii
			$tmp = array(
				array( 8, 3, 5, 19590821, 99999999, 'Admission Day'),
			);
			$this->tbl_HappyMonday = array_merge_recursive($this->tbl_HappyMonday, $tmp);
			break;
		}
	}
}
