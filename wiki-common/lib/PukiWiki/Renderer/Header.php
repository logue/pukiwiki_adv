<?php
namespace PukiWiki\Renderer;

use PukiWiki\Utility;
use PukiWiki\Router;
use Zend\Http\Response;

class Header{
	const DEFAULT_CONTENT_TYPE = 'text/html';
	
	private static $vary = array(
		'Cookie',
		'Accept-Language',
		'User-Agent',
		'Accept-Charset'
	);
	/**
	 * Check HTTP header()s were sent already, or
	 * there're blank lines or something out of php blocks '
	 */
	public static function checkSent()
	{
		global $_string;
		if (defined('PKWK_OPTIMISE')) return;

		$file = $line = '';

		if (headers_sent($file, $line)){
			Utility::dieMessage(sprintf($_string['header_sent'],Utility::htmlsc($file),$line));
		}
	}
	/**
	 * ヘッダー配列を取得
	 * @param string $content_type コンテントタイプ
	 * @param int $modified 更新日時。通常はfilemtimeの値
	 * @param int $exprire 有効期限。デフォルトは１週間
	 * @return array
	 */
	public static function getHeaders($content_type = DEFAULT_CONTENT_TYPE, $modified = 0, $expire = 604800){
		global $lastmod, $vars, $_SERVER;
		self::checkSent();
		// これまでのヘッダーを取得
		$headers = getallheaders();

		$headers['Content-Type'] = $content_type . ';charset=' . CONTENT_CHARSET;
		$headers['Content-Language'] = substr(str_replace('_','-',LANG),0,2);

		// 更新日時をチェック
		if ($modified !== 0){
			// http://firegoby.jp/archives/1730
			$last_modified = gmdate('D, d M Y H:i:s', $modified);
			$etag = md5($last_modified);
			$headers['Cache-Control'] = 'private';
			$headers['Pragma'] = 'cache';
			$headers['Expires'] = gmdate('D, d M Y H:i:s', time() + $expire) . ' GMT';
			$headers['Last-Modified'] = $last_modified;
			$headers['ETag'] = $etag;
			
			if ( (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $_SERVER['HTTP_IF_MODIFIED_SINCE'] == $last_modified) ||
				(isset($_SERVER['HTTP_IF_NONE_MATCH']) && preg_match('/'.$etag.'/', $_SERVER['HTTP_IF_NONE_MATCH'])) ){
				self::WriteResponse($headers, Response::STATUS_CODE_304, '');
			}
	//		header('If-Modified-Since: ' . $last_modified );
		}else{
			// PHPで動的に生成されるページはキャシュすべきではない
			$headers['Cache-Control'] = $headers['Pragma'] = 'no-cache';
			$headers['Expires'] = 'Sat, 26 Jul 1997 05:00:00 GMT';
		}

		// RFC2616
		// http://sonic64.com/2004-02-06.html
		$headers['Vary'] = self::getLanguageHeaderVary();
		if (preg_match('/\b(gzip|deflate|compress)\b/i', $_SERVER['HTTP_ACCEPT_ENCODING'], $matches)) {
			$headers['Vary'] .= ',Accept-Encoding';
		}

		// HTTP access control
		// JSON脆弱性対策（Adv.では外部にAjax APIを提供することを考慮しない）
		// https://developer.mozilla.org/ja/HTTP_Access_Control
		$headers['Access-Control-Allow-Origin'] = Router::get_script_uri();

		// Content Security Policy
		// https://developer.mozilla.org/ja/Security/CSP/Using_Content_Security_Policy
		// 現在の実装だとあまり意味は無いが・・・。
		$headers['X-Content-Security-Policy'] ='allow "self" "inline-script"; img-src *; media-src *; style-src *;srcipt-src *;';

		// IEの自動MIME type判別機能を無効化する
		// http://msdn.microsoft.com/ja-jp/ie/dd218497.aspx
		$headers['X-Content-Type-Options'] = 'nosniff';

		// クリックジャッキング対策（IFRAME呼び出しは禁止！）
		// https://developer.mozilla.org/ja/The_X-FRAME-OPTIONS_response_header
		$headers['X-Frame-Options'] = 'deny';

		// XSS脆弱性対策（これでいいのか？）
		// http://msdn.microsoft.com/ja-jp/ie/dd218482
		$headers['X-XSS-Protection'] = '1;mode=block';

		return $headers;
	}
	/**
	 * 出力
	 * @param array $headers ヘッダー（別途Header::getHeaders()で指定すること）
	 * @param int $status ステータスコード
	 * @param string $body 内容
	 * @return void
	 */
	public static function writeResponse($headers, $status = Response::STATUS_CODE_200, $body = ''){
		// レスポンスをコンストラクト
		$response = new Response();
		if (!empty($body)){
			if (isset($headers['If-None-Match']) && !isset($headers['ETag']) ){
				// Modifiedヘッダーが出力されてない場合、出力内容からETagを生成
				// 負荷対策にはならないが転送量を抑えることができる
				$hash = md5($body);
				if (preg_match('/'.$hash.'/', $headers['If-None-Match'])){
					$status = Response::STATUS_CODE_304;
				}
				$headers['Etag'] = $hash;
			}
			// 内容が存在する場合容量をContent-Lengthヘッダーに出力
			$headers['Content-Length'] = strlen($body);
			// レスポンスに内容を追加
			$response->setContent($body);
		}
		// ステータスコードを出力
		$response->setStatusCode($status);
		// ヘッダーをソート
		ksort($headers);
		// ヘッダーを指定
		$response->getHeaders()->addHeaders($headers);
		// ステータスコードを出力
		header($response->renderStatusLine());
		// ヘッダーを出力
		foreach ($response->getHeaders() as $_header) {
			header($_header->toString());
		}
		if (!empty($body)){
			//ob_start('ob_gzhandler');
			// 内容を出力
			echo $response->getBody();
			// 出力バッファをフラッシュ
			flush();
			// 終了
			exit;
		}
	}
	/*
	 * get_language_header_vary
	 *
	 */
	public static function getLanguageHeaderVary()
	{
		global $language_considering_setting_level;

		if ($language_considering_setting_level < 1) return '';

		$rc = 'Negotiate';

		for($i=1;$i<=$language_considering_setting_level;$i++) {
			if (empty(self::$vary[$i])) break;
			if ($rc != '') {
				$rc .= ',';
			}
			$rc .= self::$vary[$i];
		}
		return $rc;
	}
}