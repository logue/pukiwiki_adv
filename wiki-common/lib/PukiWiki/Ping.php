<?php
/**
 * Ping送信クラス
 *
 * @package   PukiWiki
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2014-2015 PukiWiki Advance Developers Team
 * @create    2014/12/24
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Ping.php,v 1.0.2 2015/01/06 21:11:00 Logue Exp $
 **/
namespace PukiWiki;

use PukiWiki\Factory;
use Zend\Feed\PubSubHubbub\Publisher;
use Zend\Feed\Pubsubhubbub\Subscriber;
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\XmlRpc\Client as XmlRpcClient;
use Zend\XmlRpc\Request as XmlRpcRequest;

/**
 * Pingクラス
 */
class Ping{
	/**
	 * Ping送信の最小インターバル（1時間）
	 */
	const PING_INTERVAL = 360;
	/**
	 * weblogUpdates ping
	 */
	protected $weblog_updates_ping_server = array(
		'http://rpc.weblogs.com/',
		'http://ping.feedburner.com/',
		'http://blogsearch.google.com/ping/RPC2'
	);
	/**
	 * PubSubHubbubの送信先
	 * https://code.google.com/p/pubsubhubbub/
	 */
	public static $pubsubhubbub_server = array(
		'https://pubsubhubbub.appspot.com',
		'https://pubsubhubbub.superfeedr.com'
	);
	/**
	 * Wikiオブジェクト
	 */
	private $wiki;
	/**
	 * コンストラクタ
	 * @param $page ページ名
	 */
	public function __construct($page){
		$this->wiki = Factory::Wiki($page);
	}
	/**
	 * Ping送信
	 */
	public function send(){
		global $use_pingback;

		// 読み取り不可のページの場合処理しない
		if (!$this->wiki->isReadable()){
			return;
		}

		// 連続更新時に何度もPingを送信しないようにする
		if (! UTIME - $this->wiki->time() > self::PING_INTERVAL){
			return;
		}

		// Pubsubhubbub送信
		$this->sendPubsubhubbub();

		// WeblogUpdatesPingの送信
		$this->sendWeblogUpdatesPing();

		// PingBackを送信
		if (isset($use_pingback) && $use_pingback === true) {
			$this->sendPingBack();
		}
	}
	/**
	 * WeblogUpdatesサーバーを設定（※仕様が決まっていない）
	 * @param array or string $host ホスト名
	 */
	public function setWeblogUpdatesServer($host){
		if (is_array($host)){
			$this->weblog_updates_ping_server = $host;
		}else{
			$this->weblog_updates_ping_server[] = $host;
		}
	}
	/**
	 * WeblogUpdatesサーバーを取得
	 * @return array
	 */
	public function getWeblogUpdatesServer(){
		return $this->weblog_updates_ping_server;
	}
	/**
	 * Pubsubhubbubサーバーを設定（※仕様が決まっていない）
	 * @param array or string $host ホスト名
	 */
	public function setPubsubhubbubServer($host){
		if (is_array($host)){
			$this->pubsubhubbub_server = $host;
		}else{
			$this->pubsubhubbub_server[] = $host;
		}
	}
	/**
	 * Pubsubhubbubサーバーを取得
	 * @return array
	 */
	public function getPubsubhubbubServer(){
		return $this->pubsubhubbub_server;
	}
	/**
	 * Pubsubhubbub送信
	 */
	protected function sendPubsubhubbub(){
		// Pubsubhubbub発行オブジェクトを生成
		$publisher = new Publisher();
		// Pubsubhubbubを送るサーバーをセット
		$publisher->addHubUrls($this->pubsubhubbub_server);
		// ページをセット
		$publisher->addUpdatedTopicUrls(array(
			$this->wiki->uri()
		));
		$publisher->notifyAll();

		if (!$publisher->isSuccess()) {
			// check for errors
			$errors     = $publisher->getErrors();
			$failedHubs = array();
			foreach ($errors as $error) {
				$failedHubs[] = $error['hubUrl'];
			}
			return $failedHubs;
		}
		return null;
	}
/*
	public function recievePubsubhubbub(){
		$subscriber = new Subscriber;
		$subscriber->addHubUrl();
		$subscriber->setTopicUrl(Router::get_resolve_uri('feed',null,null,array('type'=>'rss'));
		// 未実装
		// $subscriber->setCallbackUrl();
		$subscriber->subscribeAll();
	}
*/
	/**
	 * WeblogUpdatesPingの送信
	 */
	protected function sendWeblogUpdatesPing(){
		global $site_name;
		$err = array();

		// XMLRpcリクエストオブジェクトを生成
		$request = new XmlRpcRequest();
		// weblogUpdates.pingをセット
		$request->setMethod('weblogUpdates.ping');
		// 送るパラメータ
		$request->setParams(array($site_name, Router::get_script_absuri(), $this->wiki->uri()));

		// 送信
		foreach ($this->weblog_updates_ping_server as $uri){
			try {
				// Pingサーバーに接続
				$client = new XmlRpcClient($uri);
				// Pingの送信
				$client->doRequest($request);
			} catch (\Zend\XmlRpc\Client\Exception\FaultException $e) {
				$err[] = $e;
			}
			unset($client);
		}
		return $err;
	}
	/**
	 * PingBackを送信
	 */
	protected function sendPingBack(){
		$err = array();
		$links = array();
		// Wikiのソースのアドレスを取得
		if (preg_match_all('/(https?://[a-zA-Z0-9./~_]+)/', $wiki->get(), $links, PREG_PATTERN_ORDER) === false){
			// ない場合そのままリターン
			return;
		}
		// 重複を削除
		$target_uris = array_unique($links[0]);
		foreach ($target_uris as $target_uri){
			// 初期値
			$pingback = false;

			// ターゲットとなるURL接続
			$client = new Client($target_uri);
			// HEADメソッドで接続し、ヘッダーのみ取得
			$client->setMethod(Request::METHOD_HEAD);

			// 返り値を取得
			$response = $client->send();
			// アクセス失敗
			if (!$response->isSuccess()){
				continue;
			}

			// 返り値のヘッダーからPingBackのURIを取得
			$pingback = $response->getHeaders()->get('x-pingback');

			// x-pingbackヘッダーがない場合（このへんの処理は重そう）
			if ($pingback === false){
				try{
					// GETでアクセスしてコンテンツを取得し、linkタグを探す。
					$client->setMethod(Request::METHOD_GET);
					// 返り値を取得
					$response = $client->send();
					// linkタグからPingBackのURIを取得
					if (preg_match('<link rel="pingback" href="([^"]+)" ?/?>', $response->getBody(), $matches) !== false){
						$pingback = isset($matches[1]) ? $matches[1] : null;
					}
				}catch(Exception $e){
					$err[] = $e;
				}
			}
			// PingBack送信先が見つからない場合スキップ
			if ($pingback === false){
				continue;
			}
			unset($client, $response);

			// PingBackで送信する内容
			$request = new XmlRpcRequest();
			$request->setMethod('pingback.ping');
			$request->setParams(array($source_uri, $target_uri));

			// 例外を取得
			try {
				// PingBack送信先に接続
				$client = new XmlRpcClient($pingback);
				// 送信
				$client->doRequest($request);
			} catch (\Zend\XmlRpc\Client\Exception\FaultException $e) {
				$err[] = $e;
			}
			$err[] = '-----'."\n";
			unset($client, $request);
		}
		return $err;
	}
}
