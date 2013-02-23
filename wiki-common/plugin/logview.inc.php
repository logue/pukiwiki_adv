<?php
/**
 * PukiWiki Advance ログ閲覧プラグイン
 *
 * @copyright	Copyright (c) 2010-2011 PukiWiki Advance Developers Team.
 *                            2004-2009, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @version	$Id: logview.php,v 0.25 2011/02/05 11:03:00 Logue Exp $
 * @license	http://opensource.org/licenses/gpl-license.php GNU Public License (GPL2)
 */
use PukiWiki\Auth\Auth;

defined('MAX_LINE')      or define('MAX_LINE', 200);
defined('VIEW_ROBOTS')   or define('VIEW_ROBOTS', '0');   // robots は表示しない
defined('USE_UA_OPTION') or define('USE_UA_OPTION', '0'); // オプション
defined('PLUGIN_LOGVIEW_COLOR_AUTH_API')   or define('PLUGIN_LOGVIEW_COLOR_AUTH_API','#009900');
defined('PLUGIN_LOGVIEW_DISPLAY_AUTH_API') or define('PLUGIN_LOGVIEW_DISPLAY_AUTH_API','1');

/**
 * 初期処理
 */
function plugin_logview_init()
{
	$messages = array(
		'_logview_msg' => array(
			'msg_title'		=> T_('LogView (%s)'),
			'msg_not_auth'	=> T_('Login is required in order to refer to.'),
			'ts'			=> T_('Date'),
			'ip'			=> T_('IP Address'),
			'host'			=> T_('Host Name'),
			'auth_api'		=> T_('Authentication API Name'),
			'user'			=> T_('User Name'),
			'ntlm'			=> T_('NTLM Auth Name'),
			'proxy'			=> T_('Proxy Infomation'),
			'ua'			=> T_('Browse Infomation'),
			'del'			=> T_('Delete'),
			'sig'			=> T_('Signature'),
			'file'			=> T_('Faile Name'),
			'page'			=> T_('Page'),
			'cmd'			=> T_('CMD'),
			'local_id'		=> T_('local_id'),
			'@diff'			=> T_('Contents'),
			'@guess'		=> T_('Provisional User Name'),	// Guess
			'@guess_diff'	=> T_('Provisional Browse Contents'),  // Guess
			'info_unused'	=> T_('Unused user list'),
			'all_user'		=> T_('Number of enrollees'),
			'number_unused'	=> T_('Number of Unused'),
			'availability'	=> T_('Availability'),
			'msg_nodata'	=> T_('No entry.')
		),
		'_logview_logname' => array(
			'update'		=> T_('Update Log'),
			'download'		=> T_('Download Log'),
			'browse'		=> T_('Browse Log'),
			'cmd'			=> T_('Command Log'),
			'login'			=> T_('Login Log'),
			'check'			=> T_('Checking Log')
		)
	);
	set_plugin_messages($messages);
}

/**
 * アクションプラグイン処理
 */
