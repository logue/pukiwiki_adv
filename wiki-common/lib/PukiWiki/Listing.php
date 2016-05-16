<?php
/**
 * ページ一覧
 *
 * @package   PukiWiki
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2013 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Listing.php,v 1.0.0 2013/03/23 09:30:00 Logue Exp $
 */

namespace PukiWiki;

use PukiWiki\Auth\Auth;
use PukiWiki\File\FileFactory;
use PukiWiki\Text\Reading;

/**
 * ページ一覧表示クラス
 */
class Listing{
	// ファイルの存在一覧キャッシュの接頭辞
	const EXSISTS_CACHE_PREFIX = 'exsists-';
	// ページ一覧キャッシュの接頭辞
	const PAGENAME_HEADING_CACHE_PREFIX = 'listing-';
	/**
	 * 一覧を取得
	 * @param string $type データーのタイプ
	 * @return array
	 */
	public static function pages($type = 'wiki'){
		return FileFactory::getPages($type);
	}
	/**
	 * 一覧をページの読みでソートし出力
	 * @param string $type 一覧を表示するタイプ
	 * @param boolean $force キャッシュを再生成する（※ページの経過時間はキャッシュの対象外）
	 * @return array
	 */
	private static function getHeadings($type='wiki', $force = false){
		global $cache;
		static $heading;

		$cache_name = self::PAGENAME_HEADING_CACHE_PREFIX . $type;

		if ($force){
			// キャッシュ再生成
			unset ($heading[$type]);
			$cache['wiki']->removeItem($cache_name);
		}else if (!empty($heading[$type])){
			// メモリにキャッシュがある場合
			return $heading[$type];
		}else if ($cache['wiki']->hasItem($cache_name)) {
			// キャッシュから最終更新を読み込む
			$heading[$type] = $cache['wiki']->getItem($cache_name);
			return $heading[$type];
		}

		$ret = array();
		$pages = self::pages($type);
		if ($type !== 'attach'){
			foreach($pages as $page) {	// ここで一覧取得
				$initial = Reading::getReadingChar($page);	// ページの読みを取得
				if ($initial === $page){
					// 読み込めなかった文字
					$initial = Reading::OTHER_CHAR;
				}else if (preg_match('/^('.Reading::SYMBOL_PATTERN.')/u', $initial)) {
					$initial = Reading::SYMBOL_CHAR;
				}
				// ページの頭文字でページとページの読みを保存
				$ret[$initial][$page] =  Reading::getReading($page);
			}
		}else{
			foreach($pages as $page=>$a) {	// ここで一覧取得
				
				$initial = Reading::getReadingChar($page);	// ページの読みを取得
				if ($initial === $page){
					// 読み込めなかった文字
					$initial = Reading::OTHER_CHAR;
				}else if (preg_match('/^('.Reading::SYMBOL_PATTERN.')/u', $initial)) {
					$initial = Reading::SYMBOL_CHAR;
				}
				// ページの頭文字でページとページの読みを保存
				$ret[$initial][$page] =  Reading::getReading($page);
			}

		}
		unset($initial, $page);

		// ページの索引でソート
		ksort($ret, SORT_NATURAL);

		foreach ($ret as $initial => &$pages){
			// ページ名の「読み」でソート
			asort($pages, SORT_NATURAL);
			// 「読み」でソートしたやつを$headingに保存
			$heading[$type][$initial] = array_keys($pages);
		}
		unset($ret);

		// キャッシュに保存
		$cache['wiki']->setItem($cache_name, $heading[$type]);

		return $heading[$type];
	}
	/**
	 * ファイル一覧画面を生成
	 * プラグインでやるべきかもしれないが、この処理はバックアップ一覧などページ名一覧以外の用途でも使うためこのクラスで定義
	 * @param string $type ディレクトリのタイプ
	 * @return string
	 */
	public static function get($type = 'wiki', $cmd = 'read', $with_filename = false){
		global $_string;
		// 一覧の配列を取得
		$heading = self::getHeadings($type);
		
		if ($heading == null) return '<p class="alert alert-success">No match files</p>';
		
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
	 * ページのリンクリストを作る
	 * @param $pages ページ
	 * @param $cmd 使用するプラグイン
	 * @param boolean $with_filename ページのファイル名も表示する
	 * @return string
	 */
	private static function getPageLists($pages, $cmd = 'read', $with_filename = false){
		$contents = array();
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

			if ($cmd !== 'attach'){
				$contents[] = IS_MOBILE ?
					'<li><a href="' . $wiki->uri($cmd) . '" data-transition="slide">' . $_page . '</a>' .
					'<span class="ui-li-count">'. $wiki->passage(false, false) . '</span></li>' :
					'<li><a href="' . $wiki->uri($cmd) . '">' . $_page . '</a> ' . $wiki->passage() .
					($with_filename ? '<br /><var>' . Utility::htmlsc($wiki->filename). '</var>' : '');
				
					'</li>'
				;
			}else{
				$ret = array();
				$ret[] = '<li><a href="' . Router::get_cmd_uri('attach',null,null, array('page'=>$page,'ajax'=>'false')) . '">' . $_page . '</a> ';

				$attaches = $wiki->attach();

				if (count($attaches) !== 0){
					$ret[] = '<ul>';
					foreach ($attaches as $filename=>$files) {
						$ret[] = '<li><a href="' . Router::get_cmd_uri('attach',null,null, array('refer'=>$page,'pcmd'=>'info','file'=>$filename)). '">' . Utility::htmlsc($filename) . '</a></li>';
					}
					$ret[] = '</ul>';
				}
				$ret[] = '</li>';
				$contents[] = join("\n", $ret);
			}
		}
		return $contents;
	}
}