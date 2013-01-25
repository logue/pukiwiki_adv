<?php
namespace PukiWiki\Lib\Text;
use PukiWiki\Lib\Text\Hangul;
use PukiWiki\Lib\Text\Hiragana;
class Reading{
	// すべての漢字にマッチするパターン（ピンイン変換処理を作ってないため未使用）ハングルは含まず
	// http://tama-san.com/?p=196
	const ALL_KANJI_PATTERN = '(?:[々〇〻\u3400-\u9FFF\uF900-\uFAFF]|[\uD840-\uD87F][\uDC00-\uDFFF])';
	// JIS漢字にマッチするパターン
	const JIS_KANJI_PATTERN = '([亜-煕])';
	// ひらがな・カタカナにマッチするパターン
	const KANA_PATTERN = '([ぁ-ヶ])';
	
	// ハングル文字にマッチするパターン
	const HANGUL_PATTERN = '([가-힣])';
	
	/**
	 * 読みを取得（先頭１文字で言語を判断。複数の言語が含まれている場合を考慮しない）
	 */
	public static function getReading($str){
		if(preg_match('/^'.self::JIS_KANJI_PATTERN.'/u',$str)) {
			// JIS漢字をカタカナにする
			return Hiragana::toKana($str);
		}else if(preg_match('/^'.self::HANGUL_PATTERN.'/u',$str)) {
			// ハングル
			return Hangul::toChosung($str);
		}
		/*
		else if(preg_match('/^'.self::ALL_KANJI_PATTERN.'/u',$str)) {
			// ピンインの英数字に変換
			return PinYin::getPinYin($str);
		}
		*/
		return $str;
	}
}