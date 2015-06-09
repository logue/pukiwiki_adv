<?php
/**
 * 言語判定クラス
 *
 * @package   PukiWiki\Lang
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright Copyright &copy; 2005-2006,2008 Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @create    2013/02/12
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: AcceptLanguage.php,v 1.0.1 2015/06/09 20:43:00 Logue Exp $
 **/

namespace PukiWiki\Lang;

use Locale;

/**
 * CHARSET から言語_国を推測する
 * @abstract
 *
 * 1) COOKIE['lang']
 * 2) HTTP_ACCEPT_LANGUAGE
 * 3) HTTP_USER_AGENT
 * 4) HTTP_ACCEPT_CHARSET
 * 5) REMOTE_ADDR
 */
class AcceptLanguage
{
	// LANGUAGE_COUNTRY is guessed from CHARSET.
	private static $charset = array(
		'shift_jis'	=> 'ja_JP', // 392
		'sjis'		=> 'ja_JP',
		'ujis'		=> 'ja_JP',
		'euc_jp'	=> 'ja_JP',
		'x-euc'		=> 'ja_JP',
		'x-sjis'	=> 'ja_JP',
		'ms_kanji'	=> 'ja_JP',
		'euc-kr'	=> 'ko_KR', // 410
		'johab'		=> 'ko_KR',
		'uhc'		=> 'ko_KR',
		'gbk'		=> 'zh_CN', // 156 China, People's Republic of
		'cp936'		=> 'zh_CN',
		'ms936'		=> 'zh_CN',
		'gb18030'	=> 'zh_CN',
		'gb2312'	=> 'zh_CN',
		'hz'		=> 'zh_CN',
		'big5-hkscs'	=> 'zh_HK', // 344 Hong Kong, Special Administrative Region of China
		'big5'		=> 'zh_TW', // 158 Taiwan, Province of China
		'euc-tw'	=> 'zh_TW',
		'tis-620'	=> 'th_TH',
		'windows-874'	=> 'th_TH',
		'iso-8859-11'	=> 'th_TH',
		'tcvn'		=> 'vi_VN',
		'vps'		=> 'vi_VN',
		'koi8-u'	=> 'uk_UA',
	);

	// The LANGUAGE used is guessed from the COUNTRY.
	// 国から使用言語を推測する
	private static $flag = array(
		'jp' => 'ja',
		'kr' => 'ko',
		'tw' => 'zh',
		'de' => 'de',
		'fr' => 'fr',
		'uk' => 'en',
		'co' => 'es',
		'es' => 'es,ca,gl,eu',
		'it' => 'it',
		'se' => 'sv',
		'ch' => 'de,en,fr,it',
		'ca' => 'en,fr',
		'mx' => 'es',
		'il' => 'iw',
		'nl' => 'nl',
		'be' => 'nl,fr,de,en',
		'cl' => 'es',
		'au' => 'en',
		'id' => 'id,en,nl,jw',
		'ar' => 'es',
		'pa' => 'es,en',
		'at' => 'de',
		'pl' => 'pl',
		'dk' => 'da,fo',
		'ru' => 'ru',
		'br' => 'pt_BR',
		'nz' => 'en',
		'fi' => 'fi,sv',
		'in' => 'en,hi,bn,te,mr,ta',
		'th' => 'th,en',
		'ph' => 'tl,en',
		'pt' => 'pt_PT',
		'no' => 'no,nn',
		'lt' => 'lt',
		'ua' => 'uk,ru',
		'lu' => 'de,fr',
		'za' => 'en,af,st,zu,xh',
		'pk' => 'en,ur,pa',
		'do' => 'es',
		'cr' => 'es,en',
		'lv' => 'lv,lt,ru',
		'vn' => 'vi,en,fr,zh_TW',
		'ie' => 'en,ga',
		'my' => 'en,ms',
		'ae' => 'ar,ur,en,hi,fa',
		'gr' => 'el',
		'sk' => 'sk,hu',
		'sa' => 'ar',
		'ec' => 'es',
		'gt' => 'es',
		'sg' => 'en,zh_CN,ms,ta',
		've' => 'es',
		'pe' => 'es',
		'ro' => 'ro,hu,de',
		'hk' => 'en,zh_TW',
		'tr' => 'tr',
		'hu' => 'hu',
		'pr' => 'es,en',
		'bz' => 'en,es',
		'sv' => 'es',
		'mt' => 'mt,en',
		'tt' => 'en,hi,fr,es,zh_TW',
		'uy' => 'es',
		'bo' => 'es',
		'li' => 'de',
		'np' => 'ne,en',
		'cu' => 'es',
		'hn' => 'es',
		'ni' => 'es,en',
		'py' => 'es',
		'ci' => 'fr',
		'ly' => 'ar,it,en',
		'gl' => 'da,en',
		'az' => 'az,ru',
		'kz' => 'ru',
		'ke' => 'en,sw',
		'ug' => 'en',
		'fj' => 'en',
		'jm' => 'en',
		'mn' => 'mn',
		'na' => 'en,af',
		'am' => 'hy,ru',
		'ag' => 'en',
		'vi' => 'en',
		'vg' => 'en',
		'sm' => 'it',
		'mu' => 'en,fr',
		'bi' => 'fr',
		'as' => 'en',
		'uz' => 'uz,ru',
		'kg' => 'ky,ru',
		'rw' => 'en,fr,sw',
		'gi' => 'en,es,it,pt_PT',
		'ls' => 'en,zu',
		'tm' => 'tk,ru,uz',
		'ai' => 'en',
		'vc' => 'en',
		'sc' => 'en,fr',
		'mw' => 'en',
		'fm' => 'en',
		'ms' => 'en',
		'nf' => 'en',
		'sh' => 'en',
		'cd' => 'fr',
		'gg' => 'en,fr',
		'to' => 'en',
		'je' => 'en,fr',
		'gm' => 'en',
		'cg' => 'fr',
		// 'td' => '',
		'dj' => 'fr,ar',
		'pn' => 'en',
		'ck' => 'en',
	);

