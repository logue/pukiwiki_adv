<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: sh.inc.php,v 0.6.4 2010/11/17 08:47:00 Logue Exp $
// SyntaxHighlighter for PukiWiki
//
// Copyright (C)
//    2010 PukiWiki Advance Developers Team
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
	global $js_tags, $link_tags, $sh_count, $langs;
	
	if(!$sh_count){
		$js_tags[] = array('type'=>'text/javascript', 'src'=>PLUGIN_SH_PATH.'scripts/shCore.js');
		$link_tags[] = array('type'=>'text/css', 'rel'=>'stylesheet', 'href'=>PLUGIN_SH_PATH.'styles/shCore.css');
		$link_tags[] = array('type'=>'text/css', 'rel'=>'stylesheet', 'href'=>PLUGIN_SH_PATH.'styles/shTheme'.PLUGIN_SH_THEME.'.css', 'id'=>'shTheme');
/*
for Plus! use
		global $head_tags, $foot_tags;
		$head_tags[] = '<script type="text/javascript" src="'.PLUGIN_SH_PATH.'scripts/shCore.js"></script>';
		$head_tags[] = '<link type="text/css" rel="stylesheet" href="'.PLUGIN_SH_PATH.'styles/shCore.css"></script>';
		$head_tags[] = '<link type="text/css" rel="stylesheet" href="'.PLUGIN_SH_PATH.'styles/shTheme'.PLUGIN_SH_THEME.'.css" id="shTheme"></script>';
		$sh_path = PLUGIN_SH_PATH;
		$foot_tags[] = <<<HTML
<script type="text/javascript">
//<![CDATA[
SyntaxHighlighter.config.clipboardSwf = '$sh_path/scripts/clipboard.swf';
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

	$text = htmlspecialchars(array_pop($args), ENT_QUOTES, SOURCE_ENCODING);
	if ($num_of_arg == 1) {
		$lang = 'Plain'; // default
	}else{
		$lang = htmlspecialchars(strtolower(array_shift($args)), ENT_QUOTES, SOURCE_ENCODING);
		$ret = array();
		//  @ 引数取り
		for ($i = 0; $i <= count($args); $i++) {
			if (!empty($args[$i])){
				$cond = htmlspecialchars($args[$i]);
				switch($cond){
					case 'number':
						$ret[] = 'gutter: true;';
						break;
					case 'nonumber':
						$ret[] = 'gutter: false;';
						break;
					case 'outline':
						$ret[] = 'collapse:true;';
						break;
					case 'nooutline':
						$ret[] = 'collapse:false;';
						break;
					case 'menu':
						$ret[] = 'toolbar:true;';
						break;
					case 'nomenu':
						$ret[] = 'toolbar:false;';
						break;
					case 'link':
						$ret[] = 'auto-links:true;';
						break;
					case 'nolink':
						$ret[] = 'auto-links:false;';
						break;
					default : 
						if(preg_match('/class=\"?([^\"]*)\"?/',$cond,$match)){	// class属性のオーバーライド
							$ret[] = $match[1];
						}
						break;
				}
			}
			$option =  ' '.join(' ',$ret);
		}
	}
	// 定義されている言語
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
		$js_tags[] = array('type'=>'text/javascript', 'src'=>PLUGIN_SH_PATH.'scripts/shBrush'.$lang.'.js');
		// $head_tags[] = '<script type="text/javascript" src="'.PLUGIN_SH_PATH.'scripts/sh'.$lang.'.js"></script>';	// for Plus! use
	}

	return '<pre class="brush: '.strtolower($lang).$option .';">'."\n".$text."\n".'</pre>'."\n";
}
?>
