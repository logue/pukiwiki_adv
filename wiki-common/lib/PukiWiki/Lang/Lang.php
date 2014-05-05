<?php
/**
 * Language judgment (言語判定)
 *
 * @copyright   Copyright &copy; 2005-2006,2008, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @version     $Id: lang.php,v 0.27.3 2012/05/20 09:29:00 Logue Exp $
 *
 */

namespace PukiWiki\Lang;

use Locale;
use PukiWiki\Router;
use Zend\I18n\Translator\Translator;

class Lang {
	// CORRESPONDENCE LANGUAGE : 対応言語
	// == CASE SENSITIVE ==    : 大文字小文字を区別
	public static $language_prepared = array(
		'ja_JP',
		'zh_TW',
		'zh_CN',
		'en_US',
		'ko_KR'
	);
	private static $lng_func = array(
		'getCookieLanguage',    // 1 return ja,ja_JP
		'getAcceptLanguage',    // 2 return ja,ko
		'getUserAgentMozilla',  // 3 return ja,ja_JP
		'getAcceptCharset',     // 4 return ja_JP
		'getRemoteAddr',        // 5 return ja
	);

	private static $mb_language_key = array(
		'en'        => 'English',
		'ja'        => 'Japanese',
		'ko'        => 'Korean',
		'zh_TW'     => 'Traditional Chinese',
		'zh_CN'     => 'Simplified Chinese',
		'de'        => 'German', // 'Deutsch'
		'ru'        => 'Russian',
		'default'   => 'uni',
	);

	/*
	 * set_language
	 *
	 */
	public static function setLanguage()
	{
		global $language_considering_setting_level;
		global $language;
		global $public_holiday_guest_view;
		global $translator;

		$language = self::getLanguage($language_considering_setting_level);

		// LANG - Internal content encoding ('en', 'ja', or ...)
		define('LANG', $language);

		// Set COOKIE['lang']
		$parsed_url = parse_url(Router::get_script_absuri());
		$path = $parsed_url['path'];
		if (($pos = strrpos($path, '/')) !== FALSE) {
			$path = substr($path, 0, $pos + 1);
		}
		setcookie('lang', $language, 0, $path);
		$_COOKIE['lang'] = $language;

		// PUBLIC HOLIDAY
		// Installation person's calendar is adopted.
		$_c = explode('_', ($public_holiday_guest_view ? $language : DEFAULT_LANG));
		
		define('COUNTRY', $_c[1]);
		unset($_c);

		// I18N
		if (extension_loaded('intl')){
			Locale::setDefault($language);
		}

		// LOCALE Name specified by GETTEXT().
		define('DOMAIN', 'pukiwiki');
		// LOCALE Name specified by SETLOCALE().
		defined('PO_LANG') or define('PO_LANG', $language); // 'en_US', 'ja_JP'

		// PHP mbstring process.
		self::setMbstring($language);
	}

	/*
	 * get_language
	 *
	 */
	public static function getLanguage($level = 0)
	{
		if ($level == 0) return DEFAULT_LANG;

		$obj_lng = new AcceptLanguage();
		$level = ($level > count(self::$lng_func)) ? count(self::$lng_func) : $level;
		$obj_l2c = new Lang2Country();

		for($i=0; $i < $level; $i++){
			if ($i == $level) return DEFAULT_LANG;
			$func = self::$lng_func[$i];
			// 指定関数の実行
			$_x = $obj_lng::$func();
			if (! is_array($_x)) continue;

			foreach($_x as $_lang) {
				// 完全一致の場合 (ex. ja_JP)
				if (in_array($_lang[0], self::$language_prepared)) return $_lang[0];
				// 言語のみの場合の対応
				$_x1 = explode('_', $_lang[0]);
				if ( count($_x1) === 2) continue;
				$c = $obj_l2c->getLang2Country($_x1[0]);
				if ( empty($c) ) continue;
				$str = $_x1[0].'_'.$c;
				if ( in_array($str, self::$language_prepared) ) return $str;
			}
		}
		return DEFAULT_LANG;
	}
	/*
	 * set_mbstring
	 *
	 */
	private static function setMbstring($lang)
	{
		mb_language( self::getMbLanguage($lang) );

		mb_internal_encoding('UTF-8');
		ini_set('mbstring.http_input', 'pass');
		mb_http_output('pass');
		mb_detect_order('auto');
	}
	/*
	 * get_mb_language
	 * @return      string
	 */
	private static function getMbLanguage($lang)
	{
		// ja_JP 指定のキーは存在するか？
		if ( array_key_exists($lang, self::$mb_language_key) ) return self::$mb_language_key[ $lang ];
		// ja か ja_JP かの判定
		$x = explode('_', $lang);
		// ja のみなら処理を終了
		if ( count($x) == 1) return self::$mb_language_key['default'];
		// ja_JP を ja にして再検索
		if ( array_key_exists($x[0], self::$mb_language_key) ) return self::$mb_language_key[ $x[0] ];
		return self::$mb_language_key['default'];
	}
}