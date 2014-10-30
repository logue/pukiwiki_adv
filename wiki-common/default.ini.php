<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: default.ini.php,v 1.25.16 2012/04/29 13:22:00 Logue Exp $
// Copyright (C)
//   2010-2014 PukiWiki Advance Developers Team
//   2005-2006,2009 PukiWiki Plus! Team
//   2003-2005 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// PukiWiki setting file (user agent:default)

defined('PLUS_THEME') or define('PLUS_THEME','default');
defined('SKIN_FILE_DEFAULT') or define('SKIN_FILE_DEFAULT', PLUS_THEME);

/////////////////////////////////////////////////
// Skin file
$skin_file = isset($_COOKIE['skin_file']) ? $_COOKIE['skin_file'] : SKIN_FILE_DEFAULT;

/////////////////////////////////////////////////
// 雛形とするページの読み込みを可能にする(1:する 0:しない)
$load_template_func = 0;

/////////////////////////////////////////////////
// WikiName,BracketNameに経過時間を付加する
$show_passage = 0;

/////////////////////////////////////////////////
// リンク表示をコンパクトにする
// * ページに対するハイパーリンクからタイトルを外す
// * Dangling linkのCSSを外す
$link_compact = 0;

/////////////////////////////////////////////////
// クッキーを使用できないアドレス
// (通常、デスクトップでは存在しない)
$use_trans_sid_address = array(
);


/* End of file default.ini.php */
/* Location: ./wiki-common/default.ini.php */