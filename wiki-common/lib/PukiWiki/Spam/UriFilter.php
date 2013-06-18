<?php
/**
 * URIスパムフィルター
 * 参考：spam_filter.php（http://miasa.info/index.php?%C8%FE%CB%E3Wiki%A4%C7%A5%B7%A5%B9%A5%C6%A5%E0%C5%AA%A4%CB%BD%A4%C0%B5%A4%B7%A4%C6%A4%A4%A4%EB%C5%C0）
 * 入力されたURIに対してのフィルタ
 *
 * @package   PukiWiki\Spam\UrlFilter
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2013 PukiWiki Advance Developers Team
 * @create    2013/05/29
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: IpFilter.php,v 1.0.0 2013/05/29 18:58:00 Logue Exp $
 */

namespace PukiWiki\Spam;

use PukiWiki\Spam\IpFilter;
use PukiWiki\Utility;

/**
 * Uriフィルタクラス
 */
class UriFilter extends IpFilter{
	/**
	 * URLBLキャッシュ名
	 */
	const BL_CACHE_NAME = 'urlbl';
	/**
	 * URLBLキャッシュのエントリの有効期限（１週間）
	 */
	const BL_CACHE_ENTRY_EXPIRE = 604800;
	/**
	 * デフォルトのURLBLリスト（先頭に.入れないこと）
	 */
	private $dnsbl_hosts = array(
		'multi.surbl.org',                  // SURBL.com
		'multi.uribl.com',                  // URIBL.com
		'url.rbl.jp',                       // rbl.jp
		'zen.spamhaus.org'                  // Spamhaus
	);
}