function plugin_logview_action()
{
	global $vars, $_logview_msg, $_logview_logname;
	global $log, $sortable_tracker,$_LANG, $vars;
	static $count = 0;

	$kind = (isset($vars['kind'])) ? $vars['kind'] : null;
	$title =($kind !== null) ? sprintf($_logview_msg['msg_title'],$kind) : $_LANG['skin']['log']; // タイトルを設定
	$page = (isset($vars['page'])) ? $vars['page'] : null;
	
	$ajax = (isset($vars['ajax'])) ? $vars['ajax'] : null;
	$is_role_adm = Auth::check_role('role_adm');

	// ゲスト表示ができない場合は、認証を要求する
	if ($kind !== null && empty($log[$kind]['guest'])) {
		$obj = new Auth();
		$user = $obj->check_auth();
		if (empty($user)) {
			if (exist_plugin('login')) {
				do_plugin_action('login');
			}
			unset($obj);
			return array(
				'msg'  => $title,
				'body' => $_logview_msg['msg_not_auth'],
			);
		}
	}
	unset($obj);

	check_readable($page, false);
	if ($kind === null){
		if (!IS_MOBILE) {
			$body = '<div class="tabs" role="application">'."\n";
			$body .= '<ul role="tablist">';
			$cnt = 0;
			foreach($log as $key=>$val){
				$link_text = isset($_logview_logname[$key]) ? $_logview_logname[$key] : $key;
				if ($val['use'] === 1){
					$body .= '<li role="tab"><a href="'.get_cmd_uri('logview',$page,null,array('kind'=>$key)).'">'.$link_text.'</a></li>';
				}
/*
				else
				{
					$body .= '<li><a href="'.get_cmd_uri('logview',$page,null,array('kind'=>$key)).'" data-ajax="raw" data-disabled="true">'.$link_text.'</a></li>';
				}
*/
			}
			$body .= '</ul>'."\n";
			
			if ($kind === null) return array('msg'  => $title,'body' => $body);
			
			$body .= '<div class="no-js" role="tabpanel">';
			$nodata = $body.'<p>'.$_logview_msg['msg_nodata'].'</p></div></div>';
		}else{
			$body = '<div data-role="controlgroup" data-type="horizontal">'."\n";
			$cnt = 0;
			foreach($log as $key=>$val){
				$link_text = isset($_LANG['skin']['log_'.$key]) ? $_LANG['skin']['log_'.$key] : $key;
				if ($val['use'] === 1){
					$body .= '<a href="'.get_cmd_uri('logview',$page,null,array('kind'=>$key)).'" data-role="button">'.$link_text.'</a>';
				}
/*
				else
				{
					$body .= '<a href="'.get_cmd_uri('logview',$page,null,array('kind'=>$key)).'" data-ajax="raw" data-disabled="true">'.$link_text.'</a>';
				}
*/
			}
			$body .= '</div>'."\n". '<div class="ui-body ui-body-c"></div>';
			
			if ($kind === null) return array('msg'  => $title,'body' => $body);
		
		}
	}else{
		$body = '';
		$nodata = '<p>'.$_logview_msg['msg_nodata'].'</p>';
	}

	// 保存データの項目名を取得
	$name = log::get_log_field($kind);
	$view = log::get_view_field($kind); // 表示したい項目設定
	

	$count++;
	$body .= <<<EOD
<div class="table_wrapper">
<table class="style_table table_logview" data-pagenate="true">
<thead>
<tr>

EOD;
	$cols = 0;
	

	// タイトルの処理
	foreach ($view as $_view) {
		if ($_view === 'local_id' && $is_role_adm) continue;
		$body .= '<th class="style_th">'.$_logview_msg[$_view].'</th>'."\n";
		$cols++;
	}

	$body .= <<<EOD
</tr>
</thead>
<tbody>
EOD;

	// データを取得
	$fld = logview_get_data(log::set_filename($kind,$page), $name);

	if (empty($fld)) {
		return array(
			'msg'  => $title,
			'body' => $nodata,
		);
	}

	// USER-AGENT クラス
	$obj_ua = new user_agent(USE_UA_OPTION);
/*
	$path_flag    = IMAGE_URI .'plugin/logview/flags/';
	$path_browser = IMAGE_URI .'plugin/logview/browser/';
	$path_os      = IMAGE_URI .'plugin/logview/os/';
	$path_domain  = IMAGE_URI .'plugin/logview/option/domain/';
*/
	$guess = ($log['guess_user']['use']) ? log::read_guess() : log::summary_signature();

	$ctr = 0;
	// データの編集
	foreach($fld as $data) {
		if (!VIEW_ROBOTS && $obj_ua->is_robots($data['ua'])) continue;	// ロボットは対象外

		$body .= "<tr>\n";

		foreach ($view as $field) {
			switch ($field) {
			case 'ts': // タイムスタンプ (UTIME)
				$body .= ' <td class="style_td">' .
					get_date('Y-m-d H:i:s', $data['ts']) .
					' '.get_passage($data['ts']) . "</td>\n";
				break;

			case '@guess_diff':
			case '@diff': // 差分内容
				$update = ($field == '@diff') ? true : false;
				// FIXME: バックアップ/差分 なしの新規の場合
				// バックアップデータの確定
				$body .= ' <td class="style_td">';
				$age = log::get_backup_age($page,$data['ts'],$update);
				switch($age) {
				case -1: // データなし
					$body .= '<a class="ext" href="'.get_page_uri($page).
						'" rel="nofollow">none</a>';
					break;
				case 0:  // diff
					$body .= '<a class="ext" href="';
					$body .= (log::diff_exist($page)) ? get_cmd_uri('diff',$page) : get_page_uri($page);
					$body .= '" rel="nofollow">now</a>';
					break;
				default: // あり
					$body .= '<a class="ext" href="'.get_cmd_uri('backup',$page,'',array('age'=>$age,'action'=>'visualdiff')).'"'.
						' rel="nofollow">'.$age.'</a>';
					break;
				}
				$body .= '</td>'."\n";
				break;

			case 'host': // ホスト名 (FQDN)
				$body .= ' <td class="style_td host">';
				if ($data['ip'] != $data['host']) {
					// 国名取得
					list($flag_icon,$flag_name) = $obj_ua->get_icon_flag($data['host']);
					if (!empty($flag_icon) && $flag_icon != 'jp') {
						$body .= '<span class="flag flag-'.$flag_icon.'" title="'.$flag_name.'" ></span>';
					}
					// ドメイン取得
					$domain = $obj_ua->get_icon_domain($data['host']);
					if (!empty($domain)) {
//						$body .= '<img src="'.$path_domain.$domain.'.png"'.
//								' alt="'.$data['host'].'" title="'.$data['host'].'" />';
						$body .= '<span class="flag flag-'.$domain.'" title="'.$data['host'].'" ></span>';
					}
				}
				if ($data['ip'] !== '::1'){
					$body .= '<a href="http://robtex.com/ip/'.$data['ip'].'.html" rel="external nofollow">'.$data['host'].'</a></td>'."\n";
				}else{
					$body .= $data['host'].'</td>'."\n";
				}
				break;

			case '@guess': // 推測
				$body .= ' <td class="style_td">'.htmlsc(logview_guess_user($data, $guess), ENT_QUOTES)."</td>\n";
				break;

			case 'ua': // ブラウザ情報 (USER-AGENT)
				$body .= ' <td class="style_td">';
				$os = $obj_ua->get_icon_os($data['ua']);
				if (!empty($os)) {
//					$body .= '<img src="'.$path_os.$os.'.png"'.
//						' alt="'.$os.'" title="'.$os.'" />';
					$body .= '<span class="os os-'.$os.'" title="'.$os.'"></span>';
				}
				$browser = $obj_ua->get_icon_broeswes($data['ua']);
				if (!empty($browser)) {
					$s_ua = htmlsc($data['ua'], ENT_QUOTES);
//					$body .= '<img src="'.$path_browser.$browser.'.png"'.
//						' alt="'.htmlsc($data['ua'], ENT_QUOTES).
//						'" title="'.htmlsc($data['ua'], ENT_QUOTES).
//						'" />';
					$body .= '<span class="browser browser-'.$browser.'" title="'.$s_ua.'"></span>';
				}
				$body .= "</td>\n";
				break;

			case 'local_id':
				if ($is_role_adm) continue;
			default:
				$body .= ' <td class="style_td">'.htmlsc($data[$field], ENT_QUOTES)."</td>\n";
			}
		}

		$body .= '</tr>'."\n";
		$ctr++;
	}

	unset($obj_ua);

	if ($ctr == 0) {
		return array(
			'msg'  => $title,
			'body' => $nodata,
		);
	}

	$body .= <<<EOD
</tbody>
</table>
</div>
EOD;

	switch ($kind) {
		case 'login':
		case 'check':
			$body .= logview_user_list($fld,$page,$kind);
			break;
	}
	
	if ($ajax !== 'raw'){
		$body .= '</div></div>';
	}else{
		echo $body;
		exit();
	}

	return array(
		'msg'  => $title,
		'body' => $body
	);
}

