<?php
// $Id: back.inc.php,v 1.10.5 2011/02/05 10:42:00 Logue Exp $
// Copyright (C)
//   2010      PukiWiki Advance Developers Team
//   2008      PukiWiki Plus! Developers Team
//   2003-2004 PukiWiki Developers Team
//   2002,2006 Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
//
// back plugin

use PukiWiki\Lib\Auth\Auth;

// Allow specifying back link by page name and anchor, or
// by relative or site-abusolute path
// define('PLUGIN_BACK_ALLOW_PAGELINK', PKWK_SAFE_MODE); // FALSE(Compat), TRUE
define('PLUGIN_BACK_ALLOW_PAGELINK', Auth::check_role('safemode')); // FALSE(Compat), TRUE

// Allow JavaScript (Compat)
define('PLUGIN_BACK_ALLOW_JAVASCRIPT', TRUE); // TRUE(Compat), FALSE, PKWK_ALLOW_JAVASCRIPT

// ----
define('PLUGIN_BACK_USAGE', '#back([text],[center|left|right][,0(no hr)[,Page-or-URI-to-back]])');
function plugin_back_convert()
{
	$_msg_back_word = T_('Back');
	if (func_num_args() > 4) return PLUGIN_BACK_USAGE;
	list($word, $align, $hr, $href) = array_pad(func_get_args(), 4, '');

	$word = trim($word);
	$word = ($word == '') ? $_msg_back_word : htmlsc($word);

	$align = strtolower(trim($align));
	switch($align){
	case ''			: $align = 'center';
					/*FALLTHROUGH*/
	case 'center'	:	/*FALLTHROUGH*/
	case 'left'		:	/*FALLTHROUGH*/
	case 'right'	: break;
	default			: return PLUGIN_BACK_USAGE;
	}

	$hr = (trim($hr) != '0') ? '<hr class="full_hr" />' . "\n" : '';

	$link = TRUE;
	$href = trim($href);
	if ($href != '') {
		if (PLUGIN_BACK_ALLOW_PAGELINK) {
			if (is_url($href)) {
				$href = rawurlencode($href);
			} else {
				$array = anchor_explode($href);
				$array[1] = ($array[1] != '') ? '#' . rawurlencode($array[1]) : '';
				$href = get_page_uri($array[0]) .  $array[1];
				$link = is_page($array[0]);
			}
		} else {
			$href = rawurlencode($href);
		}
	} else {
		if (! PLUGIN_BACK_ALLOW_JAVASCRIPT)
			return PLUGIN_BACK_USAGE . ': Set a page name or an URI';
		$href  = 'javascript:history.go(-1)';
	}

	if($link){
		// Normal link
		return $hr . '<div style="text-align:' . $align . '">' .
			'[ <a href="' . $href . '">' . $word . '</a> ]</div>' . "\n";
	} else {
		// Dangling link
		return $hr . '<div style="text-align:' . $align . '">' .
			'[ <span class="noexists">' . $word . '<a href="' . $href .
			'">?</a></span> ]</div>' . "\n";
	}
}
/* End of file back.inc.php */
/* Location: ./wiki-common/plugin/back.inc.php */