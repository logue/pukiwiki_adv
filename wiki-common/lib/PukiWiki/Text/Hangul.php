<?php

namespace PukiWiki\Text;
/**
 * ハングル文字クラス
 * http://pukiwiki.sourceforge.jp/dev/?BugTrack2%2F13
 */
class Hangul{
	// ハングルの母音
	private static $LCtable = array('ㄱ','ㄱ','ㄴ','ㄷ','ㄷ','ㄹ','ㅁ','ㅂ','ㅂ','ㅅ','ㅅ','ㅇ','ㅈ','ㅈ','ㅊ','ㅋ','ㅌ','ㅍ','ㅎ');
	/**
	 * 韓国語の文字列から字母を抽出
	 * @param string $str
	 * @return string
	 */
	public static function toChosung($str)
	{
		preg_match('/^./u', $str, $temp);
		$unicode = self::utf8_to_unicode($temp[0]);	// 取得した文字をUnicodeに変換

		if ($unicode[0] >= 44032 && $unicode[0] <= 55203) {	// 字母を抽出
			return self::$LCtable[(int)($unicode[0] - 44032)/28/21];
		} else
			return $temp[0];
	}

	// http://www.phpschool.com/bbs2/inc_view.html?id=10882&code=tnt2
	public static function toJosa($str, $tail)
	{
		switch ($tail) {
		case '은':
		case '는':
			$tail1 = '은';
			$tail2 = '는';
			break;
		case '을':
		case '를':
			$tail1 = '을';
			$tail2 = '를';
			break;
		case '과':
		case '와':
			$tail1 = '과';
			$tail2 = '와';
			break;
		case '이':
		case '가':
			$tail1 = '이';
			$tail2 = '가';
			break;
		case '으로':
		case '로':
			$tail1 = '으로';
			$tail2 = '로';
			break;
		default:
			$tail1 = $tail;
			$tail2 = $tail;
		}

		preg_match('/.$/u', $str, $temp);

		if (strlen($temp[0]) == 1) {
			if (preg_match('/[lmn136780]/i', $temp[0]))
				return $str.$tail1;

			if (preg_match('/r/i', $temp[0]) && strlen($str) == 1)
				return $str.$tail1;

			return (preg_match('/([aeiou][^aeiouwy]e|mb|ck)$/i', $str)) ? $str.$tail1 : $str.$tail2;
		}

		$unicode = self::utf8_to_unicode($temp[0]);
		return (($unicode[0] - 16) % 28 !== 0) ? $str.$tail1 : $str.$tail2;
	}
	private static function utf8_to_unicode($str)
	{
		$unicode = array();
		$values = array();
		$lookingFor = 1;

		for ($i = 0; $i < strlen($str); $i++) {
			$thisValue = ord($str[$i]);

			if ($thisValue < 128)
				$unicode[] = $thisValue;
			else {
				if (count($values) == 0)
					$lookingFor = ($thisValue < 224) ? 2 : 3;

				$values[] = $thisValue;

				if (count($values) == $lookingFor) {

					$number = ($lookingFor == 3) ?
						(($values[0] % 16) * 4096) +
						(($values[1] % 64) * 64) +
						($values[2] % 64) : (($values[0] % 32) *
						64) + ($values[1] % 64);

					$unicode[] = $number;
					$values = array();
					$lookingFor = 1;
				}	// if
			}		// if
		}			// for
		return $unicode;
	}				// utf8_to_unicodea
	private static function unicode_to_utf8($str)
	{
		$utf8 = '';
		foreach($str as $unicode) {
			if ($unicode < 128) {
				$utf8 .= chr($unicode);
			} else if ($unicode < 2048) {
				$utf8 .=
					chr(192 + (($unicode - ($unicode % 64)) / 64));
				$utf8 .= chr(128 + ($unicode % 64));
			} else {
				$utf8 .=
					chr(224 + (($unicode - ($unicode % 4096)) / 4096));
				$utf8 .=
					chr(128 + ((($unicode % 4096) -
						($unicode % 64)) / 64));
				$utf8 .= chr(128 + ($unicode % 64));
			}		// if
		}			// foreach
		return $utf8;
	}				// unicode_to_utf8
}

/* End of file Hangul.php */
/* Location: /vendor/PukiWiki/Lib/Text/Hangul.php */