function logview_get_data($filename,$name)
{
	if (! file_exists($filename)) {
		return array();
	}

	$rc = array();
	$fp = @fopen($filename, 'r');
	if ($fp == FALSE) return $rc;
	@flock($fp, LOCK_SH);

	$count = 0;
	while (! feof($fp)) {
		$line = fgets($fp, 512);
		if ($line === FALSE) continue;
		$rc[] = log::line2field($line,$name);
                ++$count;
		if ($count > MAX_LINE) {
			// 古いデータを捨てる
			array_shift($rc);
		}
	}

	@flock($fp, LOCK_UN);
	if(! fclose($fp)) return array();
	rsort($rc); // 逆順にソート(最新順になる)
	return $rc;
}

/**
 * ユーザ名推測
 */
function logview_guess_user($data,$guess)
{
	// 確定的な情報
	$user  = (isset($data['user'])) ? $data['user'] : '';
	$ntlm  = (isset($data['ntlm'])) ? $data['ntlm'] : '';
	$sig   = (isset($data['sig']))  ? $data['sig']  : '';
	$now_user = log::guess_user($user,$ntlm,$sig);
	if (!empty($now_user)) return $now_user;

	// 見做し
	if (!isset($data['ua']))   return '';
	if (!isset($guess[$data['ua']])) return ''; // USER-AGENT が一致したデータがあるか
	if (!isset($data['host'])) return '';

	$user = '';
	$level = 0; // とりあえずホスト名は完全一致

	foreach($guess[$data['ua']] as $_host => $val1) {
		list($sw,$lvl) = log::check_host($data['host'],$_host,$level); // ホスト名の一致確認
		if (!$sw) continue; // ホスト名が一致しない

		// UA が等しく、同じIPなものの、複数ユーザまたは改変した場合は、複数人分出力
		foreach($val1 as $_user => $val2) {
			if (!empty($user)) $user .= ' / ';
			$user .= $_user;
		}
	}
	return $user;
}

