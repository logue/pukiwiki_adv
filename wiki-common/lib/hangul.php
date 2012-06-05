<?php

/*
// http://www.tobyto.pe.kr/view.php?seq=19&t=1107501543

$LCtable = array("ㄱ","ㄲ","ㄴ","ㄷ","ㄸ","ㄹ","ㅁ","ㅂ","ㅃ","ㅅ","ㅆ","ㅇ","ㅈ","ㅉ","ㅊ","ㅋ","ㅌ","ㅍ","ㅎ");
$MVtable = array("ㅏ","ㅐ","ㅑ","ㅒ","ㅓ","ㅔ","ㅕ","ㅖ","ㅗ","ㅘ","ㅙ","ㅚ","ㅛ","ㅜ","ㅝ","ㅞ","ㅟ","ㅠ","ㅡ","ㅢ","ㅣ");
$TCtable = array("","ㄱ","ㄲ","ㄳ","ㄴ","ㄵ","ㄶ","ㄷ","ㄹ","ㄺ","ㄻ","ㄼ","ㄽ","ㄾ","ㄿ","ㅀ","ㅁ","ㅂ","ㅄ","ㅅ","ㅆ","ㅇ","ㅈ","ㅊ","ㅋ","ㅌ","ㅍ","ㅎ");

$LCetable = array("k","kk","n","d","tt","l","m","b","pp","s","ss","","j","jj","ch","k","t","p","h");
$MVetable = array("a","ae","ya","yae","eo","e","yeo","ye","o","wa","wae","oe","yo","u","wo","we","wi","yu","eu","ui","i");
$TCetable = array("","g","kk","k","n","n","n","t","l","l","l","l","l","l","l","l","m","p","p","s","ss","ng","j","ch","k","t","p","h");

// UTF-8로 변환된 문장을 유니코드로 변환한다. 

$result = utf8_to_unicode($str);

// 유니코드로 변환된 글이 한글코드 안에 있으면 초중성으로 분리한다

while (list($key, $val) = each($result)) {
	if($val >= 44032 && $val <= 55203) {
		$chr = "";
		$code = "";
		$temp1 = "";
		$code = $val;
		$temp1 = $code - 44032;
		$T = (int) $temp1 % 28;
		$temp1 /= 28;
		$V = (int) $temp1 % 21;
		$temp1 /= 21;
		$L = (int) $temp1;
		$chr .= $LCetable[$L].$MVetable[$V].$TCetable[$T];
	}
	else {
		$temp2 = array();
		$temp2 = array($result[$key]);
		$chr .= unicode_to_utf8($temp2);
	}
}
echo $chr;

*/

// http://www.randomchaos.com/document.php?source=php_and_unicode
function utf8_to_unicode($str)
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

function unicode_to_utf8($str)
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

function hangul_chosung($str)
{
	$LCtable = array("ㄱ","ㄱ","ㄴ","ㄷ","ㄷ","ㄹ","ㅁ","ㅂ","ㅂ","ㅅ","ㅅ","ㅇ","ㅈ","ㅈ","ㅊ","ㅋ","ㅌ","ㅍ","ㅎ");
	preg_match('/^./u', $str, $temp);
	$unicode = utf8_to_unicode($temp[0]);

	if ($unicode[0] >= 44032 && $unicode[0] <= 55203) {
		return $LCtable[(int)($unicode[0] - 44032)/28/21];
	} else
		return $temp[0];
}

// http://www.phpschool.com/bbs2/inc_view.html?id=10882&code=tnt2
function hangul_josa($str, $tail)
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

	$unicode = utf8_to_unicode($temp[0]);
	return (($unicode[0] - 16) % 28 != 0) ? $str.$tail1 : $str.$tail2;
}

/* End of file hangul.php */
/* Location: ./wiki-common/lib/hangul.php */
