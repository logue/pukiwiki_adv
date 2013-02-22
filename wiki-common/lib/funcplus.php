<?php
// PukiPlus.
// $Id: funcplus.php,v 0.1.65 2011/09/11 23:01:00 Logue Exp $
// Copyright (C)
//   2010-2011 PukiWiki Advance Developers Team <http://pukiwiki.logue.be/>
//   2005-2009 PukiWiki Plus! Team
// License: GPL v2 or (at your option) any later version
//
// Plus! extension function(s)

defined('FUNC_SPAMLOG')		or define('FUNC_SPAMLOG', TRUE);
defined('FUNC_BLACKLIST')	or define('FUNC_BLACKLIST', TRUE);
defined('FUNC_SPAMREGEX')	or define('FUNC_SPAMREGEX', '#(?:cialis|hydrocodone|viagra|levitra|tramadol|xanax|\[/link\]|\[/url\])#i');
defined('FUNC_SPAMCOUNT')	or define('FUNC_SPAMCOUNT', 2);


function get_fancy_uri()
{
	$script  = (SERVER_PORT == 443 ? 'https://' : 'http://'); // scheme
	$script .= SERVER_NAME; // host
	$script .= (SERVER_PORT == 80 || SERVER_PORT == 443) ? '' : ':' . SERVER_PORT;  // port

	// SCRIPT_NAME が'/'で始まっていない場合(cgiなど) REQUEST_URIを使ってみる
	$path	= SCRIPT_NAME;
	$script .= $path; // path

	return $script;
}

function load_init_value($name,$must=0)
{
	$init_dir = array(INIT_DIR, SITE_INIT_DIR);
	$read_dir = array();
	$init_data = $name . '.ini.php';

	// Exclusion of repetition definition
	foreach($init_dir as $val) { $read_dir[$val] = ''; }

	foreach($read_dir as $key=>$val) {
		if (file_exists($key.$init_data)) {
			if ($must)
				require_once($key.$init_data);
			else
				include_once($key.$init_data);
			return TRUE;
		}
	}

	return FALSE;
}

function add_homedir($file)
{
	foreach(array(DATA_HOME,SITE_HOME) as $dir) {
		if (file_exists($dir.$file) && is_readable($dir.$file)) return $dir.$file;
	}
	return $file;
}

function add_skindir($skin_name)
{
	$cond = array(
		SKIN_DIR.THEME_PLUS_NAME.$skin_name.'/',
		EXT_SKIN_DIR.THEME_PLUS_NAME.$skin_name.'/'
	);

	$file = basepagename($skin_name).'.skin.php';
	$conf = basepagename($skin_name).'.ini.php';

	foreach($cond as $dir){
		if (file_exists($dir.$file) && is_readable($dir.$file)){
			// スキンが見つかった場合
			if ( file_exists($dir.$conf) && is_readable($dir.$conf)){
				// スキンのオーバーライド設定ファイルが存在する場合、それを読み取る。
				require_once $dir.$conf;
			}
			return $dir.$file;
		}
	}

	die_message('Skin File:<var>'.$skin_name.'</var> is not found or not readable. Please check <var>SKIN_DIR</var> value. (NOT <var>SKIN_URI</var>. )');
}


function is_localIP($ip)
{
	static $localIP = array('127.0.0.0/8','10.0.0.0/8','172.16.0.0/12','192.168.0.0/16');
	if (is_ipaddr($ip) === FALSE) return FALSE;
	return ip_scope_check($ip,$localIP);
}

function is_ipaddr($ip)
{
	$valid = ip2long($ip);
	return ($valid == -1 || $valid == FALSE) ? FALSE : $valid;
}

// IP の判定
function ip_scope_check($ip,$networks)
{
	// $l_ip = ip2long( ip2arrangement($ip) );
	$l_ip = ip2long($ip);
	foreach($networks as $network) {
		$range = explode('/', $network);
		$l_network = ip2long( ip2arrangement($range[0]) );
		// $l_network = ip2long( $range[0] );
		if (empty($range[1])) $range[1] = 32;
		$subnetmask = pow(2,32) - pow(2,32 - $range[1]);
		if (($l_ip & $subnetmask) == $l_network) return TRUE;
	}
	return FALSE;
}

// ex. ip=192.168.101.1 from=192.168.0.0 to=192.168.211.12
function ip_range_check($ip,$from,$to)
{
	if (empty($to)) return ip_scope_check($ip,array($from));
		$l_ip = ip2long($ip);
		$l_from = ip2long( ip2arrangement($from) );
		$l_to = ip2long( ip2arrangement($to) );
		return ($l_from <= $l_ip && $l_ip <= $l_to);
}

// ex. 10 -> 10.0.0.0, 192.168 -> 192.168.0.0
function ip2arrangement($ip)
{
	$x = explode('.', $ip);
	if (count($x) == 4) return $ip;
	for($i=0;$i<4;$i++) { if (empty($x[$i])) $x[$i] =0; }
	return sprintf('%d.%d.%d.%d',$x[0],$x[1],$x[2],$x[3]);
}

