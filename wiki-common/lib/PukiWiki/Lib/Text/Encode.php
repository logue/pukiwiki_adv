<?php
class Encoding{
	public static function utf8_to_unicode($str)
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

	public static function unicode_to_utf8($str)
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