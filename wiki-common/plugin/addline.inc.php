<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: addline.inc.php,v 0.13.6 2012/10/11 09:05:00 Logue Exp $
// Original is sha(0.13)
/* 
*プラグイン addline
 その場に、固定の文字列を追加する。

*Usage
 #addline(設定名[,above|below|up|down|number|nonumber][,btn:<ボタンテキスト>][,ltext:<左テキスト>][,rtext:<右テキスト>])
 &addline(設定名[,before|after|above|below|up|down|number|nonumber]){<ボタンテキスト>};

*パラメータ
  設定:   「:config/plugin/addline/設定」の設定名を記載
  above|below|up|down: 上か下に追加する。
  before|after: ボタンテキストの前か後に追加する。
  ltext: ボタンの左側のテキスト
  rtext: ボタンの右側のテキスト

*設定ページの内容
 追加する文字列を記載する。複数行でもよい。
 例：
	|&attachref;|&attachref;|&attachref;|
	|あいう|えおか|きくけ|
*/

use PukiWiki\Lib\Auth\Auth;

/////////////////////////////////////////////////
// コメントを挿入する位置 1:欄の前 0:欄の後
defined('ADDLINE_INS') or define('ADDLINE_INS', '1');

function plugin_addline_init()
{
	global $_string;
	$messages = array(
		'_addline_messages'	=> array(
			'btn_submit'		=> T_('add'),
			'title_collided'	=> $_string['title_collided'],
			'msg_collided' 		=> $_string['msg_collided'],
			'error'				=> T_('addline error'),
			'config_notfound'	=> T_('config file <var>%s</var> is not exist.'),
		)
	);
	set_plugin_messages($messages);
}

function plugin_addline_convert()
{
	global $vars,$digest;
	global $_addline_messages;
	static $numbers = array();
	static $no_flag = 0;

	if( Auth::check_role('readonly') ) return '';

	if (!array_key_exists($vars['page'],$numbers)){
		$numbers[$vars['page']] = 0;
	}
	$addline_no = $numbers[$vars['page']]++;
	
	$above = ADDLINE_INS;
	$configname = 'default';
	$btn_text = $_addline_messages['btn_submit'];
	$right_text = $left_text = '';
	if ( func_num_args() ){
		foreach ( func_get_args() as $opt ){
		if ( $opt === 'above' || $opt === 'up' ){
				$above = 1;
			}
			else if (preg_match("/btn:(.+)/i",$opt,$args)){
				$btn_text = htmlsc($args[1]);
			}
			else if (preg_match("/rtext:(.+)/i",$opt,$args)){
				$right_text = htmlsc($args[1]);
			}
			else if (preg_match("/ltext:(.+)/i",$opt,$args)){
				$left_text = htmlsc($args[1]);
			}
			else if ( $opt === 'below' || $opt === 'down' ){
				$above = 0;
			}
   			else if ( $opt === 'number' ){
		$no_flag = 1;
		}
   			else if ( $opt === 'nonumber' ){
		$no_flag = 0;
		}
			else {
				$configname = $opt;
			}
		}
		if ( $no_flag == 1 ) $btn_text .= "[$addline_no]";
	}

	$f_page   = htmlsc($vars['page']);
	$f_config = htmlsc($configname);
	$script = get_script_uri();

	return <<<EOD
<form action="$script" method="post" class="addline_form">
	<input type="hidden" name="above" value="$above" />
	<input type="hidden" name="addline_no" value="$addline_no" />
	<input type="hidden" name="configname"  value="$f_config" />
	<input type="hidden" name="digest" value="$digest" />
	<input type="hidden" name="cmd" value="addline" />
	<input type="hidden" name="refer" value="$f_page" />
	$left_text
	<input type="submit" name="addline" value="$btn_text" />
	$right_text
</form>
EOD;
}

function plugin_addline_inline()
{
	global $vars,$digest;
	global $_addline_messages;
	static $numbers = array();
	static $no_flag = 0;

	if( Auth::check_role('readonly') ) return '';

	if (!array_key_exists($vars['page'],$numbers))
	{
		$numbers[$vars['page']] = 0;
	}
	$addline_no = $numbers[$vars['page']]++;
	
	$above = ADDLINE_INS;
	$configname = 'default';
	$btn_text = $_addline_messages['btn_submit'];
	if ( func_num_args() ){
		$args =func_get_args();
		$opt = array_pop($args);
		$btn_text = $opt ? htmlsc($opt) : $btn_text;
		foreach ( $args as $opt ){
		if ( $opt === 'before' ){
				$above = 3;
			}
			else if ( $opt === 'after' ){
				$above = 2;
			}
		else if ( $opt === 'above' || $opt === 'up' ){
				$above = 1;
			}
			else if ( $opt === 'below' || $opt === 'down' ){
				$above = 0;
			}
   			else if ( $opt === 'number' ){
		$no_flag = 1;
		}
   			else if ( $opt === 'nonumber' ){
		$no_flag = 0;
		}
			else {
				$configname = $opt;
			}
		}
		if ( $no_flag == 1 ) $btn_text .= "[$addline_no]";
	}

	$f_page   = rawurlencode($vars['page']);
	$f_config = rawurlencode($configname);
	$link_uri = get_cmd_uri('addline','','',
		array(
			'addline_inno'	=> $addline_no,
			'above'			=> $above,
			'refer'			=> $f_page,
			'configname'	=> $f_config,
			'digest'		=> $digest
		)
	);
	
// <a href="$script?plugin=addline&amp;addline_inno=$addline_no&amp;above=$above&amp;refer=$f_page&amp;configname=$f_config&amp;digest=$digest"></a>
	return '<a href="'.$link_uri.'">'.$btn_text.'</a>';
}

