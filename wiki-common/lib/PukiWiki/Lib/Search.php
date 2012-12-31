<?php
namespace PukiWiki\Lib;
use PukiWiki\Lib\Auth\Auth;
use PukiWiki\Lib\File\FileFactory;

class Search{
	// Expand search words
	public static function get_search_words($words, $do_escape = FALSE)
	{
		static $init, $mb_convert_kana, $pre, $post, $quote = '/';
		global $mecab_path;

		if (! isset($init)) {
			// function: mb_convert_kana() is for Japanese code only
			if (LANG === 'ja' && function_exists('mb_convert_kana')) {
				$mb_convert_kana = create_function('$str, $option',
					'return mb_convert_kana($str, $option, SOURCE_ENCODING);');
			}else if (LANG === 'ko'){
				$mb_convert_kana = create_function('$str, $option',
					'return PukiWiki\Lib\Text\Hangul::toChosung($str);');

			} else {
				$mb_convert_kana = create_function('$str, $option',
					'return $str;');
			}
			$pre = $post = '';
			$init = TRUE;
		}

		if (! is_array($words)) $words = array($words);

		// Generate regex for the words
		$regex = array();
		foreach ($words as $word) {
	/*
			if (extension_loaded('mecab') || file_exists($mecab_path)){
				$reg = '';
				$wakati = mecab_wakati($word);
				$ws = explode(' ', $wakati);

				foreach ($ws as $k)
					$reg .= !empty($k) ? '(?:'.$k.')' : '';
				$regex[$word] = $reg;
			}else{
	*/
				$word = trim($word);
				if (empty($word)) continue;

				// Normalize: ASCII letters = to single-byte. Others = to Zenkaku and Katakana
				$word_nm = $mb_convert_kana($word, 'aKCV');
				$nmlen   = mb_strlen($word_nm, SOURCE_ENCODING);

				// Each chars may be served ...
				$chars = array();
				for ($pos = 0; $pos < $nmlen; $pos++) {
					$char = mb_substr($word_nm, $pos, 1, SOURCE_ENCODING);

					// Just normalized one? (ASCII char or Zenkaku-Katakana?)
					$or = array(preg_quote($do_escape ? htmlsc($char) : $char, $quote));
					if (strlen($char) == 1) {
						// An ASCII (single-byte) character
						foreach (array(strtoupper($char), strtolower($char)) as $_char) {
							if ($char != '&') $or[] = preg_quote($_char, $quote); // As-is?
							$ascii = ord($_char);
							$or[] = sprintf('&#(?:%d|x%x);', $ascii, $ascii); // As an entity reference?
							$or[] = preg_quote($mb_convert_kana($_char, 'A'), $quote); // As Zenkaku?
						}
					} else {
						// NEVER COME HERE with mb_substr(string, start, length, 'ASCII')
						// A multi-byte character
						$or[] = preg_quote($mb_convert_kana($char, 'c'), $quote); // As Hiragana?
						$or[] = preg_quote($mb_convert_kana($char, 'k'), $quote); // As Hankaku-Katakana?
					}
					$chars[] = '(?:' . join('|', array_unique($or)) . ')'; // Regex for the character
				}

				$regex[$word] = $pre . join('', $chars) . $post; // For the word
	//		}
		}

		return $regex; // For all words
	}

	// 'Search' main function
	public static function do_search($word, $type = 'and', $non_format = FALSE, $base = '')
	{
		global $script, $whatsnew, $non_list, $search_non_list;
	 	global $search_auth, $show_passage, $search_word_color, $ajax;
	//	global $_msg_andresult, $_msg_orresult, $_msg_notfoundresult;
		global $_string;

		$_msg_andresult = $_string['andresult'];
		$_msg_orresult = $_string['orresult'];
		$_msg_notfoundresult = $_string['notfoundresult'];

		$retval = array();

		$keys = self::get_search_words(preg_split('/\s+/', $word, -1, PREG_SPLIT_NO_EMPTY));
		foreach ($keys as $key=>$value)
			$keys[$key] = '/' . $value . '/S';

		$b_type = ($type == 'and'); // AND:TRUE OR:FALSE

		$pages = Auth::get_existpages();

		// SAFE_MODE の場合は、コンテンツ管理者以上のみ、カテゴリページ(:)も検索可能
		$role_adm_contents = (Auth::check_role('safemode')) ? Auth::check_role('role_adm_contents') : FALSE;

		// Avoid
		if ( !empty($base) ) {
			$pages = preg_grep('/^' . preg_quote($base, '/') . '/S', $pages);
		}
		if (! $search_non_list) {
			$pages = array_diff($pages, preg_grep('/' . $non_list . '/S', $pages));
		}
		$pages = array_flip($pages);
		unset($pages[$whatsnew]);

		$count = count($pages);
		foreach (array_keys($pages) as $page) {
			$b_match = FALSE;

			// Search hidden for page name (Plus!)
			if ($role_adm_contents &&substr($page, 0, 1) === ':') {
				unset($pages[$page]);
				--$count;
				continue;
			}

			// Search for page name
			if (! $non_format) {
				foreach ($keys as $key) {
					$b_match = preg_match($key, $page);
					if ($b_type xor $b_match) break; // OR
				}
				if ($b_match) continue;
			}

			// Search auth for page contents
			if ($search_auth && ! check_readable($page, false, false)) {
				unset($pages[$page]);
				--$count;
			}

			// Search for page contents
			$page_source = get_source($page, TRUE, TRUE);
			// 通常検索
			foreach ($keys as $key) {
				$b_match = preg_match($key, $page_source);
				if ($b_type xor $b_match) break; // OR
			}
			if ($b_match) continue;


			unset($pages[$page]); // Miss
		}
		unset($role_adm_contents);	// Plus!

		if ($non_format) return array_keys($pages);

		if (empty($pages))
			return str_replace('$1', htmlsc($word), $_msg_notfoundresult);

		ksort($pages, SORT_STRING);

		$retval = '<ul>' . "\n";
		foreach (array_keys($pages) as $page) {
			$passage = $show_passage ? ' ' . get_passage(get_filetime($page)) : '';
			$retval .= ' <li><a href="' . get_page_uri($page) . '" class="linktip">' . htmlsc($page) . '</a>' . $passage . '</li>' . "\n";
		}
		$retval .= '</ul>' . "\n";

		$retval .= '<p>'.str_replace('$1', htmlsc($word) , str_replace('$2', count($pages),
			str_replace('$3', $count, $b_type ? $_string['andresult'] : $_string['orresult']))).'</p>';

		return $retval;
	}
}