<?php
// $Id: bugtrack.inc.php,v 1.27.8 2012/06/08 17:46:00 Logue Exp $
//
// PukiWiki BugTrack plugin
//
// Copyright:
// 2011-2012 PukiWiki Advance Developers Team
// 2005-2008 PukiWiki Plus! Developers Team
// 2002-2005,2011 PukiWiki Developers Team
// 2002 Y.MASUI GPL2  http://masui.net/pukiwiki/ masui@masui.net

// Numbering format
define('PLUGIN_BUGTRACK_NUMBER_FORMAT', '%d'); // Like 'page/1'
//define('PLUGIN_BUGTRACK_NUMBER_FORMAT', '%03d'); // Like 'page/001'

use PukiWiki\Auth\Auth;
use PukiWiki\Factory;
use PukiWiki\Utility;
use PukiWiki\Renderer\RendererDefines;
use PukiWiki\Text\Rules;

function plugin_bugtrack_init()
{
	global $_plugin_bugtrack;
	static $init;

	if (isset($init)) return; // Already init
	if (isset($_plugin_bugtrack)) die('Global $_plugin_bugtrack had been init. Why?');
	$init = TRUE;

	$_plugin_bugtrack = array(
		'priority_list'		=> array(T_('Emergency'), T_('Importance'), T_('Normal'), T_('Low')),
		'state_list'		=> array(T_('Proposal'), T_('Start'), T_('CVS Waiting'), T_('Completion'), T_('Reservation'), T_('Rejection')),
		'state_sort'		=> array(T_('Start'), T_('CVS Waiting'), T_('Reservation'), T_('Completion'), T_('Proposal'), T_('Rejection')),
		'state_bgcolor'		=> array('#ccccff', '#ffcc99', '#ccddcc', '#ccffcc', '#ffccff', '#cccccc', '#ff3333'),
		'header_bgcolor'	=> '#ffffcc',
		'base'				=> T_('Page'),
		'summary'			=> T_('Summary'),
		'nosummary'			=> T_('Fill in the summary here. '),
		'priority'			=> T_('Priority'),
		'state'				=> T_('State'),
		'name'				=> T_('Name'),
		'noname'			=> T_('Anonymous'),
		'date'				=> T_('Date'),
		'body'				=> T_('Message'),
		'category'			=> T_('Category'),
		'pagename'			=> T_('Page Name'),
		'pagename_comment'	=> T_('Page name is automatically given for an empty column.'),
		'version_comment'	=> T_('It is acceptable also in an empty column.'),
		'version'			=> T_('Version'),
		'submit'			=> T_('Submit')
	);
}

// #bugtrack: Show bugtrack form
function plugin_bugtrack_convert()
{
	global $vars;

	// if (PKWK_READONLY) return ''; // Show nothing
	if (Auth::check_role('readonly')) return ''; // Show nothing
	if (Auth::is_check_role(PKWK_CREATE_PAGE)) return '';

	$base = $vars['page'];
	$category = array();
	if (func_num_args()) {
		$category = func_get_args();
		$_base    = get_fullname(strip_bracket(array_shift($category)), $base);
		if (is_pagename($_base)) $base = $_base;
	}

	return plugin_bugtrack_print_form($base, $category);
}

