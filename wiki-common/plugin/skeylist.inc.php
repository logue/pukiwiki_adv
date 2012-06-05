<?php
/**
 * PukiWiki 人気検索キープラグイン
 *
 * @copyright   Copyright &copy; 2004-2009, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @version     $Id: skeylist.inc.php,v 0.14 2011/02/05 12:40:00 Logue Exp $
 * @license     http://opensource.org/licenses/gpl-license.php GNU Public License (GPL2)
 *
 */

// 検索エンジン
// Google
defined('SKEYLIST_SEARCH_URL') or define('SKEYLIST_SEARCH_URL', 'http://www.google.com/search?ie=utf8&amp;oe=utf8&amp;q=');
// Yahoo!
// defined('SKEYLIST_SEARCH_URL') or define('SKEYLIST_SEARCH_URL', 'http://search.yahoo.com/search?ei=UTF-8&p=');

defined('SKEYLIST_MIN_COUNTER') or define('SKEYLIST_MIN_COUNTER', 0);

function plugin_skeylist_init()
{
	$msg = array(
		'_skeylist_msg' => array(
			'h5_title'		=> T_('Search Key Top %d'),
			'title'			=> T_('All the Search Key of %s'),
			'not_effective'	=> T_('The function of Referer is not effective.'),
			'no_data'		=> T_('no data.'),
		)
	);
	set_plugin_messages($msg);
}

function plugin_skeylist_action()
{
	global $vars;
	global $_skeylist_msg;
	global $referer;

	$page = (empty($vars['page'])) ? '' : htmlsc($vars['page'], ENT_QUOTES);
	check_readable($page, false);
	$retval['msg']  = sprintf($_skeylist_msg['title'],$page);
	if (! $referer) {
		$retval['body'] = '<p>'.$_skeylist_msg['not_effective']."</p>\n";
		return $retval;
	}

	$max  = (empty($vars['max']))  ? -1 : htmlsc($vars['max'], ENT_QUOTES);
	// $data = tb_get(tb_get_filename($page,'.ref'));
	$data = ref_get_data($page);

	//  データ無し
	if (count($data) == 0)
	{
		$retval['body'] = '<p>'.$_skeylist_msg['no_data']."</p>\n";
		return $retval;
	}

	$data = skeylist_analysis($data);
	//pr($data);
	// 0:検索キー 1:参照カウンタ
	usort($data,create_function('$a,$b','return $b[1] - $a[1];'));
	$data = skeylist_print($data,$max);

	$retval['body'] = (empty($data)) ? $_skeylist_msg['no_data'] : $data;
	return $retval;
}

function plugin_skeylist_convert()
{
	global $vars;
	global $_skeylist_msg;
	global $referer;

	if (! $referer) return '';

	list($page,$max) = func_get_args();
	if (empty($page)) $page = htmlsc($vars['page'], ENT_QUOTES);
	check_readable($page, false);
	$max = (empty($max)) ? 10 : htmlsc($max, ENT_QUOTES);

	// $data = tb_get(tb_get_filename($page,'.ref'));
	$data = ref_get_data($page);
	if (count($data) == 0) return ''; //  データ無し
	$data = skeylist_analysis($data);
	// 0:検索キー 1:参照カウンタ
	usort($data,create_function('$a,$b','return $b[1] - $a[1];'));
	$data = skeylist_print($data,$max);
	return '<div>'.$data."</div>\n";
}



