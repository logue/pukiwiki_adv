<?php
// $Id: back.inc.php,v 1.10.5 2011/02/05 10:42:00 Logue Exp $
// Copyright (C)
//   2010      PukiWiki Advance Developers Team
//   2008      PukiWiki Plus! Developers Team
//   2003-2004 PukiWiki Developers Team
//   2002,2006 Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
//
// back plugin

use PukiWiki\Auth\Auth;
use PukiWiki\Factory;
use PukiWiki\Utility;

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
	if (!empty($href)) {
		if ( Auth::check_role('safemode')) {
			if (is_url($href)) {
				$href = rawurlencode($href);
			} else {
				$wiki = Factory::Wiki($array[0]);
				$array = Utility::explodeAnchor($href);
				$array[1] = !empty($array[1]) ? '#' . rawurlencode($array[1]) : '';
				$href = $wiki->uri() . $array[1];
				$link = $wiki->has();
			}
		} else {
			$href = rawurlencode($href);
		}
	} else {
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