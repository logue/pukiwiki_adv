<?php
/**
 * 最終更新クラス
 *
 * @package   PukiWiki
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2013 PukiWiki Advance Developers Team
 * @create    2012/12/31
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Recent.php,v 1.0.0 2013/09/02 22:57:00 Logue Exp $
 **/

namespace PukiWiki;

use Exception;
use PukiWiki\Auth\Auth;
use PukiWiki\File\FileFactory;
use PukiWiki\Listing;
use PukiWiki\Renderer\Header;
use Zend\Feed\Writer\Feed;

/**
 * 最終更新クラス
 */
class Recent{
	/**
	 * 更新履歴のキャッシュ名
	 */
	const RECENT_CACHE_NAME = 'recent';
	/**
	 * フィードのキャッシュ名
	 */
	const FEED_CACHE_NAME = 'feed';
	/**
	 * 更新履歴／削除履歴で表示する最小ページ数
	 */
	const RECENT_MIN_SHOW_PAGES = 10;
	/**
	 * 更新履歴／削除履歴で表示する最大ページ数
	 */
	const RECENT_MAX_SHOW_PAGES = 60;
	/**
	 * フィードの説明文の長さ
	 */
	const FEED_ENTRY_DESCRIPTION_LENGTH = 256;
	/**
	 * PubSubHubbubの送信先
	 */
	private static $pubsubhub_uris = array(
		'http://pubsubhubbub.appspot.com',
		'http://pubsubhubbub.superfeedr.com'
	);
	/**
	 * 最終更新のキャッシュを取得
	 * @param boolean $force キャッシュを再生成する
	 * @return array
	 */
	public static function get($force = false){
		global $cache, $whatsnew;
		static $recent_pages;

		if ($force){
			// キャッシュ再生成
			unset($recent_pages);
			$cache['wiki']->removeItem(self::RECENT_CACHE_NAME);
		}else if (!empty($recent_pages)){
			// メモリにキャッシュがある場合
			return $recent_pages;
		}else if ($cache['wiki']->hasItem(self::RECENT_CACHE_NAME)) {
			// キャッシュから最終更新を読み込む
			$recent_pages = $cache['wiki']->getItem(self::RECENT_CACHE_NAME);
			return $recent_pages;
		}

		// Wikiのページ一覧を取得
		$pages = Listing::pages('wiki');

		// ページ一覧からファイルの更新日時を取得
		$recent_pages = array();
		foreach($pages as $page){
			if ($page !== $whatsnew){
				$wiki = Factory::Wiki($page);
				 if (! $wiki->isHidden() ) $recent_pages[$page] = $wiki->time();
			}
		}
		// 更新日時順にソート
		arsort($recent_pages, SORT_NUMERIC);

		$_recent = array();
		foreach($recent_pages as $key=>$value) {
			unset($recent_pages[$key]);
			$_recent[$key] = $value;
		}
		$recent_pages = & $_recent;

		// Save to recent cache data
		$cache['wiki']->setItem(self::RECENT_CACHE_NAME, $recent_pages);

		return $recent_pages;
	}
	/**
	 * 最終更新のキャッシュを更新
	 * @param string $page ページ名
	 * @param boolean $is_deleted 削除フラグ
	 * @return void
	 */
	public static function set($page, $is_deleted = false){
		global $whatsnew,$cache;

		// ページが最終更新だった場合処理しない
		if (empty($page) || $page === $whatsnew) return;

		// 削除フラグが立っている場合、削除履歴を付ける
		
		if ($is_deleted){
			self::updateRecentDeleted($page);
		}

		// 更新キャッシュを読み込み（キャッシュ再生成する）
		$recent_pages = self::get(true);
/*
		// キャッシュ内のページ情報を削除
		if (isset($recent_pages[$page])) unset($recent_pages[$page]);

		// トップにページ情報を追記
		if ( $page !== $whatsnew)
			$recent_pages = array($page => $wiki->time()) + $recent_pages;
*/
		// 最終更新ページを更新
		self::updateRecentChanges($recent_pages);
	}
	/**
	 * 最終更新ページを更新（そもそもわざわざWikiページを作成する必要あるのだろうか・・・？）
	 * @global string $whatsnew
	 * @param array $recent_pages
	 * @return void
	 */
	private static function updateRecentChanges($recent_pages){
		global $whatsnew;
		// 最終更新ページを作り直す
		// （削除履歴みたく正規表現で該当箇所を書き換えるよりも、ページを作りなおしてしまったほうが速いだろう・・・）
		$buffer[] = '#norelated';
		
		// Cut unused lines
		// BugTrack2/179: array_splice() will break integer keys in hashtable
		$count   = self::RECENT_MAX_SHOW_PAGES + self::RECENT_MIN_SHOW_PAGES;

		foreach ($recent_pages as $_page=>$time){
			// RecentChanges のwikiソース生成部分の問題
			// http://pukiwiki.sourceforge.jp/dev/?BugTrack2%2F343#f62964e7 
			$buffer[] = '- &epoch('.$time.');' . ' - ' . '[[' . str_replace('&#39;', '\'', Utility::htmlsc($_page)) . ']]';
			if (--$count < 1) break;
		}
		FileFactory::Wiki($whatsnew)->set($buffer);
	}
	/**
	 * 削除履歴を生成
	 * @global string $whatsdeleted
	 * @param string $deleted 削除したページ
	 * @return type
	 */
	public static function updateRecentDeleted($page){
		global $whatsdeleted;
		// 隠されたページの場合削除履歴を付けない
		if (Factory::Wiki($page)->isHidden()){
			return;
		}

		// 新たに削除されるページ名
		$_page = '[[' .  str_replace('&#39;', '\'', Utility::htmlsc($page)) . ']]';

		$delated_wiki = FileFactory::Wiki($whatsdeleted);
		
		$lines = array();

		// 削除履歴を確認する
		foreach ($delated_wiki->get() as $line) {
			if (preg_match('/^-(.+) - (\[\[.+\]\])$/', $line, $matches)) {
				$lines[$matches[2]] = $line;
			}
		}

		// 削除されるページ名と同じページが存在した時にそこの行を削除する
		if (isset($lines[$_page])){
			unset($lines[$_page]);
		}

		// 削除履歴に追記
		array_unshift($lines, '- &epoch(' . UTIME . '); - ' . $_page);
		array_unshift($lines, '#norelated');

		// 履歴の最大記録数を制限
		$lines = array_splice($lines, 0, self::RECENT_MAX_SHOW_PAGES);
		
		// 削除履歴を付ける（この時に最終更新のキャッシュも自動更新される）
		$delated_wiki->set(array_values($lines));
	}
	/**
	 * Atom/rssを出力
	 * string $page ページ名（ページ名が入っている場合はキャッシュは無効）
	 * string $type rssかatomか。
	 * boolean $force キャッシュ生成しない
	 * return void
	 */
	public static function getFeed($page = '', $type='rss', $force = true){
		global $vars, $site_name, $site_logo, $modifier, $modifierlink, $_string, $cache;
		static $feed;
		
		// rss, atom以外はエラー
		if (!($type === 'rss' || $type === 'atom')){
			throw new Exception('Recent::getFeed(): Unknown feed type.');
		}

		$content_type = ($type === 'rss') ? 'application/rss+xml' : 'application/atom+xml';
		$body = '';

		if (empty($page)){
			// recentキャッシュの更新チェック
			if ($cache['wiki']->getMetadata(self::RECENT_CACHE_NAME)['mtime'] > $cache['wiki']->getMetadata(self::FEED_CACHE_NAME)['mtime']){
				$force = true;
			}

			if ($force){
				// キャッシュ再生成
				unset($feed);
				$cache['wiki']->removeItem(self::FEED_CACHE_NAME);
			}else if (!empty($feed)){
				// メモリにキャッシュがある場合
				$body = $feed->export($type);
			}else if ($cache['wiki']->hasItem(self::FEED_CACHE_NAME)) {
				// キャッシュから最終更新を読み込む
				$feed = $cache['wiki']->getItem(self::FEED_CACHE_NAME);
				$body = $feed->export($type);
			}
		}

		if (empty($body)){
			// Feedを作る
			$feed = new Feed();
			// Wiki名
			$feed->setTitle($site_name);
			// Wikiのアドレス
			$feed->setLink(Router::get_script_absuri());
			// サイトのロゴ
			//$feed->setImage(array(
			//	'title'=>$site_name,
			//	'uri'=>$site_logo,
			//	'link'=>Router::get_script_absuri()
			//));
			// Feedの解説
			$feed->setDescription(sprintf($_string['feed_description'], $site_name));
			// Feedの発行者など
			$feed->addAuthor(array(
				'name'  => $modifier,
				'uri'   => $modifierlink,
			));
			// feedの更新日時（生成された時間なので、この実装で問題ない）
			$feed->setDateModified(time());
			$feed->setDateCreated(time());
			// Feedの生成
			$feed->setGenerator(S_APPNAME, S_VERSION, 'http://pukiwiki.logue.be/');

			if (empty($page)){
				// feedのアドレス
				// ※Zend\Feedの仕様上、&が自動的に&amp;に変更されてしまう
				$feed->setFeedLink(Router::get_cmd_uri('feed').'&type=atom', 'atom');
				$feed->setFeedLink(Router::get_cmd_uri('feed'), 'rss');
				// PubSubHubbubの送信
				foreach (self::$pubsubhub_uris as $uri){
					$feed->addHub($uri);
				}
			}else{
				$r_page = rawurlencode($page);
				$feed->setFeedLink(Router::get_cmd_uri('feed').'&type=atom&refer='.$r_page, 'atom');
				$feed->setFeedLink(Router::get_cmd_uri('feed').'&refer='.$r_page, 'rss');
			}

			$i = 0;
			// エントリを取得
			foreach(self::get() as $_page=>$time){
				// ページ名が指定されていた場合、そのページより下位の更新履歴のみ出力
				if (!empty($page) && strpos($_page, $page.'/') === false) continue;

				$wiki = Factory::Wiki($_page);
				if ($wiki->isHidden()) continue;

				$entry = $feed->createEntry();
				// ページのタイトル
				$entry->setTitle($wiki->title());
				// ページのアドレス
				$entry->setLink($wiki->uri());
				// ページの更新日時
				$entry->setDateModified($wiki->time());
				// ページの要約
				$entry->setDescription($wiki->description(self::FEED_ENTRY_DESCRIPTION_LENGTH));
				
				// 項目を追加
				$feed->addEntry($entry);

				$i++;
				if ($i >= self::RECENT_MAX_SHOW_PAGES) break;
			}

			if (empty($page)){
				// キャッシュに保存
				$cache['wiki']->setItem(self::FEED_CACHE_NAME, $feed);
			}

			$body = $feed->export($type);
		}

		//$headers = Header::getHeaders($content_type);
		//Header::writeResponse($headers, 200, $body);
		flush();
		header('Content-Type: ' . $content_type);
		echo $body;
		exit;
	}
}