<?php
// PukiWiki Plus! - Yet another WikiWikiWeb clone.
// $Id: rules.ini.php,v 1.10.5 2007/06/10 02:08:40 miko Exp $
// Copyright (C)
//   2005-2007 Customized/Patched by Miko.Hoshina
//   2003-2007 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
use PukiWiki\Time;
use PukiWiki\Utility;

global $vars, $date_format, $time_format;
/**
 * フィルタルール
 *
 *  正規表現で記述してください。?(){}-*./+\$^|など
 *  は \? のようにクォートしてください。
 *  前後に必ず / を含めてください。行頭指定は ^ を頭に。
 *  行末指定は $ を後ろに。
*/
return array(
	/**
	 *  フィルタルール(直接ソースを置換)
	 */
	'filter' => array(
		"^(TITLE):(.*)$" => "",
		"#tboff(.*)$" => "",
		"#skin(.*)$" => "",
	),

	/**
	 * 日時置換ルール (閲覧時に置換)
	 * $usedatetime = 1なら日時置換ルールが適用されます
	 * 必要のない方は $usedatetimeを0にしてください。
	 */
	'datetime' => array(
		'&amp;_now;'	=> '&epoch{'.time().'};',
		'&amp;_date;'	=> Time::getZoneTimeDate($date_format),
		'&amp;_time;'	=> Time::getZoneTimeDate($time_format),
	),
	/**
	 * ユーザ定義ルール(保存時に置換)
	 *  正規表現で記述してください。?(){}-*./+\$^|など
	 *  は \? のようにクォートしてください。
	 *  前後に必ず / を含めてください。行頭指定は ^ を頭に。
	 *  行末指定は $ を後ろに。
	 */
	'str' => array(
		'&now;' 	=> '\&epoch\{'.time().'\};',
		'&date;'	=> Time::getZoneTimeDate($date_format),
		'&time;'	=> Time::getZoneTimeDate($time_format),
		'&page;'	=> isset($vars['page']) ? Utility::getPageNameShort($vars['page']) : null,
		'&fpage;'	=> isset($vars['page']) ? $vars['page'] : null,
		'&t;'   	=> "\t"
	)
);

/* End of file rules.ini.php */
/* Location: ./wiki-common/rules.ini.php */