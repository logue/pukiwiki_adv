<?php
/**
 * ルータークラス
 *
 * @package   PukiWiki
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2014 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Render.php,v 1.0.0 2014/03/23 23:30:00 Logue Exp $
 */
namespace PukiWiki;

use PukiWiki\Utility;

/**
 * アドレス生成クラス
 */
class Router{
	private static function init($init_uri = '',$get_init_value=0){
		global $script_directory_index, $absolute_uri;
		static $script;

		if ( empty($init_uri) ) {
			// Get
			if (isset($script)) {
				if ($get_init_value) return $script;
				return $absolute_uri ? self::get_script_absuri() : $script;
			}
			$script = self::get_script_absuri();
			return $script;
		}

		// Set manually
		if (isset($script)) Utility::dieMessage('$script: Already init');
		if (! self::is_reluri($init_uri) && ! is_url($init_uri, TRUE)) Utility::dieMessage('$script: Invalid URI');
		$script = $init_uri;

		// Cut filename or not
		if (isset($script_directory_index)) {
			if (! file_exists($script_directory_index))
				Utility::dieMessage('Directory index file not found: ' .
					Utility::htmlsc($script_directory_index));
			$matches = array();
			if (preg_match('#^(.+/)' . preg_quote($script_directory_index, '#') . '$#',
				$script, $matches)) $script = $matches[1];
		}

		return $absolute_uri ? self::get_script_absuri() : $script;
	}

	/**
	 * スクリプトのURLを取得
	 * @params string $path パス
	 * @return string
	 */
	public static function get_script_uri($path='')
	{
	//	$uri = basename(__FILE__);
		global $absolute_uri, $script_directory_index;

		if ($absolute_uri === 1) return self::get_script_absuri();
		$uri = self::get_baseuri($path);
		if (! isset($script_directory_index)) $uri .= self::init();
		return $uri;
	}

	/**
	 * ページの基準名を取得
	 * @param string $str
	 * @return string;
	 */
	public static function getBasePageName($str){
		return preg_replace('#^.*/#', '', $str);
	}

