<?php
/**
 * 言語から国を推測する
 *
 * @copyright   Copyright &copy; 2005-2006,2008, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @version     $Id: lang.php,v 0.27.3 2012/05/20 09:29:00 Logue Exp $
 *
 */

namespace PukiWiki\Lib\Lang;

/**
 * 言語から国を推測する
 * @abstract
 */
class Lang2Country
{
	// The COUNTRY is guessed from the LANGUAGE.
	private static $lang = array(
		'ja' => 'JP',
		'ko' => 'KR',
		'zh' => 'TW',
		'de' => 'DE',
		'fr' => 'FR',
		'en' => 'US',
		'it' => 'IT',
		'lt' => 'LT',
		'pt' => 'PT',
	);

	/**
	 * get_lang2country
	 *
	 * 言語から国を推測する
	 * @return string
	 */
	public static function getLang2Country($x)
	{
		if (isset(self::$lang[$x])) return self::$lang[$x];
		return '';
	}

}