	/*
	 * get_cookie_lang
	 * @static
	 * @return	array
	 */
	public static function getCookieLanguage()
	{
		if (isset($_COOKIE['lang']) ) {
			$cookie['lang'] = $_COOKIE['lang'];
		} else {
			return '';
		}

		if ($cookie['lang'] === 'none') return '';
		$l = self::splitLocaleStr($cookie['lang']);
		return array(array($l[0],1));
	}

	/*
	 * get_accept_language
	 *
	 * HTTP_ACCEPT_LANGUAGE の文字列を分解する。
	 * @static
	 * @return	array
	 */
	public static function getAcceptLanguage()
	{
		if ( !isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ) return '';
		$accept_language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
		// TEST:
		//$accept_language = 'ko,en,ja,fr;q=0.7,DE;q=0.3';
		return extension_loaded('intl') ?
			Locale::acceptFromHttp($accept_language) : self::splitStr($accept_language);
	}

	/*
	 * get_user_agent_mozilla
	 * USER-AGENT から最近の Mozilla の場合
	 * 設定されているlocale文字列を取得する
	 * @static
	 * @return	array
	 */
	public static function getUserAgentMozilla()
	{
		if ( !isset($_SERVER['HTTP_USER_AGENT']) ) return '';
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		// TEST:
		// $user_agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-TW; rv:1.7.5) Gecko/20041119 Firefox/1.0';
		// $user_agent = 'Mozilla/5.0 (Macintosh; U; PPC Mac OS X; ja-jp) AppleWebKit/125.2 (KHTML, like Gecko) Safari/125.8';
		$rc = array();
		preg_match("'Mozilla.*? \((.*?)\) .*?'si",$user_agent,$regs);
		if ( count($regs) < 2) return '';
		foreach(split(';',$regs[1]) as $x) {
			$str = trim($x);
			$i = strlen($str);
			if ($i == 5 || $i == 2) {
				$l = self::splitLocaleStr($str);
				$rc[] = array($l[0],1);
			}
		}
		return $rc;
	}

	/*
	 * get_accept_charset
	 *
	 * HTTP_ACCEPT_CHARSET で設定される利用可能な
	 * 文字コードから言語を見做し判定する
	 * @return	array
	 */
	public static function getAcceptCharset()
	{
		if ( !isset($_SERVER['HTTP_ACCEPT_CHARSET']) ) return '';
		$accept_charset = $_SERVER['HTTP_ACCEPT_CHARSET'];
		// TEST:
		// $accept_charset = 'Shift_JIS,utf-8;q=0.7,*;q=0.7';
		// 取り扱い文字が、CHARSET列なので言語_国変換を行わない(第2引数)
		$tmp = self::splitStr($accept_charset,FALSE);
		$rc = array();
		foreach($tmp as $x) {
			$chr = strtolower( $x[0] ); // Shift_JIS などは shift_jis に変換
			if (array_key_exists($chr,$this->charset)) {
				$rc[] = array($this->charset[$chr],$x[1]);
			}
		}
		return $rc;
	}

	/*
	 * get_remote_addr
	 * IPアドレスから国を特定し、見做し言語を判定する
	 * @return	array
	 */
	public static function getRemoteAddr()
	{
		$ip = isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR'];
		if (empty($ip)){
			return '';
		}
		
		$host = gethostbyaddr($ip);
		if ($ip == $host) return '';
		$x = strtolower( substr( $host,strrpos($host, '.') + 1 ) );
		if (isset($this->flag[$x])){
			return self::splitStr($this->flag[$x], FALSE, FALSE);
		}
	}

	/*
	 * split_str
	 *
	 * x1,x2;q=0.6,x3;q=0.4 のような書式を分解する
	 * @static
	 * @return array
	 * $rc[0] = (x1,1),(x2,0.6),(x3,0.4) が入る。
	 * 値順に整列して戻す。
	 */
	private static function splitStr($env, $conv=TRUE, $sort=TRUE)
	{
		$rc = array();
		foreach( explode(',',$env) as $x ) {
			$x1 = explode(';', $x);
			// '',1 の '' は、DUMMY
			$q = count($x1) === 1 ? array('', 1) : explode('=', $x1[1]);
			if ($conv) {
				$l = self::splitLocaleStr($x1[0]);
				$rc[] = array( $l[0], $q[1]);
			} else {
				$rc[] = array( $x1[0], $q[1]);
			}
		}
		if ($sort) {
			uasort($rc,function($a,$b){
				return ($a[1] == $b[1]) ? 0 : (($a[1] > $b[1]) ? -1 : 1);
			});
			// usort: 比較結果が等しい場合、 配列の順番は定義されない
			usort($rc,function($a,$b){
				return ($a[1] == $b[1]) ? 0 : (($a[1] > $b[1]) ? -1 : 1);
			});
		}
		return $rc;
	}

	/*
	 * split_locale_str
	 *
	 * 言語-国(省略可)の文字列を一律、言語(小文字)、国(大文字)に変換
	 * 言語と国の接続文字は、ハイフンまたはアンダースコアとする
	 * @static
	 * @return string
	 */
	public static function splitLocaleStr($str)
	{
		$x = preg_split('/[-_]/', $str);

		$lang    = strtolower( $x[0] );
		$country = count($x) === 2 ? strtoupper( $x[1] ) : '';
		$join = count($x) === 2 ? '_' : '';

		return array( $lang . $join . $country, $lang, $country );
	}
}