function plugin_bugtrack_print_form($base, $category)
{
	global $_plugin_bugtrack, $session;
	static $id = 0;

	$s_base     = Utility::htmlsc($base);
	$s_name     = Utility::htmlsc($_plugin_bugtrack['name']);
	$s_category = Utility::htmlsc($_plugin_bugtrack['category']);
	$s_priority = Utility::htmlsc($_plugin_bugtrack['priority']);
	$s_state    = Utility::htmlsc($_plugin_bugtrack['state']);
	$s_pname    = Utility::htmlsc($_plugin_bugtrack['pagename']);
	$s_pnamec   = Utility::htmlsc($_plugin_bugtrack['pagename_comment']);
	$s_version  = Utility::htmlsc($_plugin_bugtrack['version']);
	$s_versionc = Utility::htmlsc($_plugin_bugtrack['version_comment']);
	$s_summary  = Utility::htmlsc($_plugin_bugtrack['summary']);
	$s_body     = Utility::htmlsc($_plugin_bugtrack['body']);
	$s_submit   = Utility::htmlsc($_plugin_bugtrack['submit']);

	++$id;

	$select_priority = "\n";
	$count = count($_plugin_bugtrack['priority_list']);
	$selected = '';
	for ($i = 0; $i < $count; ++$i) {
		if ($i == ($count - 1)) $selected = ' selected="selected"'; // The last one
		$priority_list = Utility::htmlsc($_plugin_bugtrack['priority_list'][$i]);
		$select_priority .= '    <option value="' . $priority_list . '"' .
			$selected . '>' . $priority_list . '</option>' . "\n";
	}

	$select_state = "\n";
	for ($i = 0; $i < count($_plugin_bugtrack['state_list']); ++$i) {
		$state_list = Utility::htmlsc($_plugin_bugtrack['state_list'][$i]);
		$select_state .= '    <option value="' . $state_list . '" style="background-color:'.$_plugin_bugtrack['state_bgcolor'][$i].'">' .
			$state_list . '</option>' . "\n";
	}

	if (empty($category)) {
		$encoded_category = '<input name="category" id="_p_bugtrack_category_' . $id .
			'" type="text" placeholder="'.$s_category.'" class="form-control" />';
	} else {
		$encoded_category = '<select name="category" id="_p_bugtrack_category_' . $id . '" class="form-control">';
		foreach ($category as $_category) {
			$s_category = Utility::htmlsc($_category);
			$encoded_category .= '<option value="' . $s_category . '">' .
				$s_category . '</option>' . "\n";
		}
		$encoded_category .= '</select>';
	}

//	$ticket = md5(MUTIME);
//	$keyword = 'B_' . $ticket;
//	$session->offsetSet($keyword, md5(get_ticket() . $ticket));

	$script = get_script_uri();
	$body = <<<EOD
<form action="$script" method="post" class="form-horizontal plugin-bugtrack-form">
	<input type="hidden" name="cmd" value="bugtrack" />
	<input type="hidden" name="mode"   value="submit" />
	<input type="hidden" name="base"   value="$s_base" />
	<div class="form-group">
		<label for="_p_bugtrack_name_$id" class="col-md-2 control-label">$s_name</label>
		<div class="col-md-10">
			<input id="_p_bugtrack_name_$id" name="name" size="20" class="form-control" type="text" placeholder="$s_name" required="true" />
		</div>
	</div>
	<div class="form-group">
		<label for="_p_bugtrack_category_$id" class="col-md-2 control-label">$s_category</label>
		<div class="col-md-10">
			$encoded_category
		</div>
	</div>
	<div class="form-group">
		<label for="_p_bugtrack_priority_$id" class="col-md-2 control-label">$s_priority</label>
		<div class="col-md-10">
			<select id="_p_bugtrack_priority_$id" name="priority" class="form-control">$select_priority</select>
		</div>
	</div>
	<div class="form-group">
		<label for="_p_bugtrack_state_$id" class="col-md-2 control-label">$s_state</label>
		<div class="col-md-10">
			<select id="_p_bugtrack_state_$id" name="state" class="form-control">$select_state</select>
		</div>
	</div>
	<div class="form-group">
		<label for="_p_bugtrack_pagename_$id" class="col-md-2 control-label">$s_pname</label>
		<div class="col-md-10">
			<input id="_p_bugtrack_pagename_$id" name="pagename" size="20" type="text" placeholder="$s_pname" class="form-control" />
			<span class="help-block">$s_pnamec</span>
		</div>
	</div>
	<div class="form-group">
		<label for="_p_bugtrack_version_$id" class="col-md-2 control-label">$s_version</label>
		<div class="col-md-10">
			<input id="_p_bugtrack_version_$id" name="version" size="10" type="text" placeholder="$s_version" class="form-control" />
			<span class="help-block">$s_versionc</span>
		</div>
	</div>
	<div class="form-group">
		<label for="_p_bugtrack_summary_$id" class="col-md-2 control-label">$s_summary</label>
		<div class="col-md-10">
			<input id="_p_bugtrack_summary_$id" name="summary" size="60" type="text" placeholder="$s_summary" required="true" class="form-control" />
		</div>
	</div>
	<div class="form-group">
		<label for="_p_bugtrack_body_$id" class="col-md-2 control-label">$s_body</label>
		<div class="col-md-10">
			<textarea id="_p_bugtrack_body_$id" name="body" cols="60" rows="6"  placeholder="$s_body" required="true" class="form-control"></textarea>
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-offset-2 col-md-10">
			<button type="submit" class="btn btn-primary"><span class="fa fa-check"></span>$s_submit</button>
		</div>
	</div>
</form>
EOD;

	return $body;
}

