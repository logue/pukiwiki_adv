<?php
// $Id: newpage_subdir.inc.php,v 1.3.8 2011/02/05 12:00:00 Logue Exp $
// Copyright (C)
//   2010-2011 PukiWiki Advance Developers Team
//   2005,2008 PukiWiki Plus! Team
//
// @based_on newpage.inc.php
// @based_on ls2.inc.php
// @thanks to panda (auther of newpage.inc.php/ls2.inc.php)

function build_directory_list($roots, $option=array())
{
	global $WikiName,$BracketName;

	$list = $warnings = array();
	$list['directory'] = $list['warning'] = array();
	$pages = auth::get_existpages();

	foreach($roots as $root) {
		$matched = FALSE;
		foreach($pages as $page) {
			// $page = strip_bracket($page);
//			if (preg_match("/^$root.*$/", $page)){
			if (strpos($page,$root) === 0){
				if(isset($option['directory only']) && $option['directory only'] && strrpos($page, '/') >= strlen($root) ) {
					$page = substr($page,0, strrpos($page, '/'));
				}
				$list['directory'][] = $page;
				while( strrpos($page, '/') >= strlen($root) ) {
					$page = substr($page,0, strrpos($page, '/'));
					$list['directory'][] = $page;
				}
				$matched = TRUE;
			}
		}
		if(!$matched) {
			$list['directory'][] = $root;
			$warnings[] =
				'<p><span style="color]:red">' . sprintf( T_("#%s doesn't have the corresponding page. "),$root) . '</span>' .
				'(<a href="' . get_page_uri($root) . '">' . T_('making') . "</a>)</p>\n";
		}
	}

	$list['directory'] = array_unique($list['directory']);
	natcasesort($list['directory']);

	if(isset($option['quiet']) && !$option['quiet']) {
		$list['warning'] = $warnings;
	}
	return $list;
}

function print_form_string( $list )
{
	global $vars;
	
	$form_string  = '<form action="'. get_script_uri() .'" method="post">'."\n".
			'<fieldset>'."\n". '<legend>'.T_('Page name') . '</legend>';

	if($list['directory']) {
		$form_string .= '<select name="directory">'."\n";
		foreach( $list['directory'] as $dir ) {
			$form_string .= '<option>'.htmlsc($dir).'/</option>'."\n";
		}
		$form_string .= "</select>\n";
	}
	
	$form_string .= '<input type="hidden" name="plugin" value="newpage_subdir" />'."\n".
			'<input type="hidden" name="refer" value="'.$vars['page'].'" />'."\n".
			'<input type="text" name="page" size="30" value="" />'."\n".
			'<input type="submit" value="' . T_('New') . '" />'."\n".
			'</fieldset>'."\n".
			'</form>'."\n";

	if(isset($list['warning']) && $list['warning']) {
		foreach( $list['warning'] as $warning ) {
			$form_string .= $warning;
		}
	}

	return $form_string;
}

function print_help_message()
{
	return join("\n",array(
		'#newpage_subdir([directory]... ,[option]... )<br />\n',
		'<dl>',
		'<dt>' . T_('DESCRIPTION') . '</dt>',
		'<dd>' . T_('The field that adds a new page below the directory specified for [directory] is made.') . '</dd>',
		'<dd>' . T_('The order of the parameter is arbitrary.') . '</dd>',
		'<dd>' . T_('When an undefined option is specified, Help is displayed with the message.') . '</dd>',
		'<dt>' . T_('OPTION') . '</dt>' . 
		'<dd>' . T_('-d Directory Only.	It limits it only to the one with the child page. (The directory specified specifying it is an exception. )') . '</dd>',
		'<dd>' . T_('-h Help.		This Description is displayed.') . '</dd>',
		'<dd>' . T_('-q Quiet.		Warning is not displayed.') . '</dd>',
		'<dt>' . T_('EXAMPLE') . '</dt>',
		'<dd><pre><samp>',
		'#newpage_subdir() -&gt; implies: #newpage_subdir(&lt;current dir&gt;)',
		'#newpage_subdir(foo/var)',
		'#newpage_subdir(foo/var, -n)',
		'#newpage_subdir(-d,-q, foo/var, foo/vaz)',
		'#newpage_subdir(-h)',
		'#newpage_subdir(-XYZ) -&gt; implies : #newpage_subdir(-h)',
		'</samp></pre></dd>',
		'</dl>'
	));
}

function plugin_newpage_subdir_convert()
{
	global $vars;
	// $available_option = 'rdhq';

	if (auth::check_role('readonly')) return '';
	if (auth::is_check_role(PKWK_CREATE_PAGE)) return '';

	$roots = $option = array();

	// parsing all parameters
	foreach(func_get_args() as $arg) {
		$arg = trim($arg);
		// options
		if(preg_match("/^\-[a-z\-\s]+\$/",$arg)) {
			for($i=1;$i<strlen($arg);$i++){
				switch($arg{$i}) {
					case 'd' : 
						$option['directory only'] = true; 
						break;
					case 'q' : 
						$option['quiet'] = true; 
						break;
					case ' ' :
					case '-' :
						break;
					case 'h' :						
					default:
						return print_help_message();
				}
			}
		}
		// directory roots
		else {
			$roots[] = $arg;
		}
	}

	//if(!$roots) {
	if (isset($vars['page'])) {
		// $roots[] = strip_bracket($vars['page']);
		$roots[] = $vars['page'];
	}

	return print_form_string(build_directory_list($roots, $option));
}

function plugin_newpage_subdir_action()
{
	global $vars;

	if (auth::check_role('readonly')) return '';
	if (auth::is_check_role(PKWK_CREATE_PAGE)) return '';

	$roots = $retval = array();
	$page = (empty($vars['page'])) ? '' : $vars['page'];
	$dir  = (empty($vars['directory'])) ? '' : strip_bracket($vars['directory']);

	if (empty($page)) {
		if (!empty($dir)) {
			$roots[] = (substr($dir, -1) == '/') ? substr($dir, 0, -1) : $dir;
		}
		return array(
			'msg'	=> sprintf(T_('Create new page to %s directory'), $dir),
			'body'	=> print_form_string(build_directory_list($roots))
		);
	}

	header('Location: '.get_page_location_uri($dir.$page));
	die();
}
/* End of file newpage_subdir.inc.php */
/* Location: ./wiki-common/plugin/newpage_subdir.inc.php */