function logview_user_list(& $fld, $page,$kind)
{
	global $_logview_msg;

	// 登録ユーザ以上でなければ、他のユーザを知ることは禁止
	if (Auth::check_role('role_enrollee')) return '';
	$wiki = Factory::Wiki($page);

	$all_user = Auth::user_list();
	$all_user_idx = 0;
	$excludes_user = array();
	foreach ($all_user as $auth_api=>$val1) {
	foreach ($val1 as $user=>$val) {
		$group = empty($val['group']) ? '' : $val['group'];
		if ($kind != 'login' && Auth::checkPermission($page, 'read',$user,$group)) {
			$excludes_user[$auth_api][$user] = '';
			continue;
		}
		$all_user_idx++;
	}}

	$user_list = array();
	foreach($fld as $line) {
		$user_list[$line['auth_api']][$line['user']] = '';
	}

	$check_list = array();
	foreach($all_user as $auth_api=>$val1) {
	foreach($val1 as $user=>$val) {
		if (isset($user_list[$auth_api][$val['displayname']])) continue;
		if (isset($excludes_user[$auth_api][$user])) continue;
		$check_list[] = array('name'=>$val['displayname'],'auth_api'=>$auth_api);
	}}

	$ctr = count($check_list);
	if ($ctr == 0) return '';

	$ret = '<fieldset>'."\n".'<legend>'.$_logview_msg['info_unused'].'</legend>'."\n"; // 未確認者一覧
	$ret .= '<div>'.$_logview_msg['all_user'].': '.$all_user_idx.' '.
			$_logview_msg['number_unused'].': '.$ctr.' '.
			$_logview_msg['availability'].': '.floor(100-($ctr/$all_user_idx)*100).'%</div></fieldset>'."\n"; // 人数

	sort($check_list);
	$ctr = 0;
	foreach($check_list as $user) {
		$ctr++;
		$ret .= '<small><strong>'.$ctr.'.</strong></small>'.$user['name'];
		if (PLUGIN_LOGVIEW_DISPLAY_AUTH_API) {
			$ret .= ' <small><span style="color: '.PLUGIN_LOGVIEW_COLOR_AUTH_API.'">'.$user['auth_api'].'</span></small>';
		}
		$ret .= "\n";
	}

	return $ret;
}

/* End of file logview.inc.php */
/* Location: ./wiki-common/plugin/logview.inc.php */