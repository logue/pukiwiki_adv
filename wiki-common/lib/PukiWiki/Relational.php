<?php
/**
 * 関連リンククラス
 *
 * @package   PukiWiki
 * @access    public
 * @author    Logue <logue@hotmail.co.jp>
 * @copyright 2012-2014 PukiWiki Advance Developers Team
 * @create    2012/12/18
 * @license   GPL v2 or (at your option) any later version
 * @version   $Id: Relational.php,v 1.0.0 2014/01/21 11:18:00 Logue Exp $
 **/

namespace PukiWiki;

use PukiWiki\Auth\Auth;
use PukiWiki\Factory;
use PukiWiki\Listing;
use PukiWiki\Renderer\InlineConverter;
use PukiWiki\Renderer\Inline\AutoAlias;
use PukiWiki\Renderer\Inline\AutoLink;
use PukiWiki\Search;
use PukiWiki\Utility;
use Zend\Db\Adapter\Adapter;

/**
 * 関連リンクのデーターベースクラス
 */
class Relational{
	/**
	 *キャッシュの名前空間
	 */
	const CACHE_NAMESPACE = 'wiki';
	/**
	 * 関連リンクのデーターベースファイル名
	 */
	const LINKS_DB_FILENAME = 'links.sqlite3';
	/**
	 * リンクしているページのテーブル名
	 */
	const REL_TABLE_NAME = 'rel';
	/**
	 * リンクされているページのテーブル名
	 */
	const REF_TABLE_NAME = 'ref';

	private $cache, $page, $rel_name, $ref_name, $links_obj;

	/**
	 * コンストラクタ
	 * @global object $cache
	 * @param string $page ページ名
	 */
	public function __construct($page = ''){
		global $cache;

		// データーベース
		$this->adapter = new Adapter(array(
			'driver' => 'Pdo_Sqlite',
			'database' => CACHE_DIR . self::LINKS_DB_FILENAME
		));
		// データーベースを初期化
		$this->adapter->query('CREATE TABLE IF NOT EXISTS `rel` (`page` TEXT UNIQUE NOT NULL, `data` TEXT)', Adapter::QUERY_MODE_EXECUTE);
		$this->adapter->query('CREATE TABLE IF NOT EXISTS `ref` (`page` TEXT UNIQUE NOT NULL, `data` TEXT)', Adapter::QUERY_MODE_EXECUTE);
		$this->cache = $cache[self::CACHE_NAMESPACE];
		$this->links_obj = new InlineConverter(NULL, array('note'));
		$this->page = $page;
	}

	/**
	 * デストラクタ
	 */
	public function __destruct(){
		if (!empty($this->page) && !Factory::Wiki($this->page)->has()) {
			// ページが削除されている時、関連リンクのデーターも削除
			$this->adapter->query('DELETE FROM `rel` WHERE `page` = ?', array($this->page));
			$this->adapter->query('DELETE FROM `ref` WHERE `page` = ?', array($this->page));
		}
		/*
		// 最適化
		$this->adapter->query('VACUUM', Adapter::QUERY_MODE_EXECUTE);
		*/
	}

	/**
	 * リンクしているページ名を取得
	 * @return array
	 */
	public function getRelated(){
		if (empty($this->page)) return;
		$entries = array();

		foreach (self::getRel($this->page) as $page) {
			$time = Factory::Wiki($page)->time();
			if ($time === 0) continue;	// AutoGlossaryや作成されてないページの場合0を返すので除外
			$entries[$page] = $time;
		}
		return $entries;
	}

	/**
	 * リンクされているページ名を取得
	 * @return array
	 */
	public function getReferred(){
		if (empty($this->page)) return;
		$data = self::getRef($this->page);

		$times = array();
		foreach ($data as $ref_page=>$ref_auto) {
			$time = Factory::Wiki($ref_page)->time();
			if ($time !== 0) $times[$ref_page] = $time;
		}
		return $times;
	}

