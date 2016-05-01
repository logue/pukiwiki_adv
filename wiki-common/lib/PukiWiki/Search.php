<?php
/**
 * 検索処理
 *
 * @package   PukiWiki
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013-2015 PukiWiki Advance Developers Team
 * @create    2013/05/23
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Search.php,v 1.0.4 2015/06/09 20:48:00 Logue Exp $
 */
namespace PukiWiki;

use PukiWiki\Auth\Auth;
use PukiWiki\Factory;
use PukiWiki\Utility;

/**
 * 検索クラス
 */
class Search{
	private static $init = false;
	
	/**
	 * 検索語句の正規表現を生成
	 * @param array $words 検索語句
	 * @param boolean $do_escape
	 */
	public static function get_search_words($words, $do_escape = FALSE)
	{
		static $init, $reading, $pre, $post, $quote = '/';
		$mb_convert_kana = function($str, $option){
			return $str;
		};

		if (! isset($init)) {
			
			if (LANG === 'ja' && function_exists('mb_convert_kana')) {
				$mb_convert_kana = function($str, $option){
					return mb_convert_kana($str, $option, SOURCE_ENCODING);
				};
			}else if (LANG === 'ko'){
				$mb_convert_kana = function($str, $option){
					return PukiWiki\Text\Hangul::toChosung($str);
				};
			}else if (LANG === 'zh'){
				$mb_convert_kana = function($str, $option){
					return PukiWiki\Text\PinYin::toKana($str);
				};
			}
			$pre = $post = '';
			$init = TRUE;
		}

		if (! is_array($words)){
			$words = array($words);
		}

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
					$or = array(preg_quote($do_escape ? Utility::htmlsc($char) : $char, $quote));
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

	/**
	 * 検索メイン処理
	 * @param string $word 検索ワード
	 * @param string $type 検索方法（and, or）
	 * @param boolean $non_format
	 * @param string $base ベースとなるページ
	 * @return string
	 */
	public static function do_search($word, $type = 'and', $non_format = FALSE, $base = '')
	{
		global $show_passage;
		global $_string;

		$retval = array();

		// 検索ワードをパース
		$keys = self::get_search_words(preg_split('/\s+/', $word, -1, PREG_SPLIT_NO_EMPTY));
		foreach ($keys as &$value)
			$value = '/' . $value . '/S';

		// AND:TRUE OR:FALSE
		$b_type = ($type == 'and');

		// ページ一覧
		$pages = Listing::pages();
		$count = count($pages);

		// 基準となるページが指定されてた場合、それ以外を配列から削除
		if ( !empty($base) ) {
			$pages = preg_grep('/^' . preg_quote($base, '/') . '/S', $pages);
		}

		foreach ($pages as $page) {
			if (empty($page)) continue;

			$wiki = Factory::Wiki($page);

			// 読む権限がない場合スキップ（Adv.の場合Wikiクラスで権限を管理している）
			if (!$wiki->isReadable() || $wiki->isHidden()) continue;

			$b_match = FALSE;

			// ページ名から検索
			if (! $non_format) {
				$b_match = self::search_keys($keys, $page, $b_type);
			}

			// 通常検索
			$b_match = self::search_keys($keys, $wiki->get(true), $b_type);

			// マッチしない場合スキップ
			if (!$b_match) continue;

			// 結果を配列に保存
			$retval[] = $non_format ? $page : '<li><a href="' . $wiki->uri('read', array('word'=>$word)) . '" class="search-summary">' . Utility::htmlsc($page) . '</a>' . ($show_passage ? ' ' . $wiki->passage() : '') . '</li>' . "\n";
		}
		
		$count = count($retval);
		
		// フォーマットせずに配列で出力
		if ($non_format) return $retval;

		if ($count === 0){
			return '<p class="alert alert-warning">'.str_replace('$1', Utility::htmlsc($word), $_string['notfoundresult']).'</p>';
		}

		return '<p class="alert alert-success">'.
			str_replace('$1', Utility::htmlsc($word) , str_replace('$2', $count,
			str_replace('$3', count($pages), $b_type ? $_string['andresult'] : $_string['orresult']))).
			'</p>'."\n" . 
			'<div class="list_pages"><ul>' . join("\n", $retval) . '</ul></div>';
	}
	/**
	 * 検索
	 * @param array $keys 検索ワード
	 * @param string $source 検索対象
	 * @return boolean
	 */
	private static function search_keys($keys, $source, $b_type = 'AND'){
		$b_match = false;
		foreach ($keys as $key) {
			$b_match = preg_match($key, $source);
			if ($b_type ^ $b_match) break; // XOR
		}
		return $b_match;
	}
}