// Add new issue
function plugin_bugtrack_action()
{
	global $vars;
	global $_plugin_bugtrack, $_string;

	// if (PKWK_READONLY) die_message('PKWK_READONLY prohibits editing');
	if (Auth::check_role('readonly')) die_message($_string['prohibit']);
	if (Auth::is_check_role(PKWK_CREATE_PAGE)) die_message(str_replace('PKWK_CREATE_PAGE','PKWK_READONLY',$_string['prohibit']));
	if ($vars['mode'] != 'submit') return FALSE;

	// Vaildation foreign values(by miko)
	$spam = ( (!in_array($vars['priority'], $_plugin_bugtrack['priority_list'])) || (!in_array($vars['state'], $_plugin_bugtrack['state_list'])) ) ? TRUE : FALSE;

	if ($spam) {
		honeypot_write();
		return array('msg'=>'cannot write', 'body'=>'<p>prohibits editing</p>');
	}

	$page = plugin_bugtrack_write($vars['base'], $vars['pagename'], $vars['summary'],
		$vars['name'], $vars['priority'], $vars['state'], $vars['category'],
		$vars['version'], $vars['body']);

	Utility::redirect(get_page_location_uri($page));
	exit;
}

function plugin_bugtrack_write($base, $pagename, $summary, $name, $priority, $state, $category, $version, $body)
{
	global $vars;

	$base     = strip_bracket($base);
	$pagename = strip_bracket($pagename);

	$postdata = plugin_bugtrack_template($base, $summary, $name, $priority,
		$state, $category, $version, $body);

	$id = $jump = 1;
	$page = $base . '/' . sprintf(PLUGIN_BUGTRACK_NUMBER_FORMAT, $id);
	while (is_page($page)) {
		$id   = $jump;
		$jump += 50;
		$page = $base . '/' . sprintf(PLUGIN_BUGTRACK_NUMBER_FORMAT, $jump);
	}
	$page = $base . '/' . sprintf(PLUGIN_BUGTRACK_NUMBER_FORMAT, $id);
	while (is_page($page))
		$page = $base . '/' . sprintf(PLUGIN_BUGTRACK_NUMBER_FORMAT, ++$id);

	if (empty($pagename)) {
		Factory::Wiki($page)->set($postdata);
	} else {
		$pagename = Utility::getPageName($pagename, $base);
		$wiki = Factory::Wiki($page);
		if ($wiki->isValied()) {
			$pagename = $page; // Set default
		} else {
			$wiki->set('move to [[' . $pagename . ']]');
		}
		Factory::Wiki($pagename)->set($postdata);
	}

	return $page;
}

// Generate new page contents
function plugin_bugtrack_template($base, $summary, $name, $priority, $state, $category, $version, $body)
{
	global $_plugin_bugtrack, $WikiName;

	if (! preg_match("/^$WikiName$$/",$base)) $base = '[[' . $base . ']]';
	if ($name != '' && ! preg_match("/^$WikiName$$/",$name)) $name = '[[' . $name . ']]';

	if ($name    == '') $name    = $_plugin_bugtrack['noname'];
	if ($summary == '') $summary = $_plugin_bugtrack['nosummary'];
	$utime = UTIME;

	 return <<<EOD
#navi(../)
* $summary

- ${_plugin_bugtrack['base'    ]}: $base
- ${_plugin_bugtrack['name'    ]}: $name
- ${_plugin_bugtrack['priority']}: $priority
- ${_plugin_bugtrack['state'   ]}: $state
- ${_plugin_bugtrack['category']}: $category
- ${_plugin_bugtrack['date'    ]}: &epoch($utime);
- ${_plugin_bugtrack['version' ]}: $version

*${_plugin_bugtrack['body']}
$body
--------

#comment
EOD;
}

// ----------------------------------------
// BugTrack-List plugin

