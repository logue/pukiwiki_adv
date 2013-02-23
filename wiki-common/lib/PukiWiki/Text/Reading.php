<?php
/**
 * テキスト読み取りクラス
 *
 * @package   PukiWiki\Text
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013 PukiWiki Advance Developers Team
 * @create    2013/02/02
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Reading.php,v 1.0.0 2013/02/02 17:28:00 Logue Exp $
 **/

namespace PukiWiki\Text;

use PukiWiki\Text\Hangul;
use PukiWiki\Text\Hiragana;
use PukiWiki\Text\PinYin;

/**
 * テキスト読み取りクラス
 */
class Reading{
	// 特殊ページ
	const SPECIALPAGE_PATTERN = '[\:]';
	// アルファベット（ドイツ語やフランス語などを含む）にマッチするパターン
	const ALPHABET_PATTERN = '[A-Za-zÀ-ɏ]';
	// :以外のシンボル、数字にマッチするパターン
	const SYMBOL_PATTERN = '[!-9\;-\@０－９]';
	// すべての漢字にマッチするパターン（※ハングル、サロゲートペア文字は含まず）
	const KANJI_PATTERN = '[々〇〻㐀-龻豈-頻]';
	// ひらがな・カタカナにマッチするパターン
	const KANA_PATTERN = '[ぁ-ヶ]';
	// ハングル文字にマッチするパターン
	const HANGUL_PATTERN = '[가-힣]';

	// 強制的に先頭に降るメタ文字
	const SYMBOL_CHAR = '!SYMBOL';
	// 強制的に先頭に降るメタ文字
	const SPECIAL_CHAR = ' SPECIAL';
	// 強制的に末尾に降るメタ文字
	const OTHER_CHAR = 'OTHER';	// は、ソートで使うため外字領域の&#xf8f0;の文字を入れている。

	/**
	 * 読みを取得
	 * @param string $str 入力文字列
	 * @return string
	 */
	public static function getReading($str){
		global $mecab_path;
		if (!preg_match('/zh/', DEFAULT_LANG) ){
			// 中国語でない場合はMeCabで読むことを試みる
			$mecab = new MeCab($mecab_path);
			if ($mecab->usable) {
				return $mecab->reading($str);
			}
			unset($mecab);
		}
		foreach(self::mbStringToArray($str) as $char){
			$ret[] = self::getReadingChar($char);
		}
		return join('', $ret);
	}
	/**
	 * 文字の読みを取得（先頭１文字で言語を判断。複数の言語が含まれている場合を考慮しない）
	 * @param string $char 入力文字列
	 * @return string
	 */
	public static function getReadingChar($char){
		if (preg_match('/^('.self::SPECIALPAGE_PATTERN.')/u',$char, $matches)) {
			return $matches[1];
		}else if (preg_match('/^('.self::SYMBOL_PATTERN.')/u',$char, $matches)) {
			// 数字
			return $matches[1];
		}else if (mb_ereg('^('.self::ALPHABET_PATTERN.')', mb_convert_kana($char, 'as'), $matches) !== FALSE) {
			// 英字
			return $matches[1];
		}else if (preg_match('/^('.self::KANA_PATTERN.')/u',$char, $matches)) {
			// かな／カタカナ
			return mb_convert_kana($matches[1],'KVC');
		}else if(preg_match('/^('.self::KANJI_PATTERN.')/u',$char, $matches)) {
			// 漢字
			if (preg_match('/zh/', DEFAULT_LANG) ){
				// デフォルトの言語が中国語の場合、ピンインとして処理
				return PinYin::toPinYin($matches[1]);
			}
			$reading = Hiragana::toKana($matches[1]);
			if ($reading !== $char) return $reading;
			// マッチしない場合は中国語として処理
			return PinYin::toKana($matches[1]);
			
		}else if(preg_match('/^('.self::HANGUL_PATTERN.')/u',$char, $matches)) {
			// ハングル
			return Hangul::toChosung($matches[1]);
		}
		return $char;
	}
	/**
	 * 文字列を１文字ごとの配列にする
	 * @param string $sStr 入力文字列
	 * @param string $sEnc エンコード
	 * @return array
	 */
	private static function mbStringToArray ($sStr, $sEnc='UTF-8') {
		$aRes = array();
		while ($iLen = mb_strlen($sStr, $sEnc)) {
			array_push($aRes, mb_substr($sStr, 0, 1, $sEnc));
			$sStr = mb_substr($sStr, 1, $iLen, $sEnc);
		}
		return $aRes;
	}
	/**
	 * エイリアス：読みを取得
	 */
	public function __get($str){
		return self::getReading($str);
	}
}