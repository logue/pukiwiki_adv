<?php
// PukiWiki Plus! - Yet another WikiWikiWeb clone
// $Id: link.php,v 1.20.10 2012/12/07 19:31:00 Logue Exp $
// Copyright (C)
//   2010-2012 PukiWiki Advance Developers Team
//   2005-2007 PukiWiki Plus! Team
//   2003-2007,2011 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Backlinks / AutoLinks related functions

// ------------------------------------------------------------
// DATA STRUCTURE of *.ref and *.rel files

// CACHE_DIR/md5('foobar').ref
// ---------------------------------
// Page-name1<tab>0<\n>
// Page-name2<tab>1<\n>
// ...
// Page-nameN<tab>0<\n>
//
//	0 = Added when link(s) to 'foobar' added clearly at this page
//	1 = Added when the sentence 'foobar' found from the page
//	    by AutoLink feature

// CACHE_DIR/md5('foobar').rel
// ---------------------------------
// Page-name1<tab>Page-name2<tab> ... <tab>Page-nameN
//
//	List of page-names linked from 'foobar'

// ------------------------------------------------------------

namespace PukiWiki\Lib;
use PukiWiki\Lib\Auth\Auth;
use PukiWiki\Lib\Renderer\InlineConverter;
use PukiWiki\Lib\File\FileFactory;
use PukiWiki\Lib\Renderer\Inline\AutoAlias;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
/**
 * 関連リンクのデーターベースクラス
 */
class Relational{
	// Links cache namespace
	const CACHE_NAMESPACE = 'wiki';
	// Related cache data prefix
	const REL_PREFIX = 'rel-';
	// Referred cache data prefix
	const REF_PREFIX = 'ref-';
	// links object cache data prefix
	const LINKS_PREFIX = 'links-';
	const LINKS_DB_FILENAME = 'links.sqlite3';

	private $cache, $page, $rel_name, $ref_name, $links_obj;

	/**
	 * コンストラクタ
	 * @global object $cache
	 * @param string $page ページ名
	 */
	public function __construct($page = ''){
		global $cache;
		$this->cache = $cache[self::CACHE_NAMESPACE];
		$this->links_obj = new InlineConverter(NULL, array('note'));
		// テーブル生成
		$this->adapter = new Adapter(array(
			'driver' => 'Pdo_Sqlite',
			'database' => CACHE_DIR . self::LINKS_DB_FILENAME
		));
		// FIXME
		$s = $this->adapter->query('CREATE TABLE IF NOT EXISTS "rel" ("page" TEXT UNIQUE NOT NULL, "data" TEXT)');
		$s->execute();
		$s = $this->adapter->query('CREATE TABLE IF NOT EXISTS "ref" ("page" TEXT UNIQUE NOT NULL, "data" TEXT)');
		$s->execute();
		unset($s);
		$this->page = $page;
	}

	/**
	 * 関連リンクを取得
	 * @return array
	 */
	public function get_related(){
		$data = self::get_rel($this->page);
		$times = array();
		if (is_array($data)){
			foreach ($data as $page) {
				$time = FileFactory::Wiki($page)->getTime();
				if($time !== 0) $times[$page] = $time;
			}
		}
		return $times;
	}
/*
	public function get_referred(){
		if (empty($this->page)) return;
		if (! $this->cache->hasItem($this->ref_name)){
			$data = $this->update();
			$this->cache->setItem($this->ref_name, $data);
		}else{
			$data = $this->cache->getItem($this->ref_name);
			$this->cache->touchItem($this->ref_name);
		}

		$times = array();
		foreach ($data as $ref_page=>$ref_auto) {
			$time = get_filetime($ref_page);
			if($time !== 0) $times[$ref_page] = $time;
		}
		return $times;
	}
*/
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
		$time = is_page($page, TRUE) ? get_filetime($page) : 0;
		$rel_old = self::get_rel($this->page);
		$rel_exist = ($rel_old === array());

		$rel_auto = $rel_new = array();
		foreach ($this->get_objects($page, TRUE) as $_obj) {
			if (! isset($_obj->type) || $_obj->type !== 'pagename' || $_obj->name === $page || empty($_obj->name) )
				continue;

			if ($_obj instanceof PukiWiki\Lib\Renderer\Inline\AutoLink) { // Not cool though
				$rel_auto[] = $_obj->name;
			} else if ($_obj instanceof PukiWiki\Lib\Renderer\Inline\AutoAlias) {
				$_alias = AutoAlias::get_autoalias_dict($_obj->name);
				if (FileFactory::Wiki($_alias)->is_valied()) {
					$rel_auto[] = $_alias;
				}
			} else {
				$rel_new[]  = $_obj->name;
			}
		}

		// All pages "Referenced to" only by AutoLink
		$rel_auto = array_diff(array_unique($rel_auto), $rel_new);

		// All pages "Referenced to"
		$rel_new = array_merge(array_unique($rel_new), $rel_auto);

		// update Pages referred from the $page
		if ($time) {
			// Page exists
			self::set_rel($this->page, $rel_new);
		}

		// .ref: Pages refer to the $page
		$this->add($this->page, array_diff($rel_new, $rel_old), $rel_auto);
		$this->remove($this->page, array_diff($rel_old, $rel_new));

		global $WikiName, $autolink, $nowikiname, $search_non_list;

		// $page seems newly created, and matches with AutoLink
		if ($time && ! $rel_exist && $autolink
			&& (preg_match('/^'.$WikiName.'$/', $page) !== false ? $nowikiname : strlen($page) >= $autolink))
		{
			// Update all, because they __MAY__ refer the $page [HEAVY]
			$search_non_list = 1;
			$pages           = do_search($page, 'AND', TRUE);
			foreach ($pages as $_page) {
				if ($_page !== $page)
					$this->update($_page);
			}
		}