function plugin_addline_action()
{
	global $_addline_messages, $_string, $vars;
	if( Auth::check_role('readonly') ) die_message($_string['prohibit']);

	$refer			= $vars['refer'];
	$postdata_old	= get_source($refer);
	$configname		= $vars['configname'];
	$above			= $vars['above'];

	$block_plugin = 1;
	if ( array_key_exists('addline_inno', $vars) ) {
		$addline_no = $vars['addline_inno'];
		$block_plugin = 0;
	}
	else if ( array_key_exists('addline_no', $vars) ) {
		$addline_no = $vars['addline_no'];
	}
	
	$config = new Config('plugin/addline/'.$configname);
	if (!$config->read())
	{
		return array( 
			'msg' => $_addline_messages['error'],
			'body' => sprintf($_addline_messages['config_notfound'], htmlsc($configname))
		);
	}
	$config->config_name = $configname;
	$addline = join('', addline_get_source($config->page));
	$addline = rtrim($addline);
		if ( $block_plugin ){
		$postdata = addline_block($addline,$postdata_old,$addline_no,$above);
	}
	else {
		$postdata = addline_inline($addline,$postdata_old,$addline_no,$above);
	}

	$title = $_title_updated;
	$body = '';
	if (md5(join('',$postdata_old)) !== $vars['digest'])
	{
		$title = $_addline_messages['title_collided'];
		$body  = $_addline_messages['msg_collided'] . make_pagelink($refer);
	}
	
	
//	$body = $postdata; // debug
//	foreach ( $vars as $k=>$v ){$body .= "[$k:$v]&br;";}
	page_write($refer,$postdata);
	
	$retvars['msg'] = $title;
	$retvars['body'] = $body;
//	$post['page'] = $get['page'] = $vars['page'] = $refer;
	$post['refer'] = $get['refer'] = $vars['refer'] = $refer;
	return $retvars;
}
function addline_block($addline,$postdata_old,$addline_no,$above)
{
	$postdata = '';
	$addline_ct = 0;
	foreach ($postdata_old as $line)
	{
		if (!$above) $postdata .= $line;
		if (preg_match('/^#addline/',$line) and $addline_ct++ == $addline_no){
			$postdata = rtrim($postdata)."\n$addline\n";
			if ($above) $postdata .= "\n";
		}
		if ($above) $postdata .= $line;
	}
	return $postdata;
}
function addline_inline($addline,$postdata_old,$addline_no,$above)
{
	$postdata = '';
	$addline_ct = 0;
	$skipflag = 0;
	foreach ($postdata_old as $line)
	{
		if ( $skipflag || substr($line,0,1) == ' ' || substr($line,0,2) == '//' ){
			$postdata .= $line;
			continue;
		}
		$ct = preg_match_all('/&addline\([^();]*\)({[^{};]*})?;/',$line, $out);
		if ( $ct ){
			for($i=0; $i < $ct; $i++){
				if ($addline_ct++ == $addline_no ){
					if ( $above == 3 ){ // before
						$line = preg_replace('/(&addline\([^();]*\)({[^{};]*})?;)/', $addline.'$1',$line,1);
					}
					else if ( $above == 2 ){ //after
						$line = preg_replace('/(&addline\([^();]*\)({[^{};]*})?;)/','$1'.$addline,$line,1);
					}
					else if ( $above == 1 ){ // above
						$line = $addline . "\n" . $line;
					}
					else if ( $above == 0 ){ //below
						$line .= $addline . "\n";
					}
					$skipflag = 1;
		   			break;
				}
				else if ( $above == 2 || $above == 3 ){
					$line = preg_replace('/&addline(\([^();]*\)({[^{};]*})?);/','&___addline$1___;',$line,1);
				}
			}
			if ( $above == 2 || $above == 3 ){
				$line = preg_replace('/&___addline(\([^();]*\)({[^{};]*})?)___;/','&addline$1;',$line);
			}
		}
		$postdata .= $line;
	}
	return $postdata;
}
function addline_get_source($page) // tracker.inc.phpのtracker_listから
{
	$source = get_source($page);
	// 見出しの固有ID部を削除
	$source = preg_replace('/^(\*{1,3}.*)\[#[A-Za-z][\w-]+\](.*)$/m','$1$2',$source);
	// #freezeを削除
	return preg_replace('/^#freeze\s*$/m','',$source);
}

/* End of file addline.inc.php */
/* Location: ./wiki-common/plugin/addline.inc.php */