	/**
	 * ページの関連性データーベースを更新
	 * @global string $WikiName
	 * @global boolean $autolink
	 * @global type $nowikiname
	 * @global array $search_non_list
	 * @param string $page
	 * @return void
	 */
	public function update($page = ''){
		$time = Factory::Wiki($page)->time();
		$rel_old = self::getRel($this->page);
		$rel_exist = ($rel_old === array());

		$rel_auto = $rel_new = array();
		$pages = self::getObjects($page);
		if (!empty($pages)) {
			foreach ($pages as $_obj) {
				if (! isset($_obj->type) || $_obj->type !== 'pagename' || $_obj->name === $page || empty($_obj->name) )
					continue;

				if ($_obj instanceof AutoLink) { // Not cool though
					$rel_auto[] = $_obj->name;
				} else if ($_obj instanceof AutoAlias) {
					$_alias = AutoAlias::getAutoAliasDict($_obj->name);
					if (Factory::Wiki($_alias)->isValied()) {
						$rel_auto[] = $_alias;
					}
				} else {
					$rel_new[]  = $_obj->name;
				}
			}
		}

		// All pages "Referenced to" only by AutoLink
		$rel_auto = array_diff(array_unique($rel_auto), $rel_new);

		// All pages "Referenced to"
		$rel_new = array_merge(array_unique($rel_new), $rel_auto);

		// update Pages referred from the $page
		if ($time) {
			// Page exists
			self::setRel($this->page, $rel_new);
		}

		// .ref: Pages refer to the $page
		self::add($this->page, array_diff($rel_new, $rel_old), $rel_auto);
		self::remove($this->page, array_diff($rel_old, $rel_new));

		global $autolink, $nowikiname, $search_non_list;

		// $page seems newly created, and matches with AutoLink
		if ($time && ! $rel_exist && $autolink
			&& (Utility::isWikiName($page) ? $nowikiname : strlen($page) >= $autolink))
		{
			// Update all, because they __MAY__ refer the $page [HEAVY]
			$search_non_list = 1;
			$pages           = Search::do_search($page, 'AND', TRUE);
			foreach ($pages as $_page) {
				if ($_page !== $page)
					$this->update($_page);
			}
		}

		// $pageが削除されたときに、
		foreach (self::getRef($this->page) as $line) {
			list($ref_page, $ref_auto) = explode("\t", rtrim($line));

			// $pageをAutoLinkでしか参照していないページを一斉更新する(おいおい)
			if ($ref_auto)
				self::remove($ref_page, array($page));
		}
	}

	/**
	 * リンクのデーターベースを初期化
	 * @return void
	 */
	public function init() {
		// Init database
		$this->adapter->query('DELETE FROM rel', Adapter::QUERY_MODE_EXECUTE);
		$this->adapter->query('DELETE FROM ref', Adapter::QUERY_MODE_EXECUTE);

		$ref   = array(); // Reference from
		foreach (Listing::pages('wiki') as $_page) {
			$rel   = array(); // Reference to
			$objs = self::getObjects($_page);
			if (empty($objs)) continue;
			foreach ($objs as $_obj) {
				if ( ! isset($_obj->type) || $_obj->type !== 'pagename' || empty($_obj->name) || $_obj->name === $_page ) continue;

				$_name = $_obj->name;
				if ($_obj instanceof AutoAlias) {
					$_alias = AutoAlias::getAutoAlias($_name);
					if (! Factory::Wiki($_alias)->isValied() )
						continue;	// not PageName
					$_name = $_alias;
				}
				$rel[] = $_name;
				if (! isset($ref[$_name][$_page]))
					$ref[$_name][$_page] = 1;
				if (! $_obj instanceof AutoLink)
					$ref[$_name][$_page] = 0;
			}
			ksort($rel, SORT_NATURAL);

			if (empty($rel)) continue;
			self::setRel($_page, $rel);
		}
		unset($rel, $_page);

		ksort($ref, SORT_NATURAL);
		foreach ($ref as $_page=>$arr) {
			foreach ($arr as $ref_page=>$ref_auto)
				$data[] = $ref_page . "\t" . $ref_auto;
			self::setRef($_page, $data);
			unset($data);
		}
		// メモリを開放
		self::getObjects();

		unset($ref_page,$ref_auto);

		// 最適化
		$this->adapter->query('VACUUM', Adapter::QUERY_MODE_EXECUTE);
	}

