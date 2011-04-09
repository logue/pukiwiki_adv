<?php
// PukiPlus.
// $Id: referer.inc.php,v 1.10.14 2011/02/05 12:20:00 Logue Exp $
// Copyright (C)
//   2010-2011 PukiWiki Advance DevelopersTeam.
//   2007      PukiWiki Plus! Team
//   2003,2005-2008 Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
// License: GPL
//
// Referer Plugin(Show Related-Link Plugin)

define('CONFIG_REFERER', 'plugin/referer/config');
define('REFERE_TITLE_LENGTH',70);

function plugin_referer_init()
{
	$messages = array(
		'_referer_msg' => array(
			'msg_referer'			=> T_('Referer'),
			'msg_referer_list'		=> T_('Referer List'),
			'msg_no_data'			=> T_('No data'),
			'msg_H0_Refer'			=> T_('Referer'),
			'msg_Hed_LastUpdate'	=> T_('LastUpdate'),
			'msg_Hed_1stDate'		=> T_('First Register'),
			'msg_Hed_RefCounter'	=> T_('RefCounter'),
			'msg_Hed_Referer'		=> T_('Referer'),
			'msg_Fmt_Date'			=> T_('F j, Y, g:i A'),
			'msg_Chr_uarr'			=> T_('&uArr;'),
			'msg_Chr_darr'			=> T_('&dArr;'),
			'msg_disabled'			=> T_('Referer function is disabled.'),
			'msg_notfound'			=> T_('The page you requested was not found.'),
		),
	);
	set_plugin_messages($messages);
}

function plugin_referer_action()
{
	global $vars, $referer;
	global $_referer_msg;

	// Setting: Off
	if (! $referer) return array('msg'=>$_referer_msg['msg_referer'],'body'=>$_referer_msg['msg_disabled']);

	if (isset($vars['page']) && is_page($vars['page'])) {
		check_readable($vars['page'], false);
		$sort = (empty($vars['sort'])) ? '0d' : $vars['sort'];
		return array(
			'msg'  => $_referer_msg['msg_H0_Refer'],
			'body' => plugin_referer_body($vars['page'], $sort));
	}
	$pages = auth::get_existpages(REFERER_DIR, '.ref');

	if (empty($pages)) {
		return array('msg'=>$_referer_msg['msg_referer'], 'body'=>$_referer_msg['msg_notfound']);
	} else {
		return array(
			'msg'  => $_referer_msg['msg_referer_list'],
			'body' => page_list($pages, 'referer', FALSE));
	}
}

