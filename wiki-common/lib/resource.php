<?php
// PukiWiki Plus! - Yet another WikiWikiWeb clone.
// $Id: resource.php,v 0.8.3 2012/07/07 08:29:00 Logue Exp $
//
// Resource of String
// Warning: This file is PukiWiki "core" resource strings.
//          Please Without customizing it.

global $_labels, $_string, $_title, $_LANG, $_error_type;

$_labels = array(
	'week' => array(
		array(T_('Sun'), T_('Sunday')),
		array(T_('Mon'), T_('Monday')),
		array(T_('Tue'), T_('Tuesday')),
		array(T_('Wed'), T_('Wednesday')),
		array(T_('Thu'), T_('Thursday')),
		array(T_('Fri'), T_('Friday')),
		array(T_('Sat'), T_('Saturday'))
	),
	'month'=> array(
		1	=>array(T_('_Jan'), T_('January')),
		2	=>array(T_('_Feb'), T_('February')),
		3	=>array(T_('_Mar'), T_('March')),
		4	=>array(T_('_Apr'), T_('April')),
		5	=>array(T_('_May'), T_('May')),
		6	=>array(T_('_Jun'), T_('June')),
		7	=>array(T_('_Jul'), T_('July')),
		8	=>array(T_('_Aug'), T_('August')),
		9	=>array(T_('_Sep'), T_('September')),
		10	=>array(T_('_Oct'), T_('October')),
		11	=>array(T_('_Nov'), T_('November')),
		12	=>array(T_('_Dec'), T_('December'))
	)
);

$_string = array(
	// Common core string(s)
	'freeze'			=> T_('Freeze'),
	'unfreeze'			=> T_('Unfreeze'),
	'symbol'			=> T_('Symbols'),
	'other'				=> T_('Others'),

	// Common core message(s)
	'andresult'			=> T_('In the page <strong>$2</strong>, <strong>$3</strong> pages that contain all the terms <var>$1</var> were found.'),
	'orresult'			=> T_('In the page <strong>$2</strong>, <strong>$3</strong> pages that contain at least one of the terms <var>$1</var> were found.'),
	'notfoundresult'	=> T_('No page which contains <var>$1</var> has been found.'),
	'word'				=> T_('These search terms have been highlighted:'),
	'help'				=> T_('View Text Formatting Rules'),
	'updated'			=> T_('$1 was updated'),
	'back'				=> T_('Back to %s'),

	// Common core error message(s)
	'invalidpass'		=> T_('Invalid password.'),
	'invalidiwn'		=> T_('<var>$1</var> is not a valid <var>$2</var>.'),

	// Collision messages
	'title_collided'	=> T_('On updating $1, a collision has occurred.'),
	'msg_collided'		=> T_('It seems that someone has already updated this page while you were editing it.') . '<br />'."\n".
						   T_('+ is placed at the beginning of a line that was newly added.') . '<br />'."\n".
						   T_('! is placed at the beginning of a line that has possibly been updated.') . '<br />'."\n".
						   T_('Edit those lines, and submit again.'),
	'msg_collided_auto'	=> T_('It seems that someone has already updated this page while you were editing it.').'<br />'."\n" .
						   T_('The collision has been corrected automatically, but there may still be some problems with the page.').'<br />'."\n" .
						   T_('To confirm the changes to the page, press [Update].'),
	'comment_collided'	=> T_('It seems that someone has already updated this page while you were editing it.').'<br />'."\n".
						   T_('The string was added, alhough it may be inserted in the wrong position.'),
	'collided_caption'	=> T_('l : between backup data and stored page data.<br />r : between backup data and your post data.'),

	// Generic Error messages
	'warning'			=> T_('WARNING'),
	'error_msg'			=> T_('A runtime error has occurred.').'<br />'.T_('Please contact to site admin. If you want more information, please change <var>PKWK_WARNING</var> value.'),
	'debugmode'			=> T_('This program is running in debug mode.'),
	'changeadminpass'	=> sprintf(T_('<var>$adminpass</var> is not changed! Click <a href="%s">here</a> to generate crypted password and modify auth.ini.php!'),get_cmd_uri('md5')),
	'not_writable'		=> T_('<var>%s</var> is not found or not writable.'),
	'not_found'			=> T_('Page <var>%s</var> was not found.'),
	'not_found_msg1'	=> T_('Sorry, but the page you were trying to view does not exist or deleted.'),
	'not_found_msg2'	=> T_('Please check <a href="%1s" rel="nofollow">backups</a> or <a href="%2s" rel="nofollow">create page</a>.'),
	'require_auth'		=> T_('Authorization Required'),
	'require_auth_msg'	=> T_('This server could not verify that you are authorized to access the document requested. Either you supplied the wrong credentials (e.g., bad password), or your browser doesn\'t understand how to supply the credentials required.'),
	'redirect'			=> T_('Page Redirect'),
	'redirect_msg'		=> T_('The requested page has moved to a new URL. <br />Please click <a href="%s">here</a> if you do not want to move even after a while.'),
	'header_sent'		=> T_('Headers already sent at <var>%s</var>, line: <var>%s</var>.'),

	'illegal_chars'		=> T_('Illegal characters contained.'),
	'script_error'		=> T_('A fatal error has occured at line <var>%1s</var> in file <var>%2s</var>.'),
	'script_abort'		=> T_('Script execution has been aborted.'),

	// Prohibit messages
	'error_prohibit'	=> T_('This Wiki is <var>%s</var> mode now. The action which you are trying to do is prohibited.'),
	'blacklisted'		=> T_('Sorry, Your host is prohibited by <strong>IPBL</storng> (Blocking SPAM).'),
	'prohibit_country'	=> T_('Sorry, access from your country is prohibited.'),
	'not_readable'		=> T_('You have no permission to read this page.'),
	'not_editable'		=> T_('You have no permission to edit page or create page.'),
	

	// Plugin Error messages
	'plugin_init_error'			=> T_('Plugin init failed: <var>%s</var>'),
	'plugin_multiple_call'		=> T_('<var>%1s</var> was called over <var>%2s</var> times. SPAM or someting?'),
	'plugin_postid_error'		=> T_('PostId is mismatched. Is it multi-post?'),
	'plugin_encode_error'		=> T_('Incorrect encode. Please use a modern browser.'),
	'plugin_not_implemented'	=> T_('Plugin <var>%s</var> is not implemented.'),
	
	'feed_description' => T_('Recent changes of %s')
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
	'login'		=> T_('Login'),
	'logout'	=> T_('Logout'),

	// CheckBox labels
	'notchangetimestamp'	=> T_('Do not change timestamp'),
	'addtop'				=> T_('Add to top of page'),
	'template'				=> T_('Use page as template'),
	'and'					=> T_('AND'),
	'or'					=> T_('OR'),
	'cookie'				=> T_('Save to cookie'),
	'connect'				=> T_('Connect to %s'),

	// for Error page
	'back'		=> T_('Back'),
	'try_edit'		=> T_('Try to edit this page'),
	'return_home'	=> T_('Return to FrontPage')
);

