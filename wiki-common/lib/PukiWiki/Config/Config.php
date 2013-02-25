<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: config.php,v 1.7.2 2012/05/29 17:34:00 Logue Exp $
// Copyright (C)
//               2010-2013 PukiWiki Advance Developers Team
//               2003-2007 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Parse a PukiWiki page as a configuration page

/*
 * $obj = new Config('plugin/plugin_name/')
 * $obj->read();
 * $array = $obj->get($title);
 * $array[] = array(4, 5, 6);		// Add - directly
 * $obj->add($title, array(4, 5, 6));	// Add - method of Config object
 * $array = array(1=>array(1, 2, 3));		// Replace - directly
 * $obj->set($title, array(1=>array(1, 2, 3));	// Replace - method of Config object
 * $obj->write();
 */

namespace PukiWiki\Config;

use PukiWiki\Config\ConfigTable;
use PukiWiki\Config\Direct;
use PukiWiki\Config\Sequential;
use PukiWiki\Factory;

// Configuration-page manager
class Config
{
	const CONFIG_PAGE_PREFIX = ':config/';
	const CACHE_PREFIX = 'config-';

	var $name, $page; // Page name
	var $objs = array();

	/**
	 * コンストラクタ
	 */
	public function __construct($name)
	{
		$this->name = $name;
		$this->page = self::CONFIG_PAGE_PREFIX . $name;
//		$this->cache_name = self::CACHE_PREFIX.md5($name);
		$this->wiki = Factory::Wiki($this->page);
	}
	/**
	 * :configページから項目を読み取る
	 * @param boolean $force キャッシュを再更新する
	 * @return boolean
	 */
	public function read($force = false)
	{
		global $cache;
		static $objs;
		if (!$this->wiki->has()) return FALSE;
/*
		// Wikiの更新チェック
		if ($cache['wiki']->hasItem($this->cache_name)){
			$cache_meta = $cache['wiki']->getMetadata($this->cache_name);
			if ($cache_meta['mtime'] < $this->wiki->time()) {
				$force = true;
			}
		}

		// キャッシュ処理
		if ($force) {
			unset($objs);
			$cache['wiki']->removeItem($this->cache_name);
		}else if (!empty($objs)) {
			$this->objs = $objs;
			return TRUE;
		}else if ($cache['wiki']->hasItem($this->cache_name)) {
			$objs = $cache['wiki']->getItem($this->cache_name);
			$cache['wiki']->touchItem($this->cache_name);
			$this->objs = $objs;
			return TRUE;
		}
*/
		$objs = array();
		$obj = new ConfigTable('');
		$matches = array();

		foreach ($this->wiki->get() as $line) {

			if (empty($line)) continue;

			$head  = $line{0};	// The first letter
			$level = strspn($line, $head);

			if ($level > 3) {
				$obj->add_line($line);
			} else {
				switch ($head){
					case '*':
						// 見出し
						$line = preg_replace('/^(\*{1,3}.*)\[#[A-Za-z][\w-]+\](.*)$/', '$1$2', $line);	// paraeditを削除
						if ($level == 1) {
							// 見出し1の場合
							$objs[$obj->title] = $obj;
							$obj = new ConfigTable($line);
						} else {
							// 見出し2~3は箇条書きと同じ扱い
							if (! $obj instanceof Direct) $obj = new Direct(null, $obj);
							$obj->set_key($line);
						}
						break;
					case '-':
						// 箇条書き
						if (! $obj instanceof Direct) $obj = new Direct(null, $obj);
						$obj->add_value($line);
						break;
					case '|':
						// テーブル
						if (preg_match('/^\|(.+)\|\s*$/', $line, $matches)) {
						// Table row
						if (! $obj instanceof Sequential) $obj = new Sequential(null , $obj);
							// Trim() each table cell
							$obj->add_value(array_map('trim', explode('|', $matches[1])));
						}else{
							$obj->add_line($line);
						}
						break;
/*
					case ':':
						// 定義リスト
						if (preg_match('/^:(.*)\|(.*)$/', $line, $matches)) {
							$obj->set_key($matchs[0]);
							$obj->add_value($matches[1]);
						}
						break;
*/
				}
			}
		}
		$objs[$obj->title] = $obj;

		$this->objs = $objs;
//		$objs = $cache['wiki']->setItem($this->cache_name, $objs);
		return TRUE;
	}

	/**
	 * 設定項目の値を得る
	 * @param string $title 項目名
	 * @return string
	 */
	public function get($title)
	{
		$obj = $this->get_object($title);
		return $obj->values;
	}
	/**
	 * 設定を保存する
	 * @param string $title 項目名
	 * @param string $values 値
	 */
	public function set($title, $values)
	{
		$obj         = $this->get_object($title);
		$obj->values = $values;
	}
	/**
	 * 設定を保存する（後方互換。Config::set()のエイリアス）
	 */
	public function put($title, $values)
	{
		self::set($title, $values);
	}
	/**
	 * 設定を追加する
	 * @param string $title 項目名
	 * @param string $values 値
	 */
	public function add($title, $value)
	{
		$obj = $this->get_object($title);
		$obj->values[] = $value;
	}
	/**
	 * ページに設定内容を保存する
	 */
	public function write()
	{
		global $cache;
		// ページに保存
		$this->wiki->set(self::toString());
		// キャッシュも更新
		$cache['wiki']->setItem(self::CACHE_PREFIX.$this->name, $this->objs);
	}
	/**
	 * 設定をWikiに書き込むための文字列にする
	 * @return string
	 */
	private function toString()
	{
		$retval = '';
		foreach ($this->objs as $title=>$obj)
			$retval .= $obj->toString();
		return $retval;
	}
	/**
	 * 項目のオブジェクトを取得
	 * @param string $title 項目名
	 * @return object
	 */
	private function get_object($title)
	{
		if (! isset($this->objs[$title])){
			$this->objs[$title] = new ConfigTable('*' . trim($title) . "\n");
		}
		return $this->objs[$title];
	}
}

/* End of file config.php */
/* Location: ./wiki-common/lib/config.php */