// Referer 明細行編集
function plugin_referer_body($page, $sort)
{
	global $script, $_referer_msg;
	global $referer;

	$data = ref_get_data($page);
	if (empty($data)) return '<p>'.$_referer_msg['no data'].'</p>';

	$bg = plugin_referer_set_color();

	$arrow_last = $arrow_1st = $arrow_ctr = '';
	$color_last = $color_1st = $color_ctr = $color_ref = $bg['etc'];
	$sort_last = '0d';
	$sort_1st  = '1d';
	$sort_ctr  = '2d';

	switch ($sort) {
	case '0d': // 0d 最終更新日時(新着順)
		usort($data, create_function('$a,$b', 'return $b[0] - $a[0];'));
		$color_last = $bg['cur'];
		$arrow_last = $_referer_msg['msg_Chr_darr'];
		$sort_last = '0a';
		break;
	case '0a': // 0a 最終更新日時(日付順)
		usort($data, create_function('$a,$b', 'return $a[0] - $b[0];'));
		$color_last = $bg['cur'];
		$arrow_last = $_referer_msg['msg_Chr_uarr'];
//		$sort_last = '0d';
		break;
	case '1d': // 1d 初回登録日時(新着順)
		usort($data, create_function('$a,$b', 'return $b[1] - $a[1];'));
		$color_1st = $bg['cur'];
		$arrow_1st = $_referer_msg['msg_Chr_darr'];
		$sort_1st = '1a';
		break;
	case '1a': // 1a 初回登録日時(日付順)
		usort($data, create_function('$a,$b', 'return $a[1] - $b[1];'));
		$color_1st = $bg['cur'];
		$arrow_1st = $_referer_msg['msg_Chr_uarr'];
//		$sort_1st = '1d';
		break;
	case '2d': // 2d カウンタ(大きい順)
		usort($data, create_function('$a,$b', 'return $b[2] - $a[2];'));
		$color_ctr = $bg['cur'];
		$arrow_ctr = $_referer_msg['msg_Chr_darr'];
		$sort_ctr = '2a';
		break;
	case '2a': // 2a カウンタ(小さい順)
		usort($data, create_function('$a,$b', 'return $a[2] - $b[2];'));
		$color_ctr = $bg['cur'];
		$arrow_ctr = $_referer_msg['msg_Chr_uarr'];
//		$sort_ctr = '2d';
		break;
	case '3': // 3 Referer
		usort($data, create_function('$a,$b',
			'return ($a[3] == $b[3]) ? 0 : (($a[3] > $b[3]) ? 1 : -1);'));
		$color_ref = $bg['cur'];
		break;
	}

	$body = '';
	$ctr = 0;
	foreach ($data as $arr) {
		// 0:最終更新日時, 1:初回登録日時, 2:参照カウンタ, 3:Referer ヘッダ, 4:利用可否フラグ(1は有効)
		list($ltime, $stime, $count, $url, $enable) = $arr;
		
		if ($count < 0) continue;

		// 項目不正の場合の対応
		// カウンタが数値ではない場合は、表示を抑止
		if (! is_numeric($count)) continue;

		$sw_ignore = plugin_referer_ignore_check($url);
		if ($sw_ignore && $referer > 1) continue;

		// 非ASCIIキャラクタ(だけ)をURLエンコードしておく BugTrack/440
		$e_url = htmlsc(preg_replace('/([" \x80-\xff]+)/e', 'rawurlencode("$1")', $url));
		$s_url = htmlsc(mb_convert_encoding(rawurldecode($url), SOURCE_ENCODING, 'auto'));
		$s_url = mb_strimwidth($s_url,0,REFERE_TITLE_LENGTH,'...');

		$lpass = get_passage($ltime, FALSE); // 最終更新日時からの経過時間
		$spass = get_passage($stime, FALSE); // 初回登録日時からの経過時間
		$ldate = get_date($_referer_msg['msg_Fmt_Date'], $ltime); // 最終更新日時文字列
		$sdate = get_date($_referer_msg['msg_Fmt_Date'], $stime); // 初回登録日時文字列

		$body .=
			'		<tr>' . "\n" .
			'			<td class="style_td">' . $ldate . ' ('. $lpass .')</td>' . "\n";

		$body .= ($count == 1) ?
			'			<td class="style_td">N/A</td>' . "\n" :
			'			<td class="style_td">' . $sdate .' ('. $spass .')</td>' . "\n";

		$body .= '			<td class="style_td" style="text-align:right;">' . $count . '</td>' . "\n";

		// 適用不可データのときはアンカーをつけない
		$body .= ($sw_ignore) ?
			'			<td class="style_td">' . $s_url . '</td>' . "\n" :
			'			<td class="style_td"><a href="' . $e_url . '" rel="nofollow noreferer">' . $s_url . '</a></td>' . "\n";

		$body .= '		</tr>' . "\n";
		$ctr++;
	}

	if ($ctr === 0) return '<p>no data.</p>';

	$href = $script . '?cmd=referer&amp;page=' . rawurlencode($page);
	return <<<EOD
<table summary="Referer" class="style_table">
	<thead>
		<tr>
			<th class="style_th">
				 <a href="$href&amp;sort=$sort_last">{$_referer_msg['msg_Hed_LastUpdate']}$arrow_last</a>
			</th>
			<th class="style_th">
				<a href="$href&amp;sort=$sort_1st">{$_referer_msg['msg_Hed_1stDate']}$arrow_1st</a>
			</th>
			<th class="style_th" style="text-align:right">
				<a href="$href&amp;sort=$sort_ctr">{$_referer_msg['msg_Hed_RefCounter']}$arrow_ctr</a>
			</th>
			<th class="style_th">
				<a href="$href&amp;sort=3">{$_referer_msg['msg_Hed_Referer']}</a>
			</th>
		</tr>
	</thead>
	<tbody>
$body
	</tbody>
</table>
EOD;
}

function plugin_referer_set_color()
{
	static $color;

	if (! isset($color)) {
		// Default color
		$color = array('cur' => '#99CCFF', 'etc' => 'transparent');

		$config = new Config(CONFIG_REFERER);
		$config->read();
		$pconfig_color = $config->get('COLOR');
		unset($config);

		$matches = array();
		foreach ($pconfig_color as $x)
			$color[$x[0]] = htmlsc(
				preg_match('/BGCOLOR\(([^)]+)\)/si', $x[1], $matches) ?
					$matches[1] : $x[1]);
	}
	return $color;
}

function plugin_referer_ignore_check($url)
{
	static $ignore_url;

	// config.php
	if (! isset($ignore_url)) {
		$config = new Config(CONFIG_REFERER);
		$config->read();
		$ignore_url = $config->get('IGNORE');
		unset($config);
	}

	foreach ($ignore_url as $x)
		if (strpos($url, $x) !== FALSE)
			return 1;
	
	return 0;
}
?>
