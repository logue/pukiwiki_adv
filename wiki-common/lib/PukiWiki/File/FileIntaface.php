<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: FileInterface.php,v 1.0.0 2012/12/18 11:00:00 Logue Exp $
// Copyright (C)
//   2012 PukiWiki Advance Developers Team
// License: GPL v2 or (at your option) any later version
//

namespace PukiWiki\File;

/*
 * ファイルインターフェース
 */
interface FileIntaface{
	/**
	 * 存在確認
	 */
	public function has();
	/**
	 * 読み込み
	 */
	public function get($join = false);
	/**
	 * 書き込み
	 */
	public function set($str);
	/**
	 * ファイルサイズ
	 */
	public function size();
	/**
	 * MD5ハッシュ
	 */
	public function digest();
	/**
	 * 削除
	 */
	public function remove();
	/**
	 * 更新時刻
	 */
	public function getTime();
	/**
	 * 更新時間を指定
	 */
	public function setTime($value);
	/**
	 * 経過時間
	 */
	public function getPassage();
	/**
	 * アクセス時間
	 */
	public function getAtime();
	/**
	 * アクセス時間を指定
	 */
	public function setAtime($value);
	/**
	 * ファイルの中身
	 */
	public function __toString();
}

/* End of file FileInterface.php */
/* Location: /vender/PukiWiki/Lib/File/FileInterface.php */