// #bugtrack_list plugin itself
function plugin_bugtrack_list_convert()
{
	global $vars, $_plugin_bugtrack;

	$page = $vars['page'];
	if (func_num_args()) {
		list($_page) = func_get_args();
		$_page = get_fullname(strip_bracket($_page), $page);
		if (is_pagename($_page)) $page = $_page;
	}

	$data = array();
	$pattern = $page . '/';
	$pattern_len = strlen($pattern);
	foreach (Auth::get_existpages() as $page)
		if (strpos($page, $pattern) === 0 && is_numeric(substr($page, $pattern_len)))
			array_push($data, plugin_bugtrack_list_pageinfo($page));

	$count_list = count($_plugin_bugtrack['state_list']);

	$table = array();
	for ($i = 0; $i <= $count_list + 1; ++$i) $table[$i] = array();

	foreach ($data as $line) {
		list($page, $no, $summary, $name, $priority, $state, $category) = $line;
		foreach (array('summary', 'name', 'priority', 'state', 'category') as $item)
			$$item = htmlsc($$item);
		$page_link = make_pagelink($page);

		$state_no = array_search($state, $_plugin_bugtrack['state_sort']);
		if ($state_no === NULL || $state_no === FALSE) $state_no = $count_list;
		$bgcolor = htmlsc($_plugin_bugtrack['state_bgcolor'][$state_no]);

		$row = <<<EOD
	<tr>
		<td style="background-color:$bgcolor">$page_link</td>
		<td style="background-color:$bgcolor">$state</td>
		<td style="background-color:$bgcolor">$priority</td>
		<td style="background-color:$bgcolor">$category</td>
		<td style="background-color:$bgcolor">$name</td>
		<td style="background-color:$bgcolor">$summary</td>
	</tr>
EOD;
		$table[$state_no][$no] = $row;
	}

	$table_html = '<thead>' . "\n" . '	<tr>'. "\n";
	$bgcolor = htmlsc($_plugin_bugtrack['header_bgcolor']);
	foreach (array('pagename', 'state', 'priority', 'category', 'name', 'summary') as $item)
		$table_html .= '  <th style="background-color:' . $bgcolor . '">' .
			htmlsc($_plugin_bugtrack[$item]) . '</th>' . "\n";
	$table_html .= '	</tr>' . "\n" . '</thead>' ."\n" . '<tbody>' . "\n";

	for ($i = 0; $i <= $count_list; ++$i) {
		ksort($table[$i], SORT_NUMERIC);
		$table_html .= join("\n", $table[$i]);
	}

	$table_html .= '</tbody>'."\n";

	return '<table class="table">' . "\n" .
		$table_html . "\n" .
		'</table>';
}

// Get one set of data from a page (or a page moved to $page)
function plugin_bugtrack_list_pageinfo($page, $no = NULL, $recurse = TRUE)
{
	global $_plugin_bugtrack;

	if ($no === NULL)
		$no = preg_match('/\/([0-9]+)$/', $page, $matches) ? $matches[1] : 0;

	$source = Factory::Wiki($page)->get();

	// Check 'moved' page _just once_
	$regex  = '/move\s*to\s*(' . RendererDefines::WIKINAME_PATTERN . '|' .
		RendererDefines::INTERWIKINAME_PATTERN .'|\[\[' . RendererDefines::BRACKETNAME_PATTERN . '\]\])/';
	$match  = array();
	if ($recurse && preg_match($regex, $source[0], $match))
		return plugin_bugtrack_list_pageinfo(Utility::stripBracket($match[1]), $no, FALSE);

	$body = join("\n", $source);
	foreach(array('summary', 'name', 'priority', 'state', 'category') as $item) {
		$regex = '/-\s*' . preg_quote($_plugin_bugtrack[$item], '/') . '\s*:(.*)/';
		if (preg_match($regex, $body, $matches)) {
			if ($item == 'name') {
				$$item = Utility::stripBracket(trim($matches[1]));
			} else {
				$$item = trim($matches[1]);
			}
		} else {
				$$item = ''; // Data not found
		}
	}

	if (preg_match("/\*([^\n]*)/", $body, $matches)) {
		$summary = Rules::removeHeading($matches[0]);
	}

	return array($page, $no, $summary, $name, $priority, $state, $category);
}
/* End of file bugtrack.inc.php */
/* Location: ./wiki-common/plugin/bugtrack.inc.php */