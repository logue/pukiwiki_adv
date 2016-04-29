<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: search.inc.php,v 1.14.5 2016/04/19 21:12:00 Logue Exp $
//
// Search plugin

// Allow search via GET method 'index.php?plugin=search&word=keyword'
// NOTE: Also allows DoS to your site more easily by SPAMbot or worm or ...
use PukiWiki\Router;
use PukiWiki\Search;
use PukiWiki\Utility;

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
//	static $done;

	if (isset($done)) {
		return '<p class="alert alert-info">#search(): You already view a search box</p>' . "\n";
	} else {
		$done = TRUE;
		$args = func_get_args();
		return plugin_search_search_form('', '', $args);
	}
}

function plugin_search_action()
{
	global $post, $vars;
	global $_search_msg, $_LANG;

/*
	if (isset($vars['update_index'])){
		PukiWiki\SearchLucene::updateIndex();
		return array('msg'=>'done.');
	}
*/
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
			// OpenSearch
			// http://www.opensearch.org/
			global $site_name, $notify_from, $shortcut_icon;
			$ret = array(
				'<?xml version="1.0" encoding="UTF-8"?>',
				'<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/" xmlns:moz="http://www.mozilla.org/2006/browser/search/">',
					'<ShortName>' . $_search_msg['title_search'] .' - ' . $site_name . '</ShortName>',
					'<Description>' . $_search_msg['title_search'] . ' - ' . $site_name . '</Description>',
					'<Contact>' . $notify_from . '</Contact>',
					'<Image height="16" width="16" type="image/x-icon">data:image/x-icon;base64,AAABAAEAEBAAAAEAIABoBAAAFgAAACgAAAAQAAAAIAAAAAEAIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA%2F%2F%2F%2FBP%2F8%2BjT%2F7%2BdK%2F%2F38M%2F%2F%2F%2Fw0AAAAAAAAAAAAAAAAAAAAA%2F%2F%2F%2FC%2F%2F%2B%2FTH%2F7%2BdK%2F%2Fv4N%2F%2F%2F%2FwUAAAAA%2F%2F%2F%2FBf%2Ft5IH%2F5djE%2F%2FLsn%2F7u5pT63tKe%2BNzLkOjFmnnq27p%2B%2BOfXk%2Fnl06z%2B7eOV%2F%2FLsn%2F%2Fm2cD%2F6%2BGM%2F%2F%2F%2FBv%2F072P%2F5til%2F%2F%2F%2FGfz4%2BB%2FrqJOE9qBr7vukUv3%2FzoL%2F%2F%2Bao%2F%2Fznsv7337j4662LwPXWyyX%2F%2F%2F8Y%2F%2Bfbmv%2Fz7XD%2F7uaJ%2F%2FDohf%2F%2F%2FwH729Ib9loZzv97Jv7%2Fo0v%2F%2F%2BOt%2F%2F%2Fmrf%2F%2F0oD%2F%2F9KC%2F%2Fm3bu3vsZopAAAAAP%2Fw6H7%2F7uaQ%2FvHpgP%2Fy7JL%2F9vQW%2F1kvvf9UEef%2Fhj7v%2F%2FPP9%2F%2F95Pv%2F9df7%2F7FQ%2Bf%2BxUPT%2F0IPq%2F8V3xf%2F69RT%2F8uyR%2FvHphv%2F28Vr%2F9vGB%2F6qWsf9jK%2Bb%2FhEjm%2F7iF5v%2F16Ob%2F59Ln%2F9iw5%2F%2BZL%2Bf%2Fnzvm%2F%2Bq15v%2B1Zeb%2Bxp6x%2F%2Fbygf%2F07mf%2F%2F%2F8R%2FvHqkP68p9f%2FoW7o%2F4BL5v%2BCROb%2FfCrm%2F4Al5v%2BRPOb%2FvIXm%2F9Sp5v%2Fuxeb%2FsnHn%2F8Ci3f7x6o7%2F%2F%2F8XAAAAAP%2F38lz9o4Tu%2F9K28%2F%2FUs%2Bb%2FYxrm%2F3os5v%2BWSeb%2F1LPm%2F%2F%2F%2F5v%2F%2F%2Feb%2F9Nfm%2F9Wy8v%2BceO7%2F9fBoAAAAAAAAAAD%2F%2BfZD%2Brig8%2F%2FRtvH%2F8OLv%2F5dg5v%2BOSub%2FnFjm%2F8KW5v%2F%2F%2F%2Bb%2F%2F%2F7m%2F%2FPg7v%2FMqfH%2FsZnz%2F%2FfzTQAAAAD%2F%2F%2F8J%2F%2FDpku2EZcL8up71%2F%2BfW8v%2FUu%2B7%2Fp3Dm%2F6hw5v%2FKo%2Bb%2F4s7m%2F8ip7f%2FkzvP%2FzbD1%2FXxazP%2Fw6ZH%2F%2F%2F8O%2F%2Fn1SP%2F18ILtooy19Zt16v%2FUvfL%2Fz7Xy%2F9G07%2F%2FDmub%2F1bnm%2F%2FDn7v%2Fw4%2FL%2F38jy%2FYhb6%2FeTfbn%2F9fCB%2F%2FfyVf%2Fy63v%2F9O6N%2FPX1Hed%2BW7z%2BuZjq%2F%2Bja9P%2Fo2PH%2F4c3y%2F9i%2F8v%2Ft3%2FL%2F4s%2F0%2FKV%2B6%2BlUL8f77%2B0c%2F%2FTujf%2Fy64H%2F7OKA%2F%2FTvhv%2F%2F%2FwL35OAa9rmgwP%2F58On%2B7eL2%2F%2Bre%2BP7j1Pj%2B4M72%2BrCN6uNeOcb22dQi%2F%2F%2F%2FAf%2F074H%2F7OKG%2F%2FLsbf%2Fs4qj%2F%2F%2F8J%2F%2F%2F%2FDv3y8EX84tbL%2Fenh2v7m2vn93s%2F6%2BMq42%2FS6p8z77OhM%2F%2F%2F%2FD%2F%2F%2F%2Fwj%2F7eSc%2F%2FHqev%2F%2F%2Fwn%2F6NyN%2F%2BjcvP%2Fw6KH%2F8ema%2F%2B%2Fmk%2F%2Fv54%2F%2F%2B%2Fk2%2F%2Fv6MP%2Fv54z%2F7%2BaU%2F%2FDpmf%2Fw6KH%2F6d65%2FubZmP%2F%2F%2FwwAAAAA%2F%2F%2F%2FBf%2F59UP%2B7eNl%2FvPtVv%2F%2F%2Fx7%2F%2F%2F8CAAAAAAAAAAD%2F%2F%2F8C%2F%2F%2F%2FG%2F%2F07lP%2B7eNl%2F%2Fj0R%2F%2F%2F%2FwcAAAAAg8GsQQAArEEAAKxBAASsQQAArEEAAKxBAACsQYABrEGAAaxBAACsQQAArEEAAKxBAACsQQAArEEAAKxBgYGsQQ%3D%3D</Image>',
					'<Language>' . DEFAULT_LANG . '</Language>',
					'<InputEncoding>UTF-8</InputEncoding>',
					'<OutputEncoding>UTF-8</OutputEncoding>',
						'<Url type="text/html" method="' . ( (! PLUGIN_SEARCH_DISABLE_GET_ACCESS) ? 'get' : 'post' ) .'" template="' . Router::get_script_uri() . '">',
						'<Param name="cmd" value="search" />',
						'<Param name="encode_hint" value="' . PKWK_ENCODING_HINT . '" />',
						'<Param name="type" value="AND" />',
						'<Param name="word" value="{searchTerms}" />',
					'</Url>',
					'<Url type="application/x-suggestions+json" template="' . Router::get_cmd_uri('list', null, null,  array('type'=>'json')) . '&amp;word={searchTerms}" />',
					'<moz:SearchForm>' . Router::get_cmd_uri('search'). '</moz:SearchForm>',
				'</OpenSearchDescription>'
			);
			header('Content-Type:application/opensearchdescription+xml');
			echo join("\n", $ret);
			exit;
		break;
		default : 
			if ($s_word !== '') {
				// Search
				$msg  = str_replace('$1', $s_word, $_search_msg['title_result']);
				$body = Search::do_search($vars['word'], $type, FALSE, $base);
			} else {
				// Init
				unset($vars['word']); // Stop using $_msg_word at lib/html.php
				$msg  = $_search_msg['title_search'];
				$body = '<p>'.$_search_msg['msg_searching'].'</p>' . "\n";
			}

			// Show search form
			$bases = ($base == '') ? array() : array($base);
			$body .= plugin_search_search_form($s_word, $type, $bases);
		break;
	}
	return array('msg'=>$msg, 'body'=>$body);
}

