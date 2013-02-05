<?php
/**
 * ファイルユーティリティクラス
 *
 * @package   PukiWiki\Lib\File
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2013 PukiWiki Advance Developers Team
 * @create    2013/01/09
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: FileUtility.php,v 1.0.0 2013/01/29 17:28:00 Logue Exp $
 */
namespace PukiWiki\Lib\File;

use PukiWiki\Lib\Utility;
use PukiWiki\Lib\Text\Reading;
use PukiWiki\Lib\File\FileFactory;
use PukiWiki\Lib\Auth\Auth;

class FileUtility{
	// ファイルの存在一覧キャッシュの接頭辞
	const EXSISTS_CACHE_PREFIX = 'exsists-';
	// 更新履歴のキャッシュ名
	const RECENT_CACHE_NAME = 'recent';
	// 更新履歴／削除履歴で表示する最小ページ数
	const MAXSHOW_ALLOWANCE = 10;
	// ページ一覧キャッシュの接頭辞
	const PAGENAME_HEADING_CACHE_PREFIX = 'listing-';

	/**
	 * ディレクトリ内のファイルの一覧を作成
	 * @param string $dir ディレクトリ
	 * @param boolean $force キャッシュを再生成
	 * @return array
	 */
	public static function get_exists($dir = DATA_DIR, $force = false){
		global $cache;
		static $aryret;

		$func = self::get_cache_name($dir);
		$cache_name = self::EXSISTS_CACHE_PREFIX . $func;

		if ($force){
			// キャッシュ再生成
			unset($aryret[$func]);
			$cache['wiki']->removeItem($cache_name);
		}else if (isset($aryret[$func])){
			// メモリにキャッシュがある場合
			return $aryret[$func];
		}else if ($cache['wiki']->hasItem($cache_name)) {
			// キャッシュから最終更新を読み込む
			$aryret[$func] = $cache['wiki']->getItem($cache_name);
			return $aryret[$func];
		}

		switch($dir){
			case DATA_DIR:
				$pattern = '/^((?:[0-9A-F]{2})+)\.txt$/';
				break;
			case COUNTER_DIR:
				$pattern = '/^((?:[0-9A-F]{2})+)\.count$/';
				break;
			case BACKUP_DIR:
				$pattern = '/^((?:[0-9A-F]{2})+)\.(txt|gz|bz2|lzf)$/';
				break;
			case UPLOAD_DIR:
				$pattern = '/^((?:[0-9A-F]{2})+)_((?:[0-9A-F]{2})+)$/';
				break;
			default:
				$func = encode($dir.$ext);
		}

		// キャッシュを再生成
		foreach (new \DirectoryIterator($dir) as $fileinfo) {
			$filename = $fileinfo->getFilename();
			if ($fileinfo->isFile() && preg_match($pattern, $filename, $matches)){
				$aryret[$func][$filename] = Utility::decode($matches[1]);
			}
		}
		$cache['wiki']->setItem($cache_name, $aryret[$func]);
		return $aryret[$func];
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
		global $_string;
		// 一覧の配列を取得
		$heading = self::get_headings($dir);
		$contents = array();

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
		global $cache;
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
		// ユーザ名取得
		$auth_key = Auth::get_user_info();
		// コンテンツ管理者以上は、: のページも閲覧可能
		$has_permisson = Auth::check_role('role_adm_contents');

		foreach ($pages as $page){
			if (! Auth::is_page_readable($page, $auth_key['key'], $auth_key['group'])) continue;

			$wiki = FileFactory::Wiki($page);
			if (!$wiki->has()) continue;

			if ($wiki->isHidden() && $has_permisson) continue;

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
	 * 最終更新のキャッシュを取得
	 * @param boolean $force キャッシュを再生成する
	 * @return array
	 */
	public static function get_recent($force = false){
		global $cache, $maxshow, $autolink, $whatsnew, $autobasealias, $cache;
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
				 if (! $wiki->isHidden() ) $recent_pages[$page] = $wiki->time();
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
	/**
	 * 最終更新のキャッシュを更新
	 * @param string $page_update 更新があったページ
	 * @param string $page_remove 削除されたページ
	 * @return void
	 */
	public static function set_recent($page_update, $page_remove){
		global $maxshow, $whatsnew, $autolink, $autobasealias;
		global $cache;

		// AutoLink implimentation needs everything, for now
		if ($autolink || $autobasealias) {
			self::get_recent(true);	// Try to (re)create ALL
			return;
		}

		$non_list = FileFactory::Wiki($page_update)->isHidden();

		if ((empty($page_update) || $non_list) && empty($page_remove))
			return; // No need

		// Check cache exists
		if (! $cache['wiki']->hasItem(self::RECENT_CACHE_NAME)){
			self::get_recent(true);	// Try to (re)create ALL
			return;
		}else{
			$recent_pages = $cache['wiki']->getItem(self::RECENT_CACHE_NAME);
		}

		// Remove if it exists inside
		if (isset($recent_pages[$page_update])) unset($recent_pages[$page_update]);
		if (isset($recent_pages[$page_remove])) unset($recent_pages[$page_remove]);

		// Update Cache
		$cache['wiki']->setItem(self::RECENT_CACHE_NAME, $recent_pages);

		// Add to the top: like array_unshift()

		if (!empty($update) && $update !== $whatsnew && ! $non_list)
			$recent_pages = array($update_page => FileFactory::Wiki($page_update)->time()) + $recent_pages;

		// Check
		$abort = count($recent_pages) < $maxshow;

		// Update cache
		$cache['wiki']->setItem(self::RECENT_CACHE_NAME, $recent_pages);

		if ($abort) {
			self::get_recent(true);	// Try to (re)create ALL
			return;
		}

		// ----
		// Update the page 'RecentChanges'

		$recent_pages = array_splice($recent_pages, 0, $maxshow);

		$lines[] = '#norelated';
		foreach ($recent_pages as $_page=>$time){
			$lines[] = '- &epoch('.$time.');' . ' - ' . '[[' . htmlsc($_page) . ']]';
		}

		FileFactory::Wiki($whatsnew)->set($lines);
	}
	/**
	 * TrackBack Ping IDからページ名を取得
	 * @param boolean $force キャッシュを再生成する
	 * @return string
	 */
	public static function get_page_from_tb_id($id, $force = false){
		static $tb_id;
		$cache_name = self::EXSISTS_CACHE_PREFIX . 'trackback';

		if ($force){
			// キャッシュ再生成
			unset ($tb_id);
			$cache['wiki']->removeItem($cache_name);
		}else if (!empty($tb_id)){
			// メモリにキャッシュがある場合
			return $tb_id[$id];
		}else if ($cache['wiki']->hasItem($cache_name)) {
			// キャッシュから最終更新を読み込む
			$tb_id = $cache['wiki']->getItem($cache_name);
			return $tb_id[$id];
		}

		if (empty($tb_id)){
			$pages = self::get_exists();
			foreach ($pages as $page) {
				$tb_id[md5($page)] = $page;
			}
		}

		$cache['wiki']->setItem($cache_name, $tb_id);
		return $cache[$id];
	}
}