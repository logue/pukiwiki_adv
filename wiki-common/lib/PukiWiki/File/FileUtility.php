<?php
/**
 * ファイルユーティリティクラス
 *
 * @package   PukiWiki\File
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2013 PukiWiki Advance Developers Team
 * @create    2013/01/09
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: FileUtility.php,v 1.0.0 2013/01/29 17:28:00 Logue Exp $
 */
namespace PukiWiki\File;

use DirectoryIterator;
use PukiWiki\Utility;
use PukiWiki\Text\Reading;
use PukiWiki\Factory;
use PukiWiki\Auth\Auth;

class FileUtility{
	// ファイルの存在一覧キャッシュの接頭辞
	const EXSISTS_CACHE_PREFIX = 'exsists-';
	// ページ一覧キャッシュの接頭辞
	const PAGENAME_HEADING_CACHE_PREFIX = 'listing-';

	/**
	 * キャッシュをクリア
	 */
	public static function clearCache(){
		self::getExists('',true);
	}
	/**
	 * ディレクトリ内のファイルの一覧を作成
	 * @param string $dir ディレクトリ
	 * @param boolean $force キャッシュを再生成
	 * @return array
	 */
	public static function getExists($dir = DATA_DIR, $force = false){
		global $cache;
		static $aryret;

		$func = self::getCacheName($dir);
		$cache_name = self::EXSISTS_CACHE_PREFIX . $func;

		if ($force || empty($dir)){
			// キャッシュ再生成
			unset($aryret[$func]);
			$cache['wiki']->removeItem($cache_name);
			if (empty($dir)) return;	// ディレクトリが指定されていない場合、キャッシュを削除して終わり
		}else if (isset($aryret[$func])){
			// メモリにキャッシュがある場合
			return $aryret[$func];
		}else if ($cache['wiki']->hasItem($cache_name)) {
			// キャッシュから最終更新を読み込む
			$aryret[$func] = $cache['wiki']->getItem($cache_name);
			return $aryret[$func];
		}
		$pattern = '/^((?:[0-9A-F]{2})+)\.txt$/';
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
				$pattern = '/^((?:[0-9A-F]{2})+)_((?:[0-9A-F]{2})+)(?:\.([0-9]+|log))?$/';
				break;
			default:
				$func = md5($dir);
		}

		// キャッシュを再生成
		foreach (new DirectoryIterator($dir) as $fileinfo) {
			$filename = $fileinfo->getFilename();
			if ($fileinfo->isFile() && preg_match($pattern, $filename, $matches)){
				$page = Utility::decode($matches[1]);
				if ($dir !== UPLOAD_DIR) {
					$aryret[$func][$page] = $filename;
					continue;
				}
				$aryret[$func][$page][Utility::decode($matches[2])][isset($matches[3]) ? $matches[3] : 0] = $filename;
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
	private static function getCacheName($dir = DATA_DIR){
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
				$func = md5($dir);
		}
		return $func;
	}
	/**
	 * ファイル一覧画面を生成（プラグインでやるべきかもしれないが、この処理はバックアップ一覧などページ名一覧以外の用途でも使うためこのクラスで定義）
	 * @param string $dir ディレクトリ
	 * @return string
	 */
	public static function getListing($dir = DATA_DIR, $cmd = 'read', $with_filename = false){
		global $_string;
		// 一覧の配列を取得
		$heading = self::getHeadings($dir, true);
		$contents = array();

		if (IS_MOBILE) {
			// モバイル用
			$contents[] = '<ul data-role="listview">';
			foreach ($heading as $initial=>$pages) {
				$page_lists = self::getPageLists($pages, $cmd);
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
			$page_lists = self::getPageLists($pages, $cmd, $with_filename);
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
	private static function getHeadings($dir = DATA_DIR, $force = false){
		global $cache;
		static $heading;

		$func = self::getCacheName($dir);
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

		foreach(self::getExists($dir) as $file => $page) {
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

		foreach ($ret as $initial => &$pages){
			// ページ名の「読み」でソート
			asort($pages, SORT_NATURAL);
			// 「読み」でソートしたやつを$headingに保存
			$heading[$func][$initial] = array_keys($pages);
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
	private static function getPageLists($pages, $cmd, $with_filename){
		global $read_auth_pages;

		$contents = array();
		// ユーザ名取得
		$auth_key = Auth::get_user_info();
		// コンテンツ管理者以上は、: のページも閲覧可能
		$has_permisson = Auth::check_role('role_contents_admin');

		foreach ($pages as $page){
			$wiki = Factory::Wiki($page);
			// 存在しない場合、当然スルー
			if (!$wiki->has()) continue;
			// 隠しページの場合かつ、隠しページを表示できる権限がない場合スルー
			if ($wiki->isHidden() && $has_permisson) continue;
			// 閲覧できる権限がない場合はスルー
			if (! $wiki->isReadable()) continue;
			
			$_page = Utility::htmlsc($page, ENT_QUOTES);
			$url = $wiki->uri($cmd);
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
	 * TrackBack Ping IDからページ名を取得
	 * @param boolean $force キャッシュを再生成する
	 * @return string
	 */
	public static function getPageFromTbId($id, $force = false){
		global $cache;
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
			$pages = self::getExists();
			foreach ($pages as $page) {
				$tb_id[md5($page)] = $page;
			}
		}

		$cache['wiki']->setItem($cache_name, $tb_id);
		return $cache[$id];
	}
}