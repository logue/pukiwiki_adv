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
 * $array = $obj->get($key);
 * $array[] = array(4, 5, 6);		// Add - directly
 * $obj->add($key, array(4, 5, 6));	// Add - method of Config object
 * $array = array(1=>array(1, 2, 3));		// Replace - directly
 * $obj->set($key, array(1=>array(1, 2, 3));	// Replace - method of Config object
 * $obj->write();
 */

namespace PukiWiki\Config;

use PukiWiki\Config\ConfigTable;
use PukiWiki\Config\Direct;
use PukiWiki\Config\Sequential;
use PukiWiki\File\FileFactory;

// Configuration-page manager
class Config
{
	const CONFIG_PAGE_PREFIX = ':config/';
	const CACHE_PREFIX = 'config-';

	public $name, $page; // Page name
	private $objs = array();

	/**
	 * コンストラクタ
	 * @param $name 設定名
	 * @param $autoupdate 自動更新するか
	 */
	public function __construct($name, $autoupdate = false)
	{
		$this->name = $name;
		$this->page = self::CONFIG_PAGE_PREFIX . $name;
//		$this->cache_name = self::CACHE_PREFIX.md5($name);
		$this->wiki = FileFactory::Wiki($this->page);
		$this->autoupdate = $autoupdate;
		$this->read();
	}
	/**
	 * デストラクタ
	 */
	public function __destruct(){
		if ($this->autoupdate) $this->write();
	}
	/**
	 * :configページから項目を読み取る
	 * @return boolean
	 */
	public function read()
	{
		$obj = new ConfigTable('');
		$matches = array();

		if ($this->wiki->has()){

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
		}
		return TRUE;
	}
	/**
	 * ページに設定内容を保存する
	 */
	public function write()
	{
		foreach (explode("\n",$this->toString()) as $line){
			if (trim($line) === '') continue;
			$ret[] = trim($line);
		}
		$this->wiki->set($ret);
	}

	/**
	 * 設定項目の値を得る
	 * @param string $key 項目名
	 * @return string
	 */
	public function get($key)
	{
		$obj = $this->get_object($key);
		return $obj->values;
	}
	/**
	 * 設定を保存する
	 * @param string $key 項目名
	 * @param string $value 値
	 */
	public function set($key, $value)
	{
		$obj         = $this->get_object($key);
		$obj->values = $value;
	}
	/**
	 * 設定を保存する（後方互換。Config::set()のエイリアス）
	 */
	public function put($key, $values)
	{
		$this->set($key, $values);
	}
	/**
	 * 設定を追加する
	 * @param string $key 項目名
	 * @param string $values 値
	 */
	public function add($key, $value)
	{
		$obj = $this->get_object($key);
		$obj->values[] = $value;
	}
	/**
	 * 設定をWikiに書き込むための文字列にする
	 * @return string
	 */
	private function toString()
	{
		$retval = '';
		foreach ($this->objs as $key=>$obj)
			$retval .= $obj->toString();
		return $retval;
	}
	/**
	 * 項目のオブジェクトを取得
	 * @param string $key 項目名
	 * @return object
	 */
	private function get_object($key)
	{
		if (! isset($this->objs[$key])){
			$this->objs[$key] = new ConfigTable('*' . trim($key) . "\n");
		}
		return $this->objs[$key];
	}
}

/* End of file config.php */
/* Location: ./wiki-common/lib/config.php */
