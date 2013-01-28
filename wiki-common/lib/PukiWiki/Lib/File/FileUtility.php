<?php
namespace PukiWiki\Lib\File;

use PukiWiki\Lib\Utility;
use PukiWiki\Lib\Text\Reading;
use PukiWiki\Lib\File\FileFactory;
use PukiWiki\Lib\Rooter;
use PukiWiki\Lib\Auth\Auth;

class FileUtility{
	// ファイルの存在一覧キャッシュ
	const EXSISTS_CACHE_PREFIX = 'exsists-';
	// RecentChanges
	const RECENT_CACHE_NAME = 'recent';
	const MAXSHOW_ALLOWANCE = 10;
	// ページ一覧キャッシュ
	const PAGENAME_HEADING_CACHE_PREFIX = 'listing-';

	/**
	 * ディレクトリ内のファイルの一覧を作成
	 * @param string $dir ディレクトリ
	 * @param boolean $force キャッシュを再生成
	 * @return array
	 */
	public static function get_exists_all($dir = DATA_DIR, $force = false){
		global $cache;
		static $aryret;

		switch($dir){
			case DATA_DIR:
				$func = 'wiki';
				$pattern = '/^((?:[0-9A-F]{2})+)\.txt$/';
				break;
			case COUNTER_DIR:
				$func = 'counter';
				$pattern = '/^((?:[0-9A-F]{2})+)\.count$/';
				break;
			case BACKUP_DIR:
				$func = 'backup';
				$pattern = '/^((?:[0-9A-F]{2})+)\.(txt|gz|bz2|lzf)$/';
				break;
			case UPLOAD_DIR:
				$func = 'attach';
				$pattern = '/^((?:[0-9A-F]{2})+)_((?:[0-9A-F]{2})+)$/';
				break;
			default:
				$func = encode($dir.$ext);
		}

		$func = self::get_cache_name($dir);
		$cache_name = self::EXSISTS_CACHE_PREFIX . $func;

		if ($force){
			// キャッシュを削除
			unset($aryret[$func]);
			$cache['wiki']->removeItem($cache_name);
		}

		if (!isset($aryret[$func])){
			if (! $cache['wiki']->hasItem($cache_name)){
				// キャッシュを再生成
				foreach (new \DirectoryIterator($dir) as $fileinfo) {
					$filename = $fileinfo->getFilename();
					if ($fileinfo->isFile() && preg_match($pattern, $filename, $matches)){
						$aryret[$func][$filename] = decode($matches[1]);
					}
				}
				$cache['wiki']->setItem($cache_name, $aryret[$func]);
			}else{
				// キャッシュからメモリに呼び出す
				$aryret[$func] = $cache['wiki']->getItem($cache_name);
			}
		}
		return $aryret[$func];
	}
	/**
	 * ページ一覧を取得（認証状態によって変わる）
	 * @param string $dir ディレクトリ
	 * @param boolean $force キャッシュを再生成
	 * @return array
	 */
	public static function get_exists($dir = DATA_DIR, $force = false){
		// 全ページ一覧
		$pages = self::get_exists_all($dir, $force);
		// ユーザ名取得
		$auth_key = Auth::get_user_info();
		// コンテンツ管理者以上は、: のページも閲覧可能
		$is_colon = Auth::check_role('role_adm_contents');
		if (is_array($pages)){
			foreach($pages as $file=>$page) {
				if (! Auth::is_page_readable($page, $auth_key['key'], $auth_key['group'])) continue;
				if (substr($page,0,1) != ':') {
					$rc[$file] = $page;
					continue;
				}

				// colon page
				if ($is_colon) continue;
				$rc[$file] = $page;
			}
		}
		return $rc;
	}
	/**
	 * キャッシュの識別子およびファイル
	 * @param string $dir ディレクトリ
	 * @return string
	 */
	private static function get_cache_name($dir = DATA_DIR){
		switch($dir){
			case DATA_DIR:
				$func = 'wiki';
				break;
			case COUNTER_DIR:
				$func = 'counter';
				break;
			case BACKUP_DIR:
				$func = 'backup';
				break;
			case UPLOAD_DIR:
				$func = 'attach';
				break;
			default:
				$func = encode($dir.$ext);
		}
		return $func;
	}
	/**
	 * ファイル一覧画面を生成（プラグインでやるべきかもしれないが、この処理はバックアップ一覧などページ名一覧以外の用途でも使うためこのクラスで定義）
	 * @param string $dir ディレクトリ
	 * @return string
	 */
	public static function get_listing($dir = DATA_DIR, $cmd = 'read', $with_filename = false){
		// 一覧の配列を取得
		$heading = self::get_headings($dir);

		if (IS_MOBILE) {
			// モバイル用
			$contents[] = '<ul data-role="listview">';
			foreach ($heading as $initial=>$pages) {
				$page_lists = self::get_page_lists($pages, $cmd);
				$count = count($page_lists);
				if ($count < 1) continue;

				if ($initial == Reading::OTHER_CHAR){
					$initial = $_string['other'];
				}else if ($initial == Reading::SYMBOL_CHAR){
					$initial = $_string['symbol'];
				}
				$contents[] = '<li data-role="list-divider">' . Utility::htmlsc($initial) . '<span class="ui-li-count">'. $count . '</span></li>';
				$contents[] = join("\n",$page_lists);
			}
			$contents[] = '</ul>';
			return join("\n", $contents);
		}
		// 通常用
		$header[] = '<div class="page_initial"><ul>';
		foreach ($heading as $initial=>$pages) {
			$page_lists = self::get_page_lists($pages, $cmd, $with_filename);
			$count = count($page_lists);
			if ($count < 1) continue;

			$_initial = Utility::htmlsc($initial);

			$header[] = '<li id="top_' . $_initial .'"><a href="#head_' . $_initial . '" title="'. $count .'">' . $_initial . '</a></li>';

			$contents[] = '<fieldset id="head_' . $_initial .'">';
			$contents[] = '<legend><a href="#top_' . $_initial . '">' . $_initial . '</a> <small>('.$count.')</small></legend>';
			$contents[] = '<ul>'.join("\n",$page_lists).'</ul>';
			$contents[] = '</fieldset>';
		}
		$header[] = '</ul>';
		$header[] = '</div>';
		return join("\n", $header) . '<div class="list_pages">' . join("\n", $contents) . '</div>';
	}
	/**
	 * 一覧をページの読みでソートし出力
	 * @param string $dir ディレクトリ
	 * @param boolean $force キャッシュを再生成する（※ページの経過時間はキャッシュの対象外）
	 * @return array
	 */
	private static function get_headings($dir = DATA_DIR, $force = false){
		global $cache, $_string;
		static $heading;

		$func = self::get_cache_name($dir);
		$cache_name = self::PAGENAME_HEADING_CACHE_PREFIX . $func;
		
		if ($force){
			// キャッシュ再生成
			unset ($heading[$func]);
			$cache['wiki']->removeItem($cache_name);
		}else if (!empty($heading[$func])){
			// メモリにキャッシュがある場合
			return $heading[$func];
		}else if ($cache['wiki']->hasItem($cache_name)) {
			// キャッシュから最終更新を読み込む
			$heading[$func] = $cache['wiki']->getItem($cache_name);
			return $heading[$func];
		}

		foreach(self::get_exists($dir) as $file => $page) {
			$initial = Reading::getReadingChar($page);
			if ($initial === $page){
				// 読み込めなかった文字
				$initial = Reading::OTHER_CHAR;
			}else if (preg_match('/^('.Reading::SYMBOL_PATTERN.')/u', $initial)) {
				$initial = Reading::SYMBOL_CHAR;
			}
			// ページの頭文字でページとページの読みを保存
			$ret[$initial][$page] =  Reading::getReading($page);
		}
		unset($initial, $page);
	
		// ページの索引でソート
		ksort($ret, SORT_NATURAL);
	
		foreach ($ret as $initial=>$pages){
			// ページ名の「読み」でソート
			asort($ret[$initial], SORT_NATURAL);
			// 「読み」でソートしたやつを$headingに保存
			$heading[$func][$initial] = array_keys($ret[$initial]);
		}
		unset($ret);

		// キャッシュに保存
		$cache['wiki']->setItem($cache_name, $heading[$func]);

		return $heading[$func];
	}
	/**
	 * ページのリンクリストを作る
	 * @param $pages ページ
	 * @param $cmd 使用するプラグイン
	 * @param boolean $with_filename ページのファイル名も表示する
	 * @return string
	 */
	private static function get_page_lists($pages, $cmd, $with_filename){
		$contents = array();
		foreach ($pages as $page){
			$wiki = FileFactory::Wiki($page);
			$_page = Utility::htmlsc($page, ENT_QUOTES);
			$url = $wiki->get_uri($cmd);
			if (!IS_MOBILE) {
				$contents[] = '<li><a href="' . $url . '">' . $_page . '</a> ' . $wiki->passage() .
					($with_filename ? '<br /><var>' . Utility::htmlsc($wiki->filename). '</var>' : '') .
					'</li>';
			}else{
				$contents[] = '<li><a href="' . $url . '" data-transition="slide">' . $_page . '</a>' .
					'<span class="ui-li-count">'. $wiki->passage(false, false) . '</span></li>';
			}
		}
		return $contents;
	}
	/**
	 * 最終更新のキャッシュを生成
	 * @param boolean $force キャッシュを再生成する
	 * @return array
	 */
	public static function get_recent($force = false){
		global $cache, $maxshow, $whatsnew;
		static $recent_pages;

		if ($force){
			// キャッシュ再生成
			unset ($recent_pages);
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
		$pages = self::get_exists(DATA_DIR, $force);

		// Check ALL filetime
		$recent_pages = array();
		foreach($pages as $filename=>$page){
			if ($page !== $whatsnew){
				$wiki = FileFactory::Wiki($page);
				 if (! $wiki->is_hidden() ) $recent_pages[$page] = $wiki->getTime();
			}
		}
		// Sort decending order of last-modification date
		arsort($recent_pages, SORT_NUMERIC);

		// Cut unused lines
		// BugTrack2/179: array_splice() will break integer keys in hashtable
		$count   = $maxshow + self::MAXSHOW_ALLOWANCE;
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
}