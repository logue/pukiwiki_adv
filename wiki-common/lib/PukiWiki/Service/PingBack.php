<?php
/**
 * PingBackサービス
 *
 * @package   PukiWiki
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2014 PukiWiki Advance Developers Team
 * @create    2014/02/27
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: PingBackService.php,v 1.0.1 2014/02/27 16:54:00 Logue Exp $
 */

namespace PukiWiki\Service;

use PukiWiki\Factory;
use PukiWiki\File\PingBackFile;
use Zend\Http\Client;
use Zend\Uri\Uri;

class PingBack{
	/**
	 * 送信に成功した
	 */
	const RESPONSE_SUCCESS                  = -1;
	/**
	 * 送信に失敗した
	 */
	const RESPONSE_FAULT_GENERIC            = 0;
	/**
	 * ソースURIが見つからない
	 */
	const RESPONSE_FAULT_SOURCE             = 0x0010;
	/**
	 * ソースにターゲットのリンクが存在しない
	 */
	const RESPONSE_FAULT_SOURCE_LINK        = 0x0011;
	/**
	 * ターゲットURIが見つからない
	 */
	const RESPONSE_FAULT_TARGET             = 0x0020;
	/**
	 * ターゲットのURIが無効である
	 */
	const RESPONSE_FAULT_TARGET_INVALID     = 0x0021;
	/**
	 * すでに登録されている
	 */
	const RESPONSE_FAULT_ALREADY_REGISTERED = 0x0030;
	/**
	 * アクセス拒否
	 */
	const RESPONSE_FAULT_ACCESS_DENIED      = 0x0031;
	/**
	 * リクエストされた内容が完了しなかった
	 */
	const RESPONSE_FAULT_CONNECT            = 0x0032;
	/**
	 * タイトルがない場合の名前
	 */
	const UNTITLED_TITLE = 'Untitled';
	/**
	 * Pingback
	 *
	 * @param string $source ページのPing送信用のアドレス
	 * @param string $target ページのPing待受用のアドレス
	 * @return int
	 */
	public function ping($source, $target) {
		// Zend\Uri\Uriオブジェクトを生成
		$source_url = Uri::factory($source);
		$target_url = Uri::factory($target);
		
		// 無効なアドレス
		if (!$target_url->isValid()){
			return self::RESPONSE_FAULT_TARGET_INVALID;
		}
		if (!$source_url->isValid()){
			return self::RESPONSE_FAULT_GENERIC;
		}

		if ($target_url->getHost() === $source_url->getHost()){
			// ターゲットとソースのホストが一緒
			// TODO: 同じドメインのサイトの場合、同じサイトとみなされる
			return self::RESPONSE_FAULT_SOURCE;
		}

		// 相手のサイトに接続
		$source_client = new Client($source_url);
		$source_response = $source_client->request(Client::GET);

		// 接続できたかをチェック
		if (!$source_response->isSuccessful()) {
			return self::RESPONSE_FAULT_SOURCE;
		}

		// 相手のサイトの中身を取得
		$source_body = $source_response->getBody();
		
		// 中身を取得できない
		if (!$source_body){
			return self::RESPONSE_FAULT_SOURCE;
		}

		if ($target_url->getHost() !== $source_url->getHost() && (strpos($source_body, $source_url)) === false) {
			// ソース URI のデータにターゲット URI へのリンクが存在しないため、ソースとして使用できない。
			return self::RESPONSE_FAULT_SOURCE_LINK;
		}

		// 相手サイトのタイトルを取得（XMLとして処理した方がいい？）
		$source_titles = array();
		preg_match('/<title>([^<]*?)</title>/is', $source_body, $source_titles);
		// タイトルが存在しないUntitled
		$source_title = empty($source_titles[1]) ? self::UNTITLED_TITLE : $source_titles[1];

		// ターゲットのクエリを取得（自サイト）
		$query = $target_url->getQuery();
		if ( empty($query) ){
			// http://[host]/[pagename]の場合（スラッシュは再エンコード）
			$r_page = str_replace('/', '%2F', $target_url->getPath());
			// $url_suffixが含まれる場合、正規表現でそこを削除
			//$page = empty($url_suffix) ? $r_page : preg_replace('/'.$url_suffix.'$/', '', $r_page);
			$page = rawurldecode($r_page);
			unset($r_page);
		}else{
			// ターゲットに=が含まれる場合はページではないので無効
			if (strpbrk($query, '=')) return self::RESPONSE_FAULT_TARGET_INVALID;
			$page = $query;
		}

		// ページ名からWikiを呼び出す
		$wiki = Factory::Wiki($page);
		
		if (!$wiki->isValied()){
			// 無効なページ名
			return self::RESPONSE_FAULT_TARGET_INVALID;
		}

		if (!$wiki->isReadable()){
			// 読み込み不可なページ
			return self::RESPONSE_FAULT_ACCESS_DENIED;
		}

		// PingBackファイルを読み込む
		$pb = new PingBackFile($page);
		$lines = $pb->get();
		
		if (count($lines) !== 0){
			foreach ($lines as $line){
				list($time, $url, $title) = explode("\t", $line);
				
				if ($url === $target_url){
					// すでに登録されている
					return self::RESPONSE_FAULT_ALREADY_REGISTERED;
				}
			}
		}
		// 新しいデーターを登録
		$lines[] = join("\t", array(UTIME, $source_url, $source_title));
		// 保存
		$pb->set($lines);
		
		return self::RESPONSE_SUCCESS;
	}
}