	/**
	 * リンクしているページをキャッシュに追加
	 * @param string $page ページ名
	 * @param array $add 追加するページ名
	 * @param boolean $rel_auto 自動リンクか？
	 * @return void
	 */
	private function add($page, $add, $rel_auto){
		$rel_auto = array_flip($rel_auto);

		foreach ($add as $_page) {
			$ref = array();
			$all_auto = isset($rel_auto[$_page]);
			$is_page  = Factory::Wiki($_page)->valied();

			$ref[$this->page] = $all_auto;
			foreach (self::getRef($this->page) as $line) {
				list($ref_page, $ref_auto) = explode("\t", rtrim($line));
				if ($ref_auto === 0) $all_auto = FALSE;
				if ($ref_page !== $page) $ref[] = $line;
			}

			if ($is_page || ! $all_auto ) {
				ksort($ref, SORT_NATURAL);
				self::setRef($page, $ref);
			}
			unset($ref);
		}
	}

	/**
	 * リンクしているページをキャッシュから削除
	 * @param string $page ページ名
	 * @param array $del 削除するページ名
	 * @return void
	 */
	private function remove($page, $del){
		if (Auth::check_role('readonly')) return; // Do nothing

		foreach ($del as $_page) {
			$all_auto = TRUE;
			$is_page  = Factory::Wiki($_page)->valied();

			$ref = array();
			foreach (self::get_ref($this->page) as $line) {
				list($ref_page, $ref_auto) = explode("\t", rtrim($line));
				if ($ref_page != $page) {
					if (! $ref_auto) $all_auto = FALSE;
					$ref[] = $line;
				}
			}

			if ($is_page || ! $all_auto) {
				ksort($ref, SORT_NATURAL);
				self::set_ref($page, $ref);
			}
			unset($ref);
		}
	}

	/**
	 * ページのソースからリンクオブジェクトを取得
	 * @param string $page
	 * @return object|null
	 */
	private function getObjects($page=''){
		static $result;
		if (empty($page)) {
			unset($result);
			return null;
		}
		if (! isset($result[$page]) ){
			$source = Factory::Wiki($page)->get(false);
			$result[$page] = ($source !== false) ? $this->links_obj->getObjects(join('', preg_grep('/^(?!\/\/|\s)./', $source)), $page) : null;
			return $result[$page];
		}
		return null;
	}

	// もっとマシなSQL文って無い？

	/**
	 * Relをセット
	 * @param string $page ページ名
	 */
	private function setRel($page, $rel){
		$this->adapter->query('INSERT OR REPLACE INTO `rel` (`page`, `data`) VALUES (?, ?)', array($page, join("\n", array_unique($rel))));
	}

	/**
	 * Relを取得
	 * @param string $page ページ名
	 * @return array
	 */
	private function getRel($page){
		$results = $this->adapter->query('SELECT `data` FROM `rel` WHERE `page` = ?', array($page));
		foreach ($results as $value) {
			$ret = $value['data'];
		}

		return !empty($ret) ? explode("\n", $ret) : array();
	}

	/**
	 * Refをセット
	 * @param string $page ページ名
	 */
	private function setRef($page, $ref){
		$this->adapter->query('INSERT OR REPLACE INTO `ref` (`page`, `data`) VALUES (?, ?)', array($page, join("\n", array_unique($ref))));
	}

	/**
	 * Refを取得
	 * @param string $page ページ名
	 * @return array
	 */
	private function getRef($page){
		$results = $this->adapter->query('SELECT `data` FROM `rel` WHERE `page` = ?', array($page));
		foreach ($results as $value) {
			$ret = $value['data'];
		}
		return !empty($ret) ? explode("\n", $ret) : array();
	}
}
/* End of file Relational.php */
/* Location: /vender/PukiWiki/Lib/Relational.php */