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
define('PLUGIN_SH_PATH', SKIN_DIR.'js/plugin/syntaxhighlighter'.'/');
// define('PLUGIN_SH_PATH', 'http://alexgorbatchev.com/pub/sh/current/');

// SyntaxHighlighter [tag name]
define('PLUGIN_SH_TAG_NAME', 'pre'); // 'pre' or 'textarea'

define('PLUGIN_SH_USAGE', 
	   '<p class="error">Usage:<br />#sh[(Lang)]{{<br />src<br />}}</p>');

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
	global $head_tags, $sh_count, $langs, $foot_tags;
	$sh_dir = PLUGIN_SH_PATH;

	if(!$sh_count){
		$sh_theme = PLUGIN_SH_THEME;
		$head_tags[] = <<<HTML
<script type="text/javascript" src="{$sh_dir}scripts/shCore.js"></script>
<link type="text/css" rel="stylesheet" href="{$sh_dir}styles/shCore.css" />
<link type="text/css" rel="stylesheet" href="{$sh_dir}styles/shTheme{$sh_theme}.css" id="shTheme" />
HTML;
		$foot_tags[] = <<<HTML
<script type="text/javascript">
//<![CDATA[
SyntaxHighlighter.defaults['auto-links'] = false;
SyntaxHighlighter.all();
//]]></script>
HTML;
		// 使用可能なスクリプト言語と、呼び出しフラグ
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
		$lang = htmlspecialchars(strtolower($args[0])); // 言語名かオプションの判定
		// 該当する言語がある場合、上記のスクリプト言語名を$langに代入
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
			// もし、falseだった場合、ヘッダータグに該当する言語のスクリプトを挿入し、呼び出しフラグをtrueにする。
			$langs[$lang] = true;
			$head_tags[] = '<script type="text/javascript" src="'.$sh_dir.'scripts/shBrush'.$lang.'.js"></script>';
		}
	} else {
		$lang = 'Plain'; // default
		$head_tags[] = '<script type="text/javascript" src="'.$sh_dir.'scripts/shBrushPlain.js"></script>';
		$langs['Plain'] = true;
	}

	return '<pre class="brush: '.strtolower($lang).'">'."\n".htmlspecialchars($arg)."\n".'</pre>'."\n";
}

?>
