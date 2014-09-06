<?php
// PukiWiki Advance - Yet another WikiWikiWeb clone.
// $Id: default.ini.php,v 1.25.17 2012/03/31 16:49:00 Logue Exp $
// Copyright (C)
//   2010-2014 PukiWiki Advance Developer Team
//   2005-2006,2009 PukiWiki Plus! Team
//   2003-2005 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// PukiWiki setting file (user agent:mobile)
/////////////////////////////////////////////////
// Skin file
define('IS_MOBILE',true);

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
// 添付ファイルの一覧を常に表示する (負担がかかります)
$attach_link = 0;

/////////////////////////////////////////////////
// 関連するページのリンク一覧を常に表示する(負担がかかります)
$related_link = 0;

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

/////////////////////////////////////////////////

/* End of file mobile.ini.php */
/* Location: ./wiki-common/mobile.ini.php */