	/**
	 * スクリプトの絶対URLを取得
	 * @return string
	 */
	public static function get_script_absuri()
	{
		global $script_abs, $script_directory_index;
		global $script;
		static $uri;

		// Get
		if (isset($uri)) return $uri;

		if (isset($script_abs) && Utility::isUri($script_abs,true)) {
			$uri = $script_abs;
			return $uri;
		} else
		if (isset($script) && Utility::isUri($script,true)) {
			$uri = $script;
			return $uri;
		}

		// Set automatically
		$msg	 = 'get_script_absuri() failed: Please set [$script or $script_abs] at INI_FILE manually';

		$uri  = ( ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
                if(strpos($uri,':')===FALSE) {
                        $uri .= ($_SERVER['SERVER_PORT'] == 80 || $_SERVER['SERVER_PORT'] == 443) ? '' : ':' . $_SERVER['SERVER_PORT'];  // port
                }

		// SCRIPT_NAME が'/'で始まっていない場合(cgiなど) REQUEST_URIを使ってみる
		$path	= SCRIPT_NAME;
		if ($path{0} !== '/') {
			if (! isset($_SERVER['REQUEST_URI']) || $_SERVER['REQUEST_URI']{0} != '/') {
				Utility::dieMessage($msg);
			}

			// REQUEST_URIをパースし、path部分だけを取り出す
			$parse_url = parse_url($uri . $_SERVER['REQUEST_URI']);
			if (! isset($parse_url['path']) || $parse_url['path']{0} != '/') {
				Utility::dieMessage($msg);
			}

			$path = $parse_url['path'];
		}
		$uri .= $path;

		if (! is_url($uri, true) && php_sapi_name() == 'cgi') {
			Utility::dieMessage($msg);
		}
		unset($msg);

		// Cut filename or not
		if (isset($script_directory_index)) {
			if (! file_exists($script_directory_index))
				Utility::dieMessage('Directory index file not found: ' .
				Utility::htmlsc($script_directory_index));
			$matches = array();
			if (preg_match('#^(.+/)' . preg_quote($script_directory_index, '#') . '$#',
				$uri, $matches)) $uri = $matches[1];
		}

		return $uri;
	}
	/**
	 * プラグインのアドレスを取得
	 */
	public static function get_cmd_uri($cmd='', $page='', $path_reference='rel', $query='', $fragment='')
	{
		return self::get_resolve_uri($cmd,$page,$path_reference,$query,$fragment,0);
	}
	public static function get_page_uri($page, $path_reference='rel', $query='', $fragment='')
	{
		return self::get_resolve_uri('',$page,$path_reference,$query,$fragment,0);
	}
	/**
	 * アクションに応じたアドレスを取得
	 * @param string $cmd プラグイン名
	 * @param string $page ページ名
	 * @param string $path_reference 取得するアドレスのタイプ
	 * @param array $query 渡すQueryStringの配列
	 * @param string $fragment アンカーを指定
	 * @return string
	 */
	public static function get_resolve_uri($cmd='read', $page=null, $path_reference='rel', $query=array(), $fragment=null)
	{
		global $static_url, $url_suffix;

		$path = empty($path_reference) ? 'rel' : $path_reference;
		$ret = self::get_script_uri($path);

		if ($cmd === 'read') {
		// Apacheは、:が含まれるアドレスを正確に処理できない
			// https://issues.apache.org/bugzilla/show_bug.cgi?id=41441
			if ($static_url === 1 && 
				!( stristr(getenv('SERVER_SOFTWARE'), 'apache') !== FALSE && (strstr($page, ':' ) !== FALSE || strstr($page,' ' ) !== FALSE) )){
				$ret .= str_replace('%2F', '/', rawurlencode($page));
			}else{
				$ret .= '?' . rawurlencode($page);
			}
		}else{
			$query['cmd'] = $cmd;
			if (! empty($page)) {
				$query['page'] = $page;
			}
			$ret .= '?' . http_build_query($query);
		}

		// fragment
		if (! empty($fragment)) {
			$ret .= '#' . Utility::htmlsc($fragment);
		}
		unset($flag);
		return $ret;
	}
	public static function get_baseuri($path='')
	{
		static $script;
		// RFC2396,RFC3986 : relativeURI = ( net_path | abs_path | rel_path ) [ "?" query ]
		//				   absoluteURI = scheme ":" ( hier_part | opaque_part )
		$ret = '';
		if (!isset($script)) $script = self::init();
		$parsed_url = parse_url( ($path === 'rel') ? $script : self::get_script_absuri());

		switch($path) {
			case 'net': // net_path	  = "//" authority [ abs_path ]
				$pref = '//';
				if (isset($parsed_url['user'])) {
					$ret .= $pref . $parsed_url['user'];
					$pref = '';
					$ret .= (isset($parsed_url['pass'])) ? ':'.$parsed_url['pass'] : '';
					$ret .= '@';
				}
				if (isset($parsed_url['host'])) {
					$ret .= $pref . $parsed_url['host'];
					$pref = '';
				}
				$ret .= (isset($parsed_url['port'])) ? ':'.$parsed_url['port'] : '';
			case 'abs': // abs_path	  = "/"  path_segments
				if (isset($parsed_url['path']) && ($pos = strrpos($parsed_url['path'], '/')) !== false) {
					$ret .= substr($parsed_url['path'], 0, $pos + 1);
				} else {
					$ret .= '/';
				}
				break;
			case 'rel': // rel_path	  = rel_segment [ abs_path ]
				if (Utility::isUri($script, true)) {
					$ret = './';
				} else {
					if (isset($parsed_url['path']) && ($pos = strrpos($parsed_url['path'], '/')) !== false) {
						$ret .= substr($parsed_url['path'], 0, $pos + 1);
					}
				}
				break;
			case 'full':
			default:
				$absoluteURI = self::get_script_absuri();
				$ret = substr($absoluteURI, 0, strrpos($absoluteURI, '/')+1);
				break;
		}

		return $ret;
	}
	/**
	 * 相対URLか
	 * @param string $str 入力文字
	 * @return boolean
	 */
	private function is_reluri($str)
	{
		// global $script_directory_index;
		switch ($str) {
			case '':
			case './':
			case 'index.php';
			case './index.php';
				return true;
			}
		// if (! isset($script_directory_index) && $str == 'index.php') return true;
		return false;
	}
}
