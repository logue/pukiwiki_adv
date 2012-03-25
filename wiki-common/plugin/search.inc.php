<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: search.inc.php,v 1.14.4 2011/02/05 12:37:00 Logue Exp $
//
// Search plugin

// Allow search via GET method 'index.php?plugin=search&word=keyword'
// NOTE: Also allows DoS to your site more easily by SPAMbot or worm or ...
define('PLUGIN_SEARCH_DISABLE_GET_ACCESS', 0); // 1, 0

define('PLUGIN_SEARCH_MAX_LENGTH', 80);
define('PLUGIN_SEARCH_MAX_BASE',   16); // #search(1,2,3,...,15,16)

function plugin_search_init()
{
	$msg = array(
		'_search_msg' => array(
			'title_search'	=> T_('Search for word(s)'),
			'title_result'	=> T_('Search result of  $1'),
			'msg_searching'	=> T_('Key words are case-insenstive, and are searched for in all pages.'),
			'btn_search'	=> T_('Search'),
			'btn_and'		=> T_('AND'),
			'btn_or'		=> T_('OR'),
			'search_pages'	=> T_('Search for page starts from $1'),
			'search_all'	=> T_('Search for all pages'),
			'search_words'	=> T_('Search words')
		)
	);
	set_plugin_messages($msg);
}


// Show a search box on a page
function plugin_search_convert()
{
	static $done;

	if (isset($done)) {
		return '#search(): You already view a search box<br />' . "\n";
	} else {
		$done = TRUE;
		$args = func_get_args();
		return plugin_search_search_form('', '', $args);
	}
}

function plugin_search_action()
{
	global $post, $vars;
	global $_search_msg;

	if (PLUGIN_SEARCH_DISABLE_GET_ACCESS) {
		$s_word = isset($post['word']) ? htmlsc($post['word']) : '';
	} else {
		$s_word = isset($vars['word']) ? htmlsc($vars['word']) : '';
	}
	if (strlen($s_word) > PLUGIN_SEARCH_MAX_LENGTH) {
		unset($vars['word']); // Stop using $_msg_word at lib/html.php
		die_message('Search words too long');
	}

	$type = isset($vars['type']) ? $vars['type'] : '';
	$base = isset($vars['base']) ? $vars['base'] : '';
	$format = isset($vars['format']) ? $vars['format'] : 'html';

	switch ($format) {
		case 'xml' :
			if ($s_word !== '') {
				global $page_title, $notify_from;
				$ret = array(
					'<?xml version="1.0" encoding="UTF-8"?>',
					'<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">',
					'<ShortName>' . $_search_msg['title_search'] . '</ShortName>',
					'<Contact>' . $notify_from . '</Contact>',
					'<Url type="text/html" method="GET" template="' . get_cmd_uri('search', null, null, array('term'=>'')) . '{searchTerm}"/>',
					'<InputEncoding>UTF-8</InputEncoding>',
					'<Description>' . $_search_msg['title_search'] . ' - ' . $page_title . '</Description>',
					'<moz:SearchForm xmlns:moz="http://www.mozilla.org/2006/browser/search/">' . get_cmd_uri('search', null, null, array('term'=>'')) . '</moz:SearchForm>',
					'</OpenSearchDescription>'
				);
				header('Content-Type:text/xml');
				echo join("\n", $ret);
				exit;
			}
		break;
	
		default : 
			if ($s_word !== '') {
				// Search
				$msg  = str_replace('$1', $s_word, $_search_msg['title_result']);
				$body = do_search($vars['word'], $type, FALSE, $base);
			} else {
				// Init
				unset($vars['word']); // Stop using $_msg_word at lib/html.php
				$msg  = $_search_msg['title_search'];
				$body = '<p>'.$_search_msg['msg_searching'].'</p>' . "\n";
			}

			// Show search form
			$bases = ($base == '') ? array() : array($base);
			$body .= plugin_search_search_form($s_word, $type, $bases);

			return array('msg'=>$msg, 'body'=>$body);
		break;
	}
}

function plugin_search_search_form($s_word = '', $type = '', $bases = array())
{
	global $_search_msg;

	$and_check = $or_check = '';
	$base_option = '';

	$ret[] = '<form action="'. get_script_uri() .'" method="' . ( (! PLUGIN_SEARCH_DISABLE_GET_ACCESS) ? 'get' : 'post' ) .'" class="search_form" role="search">';
	$ret[] = '<input type="hidden" name="cmd" value="search" />';
	$ret[] = '<input type="search"  name="word" value="' . $s_word . '" size="20" maxlength="' . PLUGIN_SEARCH_MAX_LENGTH . '" id="search_word" results="5" autosave="tangerine" placeholder="' . $_search_msg['search_words'] . '" />';
	$ret[] = ( IS_MOBILE ) ? '<fieldset data-role="controlgroup" data-type="horizontal" >' : null;
	$ret[] = '<input type="radio" name="type" id="_p_search_AND" value="AND" ' . ( ($type === 'OR') ? '' : 'checked="checked" ' ) . '/>';
	$ret[] = '<label for="_p_search_AND">' . $_search_msg['btn_and'] . '</label>';
	$ret[] = '<input type="radio" name="type" id="_p_search_OR" value="OR" ' . ( ($type === 'OR') ? 'checked="checked" ' : '' ) . '/>';
	$ret[] = '<label for="_p_search_OR">' . $_search_msg['btn_or'] . '</label>';
	$ret[] = ( IS_MOBILE ) ? '</fieldset>' : null;

	
	if (!empty($bases)) {
		$ret[] = ( IS_MOBILE ) ? null : '<br />';
		
		$_num = 0;
		$check = ' checked="checked"';
		$ret[] = (IS_MOBILE) ? '<fieldset data-role="controlgroup" data-mini="true">' : null;
		foreach($bases as $base) {
			++$_num;
			if (PLUGIN_SEARCH_MAX_BASE < $_num) break;
			$s_base = htmlsc($base);
			$ret[] = '<input type="radio" name="base" id="_p_search_base_id_' . $_num . '" value="' . $s_base . '" />';
			$ret[] = '<label for="_p_search_base_id_' . $_num . '">' . str_replace('$1', '<strong>' . $s_base . '</strong>', $_search_msg['search_pages']) . '</label>';
		}
		$ret[] = '<input type="radio" name="base" id="_p_search_base_id_all" value="" checked="checked" />';
		$ret[] = '<label for="_p_search_base_id_all">' . $_search_msg['search_all'] . '</label>';
		$ret[] = (IS_MOBILE) ? '</fieldset>' : null;
	}
	$ret[] = '<input type="submit" value="' . $_search_msg['btn_search'] . '" />';
	$ret[] = '</form>';
	
	return join("\n", $ret);
}
?>