$_title = array(
	// Message title
	'cannotedit'		=> T_('$1 is not editable'),
	'cannotread'		=> T_('$1 is not readable'),
	'collided'			=> T_('On updating $1, a collision has occurred.'),
	'updated'			=> T_('$1 was updated'),
	'preview'			=> T_('Preview of $1'),
	'error'				=> T_('Runtime Error'),	// 500
	'plugin_error'		=> T_('Plugin Error'),
	'prohibit'			=> T_('Access Prohibited'),	// 403
	'not_implemented'	=> T_('Not Implemented')	// 501
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
	'filelist'	=> T_('List of page files'),
	'freeze'	=> T_('Freeze'),
	'unfreeze'	=> T_('Unfreeze'),
	'full'		=> T_('Full screen'),
	'guiedit'	=> T_('Edit(GUI)'),
	'pdf'		=> T_('Export as PDF'),

	'help'		=> T_('Help'),
	'list'		=> T_('List of pages'),
	'log'		=> T_('Log'),
	'new'		=> T_('New'),
	'newsub'	=> T_('Lower page making'),
	'print'		=> T_('Image of print'),
	'rename'	=> T_('Rename'),
	'search'	=> T_('Search'),
	'template'	=> T_('Template'),
	'upload'	=> T_('Upload'),

	'trackback'	=> T_('Trackback'),
	'referer'	=> T_('Referer'),

	'reload'	=> T_('Reload'),
	'back'		=> T_('Back'),
	'source'	=> T_('Source'),

	'rss'		=> T_('RSS of recent changes'),
	'rdf'		=> T_('RDF of recent changes'),
	'atom'		=> T_('ATOM of recent changes'),

	'logo'		=> T_('Logo'),
	'formatrule'=> T_('Text Formatting Rules'),

	'login'		=> T_('Login'),
	'logout'	=> T_('Logout'),

	/* Special Page */
	'top'		=> T_('Front page'),
	'recent'	=> T_('Recent changes'),
	'deleted'	=> T_('Recent deleted'),
	'interwiki'	=> T_('Interwiki name'),
	'alias'		=> T_('Auto alias name'),
	'glossary'	=> T_('Glossary'),
	'menu'		=> T_('MenuBar'),
	'side'		=> T_('SideBar'),
	'navigation'=> T_('Navigation'),
	'head'		=> T_('Header area'),
	'foot'		=> T_('Footer area'),
	'protect'	=> T_('Protected'),

	// UI
	'site'		=> T_('Site'),
	'page'		=> T_('Pages'),
	'manage'	=> T_('Management'),
	'tool'		=> T_('Tools'),
	
	'attach_title'	=> T_('Attaches :'),
	'attach_info'	=> T_('File information'),
	'attach_count'	=> T_('%s download'),

	'related'	=> T_('Related pages: ')
);

$_error_type = array(
	1 =>	T_('Error'),
	2 =>	T_('Warning'),	// x
	4 =>	T_('Parsing Error'),
	8 =>	T_('Notice'),	// x
	16 =>	T_('Core Error'),
	32 =>	T_('Core Warning'),	// x
	64 =>	T_('Compile Error'),
	128 =>	T_('Compile Warning'),	// x
	256 =>	T_('User Error'),
	512 =>	T_('User Warning'),	// x
	1024 =>	T_('User Notice')	// x
);
/* End of file resource.php */
/* Location: ./wiki-common/lib/resource.php */
