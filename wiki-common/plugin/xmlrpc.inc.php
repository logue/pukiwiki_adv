<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone
// $Id: xmlrpc.inc.php,v 0.0.0 2012/10/30 09:45:00 Logue Exp $
// Copyright (C)
//	 2012      PukiWiki Advance Developers Team
// License: GPL v2
//
// XML-Rpc Server plugin

defined('XMLRPC_CACHE') or define('XMLRPC_CACHE', CACHE_DIR.'xmlrpc.cache');

function plugin_xmlrpc_action(){
	header('Content-Type: application/xml; charset=' . CONTENT_CHARSET);
	$server = new Zend\XmlRpc\Server();
	//if (!Zend\XmlRpc\Server\Cache::get(XMLRPC_CACHE, $server) && !DEBUG) {
		$server->setClass('PingBackService', 'pingback');
		$server->setClass('WikiRpcService', 'wiki');
	//	Zend\XmlRpc\Server\Cache::save(XMLRPC_CACHE, $server);
	//}
	echo $server->handle();
	exit;
}


// PingBack
defined('PINGBACK_PREFIX') or define('PINGBACK_PREFIX', 'pb-');
class PingBackService{
	 /**
	  * Pingbackサービス
	  *
	  * @param string $source ページのPing送信用のアドレス
	  * @param string $target ページのPing待受用のアドレス
	  * @return xml
	  */
	public function ping($source, $target) {
		global $url_suffix;
		$source_url = Zend\Uri::factory($source);
		$target_url = Zend\Uri::factory($target);

		// 相手のサイトに接続
		$source_client = new Zend_Http_Client($source_url);
		$source_response = $source_client->request(Zend_Http_Client::GET);

		// 接続できたかをチェック
		if (!$source_response->isSuccessful()) {
			return 0x0010;
		}
		// 相手のサイトの中身を取得
		$source_body = $source_response->getBody();
		// 相手サイトのタイトルを取得
		preg_match('/<title>([^<]*?)</title>/is', $source_body, $source_titles);
		$source_title = is_null($source_titles[1]) ? 'Untitled' : $source_titles[1];

		// ソース URI のデータにターゲット URI へのリンクが存在しないため、ソースとして使用できない。
		if ($target_url->getHost() !== $source_url->getHost() && (strpos($source_body, $source_url)) === false) {
			return 0x0011;
		} else if ($target_url->getHost() === $source_url->getHost() && (strpos($source_body, $source_url->getPath())) === false) {
			return 0x0011;
		}

		// 自分のサイトにページが存在するかのチェック
		if ( $target_url->getQuery() == '' ){
			// http://[host]/[pagename]の場合（スラッシュは再エンコード）
			$r_page = str_replace('/', '%2F', $target_url->getPath());
			// $url_suffixが含まれる場合、正規表現でそこを削除
			$_page = empty($url_suffix) ? $r_page : preg_replace('/'.$url_suffix.'$/', '', $r_page);
			unset($r_page);
		}else{
			// http://[host]/?[pagename]の場合
			// PukiWiki Adv.では=をページに含めることは許可していない（aa=bbみたいなページが作成できない）ため、
			// これを判定処理に使う
			// init.phpのPKWK_ILLEGAL_CHARS_PATTERNを見よ
			if (strpbrk($target_url->getQuery(), '=') !== FALSE){
				return 0x0021;	// 指定された URI はターゲットとして使用できない。
			}
			$_page = $target_url->getQuery();
		}
		$page = rawurldecode($_page);	// ページ名をデコード
		// ページの存在確認
		if (! is_page($page)) return 0x0020;	// 指定されたターゲット URI が存在しない。

		// TODO:書き込み処理
		$data  = get_pingback_data($page, 3);
		$d_url = rawurldecode($url);
		if (! isset($data[$source_url])) {
			$data[$d_url] = array(
				'',				// [0]: Last update date
				UTIME,			// [1]: Creation date
				0,				// [2]: Reference counter
				$source_url,	// [3]: Source URL
				$source_title	// [4]: Source Title
			);
		}
		pkwk_touch_file($filename);
		$data[$d_url][0] = UTIME;
		$data[$d_url][2]++;

		update_pingback_data($page);
		unset($fp,$filename);
		return TRUE;

	}
	private function get_pingback_data($page){
		$filename = REFERER_DIR . PINGBACK_PREFIX . encode($page) . '.txt';

		if (! file_exists($filename)) return array();
		$ret = array();
		$fp = @fopen($filename, 'r');
		set_file_buffer($fp, 0);
		@flock($fp, LOCK_EX);
		rewind($fp);
		while ($data = @fgets($fp, 8192)) {
			$ret[rawurldecode($data[$uniquekey])] = explode("\t", $data);
		}
		@flock($fp, LOCK_UN);
		fclose ($fp);
		return $ret;
	}
	private function update_pingback_data($page, $data){
		pkwk_touch_file($filename);
		$fp = fopen($filename, 'w');
		if ($fp === FALSE) return FALSE;
		set_file_buffer($fp, 0);
		@flock($fp, LOCK_EX);
		rewind($fp);
		foreach ($data as $line) {
			$str = trim(join("\t", $line));
			if ($str != '') fwrite($fp, $str . "\n");
		}
		@flock($fp, LOCK_UN);
		fclose($fp);
	}
}

