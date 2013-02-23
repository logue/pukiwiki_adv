<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: Factory.php,v 1.0.0 2012/12/18 11:00:00 Logue Exp $
// Copyright (C)
//   2012 PukiWiki Advance Developers Team
// License: GPL v2 or (at your option) any later version

namespace PukiWiki\File;

/**
 * ファイル操作のファクトリークラス
 */
class FileFactory
{
	private function __construct() {}
	private function __clone() {}

	protected static $classMap = array(
		'generic' => 'PukiWiki\File\File',
		'wiki'    => 'PukiWiki\File\WikiFile',
		'backup'  => 'PukiWiki\File\BackupFile',
		'diff'    => 'PukiWiki\File\DiffFile',
		'counter' => 'PukiWiki\File\CounterFile',
	);
	/**
	 * 汎用
	 */
	public static function factory($dirver, $name){
		return new $this->classMap[$driver]($name);
	}
	/**
	 * ファイル
	 */
	public static function Generic($filename){
		return new File($filename);
	}
	/**
	 * Wikiファイル
	 */
	public static function Wiki($page){
		return new WikiFile($page);
	}
	/**
	 * バックアップ
	 */
	public static function Backup($page){
		return new BackupFile($page);
	}
	/**
	 * 差分ファイル
	 */
	public static function Diff($page){
		return new DiffFile($page);
	}
	/**
	 * カウンタファイル
	 */
	public static function Counter($page){
		return CounterFile($page);
	}
}

/* End of file Factory.php */
/* Location: /vender/PukiWiki/Lib/File/Factory.php */