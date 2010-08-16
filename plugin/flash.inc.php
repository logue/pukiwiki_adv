<?php
/////////////////////////////////////////////////
// PukiPlus.
//
// $Id: flash.inc.php,v 1.2.1 2010/07/06 00:04:oo upk Exp $
//
// flash plugin for pukiwiki
// Author Nekyo.(http://nekyo.hp.infoseek.co.jp/)
// arrange tamac (http://tamac.daa.jp/)
// arrange jack (http://f29.aaa.livedoor.jp/~morg/wiki/)
// arrange Logue (http://logue.be/)

function plugin_flash_inline()
{
	$args = func_get_args();
	array_pop($args);	// インラインの場合引数の数＋１になる仕様対策
	array_push($args,"inline");
	return call_user_func_array('plugin_flash_convert', $args);
}

function plugin_flash_convert()
{
	global $flash_count, $script;
	$argc = func_num_args();

	if ($argc < 1) {
		return FALSE;
	}
	$argv = func_get_args();
	// @ インライン呼び出しかどうか
	if($argv[$argc-1] == "inline")	{ $binline = 1; array_pop($argv);}

	// @ 第一引数の受け取り
	$swf = $argv[0];
//	$swf = &::unescape(&flash::decode($swf));

	// @ 特殊変数
	$serverpath  = "http://" . getenv('SERVER_NAME') . "/";
	$wikipath    = str_replace("index.php", "",  "http://" . getenv('SERVER_NAME') . getenv('SCRIPT_NAME'));
	// @ 削除
	$swf = str_replace("{server}", $serverpath,$swf);
	$swf = str_replace("{wikiroot}", $wikipath,$swf);

	//  @ デフォルト値読み込み
	$params = array(
		'movie'			=> $swf,
		'play'				=> '',
		'loop'				=> 'false',
		'menu'				=> '',
		'quality'			=> 'high',
		'scale'				=> '',
		'salign'			=> '',
		'wmode'				=> 'opaque',
		'bgcolor'			=> 'transparent',
		'base'				=> '',
		'swliveconnect'		=> '',
		'flashvars'			=> '',
		'devicefont'		=> '',				// http://www.adobe.com/cfusion/knowledgebase/index.cfm?id=tn_13331
		'allowscriptaccess'	=> 'sameDomain',	// http://www.adobe.com/cfusion/knowledgebase/index.cfm?id=tn_16494
		'seamlesstabbing'	=> '',				// http://www.adobe.com/support/documentation/en/flashplayer/7/releasenotes.html
		'allowfullscreen'	=> '',				// http://www.adobe.com/devnet/flashplayer/articles/full_screen_mode.html
		'allownetworking'	=> '' 				// http://livedocs.adobe.com/flash/9.0/main/00001079.html
	);
	$width = '512';
	$height = '384';

	//  @ 引数取り
	for ($i = 1; $i < $argc; $i++) {
		//@ 検出正規表現  英数文字=["]任意["]
		if (preg_match('/(\w*)=\"?([^\"]*)\"?/', $argv[$i], $match)) {
			$prop = strtolower($match[1]); /* 小文字化 */
			$prot = $match[1];
			
			if ($prop == 'flashvars'){
				$flashvars = $match[2];
				// @ UTF-8に変換
				$flashvars = mb_convert_encoding($flashvars,"UTF-8","EUC-JP");//
				// @ varname =val の形に分解
				$aryVars   = split('&',$flashvars);
				for($i=0;$i < count($aryVars); $i++){
					// @ 名前と値に分解
					$aryField = split('=',$aryVars[$i]);
					if(count($aryField)==2){
						// @ 値の部分だけurlエンコード
						$aryField[1] = urlencode($aryField[1]);
						// @ 繋げて戻す
						$aryVars[$i] = join('=',$aryField);
					}
				}
				// @ &で繋げて戻す
				$params['flashvars'] = join('&amp;',$aryVars);
			}else{
				$params[$prop] = $match[2];
			}
		/* 幅x高さ  数字であり1ケタ以上４桁以下 */
		} else if (preg_match('/([0-9]{1,4})x([0-9]{1,4})/i', $argv[$i], $match)) {
			
			$numWidth  = $match[1];
			$numHeight = $match[2];
			$width     = "width=\"". $numWidth . "\"";
			$height    = "height=\"".$numHeight . "\"";
		}//if
	}
	
	// <param>タグをセット
	foreach ($params as $param_name => $param_value) {
		if($param_value != '') $param_tags[] = '<param name="'.$param_name.'" value="'.$param_value.'" />';
	}

	$param = join("\n",$param_tags);

	if ($flash_count == ''){
// PukiPlus では標準でswfobjectが読み込まれる。
//		global $head_tags;
//		$head_tags[] = '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>'."\n";
		$flash_count = 0;
	}
		
	$ret = <<<EOD
<object id="flash_$flash_count" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" $width $height>
$param
<!--[if !IE]>-->
	<object type="application/x-shockwave-flash" data="$swf" $width $height>
<!--<![endif]-->
		Error: Flash Player Cannot Installed.
<!--[if !IE]>-->
	</object>
<!--<![endif]-->
</object>
<script type="text/javascript">
//<![CDATA[
swfobject.registerObject("flash_$flash_count", "9.0.0", "$script?plugin=flash&type=.swf");
//]]></script>
EOD;
	$flash_count++;
	if ($binline==1){
		// インライン出力
		return '<span class="flash">'.$ret.'</span>'."\n";
	}else{
		return '<div class="flash" style="text-align:$align;margin-left:auto; margin-right:auto;">'.$ret.'</div>'."\n";
	}
}