function plugin_search_search_form($s_word = '', $type = '', $bases = array())
{
	global $_search_msg, $page_title;

	$and_check = $or_check = '';
	$base_option = '';

	$ret[] = '<form action="'. get_script_uri() .'" method="' . ( (! PLUGIN_SEARCH_DISABLE_GET_ACCESS) ? 'get' : 'post' ) .'" class="form-inline plugin-search-form" role="search">';
	$ret[] = '<input type="hidden" name="cmd" value="search" />';
	
	if ( IS_MOBILE ) {
		$ret[] = '<input type="search" name="word" value="' . $s_word . '" size="20" maxlength="' . PLUGIN_SEARCH_MAX_LENGTH . '" class="form-control suggest" results="5" autosave="'. $page_title .'" placeholder="' . $_search_msg['search_words'] . '" />';
		$ret[] = '<fieldset data-role="controlgroup" data-type="horizontal" >';
		$ret[] = '<input type="radio" name="type" id="_p_search_AND" value="AND" ' . ( ($type === 'OR') ? '' : 'checked="checked" ' ) . '/>';
		$ret[] = '<label for="_p_search_AND">' . $_search_msg['btn_and'] . '</label>';
		$ret[] = '<input type="radio" name="type" id="_p_search_OR" value="OR" ' . ( ($type === 'OR') ? 'checked="checked" ' : '' ) . '/>';
		$ret[] = '<label for="_p_search_OR">' . $_search_msg['btn_or'] . '</label>';
		$ret[] = '</fieldset>';
	}else{
		$ret[] = '<div class="form-group">';
		$ret[] = '<input type="search" name="word" value="' . $s_word . '" size="20" maxlength="' . PLUGIN_SEARCH_MAX_LENGTH . '" class="form-control suggest" results="5" autosave="'. $page_title .'" placeholder="' . $_search_msg['search_words'] . '" />';
		$ret[] = '</div>';
		$ret[] = '<label class="checkbox">';
		$ret[] = '<input type="radio" name="type" value="AND" ' . ( ($type === 'OR') ? '' : 'checked="checked" ' ) . '/>';
		$ret[] = $_search_msg['btn_and'] . '</label>';
		$ret[] = '<label class="checkbox">';
		$ret[] = '<input type="radio" name="type"  value="OR" ' . ( ($type === 'OR') ? 'checked="checked" ' : '' ) . '/>';
		$ret[] = $_search_msg['btn_or'] . '</label>';
	}

	if (!empty($bases)) {
		$ret[] = ( IS_MOBILE ) ? null : '<br />';
		
		$_num = 0;
		$check = ' checked="checked"';
		$ret[] = (IS_MOBILE) ? '<fieldset data-role="controlgroup" data-mini="true">' : null;
		foreach($bases as $base) {
			++$_num;
			if (PLUGIN_SEARCH_MAX_BASE < $_num) break;
			$s_base = Utility::htmlsc($base);
			$ret[] = '<input type="radio" name="base" id="_p_search_base_id_' . $_num . '" value="' . $s_base . '" />';
			$ret[] = '<label for="_p_search_base_id_' . $_num . '">' . str_replace('$1', '<strong>' . $s_base . '</strong>', $_search_msg['search_pages']) . '</label>';
		}
		$ret[] = '<input type="radio" name="base" id="_p_search_base_id_all" value="" checked="checked" />';
		$ret[] = '<label for="_p_search_base_id_all">' . $_search_msg['search_all'] . '</label>';
		$ret[] = (IS_MOBILE) ? '</fieldset>' : null;
	}
	$ret[] = '<input type="submit" class="btn btn-info" value="' . $_search_msg['btn_search'] . '" />';
	$ret[] = '</form>';
	
	return join("\n", $ret);
}
/* End of file search.inc.php */
/* Location: ./wiki-common/plugin/search.inc.php */
