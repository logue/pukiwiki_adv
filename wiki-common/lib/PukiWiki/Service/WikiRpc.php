<?php
/**
 * WikiRpcサービスクラス
 *
 * @package   PukiWiki
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2014 PukiWiki Advance Developers Team
 * @create    2014/02/27
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: WikiRpcService.php,v 1.0.1 2014/02/27 22:57:00 Logue Exp $
 **/

namespace PukiWiki\Service;

use PukiWiki\Factory;
use PukiWiki\Listing;
use PukiWiki\Recent;
use PukiWiki\Relational;
use PukiWiki\Renderer\RendererFactory;

/**
 * WikiRpcサービスクラス
 * 参考：
 * 　http://www.hyuki.com/yukiwiki/wiki.cgi?WikiRPC
 * 　http://www.ecyrd.com/JSPWiki/wiki/WikiRPCInterface2
 * 　http://trac-hacks.org/wiki/XmlRpcPlugin
 * 　https://www.dokuwiki.org/devel:xmlrpc
 *
 * ※Zend\XmlRpcの仕様上、phpdocのコメント文の最初の行を説明文として返すため、
 * 　日本語が含まれるとXMLエラーになってしまう。
 * 　このため、２行目に日本語の説明文を入れる。
 */
class WikiRpc{
	/**
	 * WikiRpcのバージョン
	 */
	const WIKI_RPC_VERSION = 2;
	/**
	 * 送信に成功した
	 */
	const RESPONSE_SUCCESS                  = -1;
	/**
	 * 閲覧権限なし
	 */
	const RESPONSE_PAGE_NOT_READABLE        = 0x0111;
	/**
	 * 編集権限なし
	 */
	const RESPONSE_PAGE_NOT_EDITABLE        = 0x0112;
	/**
	 * ページが見つからない
	 */
	const RESPONSE_PAGE_NOT_FOUND           = 0x0121;
	/**
	 * ページ名が空
	 */
	const RESPONSE_PAGE_PAGENAME_IS_EMPTY   = 0x0131;
	/**
	 * ページの内容が空（未使用）
	 */
	const RESPONSE_PAGE_CONTENT_IS_EMPTY    = 0x0132;
	/**
	 * 凍結されている
	 */
	const RESPONSE_PAGE_FREEZED             = 0x0133;
	/**
	 * アクセス拒否
	 */
	const RESPONSE_FAULT_ACCESS_DENIED      = 0x0031;
	/**
	 * Returns 2 with the supported RPC API version.
	 * サポートしているWikiRpcのバージョン。２を返す。
	 * @return int
	 */
	public function getRPCVersionSupported(){
		return self::WIKI_RPC_VERSION;
	}
	/**
	 * Returns the permission of the given wikipage
	 * ページの権限を返す。
	 * @param string ページ名
	 */
	public function aclCheck($page){
		$wiki = Factory::Wiki($page);
		return $wiki->isReadable();
	}
	/**
	  * Returns the raw Wiki text for a page.
	  * ページの生のテキストを返す
	  * @param string $pagename ページ名
	  * @return string
	  */
	public function getPage( $page ){
		$wiki = Factory::Wiki($page);
		return $wiki->isReadable() ? $wiki->get(true) : false;
	}
	/**
	  * Returns the raw Wiki text for a specific revision of a Wiki page.
	  * ページのバックアップを取得する
	  * @param string $pagename ページ名
	  * @param int $version 版
	  * @return string
	  */
	public function getPageVersion( $page, $version ){
		return Factory::Backup($page)->get($version);
	}
	/**
	  * Returns the available versions of a Wiki page. The number of pages in the result is controlled via the recent configuration setting. The offset can be used to list earlier versions in the history.
	  * 
	  * @param string $pagename ページ名
	  * @param int $version 版
	  * @return string
	  */
	public function getPageVersions( $page, $version ){
		// 未実装
	}
	/**
	  * Returns information about a Wiki page.
	  * ページの情報を取得する
	  * @param string $pagename ページ名
	  * @return struct
	  */
	public function getPageInfo( $page ){
		$wiki = Factory::Wiki($page);
		return array(
			'name' => $page,
			'lastModified' => $wiki->time(),
		);
	}
	/**
	 * Returns the rendered XHTML body of a Wiki page.
	 * ページの最新版のHTMLを返す。
	 * @param string $pagename ページ名
	 * @return string
	 */
	public function getPageHTML( $pagename ){
		return Factory::Wiki($pagename);
	}
	/**
	 * Returns the rendered HTML of a specific version of a Wiki page.
	 * 版を指定してページのHTMLを返す。
	 * @param string $pagename ページ名
	 * @param int $version 版
	 * @return string
	 */
	public function getPageHTMLVersion( $page, $version ){
		return RendererFactory::factory(Factory::Backup($page)->get($version));
	}
	/**
	  * Saves a Wiki Page.
	  * ページを編集する。
	  * @param string $pagename ページ名
	  * @param string $content ページの内容
	  * @param struct $attributes タイムスタンプを更新するしないなどのフラグの入ったオブジェクト
	  * @return array
	  */
	public function putPage( $pagename, $content, $attributes ){
		global $notimeupdate;
		$notimestamp = isset($attributes['notimestamp']) || $notimeupdate ? $attributes['notimestamp'] : false;
		$wiki = Factory::Wiki($pagename);
		if (!$wiki->isValied() || !$wiki->isEditable()) {
			return false;
		}
		return $wiki->set($content, $notimestamp);
	}
	/**
	  * Returns a list of all links contained in a Wiki page.
	  * ページ内のすべてのリンクのリスト。
	  * @param string $pagename ページ名
	  * @return array
	  */
	public function listLinks( $pagename ){
		$links = array();
		preg_match_all('/href="(https?:\/\/[^"]+)"/', Factory::Wiki($pagename)->render(), $links, PREG_PATTERN_ORDER);
		return array_unique($links[1]);
	}
	/**
	  * Returns a list of all Wiki pages in the remote Wiki
	  * 全てのページ名からなる配列を返す。
	  * @return array
	  */
	public function getAllPages(){
		return Listing::pages();
	}
	/**
	  * Returns a list of backlinks of a Wiki page.
	  * このページにリンクしているページの配列を返す。
	  * @param string $pagename ページ名
	  * @return array
	  */
	public function getBackLinks( $pagename ){
		$links = new Relational($pagename);
		return $links->getRelated();
	}
	/**
	  * Returns a list of recent changes since given timestamp.
	  * timestamp（UTC）以降に更新されたページのリストを得る。
	  * @param string $timestamp 時刻
	  * @return array
	  */
	public function getRecentChanges( $timestamp = 0 ){
		$ret = array();
		$recents = Recent::get();
		if ($timestamp === 0) return array_keys($recents);
		foreach ($recents as $page=>$time){
			if ($time < $timestamp) continue;
			$ret[] = $page;
		}
		return $ret;
	}
	/**
	  * バージョン指定版ページ情報。
	  *
	  * @param string $pagename ページ名
	  * @param int $version 版
	  * @return struct
	  */
	public function getPageInfoVersion( $pagename, $version ){
		// 未実装
	}
	/**
	  * Returns a list of media files
	  * 添付ファイル名のリスト
	  * @param string $pagename ページ名
	  * @return array
	  */
	public function listAttachments( $pagename ){
		$wiki = Factory::Wiki($pagename);
		if (!$wiki->isValied() || !$wiki->isReadable()) {
			return false;
		}
		return array_keys($wiki->attach())[0];
	}
	/**
	 * Returns the binary data of a media file
	 * base64エンコードされた添付ファイルを返す。
	 * @param string $page ページ名
	 * @param string $filename ファイル名
	 * @return base64
	 */
	public function getAttachment( $page, $filename ){
		$wiki = Factory::Wiki($pagename);
		if (!$wiki->isValied() || !$wiki->isReadable()) {
			return false;
		}
		$attach = Factory::Attach($page, $filename);
		if (!$attach->has()) {
			return false;
		}
		return base64_encode($attach->get());
	}
	/**
	 * Returns information about a media file
	 * ファイルの詳細情報
	 * @param string $page ページ名
	 * @param string $filename ファイル名
	 * @return struct
	 */
	public function getAttachmentInfo($page, $filename ){
		$wiki = Factory::Wiki($pagename);
		if (!$wiki->isValied() || !$wiki->isReadable()) {
			return false;
		}
		$attach = Factory::Attach($page, $filename);
		if (!$attach->has()) {
			return false;
		}
		
		$f = new File(UPLOAD_DIR . $this->files[0]);
		
		return array(
			'mime' => $attach->getMime(),
			'size' => $f->getSize(),
			'lastModified' => $f->getMTime(),
			'md5'  => $f->md5()
		);
		
	}
	/**
	 * Uploads a file 
	 * ファイルを添付する。
	 * @param string $page ページ名
	 * @param string $filename ファイル名
	 * @param string $data base64変換されたデーター
	 */
	public function putAttachment($page, $filename, $data ){
		$wiki = Factory::Wiki($pagename);
		if (!$wiki->isValied() || !$wiki->isEditable()) {
			return false;
		}
		$attach = Factory::Attach($page, $filename);
		if ($attach->has()) {
			return false;
		}
		return $attach->set(base64_decode($data));
	}
	/**
	 * Deletes a file. Fails if the file is still referenced from any page in the wiki.
	 * ファイルを削除する
	 * @param string $page ページ名
	 * @param string $filename ファイル名
	 */
	public function deleteAttachment($page, $filename ){
		if (!$wiki->isValied() || !$wiki->isEditable()) {
			return false;
		}
		$attach = Factory::Attach($page, $filename);
		return $attach->delete();
	}
}