function plugin_flash_action(){
	global $vars;
	
	// Express InstallのFlash埋込み・・・
	if ($vars['type'] == ".swf"){
		// ExpressInstall.swf
		header("Content-Type: application/x-shockwave-flash;base64");
		header("Content-disposition: attachment; filename=expressInstall.swf");
		print <<< BASE64
Q1dTBkEFAAB4nIVUzU/UQBT/dXfZLbDAyteilA8hMUoMNfGGIAQIhIQFWUAPxsNsO0Ch2zbtFNyz
iXLiuIkhRo+ePHrRxH+Aq/Hq0SP/wfqmXVhWMfYwfe8372t+7814yGrAxhyyChYStVptVh0HoMCr
byg4ow+5vjOtd+P8/fjnL8NZ7fDjB9Rac2SYINPX6bRCP8yRXmv9ngKSpM4mP5F0/EMZg+0yk/sL
tuUhEMwX257JBIdwN4VvObt4LioeH10KHUNYrvMClgnD5sxfcQT3D5kdBVhwy57NpZtVjpf1UCB0
5F7BPbR4ZLVVx01uswp8blo+N8R2cRWFQrPmkQH3tygzKaZLqYXNYTDbLjHjYPPZ0nppn4yx6B5F
OSYvC2ggzDG4bXOzAS0xS+o7nnmBlJnhu2VKzSYNt4zNSiB4GQE3Qt8SFVA+92jRLTPLoeTGHp8P
ycBHgYk9+MwxySeM+KKSsCeEN6Xr/wyve2FJ37FZsBcfT49ddUrmc0fowdGOzkLhxh3wJ0mfRaki
eIBdLualsOWKOuMxq1SScbAqO2hS1eKyJ6e5pi3gK6rtSKP+qdCqCajKmlwTRbkmV7rfJXAfUk5p
1QwyijRsmck3OaZJV5B8/DaBJ5FtJoqkarvSfeZ/7nHe1mIz2lYPWk1RvHrEdq3agsyvJNCxPHTS
2zxlNNr7WImSUcQ01GxKUTvW1htAJwGdV4EuAroIuIaFYp7oujqfQK6ZrtxM/mQMlkMXxLY3BRNh
QJcoMQEliJSnzA45SrKglPKGTi7j3tghRkZAh6JjdUslX1d64h3iUJfLxF/JCOy7DgRO+6+5BcBD
vHqAfXbIAsO3PDFFo+NGe5P8pefzIFiJa1+o+96994iKbIXaG/epL2KiP5qHPHWnA+pAI/nNaPdW
cVoSOahq6oC2PC1thv7kcphs6DfYmIAYH4k6rmYu7afJQFUzLRdie6YtEomdlOw9+YxGQeSh1dsz
0xl8S/eQfId4R0f+55VnSz5tx/KZSyJ7Piq138zpPOw=
BASE64;
	}else{
		return "Unknown Method";
	}
}
?>
