<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: interwiki.inc.php,v 1.10.2 2010/12/26 17:17:00 Logue Exp $
//
// InterWiki redirection plugin (OBSOLETE)

function plugin_interwiki_action()
{
	global $vars, $InterWikiName;

	// if (PKWK_SAFE_MODE) die_message('InterWiki plugin is not allowed');
	if (auth::check_role('safemode')) die_message(T_('InterWiki plugin is not allowed'));

	$match = array();
	if (! preg_match("/^$InterWikiName$/", $vars['page'], $match))
		return plugin_interwiki_invalid();

	$url = get_interwiki_url($match[2], $match[3]);
	if ($url === FALSE) return plugin_interwiki_invalid();

	pkwk_headers_sent();
	header('Location: ' . $url);
	exit;
}

function plugin_interwiki_invalid()
{
	return array(
		'msg'  => T_('This is not a valid InterWikiName'),
		'body' => str_replace(array('$1', '$2'),
			array(htmlspecialchars(''),
			make_pagelink('InterWikiName')),
			T_(' $1 is not a valid $2.')));
}
?>
