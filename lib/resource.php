<?php
// PukiWiki Plus! - Yet another WikiWikiWeb clone.
// $Id: resource.php,v 0.8.2 2010/12/22 15:38:00 Logue Exp $
//
// Resource of String
// Warning: This file is PukiWiki "core" resource strings.
//          Please Without customizing it. 

$help_page = 'Help';
$rule_page = 'FormatRule';

$weeklabels = array(
	T_('Sun'),T_('Mon'),T_('Tue'),T_('Wed'),T_('Thu'),T_('Fri'),T_('Sat'),
);

$_string = array(
	// Common core string(s)
	'freeze'	=> T_('Freeze'),
	'unfreeze'	=> T_('Unfreeze'),
	'symbol'	=> T_('Symbols'),
	'other'		=> T_('Others'),

	// Common core message(s)
	'andresult'			=> T_('In the page <strong> $2</strong>, <strong> $3</strong> pages that contain all the terms $1 were found.'),
	'orresult'			=> T_('In the page <strong> $2</strong>, <strong> $3</strong> pages that contain at least one of the terms $1 were found.'),
	'notfoundresult'	=> T_('No page which contains $1 has been found.'),
	'word'				=> T_('These search terms have been highlighted:'),
	'help'				=> T_('View Text Formatting Rules'),

	// Common core error message(s)
	'invalidpass'		=> T_('Invalid password.'),
	'invalidiwn'		=> T_('$1 is not a valid $2.'),
	'collided_comment'	=> T_('It seems that someone has already updated the page you were editing.<br />The string was added, alhough it may be inserted in the wrong position.<br />'),

	// Error messages
	'warning'			=> T_('WARNING'),
	'readonly_msg'		=> T_('This Wiki is <code>PKWK_READONLY</code> mode now. Therefore, edit is prohibited.'),
	'error_msg'			=> T_('A runtime error has occurred.').'<br />'.T_('Please contact to site admin. If you want more information, please change <code>PKWK_WARNING</code> value.'),
	'debugmode'			=> T_('This program is running in debug mode.'),
	'changeadminpass'	=> sprintf(T_('<code>$adminpass</code> is not changed! Click <a href="%s">here</a> to generate crypted password and modify auth.ini.php!'),get_cmd_uri('md5')),
	'not_writable'		=> T_('<code>%s</code> is not found or not writable.'),
	'not_found'			=> T_('Page <code>%s</code> was not found.')
);

$_button = array(
	// Native button
	'preview'	=> T_('Preview'),
	'repreview'	=> T_('Preview again'),
	'update'	=> T_('Update'),
	'cancel'	=> T_('Cancel'),
	'add'		=> T_('Add'),
	'search'	=> T_('Search'),
	'load'		=> T_('Load'),
	'edit'		=> T_('Edit'),
	'guiedit'	=> T_('Edit(GUI)'),
	'delete'	=> T_('Delete'),
	'remove'	=> T_('Remove'),

	// CheckBox labels
	'notchangetimestamp'	=> T_('Do not change timestamp'),
	'addtop'				=> T_('Add to top of page'),
	'template'				=> T_('Use page as template'),
	'and'					=> T_('AND'),
	'or'					=> T_('OR'),
	'cookie'				=> T_('Save to cookie')
);

$_title = array(
	// Message title
	'cannotedit'	=> T_('$1 is not editable'),
	'cannotread'	=> T_('$1 is not readable'),
	'collided'		=> T_('On updating $1, a collision has occurred.'),
	'updated'		=> T_('$1 was updated'),
	'preview'		=> T_('Preview of  $1'),
	'error'			=> T_('Runtime Error'),
);


// Encoding hint
$_LANG['encode_hint'] = T_('encode_hint');

$_LANG['skin'] = array(
	'add'		=> T_('Add'),
	'backup'	=> T_('Backup'),
	'brokenlink'=> T_('Broken Link List'),
	'copy'		=> T_('Copy'),
	'diff'		=> T_('Diff'),
	'edit'		=> T_('Edit'),
	'guiedit'	=> T_('Edit(GUI)'),
	'filelist'	=> T_('List of page files'),
	'freeze'	=> T_('Freeze'),
	'help'		=> T_('Help'),
	'list'		=> T_('List of pages'),
	'new'		=> T_('New'),
	'newsub'	=> T_('Lower page making'),
	'rdf'		=> T_('RDF of recent changes'),
	'recent'	=> T_('Recent changes'),
	'referer'	=> T_('Referer'),
	'reload'	=> T_('Reload'),
	'rename'	=> T_('Rename'),
	'print'		=> T_('Image of print'),
	'full'		=> T_('Full screen'),
	'rss'		=> T_('RSS of recent changes'),
	'rss10'		=> T_('RSS of recent changes'),
	'rss20'		=> T_('RSS of recent changes'),
	'rssplus'	=> T_('RSS of recent changes'),
	'mixirss'	=> T_('RSS of recent changes'),
	'search'	=> T_('Search'),
	'source'	=> T_('Source'),
	'template'	=> T_('Template'),
	'top'		=> T_('Front page'),
	'trackback'	=> T_('Trackback'),
	'unfreeze'	=> T_('Unfreeze'),
	'upload'	=> T_('Upload'),
	'skeylist'	=> T_('Search Key List'),
	'linklist'	=> T_('Link List'),
	'log_login'	=> T_('Roll Book'),
	'log_check'	=> T_('Confirmation list'),
	'log_browse'=> T_('Browse Log'),
	'log_update'=> T_('Update Log'),
	'log_down'	=> T_('Download Log'),
	'log'		=> T_('Log'),
	'logo'		=> T_('Logo'),

	'menu'		=> T_('MenuBar'),
	'side'		=> T_('SideBar'),
	'glossary'	=> T_('Glossary')
);

?>
