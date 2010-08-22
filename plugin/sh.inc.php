<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: sh.inc.php,v 0.6.2 2010/07/30 17:02:00 Logue Exp $
// SyntaxHighlighter for PukiWiki
//
// Copyright (C)
//    2010 PukiPlus Team
//    2009 ortk. http://ortk.main.jp/blog/

/* ---------------------------------------------------------------------------
 settings
--------------------------------------------------------------------------- */

// SyntaxHighlighter [folder path]
define('PLUGIN_SH_PATH', SKIN_URI.'js/syntaxhighlighter'.'/');
//define('PLUGIN_SH_PATH', 'http://alexgorbatchev.com/pub/sh/2.1.382/');

// SyntaxHighlighter [tag name]
define('PLUGIN_SH_TAG_NAME', 'pre'); // 'pre' or 'textarea'

define('PLUGIN_SH_USAGE', 
	   '<p class="error">Usage:<br />#sh[(Lang)]{{<br />src<br />}}</p>');

define('PLUGIN_SH_LANGUAGE', 'Plain');

// SyntaxHiglighter Theme
// Default, Django, Eclipse, Emacs, FadeToGrey, Midnight, RDark
define('PLUGIN_SH_THEME', 'Default');

/* ---------------------------------------------------------------------------
 functions
--------------------------------------------------------------------------- */

function plugin_sh_init(){
	$messages['_sh_messages'] = array(
		'sh_init' => true
	);
	set_plugin_messages($messages);
}

function plugin_sh_convert(){
	global $head_tags, $foot_tags, $sh_count, $langs;

	$params = array(
		'number'      => false,  // 行番号を表示する
		'nonumber'    => false,  // 行番号を表示しない
		'outline'     => false,  // アウトライン モード
		'nooutline'   => false,  // アウトライン 無効
//		'comment'     => false,  // コメント開閉する
//		'nocomment'   => false,  // コメント開閉しない
		'menu'        => false,  // メニューを表示する
		'nomenu'      => false,  // メニューを表示しない
//		'icon'        => false,  // アイコンを表示する
//		'noicon'      => false,  // アイコンを表示しない
		'link'        => false,  // オートリンク 有効
		'nolink'      => false,  // オートリンク 無効
		);
	
	
	$num_of_arg = func_num_args();
	$args = func_get_args();
	if ($num_of_arg < 1) {
		return PLUGIN_SH_USAGE;
	}

	$arg = $args[$num_of_arg-1];
	if (strlen($arg) == 0) {
		return PLUGIN_SH_USAGE;
	}

	//  @ 引数取り
	for ($i = 1; $i < $argc; $i++) {
		switch($argc){
			case 'number':
				$prop = 'gutter: true;';
				break;
			case 'nonumber':
				$prop = 'gutter: false;';
				break;
			case 'outline':
				$prop = 'collapse:true;';
				break;
			case 'nooutline':
				$prop = 'collapse:false;';
				break;
			case 'menu':
				$props = 'toolbar:true;';
				break;
			case 'nomenu':
				$props = 'toolbar:false;';
				break;
			case 'link':
				$props = 'auto-links:true;';
				break;
			case 'nolink':
				$props = 'auto-links:false;';
				break;
			}
		$ext[] = $props;
	}
	
	if(!$sh_count){
		$head_tags[] = '<script type="text/javascript" src="'.PLUGIN_SH_PATH.'scripts/shCore.js"></script>';
		$head_tags[] = '<link type="text/css" rel="stylesheet" href="'.PLUGIN_SH_PATH.'styles/shCore.css" />';
		$head_tags[] = '<link type="text/css" rel="stylesheet" href="'.PLUGIN_SH_PATH.'styles/shTheme'.PLUGIN_SH_THEME.'.css" id="shTheme" />';
/*
		$foot_tags[] = <<<HTML
<script type="text/javascript">
//<![CDATA[
SyntaxHighlighter.all();
//]]></script>
HTML;
*/
		$langs = array(
			'AS3'			=> false,
			'Bash'			=> false,
			'ColdFusion'	=> false,
			'Cpp'			=> false,
			'CSharp'		=> false,
			'Css'			=> false,
			'Delphi'		=> false,
			'Diff'			=> false,
			'Erlang'		=> false,
			'Groovy'		=> false,
			'Java'			=> false,
			'JavaFX'		=> false,
			'JScript'		=> false,
			'Perl'			=> false,
			'Php'			=> false,
			'Plain'			=> false,
			'PowerShell'	=> false,
			'Python'		=> false,
			'Ruby'			=> false,
			'Scala'			=> false,
			'Sql'			=> false,
			'Vb'			=> false,
			'Xml'			=> false
		);
		$sh_count++;
	}
	$lang = null;

	$num_of_arg = func_num_args();
	$args = func_get_args();

	if ($num_of_arg < 1) {
		return PLUGIN_SH_USAGE;
	}

	$arg = $args[$num_of_arg-1];
	if (strlen($arg) == 0) {
		return PLUGIN_SH_USAGE;
	}

	if ($num_of_arg != 1) {
		$lang = htmlspecialchars(strtolower($args[0]));
		switch($lang){
			case 'actionscript':
			case 'as':
			case 'as3':
				$lang = 'AS3';
			break;
			case 'bash':
			case 'shell':
				$lang = 'Bash';
			break;
			case 'c':
			case 'cpp':
			case 'c++':
				$lang = 'Cpp';
			break;
			case 'csharp':
			case 'c#':
			case 'cs':
				$lang = 'Csharp';
			break;
			case 'css':
			case 'style':
			case 'stylesheet':
				$lang = 'Css';
			break;
			case 'delphi':
				$lang = 'Delphi';
			break;
			case 'diff':
				$lang = 'Diff';
			break;
			case 'erlang':
				$lang = 'Erlang';
			break;
			case 'groovy':
				$lang = 'Groovy';
			break;
			case 'java':
				$lang = 'Java';
			break;
			case 'javafx':
				$lang = 'JavaFX';
			break;
			case 'javascript':
			case 'js':
			case 'jscript':
				$lang = 'JScript';
			break;
			case 'perl':
			case 'pl':
				$lang = 'Perl';
			break;
			case 'php':
				$lang = 'Php';
			break;
			case 'powershell':
				$lang = 'PowerShell';
			break;
			case 'python':
			case 'py':
				$lang = 'Python';
			break;
			case 'ruby':
			case 'rb':
				$lang = 'Ruby';
			break;
			case 'scala':
				$lang = 'Scala';
			break;
			case 'sql':
				$lang = 'Sql';
			break;
			case 'vb':
			case 'visualbasic':
				$lang = 'Vb';
			break;
			case 'xml':
			case 'html':
			case 'xslt':
				$lang = 'Xml';
			break;
			default:
				$lang = 'Plain';
			break;
		}
		if ($langs[$lang] == false){
			$langs[$lang] = true;
			$head_tags[] = '<script type="text/javascript" src="'.PLUGIN_SH_PATH.'scripts/shBrush'.$lang.'.js"></script>';
		}
	} else {
		$lang = 'Plain'; // default
	}

	return '<pre class="brush: '.strtolower($lang).' '.join(' ',$ret).'">'."\n".htmlspecialchars($arg)."\n".'</pre>'."\n";
//	return '<pre class="sh '.$lang.'">'."\n".htmlspecialchars($arg)."\n".'</pre>'."\n";
}

?>
