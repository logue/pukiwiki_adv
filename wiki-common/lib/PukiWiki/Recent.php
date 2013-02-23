<?php
namespace PukiWiki;

use PukiWiki\File\FileUtility;
use PukiWiki\Factory;

class Recent{
	// 更新履歴のキャッシュ名
	const RECENT_CACHE_NAME = 'recent';
	// 更新履歴／削除履歴で表示する最小ページ数
	const RECENT_MIN_SHOW_PAGES = 10;
	// 更新履歴／削除履歴で表示する最大ページ数
	const RECENT_MAX_SHOW_PAGES = 60;
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

		// Get WHOLE page list
		$pages = FileUtility::getExists(DATA_DIR, $force);

		// Check ALL filetime
		$recent_pages = array();
		foreach($pages as $filename=>$page){
			if ($page !== $whatsnew){
				$wiki = Factory::Wiki($page);
				 if (! $wiki->isHidden() ) $recent_pages[$page] = $wiki->time();
			}
		}
		// Sort decending order of last-modification date
		arsort($recent_pages, SORT_NUMERIC);

		// Cut unused lines
		// BugTrack2/179: array_splice() will break integer keys in hashtable
		$count   = self::RECENT_MAX_SHOW_PAGES + self::RECENT_MIN_SHOW_PAGES;
		$_recent = array();
		foreach($recent_pages as $key=>$value) {
			unset($recent_pages[$key]);
			$_recent[$key] = $value;
			if (--$count < 1) break;
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
		global $whatsnew, $autolink, $autobasealias;
		global $cache;
		
		// 削除フラグが立っている場合、削除履歴を付ける
		if ($is_deleted) self::updateRecentDeleted($page);

		// 自動リンクと自動エイリアス名を使用している場合、常時更新
		if ($autolink || $autobasealias) {
			self::getRecent(true);	// Try to (re)create ALL
		}

		$wiki = Factory::Wiki($page);
		// 更新履歴に付けないページか？
		$is_hidden = $wiki->isHidden();

		if (empty($page) || $is_hidden) return; // No need

		// Check cache exists
		if (! $cache['wiki']->hasItem(self::RECENT_CACHE_NAME)){
			self::getRecent(true);	// Try to (re)create ALL
			return;
		}else{
			$recent_pages = $cache['wiki']->getItem(self::RECENT_CACHE_NAME);
		}

		// Remove if it exists inside
		if (isset($recent_pages[$page])) unset($recent_pages[$page]);

		// Update Cache
		$cache['wiki']->setItem(self::RECENT_CACHE_NAME, $recent_pages);

		// Add to the top: like array_unshift()
		if ( $page !== $whatsnew && ! $is_hidden)
			$recent_pages = array($page => $wiki->time()) + $recent_pages;

		// Check
		$abort = count($recent_pages) < self::RECENT_MAX_SHOW_PAGES;

		// Update cache
		$cache['wiki']->setItem(self::RECENT_CACHE_NAME, $recent_pages);

		if ($abort) {
			self::getRecent(true);	// Try to (re)create ALL
			return;
		}

		self::updateRecentChanges($recent_pages);
		
	}
	/**
	 * 最終更新ページを更新
	 * @global string $whatsnew
	 * @param array $recent_pages
	 * @return void
	 */
	private static function updateRecentChanges($recent_pages){
		global $whatsnew;

		if (!self::isHidden()) return;

		// 最終更新ページを作り直す
		// （削除履歴みたく正規表現で該当箇所を書き換えるよりも、ページを作りなおしてしまったほうが速いだろう・・・）
		$buffer[] = '#norelated';
		foreach ($recent_pages as $_page=>$time){
			// RecentChanges のwikiソース生成部分の問題
			// http://pukiwiki.sourceforge.jp/dev/?BugTrack2%2F343#f62964e7 
			$buffer[] = '- &epoch('.$time.');' . ' - ' . '[[' . str_replace('&#39;', '\'', Utility::htmlsc($_page)) . ']]';
		}
		Factory::Wiki($whatsnew)->set($buffer);
	}
	/**
	 * 削除履歴を生成
	 * @global string $whatsdeleted
	 * @param string $deleted 削除したページ
	 * @return type
	 */
	private static function updateRecentDeleted($deleted){
		global $whatsdeleted;
		if (auth::check_role('readonly') || !self::isHidden($this->page)) return;

		$delated = Factory::Wiki($whatsdeleted);

		// 削除履歴を確認する
		foreach ($delated->get() as $line) {
			if (preg_match('/^-(.+) - (\[\[.+\]\])$/', $line, $matches)) {
				$lines[$matches[2]] = $line;
			}
		}

		// 新たに削除されるページ名
		$_page = '[[' .  str_replace('&#39;', '\'', Utility::htmlsc($deleted)) . ']]';

		// 削除されるページ名と同じページが存在した時にそこの行を削除する
		if (isset($lines[$_page])) unset($lines[$_page]);

		// 削除履歴に追記
		array_unshift($lines, '-&epoch(' . UTIME . '); - ' . $_page);
		array_unshift($lines, '#norelated');

		// 履歴の最大記録数を制限
		$lines = array_splice($lines, 0, self::RECENT_MAX_SHOW_PAGES);
		// ファイル一覧キャッシュを再生成
		self::getExists(DATA_DIR, true);
		// 削除履歴を付ける
		$delated->set($lines);
	}
}