// WikiRpc
// http://www.jspwiki.org/Wiki.jsp?page=WikiRPCInterface2
class WikiRpcService{
	/**
	  * timestamp（UTC）以降に更新されたページのリストを得る。
	  *
	  * @param string $timestamp 時刻
	  * @return xml
	  */
	public function getRecentChanges( $timestamp ){
		$pages = get_existpages_cache(DATA_DIR, '.txt');

		$ret = array();
		foreach ($pages as $page => $time) {
			$passage = $time - $timestamp;
			if ($passage < 0) continue;
			$ret[] = $page;
		}
		return $ret;
	}
	/**
	  * サポートしているWikiRpc APIのバージョン。2を返す。
	  *
	  * @return 2
	  */
	public function getRPCVersionSupported(){
		return 2;
	}
	/**
	  * ページの最新版の生テキストを返す。
	  *
	  * @param string $pagename ページ名
	  * @return string
	  */
	public function getPage( $pagename ){
		$wiki = new PukiWiki\Lib\File\WikiFile($pagename);
		return $wiki->source();
	}
	/**
	  * 版を指定してページの生テキストを返す。
	  *
	  * @param string $pagename ページ名
	  * @param int $version 版
	  * @return string
	  */
	public function getPageVersion( $pagename, $version ){
		$backup = new PukiWiki\Lib\File\BackupFile($pagename);
		$ret = $backup->getBackup($version);
		auth::is_role_page($ret[$version]['data']);
		return join("\n", $ret[$version]['data']);
	}
	/**
	  * ページの最新版のHTMLを返す。
	  *
	  * @param string $pagename ページ名
	  * @return string
	  */
	public function getPageHTML( $pagename ){
		$wiki = new PukiWiki\Lib\File\WikiFile($pagename);
		return $wiki->render();
	}
	/**
	  * 版を指定してページのHTMLを返す。
	  *
	  * @param string $pagename ページ名
	  * @param int $version 版
	  * @return string
	  */
	public function getPageHTMLVersion( $pagename, $version ){
		$backup = new PukiWiki\Lib\File\BackupFile($pagename);
		$ret = $backup->getBackup($version);
		auth::is_role_page($ret[$version]['data']);
		return convert_html(join("\n", $ret[$version]['data']));
	}
	/**
	  * 全てのページ名からなる配列を返す。
	  *
	  * @return array
	  */
	public function getAllPages(){
		$pages = get_existpages_cache(DATA_DIR, '.txt');
		return array_values($pages);
	}
	/**
	  * ページ情報。
	  *
	  * @param string $pagename ページ名
	  * @return struct
	  */
	public function getPageInfo( $pagename ){

	}
	/**
	  * バージョン指定版ページ情報。
	  *
	  * @param string $pagename ページ名
	  * @param int $version 版
	  * @return struct
	  */
	public function getPageInfoVersion( $pagename, $version ){

	}
	/**
	  * ページ内のすべてのリンクのリスト。
	  *
	  * @param string $pagename ページ名
	  * @return array
	  */
	public function listLinks( $pagename ){
		$wiki = new PukiWiki\Lib\File\WikiFile($pagename);
		$links = array();
		preg_match_all('#href="(https?://[^"]+)"#', $wiki->render(), $links, PREG_PATTERN_ORDER);
		return array_unique($links[1]);
	}
	/**
	  * このページにリンクしているページの配列を返す。
	  *
	  * @param string $pagename ページ名
	  * @return array
	  */
	public function getBackLinks( $pagename ){
		$links = new PukiWiki\Lib\Relational($pagename);
		return $links->get_related();
	}
	/**
	  * ページを編集する。
	  *
	  * @param string $pagename ページ名
	  * @param string $content ページの内容
	  * @param struct $attributes タイムスタンプを更新するしないなどのフラグの入ったオブジェクト
	  * @return array
	  */
	public function putPage( $pagename, $content, $attributes ){

	}
	/**
	  * 添付ファイル名のリスト
	  *
	  * @param string $pagename ページ名
	  * @return array
	  */
	public function listAttachments( $pagename ){
		if (exist_plugin('attach') && do_plugin_init('attach') !== FALSE){
			$obj = new AttachPages($pagename, 0);
			return array_keys($obj->pages[$pagename]->files);
		}
		return false;
	}
	/**
	  * base64エンコードされた添付ファイルを返す。
	  *
	  * @param string $attachmentName ファイル名
	  * @return base64
	  */
	public function getAttachment( $attachmentName ){

	}
	/**
	  * ファイルを添付、上書きする。
	  *
	  * @param string $attachmentName ファイル名
	  */
	public function putAttachment( $attachmentName, $content ){

	}
	/**
	  * ファイルの詳細情報
	  *
	  * @param string $attachmentName ファイル名
	  * @return struct
	  */
	public function getAttachmentInfo( $attachmentName){

	}
}