		// $pageが削除されたときに、
		if (! $time && file_exists($ref_file)) {
			foreach (self::get_ref($this->page) as $line) {
				list($ref_page, $ref_auto) = explode("\t", rtrim($line));

				// $pageをAutoLinkでしか参照していないページを一斉更新する(おいおい)
				if ($ref_auto)
					self::remove($ref_page, array($page));
			}
		}
	}

	/**
	 * リンクのデーターベースを初期化
	 * @return void
	 */
	public function init() {
		if (Auth::check_role('readonly')) return; // Do nothing

		// Init database
		$s = $this->adapter->query('DELETE FROM rel');
		$s->execute();
		$s = $this->adapter->query('DELETE FROM ref');
		$s->execute();

		$ref   = array(); // Reference from
		foreach (get_existpages() as $_page) {
			$rel   = array(); // Reference to
			foreach ($this->get_objects($_page) as $_obj) {
				if (! isset($_obj->type) || $_obj->type !== 'pagename' || empty($_obj->name) || $_obj->name === $_page ) continue;

				$_name = $_obj->name;
				if ($_obj instanceof PukiWiki\Lib\Renderer\Inline\AutoAlias) {
					$_alias = AutoAlias::get_autoaliases($_name);
					if (! FileFactory::Wiki($_alias)->is_valied() )
						continue;	// not PageName
					$_name = $_alias;
				}
				$rel[] = $_name;
				if (! isset($ref[$_name][$_page]))
					$ref[$_name][$_page] = 1;
				if (! $_obj instanceof PukiWiki\Lib\Renderer\Inline\AutoLink)
					$ref[$_name][$_page] = 0;
			}
			ksort($rel, SORT_NATURAL);
			
//			$this->cache->setItem(self::REL_PREFIX.md5($_page), array_unique($rel));
			if (empty($rel)) continue;
			self::set_rel($_page, $rel);
		}
		unset($rel, $_page);

		ksort($ref, SORT_NATURAL);
		foreach ($ref as $_page=>$arr) {
			foreach ($arr as $ref_page=>$ref_auto)
				$data[] = $ref_page . "\t" . $ref_auto;
			self::set_ref($_page, $data);
			unset($data);
		}
		// メモリを開放
		self::get_objects();
		
		unset($ref_page,$ref_auto);
	}

	/**
	 * リンクしているページをキャッシュに追加
	 * @param string $page ページ名
	 * @param array $add 追加するページ名
	 * @param boolean $rel_auto 自動リンクか？
	 * @return void
	 */
	private function add($page, $add, $rel_auto){
		if (Auth::check_role('readonly')) return; // Do nothing

		$rel_auto = array_flip($rel_auto);

		foreach ($add as $_page) {
			$ref = array();
			$all_auto = isset($rel_auto[$_page]);
			$is_page  = FileFactory::Wiki($_page)->valied();

			$ref[$this->page] = $all_auto;
			foreach (self::get_ref($this->page) as $line) {
				list($ref_page, $ref_auto) = explode("\t", rtrim($line));
				if ($ref_auto === 0) $all_auto = FALSE;
				if ($ref_page !== $page) $ref[] = $line;
			}

			if ($is_page || ! $all_auto ) {
				ksort($ref, SORT_NATURAL);
				self::set_ref($page, $ref);
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
			$is_page  = FileFactory::Wiki($_page)->valied();

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
	 * @param type $page
	 * @return type
	 */
	private function get_objects($page){
		static $result;
		if (empty($page)) {
			unset($result);
			return;
		}
		if (! isset($result[$page]) ){
			$result[$page] = $this->links_obj->get_objects(join('', preg_grep('/^(?!\/\/|\s)./', FileFactory::Wiki($page)->source())), $page);
		}
		return $result[$page];
	}
	
	// もっとマシなSQL文って無い？

	/**
	 * Relをセット
	 * @param string $page ページ名
	 */
	private function set_rel($page, $rel){
		$req = $this->adapter->query(
			'INSERT OR REPLACE INTO "rel" ("page", "data") VALUES (' .
				$this->adapter->platform->quoteIdentifier($page) . ','.
				$this->adapter->platform->quoteIdentifier(join("\n", array_unique($rel))).
			')'
		);
		$req->execute();
	}
	/**
	 * Relを取得
	 * @param string $page ページ名
	 */
	private function get_rel($page){
		$req = $this->adapter->query(
			'SELECT "data" FROM "rel" WHERE "page"=' . $this->adapter->platform->quoteIdentifier($page)
		);
		$results = $req->execute();
		foreach ($results as $value) {
			$ret = $value['data'];
		}
		
		return !empty($ret) ? explode("\n", $ret) : array();
	}
	/**
	 * Refをセット
	 * @param string $page ページ名
	 */
	private function set_ref($page, $ref){
		$req = $this->adapter->query(
			'INSERT OR REPLACE INTO "ref" ("page", "data") VALUES (' .
				$this->adapter->platform->quoteIdentifier($page) . ','.
				$this->adapter->platform->quoteIdentifier(join("\n", array_unique($ref))) .
			')'
		);
		$req->execute();
	}
	/**
	 * Refを取得
	 * @param string $page ページ名
	 */
	private function get_ref($page){
		$req = $this->adapter->query(
			'SELECT "data" FROM "rel" WHERE "page"=' . $this->adapter->platform->quoteIdentifier($page)
		);
		$results = $req->execute();
		foreach ($results as $value) {
			$ret = $value['data'];
		}
		return !empty($ret) ? explode("\n", $ret) : array();
	}
}
/* End of file Relational.php */
/* Location: /vender/PukiWiki/Lib/Relational.php */