<?php
/**
 * Ping送信クラス
 *
 * @package   PukiWiki
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2014 PukiWiki Advance Developers Team
 * @create    2014/12/24
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Ping.php,v 1.0.0 2014/12/24 23:28:00 Logue Exp $
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
	 * weblogUpdates ping
	 */
	public $weblog_updates_ping_server = array(
		'http://rpc.weblogs.com/',
		'http://ping.feedburner.com/',
		'http://blogsearch.google.com/ping/RPC2'
	);
	/**
	 * PubSubHubbubの送信先
	 * https://code.google.com/p/pubsubhubbub/
	 */
	public $pubsubhubbub_server = array(
		'https://pubsubhubbub.appspot.com',
		'https://pubsubhubbub.superfeedr.com'
	);
	public function __construct($page){
		$this->wiki = Factory::Wiki($page);
		if (!$this->wiki->isReadable()) return;
	}
	/**
	 * Ping送信
	 */
	public function send(){
		global $use_pingback;

		$this->sendPubsubhubbub();
		$this->sendWeblogUpdatesPing();
		if ($use_pingback === true) {
			$this->sendPingBack();
		}
	}
	/**
	 * WeblogUpdatesサーバーを設定
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
	 * Pubsubhubbubサーバーを設定
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
	public function sendPubsubhubbub(){
		$publisher = new Publisher;
		$publisher->addHubUrls($this->pubsubhubbub_server);
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
		$subscriber = new Zend\Feed\Pubsubhubbub\Subscriber;
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
	public function sendWeblogUpdatesPing(){
		global $site_name;

		$request = new XmlRpcRequest();
		$request->setMethod('weblogUpdates.ping');
		$request->setParams(array($site_name, Router::get_script_absuri(), $this->wiki->uri()));

		// 送信
		foreach ($this->ping_server as $uri){
			try {
				// Pingサーバーに接続
				$client = new XmlRpcClient($uri);
				// Pingの送信
				$client->doRequest($request);
			} catch (Zend\XmlRpc\Client\Exception\FaultException $e) {
				$err[] = $e;
			}
		}
		return $err;
	}
	/**
	 * PingBackを送信
	 */
	public function sendPingBack(){
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
			$pingback = false;
			// ターゲットとなるURL接続
			$client = new Client($target_uri);
			// HEADメソッドでヘッダーのみ取得
			$client->setMethod(Request::METHOD_HEAD);
			// 返り値を取得
			
			try{
				$response = $client->send();

				// アクセス失敗
				if (!$response->isSuccess()) continue;

				// ヘッダーからPingBackのURIを取得
				$pingback = $response->getHeaders()->get('x-pingback');

				// x-pingbackヘッダーがない場合
				if ($pingback === false){
					// GETでアクセスしてコンテンツを取得し、linkタグを探す。
					$client->setMethod(Request::METHOD_GET);
					// 返り値を取得
					$response = $client->send();
					// linkタグからPingBackのURIを取得
					if (preg_match('<link rel="pingback" href="([^"]+)" ?/?>', $response->getBody(), $matches) !== false){
						$pingback = isset($matches[1]) ? $matches[1] : null;
					}
				}
			}catch(Exception $e){
				$err[] = $e;
			}
			// PingBack送信先が見つからない場合スキップ
			if ($pingback === false) continue;

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
			} catch (Zend\XmlRpc\Client\Exception\FaultException $e) {
				$err[] = $e;
			}
			$err[] = '-----'."\n";
			unset($client, $request);
		}
		return $err;
	}
}