// データを解析
function skeylist_analysis($data)
{
	// 検索エンジンがキーワードとして使うキー
	$WordsToExtractSearchUrl = array(
		'q',		// google, msn, earthlink
		'p',		// yahoo
		'MT',		// goo
		'kw',		// fresheye
		'query',	// aol
		'search',	// excite
		'qt',		// infoseek, looksmart
		'Text',		// nifty
		'QueryString',	// www.looksmart.co.jp
		'key',		// findarticles
		'qkw',		// nbci
		'su',		// web.de
		'qry',		// www.mirago.co.uk
		'w',		// seznam.cz
		'name',		// JWord
		'wd'		// Baidu
	);

	$sum = array();

	// 0:最終更新日時 1:初回登録日時 2:参照カウンタ 3:Referer ヘッダ 4:利用可否フラグ(1は有効)
	foreach ($data as $x)
	{
		if ($x[4] === 1) continue;
		// 'scheme', 'host', 'port', 'user', 'pass', 'path', 'query', 'fragment'
		$url = parse_url($x[3]);
		if (empty($url['query'])) continue; // queryストリングが空の場合は対象外

		// queryストリングの解析
		$tok = strtok($url['query'],'&');
		while($tok) {
			$key_val = explode('=', $tok); // キーと値に分割
			$key = (isset($key_val[0])) ? $key_val[0] : '';
			$parm = (isset($key_val[1])) ? $key_val[1] : '';
			
			$tok = strtok('&'); // 次の処理の準備

			// 検索キーかの判定
			$skey = '';
			foreach ($WordsToExtractSearchUrl as $y)
			{
				if ( (strpos($key,$y) === 0 )) {
					$skey = $y;
					continue;
				}
			}
			if ($skey !== $key) continue;

			if (empty($parm)) continue; // 値が入っていない場合
			if ( (strpos($parm,'cache:') === 0 )) continue; // google のキャッシュなどの場合

			$parm = skeylist_convert_key($parm, $url['host']); // 検索キーを名寄せする
			if (!isset($sum[$parm])){ $sum[$parm] = 0; }
			$sum[$parm] += $x[2]; // 参照カウンタ
			break;
		}
	}

	$rc = array();
	$i = 0;
	foreach ($sum as $key=>$val)
	{
		$rc[$i][0] = $key;	// 検索キー
		$rc[$i][1] = $val;	// 参照カウンタ
		$i++;
	}
	return $rc;
}

// 検索キーを整形する
function skeylist_convert_key($x, $host)
{
	$rc = '';

	// テーブルに存在する検索エンジンで、指定キーが存在している場合
	$x = rawurldecode($x);
	if ($host === 'www.baidu.com'){
		$x = mb_convert_encoding($x,SOURCE_ENCODING ,'GB2312');	// Baiduは、GB2312で処理しているため
	}else{
		$x = mb_convert_encoding($x,SOURCE_ENCODING,'auto');
	}

	// "K" : 「半角片仮名」を「全角片仮名」に変換
	// "V" :  濁点付きの文字を一文字に変換
	// "a" : 「全角」英数字を「半角」に変換
	// "s" : 「全角」スペースを「半角」に変換
	$x = mb_convert_kana($x, 'KVas');

	// Yahooなど他のエンジン対応
	$x = str_replace(
		array('+', '#', '*', ' and ', ' AND ', '|', '?'),
		' ',
		$x
	);
	$x = str_replace('"', '', $x);  // 	"は除去		"

	// 文字の途中に入っている連続するスペースを１つにする
	$tok = strtok($x,' ');
	while($tok) {
		$rc .= $tok.' ';
		$tok = strtok(' ');
	}

	// 前後のスペースを取り除く
	$rc = trim($rc);
	return $rc;
}

// データを加工
function skeylist_print($data,$max)
{
	global $_skeylist_msg;
	$rc = array();

	if ($max > 0) {
		$rc[] = '<h5>'.sprintf($_skeylist_msg['h5_title'],$max).'</h5>';
		$data = array_splice($data,0,$max);
	}
	$rc[] = '<ul>';

	foreach ($data as $x)
	{
		if (SKEYLIST_MIN_COUNTER > $x[1]) continue;
		if ( !strcasecmp('utf-8',SOURCE_ENCODING) ) {
			$key = $x[0];
		} else {
			$key = mb_convert_encoding($x[0],'utf-8',SOURCE_ENCODING);
		}
		$rc[] = '<li><a href="' . SKEYLIST_SEARCH_URL.rawurlencode($key).'" rel="nofollow noreferer">'.$x[0].'</a> <var>('.$x[1].')</var></li>';
	}

	$rc[] = '</ul>';
	return join("\n",$rc);
}
?>
/* End of file skeylist.inc.php */
/* Location: ./wiki-common/plugin/skeylist.inc.php */