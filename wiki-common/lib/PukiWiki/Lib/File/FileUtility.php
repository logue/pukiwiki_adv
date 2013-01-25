<?php
namespace PukiWiki\Lib\File;

class FileUtility{
	const EXSISTS_CACHE_PREFIX = 'exsists-';
	// RecentChanges
	const RECENT_CACHE_NAME = 'recent';
	const MAXSHOW_ALLOWANCE = 10;
	// Listing
	const PAGENAME_LISTING_CACHE_PREFIX = 'listing-';

	/**
	 * ディレクトリ内のファイルの存在確認
	 */
	public static function get_exsists($dir = DATA_DIR, $force = false){
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

		$func = self::getCacheName($dir);
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
				$cache['wiki']->setItem($cache_name, $aryret[$dir]);
			}else{
				// キャッシュからメモリに呼び出す
				$aryret[$func] = $cache['wiki']->getItem($cache_name);
			}
		}
		return $aryret[$func];
	}
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
				$func = encode($dir.$ext);
		}
		return $func;
	}
	
	private static function getHeading($dir = DATA_DIR, $force = false){
		static $heading;
		$pages = self::get_exsists($dir);
		$func = self::getCacheName($dir);
		$cache_name = self::PAGENAME_LISTING_CACHE_PREFIX . $func;
		
		if ($force){
			// キャッシュ再生成
			unset $heading[$func];
			$cache['wiki']->removeItem($cache_name);
		}else if (!empty($cache_name)){
			// メモリにキャッシュがある場合
			return $heading[$func];
		}else if ($cache['wiki']->hasItem($cache_name) {
			// キャッシュから最終更新を読み込む
			$heading[$func] = $cache['wiki']->getItem($cache_name);
			return $heading[$func];
		}

		$_msg_symbol = $_string['symbol'];
		$_msg_other = $_string['other'];
		// Sentinel: symbolic-chars < alphabetic-chars < another(multibyte)-chars
		// = ' ' < '[a-zA-Z]' < 'zz'
		$sentinel_symbol  = '*';
		$sentinel_another = 'zz';

		foreach($pages as $file => $page) {
			if(mb_ereg('^(\:|[A-Za-z])', mb_convert_kana($page, 'a'), $matches) !== FALSE) {
				// 英数字
				$initial = $matches[1];
			} elseif (isset($readings[$page]) && mb_ereg('^([ァ-ヶ])', $readings[$page], $matches) !== FALSE) { // here
				// ひらがな、カタカナ
				$initial = $matches[1];
			} elseif (mb_ereg('^[ -~]|[^ぁ-ん亜-熙]', $page)) { // and here
				// 常用漢字
				$initial = $sentinel_symbol;
			} elseif (preg_match('/^([가-힣])/u', $page) !== FALSE){
				// ハングル（日本語とバッティングしないため実装）
				// http://pukiwiki.sourceforge.jp/dev/?BugTrack2%2F13
				$initial = Hangul::toChoson($page);
/*
			} elseif (mb_ereg('/^([一-龥])/',$page) !== FALSE){
				// 簡体中国語
*/
			} else {
				$initial = $sentinel_another;
			}
			$heading[$initial][] = $page;
		}
	}
	/**
	 * ファイル一覧画面を生成
	 * @param string $dir ディレクトリ
	 * @return string
	 */
	public static function get_listing($dir = DATA_DIR){
		$files = self::get_exsists($dir);
		global $pagereading_enable, $list_index, $_string;

		

			$str[] = '<li>';
			if ($cmd !== 'read'){
				$str[] = '<a href="' . get_cmd_uri($cmd, $page) . '" >' . htmlsc($page, ENT_QUOTES) . '</a>';
			}else{
				if (!IS_MOBILE) {
					$str[] = '<a href="' . get_page_uri($page) . '" >' . htmlsc($page, ENT_QUOTES) . '</a>' .get_pg_passage($page, true);
					if ($withfilename) {
						$str[] = '<br /><var>' . htmlsc($file) . '</var>';
					}
				}else{
					$str[] = '<a href="' . get_page_uri($page) . '" data-transition="slide">' . htmlsc($page, ENT_QUOTES) . '</a>' . '<span class="ui-li-count">'.get_pg_passage($page, false).'</span>';
				}
			}
			$str[] = '</li>';
			$array[$initial][$page] = $str;
			$counts[$initial] = count($array[$initial]);
		}
		unset($pages);
		ksort($array, SORT_STRING);

		$cnt = 0;
		$retval = $contents = array();
		if (!IS_MOBILE) {
			$retval[] = '<div class="list_pages">';
			foreach ($array as $_initial => $pages) {
				ksort($pages, SORT_STRING);
				if ($list_index) {
					++$cnt;
					$page_count = $counts[$_initial];
					if ($_initial == $sentinel_symbol) {
						$_initial = htmlsc($_msg_symbol);
					} else if ($_initial == $sentinel_another) {
						$_initial = htmlsc($_msg_other);
					}
					$retval[] = '<fieldset id="head_' . $cnt .'">';
					$retval[] = '<legend><a href="#top_' . $cnt . '">' . $_initial . '</a></legend>';
					$retval[] = '<ul class="list1">';

					$contents[] = '<li id="top_' . $cnt .'"><a href="#head_' . $cnt . '" title="'.$page_count.'">' .$_initial . '</a></li>';
				}
				$retval[] = join("\n", $pages);
				if ($list_index) {
					$retval[] = '</ul>';
					$retval[] = '</fieldset>';
				}
			}
			$retval[] = '</div>';
		}else{
			foreach ($array as $_initial => $pages) {
				ksort($pages, SORT_STRING);
				if ($list_index) {
					++$cnt;
					$page_count = $counts[$_initial];
					if ($_initial == $sentinel_symbol) {
						$_initial = htmlsc($_msg_symbol);
					} else if ($_initial == $sentinel_another) {
						$_initial = htmlsc($_msg_other);
					}
					$contents[] = '<li data-role="list-divider">' . $_initial . '</li>';

					if ( isset($array[$_initial]) && is_array($array[$_initial]) ){
						foreach($array[$_initial] as $page){
							$contents[] = $page;
						}
					}
					//$contents[] = $array[$_initial][$pages];
				}
			}
		}
		unset($array);

		// Insert a table of contents
		$ret = '';
		if ($list_index && $cnt) {
			while (! empty($contents)) {
				$tmp[] = join('', array_splice($contents, 0));
			}
			$contents = & $tmp;
			if (!IS_MOBILE) {
				array_unshift(
					$retval,
					'<div  class="page_initial"><ul>',
					join("\n" , $contents),
					'</ul></div>');
				$ret = '<div class="pages">'."\n".join("\n", $retval) . "\n".'</div>';
			}else{
				$ret = '<ul data-role="listview">'.join("\n", $contents).'</ul>';
			}
		}

		return $ret;
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
			unset $recent_pages;
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
		$pages = self::get_exsists(DATA_DIR, $force);

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