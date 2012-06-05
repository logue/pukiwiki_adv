<?php
/**
 * @copyright   Copyright &copy; 2006, Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
 * @version     $Id: _get.inc.php,v 0.2.1 2010/12/23 12:57:00 Logue Exp $
 * @license     http://opensource.org/licenses/gpl-license.php GNU Public License
 */

function plugin__get_inline()
{
	switch ( func_num_args() ) {
	case 2:
		list($msg) = func_get_args();
		return strip_htmltag($msg);
	case 3:
		list($name,$msg) = func_get_args();
		return i18n_gettext($name,$msg);
	case 4:
		list($name,$lang,$msg) = func_get_args();
		return i18n_setlocale($name,$lang,$msg);
	}

	return '';
}

function i18n_gettext($name,$msg)
{
	global $plugin_lang_path;
	static $checked = array();

	if (! isset($checked[$name])) {
		$checked[$name] = 1;
		if (empty($plugin_lang_path[$name])) {
			T_bindtextdomain($name, LANG_DIR);
		} else {
			T_bindtextdomain($name, $plugin_lang_path[$name]);
		}
		// bindtextdomain($name, LANG_DIR);
		T_bind_textdomain_codeset($name, SOURCE_ENCODING);
	}

	T_textdomain($name);
	$text = _( rawurldecode($msg) );
	T_textdomain(DOMAIN);
	return $text;
}

function i18n_setlocale($name,$lang,$msg)
{
	putenv('LC_ALL=' . $lang);
	T_setlocale(LC_ALL, $lang);
	$text = i18n_gettext($name,$msg);
	putenv('LC_ALL=' . PO_LANG);
	T_setlocale(LC_ALL, PO_LANG);
	return $text;
}

/* End of file _get.inc.php */
/* Location: ./wiki-common/plugin/_get.inc.php */