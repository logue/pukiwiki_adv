<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: Trie.php,v 1.0.0 2012/12/18 11:00:00 Logue Exp $
// Copyright (C)
//   2012 PukiWiki Advance Developers Team
// License: GPL v2 or (at your option) any later version

namespace PukiWiki\Lib\Renderer;

class Trie{
	// Generate one compact regex for quick reTRIEval,
	// that just matches with all $array-values.
	//
	// USAGE (PHP >= 4.4.0, PHP >= 5.0.2):
	//   $array = array(7 => 'fooa', 5 => 'foob');
	//   $array = array_unique($array);
	//   sort($array, SORT_LOCALE_STRING);	// Keys will be replaced
	//   echo generate_trie_regex($array);	// 'foo(?:a|b)'
	//
	// USAGE (PHP >= 5.2.9):
	//   $array = array(7 => 'fooa', 5 => 'foob');
	//   $array = array_unique($array, SORT_LOCALE_STRING);
	//   $array = array_values($array);
	//   echo generate_trie_regex($array);	// 'foo(?:a|b)'
	//
	// ARGUMENTS:
	//   $array  : A _sorted_string_ array
	//	 * array_keys($array) MUST BE _continuous_integers_started_with_0_.
	//	 * Type of all $array-values MUST BE string.
	//   $_offset : (int) internal use. $array[$_offset	] is the first value to check
	//   $_sentry : (int) internal use. $array[$_sentry - 1] is the last  value to check
	//   $_pos	: (int) internal use. Position of the letter to start checking. (0 = the first letter)
	//
	// REFERENCE: http://en.wikipedia.org/wiki/Trie
	//
	protected static function generate_trie_regex($array, $_offset = 0, $_sentry = NULL, $_pos = 0)
	{
		if (empty($array)) return '(?!)'; // Match with nothing
		if ($_sentry === NULL) $_sentry = count($array);

		// Question mark: array('', 'something') => '(?:something)?'
		$skip = ($_pos >= mb_strlen($array[$_offset]));
		if ($skip) ++$_offset;

		// Generate regex for each value
		$regex = array();
		$index = $_offset;
		$multi = FALSE;
		while ($index < $_sentry) {
			if ($index != $_offset) {
				$multi = TRUE;
				$regex[] = '|'; // OR
			}

			// Get one character from left side of the value
			$char = mb_substr($array[$index], $_pos, 1);

			// How many continuous keys have the same letter
			// at the same position?
			for ($i = $index + 1; $i < $_sentry; $i++) {
				if (mb_substr($array[$i], $_pos, 1) !== $char) break;
			}

			if ($index < ($i - 1)) {
				// Some more keys found
				// Recurse
				$regex[] = self::preg_quote_extended($char, '/');
				$regex[] = self::generate_trie_regex($array, $index, $i, $_pos + 1);
			} else {
				// Not found
				$regex[] = self::preg_quote_extended(mb_substr($array[$index], $_pos), '/');
			}
			$index = $i;
		}

		if ($skip || $multi) {
			array_unshift($regex, '(?:');
			$regex[] = ')';
		}
		if ($skip) $regex[] = '?'; // Match for $pages[$_offset - 1]

		return implode('', $regex);
	}

	// preg_quote(), and also escape PCRE_EXTENDED-related chars
	// REFERENCE: http://www.php.net/manual/en/reference.pcre.pattern.modifiers.php
	// NOTE: Some special whitespace characters may warned by PCRE_EXTRA,
	//	   because of mismatch-possibility between PCRE_EXTENDED and '[:space:]#'.
	private static function preg_quote_extended($string, $delimiter = NULL)
	{
		// Escape some more chars
		$regex_from = '/([[:space:]#])/';
		$regex_to   = '\\\\$1';

		if (is_string($delimiter) && preg_match($regex_from, $delimiter)) {
			$delimiter = NULL;
		}

		return preg_replace($regex_from, $regex_to, preg_quote($string, $delimiter));
	}
}