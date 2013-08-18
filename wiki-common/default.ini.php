<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: default.ini.php,v 1.25.16 2012/04/29 13:22:00 Logue Exp $
// Copyright (C)
//   2010-2012 PukiWiki Advance Developer Team
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
// 元ページのリンクを自動的に先頭につける(1:つける 0:つけない)
$load_refer_related = 0;

/////////////////////////////////////////////////
// 検索文字列を色分けする(1:する 0:しない)
$search_word_color = 1;

/////////////////////////////////////////////////
// 特殊シンボル
$_symbol_paraedit		= '<span class="pkwk-symbol symbol-edit" title="Edit here"></span>';
$_symbol_attach			= '<span class="pkwk-symbol symbol-attach" title="Attach here"></span>';

/////////////////////////////////////////////////
// 添付ファイルの一覧を常に表示する (負担がかかります)
$attach_link = 1;

/////////////////////////////////////////////////
// 関連するページのリンク一覧を常に表示する(負担がかかります)
$related_link = 1;

/////////////////////////////////////////////////
// 脚注機能関連

// 脚注のアンカーを相対パスで表示する (0 = 絶対パス)
//  * 相対パスの場合、以前のバージョンのOperaで問題になることがあります
//  * 絶対パスの場合、calendar_viewerなどで問題になることがあります
// (詳しくは: BugTrack/698)
define('PKWK_ALLOW_RELATIVE_FOOTNOTE_ANCHOR', 1);

/////////////////////////////////////////////////
// WikiName,BracketNameに経過時間を付加する
$show_passage = 1;

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