// 予約されたドメイン
function is_ReservedTLD($host)
{
	// RFC2606
	static $ReservedTLD = array('example' =>'','invalid' =>'','localhost'=>'','test'=>'',);
	$x = array_reverse(explode('.', strtolower($host) ));
	return (isset($ReservedTLD[$x[0]])) ? TRUE : FALSE;
}

function path_check($url1,$url2)
{
	$u1 = parse_url(strtolower($url1));
	$u2 = parse_url(strtolower($url2));

	// http = https とする
	if (!empty($u1['scheme']) && $u1['scheme'] == 'https') $u1['scheme'] = 'http';
	if (!empty($u2['scheme']) && $u2['scheme'] == 'https') $u2['scheme'] = 'http';

	// path の手当て
	if (!empty($u1['path'])) {
		$u1['path'] = substr($u1['path'],0,strrpos($u1['path'],'/'));
	}
	if (!empty($u2['path'])) {
		$u2['path'] = substr($u2['path'],0,strrpos($u2['path'],'/'));
	}

	foreach(array('scheme','host','path') as $x) {
		$u1[$x] = (empty($u1[$x])) ? '' : $u1[$x];
		$u2[$x] = (empty($u2[$x])) ? '' : $u2[$x];
		if ($u1[$x] == $u2[$x]) continue;
		return FALSE;
	}
	return TRUE;
}

// Check CGI/CLI(true) or MOD_PHP(false)
function is_sapi_clicgi()
{
	$sapiname = php_sapi_name();
	if ($sapiname == 'cgi' || $sapiname == 'cli')
		return TRUE;
	return FALSE;
}


function get_mimeinfo($filename)
{
	$type = '';
	if (function_exists('finfo_open')) {
		$finfo = finfo_open(FILEINFO_MIME);
		if (!$finfo) return $type;
		$type = finfo_file($finfo, $filename);
		finfo_close($finfo);
		return $type;
	}

	if (function_exists('mime_content_type')) {
		$type = mime_content_type($filename);
		return $type;
	}

	// PHP >= 4.3.0
	$filesize = @getimagesize($filename);
	if (is_array($filesize) && preg_match('/^(image\/)/i', $filesize['mime'])) {
		$type = $filesize['mime'];
	}
	return $type;
}


/* PukiWiki Adv. Extend codes *********************************************************************/

// 標準出力からMecabを実行
// https://github.com/odoku/MeCab-for-PHP5/blob/master/MeCab.php
function mecab_stdio($switch, $str){
	global $mecab_path;
	if (!file_exists($mecab_path)){
		die_message('Mecab is not found or not executable. Please check mecab path: '.$mecab_path);
	}
	$pipes = array();
	$result = $error = '';
	$descriptorspec = array (
		0 => array('pipe', "r"), // stdin
		1 => array('pipe', "w"), // stdout
		2 => array('pipe', 'w')
	);

	$cmd = $mecab_path. isset($switch) ? ' '.$switch : '';
	$process = proc_open($cmd, $descriptorspec, $pipes, null, null);
	if (!is_resource($process)) return false;

	fwrite($pipes[0], $str);
	fclose($pipes[0]);

	$lines = array();
	while ($line = fgets($pipes[1])) $lines[] = str_replace(array("\r\n", "\r", "\n"), '', $line);
	fclose($pipes[1]);

	fwrite($pipes[2], $error);
	fclose($pipes[2]);

	$status = proc_close($process);

	return join("\n",$lines);
}

function mecab_parse($input){
	if (!extension_loaded('mecab')) {
		$result = mecab_stdio('',$input);
	}else{
		$mecab = new MeCab_Tagger();
		$result = $mecab->parse($input);
	}

	// 出力フォーマット：表層形\t品詞, 品詞細分類1, 品詞細分類2, 品詞細分類3, 活用形, 活用型, 原形, 読み, 発音
	$lines = explode("\n", $result);
	foreach($lines as $line){
		if(in_array(trim($line), array('EOS', ''))){
			continue;
		}
		$s = explode("\t", $line);
		$surface = $s[0];
		$info = explode(',', $s[1]);

		$analisys[] = array(
			'surface'       => $surface,							// 表層形
			'class'         => $info[0],							// 品詞
			'detail1'       => $info[1] !== '*' ? $info[1] : null,	// 品詞細分類1
			'detail2'       => $info[2] !== '*' ? $info[2] : null,	// 品詞細分類2
			'detail3'       => $info[3] !== '*' ? $info[3] : null,	// 品詞細分類3
			'inflections'   => $info[4] !== '*' ? $info[4] : null,	// 活用形
			'conjugation'   => $info[5] !== '*' ? $info[5] : null,	// 活用型
			'origin'        => $info[6] !== '*' ? $info[6] : null,	// 原形
		);
	}
	return $analisys;
}

function mecab_wakati($input){
	if (!extension_loaded('mecab')) {
		$str = mecab_stdio('-O wakati', $input);
		return $str;
	}else{
		$mecab = new MeCab_Tagger();
		return $mecab->keyword($input);
	}
}

function mecab_reading($input){
	if (!extension_loaded('mecab')) {
		return ( mecab_stdio('-Oyomi', $input));
	}else{
		$mecab = new MeCab_Tagger();
		return $mecab->keyword($input);
	}
}