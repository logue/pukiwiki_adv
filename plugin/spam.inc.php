<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: spam.inc.php,v 1.9.1 2010/12/26 19:43:00 Logue Exp $
// Copyright (C) 
//    2010      PukiWiki Advance Developers Team
//    2003-2005, 2007 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// lib/spam.php related maintenance tools

function plugin_spam_init(){
	$msg = array(
		'_spam_messages' => array(
			'title'			=> T_('Spam tools: '),
			'title_menu'	=> T_('Menu'),
			'err_prohibit'	=> T_('PKWK_READONLY prohibits this'),
			'label_start'	=> T_('Start from: '),
			'label_sort'	=> T_('Sort (heavy)'),
			'label_pass'	=> T_('Pass: '),
			'check'			=> T_('Check'),
			'msg_pages'		=> T_('Check existing pages. (badhost only at this time)'),
			'msg_found'		=> T_('FOUND at %s.'),
			'msg_hits'		=> T_('Pages: %1s hit / %2s checked / %3s all'),
			'title_pages'	=> T_('Pages')
		)
	);
	set_plugin_messages($msg);
}

// Menu and dispatch
function plugin_spam_action()
{
	global $vars, $_spam_messages;

	if (PKWK_READONLY) die_message($_spam_messages['msg_prohibit']);

	// Dispatch
	$mode = isset($vars['mode']) ? $vars['mode'] : '';
	if ($mode == 'pages') {
		return plugin_spam_pages();
	}
	// TODO:
	// Checking own backup/*.gz, backup/*.txt for determine the clearance
	// Check text
	// Check attach

	$script = get_script_uri() . '?cmd=spam';
	$body   = 'Choose one: ' . "\n" .
		'<a href="'. get_cmd_uri('spam','','',array('mode'=>'pages')) . '">'.$_spam_messages['title_pages'].'</a>' . "\n"
		;
	return array('msg'=>$_spam_messages['title'].$_spam_messages['title_menu'], 'body'=>nl2br($body));
}

// mode=pages: Check existing pages
function plugin_spam_pages()
{
	require_once(LIB_DIR . 'spam.php');
	require_once(LIB_DIR . 'spam_pickup.php');

	global $vars, $post, $_msg_invalidpass, $_spam_messages;

	$ob      = ob_get_level();
	$script  = get_script_uri() . '?plugin=spam&mode=pages';

	$start   = isset($post['start']) ? $post['start'] : NULL;
	$s_start = ($start === NULL) ? '' : htmlspecialchars($start);
	$pass    = isset($post['pass']) ? $post['pass'] : NULL;
	$sort    = isset($post['sort']);
	$s_sort  = $sort ? ' checked' : '';
	$per     = 100;

	$form    = <<<EOD
<fieldset>
	<legend>{$_spam_messages['msg_pages']}</legend>
	<form action="$script" method="post">
		<div class="spam_form">
			<label for="start">{$_spam_messages['label_start']}</label><input type="text" name="start" id="start" size="40" value="$s_start" /><br/>
			<input type="checkbox" name="sort" value="on" id="sort" $s_sort /><label for="sort">{$_spam_messages['label_sort']}</label><br />
			<label for="pass">{$_spam_messages['label_pass']}</label><input type="password" name="pass" id="pass" size="12" /><br />
			<input type="submit" name="check" value="{$_spam_messages['check']}" />
		</div>
	</form>
</fieldset>
EOD;

	if ($pass !== NULL && pkwk_login($pass)) {
		// Check and report

		$method = array(
			'_comment'     => '_default',
			//'quantity'     =>  8,
			//'non_uniquri'  =>  3,
			//'non_uniqhost' =>  3,
			//'area_anchor'  =>  0,
			//'area_bbcode'  =>  0,
			//'uniqhost'     => TRUE,
			'badhost'      => TRUE,
			//'asap'         => TRUE, // Stop as soon as possible (quick but less-info)
		);

		echo $form;
		flush();
		if ($ob) @ob_flush();

		$pages = get_existpages();
		if ($sort) sort($pages, SORT_STRING);

		$count = $search = $hit = 0;
		foreach($pages as $pagename)
		{
			++$count;
			if ($start !== '') {
				if ($start == $pagename) {
					$start = '';
				} else {
					continue;
				}
			}
			++$search;
			if ($search % $per == 0) {
				flush();
				if ($ob) @ob_flush();
			}

			$progress = check_uri_spam(get_source($pagename, TRUE, TRUE), $method);
			if (empty($progress['is_spam'])) {
				echo htmlspecialchars($pagename);
				echo '<br />' . "\n";
			} else {
				++$hit;
				echo '<div style="padding: 0pt 0.7em;" class="ui-state-error ui-corner-all">'.
					'<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: 0.3em;"></span>'.
					sprintf($_spam_messages['msg_found'],htmlspecialchars($pagename)).'</p>';
				echo '<p>' . "\n";
				$tmp = summarize_detail_badhost($progress);
				if ($tmp != '') {
					echo '&nbsp; DETAIL_BADHOST: ' . 
						str_replace('  ', '&nbsp; ', nl2br(htmlspecialchars($tmp). "\n"));
				}
			}
		}
		echo '</p>' . "\n";
		echo '<hr />' . "\n";
	
		echo sprintf( $_spam_messages['msg_hits'], $hit, $search, $count);

		exit;
	}

	$body  = ($pass === NULL) ? '' : '<p><strong>' . $_msg_invalidpass . '</strong></p>' . "\n";
	$body .= $form;
	return array('msg'=>$_spam_messages['title'].$_spam_messages['title_pages'], 'body'=>$body);
}

?>
