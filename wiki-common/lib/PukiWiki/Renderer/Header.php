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
		global $lastmod, $vars;
		self::checkSent();

		$headers['Content-Type'] = $content_type . ';charset=' . CONTENT_CHARSET;

		// 更新日時をチェック
		if ($modified !== 0){
			$last_modified = gmdate('D, d M Y H:i:s', $modified);
			$etag = md5($last_modified);
			$headers['Cache-Control'] = 'private';
			$headers['Expires'] = gmdate('D, d M Y H:i:s',time() + $expire) . ' GMT';
			$headers['Last-Modified'] = $last_modified;
			$headers['ETag'] = $etag;
			if ( (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $_SERVER['HTTP_IF_MODIFIED_SINCE'] == $last_modified) ||
				(isset($_SERVER['HTTP_IF_NONE_MATCH']) && preg_match("/{$etag}/", $_SERVER['HTTP_IF_NONE_MATCH'])) ){
				self::notModified($headers);
			}
	//		header('If-Modified-Since: ' . $last_modified );
		}else{
			// PHPで動的に生成されるページはキャシュすべきではない
			$headers['Cache-Control'] = $headers['Pragma'] = 'no-cache';
			$headers['Expires'] = 'Sat, 26 Jul 1997 05:00:00 GMT';
		}

		// RFC2616
		// http://sonic64.com/2004-02-06.html
		$vary = self::getLanguageHeaderVary();
		if (preg_match('/\b(gzip|deflate|compress)\b/i', $_SERVER['HTTP_ACCEPT_ENCODING'], $matches)) {
			$vary .= ',Accept-Encoding';
		}
		$headers['Vary'] = $vary;

		// HTTP access control
		// JSON脆弱性対策（Adv.では外部にAjax APIを提供することを考慮しない）
		// https://developer.mozilla.org/ja/HTTP_Access_Control
		$headers['Access-Control-Allow-Origin'] = Router::get_script_uri();

		// Content Security Policy
		// https://developer.mozilla.org/ja/Security/CSP/Using_Content_Security_Policy
		// header('X-Content-Security-Policy: allow "self" "inline-script";  img-src *; media-src *;');
		// IEの自動MIME type判別機能を無効化する
		// http://msdn.microsoft.com/ja-jp/ie/dd218497.aspx
		$headers['X-Content-Type-Options'] = 'nosniff';

		// クリックジャッキング対策
		// https://developer.mozilla.org/ja/The_X-FRAME-OPTIONS_response_header
		$headers['X-Frame-Options'] = 'SameDomain';

		// XSS脆弱性対策（これでいいのか？）
		// http://msdn.microsoft.com/ja-jp/ie/dd218482
		$headers['X-XSS-Protection'] = DEBUG ? '0' :'1;mode=block';

		return $headers;
	}
	/**
	 * ヘッダーをまとめて出力（非推奨）
	 * @param array $headers
	 * @return void
	 */
	public static function writeResponse($headers){
		$response = new Response();
		$response->setStatusCode(Response::STATUS_CODE_200);
		$response->getHeaders()->addHeaders($headers);

		header($response->renderStatusLine());
		foreach ($response->getHeaders() as $_header) {
			header($_header->toString());
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
	/**
	 * 304 Not modifiedを出力
	 * @param array $headers ヘッダー配列
	 */
	private static function notModified($headers){
		$html = array();
		$html[] = '<!doctype html>';
		$html[] = '<html>';
		$html[] = '<head>';
		$html[] = '<meta charset="utf-8">';
		$html[] = '<meta name="robots" content="NOINDEX,NOFOLLOW" />';
		$html[] = '<link rel="stylesheet" href="http://code.jquery.com/ui/' . JQUERY_UI_VER . '/themes/base/jquery-ui.css" type="text/css" />';
		$html[] = '<title>304 Not Modified</title>';
		$html[] = '</head>';
		$html[] = '<body>';
		$html[] = '<div class="message_box ui-state-highlight ui-corner-all">';
		$html[] = '<p style="padding:0 .5em;">';
		$html[] = '<span class="ui-icon ui-icon-info" style="display:inline-block;"></span>';
		$html[] = 'The requested page has not modifieded. </p>';
		$html[] = '</div>';
		$html[] = '</body>';
		$html[] = '</html>';

		$response = new Response();
		$response->setContent(join("\n",$html));
		$response->setStatusCode(Response::STATUS_CODE_304);
		$response->renderStatusLine();

		header($response->renderStatusLine());
		foreach ($response->getHeaders() as $header) {
			header($header->toString());
		}
		echo $response->getBody();
		exit;
	}
}